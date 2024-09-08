<?php

namespace Pulse\Actions;

use Swoole\ConnectionPool;
use Illuminate\Database\Connection;
use Pulse\Contracts\PacketParser\Packet;
use Illuminate\Database\PostgresConnection;
use Illuminate\Database\Capsule\Manager as DB;
use Pulse\Contracts\Action\PacketActionContract;

class SavePacketAction implements PacketActionContract
{
    public function __construct(private ConnectionPool $databaseConnectionsPool, public string $table) {}

    public function handle(Packet $packet): void
    {
        $db = $this->databaseConnectionsPool->get();

        $db->table($this->table)->insert([
            'appId' => $packet->getAppId(),
            'clientId' => $packet->getClientId(),
            'coordinate' => DB::raw($this->buildInsertPointQuery($packet->toPoint()->getCoordinates(), $db)),
        ]);
        $this->databaseConnectionsPool->put($db);
    }

    public function buildInsertPointQuery(array $point, Connection $connection): string
    {
        if ($connection instanceof PostgresConnection) {
            return "ST_GeomFromText('POINT(".implode(' ', $point).")')::POINT";
        }
        return "ST_GeomFromText('POINT(".implode(' ', $point).")')";
    }

}
