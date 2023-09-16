<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $casts = [
        'showed_from' => 'datetime',
        'showed_to' => 'datetime',
    ];

    protected $guarded = [];
}
