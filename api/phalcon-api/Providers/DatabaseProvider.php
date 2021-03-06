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

use Monolog\Logger;
use Phalcon\Config;
use Phalcon\Db\Adapter\Pdo\Mysql;
//use Phalcon\Db\Adapter\Pdo\Postgresql;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Event;
use Phalcon\Events\Manager;
use function Phalcon\Api\Core\envValue;

class DatabaseProvider implements ServiceProviderInterface
{
    /**
     * @var string
     */
    public const NAME = 'db';

    /**
     * @param DiInterface $container
     */
    public function register(DiInterface $container): void
    {
        /** @var Manager $eventsManager */
        $eventsManager = $container->getShared('eventsManager');

        $container->setShared(self::NAME, function () use ($eventsManager) {
            //MySQL
            $connection = new Mysql([
                'host'       => envValue('MYSQL_HOST', 'localhost'),
                'dbname'     => envValue('MYSQL_DATABASE', 'db_local'),
                'username'   => envValue('MYSQL_USER', 'db_user'),
                'password'   => envValue('MYSQL_PASSWORD', 'db_password'),
            ]);
            $connection->execute('SET NAMES utf8mb4', []);

            //Postgresql
//            $connection = new Postgresql([
//                'host'       => envValue('POSTGRES_HOST', 'localhost'),
//                'dbname'     => envValue('POSTGRES_DB', 'db_local'),
//                'username'   => envValue('POSTGRES_USER', 'db_user'),
//                'password'   => envValue('POSTGRES_PASSWORD', 'db_password'),
//            ]);

            $connection->setEventsManager($eventsManager);
            return $connection;
        });

        /** @var Config $config */
        $config = $container->getShared('config');
        $debug = (bool) $config->path('app.debug');
        if ($debug) {
            /** @var Logger $logger */
            $logger = $container->getShared(LoggerProvider::NAME);
            $eventsManager->attach('db:afterQuery', function (Event $event, $connection) use ($logger) {
                $logger->debug('SQL: '.$connection->getSQLStatement());
                $logger->debug(var_export($connection->getSQLVariables(), true));
            });
        }
    }
}
