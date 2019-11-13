<?php

namespace App;

final class Album extends Model
{
    protected $dates = [
        'date_from',
        'date_to',
    ];

    public function coverPhoto()
    {
        return $this->hasOne(Photo::class, 'id', 'cover_photo_id');
    }

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}
