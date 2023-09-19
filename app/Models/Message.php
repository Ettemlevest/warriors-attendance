<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property string $title
 * @property string|null $body
 * @property \Illuminate\Support\Carbon|null $showed_from
 * @property \Illuminate\Support\Carbon|null $showed_to
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Message query()
 *
 * @mixin Model
 */
class Message extends Model
{
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
}
