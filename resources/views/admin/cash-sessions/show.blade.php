@extends('admin.layouts.app')

@section('title', 'Audit Caisse #' . $session->id)
@section('page-title', 'Rapport d\'Audit de Caisse')

@push('styles')
    <style>
        .audit-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .kpi-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .kpi-label {
            font-size: 10.5px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
            margin-bottom: 3px;
        }

        .kpi-value {
            font-size: 17px;
            font-weight: 800;
            color: #1e293b;
            display: block;
        }

        .audit-panels {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 22px;
            margin-bottom: 24px;
        }

        @media (max-width: 900px) {
            .audit-panels {
                grid-template-columns: 1fr;
            }
        }

        .panel-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .panel-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .panel-header h3 {
            margin: 0;
            font-size: 13.5px;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .panel-body {
            padding: 20px;
        }

        .audit-row-table {
            width: 100%;
            border-collapse: collapse;
        }

        .audit-row-table tr {
            border-bottom: 1px dashed #f1f5f9;
        }

        .audit-row-table tr:last-child {
            border-bottom: none;
        }

        .audit-row-table td {
            padding: 11px 0;
            font-size: 13.5px;
        }

        .audit-row-table td:last-child {
            text-align: right;
            font-weight: 700;
            color: #1e293b;
            font-size: 13.5px;
        }

        .audit-total-row td {
            padding-top: 14px !important;
            border-top: 2px solid #cbd5e1 !important;
            border-bottom: none !important;
        }

        .section-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 22px;
        }

        .section-header {
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        .section-header-left {
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .section-header h3 {
            margin: 0;
            font-size: 13.5px;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-count {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            background: #e2e8f0;
            padding: 2px 9px;
            border-radius: 99px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .data-table thead tr {
            background: #f8fafc;
            border-bottom: 1px solid var(--border);
        }

        .data-table th {
            padding: 11px 15px;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        .data-table td {
            padding: 11px 15px;
            font-size: 13px;
            color: #475569;
            vertical-align: middle;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-icon {
            width: 46px;
            height: 46px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            color: #94a3b8;
            font-size: 18px;
        }

        .badge-paiement {
            font-size: 11px;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-status {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 4px;
        }

        .info-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 18px;
            margin-bottom: 24px;
            align-items: center;
        }

        .info-meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #475569;
        }

        .info-meta-item i {
            color: #6366f1;
            font-size: 12px;
        }

        .info-meta-item strong {
            color: #1e293b;
        }

        .info-meta-sep {
            width: 1px;
            height: 16px;
            background: var(--border);
        }

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

            .audit-panels {
                grid-template-columns: 1fr 1fr !important;
            }
        }
    </style>
@endpush

@section('content')

    {{-- ═══════════════════ EN-TÊTE ═══════════════════ --}}
    <div
        style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:14px;">
        <div>
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                <span
                    style="background:#eef2ff; color:#6366f1; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:700; text-transform:uppercase; border:1px solid #e0e7ff;">
                    ID #{{ $session->id }}
                </span>
                @if($session->status === 'open')
                    <span
                        style="background:#f0fdf4; color:#166534; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:700; text-transform:uppercase; border:1px solid #bbf7d0;">
                        <i class="fa-solid fa-lock-open" style="font-size:10px;"></i> Session Ouverte
                    </span>
                @else
                    <span
                        style="background:#f1f5f9; color:#475569; padding:3px 10px; border-radius:99px; font-size:11px; font-weight:700; text-transform:uppercase; border:1px solid #e2e8f0;">
                        <i class="fa-solid fa-lock" style="font-size:10px;"></i> Session Clôturée
                    </span>
                @endif
            </div>
            <h2
                style="font-size:21px; font-weight:800; color:#1e293b; margin:0 0 3px 0; display:flex; align-items:center; gap:9px;">
                <i class="fa-solid fa-file-invoice-dollar" style="color:#6366f1;"></i>
                Audit de Session de Caisse
            </h2>
            <p style="font-size:13px; color:var(--text-muted); margin:0;">
                Rapport complet des transactions et de la trésorerie
            </p>
        </div>
        <div style="display:flex; gap:10px;" class="no-print">
            <a href="{{ route('admin.cash-sessions.index') }}"
                style="height:38px; padding:0 16px; border-radius:8px; font-weight:600; display:inline-flex; align-items:center; gap:7px; background:white; border:1px solid #cbd5e1; color:#475569; text-decoration:none; font-size:13px;">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
            <!-- <button onclick="window.print()"
                style="height:38px; padding:0 16px; border-radius:8px; font-weight:600; display:inline-flex; align-items:center; gap:7px; background:#6366f1; border:none; color:white; font-size:13px; cursor:pointer;">
                <i class="fa-solid fa-print"></i> Imprimer
            </button> -->
        </div>
    </div>

    {{-- ═══════════════════ MÉTA-INFOS ═══════════════════ --}}
    <div class="info-meta">
        <div class="info-meta-item">
            <i class="fa-solid fa-user-tie"></i>
            Caissier : <strong>{{ $session->user ? $session->user->name : 'Inconnu' }}</strong>
        </div>
        <div class="info-meta-sep"></div>
        <div class="info-meta-item">
            <i class="fa-solid fa-calendar-plus"></i>
            Ouverture : <strong>{{ $session->opened_at ? $session->opened_at->format('d/m/Y à H:i') : '—' }}</strong>
        </div>
        @if($session->status === 'closed')
            <div class="info-meta-sep"></div>
            <div class="info-meta-item">
                <i class="fa-solid fa-calendar-check"></i>
                Clôture : <strong>{{ $session->closed_at ? $session->closed_at->format('d/m/Y à H:i') : '—' }}</strong>
            </div>
            @php
                $duration = $session->opened_at && $session->closed_at
                    ? $session->opened_at->diffForHumans($session->closed_at, true)
                    : null;
            @endphp
            @if($duration)
                <div class="info-meta-sep"></div>
                <div class="info-meta-item">
                    <i class="fa-solid fa-clock"></i>
                    Durée : <strong>{{ $duration }}</strong>
                </div>
            @endif
        @endif
        <div class="info-meta-sep"></div>
        <div class="info-meta-item">
            <i class="fa-solid fa-receipt"></i>
            <strong>{{ $totalSalesCount }}</strong> vente(s) &nbsp;|&nbsp; <strong>{{ $debtPayments->count() }}</strong>
            encaissement(s)
        </div>
    </div>

    {{-- ═══════════════════ KPI CARDS ═══════════════════ --}}
    <div class="audit-grid">

        {{-- Fond d'ouverture --}}
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#eef2ff; color:#6366f1;">
                <i class="fa-solid fa-wallet"></i>
            </div>
            <div>
                <span class="kpi-label">Fond d'Ouverture</span>
                <span class="kpi-value">{{ number_format($session->opening_balance, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>

        {{-- Ventes actives --}}
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#f0fdf4; color:#16a34a;">
                <i class="fa-solid fa-receipt"></i>
            </div>
            <div>
                <span class="kpi-label">Ventes Actives ({{ $totalSalesCount }})</span>
                <span class="kpi-value" style="color:#16a34a;">{{ number_format($totalSalesAmount, 0, ',', ' ') }}
                    FCFA</span>
            </div>
        </div>

        {{-- Remboursements --}}
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fdf2f8; color:#db2777;">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
            <div>
                <span class="kpi-label">Remboursements ({{ $totalRefundsCount }})</span>
                <span class="kpi-value" style="color:#db2777;">{{ number_format($totalRefundsAmount, 0, ',', ' ') }}
                    FCFA</span>
            </div>
        </div>

        {{-- Encaissements crédits --}}
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#eff6ff; color:#2563eb;">
                <i class="fa-solid fa-hand-holding-dollar"></i>
            </div>
            <div>
                <span class="kpi-label">Encaissements Crédits</span>
                <span class="kpi-value" style="color:#2563eb;">{{ number_format($totalDebtPayments, 0, ',', ' ') }}
                    FCFA</span>
            </div>
        </div>

        {{-- Écart --}}
        <div class="kpi-card">
            @php
                $isDiffOk = $session->status === 'open' || $session->difference == 0;
                $isPlus = !$isDiffOk && $session->difference > 0;
            @endphp
            <div class="kpi-icon" style="background:{{ $isDiffOk ? '#f0fdf4' : ($isPlus ? '#eff6ff' : '#fdf2f2') }};
                        color:{{ $isDiffOk ? '#16a34a' : ($isPlus ? '#2563eb' : '#dc2626') }};">
                <i class="fa-solid fa-scale-balanced"></i>
            </div>
            <div>
                <span class="kpi-label">Écart Constaté</span>
                @if($session->status === 'open')
                    <span class="kpi-value" style="font-size:14px; color:#64748b; font-style:italic;">En cours...</span>
                @else
                    <span class="kpi-value" style="color:{{ $session->difference >= 0 ? '#16a34a' : '#dc2626' }};">
                        {{ $session->difference > 0 ? '+' : '' }}{{ number_format($session->difference, 0, ',', ' ') }} FCFA
                    </span>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══════════════════ PANNEAUX AUDIT ═══════════════════ --}}
    <div class="audit-panels">

        {{-- Recette Attendue (Théorique) --}}
        <div class="panel-card">
            <div class="panel-header">
                <i class="fa-solid fa-calculator" style="color:#6366f1;"></i>
                <h3>Recette Attendue (Système)</h3>
            </div>
            <div class="panel-body">
                <table class="audit-row-table">
                    <tr>
                        <td style="color:#64748b; font-weight:500;">Fond d'ouverture initial</td>
                        <td>{{ number_format($session->opening_balance, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <td style="color:#16a34a; font-weight:600;">
                            <i class="fa-solid fa-cash-register" style="font-size:10px; margin-right:4px;"></i>
                            (+) Ventes Espèces (Cash)
                        </td>
                        <td style="color:#16a34a;">+{{ number_format($totalCashSales, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <td style="color:#2563eb; font-weight:600;">
                            <i class="fa-solid fa-credit-card" style="font-size:10px; margin-right:4px;"></i>
                            (+) Ventes Carte / Mobile Money
                        </td>
                        <td style="color:#2563eb;">+{{ number_format($totalCardSales, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <td style="color:#d97706; font-weight:600;">
                            <i class="fa-solid fa-circle-exclamation" style="font-size:10px; margin-right:4px;"></i>
                            (+) Ventes Crédit (Dette)
                        </td>
                        <td style="color:#d97706;">+{{ number_format($totalCreditSales, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <td style="color:#db2777; font-weight:600;">
                            <i class="fa-solid fa-rotate-left" style="font-size:10px; margin-right:4px;"></i>
                            (-) Retours & Remboursements
                        </td>
                        <td style="color:#db2777;">-{{ number_format($totalRefundsAmount, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr>
                        <td style="color:#16a34a; font-weight:600;">
                            <i class="fa-solid fa-hand-holding-dollar" style="font-size:10px; margin-right:4px;"></i>
                            (+) Encaissements Crédits
                        </td>
                        <td style="color:#16a34a;">+{{ number_format($totalDebtPayments, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    <tr class="audit-total-row">
                        <td style="font-size:14px; font-weight:800; color:#1e293b;">Solde Théorique de Clôture</td>
                        <td style="font-size:16px; color:#6366f1;">
                            {{ number_format($session->expected_closing_balance ?? ($session->opening_balance + $totalSalesAmount + $totalDebtPayments - $totalRefundsAmount), 0, ',', ' ') }}
                            FCFA
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Déclaration Réelle & Écart --}}
        <div class="panel-card" style="display:flex; flex-direction:column;">
            <div class="panel-header">
                <i class="fa-solid fa-vault" style="color:#6366f1;"></i>
                <h3>Déclaration Réelle & Écart</h3>
            </div>
            <div class="panel-body"
                style="flex:1; display:flex; flex-direction:column; justify-content:space-between; gap:16px;">
                @if($session->status === 'open')
                    <div style="text-align:center; padding:30px; color:var(--text-muted); margin:auto;">
                        <div
                            style="width:52px; height:52px; background:#f0fdf4; color:#16a34a; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 14px; font-size:20px;">
                            <i class="fa-solid fa-lock-open"></i>
                        </div>
                        <p style="font-weight:700; font-size:14px; margin:0; color:#1e293b;">Session de caisse active</p>
                        <p style="font-size:12.5px; margin:6px 0 0 0; max-width:280px; margin-inline:auto;">
                            Les chiffres réels et les écarts apparaîtront à la clôture de caisse par l'agent.
                        </p>
                    </div>
                @else
                    <table class="audit-row-table">
                        <tr>
                            <td style="color:#64748b; font-weight:500;">Montant physique déclaré</td>
                            <td style="font-size:16px;">{{ number_format($session->actual_closing_balance, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        <tr>
                            <td style="color:#64748b; font-weight:500;">Solde attendu (Théorique)</td>
                            <td style="color:#475569;">{{ number_format($session->expected_closing_balance, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        <tr class="audit-total-row">
                            <td style="font-size:14px; font-weight:800; color:#1e293b;">Écart net audité</td>
                            <td style="text-align:right;">
                                @if($session->difference == 0)
                                    <span
                                        style="font-size:14px; font-weight:800; color:#16a34a; background:#f0fdf4; border:1px solid #bbf7d0; padding:3px 10px; border-radius:6px;">
                                        <i class="fa-solid fa-circle-check"></i> Équilibrée (0)
                                    </span>
                                @elseif($session->difference > 0)
                                    <span
                                        style="font-size:14px; font-weight:800; color:#2563eb; background:#eff6ff; border:1px solid #bfdbfe; padding:3px 10px; border-radius:6px;">
                                        <i class="fa-solid fa-circle-plus"></i>
                                        +{{ number_format($session->difference, 0, ',', ' ') }} FCFA
                                    </span>
                                @else
                                    <span
                                        style="font-size:14px; font-weight:800; color:#dc2626; background:#fdf2f2; border:1px solid #fecdd3; padding:3px 10px; border-radius:6px;">
                                        <i class="fa-solid fa-circle-minus"></i>
                                        {{ number_format($session->difference, 0, ',', ' ') }} FCFA
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    <div
                        style="background:#f8fafc; border:1px solid var(--border); padding:12px 14px; border-radius:8px; font-size:12px; color:#475569; border-left:4px solid #6366f1;">
                        <i class="fa-solid fa-circle-info" style="color:#6366f1; margin-right:4px;"></i>
                        Un <strong>déficit</strong> nécessite l'explication de l'agent. Un <strong>excédent</strong> correspond
                        généralement à de la monnaie excédentaire non déclarée.
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══════════════════ JOURNAL DES VENTES ═══════════════════ --}}
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-left">
                <i class="fa-solid fa-clock-rotate-left" style="color:#6366f1;"></i>
                <h3>Journal des Transactions</h3>
            </div>
            <span class="badge-count">{{ $sales->count() }} Transaction(s)</span>
        </div>
        <div style="padding:0;">
            @if($sales->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
                    <p style="font-weight:600; margin:0; color:#475569;">Aucune transaction</p>
                    <p style="font-size:12.5px; margin-top:4px;">Aucune vente n'a été enregistrée durant ce cycle.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th style="text-align:center;">Heure</th>
                                <th>Client</th>
                                <th style="text-align:center;">Mode</th>
                                <th style="text-align:right;">Total</th>
                                <th style="text-align:right;">Encaissé</th>
                                <th style="text-align:right;">Rendu / Dette</th>
                                <th style="text-align:center;">Statut</th>
                                <th style="text-align:center; width:55px;" class="no-print">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sales as $sale)
                                <tr>
                                    <td style="font-weight:700; color:#1e293b;">{{ $sale->reference }}</td>
                                    <td style="text-align:center; font-size:12.5px;">{{ $sale->created_at->format('H:i') }}</td>
                                    <td>
                                        @if($sale->customer)
                                            <div style="display:flex; align-items:center; gap:5px;">
                                                <i class="fa-solid fa-user" style="color:#6366f1; font-size:10px;"></i>
                                                <strong style="color:#1e293b;">{{ $sale->customer->name }}</strong>
                                            </div>
                                        @else
                                            <span style="color:#94a3b8; font-style:italic;">Passant anonyme</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if($sale->payment_method === 'cash')
                                            <span class="badge-paiement"
                                                style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">
                                                <i class="fa-solid fa-money-bill-wave" style="margin-right:3px;"></i>Espèces
                                            </span>
                                        @elseif($sale->payment_method === 'card')
                                            <span class="badge-paiement"
                                                style="background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;">
                                                <i class="fa-solid fa-credit-card" style="margin-right:3px;"></i>Carte
                                            </span>
                                        @elseif($sale->payment_method === 'credit')
                                            <span class="badge-paiement"
                                                style="background:#fef3c7; color:#92400e; border:1px solid #fde68a;">
                                                <i class="fa-solid fa-clock" style="margin-right:3px;"></i>Crédit
                                            </span>
                                        @else
                                            <span class="badge-paiement"
                                                style="background:#f1f5f9; color:#475569; border:1px solid #e2e8f0;">
                                                {{ strtoupper($sale->payment_method) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td style="text-align:right; font-weight:700; color:#1e293b;">
                                        {{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td style="text-align:right;">
                                        {{ number_format($sale->amount_received, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td style="text-align:right; font-weight:600;">
                                        @if($sale->payment_method === 'credit')
                                            <span
                                                style="color:#dc2626; background:#fdf2f2; border:1px solid #fecdd3; padding:1px 6px; border-radius:4px; font-size:12px;">
                                                Dette:
                                                {{ number_format(max(0, $sale->total_amount - $sale->amount_received), 0, ',', ' ') }}
                                                FCFA
                                            </span>
                                        @else
                                            {{ number_format($sale->change_amount, 0, ',', ' ') }} FCFA
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if($sale->status === 'completed')
                                            <span class="badge-status"
                                                style="background:#eefdf4; color:#166534; border:1px solid #bbf7d0;">Complétée</span>
                                        @elseif($sale->status === 'returned')
                                            <span class="badge-status"
                                                style="background:#fdf2f8; color:#9d174d; border:1px solid #fbcfe8;">Retournée</span>
                                        @else
                                            <span class="badge-status"
                                                style="background:#fdf2f2; color:#991b1b; border:1px solid #fecdd3;">Annulée</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;" class="no-print">
                                        <a href="{{ route('admin.sales.show', $sale->id) }}"
                                            style="background:#f1f5f9; color:#1e293b; width:28px; height:28px; border-radius:6px; display:inline-flex; align-items:center; justify-content:center; text-decoration:none;"
                                            title="Voir la facture">
                                            <i class="fa-solid fa-eye" style="font-size:12px;"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8fafc; border-top:2px solid var(--border);">
                                <td colspan="4" style="padding:11px 15px; font-size:13px; font-weight:700; color:#1e293b;">
                                    TOTAL ({{ $totalSalesCount }} vente(s) actives)
                                </td>
                                <td
                                    style="padding:11px 15px; font-size:14px; font-weight:800; color:#1e293b; text-align:right;">
                                    {{ number_format($totalSalesAmount, 0, ',', ' ') }} FCFA
                                </td>
                                <td colspan="4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════ JOURNAL DES ENCAISSEMENTS CRÉDITS ═══════════════════ --}}
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-left">
                <i class="fa-solid fa-hand-holding-dollar" style="color:#6366f1;"></i>
                <h3>Encaissements de Crédits durant la Session</h3>
            </div>
            <span class="badge-count">{{ $debtPayments->count() }} Encaissement(s)</span>
        </div>
        <div style="padding:0;">
            @if($debtPayments->isEmpty())
                <div class="empty-state">
                    <div class="empty-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
                    <p style="font-weight:600; margin:0; color:#475569;">Aucun encaissement</p>
                    <p style="font-size:12.5px; margin-top:4px;">Aucun remboursement de crédit n'a été perçu durant cette
                        session.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th style="text-align:center;">Heure</th>
                                <th>Client</th>
                                <th style="text-align:center;">Mode</th>
                                <th style="text-align:right;">Montant Encaissé</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($debtPayments as $payment)
                                <tr>
                                    <td style="font-weight:700; color:#1e293b;">{{ $payment->reference }}</td>
                                    <td style="text-align:center; font-size:12.5px;">{{ $payment->created_at->format('H:i') }}</td>
                                    <td>
                                        @if($payment->customer)
                                            <strong style="color:#1e293b;">{{ $payment->customer->name }}</strong>
                                        @else
                                            <span style="color:#94a3b8; font-style:italic;">Client inconnu/supprimé</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="badge-paiement"
                                            style="background:#f0fdf4; color:#166534; border:1px solid #bbf7d0;">Espèces</span>
                                    </td>
                                    <td style="text-align:right; font-weight:700; font-size:13.5px; color:#16a34a;">
                                        +{{ number_format($payment->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8fafc; border-top:2px solid var(--border);">
                                <td colspan="4" style="padding:11px 15px; font-size:13px; font-weight:700; color:#1e293b;">
                                    TOTAL ENCAISSÉ
                                </td>
                                <td
                                    style="padding:11px 15px; font-size:14px; font-weight:800; color:#16a34a; text-align:right;">
                                    +{{ number_format($totalDebtPayments, 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection