<?php

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\Subscription;
use App\Models\TrainingAttendance;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can soft-delete a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    $this->actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->callAction('delete');

    $this->assertSoftDeleted($user);
});

it('regular user cannot delete a user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(UserResource::getUrl('edit', ['record' => $user]))
        ->assertForbidden();
});

it('admin can restore a soft-deleted user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['deleted_at' => now()]);

    $this->actingAs($admin);

    livewire(EditUser::class, ['record' => $user->getRouteKey()])
        ->callAction('restore');

    expect($user->fresh()->deleted_at)->toBeNull();
});

it('admin can force-delete a user', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['deleted_at' => now()]);

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->filterTable('trashed', true)
        ->callTableAction('forceDelete', $user);

    $this->assertModelMissing($user);
});

it('admin can bulk delete users', function () {
    $admin = User::factory()->admin()->create();
    $users = User::factory()->count(3)->create();

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->callTableBulkAction('delete', $users);

    foreach ($users as $user) {
        $this->assertSoftDeleted($user);
    }
});

it('admin can bulk force-delete users', function () {
    $admin = User::factory()->admin()->create();
    $users = User::factory()->count(3)->create(['deleted_at' => now()]);

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->filterTable('trashed', true)
        ->callTableBulkAction('forceDelete', $users);

    foreach ($users as $user) {
        $this->assertModelMissing($user);
    }
});

it('admin can bulk restore users', function () {
    $admin = User::factory()->admin()->create();
    $users = User::factory()->count(3)->create(['deleted_at' => now()]);

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->filterTable('trashed', true)
        ->callTableBulkAction('restore', $users);

    foreach ($users as $user) {
        expect($user->fresh()->deleted_at)->toBeNull();
    }
});

it('force deleting a user removes their attendance rows', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['deleted_at' => now()]);
    $attendance = TrainingAttendance::factory()->for($user)->create();

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->filterTable('trashed', true)
        ->callTableAction('forceDelete', $user);

    $this->assertModelMissing($user);
    $this->assertDatabaseMissing('trainings_attendance', ['id' => $attendance->id]);
});

it('refuses to force delete a user who has subscriptions', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create(['deleted_at' => now()]);
    Subscription::factory()->for($user)->create();

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->filterTable('trashed', true)
        ->callTableAction('forceDelete', $user)
        ->assertNotified('A Warrior nem törölhető véglegesen');

    $this->assertDatabaseHas('users', ['id' => $user->id]);
});

it('refuses bulk force delete when any selected user has subscriptions', function () {
    $admin = User::factory()->admin()->create();
    $subscribed = User::factory()->create(['deleted_at' => now()]);
    $plain = User::factory()->create(['deleted_at' => now()]);
    Subscription::factory()->for($subscribed)->create();

    $this->actingAs($admin);

    livewire(ListUsers::class)
        ->filterTable('trashed', true)
        ->callTableBulkAction('forceDelete', [$subscribed, $plain])
        ->assertNotified('A Warrior nem törölhető véglegesen');

    $this->assertDatabaseHas('users', ['id' => $subscribed->id]);
    $this->assertDatabaseHas('users', ['id' => $plain->id]);
});
