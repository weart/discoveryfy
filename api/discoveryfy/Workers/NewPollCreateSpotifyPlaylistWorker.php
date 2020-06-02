<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Workers;

use Discoveryfy\Exceptions\InternalServerErrorException;
use Discoveryfy\Models\Polls;
use Interop\Queue\Context;
use Interop\Queue\Message;

class NewPollCreateSpotifyPlaylistWorker extends BaseWorker
{
//    protected $max_attemps = 5;

    public function process(Message $message, Context $context)
    {
        /** @var Polls $poll */
        $poll = $this->getPollById($message->getProperty('id'));
        if (!$poll) {
            $this->log(sprintf('Poll %s not exist', $message->getProperty('id')));
            return $this->returnResult(false, $this->incrementAttempt($message), false);
        }

//        $attrs = [ 'name', 'description', 'spotify_playlist_public', 'spotify_playlist_collaborative' ];
//        foreach ($attrs as $attr) {
//            if ($poll->get($attr) !== $message->getProperty($attr)) {
//                throw new InternalServerErrorException('Poll property has changed');
//            }
//        }

        if (!empty($poll->get('spotify_playlist_uri'))) {
//            throw new InternalServerErrorException('The property spotify_playlist_uri is already set');
            $this->log('The property spotify_playlist_uri is already set');
            return $this->returnResult(false, $this->incrementAttempt($message), false);
        }

        // Create Playlist in Spotify
        $playlist = $this->getSpotifyApi()->createPlaylist([
            'name' => $poll->get('name'),
            'description' => $poll->get('description'),
            'public' => $poll->get('spotify_playlist_public'),
            'collaborative' => $poll->get('spotify_playlist_collaborative'),
        ]);

        // Update poll
        $poll->set('spotify_playlist_uri', $playlist['id']);
        $poll->set('spotify_playlist_images', json_encode($playlist['images']));
        // Test if everything is in sync?
//        if ($poll->get('spotify_playlist_collaborative') !== $playlist['collaborative']) {
//            $this->log('Playlist has different collaborative state');
//        }
//        if ($poll->get('spotify_playlist_public') !== $playlist['public']) {
//            $this->log('Playlist has different public state');
//        }

        return $this->returnResult(
            $poll->save(),
            $this->incrementAttempt($message)
        );
    }
}
