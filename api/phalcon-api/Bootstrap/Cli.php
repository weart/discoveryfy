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

namespace Phalcon\Api\Bootstrap;

use Phalcon\Cli\Console;
use Phalcon\Di\FactoryDefault\Cli as PhCli;
use function Phalcon\Api\Core\appPath;

/**
 * Class Cli
 *
 * @property Console $application
 */
class Cli extends AbstractBootstrap
{
    /**
     * Run the application
     *
     * @return mixed
     */
    public function run()
    {
        return $this->application->handle($this->options);
    }

    /**
     * @return mixed
     */
    public function setup()
    {
        $this->container = new PhCli();
        $this->providers = require appPath('config/providers.cli.php');

        $this->processArguments();

        parent::setup();
    }

    /**
     * Setup the application object in the container
     *
     * @return void
     */
    protected function setupApplication()
    {
        $this->application = new Console($this->container);
        $this->container->setShared('application', $this->application);
    }

    /**
     * Parses arguments from the command line
     */
    private function processArguments()
    {
        // By Default the task is Main and the action is help
        $this->options = [
            'task'          => 'Welcome',
            'action'        => 'help',
        ];
        $cmd_args = $_SERVER['argv'];
        foreach ($cmd_args as $k => $arg) {
            if ($k === 1) { //First param is the task
                $this->options['task'] = $arg;
            } elseif ($k === 2) { //Second param is the action
                $this->options['action'] = $arg;
            } elseif ($k >= 3) { //Others params are args to the action
                $this->options['params'][] = $arg;
            }
        }
    }

    /*
    private function processArgumentsPhalconApi()
    {
        $this->options = [
            'task' => 'Main',
        ];
        $options = [
            'clear-cache' => 'ClearCache',
            'help'        => 'Main',
        ];

        $arguments = getopt('', array_keys($options));
        foreach ($options as $option => $task) {
            if (true === isset($arguments[$option])) {
                $this->options['task'] = $task;
            }
        }
    }
    */

    public static function formatExceptions($e)
    {
        $errorString = "+---------------------+\n| \e[31mUNHANDLED EXCEPTION\e[39m |\n+---------------------+\n";
        $errorString .= "Fatal error: Uncaught exception '%s' %s with message '%s' in %s on line %d\n\n";
        $errorString .= "Stack Trace:\n%s\n";

        $type = get_class($e);
        $message = $e->getMessage();
        $file = $e->getFile();
        $line = $e->getLine();
        $trace = $e->getTraceAsString();
        $code = null;
        if ($e->getCode()) {
            $code = '(' . $e->getCode() . ')';
        }

        $error = sprintf($errorString, $type, $code, $message, $file, $line, $trace);
        return $error;
    }
}
