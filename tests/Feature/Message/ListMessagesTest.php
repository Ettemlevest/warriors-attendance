<?php

use App\Filament\Resources\MessageResource;
use App\Filament\Resources\MessageResource\Pages\ListMessages;
use App\Models\Message;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can view the messages list', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(MessageResource::getUrl('index'))
        ->assertSuccessful();
});

it('regular user cannot view the messages list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(MessageResource::getUrl('index'))
        ->assertForbidden();
});

it('guest cannot view the messages list', function () {
    $this->get(MessageResource::getUrl('index'))
        ->assertRedirect('/login');
});

it('displays message title, showed_from, and showed_to', function () {
    $admin = User::factory()->admin()->create();
    $messages = Message::factory()->count(3)->create();

    $this->actingAs($admin);

    livewire(ListMessages::class)
        ->assertCanSeeTableRecords($messages)
        ->assertCanRenderTableColumn('title')
        ->assertCanRenderTableColumn('showed_from')
        ->assertCanRenderTableColumn('showed_to');
});

it('can search messages by title', function () {
    $admin = User::factory()->admin()->create();
    $target = Message::factory()->create(['title' => 'Fontos értesítés']);
    $other = Message::factory()->create(['title' => 'Egyéb üzenet']);

    $this->actingAs($admin);

    livewire(ListMessages::class)
        ->searchTable('Fontos')
        ->assertCanSeeTableRecords([$target])
        ->assertCanNotSeeTableRecords([$other]);
});

it('defaults to sorting by showed_from descending', function () {
    $admin = User::factory()->admin()->create();
    $oldest = Message::factory()->create(['showed_from' => now()->subDays(10)]);
    $newest = Message::factory()->create(['showed_from' => now()]);
    $middle = Message::factory()->create(['showed_from' => now()->subDays(5)]);

    $this->actingAs($admin);

    livewire(ListMessages::class)
        ->assertCanSeeTableRecords([$newest, $middle, $oldest], inOrder: true);
});
