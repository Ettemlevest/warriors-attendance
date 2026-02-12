<?php

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

use function Pest\Livewire\livewire;

it('admin can render the edit user page', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin)
        ->get(UserResource::getUrl('edit', ['record' => $user]))
        ->assertSuccessful();
});

it('regular user cannot render the edit user page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(UserResource::getUrl('edit', ['record' => $user]))
        ->assertForbidden();
});

it('admin can update user name and email', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm([
            'name' => 'Új Név',
            'email' => 'uj@example.com',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $user->fresh();
    expect($fresh->name)->toBe('Új Név')
        ->and($fresh->email)->toBe('uj@example.com');
});

it('admin can update user password', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['password' => 'new-secret'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Hash::check('new-secret', $user->fresh()->password))->toBeTrue();
});

it('leaving password empty does not change the existing password', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['password' => 'original']);

    $this->actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['password' => ''])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Hash::check('original', $user->fresh()->password))->toBeTrue();
});

it('admin can update user profile fields', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm([
            'size' => 'XL',
            'birth_date' => '1995-03-20',
            'phone' => '+36301111111',
            'address' => 'Szeged, Kossuth tér 5.',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $user->fresh();
    expect($fresh->size)->toBe('XL')
        ->and($fresh->phone)->toBe('+36301111111')
        ->and($fresh->address)->toBe('Szeged, Kossuth tér 5.');
});

it('admin can update safety contact fields', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm([
            'safety_person' => 'Nagy Éva',
            'safety_phone' => '+36302222222',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $user->fresh();
    expect($fresh->safety_person)->toBe('Nagy Éva')
        ->and($fresh->safety_phone)->toBe('+36302222222');
});

it('shows meta data section with created and updated timestamps', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->assertSee('Meta adatok')
        ->assertSee($user->created_at)
        ->assertSee($user->updated_at);
});
