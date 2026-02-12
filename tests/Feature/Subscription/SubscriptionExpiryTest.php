<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;

it('subscription auto-expires when usage count reaches plan session limit', function () {
})->todo();

it('subscription does not expire when usage count is below plan session limit', function () {
})->todo();

it('subscription un-expires when attendance is rejected and count drops below limit', function () {
})->todo();

it('confirming attendance on last available session expires the subscription', function () {
})->todo();

it('rejecting attendance on expired subscription reactivates it', function () {
})->todo();

it('findLiveSubscriptionForUser returns the oldest active subscription', function () {
})->todo();

it('findLiveSubscriptionForUser returns null when no active subscription exists', function () {
})->todo();

it('usedUp returns true when usages equal plan sessions', function () {
})->todo();

it('usedUp returns true when usages exceed plan sessions', function () {
})->todo();

it('usedUp returns false when usages are below plan sessions', function () {
})->todo();
