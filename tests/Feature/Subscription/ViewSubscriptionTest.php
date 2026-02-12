<?php

use App\Filament\Resources\SubscriptionResource;
use App\Filament\Resources\SubscriptionResource\Pages\ViewSubscription;
use App\Filament\Resources\SubscriptionResource\RelationManagers\UsagesRelationManager;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can view a subscription', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->for($plan)->create();

    $this->actingAs($admin)
        ->get(SubscriptionResource::getUrl('view', ['record' => $subscription]))
        ->assertSuccessful();
});

it('regular user can view their own subscription', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->for($plan)->for($user)->create();

    $this->actingAs($user)
        ->get(SubscriptionResource::getUrl('view', ['record' => $subscription]))
        ->assertSuccessful();
});

it('regular user cannot view another user subscription', function () {
    $user = User::factory()->create();
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->for($plan)->create();

    $this->actingAs($user)
        ->get(SubscriptionResource::getUrl('view', ['record' => $subscription]))
        ->assertNotFound();
});

it('displays plan name, purchase date, usage count, and expired status', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create(['name' => 'Havi bérlet']);
    $subscription = Subscription::factory()->for($plan)->create();

    $this->actingAs($admin);

    livewire(ViewSubscription::class, ['record' => $subscription->getRouteKey()])
        ->assertSee('Havi bérlet');
});

it('shows the usages relation manager with training details', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->for($plan)->create();
    $training = Training::factory()->create(['name' => 'Teszt edzés']);
    TrainingAttendance::factory()
        ->for($training)
        ->create(['subscription_id' => $subscription->id]);

    $this->actingAs($admin);

    livewire(UsagesRelationManager::class, [
        'ownerRecord' => $subscription,
        'pageClass' => ViewSubscription::class,
    ])->assertSuccessful();
});

it('usages list shows training type, name, place, start time, and length', function () {
    $admin = User::factory()->admin()->create();
    $plan = Plan::factory()->create();
    $subscription = Subscription::factory()->for($plan)->create();
    $training = Training::factory()->create();
    TrainingAttendance::factory()
        ->for($training)
        ->create(['subscription_id' => $subscription->id]);

    $this->actingAs($admin);

    livewire(UsagesRelationManager::class, [
        'ownerRecord' => $subscription,
        'pageClass' => ViewSubscription::class,
    ])
        ->assertCanRenderTableColumn('training.type')
        ->assertCanRenderTableColumn('training.name')
        ->assertCanRenderTableColumn('training.start_at')
        ->assertCanRenderTableColumn('training.length');
});
