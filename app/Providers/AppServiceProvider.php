<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\IotDevice;
use App\Models\User;
use App\Observers\IotDeviceObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        IotDevice::observe(IotDeviceObserver::class);

        Gate::before(static function (User $user, string $ability): ?bool {
            return $user->hasRole('super_admin') ? true : null;
        });
    }
}
