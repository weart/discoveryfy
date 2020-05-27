<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tests\api\Spotify;

use Codeception\Exception\TestRuntimeException;
use Codeception\Util\HttpCode;
use Page\Data;
use Step\Api\Login;
use ApiTester;

class SpotifyPostCest
{
    public function unauthorizedSearchJson(ApiTester $I): void
    {
        $this->unauthorizedSearch($I, 'application/json');
    }

    public function unauthorizedSearchJsonApi(ApiTester $I): void
    {
        $this->unauthorizedSearch($I, 'application/vnd.api+json');
    }

    private function unauthorizedSearch(ApiTester $I, string $contentType): void
    {
        $I->haveHttpHeader('Content-Type', $contentType);
        $I->haveHttpHeader('accept', $contentType);
        $I->sendPOST(Data::$spotifyUrl, [
            'query' => 'test'
        ]);
        $I->seeResponseIsError($contentType, HttpCode::BAD_REQUEST, 'Invalid Token');
    }

    public function emptySearchJson(Login $I): void
    {
        $this->emptySearch($I, 'application/json');
    }

    public function emptySearchJsonApi(Login $I): void
    {
        $this->emptySearch($I, 'application/vnd.api+json');
    }

    private function emptySearch(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);

        $I->sendPOST(Data::$spotifyUrl);
        $I->seeResponseIsError($contentType, HttpCode::BAD_REQUEST, 'Without arguments to find by');

        $I->sendPOST(Data::$spotifyUrl, []);
        $I->seeResponseIsError($contentType, HttpCode::BAD_REQUEST, 'Without arguments to find by');

        $I->sendPOST(Data::$spotifyUrl, ['test' => 'test']);
        $I->seeResponseIsError($contentType, HttpCode::BAD_REQUEST, 'Without arguments to find by');
    }

    public function searchQueryJson(Login $I): void
    {
        $this->searchQuery($I, 'application/json');
    }

    public function searchQueryJsonApi(Login $I): void
    {
        $this->searchQuery($I, 'application/vnd.api+json');
    }

    private function searchQuery(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPOST(Data::$spotifyUrl, [
            'query' => 'test'
        ]);
        $this->seeCollectionResponseIsSearchSuccessful($I, $contentType);
    }

    public function searchQueryAndTypeJson(Login $I): void
    {
        $this->searchQueryAndType($I, 'application/json');
    }

    public function searchQueryAndTypeJsonApi(Login $I): void
    {
        $this->searchQueryAndType($I, 'application/vnd.api+json');
    }

    private function searchQueryAndType(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPOST(Data::$spotifyUrl, [
            'query' => 'test',
            'type' => 'album,artist'
        ]);
        $this->seeCollectionResponseIsSearchSuccessful($I, $contentType);
    }

    //@ToDo
