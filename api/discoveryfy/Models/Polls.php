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
use Phalcon\Di;
use Phalcon\Filter;
use Phalcon\Mvc\Model\Query\Builder;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;
//use Phalcon\Mvc\Model\Resultset\Simple;
//use Phalcon\Mvc\Model\Resultset\Complex;
use Phalcon\Mvc\Model\Row;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Date;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\Regex;

/**
 * Class Polls
 *
 * @package Discoveryfy\Models
 */
class Polls extends TimestampableModel
{
    /**
     * Returns the source table from the database
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('polls');

        parent::initialize();
    }

    /**
     * @return array<string,string>
     */
    public function getPrivateAttributes(): array
    {
        return [
            'organization_id'                   => UUIDFilter::FILTER_NAME,
        ];
    }

    /**
     * @return array<string,string>
     */
    public function getPublicAttributes(): array
    {
        return [
            'id'                                => UUIDFilter::FILTER_NAME,
            'created_at'                        => Filter::FILTER_STRING,
            'updated_at'                        => Filter::FILTER_STRING,
            'name'                              => Filter::FILTER_STRING,
            'description'                       => Filter::FILTER_STRING,
            'spotify_playlist_images'           => Filter::FILTER_STRING, //array, saved in json
            'spotify_playlist_public'           => Filter::FILTER_BOOL,
            'spotify_playlist_collaborative'    => Filter::FILTER_BOOL,
            'spotify_playlist_uri'              => Filter::FILTER_STRING,
            'spotify_playlist_winner_uri'       => Filter::FILTER_STRING,
            'spotify_playlist_historic_uri'     => Filter::FILTER_STRING,
            'start_date'                        => Filter::FILTER_STRING,
            'end_date'                          => Filter::FILTER_STRING,
            'restart_date'                      => Filter::FILTER_STRING,
            'public_visibility'                 => Filter::FILTER_BOOL,
            'public_votes'                      => Filter::FILTER_BOOL,
            'anon_can_vote'                     => Filter::FILTER_BOOL,
            'who_can_add_track'                 => Filter::FILTER_STRING,
            'anon_votes_max_rating'             => Filter::FILTER_ABSINT,
            'user_votes_max_rating'             => Filter::FILTER_ABSINT,
            'multiple_user_tracks'              => Filter::FILTER_BOOL,
            'multiple_anon_tracks'              => Filter::FILTER_BOOL
        ];
    }

    public function getStartDate(): ?\DateTime
    {
        if (empty($this->start_date) || $this->start_date instanceof RawValue) {
            return null;
        }
        return new \DateTime($this->start_date);
    }

    public function getEndDate(): ?\DateTime
    {
        if (empty($this->end_date) || $this->end_date instanceof RawValue) {
            return null;
        }
        return new \DateTime($this->end_date);
    }

    /**
     * @return bool
     */
    public function validation(): bool
    {
        $crontab_regex = "/(\*|[0-5]?[0-9]|\*\/[0-9]+)\s+"
            ."(\*|1?[0-9]|2[0-3]|\*\/[0-9]+)\s+"
            ."(\*|[1-2]?[0-9]|3[0-1]|\*\/[0-9]+)\s+"
            ."(\*|[0-9]|1[0-2]|\*\/[0-9]+|jan|feb|mar|apr|may|jun|jul|aug|sep|oct|nov|dec)\s+"
            ."(\*\/[0-9]+|\*|[0-7]|sun|mon|tue|wed|thu|fri|sat)\s*"
            ."(\*\/[0-9]+|\*|[0-9]+)?/i";
        $date_format = 'Y-m-d H:i:s';

        $validator = (new Validation())
            ->add('id', new Uniqueness([
                'message' => 'This id already exists in the database',
            ]))
            ->add('id', new UuidValidator())
            ->add('organization_id', new UuidValidator())
            ->add('name', new PresenceOf())
            /*
             * @ToDo:
             * Validation fails, but I can't get why, the following code (phalcon internals) return no errors
             * ‌‌\DateTime::createFromFormat($date_format, $this->get('start_date')); && ‌‌\DateTime::getLastErrors()
             * @see: https://github.com/phalcon/cphalcon/blob/v4.0.0/phalcon/Validation/Validator/Date.zep
            ->add('start_date', new Date([  //message not provided
                'allowEmpty' => true,
                'format' => $date_format
            ]))
            ->add('end_date', new Date([ //message not provided
                'allowEmpty' => true,
                'format' => $date_format
            ]))
             */
            ->add('restart_date', new Regex([ //message not provided
                'allowEmpty' => true,
                'pattern' => $crontab_regex
            ]))
            ->add('who_can_add_track', new InclusionIn([ 'domain' => [ //message not provided
                'ANYONE', 'USERS', 'MEMBERS', 'ADMINS', 'OWNERS'
            ]]))
        ;
        return $this->validate($validator);
    }

