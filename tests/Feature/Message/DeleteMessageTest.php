<?php

use App\Filament\Resources\MessageResource;
use App\Filament\Resources\MessageResource\Pages\ListMessages;
use App\Models\Message;
use App\Models\User;

use function Pest\Livewire\livewire;

it('admin can delete a message', function () {
    $admin = User::factory()->admin()->create();
    $message = Message::factory()->create();

    $this->actingAs($admin);

    livewire(ListMessages::class)
        ->callTableAction('delete', $message);

    $this->assertModelMissing($message);
});

it('regular user cannot delete a message', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(MessageResource::getUrl('index'))
        ->assertForbidden();
});

it('admin can bulk delete messages', function () {
    $admin = User::factory()->admin()->create();
    $messages = Message::factory()->count(3)->create();

    $this->actingAs($admin);

    livewire(ListMessages::class)
        ->callTableBulkAction('delete', $messages);

    foreach ($messages as $message) {
        $this->assertModelMissing($message);
    }
});
