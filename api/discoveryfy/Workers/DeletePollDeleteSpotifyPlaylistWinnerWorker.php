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

class DeletePollDeleteSpotifyPlaylistWinnerWorker extends BaseWorker
{
//    protected $max_attemps = 5;

    public function process(Message $message, Context $context)
    {
//        /** @var Polls $poll */
//        $poll = $this->getPollById($message->getProperty('id'));
//        if ($poll->get('spotify_playlist_winner_uri') !== $message->getProperty('spotify_playlist_winner_uri')) {
//            throw new InternalServerErrorException('The attribute spotify_playlist_winner_uri has changed!');
//        }

        $this->log(sprintf('Unfollow playlist: %s', $message->getProperty('spotify_playlist_winner_uri')));
        $result = $this->getSpotifyApi()->unfollowPlaylist($message->getProperty('spotify_playlist_winner_uri'));

        return $this->returnResult($result, $this->incrementAttempt($message));
    }
}
