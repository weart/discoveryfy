<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Workers;

//use Discoveryfy\Exceptions\InternalServerErrorException;
//use Discoveryfy\Models\Polls;
//use Discoveryfy\Models\Tracks;
use Interop\Queue\Context;
use Interop\Queue\Message;

class DeleteTrackDeleteTrackSpotifyPlaylist extends BaseWorker
{
//    protected $max_attemps = 5;

    public function process(Message $message, Context $context)
    {
//        /** @var Polls $poll */
//        $poll = $this->getPollById($message->getProperty('poll_id'));
//        if ($poll->get('spotify_playlist_uri') !== $message->getProperty('spotify_playlist_uri')) {
//            throw new InternalServerErrorException('The attribute spotify_playlist_uri has changed!');
//        }
//        /** @var Tracks $track */
//        $track = $this->getTrackById($message->getProperty('track_id'));
//        if ($track->get('spotify_uri') !== $message->getProperty('spotify_uri')) {
//            throw new InternalServerErrorException('The attribute spotify_uri has changed!');
//        }

        $this->log(sprintf(
            'Delete track "%s" from playlist "%s"',
            $message->getProperty('spotify_uri'),
            $message->getProperty('spotify_playlist_uri')
        ));
        $result = (false !== $this->getSpotifyApi()->deletePlaylistTracks(
            $message->getProperty('spotify_playlist_uri'),
            [ 'tracks' => [ 'id' => $message->getProperty('spotify_uri') ]]
        ));

        return $this->returnResult($result, $this->incrementAttempt($message));
    }
}
