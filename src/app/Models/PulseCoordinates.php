<?php

namespace Pulse\Models;

use Illuminate\Database\Eloquent\Model;

class PulseCoordinates extends Model
{
    protected $fillable = [
        'appId',
        'clientId',
        'coordinate',
    ];

    protected $table = 'pulse_coordinates';
}
