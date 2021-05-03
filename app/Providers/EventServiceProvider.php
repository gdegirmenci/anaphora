<?php

namespace App\Providers;

use App\Events\Campaign\CampaignStatusUpdated;
use App\Events\CircuitBreaker\CircuitBreakerStatusUpdated;
use App\Listeners\Campaign\UpdateCampaignStatus;
use App\Listeners\CircuitBreaker\UpdateCircuitBreakerStatus;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CampaignStatusUpdated::class => [
            UpdateCampaignStatus::class,
        ],
        CircuitBreakerStatusUpdated::class => [
            UpdateCircuitBreakerStatus::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
