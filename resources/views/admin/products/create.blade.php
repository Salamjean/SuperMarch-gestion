@extends('admin.layouts.app')

@section('title', 'Nouveau produit')
@section('page-title', 'Nouveau produit')

@section('content')
<div style="position: relative; min-height: calc(100vh - 120px); width: 100%;">
    <!-- Scanner enrollment overlay -->
    <div id="enrollment-overlay"
        style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.95); z-index:998; display:flex; flex-direction:column; align-items:center; justify-content:center; color:#333; text-align:center; backdrop-filter:blur(8px); border-radius:20px;">

        <!-- Bouton en haut à droite de l'overlay -->
        <button type="button" onclick="skipScanner()"
            style="position:absolute; top:30px; right:30px; background:#fff; color:#004d99; border:2px solid #004d99; padding:12px 25px; border-radius:12px; font-weight:800; cursor:pointer; transition:0.3s; display:flex; align-items:center; gap:10px; box-shadow:0 4px 15px rgba(0,77,153,0.15);">
            <i class="fa-solid fa-plus-circle"></i> AJOUTER SANS SCANNER
        </button>

        <div
            style="background:#fff; color:#333; padding:40px; border-radius:24px; max-width:500px; width:90%; box-shadow:0 10px 40px rgba(0,0,0,0.1); border:1px solid #e2eaf3;">
            <div
                style="background:#f0f7ff; width:80px; height:80px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px;">
                <i class="fa-solid fa-barcode" style="font-size:35px; color:#004d99;"></i>
            </div>
            <h2 style="font-weight:800; font-size:24px; margin-bottom:10px;">Enrôlement Produit</h2>
            <p style="color:#64748b; margin-bottom:30px;">Veuillez scanner le code-barres du nouveau produit pour commencer
                l'enregistrement.</p>

            <div id="scanner-animation"
                style="position:relative; width:200px; height:100px; border:2px dashed #004d99; border-radius:12px; margin:0 auto 30px; overflow:hidden; background:#f8fafc; display:flex; align-items:center; justify-content:center;">
                <i class="fa-solid fa-keyboard" style="font-size:30px; color:#cbd5e1;"></i>
                <div id="scanner-line"
                    style="position:absolute; top:0; left:0; width:100%; height:2px; background:#004d99; box-shadow:0 0 10px #004d99; animation:scan 2s infinite ease-in-out;">
                </div>
            </div>

            <div style="display:flex; gap:10px; flex-direction:column;">
                <input type="text" id="manual-barcode" placeholder="Ou saisissez le code manuellement..."
                    style="width:100%; padding:15px; border:1px solid #e2eaf3; border-radius:12px; outline:none; text-align:center; font-weight:700; font-size:18px;">
                <button type="button" onclick="confirmBarcode()"
                    style="background:#004d99; color:white; border:none; padding:15px; border-radius:12px; font-weight:700; cursor:pointer; transition:0.3s; margin-top:10px;">CONFIRMER
                    LE CODE</button>

                <a href="{{ route('admin.products.index') }}"
                    style="color:#64748b; text-decoration:none; font-size:14px; margin-top:15px; display:inline-block;">Annuler
                    et retourner</a>
            </div>
        </div>
    </div>

    <style>
        @keyframes scan {
            0% {
                top: 0;
            }

            50% {
                top: 100%;
            }

            100% {
                top: 0;
            }
        }
    </style>

    <div class="prd-shell" id="product-form-shell" style="display:none;">
        <section class="prd-hero">
            <div>
                <p class="prd-kicker">ADMIN / PRODUITS</p>
                <h2>Ajouter un produit avec une fiche complete</h2>
                <p>Renseignez les informations de vente, stock et fournisseur pour une gestion fiable de la caisse et du
                    magasin.</p>
            </div>
            <div class="prd-badge-box">
                <span class="prd-badge" id="stockBadge">Stock normal</span>
                <small id="stockHint">Le stock est superieur au seuil d'alerte.</small>
            </div>
        </section>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="prd-grid"
            autocomplete="off">
            @csrf

            <article class="prd-card">
                <h3><i class="fa-solid fa-box"></i> Identite du produit</h3>

                <div class="prd-field">
                    <label for="name">Nom du produit <span>*</span></label>
                    <div class="prd-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
                        <i class="fa-solid fa-tag"></i>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" maxlength="255"
                            placeholder="Ex: Riz parfum 25kg" required>
                    </div>
                    @error('name')
                        <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="prd-field" style="display: none;">
                    <label for="barcode_value">Code-barres emballage</label>
                    <div class="prd-scan-wrap {{ $errors->has('barcode_value') ? 'is-invalid' : '' }}">
                        <div class="prd-input" style="margin-bottom:0;">
                            <i class="fa-solid fa-barcode"></i>
                            <input type="text" id="barcode_value" name="barcode_value" value="{{ old('barcode_value') }}"
                                maxlength="120" placeholder="Scanner ou saisir le code-barres du produit">
                        </div>
                        <button type="button" class="prd-scan-btn" id="scanBarcodeBtn">
                            <i class="fa-solid fa-camera"></i> Scanner
                        </button>
                    </div>
                    <small style="display:block; margin-top:6px; color:#5f7488; font-size:12px;">Ce code sera lie au
                        produit pour le scan en caisse.</small>
                    @error('barcode_value')
                        <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="prd-row-2">
                    <div class="prd-field">
                        <label for="category_name">Categorie <span>*</span></label>
                        <div class="prd-input {{ $errors->has('category_name') ? 'is-invalid' : '' }}">
                            <i class="fa-solid fa-layer-group"></i>
                            <select id="category_name" name="category_name" required>
                                <option value="" disabled {{ old('category_name') ? '' : 'selected' }}>Selectionner
                                </option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->name }}"
                                        {{ old('category_name') == $cat->name ? 'selected' : '' }}>
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
                                        {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>
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
                        <textarea id="description" name="description" rows="4" placeholder="Details ou caracteristiques du produit">{{ old('description') }}</textarea>
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
                                value="{{ old('price', 0) }}" required>
                        </div>
                        @error('price')
                            <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="prd-field">
                        <label for="stock">Stock initial <span>*</span></label>
                        <div class="prd-input {{ $errors->has('stock') ? 'is-invalid' : '' }}">
                            <i class="fa-solid fa-boxes-stacked"></i>
                            <input type="number" id="stock" name="stock" min="0"
                                value="{{ old('stock', 0) }}" required>
                        </div>
                        @error('stock')
                            <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                        @enderror
                    </div>

                    <div class="prd-field">
                        <label for="stock_threshold">Seuil alerte <span>*</span></label>
                        <div class="prd-input {{ $errors->has('stock_threshold') ? 'is-invalid' : '' }}">
                            <i class="fa-solid fa-bell"></i>
                            <input type="number" id="stock_threshold" name="stock_threshold" min="0"
                                value="{{ old('stock_threshold', 5) }}" required>
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
                        <label for="image" class="prd-upload-btn"><i class="fa-solid fa-image"></i> Choisir une
                            image</label>
                        <span id="fileName">Aucun fichier selectionne</span>
                    </div>
                    @error('image')
                        <p class="prd-error"><i class="fa-solid fa-circle-exclamation"></i> {{ $message }}</p>
                    @enderror
                </div>

                <div class="prd-preview" id="imagePreviewWrap">
                    <div class="prd-preview-empty" id="previewEmpty">
                        <i class="fa-solid fa-photo-film"></i>
                        <p>Apercu de l'image</p>
                    </div>
                    <img id="previewImage" alt="Apercu produit">
                </div>

                <div class="prd-actions">
                    <a href="{{ route('admin.products.index') }}" class="prd-btn prd-btn-muted">
                        <i class="fa-solid fa-arrow-left"></i> Retour
                    </a>
                    <button type="submit" class="prd-btn prd-btn-primary">
                        <i class="fa-solid fa-check-double"></i> Ajouter le produit
                    </button>
                </div>
            </article>
        </form>
    </div>
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

        .prd-scan-wrap {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            align-items: stretch;
        }

        .prd-scan-wrap.is-invalid .prd-input,
        .prd-scan-wrap.is-invalid .prd-scan-btn {
            border-color: #e11d48;
        }

        .prd-scan-btn {
            border: 1px solid #c9d8eb;
            background: #f4f8ff;
            color: #004d99;
            border-radius: 11px;
            padding: 0 13px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .prd-scan-btn:hover {
            background: #e9f1ff;
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
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        // Enrollment Logic
        (function() {
            let enrollmentBuffer = '';
            let lastEnrollKeyTime = Date.now();
            let isChecking = false; // Verrou pour éviter les doublons de vérification
            const enrollmentOverlay = document.getElementById('enrollment-overlay');
            const productFormShell = document.getElementById('product-form-shell');
            const barcodeInputValue = document.getElementById('barcode_value');
            const manualInput = document.getElementById('manual-barcode');

            document.addEventListener('keydown', (e) => {
                if (enrollmentOverlay.style.display === 'none') return;
                if (e.target === manualInput) return;

                const currentTime = Date.now();
                if (currentTime - lastEnrollKeyTime > 150) {
                    enrollmentBuffer = '';
                }

                if (e.key === 'Enter') {
                    if (enrollmentBuffer.length > 2) {
                        e.preventDefault(); // Empêcher d'autres actions
                        manualInput.value = enrollmentBuffer;
                        window.confirmBarcode();
                        enrollmentBuffer = '';
                    }
                } else if (e.key.length === 1) {
                    enrollmentBuffer += e.key;
                }
                lastEnrollKeyTime = currentTime;
            });

            window.confirmBarcode = function() {
                if (isChecking) return; // Si une vérification est déjà en cours, on ignore

                const code = manualInput.value.trim();
                if (code.length < 3) {
                    alert('Veuillez scanner ou saisir un code-barres valide.');
                    return;
                }

                isChecking = true; // On verrouille

                // Vérification en temps réel si le code-barres existe
                fetch(`{{ route('admin.products.check-barcode') }}?barcode=${encodeURIComponent(code)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.exists) {
                            alert('Erreur : Ce produit existe déjà dans la base de données.');
                            manualInput.value = '';
                            enrollmentBuffer = '';
                        } else {
                            // Si le code est nouveau, on remplit le champ caché et on affiche le formulaire
                            barcodeInputValue.value = code;
                            enrollmentOverlay.style.transition = '0.5s';
                            enrollmentOverlay.style.opacity = '0';
                            setTimeout(() => {
                                enrollmentOverlay.style.display = 'none';
                                productFormShell.style.display = 'grid';
                                const nameField = document.getElementById('name');
                                if (nameField) nameField.focus();
                            }, 500);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Une erreur est survenue lors de la vérification.');
                    })
                    .finally(() => {
                        isChecking = false; // On déverrouille quoi qu'il arrive
                    });
            };

            window.skipScanner = function() {
                // Pas de code-barres, le backend en générera un (PRD-...)
                barcodeInputValue.value = '';
                enrollmentOverlay.style.transition = '0.5s';
                enrollmentOverlay.style.opacity = '0';
                setTimeout(() => {
                    enrollmentOverlay.style.display = 'none';
                    productFormShell.style.display = 'grid';
                    const nameField = document.getElementById('name');
                    if (nameField) nameField.focus();
                }, 500);
            };
        })();

        (function() {
            const imageInput = document.getElementById('image');
            const fileName = document.getElementById('fileName');
            const previewImage = document.getElementById('previewImage');
            const previewEmpty = document.getElementById('previewEmpty');
            const stockInput = document.getElementById('stock');
            const thresholdInput = document.getElementById('stock_threshold');
            const stockBadge = document.getElementById('stockBadge');
            const stockHint = document.getElementById('stockHint');
            const barcodeInput = document.getElementById('barcode_value');
            const scanBarcodeBtn = document.getElementById('scanBarcodeBtn');

            const withTimeout = (promise, timeoutMs, timeoutLabel) => {
                return Promise.race([
                    promise,
                    new Promise((_, reject) => setTimeout(() => reject(new Error(timeoutLabel)), timeoutMs))
                ]);
            };

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
                fileName.textContent = file ? file.name : 'Aucun fichier selectionne';
                if (!file) {
                    previewImage.style.display = 'none';
                    previewEmpty.style.display = 'block';
                    previewImage.removeAttribute('src');
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

            const openBarcodeScanner = () => {
                if (!window.Swal) {
                    alert('Scanner indisponible. Veuillez saisir le code manuellement.');
                    return;
                }

                let html5Scanner = null;
                let mediaStream = null;
                let rafLoop = null;

                const stopAll = async () => {
                    if (rafLoop) {
                        cancelAnimationFrame(rafLoop);
                        rafLoop = null;
                    }

                    if (html5Scanner) {
                        const instance = html5Scanner;
                        html5Scanner = null;
                        try {
                            await instance.stop();
                        } catch (_) {}
                        try {
                            await instance.clear();
                        } catch (_) {}
                    }

                    if (mediaStream) {
                        mediaStream.getTracks().forEach(t => {
                            t.stop();
                            t.enabled = false;
                        });
                        mediaStream = null;
                    }
                };

                const finalizeCode = async (rawValue) => {
                    const value = String(rawValue || '').trim();
                    if (!value) return;
                    barcodeInput.value = value;
                    await stopAll();
                    Swal.close();
                };

                Swal.fire({
                    title: '<i class="fa-solid fa-barcode"></i> Scanner le code-barres',
                    html: `
                        <div id="admin-scan-reader" style="width:100%; min-height:260px; border-radius:10px; overflow:hidden; background:#111;"></div>
                        <p id="admin-scan-status" style="margin-top:10px; color:#64748b; font-size:13px; text-align:center;">
                            <i class="fa-solid fa-spinner fa-spin"></i> Initialisation du scanner...
                        </p>
                    `,
                    showCancelButton: true,
                    showConfirmButton: false,
                    cancelButtonText: '<i class="fa-solid fa-xmark"></i> Fermer',
                    cancelButtonColor: '#64748b',
                    width: 560,
                    didOpen: async () => {
                        const statusEl = document.getElementById('admin-scan-status');

                        if (typeof Html5Qrcode !== 'undefined') {
                            try {
                                html5Scanner = new Html5Qrcode('admin-scan-reader');

                                let cameraConfig = {
                                    facingMode: 'environment'
                                };
                                try {
                                    const cameras = await withTimeout(Html5Qrcode.getCameras(), 2500,
                                        'GetCamerasTimeout');
                                    if (Array.isArray(cameras) && cameras.length > 0) {
                                        cameraConfig = {
                                            deviceId: {
                                                exact: cameras[0].id
                                            }
                                        };
                                    }
                                } catch (_) {}

                                await withTimeout(html5Scanner.start(
                                    cameraConfig, {
                                        fps: 12,
                                        qrbox: {
                                            width: 260,
                                            height: 160
                                        }
                                    },
                                    (decodedText) => finalizeCode(decodedText),
                                    () => {}
                                ), 9000, 'StartScannerTimeout');

                                if (statusEl) {
                                    statusEl.innerHTML =
                                        '<i class="fa-solid fa-circle" style="color:#22c55e;font-size:10px;"></i> Scanner actif — pointez vers le code-barres';
                                }
                                return;
                            } catch (_) {
                                await stopAll();
                            }
                        }

                        if (!window.BarcodeDetector || !navigator.mediaDevices?.getUserMedia) {
                            if (statusEl) {
                                statusEl.innerHTML =
                                    '<span style="color:#e11d48"><i class="fa-solid fa-triangle-exclamation"></i> Scanner indisponible. Saisissez le code manuellement.</span>';
                            }
                            return;
                        }

                        const video = document.createElement('video');
                        video.style.width = '100%';
                        video.style.borderRadius = '10px';
                        video.autoplay = true;
                        video.muted = true;
                        video.playsInline = true;

                        const canvas = document.createElement('canvas');
                        canvas.style.display = 'none';

                        const reader = document.getElementById('admin-scan-reader');
                        if (reader) {
                            reader.innerHTML = '';
                            reader.appendChild(video);
                            reader.appendChild(canvas);
                        }

                        try {
                            mediaStream = await withTimeout(navigator.mediaDevices.getUserMedia({
                                video: true
                            }), 5000, 'CameraTimeout');
                            video.srcObject = mediaStream;

                            const detector = new BarcodeDetector({
                                formats: ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a',
                                    'upc_e',
                                    'itf', 'qr_code'
                                ]
                            });

                            if (statusEl) {
                                statusEl.innerHTML =
                                    '<i class="fa-solid fa-circle" style="color:#22c55e;font-size:10px;"></i> Scanner actif — pointez vers le code-barres';
                            }

                            const tick = async () => {
                                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                                    canvas.width = video.videoWidth;
                                    canvas.height = video.videoHeight;
                                    canvas.getContext('2d').drawImage(video, 0, 0);
                                    try {
                                        const codes = await detector.detect(canvas);
                                        if (codes.length > 0) {
                                            finalizeCode(codes[0].rawValue);
                                            return;
                                        }
                                    } catch (_) {}
                                }
                                rafLoop = requestAnimationFrame(tick);
                            };

                            rafLoop = requestAnimationFrame(tick);
                        } catch (_) {
                            if (statusEl) {
                                statusEl.innerHTML =
                                    '<span style="color:#e11d48"><i class="fa-solid fa-triangle-exclamation"></i> Impossible d\'ouvrir la camera. Saisissez le code manuellement.</span>';
                            }
                        }
                    },
                    willClose: () => {
                        stopAll();
                    },
                    didDestroy: () => {
                        stopAll();
                    }
                });
            };

            if (scanBarcodeBtn) {
                scanBarcodeBtn.addEventListener('click', openBarcodeScanner);
            }

            syncStockState();
        })();
    </script>
@endpush
