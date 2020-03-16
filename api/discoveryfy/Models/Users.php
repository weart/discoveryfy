<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Models;

use Phalcon\Api\Filters\UUIDFilter;
use Phalcon\Api\Mvc\Model\TimestampableModel;
use Phalcon\Filter;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Validation;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Email;

/**
 * Class Users
 */
class Users extends TimestampableModel
{
    // Private attributes
    protected $id;
    protected $enabled;
    protected $password;
    // Public attributes
    protected $username;
    protected $email;
    protected $public_visibility;
    protected $public_email;
    protected $language;
    protected $theme;
    protected $rol;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->setSource('users');

        $this->addBehavior(
            new SoftDelete(
                [
                    'field' => 'enabled',
                    'value' => 0
                ]
            )
        );

        //References to sessions, votes, tracks & organizations (using memberships table)
//        $this->hasOne('id', Sessions::class, 'user_id', [
//            'alias'    => 'session',
//            'reusable' => true, // cache
//            'foreignKey' => [
//                'message' => 'Session cannot be deleted because he/she has activity in the system',
//            ],
//        ]);
//        $this->hasMany('id', Votes::class, 'user_id', [
//            'alias'    => 'votes',
//            'reusable' => true, // cache
//            'foreignKey' => [
//                'message' => 'Vote cannot be deleted because he/she has activity in the system',
//            ],
//        ]);
//        $this->hasMany('id', Tracks::class, 'user_id', [
//            'alias'    => 'tracks',
//            'reusable' => true, // cache
//            'foreignKey' => [
//                'message' => 'Track cannot be deleted because he/she has activity in the system',
//            ],
//        ]);
//        $this->hasMany('id', Memberships::class, 'user_id', [
//            'alias'    => 'members',
//            'reusable' => true, // cache
//        ]);
//        $this->hasManyToMany('id', Organizations::class, 'user_id', [
//            'alias'    => 'organizations',
//            'reusable' => true, // cache
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
//        return parent::columnMap() + [ //created_at & updated_at
//            'id'                => 'id',
//            'enabled'           => 'enabled',
//            'password'          => 'password',
//            'username'          => 'username',
//            'email'             => 'email',
//            'public_visibility' => 'publicVisibility',
//            'public_email'      => 'publicEmail',
//            'language'          => 'language',
//            'theme'             => 'theme',
//            'rol'               => 'rol',
//        ];
//    }

    /**
     * @return array<string,string>
     */
    public function getPrivateAttributes(): array
    {
        return [
            'id'                => UUIDFilter::FILTER_NAME,
            'enabled'           => Filter::FILTER_BOOL,
            'password'          => Filter::FILTER_STRING,
        ];
    }

    /**
     * @return array<string,string>
     */
    public function getPublicAttributes(): array
    {
        return [
            'created_at'         => Filter::FILTER_STRING,
            'updated_at'         => Filter::FILTER_STRING,
            'username'          => Filter::FILTER_STRING,
            'email'             => Filter::FILTER_EMAIL,
            'public_visibility'  => Filter::FILTER_BOOL,
            'public_email'       => Filter::FILTER_BOOL,
            'language'          => Filter::FILTER_STRING,
            'theme'             => Filter::FILTER_STRING,
            'rol'               => Filter::FILTER_STRING,
        ];
    }

    /**
     * Validates the id uniqueness
     *
     * @return bool
     */
    public function validation()
    {
        $validator = new Validation();
        $validator->add('id', new Uniqueness([
            'message' => 'This id already exists in the database',
        ]));
        $validator->add('username', new Uniqueness([
            'message' => 'This username already exists in the database',
        ]));
        $validator->add('username', new StringLength([
            'min' => 3,
            'includedMinimum' => false,
            'messageMinimum' => 'The username must have minimum 4 characters'
        ]));
        $validator->add('email', new Email([
            'message' => 'The email field is not valid'
        ]));
        $validator->add(
            [
                'language',
                'theme',
                'rol'
            ],
            new InclusionIn([ 'domain' => [ //message not provided
                'language'          => [ 'en', 'es', 'ca' ],
                'theme'             => [ 'default' ],
                'rol'               => [ 'ROLE_ADMIN', 'ROLE_USER' ]
            ]])
        );

        return $this->validate($validator);
    }

    public function getPasswordHash()
    {
        return $this->password;
    }

    public function setPasswordHash(string $password)
    {
        $this->password = $password;
        return $this;
    }
}
