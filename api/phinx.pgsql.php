<?php

use Dotenv\Dotenv;

(Dotenv::create(__DIR__, '.env'))->load();
//var_dump(__DIR__);
//var_dump(getenv('POSTGRES_USER'));
//exit;

return [
    'paths'         => [
        'migrations' => getenv('PHINX_CONFIG_DIR') . 'storage/db/migrations',
        'seeds'      => getenv('PHINX_CONFIG_DIR') . 'storage/db/seeds',
    ],
    'environments'  => [
        'default_migration_table' => 'ut_migrations',
        'default_database'        => 'development',
        'production'              => [
            'adapter' => 'pgsql',
            'host'    => getenv('POSTGRES_HOST'),
            'name'    => getenv('POSTGRES_DB'),
            'user'    => getenv('POSTGRES_USER'),
            'pass'    => getenv('POSTGRES_PASSWORD'),
            'port'    => 5432,
            'charset' => 'utf8',
        ],
        'development'             => [
            'adapter' => 'pgsql',
            'host'    => getenv('POSTGRES_HOST'),
            'name'    => getenv('POSTGRES_DB'),
            'user'    => getenv('POSTGRES_USER'),
            'pass'    => getenv('POSTGRES_PASSWORD'),
            'port'    => 5432,
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation',
];
