<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\TrainingAttendance
 *
 * @property int $id
 * @property int $training_id
 * @property int $user_id
 * @property bool $extra
 * @property bool $attended
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Training|null $training
 * @property-read User|null $user
 * @property-read Subscription|null $subscription
 */
class TrainingAttendance extends Model
{
    use HasFactory;

    protected $table = 'trainings_attendance';

    protected $casts = [
        'extra' => 'boolean',
        'attended' => 'boolean',
    ];

    protected $fillable = [
        'training_id',
        'user_id',
        'subscription_id',
        'extra',
        'attended',
        'comment',
    ];

    protected $with = [
        'training',
        'user',
        'subscription',
    ];

    public function confirmAttendance(): void
    {
        if ($this->subscription === null) {
            $this->subscription()->associate(Subscription::findLiveSubscriptionForUser($this->user_id));
        }

        $this->update([
            'attended' => true,
        ]);

        $this->subscription?->refreshExpiry();
    }

    public function rejectAttendance(): void
    {
        $subscription = $this->subscription;

        $this->subscription()->dissociate();

        $this->update([
            'attended' => false,
        ]);

        $subscription?->refreshExpiry();
    }

    /**
     * @return BelongsTo<Training, $this>
     */
    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Subscription, $this>
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
