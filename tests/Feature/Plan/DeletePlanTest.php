<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Pages\EditPlan;
use App\Filament\Resources\PlanResource\Pages\ListPlans;
use App\Models\Plan;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can soft-delete a plan', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();

    $this->actingAs($admin);

    livewire(EditPlan::class, ['record' => $plan->getRouteKey()])
        ->callAction('delete');

    $this->assertSoftDeleted($plan);
});

it('regular user cannot delete a plan', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();

    $this->actingAs($user)
        ->get(PlanResource::getUrl('edit', ['record' => $plan]))
        ->assertForbidden();
});

it('admin can restore a soft-deleted plan', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create(['deleted_at' => now()]);

    $this->actingAs($admin);

    livewire(EditPlan::class, ['record' => $plan->getRouteKey()])
        ->callAction('restore');

    expect($plan->fresh()->deleted_at)->toBeNull();
});

it('admin can bulk delete plans', function () {
    $admin = User::factory()->admin()->create();
    $plans = Plan::factory()->count(3)->create();

    $this->actingAs($admin);

    livewire(ListPlans::class)
        ->callTableBulkAction('delete', $plans);

    foreach ($plans as $plan) {
        $this->assertSoftDeleted($plan);
    }
});

it('admin can bulk restore plans', function () {
    $admin = User::factory()->admin()->create();
    $plans = Plan::factory()->count(3)->create(['deleted_at' => now()]);

    $this->actingAs($admin);

    livewire(ListPlans::class)
        ->filterTable('trashed', true)
        ->callTableBulkAction('restore', $plans);

    foreach ($plans as $plan) {
        expect($plan->fresh()->deleted_at)->toBeNull();
    }
});
