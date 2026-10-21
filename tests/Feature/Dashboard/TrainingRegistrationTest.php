<?php

use App\Filament\Widgets\UpcomingTraining;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;

use function Pest\Livewire\livewire;

it('user can register for an upcoming training via dashboard', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->addDays(3)]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->callTableAction('attend', $training);

    $this->assertDatabaseHas('trainings_attendance', [
        'training_id' => $training->id,
        'user_id' => $user->id,
    ]);
});

it('user can withdraw from a training via dashboard', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->addDays(3)]);
    TrainingAttendance::factory()->for($training)->for($user)->create();

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->callTableAction('withdraw', $training);

    $this->assertDatabaseMissing('trainings_attendance', [
        'training_id' => $training->id,
        'user_id' => $user->id,
    ]);
});

it('user cannot register for a training that already started', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->subHours(2)]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertTableActionDisabled('attend', $training);
});

it('user cannot withdraw from a training that already started', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->subHours(2)]);
    TrainingAttendance::factory()->for($training)->for($user)->create();

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertTableActionDisabled('withdraw', $training);
});

it('user cannot register for a full training when overflow is disabled', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create([
        'start_at' => now()->addDays(3),
        'max_attendees' => 1,
        'can_attend_more' => false,
    ]);
    TrainingAttendance::factory()->for($training)->create();

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertTableActionDisabled('attend', $training->getKey());
});

it('user can register for a full training when overflow is enabled', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create([
        'start_at' => now()->addDays(3),
        'max_attendees' => 1,
        'can_attend_more' => true,
    ]);
    TrainingAttendance::factory()->for($training)->create();

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertTableActionEnabled('attend', $training->getKey());
});

it('shows "Jelentkezem" button when user is not registered', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->addDays(3)]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertTableActionVisible('attend', $training)
        ->assertTableActionHidden('withdraw', $training);
});

it('shows "Lemondom" button when user is already registered', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->addDays(3)]);
    TrainingAttendance::factory()->for($training)->for($user)->create();

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertTableActionVisible('withdraw', $training)
        ->assertTableActionHidden('attend', $training);
});

it('dashboard training links to the training view page', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->addDays(3)]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertTableActionExists('attend');
});

it('user can register for an unlimited training even when overflow is disabled', function () {
    $user = User::factory()->create();
    $training = Training::factory()->unlimited()->create([
        'start_at' => now()->addDays(3),
        'can_attend_more' => false,
    ]);
    TrainingAttendance::factory()->for($training)->create();

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertTableActionEnabled('attend', $training->getKey())
        ->callTableAction('attend', $training);

    $this->assertDatabaseHas('trainings_attendance', [
        'training_id' => $training->id,
        'user_id' => $user->id,
    ]);
});

it('flags a registration as extra when the training is already at capacity', function () {
    $user = User::factory()->create();
    $training = Training::factory()->full()->create([
        'start_at' => now()->addDays(3),
        'can_attend_more' => true,
    ]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)->callTableAction('attend', $training);

    $this->assertDatabaseHas('trainings_attendance', [
        'training_id' => $training->id,
        'user_id' => $user->id,
        'extra' => true,
    ]);
});

it('does not flag a registration as extra below capacity', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->addDays(3), 'max_attendees' => 5]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)->callTableAction('attend', $training);

    $this->assertDatabaseHas('trainings_attendance', [
        'training_id' => $training->id,
        'user_id' => $user->id,
        'extra' => false,
    ]);
});

it('never flags a registration as extra on an unlimited training', function () {
    $user = User::factory()->create();
    $training = Training::factory()->unlimited()->create(['start_at' => now()->addDays(3)]);
    TrainingAttendance::factory()->for($training)->count(2)->create();

    $this->actingAs($user);

    livewire(UpcomingTraining::class)->callTableAction('attend', $training);

    $this->assertDatabaseHas('trainings_attendance', [
        'training_id' => $training->id,
        'user_id' => $user->id,
        'extra' => false,
    ]);
});

it('user cannot withdraw from a confirmed attendance', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->addDays(3)]);
    TrainingAttendance::factory()->for($training)->for($user)->attended()->create();

    $this->actingAs($user);

    livewire(UpcomingTraining::class)->assertTableActionDisabled('withdraw', $training);
});
