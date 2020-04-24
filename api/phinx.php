<?php

use Dotenv\Dotenv;

(Dotenv::create(__DIR__, '.env'))->load();
(Dotenv::create(__DIR__, '.env.local'))->overload();
//var_dump(
//    __DIR__
//    getenv('MYSQL_HOST'),getenv('MYSQL_DATABASE'),
//    getenv('MYSQL_USER'),getenv('MYSQL_PASSWORD'),
//    getenv('PHINX_CONFIG_DIR') . 'storage/db/seeds/BaseSeeder'
//);
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
