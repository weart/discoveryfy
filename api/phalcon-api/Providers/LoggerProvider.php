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

namespace Phalcon\Api\Providers;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\envValue;

class LoggerProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    public const NAME = 'logger';

    public const DEFAULT_LOG_FILENAME='api.log';
    public const DEFAULT_LOG_PATH='storage/logs/';
    public const DEFAULT_LOG_FORMAT = '[%datetime%] %channel%.%level_name%: %message%'; # %context% %extra%
//    public const DEFAULT_LOG_FORMAT = '[%datetime%][%level_name%] %message%';
    public const DEFAULT_LOG_FORMAT_DATE = 'Y-m-d\TH:i:sP';
    public const DEFAULT_LOG_CHANNEL = 'api';

    /**
     * Registers the logger component
     *
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        $container->setShared(self::NAME, function () {
            $logPath = envValue('LOG_PATH', self::DEFAULT_LOG_PATH);
            if (substr($logPath, -1) !== '/') {
                $logPath .= '/';
            }
            $logFile = $logPath.envValue('LOG_FILENAME', self::DEFAULT_LOG_FILENAME);
            $logFormat = envValue('LOG_FORMAT', self::DEFAULT_LOG_FORMAT);
            if (substr($logFormat, -1) !== PHP_EOL) {
                $logFormat .= PHP_EOL;
            }
            $logFormatDate = envValue('LOG_FORMAT_DATE', self::DEFAULT_LOG_FORMAT_DATE);
            $logChannel = envValue('LOG_CHANNEL', self::DEFAULT_LOG_CHANNEL);

            $logger    = new Logger($logChannel);
            $handler   = new StreamHandler($logFile, Logger::DEBUG);
            $formatter = new LineFormatter($logFormat, $logFormatDate);
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);

            return $logger;
        });
    }
}
