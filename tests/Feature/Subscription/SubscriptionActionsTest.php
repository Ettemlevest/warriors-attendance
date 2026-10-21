<?php

use App\Filament\Resources\SubscriptionResource\Pages\ListSubscriptions;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\TrainingAttendance;
use App\Models\User;

use function Pest\Livewire\livewire;

describe('expire / reactivate actions', function () {
    it('admin can manually expire an active subscription', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->for($plan)->create();

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->callTableAction('expire_subscription', $subscription->getKey());

        expect($subscription->fresh()->expired_at)->not->toBeNull();
    });

    it('expire action is hidden for already expired subscriptions', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->for($plan)->expired()->create();

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->assertTableActionHidden('expire_subscription', $subscription->getKey());
    });

    it('regular user cannot see the expire action', function () {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->for($plan)->for($user)->create();

        $this->actingAs($user);

        livewire(ListSubscriptions::class)
            ->assertTableActionHidden('expire_subscription', $subscription->getKey());
    });

    it('admin can reactivate an expired subscription', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create(['sessions' => 10]);
        $subscription = Subscription::factory()->for($plan)->expired()->create();

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->callTableAction('reactivate_subscription', $subscription->getKey());

        expect($subscription->fresh()->expired_at)->toBeNull();
    });

    it('reactivate action is hidden for active subscriptions', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->for($plan)->create();

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->assertTableActionHidden('reactivate_subscription', $subscription->getKey());
    });

    it('reactivate action is disabled when subscription is fully used up', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create(['sessions' => 1]);
        $subscription = Subscription::factory()->for($plan)->expired()->create();
        TrainingAttendance::factory()->create([
            'subscription_id' => $subscription->id,
            'user_id' => $subscription->user_id,
        ]);

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->assertTableActionDisabled('reactivate_subscription', $subscription->getKey());
    });

    it('regular user cannot see the reactivate action', function () {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->for($plan)->for($user)->expired()->create();

        $this->actingAs($user);

        livewire(ListSubscriptions::class)
            ->assertTableActionHidden('reactivate_subscription', $subscription->getKey());
    });
});

describe('bulk actions', function () {
    it('admin can bulk expire subscriptions', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->for($plan)->create();

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->callTableBulkAction('bulk_expire_subscription', [$subscription->getKey()]);

        expect($subscription->fresh()->expired_at)->not->toBeNull();
    });

    it('admin can bulk reactivate subscriptions', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create(['sessions' => 10]);
        $subscription = Subscription::factory()->for($plan)->expired()->create();

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->callTableBulkAction('bulk_reactivate_subscription', [$subscription->getKey()]);

        expect($subscription->fresh()->expired_at)->toBeNull();
    });

    it('bulk reactivate leaves a used-up subscription expired', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create(['sessions' => 1]);
        $subscription = Subscription::factory()->for($plan)->expired()->create();
        TrainingAttendance::factory()->for($subscription->user)->create([
            'subscription_id' => $subscription->id,
            'attended' => true,
        ]);

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->callTableBulkAction('bulk_reactivate_subscription', [$subscription->getKey()]);

        expect($subscription->fresh()->expired_at)->not->toBeNull();
    });

    it('bulk expire handles several subscriptions at once', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create();
        $first = Subscription::factory()->for($plan)->create();
        $second = Subscription::factory()->for($plan)->create();

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->callTableBulkAction('bulk_expire_subscription', [$first->getKey(), $second->getKey()]);

        expect($first->fresh()->expired_at)->not->toBeNull()
            ->and($second->fresh()->expired_at)->not->toBeNull();
    });

    it('regular user cannot see bulk subscription actions', function () {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        Subscription::factory()->for($plan)->for($user)->create();

        $this->actingAs($user);

        livewire(ListSubscriptions::class)
            ->assertTableBulkActionHidden('bulk_expire_subscription')
            ->assertTableBulkActionHidden('bulk_reactivate_subscription');
    });
});

describe('delete', function () {
    it('admin can delete a subscription', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->for($plan)->create();

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->callTableAction('delete', $subscription->getKey());

        $this->assertDatabaseMissing('subscriptions', ['id' => $subscription->id]);
    });

    it('regular user cannot delete a subscription', function () {
        $user = User::factory()->create();
        $plan = Plan::factory()->create();
        $subscription = Subscription::factory()->for($plan)->for($user)->create();

        $this->actingAs($user);

        livewire(ListSubscriptions::class)
            ->assertTableActionHidden('delete', $subscription->getKey());
    });

    it('admin can bulk delete subscriptions', function () {
        $admin = User::factory()->admin()->create();
        $plan = Plan::factory()->create();
        $subscriptions = Subscription::factory()->for($plan)->count(2)->create();

        $this->actingAs($admin);

        livewire(ListSubscriptions::class)
            ->callTableBulkAction('delete', $subscriptions->pluck('id')->toArray());

        $this->assertDatabaseCount('subscriptions', 0);
    });
});
