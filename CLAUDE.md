# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Warriors Attendance is a Laravel 10 application for managing attendance registration for Kiskunlacháza Warriors sports organization. The admin interface is built with Filament v3. The entire UI is in Hungarian.

## Common Commands

```bash
# Development
pnpm run dev                   # Start Vite dev server
php artisan serve              # Start PHP dev server

# Testing
./vendor/bin/phpunit           # Run all tests
./vendor/bin/phpunit tests/Feature/ExampleTest.php  # Run single test file
./vendor/bin/phpunit --filter testMethodName        # Run single test method

# Code Quality
./vendor/bin/pint              # Format code (Laravel preset)
./vendor/bin/phpstan           # Static analysis (level 5)

# Database
php artisan migrate            # Run migrations
php artisan db:seed            # Seed database
```

## Architecture

### Tech Stack
- **Backend**: Laravel 10, PHP 8.1+
- **Admin UI**: Filament v3 (Livewire-based)
- **Auth**: Laravel Sanctum (session + API tokens)
- **Frontend Build**: Vite with pnpm (not npm)

### Filament-Only Architecture

There are no traditional Laravel controllers or web routes. All UI is handled by Filament's auto-discovered resources, pages, and widgets. The only defined route is `GET /api/user` (Sanctum-protected). All users can access the Filament panel (`canAccessPanel()` returns true), but policies restrict what non-admins can do.

### Domain Model

The core entities and their relationships:

- **User** - Athletes/members with profile data (soft deletes enabled)
  - `owner` boolean determines admin access via `isAdmin()`
  - Has many: `subscriptions`, `trainingAttendances`

- **Training** - Workout sessions with type (easy/running/hard/other), place, time, max attendees
  - Types map to Hungarian labels, colors, and icons (defined in TrainingResource)
  - Has many: `attendees` (through TrainingAttendance)

- **TrainingAttendance** - Join table (with its own ID) linking users to trainings
  - Tracks `attended` (confirmed by admin), `extra` (over-capacity), `subscription_id`, `comment`
  - Always eager-loads `training`, `user`, `subscription`

- **Subscription** - Links a user to a Plan, tracks purchase/expiry dates
  - Auto-expires when session limit reached (`refreshExpiry()`, `usedUp()`)
  - Has many: `usages` (TrainingAttendance records)

- **Plan** - Subscription templates defining session count (soft deletes enabled)

- **Message** - Broadcast announcements with active date range (`showed_from`/`showed_to`)

### Attendance Workflow

This is the core business process:

1. **Registration**: User signs up for a training → `TrainingAttendance` created with `attended=false`, `subscription_id=null`
2. **Confirmation**: Admin confirms attendance → `confirmAttendance()` sets `attended=true`, auto-associates the user's live subscription, and refreshes subscription expiry
3. **Rejection**: Admin marks not attended → `rejectAttendance()` sets `attended=false`, dissociates subscription, recalculates expiry

Subscription auto-expiry: `refreshExpiry()` checks if `usages_count >= plan->sessions` and sets `expired_at` accordingly. This is called on every confirm/reject action.

### Authorization

Policy classes in `app/Policies/` control access:
- `AdminCheckingPolicy` - base policy, requires `isAdmin()` for all actions
- `TrainingPolicy` - overrides `viewAny`/`view` to allow all users
- `SubscriptionPolicy` - overrides `viewAny`/`view` to allow all users; non-admins see only their own subscriptions (filtered in `SubscriptionResource::getEloquentQuery()`)

Policy mappings: Message and Plan use `AdminCheckingPolicy`, User uses `AdminCheckingPolicy`, Training and Subscription use their specific policies.

### Dashboard Widgets

- `ActiveMessages` (sort 10) - displays current broadcast messages
- `UpcomingTraining` (sort 20) - trainings in next 30 days with register/cancel actions
- `SubscriptionOverview` - user's active subscription stats and 6-month attendance chart
- `AccountWidget` - Filament's built-in account widget

### Custom Pages

- `EditProfile` (`app/Filament/Pages/EditProfile.php`) - extends Filament's profile page with safety contact and competition registration fields

## Code Style

- Laravel Pint with default Laravel preset
- PHPStan level 5 via Larastan
- Custom Pint rule: `nullable_type_declaration_for_default_null_value: false`

## Test Credentials

After running `php artisan db:seed`:
- Email: johndoe@example.com
- Password: secret
