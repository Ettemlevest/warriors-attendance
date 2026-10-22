<?php

use App\Models\Training;

it('is open for registration until one hour after the start', function () {
    expect(Training::factory()->create(['start_at' => now()->addDay()])->isOpenForRegistration())->toBeTrue()
        ->and(Training::factory()->create(['start_at' => now()->subMinutes(59)])->isOpenForRegistration())->toBeTrue()
        ->and(Training::factory()->create(['start_at' => now()->subMinutes(61)])->isOpenForRegistration())->toBeFalse();
});

it('has ended once the start time plus the length has passed', function () {
    expect(Training::factory()->create(['start_at' => now()->addDay(), 'length' => 60])->hasEnded())->toBeFalse()
        ->and(Training::factory()->create(['start_at' => now()->subMinutes(30), 'length' => 60])->hasEnded())->toBeFalse()
        ->and(Training::factory()->create(['start_at' => now()->subMinutes(61), 'length' => 60])->hasEnded())->toBeTrue();
});

it('does not mutate the start time when checking whether it has ended', function () {
    $training = Training::factory()->create(['start_at' => now()->subHours(2)->startOfMinute(), 'length' => 60]);
    $startBefore = $training->start_at->toDateTimeString();

    $training->hasEnded();

    expect($training->start_at->toDateTimeString())->toBe($startBefore);
});
