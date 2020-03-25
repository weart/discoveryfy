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
use Phalcon\Api\Validators\UuidValidator;
use Phalcon\Filter;
use Phalcon\Validation;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Class Organizations
 *
 * @package Discoveryfy\Models
 */
class Organizations extends TimestampableModel
{
    protected $id;
    protected $name;
    protected $description;
    protected $public_visibility;
    protected $public_membership;
    protected $who_can_create_polls;

    /**
     * Returns the source table from the database
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('organizations');

        /*
        $this->hasManyToMany(
            'id',
            Memberships::class,
            'organization_id',
            'user_id',
            Users::class,
            'id',
            [
                'reusable' => true, //cache relations
                'alias'    => 'members',
            ]
        );
        $this->hasMany('id', Polls::class, 'organization_id', [
            'alias'    => 'polls',
            'reusable' => true, // cache
            'foreignKey' => [
                'message' => 'Poll cannot be deleted because he/she has activity in the system',
            ],
        ]);
        //Comments
        */

        parent::initialize();
    }

    /**
     * @return array<string,string>
     */
    public function getPrivateAttributes(): array
    {
        return [];
    }

    /**
     * @return array<string,string>
     */
    public function getPublicAttributes(): array
    {
        return [
            'id'                    => UUIDFilter::FILTER_NAME,
            'created_at'            => Filter::FILTER_STRING,
            'updated_at'            => Filter::FILTER_STRING,
            'name'                  => Filter::FILTER_STRING,
            'description'           => Filter::FILTER_STRING,
            'public_visibility'     => Filter::FILTER_BOOL,
            'public_membership'     => Filter::FILTER_BOOL,
            'who_can_create_polls'  => Filter::FILTER_STRING,
        ];
    }

    /**
     * @return bool
     */
    public function validation(): bool
    {
        $validator = (new Validation())
            ->add('id', new Uniqueness([
                'message' => 'This id already exists in the database',
            ]))
            ->add('id', new UuidValidator())
            ->add('username', new Uniqueness([
                'message' => 'This username already exists in the database',
            ]))
            ->add('username', new StringLength([
                'min' => 3,
                'includedMinimum' => false,
                'messageMinimum' => 'The username must have minimum 4 characters'
            ]))
            ->add('who_can_create_polls', new InclusionIn([
                'message' => 'The field who_can_create_polls must be MEMBERS, ADMINS or OWNERS',
                'domain'  => [ 'MEMBERS', 'ADMINS', 'OWNERS' ],
            ]))
        ;
        return $this->validate($validator);
    }
}
