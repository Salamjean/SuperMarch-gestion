<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use App\Models\Category;
use App\Models\Customer;
use App\Models\CashSession;
use App\Models\RestockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // ── Produits ──────────────────────────────────────────────
        $totalProducts      = Product::count();
        $totalCategories    = Category::count();
        $productsAtThreshold = Product::whereColumn('stock', '<=', 'stock_threshold')->count();
        $outOfStock         = Product::where('stock', 0)->count();

        // ── Ventes du jour ────────────────────────────────────────
        $todaysSales      = Sale::whereDate('created_at', today())->sum('total_amount');
        $todaysSalesCount = Sale::whereDate('created_at', today())->count();

        // ── Ventes du mois ────────────────────────────────────────
        $monthSales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        $monthSalesCount = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── Ventes d'hier (pour delta) ─────────────────────────────
        $yesterdaySales = Sale::whereDate('created_at', today()->subDay())->sum('total_amount');
        $salesDelta = $yesterdaySales > 0
            ? round((($todaysSales - $yesterdaySales) / $yesterdaySales) * 100, 1)
            : ($todaysSales > 0 ? 100 : 0);

        // ── Clients ───────────────────────────────────────────────
        $totalCustomers       = Customer::count();
        $totalCustomerDebt    = Customer::sum('debt_balance');
        $indebtedCustomersCount = Customer::where('debt_balance', '>', 0)->count();

        // ── Personnel ─────────────────────────────────────────────
        $totalEmployees   = User::where('role', '!=', 'admin')->count();
        $activeEmployees  = User::where('role', '!=', 'admin')->whereNull('deleted_at')->count();
        $blockedEmployees = User::withTrashed()->where('role', '!=', 'admin')->whereNotNull('deleted_at')->count();

        // ── Caisse & Audit ────────────────────────────────────────
        $openCashSessions  = CashSession::whereNull('closed_at')->count();
        $totalCashSessionDiscrepancies = CashSession::whereNotNull('closed_at')->sum('difference');

        // ── Demandes de réappro ───────────────────────────────────
        $pendingRestockCount = RestockRequest::where('status', 'pending')->count();

        // ── Top produits vendus ce mois ──────────────────────────
        $topProducts = SaleItem::select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('sale', function ($q) {
                $q->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
            })
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ── Ventes par mode de paiement ce mois ─────────────────
        $salesByPayment = Sale::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('payment_method')
            ->get();

        // ── Performance des caissiers ce mois ───────────────────
        $cashierPerformance = Sale::select('user_id', DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(total_amount) as total'))
            ->with('user')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // ── Produits en stock faible ──────────────────────────────
        $lowStockProducts = Product::where('stock', '<=', DB::raw('stock_threshold'))
            ->orderBy('stock')
            ->limit(8)
            ->get();

        // ── Dernières ventes ──────────────────────────────────────
        $recentSales = Sale::with('user')
            ->latest()
            ->limit(3)
            ->get();

        // ── Évolution des ventes (7 derniers jours) ──────────────
        $salesEvolution = Sale::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Construire un tableau complet sur 7 jours (même si aucune vente certains jours)
        $joursFr = ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'];
        $salesChart = [];
        for ($i = 6; $i >= 0; $i--) {
            $d     = now()->subDays($i);
            $day   = $d->format('Y-m-d');
            $label = $joursFr[$d->dayOfWeek] . ' ' . $d->format('d/m');
            $salesChart[] = [
                'label' => $label,
                'total' => $salesEvolution->get($day)?->total ?? 0,
                'count' => $salesEvolution->get($day)?->count ?? 0,
            ];
        }

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'productsAtThreshold',
            'outOfStock',
            'todaysSales',
            'todaysSalesCount',
            'monthSales',
            'monthSalesCount',
            'yesterdaySales',
            'salesDelta',
            'totalCustomers',
            'totalCustomerDebt',
            'indebtedCustomersCount',
            'totalEmployees',
            'activeEmployees',
            'blockedEmployees',
            'openCashSessions',
            'totalCashSessionDiscrepancies',
            'pendingRestockCount',
            'topProducts',
            'salesByPayment',
            'cashierPerformance',
            'lowStockProducts',
            'recentSales',
            'salesChart'
        ));
    }

    public function resolveRestock(Request $request, $id)
    {
        $restockRequest = \App\Models\RestockRequest::findOrFail($id);

        $quantity = intval($request->input('quantity', 0));
        $initialStock = 0;

        if ($quantity > 0) {
            $product = $restockRequest->product;
            if ($product) {
                $initialStock = $product->stock;
                $product->increment('stock', $quantity);
            }
        }

        $restockRequest->update([
            'status' => 'completed',
            'initial_stock' => $initialStock,
            'added_stock' => $quantity,
        ]);

        return response()->json([
            'success' => true,
            'initial_stock' => $initialStock,
            'added_stock' => $quantity,
            'new_stock' => isset($product) ? $product->stock : 0,
            'threshold' => isset($product) ? $product->stock_threshold : null,
            'message' => 'La demande a été marquée comme traitée.' . ($quantity > 0 ? " Le stock de " . ($product->name ?? '') . " a été augmenté de {$quantity} unités." : '')
        ]);
    }

    public function restockRequestsIndex()
    {
        $requests = \App\Models\RestockRequest::with(['product', 'user'])
            ->latest()
            ->paginate(15);

        return view('admin.restock-requests.index', compact('requests'));
    }

    public function showProfile()
    {
        $user = auth()->user();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'current_password' => ['required', 'string']
        ]);

        if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.'])->with('error', 'Le mot de passe actuel saisi est incorrect.');
        }

        $data = [
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'gender' => $validated['gender'] ?? null,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.profile.show')
            ->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
