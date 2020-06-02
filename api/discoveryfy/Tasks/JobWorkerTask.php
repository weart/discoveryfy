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

class JobWorkerTask extends BaseTask
{
    public function helpAction()
    {
        echo $this->formatTitle('Job Worker');
        echo $this->format('run', 'red').' -> Execute the next job in the queue'.PHP_EOL;
        echo $this->format('loop', 'red').' -> Execute jobs until the end of time... or killed'.PHP_EOL;
        parent::helpAction();
    }

    public function runAction()
    {
        echo $this->formatTitle('Running One Job Worker');
        echo 'Output: '.$this->format($this->getJobManager()->doJob(), 'red').PHP_EOL;
    }

    public function loopAction()
    {
        echo $this->formatTitle('Running Job Worker forever');
        $this->getJobManager()->loopJobs();
//        while (true) {
//            $this->application->handle([
//                'task'   => 'JobWorker',
//                'action' => 'run'
//            ]);
//        }
    }

//    public function addAction(int $first, int $second)
//    {
//        echo $first + $second . PHP_EOL;
//    }

    protected function getJobManager(): JobManager
    {
        return $this->getDI()->getShared(JobsProvider::NAME);
//        return $this->jobs;
    }
}
