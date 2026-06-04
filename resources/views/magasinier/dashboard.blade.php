@extends('magasinier.layouts.app')

@section('title', 'Tableau de bord magasinier')
@section('page_title', 'Tableau de bord')

@push('styles')
    <style>
        .dashboard-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .dashboard-stats-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);
        }

        .dashboard-stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 20px -8px rgba(0,0,0,0.08);
            border-color: var(--primary);
        }

        .stats-icon-wrap {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stats-info {
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .stats-label {
            font-size: 13px;
            color: var(--muted);
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stats-val {
            font-size: 20px;
            font-weight: 800;
            color: var(--text);
            margin-top: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .dashboard-main-grid {
            display: grid;
            grid-template-columns: 1.3fr 1fr;
            gap: 25px;
            align-items: start;
        }

        @media (max-width: 980px) {
            .dashboard-main-grid {
                grid-template-columns: 1fr;
            }
        }

        .card-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            padding: 16px 20px;
        }

        .card-header-flex h3 {
            font-size: 15px;
            font-weight: 800;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        /* Animation de clignotement pour les alertes */
        .badge-blink {
            animation: blinker 1.5s linear infinite;
        }

        @keyframes blinker {
            50% { opacity: 0.5; }
        }

        /* Custom progress bar styles for category stocks */
        .progress-bar-container {
            width: 100%;
            height: 6px;
            background: #e2eaf3;
            border-radius: 99px;
            overflow: hidden;
            margin-top: 6px;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 99px;
            transition: width 0.4s ease-in-out;
        }

        /* Category item layout */
        .category-stat-item {
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .category-stat-item:last-child {
            border-bottom: none;
        }

        .category-stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }

        .category-name-label {
            font-weight: 700;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .category-stock-val {
            font-weight: 800;
            color: var(--primary);
        }

        .category-meta-info {
            font-size: 11px;
            color: var(--muted);
        }

        /* Supplier list styles */
        .supplier-stat-item {
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .supplier-stat-item:last-child {
            border-bottom: none;
        }

        .supplier-info-wrap {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .supplier-name-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
        }

        .supplier-contact-sub {
            font-size: 11px;
            color: var(--muted);
        }

        .supplier-products-badge {
            font-size: 12px;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 99px;
            background: #f0f7ff;
            color: var(--primary);
            border: 1px solid rgba(0, 77, 153, 0.1);
        }
    </style>
@endpush

@section('content')
    <div class="dashboard-container">
        
        <!-- Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
            <div>
                <h2 style="font-size: 24px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-gauge-high"></i> Tableau de bord
                </h2>
                <p style="color: var(--muted); font-size: 14px; margin-top: 4px;">Suivez en temps réel l'état des stocks, les alertes de seuil et les demandes de la caisse.</p>
            </div>
            <div style="background: #e0f2fe; color: #0369a1; padding: 8px 16px; border-radius: 12px; font-weight: 700; font-size: 13.5px; display: flex; align-items: center; gap: 8px; border: 1px solid #bae6fd;">
                <i class="fa-solid fa-warehouse"></i> Espace Magasin
            </div>
        </div>

        <!-- Section: Stats -->
        <div class="stats-grid">
            <!-- Card 1: Total Produits -->
            <div class="dashboard-stats-card">
                <div class="stats-icon-wrap" style="background: #eff6ff; color: var(--primary);">
                    <i class="fa-solid fa-box"></i>
                </div>
                <div class="stats-info">
                    <span class="stats-label">Produits au catalogue</span>
                    <span class="stats-val">{{ $totalProductsCount }}</span>
                </div>
            </div>


            <!-- Card 3: En rupture -->
            <div class="dashboard-stats-card">
                <div class="stats-icon-wrap" style="background: #fef2f2; color: var(--danger);">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
                <div class="stats-info">
                    <span class="stats-label">En rupture de stock</span>
                    <span class="stats-val" style="color: {{ $outOfStockCount > 0 ? 'var(--danger)' : 'var(--text)' }};">
                        {{ $outOfStockCount }}
                    </span>
                </div>
            </div>

            <!-- Card 4: Stock critique (seuil atteint) -->
            <div class="dashboard-stats-card">
                <div class="stats-icon-wrap" style="background: #fffbeb; color: #d97706;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="stats-info">
                    <span class="stats-label">Stock critique / Alerte</span>
                    <span class="stats-val" style="color: {{ $lowStockCount > 0 ? '#d97706' : 'var(--text)' }};">
                        {{ $lowStockCount }}
                    </span>
                </div>
            </div>

            <!-- Card 5: Demandes en attente -->
            <div class="dashboard-stats-card">
                <div class="stats-icon-wrap" style="background: #faf5ff; color: #9333ea;">
                    <i class="fa-solid fa-bell {{ $pendingRequestsCount > 0 ? 'badge-blink' : '' }}"></i>
                </div>
                <div class="stats-info">
                    <span class="stats-label">Demandes en attente</span>
                    <span class="stats-val" style="color: {{ $pendingRequestsCount > 0 ? '#9333ea' : 'var(--text)' }};">
                        {{ $pendingRequestsCount }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Section: Content Grid -->
        <div class="dashboard-main-grid">
            <!-- Left Column: Demandes de réappro. récentes & Derniers produits -->
            <div style="display: flex; flex-direction: column; gap: 25px;">
                
                <!-- Demandes de réapprovisionnement récentes -->
                <div class="card" style="padding: 0;">
                    <div class="card-header-flex">
                        <h3><i class="fa-solid fa-truck-loading" style="color: var(--primary);"></i> Demandes de réappro. récentes</h3>
                        <a href="{{ route('magasinier.restock-requests.index') }}" style="font-size: 12px; color: var(--primary-light); text-decoration: none; font-weight: 700;">
                            Voir tout <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        @if ($recentRequests->isEmpty())
                            <div class="empty-state" style="padding: 30px;">
                                <i class="fa-solid fa-clipboard-list" style="font-size: 28px;"></i>
                                <p>Aucune demande de réapprovisionnement récente.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="padding: 10px 16px;">Produit</th>
                                            <th style="padding: 10px 16px; text-align: center;">Caissier</th>
                                            <th style="padding: 10px 16px; text-align: center;">Date</th>
                                            <th style="padding: 10px 16px; text-align: center;">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentRequests as $req)
                                            <tr>
                                                <td style="padding: 12px 16px; font-weight: 700; color: var(--text);">
                                                    {{ $req->product ? $req->product->name : 'Produit supprimé' }}
                                                </td>
                                                <td style="padding: 12px 16px; text-align: center;" class="td-muted">
                                                    {{ $req->user ? $req->user->name : 'Inconnu' }}
                                                </td>
                                                <td style="padding: 12px 16px; text-align: center; font-size: 12px;" class="td-muted">
                                                    {{ $req->created_at->format('d/m/Y H:i') }}
                                                </td>
                                                <td style="padding: 12px 16px; text-align: center;">
                                                    @if ($req->status === 'completed')
                                                        <span class="badge-green" style="display:inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 700;">
                                                            Traitée
                                                        </span>
                                                    @else
                                                        <span class="badge-blue" style="display:inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 700;">
                                                            En attente
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Derniers produits enrôlés -->
                <div class="card" style="padding: 0;">
                    <div class="card-header-flex">
                        <h3><i class="fa-solid fa-clock-rotate-left" style="color: var(--primary);"></i> Derniers produits enrôlés</h3>
                        <a href="{{ route('magasinier.products.index') }}" style="font-size: 12px; color: var(--primary-light); text-decoration: none; font-weight: 700;">
                            Voir tout <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        @if ($recentProducts->isEmpty())
                            <div class="empty-state" style="padding: 30px;">
                                <i class="fa-solid fa-boxes-stacked" style="font-size: 28px;"></i>
                                <p>Aucun produit enregistré.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="padding: 10px 16px;">Produit</th>
                                            <th style="padding: 10px 16px;">Catégorie</th>
                                            <th style="padding: 10px 16px; text-align: right;">Prix</th>
                                            <th style="padding: 10px 16px; text-align: center;">Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentProducts as $prod)
                                            <tr>
                                                <td style="padding: 12px 16px;">
                                                    <div style="display: flex; align-items: center; gap: 10px;">
                                                        @if ($prod->image)
                                                            <img src="{{ asset('storage/' . $prod->image) }}" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border);">
                                                        @else
                                                            <div style="width: 32px; height: 32px; border-radius: 6px; background: #f0f4f8; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 14px;">
                                                                <i class="fa-solid fa-box"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div style="font-weight: 700; color: var(--text);">{{ $prod->name }}</div>
                                                            <div style="font-size: 11px; color: var(--muted); font-family: monospace;">{{ $prod->reference }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="padding: 12px 16px;">
                                                    <span class="badge-blue" style="display:inline-block; padding: 2px 8px; border-radius: 99px; font-size: 11px; font-weight: 700;">
                                                        {{ $prod->category_name }}
                                                    </span>
                                                </td>
                                                <td style="padding: 12px 16px; text-align: right; font-weight: 700; color: var(--primary);">
                                                    {{ number_format($prod->price, 0, ',', ' ') }} F
                                                </td>
                                                <td style="padding: 12px 16px; text-align: center;">
                                                    @if ($prod->stock <= $prod->stock_threshold)
                                                        <span style="color: var(--danger); font-weight: 800; background: #fef2f2; padding: 2px 6px; border-radius: 4px; font-size: 12px; border: 1px solid rgba(225,29,72,0.1);">
                                                            {{ $prod->stock }}
                                                        </span>
                                                    @else
                                                        <span style="color: var(--ok); font-weight: 700; background: #e8f9f0; padding: 2px 6px; border-radius: 4px; font-size: 12px; border: 1px solid rgba(15,118,110,0.1);">
                                                            {{ $prod->stock }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <!-- Right Column: Alertes, Catégories, Fournisseurs -->
            <div style="display: flex; flex-direction: column; gap: 25px;">
                
                <!-- Stocks faibles / critiques -->
                <div class="card" style="padding: 0;">
                    <div class="card-header-flex">
                        <h3><i class="fa-solid fa-triangle-exclamation" style="color: #d97706;"></i> Stocks faibles / critiques</h3>
                        <a href="{{ route('magasinier.products.threshold') }}" style="font-size: 12px; color: var(--primary-light); text-decoration: none; font-weight: 700;">
                            Voir tout <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body" style="padding: 0;">
                        @if ($lowStockProducts->isEmpty())
                            <div class="empty-state" style="padding: 30px;">
                                <i class="fa-solid fa-square-check" style="font-size: 28px; color: var(--ok);"></i>
                                <p>Tous les produits ont un niveau de stock satisfaisant.</p>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th style="padding: 10px 16px;">Produit</th>
                                            <th style="padding: 10px 16px; text-align: center;">Stock</th>
                                            <th style="padding: 10px 16px; text-align: center;">Seuil</th>
                                            <th style="padding: 10px 16px; text-align: right;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($lowStockProducts as $product)
                                            <tr>
                                                <td style="padding: 12px 16px; font-weight: 700; color: var(--text);">
                                                    {{ $product->name }}
                                                </td>
                                                <td style="padding: 12px 16px; text-align: center;">
                                                    @if ($product->stock === 0)
                                                        <span class="badge-blink" style="color: var(--danger); font-weight: 800; background: #fff1f2; padding: 2px 6px; border-radius: 4px; font-size: 12px; border: 1px solid rgba(225,29,72,0.15);">
                                                            Rupture
                                                        </span>
                                                    @else
                                                        <span style="color: var(--danger); font-weight: 700; background: #fffbeb; padding: 2px 6px; border-radius: 4px; font-size: 12px; border: 1px solid rgba(217,119,6,0.15);">
                                                            {{ $product->stock }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td style="padding: 12px 16px; text-align: center;" class="td-muted">
                                                    {{ $product->stock_threshold }}
                                                </td>
                                                <td style="padding: 12px 16px; text-align: right;" class="td-actions">
                                                    <a href="{{ route('magasinier.products.edit', $product->id) }}" class="btn-icon" title="Réapprovisionner">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Répartition par Catégorie -->
                <div class="card" style="padding: 0;">
                    <div class="card-header-flex">
                        <h3><i class="fa-solid fa-tags" style="color: var(--primary);"></i> Répartition du stock par Catégorie</h3>
                        <a href="{{ route('magasinier.categories.index') }}" style="font-size: 12px; color: var(--primary-light); text-decoration: none; font-weight: 700;">
                            Gérer <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body" style="padding: 10px 0;">
                        @if ($categoryStats->isEmpty())
                            <div class="empty-state" style="padding: 30px;">
                                <i class="fa-solid fa-tag" style="font-size: 28px;"></i>
                                <p>Aucune catégorie enregistrée.</p>
                            </div>
                        @else
                            @foreach ($categoryStats->take(5) as $stat)
                                @php
                                    $pct = $totalStock > 0 ? ($stat->total_stock / $totalStock) * 100 : 0;
                                @endphp
                                <div class="category-stat-item">
                                    <div class="category-stat-top">
                                        <span class="category-name-label">
                                            <i class="fa-solid fa-hashtag" style="color: var(--muted); font-size: 12px;"></i>
                                            {{ $stat->category_name }}
                                        </span>
                                        <span class="category-stock-val">
                                            {{ number_format($stat->total_stock, 0, ',', ' ') }} u
                                        </span>
                                    </div>
                                    <div class="category-meta-info">
                                        {{ $stat->product_count }} produit(s) • {{ number_format($pct, 1) }}% du stock
                                    </div>
                                    <div class="progress-bar-container">
                                        <div class="progress-bar-fill" style="width: {{ $pct }}%; background: {{ $pct > 50 ? 'var(--primary)' : ($pct > 20 ? 'var(--primary-light)' : 'var(--secondary)') }};"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Fournisseurs Partenaires -->
                <div class="card" style="padding: 0;">
                    <div class="card-header-flex">
                        <h3><i class="fa-solid fa-truck" style="color: var(--primary);"></i> Fournisseurs Partenaires</h3>
                        <a href="{{ route('magasinier.suppliers.index') }}" style="font-size: 12px; color: var(--primary-light); text-decoration: none; font-weight: 700;">
                            Gérer <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body" style="padding: 10px 0;">
                        @if ($supplierStats->isEmpty())
                            <div class="empty-state" style="padding: 30px;">
                                <i class="fa-solid fa-truck-field" style="font-size: 28px;"></i>
                                <p>Aucun fournisseur enregistré.</p>
                            </div>
                        @else
                            @foreach ($supplierStats as $supplier)
                                <div class="supplier-stat-item">
                                    <div class="supplier-info-wrap">
                                        <span class="supplier-name-title">{{ $supplier->name }}</span>
                                        @if ($supplier->contact_person)
                                            <span class="supplier-contact-sub">Contact : {{ $supplier->contact_person }}</span>
                                        @else
                                            <span class="supplier-contact-sub">Aucun contact enregistré</span>
                                        @endif
                                    </div>
                                    <span class="supplier-products-badge">
                                        {{ $supplier->products_count }} réf.
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
