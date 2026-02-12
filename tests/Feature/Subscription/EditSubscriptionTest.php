<?php

use App\Filament\Resources\SubscriptionResource;
use App\Filament\Resources\SubscriptionResource\Pages\EditSubscription;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\TrainingAttendance;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can render the edit subscription page', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->for($plan)->create();

    $this->actingAs($admin)
        ->get(SubscriptionResource::getUrl('edit', ['record' => $subscription]))
        ->assertSuccessful();
});

it('regular user cannot render the edit subscription page', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->for($plan)->for($user)->create();

    $this->actingAs($user)
        ->get(SubscriptionResource::getUrl('edit', ['record' => $subscription]))
        ->assertForbidden();
});

it('admin can update the purchase date', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->for($plan)->create();
    $newDate = now()->subDays(3)->format('Y-m-d');

    $this->actingAs($admin);

    livewire(EditSubscription::class, ['record' => $subscription->getRouteKey()])
        ->fillForm(['purchased_at' => $newDate])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($subscription->fresh()->purchased_at->format('Y-m-d'))->toBe($newDate);
});

it('plan selector is disabled when subscription has usages', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create(['sessions' => 10]);
    $subscription = Subscription::factory()->for($plan)->create();
    TrainingAttendance::factory()->create(['subscription_id' => $subscription->id]);

    $this->actingAs($admin);

    livewire(EditSubscription::class, ['record' => $subscription->getRouteKey()])
        ->assertFormFieldIsDisabled('plan_id');
});

it('user selector is disabled when subscription has usages', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create(['sessions' => 10]);
    $subscription = Subscription::factory()->for($plan)->create();
    TrainingAttendance::factory()->create(['subscription_id' => $subscription->id]);

    $this->actingAs($admin);

    livewire(EditSubscription::class, ['record' => $subscription->getRouteKey()])
        ->assertFormFieldIsDisabled('user_id');
});

it('purchase date is disabled when subscription is fully used up', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create(['sessions' => 1]);
    $subscription = Subscription::factory()->for($plan)->create(['expired_at' => now()]);
    TrainingAttendance::factory()->create(['subscription_id' => $subscription->id]);

    $this->actingAs($admin);

    livewire(EditSubscription::class, ['record' => $subscription->getRouteKey()])
        ->assertFormFieldIsDisabled('purchased_at');
});

it('shows meta data section with created and updated timestamps', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->for($plan)->create();

    $this->actingAs($admin);

    livewire(EditSubscription::class, ['record' => $subscription->getRouteKey()])
        ->assertSee('Meta adatok')
        ->assertSee($subscription->created_at)
        ->assertSee($subscription->updated_at);
});
