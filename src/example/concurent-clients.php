<?php

use Swoole\Coroutine;
use Swoole\Coroutine\Client;

use function Swoole\Coroutine\run;

$numberOfClients = 10000;

$sendInterval = 100;

$data = ['appId' => '123', 'clientId' => '22f8e456-93f2-4173-8f2d-8a010abcceb1', 'data' => ['type' => 'Point', 'coordinates' => [1, 1]]];
$jsonData = msgpack_pack($data);

function simulateClient($host, $port, $jsonData, $sendInterval)
{
    $client = new Client(SWOOLE_SOCK_UDP);

    if (! $client->connect($host, $port, 0.5)) {
        echo "Connect failed. Error: {$client->errCode}\n";

        return;
    }
    while (true) {
        $client->send($jsonData);
        Coroutine::sleep($sendInterval / 1000);
    }

    $client->close();
}

run(function () use ($numberOfClients, $jsonData, $sendInterval) {
    $host = '192.168.1.15';
    $port = 9505;
    for ($i = 0; $i < $numberOfClients; $i++) {
        go(function () use ($host, $port, $jsonData, $sendInterval) {
            simulateClient($host, $port, $jsonData, $sendInterval);
        });
    }

    echo "Load test with {$numberOfClients} clients sending messages every {$sendInterval}ms started.\n";
});
