<?php

use PHPUnit\Framework\TestCase;
use Pulse\Actions\EnqueuePacketAction;
use Pulse\Actions\SavePacketAction;
use Pulse\Server\EventHandler\SwooleUdpServerEventHandler;
use Pulse\Server\PacketParser\UdpPacketParser;
use Pulse\Services\BroadcastPacketService;
use Swoole\Server;
use Swoole\Server\Task;

class SwooleUdpServerIntegrationTest extends TestCase
{
    public function setUp(): void
    {
        error_reporting(E_ALL);
    }

    public function testEndToEndDataFlow()
    {
        $packetData = json_encode([
            'appId' => 'validAppId',
            'clientId' => 'client123',
            'data' => [
                'type' => 'Point',
                'coordinates' => [102.0, 0.5],
            ],
        ]);

        $serverStub = $this->getMockBuilder(Server::class)->disableOriginalConstructor()->getMock();
        $serverStub->method('task')
            ->willReturn(1);
        $udpPacketParser = new UdpPacketParser(false);
        $broadcastService = $this->createMock(BroadcastPacketService::class);
        $enqueuePacketAction = $this->createMock(EnqueuePacketAction::class);
        $savePacketAction = $this->createMock(SavePacketAction::class);
        $broadcastService->addAction($enqueuePacketAction);
        $broadcastService->addAction($savePacketAction);

        $serverHandler = new SwooleUdpServerEventHandler($udpPacketParser, $broadcastService, 'validAppId');
        $broadcastService->expects($this->once())
            ->method('dropAndPopPacket');
        $result = $serverHandler->onPacket($serverStub, $packetData, ['address' => '127.0.0.1', 'port' => 12345]);
        $this->assertTrue($result);
        $packet = $udpPacketParser->fromString($packetData);
        $this->assertNotNull($packet);
    }
}
