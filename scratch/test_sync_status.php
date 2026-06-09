<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $sqliteSales = DB::table('sales')->count();
    $sqlitePendingSync = DB::table('sync_queue')->where('synced', 0)->count();
    $sqliteTotalSync = DB::table('sync_queue')->count();
} catch (\Exception $e) {
    $sqliteSales = 'Error: ' . $e->getMessage();
    $sqlitePendingSync = 'Error';
    $sqliteTotalSync = 'Error';
}

try {
    $mysqlSales = DB::connection('mysql')->table('sales')->count();
} catch (\Exception $e) {
    $mysqlSales = 'Error: ' . $e->getMessage();
}

try {
    $sqliteCustomers = DB::table('customers')->count();
    $mysqlCustomers = DB::connection('mysql')->table('customers')->count();
} catch (\Exception $e) {
    $sqliteCustomers = 'Error';
    $mysqlCustomers = 'Error';
}

echo "--- BILAN DE SYNCHRONISATION LOCAL ---\n";
echo "SQLite Sales: $sqliteSales\n";
echo "MySQL Sales: $mysqlSales\n";
echo "SQLite Customers: $sqliteCustomers\n";
echo "MySQL Customers: $mysqlCustomers\n";
echo "sync_queue en attente (synced = 0): $sqlitePendingSync\n";
echo "sync_queue total: $sqliteTotalSync\n";
