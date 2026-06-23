<?php

namespace App\Providers;

use App\Interfaces\BaseInterface;
use App\Interfaces\Permissions\PermissionsInterface;
use App\Interfaces\Permissions\RolePermissionsInterface;
use App\Services\BaseService;
use App\Services\Permissions\PermissionsService;
use App\Services\Permissions\RolePermissionsService;
use Illuminate\Support\ServiceProvider;


class PermissionsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void {
        $this->app->bind(BaseInterface::class,BaseService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void{
        //
    }
}
