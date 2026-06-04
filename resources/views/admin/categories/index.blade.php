@extends('admin.layouts.app')

@section('title', 'Catégories')
@section('page-title', 'Catégories')

@section('content')

    <div class="list-header">
        <div>
            <h2 class="list-title"><i class="fa-solid fa-tags"></i> Catégories</h2>
            <p class="list-sub">{{ $categories->count() }} catégorie(s) enregistrée(s)</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-yellow">
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
                                            <span class="color-dot" style="background:{{ $cat->color }};"></span>
                                        </div>
                                    </td>
                                    <td class="td-name" style="text-align:center;">{{ $cat->name }}</td>
                                    <td class="td-muted" style="text-align:center;">{{ $cat->description ?? '—' }}</td>
                                    <td class="td-muted" style="text-align:center;">
                                        {{ $cat->creator ? $cat->creator->name : 'Non trace (ancien enregistrement)' }}
                                    </td>
                                    <td style="text-align:center;">
                                        @if ($cat->is_active)
                                            <span class="badge badge-green">Actif</span>
                                        @else
                                            <span class="badge badge-gray">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="td-muted" style="text-align:center;">{{ $cat->created_at->format('d/m/Y') }}</td>
                                    <td class="td-actions" style="justify-content:center;">
                                        <a href="{{ route('admin.products.index', ['category' => $cat->name]) }}" class="btn-icon" title="Voir les produits">
                                            <i class="fa-solid fa-box-open"></i>
                                        </a>
                                        <a href="{{ route('admin.categories.edit', $cat) }}" class="btn-icon" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <button type="button" class="btn-icon btn-icon-red" title="Supprimer"
                                            onclick="confirmDelete('{{ $cat->id }}', '{{ addslashes($cat->name) }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $cat->id }}"
                                            action="{{ route('admin.categories.destroy', $cat) }}" method="POST"
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

            .color-dot {
                display: inline-block;
                width: 20px;
                height: 20px;
                border-radius: 50%;
                border: 2px solid rgba(0, 0, 0, .1);
            }

            .badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 10px;
                border-radius: 10px;
                font-size: 11px;
                font-weight: 700;
            }

            .badge-green {
                background: #e8f9f0;
                color: #1a9e5a;
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

            .btn-icon-red {
                color: #e11d48;
            }

            .btn-icon-red:hover {
                background: #fff1f2;
                border-color: #ffd0d0;
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
        </style>
    @endpush

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
