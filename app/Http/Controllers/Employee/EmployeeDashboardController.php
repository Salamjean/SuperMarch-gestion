<?php

namespace App\Http\Controllers\Employee;

use App\Models\Product;
use App\Models\Category;
use App\Models\CashSession;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DebtPayment;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index()
    {
        // Session de caisse active
        $activeSession = CashSession::where('user_id', auth()->id())->where('status', 'open')->first();

        $expectedClosingBalance = 0;
        if ($activeSession) {
            $totalSales = Sale::where('cash_session_id', $activeSession->id)
                ->where('status', 'completed')
                ->sum('total_amount');
            $totalRepayments = DebtPayment::where('cash_session_id', $activeSession->id)
                ->sum('amount');
            $expectedClosingBalance = $activeSession->opening_balance + $totalSales + $totalRepayments;
        }

        // Liste des clients
        $customers = Customer::orderBy('name')->get();

        // Produits pour la caisse (stock > 0)
        $products = Product::where('is_active', true)->where('stock', '>', 0)->latest()->get();

        // Tous les produits pour l'onglet Stock
        $allProducts = Product::where('is_active', true)->latest()->get();

        $categories = Category::where('is_active', true)->get();

        $salesQuery = Sale::where('user_id', auth()->id());

        // Toutes les ventes pour l'historique (avec client)
        $sales = (clone $salesQuery)->with(['items.product', 'customer', 'user'])->latest()->take(50)->get();

        // --- Statistiques ---
        // Ventes d'aujourd'hui
        $todaySales = (clone $salesQuery)->whereDate('created_at', Carbon::today())->where('status', 'completed')->get();
        $todayRevenue = $todaySales->sum('total_amount');
        $todayCount = $todaySales->count();
        $todayAverage = $todayCount > 0 ? $todayRevenue / $todayCount : 0;

        // Ventes globales du caissier connecté
        $totalSales = (clone $salesQuery)->where('status', 'completed')->get();
        $totalRevenue = $totalSales->sum('total_amount');
        $totalCount = $totalSales->count();
        $totalAverage = $totalCount > 0 ? $totalRevenue / $totalCount : 0;

        // Top produits vendus par ce caissier
        $topProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_subtotal'))
            ->whereHas('sale', function ($query) {
                $query->where('user_id', auth()->id())->where('status', 'completed');
            })
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->with('product')
            ->take(5)
            ->get();

        // Ventes des 7 derniers jours pour un mini graphique/tendance
        $weeklySales = [];
        $daysFr = ['Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mer', 'Thu' => 'Jeu', 'Fri' => 'Ven', 'Sat' => 'Sam', 'Sun' => 'Dim'];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayRevenue = Sale::where('user_id', auth()->id())
                ->whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total_amount');

            $weeklySales[] = [
                'day' => $daysFr[$date->format('D')] ?? $date->format('D'),
                'date' => $date->format('d/m'),
                'revenue' => $dayRevenue
            ];
        }

        $pendingRestockRequestIds = \App\Models\RestockRequest::where('status', 'pending')
            ->pluck('product_id')
            ->toArray();

        return view('employee.dashboard', compact(
            'products',
            'allProducts',
            'categories',
            'sales',
            'todayRevenue',
            'todayCount',
            'todayAverage',
            'totalRevenue',
            'totalCount',
            'totalAverage',
            'topProducts',
            'weeklySales',
            'pendingRestockRequestIds',
            'activeSession',
            'expectedClosingBalance',
            'customers'
        ));
    }

    public function checkout(Request $request)
    {
        // Vérifier si une session de caisse est active
        $activeSession = CashSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune session de caisse ouverte. Vous devez ouvrir la caisse avant de vendre.'
            ], 403);
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'total_amount' => 'required|numeric',
            'amount_received' => 'required|numeric',
            'change_amount' => 'required|numeric',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_method' => 'nullable|string|in:cash,card,credit'
        ]);

        $paymentMethod = $request->input('payment_method', 'cash');

        if ($paymentMethod === 'credit' && !$request->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Un client doit être sélectionné pour une vente à crédit.'
            ], 422);
        }

        if ($paymentMethod === 'credit' && $request->customer_id) {
            $customer = Customer::find($request->customer_id);
            if ($customer && $customer->is_credit_blocked) {
                return response()->json([
                    'success' => false,
                    'message' => "Ce client est bloqué pour les achats à crédit par l'administrateur."
                ], 403);
            }
        }

        return DB::transaction(function () use ($request, $activeSession, $paymentMethod) {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'cash_session_id' => $activeSession->id,
                'customer_id' => $request->customer_id,
                'total_amount' => $request->total_amount,
                'amount_received' => $request->amount_received,
                'change_amount' => $request->change_amount,
                'payment_method' => $paymentMethod,
                'reference' => 'SAL-' . strtoupper(Str::random(8)),
                'status' => 'completed'
            ]);

            // Gestion du crédit client
            if ($request->customer_id) {
                $customer = Customer::findOrFail($request->customer_id);

                // Si paiement à crédit, on augmente sa dette
                if ($paymentMethod === 'credit') {
                    // La dette est le montant total moins ce que le client a déjà payé
                    $debt = max(0, $request->total_amount - $request->amount_received);
                    if ($debt > 0) {
                        $customer->increment('debt_balance', $debt);
                    }
                }
            }

            $lowStockAlerts = [];

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['id']);

                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stock insuffisant pour le produit: {$product->name}");
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['price'] * $item['qty'],
                ]);

                // Décrémenter le stock
                $product->decrement('stock', $item['qty']);

                // Vérifier le seuil après décrémentation
                $product->refresh();
                if ($product->stock <= $product->stock_threshold) {
                    $lowStockAlerts[] = [
                        'name' => $product->name,
                        'current_stock' => $product->stock
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Vente enregistrée avec succès',
                'sale' => $sale->load(['items.product', 'user', 'customer']),
                'low_stock_alerts' => $lowStockAlerts
            ]);
        });
    }

    public function openSession(Request $request)
    {
        $request->validate([
            'opening_balance' => 'required|numeric|min:0'
        ]);

        // Vérifier si une session est déjà ouverte
        $existing = CashSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Une session de caisse est déjà active.'
            ], 400);
        }

        $session = CashSession::create([
            'user_id' => auth()->id(),
            'opening_balance' => $request->opening_balance,
            'opened_at' => now(),
            'status' => 'open'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Caisse ouverte avec succès',
            'session' => $session
        ]);
    }

    public function closeSession(Request $request)
    {
        $request->validate([
            'actual_closing_balance' => 'required|numeric|min:0'
        ]);

        $activeSession = CashSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune session active à clôturer.'
            ], 400);
        }

        // Calculer l'attendu théorique (ventes + encaissements de crédit)
        $totalSales = Sale::where('cash_session_id', $activeSession->id)
            ->where('status', 'completed')
            ->sum('total_amount');

        $totalRepayments = DebtPayment::where('cash_session_id', $activeSession->id)
            ->sum('amount');

        $expected = $activeSession->opening_balance + $totalSales + $totalRepayments;
        $actual = $request->actual_closing_balance;
        $difference = $actual - $expected;

        $activeSession->update([
            'expected_closing_balance' => $expected,
            'actual_closing_balance' => $actual,
            'difference' => $difference,
            'closed_at' => now(),
            'status' => 'closed'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Caisse clôturée avec succès.',
            'session' => $activeSession
        ]);
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->get('q', '');
        $customers = Customer::where('name', 'LIKE', "%{$query}%")
            ->orWhere('phone', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->take(10)
            ->get();

        return response()->json($customers);
    }

    public function storeCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string'
        ]);

        $customer = Customer::create($request->only('name', 'phone', 'email', 'address'));

        return response()->json([
            'success' => true,
            'message' => 'Client enregistré avec succès.',
            'customer' => $customer
        ]);
    }

    public function refundSale(Request $request, $id)
    {
        return DB::transaction(function () use ($id) {
            $sale = Sale::with('items')->findOrFail($id);

            if ($sale->status === 'returned') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cette vente a déjà été remboursée/retournée.'
                ], 400);
            }

            // Remettre en stock les produits concernés
            foreach ($sale->items as $item) {
                $product = Product::findOrFail($item->product_id);
                $product->increment('stock', $item->quantity);

                // Mettre à jour la quantité retournée dans SaleItem
                $item->update([
                    'returned_quantity' => $item->quantity
                ]);
            }

            // Si c'était à crédit, on réduit la dette du client
            if ($sale->customer_id) {
                $customer = Customer::find($sale->customer_id);
                if ($customer && $sale->payment_method === 'credit') {
                    $debt = max(0, $sale->total_amount - $sale->amount_received);
                    if ($debt > 0) {
                        $customer->decrement('debt_balance', min($customer->debt_balance, $debt));
                    }
                }
            }

            // Mettre à jour le statut de la vente
            $sale->update([
                'status' => 'returned',
                'refunded_amount' => $sale->total_amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'La vente a été annulée et les articles ont été remis en stock avec succès.',
                'sale' => $sale->load('items.product')
            ]);
        });
    }

    public function requestRestock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $exists = \App\Models\RestockRequest::where('product_id', $request->product_id)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Une demande de réapprovisionnement est déjà en attente pour ce produit.'
            ]);
        }

        \App\Models\RestockRequest::create([
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'La demande de réapprovisionnement a été envoyée avec succès.'
        ]);
    }
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'password' => 'nullable|string|min:6|confirmed'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'gender' => $request->gender
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil mis à jour avec succès.',
            'user' => $user
        ]);
    }

    public function syncSales(Request $request)
    {
        $request->validate([
            'sales' => 'required|array'
        ]);

        $activeSession = CashSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune session de caisse ouverte dans la base de données. Ouvrez une session.'
            ], 403);
        }

        $salesData = $request->input('sales');
        $syncedIds = [];
        $errors = [];

        foreach ($salesData as $saleObj) {
            $localId = $saleObj['localId'];
            try {
                DB::transaction(function () use ($saleObj, $activeSession, &$syncedIds, $localId) {
                    // Check if already synchronized
                    $reference = $saleObj['reference'] ?? ('SAL-OFF-' . strtoupper(Str::random(8)));
                    $existing = Sale::where('reference', $reference)->first();

                    if (!$existing) {
                        $sale = Sale::create([
                            'user_id' => auth()->id(),
                            'cash_session_id' => $activeSession->id,
                            'customer_id' => $saleObj['customer_id'] ?: null,
                            'total_amount' => $saleObj['total_amount'],
                            'amount_received' => $saleObj['amount_received'],
                            'change_amount' => $saleObj['change_amount'],
                            'payment_method' => $saleObj['payment_method'],
                            'reference' => $reference,
                            'status' => 'completed',
                            'created_at' => $saleObj['created_at'] ? Carbon::parse($saleObj['created_at']) : now(),
                        ]);

                        // Debt calculation
                        if ($sale->customer_id) {
                            $customer = Customer::find($sale->customer_id);
                            if ($customer) {
                                if ($sale->payment_method === 'credit') {
                                    if ($customer->is_credit_blocked) {
                                        throw new \Exception("Le client {$customer->name} est bloqué pour les achats à crédit.");
                                    }
                                    $debt = max(0, $sale->total_amount - $sale->amount_received);
                                    if ($debt > 0) {
                                        $customer->increment('debt_balance', $debt);
                                    }
                                }
                            }
                        }

                        foreach ($saleObj['items'] as $item) {
                            $product = Product::find($item['id']);
                            if ($product) {
                                SaleItem::create([
                                    'sale_id' => $sale->id,
                                    'product_id' => $product->id,
                                    'quantity' => $item['qty'],
                                    'unit_price' => $item['price'],
                                    'subtotal' => $item['price'] * $item['qty'],
                                    'created_at' => $sale->created_at,
                                ]);

                                // Decrement stock (if it's still positive)
                                $product->decrement('stock', $item['qty']);
                            }
                        }
                    }

                    $syncedIds[] = $localId;
                });
            } catch (\Exception $e) {
                $errors[] = "Erreur vente {$localId}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'syncedLocalIds' => $syncedIds,
            'errors' => $errors
        ]);
    }

    public function payCustomerDebt(Request $request, $id)
    {
        $activeSession = CashSession::where('user_id', auth()->id())->where('status', 'open')->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune session de caisse ouverte. Vous devez ouvrir la caisse avant d\'encaisser.'
            ], 403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $customer = Customer::findOrFail($id);
        $amountToPay = min($customer->debt_balance, $request->amount);

        if ($amountToPay <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ce client n\'a pas de dette active.'
            ], 400);
        }

        try {
            return DB::transaction(function () use ($customer, $amountToPay, $activeSession) {
                $customer->decrement('debt_balance', $amountToPay);

                $payment = DebtPayment::create([
                    'customer_id' => $customer->id,
                    'user_id' => auth()->id(),
                    'cash_session_id' => $activeSession->id,
                    'amount' => $amountToPay,
                    'reference' => 'PAY-' . strtoupper(Str::random(8)),
                    'payment_method' => 'cash'
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Encaissement de ' . number_format($amountToPay, 0, ',', ' ') . ' FCFA enregistré avec succès.',
                    'payment' => $payment->load(['customer', 'user']),
                    'new_debt_balance' => $customer->debt_balance
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement : ' . $e->getMessage()
            ], 500);
        }
    }

    public function getProductsStock()
    {
        $products = Product::where('is_active', true)->select('id', 'stock', 'stock_threshold')->get();
        return response()->json([
            'success' => true,
            'products' => $products
        ]);
    }
}
