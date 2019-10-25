<?php

namespace App\Http\Controllers;

use App\Album;
use App\Http\Requests\AlbumCreationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
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
                'photos' => [],
            ],
        ]);
    }
}
