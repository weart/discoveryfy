<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Users;

use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Phalcon\Api\Controllers\BaseItemApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Discoveryfy\Models\Users;
use Phalcon\Api\Controllers\BaseController;
use Phalcon\Api\Transformers\BaseTransformer;
use Phalcon\Cache;
use Phalcon\Config;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;

/**
 * Update the user information
 *
 * Module       Users
 * Class        PutController
 * OperationId  user.put
 * Operation    PUT
 * OperationUrl /users/{user_uuid}
 * Security     Only the current logged user or app admin
 *
 * @property Cache        $cache
 * @property Config       $config
 * @property Request      $request
 * @property Response     $response
 */
class PutController extends BaseItemApiController
{
    /** @var string */
    protected $model       = Users::class;

    /** @var string */
    protected $resource    = Relationships::USER;

    /** @var string */
//    protected $transformer = BaseTransformer::class;

    /** @var string */
//    protected $method = 'item';

    public function checkSecurity(array $parameters): array
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available to registered users');
        }
        if ($this->auth->getUser()->get('id') !== $parameters['id'] && !$this->auth->getUser()->isAdmin()) {
            // Save security_event?
            throw new UnauthorizedException('User unauthorized for this action');
        }
        return $parameters;
    }

    public function coreAction(array $parameters): ResponseInterface
    {
        $attrs = [
            'username'          => Filter::FILTER_STRIPTAGS,
            'email'             => Filter::FILTER_EMAIL,
//            'public_visibility' => Filter::FILTER_BOOL,
//            'public_email'      => Filter::FILTER_BOOL,
            'language'          => Filter::FILTER_STRIPTAGS,
            'theme'             => Filter::FILTER_STRIPTAGS,
        ];
        foreach ($attrs as $attr => $filter) {
            if ($this->request->hasPut($attr)) {
                $this->auth->getUser()->set($attr, $this->request->getPut($attr, $filter));
            }
        }
        // Different behaviour than standard: password must be hashed
        if ($this->request->hasPut('password')) {
            $this->auth->getUser()
                ->setPasswordHash($this->security->hash($this->request->getPut('password', Filter::FILTER_STRING)));
//            ->set('password', $this->security->hash($this->request->getPut('password', Filter::FILTER_STRING)))
        }
        // Different behaviour than standard: request param and column name are not the equals
        if ($this->request->hasPut('public_visibility')) {
            $this->auth->getUser()
                ->set('public_visibility', $this->request->getPut('public_visibility', Filter::FILTER_BOOL, false));
        }
        if ($this->request->hasPut('public_email')) {
            $this->auth->getUser()
                ->set('public_email', $this->request->getPut('public_email', Filter::FILTER_BOOL, false));
        }
        // Different behaviour than standard: only admins can modify this columns
        if ($this->auth->getUser()->isAdmin()) {
            if ($this->request->hasPut('enabled')) {
                $this->auth->getUser()
                    ->set('enabled', $this->request->getPut('enabled', Filter::FILTER_BOOL, true));
            }
            if ($this->request->hasPut('rol')) {
                $this->auth->getUser()
                    ->set('rol', $this->request->getPut('rol', Filter::FILTER_STRIPTAGS, 'ROLE_USER'));
            }
        }

        if (true !== $this->auth->getUser()->validation() || true !== $this->auth->getUser()->save()) {
            if (false === $this->auth->getUser()->validationHasFailed()) {
                throw new InternalServerErrorException('Error changing user');
            }
            return $this->response->sendApiErrors($this->request->getContentType(), $this->auth->getUser()->getMessages());
        }

        return $this->sendApiData($this->auth->getUser());
    }
}
