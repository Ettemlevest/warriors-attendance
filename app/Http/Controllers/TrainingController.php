<?php

namespace App\Http\Controllers;

use App\Training;
use Carbon\Carbon;
use Inertia\Inertia;
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

    public function store()
    {
        //
    }

    public function edit(Training $training)
    {
        return Inertia::render('Trainings/Edit', [
            'training' => [
                'id' => $training->id,
                'name' => $training->name,
                'place' => $training->place,
                'date' => $training->date,
                'start_at' => $training->start_at,
                'end_at' => $training->end_at,
                'max_attendees' => $training->max_attendees,
            ],
        ]);
    }
}
