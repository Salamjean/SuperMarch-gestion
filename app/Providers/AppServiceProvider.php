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

        view()->composer('*', function ($view) {
            if (Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::firstOr(function () {
                    return \App\Models\Setting::create([
                        'store_name' => 'SUPERMARCHÉ PRO',
                        'phone' => '+225 07 00 00 00 00',
                        'address' => 'Abidjan, Cocody Riviera Palmeraie',
                        'email' => 'contact@supermarchepro.com',
                        'invoice_footer' => 'Merci pour votre confiance ! Les marchandises vendues ne sont ni reprises ni échangées, sauf accord de la direction.',
                        'invoice_format' => 'ticket',
                    ]);
                });
            } else {
                $settings = new \stdClass();
                $settings->store_name = 'SUPERMARCHÉ PRO';
                $settings->phone = '+225 07 00 00 00 00';
                $settings->address = 'Abidjan, Cocody Riviera Palmeraie';
                $settings->email = 'contact@supermarchepro.com';
                $settings->invoice_footer = 'Merci pour votre confiance ! Les marchandises vendues ne sont ni reprises ni échangées, sauf accord de la direction.';
                $settings->invoice_format = 'ticket';
            }
            $view->with('storeSettings', $settings);
        });

        view()->composer('admin.layouts.navbar', function ($view) {
            $restockRequests = \App\Models\RestockRequest::where('status', 'pending')
                ->with(['product', 'user'])
                ->latest()
                ->get();
            $view->with('restockRequests', $restockRequests);
        });

        view()->composer(['admin.layouts.sidebar', 'magasinier.layouts.sidebar'], function ($view) {
            $productsAtThresholdCount = \App\Models\Product::where(function ($query) {
                $query->whereColumn('stock', '<=', 'stock_threshold')
                      ->orWhereHas('restockRequests', function ($q) {
                          $q->where('status', 'pending');
                      });
            })->count();
            $view->with('productsAtThresholdCount', $productsAtThresholdCount);
        });
    }
}
