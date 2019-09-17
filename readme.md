# Warriors attendance

Attendance registration application for Kiskunlacháza Warriors based on [@reinink](https://twitter.com/reinink)'s [PingCRM](https://github.com/inertiajs/pingcrm) example application.

![](https://raw.githubusercontent.com/Ettemlevest/warriors-attendance/master/warriors-example-screenshot.png)

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

Install NPM dependencies:

```sh
npm install
```

Build assets:

```sh
npm run dev
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

You're ready to go! Visit Ping CRM in your browser, and login with:

- **Username:** johndoe@example.com
- **Password:** secret

## Running tests

To run the tests, run:

```sh
phpunit
```

## TODOs

* write tests
* docblock comments
* use API resources
