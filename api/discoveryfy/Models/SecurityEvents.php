<?php
declare(strict_types=1);

/**
 * This file is part of the Vökuró.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Models;

use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\ModelException;
use Phalcon\Api\Filters\UUIDFilter;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Config;
use Phalcon\Db\RawValue;
use Phalcon\Filter;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Security\Random;
use Phalcon\Validation;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Ip;
use Phalcon\Validation\Validator\Ip as IpValidator;

class SecurityEvents extends AbstractModel
{
    protected $timestamps_from_db;

    protected $created_at;
    protected $user_id;
    protected $type;
    protected $ip_address;
    protected $user_agent;

    /**
     * Similar to TimestampableModel, but only with created_at
     */
    public function initialize()
    {
        $this->setSource('security_events');

        /** @var Config $config */
        $config = $this->getDI()->get('config');
        $this->timestamps_from_db = (bool) $config->path('db.timestamps_from_db', true);

        if ($this->timestamps_from_db) {
            //Sets a list of attributes that must be skipped from the generated INSERT/UPDATE statement
            $this->skipAttributes(
                [
                    'createdAt',
                ]
            );

        } else {
            $this->addBehavior(
                new Timestampable(
                    [
                        'beforeCreate' => [
                            'field'  => 'created_at',
                            'format' => 'Y-m-d H:i:s',
                        ]
                    ]
                )
            );
        }

//        $this->belongsTo('user_id', Users::class, 'id', [
//            'alias' => 'user',
//        ]);

        parent::initialize();
    }

    /**
     * Column Mapping: Property rename them to match the respective columns in the database
     *
     * @return array
     */
//    public function columnMap(): array
//    {
//        return [
//            'id'            => 'id',
//            'user_id'       => 'userId',
//            'type'          => 'type',
//            'ip_address'    => 'ipAddress',
//            'user_agent'    => 'userAgent'
//        ];
//    }

    /**
     * @return array<string,string>
     */
    public function getPrivateAttributes(): array
    {
        return [
        ];
    }

    /**
     * @return array<string,string>
     */
    public function getPublicAttributes(): array
    {
        return [
            'id'            => UUIDFilter::FILTER_NAME,
            'user_id'       => UUIDFilter::FILTER_NAME,
            'created_at'    => Filter::FILTER_STRING,
            'type'          => Filter::FILTER_STRING,
            'ip_address'    => Filter::FILTER_STRING, //@ToDo: Improve filter? In validator is checked
            'user_agent'    => Filter::FILTER_STRING, //@ToDo: Improve filter?
        ];
    }

    public function getCreatedAt(): \DateTime
    {
        if ($this->created_at instanceof RawValue) {
            throw new InternalServerErrorException('Field only available after persist the object in the db');
        }
        return new \DateTime($this->created_at);
    }

    /**
     * Validates the id uniqueness
     *
     * @return bool
     */
    public function validation()
    {
        $validator = (new Validation())
            ->add('id', new Uniqueness([
                'message' => 'The id already exists in the database',
            ]))
            ->add('type', new InclusionIn([
                'message' => ':field is not a valid type',
                'domain' => [
                    'login_success', 'login_failure', 'email_confirmation', 'password_change', 'password_reset'
                ]
            ]))
            ->add('ip_address', new IpValidator([
                'message' => ':field must contain only ip addresses',
                'version' => IpValidator::VERSION_4,
                'allowReserved' => true,
                'allowPrivate' => true,
                'allowEmpty' => false,
            ]))
        ;
        return $this->validate($validator);
    }

    public function createLoginSuccessEvent(Request $request, ?Users $user)
    {
        return $this->createEvent($request, $user, 'login_success');
    }

    public function createLoginFailureEvent(Request $request, ?Users $user)
    {
        return $this->createEvent($request, $user, 'login_failure');
    }

    public function createEmailConfirmationEvent(Request $request, ?Users $user)
    {
        return $this->createEvent($request, $user, 'email_confirmation');
    }

    public function createPasswordChangeEvent(Request $request, ?Users $user)
    {
        return $this->createEvent($request, $user, 'password_change');
    }

    public function createPasswordResetEvent(Request $request, ?Users $user)
    {
        return $this->createEvent($request, $user, 'password_reset');
    }

    private function createEvent(Request $request, ?Users $user, string $type)
    {
        $this
            ->set('id', (new Random())->uuid())
            ->set('type', $type)
            ->set('ip_address', $request->getClientAddress())
            ->set('user_agent', $request->getUserAgent());
//        $this->assign([
//            'id'            => (new Random())->uuid(),
//            'user_id'       => $user ? $user->get('id') : null,
//            'type'          => $type,
//            'ip_address'    => $request->getClientAddress(),
//            'user_agent'    => $request->getUserAgent()
//        ]);
        if ($user) {
            $this->set('user_id', $user->get('id'));
        }
        if (!$this->save()) {
            throw new ModelException($this->getMessage());
//            throw new ModelException($this->getModelMessages()[0]);
        }
        return $this;
    }
}
