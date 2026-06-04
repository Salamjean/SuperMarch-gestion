@extends('magasinier.layouts.app')

@section('title', 'Catégories')
@section('page_title', 'Gestion des catégories')

@section('content')

    <div class="list-header">
        <div>
            <h2 class="list-title"><i class="fa-solid fa-tags"></i> Catégories</h2>
            <p class="list-sub">{{ $categories->count() }} catégorie(s) enregistrée(s)</p>
        </div>
        <a href="{{ route('magasinier.categories.create') }}" class="mg-logout-btn" style="text-decoration:none;">
            <i class="fa-solid fa-plus"></i> Nouvelle catégorie
        </a>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if ($categories->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-tags"></i>
                    <p>Aucune catégorie pour l'instant.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:48px; text-align:center;">#</th>
                                <th style="text-align:center;">Identité</th>
                                <th style="text-align:center;">Nom</th>
                                <th style="text-align:center;">Description</th>
                                <th style="text-align:center;">Ajoutée par</th>
                                <th style="text-align:center;">Statut</th>
                                <th style="text-align:center;">Créée le</th>
                                <th style="width:110px; text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $i => $cat)
                                <tr>
                                    <td class="td-id" style="text-align:center;">{{ $i + 1 }}</td>
                                    <td>
                                        <div style="display:flex; justify-content:center;">
                                            <span class="color-dot" style="display:inline-block; width:20px; height:20px; border-radius:50%; border:2px solid rgba(0, 0, 0, .1); background:{{ $cat->color }};"></span>
                                        </div>
                                    </td>
                                    <td class="td-name" style="text-align:center;">{{ $cat->name }}</td>
                                    <td class="td-muted" style="text-align:center;">{{ $cat->description ?? '—' }}</td>
                                    <td class="td-muted" style="text-align:center;">
                                        {{ $cat->creator ? $cat->creator->name : 'Inconnu' }}
                                    </td>
                                    <td style="text-align:center;">
                                        @if ($cat->is_active)
                                            <span class="badge-green" style="display:inline-flex; align-items:center; padding:3px 10px; border-radius:10px; font-size:11px; font-weight:700;">Actif</span>
                                        @else
                                            <span class="badge-gray" style="display:inline-flex; align-items:center; padding:3px 10px; border-radius:10px; font-size:11px; font-weight:700;">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="td-muted" style="text-align:center;">{{ $cat->created_at->format('d/m/Y') }}</td>
                                    <td class="td-actions" style="justify-content:center;">
                                        <a href="{{ route('magasinier.products.index', ['category' => $cat->name]) }}" class="btn-icon" title="Voir les produits">
                                            <i class="fa-solid fa-box-open"></i>
                                        </a>
                                        <a href="{{ route('magasinier.categories.edit', $cat) }}" class="btn-icon" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <button type="button" class="btn-icon btn-icon-red" title="Supprimer"
                                            onclick="confirmDelete('{{ $cat->id }}', '{{ addslashes($cat->name) }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $cat->id }}"
                                            action="{{ route('magasinier.categories.destroy', $cat) }}" method="POST"
                                            style="display:none;">
                                            @csrf @method('DELETE')
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
            function confirmDelete(id, name) {
                Swal.fire({
                    title: 'Supprimer la catégorie ?',
                    text: `Êtes-vous sûr de vouloir supprimer "${name}" ? Cette action est irréversible.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#7a94aa',
                    confirmButtonText: 'Oui, supprimer',
                    cancelButtonText: 'Annuler',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                })
            }
        </script>
    @endpush

@endsection
