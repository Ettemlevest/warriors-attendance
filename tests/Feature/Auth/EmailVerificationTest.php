<?php

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Notifications\Auth\VerifyEmail;
use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt;
use Filament\Pages\Auth\Register;
use Illuminate\Support\Facades\Notification;

use function Pest\Livewire\livewire;

it('creates an unverified user and sends the verification mail on registration', function () {
    Notification::fake();

    livewire(Register::class)
        ->fillForm([
            'name' => 'Új Warrior',
            'email' => 'uj@example.com',
            'password' => 'password123',
            'passwordConfirmation' => 'password123',
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    $user = User::query()->where('email', 'uj@example.com')->firstOrFail();

    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});

it('redirects an unverified user to the verification prompt', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get('/')->assertRedirect('/email-verification/prompt');
    $this->actingAs($user)->get('/trainings')->assertRedirect('/email-verification/prompt');
});

it('shows the verification prompt to an unverified user', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get('/email-verification/prompt')->assertSuccessful();
});

it('lets a verified user use the application', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/')->assertSuccessful();
});

it('verifies the email through the signed link', function () {
    $user = User::factory()->unverified()->create();
    $url = Filament::getDefaultPanel()->getVerifyEmailUrl($user);

    $this->actingAs($user)->get($url)->assertRedirect('/');

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

it('rejects a tampered verification link', function () {
    $user = User::factory()->unverified()->create();
    $url = preg_replace('#/verify/(\d+)/[^?]+#', '/verify/$1/tampered', Filament::getDefaultPanel()->getVerifyEmailUrl($user));

    $this->actingAs($user)->get($url)->assertForbidden();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

it('resends the verification mail from the prompt', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user);

    livewire(EmailVerificationPrompt::class)->callAction('resendNotification');

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('keeps the verified state of a soft-deleted and restored user', function () {
    $user = User::factory()->create();

    $user->delete();
    $user->restore();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});
