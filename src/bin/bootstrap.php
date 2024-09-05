<?php

use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Queue\Capsule\Manager as Queue;
use Illuminate\Redis\RedisManager;
use League\Container\Container;
use Pulse\Actions\EnqueuePacketAction;
use Pulse\Actions\SavePacketAction;
use Pulse\Pool\DatabaseConnectionPool;
use Pulse\Pool\QueueConnectionPool;
use Pulse\Server\EventHandler\SwooleUdpServerEventHandler;
use Pulse\Server\PacketParser\UdpPacketParser;
use Pulse\Services\BroadcastPacketService;

require 'vendor/autoload.php';
$config = require 'config/pulse.php';

$container = new Container;
if ($config['enable-queue']) {
    $container->add(Queue::class, function () use ($config) {
        $queue = new Queue;
        $queue->addConnection($config['queue-connection']);
        $queue->setAsGlobal();

        if ($config['queue-connection']['driver'] === 'redis') {
            $laravelContainer = new LaravelContainer;
            $redisConfig = $config['redis'];
            $redisConfig['client'] = 'predis';
            $redisManager = new RedisManager($laravelContainer, $redisConfig['client'], $redisConfig);
            $queue->getContainer()->singleton('redis', function () use ($redisManager) {
                return $redisManager;
            });
        }

        return $queue;
    });
}

if ($config['enable-database']) {
    $container->add(DB::class, function () use ($config) {
        $db = new DB;

        $db->addConnection($config['database-connection']);
        $db->setAsGlobal();
        $db->bootEloquent();

        return $db;
    });
}

$container->add(BroadcastPacketService::class, function () use ($config, $container) {
    $broadcaster = new BroadcastPacketService;
    if ($config['enable-queue']) {
        $queueConnectionsPool = new QueueConnectionPool($container, $config['queue-pool-size']);
        $broadcaster->addAction(new EnqueuePacketAction($queueConnectionsPool));
    }
    if ($config['enable-database']) {
        $databaseConnectionsPool = new DatabaseConnectionPool($container, $config['db-pool-size']);
        $broadcaster->addAction(new SavePacketAction($databaseConnectionsPool, $config['table-name']));
    }

    return $broadcaster;
});

$container->add(UdpPacketParser::class)->addArgument($config['use-msgPack']);
$container->add(SwooleUdpServerEventHandler::class)
    ->addArgument(UdpPacketParser::class)
    ->addArgument(BroadcastPacketService::class)
    ->addArgument($config['appId']);
$swooleUdpEventsHandler = $container->get(SwooleUdpServerEventHandler::class);
