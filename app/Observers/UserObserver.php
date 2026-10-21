<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function updating(User $user): void
    {
        if (! $user->isDirty('email')) {
            return;
        }

        $user->email_verified_at = null;
    }

    public function updated(User $user): void
    {
        if (! $user->wasChanged('email')) {
            return;
        }

        $user->sendEmailVerificationNotification();
    }
}
