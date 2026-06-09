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
     * PUSH : Envoie les opérations hors-ligne de SQLite vers la base de données MySQL locale
     */
    public function pushPending()
    {
        try {
            // Tenter de se connecter à la base MySQL de destination
            try {
                DB::connection('mysql')->getPdo();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de se connecter à la base MySQL locale : ' . $e->getMessage()
                ], 500);
            }

            // Tables d'entités qui bénéficient de la colonne synced
            $tablesMapping = [
                'users' => User::class,
                'categories' => Category::class,
                'suppliers' => Supplier::class,
                'products' => Product::class,
                'customers' => Customer::class,
                'cash_sessions' => CashSession::class,
                'sales' => Sale::class,
                'sale_items' => SaleItem::class,
                'debt_payments' => DebtPayment::class,
                'restock_requests' => RestockRequest::class
            ];

            $syncedCount = 0;
            $errorsCount = 0;

            foreach ($tablesMapping as $table => $modelClass) {
                // Trouver toutes les lignes non synchronisées dans SQLite local
                $nonSyncedRecords = DB::table($table)->where('synced', 0)->get();

                foreach ($nonSyncedRecords as $record) {
                    try {
                        $attributes = (array) $record;
                        $attributes['synced'] = 1;

                        // Pousser l'insertion ou la mise à jour correspondante dans MySQL locale
                        DB::connection('mysql')->table($table)->updateOrInsert(['id' => $record->id], $attributes);

                        // Mettre à jour l'enregistrement en SQLite
                        DB::table($table)->where('id', $record->id)->update(['synced' => 1]);
                        $syncedCount++;
                    } catch (\Exception $ex) {
                        $errorsCount++;
                        Log::error("Erreur de poussée sync pour la table {$table} (ID : {$record->id}) : " . $ex->getMessage());
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$syncedCount} opération(s) synchronisée(s) avec succès.",
                'synced_count' => $syncedCount,
                'errors_count' => $errorsCount
            ]);
        } catch (\Exception $e) {
            Log::error('LocalSyncController::pushPending error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de synchronisation PUSH : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PULL : Télécharge toutes les données MySQL locales vers la base SQLite locale
     */
    public function pullUpdates()
    {
        try {
            Log::info('LocalSyncController::pullUpdates — Début du Pull direct DB MySQL');

            // Tenter de se connecter à la base MySQL locale
            try {
                DB::connection('mysql')->getPdo();
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de se connecter à la base MySQL locale : ' . $e->getMessage()
                ], 500);
            }

            // Récupérer directement les données depuis MySQL local
            $data = [
                'users'         => DB::connection('mysql')->table('users')->get()->map(fn($item) => (array)$item)->toArray(),
                'categories'    => DB::connection('mysql')->table('categories')->get()->map(fn($item) => (array)$item)->toArray(),
                'suppliers'     => DB::connection('mysql')->table('suppliers')->get()->map(fn($item) => (array)$item)->toArray(),
                'products'      => DB::connection('mysql')->table('products')->get()->map(fn($item) => (array)$item)->toArray(),
                'customers'     => DB::connection('mysql')->table('customers')->get()->map(fn($item) => (array)$item)->toArray(),
                'settings'      => (array) DB::connection('mysql')->table('settings')->first(),
                'cash_sessions' => DB::connection('mysql')->table('cash_sessions')->get()->map(fn($item) => (array)$item)->toArray(),
                'sales'         => DB::connection('mysql')->table('sales')->get()->map(function ($sale) {
                    $saleArr = (array)$sale;
                    $saleArr['items'] = DB::connection('mysql')->table('sale_items')
                        ->where('sale_id', $sale->id)
                        ->get()
                        ->map(fn($item) => (array)$item)
                        ->toArray();
                    return $saleArr;
                })->toArray(),
                'debt_payments' => DB::connection('mysql')->table('debt_payments')->get()->map(fn($item) => (array)$item)->toArray(),
                'restock_requests' => DB::connection('mysql')->table('restock_requests')->get()->map(fn($item) => (array)$item)->toArray(),
            ];

            // Désactiver temporairement les observateurs lors du PULL pour éviter le loop de sync_queue
            // et utiliser les transactions de base de données
            config(['app.is_syncing_pull' => true]);
            DB::transaction(function () use ($data) {

                // 1. Users
                if (isset($data['users'])) {
                    foreach ($data['users'] as $u) {
                        $existing = User::find($u['id']);
                        $userData = [
                            'name' => $u['name'],
                            'email' => $u['email'],
                            'role' => $u['role'] ?? 'employee',
                            'phone' => $u['phone'] ?? null,
                            'address' => $u['address'] ?? null,
                            'gender' => $u['gender'] ?? null,
                            'login_code' => $u['login_code'] ?? null,
                            'is_blocked' => $u['is_blocked'] ?? 0,
                            'created_at' => $u['created_at'] ?? null,
                            'updated_at' => $u['updated_at'] ?? null,
                        ];

                        if (isset($u['password'])) {
                            $userData['password'] = $u['password'];
                        } elseif (!$existing) {
                            // Si l'utilisateur n'existe pas en local et qu'on n'a pas de mot de passe,
                            // on définit un mot de passe par défaut temporaire (ex: "supermarche2026" ou "password")
                            $userData['password'] = bcrypt('123456');
                        }

                        User::updateOrCreate(
                            ['id' => $u['id']],
                            $userData
                        );
                    }
                }

                // 2. Categories
                if (isset($data['categories'])) {
                    foreach ($data['categories'] as $c) {
                        Category::updateOrCreate(
                            ['id' => $c['id']],
                            [
                                'name' => $c['name'],
                                'slug' => $c['slug'] ?? null,
                                'description' => $c['description'] ?? null,
                                'color' => $c['color'] ?? '#004d99',
                                'is_active' => $c['is_active'] ?? 1,
                                'created_by' => $c['created_by'] ?? null,
                                'created_at' => $c['created_at'] ?? null,
                                'updated_at' => $c['updated_at'] ?? null,
                            ]
                        );
                    }
                }

                // 3. Suppliers
                if (isset($data['suppliers'])) {
                    foreach ($data['suppliers'] as $s) {
                        Supplier::updateOrCreate(
                            ['id' => $s['id']],
                            [
                                'name' => $s['name'],
                                'email' => $s['email'] ?? null,
                                'phone' => $s['phone'] ?? null,
                                'contact_person' => $s['contact_person'] ?? null,
                                'address' => $s['address'] ?? null,
                                'city' => $s['city'] ?? null,
                                'website' => $s['website'] ?? null,
                                'is_active' => $s['is_active'] ?? 1,
                                'notes' => $s['notes'] ?? null,
                                'created_at' => $s['created_at'] ?? null,
                                'updated_at' => $s['updated_at'] ?? null,
                            ]
                        );
                    }
                }

                // 4. Products
                if (isset($data['products'])) {
                    foreach ($data['products'] as $p) {
                        Product::updateOrCreate(
                            ['id' => $p['id']],
                            [
                                'name' => $p['name'],
                                'slug' => $p['slug'] ?? null,
                                'reference' => $p['reference'] ?? null,
                                'qr_code' => $p['qr_code'] ?? null,
                                'category_name' => $p['category_name'] ?? null,
                                'supplier_id' => $p['supplier_id'] ?? null,
                                'price' => $p['price'] ?? 0,
                                'stock' => $p['stock'] ?? 0,
                                'stock_threshold' => $p['stock_threshold'] ?? 5,
                                'image' => $p['image'] ?? null,
                                'description' => $p['description'] ?? null,
                                'is_active' => $p['is_active'] ?? 1,
                                'created_by' => $p['created_by'] ?? null,
                                'created_at' => $p['created_at'] ?? null,
                                'updated_at' => $p['updated_at'] ?? null,
                            ]
                        );
                    }
                }

                // 5. Customers
                if (isset($data['customers'])) {
                    foreach ($data['customers'] as $c) {
                        Customer::updateOrCreate(
                            ['id' => $c['id']],
                            [
                                'name' => $c['name'],
                                'phone' => $c['phone'] ?? null,
                                'email' => $c['email'] ?? null,
                                'address' => $c['address'] ?? null,
                                'loyalty_points' => $c['loyalty_points'] ?? 0,
                                'debt_balance' => $c['debt_balance'] ?? 0,
                                'is_credit_blocked' => $c['is_credit_blocked'] ?? 0,
                                'created_at' => $c['created_at'] ?? null,
                                'updated_at' => $c['updated_at'] ?? null,
                            ]
                        );
                    }
                }

                // 6. Settings
                if (isset($data['settings']) && !empty($data['settings'])) {
                    $s = $data['settings'];
                    Setting::updateOrCreate(
                        ['id' => $s['id']],
                        [
                            'store_name' => $s['store_name'] ?? 'SUPERMARCHÉ PRO',
                            'phone' => $s['phone'] ?? '+225 07 00 00 00 00',
                            'address' => $s['address'] ?? 'Abidjan, Cocody Riviera Palmeraie',
                            'email' => $s['email'] ?? null,
                            'invoice_footer' => $s['invoice_footer'] ?? null,
                            'invoice_format' => $s['invoice_format'] ?? 'ticket',
                            'created_at' => $s['created_at'] ?? null,
                            'updated_at' => $s['updated_at'] ?? null,
                        ]
                    );
                }

                // 7. Cash Sessions (Récents)
                if (isset($data['cash_sessions'])) {
                    foreach ($data['cash_sessions'] as $cs) {
                        CashSession::updateOrCreate(
                            ['id' => $cs['id']],
                            [
                                'user_id' => $cs['user_id'],
                                'opening_balance' => $cs['opening_balance'],
                                'expected_closing_balance' => $cs['expected_closing_balance'] ?? null,
                                'actual_closing_balance' => $cs['actual_closing_balance'] ?? null,
                                'difference' => $cs['difference'] ?? null,
                                'opened_at' => $cs['opened_at'] ?? null,
                                'closed_at' => $cs['closed_at'] ?? null,
                                'status' => $cs['status'] ?? 'open',
                                'created_at' => $cs['created_at'] ?? null,
                                'updated_at' => $cs['updated_at'] ?? null,
                                'synced' => 1,
                            ]
                        );
                    }
                }

                // 8. Sales + items (Récents)
                if (isset($data['sales'])) {
                    foreach ($data['sales'] as $sale) {
                        Sale::updateOrCreate(
                            ['id' => $sale['id']],
                            [
                                'user_id' => $sale['user_id'],
                                'cash_session_id' => $sale['cash_session_id'] ?? null,
                                'customer_id' => $sale['customer_id'] ?? null,
                                'total_amount' => $sale['total_amount'],
                                'amount_received' => $sale['amount_received'] ?? 0,
                                'change_amount' => $sale['change_amount'] ?? 0,
                                'payment_method' => $sale['payment_method'] ?? 'cash',
                                'reference' => $sale['reference'] ?? null,
                                'status' => $sale['status'] ?? 'completed',
                                'refunded_amount' => $sale['refunded_amount'] ?? 0,
                                'created_at' => $sale['created_at'] ?? null,
                                'updated_at' => $sale['updated_at'] ?? null,
                                'synced' => 1,
                            ]
                        );

                        if (isset($sale['items'])) {
                            foreach ($sale['items'] as $item) {
                                SaleItem::updateOrCreate(
                                    ['id' => $item['id']],
                                    [
                                        'sale_id' => $item['sale_id'],
                                        'product_id' => $item['product_id'],
                                        'quantity' => $item['quantity'],
                                        'unit_price' => $item['unit_price'],
                                        'subtotal' => $item['subtotal'],
                                        'returned_quantity' => $item['returned_quantity'] ?? 0,
                                        'created_at' => $item['created_at'] ?? null,
                                        'updated_at' => $item['updated_at'] ?? null,
                                    ]
                                );
                            }
                        }
                    }
                }

                // 9. Debt Payments (Récents)
                if (isset($data['debt_payments'])) {
                    foreach ($data['debt_payments'] as $dp) {
                        DebtPayment::updateOrCreate(
                            ['id' => $dp['id']],
                            [
                                'customer_id' => $dp['customer_id'],
                                'user_id' => $dp['user_id'],
                                'cash_session_id' => $dp['cash_session_id'] ?? null,
                                'amount' => $dp['amount'],
                                'reference' => $dp['reference'] ?? null,
                                'payment_method' => $dp['payment_method'] ?? 'cash',
                                'created_at' => $dp['created_at'] ?? null,
                                'updated_at' => $dp['updated_at'] ?? null,
                                'synced' => 1,
                            ]
                        );
                    }
                }

                // 10. Restock Requests
                if (isset($data['restock_requests'])) {
                    foreach ($data['restock_requests'] as $r) {
                        RestockRequest::updateOrCreate(
                            ['id' => $r['id']],
                            [
                                'product_id' => $r['product_id'] ?? null,
                                'user_id' => $r['user_id'] ?? null,
                                'status' => $r['status'] ?? 'pending',
                                'quantity_requested' => $r['quantity_requested'] ?? 0,
                                'quantity_received' => $r['quantity_received'] ?? 0,
                                'created_at' => $r['created_at'] ?? null,
                                'updated_at' => $r['updated_at'] ?? null,
                                'synced' => 1,
                            ]
                        );
                    }
                }
            });

            config(['app.is_syncing_pull' => false]);

            return response()->json([
                'success' => true,
                'message' => 'Base SQLite locale mise à jour avec succès depuis le serveur en ligne.'
            ]);
        } catch (\Exception $e) {
            config(['app.is_syncing_pull' => false]);
            Log::error('LocalSyncController::pullUpdates error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de synchronisation PULL : ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retourne le nombre d'opérations locales en attente de synchronisation
     */
    public function getPendingCount()
    {
        try {
            $tables = ['users', 'categories', 'suppliers', 'products', 'customers', 'cash_sessions', 'sales', 'sale_items', 'debt_payments', 'restock_requests'];
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
     * Vérifie la connexion à la base MySQL locale
     */
    public function checkMysqlConnection()
    {
        try {
            DB::connection('mysql')->getPdo();
            return response()->json([
                'success' => true,
                'message' => 'Connexion MySQL locale établie avec succès.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'MySQL déconnecté : ' . $e->getMessage()
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
                'sale' => 'sales',
                'cash_session' => 'cash_sessions',
                'debt_payment' => 'debt_payments',
                'restock_request' => 'restock_requests',
                default => null
            };

            if ($table) {
                DB::table($table)->where('id', $id)->update(['synced' => 1]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to mark local entity as synced [{$entityType} ID={$id}]: " . $e->getMessage());
        }
    }
}
