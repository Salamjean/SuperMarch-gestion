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

        $this->registerLocalSyncObservers();
    }

    /**
     * Enregistre les observateurs Eloquent en local pour alimenter la sync_queue
     */
    private function registerLocalSyncObservers(): void
    {
        if (config('app.env') !== 'local') {
            return;
        }

        $models = [
            \App\Models\Product::class => 'product',
            \App\Models\Customer::class => 'customer',
            \App\Models\Sale::class => 'sale',
            \App\Models\SaleItem::class => 'sale_item',
            \App\Models\DebtPayment::class => 'debt_payment',
            \App\Models\CashSession::class => 'cash_session',
            \App\Models\Category::class => 'category',
            \App\Models\Supplier::class => 'supplier',
            \App\Models\User::class => 'user',
            \App\Models\RestockRequest::class => 'restock_request',
        ];

        foreach ($models as $modelClass => $entityType) {
            if (!class_exists($modelClass)) continue;

            $modelClass::saving(function ($model) {
                if (request()->header('X-Sync-Key') || config('app.is_syncing_pull')) {
                    $model->synced = 1;
                    return;
                }

                // Essai de connectivité MySQL pour mettre à jour en doublon direct
                try {
                    $mysqlConn = \Illuminate\Support\Facades\DB::connection('mysql');
                    $mysqlConn->getPdo();
                    $model->synced = 1;
                } catch (\Exception $e) {
                    $model->synced = 0;
                }
            });

            $modelClass::saved(function ($model) {
                if (request()->header('X-Sync-Key') || config('app.is_syncing_pull')) {
                    return;
                }

                if ($model->synced == 1) {
                    try {
                        $mysqlConn = \Illuminate\Support\Facades\DB::connection('mysql');
                        $tableName = $model->getTable();
                        $attributes = $model->getAttributes();
                        $attributes['synced'] = 1;
                        unset($attributes['items']); // nettoiement au cas où

                        $mysqlConn->table($tableName)->updateOrInsert(['id' => $model->id], $attributes);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\DB::table($model->getTable())
                            ->where('id', $model->id)
                            ->update(['synced' => 0]);
                    }
                }
            });

            $modelClass::deleted(function ($model) {
                if (request()->header('X-Sync-Key') || config('app.is_syncing_pull')) {
                    return;
                }

                try {
                    $mysqlConn = \Illuminate\Support\Facades\DB::connection('mysql');
                    $mysqlConn->getPdo();
                    $mysqlConn->table($model->getTable())->where('id', $model->id)->delete();
                } catch (\Exception $e) {
                    // Suppression en attente (on le laisse ainsi pour SQLite)
                }
            });
        }
    }
}
