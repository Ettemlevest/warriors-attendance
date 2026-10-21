<?php

use App\Filament\Resources\TrainingResource;
use App\Filament\Resources\TrainingResource\Pages\CreateTraining;
use App\Models\User;
use Filament\Forms\Components\Select;

use function Pest\Livewire\livewire;

it('admin can render the create training page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(TrainingResource::getUrl('create'))
        ->assertSuccessful();
});

it('regular user cannot render the create training page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(TrainingResource::getUrl('create'))
        ->assertForbidden();
});

it('admin can create a training with type "easy"', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->fillForm([
            'type' => 'easy',
            'name' => 'Felzárkóztató edzés',
            'place' => 'Sportpálya',
            'start_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'length' => 90,
            'max_attendees' => 20,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('trainings', [
        'type' => 'easy',
        'name' => 'Felzárkóztató edzés',
    ]);
});

it('admin can create a training with type "running"', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->fillForm([
            'type' => 'running',
            'name' => 'Futó edzés',
            'place' => 'Sportpálya',
            'start_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'length' => 60,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('trainings', ['type' => 'running']);
});

it('admin can create a training with type "hard"', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->fillForm([
            'type' => 'hard',
            'name' => 'Haladó edzés',
            'place' => 'Sportpálya',
            'start_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'length' => 120,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('trainings', ['type' => 'hard']);
});

it('admin can create a training with type "other"', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->fillForm([
            'type' => 'other',
            'name' => 'Egyéb foglalkozás',
            'place' => 'Sportpálya',
            'start_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'length' => 60,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('trainings', ['type' => 'other']);
});

it('admin can create a training with a description', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->fillForm([
            'type' => 'easy',
            'name' => 'Edzés leírással',
            'place' => 'Sportpálya',
            'start_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'length' => 60,
            'description' => '<p>Ez egy fontos edzés.</p>',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('trainings', [
        'name' => 'Edzés leírással',
        'description' => '<p>Ez egy fontos edzés.</p>',
    ]);
});

it('admin can create a training with overflow enabled', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->fillForm([
            'type' => 'easy',
            'name' => 'Overflow edzés',
            'place' => 'Sportpálya',
            'start_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'length' => 60,
            'can_attend_more' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('trainings', [
        'name' => 'Overflow edzés',
        'can_attend_more' => true,
    ]);
});

it('admin can create a training with overflow disabled', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->fillForm([
            'type' => 'easy',
            'name' => 'Strict edzés',
            'place' => 'Sportpálya',
            'start_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'length' => 60,
            'can_attend_more' => false,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('trainings', [
        'name' => 'Strict edzés',
        'can_attend_more' => false,
    ]);
});

it('admin can set max attendees to 0 for unlimited', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->fillForm([
            'type' => 'easy',
            'name' => 'Korlátlan edzés',
            'place' => 'Sportpálya',
            'start_at' => now()->addDays(3)->format('Y-m-d H:i:s'),
            'length' => 60,
            'max_attendees' => 0,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('trainings', [
        'name' => 'Korlátlan edzés',
        'max_attendees' => 0,
    ]);
});

it('requires name, place, start time, and length', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->fillForm([
            'name' => null,
            'place' => null,
            'start_at' => null,
            'length' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'name' => 'required',
            'place' => 'required',
            'start_at' => 'required',
            'length' => 'required',
        ]);
});

it('defaults type to "easy"', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->assertFormFieldExists('type')
        ->assertFormSet(['type' => 'easy']);
});

it('defaults length to 60 minutes', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->assertFormSet(['length' => 60]);
});

it('defaults max attendees to 32', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->assertFormSet(['max_attendees' => 32]);
});

it('defaults overflow to enabled', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->assertFormSet(['can_attend_more' => true]);
});

it('offers the four training types with their labels', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateTraining::class)
        ->assertFormFieldExists('type', fn (Select $field): bool => $field->getOptions() === [
            'easy' => 'Felzárkóztató edzés',
            'running' => 'Futó edzés',
            'hard' => 'Haladó edzés',
            'other' => 'Egyéb',
        ]);
});
