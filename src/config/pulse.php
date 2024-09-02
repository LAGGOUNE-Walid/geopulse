<?php

return [
    /*
        * Configuration: appId
        *
        * The unique application identifier used to authenticate clients.
        * This App ID allows the server to verify that a client is authorized to send UDP packets to GeoPulse.
        * Every UDP packet sent by a client must include this App ID for validation purposes.
    */
    'appId' => '123',
    /*
        * Configuration: port
        *
        * The application port number for the UDP server.
        * This port is used to receive incoming packets containing coordinate data.
        * Ensure that the specified port (default: 9505) is open and not blocked by a firewall.
    */
    'port' => 9505,
    /*
        * Configuration: use-msgPack
        *
        * A boolean flag to enable or disable MessagePack for data serialization and deserialization.
        * When set to true, the server will use MessagePack to unpack received data packets.
        * If set to false, the server will process data as raw strings.
    */
    'use-msgPack' => true,

    'swoole' => [
        'debug_mode' => true,
        'display_errors' => true,
        'worker_num' => 4,
        'task_worker_num' => swoole_cpu_num() * 5,
        'enable_coroutine' => true,
        'task_enable_coroutine' => true,
        'open_eof_check' => true,
        'package_eof' => "\r\n",
        'dispatch_mode' => 1,
    ],
    /*
        * Configuration: enable-queue
        *
        * Determines whether the server should packets to your application using queues.
    */
    'enable-queue' => true,

    /*
        * Configuration: queue-connection
        *
        * Defines the connection settings for the queue driver. Follows the same configuration structure
        * used by Laravel's queue system to ensure compatibility and flexibility.
    */
    'queue-connection' => [
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'geopulse',
        'retry_after' => 90,
        'block_for' => null,
        'after_commit' => false,
    ],

    'redis' => [
        'options' => [
            'cluster' => 'redis',
            'prefix' => 'YOUR_APP_NAME'.'_database_',
        ],
        'default' => [
            'url' => '',
            'host' => 'redis',
            'username' => '',
            'password' => '',
            'port' => 6379,
            'database' => '0',
        ],
    ],

    /*
        * Configuration: enable-database
        *
        * Determines whether the server should use a database for storing location records.
        * When set to true, the server will persist each location data to the specified database table.
    */
    'enable-database' => true,

    /*
        * Configuration: database-connection
        *
        * Defines the database connection settings, following the same structure as Laravel's
        * database configuration file. This ensures compatibility with Laravel's database handling
        * capabilities and allows for flexible configuration across different environments.
    */
    'database-connection' => [
        'driver' => 'mariadb',
        'url' => null,
        'host' => 'mariadb-geopulse',
        'port' => '3306',
        'database' => 'geopulse',
        'username' => 'root',
        'password' => 'geopulse',
        'unix_socket' => null,
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => false,
        'engine' => null,
    ],

];
