@extends('magasinier.layouts.app')

@section('title', 'Gestion des Fournisseurs')
@section('page_title', 'Fournisseurs')

@section('content')
    <div class="card" style="padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:14px;">
            <div>
                <h2 style="font-size:18px; margin:0; font-weight:800; color:var(--primary); display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-truck"></i> Fournisseurs
                </h2>
                <p style="font-size:12px; color:var(--muted); margin:3px 0 0 0;">
                    {{ $suppliers->count() }} fournisseur(s) enregistré(s) dans le catalogue
                </p>
            </div>
            <a href="{{ route('magasinier.suppliers.create') }}" class="mg-logout-btn" style="text-decoration:none;">
                <i class="fa-solid fa-plus"></i> Nouveau fournisseur
            </a>
        </div>

        @if (session('success'))
            <div style="margin-bottom:15px; padding:10px; border:1px solid #86efac; background:#f0fdf4; color:#166534; border-radius:8px; font-size:13.5px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive" style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:850px;">
                <thead>
                    <tr>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; width:48px;">#</th>
                        <th style="text-align:left; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Entreprise</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Responsable</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Téléphone / E-mail</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Ville</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Statut</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; width:120px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suppliers as $i => $supplier)
                        <tr>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:12px; font-weight:600;">
                                {{ $i + 1 }}
                            </td>
                            <td style="text-align:left; padding:10px; border-bottom:1px solid var(--border);">
                                <div style="display:flex; align-items:center; gap:10px;">
                                    <div class="supplier-avatar">{{ strtoupper(substr($supplier->name, 0, 1)) }}</div>
                                    <div style="text-align:left;">
                                        <div style="font-weight:700; color:var(--text); font-size:13.5px;">{{ $supplier->name }}</div>
                                        @if ($supplier->website)
                                            <a href="{{ $supplier->website }}" target="_blank" style="font-size:11px; color:var(--primary-light); text-decoration:none;">
                                                <i class="fa-solid fa-globe" style="font-size:10px;"></i> Site web
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--text); font-weight:500;">
                                {{ $supplier->contact_person ?? '—' }}
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                                <div style="font-size:13px; font-weight:600; color:var(--text);">{{ $supplier->phone ?? '—' }}</div>
                                <div style="font-size:11px; color:var(--muted);">{{ $supplier->email ?? '—' }}</div>
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted);">
                                {{ $supplier->city ?? '—' }}
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                                @if ($supplier->is_active)
                                    <span class="badge" style="background:#e6fdf5; color:#0f766e; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700;">Actif</span>
                                @else
                                    <span class="badge" style="background:#fef2f2; color:#b91c1c; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700;">Inactif</span>
                                @endif
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <a href="{{ route('magasinier.suppliers.edit', $supplier) }}" class="mg-logout-btn" 
                                        style="padding:6px 10px; font-size:12px; margin:0; text-decoration:none; background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;" 
                                        title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('magasinier.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirmDelete(event, this);" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="mg-logout-btn" 
                                            style="padding:6px 10px; font-size:12px; margin:0; background:#fff1f2; color:#be123c; border:1px solid #fecdd3; cursor:pointer;" 
                                            title="Supprimer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:30px; color:var(--muted);">Aucun fournisseur enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('styles')
        <style>
            .supplier-avatar {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                background: var(--primary);
                color: #fff;
                font-size: 13px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            table tbody tr {
                transition: background 0.15s;
            }
            table tbody tr:hover {
                background: #f8fafc;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function confirmDelete(event, form) {
                event.preventDefault();
                Swal.fire({
                    title: 'Supprimer le fournisseur ?',
                    text: "Cette action est irréversible et supprimera définitivement le fournisseur.",
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
