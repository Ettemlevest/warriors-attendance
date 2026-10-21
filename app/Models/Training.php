<?php

namespace App\Models;

use App\Enums\TrainingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 * @property TrainingType $type
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
    use HasFactory;

    protected $casts = [
        'type' => TrainingType::class,
        'start_at' => 'datetime',
        'length' => 'integer',
        'max_attendees' => 'integer',
        'can_attend_more' => 'boolean',
    ];

    protected $fillable = [
        'type',
        'name',
        'place',
        'start_at',
        'length',
        'max_attendees',
        'can_attend_more',
        'description',
    ];

    public function attendees(): HasMany
    {
        return $this->hasMany(TrainingAttendance::class);
    }

    public function hasAttendee(User $attendee): bool
    {
        return $this->attendees->contains('user_id', $attendee->id);
    }

    public function doesntHaveAttendee(User $attendee): bool
    {
        return $this->attendees->doesntContain('user_id', $attendee->id);
    }

    public function hasUnlimitedCapacity(): bool
    {
        return $this->max_attendees === 0;
    }

    public function registeredAttendeesCount(): int
    {
        return $this->attendees_count ?? $this->attendees()->count();
    }

    public function isFull(): bool
    {
        if ($this->hasUnlimitedCapacity()) {
            return false;
        }

        return $this->registeredAttendeesCount() >= $this->max_attendees;
    }

    public function registerAttendee(User $user): TrainingAttendance
    {
        return $this->attendees()->create([
            'user_id' => $user->id,
            'extra' => $this->isFull(),
        ]);
    }

    public function canAcceptRegistration(): bool
    {
        if (! $this->isFull()) {
            return true;
        }

        return $this->can_attend_more;
    }
}
