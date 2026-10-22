<?php

use App\Filament\Resources\SubscriptionResource;
use App\Filament\Resources\SubscriptionResource\Pages\ListSubscriptions;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can view all subscriptions', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $subscriptions = Subscription::factory()->for($plan)->count(3)->create();

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->assertCanSeeTableRecords($subscriptions);
});

it('regular user can view the subscriptions list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(SubscriptionResource::getUrl('index'))
        ->assertSuccessful();
});

it('regular user only sees their own subscriptions', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();
    $ownSubscription = Subscription::factory()->for($plan)->for($user)->create();
    $otherSubscription = Subscription::factory()->for($plan)->create();

    $this->actingAs($user);

    livewire(ListSubscriptions::class)
        ->assertCanSeeTableRecords([$ownSubscription])
        ->assertCanNotSeeTableRecords([$otherSubscription]);
});

it('guest cannot view subscriptions', function () {
    $this->get(SubscriptionResource::getUrl('index'))
        ->assertRedirect('/login');
});

it('admin sees user avatar and name columns', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    Subscription::factory()->for($plan)->create();

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->assertCanRenderTableColumn('user.avatar_url')
        ->assertCanRenderTableColumn('user.name');
});

it('regular user does not see user avatar and name columns', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();
    Subscription::factory()->for($plan)->for($user)->create();

    $this->actingAs($user);

    $component = livewire(ListSubscriptions::class);

    expect($component->instance()->getTable()->getColumn('user.avatar_url'))->toBeNull()
        ->and($component->instance()->getTable()->getColumn('user.name'))->toBeNull();
});

it('displays plan name, purchase date, usage count, and expired status', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    Subscription::factory()->for($plan)->create();

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->assertCanRenderTableColumn('plan.name')
        ->assertCanRenderTableColumn('purchased_at')
        ->assertCanRenderTableColumn('usages_count')
        ->assertCanRenderTableColumn('expired');
});

it('admin can group subscriptions by user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->assertTableColumnExists('user.name');
});

it('admin can group subscriptions by plan type', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->assertTableColumnExists('plan.name');
});

it('can filter by active/expired status', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $active = Subscription::factory()->for($plan)->create(['expired_at' => null]);
    $expired = Subscription::factory()->for($plan)->expired()->create();

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->filterTable('expired_at', true)
        ->assertCanSeeTableRecords([$active])
        ->assertCanNotSeeTableRecords([$expired]);
});

it('admin can filter by user', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $sub1 = Subscription::factory()->for($plan)->for($user1)->create();
    $sub2 = Subscription::factory()->for($plan)->for($user2)->create();

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->filterTable('user', $user1->id)
        ->assertCanSeeTableRecords([$sub1])
        ->assertCanNotSeeTableRecords([$sub2]);
});

it('can filter by plan type', function () {
    $admin = User::factory()->admin()->create();
    $planA = Plan::factory()->create(['name' => 'Havi']);
    $planB = Plan::factory()->create(['name' => 'Éves']);
    $subA = Subscription::factory()->for($planA)->create();
    $subB = Subscription::factory()->for($planB)->create();

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->filterTable('plan', $planA->id)
        ->assertCanSeeTableRecords([$subA])
        ->assertCanNotSeeTableRecords([$subB]);
});

it('defaults to sorting by purchase date descending', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $user = User::factory()->create();
    $older = Subscription::factory()->for($plan)->for($user)->create(['purchased_at' => now()->subDays(10)]);
    $newer = Subscription::factory()->for($plan)->for($user)->create(['purchased_at' => now()]);

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->assertCanSeeTableRecords([$newer, $older], inOrder: true);
});

it('shows the plan name of a soft-deleted plan', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create(['name' => 'Törölt terv']);
    $subscription = Subscription::factory()->for($plan)->create();
    $plan->delete();

    $this->actingAs($admin);

    livewire(ListSubscriptions::class)
        ->assertCanSeeTableRecords([$subscription])
        ->assertSee('Törölt terv');
});
