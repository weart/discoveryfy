<?php
declare(strict_types=1);

/**
 * This file is part of the Discoveryfy.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Phalcon\Api\Tasks;

use Phalcon\Cli\Task as PhTask;
use const PHP_EOL;

/**
 * Simplified version of:
 * @see https://github.com/symfony/symfony/blob/5.0/src/Symfony/Component/Console/Formatter/OutputFormatterStyle.php
 */
class BaseTask extends PhTask
{
    protected function helpAction()
    {
        echo $this->format('In order to execute the task add "run"').PHP_EOL;
    }

    protected static $availableForegroundColors = [
        'black' => ['set' => 30, 'unset' => 39],
        'red' => ['set' => 31, 'unset' => 39],
        'green' => ['set' => 32, 'unset' => 39],
        'yellow' => ['set' => 33, 'unset' => 39],
        'blue' => ['set' => 34, 'unset' => 39],
        'magenta' => ['set' => 35, 'unset' => 39],
        'cyan' => ['set' => 36, 'unset' => 39],
        'white' => ['set' => 37, 'unset' => 39],
        'default' => ['set' => 39, 'unset' => 39],
    ];
    protected static $availableBackgroundColors = [
        'black' => ['set' => 40, 'unset' => 49],
        'red' => ['set' => 41, 'unset' => 49],
        'green' => ['set' => 42, 'unset' => 49],
        'yellow' => ['set' => 43, 'unset' => 49],
        'blue' => ['set' => 44, 'unset' => 49],
        'magenta' => ['set' => 45, 'unset' => 49],
        'cyan' => ['set' => 46, 'unset' => 49],
        'white' => ['set' => 47, 'unset' => 49],
        'default' => ['set' => 49, 'unset' => 49],
    ];
    protected static $availableOptions = [
        'bold' => ['set' => 1, 'unset' => 22],
        'underscore' => ['set' => 4, 'unset' => 24],
        'blink' => ['set' => 5, 'unset' => 25],
        'reverse' => ['set' => 7, 'unset' => 27],
        'conceal' => ['set' => 8, 'unset' => 28],
    ];

    protected function format(string $text, string $foreground = null, string $background = null, array $options = [])
    {
        $setCodes = [];
        $unsetCodes = [];

        if (null !== $foreground) {
            if (!isset(static::$availableForegroundColors[$foreground])) {
                throw new \InvalidArgumentException(sprintf('Invalid foreground color specified: "%s". Expected one of (%s).', $foreground, implode(', ', array_keys(static::$availableForegroundColors))));
            }
            $setCodes[] = static::$availableForegroundColors[$foreground]['set'];
            $unsetCodes[] = static::$availableForegroundColors[$foreground]['unset'];
        }

        if (null !== $background) {
            if (!isset(static::$availableBackgroundColors[$background])) {
                throw new \InvalidArgumentException(sprintf('Invalid background color specified: "%s". Expected one of (%s).', $background, implode(', ', array_keys(static::$availableBackgroundColors))));
            }
            $setCodes[] = static::$availableBackgroundColors[$background]['set'];
            $unsetCodes[] = static::$availableBackgroundColors[$background]['unset'];
        }

        foreach ($options as $option) {
            if (!isset(static::$availableOptions[$option])) {
                throw new \InvalidArgumentException(sprintf('Invalid option specified: "%s". Expected one of (%s).', $option, implode(', ', array_keys(static::$availableOptions))));
            }
            $setCodes[] = static::$availableOptions[$option]['set'];
            $unsetCodes[] = static::$availableOptions[$option]['unset'];
        }

        if (0 === \count($setCodes)) {
            return $text;
        }
        return sprintf("\033[%sm%s\033[%sm", implode(';', $setCodes), $text, implode(';', $unsetCodes));
    }

    protected function formatTitle(string $text)
    {
        $l = strlen($text)+4;
        return
            str_repeat('*', $l) . PHP_EOL .
            '* ' . $this->format($text, 'green') . ' *'. PHP_EOL .
            str_repeat('*', $l) . PHP_EOL . PHP_EOL
        ;
    }

    protected function formatResult(bool $result)
    {
        if ($result) {
            return $this->format('.', 'green');

        } else {
            return $this->format('F', 'red');
        }
    }
}
