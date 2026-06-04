@extends('magasinier.layouts.app')

@section('title', 'Produits')
@section('page_title', 'Gestion des produits')

@section('content')

    <div class="list-header">
        <div>
            <h2 class="list-title"><i class="fa-solid fa-box"></i> Produits</h2>
            <p class="list-sub">{{ $products->count() }} produit(s) en inventaire</p>
        </div>
        <a href="{{ route('magasinier.products.create') }}" class="mg-logout-btn" style="text-decoration:none;">
            <i class="fa-solid fa-plus"></i> Nouveau produit
        </a>
    </div>

    @if(isset($selectedCategory) && $selectedCategory)
        <div style="background: #e0f2fe; border: 1px solid #bae6fd; color: #0369a1; padding: 12px 20px; border-radius: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; font-size: 14px;">
            <span><i class="fa-solid fa-filter" style="margin-right: 6px;"></i> Filtré par la catégorie : <strong>{{ $selectedCategory }}</strong></span>
            <a href="{{ route('magasinier.products.index') }}" style="color: #0369a1; text-decoration: none; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 4px; border: 1px solid rgba(3, 105, 161, 0.2); padding: 4px 10px; border-radius: 8px; background: rgba(3, 105, 161, 0.05);">
                <i class="fa-solid fa-xmark"></i> Réinitialiser
            </a>
        </div>
    @endif

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
                                        <span class="badge-gray"
                                            style="display:inline-block; padding:3px 10px; border-radius:10px; font-size:11px; font-weight:700; font-family: monospace; background:#f1f5f9; color:#475569;">{{ $product->reference }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="td-name">{{ $product->name }}</div>
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="badge-blue"
                                            style="display:inline-block; padding:3px 10px; border-radius:10px; font-size:11px; font-weight:700;">{{ $product->category_name }}</span>
                                    </td>
                                    <td style="text-align:center;" class="td-muted">
                                        {{ $product->supplier->name ?? 'Aucun' }}
                                    </td>
                                    <td style="text-align:center;" class="td-muted">
                                        {{ $product->creator ? $product->creator->name : 'Inconnu' }}
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
                                    <td class="td-actions" style="justify-content:center;">
                                        <a href="{{ route('magasinier.products.edit', $product) }}" class="btn-icon"
                                            title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <form action="{{ route('magasinier.products.destroy', $product) }}" method="POST" onsubmit="return confirmDelete(event, this);" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icon btn-icon-red" title="Supprimer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(event, form) {
                event.preventDefault();
                Swal.fire({
                    title: 'Supprimer le produit ?',
                    text: "Cette action est irréversible et supprimera définitivement le produit du catalogue.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: 'var(--danger)',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            }
        </script>
    @endpush
@endsection
