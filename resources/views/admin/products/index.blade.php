@extends('admin.layouts.app')

@section('title', 'Produits')
@section('page-title', 'Gestion des produits')

@section('content')

    <div class="list-header">
        <div>
            <h2 class="list-title"><i class="fa-solid fa-box"></i> Produits</h2>
            <p class="list-sub">{{ $products->count() }} produit(s) en inventaire</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-yellow">
            <i class="fa-solid fa-plus"></i> Nouveau produit
        </a>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if ($products->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-box-open"></i>
                    <p>Aucun produit enregistré pour l'instant.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:48px; text-align:center;">#</th>
                                <th style="text-align:center;">Image</th>
                                <th style="text-align:center;">Référence</th>
                                <th style="text-align:center;">Produit</th>
                                <th style="text-align:center;">Catégorie</th>
                                <th style="text-align:center;">Fournisseur</th>
                                <th style="text-align:center;">Ajouté par</th>
                                <th style="text-align:center;">Prix</th>
                                <th style="text-align:center;">Stock</th>
                                <th style="text-align:center;">QR Code</th>
                                <th style="width:110px; text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $i => $product)
                                <tr>
                                    <td class="td-id" style="text-align:center;">{{ $i + 1 }}</td>
                                    <td>
                                        <div style="display:flex; justify-content:center;">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                    style="width:45px; height:45px; border-radius:10px; object-fit:cover; border:1px solid #e2eaf3;">
                                            @else
                                                <div
                                                    style="width:45px; height:45px; border-radius:10px; background:#f0f4f8; display:flex; align-items:center; justify-content:center; color:#cbd5e1; border:1px solid #e2eaf3;">
                                                    <i class="fa-solid fa-image" style="font-size:18px;"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="badge badge-gray"
                                            style="font-family: monospace; background:#f1f5f9; color:#475569;">{{ $product->reference }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="td-name">{{ $product->name }}</div>
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="badge badge-gray"
                                            style="background:#eef4ff; color:#004d99; border:1px solid rgba(0,77,153,0.1);">{{ $product->category_name }}</span>
                                    </td>
                                    <td style="text-align:center;" class="td-muted">
                                        {{ $product->supplier->name ?? 'Aucun' }}
                                    </td>
                                    <td style="text-align:center;" class="td-muted">
                                        {{ $product->creator ? $product->creator->name : 'Non trace (ancien enregistrement)' }}
                                    </td>
                                    <td style="text-align:center; font-weight:700; color:#1a2840;">
                                        {{ number_format($product->price, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td style="text-align:center;">
                                        @if ($product->stock <= ($product->stock_threshold ?? 5))
                                            <span style="color:#e11d48; font-weight:700;"
                                                title="Seuil: {{ $product->stock_threshold }}">
                                                <i class="fa-solid fa-triangle-exclamation"></i> {{ $product->stock }}
                                            </span>
                                        @else
                                            <span style="color:#059669; font-weight:700;">{{ $product->stock }}</span>
                                        @endif
                                    </td>
                                    <td style="text-align:center;">
                                        @if ($product->reference)
                                            <div class="qr-thumbnail"
                                                onclick="viewQR('{{ $product->reference }}', '{{ addslashes($product->name) }}')">
                                                @if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                                                    {!! QrCode::size(100)->generate($product->reference) !!}
                                                @else
                                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ $product->reference }}"
                                                        alt="QR">
                                                @endif
                                            </div>
                                        @else
                                            <span class="td-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="td-actions" style="justify-content:center;">
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-icon"
                                            title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
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

    @push('styles')
        <style>
            .list-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 20px;
            }

            .list-title {
                font-size: 17px;
                font-weight: 800;
                color: #004d99;
                margin: 0 0 3px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .list-sub {
                font-size: 12.5px;
                color: #7a94aa;
                margin: 0;
            }

            .data-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 13.5px;
            }

            .data-table thead tr {
                background: #f5f9ff;
                border-bottom: 1.5px solid #e2eaf3;
            }

            .data-table th {
                padding: 11px 16px;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .05em;
                color: #7a94aa;
            }

            .data-table tbody tr {
                border-bottom: 1px solid #f0f4f8;
                transition: background .15s;
            }

            .data-table tbody tr:hover {
                background: #f8fbff;
            }

            .data-table td {
                padding: 12px 16px;
                vertical-align: middle;
            }

            .td-id {
                color: #a0b5c8;
                font-size: 12px;
                font-weight: 600;
            }

            .td-name {
                font-weight: 600;
                color: #1a2e44;
            }

            .td-muted {
                color: #7a94aa;
            }

            .td-actions {
                display: flex;
                gap: 6px;
                align-items: center;
            }

            .badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 10px;
                border-radius: 10px;
                font-size: 11px;
                font-weight: 700;
            }

            .badge-gray {
                background: #f0f4f8;
                color: #7a94aa;
            }

            .btn-icon {
                width: 30px;
                height: 30px;
                border-radius: 7px;
                border: 1px solid #e2eaf3;
                background: #fff;
                color: #004d99;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                cursor: pointer;
                text-decoration: none;
                transition: background .15s;
            }

            .btn-icon:hover {
                background: #eef4ff;
            }

            .empty-state {
                text-align: center;
                padding: 48px 20px;
                color: #a0b5c8;
                font-size: 14px;
            }

            .empty-state i {
                font-size: 36px;
                margin-bottom: 12px;
                display: block;
            }

            .qr-thumbnail {
                width: 45px;
                height: 45px;
                cursor: zoom-in;
                border: 1px solid #e2eaf3;
                padding: 3px;
                border-radius: 8px;
                background: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto;
                transition: transform 0.2s;
            }

            .qr-thumbnail:hover {
                transform: scale(1.1);
                border-color: #004d99;
            }

            .qr-thumbnail img,
            .qr-thumbnail svg {
                width: 100%;
                height: 100%;
                object-fit: contain;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function viewQR(code, name) {
                @if (class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode'))
                    const qrHtml = `{!! QrCode::size(250)->generate('${code}') !!}`;
                    Swal.fire({
                        title: `QR Code : ${name}`,
                        text: `Référence : ${code}`,
                        html: `<div style="display:flex; justify-content:center; margin-top:20px;">${qrHtml}</div>`,
                        confirmButtonColor: '#004d99',
                        confirmButtonText: 'Fermer'
                    });
                @else
                    Swal.fire({
                        title: `QR Code : ${name}`,
                        text: `Référence : ${code}`,
                        imageUrl: `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${code}`,
                        imageWidth: 250,
                        imageHeight: 250,
                        imageAlt: 'QR Code',
                        confirmButtonColor: '#004d99',
                        confirmButtonText: 'Fermer'
                    });
                @endif
            }
        </script>
    @endpush

@endsection
