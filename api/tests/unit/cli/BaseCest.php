<?php

namespace Discoveryfy\Tests\unit\cli;

use Discoveryfy\Tasks\WelcomeTask;
use Phalcon\Di\FactoryDefault\Cli;
use UnitTester;
use function ob_end_clean;
use function ob_get_contents;
use function ob_start;

class BaseCest
{
    public function checkOutput(UnitTester $I)
    {
        $container = new Cli();
        $task      = new WelcomeTask();
        $task->setDI($container);

        ob_start();
        $task->runAction();
        $actual = ob_get_contents();
        ob_end_clean();

        $expected = <<<EOF
*********************************
* \e[32mWelcome to Discoveryfy Tasks!\e[39m *
*********************************

Available tasks:
  * \e[31mClearCache\e[39m -> Clear cache in Redis, files in storage/cache folder
  * \e[31mJobStats\e[39m -> Show job queue information
  * \e[31mJobWorker\e[39m -> Execute the next job in the queue
  * \e[31mJobCleaner\e[39m -> Remove jobs from the queue
  * \e[31mRestartPolls\e[39m -> Check if any poll should be restarted
  * \e[31mUpdatePollsImages\e[39m -> Check if any poll should grab new images from spotify


EOF;
        $I->assertEquals($expected, $actual);
    }
}
