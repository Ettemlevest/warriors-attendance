<?php

namespace App\Http\Controllers;

use League\Glide\Server;

final class ImagesController extends Controller
{
    public function show(Server $glide)
    {
        return $glide->fromRequest()->response();
    }
}
