<?php

require 'bin/bootstrap.php';

$server = new Swoole\Server(
    '0.0.0.0',
    $config['port'],
    SWOOLE_PROCESS,
    SWOOLE_SOCK_UDP
);
$server->set($config['swoole']);
$server->on('Packet', [$swooleUdpEventsHandler, 'onPacket']);
$server->on('Task', [$swooleUdpEventsHandler, 'onTask']);
$server->start();
