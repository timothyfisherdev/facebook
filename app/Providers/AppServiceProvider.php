<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Passport::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Collection::macro('fillTimestamps', function () {
            return $this->map(function ($data) {
                $data['created_at'] = $data['updated_at'] = now();
                return $data;
            });
        });
    }
}
