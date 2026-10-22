<?php

use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Pages\ListPlans;
use App\Models\Plan;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can view the plans list', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(PlanResource::getUrl('index'))
        ->assertSuccessful();
});

it('regular user cannot view the plans list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(PlanResource::getUrl('index'))
        ->assertForbidden();
});

it('guest cannot view the plans list', function () {
    $this->get(PlanResource::getUrl('index'))
        ->assertRedirect('/login');
});

it('displays plan name, sessions, subscription count, and created date', function () {
    $admin = User::factory()->admin()->create();
    $plans = Plan::factory()->count(3)->create();

    $this->actingAs($admin);

    livewire(ListPlans::class)
        ->assertCanSeeTableRecords($plans)
        ->assertCanRenderTableColumn('name')
        ->assertCanRenderTableColumn('sessions')
        ->assertCanRenderTableColumn('subscriptions_count')
        ->assertCanRenderTableColumn('created_at');
});

it('can see soft-deleted plans with trashed filter', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $trashedPlan = Plan::factory()->create(['deleted_at' => now()]);

    $this->actingAs($admin);

    livewire(ListPlans::class)
        ->assertCanSeeTableRecords([$plan])
        ->assertCanNotSeeTableRecords([$trashedPlan])
        ->filterTable('trashed', true)
        ->assertCanSeeTableRecords([$plan, $trashedPlan]);
});

it('defaults to sorting by name ascending', function () {
    $admin = User::factory()->admin()->create();
    $planB = Plan::factory()->create(['name' => 'Beta']);
    $planA = Plan::factory()->create(['name' => 'Alpha']);
    $planC = Plan::factory()->create(['name' => 'Charlie']);

    $this->actingAs($admin);

    livewire(ListPlans::class)
        ->assertCanSeeTableRecords([$planA, $planB, $planC], inOrder: true);
});
