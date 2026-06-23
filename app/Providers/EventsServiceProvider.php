<?php

namespace App\Providers;

use App\Interfaces\BaseInterface;
use App\Interfaces\Events\EventsInterface;
use App\Services\BaseService;
use App\Services\Events\EventsServices;
use Illuminate\Support\ServiceProvider;


class EventsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {
        $this->app->bind(BaseInterface::class,BaseService::class);
        $this->app->bind(EventsInterface::class, EventsServices::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void{
        //
    }
}
