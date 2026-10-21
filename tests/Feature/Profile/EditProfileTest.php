<?php

use App\Filament\Pages\EditProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

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
    Storage::fake('public');
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['avatar_url' => UploadedFile::fake()->image('avatar.jpg')])
        ->call('save')
        ->assertHasNoFormErrors();

    $avatar = $user->fresh()->avatar_url;

    expect($avatar)->toStartWith('users/');
    Storage::disk('public')->assertExists($avatar);
});

it('user cannot upload a non-image file as avatar', function () {
    Storage::fake('public');
    $user = User::factory()->create(['avatar_url' => null]);

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['avatar_url' => UploadedFile::fake()->create('shell.php', 10, 'application/x-php')])
        ->call('save')
        ->assertHasFormErrors(['avatar_url']);

    expect($user->fresh()->avatar_url)->toBeNull();
    Storage::disk('public')->assertDirectoryEmpty('users');
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

it('sends no mail when the user changes their email', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(EditProfile::class)
        ->fillForm(['email' => 'uj@example.com'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->email)->toBe('uj@example.com');
    Notification::assertNothingSent();
});
