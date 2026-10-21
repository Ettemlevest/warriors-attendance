<?php

use App\Filament\Resources\TrainingResource;
use App\Filament\Resources\TrainingResource\Pages\ViewTraining;
use App\Models\Training;
use App\Models\User;

use function Pest\Livewire\livewire;

it('regular user can view a training', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create();

    $this->actingAs($user)
        ->get(TrainingResource::getUrl('view', ['record' => $training]))
        ->assertSuccessful();
});

it('admin can view a training', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create();

    $this->actingAs($admin)
        ->get(TrainingResource::getUrl('view', ['record' => $training]))
        ->assertSuccessful();
});

it('guest cannot view a training', function () {
    $training = Training::factory()->create();

    $this->get(TrainingResource::getUrl('view', ['record' => $training]))
        ->assertRedirect('/login');
});

it('displays training details: type, name, place, start time, length', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create([
        'type' => 'hard',
        'name' => 'Haladó edzés',
        'place' => 'Sportcsarnok',
        'length' => 90,
    ]);

    $this->actingAs($admin);

    livewire(ViewTraining::class, ['record' => $training->getRouteKey()])
        ->assertSee('Haladó edzés')
        ->assertSee('Sportcsarnok')
        ->assertSee('90');
});

it('displays training description when present', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create([
        'description' => '<p>Részletes leírás az edzésről.</p>',
    ]);

    $this->actingAs($admin);

    livewire(ViewTraining::class, ['record' => $training->getRouteKey()])
        ->assertSee('Részletes leírás az edzésről.');
});

it('shows edit button for admin', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create();

    $this->actingAs($admin);

    livewire(ViewTraining::class, ['record' => $training->getRouteKey()])
        ->assertActionVisible('edit');
});

it('does not show edit button for regular user', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create();

    $this->actingAs($user);

    livewire(ViewTraining::class, ['record' => $training->getRouteKey()])
        ->assertActionHidden('edit');
});

it('displays the type label from the enum without the old typo', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['type' => 'easy']);

    $this->actingAs($user);

    livewire(ViewTraining::class, ['record' => $training->getRouteKey()])
        ->assertSee('Felzárkóztató edzés')
        ->assertDontSee('Felzárkózatató');
});
