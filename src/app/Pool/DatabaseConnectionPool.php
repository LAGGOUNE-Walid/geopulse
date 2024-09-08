<?php

namespace Pulse\Pool;

use Illuminate\Database\Capsule\Manager as DB;
use League\Container\Container;
use Swoole\ConnectionPool;

class DatabaseConnectionPool extends ConnectionPool
{
    public function __construct(Container $container, int $size)
    {
        parent::__construct(function () use ($container) {
            $db = $container->get(DB::class);
            $db->getConnection()->getPdo();
            return $db->connection('default');
        }, $size);
    }
}
