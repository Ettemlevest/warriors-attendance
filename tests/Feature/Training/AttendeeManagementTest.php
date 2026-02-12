<?php

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;

// --- Viewing attendees ---

it('admin can see the attendees relation manager on training view', function () {
})->todo();

it('regular user can see the attendees list on training view', function () {
})->todo();

it('admin sees attended, extra, and subscription columns', function () {
})->todo();

it('regular user does not see attended, extra, and subscription columns', function () {
})->todo();

// --- Adding attendees (admin) ---

it('admin can manually add an attendee to a training', function () {
})->todo();

it('admin can search for users when adding an attendee', function () {
})->todo();

// --- Confirming attendance ---

it('admin can confirm attendance for a single attendee', function () {
})->todo();

it('confirming attendance sets attended to true', function () {
})->todo();

it('confirming attendance auto-associates the user live subscription', function () {
})->todo();

it('confirming attendance triggers subscription expiry check', function () {
})->todo();

it('confirming attendance when user has no subscription still sets attended', function () {
})->todo();

it('confirm button is hidden when already attended', function () {
})->todo();

it('regular user cannot see the confirm button', function () {
})->todo();

// --- Rejecting attendance ---

it('admin can reject attendance for a single attendee', function () {
})->todo();

it('rejecting attendance sets attended to false', function () {
})->todo();

it('rejecting attendance dissociates the subscription', function () {
})->todo();

it('rejecting attendance recalculates subscription expiry', function () {
})->todo();

it('reject button is hidden when not attended', function () {
})->todo();

it('regular user cannot see the reject button', function () {
})->todo();

// --- Bulk attendance actions ---

it('admin can bulk confirm attendance', function () {
})->todo();

it('admin can bulk reject attendance', function () {
})->todo();

it('regular user cannot see bulk attendance actions', function () {
})->todo();

// --- Deleting attendees ---

it('admin can delete an attendee from a training', function () {
})->todo();

it('admin can bulk delete attendees', function () {
})->todo();

// --- Filtering attendees ---

it('admin can filter attendees by attended status', function () {
})->todo();

it('admin can filter attendees by extra status', function () {
})->todo();

it('admin can filter attendees by subscription usage', function () {
})->todo();

it('regular user does not see admin-only filters', function () {
})->todo();
