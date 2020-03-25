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
 *
 * @property Cache        $cache
 * @property Config       $config
 * @property Request      $request
 * @property Response     $response
 */
class PutController extends BaseController
{
    /** @var string */
    protected $model       = Users::class;

    /** @var string */
    protected $resource    = Relationships::USERS;

    /** @var string */
    protected $transformer = BaseTransformer::class;

    /** @var string */
    protected $method = 'item';

    public function callAction(string $user_uuid = ''): ResponseInterface
    {
        if (!$this->auth->getUser()) {
            throw new UnauthorizedException('Only available for registered users');
        }
        if ($this->auth->getUser()->get('id') !== $user_uuid && !$this->auth->getUser()->isAdmin()) {
            // Save security_event?
            throw new UnauthorizedException('User unauthorized for this action');
        }

        // Improve this with something more beautiful
        if ($this->request->hasPut('username')) {
            $this->auth->getUser()->set('username', $this->request->getPut('username', Filter::FILTER_STRIPTAGS));
        }
        if ($this->request->hasPut('password')) {
            $this->auth->getUser()
                ->setPasswordHash($this->security->hash($this->request->getPut('password', Filter::FILTER_STRING)));
//            ->set('password', $this->security->hash($this->request->getPut('password', Filter::FILTER_STRING)))
        }
        if ($this->request->hasPut('email')) {
            $this->auth->getUser()->set('email', $this->request->getPut('email', Filter::FILTER_EMAIL));
        }
        if ($this->request->hasPut('public_visibility')) {
            $this->auth->getUser()
                ->set('public_visibility', $this->request->getPut('public-visibility', Filter::FILTER_BOOL, false));
        }
        if ($this->request->hasPut('public_email')) {
            $this->auth->getUser()
                ->set('public_email', $this->request->getPut('public-email', Filter::FILTER_BOOL, false));
        }
        if ($this->request->hasPut('language')) {
            $this->auth->getUser()
                ->set('language', $this->request->getPut('language', Filter::FILTER_STRIPTAGS, 'en'));
        }
        if ($this->request->hasPut('theme')) {
            $this->auth->getUser()
                ->set('theme', $this->request->getPut('theme', Filter::FILTER_STRIPTAGS, 'default'));
        }

        if ($this->auth->getUser()->isAdmin()) {
            if ($this->request->hasPut('enabled')) {
                $this->auth->getUser()
                    ->set('enabled', $this->request->getPut('enabled', Filter::FILTER_BOOL, true));
            }
            if ($this->request->hasPut('enabled')) {
                $this->auth->getUser()
                    ->set('rol', $this->request->getPut('rol', Filter::FILTER_STRIPTAGS, 'ROLE_USER'));
            }
        }

        if (true !== $this->auth->getUser()->validation() || true !== $this->auth->getUser()->save()) {
            if (false === $this->auth->getUser()->validationHasFailed()) {
                throw new InternalServerErrorException('Error changing user');
            }
            return $this->response
                ->setPayloadErrors($this->auth->getUser()->getMessages())
                ->send();
        }

        return parent::callAction($user_uuid);
    }
}
