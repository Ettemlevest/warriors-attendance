<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('enforces cascading foreign keys on the attendance table', function () {
    $foreignKeys = collect(DB::select('PRAGMA foreign_key_list(trainings_attendance)'));

    expect($foreignKeys->firstWhere('from', 'training_id'))->toMatchObject(['table' => 'trainings', 'to' => 'id', 'on_delete' => 'CASCADE'])
        ->and($foreignKeys->firstWhere('from', 'user_id'))->toMatchObject(['table' => 'users', 'to' => 'id', 'on_delete' => 'CASCADE'])
        ->and($foreignKeys->firstWhere('from', 'subscription_id'))->toMatchObject(['table' => 'subscriptions', 'to' => 'id', 'on_delete' => 'RESTRICT']);
})->skip(fn () => DB::getDriverName() !== 'sqlite', 'reads the SQLite foreign key list');

it('keeps the restricting foreign keys on subscriptions', function () {
    $foreignKeys = collect(DB::select('PRAGMA foreign_key_list(subscriptions)'));

    expect($foreignKeys->firstWhere('from', 'user_id'))->toMatchObject(['table' => 'users', 'on_delete' => 'RESTRICT'])
        ->and($foreignKeys->firstWhere('from', 'plan_id'))->toMatchObject(['table' => 'plans', 'on_delete' => 'RESTRICT']);
})->skip(fn () => DB::getDriverName() !== 'sqlite', 'reads the SQLite foreign key list');

it('has no attendance backup table left', function () {
    expect(Schema::hasTable('trainings_attendance_backup'))->toBeFalse();
});
