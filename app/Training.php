<?php

namespace App;

use Carbon\Carbon;

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

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('place', 'like', '%'.$search.'%');
            });
        })->when($filters['start_at'] ?? null, function ($query, $start_at) {
            if ($start_at === 'future') {
                $query->where('start_at', '>=', Carbon::now());
            } elseif ($start_at === 'past') {
                $query->where('start_at', '<=', Carbon::now());
            }
        });
    }
}
