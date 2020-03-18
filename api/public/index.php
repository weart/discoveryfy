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

use Phalcon\Api\Bootstrap\Api;
use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\envValue;

require_once __DIR__ . '/../phalcon-api/Core/autoload.php';
include appPath().'/c3.php';

/** @var string $logPath */
$logPath   = envValue('LOGGER_DEFAULT_PATH', 'storage/logs');

$appLogName  = sprintf('%s/%s', $logPath, envValue('LOGGER_DEFAULT_FILENAME', 'api.log'));
$testLogName = sprintf('%s/%s', $logPath, 'c3_error.log');

define('C3_CODECOVERAGE_ERROR_LOG_FILE', $testLogName);
define('MY_APP_STARTED', true);

try {
    $bootstrap = new Api();
    $bootstrap->setup();
    $bootstrap->run();

} catch (\Exception $e) {
    /**
     * @ToDo!
//    echo $e;
     */
    file_put_contents($appLogName, $e->getMessage().PHP_EOL, FILE_APPEND);
}
