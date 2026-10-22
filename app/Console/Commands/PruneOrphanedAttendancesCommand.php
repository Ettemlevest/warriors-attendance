<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\Training;
use App\Models\TrainingAttendance;
use App\Models\User;
use Closure;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class PruneOrphanedAttendancesCommand extends Command
{
    protected $signature = 'attendance:prune-orphans {--dry-run : Report the orphaned rows without changing anything}';

    protected $description = 'Remove attendance rows whose training or user no longer exists and unlink subscriptions that are gone';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->handleOrphans(
            'attendance rows without a training',
            TrainingAttendance::query()->whereNotIn('training_id', Training::query()->select('id')),
            $dryRun,
            fn (Builder $query) => $query->delete(),
        );

        $this->handleOrphans(
            'attendance rows without a user',
            TrainingAttendance::query()->whereNotIn('user_id', User::withTrashed()->select('id')),
            $dryRun,
            fn (Builder $query) => $query->delete(),
        );

        $this->handleOrphans(
            'attendance rows pointing to a missing subscription',
            TrainingAttendance::query()
                ->whereNotNull('subscription_id')
                ->whereNotIn('subscription_id', Subscription::query()->select('id')),
            $dryRun,
            fn (Builder $query) => $query->update(['subscription_id' => null]),
        );

        $this->comment($dryRun ? 'Dry run, nothing was changed.' : 'Done.');

        return self::SUCCESS;
    }

    /**
     * @param  Builder<TrainingAttendance>  $query
     * @param  Closure(Builder<TrainingAttendance>): mixed  $apply
     */
    private function handleOrphans(string $label, Builder $query, bool $dryRun, Closure $apply): void
    {
        $count = $query->count();

        $this->info("{$count} {$label} ".($dryRun ? 'would be handled.' : 'handled.'));

        if ($dryRun) {
            return;
        }

        if ($count === 0) {
            return;
        }

        $apply($query);
    }
}
