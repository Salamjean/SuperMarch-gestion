<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistiques globales
        $totalProducts = Product::count();
        $productsAtThreshold = Product::whereColumn('stock', '<=', 'stock_threshold')->count();

        // Statistiques ventes (aujourd'hui)
        $todaysSales = Sale::whereDate('created_at', today())->sum('total_amount');
        $todaysSalesCount = Sale::whereDate('created_at', today())->count();

        // Statistiques ventes (ce mois)
        $monthSales = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Produits en stock faible
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->orderBy('stock')
            ->limit(5)
            ->get();

        // Dernières ventes avec le caissier
        $recentSales = Sale::with('user')->latest()->limit(5)->get();

        // Statistiques d'Audit administratives additionnelles
        $totalCustomerDebt = \App\Models\Customer::sum('debt_balance');
        $totalLoyaltyPoints = \App\Models\Customer::sum('loyalty_points');
        $totalCashSessionDiscrepancies = \App\Models\CashSession::whereNotNull('closed_at')->sum('difference');

        return view('admin.dashboard', compact(
            'totalProducts',
            'productsAtThreshold',
            'todaysSales',
            'todaysSalesCount',
            'monthSales',
            'lowStockProducts',
            'recentSales',
            'totalCustomerDebt',
            'totalLoyaltyPoints',
            'totalCashSessionDiscrepancies'
        ));
    }

    public function resolveRestock(Request $request, $id)
    {
        $restockRequest = \App\Models\RestockRequest::findOrFail($id);

        $quantity = intval($request->input('quantity', 0));

        if ($quantity > 0) {
            $product = $restockRequest->product;
            if ($product) {
                $product->increment('stock', $quantity);
            }
        }

        $restockRequest->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'La demande a été marquée comme traitée.' . ($quantity > 0 ? " Le stock de " . $product->name . " a été augmenté de {$quantity} unités." : '')
        ]);
    }

    public function restockRequestsIndex()
    {
        $requests = \App\Models\RestockRequest::with(['product', 'user'])
            ->latest()
            ->paginate(15);

        return view('admin.restock-requests.index', compact('requests'));
    }
}
