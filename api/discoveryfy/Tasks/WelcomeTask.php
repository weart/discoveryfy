<?php
declare(strict_types=1);

/**
 * This file is part of the Phalcon API.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Discoveryfy\Tasks;

use Phalcon\Api\Tasks\BaseTask;
use const PHP_EOL;

class WelcomeTask extends BaseTask
{
    // If this is modified, change \Discoveryfy\Tests\unit\cli\BaseCest also
    protected $tasks = [
        'ClearCache' => 'Clear cache in Redis, files in storage/cache folder', // and memcached
        'JobStats' => 'Show job queue information',
        'JobWorker' => 'Execute the next job in the queue',
        'JobCleaner' => 'Remove jobs from the queue',
        'RestartPolls' => 'Check if any poll should be restarted',
        'UpdatePollsImages' => 'Check if any poll should grab new images from spotify',
    ];

    /**
     * Executes the main action, show all the possible tasks
     */
    public function helpAction()
    {
        $this->application->handle([
            'task'   => 'Welcome',
            'action' => 'run'
        ]);
    }

    public function runAction()
    {
        echo $this->formatTitle('Welcome to Discoveryfy Tasks!');
        echo 'Available tasks:' . PHP_EOL;
        foreach ($this->tasks as $task => $desc) {
            echo '  * ' . $this->format($task, 'red') . ' -> ' . $desc . PHP_EOL;
        }
        echo PHP_EOL;
    }
}
