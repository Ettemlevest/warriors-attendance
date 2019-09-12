<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $dates = [
        'start_at',
        'end_at',
    ];
}
