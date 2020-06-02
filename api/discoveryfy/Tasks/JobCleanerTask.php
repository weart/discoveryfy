<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tasks;

use Phalcon\Api\Providers\JobsProvider;
use Phalcon\Api\Queue\JobManager;
use Phalcon\Api\Tasks\BaseTask;
use const PHP_EOL;

class JobCleanerTask extends BaseTask
{
    public function helpAction()
    {
        echo $this->formatTitle('Job Cleaner');
        echo $this->format('clean', 'red').' -> Mark all jobs as done'.PHP_EOL;
        echo $this->format('purge', 'red').' -> Remove all queued jobs'.PHP_EOL;
        parent::helpAction();
    }

    public function cleanAction()
    {
        echo $this->formatTitle('Job Cleaner: Clean');
        echo 'All jobs will be marked as done... ';
        $this->getJobManager()->cleanJobs();
        echo $this->format('done!', 'green').PHP_EOL;
    }

    public function purgeAction()
    {
        echo $this->formatTitle('Job Cleaner: Purge');
        echo 'All jobs will be removed... ';
        $this->getJobManager()->purgeJobs();
        echo $this->format('done!', 'green').PHP_EOL;
    }

    protected function getJobManager(): JobManager
    {
        return $this->getDI()->getShared(JobsProvider::NAME);
//        return $this->jobs;
    }
}
