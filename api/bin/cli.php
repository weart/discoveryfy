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

use Exception as PhpException;
use Phalcon\Api\Bootstrap\Cli;
use Phalcon\Exception as PhalconException;

require_once __DIR__ . '/../phalcon-api/Core/autoload.php';

try {
    $cli = new Cli();
    $cli->setup();
    $cli->run();

} catch (PhalconException $e) {
    fwrite(STDERR, Cli::formatExceptions($e));
    exit(1);
} catch (\Throwable $throwable) {
    fwrite(STDERR, Cli::formatExceptions($throwable));
    exit(1);
} catch (PhpException $e) {
    fwrite(STDERR, Cli::formatExceptions($e));
    exit(1);
}
