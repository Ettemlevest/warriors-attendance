<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property string $title
 * @property string|null $body
 * @property Carbon|null $showed_from
 * @property Carbon|null $showed_to
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message query()
 *
 * @mixin Model
 */
class Message extends Model
{
    use HasFactory;

    protected $casts = [
        'showed_from' => 'datetime',
        'showed_to' => 'datetime',
    ];

    protected $fillable = [
        'title',
        'body',
        'showed_from',
        'showed_to',
    ];

    /**
     * @return Collection<Message>
     */
    public static function activeMessages(): Collection
    {
        $now = Carbon::now();

        return Message::query()
            ->where(fn (Builder $query) => $query->whereNull('showed_from')->orWhere('showed_from', '<=', $now))
            ->where(fn (Builder $query) => $query->whereNull('showed_to')->orWhere('showed_to', '>=', $now))
            ->get();
    }
}
