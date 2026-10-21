<?php

use App\Filament\Resources\TrainingResource;
use App\Filament\Resources\TrainingResource\Pages\ListTrainings;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;

use function Pest\Livewire\livewire;

it('regular user can view the trainings list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(TrainingResource::getUrl('index'))
        ->assertSuccessful();
});

it('admin can view the trainings list', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(TrainingResource::getUrl('index'))
        ->assertSuccessful();
});

it('guest cannot view the trainings list', function () {
    $this->get(TrainingResource::getUrl('index'))
        ->assertRedirect('/login');
});

it('displays training name, place, type, start time, length, and attendee count', function () {
    $admin = User::factory()->admin()->create();
    $trainings = Training::factory()->count(3)->create();

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->assertCanSeeTableRecords($trainings)
        ->assertCanRenderTableColumn('type')
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('start_at')
        ->assertCanRenderTableColumn('length')
        ->assertCanRenderTableColumn('attendees_count');
});

it('can search trainings by name', function () {
    $admin = User::factory()->admin()->create();
    $match = Training::factory()->create(['name' => 'Hétfői edzés']);
    $noMatch = Training::factory()->create(['name' => 'Szerdai futás']);

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->searchTable('Hétfői')
        ->assertCanSeeTableRecords([$match])
        ->assertCanNotSeeTableRecords([$noMatch]);
});

it('can sort trainings by start date', function () {
    $admin = User::factory()->admin()->create();
    $earlier = Training::factory()->create(['start_at' => now()->addDays(1)]);
    $later = Training::factory()->create(['start_at' => now()->addDays(10)]);

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->sortTable('start_at', 'asc')
        ->assertCanSeeTableRecords([$earlier, $later], inOrder: true);
});

it('can filter trainings by type', function () {
    $admin = User::factory()->admin()->create();
    $easy = Training::factory()->create(['type' => 'easy']);
    $hard = Training::factory()->create(['type' => 'hard']);

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->filterTable('type', 'easy')
        ->assertCanSeeTableRecords([$easy])
        ->assertCanNotSeeTableRecords([$hard]);
});

it('can filter trainings the current user registered for', function () {
    $otherUser = User::factory()->create();
    $user = User::factory()->create();
    $mine = Training::factory()->create();
    $theirs = Training::factory()->create();
    TrainingAttendance::factory()->for($mine)->for($user)->create();
    TrainingAttendance::factory()->for($theirs)->for($otherUser)->create();

    $this->actingAs($user);

    livewire(ListTrainings::class)
        ->filterTable('is_attendee', true)
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$theirs]);
});

it('can filter trainings the current user did not register for', function () {
    $otherUser = User::factory()->create();
    $user = User::factory()->create();
    $mine = Training::factory()->create();
    $theirs = Training::factory()->create();
    TrainingAttendance::factory()->for($mine)->for($user)->create();
    TrainingAttendance::factory()->for($theirs)->for($otherUser)->create();

    $this->actingAs($user);

    livewire(ListTrainings::class)
        ->filterTable('is_attendee', false)
        ->assertCanSeeTableRecords([$theirs])
        ->assertCanNotSeeTableRecords([$mine]);
});

it('shows correct attendee count vs max attendees', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create(['max_attendees' => 20]);
    TrainingAttendance::factory()->for($training)->count(5)->create();

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->assertTableColumnFormattedStateSet('attendees_count', '5 / 20', $training->getKey());
});

it('defaults to sorting by start date descending', function () {
    $admin = User::factory()->admin()->create();
    $earlier = Training::factory()->create(['start_at' => now()->addDays(1)]);
    $later = Training::factory()->create(['start_at' => now()->addDays(10)]);

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->assertCanSeeTableRecords([$later, $earlier], inOrder: true);
});
