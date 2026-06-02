@extends('admin.layouts.app')

@section('title', 'Liste des employés')
@section('page-title', 'Employés')

@section('content')

    <div class="list-header">
        <div>
            <h2 class="list-title">
                <i class="fa-solid fa-users"></i> {{ isset($isBlocked) ? 'Employés bloqués' : 'Employés enregistrés' }}
            </h2>
            <p class="list-sub">{{ $employees->count() }} employé(s) au total</p>
        </div>
        <div style="display:flex; gap:10px;">
            @if(isset($isBlocked))
                <a href="{{ route('admin.employees.index') }}" class="btn btn-primary">
                    <i class="fa-solid fa-users"></i> Voir actifs
                </a>
            @else
                <a href="{{ route('admin.employees.blocked') }}" class="btn btn-cancel">
                    <i class="fa-solid fa-user-slash"></i> Comptes bloqués
                </a>
                <a href="{{ route('admin.employees.create') }}" class="btn btn-yellow">
                    <i class="fa-solid fa-user-plus"></i> Nouveau
                </a>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if ($employees->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-users"></i>
                    <p>Aucun employé enregistré pour l'instant.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:48px; text-align:center;">#</th>
                                <th style="text-align:center;">Employé</th>
                                <th style="text-align:center;">Poste / Département</th>
                                <th style="text-align:center;">Contact</th>
                                <th style="text-align:center;">Arrivée</th>
                                <th style="width:130px; text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $i => $employee)
                                <tr>
                                    <td class="td-id" style="text-align:center;">{{ $i + 1 }}</td>
                                    <td>
                                        <div style="display:flex; align-items:center; gap:10px; justify-content:center;">
                                            <div class="emp-avatar">{{ strtoupper(substr($employee->name, 0, 1)) }}</div>
                                            <div style="text-align:left;">
                                                <div style="display:flex; align-items:center; gap:6px;">
                                                    <div class="td-name">{{ $employee->name }}</div>
                                                    <span style="font-size:11px; font-weight:700; background:#eef2ff; color:#4338ca; padding:1px 6px; border-radius:4px; border:1px solid #e0e7ff;"><i class="fa-solid fa-id-badge" style="font-size: 10px; margin-right: 3px;"></i>{{ $employee->login_code }}</span>
                                                </div>
                                                <div style="font-size:11.5px; color:#7a94aa;">{{ $employee->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align:center">
                                        <div style="font-weight:600; color:#004d99; font-size:13px;">{{ $employee->position ?? '—' }}</div>
                                        <div style="font-size:11px; color:#7a94aa; text-transform:uppercase; letter-spacing:0.5px;">{{ $employee->department ?? 'Non défini' }}</div>
                                    </td>
                                    <td style="text-align:center; color:#5a7a99; font-size:13px;">
                                        <i class="fa-solid fa-phone" style="font-size:11px; opacity:0.5;"></i> {{ $employee->phone ?? '—' }}
                                    </td>
                                    <td style="text-align:center; color:#7a94aa; font-size:12.5px;">
                                        {{ $employee->hire_date ? \Carbon\Carbon::parse($employee->hire_date)->format('d/m/Y') : $employee->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="td-actions" style="justify-content:center;">
                                        @if(isset($isBlocked))
                                            <button class="btn-icon btn-icon-green" title="Débloquer" onclick="confirmUnblock('{{ $employee->id }}')">
                                                <i class="fa-solid fa-user-check"></i>
                                            </button>
                                            <form id="unblock-form-{{ $employee->id }}" action="{{ route('admin.employees.unblock', $employee->id) }}" method="POST" style="display: none;">
                                                @csrf
                                            </form>
                                        @else
                                            <a href="{{ route('admin.employees.show', $employee) }}" class="btn-icon" title="Détails">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.employees.edit', $employee) }}" class="btn-icon btn-icon-yellow" title="Modifier" style="color: #f59e0b;">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button class="btn-icon btn-icon-red" title="Bloquer" onclick="confirmBlock('{{ $employee->id }}')">
                                                <i class="fa-solid fa-user-slash"></i>
                                            </button>
                                        @endif

                                        <form id="delete-form-{{ $employee->id }}" action="{{ route('admin.employees.destroy', $employee) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
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

            .td-actions {
                display: flex;
                gap: 6px;
                align-items: center;
            }

            .emp-avatar {
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
                transition: all 0.2s;
            }

            .btn-icon:hover {
                background: #f0f7ff;
                transform: translateY(-1px);
            }

            .btn-icon-red {
                color: #e11d48 !important;
            }
            .btn-icon-red:hover {
                background: #fff1f2;
                border-color: #ffd0d0;
            }

            .btn-icon-green {
                color: #059669 !important;
            }
            .btn-icon-green:hover {
                background: #ecfdf5;
                border-color: #a7f3d0;
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
            function confirmBlock(id) {
                Swal.fire({
                    title: 'Bloquer l\'employé ?',
                    text: "L'employé ne pourra plus se connecter mais ses données seront conservées.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e11d48',
                    cancelButtonColor: '#7a94aa',
                    confirmButtonText: 'Oui, bloquer',
                    cancelButtonText: 'Annuler',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                })
            }

            function confirmUnblock(id) {
                Swal.fire({
                    title: 'Débloquer l\'employé ?',
                    text: "L'employé pourra de nouveau se connecter au système.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#059669',
                    cancelButtonColor: '#7a94aa',
                    confirmButtonText: 'Oui, débloquer',
                    cancelButtonText: 'Annuler',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('unblock-form-' + id).submit();
                    }
                })
            }
        </script>
    @endpush

@endsection
