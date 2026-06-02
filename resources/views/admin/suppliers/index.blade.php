@extends('admin.layouts.app')

@section('title', 'Fournisseurs')
@section('page-title', 'Fournisseurs')

@section('content')

    <div class="list-header">
        <div>
            <h2 class="list-title"><i class="fa-solid fa-truck"></i> Fournisseurs</h2>
            <p class="list-sub">{{ $suppliers->count() }} fournisseur(s) enregistré(s)</p>
        </div>
        <a href="{{ route('admin.suppliers.create') }}" class="btn btn-yellow">
            <i class="fa-solid fa-plus"></i> Nouveau fournisseur
        </a>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if ($suppliers->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-truck"></i>
                    <p>Aucun fournisseur pour l'instant.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:48px; text-align:center;">#</th>
                                <th style="text-align:center;">Entreprise</th>
                                <th style="text-align:center;">Responsable</th>
                                <th style="text-align:center;">Téléphone</th>
                                <th style="text-align:center;">Email</th>
                                <th style="text-align:center;">Ville</th>
                                <th style="text-align:center;">Statut</th>
                                <th style="width:110px; text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suppliers as $i => $sup)
                                <tr>
                                    <td class="td-id" style="text-align:center;">{{ $i + 1 }}</td>
                                    <td>
                                        <div class="supplier-name" style="justify-content:center;">
                                            <div class="supplier-avatar">{{ strtoupper(substr($sup->name, 0, 1)) }}</div>
                                            <div style="text-align:left;">
                                                <div class="td-name">{{ $sup->name }}</div>
                                                @if ($sup->website)
                                                    <a href="{{ $sup->website }}" target="_blank" class="td-link">
                                                        <i class="fa-solid fa-globe" style="font-size:10px;"></i> Site web
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="td-muted" style="text-align:center;">{{ $sup->contact_person ?? '—' }}</td>
                                    <td class="td-muted" style="text-align:center;">{{ $sup->phone ?? '—' }}</td>
                                    <td class="td-muted" style="text-align:center;">{{ $sup->email ?? '—' }}</td>
                                    <td class="td-muted" style="text-align:center;">{{ $sup->city ?? '—' }}</td>
                                    <td style="text-align:center;">
                                        @if ($sup->is_active)
                                            <span class="badge badge-green">Actif</span>
                                        @else
                                            <span class="badge badge-gray">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="td-actions" style="justify-content:center;">
                                        <a href="{{ route('admin.suppliers.edit', $sup) }}" class="btn-icon" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <button type="button" class="btn-icon btn-icon-red" title="Supprimer" 
                                            onclick="confirmDelete('{{ $sup->id }}', '{{ addslashes($sup->name) }}')">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $sup->id }}" action="{{ route('admin.suppliers.destroy', $sup) }}" method="POST" style="display:none;">
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
                text-align: left;
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

            .td-link {
                font-size: 11.5px;
                color: #004d99;
                text-decoration: none;
            }

            .td-link:hover {
                text-decoration: underline;
            }

            .supplier-name {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .supplier-avatar {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                background: #004d99;
                color: #fff;
                font-size: 13px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
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
                    title: 'Supprimer le fournisseur ?',
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
