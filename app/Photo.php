<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

final class Photo extends Model
{
    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
