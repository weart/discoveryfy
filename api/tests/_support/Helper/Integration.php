<?php

namespace Helper;

use Codeception\Module;
use Codeception\Exception\TestRuntimeException;
use Codeception\TestInterface;

//use Discoveryfy\Models\Companies;
//use Discoveryfy\Models\CompaniesXProducts;
//use Discoveryfy\Models\Individuals;
//use Discoveryfy\Models\IndividualTypes;
//use Discoveryfy\Models\Products;
//use Discoveryfy\Models\ProductTypes;
use Discoveryfy\Models\Users;
use Phalcon\Api\Bootstrap\Api;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Config as PhConfig;
use Phalcon\DI\FactoryDefault as PhDI;
use Phalcon\Security\Random;
use Phalcon\Security;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Integration extends Module
{
    /**
     * @var null|PhDI
     */
    protected $diContainer  = null;
    protected $savedModels  = [];
    protected $savedRecords = [];
    protected $config       = ['rollback' => false];

    /**
     * Test initializer
     */
    public function _before(TestInterface $test)
    {
        PhDI::reset();

        $app = new Api();
        $app->setup();
        $this->diContainer = $app->getContainer();

        if ($this->config['rollback']) {
            $this->diContainer->get('db')->begin();
        }
        $this->savedModels  = [];
        $this->savedRecords = [];
    }

    public function _after(TestInterface $test)
    {
        if (!$this->config['rollback']) {
            foreach ($this->savedRecords as $record) {
                $record->delete();
            }
        } else {
            $this->diContainer->get('db')->rollback();
        }
        $this->diContainer->get('db')->close();
    }

    public function getDefaultModel(): string
    {
        return Users::class;
    }

    public function getDefaultModelAttributes(array $whitelist = [], array $blacklist = []): array
    {
        $rtn = $this->getKnownUserAttributes();
        if (empty($whitelist) && empty($blacklist)) {
            return $rtn;
        }
        if (!empty($blacklist)) {
            $rtn = array_filter($rtn, function ($key) use ($blacklist) {
                return !in_array($key, $blacklist, true);
            }, ARRAY_FILTER_USE_KEY);
        }
        if (!empty($whitelist)) {
            $rtn = array_filter($rtn, function ($key) use ($whitelist) {
                return in_array($key, $whitelist, true);
            }, ARRAY_FILTER_USE_KEY);
        }
        return $rtn;
    }

    private function getKnownUserAttributes()
    {
        $security = $this->getSecurityService();
        return [
            'id'                => 'f756ffbe-6143-46f0-b914-f898b1f05f84',
            'enabled'           => true,
            'created_at'        => '2020-03-18 17:43:09',
            'updated_at'        => null,
//            'deleted_at'        => null,
            'username'          => 'testuser',
            'password'          => $security->hash('testpassword'),
            'email'             => 'test@user.net',
            'public_visibility' => false,
            'public_email'      => false,
            'language'          => 'en',
            'theme'             => 'default',
            'rol'               => 'ROLE_USER',
        ];
    }

    /**
     * Taken from SecurityProvider
     */
    private function getSecurityService(): Security
    {
        $security = new Security();
        // set Work factor (how many times we go through)
        $security->setWorkFactor(12); // can be a number from 1-12
        // set Default Hash
        $security->setDefaultHash(Security::CRYPT_BLOWFISH_Y); // choose default hash
        return $security;
    }

    public function getRandomUserAttributes()
    {
        $datetime = new \Datetime('now', new \DateTimeZone('UTC'));
        return [
            'id'                => (new Random())->uuid(),
            'enabled'           => true,
            'created_at'        => $datetime->format('Y-m-d H:i:s'),
            'updated_at'        => $datetime->format('Y-m-d H:i:s'),
            'username'          => 'test_'.(new Random())->hex(5),
            'password'          => 'test_'.(new Random())->hex(5),
            'email'             => 'test_'.(new Random())->hex(5).'@test.com',
            'public_visibility' => true,
            'public_email'      => true,
            'language'          => 'en',
            'theme'             => 'default',
            'rol'               => 'ROLE_USER',
        ];
    }

    public function getKnownGroupAttributes()
    {
        return [
            'id'                    => '52bff0b0-4d4f-4df7-b55a-d55dbac5d033', //Fixed id in seeder
//            'created_at'            => $datetime->format('Y-m-d H:i:s'),
//            'updated_at'            => $datetime->format('Y-m-d H:i:s'),
            'name'                  => 'Discoveryfy Testing Group',
            'description'           => 'Nothing to see here, this is just for testing',
            'public_visibility'     => false,
            'public_membership'     => false,
            'who_can_create_polls'  => 'OWNERS',
        ];
    }


    /**
     * @param string $namePrefix
     * @param string $addressPrefix
     * @param string $cityPrefix
     * @param string $phonePrefix
     *
     * @return Companies
     */
//    public function addCompanyRecord(
//        string $namePrefix = '',
//        string $addressPrefix = '',
//        string $cityPrefix = '',
//        string $phonePrefix = ''
//    ) {
//        return $this->haveRecordWithFields(
//            Companies::class,
//            [
//                'name'    => uniqid($namePrefix),
//                'address' => uniqid($addressPrefix),
//                'city'    => uniqid($cityPrefix),
//                'phone'   => uniqid($phonePrefix),
//            ]
//        );
//    }

    /**
     * @param int $companyId
     * @param int $productId
     *
     * @return CompaniesXProducts
     */
//    public function addCompanyXProduct(int $companyId, int $productId)
//    {
//        return $this->haveRecordWithFields(
//            CompaniesXProducts::class,
//            [
//                'companyId' => $companyId,
//                'productId' => $productId,
//            ]
//        );
//    }

    /**
     * @param string $namePrefix
     *
     * @return IndividualTypes
     */
//    public function addIndividualTypeRecord(string $namePrefix = '')
//    {
//        return $this->haveRecordWithFields(
//            IndividualTypes::class,
//            [
//                'name'        => uniqid($namePrefix),
//                'description' => uniqid(),
//            ]
//        );
//    }

    /**
     * @param string $namePrefix
     * @param int    $comId
     * @param int    $typeId
     *
     * @return Individuals
     */
//    public function addIndividualRecord(string $namePrefix = '', int $comId = 0, int $typeId = 0)
//    {
//        return $this->haveRecordWithFields(
//            Individuals::class,
//            [
//                'companyId' => $comId,
//                'typeId'    => $typeId,
//                'prefix'    => uniqid(),
//                'first'     => uniqid($namePrefix),
//                'middle'    => uniqid(),
//                'last'      => uniqid(),
//                'suffix'    => uniqid(),
//            ]
//        );
//    }

    /**
     * @param string $namePrefix
     * @param int    $typeId
     *
     * @return Products
     */
//    public function addProductRecord(string $namePrefix = '', int $typeId = 0)
//    {
//        return $this->haveRecordWithFields(
//            Products::class,
//            [
//                'name'        => uniqid($namePrefix),
//                'typeId'      => $typeId,
//                'description' => uniqid(),
//                'quantity'    => 25,
//                'price'       => 19.99,
//            ]
//        );
//    }

    /**
     * @param string $namePrefix
     *
     * @return ProductTypes
     */
//    public function addProductTypeRecord(string $namePrefix = '')
//    {
//        return $this->haveRecordWithFields(
//            ProductTypes::class,
//            [
//                'name'        => uniqid($namePrefix),
//                'description' => uniqid(),
//            ]
//        );
//    }

    /**
     * @return mixed
     */
    public function grabDi()
    {
        return $this->diContainer;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function grabFromDi(string $name)
    {
        return $this->diContainer->get($name);
    }

    /**
     * Returns the relationships that a model has
     *
     * @param string $class
     *
     * @return array
     */
    public function getModelRelationships(string $class): array
    {
        /** @var AbstractModel $class */
        $model         = new $class();
        $manager       = $model->getModelsManager();
        $relationships = $manager->getRelations($class);

        $data = [];
        foreach ($relationships as $relationship) {
            $data[] = [
                $relationship->getType(),
                $relationship->getFields(),
                $relationship->getReferencedModel(),
                $relationship->getReferencedFields(),
                $relationship->getOptions(),
            ];
        }

        return $data;
    }

    /**
     * Get a record from $modelName with fields provided
     *
     * @param string $modelName
     * @param array  $fields
     *
     * @return bool|AbstractModel
     */
    public function getRecordWithFields(string $modelName, $fields = [])
    {
        $record = false;
        if (count($fields) > 0) {
            $conditions = '';
            $bind       = [];
            foreach ($fields as $field => $value) {
                $conditions   .= sprintf(
                    '%s = :%s: AND ',
                    $field,
                    $field
                );
                $bind[$field] = $value;
            }

            $conditions = rtrim($conditions, ' AND ');

            /** @var AbstractModel $record */
            $record = $modelName::findFirst(
                [
                    'conditions' => $conditions,
                    'bind'       => $bind,
                ]
            );
        }

        return $record;
    }

    /**
     * @param array $configData
     */
    public function haveConfig(array $configData)
    {
        $config = new PhConfig($configData);
        $this->diContainer->set('config', $config);
    }

    /**
     * Checks model fields
     *
     * @param string $modelName
     * @param array  $fields
     */
    public function haveModelDefinition(string $modelName, array $fields)
    {
        /** @var AbstractModel $model */
        $model      = new $modelName;
        $metadata   = $model->getModelsMetaData();
        $attributes = $metadata->getAttributes($model);

        $this->assertEquals(
            count($fields),
            count($attributes),
            "Field count not correct for $modelName"
        );

        foreach ($fields as $value) {
            $this->assertContains(
                $value,
                $attributes,
                "Field not exists in $modelName"
            );
        }
    }

    /**
     * Create a record for $modelName with fields provided
     *
     * @param string $modelName
     * @param array  $fields
     *
     * @return mixed
     */
    public function haveRecordWithFields(string $modelName, array $fields = [])
    {
        $record = new $modelName;
        foreach ($fields as $key => $val) {
            $record->set($key, $val);
        }

        $result = $record->save();
        $this->savedModels[$modelName] = $fields;
        $this->assertNotSame(false, $result);

        $this->savedRecords[] = $record;

        return $record;
    }

    /**
     * @param string $name
     * @param mixed  $service
     */
    public function haveService(string $name, $service)
    {
        $this->diContainer->set($name, $service);
    }

    /**
     * @param string $name
     */
    public function removeService(string $name)
    {
        if ($this->diContainer->has($name)) {
            $this->diContainer->remove($name);
        }
    }

    /**
     * Check that record created with haveRecordWithFields can be fetched and
     * all its fields contain valid values
     *
     * @param       $modelName
     * @param       $by
     * @param array $except
     *
     * @return mixed
     */
    public function seeRecordFieldsValid($modelName, $by, array $except = [])
    {
        if (!isset($this->savedModels[$modelName])) {
            throw new TestRuntimeException(
                'Should be used after haveModelWithFields with ' . $modelName
            );
        }
        $fields = $this->savedModels[$modelName];
        if (!is_array($by)) {
            $by = [$by];
        }
        $bySelector = implode(
            ' AND ',
            array_map(
                function ($key) {
                    return "$key = :$key:";
                },
                $by
            )
        );
        $byBind     = [];
        foreach ($by as $byVal) {
            if (!isset($fields[$byVal])) {
                throw new TestRuntimeException("Field $byVal is not set");
            }
            $byBind[$byVal] = $fields[$byVal];
        }
        $record = call_user_func(
            [
                $modelName, 'findFirst',
            ],
            [
                'conditions' => $bySelector,
                'bind'       => $byBind,
            ]
        );
        if (!$record) {
            $this->fail("Record $modelName for $by not found");
        }

        foreach ($fields as $key => $val) {
            if (isset($except[$key])) {
                continue;
            }
            $this->assertEquals(
                $val,
                $record->get($key),
                "Field in $modelName for $key not valid"
            );
        }

        return $record;
    }

    /**
     * Checks that record exists and has provided fields
     *
     * @param $model
     * @param $by
     * @param $fields
     */
    public function seeRecordSaved($model, $by, $fields)
    {
        $this->savedModels[$model] = array_merge($by, $fields);
        $record                    = $this->seeRecordFieldsValid(
            $model,
            array_keys($by),
            array_keys($by)
        );
        $this->savedRecords[]      = $record;
    }
}
