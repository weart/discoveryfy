<?php

use Phalcon\Api\Bootstrap\Tests;

require_once __DIR__ . '/../../phalcon-api/Core/autoload.php';

$bootstrap = new Tests();
$bootstrap->setup();

return $bootstrap->run();
