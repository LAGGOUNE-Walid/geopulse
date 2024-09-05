<?php

namespace Pulse\Server\EventHandler;

use Pulse\Contracts\PacketParser\Packet;
use Pulse\Services\BroadcastPacketService;
use Swoole\Coroutine as Co;
use Swoole\Server;
use Swoole\Server\Task;

class SwooleUdpServerEventHandler
{
    public function __construct(private Packet $udpPacketParser, private BroadcastPacketService $broadcastPacketService, private string $appId) {}

    public function onPacket(Server $server, string $data, array $clientInfo): bool
    {
        // Read and unpack the message if it is compressed with msgpack
        // This uses the udpPacketParser service to deserialize the incoming UDP packet data
        $packet = $this->udpPacketParser->fromString($data);

        // Verify that the App ID sent from the client matches the server's configured App ID
        // This ensures that only authorized clients can send data to the server
        if ($this->appId !== $packet->getAppId()) {
            // Since UDP is connectionless protocol, we simply return false if the App ID does not match
            // No further action is needed for unauthorized packets
            return false;
        }
        $this->broadcastPacketService->dropAndPopPacket($packet);

        return true;
    }
}
