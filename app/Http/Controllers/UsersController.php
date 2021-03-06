<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreationRequest;
use App\Http\Requests\UserDestroyRequest;
use App\Http\Requests\UserRestoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

final class UsersController extends Controller
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
                        'email' => $user->email,
                        'owner' => $user->owner,
                        'age' => $user->age,
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
            'email',
            'password',
            'owner',
            'size',
            'birth_date',
            'phone',
            'address',
            'safety_person',
            'safety_phone',
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
                'email' => $user->email,
                'owner' => $user->owner,
                'photo' => $user->photoUrl(['w' => 60, 'h' => 60, 'fit' => 'crop']),
                'size' => $user->size,
                'birth_date' => $user->birth_date ? $user->birth_date->format('Y-m-d') : null,
                'phone' => $user->phone,
                'address' => $user->address,
                'safety_person' => $user->safety_person,
                'safety_phone' => $user->safety_phone,
                'deleted_at' => $user->deleted_at,
            ],
        ]);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        if ($request->file('photo')) {
            $user->photo_path = $request->file('photo')->store('users');
        }

        if ($request->get('password')) {
            $user->password = $request->get('password');
        }

        $user->update($request->only([
            'name',
            'email',
            'owner',
            'size',
            'birth_date',
            'phone',
            'address',
            'safety_person',
            'safety_phone'
        ]));

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
