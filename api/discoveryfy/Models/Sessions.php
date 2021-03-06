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
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Class Sessions
 *
 * @see https://docs.phalcon.io/4.0/en/db-models
 * @see https://docs.phalcon.io/4.0/en/db-models-relationships
 * @see https://docs.phalcon.io/4.0/en/api/phalcon_mvc
 * @package Discoveryfy\Models
 */
class Sessions extends TimestampableModel
{
    // Private attributes
    protected $id;
    protected $user_id;
    // Public attributes
    protected $name;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $this->setSource('sessions');

        //References to users, votes & tracks
        $this->belongsTo('user_id', Users::class, 'id', [
            'alias'    => 'user',
            'reusable' => true,
        ]);
        $this->hasMany('id', Votes::class, 'session_id', [
            'alias'    => 'votes',
            'reusable' => true,
        ]);
        $this->hasMany('id', Tracks::class, 'session_id', [
            'alias'    => 'tracks',
            'reusable' => true,
        ]);

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
//                'user_id' => 'userId'
//            ];
//    }

    /**
     * @return array<string,string>
     */
    public function getPrivateAttributes(): array
    {
        return [
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
            'name'              => Filter::FILTER_STRING,
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
        ;
        return $this->validate($validator);
    }
}
