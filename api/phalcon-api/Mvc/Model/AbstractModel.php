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

use Discoveryfy\Exceptions\ModelException;
use Monolog\Logger;
use Phalcon\Filter;
use Phalcon\Mvc\Model as PhModel;

abstract class AbstractModel extends PhModel
{
    /**
     * The initialize() method is only called once during the request.
     * This method is intended to perform initializations that apply for all instances of the model created within the application.
     * If you want to perform initialization tasks for every instance created you can use the onConstruct() method.
     */
    public function initialize()
    {
        //Just the fields that had changed are used to create the final UPDATE SQL statement.
        $this->useDynamicUpdate(true);

        $this->setup(
            [
                'phqlLiterals'       => false, //Exception if bound parameters are not used
                'notNullValidations' => false, //Automatically validate the not null columns present
            ]
        );

//        parent::initialize();
    }

//    public function onConstruct() {}

//    public function beforeSave()
//    {
//        $this->validate();
//    }

    /**
     * Returns an array of all the fields/filters for this model
     *
     * @return array<string,string>
     */
    public function getAttributes(): array
    {
        //Valid, but check the diff between this and array_merge
        return $this->getPublicAttributes()+$this->getPrivateAttributes();
    }

    /**
     * Returns an array of the public fields/filters for this model
     *
     * @return array<string,string>
     */
    abstract public function getPublicAttributes(): array;

    /**
     * Returns an array of the private fields/filters for this model
     *
     * @return array<string,string>
     */
    abstract public function getPrivateAttributes(): array;

    /**
     * Gets a field from this model
     *
     * @param string $field The name of the field
     *
     * @return mixed
     * @throws ModelException
     */
    public function get($field)
    {
        return $this->getSetFields('get', $field);
    }

    /**
     * Sets a field in the model sanitized
     *
     * @param string $field The name of the field
     * @param mixed  $value The value of the field
     *
     * @return AbstractModel
     * @throws ModelException
     */
    public function set($field, $value): AbstractModel
    {
        $this->getSetFields('set', $field, $value);

        return $this;
    }

    /**
     * Gets or sets a field and sanitizes it if necessary
     *
     * @param string $type
     * @param string $field
     * @param mixed  $value
     *
     * @return mixed
     * @throws ModelException
     */
    private function getSetFields(string $type, string $field, $value = '')
    {
        $return      = null;
        $modelFields = $this->getAttributes();
        $filter      = $modelFields[$field] ?? false;

        if (false === $filter) {
            throw new ModelException(
                sprintf(
                    'Field [%s] not found in this model',
                    $field
                )
            );
        }

        if ('get' === $type) {
            $return = $this->sanitize($this->$field, $filter);
        } else {
            $this->$field = $this->sanitize($value, $filter);
        }

        return $return;
    }

    /**
     * Uses the Phalcon Filter to sanitize the variable passed
     *
     * @param mixed        $value  The value to sanitize
     * @param string|array $filter The filter to apply
     *
     * @return mixed
     */
    private function sanitize($value, $filter)
    {
        /** @var Filter $filterService */
        $filterService = $this->getDI()->get('filter');

        return $filterService->sanitize($value, $filter);
    }

    /**
     * Returns model messages as one string separated with PHP_EOL char
     *
     * @param Logger|null $logger
     *
     * @return  string
     */
    public function getMessage(Logger $logger = null): string
    {
        $error = '';
        foreach ($this->getMessages() as $message) {
            $error .= $message->getMessage() . PHP_EOL;
            if (null !== $logger) {
                $logger->error($message->getMessage());
            }
        }

        return $error;
    }
}
