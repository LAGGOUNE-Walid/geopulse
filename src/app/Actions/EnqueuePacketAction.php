<?php

namespace Pulse\Actions;

use Illuminate\Queue\Capsule\Manager as Queue;
use Pulse\Contracts\Action\PacketActionContract;
use Pulse\Contracts\PacketParser\Packet;

class EnqueuePacketAction implements PacketActionContract
{
    public function handle(Packet $packet): void
    {
        Queue::push('App\Jobs\PulseLocationUpdatedJob@handle', $packet->toArray());
    }
}
