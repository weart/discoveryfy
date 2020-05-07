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
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Class Tracks
 *
 * @package Discoveryfy\Models
 */
class Tracks extends TimestampableModel
{

    /**
     * Returns the source table from the database
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('tracks');

        parent::initialize();
    }

    /**
     * @return array<string,string>
     */
    public function getPrivateAttributes(): array
    {
        return [
            'poll_id'           => UUIDFilter::FILTER_NAME,
            'session_id'        => UUIDFilter::FILTER_NAME,
            'user_id'           => UUIDFilter::FILTER_NAME,
        ];
    }

    /**
     * @return array<string,string>
     */
    public function getPublicAttributes(): array
    {
        return [
            'id'                => UUIDFilter::FILTER_NAME,
            'created_at'        => Filter::FILTER_STRING,
            'updated_at'        => Filter::FILTER_STRING,
            'artist'            => Filter::FILTER_STRING,
            'name'              => Filter::FILTER_STRING,
            'spotify_uri'       => Filter::FILTER_STRING,
            'spotify_images'    => Filter::FILTER_STRING, //array, saved in json
            'youtube_uri'       => Filter::FILTER_STRING
        ];
    }

    /**
     * @return bool
     */
    public function validation(): bool
    {
        $validator = (new Validation())
            ->add('id', new Uniqueness([
                'message' => 'The id already exists in the database',
            ]))
            ->add('id', new UuidValidator())
            ->add('poll_id', new UuidValidator())
            ->add('session_id', new UuidValidator())
            ->add('user_id', new UuidValidator([
                'allowEmpty' => true
            ]))
            ->add('artist', new PresenceOf()) //AlnumValidator?
            ->add('name', new PresenceOf())

            //test spotify  & youtube uri?
            //remove 'spotify:playlist:' from field?
        ;
        return $this->validate($validator);
    }
}
