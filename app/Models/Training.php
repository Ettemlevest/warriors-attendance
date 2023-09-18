<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\Models\Training
 *
 * @property int $id
 * @property string $name
 * @property string $place
 * @property \Illuminate\Support\Carbon $start_at
 * @property int $length
 * @property int $max_attendees
 * @property bool $can_attend_more
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $type
 * @property string|null $description
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $attendees
 * @property-read int|null $attendees_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Training newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Training newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Training query()
 *
 * @mixin Model
 */
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
