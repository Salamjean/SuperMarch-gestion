@extends('admin.layouts.app')

@section('title', 'Audit Caisse #' . $session->id)
@section('page-title', 'Rapport d\'Audit de Caisse')

@section('content')

    <!-- En-tête Moderne -->
    <div
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                <span
                    style="background: #eef2ff; color: #6366f1; padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid #e0e7ff;">
                    ID #{{ $session->id }}
                </span>
                <span
                    style="background: {{ $session->status === 'open' ? '#f0fdf4' : '#f1f5f9' }}; color: {{ $session->status === 'open' ? '#166534' : '#475569' }}; padding: 4px 10px; border-radius: 99px; font-size: 11px; font-weight: 700; text-transform: uppercase; border: 1px solid {{ $session->status === 'open' ? '#bbf7d0' : '#e2e8f0' }};">
                    {{ $session->status === 'open' ? 'Session Ouverte' : 'Session Clôturée' }}
                </span>
            </div>
            <h2 class="list-title"
                style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-file-invoice-dollar" style="color: #6366f1;"></i> Audit de Session de Caisse
            </h2>
            <p class="list-sub" style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0 0;">
                Tenu par : <strong style="color: #475569;"><i class="fa-solid fa-user-tie" style="margin-right: 3px;"></i>
                    {{ $session->user ? $session->user->name : 'Inconnu' }}</strong>
            </p>
        </div>
        <div style="display: flex; gap: 10px;" class="no-print">
            <a href="{{ route('admin.cash-sessions.index') }}" class="btn"
                style="height: 40px; padding: 0 16px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; background: white; border: 1px solid #cbd5e1; color: #475569; text-decoration: none; font-size: 13.5px; transition: all 0.2s;"
                onmouseover="this.style.background='#f8fafc';" onmouseout="this.style.background='white';">
                <i class="fa-solid fa-arrow-left"></i> Retour au suivi
            </a>
            <button onclick="window.print()" class="btn"
                style="height: 40px; padding: 0 16px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; background: #6366f1; border: 1px solid #6366f1; color: white; font-size: 13.5px; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.background='#4f46e5';" onmouseout="this.style.background='#6366f1';">
                <i class="fa-solid fa-print"></i> Imprimer l'Audit
            </button>
        </div>
    </div>

    <!-- KPI Statistiques Cards -->
    <div
        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #eef2ff; color: #6366f1; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Fond
                    d'Ouverture</span>
                <span
                    style="font-size: 18px; font-weight: 800; color: #1e293b;">{{ number_format($session->opening_balance, 0, ',', ' ') }}
                    FCFA</span>
            </div>
        </div>

        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #f0fdf4; color: #16a34a; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Ventes
                    Actives ({{ $totalSalesCount }})</span>
                <span
                    style="font-size: 18px; font-weight: 800; color: #16a34a;">{{ number_format($totalSalesAmount, 0, ',', ' ') }}
                    FCFA</span>
            </div>
        </div>

        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            <div
                style="background: #fdf2f8; color: #db2777; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Remboursements</span>
                <span
                    style="font-size: 18px; font-weight: 800; color: #db2777;">{{ number_format($totalRefundsAmount, 0, ',', ' ') }}
                    FCFA</span>
            </div>
        </div>

        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px;">
            @php
                $isDiffOk = $session->status === 'open' || $session->difference == 0;
                $isPlus = !$isDiffOk && $session->difference > 0;
            @endphp
            <div
                style="background: {{ $isDiffOk ? '#f0fdf4' : ($isPlus ? '#eff6ff' : '#fdf2f2') }}; color: {{ $isDiffOk ? '#16a34a' : ($isPlus ? '#2563eb' : '#dc2626') }}; width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span
                    style="font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block;">Écart
                    Constaté</span>
                @if ($session->status === 'open')
                    <span style="font-size: 14px; font-weight: 700; color: #64748b; font-style: italic;">En cours...</span>
                @else
                    <span
                        style="font-size: 18px; font-weight: 800; color: {{ $session->difference >= 0 ? '#16a34a' : '#dc2626' }};">
                        {{ $session->difference > 0 ? '+' : '' }}{{ number_format($session->difference, 0, ',', ' ') }}
                        FCFA
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Panneau à deux colonnes -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 25px; margin-bottom: 25px;"
        class="audit-row">

        <!-- Recette attends (Théorique) -->
        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
            <div
                style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 15px 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-calculator" style="color: #6366f1;"></i>
                <h3
                    style="margin: 0; font-size: 14.5px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.02em;">
                    Recette Attendue (Système)</h3>
            </div>
            <div style="padding: 20px;">
                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr style="border-bottom: 1px dashed #f1f5f9;">
                            <td style="padding: 12px 0; font-size: 13.5px; color: #64748b; font-weight: 500;">Fond d'ouverture
                                initial</td>
                            <td style="padding: 12px 0; font-size: 14px; font-weight: 700; color: #1e293b; text-align: right;">
                                {{ number_format($session->opening_balance, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr style="border-bottom: 1px dashed #f1f5f9;">
                            <td style="padding: 12px 0; font-size: 13.5px; color: #16a34a; font-weight: 600;"><i
                                    class="fa-solid fa-cash-register" style="font-size: 11px; margin-right: 5px;"></i> (+)
                                Ventes Espèces (Cash)</td>
                            <td style="padding: 12px 0; font-size: 14px; font-weight: 800; color: #16a34a; text-align: right;">
                                +{{ number_format($totalCashSales, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr style="border-bottom: 1px dashed #f1f5f9;">
                            <td style="padding: 12px 0; font-size: 13.5px; color: #2563eb; font-weight: 600;"><i
                                    class="fa-solid fa-credit-card" style="font-size: 11px; margin-right: 5px;"></i> (+) Ventes
                                Carte / Mobile Money</td>
                            <td style="padding: 12px 0; font-size: 14px; font-weight: 800; color: #2563eb; text-align: right;">
                                +{{ number_format($totalCardSales, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr style="border-bottom: 1px dashed #f1f5f9;">
                            <td style="padding: 12px 0; font-size: 13.5px; color: #d97706; font-weight: 600;"><i
                                    class="fa-solid fa-circle-exclamation" style="font-size: 11px; margin-right: 5px;"></i> (+)
                                Ventes Crédit client (Dette)</td>
                            <td style="padding: 12px 0; font-size: 14px; font-weight: 800; color: #d97706; text-align: right;">
                                +{{ number_format($totalCreditSales, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr style="border-bottom: 1px dashed #f1f5f9;">
                            <td style="padding: 12px 0; font-size: 13.5px; color: #db2777; font-weight: 600;"><i
                                    class="fa-solid fa-rotate-left" style="font-size: 11px; margin-right: 5px;"></i> (-) Retours
                                & Remboursements</td>
                            <td style="padding: 12px 0; font-size: 14px; font-weight: 800; color: #db2777; text-align: right;">
                                -{{ number_format($totalRefundsAmount, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr style="border-bottom: 1px dashed #f1f5f9;">
                            <td style="padding: 12px 0; font-size: 13.5px; color: #16a34a; font-weight: 600;"><i
                                    class="fa-solid fa-hand-holding-dollar" style="font-size: 11px; margin-right: 5px;"></i> (+) Encaissements Crédits (Remboursements)</td>
                            <td style="padding: 12px 0; font-size: 14px; font-weight: 800; color: #16a34a; text-align: right;">
                                +{{ number_format($totalDebtPayments, 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr style="border-top: 2px solid #cbd5e1;">
                            <td style="padding: 14px 0 0 0; font-size: 14px; font-weight: 800; color: #1e293b;">Solde Théorique
                                de Clôture</td>
                            <td
                                style="padding: 14px 0 0 0; font-size: 16px; font-weight: 800; color: #6366f1; text-align: right;">
                                {{ number_format($session->expected_closing_balance ?? ($session->opening_balance + $totalSalesAmount + $totalDebtPayments), 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            </div>
        </div>

        <!-- Réel & Écart (Déclaration) -->
        <div
            style="background: white; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
            <div
                style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 15px 20px; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-vault" style="color: #6366f1;"></i>
                <h3
                    style="margin: 0; font-size: 14.5px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.02em;">
                    Déclaration Réelle & Écart</h3>
            </div>

            <div
                style="padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between; gap: 15px;">
                @if ($session->status === 'open')
                    <div style="text-align: center; padding: 30px; color: var(--text-muted); margin: auto;">
                        <div
                            style="width: 54px; height: 54px; background: #f0fdf4; color: #16a34a; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 20px;">
                            <i class="fa-solid fa-lock-open"></i>
                        </div>
                        <p style="font-weight: 700; font-size: 14.5px; margin: 0; color: #1e293b;">Session de caisse active
                        </p>
                        <p style="font-size: 12.5px; margin: 5px 0 0 0; max-width: 280px; margin-inline: auto;">
                            Les chiffres réels et les écarts apparaîtront à la clôture de caisse par l'agent depuis son
                            terminal.
                        </p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr style="border-bottom: 1px dashed #f1f5f9;">
                                <td style="padding: 12px 0; font-size: 13.5px; color: #64748b; font-weight: 500;">Montant
                                    physique déclaré</td>
                                <td
                                    style="padding: 12px 0; font-size: 16px; font-weight: 800; color: #1e293b; text-align: right;">
                                    {{ number_format($session->actual_closing_balance, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr style="border-bottom: 1px dashed #f1f5f9;">
                                <td style="padding: 12px 0; font-size: 13.5px; color: #64748b; font-weight: 500;">Solde attendu
                                    (Théorique)</td>
                                <td
                                    style="padding: 12px 0; font-size: 14px; font-weight: 600; color: #475569; text-align: right;">
                                    {{ number_format($session->expected_closing_balance, 0, ',', ' ') }} FCFA</td>
                            </tr>
                            <tr style="border-top: 2px solid #cbd5e1;">
                                <td style="padding: 14px 0 0 0; font-size: 14px; font-weight: 800; color: #1e293b;">Écart net
                                    audité</td>
                                <td style="padding: 14px 0 0 0; text-align: right;">
                                    @if ($session->difference == 0)
                                        <span
                                            style="font-size: 16px; font-weight: 800; color: #16a34a; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 2px 10px; border-radius: 6px;">
                                            <i class="fa-solid fa-circle-check"></i> Équilibrée (0)
                                        </span>
                                    @elseif ($session->difference > 0)
                                        <span
                                            style="font-size: 16px; font-weight: 800; color: #2563eb; background: #eff6ff; border: 1px solid #bfdbfe; padding: 2px 10px; border-radius: 6px;">
                                            <i class="fa-solid fa-circle-plus"></i>
                                            +{{ number_format($session->difference, 0, ',', ' ') }} FCFA
                                        </span>
                                    @else
                                        <span
                                            style="font-size: 16px; font-weight: 800; color: #dc2626; background: #fdf2f2; border: 1px solid #fecdd3; padding: 2px 10px; border-radius: 6px;">
                                            <i class="fa-solid fa-circle-minus"></i>
                                            {{ number_format($session->difference, 0, ',', ' ') }} FCFA
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div
                        style="background: #f8fafc; border: 1px solid var(--border); padding: 12px; border-radius: 8px; font-size: 12px; color: #475569; border-left: 4px solid #6366f1;">
                        <i class="fa-solid fa-circle-info" style="color: #6366f1; margin-right: 3px;"></i>
                        Un <strong>déficit</strong> nécessite l'explication de l'agent. Un <strong>excédent</strong>
                        correspond généralement à de la monnaie excédentaire non déclarée.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Journal des ventes de la session -->
    <div class="card"
        style="border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div
            style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-clock-rotate-left" style="color: #6366f1;"></i>
                <h3
                    style="margin: 0; font-size: 14.5px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.02em;">
                    Journal des Transactions</h3>
            </div>
            <span
                style="font-size: 11.5px; font-weight: 700; color: #475569; background: #e2e8f0; padding: 2px 8px; border-radius: 99px;">
                {{ $sales->count() }} Transactions
            </span>
        </div>
        <div class="card-body" style="padding:0;">
            @if ($sales->isEmpty())
                <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                    <div
                        style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #94a3b8; font-size: 18px;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <p style="font-weight: 600; margin: 0; color: #475569;">Aucune transaction</p>
                    <p style="font-size: 12.5px; margin-top: 3px;">Aucune vente n'a été enregistrée durant ce cycle.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Référence</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Heure</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Client</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Mode Paiement</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">
                                    Total à Payer</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">
                                    Encaissé</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">
                                    Rendu / Dette</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Statut</th>
                                <th style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center; width: 60px;"
                                    class="no-print">Actions</th>
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
                                        {{ $sale->created_at->format('H:i') }}
                                    </td>
                                    <td style="padding: 12px 15px; font-size: 13px; color: #475569;">
                                        @if ($sale->customer)
                                            <div style="display: flex; align-items: center; gap: 6px;">
                                                <div
                                                    style="width: 20px; height: 28px; display: inline-flex; align-items: center;">
                                                    <i class="fa-solid fa-user" style="color: #6366f1; font-size: 11px;"></i>
                                                </div>
                                                <strong>{{ $sale->customer->name }}</strong>
                                            </div>
                                        @else
                                            <span style="color: #94a3b8; font-style: italic;">Passant anonyme</span>
                                        @endif
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
                                                    class="fa-solid fa-clock" style="margin-right: 3px;"></i> Crédit</span>
                                        @else
                                            <span
                                                style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 700;">{{ strtoupper($sale->payment_method) }}</span>
                                        @endif
                                    </td>
                                    <td
                                        style="padding: 12px 15px; font-weight: 700; font-size: 13px; color: #1e293b; text-align: right;">
                                        {{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td style="padding: 12px 15px; font-size: 13px; color: #475569; text-align: right;">
                                        {{ number_format($sale->amount_received, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td style="padding: 12px 15px; font-weight: 600; font-size: 12.5px; text-align: right;">
                                        @if ($sale->payment_method === 'credit')
                                            <span
                                                style="color:#dc2626; background: #fdf2f2; border: 1px solid #fecdd3; padding: 1px 6px; border-radius: 4px;">Dette:
                                                {{ number_format(max(0, $sale->total_amount - $sale->amount_received), 0, ',', ' ') }}
                                                FCFA</span>
                                        @else
                                            <span
                                                style="color: #475569;">{{ number_format($sale->change_amount, 0, ',', ' ') }}
                                                FCFA</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        @if ($sale->status === 'completed')
                                            <span
                                                style="background: #eefdf4; color: #166534; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; border: 1px solid #bbf7d0;">Complétée</span>
                                        @else
                                            <span
                                                style="background: #fdf2f2; color: #991b1b; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 4px; border: 1px solid #fecdd3;">Annulée</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;" class="no-print">
                                        <a href="{{ route('admin.sales.show', $sale->id) }}" class="btn-icon"
                                            style="background: #f1f5f9; color: #1e293b; width: 28px; height: 28px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;"
                                            title="Voir la Factre originale">
                                            <i class="fa-solid fa-eye" style="font-size: 12px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
            @endif
        </div>
    </div>

    <!-- Journal des Encaissements de Crédits de la session -->
    <div class="card"
        style="border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-top: 25px;">
        <div
            style="background: #f8fafc; border-bottom: 1px solid var(--border); padding: 15px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-hand-holding-dollar" style="color: #6366f1;"></i>
                <h3
                    style="margin: 0; font-size: 14.5px; font-weight: 800; color: #1e293b; text-transform: uppercase; letter-spacing: 0.02em;">
                    Encaissements de Crédits durant la Session</h3>
            </div>
            <span
                style="font-size: 11.5px; font-weight: 700; color: #475569; background: #e2e8f0; padding: 2px 8px; border-radius: 99px;">
                {{ $debtPayments->count() }} Encaissements
            </span>
        </div>
        <div class="card-body" style="padding:0;">
            @if ($debtPayments->isEmpty())
                <div style="text-align: center; padding: 40px 20px; color: var(--text-muted);">
                    <div
                        style="width: 48px; height: 48px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #94a3b8; font-size: 18px;">
                        <i class="fa-solid fa-circle-exclamation"></i>
                    </div>
                    <p style="font-weight: 600; margin: 0; color: #475569;">Aucun encaissement</p>
                    <p style="font-size: 12.5px; margin-top: 3px;">Aucun remboursement de crédit n'a été perçu durant cette session.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                                <th style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">Référence</th>
                                <th style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">Heure</th>
                                <th style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">Client</th>
                                <th style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">Mode</th>
                                <th style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: right;">Montant Encaissé</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($debtPayments as $payment)
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f8fafc'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 12px 15px; font-size: 13px; font-weight: 700; color: #1e293b;">
                                        {{ $payment->reference }}
                                    </td>
                                    <td style="padding: 12px 15px; font-size: 12.5px; color: #64748b; text-align: center;">
                                        {{ $payment->created_at->format('H:i') }}
                                    </td>
                                    <td style="padding: 12px 15px; font-size: 13px; color: #475569;">
                                        @if ($payment->customer)
                                            <strong>{{ $payment->customer->name }}</strong>
                                        @else
                                            <span style="color: #94a3b8; font-style: italic;">Client inconnu/supprimé</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        <span style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 700;">Espèces</span>
                                    </td>
                                    <td style="padding: 12px 15px; font-weight: 700; font-size: 13.5px; color: #16a34a; text-align: right;">
                                        +{{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            @media print {
                .no-print {
                    display: none !important;
                }

                .sidebar,
                .navbar,
                .main-header {
                    display: none !important;
                }

                .content-wrapper {
                    margin-left: 0 !important;
                    padding: 0 !important;
                }

                .audit-row {
                    display: grid !important;
                    grid-template-columns: 1fr 1fr !important;
                }

                .audit-row .card {
                    box-shadow: none !important;
                    border: 1px solid #cbd5e1 !important;
                }
            }
        </style>
    @endpush

@endsection
