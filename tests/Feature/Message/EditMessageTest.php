<?php

use App\Filament\Resources\MessageResource;
use App\Filament\Resources\MessageResource\Pages\EditMessage;
use App\Models\Message;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can render the edit message page', function () {
    $admin = User::factory()->admin()->create();
    $message = Message::factory()->create();

    $this->actingAs($admin)
        ->get(MessageResource::getUrl('edit', ['record' => $message]))
        ->assertSuccessful();
});

it('regular user cannot render the edit message page', function () {
    $user = User::factory()->create();
    $message = Message::factory()->create();

    $this->actingAs($user)
        ->get(MessageResource::getUrl('edit', ['record' => $message]))
        ->assertForbidden();
});

it('admin can update message title', function () {
    $admin = User::factory()->admin()->create();
    $message = Message::factory()->create(['title' => 'Régi cím']);

    $this->actingAs($admin);

    livewire(EditMessage::class, ['record' => $message->getRouteKey()])
        ->fillForm(['title' => 'Új cím'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($message->fresh()->title)->toBe('Új cím');
});

it('admin can update message body', function () {
    $admin = User::factory()->admin()->create();
    $message = Message::factory()->create();

    $this->actingAs($admin);

    livewire(EditMessage::class, ['record' => $message->getRouteKey()])
        ->fillForm(['body' => '<p>Frissített tartalom</p>'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($message->fresh()->body)->toContain('Frissített tartalom');
});

it('admin can update message date range', function () {
    $admin = User::factory()->admin()->create();
    $message = Message::factory()->create();

    $this->actingAs($admin);

    livewire(EditMessage::class, ['record' => $message->getRouteKey()])
        ->fillForm([
            'showed_from' => '2026-06-01',
            'showed_to' => '2026-06-30',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $fresh = $message->fresh();
    expect($fresh->showed_from->format('Y-m-d'))->toBe('2026-06-01')
        ->and($fresh->showed_to->format('Y-m-d'))->toBe('2026-06-30');
});

it('shows meta data section with created and updated timestamps', function () {
    $admin = User::factory()->admin()->create();
    $message = Message::factory()->create();

    $this->actingAs($admin);

    livewire(EditMessage::class, ['record' => $message->getRouteKey()])
        ->assertSee('Meta adatok')
        ->assertSee($message->created_at)
        ->assertSee($message->updated_at);
});
