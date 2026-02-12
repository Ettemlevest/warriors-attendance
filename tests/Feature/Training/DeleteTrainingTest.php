<?php

use App\Filament\Resources\TrainingResource\Pages\ListTrainings;
use App\Models\Training;
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
