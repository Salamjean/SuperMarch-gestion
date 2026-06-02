@extends('magasinier.layouts.app')

@section('title', 'Tableau de bord magasinier')
@section('page_title', 'Tableau de bord magasinier')

@push('styles')
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px;
        }

        .stat-title {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .stat-value {
            font-size: 30px;
            font-weight: 800;
            margin-top: 6px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        @media (max-width: 980px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
            font-size: 14px;
        }

        th,
        td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid var(--border);
        }

        th {
            color: var(--muted);
            font-size: 12px;
            text-transform: uppercase;
        }

        .badge {
            display: inline-block;
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-danger {
            background: #ffe4e6;
            color: var(--danger);
        }

        .badge-ok {
            background: #ccfbf1;
            color: var(--ok);
        }

        .section-title {
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <div class="stats">
            <div class="card">
                <p class="stat-title">Produits total</p>
                <p class="stat-value">{{ $products->count() }}</p>
            </div>
            <div class="card">
                <p class="stat-title">Produits au seuil</p>
                <p class="stat-value" style="color: var(--danger);">{{ $lowStockProducts->count() }}</p>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h2 class="section-title"><i class="fa-solid fa-triangle-exclamation" style="color: var(--danger);"></i>
                    Stock critique</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Stock</th>
                            <th>Seuil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($lowStockProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td><span class="badge badge-danger">{{ $product->stock }}</span></td>
                                <td>{{ $product->stock_threshold }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3">Aucun produit au seuil.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2 class="section-title"><i class="fa-solid fa-boxes-stacked"></i> Etat du stock</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Stock</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    @if ($product->stock <= $product->stock_threshold)
                                        <span class="badge badge-danger">Seuil atteint</span>
                                    @else
                                        <span class="badge badge-ok">Normal</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
