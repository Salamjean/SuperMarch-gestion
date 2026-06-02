@extends('admin.layouts.app')

@section('title', 'Historique des Ventes')
@section('page-title', 'Historique des Ventes')

@section('content')

    <div class="list-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 class="list-title"
                style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-receipt" style="color: #6366f1;"></i> Historique des Ventes
            </h2>
            <p class="list-sub" style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0 0;">Parcourez, filtrez et
                auditez toutes les transactions enregistrées aux caisses</p>
        </div>
    </div>

    <!-- Rapid Stats Ventes -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #e0e7ff; color: #6366f1; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Total
                    Transactions</span>
                <span style="font-size: 20px; font-weight: 800; color: #1e293b;">{{ $sales->total() }} Ventes</span>
            </div>
        </div>

        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #ecfdf5; color: #059669; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-calculator"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Volume
                    Global</span>
                <span
                    style="font-size: 20px; font-weight: 800; color: #059669;">{{ number_format(\App\Models\Sale::where('status', 'completed')->sum('total_amount'), 0, ',', ' ') }}
                    FCFA</span>
            </div>
        </div>

        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #fee2e2; color: #dc2626; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-ban"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Retours
                    / Annulés</span>
                <span
                    style="font-size: 20px; font-weight: 800; color: #dc2626;">{{ \App\Models\Sale::where('status', 'returned')->count() }}
                    Annulé(s)</span>
            </div>
        </div>
    </div>

    <!-- Filtres Modernisés -->
    <div class="card"
        style="margin-bottom: 25px; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body" style="padding: 20px;">
            <form method="GET" action="{{ route('admin.sales.index') }}">
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px; align-items: flex-end;">
                    <div class="form-group" style="margin:0;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Recherche
                            Réf</label>
                        <input type="text" name="search" class="form-control"
                            style="font-size: 13px; height: 40px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none; padding: 10px;"
                            placeholder="SAL-XXXXXXXX..." value="{{ request('search') }}">
                    </div>

                    <div class="form-group" style="margin:0;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Caissier</label>
                        <select name="user_id" class="form-control"
                            style="font-size: 13px; height: 40px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;">
                            <option value="">Tous</option>
                            @foreach ($cashiers as $cashier)
                                <option value="{{ $cashier->id }}"
                                    {{ request('user_id') == $cashier->id ? 'selected' : '' }}>{{ $cashier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin:0;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Client</label>
                        <select name="customer_id" class="form-control"
                            style="font-size: 13px; height: 40px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;">
                            <option value="">Tous</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}"
                                    {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" style="margin:0;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Mode
                            Paiement</label>
                        <select name="payment_method" class="form-control"
                            style="font-size: 13px; height: 40px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;">
                            <option value="">Tous</option>
                            <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Espèces
                            </option>
                            <option value="card" {{ request('payment_method') === 'card' ? 'selected' : '' }}>Carte /
                                Mobile</option>
                            <option value="credit" {{ request('payment_method') === 'credit' ? 'selected' : '' }}>Crédit
                            </option>
                        </select>
                    </div>

                    <div class="form-group" style="margin:0;">
                        <label
                            style="font-size: 11px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Statut</label>
                        <select name="status" class="form-control"
                            style="font-size: 13px; height: 40px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;">
                            <option value="">Tous</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Complétée
                            </option>
                            <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Retournée
                            </option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 8px; grid-column: span 1;">
                        <button type="submit" class="btn btn-yellow"
                            style="height: 40px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px; flex: 1; background: #1e293b; border-color: #1e293b; color: white;">
                            Filtrer
                        </button>
                        <a href="{{ route('admin.sales.index') }}" class="btn btn-gray"
                            style="height: 40px; width: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: #e2e8f0; border: none; color: #475569;"
                            title="Réinitialiser">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table Modernisée -->
    <div class="card"
        style="border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body" style="padding:0;">
            @if ($sales->isEmpty())
                <div class="empty-state" style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                    <div
                        style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fa-solid fa-receipt" style="font-size: 24px; color: #94a3b8;"></i>
                    </div>
                    <p style="font-weight: 600; font-size: 15px; margin: 0; color: #334155;">Aucune transaction trouvée</p>
                    <p style="font-size: 13px; margin: 5px 0 0 0;">Ajustez vos filtres pour étendre votre recherche.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Référence</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Date / Heure</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Caissier</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Client associé</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Moyen Pay.</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">
                                    Total TTC</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center; width: 110px;">
                                    Statut</th>
                                <th
                                    style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right; width: 120px;">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f8fafc'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 14px 20px; font-weight: 800; color: #1e293b; font-size:13.5px;">
                                        {{ $sale->reference }}
                                    </td>
                                    <td style="padding: 14px 20px; color: var(--text-muted); font-size: 13px;">
                                        {{ $sale->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td style="padding: 14px 20px;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div
                                                style="width: 24px; height: 24px; background: #e0f2fe; color: #0284c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 10px;">
                                                {{ strtoupper(substr($sale->user->name ?? '?', 0, 1)) }}
                                            </div>
                                            <span
                                                style="font-size: 13.5px; font-weight: 500; color: #334155;">{{ $sale->user ? $sale->user->name : 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td style="padding: 14px 20px;">
                                        @if ($sale->customer)
                                            <div style="display: flex; align-items: center; gap: 6px;">
                                                <i class="fa-solid fa-id-card" style="font-size: 11px; color: #6366f1;"></i>
                                                <a href="{{ route('admin.customers.show', $sale->customer_id) }}"
                                                    style="font-weight: 600; color: #4f46e5; text-decoration: none; font-size:13px;">
                                                    {{ $sale->customer->name }}
                                                </a>
                                            </div>
                                        @else
                                            <span style="color: #94a3b8; font-size: 13px;">Passant Anonyme</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 20px; text-align: center;">
                                        @if ($sale->payment_method === 'cash')
                                            <span class="badge"
                                                style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 11px; border-radius: 6px; padding: 3px 6px;">
                                                <i class="fa-solid fa-money-bill-wave"></i> Espèces
                                            </span>
                                        @elseif ($sale->payment_method === 'card')
                                            <span class="badge"
                                                style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 11px; border-radius: 6px; padding: 3px 6px;">
                                                <i class="fa-solid fa-credit-card"></i> Carte / Mobile
                                            </span>
                                        @elseif ($sale->payment_method === 'credit')
                                            <span class="badge"
                                                style="background: #fffbeb; color: #9a3412; border: 1px solid #fde68a; font-size: 11px; border-radius: 6px; padding: 3px 6px;">
                                                <i class="fa-solid fa-hand-holding-dollar"></i> Crédit (Dette)
                                            </span>
                                        @else
                                            <span class="badge"
                                                style="background: #f1f5f9; color: #475569; font-size: 11px; border-radius: 6px; padding: 3px 6px;">
                                                {{ ucfirst($sale->payment_method) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td
                                        style="padding: 14px 20px; text-align: right; font-weight: 800; color: #0f172a; font-size: 14px;">
                                        {{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td style="padding: 14px 20px; text-align: center;">
                                        @if ($sale->status === 'completed')
                                            <span class="badge"
                                                style="background: #ecfdf5; color: #047857; font-weight: 700; border-radius: 6px; padding: 3.5px 8px; font-size: 11.5px;">Validée</span>
                                        @else
                                            <span class="badge"
                                                style="background: #fef2f2; color: #b91c1c; font-weight: 700; border-radius: 6px; padding: 3.5px 8px; font-size: 11.5px;">Annulée</span>
                                        @endif
                                    </td>
                                    <td style="padding: 14px 20px; text-align: right;">
                                        <div style="display: inline-flex; gap: 6px;">
                                            <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn-icon"
                                                style="background: #f1f5f9; color: #475569; width: 32px; height: 35px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;"
                                                title="Facture & Impression">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                            @if ($sale->status === 'completed')
                                                <button type="button" class="btn-icon"
                                                    style="background: #fee2e2; color: #dc2626; border: none; width: 32px; height: 35px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"
                                                    title="Rembourser la transaction"
                                                    onclick="confirmRefund('{{ $sale->id }}', '{{ $sale->reference }}')">
                                                    <i class="fa-solid fa-arrow-rotate-left"></i>
                                                </button>
                                                <form id="refund-form-{{ $sale->id }}"
                                                    action="{{ route('admin.sales.refund', $sale->id) }}" method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 15px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                    {{ $sales->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmRefund(id, ref) {
                Swal.fire({
                    title: 'Annuler cette vente ?',
                    html: `Voulez-vous vraiment annuler la vente <b>#${ref}</b> ?<br><br><span style="color:var(--danger); font-size:13px;">Les articles de cette facture seront automatiquement restitués en stock. Les éventuels points fidélités accordés ou dettes enregistrées pour ce client seront réajustés immédiatement.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Oui, annuler & rembourser',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('refund-form-' + id).submit();
                    }
                });
            }
        </script>
    @endpush

@endsection
