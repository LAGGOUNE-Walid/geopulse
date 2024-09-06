<?php

use GeoJson\GeoJson;
use GeoJson\Geometry\Point;
use PHPUnit\Framework\TestCase;
use Pulse\Server\PacketParser\UdpPacketParser;

final class UdpPacketParserTest extends TestCase
{
    public function setUp(): void
    {
        error_reporting(E_ALL);
    }

    public function testThatMsgPackedDataIsUnpacked(): void
    {
        $udpPacketParser = new UdpPacketParser(true);
        $data = msgpack_pack(['appId' => 123, 'clientId' => 123, 'data' => []]);
        $packet = $udpPacketParser->fromString($data);
        $this->assertEquals($packet->getAppId(), 123);
    }

    public function testThatMsgPackedDataIsNotUnpackedIfMsgpackDisabled(): void
    {
        $udpPacketParser = new UdpPacketParser(false);
        $data = json_encode(['appId' => 123, 'clientId' => 123, 'data' => []]);
        $packet = $udpPacketParser->fromString($data);
        $this->assertEquals($packet->getAppId(), 123);
    }

    public function testGettingAppId(): void
    {
        $udpPacketParser = new UdpPacketParser(false);
        $data = '{"data":{"type":"Point","coordinates":[1,1]},"appId":"22f8e456-93f2-4173-8f2d-8a010abcceb1","clientId":"22f8e456-93f2-4173-8f2d-8a010abcceb1"}';
        $packet = $udpPacketParser->fromString($data);
        $this->assertEquals('22f8e456-93f2-4173-8f2d-8a010abcceb1', $packet->getAppId());
    }

    public function testGettingPointFromData(): void
    {
        $udpPacketParser = new UdpPacketParser(false);
        $data = '{"data":{"type":"Point","coordinates":[1,1]},"appId":"22f8e456-93f2-4173-8f2d-8a010abcceb1","clientId":"22f8e456-93f2-4173-8f2d-8a010abcceb1"}';
        $packet = $udpPacketParser->fromString($data);
        $this->assertTrue($packet->toPoint() instanceof Point);
    }

    public function testGettingNullPointFromDataThatDosentHaveJson(): void
    {
        $udpPacketParser = new UdpPacketParser(false);
        $data = '';
        $packet = $udpPacketParser->fromString($data);
        $this->assertEquals(null, $packet);
    }

    public function testGettingNullPointFromData(): void
    {
        $udpPacketParser = new UdpPacketParser(false);
        $data = '{"data":{},"appId":"22f8e456-93f2-4173-8f2d-8a010abcceb1","clientId":"22f8e456-93f2-4173-8f2d-8a010abcceb1"}';
        $packet = $udpPacketParser->fromString($data);
        $this->assertEquals([0,0], $packet->toPoint()->getCoordinates());
    }

    public function testGettingEmptyJsonOfNonValidePoint(): void
    {
        $udpPacketParser = new UdpPacketParser(false);
        $data = '{"data":{"type":"Point"},"appId":"22f8e456-93f2-4173-8f2d-8a010abcceb1","clientId":"22f8e456-93f2-4173-8f2d-8a010abcceb1"}';
        $packet = $udpPacketParser->fromString($data);
        $this->assertEquals($packet->toPoint()->getCoordinates(), [0, 0]);
        $this->assertEquals('{"point":{"type":"Point","coordinates":[0,0]},"appId":"22f8e456-93f2-4173-8f2d-8a010abcceb1","clientId":"22f8e456-93f2-4173-8f2d-8a010abcceb1"}', $packet->toJson());
    }
}
