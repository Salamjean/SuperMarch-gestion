<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'users',
            'categories',
            'suppliers',
            'products',
            'customers',
            'cash_sessions',
            'sales',
            'sale_items',
            'debt_payments',
            'restock_requests'
        ];

        foreach ($tables as $t) {
            if (Schema::hasTable($t)) {
                Schema::table($t, function (Blueprint $table) use ($t) {
                    if (!Schema::hasColumn($t, 'synced')) {
                        $table->boolean('synced')->default(1)->after('created_at'); // Default to 1 (synced) so existing data is not synced again unless wanted
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'categories',
            'suppliers',
            'products',
            'customers',
            'cash_sessions',
            'sales',
            'sale_items',
            'debt_payments',
            'restock_requests'
        ];

        foreach ($tables as $t) {
            if (Schema::hasTable($t)) {
                Schema::table($t, function (Blueprint $table) use ($t) {
                    if (Schema::hasColumn($t, 'synced')) {
                        $table->dropColumn('synced');
                    }
                });
            }
        }
    }
};
