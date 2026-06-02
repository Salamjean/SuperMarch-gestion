<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

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
        Schema::defaultStringLength(191);

        Paginator::defaultView('vendor.pagination.custom');

        view()->composer('admin.layouts.navbar', function ($view) {
            $restockRequests = \App\Models\RestockRequest::where('status', 'pending')
                ->with(['product', 'user'])
                ->latest()
                ->get();
            $view->with('restockRequests', $restockRequests);
        });
    }
}
