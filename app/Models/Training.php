<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

final class Training extends Model
{
    protected $dates = [
        'start_at',
    ];

    protected $casts = [
        'length' => 'integer',
        'max_attendees' => 'integer',
        'can_attend_more' => 'boolean',
    ];

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'trainings_attendance')
            ->withPivot(['extra', 'attended', 'comment'])
            ->withTimestamps();
    }

    public function hasAttendee(User $attendee)
    {
        return $this->attendees()->whereUserId($attendee->id)->exists();
    }

    public function doesntHaveAttendee(User $attendee)
    {
        return $this->attendees()->whereUserId($attendee->id)->doesntExist();
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('place', 'like', '%'.$search.'%')
                    ->orWhereRaw('CAST(start_at AS VARCHAR) LIKE ?', '%'.$search.'%');
            });
        })->when($filters['type'] ?? null, function ($query, $type) {
            $query->where('type', $type);
        })->when($filters['start_at'] ?? null, function ($query, $start_at) {
            if ($start_at === 'future') {
                $query->where('start_at', '>=', Carbon::now());

                return;
            }

            if ($start_at === 'past') {
                $query->where('start_at', '<=', Carbon::now());

                return;
            }

            if ($start_at === 'this_year') {
                $query->whereYear('start_at', Carbon::now()->year);

                return;
            }

            if ($start_at === 'next_seven_days') {
                $query->whereBetween('start_at', [
                    Carbon::now(),
                    Carbon::now()->addDays(7)
                ]);

                return ;
            }

            if ($start_at === 'prev_seven_days') {
                $query->whereBetween('start_at', [
                    Carbon::now()->addDays(-7),
                    Carbon::now()
                ]);

                return;
            }
        })->when($filters['attendance'] ?? null, function ($query, $attendance) {
            if (in_array($attendance, ['attended', 'not_attended'])) {
                $query->whereIn('id', function ($q) use ($attendance) {
                    $operator = $attendance === 'attended' ? '=' : '<>';

                    $q->select('training_id')
                        ->from('trainings_attendance')
                        ->where('user_id', $operator, Auth::user()->id);
                });
            }
        });
    }
}
