<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Subscription
 *
 * @property int $id
 * @property int $plan_id
 * @property int $user_id
 * @property Carbon $purchased_at
 * @property Carbon|null $expired_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Plan $plan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TrainingAttendance> $usages
 * @property-read int|null $usages_count
 * @property-read \App\Models\User $user
 * @property-read bool $expired
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Subscription query()
 */
class Subscription extends Model
{
    use HasFactory;

    protected $casts = [
        'purchased_at' => 'date',
        'expired_at' => 'date',
        'expired' => 'boolean',
    ];

    protected $appends = [
        'expired',
    ];

    protected $fillable = [
        'plan_id',
        'user_id',
        'purchased_at',
        'expired_at',
    ];

    protected $with = [
        'plan',
    ];

    protected $withCount = [
        'usages',
    ];

    public function expired(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['expired_at'] !== null
        );
    }

    /**
     * @return BelongsTo<Plan, Subscription>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * @return BelongsTo<User, Subscription>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<TrainingAttendance>
     */
    public function usages(): HasMany
    {
        return $this->hasMany(TrainingAttendance::class);
    }

    public static function findLiveSubscriptionForUser(int $userId): ?Subscription
    {
        return self::query()
            ->where('user_id', '=', $userId)
            ->whereNull('expired_at')
            ->orderBy('purchased_at')
            ->first();
    }

    public function refreshExpiry(): void
    {
        $this->refresh();

        $this->update([
            'expired_at' => $this->usedUp() ? Carbon::now() : null,
        ]);
    }

    public function usedUp(): bool
    {
        if (! $this->exists) {
            return false;
        }

        return $this->usages_count >= $this->plan->sessions;
    }
}
