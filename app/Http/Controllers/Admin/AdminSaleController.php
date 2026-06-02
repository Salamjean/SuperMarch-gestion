<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'customer', 'items.product'])->latest();

        // Filtre par caissier
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filtre par client
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filtre par mode de paiement
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par date
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Recherche par référence
        if ($request->filled('search')) {
            $query->where('reference', 'LIKE', '%' . $request->search . '%');
        }

        $sales = $query->paginate(15)->withQueryString();

        $cashiers = User::where('role', 'employee')->orWhere('role', 'admin')->get();
        $customers = Customer::orderBy('name')->get();

        return view('admin.sales.index', compact('sales', 'cashiers', 'customers'));
    }

    public function show($id)
    {
        $sale = Sale::with(['user', 'customer', 'items.product'])->findOrFail($id);
        return view('admin.sales.show', compact('sale'));
    }

    public function refund($id)
    {
        return DB::transaction(function () use ($id) {
            $sale = Sale::with(['items', 'customer'])->findOrFail($id);

            if ($sale->status === 'returned') {
                return back()->with('error', 'Cette vente a déjà été remboursée ou retournée.');
            }

            // Remettre en stock les produits concernés
            foreach ($sale->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->quantity);
                }
                $item->update([
                    'returned_quantity' => $item->quantity
                ]);
            }

            // Gérer la fidélité et la dette si client associé
            if ($sale->customer_id && $sale->customer) {
                $customer = $sale->customer;

                // Déduire les points gagnés lors de cette vente
                $points = floor($sale->total_amount / 10);
                if ($points > 0) {
                    $customer->decrement('loyalty_points', min($customer->loyalty_points, $points));
                }

                // Si c'était à crédit, on réduit la dette du client
                if ($sale->payment_method === 'credit') {
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

            return redirect()->route('admin.sales.index')->with('success', 'La vente a été annulée et stockée à nouveau.');
        });
    }
}
