<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['users', 'categories', 'suppliers', 'products', 'customers', 'sales', 'sale_items', 'cash_sessions', 'debt_payments', 'restock_requests', 'settings'];

echo "--- COMPARAISON DETAILLEE DES TABLES ---\n";
echo str_pad("Table", 20) . " | " . str_pad("SQLite (Local)", 15) . " | " . str_pad("MySQL (Serveur)", 15) . "\n";
echo str_repeat("-", 58) . "\n";

foreach ($tables as $table) {
    try {
        $sqliteCount = DB::table($table)->count();
    } catch (\Exception $e) {
        $sqliteCount = "Erreur";
    }

    try {
        $mysqlCount = DB::connection('mysql')->table($table)->count();
    } catch (\Exception $e) {
        $mysqlCount = "Erreur";
    }

    echo str_pad($table, 20) . " | " . str_pad($sqliteCount, 15) . " | " . str_pad($mysqlCount, 15) . "\n";
}
