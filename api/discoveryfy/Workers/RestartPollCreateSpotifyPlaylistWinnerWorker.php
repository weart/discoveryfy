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

class RestartPollCreateSpotifyPlaylistWinnerWorker extends BaseWorker
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

//        $attrs = [ 'name', 'description' ];
//        foreach ($attrs as $attr) {
//            if ($poll->get($attr) !== $message->getProperty($attr)) {
//                throw new InternalServerErrorException('Poll property has changed');
//            }
//        }

        if (!empty($poll->get('spotify_playlist_winner_uri'))) {
//            throw new InternalServerErrorException('The property spotify_playlist_winner_uri is already set');
            $this->log('The property spotify_playlist_winner_uri is already set');
            return $this->returnResult(false, $this->incrementAttempt($message), false);
        }

        // Create Playlist in Spotify
        $playlist = $this->getSpotifyApi()->createPlaylist([
            'name' => $poll->getWinnerPlaylistName(),
            'description' => $poll->getWinnerPlaylistDescription(),
            'public' => true,
            'collaborative' => false,
        ]);

        // Update historic poll
        $poll->set('spotify_playlist_winner_uri', $playlist['id']);

        return $this->returnResult(
            $poll->save(),
            $this->incrementAttempt($message)
        );
    }
}
