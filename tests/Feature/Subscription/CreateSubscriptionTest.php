<?php

use App\Filament\Resources\SubscriptionResource;
use App\Filament\Resources\SubscriptionResource\Pages\CreateSubscription;
use App\Models\Plan;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can render the create subscription page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(SubscriptionResource::getUrl('create'))
        ->assertSuccessful();
});

it('regular user cannot render the create subscription page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(SubscriptionResource::getUrl('create'))
        ->assertForbidden();
});

it('admin can create a subscription for a user with a plan', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($admin);

    livewire(CreateSubscription::class)
        ->fillForm([
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'purchased_at' => now()->format('Y-m-d'),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('subscriptions', [
        'plan_id' => $plan->id,
        'user_id' => $user->id,
    ]);
});

it('requires plan and user to create a subscription', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateSubscription::class)
        ->fillForm([
            'plan_id' => null,
            'user_id' => null,
            'purchased_at' => null,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'plan_id' => 'required',
            'user_id' => 'required',
            'purchased_at' => 'required',
        ]);
});

it('defaults purchase date to today', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateSubscription::class)
        ->assertFormSet(['purchased_at' => now()->format('Y-m-d')]);
});

it('admin can set a custom purchase date', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $user = User::factory()->create();
    $customDate = now()->subDays(5)->format('Y-m-d');

    $this->actingAs($admin);

    livewire(CreateSubscription::class)
        ->fillForm([
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'purchased_at' => $customDate,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('subscriptions', [
        'plan_id' => $plan->id,
        'purchased_at' => $customDate.' 00:00:00',
    ]);
});

it('admin can search for plans when creating', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateSubscription::class)
        ->assertFormFieldExists('plan_id');
});

it('admin can search for users when creating', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateSubscription::class)
        ->assertFormFieldExists('user_id');
});
