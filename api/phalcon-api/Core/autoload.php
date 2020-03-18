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

use Dotenv\Dotenv;
use Phalcon\Loader;
use function Phalcon\Api\Core\appPath;

// Register the auto loader
require __DIR__ . '/functions.php';

$loader     = new Loader();
$namespaces = [
    'Phalcon\Api'           => appPath('phalcon-api'),
    'Discoveryfy\Tests'     => appPath('tests'),
    'Discoveryfy'           => appPath('discoveryfy'),
];

$loader->registerNamespaces($namespaces);
$loader->register();

/**
 * Composer Autoloader
 */
require appPath('/vendor/autoload.php');

// Load environment vars
(Dotenv::create(appPath(), '.env'))->overload();
(Dotenv::create(appPath(), '.env.local'))->overload();
