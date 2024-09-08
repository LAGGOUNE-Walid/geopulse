<?php

namespace Pulse\Pool;

use Illuminate\Queue\Capsule\Manager as Queue;
use League\Container\Container;
use Swoole\ConnectionPool;

class QueueConnectionPool extends ConnectionPool
{
    public function __construct(Container $container, int $size)
    {
        parent::__construct(function () use ($container) {
            return $container->get(Queue::class);
        }, $size);
    }
}
