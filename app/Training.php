<?php

namespace App;

class Training extends Model
{
    protected $dates = [
        'start_at',
    ];

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'trainings_attendance')->withTimestamps();
    }
}
