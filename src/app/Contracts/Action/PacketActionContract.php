<?php

namespace Pulse\Contracts\Action;

use Pulse\Contracts\PacketParser\Packet;

interface PacketActionContract
{
    public function handle(Packet $data): void;
}
