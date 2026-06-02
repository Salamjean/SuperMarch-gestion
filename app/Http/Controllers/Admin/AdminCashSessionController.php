<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashSession;
use App\Models\User;
use App\Models\Sale;
use Illuminate\Http\Request;

class AdminCashSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = CashSession::with(['user'])->latest();

        // Filtre par caissier
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par date
        if ($request->filled('date')) {
            $query->whereDate('opened_at', $request->date);
        }

        $sessions = $query->paginate(15)->withQueryString();
        $cashiers = User::where('role', 'employee')->orWhere('role', 'admin')->get();

        return view('admin.cash-sessions.index', compact('sessions', 'cashiers'));
    }

    public function show($id)
    {
        $session = CashSession::with(['user'])->findOrFail($id);

        // Toutes les ventes associées à cette session
        $sales = Sale::with(['customer', 'user', 'items.product'])
            ->where('cash_session_id', $id)
            ->oldest()
            ->get();

        // Statistiques de la session
        $totalSalesCount = $sales->where('status', 'completed')->count();
        $totalSalesAmount = $sales->where('status', 'completed')->sum('total_amount');

        $totalCashSales = $sales->where('status', 'completed')->where('payment_method', 'cash')->sum('total_amount');
        $totalCardSales = $sales->where('status', 'completed')->where('payment_method', 'card')->sum('total_amount');
        $totalCreditSales = $sales->where('status', 'completed')->where('payment_method', 'credit')->sum('total_amount');

        $totalRefundsCount = $sales->where('status', 'returned')->count();
        $totalRefundsAmount = $sales->where('status', 'returned')->sum('total_amount');

        return view('admin.cash-sessions.show', compact(
            'session',
            'sales',
            'totalSalesCount',
            'totalSalesAmount',
            'totalCashSales',
            'totalCardSales',
            'totalCreditSales',
            'totalRefundsCount',
            'totalRefundsAmount'
        ));
    }
}
