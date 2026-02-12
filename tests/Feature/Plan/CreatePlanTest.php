<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Pages\CreatePlan;
use App\Models\Plan;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can render the create plan page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(PlanResource::getUrl('create'))
        ->assertSuccessful();
});

it('regular user cannot render the create plan page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(PlanResource::getUrl('create'))
        ->assertForbidden();
});

it('admin can create a plan with name and session count', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreatePlan::class)
        ->fillForm([
            'name' => 'Havi bérlet',
            'sessions' => 12,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Plan::class, [
        'name' => 'Havi bérlet',
        'sessions' => 12,
    ]);
});

it('requires name and sessions to create a plan', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreatePlan::class)
        ->fillForm([
            'name' => '',
            'sessions' => '',
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'required', 'sessions' => 'required']);
});

it('plan name must be unique', function () {
    $admin = User::factory()->admin()->create();
    Plan::factory()->create(['name' => 'Havi bérlet']);

    $this->actingAs($admin);

    livewire(CreatePlan::class)
        ->fillForm([
            'name' => 'Havi bérlet',
            'sessions' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('sessions must be at least 1', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreatePlan::class)
        ->fillForm([
            'name' => 'Test',
            'sessions' => 0,
        ])
        ->call('create')
        ->assertHasFormErrors(['sessions' => 'min']);
});

it('sessions must be at most 255', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreatePlan::class)
        ->fillForm([
            'name' => 'Test',
            'sessions' => 256,
        ])
        ->call('create')
        ->assertHasFormErrors(['sessions' => 'max']);
});

it('defaults sessions to 10', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreatePlan::class)
        ->assertFormFieldExists('sessions', function ($field) {
            return $field->getState() == 10;
        });
});
