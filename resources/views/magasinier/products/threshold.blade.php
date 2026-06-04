@extends('magasinier.layouts.app')

@section('title', 'Stocks Faibles')

@section('content')

    <div class="card" style="padding:18px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap:wrap; gap:14px;">
            <div>
                <h2 style="font-size:18px; margin:0; font-weight:800; color:#e11d48; display:flex; align-items:center; gap:8px;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Produits sous le seuil
                </h2>
                <p style="font-size:12px; color:var(--muted); margin:3px 0 0 0;">
                    {{ $products->count() }} produit(s) nécessitant un réapprovisionnement
                </p>
            </div>
            
            @if (!$products->isEmpty())
                <!-- Recherche produit -->
                <div style="width:100%; max-width:320px; position:relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position:absolute; left:12px; top:12px; color:var(--muted); font-size:14px;"></i>
                    <input type="text" id="search-input" placeholder="Rechercher un produit..." 
                        style="width:100%; height:38px; padding:6px 12px 6px 36px; border-radius:8px; border:1px solid var(--border); outline:none; font-size:13.5px; transition:border-color 0.2s; background:#fff;">
                </div>
            @endif
        </div>

        <div class="table-responsive" style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:800px;">
                <thead>
                    <tr>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">#</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Image</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Référence</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Produit</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Catégorie</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Fournisseur</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Seuil Alerte</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em;">Stock Actuel</th>
                        <th style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; width:150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $i => $product)
                        <tr id="product-row-{{ $product->id }}">
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted); font-size:12px; font-weight:600;">
                                {{ $i + 1 }}
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                                <div style="display:flex; justify-content:center;">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                            style="width:40px; height:40px; border-radius:8px; object-fit:cover; border:1px solid var(--border);">
                                    @else
                                        <div style="width:40px; height:40px; border-radius:8px; background:#f0f4f8; display:flex; align-items:center; justify-content:center; color:#cbd5e1; border:1px solid var(--border);">
                                            <i class="fa-solid fa-image" style="font-size:16px;"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                                <span class="badge" style="font-family:monospace; background:#f1f5f9; color:#475569; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600;">
                                    {{ $product->reference }}
                                </span>
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); font-weight:600; color:var(--text);">
                                {{ $product->name }}
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                                <span class="badge" style="background:#eef4ff; color:#004d99; border:1px solid rgba(0,77,153,0.1); padding:2px 8px; border-radius:6px; font-size:11px; font-weight:600;">
                                    {{ $product->category_name }}
                                </span>
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); color:var(--muted);">
                                {{ $product->supplier->name ?? 'Aucun' }}
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border); font-weight:600; color:var(--muted);">
                                {{ $product->stock_threshold }}
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);" id="product-stock-{{ $product->id }}">
                                <span style="color:#e11d48; font-weight:700; background:#fff1f2; padding:3px 8px; border-radius:6px; border:1px solid #fecdd3; font-size:12px; display:inline-flex; align-items:center; gap:4px;" title="Sous le seuil d'alerte">
                                    <i class="fa-solid fa-triangle-exclamation"></i> {{ $product->stock }}
                                </span>
                            </td>
                            <td style="text-align:center; padding:10px; border-bottom:1px solid var(--border);">
                                <div style="display:flex; gap:6px; justify-content:center;">
                                    <button onclick="restockProduct({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }})" 
                                        class="mg-logout-btn" 
                                        style="padding:6px 10px; font-size:12px; margin:0; background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; cursor:pointer;" 
                                        title="Réapprovisionner">
                                        <i class="fa-solid fa-plus-square"></i> Réappro.
                                    </button>
                                    <a href="{{ route('magasinier.products.edit', $product) }}" class="mg-logout-btn" style="padding:6px 10px; font-size:12px; margin:0; text-decoration:none; background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe;" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr id="no-results-row">
                            <td colspan="9" style="text-align:center; padding:30px; color:var(--muted);">
                                Aucun produit n'est actuellement sous le seuil d'alerte de stock.
                            </td>
                        </tr>
                    @endforelse
                    <tr id="no-results-search-row" style="display: none;">
                        <td colspan="9" style="text-align: center; color: var(--muted); padding: 40px;">
                            <i class="fa-solid fa-magnifying-glass-slash" style="font-size: 32px; display: block; margin-bottom: 12px; color: #cbd5e1;"></i>
                            Aucun produit ne correspond à votre recherche.
                        </td>
                    </tr>
                </tbody>
            </table>
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
                padding-left: 14px !important;
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
                const rows = document.querySelectorAll('table tbody tr');
                let visibleCount = 0;

                rows.forEach(row => {
                    if (row.id === 'no-results-search-row' || row.id === 'no-results-row') return;
                    
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(query)) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                const noResults = document.getElementById('no-results-search-row');
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
                        
                        fetch(`/magasinier/products/${productId}/restock`, {
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
                                            const rows = document.querySelectorAll('table tbody tr');
                                            const activeRows = Array.from(rows).filter(r => r.id !== 'no-results-row' && r.id !== 'no-results-search-row');
                                            if (activeRows.length === 0) {
                                                location.reload();
                                            }
                                        }, 300);
                                    } else {
                                        const stockCell = document.getElementById(`product-stock-${productId}`);
                                        if (stockCell) {
                                            stockCell.innerHTML = `<span style="color:#e11d48; font-weight:700; background:#fff1f2; padding:3px 8px; border-radius:6px; border:1px solid #fecdd3; font-size:12px; display:inline-flex; align-items:center; gap:4px;"><i class="fa-solid fa-triangle-exclamation"></i> ${data.new_stock}</span>`;
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
