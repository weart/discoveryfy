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
use Discoveryfy\Exceptions\NotImplementedException;
use Phalcon\Http\Request as PhRequest;
use function str_replace;

/**
 * Class Request
 *
 * @see https://www.bookstack.cn/read/phalcon-4.0-en
 * @package Phalcon\Api\Http
 */
class Request extends PhRequest
{
    private $acceptedContentType = [
        'application/json',
        'application/vnd.api+json',
        'application/ld+json'
    ];
    public function getContentType(): string
    {
//        if (!$this->hasHeader('accept') && !$this->hasHeader('Content-Type')) {
//            throw new BadRequestException('Undefined content type');
//        }
        $type = trim((
            $this->getHeader('accept') !== '*/*' ?
            $this->getHeader('accept') : parent::getContentType() //$this->getHeader('Content-Type');
        ));
        //If Type is any or empty, by default the most simple type is used: application/json
        if (empty($type) || $type === '*/*') {
//            throw new BadRequestException('Undefined content type');
            $type = 'application/json';
        }
        //Remove charset
        if (false !== strpos($type, ';')) {
            //Raise Exception if different than utf8?
            $type = strstr($type, ';', true);
        }
        if (!in_array($type, $this->acceptedContentType, true)) {
            throw new BadRequestException('Invalid content type');
        }
        if ($type === 'application/ld+json') {
            throw new NotImplementedException();
        }
        return $type;
    }

    /**
     * @return string
     */
    public function getBearerTokenFromHeader(): string
    {
        return str_replace('Bearer ', '', $this->getHeader('Authorization'));
    }

    /**
     * @return bool
     */
    public function isEmptyBearerToken(): bool
    {
        return true === empty($this->getBearerTokenFromHeader());
    }
}
