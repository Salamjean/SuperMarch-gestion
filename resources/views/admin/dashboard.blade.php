@extends('admin.layouts.app')

@section('content')
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1><i class="fa-solid fa-chart-line"></i> Tableau de bord</h1>
            <p class="dashboard-subtitle">Bienvenue {{ auth()->user()->name }}. Voici un aperçu de votre supermarché.</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <!-- Total Produits -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Produits</p>
                    <h3 class="stat-value">{{ $totalProducts }}</h3>
                </div>
            </div>

            <!-- Produits atteints au seuil -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Produits au seuil</p>
                    <h3 class="stat-value">{{ $productsAtThreshold }}</h3>
                </div>
            </div>

            <!-- Ventes Aujourd'hui -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <i class="fa-solid fa-calendar"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Ventes Aujourd'hui</p>
                    <h3 class="stat-value">{{ $todaysSalesCount }}</h3>
                    <p class="stat-subtext">{{ number_format($todaysSales, 0, ',', ' ') }} FCFA</p>
                </div>
            </div>

            <!-- Ventes Ce Mois -->
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Ventes Ce Mois</p>
                    <h3 class="stat-value">{{ number_format($monthSales, 0, ',', ' ') }}</h3>
                    <p class="stat-subtext">FCFA</p>
                </div>
            </div>
        </div>

        <!-- Section Audit, Caisse & Fidélité -->
        <div
            style="font-size: 14px; font-weight: 800; color: #1e293b; margin: 30px 0 15px 0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-calculator" style="color:var(--primary);"></i> Suivi Administratif & Audit Financier
        </div>

        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
            <!-- Crédit / Encours Total -->
            <div class="stat-card" style="border-left: 4px solid #e11d48;">
                <div class="stat-icon" style="background: #fff1f2; color: #e11d48;">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Crédits Clients Actifs</p>
                    <h3 class="stat-value" style="color: #e11d48;">{{ number_format($totalCustomerDebt, 0, ',', ' ') }} FCFA
                    </h3>
                    <p class="stat-subtext">Dû par la clientèle à l'établissement</p>
                </div>
            </div>

            <!-- Points de Fidélité Cumulés -->
            <div class="stat-card" style="border-left: 4px solid #16a34a;">
                <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Points Fidélité Distribués</p>
                    <h3 class="stat-value" style="color: #16a34a;">{{ number_format($totalLoyaltyPoints, 0, ',', ' ') }} pts
                    </h3>
                    <p class="stat-subtext">Capital de fidélisation actif</p>
                </div>
            </div>

            <!-- Écarts de Caisse Cumulés -->
            <div class="stat-card"
                style="border-left: 4px solid {{ $totalCashSessionDiscrepancies == 0 ? '#4b5563' : ($totalCashSessionDiscrepancies < 0 ? '#dc2626' : '#2563eb') }};">
                <div class="stat-icon" style="background: #f8fafc; color: #4338ca;">
                    <i class="fa-solid fa-scale-unbalanced"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Écarts de Caisse (Audit)</p>
                    <h3 class="stat-value"
                        style="color: {{ $totalCashSessionDiscrepancies == 0 ? 'var(--text)' : ($totalCashSessionDiscrepancies < 0 ? '#dc2626' : '#2563eb') }};">
                        {{ number_format($totalCashSessionDiscrepancies, 0, ',', ' ') }} FCFA
                    </h3>
                    <p class="stat-subtext">Somme des anomalies physiques déclarées</p>
                </div>
            </div>
        </div>

        <!-- Conteneur avec 2 colonnes -->
        <div class="dashboard-content">
            <!-- Colonne Gauche -->
            <div class="dashboard-column">
                <!-- Produits en Stock Faible -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2><i class="fa-solid fa-exclamation-triangle" style="color: #e11d48;"></i> Stock Faible</h2>
                        <a href="{{ route('admin.products.index') }}" class="card-link">Voir tous</a>
                    </div>
                    <div class="card-body">
                        @if ($lowStockProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="dashboard-table">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th>Catégorie</th>
                                            <th>Quantité</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lowStockProducts as $product)
                                            <tr class="stock-warning">
                                                <td><strong>{{ $product->name }}</strong></td>
                                                <td>{{ $product->category_name ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge badge-danger">{{ $product->stock }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="empty-state"><i class="fa-solid fa-check-circle"></i> Tous les produits ont un bon
                                stock.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne Droite -->
            <div class="dashboard-column">
                <!-- Dernières Ventes -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h2><i class="fa-solid fa-receipt"></i> Dernières Ventes</h2>
                        <a href="{{ route('admin.sales.index') }}" class="card-link">Historique</a>
                    </div>
                    <div class="card-body">
                        @if ($recentSales->count() > 0)
                            <div class="table-responsive">
                                <table class="dashboard-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Caissier</th>
                                            <th>Montant</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentSales as $sale)
                                            <tr>
                                                <td><strong>#{{ $sale->id }}</strong></td>
                                                <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                                <td><strong>{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</strong>
                                                </td>
                                                <td><small>{{ $sale->created_at->format('d/m/Y H:i') }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="empty-state"><i class="fa-solid fa-inbox"></i> Aucune vente enregistrée.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-container {
            background: var(--bg);
            border-radius: 12px;
        }

        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            font-size: 28px;
            color: var(--text);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dashboard-subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            flex-shrink: 0;
        }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 6px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--text);
            margin: 0;
        }

        .stat-subtext {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Dashboard Content */
        .dashboard-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 1200px) {
            .dashboard-content {
                grid-template-columns: 1fr;
            }
        }

        .dashboard-card {
            background: white;
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 16px;
            color: var(--text);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-link {
            font-size: 12px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .card-link:hover {
            color: var(--primary-light);
        }

        .card-body {
            padding: 20px;
        }

        .dashboard-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .dashboard-table thead {
            background: var(--bg);
            border-bottom: 2px solid var(--border);
        }

        .dashboard-table th {
            padding: 12px;
            text-align: left;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }

        .dashboard-table td {
            padding: 12px;
            border-bottom: 1px solid var(--border);
        }

        .dashboard-table tbody tr:last-child td {
            border-bottom: none;
        }

        .dashboard-table tr.stock-warning {
            background: rgba(225, 29, 72, 0.05);
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 11px;
        }

        .badge-danger {
            background: rgba(225, 29, 72, 0.2);
            color: var(--danger);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 32px;
            color: var(--border);
            display: block;
            margin-bottom: 10px;
        }
    </style>
@endsection
