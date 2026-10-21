<?php

use App\Models\User;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

use function Pest\Livewire\livewire;

it('can render the password reset request page', function () {
    $this->get('/password-reset/request')
        ->assertSuccessful();
});

it('can request a password reset link', function () {
    Notification::fake();

    $user = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('request')
        ->assertHasNoFormErrors()
        ->assertNotified();

    Notification::assertSentTo($user, ResetPassword::class);
});

it('can reset password with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('request');

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        livewire(Filament\Auth\Pages\PasswordReset\ResetPassword::class, [
            'email' => $user->email,
            'token' => $notification->token,
        ])
            ->fillForm([
                'password' => 'new-password',
                'passwordConfirmation' => 'new-password',
            ])
            ->call('resetPassword')
            ->assertHasNoFormErrors()
            ->assertNotified();

        return true;
    });

    $this->assertTrue(
        Hash::check('new-password', $user->fresh()->password)
    );
});
