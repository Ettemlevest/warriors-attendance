<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrainingCreationRequest;
use App\Http\Requests\TrainingDestroyRequest;
use App\Http\Requests\TrainingUpdateRequest;
use App\Http\Resources\UserResource;
use App\Training;
use App\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;

final class TrainingController extends Controller
{
    public function index()
    {
        return Inertia::render('Trainings/Index', [
            'filters' => Request::all('search', 'start_at', 'attendance'),
            'trainings' => Training::orderBy('start_at', 'desc')
                    ->filter(Request::all('search', 'start_at', 'attendance'))
                    ->with('attendees')
                    ->paginate()
                    ->transform(function ($training) {
                        return [
                            'id' => $training->id,
                            'name' => $training->name,
                            'place' => $training->place,
                            'start_at' => $training->start_at->format('Y-m-d H:i'),
                            'diff' => $training->start_at->diffForHumans(),
                            'length' => $training->length,
                            'attendees' => $training->attendees->count(),
                            'max_attendees' => $training->max_attendees,
                            'can_attend_more' => (boolean)$training->can_attend_more,
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
                'start_at' => $training->start_at,
                'start_at_day' => $training->start_at->format('Y-m-d'),
                'start_at_time' => $training->start_at->format('H:i'),
                'diff' => $training->start_at->diffForHumans(),
                'length' => $training->length,
                'attendees' => UserResource::collection($training->attendees),
                'registered' => $training->attendees->contains(Auth::user()->id),
                'max_attendees' => $training->max_attendees,
                'can_attend_more' => (boolean)$training->can_attend_more,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Trainings/Create');
    }

    public function store(TrainingCreationRequest $request)
    {
        Training::create($this->prepareDataForDB($request->all()));

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
                'start_at' => $training->start_at,
                'start_at_day' => $training->start_at->format('Y-m-d'),
                'start_at_time' => $training->start_at->format('H:i'),
                'length' => $training->length,
                'attendees' => UserResource::collection($training->attendees),
                'max_attendees' => $training->max_attendees,
                'can_attend_more' => (boolean)$training->can_attend_more,
            ],
        ]);
    }

    public function update(TrainingUpdateRequest $request, Training $training)
    {
        // do not allow to modify a training after two days have past from start time
        if ($training->start_at < now()->addDay(-2)) {
            return Redirect::back()->with('error', 'Múltbeli edzés már nem módosítható!');
        }

        $training->update($this->prepareDataForDB($request->all()));

        return Redirect::route('trainings.edit', $training)->with('success', 'Edzés sikeresen mentve.');
    }

    public function destroy(TrainingDestroyRequest $request, Training $training)
    {
        // do not allow to delete a training in the past
        if ($training->start_at < now()) {
            return Redirect::back()->with('error', 'Múltbeli edzés már nem törölhető!');
        }

        $training->delete();

        return Redirect::route('trainings')->with('success', 'Edzés sikeresen törölve.');
    }

    public function attend(Training $training)
    {
        $message = 'Sikeresen jelentkeztél az edzésre.';
        $extra = false;

        if (Auth::user()->trainings->contains($training->id)) {
            return Redirect::back()->with('error', 'Már jelentkeztél az edzésre.');
        }

        if ($training->start_at < now()) {
            return Redirect::back()->with('error', 'Nem megengedett művelet múltbeli edzéshez!');
        }

        if ($training->start_at > now()->addDays(7)) {
            return Redirect::back()->with('error', 'Még nem lehet jelentkezni az edzésre!');
        }

        if (
            $training->attendees->count() >= $training->max_attendees
            &&
            ! $training->can_attend_more
        ) {
            return Redirect::back()->with('error', 'Már megtelt az edzés, nem lehet rá jelentkezni.');
        }

        if ($training->can_attend_more && $training->max_attendees > 0 && $training->attendees->count() >= $training->max_attendees) {
            $extra = true;
            $message .= ' Vállaltam a 10 burpeet beugrónak!';
        }

        Auth::user()->trainings()->attach($training->id, ['extra' => $extra]);

        return Redirect::back()->with('success', $message);
    }

    public function withdraw(Training $training)
    {
        if ($training->start_at < now()) {
            return Redirect::back()->with('error', 'Nem megengedett művelet múltbeli edzéshez!');
        }

        Auth::user()->trainings()->detach($training->id);

        return Redirect::back()->with('success', 'Sikeresen visszavontad a jelentkezésed.');
    }

    public function confirmAttendance(Training $training, User $attendee)
    {
        if ($training->doesntHaveAttendee($attendee)) {
            return Redirect::back()->with('error', 'A Warrior nem jelentkezett az edzésre!');
        }

        if ($training->start_at > now()) {
            return Redirect::back()->with("error", "Még nem indult el az edzés!");
        }

        $training->attendees()->updateExistingPivot($attendee, ['attended' => true]);

        return Redirect::back()->with('success', "Sikeresen jóváhagytad {$attendee->name} részvételét.");
    }

    public function rejectAttendance(Training $training, User $attendee)
    {
        if ($training->doesntHaveAttendee($attendee)) {
            return Redirect::back()->with('error', 'A Warrior nem jelentkezett az edzésre!');
        }

        if ($training->start_at > now()) {
            return Redirect::back()->with("error", "Még nem indult el az edzés!");
        }

        $training->attendees()->updateExistingPivot($attendee, ['attended' => false]);

        return Redirect::back()->with('success', "Sikeresen törölted {$attendee->name} részvételét.");
    }

    protected function prepareDataForDB(Array $inputs)
    {
        return [
            'name' => $inputs['name'],
            'place' => $inputs['place'],
            'start_at' => $inputs['start_at_day'] .' '. $inputs['start_at_time'] .':00',
            'length' => $inputs['length'],
            'max_attendees' => $inputs['max_attendees'],
            'can_attend_more' => $inputs['can_attend_more'] == 'true' ? 1 : 0,
        ];
    }
}
