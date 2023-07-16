<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\OrderCreated;
use App\Listeners\UpdateIngredientQuantities;
use App\Listeners\insertOrderProductImage;

use App\Events\OrderProductCanceled;
use App\Listeners\OnOrderProductCanceled;




class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        OrderCreated::class => [
            UpdateIngredientQuantities::class,

        ],


        OrderProductCanceled::class => [
            OnOrderProductCanceled::class

        ],
    ];




    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
