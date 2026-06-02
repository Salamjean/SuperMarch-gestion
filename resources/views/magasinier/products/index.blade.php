@extends('magasinier.layouts.app')

@section('title', 'Produits magasinier')
@section('page_title', 'Produits')

@section('content')
    <div class="card" style="padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h2 style="font-size:18px;"><i class="fa-solid fa-box"></i> Produits</h2>
            <a href="{{ route('magasinier.products.create') }}" class="mg-logout-btn" style="text-decoration:none;">
                <i class="fa-solid fa-plus"></i> Nouveau produit
            </a>
        </div>

        @if (session('success'))
            <div
                style="margin-bottom:10px; padding:10px; border:1px solid #86efac; background:#f0fdf4; color:#166534; border-radius:8px;">
                {{ session('success') }}
            </div>
        @endif

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Reference</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Produit</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Categorie</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Stock</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Ajoute par</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $product->reference }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $product->name }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $product->category_name }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $product->stock }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $product->creator->name ?? 'Inconnu' }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="{{ route('magasinier.products.edit', $product) }}" class="mg-logout-btn" style="padding:6px 10px; font-size:12px; margin:0; text-decoration:none; background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;" title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('magasinier.products.destroy', $product) }}" method="POST" onsubmit="return confirmDelete(event, this);" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mg-logout-btn" style="padding:6px 10px; font-size:12px; margin:0; background:#fff1f2; color:#be123c; border:1px solid #fecdd3;" title="Supprimer">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:14px;">Aucun produit.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
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
