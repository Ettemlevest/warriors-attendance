<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Message extends Model
{
    protected $casts = [
        'showed_from' => 'datetime',
        'showed_to' => 'datetime',
    ];

    protected $guarded = [];

    /**
     * @return Collection<Message>
     */
    public static function activeMessages(): Collection
    {
        $now = Carbon::now();

        return Message::query()
            ->where('showed_from', '<=', $now)
            ->where('showed_to', '>=', $now)
            ->get();
    }
}
