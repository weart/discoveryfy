<?php

namespace Discoveryfy\Tests\cli;

use CliTester;

class CheckHelpTaskCest
{
    public function checkWelcome(CliTester $I)
    {
        $I->runShellCommand('./runCli');
        $I->seeResultCodeIs(0);
        $I->seeInShellOutput('Welcome to Discoveryfy Tasks!');
        $I->seeInShellOutput('ClearCache');
        $I->seeInShellOutput('JobWorker');
        $I->seeInShellOutput('JobStats');
    }

    public function checkClearCache(CliTester $I)
    {
        $I->runShellCommand('./runCli ClearCache');
        $I->seeResultCodeIs(0);
        $I->seeInShellOutput('Clear Cache');
        $I->seeInShellOutput('Available options: files, redis, redis-flush, default, all');
        $I->seeInShellOutput('In order to execute the task add "run"');
    }

    public function checkJobWorker(CliTester $I)
    {
        $I->runShellCommand('./runCli JobWorker');
        $I->seeResultCodeIs(0);
        $I->seeInShellOutput('Job Worker');
        $I->seeInShellOutput('Execute the next job in the queue');
        $I->seeInShellOutput('In order to execute the task add "run"');
    }

    public function checkJobStats(CliTester $I)
    {
        $I->runShellCommand('./runCli JobStats');
        $I->seeResultCodeIs(0);
        $I->seeInShellOutput('Job Stats');
        $I->seeInShellOutput('Get information about the jobs in the queue');
        $I->seeInShellOutput('In order to execute the task add "run"');
    }
}
