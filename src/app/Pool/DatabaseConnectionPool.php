<?php

namespace Pulse\Pool;

use Illuminate\Database\Capsule\Manager as DB;
use League\Container\Container;
use Swoole\ConnectionPool;

class DatabaseConnectionPool extends ConnectionPool
{
    public function __construct(Container $container, public int $size)
    {
        parent::__construct(function () use ($container) {
            $db = $container->get(DB::class);

            return $db->connection('default');
        }, $size);
    }
}
