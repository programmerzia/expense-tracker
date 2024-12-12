<?php

namespace App\Providers;

use App\Extensions\StatusCheckUserProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class CustomAuthServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Auth::provider('status_check', function ($app, array $config) {
            return new StatusCheckUserProvider($app['hash'], $config['model']);
        });
    }
}
