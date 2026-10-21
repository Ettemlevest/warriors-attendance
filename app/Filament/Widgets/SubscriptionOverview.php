<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SubscriptionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();

        $subscription = Subscription::findLiveSubscriptionForUser($user->id);

        $registrationsPerMonth = $this->registrationsPerMonth($user);

        return [
            $subscription === null
                ? Stat::make('Nincs aktív bérlet', '-')
                : Stat::make("{$subscription->plan->name} bérlet", "{$subscription->usages_count} felhasználva")
                    ->description("{$subscription->purchased_at->diffForHumans()} vásárolva"),

            Stat::make('Elmúlt 6 hónapban', $registrationsPerMonth->sum())
                ->description('edzés részvétel')
                ->descriptionColor('gray')
                ->color('success')
                ->chart($registrationsPerMonth->values()->all()),
        ];
    }

    /**
     * @return Collection<string, int>
     */
    protected function registrationsPerMonth(User $user): Collection
    {
        $firstMonth = now()->startOfMonth()->subMonths(5);

        $months = collect(range(0, 5))
            ->mapWithKeys(fn (int $offset) => [$firstMonth->copy()->addMonths($offset)->format('Y-m') => 0]);

        $registrations = $user->trainingAttendances()
            ->where('created_at', '>=', $firstMonth)
            ->pluck('created_at')
            ->countBy(fn (Carbon $createdAt) => $createdAt->format('Y-m'));

        return $months->merge($registrations->only($months->keys()));
    }
}
