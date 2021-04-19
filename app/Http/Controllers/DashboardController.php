<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Message;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard/Index', [

            // select * from messages
            // where
            // (showed_from is null or showed_from <= date())
            // and
            // (showed_to is null or showed_to >= date())
            // order by created_at desc

            'message' => Message::orderBy('created_at', 'desc')
                ->where(function ($query) {
                    $query->whereNull('showed_from');
                    $query->orWhere('showed_from', '<=', Carbon::now());
                })
                ->where(function ($query) {
                    $query->whereNull('showed_to');
                    $query->orWhere('showed_to', '>=', Carbon::now());
                })
                ->first(),
            'trainings' => Training::whereBetween('start_at', [
                                        Carbon::now(),
                                        Carbon::now()->next('month')->lastOfMonth()
                                    ])
                    ->orderBy('start_at', 'asc')
                    ->with('attendees')
                    ->get()
                    ->transform(function ($training) {
                        return [
                            'id' => $training->id,
                            'type' => $training->type,
                            'name' => $training->name,
                            'place' => $training->place,
                            'start_at' => $training->start_at,
                            'formatted_start_at' => $training->start_at->format('Y-m-d H:i'),
                            'diff' => $training->start_at->diffForHumans(),
                            'length' => (int) $training->length,
                            'attendees' => UserResource::collection($training->attendees),
                            'registered' => $training->attendees->contains(Auth::user()->id),
                            'max_attendees' => (int) $training->max_attendees,
                            'can_attend_more' => $training->can_attend_more,
                            'can_attend_from' => $training->start_at->addDays(-7),
                        ];
                }),
        ]);
    }
}
