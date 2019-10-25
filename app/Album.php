<?php

namespace App;

final class Album extends Model
{
    protected $dates = [
        'date_from',
        'date_to',
    ];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
