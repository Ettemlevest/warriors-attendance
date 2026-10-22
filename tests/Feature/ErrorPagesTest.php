<?php

use App\Models\User;

it('renders the not found page in hungarian', function () {
    $this->get('/nincs-ilyen-oldal')
        ->assertNotFound()
        ->assertSee('Az oldal nem található');
});

it('renders the forbidden page in hungarian', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/users')
        ->assertForbidden()
        ->assertSee('Tiltott');
});
