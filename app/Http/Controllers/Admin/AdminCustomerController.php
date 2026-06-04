<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            'debt_balance' => 'nullable|numeric|min:0'
        ]);

        Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
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

    // Ajustement manuel de la dette (Crédit)
    public function adjustDebt(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $request->validate([
            'operation' => 'required|in:add,subtract',
            'amount' => 'required|numeric|min:1'
        ]);

        if ($request->operation === 'add') {
            $customer->increment('debt_balance', $request->amount);
            $message = 'Dette augmentée de ' . number_format($request->amount, 0, ',', ' ') . ' FCFA.';
        } else {
            $amountToSubtract = min($customer->debt_balance, $request->amount);
            $customer->decrement('debt_balance', $amountToSubtract);
            $message = 'Dette diminuée de ' . number_format($amountToSubtract, 0, ',', ' ') . ' FCFA.';
        }

        return back()->with('success', 'Crédit/Dette ajusté avec succès. ' . $message);
    }

    // Remboursement d'une dette (Crédit)
    public function payDebt(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $amountToPay = min($customer->debt_balance, $request->amount);
        if ($amountToPay <= 0) {
            return back()->with('error', 'Ce client n\'a pas de dette active.');
        }

        DB::transaction(function () use ($customer, $amountToPay) {
            $customer->decrement('debt_balance', $amountToPay);

            $activeSession = \App\Models\CashSession::where('user_id', auth()->id())->where('status', 'open')->first();

            \App\Models\DebtPayment::create([
                'customer_id' => $customer->id,
                'user_id' => auth()->id(),
                'cash_session_id' => $activeSession ? $activeSession->id : null,
                'amount' => $amountToPay,
                'reference' => 'PAY-ADM-' . strtoupper(Str::random(8)),
                'payment_method' => 'cash'
            ]);
        });

        return back()->with('success', 'Règlement de dette enregistré. La dette a diminué de ' . number_format($amountToPay, 0, ',', ' ') . ' FCFA.');
    }

    // Basculer l'état du blocage de crédit
    public function toggleCreditBlock($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->is_credit_blocked = !$customer->is_credit_blocked;
        $customer->save();

        $status = $customer->is_credit_blocked ? 'bloqué pour les crédits' : 'autorisé à prendre des crédits';
        return back()->with('success', "Le client {$customer->name} est maintenant {$status}.");
    }

    // Liste des encaissements de crédit pour l'administrateur
    public function debtPaymentsIndex(Request $request)
    {
        $query = \App\Models\DebtPayment::with(['customer', 'user', 'cashSession'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%");
                })->orWhereHas('user', function($sub) use ($search) {
                    $sub->where('name', 'LIKE', "%{$search}%");
                })->orWhere('reference', 'LIKE', "%{$search}%");
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('admin.customers.debt-payments', compact('payments'));
    }
}
