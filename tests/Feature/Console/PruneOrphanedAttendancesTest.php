<?php

use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * @return array<string, int>
 */
function insertOrphanedAttendances(): array
{
    match (DB::getDriverName()) {
        'sqlite' => DB::statement('PRAGMA defer_foreign_keys = ON'),
        'mysql' => DB::statement('SET FOREIGN_KEY_CHECKS = 0'),
        default => null,
    };

    $training = Training::factory()->create();
    $user = User::factory()->create();
    $subscription = Subscription::factory()->for($user)->create();
    $timestamps = ['created_at' => now(), 'updated_at' => now()];

    return [
        'without_training' => DB::table('trainings_attendance')->insertGetId(['training_id' => 999999, 'user_id' => $user->id, ...$timestamps]),
        'without_user' => DB::table('trainings_attendance')->insertGetId(['training_id' => $training->id, 'user_id' => 999999, ...$timestamps]),
        'missing_subscription' => DB::table('trainings_attendance')->insertGetId(['training_id' => $training->id, 'user_id' => $user->id, 'subscription_id' => 999999, ...$timestamps]),
        'healthy' => TrainingAttendance::factory()->for($training)->for(User::factory()->create())->create(['subscription_id' => $subscription->id])->id,
    ];
}

it('reports orphaned rows in a dry run without changing anything', function () {
    $rows = insertOrphanedAttendances();

    $this->artisan('attendance:prune-orphans', ['--dry-run' => true])
        ->expectsOutputToContain('1 attendance rows without a training would be handled.')
        ->expectsOutputToContain('1 attendance rows without a user would be handled.')
        ->expectsOutputToContain('1 attendance rows pointing to a missing subscription would be handled.')
        ->expectsOutputToContain('Dry run, nothing was changed.')
        ->assertSuccessful();

    foreach ($rows as $id) {
        $this->assertDatabaseHas('trainings_attendance', ['id' => $id]);
    }

    $this->assertDatabaseHas('trainings_attendance', ['id' => $rows['missing_subscription'], 'subscription_id' => 999999]);
});

it('removes orphaned rows and unlinks missing subscriptions', function () {
    $rows = insertOrphanedAttendances();

    $this->artisan('attendance:prune-orphans')
        ->expectsOutputToContain('1 attendance rows without a training handled.')
        ->expectsOutputToContain('Done.')
        ->assertSuccessful();

    $this->assertDatabaseMissing('trainings_attendance', ['id' => $rows['without_training']]);
    $this->assertDatabaseMissing('trainings_attendance', ['id' => $rows['without_user']]);
    $this->assertDatabaseHas('trainings_attendance', ['id' => $rows['missing_subscription'], 'subscription_id' => null]);

    $healthy = TrainingAttendance::query()->findOrFail($rows['healthy']);

    expect($healthy->subscription_id)->not->toBeNull();
});

it('reports zero rows on a clean database', function () {
    $this->artisan('attendance:prune-orphans')
        ->expectsOutputToContain('0 attendance rows without a training handled.')
        ->expectsOutputToContain('0 attendance rows without a user handled.')
        ->expectsOutputToContain('0 attendance rows pointing to a missing subscription handled.')
        ->assertSuccessful();
});
