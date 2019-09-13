<?php

namespace App\Http\Controllers;

use App\Training;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class TrainingController extends Controller
{
    public function index()
    {
        return Inertia::render('Trainings/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'trainings' => Training::orderBy('start_at', 'desc')
                    ->get()
                    ->transform(function ($training) {
                        return [
                            'id' => $training->id,
                            'name' => $training->name,
                            'place' => $training->place,
                            'start_at' => $training->start_at->format('Y-m-d H:i'),
                            'diff' => $training->start_at->diffForHumans(),
                            'length' => $training->length,
                            'attendees' => 0,
                            'max_attendees' => $training->max_attendees,
                        ];
                }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Trainings/Create');
    }

    public function store(Request $request)
    {
        if (! Auth::user()->owner) {
            return Redirect::route('trainings');
        }

        Training::create([
            'name' => $request->name,
            'place' => $request->place,
            'start_at' => $request->start_at,
            'length' => $request->length,
            'max_attendees' => $request->max_attendees,
        ]);
    }

    public function edit(Training $training)
    {
        if (! Auth::user()->owner) {
            return Redirect::route('trainings');
        }

        return Inertia::render('Trainings/Edit', [
            'training' => [
                'id' => $training->id,
                'name' => $training->name,
                'place' => $training->place,
                'start_at' => $training->start_at,
                'length' => $training->length,
                'max_attendees' => $training->max_attendees,
            ],
        ]);
    }

    public function update(Request $request, Training $training)
    {
        if (! Auth::user()->owner) {
            return Redirect::route('trainings');
        }

        $training->update($request->only(['name', 'place', 'start_at', 'length', 'max_attendees']));

        return Redirect::route('trainings.edit', $training)->with('success', 'Edzés sikeresen mentve.');
    }

    public function destroy(Training $training)
    {
        if (! Auth::user()->owner) {
            return Redirect::route('trainings');
        }
    }
}
