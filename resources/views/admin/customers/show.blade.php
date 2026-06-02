@extends('admin.layouts.app')

@section('title', 'Profil Client - ' . $customer->name)
@section('page-title', 'Profil du Client')

@section('content')

    <!-- En-tête Moderne -->
    <div
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 class="list-title"
                style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-address-card" style="color: #6366f1;"></i> Profil Client : {{ $customer->name }}
            </h2>
            <p class="list-sub" style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0 0;">Visualisez les
                transactions de fidélité, encours de crédit et historique d'achats</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.customers.index') }}" class="btn"
                style="height: 40px; padding: 0 16px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; background: white; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; font-size: 13px; transition: all 0.2s;"
                onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                <i class="fa-solid fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn"
                style="height: 40px; padding: 0 16px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; background: #6366f1; border: 1px solid #6366f1; color: white; text-decoration: none; font-size: 13px; transition: all 0.2s;"
                onmouseover="this.style.background='#4f46e5';" onmouseout="this.style.background='#6366f1';">
                <i class="fa-solid fa-pen"></i> Modifier la fiche
            </a>
        </div>
    </div>

    <!-- Mise en page adaptative -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)) 2.2fr; gap: 25px; margin-top: 20px;">

        <!-- Colonne Gauche : Identité & Infos Comptes -->
        <div style="display: flex; flex-direction: column; gap: 25px;">

            <!-- Fiche Identité Modernisée -->
            <div class="card"
                style="border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div class="card-body" style="padding: 25px;">
                    <div style="text-align: center; margin-bottom: 25px;">
                        <div
                            style="width: 72px; height: 72px; background: #eef2ff; color: #6366f1; border-radius: 50%; font-size: 26px; font-weight: 800; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; border: 4px solid #f5f3ff; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.03);">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                        <h3 style="margin: 0; font-size: 17px; font-weight: 800; color: #1e293b;">{{ $customer->name }}</h3>
                        <p style="margin: 4px 0 0 0; font-size: 12px; color: var(--text-muted); font-weight: 500;">
                            <i class="fa-regular fa-calendar-check" style="margin-right: 3px;"></i> Inscrit le
                            {{ $customer->created_at ? $customer->created_at->format('d/m/Y') : '—' }}
                        </p>
                    </div>

                    <div
                        style="border-top: 1px solid #f1f5f9; padding-top: 20px; display: flex; flex-direction: column; gap: 15px;">
                        <div>
                            <span
                                style="font-size: 10.5px; font-weight: 700; color: #64748b; display: block; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Téléphone</span>
                            <span
                                style="font-size: 13.5px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-phone" style="color: #94a3b8; font-size: 12px;"></i>
                                {{ $customer->phone ?? '— Non renseigné' }}
                            </span>
                        </div>
                        <div>
                            <span
                                style="font-size: 10.5px; font-weight: 700; color: #64748b; display: block; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Adresse
                                email</span>
                            <span
                                style="font-size: 13.5px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 6px; word-break: break-all;">
                                <i class="fa-solid fa-envelope" style="color: #94a3b8; font-size: 12px;"></i>
                                {{ $customer->email ?? '— Non renseigné' }}
                            </span>
                        </div>
                        <div>
                            <span
                                style="font-size: 10.5px; font-weight: 700; color: #64748b; display: block; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Adresse
                                physique</span>
                            <span
                                style="font-size: 13.5px; font-weight: 600; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-location-dot" style="color: #94a3b8; font-size: 12px;"></i>
                                {{ $customer->address ?? '— Non renseigné' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fiche Soldes & Fidélité -->
            <div class="card"
                style="border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                <div class="card-body" style="padding: 22px;">
                    <h3
                        style="margin: 0 0 18px; font-size: 13.5px; font-weight: 800; color: #1e293b; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; text-transform: uppercase; letter-spacing: 0.02em;">
                        <i class="fa-solid fa-piggy-bank" style="color: #6366f1;"></i> Status financier & Fidélité
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <!-- Fidélité (Points) -->
                        <div
                            style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span
                                    style="font-size: 10px; color:#15803d; font-weight:800; text-transform:uppercase; letter-spacing: 0.04em; display:block;">Fidélisation</span>
                                <span
                                    style="font-size: 18px; font-weight: 800; color:#15803d; display: flex; align-items: center; gap: 4px; margin-top: 2px;">
                                    ⭐ {{ $customer->loyalty_points }} <span
                                        style="font-size: 13px; font-weight: 600;">pts</span>
                                </span>
                            </div>
                            <button onclick="openAdjustPointsModal()" class="btn"
                                style="padding: 6px 12px; font-size: 11px; font-weight: 700; border-radius: 6px; background: white; border: 1px solid #bbf7d0; color: #15803d; cursor: pointer; transition: all 0.2s;"
                                onmouseover="this.style.background='#dcfce7';" onmouseout="this.style.background='white';">
                                Ajuster
                            </button>
                        </div>

                        <!-- Crédits / Dettes -->
                        <div
                            style="background: {{ $customer->debt_balance > 0 ? '#fdf2f2' : '#f8fafc' }}; border: 1px solid {{ $customer->debt_balance > 0 ? '#fecdd3' : '#e2e8f0' }}; border-radius: 10px; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span
                                    style="font-size: 10px; color: {{ $customer->debt_balance > 0 ? '#991b1b' : '#64748b' }}; font-weight:800; text-transform:uppercase; letter-spacing: 0.04em; display:block;">Dette
                                    (Crédit actif)</span>
                                <span
                                    style="font-size: 18px; font-weight: 800; color: {{ $customer->debt_balance > 0 ? '#dc2626' : '#1e293b' }}; display: block; margin-top: 2px;">
                                    {{ number_format($customer->debt_balance, 0, ',', ' ') }} <span
                                        style="font-size: 12px; font-weight:600;">FCFA</span>
                                </span>
                            </div>
                            @if ($customer->debt_balance > 0)
                                <button onclick="openPayDebtModal()" class="btn"
                                    style="padding: 6px 12px; font-size: 11px; font-weight: 700; border-radius: 6px; background: #dc2626; border: 1px solid #dc2626; color: white; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='#b91c1c';"
                                    onmouseout="this.style.background='#dc2626';">
                                    Encaisser
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne Droite : Historique des Ventes du Client -->
        <div class="card"
            style="border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02); height: fit-content;">
            <div
                style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-clock-rotate-left" style="color: #6366f1;"></i>
                    <h3
                        style="margin: 0; font-size: 14px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.02em;">
                        Historique des Commandes</h3>
                </div>
                <span
                    style="font-size: 11.5px; font-weight: 700; color: #475569; background: #e2e8f0; padding: 2px 8px; border-radius: 99px;">
                    {{ $sales->total() }} Transactions
                </span>
            </div>

            <div class="card-body" style="padding:0;">
                @if ($sales->isEmpty())
                    <div style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                        <div
                            style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #94a3b8; font-size: 18px;">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <p style="font-weight: 700; margin: 0; color: #475569; font-size:14px;">Aucune transaction
                            enregistrée</p>
                        <p style="font-size: 12.5px; margin-top: 5px;">Les commandes de ce client s'afficheront ici au fur
                            et à mesure.</p>
                    </div>
                @else
                    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Référence</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Date d'achat</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Type de Paiement</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">
                                    Total Facturé</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Statut</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center; width: 60px;">
                                    Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sales as $sale)
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f8fafc'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 12px 15px; font-size: 13px; font-weight: 700; color: #1e293b;">
                                        {{ $sale->reference }}
                                    </td>
                                    <td style="padding: 12px 15px; font-size: 12.5px; color: #64748b; text-align: center;">
                                        {{ $sale->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        @if ($sale->payment_method === 'cash')
                                            <span
                                                style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 700;"><i
                                                    class="fa-solid fa-money-bill-wave" style="margin-right: 3px;"></i>
                                                Espèces</span>
                                        @elseif ($sale->payment_method === 'card')
                                            <span
                                                style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 700;"><i
                                                    class="fa-solid fa-credit-card" style="margin-right: 3px;"></i>
                                                Carte</span>
                                        @elseif ($sale->payment_method === 'credit')
                                            <span
                                                style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 700;"><i
                                                    class="fa-solid fa-clock" style="margin-right: 3px;"></i>
                                                Crédit</span>
                                        @else
                                            <span
                                                style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 700;">{{ strtoupper($sale->payment_method) }}</span>
                                        @endif
                                    </td>
                                    <td
                                        style="padding: 12px 15px; font-weight: 700; font-size: 13px; color: #1e293b; text-align: right;">
                                        {{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        @if ($sale->status === 'completed')
                                            <span
                                                style="background: #eefdf4; color: #166534; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; border: 1px solid #bbf7d0;">Validée</span>
                                        @else
                                            <span
                                                style="background: #fdf2f2; color: #991b1b; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; border: 1px solid #fecdd3;">Annulée</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn-icon"
                                            style="background: #f1f5f9; color: #1e293b; width: 28px; height: 28px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;"
                                            title="Voir la Facture originale">
                                            <i class="fa-solid fa-eye" style="font-size: 12px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div
                        style="padding: 15px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                        {{ $sales->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Formulaires cachés pour actions directes -->
    <form id="adjust-points-form" action="{{ route('admin.customers.adjust-points', $customer->id) }}" method="POST"
        style="display:none;">
        @csrf
        <input type="hidden" name="operation" id="adjust-operation">
        <input type="hidden" name="points" id="adjust-points-vals">
    </form>

    <form id="pay-debt-direct-form" action="{{ route('admin.customers.pay-debt', $customer->id) }}" method="POST"
        style="display:none;">
        @csrf
        <input type="hidden" name="amount" id="pay-debt-direct-amount">
    </form>

    @push('scripts')
        <script>
            function openAdjustPointsModal() {
                Swal.fire({
                    title: 'Ajuster les points de fidélité',
                    html: `
                        <div style="text-align: left; font-family: 'Outfit', sans-serif;">
                            <div style="margin-bottom: 15px;">
                                <label style="font-weight:600; display:block; margin-bottom:5px;">Opération</label>
                                <select id="swal-adjust-operation" class="form-control" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);">
                                    <option value="add">Ajouter des points (+)</option>
                                    <option value="subtract">Retirer des points (-)</option>
                                </select>
                            </div>
                            <div>
                                <label style="font-weight:600; display:block; margin-bottom:5px;">Nombre de points</label>
                                <input type="number" id="swal-adjust-points" class="form-control" style="width:100%; padding:8px; border-radius:6px; border:1px solid var(--border);" placeholder="Ex: 50" min="1" required>
                            </div>
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Valider',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#eab308',
                    focusConfirm: false,
                    preConfirm: () => {
                        const operation = document.getElementById('swal-adjust-operation').value;
                        const points = parseInt(document.getElementById('swal-adjust-points').value || 0);

                        if (!points || points <= 0) {
                            Swal.showValidationMessage('Veuillez saisir un nombre de points correct et positif !');
                            return false;
                        }

                        return {
                            operation,
                            points
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('adjust-operation').value = result.value.operation;
                        document.getElementById('adjust-points-vals').value = result.value.points;
                        document.getElementById('adjust-points-form').submit();
                    }
                });
            }

            function openPayDebtModal() {
                const maxDebt = '{{ $customer->debt_balance }}';
                Swal.fire({
                    title: 'Règlement de crédit',
                    html: `Saisir le montant du remboursement apporté par <b>{{ addslashes($customer->name) }}</b> :<br><span style="font-size:12.5px;color:var(--text-muted);">Encours maximal : ${parseFloat(maxDebt).toLocaleString()} FCFA</span>`,
                    input: 'number',
                    inputPlaceholder: 'Montant versé en FCFA...',
                    showCancelButton: true,
                    confirmButtonText: 'Valider le paiement',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#059669',
                    inputValidator: (value) => {
                        if (!value || value <= 0) {
                            return 'Le montant doit être positif et non nul !'
                        }
                        if (parseFloat(value) > parseFloat(maxDebt)) {
                            return `Le montant ne peut pas dépasser la dette actuelle (${parseFloat(maxDebt).toLocaleString()} FCFA)`
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed && result.value) {
                        document.getElementById('pay-debt-direct-amount').value = result.value;
                        document.getElementById('pay-debt-direct-form').submit();
                    }
                });
            }
        </script>
    @endpush

@endsection
