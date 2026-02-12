<?php

use App\Models\User;
use Filament\Notifications\Auth\ResetPassword as ResetPasswordNotification;
use Filament\Pages\Auth\PasswordReset\RequestPasswordReset;
use Filament\Pages\Auth\PasswordReset\ResetPassword;
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

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('can reset password with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    livewire(RequestPasswordReset::class)
        ->fillForm([
            'email' => $user->email,
        ])
        ->call('request');

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
        livewire(ResetPassword::class, [
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
        \Illuminate\Support\Facades\Hash::check('new-password', $user->fresh()->password)
    );
});
