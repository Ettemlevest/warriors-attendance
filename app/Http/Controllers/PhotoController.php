<?php

namespace App\Http\Controllers;

use App\Http\Requests\PhotoDestroyRequest;
use App\Photo;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function destroy(PhotoDestroyRequest $request, Photo $photo)
    {
        $album = $photo->album;

        Storage::delete($photo->path);

        $photo->delete();

        return Redirect::route('albums.edit', $album)->with('success', 'Kép sikeresen törölve.');
    }
}
