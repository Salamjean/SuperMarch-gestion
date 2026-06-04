<?php

namespace App\Http\Controllers\Magasinier;

use App\Http\Controllers\Controller;
use App\Models\Product;

use Illuminate\Http\Request;
use App\Models\RestockRequest;

class MagasinierDashboardController extends Controller
{
    public function index()
    {
        $totalProductsCount = Product::count();
        $estimatedStockValue = Product::selectRaw('SUM(stock * price) as total')->first()->total ?? 0;
        
        $lowStockCount = Product::whereColumn('stock', '<=', 'stock_threshold')->count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'stock_threshold')
            ->orderBy('stock')
            ->take(5)
            ->get();

        $outOfStockCount = Product::where('stock', 0)->count();
        $categoriesCount = \App\Models\Category::count();
        $suppliersCount = \App\Models\Supplier::count();
        $pendingRequestsCount = RestockRequest::where('status', 'pending')->count();
        
        $recentRequests = RestockRequest::with(['product', 'user'])
            ->latest()
            ->take(5)
            ->get();

        $recentProducts = Product::latest()->take(5)->get();

        $totalStock = Product::sum('stock');

        $categoryStats = Product::select('category_name')
            ->selectRaw('count(*) as product_count')
            ->selectRaw('sum(stock) as total_stock')
            ->groupBy('category_name')
            ->orderByDesc('total_stock')
            ->get();

        $supplierStats = \App\Models\Supplier::withCount('products')
            ->orderByDesc('products_count')
            ->take(5)
            ->get();

        return view('magasinier.dashboard', compact(
            'totalProductsCount',
            'estimatedStockValue',
            'lowStockCount',
            'lowStockProducts',
            'outOfStockCount',
            'categoriesCount',
            'suppliersCount',
            'pendingRequestsCount',
            'recentRequests',
            'recentProducts',
            'categoryStats',
            'supplierStats',
            'totalStock'
        ));
    }

    public function restockRequestsIndex()
    {
        $requests = RestockRequest::with(['product', 'user'])
            ->latest()
            ->paginate(15);

        return view('magasinier.restock-requests.index', compact('requests'));
    }

    public function resolveRestock(Request $request, $id)
    {
        $restockRequest = RestockRequest::findOrFail($id);
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
            'added_stock' => $quantity
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

    public function showProfile()
    {
        $user = auth()->user();
        return view('magasinier.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed']
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'gender' => $validated['gender'] ?? null,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($validated['password']);
        }

        $user->update($data);

        return redirect()->route('magasinier.profile.show')
            ->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
