@extends('admin.layouts.app')

@section('content')
<div class="adm-dash">

    {{-- ═══════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════ --}}
    <div class="adm-dash__header">
        <div class="adm-dash__header-left">
            <div class="adm-dash__greeting-icon">
                <i class="fa-solid fa-gauge-high"></i>
            </div>
            <div>
                <h1 class="adm-dash__title">Tableau de bord Admin</h1>
                <p class="adm-dash__subtitle">
                    Bienvenue, <strong>{{ auth()->user()->name }}</strong> —
                    @php
                        $jours = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
                        $mois  = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
                    @endphp
                    {{ $jours[now()->dayOfWeek] }} {{ now()->day }} {{ $mois[now()->month] }} {{ now()->year }}
                </p>
            </div>
        </div>
        <div class="adm-dash__header-right">
            @if($pendingRestockCount > 0)
            <a href="{{ route('admin.restock-requests.index') }}" class="adm-dash__alert-badge">
                <i class="fa-solid fa-bell"></i>
                {{ $pendingRestockCount }} réappro. en attente
            </a>
            @endif
            @if($openCashSessions > 0)
            <a href="{{ route('admin.cash-sessions.index') }}" class="adm-dash__alert-badge adm-dash__alert-badge--green">
                <i class="fa-solid fa-cash-register"></i>
                {{ $openCashSessions }} caisse(s) ouverte(s)
            </a>
            @endif
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         KPI CARDS ROW 1 — Ventes
    ═══════════════════════════════════════════════ --}}
    <div class="adm-kpi-row">

        {{-- Ventes du jour --}}
        <div class="adm-kpi adm-kpi--purple">
            <div class="adm-kpi__icon"><i class="fa-solid fa-sun"></i></div>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Ventes Aujourd'hui</span>
                <span class="adm-kpi__value">{{ number_format($todaysSales, 0, ',', ' ') }}</span>
                <span class="adm-kpi__sub">{{ $todaysSalesCount }} transaction(s) · FCFA</span>
            </div>
            <div class="adm-kpi__badge {{ $salesDelta >= 0 ? 'adm-kpi__badge--up' : 'adm-kpi__badge--down' }}">
                <i class="fa-solid fa-arrow-{{ $salesDelta >= 0 ? 'up' : 'down' }}"></i>
                {{ abs($salesDelta) }}%
            </div>
        </div>

        {{-- Ventes du mois --}}
        <div class="adm-kpi adm-kpi--blue">
            <div class="adm-kpi__icon"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Ventes Ce Mois</span>
                <span class="adm-kpi__value">{{ number_format($monthSales, 0, ',', ' ') }}</span>
                <span class="adm-kpi__sub">{{ $monthSalesCount }} transaction(s) · FCFA</span>
            </div>
        </div>

        {{-- Crédits clients --}}
        <div class="adm-kpi adm-kpi--red">
            <div class="adm-kpi__icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Crédits Clients</span>
                <span class="adm-kpi__value">{{ number_format($totalCustomerDebt, 0, ',', ' ') }}</span>
                <span class="adm-kpi__sub">{{ $indebtedCustomersCount }} client(s) endettés · FCFA</span>
            </div>
        </div>

        {{-- Écarts de caisse --}}
        <div class="adm-kpi {{ $totalCashSessionDiscrepancies == 0 ? 'adm-kpi--gray' : ($totalCashSessionDiscrepancies < 0 ? 'adm-kpi--red' : 'adm-kpi--blue') }}">
            <div class="adm-kpi__icon"><i class="fa-solid fa-scale-unbalanced"></i></div>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Écarts de Caisse</span>
                <span class="adm-kpi__value">{{ number_format($totalCashSessionDiscrepancies, 0, ',', ' ') }}</span>
                <span class="adm-kpi__sub">Cumul des anomalies · FCFA</span>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         KPI CARDS ROW 2 — Inventaire & Personnel
    ═══════════════════════════════════════════════ --}}
    <div class="adm-kpi-row adm-kpi-row--sm">

        <div class="adm-kpi adm-kpi--sm adm-kpi--indigo">
            <i class="fa-solid fa-boxes-stacked adm-kpi__icon-sm"></i>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Produits</span>
                <span class="adm-kpi__value adm-kpi__value--sm">{{ $totalProducts }}</span>
            </div>
        </div>

        <div class="adm-kpi adm-kpi--sm adm-kpi--orange">
            <i class="fa-solid fa-triangle-exclamation adm-kpi__icon-sm"></i>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Stock Faible</span>
                <span class="adm-kpi__value adm-kpi__value--sm">{{ $productsAtThreshold }}</span>
            </div>
        </div>

        <div class="adm-kpi adm-kpi--sm adm-kpi--red">
            <i class="fa-solid fa-circle-xmark adm-kpi__icon-sm"></i>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Rupture</span>
                <span class="adm-kpi__value adm-kpi__value--sm">{{ $outOfStock }}</span>
            </div>
        </div>

        <div class="adm-kpi adm-kpi--sm adm-kpi--teal">
            <i class="fa-solid fa-tag adm-kpi__icon-sm"></i>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Catégories</span>
                <span class="adm-kpi__value adm-kpi__value--sm">{{ $totalCategories }}</span>
            </div>
        </div>

        <div class="adm-kpi adm-kpi--sm adm-kpi--purple">
            <i class="fa-solid fa-users adm-kpi__icon-sm"></i>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Employés Actifs</span>
                <span class="adm-kpi__value adm-kpi__value--sm">{{ $activeEmployees }}</span>
            </div>
        </div>

        <div class="adm-kpi adm-kpi--sm adm-kpi--blue">
            <i class="fa-solid fa-user-group adm-kpi__icon-sm"></i>
            <div class="adm-kpi__body">
                <span class="adm-kpi__label">Clients</span>
                <span class="adm-kpi__value adm-kpi__value--sm">{{ $totalCustomers }}</span>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         GRAPHIQUE + MODES DE PAIEMENT
    ═══════════════════════════════════════════════ --}}
    <div class="adm-grid adm-grid--7-3">

        {{-- Évolution des ventes (7 jours) --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">
                    <i class="fa-solid fa-chart-area" style="color:var(--primary);"></i>
                    Évolution des Ventes — 7 derniers jours
                </h2>
            </div>
            <div class="adm-card__body">
                <div class="chart-wrap">
                    @php
                        $maxVal = collect($salesChart)->max('total');
                        if ($maxVal == 0) $maxVal = 1;
                    @endphp
                    <div class="bar-chart">
                        @foreach($salesChart as $day)
                        <div class="bar-chart__col">
                            <div class="bar-chart__tooltip">
                                {{ number_format($day['total'], 0, ',', ' ') }} FCFA<br>
                                <small>{{ $day['count'] }} vente(s)</small>
                            </div>
                            <div class="bar-chart__bar-wrap">
                                <div class="bar-chart__bar"
                                     style="height: {{ $maxVal > 0 ? round(($day['total'] / $maxVal) * 100) : 0 }}%">
                                </div>
                            </div>
                            <span class="bar-chart__label">{{ $day['label'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Modes de paiement --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">
                    <i class="fa-solid fa-wallet" style="color:var(--primary);"></i>
                    Paiements (ce mois)
                </h2>
            </div>
            <div class="adm-card__body">
                @php
                    $paymentIcons = [
                        'cash'    => ['icon' => 'fa-money-bill-wave', 'color' => '#10b981', 'label' => 'Espèces'],
                        'card'    => ['icon' => 'fa-credit-card',     'color' => '#3b82f6', 'label' => 'Carte'],
                        'mobile'  => ['icon' => 'fa-mobile-screen',   'color' => '#8b5cf6', 'label' => 'Mobile'],
                        'credit'  => ['icon' => 'fa-hand-holding-dollar', 'color' => '#f59e0b', 'label' => 'Crédit'],
                    ];
                    $totalPayments = $salesByPayment->sum('total');
                @endphp
                @forelse($salesByPayment as $pm)
                @php
                    $pmInfo = $paymentIcons[$pm->payment_method] ?? ['icon'=>'fa-circle-dot','color'=>'#6b7280','label'=>ucfirst($pm->payment_method)];
                    $pct    = $totalPayments > 0 ? round(($pm->total / $totalPayments) * 100) : 0;
                @endphp
                <div class="payment-item">
                    <div class="payment-item__icon" style="background:{{ $pmInfo['color'] }}20; color:{{ $pmInfo['color'] }};">
                        <i class="fa-solid {{ $pmInfo['icon'] }}"></i>
                    </div>
                    <div class="payment-item__info">
                        <span class="payment-item__label">{{ $pmInfo['label'] }}</span>
                        <div class="payment-item__bar-wrap">
                            <div class="payment-item__bar" style="width:{{ $pct }}%; background:{{ $pmInfo['color'] }};"></div>
                        </div>
                    </div>
                    <div class="payment-item__stats">
                        <span class="payment-item__pct">{{ $pct }}%</span>
                        <span class="payment-item__count">{{ $pm->count }} vente(s)</span>
                    </div>
                </div>
                @empty
                <p class="adm-empty"><i class="fa-solid fa-inbox"></i> Aucune vente ce mois.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         TOP PRODUITS + PERF CAISSIERS
    ═══════════════════════════════════════════════ --}}
    <div class="adm-grid adm-grid--2">

        {{-- Top Produits du mois --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">
                    <i class="fa-solid fa-trophy" style="color:#f59e0b;"></i>
                    Top Produits (ce mois)
                </h2>
                <a href="{{ route('admin.products.index') }}" class="adm-card__link">Voir tous</a>
            </div>
            <div class="adm-card__body adm-card__body--flush">
                @forelse($topProducts as $idx => $item)
                <div class="top-row {{ $idx === 0 ? 'top-row--gold' : '' }}">
                    <span class="top-row__rank">{{ $idx + 1 }}</span>
                    <div class="top-row__info">
                        <span class="top-row__name">{{ $item->product?->name ?? 'Produit supprimé' }}</span>
                        <span class="top-row__sub">{{ number_format($item->total_revenue, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <span class="top-row__qty">{{ $item->total_qty }} unités</span>
                </div>
                @empty
                <p class="adm-empty"><i class="fa-solid fa-chart-simple"></i> Aucune donnée ce mois.</p>
                @endforelse
            </div>
        </div>

        {{-- Performance des caissiers --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">
                    <i class="fa-solid fa-user-tie" style="color:#6366f1;"></i>
                    Performance Caissiers (ce mois)
                </h2>
                <a href="{{ route('admin.employees.index') }}" class="adm-card__link">Voir tous</a>
            </div>
            <div class="adm-card__body adm-card__body--flush">
                @forelse($cashierPerformance as $idx => $cp)
                <div class="top-row {{ $idx === 0 ? 'top-row--blue' : '' }}">
                    <span class="top-row__rank">{{ $idx + 1 }}</span>
                    <div class="top-row__info">
                        <span class="top-row__name">{{ $cp->user?->name ?? 'Employé supprimé' }}</span>
                        <span class="top-row__sub">{{ number_format($cp->total, 0, ',', ' ') }} FCFA encaissés</span>
                    </div>
                    <span class="top-row__qty">{{ $cp->sales_count }} ventes</span>
                </div>
                @empty
                <p class="adm-empty"><i class="fa-solid fa-user-clock"></i> Aucune donnée ce mois.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         STOCK FAIBLE + DERNIÈRES VENTES
    ═══════════════════════════════════════════════ --}}
    <div class="adm-grid adm-grid--2">

        {{-- Produits en stock faible --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">
                    <i class="fa-solid fa-triangle-exclamation" style="color:#e11d48;"></i>
                    Stock Faible / Rupture
                </h2>
                <a href="{{ route('admin.products.threshold') }}" class="adm-card__link">Voir tous</a>
            </div>
            <div class="adm-card__body adm-card__body--flush">
                @forelse($lowStockProducts as $product)
                <div class="stock-row">
                    <div class="stock-row__info">
                        <span class="stock-row__name">{{ $product->name }}</span>
                        <span class="stock-row__cat">{{ $product->category_name ?? 'N/A' }}</span>
                    </div>
                    <div class="stock-row__right">
                        @if($product->stock == 0)
                            <span class="adm-badge adm-badge--danger">Rupture</span>
                        @else
                            <span class="adm-badge adm-badge--warning">{{ $product->stock }} restant(s)</span>
                        @endif
                        <span class="stock-row__threshold">Seuil: {{ $product->stock_threshold }}</span>
                    </div>
                </div>
                @empty
                <p class="adm-empty"><i class="fa-solid fa-circle-check" style="color:#10b981;"></i> Tous les stocks sont suffisants.</p>
                @endforelse
            </div>
        </div>

        {{-- Dernières ventes --}}
        <div class="adm-card">
            <div class="adm-card__header">
                <h2 class="adm-card__title">
                    <i class="fa-solid fa-receipt" style="color:#3b82f6;"></i>
                    Dernières Ventes
                </h2>
                <a href="{{ route('admin.sales.index') }}" class="adm-card__link">Historique</a>
            </div>
            <div class="adm-card__body adm-card__body--flush">
                @forelse($recentSales as $sale)
                <div class="sale-row">
                    <div class="sale-row__id">#{{ $sale->id }}</div>
                    <div class="sale-row__info">
                        <span class="sale-row__cashier">{{ $sale->user?->name ?? 'N/A' }}</span>
                        <span class="sale-row__date">{{ $sale->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="sale-row__right">
                        <span class="sale-row__amount">{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</span>
                        @php
                            $pmColors = ['cash'=>'#10b981','card'=>'#3b82f6','mobile'=>'#8b5cf6','credit'=>'#f59e0b'];
                            $pmLabels = ['cash'=>'Espèces','card'=>'Carte','mobile'=>'Mobile','credit'=>'Crédit'];
                        @endphp
                        <span class="adm-badge" style="background:{{ ($pmColors[$sale->payment_method] ?? '#6b7280') }}20; color:{{ $pmColors[$sale->payment_method] ?? '#6b7280' }};">
                            {{ $pmLabels[$sale->payment_method] ?? ucfirst($sale->payment_method) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="adm-empty"><i class="fa-solid fa-inbox"></i> Aucune vente enregistrée.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

<style>
/* ════════════════════════════════════════════════════════
   ADMIN DASHBOARD — Design système complet
════════════════════════════════════════════════════════ */
.adm-dash {
    width: 100%;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* ── Header ─────────────────────────────────────────── */
.adm-dash__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
    background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
    border-radius: 16px;
    padding: 28px 32px;
    color: white;
}
.adm-dash__header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}
.adm-dash__greeting-icon {
    width: 60px; height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px;
    flex-shrink: 0;
}
.adm-dash__title {
    font-size: 24px;
    font-weight: 800;
    margin: 0 0 6px 0;
    color: white;
}
.adm-dash__subtitle {
    font-size: 14px;
    color: rgba(255,255,255,0.8);
    margin: 0;
}
.adm-dash__header-right {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}
.adm-dash__alert-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(4px);
    color: white;
    padding: 8px 16px;
    border-radius: 24px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid rgba(255,255,255,0.3);
    transition: background 0.2s;
}
.adm-dash__alert-badge:hover { background: rgba(255,255,255,0.3); color: white; }
.adm-dash__alert-badge--green { background: rgba(16,185,129,0.35); border-color: rgba(16,185,129,0.5); }

/* ── KPI Row ─────────────────────────────────────────── */
.adm-kpi-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
}
.adm-kpi-row--sm {
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
}

.adm-kpi {
    background: white;
    border-radius: 14px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 16px;
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
}
.adm-kpi::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 4px;
    height: 100%;
    border-radius: 14px 0 0 14px;
}
.adm-kpi:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }

.adm-kpi--purple::before { background: linear-gradient(180deg, #8b5cf6, #6d28d9); }
.adm-kpi--blue::before   { background: linear-gradient(180deg, #3b82f6, #1d4ed8); }
.adm-kpi--red::before    { background: linear-gradient(180deg, #e11d48, #be123c); }
.adm-kpi--gray::before   { background: linear-gradient(180deg, #6b7280, #4b5563); }
.adm-kpi--indigo::before { background: linear-gradient(180deg, #6366f1, #4338ca); }
.adm-kpi--orange::before { background: linear-gradient(180deg, #f59e0b, #d97706); }
.adm-kpi--teal::before   { background: linear-gradient(180deg, #14b8a6, #0d9488); }

.adm-kpi__icon {
    width: 56px; height: 56px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
    color: white;
    flex-shrink: 0;
}
.adm-kpi--purple .adm-kpi__icon { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
.adm-kpi--blue   .adm-kpi__icon { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.adm-kpi--red    .adm-kpi__icon { background: linear-gradient(135deg, #e11d48, #be123c); }
.adm-kpi--gray   .adm-kpi__icon { background: linear-gradient(135deg, #6b7280, #4b5563); }

.adm-kpi__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.adm-kpi__label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--text-muted);
}
.adm-kpi__value {
    font-size: 26px;
    font-weight: 800;
    color: var(--text);
    line-height: 1.1;
}
.adm-kpi__sub {
    font-size: 11px;
    color: var(--text-muted);
}
.adm-kpi__badge {
    position: absolute;
    top: 14px; right: 14px;
    display: flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
}
.adm-kpi__badge--up   { background: #dcfce7; color: #16a34a; }
.adm-kpi__badge--down { background: #fee2e2; color: #dc2626; }

/* Small KPI variant */
.adm-kpi--sm {
    padding: 16px;
    gap: 12px;
    flex-direction: column;
    align-items: flex-start;
}
.adm-kpi__icon-sm {
    font-size: 22px;
    margin-bottom: 4px;
}
.adm-kpi--sm.adm-kpi--purple .adm-kpi__icon-sm { color: #8b5cf6; }
.adm-kpi--sm.adm-kpi--blue   .adm-kpi__icon-sm { color: #3b82f6; }
.adm-kpi--sm.adm-kpi--red    .adm-kpi__icon-sm { color: #e11d48; }
.adm-kpi--sm.adm-kpi--orange .adm-kpi__icon-sm { color: #f59e0b; }
.adm-kpi--sm.adm-kpi--teal   .adm-kpi__icon-sm { color: #14b8a6; }
.adm-kpi--sm.adm-kpi--indigo .adm-kpi__icon-sm { color: #6366f1; }
.adm-kpi__value--sm {
    font-size: 28px;
    font-weight: 800;
    color: var(--text);
}

/* ── Grid layouts ────────────────────────────────────── */
.adm-grid {
    display: grid;
    gap: 20px;
}
.adm-grid--2    { grid-template-columns: 1fr 1fr; }
.adm-grid--7-3  { grid-template-columns: 2fr 1fr; }

@media (max-width: 1200px) {
    .adm-grid--7-3,
    .adm-grid--2 { grid-template-columns: 1fr; }
}

/* ── Cards ───────────────────────────────────────────── */
.adm-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}
.adm-card__header {
    padding: 18px 22px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.adm-card__title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.adm-card__link {
    font-size: 12px;
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
    padding: 4px 10px;
    background: rgba(99,102,241,0.08);
    border-radius: 6px;
    transition: background 0.2s;
}
.adm-card__link:hover { background: rgba(99,102,241,0.16); }
.adm-card__body {
    padding: 20px;
    flex: 1;
}
.adm-card__body--flush { padding: 0; }

/* ── Bar Chart ───────────────────────────────────────── */
.chart-wrap { height: 180px; display: flex; align-items: flex-end; }
.bar-chart {
    display: flex;
    align-items: flex-end;
    justify-content: space-around;
    width: 100%;
    height: 100%;
    gap: 6px;
    padding: 0 4px;
}
.bar-chart__col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    height: 100%;
    position: relative;
    gap: 4px;
}
.bar-chart__col:hover .bar-chart__tooltip { opacity: 1; transform: translateY(-4px); }
.bar-chart__tooltip {
    position: absolute;
    top: -60px;
    background: #1e293b;
    color: white;
    font-size: 10px;
    padding: 6px 10px;
    border-radius: 8px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: all 0.2s;
    text-align: center;
    z-index: 10;
}
.bar-chart__tooltip::after {
    content: '';
    position: absolute;
    bottom: -5px; left: 50%;
    transform: translateX(-50%);
    border: 5px solid transparent;
    border-top-color: #1e293b;
    border-bottom: none;
}
.bar-chart__bar-wrap {
    flex: 1;
    width: 100%;
    display: flex;
    align-items: flex-end;
}
.bar-chart__bar {
    width: 100%;
    min-height: 4px;
    background: linear-gradient(180deg, var(--primary) 0%, #7c3aed 100%);
    border-radius: 6px 6px 0 0;
    transition: height 0.5s cubic-bezier(.4,0,.2,1);
}
.bar-chart__col:hover .bar-chart__bar { filter: brightness(1.15); }
.bar-chart__label {
    font-size: 9px;
    color: var(--text-muted);
    white-space: nowrap;
    text-align: center;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

/* ── Payment items ───────────────────────────────────── */
.payment-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
}
.payment-item:last-child { border-bottom: none; }
.payment-item__icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.payment-item__info { flex: 1; display: flex; flex-direction: column; gap: 6px; }
.payment-item__label { font-size: 13px; font-weight: 600; color: var(--text); }
.payment-item__bar-wrap { background: var(--bg); border-radius: 4px; height: 6px; overflow: hidden; }
.payment-item__bar { height: 100%; border-radius: 4px; transition: width 0.6s ease; }
.payment-item__stats { display: flex; flex-direction: column; align-items: flex-end; gap: 2px; }
.payment-item__pct { font-size: 15px; font-weight: 800; color: var(--text); }
.payment-item__count { font-size: 10px; color: var(--text-muted); }

/* ── Top rows ────────────────────────────────────────── */
.top-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
.top-row:last-child { border-bottom: none; }
.top-row:hover { background: var(--bg); }
.top-row--gold { background: linear-gradient(90deg, #fef9c3 0%, transparent 80%); }
.top-row--blue { background: linear-gradient(90deg, #eff6ff 0%, transparent 80%); }
.top-row__rank {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: var(--bg);
    border: 2px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 800;
    color: var(--text-muted);
    flex-shrink: 0;
}
.top-row--gold .top-row__rank { background: #fef08a; border-color: #f59e0b; color: #92400e; }
.top-row--blue .top-row__rank { background: #dbeafe; border-color: #3b82f6; color: #1d4ed8; }
.top-row__info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.top-row__name { font-size: 13px; font-weight: 600; color: var(--text); }
.top-row__sub  { font-size: 11px; color: var(--text-muted); }
.top-row__qty  { font-size: 13px; font-weight: 700; color: var(--primary); white-space: nowrap; }

/* ── Stock rows ──────────────────────────────────────── */
.stock-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
.stock-row:last-child { border-bottom: none; }
.stock-row:hover { background: var(--bg); }
.stock-row__info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.stock-row__name { font-size: 13px; font-weight: 600; color: var(--text); }
.stock-row__cat  { font-size: 11px; color: var(--text-muted); }
.stock-row__right { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }
.stock-row__threshold { font-size: 10px; color: var(--text-muted); }

/* ── Sale rows ───────────────────────────────────────── */
.sale-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
}
.sale-row:last-child { border-bottom: none; }
.sale-row:hover { background: var(--bg); }
.sale-row__id {
    font-size: 12px; font-weight: 700;
    color: var(--text-muted);
    min-width: 40px;
}
.sale-row__info { flex: 1; display: flex; flex-direction: column; gap: 2px; }
.sale-row__cashier { font-size: 13px; font-weight: 600; color: var(--text); }
.sale-row__date    { font-size: 11px; color: var(--text-muted); }
.sale-row__right { display: flex; flex-direction: column; align-items: flex-end; gap: 4px; }
.sale-row__amount { font-size: 13px; font-weight: 700; color: var(--text); }

/* ── Badges ──────────────────────────────────────────── */
.adm-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}
.adm-badge--danger  { background: #fee2e2; color: #dc2626; }
.adm-badge--warning { background: #fef3c7; color: #d97706; }
.adm-badge--success { background: #dcfce7; color: #16a34a; }

/* ── Empty state ─────────────────────────────────────── */
.adm-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-muted);
    font-size: 13px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}
.adm-empty i { font-size: 28px; color: var(--border); }
</style>
@endsection
