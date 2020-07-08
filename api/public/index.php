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
use Phalcon\Api\Providers\LoggerProvider;
use function Phalcon\Api\Core\appPath;
use function Phalcon\Api\Core\envValue;

require_once __DIR__ . '/../phalcon-api/Core/autoload.php';

/** @var string $logPath */
$logPath = envValue('LOG_PATH', LoggerProvider::DEFAULT_LOG_PATH);
if (substr($logPath, -1) !== '/') {
    $logPath .= '/';
}
$appLogName = appPath($logPath.envValue('LOG_FILENAME', LoggerProvider::DEFAULT_LOG_FILENAME));

if ((bool) envValue('APP_DEBUG', false)) {
    include appPath().'/c3.php';
    $testLogName = $logPath.'c3_error.log';
    define('C3_CODECOVERAGE_ERROR_LOG_FILE', $testLogName);
    define('MY_APP_STARTED', true);
}

try {
    $bootstrap = new Api();
    $bootstrap->setup();
    $bootstrap->run();

} catch (\Exception $e) {
    /**
     * @ToDo!
//    echo $e;
     */
    file_put_contents($appLogName, $e->getMessage().' (index.php)'.PHP_EOL, FILE_APPEND);
}
