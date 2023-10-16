<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Services\ZohoApi;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
       // $this->app->singleton(ZohoApi::class, ZohoApi::class);
    }
    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
