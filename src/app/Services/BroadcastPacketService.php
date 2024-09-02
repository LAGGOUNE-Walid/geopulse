<?php

namespace Pulse\Services;

use Pulse\Contracts\Action\PacketActionContract;
use Pulse\Contracts\PacketParser\Packet;
use function Swoole\Coroutine\go;
use function Swoole\Coroutine\run;
use Swoole\Coroutine;

class BroadcastPacketService
{
    private array $actions = [];

    public function addAction(PacketActionContract $action): void
    {
        $this->actions[] = $action;
    }

    public function dropAndPopPacket(Packet $packet): void
    {
        foreach($this->actions as $action) {
            $action->handle($packet);
        }
    }
}
