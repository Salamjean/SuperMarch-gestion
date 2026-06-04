@extends('admin.layouts.app')

@section('title', 'Historique des Encaissements de Crédits')
@section('page-title', 'Encaissements de Crédits')

@section('content')

    <div class="list-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h2 class="list-title"
                style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-hand-holding-dollar" style="color: #6366f1;"></i> Encaissements de Crédits
            </h2>
            <p class="list-sub" style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0 0;">Visualisez l'historique complet des remboursements de crédits encaissés par vos caissiers</p>
        </div>
    </div>

    <!-- Filtres Modernes -->
    <div class="card"
        style="margin-bottom: 25px; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body" style="padding: 20px;">
            <form method="GET" action="{{ route('admin.debt-payments.index') }}">
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; align-items: flex-end;">
                    <div class="form-group" style="margin: 0;">
                        <label
                            style="font-size: 11.5px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase;">Rechercher un encaissement</label>
                        <div style="position: relative;">
                            <i class="fa-solid fa-magnifying-glass"
                                style="position: absolute; left: 12px; top: 12px; color: var(--text-muted);"></i>
                            <input type="text" name="search" class="form-control"
                                style="font-size: 13px; height: 40px; padding-left: 36px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none;"
                                placeholder="Saisir référence, nom du client ou nom du caissier..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="submit" class="btn btn-yellow"
                            style="height: 40px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 6px; flex: 1; background: #1e293b; border-color: #1e293b; color: white;">
                            Rechercher
                        </button>
                        <a href="{{ route('admin.debt-payments.index') }}" class="btn btn-gray"
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
            @if ($payments->isEmpty())
                <div class="empty-state" style="text-align: center; padding: 50px 20px; color: var(--text-muted);">
                    <div
                        style="width: 60px; height: 60px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px;">
                        <i class="fa-solid fa-receipt" style="font-size: 24px; color: #94a3b8;"></i>
                    </div>
                    <p style="font-weight: 600; font-size: 15px; margin: 0; color: #334155;">Aucun encaissement trouvé</p>
                    <p style="font-size: 13px; margin: 5px 0 0 0;">Aucun remboursement n'a été enregistré pour le moment.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                                <th style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">Référence</th>
                                <th style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">Date / Heure</th>
                                <th style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">Client</th>
                                <th style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">Caissier (Encaissé par)</th>
                                <th style="padding: 14px 20px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right; width: 180px;">Montant Versé</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f8fafc'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 14px 20px; font-size: 13.5px; font-weight: 700; color: #1e293b;">
                                        {{ $payment->reference }}
                                    </td>
                                    <td style="padding: 14px 20px; font-size: 13px; color: #475569;">
                                        {{ $payment->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td style="padding: 14px 20px;">
                                        <div style="font-weight: 700; color: #1e293b; font-size: 13.5px;">
                                            @if($payment->customer)
                                                <a href="{{ route('admin.customers.show', $payment->customer->id) }}" style="text-decoration: none; color: #6366f1;">
                                                    {{ $payment->customer->name }}
                                                </a>
                                            @else
                                                <span style="color: var(--text-muted);">— Client supprimé</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding: 14px 20px;">
                                        <span style="font-weight: 600; font-size: 13px; color: #334155;">
                                            <i class="fa-solid fa-user-circle" style="color: #94a3b8; margin-right: 4px;"></i>
                                            {{ $payment->user?->name ?? 'Système' }}
                                        </span>
                                    </td>
                                    <td style="padding: 14px 20px; text-align: right;">
                                        <span style="color: #16a34a; font-weight: 800; font-size: 14px; background: #f0fdf4; padding: 4px 10px; border-radius: 6px; border: 1px solid #bbf7d0; display: inline-block;">
                                            + {{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 15px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                    {{ $payments->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection
