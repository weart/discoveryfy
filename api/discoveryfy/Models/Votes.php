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
//use Phalcon\Validation\Validator\Numericality;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\Uniqueness;

/**
 * Class Votes
 *
 * @package Discoveryfy\Models
 */
class Votes extends TimestampableModel
{
    /**
     * Returns the source table from the database
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->setSource('votes');

        parent::initialize();
    }

    /**
     * @return array<string,string>
     */
    public function getPrivateAttributes(): array
    {
        return [
            'poll_id'       => UUIDFilter::FILTER_NAME,
            'track_id'      => UUIDFilter::FILTER_NAME,
            'session_id'    => UUIDFilter::FILTER_NAME,
            'user_id'       => UUIDFilter::FILTER_NAME,
        ];
    }

    /**
     * @return array<string,string>
     */
    public function getPublicAttributes(): array
    {
        return [
            'id'            => UUIDFilter::FILTER_NAME,
            'created_at'    => Filter::FILTER_STRING,
            'updated_at'    => Filter::FILTER_STRING,
            'rate'          => Filter::FILTER_ABSINT
        ];
    }

    /**
     * @return bool
     */
    public function validation(): bool
    {
        //@ToDo: Check poll->has anon_can_vote & membership
        //@ToDo: Check anon_votes_max_rating & user_votes_max_rating
        $validator = (new Validation())
            ->add('id', new Uniqueness([
                'message' => 'The id already exists in the database',
            ]))
            ->add('id', new UuidValidator())
            ->add('poll_id', new UuidValidator())
            ->add('track_id', new UuidValidator())
            ->add('session_id', new UuidValidator())
            ->add('user_id', new UuidValidator([
                'allowEmpty' => true
            ]))
//            ->add('rate', new Numericality([
//                'message' => ':field is not numeric',
//            ]))
            ->add('rate', new Between([
                'minimum' => 0,
                'maximum' => 100, //Mandatory? Should be taken from poll...
//                'message' => 'The price must be between 0 and 100',
            ]))
        ;
        return $this->validate($validator);
    }
}
