# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Warriors Attendance is a Laravel 10 application for managing attendance registration for Kiskunlacháza Warriors sports organization. The admin interface is built with Filament v3.

## Common Commands

```bash
# Development
npm run dev                    # Start Vite dev server
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
- **Frontend Build**: Vite

### Domain Model

The core entities and their relationships:

- **User** - Athletes/members with profile data (soft deletes enabled)
  - `owner` boolean determines admin access via `isAdmin()`
  - Has many: `subscriptions`, `trainingAttendances`

- **Training** - Workout sessions with type (easy/running/hard/other), place, time, max attendees
  - Has many: `attendees` (through TrainingAttendance)

- **TrainingAttendance** - Join table linking users to trainings, tracks which subscription was used

- **Subscription** - Links a user to a Plan, tracks purchase/expiry dates
  - Auto-expires when session limit reached (`refreshExpiry()`, `usedUp()`)
  - Has many: `usages` (TrainingAttendance records)

- **Plan** - Subscription templates defining session count and pricing

- **Message** - Broadcast announcements to users

### Filament Resources

All admin CRUD is managed through Filament Resources in `app/Filament/Resources/`:
- `UserResource`, `TrainingResource`, `SubscriptionResource`, `PlanResource`, `MessageResource`

Dashboard widgets: `ActiveMessages`, `AccountWidget`, `UpcomingTraining`

### Authorization

Policy classes in `app/Policies/` control access. The `User::isAdmin()` method (checks `owner` column) is the primary admin check.

## Code Style

- Laravel Pint with default Laravel preset
- PHPStan level 5 via Larastan
- Custom rule: `nullable_type_declaration_for_default_null_value: false`

## Test Credentials

After running `php artisan db:seed`:
- Email: johndoe@example.com
- Password: secret
