<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

final class Training extends Model
{
    protected $casts = [
        'start_at' => 'datetime',
        'length' => 'integer',
        'max_attendees' => 'integer',
        'can_attend_more' => 'boolean',
    ];

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'trainings_attendance')
            ->withPivot([
                'extra',
                'attended',
                'comment',
            ])
            ->withTimestamps();
    }

    public function hasAttendee(User $attendee): bool
    {
        return $this->attendees()
            ->where('user_id', '=', $attendee->id)
            ->exists();
    }

    public function doesntHaveAttendee(User $attendee): bool
    {
        return ! $this->hasAttendee($attendee);
    }
}
