<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tasks;

use Discoveryfy\Models\Polls;
use Discoveryfy\Workers\UpdatePollImagesWorker;
use Phalcon\Api\Providers\JobsProvider;
use Phalcon\Api\Queue\JobManager;
use Phalcon\Api\Tasks\BaseTask;
use Phalcon\Db\Column;
use Phalcon\Db\RawValue;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
use const PHP_EOL;

class UpdatePollsImagesTask extends BaseTask
{
    public function helpAction()
    {
        echo $this->formatTitle('Update Polls Images');
        echo 'Check if any poll should grab new images from spotify'.PHP_EOL;
        parent::helpAction();
    }

    public function runAction()
    {
        echo $this->formatTitle('StartUpdate Polls Images');
        // DATE_SUB function is treated as a string and doesnt work
//        $polls = Polls::find([
//            'conditions' => 'DATE(updated_at) < DATE_SUB(CURDATE(), INTERVAL :days: DAY) AND spotify_playlist_uri is not null AND spotify_playlist_uri <> :empty:',
//            'bind'       => [ 'days' => 1, 'empty' => '' ],
//            'bindTypes'  => [ 'days' => Column::BIND_SKIP, 'empty' => Column::BIND_PARAM_STR ]
//        ])->toArray();
        // Raw query instead
        $poll = new Polls();
        $sql = 'SELECT * FROM polls WHERE DATE(updated_at) < DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND spotify_playlist_uri is not null AND spotify_playlist_uri <> ""';
        $polls = new Resultset(
            null,
            $poll,
            $poll->getReadConnection()->query($sql)
        );
        $polls = $polls->toArray();

        echo 'Outdated polls: '.$this->format((string) count($polls), 'yellow').PHP_EOL;
        foreach ($polls as $poll) {
            echo sprintf(' * id: %s, spotify uri: %s, updated_at:  %s', $poll['id'], $poll['spotify_playlist_uri'], $poll['updated_at']).PHP_EOL;
            $this->getJobManager()->addJob(UpdatePollImagesWorker::class, [
                'id' => $poll['id']
            ]);
        }
        echo $this->format('Enqueued', 'green').PHP_EOL;
    }

    protected function getJobManager(): JobManager
    {
        return $this->getDI()->getShared(JobsProvider::NAME);
//        return $this->jobs;
    }
}
