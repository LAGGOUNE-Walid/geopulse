<?php

use Swoole\Coroutine\Client;

use function Swoole\Coroutine\run;

run(function () {
    $client = new Client(SWOOLE_SOCK_UDP);
    if (! $client->connect('0.0.0.0', 9505, 0.5)) {
        echo "connect failed. Error: {$client->errCode}\n";
    }
    $data = ['appId' => 'your_app_id_here', 'clientId' => '22f8e456-93f2-4173-8f2d-8a010abcceb1', 'data' => ['type' => 'Point', 'coordinates' => [1, 1]]];
    $data = msgpack_pack($data);
    $client->send($data);
    $client->close();
});
