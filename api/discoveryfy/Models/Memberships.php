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
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Class Memberships
 *
 * @package Discoveryfy\Models
 */
class Memberships extends TimestampableModel
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->setSource('memberships');

        parent::initialize();
    }

    /**
     * @return array<string,string>
     */
    public function getPrivateAttributes(): array
    {
        return [
            'user_id'           => UUIDFilter::FILTER_NAME,
            'organization_id'   => UUIDFilter::FILTER_NAME,
        ];
    }

    /**
     * @return array<string,string>
     */
    public function getPublicAttributes(): array
    {
        return [
            //created_at & updated_at
            'id'                => UUIDFilter::FILTER_NAME,
            'created_at'        => Filter::FILTER_STRING,
            'updated_at'        => Filter::FILTER_STRING,
            'rol'               => Filter::FILTER_STRING
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
            ->add('user_id', new UuidValidator())
            ->add('organization_id', new UuidValidator())
            ->add('rol', new InclusionIn([
                'message' => 'The field who_can_create_polls must be ROLE_INVITED, ROLE_MEMBER, ROLE_ADMIN or ROLE_OWNER',
                'domain'  => [ 'ROLE_INVITED', 'ROLE_MEMBER', 'ROLE_ADMIN', 'ROLE_OWNER' ],
            ]))
        ;
        return $this->validate($validator);
    }
}
