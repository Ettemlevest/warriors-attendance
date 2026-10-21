<?php

use App\Models\Training;
use App\Models\TrainingAttendance;

it('treats zero max attendees as unlimited capacity', function () {
    $training = Training::factory()->unlimited()->create(['can_attend_more' => false]);
    TrainingAttendance::factory()->for($training)->count(3)->create();

    expect($training->hasUnlimitedCapacity())->toBeTrue()
        ->and($training->isFull())->toBeFalse()
        ->and($training->canAcceptRegistration())->toBeTrue();
});

it('is full once registrations reach the maximum', function () {
    $training = Training::factory()->full()->create();

    expect($training->isFull())->toBeTrue()
        ->and($training->canAcceptRegistration())->toBeFalse();
});

it('accepts registrations over the maximum when overflow is allowed', function () {
    $training = Training::factory()->full()->create(['can_attend_more' => true]);

    expect($training->isFull())->toBeTrue()
        ->and($training->canAcceptRegistration())->toBeTrue();
});

it('is not full below the maximum', function () {
    $training = Training::factory()->create(['max_attendees' => 2, 'can_attend_more' => false]);
    TrainingAttendance::factory()->for($training)->create();

    expect($training->isFull())->toBeFalse()
        ->and($training->canAcceptRegistration())->toBeTrue();
});

it('uses the eager loaded attendees count when present', function () {
    $training = Training::factory()->create(['max_attendees' => 1, 'can_attend_more' => false]);
    TrainingAttendance::factory()->for($training)->create();

    $loaded = Training::query()->withCount('attendees')->findOrFail($training->id);

    expect($loaded->registeredAttendeesCount())->toBe(1)
        ->and($loaded->isFull())->toBeTrue();
});