//    public function searchQueryAndTypeAndOrderBy
//    public function searchQueryAndTypeAndSortBy
//    public function searchQueryAndTypeAndPagination
//    public function searchQueryAndTypeAndOrderByAndSortByAndPagination

    public function invalidTypeJson(Login $I): void
    {
        $this->invalidType($I, 'application/json');
    }

    public function invalidTypeJsonApi(Login $I): void
    {
        $this->invalidType($I, 'application/vnd.api+json');
    }

    private function invalidType(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPOST(Data::$spotifyUrl, [
            'query' => 'test',
            'type' => 'error'
        ]);
        $I->seeResponseIsError($contentType, HttpCode::BAD_REQUEST, 'Invalid type');
    }

    public function searchAlbumJson(Login $I): void
    {
        $this->searchAlbum($I, 'application/json');
    }

    public function searchAlbumJsonApi(Login $I): void
    {
        $this->searchAlbum($I, 'application/vnd.api+json');
    }

    private function searchAlbum(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPOST(Data::$spotifyUrl, [
            'album_uri' => '0rA9Had6Q4xez5wInSk9Az'
        ]);
        $this->seeItemResponseIsSearchSuccessful($I, [
            'type' => 'album',
            'artist' => 'Nina Simone',
            'album' => 'Work From Home with Nina Simone',
            'track' => '',
            'playlist' => '',
        ], $contentType);
    }

    public function searchArtistJson(Login $I): void
    {
        $this->searchArtist($I, 'application/json');
    }

    public function searchArtistJsonApi(Login $I): void
    {
        $this->searchArtist($I, 'application/vnd.api+json');
    }

    private function searchArtist(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPOST(Data::$spotifyUrl, [
            'artist_uri' => '7G1GBhoKtEPnP86X2PvEYO'
        ]);
        $this->seeItemResponseIsSearchSuccessful($I, [
            'type' => 'artist',
            'artist' => 'Nina Simone',
            'album' => '',
            'track' => '',
            'playlist' => '',
        ], $contentType);
    }

    public function searchTrackJson(Login $I): void
    {
        $this->searchTrack($I, 'application/json');
    }

    public function searchTrackJsonApi(Login $I): void
    {
        $this->searchTrack($I, 'application/vnd.api+json');
    }

    private function searchTrack(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPOST(Data::$spotifyUrl, [
            'track_uri' => '5xRP5iyVdGglqlY4Vcjhkx'
        ]);
        $this->seeItemResponseIsSearchSuccessful($I, [
            'type' => 'track',
            'artist' => 'Nina Simone',
            'album' => 'Pastel Blues',
            'track' => 'Sinnerman - Live In New York/1965',
            'playlist' => '',
        ], $contentType);
    }

    public function searchPlaylistJson(Login $I): void
    {
        $this->searchPlaylist($I, 'application/json');
    }

    public function searchPlaylistJsonApi(Login $I): void
    {
        $this->searchPlaylist($I, 'application/vnd.api+json');
    }

    private function searchPlaylist(Login $I, string $contentType): void
    {
        list($jwt, $session_id, $user_id) = $I->loginAsTest();
        $I->setContentType($contentType);
        $I->haveHttpHeader('Authorization', 'Bearer '.$jwt);
        $I->sendPOST(Data::$spotifyUrl, [
            'playlist_uri' => '37i9dQZF1DX3fXJqxGjuEP'
        ]);
        $this->seeItemResponseIsSearchSuccessful($I, [
            'type' => 'playlist',
            'artist' => '',
            'album' => '',
            'track' => '',
            'playlist' => 'Vibra Tropical',
        ], $contentType);
    }

    private function seeCollectionResponseIsSearchSuccessful(Login $I, string $contentType): void
    {
        if ($contentType === 'application/json') {
            $this->seeCollectionResponseIsSearchJsonSuccessful($I);

        } else if ($contentType === 'application/vnd.api+json') {
            $this->seeCollectionResponseIsSearchJsonApiSuccessful($I);

        } else {
            throw new TestRuntimeException('Invalid contentType');
        }
    }

    private function seeCollectionResponseIsSearchJsonSuccessful(Login $I): void
    {
        $I->seeCollectionResponseIsJsonSuccessful(HttpCode::OK, Data::searchResponseJsonType(), [
            'type'                  => 'results'
        ]);
    }
    private function seeCollectionResponseIsSearchJsonApiSuccessful(Login $I): void
    {
        $I->seeCollectionResponseIsJsonApiSuccessful(HttpCode::OK, Data::searchResponseJsonApiType(), [
            'type'                  => 'results'
        ]);
    }

    private function seeItemResponseIsSearchSuccessful(Login $I, array $attrs, string $contentType): void
    {
        if ($contentType === 'application/json') {
            $this->seeItemResponseIsSearchJsonSuccessful($I, $attrs);

        } else if ($contentType === 'application/vnd.api+json') {
            $this->seeItemResponseIsSearchJsonApiSuccessful($I, $attrs);

        } else {
            throw new TestRuntimeException('Invalid contentType');
        }
    }

    private function seeItemResponseIsSearchJsonSuccessful(Login $I, array $attrs): void
    {
        $I->seeItemResponseIsJsonSuccessful(HttpCode::OK, Data::searchResponseJsonType(), [
            'type'                  => 'results',
            'attributes.type'       => $attrs['type'],
            'attributes.artist'     => $attrs['artist'],
            'attributes.album'      => $attrs['album'],
            'attributes.track'      => $attrs['track'],
            'attributes.playlist'   => $attrs['playlist']
        ]);
    }

    private function seeItemResponseIsSearchJsonApiSuccessful(Login $I, array $attrs): void
    {
        $I->seeItemResponseIsJsonApiSuccessful(HttpCode::OK, Data::searchResponseJsonApiType(), [
            'type'                  => 'results',
            'attributes' => [
                'type'              => $attrs['type'],
                'artist'            => $attrs['artist'],
                'album'             => $attrs['album'],
                'track'             => $attrs['track'],
                'playlist'          => $attrs['playlist'],
            ]
        ]);
    }
}
