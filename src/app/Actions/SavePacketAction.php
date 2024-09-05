<?php

namespace Pulse\Actions;

use Illuminate\Database\Capsule\Manager as DB;
use Pulse\Contracts\Action\PacketActionContract;
use Pulse\Contracts\PacketParser\Packet;
use Swoole\ConnectionPool;

class SavePacketAction implements PacketActionContract
{
    public function __construct(private ConnectionPool $databaseConnectionsPool, public string $table) {}

    public function handle(Packet $packet): void
    {
        $db = $this->databaseConnectionsPool->get();
        $db->table($this->table)->insert([
            'appId' => $packet->getAppId(),
            'clientId' => $packet->getClientId(),
            'coordinate' => DB::raw("(GeomFromText('POINT(".implode(' ', $packet->toPoint()->getCoordinates()).")'))"),
        ]);
        $this->databaseConnectionsPool->put($db);
    }
}
