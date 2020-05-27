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
    public static $spotifyUrl           = '/spotify';
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

    public static function loginAltJson()
    {
        return [
            'username' => 'testaltuser',
            'password' => 'testaltpassword',
        ];
    }

    public static function registerJson()
    {
        return [
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

    public static function pollJson()
    {
        $now = new \DateTime();
        $date_format = 'Y-m-d H:i:s';

        return [
            'name'                              => 'test_'.(new Random())->hex(5),
            'description'                       => 'test_'.(new Random())->hex(5),
//            'spotify_playlist_images'           => Filter::FILTER_STRING, //array, saved in json
//            'spotify_playlist_public'           => Filter::FILTER_BOOL,
//            'spotify_playlist_collaborative'    => Filter::FILTER_BOOL,
//            'spotify_playlist_uri'              => Filter::FILTER_STRING,
//            'spotify_playlist_winner_uri'       => Filter::FILTER_STRING,
//            'spotify_playlist_historic_uri'     => Filter::FILTER_STRING,
            'start_date'                        => $now->add(new \DateInterval('P2D'))->format($date_format),
            'end_date'                          => $now->add(new \DateInterval('P2M'))->format($date_format),
//            'restart_date'                      => '',
            'public_visibility'                 => false,
            'public_votes'                      => false,
            'anon_can_vote'                     => false,
            'who_can_add_track'                 => 'OWNERS',
            'anon_votes_max_rating'             => 0,
            'user_votes_max_rating'             => 1,
            'multiple_user_tracks'              => true,
            'multiple_anon_tracks'              => false,
        ];
    }

    public static function trackJson()
    {
        $tracks_schema = [
            [
                'artist' => 'Toti Soler',
                'name' => 'Em Dius Que El Nostre Amor',
                'youtube_uri' => 'rd55dcyjCSY',
                'spotify_uri' => '5o31tm7aa5PdsThhw36it9',
            ],[
                'artist' => 'Queen',
                'name' => 'I Was Born To Love You - 2011 Remaster',
                'spotify_uri' => '7DtdhIJlSSOaAFNk4JdXCb',
            ],[
                'artist' => 'Queen',
                'name' => 'Hammer To Fall - 2011 Remaster',
                'spotify_uri' => '61lj5cHhOifNzSMXuWg54Z',
            ],[
                'artist' => 'Giacomo Puccini',
                'name' => 'Turandot / Act 3: Nessun dorma!',
                'spotify_uri' => '74WjYdm3Lvbwnds4thYPUU',
            ],[
                'artist' => 'Zoufris Maracas',
                'name' => 'Cocagne',
                'spotify_uri' => '7FzdSwzenyUeiO6Dld2Y3v',
            ],[
                'artist' => 'Rozalén',
                'name' => 'La Puerta Violeta',
                'spotify_uri' => '60kCg3tKhxb61QbBOFVzXh',
            ],[
                'artist' => 'The Gramophone Allstars',
                'name' => 'I Wish I Knew How It Would Feel To Be Free',
                'spotify_uri' => '00POrfLzrW7hEtyAi1IeeM',
            ],[
                'artist' => 'Tokyo Ska Paradise Orchestra',
                'name' => '水琴窟-SUIKINKUTSU-',
                'spotify_uri' => '6fOGYN4YLmaA5Yr6GjcDYV',
            ]
        ];
        $random_track = rand(0, (count($tracks_schema)-1));
        return $tracks_schema[$random_track];
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
//            'type'       => Relationships::PRODUCT_TYPES,
//            'id'         => $record->get('id'),
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
            'type'       => Relationships::USER,
            'id'         => $record->get('id'),
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

    public static function registerResponseJsonType(): array
    {
        return [
            'id'                            => 'string:!empty', //'016aeb55-7ecf-4862-a229-dd7478b17537'
            'attributes.created_at'         => 'string:date', //'2020-03-23 11:57:46'
//            'attributes.updated_at'         => 'string:date|null', //''
            'links.self'                    => 'string:url', //'https://api.discoveryfy.fabri...b17537'
        ];
    }

    public static function registerResponseJsonApiType(): array
    {
        return [
            'id'                            => 'string:!empty', //'016aeb55-7ecf-4862-a229-dd7478b17537'
            'attributes' => [
                'email'                     => 'string:email',
                'created_at'                => 'string:date', //'2020-03-23 11:57:46'
//                'updated_at'                => 'string:date|null', //''
            ],
            'links' => [
                'self'                      => 'string:url', //'https://api.discoveryfy.fabri...b17537'
            ]
        ];
    }

    public static function sessionResponseJsonType(): array
    {
        return [
            'type'                  => 'string:!empty',
            'id'                    => 'string:!empty',
            'attributes.created_at' => 'string:date',
            'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'       => 'string',
            'links.self'            => 'string:url',
        ];
    }

    public static function sessionResponseJsonApiType(): array
    {
        return [
            'type'              => 'string:!empty',
            'id'                => 'string:!empty',
            'attributes'        => [
                'created_at'    => 'string:date',
                'updated_at'    => 'string:date|string', //When is empty is not null... is an empty string
                'name'          => 'string',
            ],
            'links'             => [
                'self'          => 'string:url'
            ],
        ];
    }

    public static function userResponseJsonType(): array
    {
        return [
            'type'                  => 'string:!empty',
            'id'                    => 'string:!empty',
            'attributes.created_at' => 'string:date',
            'attributes.updated_at' => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.username'   => 'string:!empty',
            'attributes.email'      => 'string:email',
            'attributes.language'   => 'string:!empty',
            'attributes.theme'      => 'string:!empty',
            'attributes.rol'        => 'string:!empty',
            'links.self'            => 'string:url',
        ];
    }

    public static function userResponseJsonApiType(): array
    {
        return [
            'type'          => 'string:!empty',
            'id'            => 'string:!empty',
            'attributes'    => [
                'created_at'    => 'string:date',
                'updated_at'    => 'string:date|string', //When is empty is not null... is an empty string
                'username'      => 'string:!empty',
                'email'         => 'string:email',
                'language'      => 'string:!empty',
                'theme'         => 'string:!empty',
                'rol'           => 'string:!empty',
            ],
            'links'         => [
                'self'          => 'string:url',
            ],
        ];
    }

    public static function groupResponseJsonType(): array
    {
        return [
            'type'                              => 'string:!empty',
            'id'                                => 'string:!empty',
            'attributes.created_at'             => 'string:date',
            'attributes.updated_at'             => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'                   => 'string:!empty',
            'attributes.description'            => 'string',
            'attributes.public_visibility'      => 'boolean',
            'attributes.public_membership'      => 'boolean',
            'attributes.who_can_create_polls'   => 'string',
            'links.self'                        => 'string:url',
        ];
    }

    public static function groupResponseJsonApiType(): array
    {
        return [
            'type'                          => 'string:!empty',
            'id'                            => 'string:!empty',     //'016aeb55-7ecf-4862-a229-dd7478b17537'
            'attributes' => [
                'created_at'                => 'string:date',       //'2020-03-23 11:57:46'
                'updated_at'                => 'string:date|string', //When is empty is not null... is an empty string
                'name'                      => 'string:!empty',
                'description'               => 'string',
                'public_visibility'         => 'boolean',
                'public_membership'         => 'boolean',
                'who_can_create_polls'      => 'string',
            ],
            'links' => [
                'self'                      => 'string:url', //'https://api.discoveryfy.fabri...b17537'
            ]
        ];
    }

    public static function memberResponseJsonType(): array
    {
        return [
            'type'                              => 'string:!empty',
            'id'                                => 'string:!empty',
            //organization_id
            'attributes.created_at'             => 'string:date',
            'attributes.updated_at'             => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.rol'                    => 'string:!empty',
            'links.self'                        => 'string:url',
        ];
    }

    public static function memberResponseJsonApiType(): array
    {
        return [
            'type'                          => 'string:!empty',
            'id'                            => 'string:!empty',
            'attributes' => [
                'created_at'                => 'string:date',
                'updated_at'                => 'string:date|string', //When is empty is not null... is an empty string
                'rol'                      => 'string:!empty',
            ],
            'links' => [
                'self'                      => 'string:url', //'https://api.discoveryfy.fabri...b17537'
            ]
        ];
    }

    public static function pollResponseJsonType(): array
    {
        return [
            'type'                                      => 'string:!empty',
            'id'                                        => 'string:!empty',
            'attributes.created_at'                     => 'string:date',
            'attributes.updated_at'                     => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.name'                           => 'string:!empty',
            'attributes.description'                    => 'string:!empty',
            'attributes.spotify_playlist_images'        => 'array|null|string', //@ToDo: All Spotify attributes should be !empty
            'attributes.spotify_playlist_public'        => 'boolean',
            'attributes.spotify_playlist_collaborative' => 'boolean',
            'attributes.spotify_playlist_uri'           => 'string',
            'attributes.spotify_playlist_winner_uri'    => 'string',
            'attributes.spotify_playlist_historic_uri'  => 'string',
            'attributes.start_date'                     => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.end_date'                       => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.restart_date'                   => 'string',
            'attributes.public_visibility'              => 'boolean',
            'attributes.public_votes'                   => 'boolean',
            'attributes.anon_can_vote'                  => 'boolean',
            'attributes.who_can_add_track'              => 'string:!empty',
            'attributes.anon_votes_max_rating'          => 'integer',
            'attributes.user_votes_max_rating'          => 'integer',
            'attributes.multiple_user_tracks'           => 'boolean',
            'attributes.multiple_anon_tracks'           => 'boolean',
            'links.self'                                => 'string:url',
        ];
    }

    public static function pollResponseJsonApiType(): array
    {
        return [
            'type'                                  => 'string:!empty',
            'id'                                    => 'string:!empty',
            'attributes' => [
                'created_at'                        => 'string:date',
                'updated_at'                        => 'string:date|string', //When is empty is not null... is an empty string
                'name'                              => 'string:!empty',
                'description'                       => 'string:!empty',
                'spotify_playlist_images'           => 'array|null|string', //@ToDo
                'spotify_playlist_public'           => 'boolean',
                'spotify_playlist_collaborative'    => 'boolean',
                'spotify_playlist_uri'              => 'string',
                'spotify_playlist_winner_uri'       => 'string',
                'spotify_playlist_historic_uri'     => 'string',
                'start_date'                        => 'string:date|string', //When is empty is not null... is an empty string
                'end_date'                          => 'string:date|string', //When is empty is not null... is an empty string
                'restart_date'                      => 'string',
                'public_visibility'                 => 'boolean',
                'public_votes'                      => 'boolean',
                'anon_can_vote'                     => 'boolean',
                'who_can_add_track'                 => 'string:!empty',
                'anon_votes_max_rating'             => 'integer',
                'user_votes_max_rating'             => 'integer',
                'multiple_user_tracks'              => 'boolean',
                'multiple_anon_tracks'              => 'boolean'
            ],
            'links' => [
                'self'                              => 'string:url',
            ]
        ];
    }

    public static function trackResponseJsonType(): array
    {
        return [
            'type'                                  => 'string:!empty',
            'id'                                    => 'string:!empty',
            'attributes.created_at'                 => 'string:date',
            'attributes.updated_at'                 => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.artist'                     => 'string:!empty',
            'attributes.name'                       => 'string:!empty',
            'attributes.spotify_uri'                => 'string',
            'attributes.spotify_images'             => 'array|null|string', //@ToDo
            'attributes.youtube_uri'                => 'string',
            'links.self'                            => 'string:url',
        ];
    }

    public static function trackResponseJsonApiType(): array
    {
        return [
            'type'                                  => 'string:!empty',
            'id'                                    => 'string:!empty',
            'attributes' => [
                'created_at'                        => 'string:date',
                'updated_at'                        => 'string:date|string', //When is empty is not null... is an empty string
                'artist'                            => 'string:!empty',
                'name'                              => 'string:!empty',
                'spotify_uri'                       => 'string',
                'spotify_images'                    => 'array|null|string', //@ToDo
                'youtube_uri'                       => 'string',
            ],
            'links' => [
                'self'                              => 'string:url',
            ]
        ];
    }

    public static function rateResponseJsonType(): array
    {
        return [
            'type'                                  => 'string:!empty',
            'id'                                    => 'string:!empty',
            'attributes.created_at'                 => 'string:date',
            'attributes.updated_at'                 => 'string:date|string', //When is empty is not null... is an empty string
            'attributes.rate'                       => 'integer',
            'links.self'                            => 'string:url',
        ];
    }

    public static function rateResponseJsonApiType(): array
    {
        return [
            'type'                                  => 'string:!empty',
            'id'                                    => 'string:!empty',
            'attributes' => [
                'created_at'                        => 'string:date',
                'updated_at'                        => 'string:date|string', //When is empty is not null... is an empty string
                'rate'                              => 'integer',
            ],
            'links' => [
                'self'                              => 'string:url',
            ]
        ];
    }

    public static function searchResponseJsonType(): array
    {
        return [
            'type'                                  => 'string:!empty',
            'id'                                    => 'string:!empty',
            'attributes.type'                       => 'string',
            'attributes.artist'                     => 'string',
            'attributes.album'                      => 'string',
            'attributes.track'                      => 'string',
            'attributes.playlist'                   => 'string',
            'attributes.show'                       => 'string',
            'attributes.episode'                    => 'string',
//            'links.self'                            => 'string:url',
        ];
    }

    public static function searchResponseJsonApiType(): array
    {
        return [
            'type'                                  => 'string:!empty',
            'id'                                    => 'string:!empty',
            'attributes' => [
                'type'                              => 'string',
                'artist'                            => 'string',
                'album'                             => 'string',
                'track'                             => 'string',
                'playlist'                          => 'string',
                'show'                              => 'string',
                'episode'                           => 'string',
            ],
//            'links' => [
//                'self'                              => 'string:url',
//            ]
        ];
    }
}

