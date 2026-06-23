<?php

namespace App\Providers;

use App\Interfaces\BaseInterface;
use App\Interfaces\Content\ContentInterface;
use App\Services\BaseService;
use App\Services\Content\ContentServices;
use Illuminate\Support\ServiceProvider;


class ContentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {
        $this->app->bind(BaseInterface::class,BaseService::class);
        $this->app->bind(ContentInterface::class, ContentServices::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void{
        //
    }
}
