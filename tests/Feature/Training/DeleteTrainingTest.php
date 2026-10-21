<?php

use App\Filament\Resources\TrainingResource\Pages\EditTraining;
use App\Filament\Resources\TrainingResource\Pages\ListTrainings;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can delete a training from the list', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create();

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->callTableAction('delete', $training);

    $this->assertDatabaseMissing('trainings', ['id' => $training->id]);
});

it('regular user cannot delete a training', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create();

    $this->actingAs($user);

    livewire(ListTrainings::class)
        ->assertTableActionHidden('delete', $training);
});

it('admin can bulk delete trainings', function () {
    $admin = User::factory()->admin()->create();
    $trainings = Training::factory()->count(3)->create();

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->callTableBulkAction('delete', $trainings);

    foreach ($trainings as $training) {
        $this->assertDatabaseMissing('trainings', ['id' => $training->id]);
    }
});

it('regular user cannot bulk delete trainings', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(ListTrainings::class)
        ->assertTableBulkActionHidden('delete');
});

it('deleting a training removes its attendance rows', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create();
    $attendance = TrainingAttendance::factory()->for($training)->create();

    $this->actingAs($admin);

    livewire(ListTrainings::class)->callTableAction('delete', $training);

    $this->assertDatabaseMissing('trainings_attendance', ['id' => $attendance->id]);
});

it('admin cannot delete a training that has a confirmed attendance', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->past()->create();
    $attendance = TrainingAttendance::factory()->for($training)->attended()->create();

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->callTableAction('delete', $training)
        ->assertNotified('Az edzés nem törölhető');

    $this->assertDatabaseHas('trainings', ['id' => $training->id]);
    $this->assertDatabaseHas('trainings_attendance', ['id' => $attendance->id]);
});

it('admin cannot delete a training with a confirmed attendance from the edit page', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->past()->create();
    TrainingAttendance::factory()->for($training)->attended()->create();

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->callAction('delete')
        ->assertNotified('Az edzés nem törölhető');

    $this->assertDatabaseHas('trainings', ['id' => $training->id]);
});

it('refuses bulk delete when a selected training has a confirmed attendance', function () {
    $admin = User::factory()->admin()->create();
    $pending = Training::factory()->create();
    $confirmed = Training::factory()->past()->create();
    TrainingAttendance::factory()->for($confirmed)->attended()->create();

    $this->actingAs($admin);

    livewire(ListTrainings::class)
        ->callTableBulkAction('delete', [$pending, $confirmed])
        ->assertNotified('Az edzés nem törölhető');

    $this->assertDatabaseHas('trainings', ['id' => $pending->id]);
    $this->assertDatabaseHas('trainings', ['id' => $confirmed->id]);
});
