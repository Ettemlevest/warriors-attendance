<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Http\Requests\AlbumCoverSettingRequest;
use App\Http\Requests\AlbumCreationRequest;
use App\Http\Requests\AlbumDestroyRequest;
use App\Http\Requests\AlbumUpdateRequest;
use App\Models\Photo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use League\Glide\Server;

class AlbumController extends Controller
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
                        'cover_photo_url' => $album->coverPhoto
                            ? $album->coverPhoto->photoUrl(['w' => 500, 'h' => 500]) ?? ''
                            : URL::to(App::make(Server::class)->fromPath('placeholder-image.png', ['w' => 500, 'h' => 500])),
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

    public function view(Album $album)
    {
        return Inertia::render('Albums/View', [
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
                        'thumbnail' => $photo->photoUrl(['w' => 400, 'fit' => 'stretch']),
                        'src' => $photo->photoUrl(['fit' => 'contain']),
                    ];
                }),
            ],
        ]);
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

    public function setCover(AlbumCoverSettingRequest $request, Album $album, Photo $photo)
    {
        $album->update([
            'cover_photo_id' => $photo->id,
        ]);

        return Redirect::route('albums.edit', $album)->with('success', 'Borítókép sikeresen beállítva.');
    }
}
