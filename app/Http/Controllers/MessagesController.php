<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageDestroyRequest;
use App\Http\Requests\MessagesCreationRequest;
use App\Http\Requests\MessagesUpdateRequest;
use App\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;

class MessagesController extends Controller
{
    public function index()
    {
        return Inertia::render('Messages/Index', [
            'filters' => Request::all('search'),
            'messages' => Message::orderBy('showed_from', 'desc')
                ->paginate()
                ->transform(function ($message) {
                    return [
                        'id' => $message->id,
                        'title' => $message->title,
                        'body' => $message->body,
                        'showed_from' => $message->showed_from ? $message->showed_from->format('Y-m-d') : null,
                        'showed_to' => $message->showed_to ? $message->showed_to->format('Y-m-d') : null,
                    ];
                }),
        ]);
    }

    public function create()
    {
        return Inertia::render('Messages/Create');
    }

    public function store(MessagesCreationRequest $request)
    {
        Message::create($this->prepareDataForDB($request->all()));

        return Redirect::route('messages')->with('success', 'Üzenet sikeresen létrehozva.');
    }

    public function edit(Message $message)
    {
        if (! Auth::user()->owner) {
            return Redirect::route('dashboard');
        }

        return Inertia::render('Messages/Edit', [
            'message' => [
                'id' => $message->id,
                'title' => $message->title,
                'body' => $message->body,
                'showed_from' => $message->showed_from ? $message->showed_from->format('Y-m-d') : null,
                'showed_to' => $message->showed_to ? $message->showed_to->format('Y-m-d') : null,
            ],
        ]);
    }

    public function update(MessagesUpdateRequest $request, Message $message)
    {
        $message->update($this->prepareDataForDB($request->all()));

        return Redirect::route('messages.edit', $message)->with('success', 'Üzenet sikeresen mentve.');
    }

    public function destroy(MessageDestroyRequest $request, Message $message)
    {
        $message->delete();

        return Redirect::route('messages')->with('success', 'Üzenet sikeresen törölve.');
    }

    protected function prepareDataForDB(Array $inputs)
    {
        return [
            'title' => $inputs['title'],
            'body' => $inputs['body'],
            'showed_from' => $inputs['showed_from'],
            'showed_to' => $inputs['showed_to'],
        ];
    }
}
