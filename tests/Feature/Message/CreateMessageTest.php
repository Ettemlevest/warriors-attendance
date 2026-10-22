<?php

use App\Filament\Resources\MessageResource;
use App\Filament\Resources\MessageResource\Pages\CreateMessage;
use App\Models\Message;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can render the create message page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(MessageResource::getUrl('create'))
        ->assertSuccessful();
});

it('regular user cannot render the create message page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(MessageResource::getUrl('create'))
        ->assertForbidden();
});

it('admin can create a message with title, body, and date range', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateMessage::class)
        ->fillForm([
            'title' => 'Fontos értesítés',
            'body' => '<p>Ez egy fontos üzenet.</p>',
            'showed_from' => '2026-01-01',
            'showed_to' => '2026-01-31',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Message::class, [
        'title' => 'Fontos értesítés',
    ]);
});

it('admin can create a message without a body', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateMessage::class)
        ->fillForm([
            'title' => 'Rövid értesítés',
            'body' => null,
            'showed_from' => '2026-01-01',
            'showed_to' => '2026-01-31',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Message::class, [
        'title' => 'Rövid értesítés',
        'body' => null,
    ]);
});

it('admin can create a message without date constraints', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateMessage::class)
        ->fillForm([
            'title' => 'Dátum nélküli üzenet',
            'showed_from' => null,
            'showed_to' => null,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas(Message::class, [
        'title' => 'Dátum nélküli üzenet',
        'showed_from' => null,
        'showed_to' => null,
    ]);
});

it('requires a title', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    livewire(CreateMessage::class)
        ->fillForm(['title' => '', 'body' => 'Szöveg'])
        ->call('create')
        ->assertHasFormErrors(['title' => 'required']);
});
