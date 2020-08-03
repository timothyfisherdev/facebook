<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        Validator::extend('valid_user_relationship', function ($attribute, $value, $parameters, $validator) {
            return \App\UserRelationship::where('requester_id', auth()->id())
                ->where('requested_id', $validator->getData()['data']['attributes']['related_user_id'])->get()->isEmpty();
        });
    }
}
