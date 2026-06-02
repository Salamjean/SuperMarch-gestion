@extends('admin.layouts.app')

@section('title', 'Réapprovisionnements')
@section('page-title', 'Réapprovisionnements')

@section('content')

    <!-- En-tête Moderne -->
    <div
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 class="list-title"
                style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-truck-loading" style="color: #6366f1;"></i> Demandes de réapprovisionnement
            </h2>
            <p class="list-sub" style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0 0;">Visualisez et traitez
                les demandes de réapprovisionnement envoyées par vos caissiers</p>
        </div>
    </div>

    <!-- Table des Demandes -->
    <div class="card"
        style="border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body" style="padding:0;">
            @if ($requests->isEmpty())
                <div style="text-align: center; padding: 60px 20px; color: var(--text-muted);">
                    <div
                        style="width: 50px; height: 50px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; color: #94a3b8; font-size: 18px;">
                        <i class="fa-solid fa-bell-slash"></i>
                    </div>
                    <p style="font-weight: 700; margin: 0; color: #475569; font-size:14px;">Aucune demande enregistrée</p>
                    <p style="font-size: 12.5px; margin-top: 5px;">Les demandes de vos caissiers s'afficheront ici en temps
                        réel.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="data-table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 1px solid var(--border);">
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Aperçu</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase;">
                                    Produit</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Catégorie</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Stock Actuel</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Demandé Par</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Date de demande</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center;">
                                    Statut</th>
                                <th
                                    style="padding: 12px 15px; font-size: 11.5px; font-weight: 700; color: #475569; text-transform: uppercase; text-align: center; width: 140px;">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $req)
                                <tr id="restock-row-{{ $req->id }}"
                                    style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                    onmouseover="this.style.backgroundColor='#f8fafc'"
                                    onmouseout="this.style.backgroundColor='transparent'">
                                    <td style="padding: 12px 15px;">
                                        <div
                                            style="width: 40px; height: 40px; border-radius: 8px; overflow: hidden; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                            @if ($req->product && $req->product->image)
                                                <img src="{{ asset('storage/' . $req->product->image) }}"
                                                    style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <i class="fa-solid fa-image" style="color: #cbd5e1; font-size: 14px;"></i>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding: 12px 15px; font-size: 13.5px; font-weight: 700; color: #1e293b;">
                                        {{ $req->product ? $req->product->name : 'Produit supprimé' }}
                                    </td>
                                    <td style="padding: 12px 15px; font-size: 13px; color: #64748b; text-align: center;">
                                        {{ $req->product ? $req->product->category_name : '—' }}
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;">
                                        @if ($req->product)
                                            @if ($req->product->stock <= $req->product->stock_threshold)
                                                <span
                                                    style="background: #fff1f2; color: #e11d48; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid #fecdd3;">
                                                    {{ $req->product->stock }} (Seuil: {{ $req->product->stock_threshold }})
                                                </span>
                                            @else
                                                <span
                                                    style="background: #eefdf4; color: #166534; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-weight: 700; border: 1px solid #bbf7d0;">
                                                    {{ $req->product->stock }}
                                                </span>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td
                                        style="padding: 12px 15px; font-size: 13px; color: #1e293b; text-align: center; font-weight: 600;">
                                        {{ $req->user ? $req->user->name : 'Caissier inconnu' }}
                                    </td>
                                    <td style="padding: 12px 15px; font-size: 12.5px; color: #64748b; text-align: center;">
                                        {{ $req->created_at->format('d/m/Y H:i') }} <span
                                            style="font-size: 11px; color: #94a3b8;">({{ $req->created_at->diffForHumans() }})</span>
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;" id="status-badge-{{ $req->id }}">
                                        @if ($req->status === 'completed')
                                            <span
                                                style="background: #eefdf4; color: #15803d; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-circle-check"></i> Traitée
                                            </span>
                                        @else
                                            <span
                                                style="background: #eff6ff; color: #1d4ed8; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px; border: 1px solid #bfdbfe; display: inline-flex; align-items: center; gap: 4px;">
                                                <i class="fa-solid fa-clock"></i> En attente
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 15px; text-align: center;"
                                        id="action-btn-cell-{{ $req->id }}">
                                        @if ($req->status === 'pending')
                                            <button onclick="resolveRequestPage({{ $req->id }}, this)" class="btn"
                                                style="background: #059669; border: 1px solid #059669; color: white; padding: 6px 12px; border-radius: 6px; font-size: 11.5px; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; transition: all 0.2s;"
                                                onmouseover="this.style.background='#047857';"
                                                onmouseout="this.style.background='#059669';">
                                                <i class="fa-solid fa-check"></i> Traiter
                                            </button>
                                        @else
                                            <span style="font-size: 12px; color: #94a3b8; font-weight: 500;">
                                                <i class="fa-solid fa-ban"></i> Aucune action
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding: 15px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end;">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .swal-custom-qty-input {
                max-width: 250px !important;
                margin: 15px auto !important;
                box-sizing: border-box !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function resolveRequestPage(requestId, button) {
                Swal.fire({
                    title: 'Valider le réapprovisionnement',
                    text: 'Saisir la quantité à ajouter au stock pour ce produit :',
                    input: 'number',
                    customClass: {
                        input: 'swal-custom-qty-input'
                    },
                    inputAttributes: {
                        min: 1,
                        step: 1
                    },
                    inputValue: 50,
                    showCancelButton: true,
                    confirmButtonText: 'Ajouter au Stock',
                    cancelButtonText: 'Annuler',
                    confirmButtonColor: '#059669',
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

                        fetch(`/admin/stock/request/${requestId}/resolve`, {
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
                                        confirmButtonColor: '#059669',
                                        timer: 2000,
                                        timerProgressBar: true
                                    });

                                    // Mettre à jour la ligne à la volée
                                    const statusBadge = document.getElementById('status-badge-' + requestId);
                                    if (statusBadge) {
                                        statusBadge.innerHTML = `
                                        <span style="background: #eefdf4; color: #15803d; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 99px; border: 1px solid #bbf7d0; display: inline-flex; align-items: center; gap: 4px;">
                                            <i class="fa-solid fa-circle-check"></i> Traitée
                                        </span>
                                    `;
                                    }

                                    const actionBtnCell = document.getElementById('action-btn-cell-' + requestId);
                                    if (actionBtnCell) {
                                        actionBtnCell.innerHTML = `
                                        <span style="font-size: 12px; color: #94a3b8; font-weight: 500;">
                                            <i class="fa-solid fa-ban"></i> Aucune action
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
                                        confirmButtonColor: '#6366f1'
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
                                    confirmButtonColor: '#6366f1'
                                });
                            });
                    }
                });
            }
        </script>
    @endpush

@endsection
