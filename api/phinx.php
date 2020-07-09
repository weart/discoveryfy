<?php

use Dotenv\Dotenv;

if ('production' !== getenv('APP_ENV')) {
    (Dotenv::create(__DIR__, '.env'))->load();
    if (is_readable(rtrim(appPath(), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'.env.local')) {
        (Dotenv::create(__DIR__, '.env.local'))->overload();
    }
    $migrations = getenv('PHINX_CONFIG_DIR') . 'storage/db/migrations';
} else {
    $migrations = 'db_migrations';
}


return [
    'paths'         => [
        'migrations' => $migrations,
        'seeds'      => getenv('PHINX_CONFIG_DIR') . 'storage/db/seeds',
    ],
    'environments'  => [
        'default_migration_table' => 'ut_migrations',
        'default_database'        => 'development',
        'production'              => [
            'adapter' => 'mysql',
            'host'    => getenv('MYSQL_HOST'),
            'name'    => getenv('MYSQL_DATABASE'),
            'user'    => getenv('MYSQL_USER'),
            'pass'    => getenv('MYSQL_PASSWORD'),
            'port'    => 3306,
            'charset' => 'utf8',
        ],
        'development'             => [
            'adapter' => 'mysql',
            'host'    => getenv('MYSQL_HOST'),
            'name'    => getenv('MYSQL_DATABASE'),
            'user'    => getenv('MYSQL_USER'),
            'pass'    => getenv('MYSQL_PASSWORD'),
            'port'    => 3306,
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation',
];
