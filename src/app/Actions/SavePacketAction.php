<?php

namespace Pulse\Actions;

use Illuminate\Database\Capsule\Manager as DB;
use Pulse\Contracts\Action\PacketActionContract;
use Pulse\Contracts\PacketParser\Packet;
use Pulse\Models\PulseCoordinates;

class SavePacketAction implements PacketActionContract
{
    public function handle(Packet $packet): void
    {
        $connection = new PulseCoordinates;
        $connection->appId = $packet->getAppId();
        $connection->clientId = $packet->getClientId();
        $connection->coordinate = DB::raw("(GeomFromText('POINT(".implode(' ', $packet->toPoint()->getCoordinates()).")'))");
        $connection->save();
    }
}
