<?php

namespace App\Http\Controllers;

use App\Training;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard/Index', [
            'trainings' => Training::whereDate('start_at', '>=', Carbon::today())
                    ->orderBy('start_at', 'asc')
                    ->get()
                    ->take(2)
                    ->transform(function ($training) {
                        return [
                            'id' => $training->id,
                            'name' => $training->name,
                            'place' => $training->place,
                            'start_at' => $training->start_at->format('Y-m-d H:i'),
                            'diff' => $training->start_at->diffForHumans(),
                            'length' => (int) $training->length,
                            'attendees' => $training->attendees,
                            'registered' => $training->attendees->contains(Auth::user()->id),
                            'max_attendees' => (int) $training->max_attendees,
                        ];
                }),
        ]);
    }
}
