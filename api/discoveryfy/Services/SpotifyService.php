<?php
declare(strict_types=1);

namespace Discoveryfy\Services;

use Discoveryfy\Constants\CacheKeys;
use Discoveryfy\Exceptions\BadRequestException;
use Discoveryfy\Exceptions\InternalServerErrorException;
use Phalcon\Cache;
use Phalcon\Di;
//use Phalcon\Di\AbstractInjectionAware;
use Phalcon\DiInterface;
use Phalcon\Di\Injectable;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;
use function Phalcon\Api\Core\envValue;

/**
 * @property Cache        $cache
 */
class SpotifyService extends Injectable // extends AbstractInjectionAware
{
    /**
     * @var SpotifyWebAPI api
     */
    private $api;

    /**
     * @var SpotifySession $session
     */
    private $session;

    public function __construct()
    {
        $keys = ['SPOTIFY_CLIENT_ID', 'SPOTIFY_CLIENT_SECRET', 'SPOTIFY_REFRESH_TOKEN']; //, 'SPOTIFY_REDIRECT_URI'
        foreach ($keys as $key) {
            if (empty(envValue($key))) {
//            if (!array_key_exists($key, $_ENV)) {
                throw new \InvalidArgumentException(sprintf('Undefined key "%s" in $_ENV, define it in .env file', $key));
            }
        }

        /*
         * https://github.com/jwilsson/spotify-web-api-php/blob/3.4.0/docs/examples/automatically-refreshing-access-tokens.md
         */
        $this->session = new SpotifySession(
            envValue('SPOTIFY_CLIENT_ID'),
            envValue('SPOTIFY_CLIENT_SECRET')
        );
        // Use previously requested tokens fetched from somewhere. A database for example.
        $accessToken = $this->cache->get(CacheKeys::getSpotifyAccessToken());
        if ($accessToken) {
            $this->session->setAccessToken($accessToken);
            $this->session->setRefreshToken(envValue('SPOTIFY_REFRESH_TOKEN'));
        } else {
            // Or request a new access token
            $this->session->refreshAccessToken(envValue('SPOTIFY_REFRESH_TOKEN'));
        }

        $options = [
            'auto_refresh' => true,
        ];

        $this->api = new SpotifyWebAPI($options, $this->session);
        $this->api->setReturnType(SpotifyWebAPI::RETURN_ASSOC);
        $this->refreshTokens($accessToken);
    }

    private function refreshTokens(string $accessToken = null)
    {
        if (empty($accessToken)) {
            $accessToken = $this->cache->get(CacheKeys::getSpotifyAccessToken());
        }

        // Grab the tokens afterwards, they might have been updated
        if ($this->session->getAccessToken() !== $accessToken) {
            $this->cache->set(CacheKeys::getSpotifyAccessToken(), $accessToken);
        }
        // Sometimes, a new refresh token will be returned
        if ($this->session->getRefreshToken() !== envValue('SPOTIFY_REFRESH_TOKEN')) {
            throw new InternalServerErrorException('Different refresh token!');
        }
    }


    public function search(string $query, string $types, array $pagination)
    {
        unset($pagination['page']);
        $results = $this->api->search($query, $types, $pagination);
        $rtn = [];
        foreach (explode(',', $types) as $type) {
            if (isset($results[$type.'s'])) {
                foreach ($results[$type.'s']['items'] as $result) {
                    if (is_array($result)) { //Sometimes $result is null...
                        $rtn[] = $this->rtnResult($type, $result);
                    }
                }
            } else {
                throw new \Exception('Invalid type: '.$type);
            }
        }
        return $rtn;
    }

    public function getAlbumInfo(string $uri): array
    {
        $info = $this->api->getAlbum($uri);
        return $this->rtnResult('album', $info);
    }

    public function getArtistInfo(string $uri): array
    {
        $info = $this->api->getArtist($uri);
        return $this->rtnResult('artist', $info);
    }

    public function getTrackInfo(string $uri): array
    {
        $info = $this->api->getTrack($uri);
        return $this->rtnResult('track', $info);
    }

