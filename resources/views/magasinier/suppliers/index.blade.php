@extends('magasinier.layouts.app')

@section('title', 'Gestion des Fournisseurs')
@section('page_title', 'Fournisseurs')

@section('content')
    <div class="card" style="padding:18px; margin: 0 auto; max-width: 1100px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
            <h2 style="font-size:18px; margin:0; font-weight:800; color:var(--primary);"><i class="fa-solid fa-truck"></i> Fournisseurs</h2>
            <a href="{{ route('magasinier.suppliers.create') }}" class="mg-logout-btn" style="text-decoration:none;">
                <i class="fa-solid fa-plus"></i> Nouveau fournisseur
            </a>
        </div>

        @if (session('success'))
            <div style="margin-bottom:10px; padding:10px; border:1px solid #86efac; background:#f0fdf4; color:#166534; border-radius:8px;">
                {{ session('success') }}
            </div>
        @endif

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Nom</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Contact</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Téléphone / E-mail</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Ville</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">Statut</th>
                    <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); width:120px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); font-weight:700;">
                            {{ $supplier->name }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $supplier->contact_person ?? '—' }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            <div style="font-size:13px; font-weight:600;">{{ $supplier->phone ?? '—' }}</div>
                            <div style="font-size:11px; color:var(--muted);">{{ $supplier->email ?? '—' }}</div>
                        </td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            {{ $supplier->city ?? '—' }}</td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            @if ($supplier->is_active)
                                <span class="badge" style="background:#e6fdf5; color:#0f766e; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700;">Actif</span>
                            @else
                                <span class="badge" style="background:#fef2f2; color:#b91c1c; padding:3px 10px; border-radius:999px; font-size:11px; font-weight:700;">Inactif</span>
                            @endif
                        </td>
                        <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                            <div style="display:flex; gap:6px; justify-content:center;">
                                <a href="{{ route('magasinier.suppliers.edit', $supplier) }}" class="mg-logout-btn" style="padding:6px 10px; font-size:12px; margin:0; text-decoration:none; background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;" title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('magasinier.suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirmDelete(event, this);" style="display:inline;">
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
                        <td colspan="6" style="text-align:center; padding:14px;">Aucun fournisseur enregistré.</td>
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
                    title: 'Supprimer le fournisseur ?',
                    text: "Cette action est irréversible et supprimera le fournisseur.",
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
