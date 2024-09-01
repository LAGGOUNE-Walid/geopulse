<?php

namespace Pulse\Server\PacketParser;

use GeoJson\GeoJson;
use GeoJson\Geometry\Point;
use Pulse\Contracts\PacketParser\Packet;

class UdpPacketParser implements Packet
{
    /**
     * The raw payload data associated with the packet.
     */
    private ?array $payload = null;

    /**
     * The Application ID extracted from the packet data.
     */
    private ?string $appId = null;

    /**
     * The Client ID extracted from the packet data.
     */
    private ?string $clientId = null;

    public function __construct(private bool $usingMsgPack) {}

    public function fromString(string $data): Packet
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

        if ($unpackedData !== [] and $unpackedData !== null) {
            if ($this->dataIsValide($unpackedData)) {
                $this->appId = $unpackedData['appId'];
                $this->clientId = $unpackedData['clientId'];
                $this->payload = $unpackedData['data'];
            }
        }

        return $this;
    }

    public function dataIsValide(array $data): bool
    {
        return array_key_exists('appId', $data) and array_key_exists('clientId', $data) and array_key_exists('data', $data);
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function toPoint(): ?Point
    {
        if (! $this->payload) {
            return null;
        }
        try {
            $point = GeoJson::jsonUnserialize($this->payload);
        } catch (\Throwable $th) {
            return null;
        }

        if (! ($point instanceof Point)) {
            return null;
        }

        return $point;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
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
