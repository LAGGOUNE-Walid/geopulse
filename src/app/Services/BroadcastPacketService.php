<?php

namespace Pulse\Services;

use Pulse\Contracts\Action\PacketActionContract;
use Pulse\Contracts\PacketParser\Packet;

use function Swoole\Coroutine\go;

class BroadcastPacketService
{
    private array $actions = [];

    public function addAction(PacketActionContract $action): void
    {
        $this->actions[] = $action;
    }

    public function dropAndPopPacket(Packet $packet): void
    {
        foreach ($this->actions as $action) {
            // @phpstan-ignore-next-line
            go(function () use ($action, $packet) {
                $action->handle($packet); 
            });

        }
    }
}
