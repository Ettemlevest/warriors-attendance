<?php

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Models\User;
use Filament\Auth\Notifications\VerifyEmail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

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
            'password' => 'secret123',
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
            'password' => 'secret123',
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

it('requires a password when creating a user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm(['name' => 'Új Warrior', 'email' => 'uj@example.com', 'password' => null])
        ->call('create')
        ->assertHasFormErrors(['password' => 'required']);
});

it('rejects a weak password when creating a user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm(['name' => 'Új Warrior', 'email' => 'uj@example.com', 'password' => 'rövid'])
        ->call('create')
        ->assertHasFormErrors(['password']);
});

it('fills in a generated password that passes validation', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    $component = livewire(CreateUser::class)
        ->fillForm(['name' => 'Új Warrior', 'email' => 'uj@example.com'])
        ->callFormComponentAction('password', 'generatePassword');

    $generated = $component->get('data.password');

    expect($generated)->toBeString()->toHaveLength(16);

    $component->call('create')->assertHasNoFormErrors();

    expect(Hash::check($generated, User::query()->where('email', 'uj@example.com')->firstOrFail()->password))->toBeTrue();
});

it('database refuses a user without a password', function () {
    expect(fn () => User::factory()->create(['password' => null]))->toThrow(QueryException::class);
});

it('creates the user unverified and sends the verification mail', function () {
    Notification::fake();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateUser::class)
        ->fillForm(['name' => 'Új Warrior', 'email' => 'uj@example.com', 'password' => 'secret123'])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::query()->where('email', 'uj@example.com')->firstOrFail();

    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});
