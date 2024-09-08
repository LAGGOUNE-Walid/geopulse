<?php

use Swoole\Coroutine\Client;

use function Swoole\Coroutine\run;

run(function () {
    $client = new Client(SWOOLE_SOCK_UDP);
    if (! $client->connect('192.168.1.4', 9505, 0.5)) {
        echo "connect failed. Error: {$client->errCode}\n";
    }
    $data = ['appId' => '123', 'clientId' => '22f8e456-93f2-4173-8f2d-8a010abcceb1', 'data' => ['type' => 'Point', 'coordinates' => [-14.80665, -140.22159]]];
    // $data = msgpack_pack($data);
    $data = json_encode($data);
    $client->send($data);
    $client->close();
});
