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
        $products = Product::latest()->get();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'stock_threshold')
            ->orderBy('stock')
            ->get();

        return view('magasinier.dashboard', compact('products', 'lowStockProducts'));
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
