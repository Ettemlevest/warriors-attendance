<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;

it('subscription auto-expires when usage count reaches plan session limit', function () {
    $plan = Plan::factory()->create(['sessions' => 2]);
    $subscription = Subscription::factory()->for($plan)->create();

    TrainingAttendance::factory()->count(2)->create([
        'subscription_id' => $subscription->id,
        'user_id' => $subscription->user_id,
    ]);

    $subscription->refreshExpiry();

    expect($subscription->fresh()->expired_at)->not->toBeNull();
});

it('subscription does not expire when usage count is below plan session limit', function () {
    $plan = Plan::factory()->create(['sessions' => 5]);
    $subscription = Subscription::factory()->for($plan)->create();

    TrainingAttendance::factory()->count(3)->create([
        'subscription_id' => $subscription->id,
        'user_id' => $subscription->user_id,
    ]);

    $subscription->refreshExpiry();

    expect($subscription->fresh()->expired_at)->toBeNull();
});

it('subscription un-expires when attendance is rejected and count drops below limit', function () {
    $plan = Plan::factory()->create(['sessions' => 1]);
    $user = User::factory()->create();
    $subscription = Subscription::factory()->for($plan)->for($user)->create();
    $training = Training::factory()->create();
    $attendance = TrainingAttendance::factory()
        ->for($training)
        ->for($user)
        ->attended()
        ->create(['subscription_id' => $subscription->id]);

    $subscription->refreshExpiry();
    expect($subscription->fresh()->expired_at)->not->toBeNull();

    $attendance->rejectAttendance();

    expect($subscription->fresh()->expired_at)->toBeNull();
});

it('confirming attendance on last available session expires the subscription', function () {
    $plan = Plan::factory()->create(['sessions' => 1]);
    $user = User::factory()->create();
    $subscription = Subscription::factory()->for($plan)->for($user)->create();
    $training = Training::factory()->create();
    $attendance = TrainingAttendance::factory()
        ->for($training)
        ->for($user)
        ->create();

    $attendance->confirmAttendance();

    expect($subscription->fresh()->expired_at)->not->toBeNull();
});

it('rejecting attendance on expired subscription reactivates it', function () {
    $plan = Plan::factory()->create(['sessions' => 1]);
    $user = User::factory()->create();
    $subscription = Subscription::factory()->for($plan)->for($user)->expired()->create();
    $training = Training::factory()->create();
    $attendance = TrainingAttendance::factory()
        ->for($training)
        ->for($user)
        ->attended()
        ->create(['subscription_id' => $subscription->id]);

    $attendance->rejectAttendance();

    expect($subscription->fresh()->expired_at)->toBeNull();
});

it('findLiveSubscriptionForUser returns the oldest active subscription', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();
    $older = Subscription::factory()->for($plan)->for($user)->create(['purchased_at' => now()->subDays(10)]);
    $newer = Subscription::factory()->for($plan)->for($user)->create(['purchased_at' => now()]);

    $result = Subscription::findLiveSubscriptionForUser($user->id);

    expect($result->id)->toBe($older->id);
});

it('findLiveSubscriptionForUser returns null when no active subscription exists', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();
    Subscription::factory()->for($plan)->for($user)->expired()->create();

    $result = Subscription::findLiveSubscriptionForUser($user->id);

    expect($result)->toBeNull();
});

it('usedUp returns true when usages equal plan sessions', function () {
    $plan = Plan::factory()->create(['sessions' => 2]);
    $subscription = Subscription::factory()->for($plan)->create();
    TrainingAttendance::factory()->count(2)->create([
        'subscription_id' => $subscription->id,
        'user_id' => $subscription->user_id,
    ]);

    expect($subscription->fresh()->usedUp())->toBeTrue();
});

it('usedUp returns true when usages exceed plan sessions', function () {
    $plan = Plan::factory()->create(['sessions' => 1]);
    $subscription = Subscription::factory()->for($plan)->create();
    TrainingAttendance::factory()->count(3)->create([
        'subscription_id' => $subscription->id,
        'user_id' => $subscription->user_id,
    ]);

    expect($subscription->fresh()->usedUp())->toBeTrue();
});

it('usedUp returns false when usages are below plan sessions', function () {
    $plan = Plan::factory()->create(['sessions' => 5]);
    $subscription = Subscription::factory()->for($plan)->create();
    TrainingAttendance::factory()->count(2)->create([
        'subscription_id' => $subscription->id,
        'user_id' => $subscription->user_id,
    ]);

    expect($subscription->fresh()->usedUp())->toBeFalse();
});
