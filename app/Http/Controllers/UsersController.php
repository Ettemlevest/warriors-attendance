<?php

namespace App\Http\Controllers;

use App\User;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class UsersController extends Controller
{
    public function index()
    {
        $query = User::orderByName()->filter(Request::only('search', 'role', 'trashed'));

        if (! Auth::user()->owner) {
            $query->where('id', Auth::user()->id);
        }

        return Inertia::render('Users/Index', [
            'filters' => Request::all('search', 'role', 'trashed'),
            'users' => $query->get()
                ->transform(function ($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'nickname' => $user->nickname,
                        'email' => $user->email,
                        'owner' => $user->owner,
                        'photo' => $user->photoUrl(['w' => 40, 'h' => 40, 'fit' => 'crop']),
                        'deleted_at' => $user->deleted_at,
                    ];
                }),
        ]);
    }

    public function create()
    {
        if (! Auth::user()->owner) {
            return Redirect::route('users');
        }

        return Inertia::render('Users/Create');
    }

    public function store()
    {
        if (! Auth::user()->owner) {
            return Redirect::route('users');
        }

        Request::validate([
            'name' => ['required', 'max:255'],
            'nickname' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', Rule::unique('users')],
            'password' => ['nullable'],
            'owner' => ['required', 'boolean'],
            'photo' => ['nullable', 'image'],
        ]);

        User::create([
            'name' => Request::get('name'),
            'nickname' => Request::get('nickname'),
            'email' => Request::get('email'),
            'password' => Request::get('password'),
            'owner' => Request::get('owner'),
            'photo_path' => Request::file('photo') ? Request::file('photo')->store('users') : null,
        ]);

        return Redirect::route('users')->with('success', 'Warrior sikeresen létrehozva.');
    }

    public function edit(User $user)
    {
        if (! Auth::user()->owner && $user->id <> Auth::user()->id) {
            return Redirect::route('users');
        }

        return Inertia::render('Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'nickname' => $user->nickname,
                'email' => $user->email,
                'owner' => $user->owner,
                'photo' => $user->photoUrl(['w' => 60, 'h' => 60, 'fit' => 'crop']),
                'deleted_at' => $user->deleted_at,
            ],
        ]);
    }

    public function update(User $user)
    {
        if (! Auth::user()->owner && $user->id <> Auth::user()->id) {
            return Redirect::route('users');
        }

        Request::validate([
            'name' => ['required', 'max:255'],
            'nickname' => ['required', 'max:255'],
            'email' => ['required', 'max:50', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable'],
            'owner' => ['required', 'boolean'],
            'photo' => ['nullable', 'image'],
        ]);

        $user->update(Request::only('name', 'nickname', 'email', 'owner'));

        if (Request::file('photo')) {
            $user->update(['photo_path' => Request::file('photo')->store('users')]);
        }

        if (Request::get('password')) {
            $user->update(['password' => Request::get('password')]);
        }

        return Redirect::route('users.edit', $user)->with('success', 'Warrior sikeresen frissítve.');
    }

    public function destroy(User $user)
    {
        if (! Auth::user()->owner) {
            return Redirect::route('users');
        }

        $user->delete();

        return Redirect::route('users.edit', $user)->with('success', 'Warrior sikeresen törölve.');
    }

    public function restore(User $user)
    {
        if (! Auth::user()->owner) {
            return Redirect::route('users');
        }

        $user->restore();

        return Redirect::route('users.edit', $user)->with('success', 'Warrior sikeresen visszaállítva.');
    }
}
