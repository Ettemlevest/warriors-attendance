<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use App\Models\TrainingAttendance;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SubscriptionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        /** @var User $user */
        $user = auth()->user();

        /** @var ?Subscription $subscription */
        $subscription = $user->subscriptions()
            ->whereNull('expired_at')
            ->latest()
            ->first();

        $startingDate = now()->subMonths(6);
        $now = now();

        $possibleDates = [
            now()->subMonths(5)->format('Y-m') => 0,
            now()->subMonths(4)->format('Y-m') => 0,
            now()->subMonths(3)->format('Y-m') => 0,
            now()->subMonths(2)->format('Y-m') => 0,
            now()->subMonths(1)->format('Y-m') => 0,
            $now->format('Y-m') => 0,
        ];

        $results = DB::table((new TrainingAttendance)->getTable())
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as date_result, COUNT(DISTINCT id) as aggregate'))
            ->where('user_id', '=', $user->id)
            ->whereBetween('created_at', [$startingDate, $now])
            ->groupBy(DB::raw('DATE_FORMAT(created_at, "%Y-%m")'))
            ->orderBy('date_result')
            ->get();

        return [
            ...($subscription === null ? [
                Stat::make('Nincs aktív bérlet', '-'),
            ] : [
                Stat::make("{$subscription->plan->name} bérlet", "$subscription->usages_count felhasználva")
                    ->description("{$subscription->purchased_at->diffForHumans()} vásárolva"),
            ]
            ),

            Stat::make('Elmúlt 6 hónapban', $results->sum('aggregate'))
                ->description('edzés részvétel')
                ->descriptionColor('gray')
                ->color('success')
                ->chart(array_merge($possibleDates, $results->pluck('aggregate', 'date_result')->toArray())),
        ];
    }
}
