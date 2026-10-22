# Warriors attendance

Attendance and subscription management for the Kiskunlacháza Warriors. Members register for trainings from a dashboard, admins confirm who showed up, and confirmed sessions are counted against the member's subscription. The whole application is a [Filament](https://filamentphp.com) v4 panel on Laravel 12, with a Hungarian UI. It started out from [@reinink](https://twitter.com/reinink)'s [PingCRM](https://github.com/inertiajs/pingcrm) demo.

## Requirements

- PHP 8.2 or newer with the usual Laravel extensions and GD
- Composer
- SQLite or MySQL

## Installation

Clone the repository:

```sh
git clone https://github.com/ettemlevest/warriors-attendance.git warriors-attendance
cd warriors-attendance
```

Install PHP dependencies:

```sh
composer install
```

Create the configuration and the application key:

```sh
cp .env.example .env
php artisan key:generate
```

`.env.example` points at a MySQL database named `filament`. For SQLite instead, set `DB_CONNECTION=sqlite`, remove the other `DB_*` lines and create the file:

```sh
touch database/database.sqlite
```

Run the migrations:

```sh
php artisan migrate
```

Serve the application with [Laravel Herd](https://herd.laravel.com) or the built-in server:

```sh
php artisan serve
```

### First admin

There is no seeder. Register through the panel's registration page, then promote that account. The `owner` flag is not editable from the UI:

```sh
php artisan tinker --execute="App\Models\User::first()->forceFill(['owner' => true])->save();"
```

Admins see every resource; other members see the dashboard, the trainings and their own subscriptions.

## Development

```sh
php artisan test --compact      # Pest suite on an in-memory SQLite database
./vendor/bin/pint --dirty       # code style
./vendor/bin/phpstan            # static analysis
```

Hungarian translations are maintained by hand under `lang/`; a coverage test fails when Laravel or Filament ships a string that has no Hungarian value.

## Upgrading an existing installation

- Run `php artisan attendance:prune-orphans --dry-run` before migrating. The foreign-key migration removes attendance rows whose training or user no longer exists and prints what it would delete.
- The migration that makes passwords required refuses to run while any user has no password; reset those accounts first.
- Set `MAIL_FROM_ADDRESS` in `.env`, otherwise password reset mails cannot be sent.

## License

The MIT License (MIT). See the [LICENSE](LICENSE) file.
