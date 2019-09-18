<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingAttendRequest;
use App\Http\Requests\TrainingCreationRequest;
use App\Http\Requests\TrainingDestroyRequest;
use App\Http\Requests\TrainingUpdateRequest;
use App\Training;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

class TrainingController extends Controller
{
    public function index()
    {
        return Inertia::render('Trainings/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'trainings' => Training::orderBy('start_at', 'desc')
                    ->paginate()
                    ->transform(function ($training) {
                        return [
                            'id' => $training->id,
                            'name' => $training->name,
                            'place' => $training->place,
                            'start_at' => $training->start_at->format('Y-m-d H:i'),
                            'diff' => $training->start_at->diffForHumans(),
                            'length' => $training->length,
                            'attendees' => $training->attendees()->count(),
                            'max_attendees' => $training->max_attendees,
                        ];
                }),
        ]);
    }

    public function view(Training $training)
    {
        return Inertia::render('Trainings/View', [
            'training' => [
                'id' => $training->id,
                'name' => $training->name,
                'place' => $training->place,
                'start_at' => $training->start_at->format('Y-m-d H:i'),
                'diff' => $training->start_at->diffForHumans(),
                'length' => $training->length,
                'attendees' => $training->attendees,
                'registered' => $training->attendees->contains(Auth::user()->id),
                'max_attendees' => $training->max_attendees,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Trainings/Create');
    }

    public function store(TrainingCreationRequest $request)
    {
        Training::create($request->only([
            'name',
            'place',
            'start_at',
            'length',
            'max_attendees',
        ]));

        return Redirect::route('trainings')->with('success', 'Edzés sikeresen létrehozva.');
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
                'start_at' => $training->start_at->format('Y-m-d H:i:s'),
                'length' => $training->length,
                'attendees' => $training->attendees,
                'max_attendees' => $training->max_attendees,
            ],
        ]);
    }

    public function update(TrainingUpdateRequest $request, Training $training)
    {
        $training->update($request->only(['name', 'place', 'start_at', 'length', 'max_attendees']));

        return Redirect::route('trainings.edit', $training)->with('success', 'Edzés sikeresen mentve.');
    }

    public function destroy(TrainingDestroyRequest $request, Training $training)
    {
        $training->delete();

        return Redirect::route('trainings')->with('success', 'Edzés sikeresen törölve.');
    }

    public function attend(Training $training)
    {
        if (Auth::user()->trainings->contains($training->id)) {
            return Redirect::back()->with('error', 'Már jelentkeztél az edzésre.');
        }

        if ($training->attendees->count() >= $training->max_attendees) {
            return Redirect::back()->with('error', 'Már megtelt az edzés, nem lehet rá jelentkezni.');
        }

        Auth::user()->trainings()->attach($training->id);

        return Redirect::back()->with('success', 'Sikeresen jelentkeztél az edzésre.');
    }

    public function withdraw(Training $training)
    {
        Auth::user()->trainings()->detach($training->id);

        return Redirect::back()->with('success', 'Sikeresen visszavontad a jelentkezésed.');
    }
}
