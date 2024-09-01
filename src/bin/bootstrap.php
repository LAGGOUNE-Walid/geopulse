<?php

use Illuminate\Container\Container as LaravelContainer;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Queue\Capsule\Manager as Queue;
use Illuminate\Redis\RedisManager;
use League\Container\Container;
use Pulse\Actions\EnqueuePacketAction;
use Pulse\Actions\SavePacketAction;
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
    $container->get(Queue::class);
}

if ($config['enable-database']) {
    $container->add(Capsule::class, function () use ($config) {
        $db = new Capsule;

        $db->addConnection($config['database-connection']);
        $db->setAsGlobal();
        $db->bootEloquent();

        return $db;
    });
    $container->get(Capsule::class);
}

$container->add(BroadcastPacketService::class, function () use ($config) {
    $broadcaster = new BroadcastPacketService;
    if ($config['enable-queue']) {
        $broadcaster->addAction(new EnqueuePacketAction);
    }
    if ($config['enable-database']) {
        $broadcaster->addAction(new SavePacketAction);
    }

    return $broadcaster;
});

$container->add(UdpPacketParser::class)->addArgument($config['use-msgPack']);
$container->add(SwooleUdpServerEventHandler::class)
    ->addArgument(UdpPacketParser::class)
    ->addArgument(BroadcastPacketService::class)
    ->addArgument($config['appId']);
$swooleUdpEventsHandler = $container->get(SwooleUdpServerEventHandler::class);
