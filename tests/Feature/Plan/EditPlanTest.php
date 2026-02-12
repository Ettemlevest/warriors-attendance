<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Pages\EditPlan;
use App\Models\Plan;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can render the edit plan page', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();

    $this->actingAs($admin)
        ->get(PlanResource::getUrl('edit', ['record' => $plan]))
        ->assertSuccessful();
});

it('regular user cannot render the edit plan page', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();

    $this->actingAs($user)
        ->get(PlanResource::getUrl('edit', ['record' => $plan]))
        ->assertForbidden();
});

it('admin can update plan name', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create(['name' => 'Old Name']);

    $this->actingAs($admin);

    livewire(EditPlan::class, ['record' => $plan->getRouteKey()])
        ->fillForm(['name' => 'New Name'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($plan->fresh()->name)->toBe('New Name');
});

it('admin can update plan session count', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create(['sessions' => 10]);

    $this->actingAs($admin);

    livewire(EditPlan::class, ['record' => $plan->getRouteKey()])
        ->fillForm(['sessions' => 20])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($plan->fresh()->sessions)->toBe(20);
});

it('plan name must remain unique when editing', function () {
    $admin = User::factory()->admin()->create();
    Plan::factory()->create(['name' => 'Existing Plan']);
    $plan = Plan::factory()->create(['name' => 'My Plan']);

    $this->actingAs($admin);

    livewire(EditPlan::class, ['record' => $plan->getRouteKey()])
        ->fillForm(['name' => 'Existing Plan'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

it('shows meta data section with created and updated timestamps', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();

    $this->actingAs($admin);

    livewire(EditPlan::class, ['record' => $plan->getRouteKey()])
        ->assertSee('Meta adatok')
        ->assertSee($plan->created_at)
        ->assertSee($plan->updated_at);
});
