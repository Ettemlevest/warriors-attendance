<?php

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can view the users list', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(UserResource::getUrl('index'))
        ->assertSuccessful();
});

it('regular user cannot view the users list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(UserResource::getUrl('index'))
        ->assertForbidden();
});

it('guest cannot view the users list', function () {
    $this->get(UserResource::getUrl('index'))
        ->assertRedirect('/login');
});

it('displays user avatar, name, email, and age', function () {
    $admin = User::factory()->admin()->create();
    $users = User::factory()->count(3)->create(['birth_date' => '1990-01-01']);

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users)
        ->assertCanRenderTableColumn('avatar_url')
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('email')
        ->assertCanRenderTableColumn('age');
});

it('can search users by name', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create(['name' => 'Kiss Péter']);
    $other = User::factory()->create(['name' => 'Nagy Anna']);

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->searchTable('Kiss')
        ->assertCanSeeTableRecords([$target])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can search users by email', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->create(['email' => 'peter@example.com']);
    $other = User::factory()->create(['email' => 'anna@example.com']);

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->searchTable('peter@example')
        ->assertCanSeeTableRecords([$target])
        ->assertCanNotSeeTableRecords([$other]);
});

it('can see soft-deleted users with trashed filter', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $trashedUser = User::factory()->create(['deleted_at' => now()]);

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords([$user])
        ->assertCanNotSeeTableRecords([$trashedUser])
        ->filterTable('trashed', true)
        ->assertCanSeeTableRecords([$user, $trashedUser]);
});

it('defaults to sorting by id descending', function () {
    $admin = User::factory()->admin()->create();
    $first = User::factory()->create();
    $second = User::factory()->create();
    $third = User::factory()->create();

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords([$third, $second, $first], inOrder: true);
});
