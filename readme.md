# Warriors attendance

Attendance registration application for Kiskunlacháza Warriors based on [@reinink](https://twitter.com/reinink)'s [PingCRM](https://github.com/inertiajs/pingcrm) demo application.

![](https://raw.githubusercontent.com/Ettemlevest/warriors-attendance/master/screenshot.png)

## Installation

Clone the repo locally:

```sh
git clone https://github.com/ettemlevest/warriors-attendance.git warriors-attendance
cd warriors-attendance
```

Install PHP dependencies:

```sh
composer install
```

Install dependencies:

```sh
pnpm install
```

Build assets:

```sh
pnpm run dev
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```

Create an SQLite database. You can also use another database (MySQL, Postgres), simply update your configuration accordingly.

```sh
touch database/database.sqlite
```

Run database migrations:

```sh
php artisan migrate
```

Run database seeder:

```sh
php artisan db:seed
```

Start php development server
```sh
php artisan serve
```

You're ready to go! Open link in your browser, and login with:

- **Username:** johndoe@example.com
- **Password:** secret

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
