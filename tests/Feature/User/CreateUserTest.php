<?php

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Livewire\livewire;

it('admin can render the create user page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(UserResource::getUrl('create'))
        ->assertSuccessful();
});

it('regular user cannot render the create user page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(UserResource::getUrl('create'))
        ->assertForbidden();
});

it('admin can create a user with name, email, and password', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Kiss Péter',
            'email' => 'peter@example.com',
            'password' => 'secret123',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'name' => 'Kiss Péter',
        'email' => 'peter@example.com',
    ]);
});

it('requires name and email to create a user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => '',
            'email' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'email' => 'required']);
});

it('email must be valid', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Test',
            'email' => 'not-an-email',
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'email']);
});

it('admin can set user profile fields: avatar, size, birth date, phone, address', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Kiss Péter',
            'email' => 'peter@example.com',
            'size' => 'L',
            'birth_date' => '1990-05-15',
            'phone' => '+36301234567',
            'address' => 'Budapest, Fő utca 1.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'email' => 'peter@example.com',
        'size' => 'L',
        'phone' => '+36301234567',
        'address' => 'Budapest, Fő utca 1.',
    ]);
});

it('admin can set safety contact fields', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Kiss Péter',
            'email' => 'peter@example.com',
            'safety_person' => 'Kiss Éva',
            'safety_phone' => '+36309876543',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(User::class, [
        'email' => 'peter@example.com',
        'safety_person' => 'Kiss Éva',
        'safety_phone' => '+36309876543',
    ]);
});

it('password is hashed when creating a user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Kiss Péter',
            'email' => 'peter@example.com',
            'password' => 'secret123',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::where('email', 'peter@example.com')->first();
    expect(Hash::check('secret123', $user->password))->toBeTrue();
});

it('rejects an email that another user already uses', function () {
    $admin = User::factory()->admin()->create();
    $existing = User::factory()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm(['name' => 'Új Warrior', 'email' => $existing->email, 'password' => 'secret123'])
        ->call('create')
        ->assertHasFormErrors(['email' => 'unique']);
});
