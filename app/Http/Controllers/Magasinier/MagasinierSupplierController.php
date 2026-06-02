<?php

namespace App\Http\Controllers\Magasinier;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class MagasinierSupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->get();
        return view('magasinier.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('magasinier.suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'email'          => ['nullable', 'email', 'unique:suppliers,email'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'address'        => ['nullable', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:100'],
            'website'        => ['nullable', 'url', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        Supplier::create([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('magasinier.suppliers.index')
            ->with('success', 'Fournisseur créé avec succès.');
    }

    public function edit(Supplier $supplier)
    {
        return view('magasinier.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:150'],
            'email'          => ['nullable', 'email', 'unique:suppliers,email,' . $supplier->id],
            'phone'          => ['nullable', 'string', 'max:20'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'address'        => ['nullable', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:100'],
            'website'        => ['nullable', 'url', 'max:255'],
            'notes'          => ['nullable', 'string', 'max:1000'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $supplier->update([
            ...$validated,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('magasinier.suppliers.index')
            ->with('success', 'Fournisseur mis à jour.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('magasinier.suppliers.index')
            ->with('success', 'Fournisseur supprimé.');
    }
}
