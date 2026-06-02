@extends('admin.layouts.app')

@section('title', 'Gestion Clients (Fidélité & Crédits)')
@section('page-title', 'Gestion Clientèle')

@section('content')

    <div class="list-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 class="list-title"
                style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-users" style="color: #6366f1;"></i> Gestion des Clients
            </h2>
            <p class="list-sub" style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0 0;">Visualisez les comptes
                fidélités et encours crédits de vos clients</p>
        </div>
        <a href="{{ route('admin.customers.create') }}" class="btn btn-yellow"
            style="padding: 10px 20px; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; background: #6366f1; border-color: #6366f1; color: white;">
            <i class="fa-solid fa-user-plus"></i> Nouveau client
        </a>
    </div>

    <!-- Rapid Stats CRM -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #e0e7ff; color: #6366f1; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-address-book"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Total
                    Fiches</span>
                <span style="font-size: 20px; font-weight: 800; color: #1e293b;">{{ $customers->total() }} Clients</span>
            </div>
        </div>

        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #fef3c7; color: #d97706; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Points
                    octroyés</span>
                <span style="font-size: 20px; font-weight: 800; color: #d97706;">⭐
                    {{ number_format(\App\Models\Customer::sum('loyalty_points'), 0, ',', ' ') }} pts</span>
            </div>
        </div>

        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #fee2e2; color: #dc2626; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Total
                    Crédits</span>
                <span
                    style="font-size: 20px; font-weight: 800; color: #dc2626;">{{ number_format(\App\Models\Customer::sum('debt_balance'), 0, ',', ' ') }}
                    FCFA</span>
            </div>
        </div>
    </div>

    <!-- Filtres Modernes -->
    <div class="card"
        style="margin-bottom: 25px; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body" style="padding: 20px;">
            <form method="GET" action="{{ route('admin.customers.index') }}">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; align-items: flex-end;">
                    <div class="form-group" style="margin: 0;">
                        <label
                            style="font-size: 11.5px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Rechercher
                            un client</label>
                        <div style="position: relative;">
                            <i class="fa-solid fa-magnifying-glass"
                                style="position: absolute; left: 12px; top: 12px; color: var(--text-muted);"></i>
                            <input type="text" name="search" class="form-control"
                                style="font-size: 13px; height: 40px; padding-left: 36px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;"
                                placeholder="Saisir un nom, téléphone ou email..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label
                            style="font-size: 11.5px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Filtrer
                            par situation</label>
                        <select name="has_debt" class="form-control"
                            style="font-size: 13px; height: 40px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;">
                            <option value="">Tous les clients</option>
                            <option value="yes" {{ request('has_debt') === 'yes' ? 'selected' : '' }}>Endettés (Crédit
                                actif)</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn btn-yellow"
                            style="height: 40px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px; flex: 1; background: #1e293b; border-color: #1e293b; color: white;">
                            Filtrer
                        </button>
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-gray"
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
            @if ($customers->isEmpty())
                <div class="empty-state" style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                    <div
                        style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fa-solid fa-user-slash" style="font-size: 24px; color: #94a3b8;"></i>
                    </div>
                    <p style="font-weight: 600; font-size: 15px; margin: 0; color: #334155;">Aucun client trouvé</p>
                    <p style="font-size: 13px; margin: 5px 0 0 0;">Modifiez vos critères de recherche ou enregistrez un
                        nouveau profil.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                        <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                            <th
                                style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                Client</th>
                            <th
                                style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                Coordonnées</th>
                            <th
                                style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                Fidélité</th>
                            <th
                                style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                Crédit actif</th>
                            <th
                                style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                Inscrit le</th>
                            <th
                                style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right; width: 180px;">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $cust)
                            <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                onmouseover="this.style.backgroundColor='#f8fafc'"
                                onmouseout="this.style.backgroundColor='transparent'">
                                <td style="padding: 14px 20px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div
                                            style="width: 38px; height: 38px; background: #eef2ff; color: #6366f1; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;">
                                            {{ strtoupper(substr($cust->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.customers.show', $cust->id) }}"
                                                style="font-weight: 700; color: #1e293b; text-decoration: none; font-size: 14px;">
                                                {{ $cust->name }}
                                            </a>
                                            <span style="display: block; font-size: 11px; color: var(--text-muted);">ID :
                                                #{{ $cust->id }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 14px 20px;">
                                    <span style="font-weight: 600; font-size: 13px; color: #334155; display: block;"><i
                                            class="fa-solid fa-phone"
                                            style="font-size: 10px; color: #94a3b8; margin-right: 4px;"></i>
                                        {{ $cust->phone ?? '—' }}</span>
                                    <span style="font-size: 11px; color: var(--text-muted); display: block;"><i
                                            class="fa-solid fa-envelope"
                                            style="font-size: 10px; color: #94a3b8; margin-right: 4px;"></i>
                                        {{ $cust->email ?? '— Non renseigné' }}</span>
                                </td>
                                <td style="padding: 14px 20px; text-align: center;">
                                    <span class="badge"
                                        style="background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 700; font-size: 12px; padding: 4px 8px; border-radius: 6px;">
                                        ⭐ {{ $cust->loyalty_points }} pts
                                    </span>
                                </td>
                                <td style="padding: 14px 20px; text-align: center;">
                                    @if ($cust->debt_balance > 0)
                                        <span
                                            style="color: #dc2626; font-weight: 800; font-size: 13.5px; background: #fee2e2; padding: 3px 8px; border-radius: 6px; border: 1px solid #fecdd3; display: inline-block;">
                                            {{ number_format($cust->debt_balance, 0, ',', ' ') }} FCFA
                                        </span>
                                    @else
                                        <span
                                            style="color: #15803d; font-weight: 600; font-size: 12px; background: #f0fdf4; padding: 3px 8px; border-radius: 6px; border: 1px solid #bbf7d0; display: inline-block;">
                                            Sain
                                        </span>
                                    @endif
                                </td>
                                <td
                                    style="padding: 14px 20px; text-align: center; color: var(--text-muted); font-size: 13px;">
                                    {{ $cust->created_at->format('d/m/Y') }}
                                </td>
                                <td style="padding: 14px 20px; text-align: right;">
                                    <div style="display: inline-flex; gap: 6px;">
                                        <a href="{{ route('admin.customers.show', $cust->id) }}" class="btn-icon"
                                            style="background-color: #f1f5f9; color: #475569; width: 32px; height: 38px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;"
                                            title="Profil & Historique">
                                            <i class="fa-solid fa-id-card"></i>
                                        </a>
                                        <a href="{{ route('admin.customers.edit', $cust->id) }}" class="btn-icon"
                                            style="background-color: #fef3c7; color: #d97706; width: 32px; height: 38px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;"
                                            title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        @if ($cust->debt_balance > 0)
                                            <button type="button" class="btn-icon"
                                                style="background-color: #ecfdf5; color: #059669; border: none; width: 32px; height: 38px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"
                                                title="Encaisser le crédit"
                                                onclick="openPayDebtModal('{{ $cust->id }}', '{{ addslashes($cust->name) }}', '{{ $cust->debt_balance }}')">
                                                <i class="fa-solid fa-hand-holding-dollar"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn-icon btn-icon-red"
                                            style="background-color: #fee2e2; color: #dc2626; border: none; width: 32px; height: 38px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"
                                            title="Supprimer"
                                            onclick="confirmDelete('{{ $cust->id }}', '{{ addslashes($cust->name) }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>

                                    <form id="delete-form-{{ $cust->id }}"
                                        action="{{ route('admin.customers.destroy', $cust->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>

                                    <form id="pay-debt-form-{{ $cust->id }}"
                                        action="{{ route('admin.customers.pay-debt', $cust->id) }}" method="POST"
                                        style="display:none;">
                                        @csrf
                                        <input type="hidden" name="amount" id="pay-debt-amount-{{ $cust->id }}">
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                <div style="padding: 15px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                    {{ $customers->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Supprimer ce client ?',
                    html: `Voulez-vous vraiment supprimer le client : <b>${name}</b> ?<br><span style="color:var(--danger); font-size:12px;">Cette opération est irréversible.</span>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Oui, supprimer !',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                });
            }

            function openPayDebtModal(id, name, maxDebt) {
                Swal.fire({
                    title: 'Règlement de crédit',
                    html: `Saisir le montant du remboursement apporté par <b>${name}</b> :<br><span style="font-size:12.5px;color:var(--text-muted);">Encours maximal : ${parseFloat(maxDebt).toLocaleString()} FCFA</span>`,
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
                        document.getElementById('pay-debt-amount-' + id).value = result.value;
                        document.getElementById('pay-debt-form-' + id).submit();
                    }
                });
            }
        </script>
    @endpush

@endsection
