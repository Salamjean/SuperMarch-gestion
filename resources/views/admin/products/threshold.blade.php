@extends('admin.layouts.app')

@section('title', 'Stocks Faibles')
@section('page-title', 'Produits sous le seuil de stock')

@section('content')

    <div class="list-header" style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; gap: 20px; flex-wrap: wrap;">
        <div>
            <h2 class="list-title" style="color: #e11d48;"><i class="fa-solid fa-triangle-exclamation"></i> Produits sous le seuil</h2>
            <p class="list-sub">{{ $products->count() }} produit(s) nécessitant un réapprovisionnement</p>
        </div>
        
        @if (!$products->isEmpty())
            <!-- Recherche produit -->
            <div style="flex: 1; max-width: 400px; min-width: 250px;">
                <div class="input-wrap">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="search-input" placeholder="Rechercher par nom, catégorie ou référence..." style="padding-left: 38px; background: #fff;">
                </div>
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-body" style="padding:0;">
            @if ($products->isEmpty())
                <div class="empty-state">
                    <i class="fa-solid fa-circle-check" style="color: #059669; font-size: 38px;"></i>
                    <p style="margin-top: 8px;">Aucun produit n'est actuellement sous le seuil d'alerte de stock.</p>
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
                                <th style="text-align:center;">Seuil Alerte</th>
                                <th style="text-align:center;">Stock Actuel</th>
                                <th style="width:110px; text-align:center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $i => $product)
                                <tr id="product-row-{{ $product->id }}">
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
                                        <span class="badge badge-gray"
                                            style="font-family: monospace; background:#f1f5f9; color:#475569;">{{ $product->reference }}</span>
                                    </td>
                                    <td style="text-align:center;">
                                        <div class="td-name">{{ $product->name }}</div>
                                    </td>
                                    <td style="text-align:center;">
                                        <span class="badge badge-gray"
                                            style="background:#eef4ff; color:#004d99; border:1px solid rgba(0,77,153,0.1);">{{ $product->category_name }}</span>
                                    </td>
                                    <td style="text-align:center;" class="td-muted">
                                        {{ $product->supplier->name ?? 'Aucun' }}
                                    </td>
                                    <td style="text-align:center; font-weight: 600; color: #64748b;">
                                        {{ $product->stock_threshold }}
                                    </td>
                                    <td style="text-align:center;" id="product-stock-{{ $product->id }}">
                                        <span style="color:#e11d48; font-weight:700;" title="Sous le seuil d'alerte">
                                            <i class="fa-solid fa-triangle-exclamation"></i> {{ $product->stock }}
                                        </span>
                                    </td>
                                    <td class="td-actions" style="justify-content:center;">
                                        <button onclick="restockProduct({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }})" 
                                            class="btn-icon" 
                                            style="color: #059669; border-color: rgba(5,150,105,0.2); background: #f0fdf4;" 
                                            title="Réapprovisionner">
                                            <i class="fa-solid fa-plus-square"></i>
                                        </button>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-icon" title="Modifier">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr id="no-results-row" style="display: none;">
                                <td colspan="9" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                    <i class="fa-solid fa-magnifying-glass-slash" style="font-size: 32px; display: block; margin-bottom: 12px; color: #cbd5e1;"></i>
                                    Aucun produit ne correspond à votre recherche.
                                </td>
                            </tr>
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

            .badge {
                display: inline-flex;
                align-items: center;
                padding: 3px 10px;
                border-radius: 10px;
                font-size: 11px;
                font-weight: 700;
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
                transition: all 0.15s;
            }

            .btn-icon:hover {
                background: #eef4ff;
                transform: scale(1.05);
            }

            .empty-state {
                text-align: center;
                padding: 48px 20px;
                color: #7a94aa;
                font-size: 14px;
            }

            .empty-state i {
                font-size: 36px;
                margin-bottom: 12px;
                display: block;
            }

            /* SweetAlert2 Input Fix */
            .swal-custom-qty-input {
                max-width: 250px !important;
                padding-left: 14px !important; /* Overrides the global padding-left: 38px of app.blade.php */
                margin: 15px auto !important;
                box-sizing: border-box !important;
                height: 42px !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Recherche en temps réel
            document.getElementById('search-input')?.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase().trim();
                const rows = document.querySelectorAll('table.data-table tbody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    if (row.id === 'no-results-row') return;
                    
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const noResults = document.getElementById('no-results-row');
                if (noResults) {
                    noResults.style.display = visibleCount === 0 ? '' : 'none';
                }
            });

            function restockProduct(productId, productName, currentStock) {
                Swal.fire({
                    title: 'Réapprovisionner',
                    text: `Saisir la quantité à ajouter pour le produit "${productName}" (Stock actuel : ${currentStock}) :`,
                    input: 'number',
                    inputAttributes: {
                        min: 1,
                        step: 1,
                        required: 'required'
                    },
                    customClass: {
                        input: 'swal-custom-qty-input'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Valider',
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
                        
                        fetch(`/admin/products/${productId}/restock`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ quantity: qty })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Stock mis à jour !',
                                    text: data.message,
                                    confirmButtonColor: '#004d99',
                                    timer: 2000,
                                    timerProgressBar: true
                                });

                                // Dynamically update stock in the row, or remove the row if new stock is above threshold
                                const row = document.getElementById(`product-row-${productId}`);
                                if (row) {
                                    if (data.new_stock > data.threshold) {
                                        row.style.opacity = '0';
                                        row.style.transform = 'scale(0.95)';
                                        row.style.transition = 'all 0.3s ease';
                                        setTimeout(() => {
                                            row.remove();
                                            
                                            // If table is now empty, refresh the page to show empty state
                                            const rows = document.querySelectorAll('table.data-table tbody tr');
                                            // Filter out the no-results-row
                                            const activeRows = Array.from(rows).filter(r => r.id !== 'no-results-row');
                                            if (activeRows.length === 0) {
                                                location.reload();
                                            }
                                        }, 300);
                                    } else {
                                        const stockCell = document.getElementById(`product-stock-${productId}`);
                                        if (stockCell) {
                                            stockCell.innerHTML = `<span style="color:#e11d48; font-weight:700;"><i class="fa-solid fa-triangle-exclamation"></i> ${data.new_stock}</span>`;
                                        }
                                    }
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    text: data.message || 'Une erreur est survenue.',
                                    confirmButtonColor: '#004d99'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Impossible de joindre le serveur.',
                                confirmButtonColor: '#004d99'
                            });
                        });
                    }
                });
            }
        </script>
    @endpush

@endsection
