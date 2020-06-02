<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Controllers\Spotify;

use Discoveryfy\Constants\Relationships;
//use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Providers\SpotifyProvider;
use Discoveryfy\Services\SpotifyService as Spotify;
use Discoveryfy\Transformers\SearchTransformer;
use Phalcon\Api\Controllers\BaseCollectionApiController;
use Phalcon\Api\Http\Request;
use Phalcon\Api\Http\Response;
use Phalcon\Api\Plugins\Auth\AuthPlugin as Auth;
use Phalcon\Filter;
use Phalcon\Http\ResponseInterface;

/**
 * Search information in Spotify
 *
 * Module       Spotify
 * Class        PostController
 * OperationId  spotify.search
 * Operation    POST
 * OperationUrl /spotify
 * Security     Already checked in AuthenticationMiddleware (The request must come with a valid jwt token)
 *
 * @property Auth         $auth
 * @property Spotify      $spotify
 * @property Request      $request
 * @property Response     $response
 */
class PostController extends BaseCollectionApiController
{
    protected $resource    = Relationships::SEARCH;

    /** @var string */
    protected $transformer = SearchTransformer::class;

    /**
     * Sorting is not supported by Spotify API
     */

    /** @var string */
//    protected $orderBy = '';

    /** @var array */
//    protected $sortFields = [];

    /** @var string */
    protected $method = 'item';

    public function callAction(): ResponseInterface
    {
        putenv('APP_URL'); //Ugly hack for avoid links

        if ($this->request->hasPost('query')) {
            $query = $this->request->getPost('query', Filter::FILTER_STRING);
            $types = $this->request->getPost('type', Filter::FILTER_STRING, 'album,artist,track,playlist');
            foreach (explode(',', $types) as $type) {
                if (!in_array($type, ['album','artist','playlist','track','show','episode'], true)) {
                    return $this->response->sendApiError($this->request->getContentType(), $this->response::BAD_REQUEST, 'Invalid type');
                }
            }
            $this->method = 'collection';
            $results = $this->getSpotifyService()->search($query, $types, $this->getPagination());

        } else if ($this->hasParam('album_uri')) {
            $results = $this->getSpotifyService()->getAlbumInfo($this->getParam('album_uri'));

        } else if ($this->hasParam('artist_uri')) {
            $results = $this->getSpotifyService()->getArtistInfo($this->getParam('artist_uri'));

        } else if ($this->hasParam('track_uri')) {
            $results = $this->getSpotifyService()->getTrackInfo($this->getParam('track_uri'));

        } else if ($this->hasParam('playlist_uri')) {
            $results = $this->getSpotifyService()->getPlaylistInfo($this->getParam('playlist_uri'));

        } else {
            return $this->response->sendApiError($this->request->getContentType(), $this->response::BAD_REQUEST, 'Without arguments to find by');
//            throw new BadRequestException('Without arguments to find by');
        }
        return $this->sendApiData($results);
//        return $this->response
//            ->setStatusCode(
//                $this->response::OK,
//                $this->response->getHttpCodeDescription($this->response::OK)
//            )
//            ->sendApiContent($this->request->getContentType(), $results);
    }

    private function hasParam(string $param): bool
    {
        return (
            $this->request->hasPost($param)
            && !empty($this->request->getPost($param, Filter::FILTER_STRING))
        );
    }

    private function getParam(string $param): string
    {
        return $this->request->getPost($param, Filter::FILTER_STRING);
    }

    private function getSpotifyService(): Spotify
    {
        return $this->getDI()->getShared(SpotifyProvider::NAME);
//        return $this->spotify;
    }
}
