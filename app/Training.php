<?php

namespace App;

class Training extends Model
{
    protected $dates = [
        'start_at',
    ];

    protected $casts = [
        'length' => 'integer',
        'max_attendees' => 'integer',
    ];

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'trainings_attendance')->withTimestamps();
    }
}
