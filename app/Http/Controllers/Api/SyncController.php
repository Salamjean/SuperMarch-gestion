<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SyncController extends Controller
{
    /**
     * Vérification de la clé API de synchronisation.
     * Retourne une réponse 401 si la clé est invalide.
     */
    private function checkSyncKey(Request $request): bool
    {
        $expectedKey = config('app.sync_api_key', env('SYNC_API_KEY', 'supermarche-sync-secret-2026'));
        $providedKey = $request->header('X-Sync-Key');
        return $providedKey === $expectedKey;
    }

    /**
     * PULL — Envoie toutes les données MySQL vers le client Electron (SQLite).
     *
     * GET /api/sync/pull
     */
    public function pull(Request $request)
    {
        if (!$this->checkSyncKey($request)) {
            return response()->json(['error' => 'Clé API invalide.'], 401);
        }

        try {
            $data = [
                'users' => User::select('id', 'name', 'email', 'password', 'role', 'phone', 'address', 'gender', 'login_code', 'created_at', 'updated_at')
                    ->get(),

                'categories' => Category::all(),

                'suppliers' => Supplier::all(),

                'products' => Product::all(),

                'customers' => Customer::all(),

                'settings' => Setting::first(),

                // Sessions de caisse récentes (30 derniers jours)
                'cash_sessions' => CashSession::where('opened_at', '>=', now()->subDays(30))
                    ->get(),

                // Ventes récentes (30 derniers jours) avec leurs items
                'sales' => Sale::where('created_at', '>=', now()->subDays(30))
                    ->with('items')
                    ->get()
                    ->map(function ($sale) {
                        $arr = $sale->toArray();
                        $arr['items'] = $sale->items->toArray();
                        return $arr;
                    }),

                // Paiements de dettes récents (30 derniers jours)
                'debt_payments' => DebtPayment::where('created_at', '>=', now()->subDays(30))
                    ->get(),

                'restock_requests' => RestockRequest::all(),
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            Log::error('SyncController::pull error: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors du pull : ' . $e->getMessage()], 500);
        }
    }

    /**
     * PUSH — Reçoit les opérations hors-ligne depuis Electron et les applique dans MySQL.
     *
     * POST /api/sync/push
     * Body: { "operations": [...] }
     */
    public function push(Request $request)
    {
        if (!$this->checkSyncKey($request)) {
            return response()->json(['error' => 'Clé API invalide.'], 401);
        }

        $request->validate([
            'operations' => 'required|array'
        ]);

        $operations = $request->input('operations');
        $syncedQueueIds = [];
        $errors = [];

        foreach ($operations as $op) {
            $queueId      = $op['queue_id'];
            $entityType   = $op['entity_type'] ?? '';
            $operation    = $op['operation'] ?? '';
            $data         = $op['data'] ?? [];
            $createdAt    = $op['created_at'] ?? now()->toDateTimeString();

            try {
                switch ($entityType) {

                    case 'sale':
                        $this->applySaleOperation($operation, $data, $createdAt);
                        break;

                    case 'customer':
                        $this->applyCustomerOperation($operation, $data);
                        break;

                    case 'debt_payment':
                        $this->applyDebtPaymentOperation($operation, $data, $createdAt);
                        break;

                    case 'cash_session':
                        $this->applyCashSessionOperation($operation, $data);
                        break;

                    case 'product':
                        $this->applyProductOperation($operation, $data);
                        break;

                    case 'category':
                        $this->applyCategoryOperation($operation, $data);
                        break;

                    case 'supplier':
                        $this->applySupplierOperation($operation, $data);
                        break;

                    case 'user':
                        $this->applyUserOperation($operation, $data);
                        break;

                    case 'restock_request':
                        $this->applyRestockRequestOperation($operation, $data);
                        break;

                    case 'sale_legacy':
                        // Ancienne structure offline_sales JSON (rétrocompatibilité)
                        $this->applyLegacySale($data, $createdAt);
                        break;

                    default:
                        Log::warning("SyncController::push — type inconnu: {$entityType}");
                        break;
                }

                $syncedQueueIds[] = $queueId;

            } catch (\Exception $e) {
                Log::error("SyncController::push error [queue_id={$queueId}]: " . $e->getMessage());
                $errors[] = [
                    'queue_id' => $queueId,
                    'entity_type' => $entityType,
                    'message' => $e->getMessage()
                ];
            }
        }

        return response()->json([
            'success'          => true,
            'synced_queue_ids' => $syncedQueueIds,
            'errors'           => $errors,
            'message'          => count($syncedQueueIds) . ' opération(s) appliquée(s).'
        ]);
    }

    // ─── Helpers privés ───────────────────────────────────────────────────────

    private function applySaleOperation(string $operation, array $data, string $createdAt): void
    {
        if ($operation === 'create') {
            $saleData = $data['sale'] ?? $data;
            $items    = $data['items'] ?? ($saleData['items'] ?? []);

            $reference = $saleData['reference'] ?? ('SAL-OFF-' . strtoupper(Str::random(8)));

            // Éviter les doublons
            if (Sale::where('reference', $reference)->exists()) {
                return;
            }

            // Trouver une session de caisse ouverte pour cet utilisateur
            $userId = $saleData['user_id'] ?? null;
            $cashSessionId = $saleData['cash_session_id'] ?? null;

            // Si la session locale n'existe pas encore en MySQL, prendre la dernière ouverte
            if ($cashSessionId && !CashSession::find($cashSessionId)) {
                $session = CashSession::where('user_id', $userId)->where('status', 'open')->first();
                $cashSessionId = $session?->id;
            }

            DB::transaction(function () use ($saleData, $items, $reference, $cashSessionId, $createdAt) {
                $sale = Sale::create([
                    'user_id'          => $saleData['user_id'],
                    'cash_session_id'  => $cashSessionId,
                    'customer_id'      => $saleData['customer_id'] ?? null,
                    'total_amount'     => $saleData['total_amount'],
                    'amount_received'  => $saleData['amount_received'] ?? 0,
                    'change_amount'    => $saleData['change_amount'] ?? 0,
                    'payment_method'   => $saleData['payment_method'] ?? 'cash',
                    'reference'        => $reference,
                    'status'           => 'completed',
                    'created_at'       => Carbon::parse($createdAt),
                    'updated_at'       => Carbon::parse($createdAt),
                ]);

                // Gérer la dette client
                if ($sale->customer_id && $sale->payment_method === 'credit') {
                    $customer = Customer::find($sale->customer_id);
                    if ($customer) {
                        $debt = max(0, $sale->total_amount - $sale->amount_received);
                        if ($debt > 0) $customer->increment('debt_balance', $debt);
                    }
                }

                // Créer les items et décrémenter le stock
                foreach ($items as $item) {
                    $product = Product::find($item['id'] ?? $item['product_id'] ?? null);
                    if ($product) {
                        SaleItem::create([
                            'sale_id'    => $sale->id,
                            'product_id' => $product->id,
                            'quantity'   => $item['qty'] ?? $item['quantity'],
                            'unit_price' => $item['price'] ?? $item['unit_price'],
                            'subtotal'   => ($item['price'] ?? $item['unit_price']) * ($item['qty'] ?? $item['quantity']),
                            'created_at' => Carbon::parse($createdAt),
                        ]);
                        $product->decrement('stock', $item['qty'] ?? $item['quantity']);
                    }
                }
            });

        } elseif ($operation === 'refund') {
            $saleId = $data['saleId'] ?? null;
            if (!$saleId) return;

            $sale = Sale::with('items')->find($saleId);
            if (!$sale || $sale->status === 'returned') return;

            DB::transaction(function () use ($sale) {
                foreach ($sale->items as $item) {
                    Product::find($item->product_id)?->increment('stock', $item->quantity);
                    $item->update(['returned_quantity' => $item->quantity]);
                }
                if ($sale->customer_id && $sale->payment_method === 'credit') {
                    $debt = max(0, $sale->total_amount - ($sale->amount_received ?? 0));
                    $customer = Customer::find($sale->customer_id);
                    if ($customer && $debt > 0) {
                        $customer->decrement('debt_balance', min($customer->debt_balance, $debt));
                    }
                }
                $sale->update(['status' => 'returned', 'refunded_amount' => $sale->total_amount]);
            });
        }
    }

    private function applyCustomerOperation(string $operation, array $data): void
    {
        if ($operation === 'create') {
            // Éviter les doublons par téléphone ou email si fournis
            $existing = null;
            if (!empty($data['phone'])) {
                $existing = Customer::where('phone', $data['phone'])->first();
            }
            if (!$existing && !empty($data['email'])) {
                $existing = Customer::where('email', $data['email'])->first();
            }

            if (!$existing) {
                Customer::create([
                    'name'    => $data['name'],
                    'phone'   => $data['phone'] ?? null,
                    'email'   => $data['email'] ?? null,
                    'address' => $data['address'] ?? null,
                ]);
            }
        }
    }

    private function applyDebtPaymentOperation(string $operation, array $data, string $createdAt): void
    {
        if ($operation === 'create') {
            $reference = $data['reference'] ?? ('PAY-OFF-' . strtoupper(Str::random(8)));
            if (DebtPayment::where('reference', $reference)->exists()) return;

            $customer = Customer::find($data['customer_id'] ?? null);
            if (!$customer) return;

            $amount = min($customer->debt_balance, $data['amount']);
            if ($amount <= 0) return;

            DB::transaction(function () use ($customer, $amount, $data, $reference, $createdAt) {
                $customer->decrement('debt_balance', $amount);
                DebtPayment::create([
                    'customer_id'     => $customer->id,
                    'user_id'         => $data['user_id'],
                    'cash_session_id' => $data['cash_session_id'] ?? null,
                    'amount'          => $amount,
                    'reference'       => $reference,
                    'payment_method'  => $data['payment_method'] ?? 'cash',
                    'created_at'      => Carbon::parse($createdAt),
                ]);
            });
        }
    }

    private function applyCashSessionOperation(string $operation, array $data): void
    {
        if ($operation === 'create') {
            $existing = CashSession::find($data['id'] ?? null);
            if ($existing) return;

            CashSession::create([
                'user_id'         => $data['user_id'],
                'opening_balance' => $data['opening_balance'],
                'opened_at'       => $data['opened_at'] ?? now(),
                'status'          => 'open',
            ]);

        } elseif ($operation === 'close') {
            $session = CashSession::find($data['id'] ?? null);
            if (!$session || $session->status === 'closed') return;

            $session->update([
                'expected_closing_balance' => $data['expected_closing_balance'],
                'actual_closing_balance'   => $data['actual_closing_balance'],
                'difference'               => $data['difference'],
                'closed_at'                => $data['closed_at'] ?? now(),
                'status'                   => 'closed',
            ]);
        }
    }

    private function applyLegacySale(array $saleData, string $createdAt): void
    {
        // Rétrocompatibilité avec l'ancien format JSON offline_sales
        $reference = $saleData['reference'] ?? ('SAL-OFF-' . strtoupper(Str::random(8)));
        if (Sale::where('reference', $reference)->exists()) return;

        $userId = $saleData['user_id'] ?? null;
        $session = CashSession::where('user_id', $userId)->where('status', 'open')->first();
        if (!$session) return;

        DB::transaction(function () use ($saleData, $session, $reference, $createdAt) {
            $sale = Sale::create([
                'user_id'         => $saleData['user_id'] ?? $session->user_id,
                'cash_session_id' => $session->id,
                'customer_id'     => $saleData['customer_id'] ?? null,
                'total_amount'    => $saleData['total_amount'],
                'amount_received' => $saleData['amount_received'] ?? 0,
                'change_amount'   => $saleData['change_amount'] ?? 0,
                'payment_method'  => $saleData['payment_method'] ?? 'cash',
                'reference'       => $reference,
                'status'          => 'completed',
                'created_at'      => Carbon::parse($createdAt),
            ]);

            foreach ($saleData['items'] ?? [] as $item) {
                $product = Product::find($item['id'] ?? $item['product_id'] ?? null);
                if ($product) {
                    SaleItem::create([
                        'sale_id'    => $sale->id,
                        'product_id' => $product->id,
                        'quantity'   => $item['qty'] ?? $item['quantity'],
                        'unit_price' => $item['price'] ?? $item['unit_price'],
                        'subtotal'   => ($item['price'] ?? $item['unit_price']) * ($item['qty'] ?? $item['quantity']),
                        'created_at' => Carbon::parse($createdAt),
                    ]);
                    $product->decrement('stock', $item['qty'] ?? $item['quantity']);
                }
            }
        });
    }

    private function applyProductOperation(string $operation, array $data): void
    {
        if ($operation === 'create' || $operation === 'update') {
            Product::updateOrCreate(['id' => $data['id']], [
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'reference' => $data['reference'] ?? null,
                'qr_code' => $data['qr_code'] ?? null,
                'category_name' => $data['category_name'] ?? null,
                'supplier_id' => $data['supplier_id'] ?? null,
                'price' => $data['price'] ?? 0,
                'stock' => $data['stock'] ?? 0,
                'stock_threshold' => $data['stock_threshold'] ?? 5,
                'image' => $data['image'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? 1,
                'created_by' => $data['created_by'] ?? null,
            ]);
        } elseif ($operation === 'delete') {
            Product::destroy($data['id']);
        }
    }

    private function applyCategoryOperation(string $operation, array $data): void
    {
        if ($operation === 'create' || $operation === 'update') {
            Category::updateOrCreate(['id' => $data['id']], [
                'name' => $data['name'],
                'slug' => $data['slug'] ?? Str::slug($data['name']),
                'description' => $data['description'] ?? null,
                'color' => $data['color'] ?? '#004d99',
                'is_active' => $data['is_active'] ?? 1,
                'created_by' => $data['created_by'] ?? null,
            ]);
        } elseif ($operation === 'delete') {
            Category::destroy($data['id']);
        }
    }

    private function applySupplierOperation(string $operation, array $data): void
    {
        if ($operation === 'create' || $operation === 'update') {
            Supplier::updateOrCreate(['id' => $data['id']], [
                'name' => $data['name'],
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'contact_person' => $data['contact_person'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'website' => $data['website'] ?? null,
                'is_active' => $data['is_active'] ?? 1,
                'notes' => $data['notes'] ?? null,
            ]);
        } elseif ($operation === 'delete') {
            Supplier::destroy($data['id']);
        }
    }

    private function applyUserOperation(string $operation, array $data): void
    {
        if ($operation === 'create' || $operation === 'update') {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'] ?? 'employee',
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'gender' => $data['gender'] ?? null,
                'login_code' => $data['login_code'] ?? null,
                'is_blocked' => $data['is_blocked'] ?? 0,
            ];
            if (!empty($data['password'])) {
                $userData['password'] = $data['password'];
            }
            User::updateOrCreate(['id' => $data['id']], $userData);
        } elseif ($operation === 'delete') {
            User::destroy($data['id']);
        }
    }

    private function applyRestockRequestOperation(string $operation, array $data): void
    {
        if ($operation === 'create' || $operation === 'update') {
            RestockRequest::updateOrCreate(['id' => $data['id']], [
                'product_id' => $data['product_id'] ?? null,
                'user_id' => $data['user_id'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'quantity_requested' => $data['quantity_requested'] ?? 0,
                'quantity_received' => $data['quantity_received'] ?? 0,
            ]);
        } elseif ($operation === 'delete') {
            RestockRequest::destroy($data['id']);
        }
    }
}
