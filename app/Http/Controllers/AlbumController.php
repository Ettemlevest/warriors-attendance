<?php

namespace App\Http\Controllers;

use App\Album;
use App\Http\Requests\AlbumCreationRequest;
use App\Http\Requests\AlbumDestroyRequest;
use App\Http\Requests\AlbumUpdateRequest;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

final class AlbumController extends Controller
{
    public function index()
    {
        return Inertia::render('Albums/Index', [
            'albums' => Album::orderBy('date_from', 'desc')
                ->get()
                ->transform(function ($album) {
                    return [
                        'id' => $album->id,
                        'name' => $album->name,
                        'description' => $album->description,
                        'place' => $album->place,
                        'date_from' => $album->date_from->format('Y-m-d'),
                        'date_to' => $album->date_to->format('Y-m-d'),
                        // 'cover_photo_url' => $album->coverUrl(['w' => 200, 'h' => 200, 'fit' => 'crop']),
                    ];
                }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Albums/Create');
    }

    public function store(AlbumCreationRequest $request)
    {
        $album = Album::create($request->all());

        return Redirect::route('albums.edit', $album)->with('success', 'Album sikeresen létrehozva.');
    }

    public function edit(Album $album)
    {
        if (! Auth::user()->owner) {
            return Redirect::route('albums');
        }



        return Inertia::render('Albums/Edit', [
            'album' => [
                'id' => $album->id,
                'name' => $album->name,
                'description' => $album->description,
                'place' => $album->place,
                'date_from' => $album->date_from->format('Y-m-d'),
                'date_to' => $album->date_to->format('Y-m-d'),
                'photos' => $album->photos->transform(function ($photo) {
                    return [
                        'id' => $photo->id,
                        'path' => $photo->photoUrl(['w' => 200, 'fit' => 'strech'])
                    ];
                }),
            ],
        ]);
    }

    public function update(AlbumUpdateRequest $request, Album $album)
    {
        $album->update($request->only([
            'name',
            'description',
            'place',
            'date_from',
            'date_to',
        ]));

        foreach($request->file('photos') as $file) {
            $path = $file->store('album_'.$album->id);

            $album->photos()->create([
                'path' => $path,
            ]);
        }

        return Redirect::route('albums.edit', $album)->with('success', 'Album sikeresen frissítve.');
    }

    public function destroy(AlbumDestroyRequest $request, Album $album)
    {
        Storage::deleteDirectory('album_'.$album->id);

        $album->delete();

        return Redirect::route('albums')->with('success', 'Album sikeresen törölve.');
    }
}
