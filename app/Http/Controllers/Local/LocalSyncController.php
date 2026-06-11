<?php

namespace App\Http\Controllers\Local;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\CashSession;
use App\Models\DebtPayment;
use App\Models\Setting;
use App\Models\RestockRequest;

class LocalSyncController extends Controller
{
    /**
     * PUSH : Envoie les opérations hors-ligne de SQLite vers MySQL (production)
     * Ordre strict pour respecter les clés étrangères
     */
    public function pushPending()
    {
        try {
            // Ordre strict pour respecter les clés étrangères
            $tablesOrder = [
                'users', 'categories', 'suppliers', 'products', 'customers',
                'cash_sessions', 'sales', 'sale_items', 'debt_payments', 'restock_requests'
            ];

            $payload    = [];
            $totalCount = 0;

            foreach ($tablesOrder as $table) {
                $nonSyncedRecords = DB::table($table)->where('synced', 0)->get()->map(fn($item) => (array)$item)->toArray();
                if (!empty($nonSyncedRecords)) {
                    $payload[$table] = $nonSyncedRecords;
                    $totalCount     += count($nonSyncedRecords);
                }
            }

            if ($totalCount === 0) {
                return response()->json([
                    'success'      => true,
                    'message'      => "0 opération(s) synchronisée(s) avec succès.",
                    'synced_count' => 0,
                    'errors_count' => 0,
                ]);
            }

            // Désactiver les FK MySQL EN DEHORS de la transaction pour être effectif
            DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=0;');
            try {
                DB::connection('mysql')->transaction(function () use ($payload, $tablesOrder) {
                    foreach ($tablesOrder as $table) {
                        if (!isset($payload[$table])) continue;
                        foreach ($payload[$table] as $record) {
                            $attributes = $record;
                            unset($attributes['synced']); // La colonne synced n'existe pas dans MySQL
                            DB::connection('mysql')->table($table)->updateOrInsert(['id' => $record['id']], $attributes);
                        }
                    }
                });
            } finally {
                DB::connection('mysql')->statement('SET FOREIGN_KEY_CHECKS=1;');
            }

            // Marquer tout comme synchronisé dans SQLite
            foreach ($tablesOrder as $table) {
                DB::table($table)->where('synced', 0)->update(['synced' => 1]);
            }

            return response()->json([
                'success'      => true,
                'message'      => "{$totalCount} opération(s) synchronisée(s) avec succès vers MySQL.",
                'synced_count' => $totalCount,
                'errors_count' => 0,
            ]);

        } catch (\Exception $e) {
            Log::error('LocalSyncController::pushPending error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de synchronisation PUSH : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PULL : Télécharge toutes les données MySQL vers la base SQLite locale
     * Utilise DB::table() direct + PRAGMA foreign_keys=OFF pour éviter les violations FK SQLite
     */
    public function pullUpdates()
    {
        try {
            Log::info('LocalSyncController::pullUpdates — Début du Pull via BD MySQL');

            // Récupérer toutes les données depuis MySQL dans le bon ordre (parents avant enfants)
            $users        = DB::connection('mysql')->table('users')->get()->map(fn($i) => (array)$i)->toArray();
            $categories   = DB::connection('mysql')->table('categories')->get()->map(fn($i) => (array)$i)->toArray();
            $suppliers    = DB::connection('mysql')->table('suppliers')->get()->map(fn($i) => (array)$i)->toArray();
            $products     = DB::connection('mysql')->table('products')->get()->map(fn($i) => (array)$i)->toArray();
            $customers    = DB::connection('mysql')->table('customers')->get()->map(fn($i) => (array)$i)->toArray();
            $settings     = DB::connection('mysql')->table('settings')->first();
            $cashSessions = DB::connection('mysql')->table('cash_sessions')->get()->map(fn($i) => (array)$i)->toArray();
            $sales        = DB::connection('mysql')->table('sales')->get()->map(fn($i) => (array)$i)->toArray();
            $saleItems    = DB::connection('mysql')->table('sale_items')->get()->map(fn($i) => (array)$i)->toArray();
            $debtPayments = DB::connection('mysql')->table('debt_payments')->get()->map(fn($i) => (array)$i)->toArray();
            $restockReqs  = DB::connection('mysql')->table('restock_requests')->get()->map(fn($i) => (array)$i)->toArray();

            // Désactiver les clés étrangères SQLite AVANT la transaction
            DB::connection('sqlite')->statement('PRAGMA foreign_keys = OFF;');
            config(['app.is_syncing_pull' => true]);

            try {
                DB::connection('sqlite')->transaction(function () use (
                    $users, $categories, $suppliers, $products, $customers,
                    $settings, $cashSessions, $sales, $saleItems, $debtPayments, $restockReqs
                ) {
                    // 1. Users
                    foreach ($users as $u) {
                        $attrs = $u;
                        $attrs['synced'] = 1;
                        DB::table('users')->updateOrInsert(['id' => $u['id']], $attrs);
                    }

                    // 2. Categories
                    foreach ($categories as $c) {
                        $attrs = $c;
                        $attrs['synced'] = 1;
                        DB::table('categories')->updateOrInsert(['id' => $c['id']], $attrs);
                    }

                    // 3. Suppliers
                    foreach ($suppliers as $s) {
                        $attrs = $s;
                        $attrs['synced'] = 1;
                        DB::table('suppliers')->updateOrInsert(['id' => $s['id']], $attrs);
                    }

                    // 4. Products
                    foreach ($products as $p) {
                        $attrs = $p;
                        $attrs['synced'] = 1;
                        DB::table('products')->updateOrInsert(['id' => $p['id']], $attrs);
                    }

                    // 5. Customers
                    foreach ($customers as $c) {
                        $attrs = $c;
                        $attrs['synced'] = 1;
                        DB::table('customers')->updateOrInsert(['id' => $c['id']], $attrs);
                    }

                    // 6. Settings
                    if ($settings) {
                        $sArr = (array) $settings;
                        DB::table('settings')->updateOrInsert(['id' => $sArr['id']], $sArr);
                    }

                    // 7. Cash Sessions (parents des sales)
                    foreach ($cashSessions as $cs) {
                        $attrs = $cs;
                        $attrs['synced'] = 1;
                        DB::table('cash_sessions')->updateOrInsert(['id' => $cs['id']], $attrs);
                    }

                    // 8. Sales (dépend de cash_sessions + users + customers)
                    foreach ($sales as $sale) {
                        $attrs = $sale;
                        $attrs['synced'] = 1;
                        DB::table('sales')->updateOrInsert(['id' => $sale['id']], $attrs);
                    }

                    // 9. Sale Items (dépend de sales + products)
                    foreach ($saleItems as $item) {
                        $attrs = $item;
                        $attrs['synced'] = 1;
                        DB::table('sale_items')->updateOrInsert(['id' => $item['id']], $attrs);
                    }

                    // 10. Debt Payments (dépend de customers + users + cash_sessions)
                    foreach ($debtPayments as $dp) {
                        $attrs = $dp;
                        $attrs['synced'] = 1;
                        DB::table('debt_payments')->updateOrInsert(['id' => $dp['id']], $attrs);
                    }

                    // 11. Restock Requests (dépend de products + users)
                    foreach ($restockReqs as $r) {
                        $attrs = $r;
                        $attrs['synced'] = 1;
                        DB::table('restock_requests')->updateOrInsert(['id' => $r['id']], $attrs);
                    }
                });
            } finally {
                // Toujours réactiver les FK SQLite même en cas d'erreur
                DB::connection('sqlite')->statement('PRAGMA foreign_keys = ON;');
                config(['app.is_syncing_pull' => false]);
            }

            $counts = [
                'users'     => count($users),
                'products'  => count($products),
                'sales'     => count($sales),
                'customers' => count($customers),
            ];

            Log::info('LocalSyncController::pullUpdates — Pull terminé avec succès', $counts);

            return response()->json([
                'success' => true,
                'message' => 'Base SQLite locale mise à jour avec succès depuis le serveur en ligne.',
                'counts'  => $counts,
            ]);

        } catch (\Exception $e) {
            DB::connection('sqlite')->statement('PRAGMA foreign_keys = ON;');
            config(['app.is_syncing_pull' => false]);
            Log::error('LocalSyncController::pullUpdates error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de synchronisation PULL : ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Retourne le nombre d'opérations locales en attente de synchronisation
     */
    public function getPendingCount()
    {
        try {
            $tables = [
                'users', 'categories', 'suppliers', 'products', 'customers',
                'cash_sessions', 'sales', 'sale_items', 'debt_payments', 'restock_requests'
            ];
            $count = 0;
            foreach ($tables as $t) {
                if (Schema::hasTable($t)) {
                    $count += DB::table($t)->where('synced', 0)->count();
                }
            }
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Vérifie la connexion à la base MySQL de production
     */
    public function checkMysqlConnection()
    {
        try {
            DB::connection('mysql')->getPdo();
            return response()->json([
                'success' => true,
                'message' => 'Connexion au serveur MySQL établie avec succès.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Serveur MySQL déconnecté : ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Marque l'entité locale correspondante comme synchronisée
     */
    private function markLocalEntityAsSynced(string $entityType, int $id): void
    {
        try {
            $table = match ($entityType) {
                'sale'            => 'sales',
                'cash_session'    => 'cash_sessions',
                'debt_payment'    => 'debt_payments',
                'restock_request' => 'restock_requests',
                default           => null,
            };

            if ($table) {
                DB::table($table)->where('id', $id)->update(['synced' => 1]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to mark local entity as synced [{$entityType} ID={$id}]: " . $e->getMessage());
        }
    }
}
