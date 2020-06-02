<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Workers;

use Discoveryfy\Models\Polls;
use Discoveryfy\Models\Tracks;
use Interop\Queue\Context;
use Interop\Queue\Message;

class UpdateTrackUpdateTrackSpotifyPlaylist extends BaseWorker
{
//    protected $max_attemps = 5;

    public function process(Message $message, Context $context)
    {
        /** @var Polls $poll */
        $poll = $this->getPollById($message->getProperty('poll_id'));
        if (!$poll) {
            $this->log(sprintf('Poll %s not exist', $message->getProperty('poll_id')));
            return $this->returnResult(false, $this->incrementAttempt($message), false);
        }
        /** @var Tracks $track */
        $track = $this->getTrackById($message->getProperty('id'));
        if (!$track) {
            $this->log(sprintf('Track %s not exist', $message->getProperty('id')));
            return $this->returnResult(false, $this->incrementAttempt($message), false);
        }

        if ($message->getProperty('remove_spotify_uri')) {
            $this->getSpotifyApi()->deletePlaylistTracks(
                $poll->get('spotify_playlist_uri'),
                [ 'tracks' => [ 'id' => $message->getProperty('remove_spotify_uri') ]]
            );
        }

        $result = $this->getSpotifyApi()->addPlaylistTracks(
            $poll->get('spotify_playlist_uri'),
            $track->get('spotify_uri')
        );

        return $this->returnResult($result, $this->incrementAttempt($message));
    }
}
