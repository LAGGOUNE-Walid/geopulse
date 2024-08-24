<?php

return [
    'port' => 9505,
    'use-msgPack' => true,
    'protocol' => SWOOLE_SOCK_UDP,
    'swoole' => [
        'debug_mode' => true,
        'display_errors' => true,
        'worker_num' => 4,
        'task_worker_num' => swoole_cpu_num() * 10,
        'open_eof_check' => true,
        'package_eof'    => "\r\n",
    ],
    // use the same configuration structure used by laravel https://raw.githubusercontent.com/laravel/framework/11.x/config/queue.php
    'queue-connection' => [
        'driver' => 'beanstalkd',
        'host' => 'beanstalkd-geopulse',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => 0,
        'after_commit' => false,
    ],
    // the table that holds the records of locations
    'database-table' => 'geopulse-data',
    // use the same database configuration used by laravel https://raw.githubusercontent.com/laravel/framework/11.x/config/database.php
    'database-connection' => [
        'driver' => 'mariadb',
        'url' => null,
        'host' => "mariadb-geopulse",
        'port' => "3306",
        'database' => 'geopulse',
        'username' => 'root',
        'password' => "geopulse",
        'unix_socket' => null,
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => false,
        'engine' => null,
    ]
];
