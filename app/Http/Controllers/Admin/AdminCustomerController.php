<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filtre de dettes
        if ($request->get('has_debt') === 'yes') {
            $query->where('debt_balance', '>', 0);
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'loyalty_points' => 'nullable|integer|min:0',
            'debt_balance' => 'nullable|numeric|min:0'
        ]);

        Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'loyalty_points' => $request->input('loyalty_points', 0),
            'debt_balance' => $request->input('debt_balance', 0.00),
        ]);

        return redirect()->route('admin.customers.index')->with('success', 'Client créé avec succès.');
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'loyalty_points' => 'required|integer|min:0',
            'debt_balance' => 'required|numeric|min:0'
        ]);

        $customer->update($request->all());

        return redirect()->route('admin.customers.index')->with('success', 'Informations du client mises à jour.');
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        // Historique des ventes de ce client
        $sales = Sale::with(['user'])->where('customer_id', $id)->latest()->paginate(10);

        return view('admin.customers.show', compact('customer', 'sales'));
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Fiche client supprimée.');
    }

    // Ajustement de points de fidélité
    public function adjustPoints(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $request->validate([
            'operation' => 'required|in:add,subtract',
            'points' => 'required|integer|min:1'
        ]);

        if ($request->operation === 'add') {
            $customer->increment('loyalty_points', $request->points);
        } else {
            $customer->decrement('loyalty_points', min($customer->loyalty_points, $request->points));
        }

        return back()->with('success', 'Points de fidélité ajustés avec succès.');
    }

    // Remboursement d'une dette (Crédit)
    public function payDebt(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $amountToPay = min($customer->debt_balance, $request->amount);
        $customer->decrement('debt_balance', $amountToPay);

        return back()->with('success', 'Règlement de dette enregistré. La dette a diminué de ' . number_format($amountToPay, 0, ',', ' ') . ' FCFA.');
    }
}
