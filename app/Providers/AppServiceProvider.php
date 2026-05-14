<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Produk;
use Illuminate\Support\Facades\View;

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
         if (env('APP_ENV') !== 'local') {
        URL::forceScheme('https');
    }
     View::composer('*', function ($view) {

        $newestproduct = Produk::latest()->take(2)->get();

        $view->with('newestproduct', $newestproduct);
    });
    }
}