    public function getPlaylistInfo(string $uri): array
    {
        $info = $this->api->getPlaylist($uri);
        return $this->rtnResult('playlist', $info);
    }

    private function rtnResult(string $type, array $info)
    {
        if ($type === 'album') {
            $artist = $this->getArtistName($info['artists']);
            $album = $info['name'];
            $track = $playlist = $show = $episode = '';
        } else if ($type === 'artist') {
            $artist = $info['name'];
            $album = $track = $playlist = $show = $episode = '';
        } else if ($type === 'track') {
            $artist = $this->getArtistName($info['artists']);
            $album = $info['album']['name'];
            $track = $info['name'];
            $playlist = $show = $episode = '';
        } else if ($type === 'playlist') {
            $playlist = $info['name'];
            $artist = $album = $track = $show = $episode = '';
        } else if ($type === 'show') {
            $show = $info['name'];
            $artist = $album = $track = $playlist = $episode = '';
        } else if ($type === 'episode') {
            $episode = $info['name'];
            $artist = $album = $track = $playlist = $show = '';
        } else {
            throw new BadRequestException('Invalid type');
        }
        return [
            'type' => $type,
            'id' => $info['id'],
            'artist' => $artist,
            'album' => $album,
            'track' => $track,
            'playlist' => $playlist,
            'show' => $show,
            'episode' => $episode
        ];
    }

    private function getArtistName(array $artists): string
    {
        return implode(', ', array_column($artists, 'name'));
    }

    //getTrackInfo
    //getPlaylistInfo
    //createPlaylist
    //addTrackToPlaylist
    //getPlaylistTracks
    //finishPoll
    //restartPoll
    /**
     * Generate the Spotify authorization URL
    public function getAuthorizeUrl(): string
    {
        $session = $this->getSpotifySession();
        $options = [
            'scope' => [
                'playlist-modify-public',
                'playlist-modify-private',
            ],
        ];
        return $session->getAuthorizeUrl($options);
    }
    */

    /*
    public function login(string $authorizationCode): SpotifySession
    {
        $session = $this->getSpotifySession();
        $session->requestAccessToken($authorizationCode);

        $event = new SpotifyLogged($session->getAccessToken(), $session->getRefreshToken());
        $this->dispatcher->dispatch($event, SpotifyLogged::NAME);

        return $session;
    }
        public function getClient($accessToken): SpotifyWebAPI
        {
            $client = new SpotifyWebAPI();
            $client->setReturnType(SpotifyWebAPI::RETURN_ASSOC);
            $client->setAccessToken($accessToken);
            return $client;
        }

        public function refreshSession($refreshToken, SpotifyWebAPI $client): SpotifyWebAPI
        {
            $session = $this->getSpotifySession();
            $session->refreshAccessToken($refreshToken);
            $client->setAccessToken($session->getAccessToken());
            return $client;
        }

    public function getClient(): SpotifyWebAPI
    {
        $session = $this->getSpotifySession();
        if (!$session->refreshAccessToken($_ENV['SPOTIFY_REFRESH_TOKEN'])) {
            throw new \Exception('Invalid refresh token');
        }
        $client = new SpotifyWebAPI();
        $client->setReturnType(SpotifyWebAPI::RETURN_ASSOC);
        $client->setAccessToken($session->getAccessToken());
        return $client;
    }

    public function refreshAccessToken(SpotifyWebAPI &$client, $refreshToken): bool
    {
        if ((int) $client->getLastResponse()['status'] === 401) {
            $session = $this->getSpotifySession();
            if ($session->refreshAccessToken($refreshToken)) {
                $client->setAccessToken($session->getAccessToken());
                return true;
            }
        }
        return false;
    }

    private function getSpotifySession(): SpotifySession
    {
        return new SpotifySession(
            $_ENV['SPOTIFY_CLIENT_ID'],
            $_ENV['SPOTIFY_CLIENT_SECRET'],
            $_ENV['SPOTIFY_REDIRECT_URI']
        );
    }
    */
}
