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
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;

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
            ->add('organization_id', new UuidValidator())
            ->add('name', new PresenceOf())
            ->add('who_can_add_track', new InclusionIn([ 'domain' => [ //message not provided
                'ANYONE', 'USERS', 'MEMBERS', 'ADMINS', 'OWNERS'
            ]]))
        ;
        return $this->validate($validator);
    }
}
