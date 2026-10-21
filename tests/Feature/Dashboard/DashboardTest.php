<?php

use App\Filament\Resources\MessageResource\Widgets\ActiveMessages;
use App\Filament\Widgets\SubscriptionOverview;
use App\Filament\Widgets\UpcomingTraining;
use App\Models\Message;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAttendance;
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
});

it('shows "no active subscription" when user has no subscription', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(SubscriptionOverview::class)
        ->assertSee('Nincs aktív bérlet');
});

it('shows 6-month attendance chart', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    livewire(SubscriptionOverview::class)
        ->assertSee('Elmúlt 6 hónapban');
});

it('shows a message that has no date range', function () {
    $user = User::factory()->create();
    Message::factory()->create(['title' => 'Állandó közlemény', 'showed_from' => null, 'showed_to' => null]);

    $this->actingAs($user);

    livewire(ActiveMessages::class)->assertSee('Állandó közlemény');
});

it('shows a message with only a start date once that date has passed', function () {
    $user = User::factory()->create();
    Message::factory()->create(['title' => 'Már látható', 'showed_from' => now()->subDay(), 'showed_to' => null]);
    Message::factory()->create(['title' => 'Még nem látható', 'showed_from' => now()->addDay(), 'showed_to' => null]);

    $this->actingAs($user);

    livewire(ActiveMessages::class)
        ->assertSee('Már látható')
        ->assertDontSee('Még nem látható');
});

it('hides a message with only an end date once that date has passed', function () {
    $user = User::factory()->create();
    Message::factory()->create(['title' => 'Még látható', 'showed_from' => null, 'showed_to' => now()->addDay()]);
    Message::factory()->create(['title' => 'Már lejárt', 'showed_from' => null, 'showed_to' => now()->subDay()]);

    $this->actingAs($user);

    livewire(ActiveMessages::class)
        ->assertSee('Még látható')
        ->assertDontSee('Már lejárt');
});

it('lists a training that started within the last hour with its actions enabled', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->subMinutes(30)]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertCanSeeTableRecords([$training])
        ->assertTableActionEnabled('attend', $training);
});

it('does not list a training that started more than an hour ago', function () {
    $user = User::factory()->create();
    $training = Training::factory()->create(['start_at' => now()->subHours(2)]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)->assertCanNotSeeTableRecords([$training]);
});

it('lists trainings up to 30 days ahead and not beyond', function () {
    $user = User::factory()->create();
    $inside = Training::factory()->create(['start_at' => now()->addDays(29)]);
    $outside = Training::factory()->create(['start_at' => now()->addDays(31)]);

    $this->actingAs($user);

    livewire(UpcomingTraining::class)
        ->assertCanSeeTableRecords([$inside])
        ->assertCanNotSeeTableRecords([$outside]);
});

it('counts the current user registrations per month for six months regardless of confirmation', function () {
    $user = User::factory()->create();
    TrainingAttendance::factory()->for($user)->count(2)->create(['created_at' => now()->startOfMonth()->subMonths(2)->addDays(3)]);
    TrainingAttendance::factory()->for($user)->attended()->create(['created_at' => now()->startOfMonth()->subMonth()->addDays(3)]);
    TrainingAttendance::factory()->for($user)->create(['created_at' => now()->startOfMonth()->subMonths(8)]);
    TrainingAttendance::factory()->create(['created_at' => now()]);

    $this->actingAs($user);

    $stats = (fn () => $this->getStats())->call(livewire(SubscriptionOverview::class)->instance());

    expect($stats[1]->getValue())->toBe(3)
        ->and($stats[1]->getChart())->toBe([0, 0, 0, 2, 1, 0]);
});

it('shows the oldest active subscription, which is the one confirmations consume', function () {
    $user = User::factory()->create();
    Subscription::factory()->for(Plan::factory()->create(['name' => 'Régi']))->for($user)->create(['purchased_at' => now()->subMonths(2)]);
    Subscription::factory()->for(Plan::factory()->create(['name' => 'Új']))->for($user)->create(['purchased_at' => now()]);

    $this->actingAs($user);

    livewire(SubscriptionOverview::class)
        ->assertSee('Régi bérlet')
        ->assertDontSee('Új bérlet');
});

it('ignores expired subscriptions in the overview', function () {
    $user = User::factory()->create();
    Subscription::factory()->for(Plan::factory()->create(['name' => 'Lejárt']))->for($user)->expired()->create();

    $this->actingAs($user);

    livewire(SubscriptionOverview::class)
        ->assertSee('Nincs aktív bérlet')
        ->assertDontSee('Lejárt bérlet');
});

it('strips scripts and event handlers from message bodies before rendering', function () {
    $user = User::factory()->create();
    Message::factory()->create([
        'title' => 'Formázott közlemény',
        'body' => '<p>Fontos <strong>infó</strong></p><script>alert(1)</script><img src="x" onerror="alert(1)">',
        'showed_from' => now()->subDay(),
        'showed_to' => now()->addDay(),
    ]);

    $this->actingAs($user);

    livewire(ActiveMessages::class)
        ->assertSee('Formázott közlemény')
        ->assertSee('<p>Fontos <strong>infó</strong></p>', false)
        ->assertDontSee('<script>', false)
        ->assertDontSee('onerror', false);
});
