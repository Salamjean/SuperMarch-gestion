@extends('admin.layouts.app')

@section('title', 'Modifier produit')
@section('page-title', 'Modifier produit')

@section('content')
    <div class="prd-shell">
        <section class="prd-hero">
            <div>
                <p class="prd-kicker">ADMIN / PRODUITS</p>
                <h2>Modifier la fiche produit</h2>
                <p>Mettez a jour les informations de vente, stock et fournisseur sans perdre la coherence de l'inventaire.
                </p>
            </div>
            <div class="prd-badge-box">
                <span class="prd-badge" id="stockBadge">Stock normal</span>
                <small id="stockHint">Le stock est superieur au seuil d'alerte.</small>
            </div>
        </section>

        <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data"
            class="prd-grid" autocomplete="off">
            @csrf
            @method('PUT')

            <article class="prd-card">
                <h3><i class="fa-solid fa-box"></i> Identite du produit</h3>

                <div class="prd-field">
                    <label for="name">Nom du produit <span>*</span></label>
                    <div class="prd-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
                        <i class="fa-solid fa-tag"></i>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                            maxlength="255" placeholder="Ex: Riz parfum 25kg" required>
                    </div>
                    @error('name')
                        <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="prd-row-2">
                    <div class="prd-field">
                        <label for="category_name">Categorie <span>*</span></label>
                        <div class="prd-input {{ $errors->has('category_name') ? 'is-invalid' : '' }}">
                            <i class="fa-solid fa-layer-group"></i>
                            <select id="category_name" name="category_name" required>
                                <option value="" disabled>Selectionner</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->name }}"
                                        {{ old('category_name', $product->category_name) == $cat->name ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('category_name')
                            <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="prd-field">
                        <label for="supplier_id">Fournisseur</label>
                        <div class="prd-input {{ $errors->has('supplier_id') ? 'is-invalid' : '' }}">
                            <i class="fa-solid fa-truck"></i>
                            <select id="supplier_id" name="supplier_id">
                                <option value="">Aucun fournisseur</option>
                                @foreach ($suppliers as $sup)
                                    <option value="{{ $sup->id }}"
                                        {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>
                                        {{ $sup->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('supplier_id')
                            <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="prd-field">
                    <label for="description">Description</label>
                    <div class="prd-input prd-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}">
                        <i class="fa-solid fa-align-left"></i>
                        <textarea id="description" name="description" rows="4" placeholder="Details ou caracteristiques du produit">{{ old('description', $product->description) }}</textarea>
                    </div>
                    @error('description')
                        <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>
            </article>

            <article class="prd-card">
                <h3><i class="fa-solid fa-sack-dollar"></i> Prix, stock et media</h3>

                <div class="prd-row-3">
                    <div class="prd-field">
                        <label for="price">Prix (FCFA) <span>*</span></label>
                        <div class="prd-input {{ $errors->has('price') ? 'is-invalid' : '' }}">
                            <i class="fa-solid fa-money-bill-wave"></i>
                            <input type="number" id="price" name="price" min="0" step="0.01"
                                value="{{ old('price', $product->price) }}" required>
                        </div>
                        @error('price')
                            <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="prd-field">
                        <label for="stock">Stock (Lecture seule)</label>
                        <div class="prd-input" style="background: #f1f5f9; cursor: not-allowed;">
                            <i class="fa-solid fa-boxes-stacked" style="color: #94a3b8;"></i>
                            <input type="number" id="stock" value="{{ $product->stock }}" readonly style="cursor: not-allowed; color: #64748b;" title="Le stock ne peut être modifié que via l'interface de réapprovisionnement.">
                        </div>
                    </div>

                    <div class="prd-field">
                        <label for="stock_threshold">Seuil alerte <span>*</span></label>
                        <div class="prd-input {{ $errors->has('stock_threshold') ? 'is-invalid' : '' }}">
                            <i class="fa-solid fa-bell"></i>
                            <input type="number" id="stock_threshold" name="stock_threshold" min="0"
                                value="{{ old('stock_threshold', $product->stock_threshold ?? 5) }}" required>
                        </div>
                        @error('stock_threshold')
                            <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="prd-field">
                    <label for="image">Image du produit</label>
                    <div class="prd-upload {{ $errors->has('image') ? 'is-invalid' : '' }}">
                        <input type="file" id="image" name="image"
                            accept="image/jpeg,image/png,image/jpg,image/webp">
                        <label for="image" class="prd-upload-btn"><i class="fa-solid fa-image"></i> Changer
                            l'image</label>
                        <span
                            id="fileName">{{ $product->image ? 'Image actuelle conservee' : 'Aucun fichier selectionne' }}</span>
                    </div>
                    @error('image')
                        <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="prd-preview" id="imagePreviewWrap">
                    <div class="prd-preview-empty" id="previewEmpty"
                        style="{{ $product->image ? 'display:none;' : '' }}">
                        <i class="fa-solid fa-photo-film"></i>
                        <p>Apercu de l'image</p>
                    </div>
                    <img id="previewImage" alt="Apercu produit"
                        src="{{ $product->image ? asset('storage/' . $product->image) : '' }}"
                        style="{{ $product->image ? 'display:block;' : 'display:none;' }}">
                </div>

                <div class="prd-actions">
                    <a href="{{ route('admin.products.index') }}" class="prd-btn prd-btn-muted">
                        <i class="fa-solid fa-arrow-left"></i> Retour
                    </a>
                    <button type="submit" class="prd-btn prd-btn-primary">
                        <i class="fa-solid fa-save"></i> Enregistrer modifications
                    </button>
                </div>
            </article>
        </form>
    </div>
@endsection

@push('styles')
    <style>
        .prd-shell {
            display: grid;
            gap: 18px;
            max-width: 1180px;
            margin: 0 auto;
        }

        .prd-hero {
            background: linear-gradient(125deg, #003d7a 0%, #004d99 45%, #0066cc 100%);
            border-radius: 20px;
            padding: 22px;
            color: #fff;
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 14px;
            box-shadow: 0 16px 30px rgba(0, 77, 153, .24);
        }

        .prd-kicker {
            font-size: 11px;
            letter-spacing: .11em;
            font-weight: 700;
            opacity: .85;
            margin: 0 0 8px;
        }

        .prd-hero h2 {
            margin: 0;
            font-size: 26px;
            line-height: 1.2;
        }

        .prd-hero p {
            margin: 10px 0 0;
            font-size: 14px;
            opacity: .92;
        }

        .prd-badge-box {
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .2);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 8px;
        }

        .prd-badge {
            display: inline-flex;
            align-self: flex-start;
            padding: 7px 12px;
            border-radius: 999px;
            background: #dcfce7;
            color: #166534;
            font-size: 12px;
            font-weight: 800;
        }

        .prd-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .prd-card {
            background: #fff;
            border: 1px solid #e2eaf3;
            border-radius: 16px;
            padding: 20px;
        }

        .prd-card h3 {
            margin: 0 0 14px;
            font-size: 16px;
            color: #1a2840;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .prd-field {
            margin-bottom: 14px;
        }

        .prd-field label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 700;
            color: #425466;
        }

        .prd-field label span {
            color: #e11d48;
        }

        .prd-row-2,
        .prd-row-3 {
            display: grid;
            gap: 12px;
        }

        .prd-row-2 {
            grid-template-columns: 1fr 1fr;
        }

        .prd-row-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .prd-input {
            border: 1.5px solid #dfe8f3;
            border-radius: 11px;
            padding: 0 12px;
            display: flex;
            align-items: center;
            gap: 9px;
            transition: .2s;
            background: #fff;
        }

        .prd-input i {
            color: #7a94aa;
            font-size: 13px;
        }

        .prd-input input,
        .prd-input select,
        .prd-input textarea {
            width: 100%;
            border: none;
            outline: none;
            padding: 12px 0;
            font-size: 14px;
            background: transparent;
            color: #1a2840;
            font-family: inherit;
        }

        .prd-textarea {
            align-items: flex-start;
        }

        .prd-textarea i {
            margin-top: 14px;
        }

        .prd-input:focus-within {
            border-color: #004d99;
            box-shadow: 0 0 0 4px rgba(0, 77, 153, .1);
        }

        .prd-input.is-invalid,
        .prd-upload.is-invalid {
            border-color: #e11d48;
            box-shadow: 0 0 0 3px rgba(225, 29, 72, .08);
        }

        .prd-upload {
            border: 1.5px dashed #cbd9ea;
            border-radius: 12px;
            padding: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #f8fbff;
        }

        .prd-upload input[type="file"] {
            display: none;
        }

        .prd-upload-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 9px;
            background: #eef4ff;
            color: #004d99;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
        }

        #fileName {
            color: #5f7488;
            font-size: 13px;
        }

        .prd-preview {
            margin-top: 8px;
            border: 1px solid #e2eaf3;
            border-radius: 12px;
            min-height: 180px;
            background: #f8fbff;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .prd-preview-empty {
            text-align: center;
            color: #8ca1b4;
            font-size: 13px;
        }

        .prd-preview-empty i {
            font-size: 32px;
            display: block;
            margin-bottom: 6px;
        }

        #previewImage {
            width: 100%;
            height: 240px;
            object-fit: cover;
            display: none;
        }

        .prd-actions {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .prd-btn {
            border: none;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .prd-btn-primary {
            background: #ffc300;
            color: #003d7a;
            border: 1px solid #e6b000;
        }

        .prd-btn-primary:hover {
            background: #e6b000;
        }

        .prd-btn-muted {
            background: #fff;
            color: #004d99;
            border: 1px solid #d6e1ef;
        }

        .prd-btn-muted:hover {
            background: #f5f9ff;
        }

        .prd-error {
            margin: 6px 0 0;
            font-size: 12px;
            color: #e11d48;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        @media (max-width: 1080px) {

            .prd-hero,
            .prd-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 780px) {

            .prd-row-2,
            .prd-row-3,
            .prd-actions {
                grid-template-columns: 1fr;
                flex-direction: column;
            }

            .prd-btn {
                justify-content: center;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function() {
            const imageInput = document.getElementById('image');
            const fileName = document.getElementById('fileName');
            const previewImage = document.getElementById('previewImage');
            const previewEmpty = document.getElementById('previewEmpty');
            const stockInput = document.getElementById('stock');
            const thresholdInput = document.getElementById('stock_threshold');
            const stockBadge = document.getElementById('stockBadge');
            const stockHint = document.getElementById('stockHint');

            const syncStockState = () => {
                const stock = Number(stockInput.value || 0);
                const threshold = Number(thresholdInput.value || 0);

                if (stock <= threshold) {
                    stockBadge.textContent = 'Seuil atteint';
                    stockBadge.style.background = '#fee2e2';
                    stockBadge.style.color = '#991b1b';
                    stockHint.textContent = "Le stock est inferieur ou egal au seuil d'alerte.";
                } else {
                    stockBadge.textContent = 'Stock normal';
                    stockBadge.style.background = '#dcfce7';
                    stockBadge.style.color = '#166534';
                    stockHint.textContent = "Le stock est superieur au seuil d'alerte.";
                }
            };

            const syncPreviewImage = () => {
                const file = imageInput.files && imageInput.files[0];
                fileName.textContent = file ? file.name : 'Image actuelle conservee';
                if (!file) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    previewEmpty.style.display = 'none';
                };
                reader.readAsDataURL(file);
            };

            imageInput.addEventListener('change', syncPreviewImage);
            stockInput.addEventListener('input', syncStockState);
            thresholdInput.addEventListener('input', syncStockState);

            syncStockState();
        })();
    </script>
@endpush
