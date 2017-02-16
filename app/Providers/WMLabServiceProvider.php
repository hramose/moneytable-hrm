<?php

namespace App\Providers;

use Illuminate\Validation\Validator;
use Illuminate\Support\ServiceProvider;

class WMLabServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //  \Validator::resolver(function($translator, $data, $rules, $messages)
        // {
        //     return new Validator($translator, $data, $rules, $messages);
        // });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
