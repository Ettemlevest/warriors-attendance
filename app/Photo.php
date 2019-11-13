<?php

namespace App;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use League\Glide\Server;

final class Photo extends Model
{
    public function photoUrl(array $attributes)
    {
        if ($this->path) {
            return URL::to(App::make(Server::class)->fromPath($this->path, $attributes));
        }
    }

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}
