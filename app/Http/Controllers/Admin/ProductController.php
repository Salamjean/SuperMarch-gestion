<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['supplier', 'creator']);
        if ($request->filled('category')) {
            $query->where('category_name', $request->get('category'));
        }
        $products = $query->latest()->get();
        $selectedCategory = $request->get('category');
        return view('admin.products.index', compact('products', 'selectedCategory'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'suppliers'));
    }

    public function checkBarcode(Request $request)
    {
        $barcode = $request->get('barcode');
        $exists = Product::where('reference', $barcode)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'barcode_value' => ['nullable', 'string', 'max:120', 'unique:products,reference'],
            'category_name' => ['required', 'string'],
            'supplier_id'   => ['nullable', 'exists:suppliers,id'],
            'price'           => ['required', 'numeric', 'min:0'],
            'stock'           => ['required', 'integer', 'min:0'],
            'stock_threshold' => ['required', 'integer', 'min:0'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description'     => ['nullable', 'string'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $reference = trim((string) ($validated['barcode_value'] ?? ''));

        if ($reference === '') {
            // Fallback: generation auto si aucun code-barres n'est fourni.
            $year = date('y');
            $lastProduct = Product::where('reference', 'LIKE', "PRD-{$year}-%")->latest()->first();
            $nextNumber = 1;
            if ($lastProduct) {
                $lastNumber = intval(substr($lastProduct->reference, 7));
                $nextNumber = $lastNumber + 1;
            }
            $reference = "PRD-{$year}-" . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        Product::create([
            'name'            => $validated['name'],
            'slug'            => Str::slug($validated['name']) . '-' . time(),
            'category_name'   => $validated['category_name'],
            'supplier_id'     => $validated['supplier_id'],
            'price'           => $validated['price'],
            'stock'           => $validated['stock'],
            'stock_threshold' => $validated['stock_threshold'],
            'image'           => $imagePath,
            'description'     => $validated['description'],
            'reference'       => $reference,
            'qr_code'         => $reference,
            'is_active'       => true,
            'created_by'      => auth()->id(),
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit ajouté avec succès.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get();
        $suppliers = Supplier::where('is_active', true)->get();
        return view('admin.products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'category_name' => ['required', 'string'],
            'supplier_id'   => ['nullable', 'exists:suppliers,id'],
            'price'           => ['required', 'numeric', 'min:0'],
            'stock_threshold' => ['required', 'integer', 'min:0'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description'     => ['nullable', 'string'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name'            => $validated['name'],
            'slug'            => Str::slug($validated['name']) . '-' . $product->id,
            'category_name'   => $validated['category_name'],
            'supplier_id'     => $validated['supplier_id'],
            'price'           => $validated['price'],
            'stock_threshold' => $validated['stock_threshold'],
            'description'     => $validated['description'],
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit mis à jour.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produit supprimé.');
    }

    public function thresholdIndex()
    {
        $products = Product::where(function ($query) {
            $query->whereColumn('stock', '<=', 'stock_threshold')
                  ->orWhereHas('restockRequests', function ($q) {
                      $q->where('status', 'pending');
                  });
        })
        ->with(['supplier', 'creator'])
        ->latest()
        ->get();
        return view('admin.products.threshold', compact('products'));
    }

    public function restockGrid()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->get();
        return view('admin.products.restock-grid', compact('products', 'categories'));
    }

    public function restock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1']
        ]);

        $initialStock = $product->stock;
        $addedStock = intval($request->quantity);

        $product->increment('stock', $addedStock);

        // Résoudre la requête en attente s'il y en a une, sinon créer une nouvelle entrée complétée
        $pendingRequest = \App\Models\RestockRequest::where('product_id', $product->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingRequest) {
            $pendingRequest->update([
                'status' => 'completed',
                'initial_stock' => $initialStock,
                'added_stock' => $addedStock
            ]);
        } else {
            \App\Models\RestockRequest::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'status' => 'completed',
                'initial_stock' => $initialStock,
                'added_stock' => $addedStock
            ]);
        }

        return response()->json([
            'success' => true,
            'new_stock' => $product->stock,
            'threshold' => $product->stock_threshold,
            'message' => "Le stock de {$product->name} a été réapprovisionné de {$request->quantity} unités. Nouveau stock : {$product->stock}."
        ]);
    }
}
