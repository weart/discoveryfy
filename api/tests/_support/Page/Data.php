<?php

namespace Page;

//use Discoveryfy\Constants\Relationships;
use Discoveryfy\Exceptions\ModelException;
use Phalcon\Api\Mvc\Model\AbstractModel;
use Phalcon\Security\Random;
use function Phalcon\Api\Core\envValue;

class Data
{
    public static $loginUrl             = '/login';
    public static $registerUrl          = '/register';
    public static $usersUrl             = '/users/%s';
    public static $sessionsUrl          = '/sessions/%s';
    public static $groupsUrl            = '/groups';
    public static $groupUrl             = '/groups/%s';
    public static $membersUrl           = '/groups/%s/members';
    public static $memberUrl            = '/groups/%s/members/%s';
    public static $groupPollsUrl        = '/groups/%s/polls';
    public static $pollsUrl             = '/polls';
    public static $pollUrl              = '/polls/%s';
    public static $pollTracksUrl        = '/polls/%s/tracks';
    public static $pollTrackUrl         = '/polls/%s/tracks/%s';
    public static $pollTrackRateUrl     = '/polls/%s/tracks/%s/rate';
    public static $wrongUrl             = '/sommething';

//    public static $companiesSortUrl                 = '/companies?sort=%s';
//    public static $companiesRecordIncludesUrl       = '/companies/%s?includes=%s';

    /**
     * @return array
     */
    public static function loginJson()
    {
        return [
            'username' => 'testuser',
            'password' => 'testpassword',
        ];
    }

    public static function registerJson()
    {
        return [
            'username'          => 'test_'.(new Random())->hex(5),
            'password'          => 'test_'.(new Random())->hex(5),
            'email'             => 'test_'.(new Random())->hex(5).'@test.com',
            'public-visibility' => true,
            'public-email'      => true,
            'language'          => 'en',
            'theme'             => 'default',
            'rol'               => 'ROLE_USER',
        ];
    }

    public static function groupJson()
    {
        return [
            'name'                  => 'test_'.(new Random())->hex(5),
            'description'           => 'test_'.(new Random())->hex(5),
            'public_visibility'     => false,
            'public_membership'     => false,
            'who_can_create_polls'  => 'OWNERS',
        ];
    }

    /**
     * @param        $name
     * @param string $address
     * @param string $city
     * @param string $phone
     *
     * @return array
     */
//    public static function companyAddJson($name, $address = '', $city = '', $phone = '')
//    {
//        return [
//            'name'    => $name,
//            'address' => $address,
//            'city'    => $city,
//            'phone'   => $phone,
//        ];
//    }

    /**
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
//    public static function companiesResponse(AbstractModel $record)
//    {
//        return [
//            'id'         => $record->get('id'),
//            'type'       => Relationships::COMPANIES,
//            'attributes' => [
//                'name'    => $record->get('name'),
//                'address' => $record->get('address'),
//                'city'    => $record->get('city'),
//                'phone'   => $record->get('phone'),
//            ],
//            'links'      => [
//                'self' => sprintf(
//                    '%s/%s/%s',
//                    envValue('APP_URL'),
//                    Relationships::COMPANIES,
//                    $record->get('id')
//                ),
//            ],
//        ];
//    }

    /**
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
//    public static function individualResponse(AbstractModel $record)
//    {
//        return [
//            'id'         => $record->get('id'),
//            'type'       => Relationships::INDIVIDUALS,
//            'attributes' => [
//                'companyId' => $record->get('companyId'),
//                'typeId'    => $record->get('typeId'),
//                'prefix'    => $record->get('prefix'),
//                'first'     => $record->get('first'),
//                'middle'    => $record->get('middle'),
//                'last'      => $record->get('last'),
//                'suffix'    => $record->get('suffix'),
//            ],
//            'links'      => [
//                'self' => sprintf(
//                    '%s/%s/%s',
//                    envValue('APP_URL'),
//                    Relationships::INDIVIDUALS,
//                    $record->get('id')
//                ),
//            ],
//        ];
//    }

    /**
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
//    public static function individualTypeResponse(AbstractModel $record)
//    {
//        return [
//            'id'         => $record->get('id'),
//            'type'       => Relationships::INDIVIDUAL_TYPES,
//            'attributes' => [
//                'name'        => $record->get('name'),
//                'description' => $record->get('description'),
//            ],
//            'links'      => [
//                'self' => sprintf(
//                    '%s/%s/%s',
//                    envValue('APP_URL'),
//                    Relationships::INDIVIDUAL_TYPES,
//                    $record->get('id')
//                ),
//            ],
//        ];
//    }

    /**
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
//    public static function productResponse(AbstractModel $record)
//    {
//        return [
//            'type'       => Relationships::PRODUCTS,
//            'id'         => $record->get('id'),
//            'attributes' => [
//                'typeId'      => $record->get('typeId'),
//                'name'        => $record->get('name'),
//                'description' => $record->get('description'),
//                'quantity'    => $record->get('quantity'),
//                'price'       => $record->get('price'),
//            ],
//            'links'      => [
//                'self' => sprintf(
//                    '%s/%s/%s',
//                    envValue('APP_URL'),
//                    Relationships::PRODUCTS,
//                    $record->get('id')
//                ),
//            ],
//        ];
//    }

    /**
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
//    public static function productFieldsResponse(AbstractModel $record)
//    {
//        return [
//            'type'       => Relationships::PRODUCTS,
//            'id'         => $record->get('id'),
//            'attributes' => [
//                'name'        => $record->get('name'),
//                'price'       => $record->get('price'),
//            ],
//            'links'      => [
//                'self' => sprintf(
//                    '%s/%s/%s',
//                    envValue('APP_URL'),
//                    Relationships::PRODUCTS,
//                    $record->get('id')
//                ),
//            ],
//        ];
//    }

    /**
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
//    public static function productTypeResponse(AbstractModel $record)
//    {
//        return [
//            'id'         => $record->get('id'),
//            'type'       => Relationships::PRODUCT_TYPES,
//            'attributes' => [
//                'name'        => $record->get('name'),
//                'description' => $record->get('description'),
//            ],
//            'links'      => [
//                'self' => sprintf(
//                    '%s/%s/%s',
//                    envValue('APP_URL'),
//                    Relationships::PRODUCT_TYPES,
//                    $record->get('id')
//                ),
//            ],
//        ];
//    }

    /**
     * @param AbstractModel $record
     *
     * @return array
     * @throws ModelException
     */
    public static function userResponse(AbstractModel $record)
    {
        return [
            'id'         => $record->get('id'),
            'type'       => Relationships::USER,
            'attributes' => [
                'status'        => $record->get('status'),
                'username'      => $record->get('username'),
                'issuer'        => $record->get('issuer'),
                'tokenPassword' => $record->get('tokenPassword'),
                'tokenId'       => $record->get('tokenId'),
            ],
            'links'      => [
                'self' => sprintf(
                    '%s/%s/%s',
                    envValue('APP_URL'),
                    Relationships::USER,
                    $record->get('id')
                ),
            ],
        ];
    }
}
