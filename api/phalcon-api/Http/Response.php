<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Http;

use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\NotImplementedException;
use Phalcon\Http\Response as PhResponse;
use Phalcon\Http\ResponseInterface;
use Phalcon\Messages\Message;
use function date;
use function json_decode;
use function sha1;
use function is_array;

class Response extends PhResponse
{
    const OK                    = 200;
    const CREATED               = 201;
    const ACCEPTED              = 202;
    const MOVED_PERMANENTLY     = 301;
    const FOUND                 = 302;
    const TEMPORARY_REDIRECT    = 307;
    const PERMANENTLY_REDIRECT  = 308;
    const BAD_REQUEST           = 400;
    const UNAUTHORIZED          = 401;
    const FORBIDDEN             = 403;
    const NOT_FOUND             = 404;
    const INTERNAL_SERVER_ERROR = 500;
    const NOT_IMPLEMENTED       = 501;
    const BAD_GATEWAY           = 502;

    private $codes = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        301 => 'Moved Permanently',
        302 => 'Found',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
    ];

    /**
     * Returns the http code description or if not found the code itself
     * @param int $code
     *
     * @return int|string
     */
    public function getHttpCodeDescription(int $code)
    {
        if (true === isset($this->codes[$code])) {
            return sprintf('%d (%s)', $code, $this->codes[$code]);
        }

        return $code;
    }

    /**
     * Deals with format content negotiation
     *
     * @param string $ContentType
     * @param array $data
     * @return ResponseInterface
     */
    public function sendApiContent(string $ContentType, array $data): ResponseInterface
    {
        if ($ContentType === 'application/vnd.api+json') {
            return $this->setJsonApiContent($data)->sendJsonApi();

        } elseif ($ContentType === 'application/json') {
            if (isset($data['id'])) { //Only one item
                if (isset($data['attributes'])) {
                    return $this->setJsonContent($this->array_flatten($data))->send();
                } else {
                    return $this->setJsonContent($data['id'])->send();
                }
            }
            $rtn = [];
            foreach ($data as $obj) {
                $rtn[] = $this->array_flatten($obj);
            }
            return $this->setJsonContent($rtn)->send();


        } elseif ($ContentType === 'application/ld+json') {
            throw new NotImplementedException();
//            $this->setJsonContent([
//                '@context' => 'string',
//                '@id' => 'string',
//                '@type' => 'string',
//                'User.Read' => $schema
//            ])->sendJsonLd();
        }
        throw new BadRequestException();
    }

    protected function array_flatten($array, $prefix = '')
    {
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = $result + $this->array_flatten($value, $prefix . $key . '.');
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }

    /**
     * Send the response back
     *
     * @return ResponseInterface
     */
    public function sendJsonApi(): ResponseInterface
    {
        $content   = $this->getContent();
        $timestamp = date('c');
        $hash      = sha1($timestamp . $content);
        $eTag      = sha1($content);

        /** @var array $content */
        $content = json_decode($this->getContent(), true);
        $jsonapi = [
            'jsonapi' => [
                'version' => '1.0',
            ],
        ];
        $meta    = [
            'meta' => [
                'timestamp' => $timestamp,
                'hash'      => $hash,
            ]
        ];

        /**
         * Join the array again
         */
        $data = $jsonapi + $content + $meta;
        $this
            ->setHeader('E-Tag', $eTag)
            ->setContentType('application/vnd.api+json', 'UTF-8')
            ->setJsonContent($data);

        if (is_null($this->getStatusCode())) {
            $this->setStatusCode(self::OK);
        }

        return parent::send();
    }

    public function sendJsonLd(): ResponseInterface
    {
        throw new NotImplementedException();
        $this->setContentType('application/ld+json', 'UTF-8');
        return parent::send();
    }

    /**
     * Sets the payload code as Error
     *
     * @param array $error
     *
     * @return Response
     */
    public function setPayloadJsonApiError(array $error): Response
    {
        if (!isset($error['code'])) {
            throw new InternalServerErrorException('Error code is mandatory');
        }
        if (is_null($this->getStatusCode()) && isset($this->codes[$error['code']])) {
//            Non-standard statuscode given without a message
            $this->setStatusCode($error['code'], $this->getHttpCodeDescription($error['code']));
        }
        if (!isset($error['title'])) {
            $error['title'] = $this->getHttpCodeDescription($error['code']);
        }
        if (!isset($error['status'])) {
            $error['status'] = $error['code'];
        }

        if (empty($this->getContent())) {
            $this->setJsonContent(['errors' => [$error]]);

        } else {
            /** @var array $content */
            $content = json_decode($this->getContent(), true);
            if (!isset($content['errors'])) {
                throw new InternalServerErrorException('Mixing errors with a normal response?');
            }
            $content['errors'][] = $error;
            $this->setJsonContent($content);
        }

        return $this;
    }

    public function setPayloadError(int $httpCode = self::BAD_REQUEST, ?string $title = null, ?int $appCode = null)
    {
        return $this->setPayloadJsonApiError([
            'code'    => $httpCode,
            'status'  => $appCode,
            'title' => $title,
        ]);
    }

    /**
     * Traverses the errors collection and sets the errors in the payload
     *
     * @param iterable<Message|string|array> $errors
     * @return Response
     */
    public function setPayloadErrors(iterable $errors): Response
    {
        foreach ($errors as $error) {
            if ($error instanceof Message) {
                $this->setPayloadError($error->getCode() ?? self::INTERNAL_SERVER_ERROR, $error->getMessage());

            } else if (is_string($error)) {
                $this->setPayloadError(self::INTERNAL_SERVER_ERROR, $error);

            } else if (is_array($error)) {
                $this->setPayloadJsonApiError($error);

            } else {
                throw new InternalServerErrorException('Invalid error');
            }
        }

        return $this;
    }

    public function sendException(\Throwable $e, string $ContentType, bool $debug = false): ResponseInterface
    {
        if (is_subclass_of($e, \Discoveryfy\Exceptions\Exception::class)) {
            $this
                ->setStatusCode($e->getCode())
                ->setPayloadJsonApiError($e->toJson()); //In non debug mode send all?

        } else {
            $msg = $debug ? $e->getMessage() : $this->getHttpCodeDescription(self::INTERNAL_SERVER_ERROR);

            $this
                ->setPayloadJsonApiError([
                    'code'  => $e->getCode(),
                    'title' => $msg,
                ]);
        }

        if ($ContentType === 'application/vnd.api+json') {
            return $this->sendJsonApi();

//        } elseif ($ContentType === 'application/ld+json') {
//        } elseif ($ContentType === 'application/json') { //By default
        }
        return parent::send();
    }

    /**
     * Sets the payload code as Success
     *
     * @param null|string|array $content The content
     *
     * @return Response
     */
    public function setJsonApiContent($content = []): Response
    {
        $data = (true === is_array($content)) ? $content : ['data' => $content];
        $data = (true === isset($data['data'])) ? $data  : ['data' => $data];

        $this->setJsonContent($data);

        return $this;
    }
}
