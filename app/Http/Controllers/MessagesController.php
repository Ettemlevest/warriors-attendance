<?php

namespace App\Http\Controllers;

use App\Message;
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
                        'showed_from' => $message->showed_from,
                        'showed_to' => $message->showed_to,
                    ];
                }),
        ]);
    }
}
