<?php

use App\Filament\Resources\MessageResource\Widgets\ActiveMessages;
use App\Filament\Widgets\SubscriptionOverview;
use App\Filament\Widgets\UpcomingTraining;
use App\Models\Message;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;

use function Pest\Livewire\livewire;

it('cannot be accessed by guests', function () {
    $this->get('/')
        ->assertRedirect('/login');
});

it('can be accessed by authenticated users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertSuccessful();
});

it('shows the account widget', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/')
        ->assertSee($user->name);
});

it('shows active messages when messages exist', function () {
    $user = User::factory()->create();
    Message::factory()->create([
        'title' => 'Fontos értesítés',
        'showed_from' => now()->subDay(),
        'showed_to' => now()->addDay(),
    ]);

    $this->actingAs($user);

    livewire(ActiveMessages::class)
        ->assertSee('Fontos értesítés');
});

it('does not show messages outside their active date range', function () {
    $user = User::factory()->create();
    Message::factory()->create([
        'title' => 'Lejárt üzenet',
        'showed_from' => now()->subDays(10),
        'showed_to' => now()->subDays(5),
    ]);

    $this->actingAs($user);

    livewire(ActiveMessages::class)
        ->assertDontSee('Lejárt üzenet');
});

it('shows upcoming trainings within the next 30 days', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create([
        'name' => 'Hétfői edzés',
        'start_at' => now()->addDays(3),
    ]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertCanSeeTableRecords([$training]);
});

it('does not show trainings older than 30 days', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create([
        'name' => 'Régi edzés',
        'start_at' => now()->subDays(31),
    ]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertCanNotSeeTableRecords([$training]);
});

it('shows subscription overview with active subscription stats', function () {
    $plan = Plan::factory()->create(['name' => 'Havi', 'sessions' => 10]);
    $user = User::factory()->create();
    Subscription::factory()->for($plan)->for($user)->create();

    $this->actingAs($user);

    livewire(SubscriptionOverview::class)
        ->assertSee('Havi bérlet');
})->skip(fn () => config('database.default') === 'sqlite', 'SubscriptionOverview uses MySQL-specific DATE_FORMAT');

it('shows "no active subscription" when user has no subscription', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(SubscriptionOverview::class)
        ->assertSee('Nincs aktív bérlet');
})->skip(fn () => config('database.default') === 'sqlite', 'SubscriptionOverview uses MySQL-specific DATE_FORMAT');

it('shows 6-month attendance chart', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(SubscriptionOverview::class)
        ->assertSee('Elmúlt 6 hónapban');
})->skip(fn () => config('database.default') === 'sqlite', 'SubscriptionOverview uses MySQL-specific DATE_FORMAT');
