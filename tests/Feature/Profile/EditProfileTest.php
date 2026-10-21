<?php

use App\Filament\Pages\EditProfile;
use App\Models\User;
use Filament\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

use function Pest\Livewire\livewire;

it('user can render the edit profile page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/profile')
        ->assertSuccessful();
});

it('user can update their name', function () {
    $user = User::factory()->create(['name' => 'Régi Név']);

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['name' => 'Új Név'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->name)->toBe('Új Név');
});

it('user can update their email', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['email' => 'uj@example.com'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->email)->toBe('uj@example.com');
});

it('user can update their password', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm([
            'password' => 'new-password',
            'passwordConfirmation' => 'new-password',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

it('user can set safety contact person and phone', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm([
            'safety_person' => 'Kiss Éva',
            'safety_phone' => '+36301234567',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $user->fresh();
    expect($fresh->safety_person)->toBe('Kiss Éva')
        ->and($fresh->safety_phone)->toBe('+36301234567');
});

it('user can upload an avatar photo', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->assertFormFieldExists('avatar_url');
});

it('user can set their t-shirt size', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['size' => 'L'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->size)->toBe('L');
});

it('user can set their birth date', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['birth_date' => '1990-05-15'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->birth_date->format('Y-m-d'))->toBe('1990-05-15');
});

it('user can set their phone number', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['phone' => '+36309876543'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->phone)->toBe('+36309876543');
});

it('user can set their address', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['address' => 'Budapest, Fő utca 1.'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->address)->toBe('Budapest, Fő utca 1.');
});

it('resets verification and sends a new mail when the user changes their email', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['email' => 'uj@example.com'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});

it('keeps verification when the profile is saved with the same email', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['name' => 'Más Név'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Notification::assertNothingSent();
});
