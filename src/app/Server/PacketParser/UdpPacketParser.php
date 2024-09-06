<?php

namespace Pulse\Server\PacketParser;

use GeoJson\GeoJson;
use GeoJson\Geometry\Point;
use Pulse\Contracts\PacketParser\Packet;

class UdpPacketParser implements Packet
{
    /**
     * The raw payload data associated with the packet.
     *
     * @var array{
     *     type: string,
     *     coordinates: array<float>
     * }
     */
    private array $payload;

    /**
     * The Application ID extracted from the packet data.
     */
    private string $appId;

    /**
     * The Client ID extracted from the packet data.
     */
    private string $clientId;

    public function __construct(private bool $usingMsgPack) {}

    public function fromString(string $data): ?Packet
    {
        try {
            if ($this->usingMsgPack) {
                $unpackedData = msgpack_unpack($data);
            } else {
                $unpackedData = json_decode($data, true);
            }
        } catch (\Throwable $th) {
            $unpackedData = [];
        }

        if ($unpackedData !== [] and $unpackedData !== null and is_array($unpackedData)) {
            if ($this->dataIsValide($unpackedData)) {
                $this->appId = $unpackedData['appId'];
                $this->clientId = $unpackedData['clientId'];
                $this->payload = $unpackedData['data'];
                return $this;
            }
        }

        return null;
    }

    public function dataIsValide(array $data): bool
    {
        return array_key_exists('appId', $data) and array_key_exists('clientId', $data) and array_key_exists('data', $data);
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function toPoint(): Point
    {
        try {
            $point = GeoJson::jsonUnserialize($this->payload);
        } catch (\Throwable $th) {
            return GeoJson::jsonUnserialize(['type' => 'Point', 'coordinates' => [0, 0]]);
        }

        if (! ($point instanceof Point)) {
            return GeoJson::jsonUnserialize(['type' => 'Point', 'coordinates' => [0, 0]]);
        }

        return $point;
    }

    public function toJson(): string
    {
        $json = json_encode($this->toArray());
        if (! $json) {
            return "{}";
        }
        return $json;
    }

    public function toArray(): array
    {
        return [
            'point' => $this->toPoint(),
            'appId' => $this->getAppId(),
            'clientId' => $this->getClientId(),
        ];
    }
}
