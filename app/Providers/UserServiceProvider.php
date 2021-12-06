<?php

namespace App\Providers;

use App\Jobs\User;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('user',function(){
            return new User();
        });
    }

    public function provides()
    {
        return ['App\Jobs\User'];
    }
}
