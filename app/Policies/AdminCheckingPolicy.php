<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminCheckingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user): bool
    {
        return $user->isAdmin();
    }
}