    public static function getUserMembership(string $poll_uuid, string $user_uuid): Row
    {
        $q = self::getBuilder()
            ->columns('poll.*, org.*, member.*')
            ->from([ 'poll' => Polls::class ])
            ->innerJoin(Organizations::class, 'poll.organization_id = org.id AND org.deleted_at IS NULL', 'org')
            ->innerJoin(Memberships::class, 'org.id = member.organization_id', 'member')
            ->innerJoin(Users::class, 'member.user_id = user.id AND user.deleted_at IS NULL AND user.enabled = :enabled: AND user.id = :user_uuid:', 'user') // Necessary for check if user isActive
            ->where('poll.id = :poll_uuid:')
            ->setBindTypes([ 'poll_uuid' => \PDO::PARAM_STR, 'user_uuid' => \PDO::PARAM_STR, 'enabled' => \PDO::PARAM_BOOL ])
            ->setBindParams([ 'poll_uuid' => $poll_uuid, 'user_uuid' => $user_uuid, 'enabled' => true ]);

        $res = $q->getQuery()->execute();
        if ($res->count() === 0) {
            throw new UnauthorizedException('This user not belong to the group of this poll');
        }
//        $res = self::getFirstOrThrow($res);
        if ($res->count() > 1) {
            throw new InternalServerErrorException('Only one owner membership should be possible');
        }
        $res = $res->getFirst();
        if ($res->poll->get('id') !== $poll_uuid || $res->member->get('user_id') !== $user_uuid) {
            throw new InternalServerErrorException('Strange error in the query');
        }
        return $res;
    }

    // Poll::public_visibility == true || $user_uuid->rol !== INVITED
    public static function isPublicVisibilityOrMember(string $poll_uuid, ?string $user_uuid): ResultsetInterface // Simple or Complex
    {
        $q = self::getBuilder()
            ->from([ 'poll' => Polls::class ])
            ->innerJoin(Organizations::class, 'poll.organization_id = org.id AND org.deleted_at IS NULL', 'org')
//            ->leftJoin(Tracks::class, 'poll.id = track.poll_id', 'track')
            ->where('poll.id = :poll_uuid:')
            ->setBindTypes([ 'poll_uuid' => \PDO::PARAM_STR, 'public_visibility' => \PDO::PARAM_BOOL ])
            ->setBindParams([ 'poll_uuid' => $poll_uuid, 'public_visibility' => true ]);

        if ($user_uuid) {
            $q
//                ->columns('poll.*, org.*, member.*')
                ->leftJoin(Memberships::class, 'poll.organization_id = member.organization_id', 'member')
                ->innerJoin(Users::class, 'member.user_id = user.id', 'user') // Necessary for check if user isActive
                ->andWhere('((poll.public_visibility = :public_visibility:) OR (user.id = :user_uuid: AND user.deleted_at IS NULL AND user.enabled = :enabled: AND member.rol != :member_rol:))')
                ->setBindTypes([ 'user_uuid' => \PDO::PARAM_STR, 'member_rol' => \PDO::PARAM_STR, 'enabled' => \PDO::PARAM_BOOL ], true)
                ->setBindParams([ 'user_uuid' => $user_uuid, 'member_rol' => 'ROLE_INVITED', 'enabled' => true ], true);
        } else {
            $q
//                ->columns('poll.*, org.*')
                ->andWhere('poll.public_visibility = :public_visibility:');
        }
        return $q->getQuery()->execute();
    }

    public static function getPublicVisibilityOrMember(?string $user_uuid, string $orderBy = '', array $pagination = []): ResultsetInterface // Simple or Complex
    {
        $q = self::getBuilder()
//            ->columns('poll.*')
            ->from([ 'poll' => Polls::class ])
            ->innerJoin(Organizations::class, 'poll.organization_id = org.id AND org.deleted_at IS NULL', 'org')
            ->setBindTypes([ 'public_visibility' => \PDO::PARAM_BOOL ])
            ->setBindParams([ 'public_visibility' => true ]);

        if ($user_uuid) {
            $q
//                ->columns('poll.*, org.*, member.*')
                ->leftJoin(Memberships::class, 'poll.organization_id = member.organization_id', 'member')
                ->innerJoin(Users::class, 'member.user_id = user.id AND user.deleted_at IS NULL AND user.enabled = :enabled: AND user.id = :user_uuid:', 'user') // Necessary for check if user isActive
                ->orWhere('((poll.public_visibility = :public_visibility:) OR (user.id = :user_uuid: AND member.rol != :member_rol:))')
                ->setBindTypes([ 'user_uuid' => \PDO::PARAM_STR, 'member_rol' => \PDO::PARAM_STR, 'enabled' => \PDO::PARAM_BOOL ], true)
                ->setBindParams([ 'user_uuid' => $user_uuid, 'member_rol' => 'ROLE_INVITED', 'enabled' => true ], true);
        } else {
            $q
//                ->columns('poll.*, org.*')
                ->where('poll.public_visibility = :public_visibility:');
        }
        if (true !== empty($orderBy)) {
            $q->orderBy($orderBy);
        }
        if (true !== empty($pagination)) {
            $q->limit($pagination['limit'], $pagination['offset']);
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
