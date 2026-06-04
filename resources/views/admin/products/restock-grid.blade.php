@extends('admin.layouts.app')

@section('title', 'Réapprovisionnement Rapide')
@section('page-title', 'Réapprovisionnement par Cartes')

@section('content')

    <!-- En-tête Moderne -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h2 class="list-title"
                style="font-size: 22px; font-weight: 800; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-boxes-stacked" style="color: #6366f1;"></i> Réapprovisionnement Rapide
            </h2>
            <p class="list-sub" style="font-size: 13px; color: var(--text-muted); margin: 3px 0 0 0;">
                Cliquez sur un produit pour réapprovisionner instantanément son stock
            </p>
        </div>
    </div>

    <!-- Barre de Recherche & Filtres Catégories -->
    <div class="card"
        style="margin-bottom: 25px; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div class="card-body" style="padding: 20px;">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; align-items: center;">
                <div style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass"
                        style="position: absolute; left: 15px; top: 13px; color: var(--text-muted);"></i>
                    <input type="text" id="admin-search-input" oninput="filterRestockGrid()"
                        style="font-size: 14px; height: 42px; padding-left: 42px; border-radius: 8px; border: 1px solid var(--border); width: 100%; outline: none; transition: border-color 0.2s;"
                        placeholder="Rechercher un produit par nom, référence...">
                </div>
                <div>
                    <select id="admin-category-select" onchange="filterRestockGrid()"
                        style="font-size: 14px; height: 42px; border-radius: 8px; border: 1px solid var(--border); width: 100%; padding: 0 10px; outline: none; cursor: pointer; background: white;">
                        <option value="all">Toutes les catégories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Grille des Produits -->
    <div class="products-grid" id="admin-products-container">
        @foreach ($products as $product)
            <div class="product-card" id="admin-prod-card-{{ $product->id }}" 
                 data-id="{{ $product->id }}"
                 data-name="{{ strtolower($product->name) }}"
                 data-ref="{{ strtolower($product->reference) }}" 
                 data-category="{{ $product->category_name }}"
                 data-stock="{{ $product->stock }}"
                 data-threshold="{{ $product->stock_threshold }}"
                 onclick="triggerRestock({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock }})">
                
                <div class="product-img">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #e2e8f0; color: #94a3b8;">
                            <i class="fa-solid fa-image" style="font-size: 32px;"></i>
                        </div>
                    @endif
                </div>

                <div class="product-info-wrap">
                    <h3 class="product-title">{{ $product->name }}</h3>
                    <p class="product-category-label">{{ $product->category_name }}</p>
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 8px; border-top: 1px solid #f1f5f9;">
                        <span class="product-price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                        
                        <div id="stock-badge-{{ $product->id }}">
                            @if ($product->stock <= $product->stock_threshold)
                                <span class="stock-badge badge-low">
                                    Stock: {{ $product->stock }} (Bas)
                                </span>
                            @else
                                <span class="stock-badge badge-ok">
                                    Stock: {{ $product->stock }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @push('styles')
        <style>
            .products-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 20px;
                padding-bottom: 30px;
            }

            .product-card {
                background: #ffffff;
                border-radius: 14px;
                border: 1px solid var(--border);
                overflow: hidden;
                cursor: pointer;
                transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                flex-direction: column;
                box-shadow: 0 1px 3px rgba(0,0,0,0.01);
            }

            .product-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 20px rgba(0, 0, 0, 0.05);
                border-color: #6366f1;
            }

            .product-img {
                width: 100%;
                height: 140px;
                background: #f8fafc;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                border-bottom: 1px solid #f1f5f9;
                position: relative;
            }

            .product-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .product-card:hover .product-img img {
                transform: scale(1.05);
            }

            .product-info-wrap {
                padding: 14px;
                display: flex;
                flex-direction: column;
                flex: 1;
                gap: 4px;
            }

            .product-title {
                margin: 0;
                font-size: 14px;
                font-weight: 700;
                color: #1e293b;
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                line-height: 1.3;
            }

            .product-category-label {
                margin: 0 0 8px 0;
                font-size: 11px;
                color: var(--text-muted);
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.02em;
            }

            .product-price {
                font-size: 14px;
                font-weight: 800;
                color: #475569;
            }

            .stock-badge {
                font-size: 11px;
                font-weight: 700;
                padding: 3px 8px;
                border-radius: 6px;
                display: inline-block;
            }

            .badge-low {
                background: #fff1f2;
                color: #e11d48;
                border: 1px solid #fecdd3;
            }

            .badge-ok {
                background: #e8f9f0;
                color: #059669;
                border: 1px solid #bbf7d0;
            }

            .swal-custom-qty-input {
                max-width: 250px !important;
                margin: 15px auto !important;
                box-sizing: border-box !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function filterRestockGrid() {
                const query = document.getElementById('admin-search-input').value.toLowerCase();
                const selectedCat = document.getElementById('admin-category-select').value;
                const cards = document.querySelectorAll('.product-card');

                cards.forEach(card => {
                    const name = card.dataset.name;
                    const ref = card.dataset.ref;
                    const category = card.dataset.category;

                    const matchName = name.includes(query) || ref.includes(query);
                    const matchCat = (selectedCat === 'all' || category === selectedCat);

                    card.style.display = (matchName && matchCat) ? 'flex' : 'none';
                });
            }

            function triggerRestock(productId, productName, currentStock) {
                Swal.fire({
                    title: 'Réapprovisionner le stock',
                    html: `Produit: <b>${productName}</b><br>Stock actuel: <b>${currentStock} unités</b><br><br>Saisir la quantité à ajouter au stock :`,
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
                    confirmButtonColor: '#6366f1',
                    cancelButtonColor: '#64748b',
                    inputValidator: (value) => {
                        if (!value || value <= 0) {
                            return 'Veuillez entrer une quantité positive valide !'
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const qty = parseInt(result.value);

                        fetch(`/admin/products/${productId}/restock`, {
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
                                    confirmButtonColor: '#6366f1',
                                    timer: 2000,
                                    timerProgressBar: true
                                });

                                // Mettre à jour les données de la carte locale
                                const card = document.getElementById('admin-prod-card-' + productId);
                                if (card) {
                                    card.dataset.stock = data.new_stock;
                                }

                                const badgeContainer = document.getElementById('stock-badge-' + productId);
                                if (badgeContainer) {
                                    if (data.new_stock <= data.threshold) {
                                        badgeContainer.innerHTML = `
                                            <span class="stock-badge badge-low">
                                                Stock: ${data.new_stock} (Bas)
                                            </span>
                                        `;
                                    } else {
                                        badgeContainer.innerHTML = `
                                            <span class="stock-badge badge-ok">
                                                Stock: ${data.new_stock}
                                            </span>
                                        `;
                                    }
                                }
                            } else {
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
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Une erreur est survenue lors de la communication avec le serveur.',
                                confirmButtonColor: '#6366f1'
                            });
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
