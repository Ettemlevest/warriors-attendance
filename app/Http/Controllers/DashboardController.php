<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Training;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard/Index', [
            'trainings' => Training::where('start_at', '>=', Carbon::now())
                    ->orderBy('start_at', 'asc')
                    ->with('attendees')
                    ->take(2)
                    ->get()
                    ->transform(function ($training) {
                        return [
                            'id' => $training->id,
                            'name' => $training->name,
                            'place' => $training->place,
                            'start_at' => $training->start_at->format('Y-m-d H:i'),
                            'diff' => $training->start_at->diffForHumans(),
                            'length' => (int) $training->length,
                            'attendees' => UserResource::collection($training->attendees),
                            'registered' => $training->attendees->contains(Auth::user()->id),
                            'max_attendees' => (int) $training->max_attendees,
                        ];
                }),
        ]);
    }
}
