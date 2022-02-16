<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();
    }
}


// namespace App\Providers;
// use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\ServiceProvider;
// use Illuminate\Pagination\Paginator;
// class AppServiceProvider extends ServiceProvider {
	
// 	public function boot() {
// 		Schema::defaultStringLength(191);Paginator::useBootstrap();
// 	}

// 	public function register() {
// 		//
// 	}
// }

