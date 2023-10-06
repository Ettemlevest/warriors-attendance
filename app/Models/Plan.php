<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Plan
 *
 * @property int $id
 * @property string $name
 * @property int $sessions
 * @property int|null $expiration_in_days
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Plan query()
 */
class Plan extends Model
{
    use SoftDeletes;

    protected $casts = [
        'sessions' => 'integer',
        'expiration_in_days' => 'integer',
    ];

    protected $fillable = [
        'name',
        'sessions',
    ];

    protected $withCount = [
        'subscriptions',
    ];

    /**
     * @return HasMany<Subscription>
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
