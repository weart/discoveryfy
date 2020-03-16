<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Mvc\Model;

use Discoveryfy\Exceptions\InternalServerErrorException;
use Phalcon\Config;
use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model\Behavior\Timestampable;

abstract class TimestampableModel extends AbstractModel
{
    protected $timestamps_from_db;
//    protected $timezone;

    protected $created_at;
    protected $updated_at;

    /**
     * The initialize() method is only called once during the request.
     * This method is intended to perform initializations that apply for all instances of the model created within the application.
     * If you want to perform initialization tasks for every instance created you can use the onConstruct() method.
     */
    public function initialize()
    {
        /** @var Config $config */
        $config = $this->getDI()->get('config');
        $this->timestamps_from_db = (bool) $config->path('db.timestamps_from_db', true);
//        $this->timezone = $config->path('app.timezone', 'UTC');

        if ($this->timestamps_from_db) {
            //Sets a list of attributes that must be skipped from the generated INSERT/UPDATE statement
            $this->skipAttributes(
                [
                    'created_at',
                    'updated_at',
                ]
            );
            if ($this->getDirtyState() == self::DIRTY_STATE_DETACHED) {
                $this->created_at = new RawValue('default'); //By default: CURRENT_TIMESTAMP
                $this->updated_at = new RawValue('default'); //By default: null
            }

        } else {
//            $datetime = new \Datetime('now', new \DateTimeZone($this->timezone));
            $this->addBehavior(
                new Timestampable(
                    [
                        'beforeCreate' => [ //onCreate in Phalcon V3?
                            'field'  => 'created_at',
                            'format' => 'Y-m-d H:i:s',
//                            'format' => $datetime->format('r'),
                        ],
                        'beforeUpdate' => [ //onUpdate in Phalcon V3?
                            'field'  => 'updated_at',
                            'format' => 'Y-m-d H:i:s',
                        ]
                    ]
                )
            );
        }

        parent::initialize();
    }

//    public function onConstruct() {}

    /**
     * Column Mapping: Property rename them to match the respective columns in the database
     *
     * @return array
     */
//    public function columnMap(): array
//    {
//        return [
//            'created_at' => 'createdAt',
//            'updated_at' => 'updatedAt'
//        ];
//    }

    public function getCreatedAt(): \DateTime
    {
        if ($this->created_at instanceof RawValue) {
            throw new InternalServerErrorException('Field only available after persist the object in the db');
        }
        return new \DateTime($this->created_at);
    }

    public function getUpdatedAt(): \DateTime
    {
        if ($this->updated_at instanceof RawValue) {
            throw new InternalServerErrorException('Field only available after persist the object in the db');
        }
        //Check getDirtyState() function?
        return new \DateTime($this->updated_at);
    }

}
