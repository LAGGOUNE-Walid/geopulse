<?php

use Illuminate\Queue\Capsule\Manager as Queue;
use Illuminate\Database\Capsule\Manager as Capsule;

require 'vendor/autoload.php';
$config = require 'config/pulse.php';

$queue = new Queue;

$queue->addConnection($config['queue-connection']);
$queue->setAsGlobal();


$db = new Capsule;

$db->addConnection($config['database-connection']);

$server = new Swoole\Server('0.0.0.0', $config['port'], SWOOLE_PROCESS, $config['protocol']);
$server->set($config['swoole']);

$server->on('Packet', function ($server, $data, $clientInfo) {
    var_dump($data);
    $task_id = $server->task($data);
    var_dump("task id ".$task_id);
});

$server->on('Task', function ($server, $task_id, $reactor_id, $data) {
    echo "New AsyncTask[id={$task_id}]".PHP_EOL;
});
$server->start();