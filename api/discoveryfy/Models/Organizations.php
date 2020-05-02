<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Models;

use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Exceptions\UnauthorizedException;
use Phalcon\Api\Filters\UUIDFilter;
use Phalcon\Api\Mvc\Model\TimestampableModel;
use Phalcon\Api\Validators\UuidValidator;
use Phalcon\Db\RawValue;
use Phalcon\DI;
use Phalcon\Filter;
use Phalcon\Mvc\Model\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\Resultset\Complex;
use Phalcon\Mvc\Model\Row;
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

        $this->addBehavior(
            new SoftDelete([
                'field' => 'deleted_at',
                'value' => date('Y-m-d H:i:s'),
            ])
        );

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
        return [
            'deleted_at'            => Filter::FILTER_STRING,
        ];
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
//            ->add('name', new Uniqueness([
//                'message' => 'This name already exists in the database',
//            ]))
            ->add('name', new StringLength([
                'min' => 3,
                'includedMinimum' => false,
                'messageMinimum' => 'The name must have minimum 4 characters'
            ]))
            ->add('who_can_create_polls', new InclusionIn([
                'message' => 'The field who_can_create_polls must be MEMBERS, ADMINS or OWNERS',
                'domain'  => [ 'MEMBERS', 'ADMINS', 'OWNERS' ],
            ]))
        ;
        return $this->validate($validator);
    }

    public function getDeletedAt(): ?\DateTime
    {
        if (empty($this->deleted_at) || $this->deleted_at instanceof RawValue) {
            return null;
        }
        return new \DateTime($this->deleted_at);
    }

    /**
     * Inside the return three objects are returned: org, member & user_id
     *
     * @param string $group_uuid
     * @param string $user_uuid
     * #return Complex
     * @return Row
     * @throws InternalServerErrorException
     * @throws UnauthorizedException
     */
    public static function getUserMembership(string $group_uuid, string $user_uuid): Row
    {
        /** #var Complex $res */
        /** @var Row $res */
        $res = self::getBuilder()
//            ->columns('org.*, member.rol as member_rol, user.id as user_id')
            ->columns('org.*, member.*, user.id as user_id')
            ->from([ 'org' => self::class]) //Organizations::class
            ->innerJoin(Memberships::class, 'org.id = member.organization_id', 'member')
            ->innerJoin(Users::class, 'member.user_id = user.id', 'user') // Necessary for check deleted_at
            ->where('org.id = :org_uuid: AND user.id = :user_uuid: AND org.deleted_at IS NULL AND user.deleted_at IS NULL')
            ->setBindTypes([ 'org_uuid' => \PDO::PARAM_STR, 'user_uuid' => \PDO::PARAM_STR ])
            ->setBindParams([ 'org_uuid' => $group_uuid, 'user_uuid' => $user_uuid ])
            ->getQuery()->execute();

        if ($res->count() === 0) {
            throw new UnauthorizedException('This user not belong to this group');
        }
        if ($res->count() > 1) {
            throw new InternalServerErrorException('Only one membership should be possible');
        }
        $res = $res->getFirst();
        if ($res->user_id !== $user_uuid || $res->org->get('id') !== $group_uuid) {
            throw new InternalServerErrorException('Strange error in the query');
        }
        return $res;
    }

    /**
     * Organization::public_visibility == true || $user_uuid->rol !== INVITED
     *
     * @param string $group_uuid
     * @param string|null $user_uuid
     * @return Resultset Simple when user_id is null, Complex otherwise
     */
    public static function isPublicVisibilityOrMember(string $group_uuid, ?string $user_uuid): Resultset
    {
        $q = self::getBuilder()
            ->columns('org.*')
            ->from([ 'org' => Organizations::class])
            ->where('org.id = :org_uuid: AND org.public_visibility = :public_visibility: AND org.deleted_at IS NULL')
            ->setBindTypes([ 'org_uuid' => \PDO::PARAM_STR, 'public_visibility' => \PDO::PARAM_BOOL ])
            ->setBindParams([ 'org_uuid' => $group_uuid, 'public_visibility' => true ]);

        if ($user_uuid) {
            $q
                ->columns('org.*, member.*, user.id as user_id')
                ->leftJoin(Memberships::class, 'org.id = member.organization_id', 'member')
                ->innerJoin(Users::class, 'member.user_id = user.id AND user.deleted_at IS NULL', 'user') // Necessary for check deleted_at
                ->orWhere('(user.id = :user_uuid: AND member.rol != :member_rol:)')
                ->setBindTypes([ 'user_uuid' => \PDO::PARAM_STR, 'member_rol' => \PDO::PARAM_STR ], true)
                ->setBindParams([ 'user_uuid' => $user_uuid, 'member_rol' => 'ROLE_INVITED' ], true);
        }
        return $q->getQuery()->execute();
    }

    /**
     * Organization::public_membership == true || $user_uuid->rol !== INVITED
     * @param string $group_uuid
     * @param string $user_uuid
     * @return Complex
     */
    public static function isPublicMembershipOrMember(string $group_uuid, string $user_uuid): Complex
    {
        return self::getBuilder()
            ->columns('org.*, member.*, user.id as user_id')
            ->from([ 'org' => Organizations::class])
            ->innerJoin(Memberships::class, 'org.id = member.organization_id', 'member')
            ->innerJoin(Users::class, 'member.user_id = user.id AND user.deleted_at IS NULL', 'user') // Necessary for check deleted_at
            ->where('org.id = :org_uuid: AND org.deleted_at IS NULL')
            ->andWhere('(org.public_membership = :public_membership: OR (user.id = :user_uuid: AND member.rol != :member_rol:))')
            ->setBindTypes([ 'org_uuid' => \PDO::PARAM_STR, 'public_membership' => \PDO::PARAM_BOOL, 'user_uuid' => \PDO::PARAM_STR, 'member_rol' => \PDO::PARAM_STR ])
            ->setBindParams([ 'org_uuid' => $group_uuid, 'public_membership' => true, 'user_uuid' => $user_uuid, 'member_rol' => 'ROLE_INVITED' ])
            ->getQuery()->execute();
    }

    /**
     * @param string|null $user_uuid
     * @param string $orderBy
     * @return Resultset Simple when user_id is null, Complex otherwise
     */
    public static function getPublicVisibilityOrMemberGroups(?string $user_uuid, string $orderBy = ''): Resultset
    {
        $q = self::getBuilder()
            ->columns('org.*')
            ->from([ 'org' => Organizations::class])
            ->where('org.public_visibility = :public_visibility: AND org.deleted_at IS NULL')
            ->setBindTypes([ 'public_visibility' => \PDO::PARAM_BOOL ])
            ->setBindParams([ 'public_visibility' => true ]);

        if ($user_uuid) {
            $q
                ->columns('org.*, member.*, user.id as user_id')
                ->leftJoin(Memberships::class, 'org.id = member.organization_id', 'member')
                ->innerJoin(Users::class, 'member.user_id = user.id AND user.deleted_at IS NULL', 'user') // Necessary for check deleted_at
                ->orWhere('(user.id = :user_uuid: AND member.rol != :member_rol:)')
                ->setBindTypes([ 'user_uuid' => \PDO::PARAM_STR, 'member_rol' => \PDO::PARAM_STR ], true)
                ->setBindParams([ 'user_uuid' => $user_uuid, 'member_rol' => 'ROLE_INVITED' ], true);
        }
        if (true !== empty($orderBy)) {
            $q->orderBy($orderBy);
        }
        return $q->getQuery()->execute();
    }

    // In non static context this function can be called directly this way: $this->modelsManager->createBuilder()
    public static function getBuilder(): Builder
    {
        $builder = new Builder();
        $di = DI::getDefault();
        $builder->setDI($di);
        return $builder;
    }
}
