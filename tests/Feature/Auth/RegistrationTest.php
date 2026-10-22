<?php

use App\Models\User;
use Filament\Auth\Pages\Register;

use function Pest\Livewire\livewire;

it('can render the registration page', function () {
    $this->get('/register')
        ->assertSuccessful();
});

it('can register a new user', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'passwordConfirmation' => 'password123',
        ])
        ->call('register')
        ->assertHasNoFormErrors();

    $this->assertAuthenticated();
    $this->assertDatabaseHas(User::class, [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);
});

it('lets a freshly registered user use the application right away', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => 'Új Warrior',
            'email' => 'uj@example.com',
            'password' => 'password123',
            'passwordConfirmation' => 'password123',
        ])
        ->call('register')
        ->assertHasNoFormErrors()
        ->assertRedirect('/');

    $this->assertAuthenticated();
    $this->get('/')->assertSuccessful();
    $this->get('/trainings')->assertSuccessful();
});

it('cannot register with an existing email', function () {
    $user = User::factory()->create();

    livewire(Register::class)
        ->fillForm([
            'name' => 'Another User',
            'email' => $user->email,
            'password' => 'password123',
            'passwordConfirmation' => 'password123',
        ])
        ->call('register')
        ->assertHasFormErrors(['email']);
});

it('requires name, email, and password to register', function () {
    livewire(Register::class)
        ->fillForm([
            'name' => '',
            'email' => '',
            'password' => '',
            'passwordConfirmation' => '',
        ])
        ->call('register')
        ->assertHasFormErrors(['name', 'email', 'password']);
});
