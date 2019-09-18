<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreationRequest;
use App\Http\Requests\UserDestroyRequest;
use App\Http\Requests\UserRestoreRequest;
use App\Http\Requests\UserUpdateRequest;
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
            'users' => $query->paginate()
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

    public function store(UserCreationRequest $request)
    {
        User::create(array_merge($request->only([
            'name',
            'nickname',
            'email',
            'password',
            'owner',
        ]), [
            'photo_path' => $request->file('photo') ? $request->file('photo')->store('users') : null,
        ]));

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

    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->only('name', 'nickname', 'email', 'owner'));

        if ($request->file('photo')) {
            $user->update(['photo_path' => $request->file('photo')->store('users')]);
        }

        if ($request->get('password')) {
            $user->update(['password' => $request->get('password')]);
        }

        return Redirect::route('users.edit', $user)->with('success', 'Warrior sikeresen frissítve.');
    }

    public function destroy(UserDestroyRequest $request, User $user)
    {
        $user->delete();

        return Redirect::route('users.edit', $user)->with('success', 'Warrior sikeresen törölve.');
    }

    public function restore(UserRestoreRequest $request, User $user)
    {
        $user->restore();

        return Redirect::route('users.edit', $user)->with('success', 'Warrior sikeresen visszaállítva.');
    }
}
