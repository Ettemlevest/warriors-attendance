<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Training;
use App\Models\User;
use App\Policies\AdminCheckingPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\TrainingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Message::class => AdminCheckingPolicy::class,
        Plan::class => AdminCheckingPolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        Training::class => TrainingPolicy::class,
        User::class => AdminCheckingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
