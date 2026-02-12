<?php

use App\Filament\Resources\TrainingResource;
use App\Filament\Resources\TrainingResource\Pages\EditTraining;
use App\Models\Training;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can render the edit training page', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create();

    $this->actingAs($admin)
        ->get(TrainingResource::getUrl('edit', ['record' => $training]))
        ->assertSuccessful();
});

it('regular user cannot render the edit training page', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create();

    $this->actingAs($user)
        ->get(TrainingResource::getUrl('edit', ['record' => $training]))
        ->assertForbidden();
});

it('admin can update training name', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create(['name' => 'Régi név']);

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->fillForm(['name' => 'Új név'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($training->fresh()->name)->toBe('Új név');
});

it('admin can update training type', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create(['type' => 'easy']);

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->fillForm(['type' => 'hard'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($training->fresh()->type)->toBe('hard');
});

it('admin can update training place', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create(['place' => 'Régi helyszín']);

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->fillForm(['place' => 'Új helyszín'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($training->fresh()->place)->toBe('Új helyszín');
});

it('admin can update training start time', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create();
    $newDate = now()->addDays(7)->startOfHour();

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->fillForm(['start_at' => $newDate->format('Y-m-d H:i:s')])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($training->fresh()->start_at->format('Y-m-d H:i'))->toBe($newDate->format('Y-m-d H:i'));
});

it('admin can update training length', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create(['length' => 60]);

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->fillForm(['length' => 120])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($training->fresh()->length)->toBe(120);
});

it('admin can update max attendees', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create(['max_attendees' => 20]);

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->fillForm(['max_attendees' => 50])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($training->fresh()->max_attendees)->toBe(50);
});

it('admin can toggle overflow setting', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create(['can_attend_more' => false]);

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->fillForm(['can_attend_more' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($training->fresh()->can_attend_more)->toBeTrue();
});

it('admin can update training description', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create(['description' => null]);

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->fillForm(['description' => '<p>Új leírás</p>'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($training->fresh()->description)->toBe('<p>Új leírás</p>');
});

it('shows meta data section with created and updated timestamps', function () {
    $admin = User::factory()->admin()->create();
    $training = Training::factory()->create();

    $this->actingAs($admin);

    livewire(EditTraining::class, ['record' => $training->getRouteKey()])
        ->assertSee('Meta adatok')
        ->assertSee($training->created_at)
        ->assertSee($training->updated_at);
});
