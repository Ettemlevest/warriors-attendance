<?php

it('redirects guests to the filament login page', function () {
    $this->get('/')
        ->assertRedirect('/login');
});
