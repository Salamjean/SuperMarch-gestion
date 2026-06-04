@extends('magasinier.layouts.app')

@section('title', 'Réapprovisionnements')

@section('content')

    <div class="card" style="padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:14px;">
            <div>
                <h2 style="font-size:18px; margin:0; font-weight:800; color:var(--primary); display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-truck-loading"></i> Demandes de réapprovisionnement
                </h2>
                <p style="font-size:12px; color:var(--muted); margin:3px 0 0 0;">
                    Visualisez et traitez les demandes de réapprovisionnement envoyées par vos caissiers
                </p>
            </div>
        </div>

        <div class="table-responsive" style="overflow-x:auto;">
            @if ($requests->isEmpty())
                <div style="text-align: center; padding: 60px 20px; color: var(--muted);">
                    <div style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #94a3b8; font-size: 18px;">
                        <i class="fa-solid fa-bell-slash"></i>
                    </div>
                    <p style="font-weight: 700; margin: 0; color: #475569; font-size:14px;">Aucune demande enregistrée</p>
                    <p style="font-size: 12.5px; margin-top: 5px;">Les demandes de vos caissiers s'afficheront ici en temps réel.</p>
                </div>
            @else
                <table style="width:100%; border-collapse:collapse; min-width:950px;">
                    <thead>
                        <tr>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Aperçu</th>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; text-align:left;">Produit</th>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Catégorie</th>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Stock Initial</th>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Stock Ajouté</th>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Stock Actuel</th>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Demandé Par</th>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Date</th>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Statut</th>
                            <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; width:140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $req)
                            <tr id="restock-row-{{ $req->id }}">
                                <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                                    <div style="display:flex; justify-content:center;">
                                        <div style="width:40px; height:40px; border-radius:8px; overflow:hidden; background:#f1f5f9; display:flex; align-items:center; justify-content:center; border:1px solid var(--border);">
                                            @if ($req->product && $req->product->image)
                                                <img src="{{ asset('storage/' . $req->product->image) }}" style="width:100%; height:100%; object-fit:cover;">
                                            @elseif($req->product)
                                                <div style="width:100%; height:100%; background: linear-gradient(135deg, #004d99, #1a6bbf); display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:16px;">
                                                    {{ strtoupper(mb_substr($req->product->name, 0, 1)) }}
                                                </div>
                                            @else
                                                <i class="fa-solid fa-box" style="color:#cbd5e1; font-size:14px;"></i>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td style="text-align:left; padding:10px; border-bottom:1px solid var(--border); font-weight:600; color:var(--text); font-size:13.5px;">
                                    {{ $req->product ? $req->product->name : 'Produit supprimé' }}
                                </td>
                                <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:13px;">
                                    {{ $req->product ? $req->product->category_name : '—' }}
                                </td>
                                <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:13px;" id="initial-stock-{{ $req->id }}">
                                    @if($req->status === 'completed')
                                        <span style="font-weight:600; color:#475569;">{{ $req->initial_stock }}</span>
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </td>
                                <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); font-size:13px;" id="added-stock-{{ $req->id }}">
                                    @if($req->status === 'completed')
                                        <span style="font-weight:700; color:#059669; background:#eefdf4; padding:2px 6px; border-radius:4px; border:1px solid #bbf7d0;">
                                            +{{ $req->added_stock }}
                                        </span>
                                    @else
                                        <span style="color:#94a3b8;">—</span>
                                    @endif
                                </td>
                                <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);" id="current-stock-{{ $req->id }}">
                                    @if ($req->product)
                                        @if ($req->product->stock <= ($req->product->stock_threshold ?? 5))
                                            <span style="background:#fff1f2; color:#e11d48; padding:3px 8px; border-radius:6px; font-size:12px; font-weight:700; border:1px solid #fecdd3; display:inline-flex; align-items:center; gap:4px;">
                                                <i class="fa-solid fa-triangle-exclamation"></i> {{ $req->product->stock }} (Seuil: {{ $req->product->stock_threshold }})
                                            </span>
                                        @else
                                            <span style="background:#eefdf4; color:#166534; padding:3px 8px; border-radius:6px; font-size:12px; font-weight:700; border:1px solid #bbf7d0;">
                                                {{ $req->product->stock }}
                                            </span>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); font-size:13px; font-weight:600; color:var(--text);">
                                    {{ $req->user ? $req->user->name : 'Caissier inconnu' }}
                                </td>
                                <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); font-size:12.5px; color:var(--muted);">
                                    {{ $req->created_at->format('d/m/Y H:i') }} <span style="font-size:11px; color:#94a3b8; display:block;">({{ $req->created_at->diffForHumans() }})</span>
                                </td>
                                <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);" id="status-badge-{{ $req->id }}">
                                    @if ($req->status === 'completed')
                                        <span style="background:#eefdf4; color:#15803d; font-size:11px; font-weight:700; padding:2px 8px; border-radius:99px; border:1px solid #bbf7d0; display:inline-flex; align-items:center; gap:4px;">
                                            <i class="fa-solid fa-circle-check"></i> Traitée
                                        </span>
                                    @else
                                        <span style="background:#eff6ff; color:#1d4ed8; font-size:11px; font-weight:700; padding:2px 8px; border-radius:99px; border:1px solid #bfdbfe; display:inline-flex; align-items:center; gap:4px;">
                                            <i class="fa-solid fa-clock"></i> En attente
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);" id="action-btn-cell-{{ $req->id }}">
                                    @if ($req->status === 'pending')
                                        <button onclick="resolveRequestPage({{ $req->id }}, this)" class="mg-logout-btn" 
                                            style="padding:6px 12px; font-size:11.5px; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; gap:5px; background:var(--secondary); color:var(--primary); border:1px solid #e6b000;">
                                            <i class="fa-solid fa-check"></i> Traiter
                                        </button>
                                    @else
                                        <span style="font-size:12px; color:#94a3b8; font-weight:500;">
                                            <i class="fa-solid fa-ban"></i> Aucune
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div style="padding:15px 0 0 0; display:flex; justify-content:flex-end;">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            table tbody tr {
                transition: background 0.15s;
            }
            table tbody tr:hover {
                background: #f8fafc;
            }
            .swal-custom-qty-input {
                max-width: 250px !important;
                margin: 15px auto !important;
                box-sizing: border-box !important;
                padding: 10px !important;
                border-radius: 8px !important;
                border: 1px solid var(--border) !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function resolveRequestPage(requestId, button) {
                Swal.fire({
                    title: 'Valider le réapprovisionnement',
                    text: 'Saisir la quantité physique à ajouter au stock pour ce produit :',
                    input: 'number',
                    customClass: {
                        input: 'swal-custom-qty-input'
                    },
                    inputAttributes: {
                        min: 1,
                        step: 1
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Ajouter au Stock',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: 'var(--primary)',
                    cancelButtonColor: '#64748b',
                    inputValidator: (value) => {
                        if (!value || value <= 0) {
                            return 'Veuillez entrer une quantité positive valide !'
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const qty = result.value;
                        const originalContent = button.innerHTML;
                        button.disabled = true;
                        button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                        fetch(`/magasinier/stock/request/${requestId}/resolve`, {
                                method: "POST",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                },
                                body: JSON.stringify({
                                    quantity: qty
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Stock mis à jour !',
                                        text: data.message,
                                        confirmButtonColor: 'var(--primary)',
                                        timer: 2000,
                                        timerProgressBar: true
                                    });

                                    // Mettre à jour la ligne
                                    const statusBadge = document.getElementById('status-badge-' + requestId);
                                    if (statusBadge) {
                                        statusBadge.innerHTML = `
                                        <span style="background: #eefdf4; color: #15803d; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-circle-check"></i> Traitée
                                        </span>
                                    `;
                                    }

                                    const initialCell = document.getElementById('initial-stock-' + requestId);
                                    if (initialCell && data.initial_stock !== undefined) {
                                        initialCell.innerHTML = `<span style="font-weight: 600; color: #475569;">${data.initial_stock}</span>`;
                                    }

                                    const addedCell = document.getElementById('added-stock-' + requestId);
                                    if (addedCell && data.added_stock !== undefined) {
                                        addedCell.innerHTML = `<span style="font-weight: 700; color: #059669; background: #eefdf4; padding: 2px 6px; border-radius: 4px;">+${data.added_stock}</span>`;
                                    }

                                    const currentStockCell = document.getElementById('current-stock-' + requestId);
                                    if (currentStockCell && data.new_stock !== undefined) {
                                        if (data.threshold !== null && data.new_stock <= data.threshold) {
                                            currentStockCell.innerHTML = `
                                                <span style="background: #fff1f2; color: #e11d48; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid #fecdd3;">
                                                    ${data.new_stock} (Seuil: ${data.threshold})
                                                </span>
                                            `;
                                        } else {
                                            currentStockCell.innerHTML = `
                                                <span style="background: #eefdf4; color: #166534; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid #bbf7d0;">
                                                    ${data.new_stock}
                                                </span>
                                            `;
                                        }
                                    }

                                    const actionBtnCell = document.getElementById('action-btn-cell-' + requestId);
                                    if (actionBtnCell) {
                                        actionBtnCell.innerHTML = `
                                        <span style="font-size: 12px; color: #94a3b8; font-weight: 500;">
                                            <i class="fa-solid fa-ban"></i> Aucune
                                        </span>
                                    `;
                                    }
                                } else {
                                    button.disabled = false;
                                    button.innerHTML = originalContent;
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Erreur',
                                        text: data.message,
                                        confirmButtonColor: 'var(--primary)'
                                    });
                                }
                            })
                            .catch(error => {
                                console.error("Erreur:", error);
                                button.disabled = false;
                                button.innerHTML = originalContent;
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: 'Une erreur est survenue lors de la résolution.',
                                    confirmButtonColor: 'var(--primary)'
                                });
                            });
                    }
                });
            }
        </script>
    @endpush
@endsection
