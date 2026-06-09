@extends('employee.layouts.app')

@section('title', 'Interface Caisse')

@section('content')
    @if (!$activeSession)
        <!-- Overlay d'ouverture de caisse -->
        <div class="session-gate"
            style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(10px); z-index: 9999; display: flex; align-items: center; justify-content: center; color: white;">
            <div class="gate-card"
                style="background: #1e293b; border: 1px solid #334155; border-radius: 20px; padding: 40px; width: 100%; max-width: 450px; text-align: center; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                <div
                    style="background: rgba(99, 102, 241, 0.15); width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fa-solid fa-cash-register" style="font-size: 36px; color: #6366f1;"></i>
                </div>
                <h2 style="margin: 0 0 10px; font-size: 24px; font-weight: 700;">Ouverture de la Caisse</h2>
                <p style="color: #94a3b8; font-size: 14px; margin: 0 0 30px;">Veuillez déclarer le montant du fond de caisse
                    initial disponible dans le tiroir-caisse pour démarrer votre journée.</p>

                <form id="open-session-form" onsubmit="submitOpenSession(event)">
                    <div style="text-align: left; margin-bottom: 20px;">
                        <label for="opening_balance"
                            style="display: block; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #94a3b8; margin-bottom: 8px;">Fond
                            de caisse initial (FCFA)</label>
                        <input type="number" id="opening_balance" required min="0" value="0"
                            style="width: 100%; background: #0f172a; border: 1px solid #334155; border-radius: 12px; padding: 12px 16px; color: white; font-size: 18px; font-weight: 600; text-align: center; outline: none; transition: border-color 0.2s;"
                            placeholder="0 FCFA" autofocus>
                    </div>
                    <button type="submit"
                        style="width: 100%; background: #6366f1; color: white; border: none; border-radius: 12px; padding: 14px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                        <i class="fa-solid fa-play"></i> DÉMARRER LA SESSION
                    </button>
                </form>

                <!-- Bouton Se Déconnecter -->
                <div style="margin-top: 20px; border-top: 1px solid #334155; padding-top: 20px;">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf @method('DELETE')
                        <button type="submit"
                            style="width: 100%; background: transparent; color: #f87171; border: 1px dashed #f87171; border-radius: 12px; padding: 12px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px;"
                            onmouseover="this.style.background='rgba(239, 68, 68, 0.1)';"
                            onmouseout="this.style.background='transparent';">
                            <i class="fa-solid fa-right-from-bracket"></i> SE DÉCONNECTER
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- POS Central (Caisse View) -->
    <main class="pos-center" id="view-caisse">
        <div class="search-area">
            <div class="search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="search-input" id="search-input" oninput="searchProducts()"
                    placeholder="Scanner un produit ou taper son nom..." autofocus>
            </div>
            <button class="toggle-manual-btn" id="toggle-manual-btn" onclick="toggleManualProducts()">
                <i class="fa-solid fa-list-check"></i>
                <span>Voir Non-Enrôlés</span>
            </button>
            @if ($activeSession)
                <button class="close-session-btn" onclick="showCloseSessionModal()"
                    style="background: #ef4444; color: white; border: none; padding: 10px 18px; border-radius: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 13px; transition: background 0.2s; white-space: nowrap;"
                    onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                    <i class="fa-solid fa-shop-slash"></i>
                    <span>Fermer la caisse</span>
                </button>
            @endif
        </div>

        <div class="category-filter">
            <button class="cat-badge active" onclick="filterCategory('all', this)">Tous les produits</button>
            @foreach ($categories as $cat)
                <button class="cat-badge" onclick="filterCategory('{{ $cat->name }}', this)">{{ $cat->name }}</button>
            @endforeach
        </div>

        <div class="products-grid" id="products-container">
            @foreach ($products as $product)
                <div class="product-card" id="prod-card-{{ $product->id }}" data-name="{{ strtolower($product->name) }}"
                    data-ref="{{ strtolower($product->reference) }}" data-category="{{ $product->category_name }}"
                    style="display: flex;" onclick="addToCart({{ json_encode($product) }})">
                    <div class="product-card-stock-badge product-stock" id="stock-badge-{{ $product->id }}"
                        style="color: {{ $product->stock <= $product->stock_threshold ? '#e11d48' : '#059669' }};">
                        Stock: {{ $product->stock }}
                    </div>
                    <div class="selected-indicator">0</div>
                    <div class="product-img">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div
                                style="width:100%; height:100%; background: linear-gradient(135deg, #004d99, #1a6bbf); display:flex; align-items:center; justify-content:center; color:white; font-weight:800; font-size:28px;">
                                {{ strtoupper(mb_substr($product->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="product-info">
                        <h3>{{ $product->name }}</h3>
                        <div class="product-price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <!-- Stock View (Table) -->
    @include('employee.stock')

    <!-- History View -->
    @include('employee.historique')

    <!-- Credits View -->
    @include('employee.credits')

    <!-- Statistics View -->
    @include('employee.statistique')

    <!-- Profile View -->
    @include('employee.profile')


    <!-- POS Right (Cart) -->
    <section class="pos-right">
        <div class="cart-header"
            style="display: flex; align-items: center; justify-content: space-between; padding: 20px 25px;">
            <div>
                <h2 style="margin: 0; font-size: 18px; font-weight: 800;">Panier Actuel</h2>
                <span class="count" style="font-size: 12px; color: var(--text-muted);">0 articles</span>
            </div>
            <button id="clear-cart-btn" onclick="clearCartWithConfirmation()" title="Vider le panier"
                style="background: rgba(225, 29, 72, 0.1); border: none; color: var(--danger); width: 36px; height: 36px; border-radius: 10px; display: none; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.background='var(--danger)'; this.style.color='white'"
                onmouseout="this.style.background='rgba(225, 29, 72, 0.1)'; this.style.color='var(--danger)'">
                <i class="fa-solid fa-trash-can" style="font-size: 16px;"></i>
            </button>
        </div>

        <div class="cart-items" id="cart-container">
            <div class="empty-cart">
                <i class="fa-solid fa-basket-shopping"></i>
                <p>Le panier est vide.<br>Sélectionnez un produit pour commencer.</p>
            </div>
        </div>

        <!-- Client Selection -->
        <div class="cart-client"
            style="padding: 15px; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: #f8fafc; display: flex; flex-direction: column; gap: 8px;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <label
                    style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; display: flex; align-items: center; gap: 5px;">
                    <i class="fa-solid fa-user" style="color: var(--primary);"></i>
                    Client
                </label>
                <button onclick="quickAddCustomer()" title="Ajouter rapidement un client"
                    style="background: none; border: none; color: var(--primary); font-size: 12px; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 4px; padding: 2px 5px; border-radius: 4px; transition: background 0.2s;"
                    onmouseover="this.style.background='rgba(99,102,241,0.1)'" onmouseout="this.style.background='none'">
                    <i class="fa-solid fa-user-plus"></i> Nouveau
                </button>
            </div>
            <select id="cart-customer-id"
                style="width: 100%; border: 1px solid var(--border); border-radius: 8px; padding: 8px 12px; font-size: 13px; color: var(--text); background: white; outline: none; cursor: pointer;">
                <option value="" data-blocked="0">Client de passage (Anonyme)</option>
                @foreach ($customers as $c)
                    <option value="{{ $c->id }}" data-blocked="{{ $c->is_credit_blocked ? '1' : '0' }}">
                        {{ $c->name }} {{ $c->phone ? '(' . $c->phone . ')' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="cart-footer">
            <div class="summary-total">
                <span>TOTAL</span>
                <span id="total-price">0 FCFA</span>
            </div>

            <!-- Actions supplémentaires panier -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 15px;">
                <button type="button" onclick="holdCurrentCart()"
                    style="background: #64748b; color: white; border: none; border-radius: 8px; padding: 10px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; transition: background 0.2s;"
                    onmouseover="this.style.background='#475569'" onmouseout="this.style.background='#64748b'">
                    <i class="fa-solid fa-pause"></i> Suspendre
                </button>
                <button type="button" id="held-carts-btn" onclick="showHeldCarts()"
                    style="background: #334155; color: white; border: none; border-radius: 8px; padding: 10px; font-size: 12px; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 5px; transition: background 0.2s;"
                    onmouseover="this.style.background='#1e293b'" onmouseout="this.style.background='#334155'">
                    <i class="fa-solid fa-box-archive"></i> En Attente (<span id="held-carts-count">0</span>)
                </button>
            </div>

            <button class="checkout-btn" id="checkout-btn" disabled onclick="validateSale()">
                <i class="fa-solid fa-circle-check"></i> VALIDER LA VENTE
            </button>
        </div>
    </section>
@endsection

@push('print')
    <!-- Receipt Template (Hidden) -->
    <div id="receipt-print">
        <div style="text-align: center; margin-bottom: 15px;">
            <p style="margin: 0; font-size: 18px; font-weight: normal;">{{ $storeSettings->store_name }}</p>
            <p style="margin: 5px 0; font-size: 11px;">{{ $storeSettings->address }}<br>Tel: {{ $storeSettings->phone }}
                @if ($storeSettings->email)
                    <br>Email: {{ $storeSettings->email }}
                @endif
            </p>
            <div style="border-bottom: 1px dashed #000; margin: 10px 0;"></div>
            <p style="margin: 5px 0;" id="receipt-ref">REF: #SAL-000000</p>
            <p style="margin: 5px 0;" id="receipt-date">Date: 27/04/2026 00:00</p>
            <p style="margin: 5px 0; font-size: 11px; display: none;" id="receipt-customer">Client: </p>
            <p style="margin: 5px 0; font-size: 11px;" id="receipt-cashier">Caissier: —</p>
        </div>

        <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #000;">
                    <th style="text-align: left; padding: 5px 0; font-weight: normal;">Article</th>
                    <th style="text-align: center; padding: 5px 0; font-weight: normal;">Qté</th>
                    <th style="text-align: right; padding: 5px 0; font-weight: normal;">Total</th>
                </tr>
            </thead>
            <tbody id="receipt-items">
                <!-- Dynamically populated -->
            </tbody>
        </table>

        <div style="border-top: 1px dashed #000; margin: 15px 0; padding-top: 10px;">
            <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 5px;">
                <span>TOTAL</span>
                <span id="receipt-total">0 FCFA</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px;">
                <span>Paiement:</span>
                <span id="receipt-method">Espèces</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px;">
                <span>Reçu:</span>
                <span id="receipt-received">0 FCFA</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px;">
                <span>Rendu:</span>
                <span id="receipt-change">0 FCFA</span>
            </div>
        </div>

        <div style="text-align: center; margin-top: 20px; font-size: 10px;">
            <div id="receipt-qrcode" style="display: flex; justify-content: center; margin-bottom: 10px;"></div>
            <p style="margin: 5px 0; line-height: 1.4;">
                {{ $storeSettings->invoice_footer ?? 'Merci de votre visite ! A bientôt.' }}</p>
        </div>
    </div>

    <!-- A4 Receipt Template (Hidden) -->
    <div id="receipt-print-a4">
        <div style="font-family: 'Inter', sans-serif; color: #1e293b; padding: 20px; line-height: 1.5;">
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div>
                    <h1
                        style="margin: 0 0 5px 0; font-size: 24px; font-weight: 800; color: #004d99; letter-spacing: -0.5px;">
                        {{ $storeSettings->store_name }}</h1>
                    <p style="margin: 0; font-size: 12px; color: #64748b; line-height: 1.6;">
                        {{ $storeSettings->address }}<br>
                        Tel: {{ $storeSettings->phone }}@if ($storeSettings->email)
                            / Email: {{ $storeSettings->email }}
                        @endif
                    </p>
                </div>
                <div style="text-align: right;">
                    <h2 style="margin: 0; color: #004d99; font-size: 20px; font-weight: 800;">FACTURE</h2>
                    <p style="margin: 5px 0 0 0; font-size: 13px; color: #64748b;">
                        Réf : <strong id="receipt-a4-ref">#SAL-000000</strong><br>
                        Date : <span id="receipt-a4-date">27/04/2026 00:00</span>
                    </p>
                </div>
            </div>

            <div style="border-bottom: 2px solid #f1f5f9; margin: 20px 0;"></div>

            <!-- Meta info grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                <div>
                    <h3
                        style="margin: 0 0 8px 0; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">
                        Client :</h3>
                    <p id="receipt-a4-customer-box" style="margin: 0; font-size: 14px;"><strong>Client de Passage</strong>
                    </p>
                </div>
                <div style="text-align: right;">
                    <h3
                        style="margin: 0 0 8px 0; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b;">
                        Opérateur :</h3>
                    <p style="margin: 0 0 5px 0; font-size: 14px;"><strong id="receipt-a4-cashier">Caissier: —</strong>
                    </p>
                    <p style="margin: 0 0 3px 0; font-size: 12px; color: #64748b;">Rôle : Caissier / POS</p>
                    <p style="margin: 0; font-size: 12px; color: #64748b;">Mode de paiement : <strong
                            id="receipt-a4-method">Espèces</strong></p>
                </div>
            </div>

            <!-- Table -->
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 30px;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th
                            style="padding: 12px 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">
                            Désignation de l'article</th>
                        <th
                            style="text-align: center; width: 120px; padding: 12px 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">
                            Prix unitaire</th>
                        <th
                            style="text-align: center; width: 80px; padding: 12px 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">
                            Quantité</th>
                        <th
                            style="text-align: right; width: 150px; padding: 12px 10px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #475569;">
                            Montant Total</th>
                    </tr>
                </thead>
                <tbody id="receipt-a4-items">
                    <!-- Dynamically populated -->
                </tbody>
            </table>

            <!-- Totaux (sans signature) -->
            <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <div style="width: 300px;">
                    <div
                        style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9;">
                        <span>Total Global:</span>
                        <strong id="receipt-a4-total" style="font-size: 16px; color: #004d99;">0 FCFA</strong>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9; color: #64748b;">
                        <span>Montant Encaissé:</span>
                        <span id="receipt-a4-received">0 FCFA</span>
                    </div>
                    <div
                        style="display: flex; justify-content: space-between; padding: 8px 0; font-size: 13px; border-bottom: 1px solid #f1f5f9; color: #64748b;">
                        <span>Monnaie Rendue:</span>
                        <span id="receipt-a4-change">0 FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                style="margin-top: 40px; text-align: center; border-top: 1px dashed #e2e8f0; padding-top: 20px; font-size: 11px; color: #64748b; line-height: 1.5;">
                <p id="receipt-a4-footer">{{ $storeSettings->invoice_footer ?? 'Merci pour votre confiance !' }}</p>
                <p style="font-size: 10px; color: #94a3b8; margin-top: 10px;">{{ $storeSettings->store_name }} - Solution
                    de Facturation Intégrée</p>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        // View Switcher Logic
        function switchView(view) {
            const views = ['caisse', 'stock', 'history', 'stats', 'profile', 'credits'];
            views.forEach(v => {
                const el = document.getElementById('view-' + v);
                if (el) el.style.display = (v === view) ? 'flex' : 'none';
                const nav = document.getElementById('nav-' + v);
                if (nav) nav.classList.toggle('active', v === view);
            });

            // Special handling for Right POS panel (only in caisse)
            const rightPanel = document.querySelector('.pos-right');
            if (rightPanel) rightPanel.style.display = (view === 'caisse') ? 'flex' : 'none';

            const appContent = document.querySelector('.app-content');
            if (appContent) {
                appContent.style.gridTemplateColumns = (view === 'caisse') ? '240px 1fr 380px' : '240px 1fr';
            }

            // Gérer la visibilité du contrôleur offline dans la navbar
            const offlineIndicator = document.getElementById('nav-offline-indicator');
            if (offlineIndicator) {
                offlineIndicator.style.display = (view === 'caisse') ? 'flex' : 'none';
            }
        }

        // Stock Filtering
        let stockCategory = 'all';

        function filterStockCategory(cat, btn) {
            stockCategory = cat;
            document.querySelectorAll('.cat-badge-stock').forEach(b => {
                b.style.background = '#fff';
                b.style.color = 'var(--text)';
                b.style.borderColor = 'var(--border)';
            });
            btn.style.background = 'var(--primary)';
            btn.style.color = '#fff';
            btn.style.borderColor = 'var(--primary)';
            filterStockTable();
        }

        function filterStockTable() {
            const query = document.getElementById('stock-search-input').value.toLowerCase();
            const rows = document.querySelectorAll('.stock-row');

            rows.forEach(row => {
                const name = row.dataset.name;
                const category = row.dataset.category;
                const isLow = row.dataset.isLow === 'true';

                const matchName = name.includes(query);
                const matchCat = (stockCategory === 'all' || category === stockCategory);
                const matchCritical = !onlyCritical || isLow;

                row.style.display = (matchName && matchCat && matchCritical) ? '' : 'none';
            });
        }

        // History Filtering
        function filterSalesHistory() {
            const query = document.getElementById('history-search-input').value.toLowerCase();
            const rows = document.querySelectorAll('.sale-row');

            rows.forEach(row => {
                const ref = row.dataset.ref;
                row.style.display = ref.includes(query) ? '' : 'none';
            });
        }

        let onlyCritical = false;

        function showCriticalStock(btn) {
            onlyCritical = !onlyCritical;
            if (onlyCritical) {
                btn.style.background = 'var(--danger)';
                btn.style.color = '#fff';
            } else {
                btn.style.background = '#fff';
                btn.style.color = 'var(--danger)';
            }
            filterStockTable();
        }

        // Clock Update
        function updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('live-clock').textContent = `${h}:${m}:${s}`;

            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            document.getElementById('live-date').textContent = now.toLocaleDateString('fr-FR', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
        // POS Logic
        let productsRegistry = @json($allProducts->keyBy('id'));
        let cart = JSON.parse(localStorage.getItem('pos_cart')) || [];
        let currentCategory = 'all';
        let showManual = false;
        let hasBeenScanned = false; // Flag pour savoir si un scan a eu lieu

        async function refreshProductStocks() {
            try {
                const response = await fetch('{{ route('employee.pos.products-stock') }}');
                if (!response.ok) return;
                const data = await response.json();
                if (data && data.success && Array.isArray(data.products)) {
                    data.products.forEach(p => {
                        // Mettre à jour dans notre registre local
                        if (productsRegistry[p.id]) {
                            productsRegistry[p.id].stock = p.stock;
                        } else {
                            productsRegistry[p.id] = {
                                id: p.id,
                                stock: p.stock,
                                stock_threshold: p.stock_threshold
                            };
                        }

                        // Mettre à jour l'élément badge de stock du produit dans la grille POS
                        const badge = document.getElementById(`stock-badge-${p.id}`);
                        const card = document.getElementById(`prod-card-${p.id}`);
                        const threshold = p.stock_threshold !== undefined ? p.stock_threshold : 5;
                        const isLow = p.stock <= threshold;

                        if (badge) {
                            badge.textContent = `Stock: ${p.stock}`;
                            badge.style.color = isLow ? '#e11d48' : '#059669';
                        }

                        if (card) {
                            if (p.stock <= 0) {
                                card.style.opacity = '0.5';
                                card.style.pointerEvents = 'none';
                                if (badge) {
                                    badge.textContent = `En rupture`;
                                    badge.style.color = '#e11d48';
                                }
                            } else {
                                card.style.opacity = '1';
                                card.style.pointerEvents = 'auto';
                            }
                        }

                        // Mettre à jour la ligne de stock dans le tableau de l'onglet Inventaire/Stock
                        const stockValCell = document.getElementById(`stock-val-${p.id}`);
                        if (stockValCell) {
                            if (isLow) {
                                stockValCell.innerHTML = `
                                    <span style="background: #fff1f2; color: #e11d48; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 700;">
                                        ${p.stock} (Bas)
                                    </span>
                                `;
                            } else {
                                stockValCell.innerHTML = `
                                    <span style="background: #e8f9f0; color: #059669; padding: 4px 10px; border-radius: 6px; font-size: 13px; font-weight: 700;">
                                        ${p.stock}
                                    </span>
                                `;
                            }
                        }
                    });
                }
            } catch (err) {
                console.error("Erreur actualisation stocks:", err);
            }
        }

        // Lancer l'actualisation en arrière-plan toutes les 2 secondes
        setInterval(refreshProductStocks, 2000);

        function searchProducts() {
            const query = document.getElementById('search-input').value.toLowerCase();
            const cards = document.querySelectorAll('.product-card');

            cards.forEach(card => {
                const name = card.dataset.name;
                const ref = card.dataset.ref;
                const cat = card.dataset.category;
                const isAuto = ref.startsWith('prd-');
                const indicator = card.querySelector('.selected-indicator');
                const isSelected = indicator && indicator.textContent !== '0';

                const matchesSearch = name.includes(query) || ref.includes(query);
                const matchesCat = currentCategory === 'all' || cat === currentCategory;

                let matchesVisibility = true;

                if (hasBeenScanned) {
                    // Si un scan a eu lieu, on applique la règle de filtrage
                    matchesVisibility = isSelected || (showManual && isAuto);
                }
                // Si pas encore de scan, matchesVisibility reste true (on voit tout)

                card.style.display = (matchesSearch && matchesCat && matchesVisibility) ? 'flex' : 'none';
            });
        }

        function toggleManualProducts() {
            showManual = !showManual;
            const btn = document.getElementById('toggle-manual-btn');
            if (showManual) {
                btn.classList.add('active');
                btn.querySelector('span').innerText = 'Masquer Non-Enrôlés';
            } else {
                btn.classList.remove('active');
                btn.querySelector('span').innerText = 'Voir Non-Enrôlés';
            }
            searchProducts();
        }

        function filterCategory(cat, el) {
            currentCategory = cat;
            document.querySelectorAll('.cat-badge').forEach(b => b.classList.remove('active'));
            el.classList.add('active');
            searchProducts();
        }

        function addToCart(product) {
            const registryProduct = productsRegistry[product.id] || product;
            const existing = cart.find(item => item.id === product.id);
            if (existing) {
                if (existing.qty >= registryProduct.stock) {
                    Swal.fire('Oups!', 'Stock insuffisant', 'warning');
                    return;
                }
                existing.qty++;
            } else {
                if (registryProduct.stock <= 0) {
                    Swal.fire('Oups!', 'Produit en rupture de stock', 'error');
                    return;
                }
                cart.push({
                    ...registryProduct,
                    qty: 1
                });
            }
            saveCart();
            updateCartUI();
        }

        function updateQty(id, delta) {
            const item = cart.find(i => i.id === id);
            if (item) {
                const registryProduct = productsRegistry[id] || item;
                item.qty += delta;
                if (item.qty <= 0) {
                    removeFromCart(id);
                } else if (item.qty > registryProduct.stock) {
                    item.qty = registryProduct.stock;
                    Swal.fire('Max!', 'Stock maximum atteint', 'info');
                }
            }
            saveCart();
            updateCartUI();
        }

        function removeFromCart(id) {
            cart = cart.filter(i => i.id !== id);
            saveCart();
            updateCartUI();

            // Rafraîchir l'affichage pour masquer le produit s'il n'est plus dans le panier
            searchProducts();

            // Si le panier devient vide, on réinitialise tout
            if (cart.length === 0) {
                resetCartState();
            }
        }

        function resetCartState() {
            hasBeenScanned = false; // Désactive le mode filtrage
            const btn = document.getElementById('toggle-manual-btn');
            if (btn) {
                btn.style.display = 'none';
                btn.classList.remove('active');
                btn.querySelector('span').innerText = 'Voir Non-Enrôlés';
            }
            showManual = false;
            searchProducts(); // Réaffiche tout car hasBeenScanned est false
        }

        function clearCartWithConfirmation() {
            if (cart.length === 0) return;

            Swal.fire({
                title: 'Vider le panier ?',
                text: 'Voulez-vous vraiment retirer tous les articles du panier actuel ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--danger)',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, vider',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    saveCart();
                    updateCartUI();
                    resetCartState();
                    Swal.fire({
                        title: 'Vidé !',
                        text: 'Le panier a été vidé avec succès.',
                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });
        }

        function saveCart() {
            localStorage.setItem('pos_cart', JSON.stringify(cart));
        }

        function updateCartUI() {
            const container = document.getElementById('cart-container');
            const totalItemsEl = document.querySelector('.cart-header .count');
            const totalEl = document.getElementById('total-price');
            const btn = document.getElementById('checkout-btn');
            const clearBtn = document.getElementById('clear-cart-btn');

            if (cart.length === 0) {
                container.innerHTML = `
                            <div class="empty-cart">
                                <i class="fa-solid fa-basket-shopping"></i>
                                <p>Le panier est vide.<br>Sélectionnez un produit pour commencer.</p>
                            </div>`;
                totalItemsEl.textContent = '0 articles';
                totalEl.textContent = '0 FCFA';
                btn.disabled = true;
                if (clearBtn) clearBtn.style.display = 'none';

                // Sync products
                document.querySelectorAll('.product-card').forEach(card => card.classList.remove('selected'));
                return;
            }

            if (clearBtn) clearBtn.style.display = 'flex';
            let total = 0;
            let itemsCount = 0;
            container.innerHTML = cart.map(item => {
                const itemTotal = item.price * item.qty;
                total += itemTotal;
                itemsCount += item.qty;
                const currentStock = productsRegistry[item.id] ? productsRegistry[item.id].stock : item.stock;
                return `
                            <div class="cart-item">
                                <div class="item-details">
                                    <div class="item-name">${item.name}</div>
                                    <div class="item-meta">Stock dispo: ${currentStock}</div>
                                </div>
                                <div class="item-qty">
                                    <button class="qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                                    <span class="qty-val">${item.qty}</span>
                                    <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                                </div>
                                <div class="item-price-wrap">
                                    <div class="item-price">${itemTotal.toLocaleString()} FCFA</div>
                                    <div class="item-qty-total">${item.qty} x ${item.price.toLocaleString()} FCFA</div>
                                </div>
                                <div class="remove-item" onclick="removeFromCart(${item.id})" title="Retirer">
                                    <i class="fa-solid fa-trash-can"></i>
                                </div>
                            </div>
                        `;
            }).join('');

            totalItemsEl.textContent = `${itemsCount} article(s)`;
            totalEl.textContent = `${total.toLocaleString()} FCFA`;
            btn.disabled = false;

            // Sync product cards selected state
            document.querySelectorAll('.product-card').forEach(card => {
                const id = parseInt(card.id.replace('prod-card-', ''));
                const cartItem = cart.find(i => i.id === id);
                const indicator = card.querySelector('.selected-indicator');

                if (cartItem) {
                    card.classList.add('selected');
                    indicator.textContent = cartItem.qty;
                } else {
                    card.classList.remove('selected');
                    indicator.textContent = '0';
                }
            });
        }

        async function validateSale() {
            if (cart.length === 0) return;

            const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const customerSelect = document.getElementById('cart-customer-id');
            const selectedOption = customerSelect.options[customerSelect.selectedIndex];
            const customerId = customerSelect.value;
            const customerName = selectedOption ? selectedOption.text : '';
            const isBlocked = selectedOption ? selectedOption.getAttribute('data-blocked') === '1' : false;

            // Step 1: Integrated dialog for Payment Method and Amount Received
            const {
                value: formValues
            } = await Swal.fire({
                title: 'Règlement de la commande',
                html: `
                            <div style="text-align: left; font-size: 15px; font-family: 'Outfit', sans-serif;">
                                <div style="background: #f3f4f6; padding: 12px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid var(--success);">
                                    <p style="margin:0; font-size: 15px; color: var(--text-muted);">Total à payer :</p>
                                    <p style="margin:5px 0 0 0; font-size: 22px; font-weight: 800; color: var(--success);">${total.toLocaleString()} FCFA</p>
                                    ${customerId ? `<p style="margin:5px 0 0 0; font-size:13px; color:#4b5563;"><i class="fa-solid fa-user"></i> Client : <strong>${customerName}</strong></p>` : ''}
                                </div>
                                ${customerId ? `
                                                            <div style="margin-bottom: 15px;">
                                                                <label style="font-weight: 600; display: block; margin-bottom: 6px; color: var(--text);">Type de Vente :</label>
                                                                <select id="swal-sale-type" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--card);">
                                                                    <option value="paiement" selected>Paiement Direct (Comptant)</option>
                                                                    <option value="credit">Vente à Crédit (Compte client)</option>
                                                                </select>
                                                            </div>
                                                            ` : '<input type="hidden" id="swal-sale-type" value="paiement">'}
                                <div id="payment-method-container" style="margin-bottom: 15px;">
                                    <label style="font-weight: 600; display: block; margin-bottom: 6px; color: var(--text);">Moyen de Paiement :</label>
                                    <select id="swal-payment-method" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--card);">
                                        <option value="cash" selected>Espèces (Cash)</option>
                                        <option value="card">Carte Bancaire / Mobile Money</option>
                                    </select>
                                </div>
                                <div id="received-amount-container" style="margin-bottom: 15px;">
                                    <label style="font-weight: 600; display: block; margin-bottom: 6px; color: var(--text);">Montant Reçu (FCFA) :</label>
                                    <input id="swal-amount-received" type="number" class="form-control" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid var(--border); background: var(--card);" value="${total}" step="5">
                                </div>
                            </div>
                        `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Valider',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#059669',
                didOpen: () => {
                    const saleType = document.getElementById('swal-sale-type');
                    const paymentMethodContainer = document.getElementById('payment-method-container');
                    const receivedContainer = document.getElementById('received-amount-container');
                    const amountInput = document.getElementById('swal-amount-received');

                    if (saleType) {
                        const toggleFields = () => {
                            if (saleType.value === 'credit') {
                                if (paymentMethodContainer) paymentMethodContainer.style.display =
                                    'none';
                                if (receivedContainer) receivedContainer.style.display = 'none';
                                amountInput.value = 0;
                            } else {
                                if (paymentMethodContainer) paymentMethodContainer.style.display =
                                    'block';
                                if (receivedContainer) receivedContainer.style.display = 'block';
                                amountInput.value = total;
                            }
                        };
                        saleType.addEventListener('change', toggleFields);
                        toggleFields();
                    }
                },
                preConfirm: () => {
                    const saleType = document.getElementById('swal-sale-type').value;
                    const paymentMethod = saleType === 'credit' ? 'credit' : document.getElementById(
                        'swal-payment-method').value;
                    const amountInput = document.getElementById('swal-amount-received');
                    const amountReceived = parseFloat(amountInput.value || 0);

                    if (saleType === 'credit' && isBlocked) {
                        Swal.showValidationMessage(
                            `Ce client est bloqué pour les achats à crédit par l'administrateur.`
                        );
                        return false;
                    }

                    if (saleType !== 'credit' && amountReceived < total) {
                        Swal.showValidationMessage(
                            `Le montant reçu doit être supérieur ou égal à ${total.toLocaleString()} FCFA`
                        );
                        return false;
                    }

                    return {
                        paymentMethod: paymentMethod,
                        amountReceived: amountReceived,
                        changeAmount: saleType === 'credit' ? 0 : (amountReceived - total)
                    }
                }
            });

            if (!formValues) return;

            const received = formValues.amountReceived;
            const change = formValues.changeAmount;
            const paymentMethodSelected = formValues.paymentMethod;

            // Step 2: Process request
            Swal.fire({
                title: 'Enregistrement...',
                text: 'Veuillez patienter',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const customerIdVal = document.getElementById('cart-customer-id').value;



            // Détection réelle offline globale (Utilise checkActualConnection défini dans layouts/app)
            let isCurrentlyOnline = navigator.onLine;
            if (typeof checkActualConnection === 'function') {
                isCurrentlyOnline = await checkActualConnection();
            }

            if (!isCurrentlyOnline) {
                // FALLBACK MODE OFFLINE : Enregistrement local
                const offlineReference = 'SAL-OFF-' + Math.random().toString(36).substr(2, 9).toUpperCase();

                // Mettre à jour fictivement les stocks locaux dans l'interface en attendant la synchro
                cart.forEach(item => {
                    const card = document.getElementById(`prod-card-${item.id}`);
                    if (card) {
                        const stockBadge = card.querySelector('.product-stock');
                        if (stockBadge) {
                            let currStock = parseInt(stockBadge.textContent.replace('Stock: ', '')) || 0;
                            let nextStock = Math.max(0, currStock - item.qty);
                            stockBadge.textContent = `Stock: ${nextStock}`;
                        }
                    }
                });

                const offlineSale = {
                    user_id: {{ auth()->id() }},
                    cash_session_id: {{ $activeSession->id ?? 'null' }},
                    items: cart,
                    total_amount: total,
                    amount_received: received,
                    change_amount: change,
                    customer_id: customerIdVal,
                    payment_method: paymentMethodSelected,
                    reference: offlineReference,
                    created_at: new Date().toISOString()
                };

                try {
                    if (window.electronAPI && typeof window.electronAPI.saveOfflineSale === 'function') {
                        await window.electronAPI.saveOfflineSale(offlineSale);
                    } else {
                        console.warn("saveOfflineSale non disponible dans ce navigateur, sauvegarde locale simulée.");
                    }
                    printLocalReceipt(offlineSale);

                    Swal.fire({
                        title: 'Vente sauvegardée (Hors-ligne)',
                        text: 'La vente a été enregistrée localement et sera synchronisée dès le retour d\'une connexion internet.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        cart = [];
                        saveCart();
                        updateCartUI();
                        refreshProductStocks();
                    });
                } catch (err) {
                    console.error("Erreur de sauvegarde locale :", err);
                    Swal.fire("Erreur", "Impossible d'enregistrer la vente localement.", "error");
                }
                return;
            }

            fetch('{{ route('employee.pos.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: cart,
                        total_amount: total,
                        amount_received: received,
                        change_amount: change,
                        customer_id: customerIdVal,
                        payment_method: paymentMethodSelected
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || response.statusText);
                        });
                    }
                    return response.json();
                })
                .then((result) => {
                    if (result.success) {
                        const sale = result.value ? result.value.sale : result.sale;

                        // Imprimer avec le moteur d'impression pro unifié
                        printLocalReceipt(sale);

                        // Check for low stock alerts
                        if (result.low_stock_alerts && result.low_stock_alerts.length > 0) {
                            let alertMsg =
                                'Les produits suivants ont atteint le seuil critique :<br><br>';
                            result.low_stock_alerts.forEach(item => {
                                alertMsg +=
                                    `• <b>${item.name}</b> : ${item.current_stock} restant(s)<br>`;
                            });

                            Swal.fire({
                                title: 'Alerte Stock Bas !',
                                html: alertMsg,
                                icon: 'warning',
                                confirmButtonText: 'Compris'
                            }).then(() => {
                                cart = [];
                                saveCart();
                                updateCartUI();
                                refreshProductStocks();
                            });
                        } else {
                            Swal.fire({
                                title: 'Succès !',
                                text: 'Vente enregistrée et ticket imprimé.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                cart = [];
                                saveCart();
                                updateCartUI();
                                refreshProductStocks();
                            });
                        }
                    }
                })
                .catch(error => {
                    console.error("Erreur validation vente :", error);
                    Swal.fire('Erreur !', error.message || 'Une erreur est survenue lors de la validation.',
                        'error');
                });
        }

        // Fonction partagée pour populer le reçu (Ticket + A4)
        function populateReceipt(saleObj) {
            if (!saleObj) return;
            const saleItems = saleObj.items || [];
            const reference = saleObj.reference || 'INCONNUE';
            const created_at = saleObj.created_at || new Date().toISOString();
            const now = new Date(created_at);

            const totalAmount = parseFloat(saleObj.total_amount || saleObj.total || 0);
            const amountReceived = parseFloat(saleObj.amount_received || saleObj.received || 0);
            const changeAmount = parseFloat(saleObj.change_amount || saleObj.change || 0);
            const cashierNameVal = (saleObj.user && saleObj.user.name) ? saleObj.user.name : (document.querySelector(
                '.cashier-name')?.textContent || 'Caisse');

            let methodText = 'Espèces';
            if (saleObj.payment_method === 'card') methodText = 'CB / Mobile Money';
            if (saleObj.payment_method === 'credit') methodText = 'Crédit (Dette client)';

            // --- 1. POPULATE TICKET (80mm) ---
            document.getElementById('receipt-ref').textContent = `REF: #${reference}`;
            document.getElementById('receipt-date').textContent = `Date: ${now.toLocaleString('fr-FR')}`;

            const custEl = document.getElementById('receipt-customer');
            if (saleObj.customer) {
                custEl.textContent = `Client: ${saleObj.customer.name}`;
                custEl.style.display = 'block';
            } else {
                custEl.style.display = 'none';
            }

            document.getElementById('receipt-cashier').textContent = `Caissier: ${cashierNameVal}`;

            const receiptItems = document.getElementById('receipt-items');
            receiptItems.innerHTML = saleItems.map(item => {
                const name = (item.product ? item.product.name : (item.name || 'Produit')).toUpperCase();
                const qty = parseFloat(item.quantity || item.qty || 0);
                const unitPrice = parseFloat(item.unit_price || item.price || 0);
                const lineTotal = unitPrice * qty;
                return `
                    <tr>
                        <td style="padding: 5px 0; text-align: left; word-break: break-word;">${name}</td>
                        <td style="text-align: center; padding: 5px 0;">${qty}</td>
                        <td style="text-align: right; padding: 5px 0;">${lineTotal.toLocaleString()}</td>
                    </tr>
                `;
            }).join('');

            document.getElementById('receipt-method').textContent = methodText;
            document.getElementById('receipt-total').textContent = `${totalAmount.toLocaleString()} FCFA`;
            document.getElementById('receipt-received').textContent = `${amountReceived.toLocaleString()} FCFA`;
            document.getElementById('receipt-change').textContent = `${changeAmount.toLocaleString()} FCFA`;

            const qrcodeContainer = document.getElementById('receipt-qrcode');
            qrcodeContainer.innerHTML = '';
            if (typeof QRCode !== 'undefined') {
                try {
                    new QRCode(qrcodeContainer, {
                        text: reference,
                        width: 80,
                        height: 80,
                        colorDark: "#000000",
                        colorLight: "#ffffff",
                        correctLevel: QRCode.CorrectLevel.H
                    });
                } catch (qrErr) {
                    console.error("Erreur génération QR Code:", qrErr);
                }
            }

            // --- 2. POPULATE A4 INVOICE ---
            document.getElementById('receipt-a4-ref').textContent = `#${reference}`;
            document.getElementById('receipt-a4-date').textContent = now.toLocaleString('fr-FR');

            const customerBox = document.getElementById('receipt-a4-customer-box');
            if (saleObj.customer) {
                let custDetails = `<strong>${saleObj.customer.name}</strong>`;
                if (saleObj.customer.phone) custDetails += `<br>Tél : ${saleObj.customer.phone}`;
                if (saleObj.customer.email) custDetails += `<br>Email : ${saleObj.customer.email}`;
                if (saleObj.customer.address) custDetails += `<br>Adresse : ${saleObj.customer.address}`;
                customerBox.innerHTML = custDetails;
            } else {
                customerBox.innerHTML = `<strong>Client de Passage (Anonyme)</strong>`;
            }

            document.getElementById('receipt-a4-cashier').textContent = cashierNameVal;
            document.getElementById('receipt-a4-method').textContent = methodText;

            const receiptA4Items = document.getElementById('receipt-a4-items');
            receiptA4Items.innerHTML = saleItems.map(item => {
                const name = (item.product ? item.product.name : (item.name || 'Produit'));
                const qty = parseFloat(item.quantity || item.qty || 0);
                const unitPrice = parseFloat(item.unit_price || item.price || 0);
                const lineTotal = unitPrice * qty;
                return `
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 12px 10px; font-size: 13px;"><strong>${name}</strong></td>
                        <td style="text-align: center; padding: 12px 10px; font-size: 13px;">${unitPrice.toLocaleString()} FCFA</td>
                        <td style="text-align: center; padding: 12px 10px; font-size: 13px;">${qty}</td>
                        <td style="text-align: right; padding: 12px 10px; font-size: 13px; font-weight: 700;">${lineTotal.toLocaleString()} FCFA</td>
                    </tr>
                `;
            }).join('');

            document.getElementById('receipt-a4-total').textContent = `${totalAmount.toLocaleString()} FCFA`;
            document.getElementById('receipt-a4-received').textContent = `${amountReceived.toLocaleString()} FCFA`;
            document.getElementById('receipt-a4-change').textContent = `${changeAmount.toLocaleString()} FCFA`;
        }

        // Fonction auxiliaire pour formater le reçu et l'imprimer
        function printLocalReceipt(saleObj) {
            populateReceipt(saleObj);
            setTimeout(() => {
                window.print();
            }, 150);
        }

        function viewSaleDetails(sale) {
            let customerHtml = sale.customer ? `<p><b>Client:</b> ${sale.customer.name}</p>` :
                '<p><b>Client:</b> Client de passage (Anonyme)</p>';

            let statusHtml = '';
            let refundButtonHtml = '';

            if (sale.status === 'returned') {
                statusHtml = `
                            <div style="margin-bottom: 15px; padding: 10px; background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; border-radius: 12px; font-weight: 700; text-align: center; font-size: 13.5px;">
                                <i class="fa-solid fa-rotate-left"></i> Vente Annulée / Retournée
                            </div>
                        `;
            } else {
                statusHtml = `
                            <div style="margin-bottom: 15px; padding: 10px; background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; border-radius: 12px; font-weight: 700; text-align: center; font-size: 13.5px;">
                                <i class="fa-solid fa-circle-check"></i> Vente Complétée
                            </div>
                        `;

                refundButtonHtml = `
                            <div style="margin-top: 20px; border-top: 1px dashed var(--border); padding-top: 15px;">
                                <button type="button" class="checkout-btn" style="background: var(--danger); box-shadow: 0 4px 15px rgba(225, 29, 72, 0.2); margin: 0; padding: 12px; width: 100%; border-radius: 12px; font-size: 14px;" onclick="confirmRefund(${sale.id}, '${sale.reference}')">
                                    <i class="fa-solid fa-rotate-left"></i> Annuler la vente & Retourner les articles
                                </button>
                            </div>
                        `;
            }

            let itemsHtml = `
                        <div style="text-align: left; margin-top: 15px; font-family: 'Outfit', sans-serif;">
                            ${statusHtml}
                            <p><b>Référence:</b> #${sale.reference}</p>
                            <p><b>Date:</b> ${new Date(sale.created_at).toLocaleString('fr-FR')}</p>
                            ${customerHtml}
                            <div style="margin: 15px 0; border-top: 1px solid #eee; padding-top: 10px;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                    <thead>
                                        <tr style="border-bottom: 1px solid #eee; text-align: left;">
                                            <th style="padding: 8px 0;">Produit</th>
                                            <th style="padding: 8px 0; text-align: center;">Qté</th>
                                            <th style="padding: 8px 0; text-align: right;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${sale.items.map(item => {
                                            const prodName = item.product ? item.product.name : (item.name || 'Produit supprimé');
                                            const qty = item.quantity || item.qty || 0;
                                            const unitPrice = item.unit_price || item.price || 0;
                                            const lineTotal = unitPrice * qty;
                                            return `
                                                                            <tr style="border-bottom: 1px dotted #eee;">
                                                                                <td style="padding: 8px 0;">${prodName}</td>
                                                                                <td style="padding: 8px 0; text-align: center;">${qty}</td>
                                                                                <td style="padding: 8px 0; text-align: right;">${lineTotal.toLocaleString()} FCFA</td>
                                                                            </tr>
                                                                        `;
                                        }).join('')}
                                    </tbody>
                                    <tfoot>
                                        <tr style="border-top: 2px solid #eee;">
                                            <td colspan="2" style="padding: 10px 0; font-weight: 800;">TOTAL</td>
                                            <td style="padding: 10px 0; font-weight: 800; text-align: right;">${sale.total_amount.toLocaleString()} FCFA</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding: 5px 0; color: #666;">Encaissé</td>
                                            <td style="padding: 5px 0; text-align: right;">${parseFloat(sale.amount_received).toLocaleString()} FCFA</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="padding: 5px 0; font-weight: 700; color: var(--success);">Rendu</td>
                                            <td style="padding: 5px 0; font-weight: 700; text-align: right; color: var(--success);">${parseFloat(sale.change_amount).toLocaleString()} FCFA</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            ${refundButtonHtml}
                        </div>
                    `;

            Swal.fire({
                title: 'Détails de la Vente',
                html: itemsHtml,
                width: '500px',
                showDenyButton: true,
                confirmButtonText: '<i class="fa-solid fa-xmark"></i> Fermer',
                confirmButtonColor: '#64748b',
                denyButtonText: '<i class="fa-solid fa-print"></i> Réimprimer le Ticket',
                denyButtonColor: 'var(--primary)'
            }).then((result) => {
                if (result.isDenied) {
                    reprintReceipt(sale);
                }
            });
        }

        function confirmRefund(saleId, reference) {
            Swal.fire({
                title: 'Confirmer l\'annulation ?',
                text: `Voulez-vous vraiment annuler la vente #${reference} ? Cette action réintégrera tous les articles en stock et annulera la transaction financière.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--danger)',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, annuler la vente',
                cancelButtonText: 'Non, conserver',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`/employee/pos/sales/${saleId}/refund`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => {
                                    throw new Error(err.message || response.statusText);
                                });
                            }
                            return response.json();
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Erreur: ${error.message || error}`);
                        });
                }
            }).then((result) => {
                if (result.isConfirmed && result.value.success) {
                    Swal.fire({
                        title: 'Annulation Réussie !',
                        text: result.value.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                }
            });
        }

        function reprintReceipt(sale) {
            populateReceipt(sale);
            setTimeout(() => {
                window.print();
            }, 150);
        }


        let html5QrCode = null;
        let scannerSessionId = 0;
        let activeCameraStream = null;

        async function stopScannerAsync() {
            if (html5QrCode) {
                const instance = html5QrCode;
                html5QrCode = null;
                try {
                    await instance.stop();
                } catch (e) {
                    try {
                        if (instance.isScanning) await instance.stop();
                    } catch (_) {}
                }

                try {
                    await instance.clear();
                } catch (_) {}
            }

            if (activeCameraStream) {
                activeCameraStream.getTracks().forEach(track => {
                    track.stop();
                    track.enabled = false;
                });
                activeCameraStream = null;
            }

            document.querySelectorAll('video').forEach(v => cleanupVideoStream(v));
        }

        function cleanupVideoStream(video) {
            if (!video) return;
            try {
                if (video.srcObject) {
                    video.srcObject.getTracks().forEach(track => {
                        track.stop();
                        track.enabled = false;
                    });
                    video.srcObject = null;
                }
                video.pause();
                video.src = '';
                video.load();
            } catch (e) {}
        }

        async function startScanner() {
            // Nettoyer toute instance précédente et attendre la libération de la caméra
            await stopScannerAsync();

            const currentSession = ++scannerSessionId;
            let cameraAttemptToken = 0;

            Swal.fire({
                title: '<i class="fa-solid fa-camera" style="color:var(--primary); margin-right:8px;"></i>Scanner un produit',
                html: `
                            <video id="scan-video" style="width:100%; border-radius:10px; background:#111; min-height:260px;" autoplay muted playsinline></video>
                            <canvas id="scan-canvas" style="display:none;"></canvas>
                            <p id="scan-status" style="margin-top:10px; color:var(--text-muted); font-size:13px; text-align:center;">
                                <i class="fa-solid fa-spinner fa-spin"></i> Activation de la caméra...
                            </p>`,
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: '<i class="fa-solid fa-xmark"></i> Fermer',
                cancelButtonColor: '#64748b',
                width: 520,
                didOpen: () => {
                    const statusEl = () => document.getElementById('scan-status');
                    const video = document.getElementById('scan-video');
                    const canvas = document.getElementById('scan-canvas');
                    let scanLoop = null;
                    let mediaStream = null;
                    let lastScannedRef = '';
                    let lastScannedAt = 0;
                    const detectionCooldownMs = 1200;
                    let statusResetTimer = null;

                    const setScanStatus = (html, resetToActive = false) => {
                        if (!statusEl()) return;
                        statusEl().innerHTML = html;

                        if (statusResetTimer) {
                            clearTimeout(statusResetTimer);
                            statusResetTimer = null;
                        }

                        if (resetToActive) {
                            statusResetTimer = setTimeout(() => {
                                if (statusEl()) {
                                    statusEl().innerHTML =
                                        '<i class="fa-solid fa-circle" style="color:#22c55e;font-size:10px;"></i> Caméra active — pointez vers un code-barres';
                                }
                            }, 1000);
                        }
                    };

                    const cleanup = () => {
                        if (scanLoop) {
                            cancelAnimationFrame(scanLoop);
                            scanLoop = null;
                        }
                        if (mediaStream) {
                            mediaStream.getTracks().forEach(t => {
                                t.stop();
                                t.enabled = false;
                            });
                            mediaStream = null;
                        }
                        if (activeCameraStream) {
                            activeCameraStream.getTracks().forEach(t => {
                                t.stop();
                                t.enabled = false;
                            });
                            activeCameraStream = null;
                        }
                        cleanupVideoStream(video);
                    };

                    const onCodeDetected = (decodedText) => {
                        const ref = decodedText.trim().toLowerCase();
                        if (!ref) return;

                        const now = Date.now();
                        if (ref === lastScannedRef && (now - lastScannedAt) < detectionCooldownMs) {
                            return;
                        }
                        lastScannedRef = ref;
                        lastScannedAt = now;

                        const cards = document.querySelectorAll('.product-card');
                        let found = false;
                        cards.forEach(card => {
                            if (card.dataset.ref === ref) {
                                found = true;
                                card.click();
                            }
                        });

                        if (found) {
                            setScanStatus(
                                `<span style="color:#22c55e"><i class="fa-solid fa-check-circle"></i> Produit ajouté (${decodedText})</span>`,
                                true
                            );
                        } else {
                            setScanStatus(
                                `<span style="color:#f59e0b"><i class="fa-solid fa-triangle-exclamation"></i> Produit introuvable : ${decodedText}</span>`,
                                true
                            );
                        }
                    };

                    const startHtml5Scanner = async () => {
                        if (typeof Html5Qrcode === 'undefined') {
                            return false;
                        }

                        const withTimeout = (promise, timeoutMs, timeoutLabel) => {
                            return Promise.race([
                                promise,
                                new Promise((_, reject) => setTimeout(() => reject(
                                        new Error(timeoutLabel)),
                                    timeoutMs))
                            ]);
                        };

                        if (mediaStream) {
                            mediaStream.getTracks().forEach(t => {
                                t.stop();
                                t.enabled = false;
                            });
                            mediaStream = null;
                        }

                        cleanupVideoStream(video);

                        let readerDiv = document.getElementById('reader');
                        if (!readerDiv) {
                            readerDiv = document.createElement('div');
                            readerDiv.id = 'reader';
                            readerDiv.style.cssText =
                                'width:100%;border-radius:10px;overflow:hidden;background:#111;min-height:260px;';
                            video.replaceWith(readerDiv);
                        }

                        try {
                            html5QrCode = new Html5Qrcode('reader');

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

                            await withTimeout(
                                html5QrCode.start(
                                    cameraConfig, {
                                        fps: 12,
                                        qrbox: {
                                            width: 260,
                                            height: 160
                                        }
                                    },
                                    (decodedText) => {
                                        onCodeDetected(decodedText);
                                    },
                                    () => {}
                                ),
                                8000,
                                'StartScannerTimeout'
                            );

                            if (statusEl()) statusEl().innerHTML =
                                '<i class="fa-solid fa-circle" style="color:#22c55e;font-size:10px;"></i> Caméra active — pointez vers un code-barres';
                            return true;
                        } catch (err) {
                            stopScannerAsync().catch(() => {});
                            if (statusEl()) statusEl().innerHTML =
                                `<span style="color:#f59e0b"><i class="fa-solid fa-triangle-exclamation"></i> <strong>Scanner desktop lent/bloqué.</strong><br><small style="color:#94a3b8">Bascule automatique vers le mode caméra...</small></span>`;
                            return false;
                        }
                    };

                    const startScanLoop = () => {
                        if (!window.BarcodeDetector) {
                            // Fallback desktop: html5-qrcode si BarcodeDetector indisponible.
                            if (typeof Html5Qrcode === 'undefined') {
                                if (statusEl()) statusEl().innerHTML =
                                    `<span style="color:var(--danger)"><i class="fa-solid fa-triangle-exclamation"></i> <strong>Scanner indisponible.</strong><br><small style="color:#94a3b8">Le moteur BarcodeDetector est absent et la librairie html5-qrcode n'est pas chargée.</small></span>`;
                                return;
                            }

                            if (mediaStream) {
                                mediaStream.getTracks().forEach(t => {
                                    t.stop();
                                    t.enabled = false;
                                });
                                mediaStream = null;
                            }
                            cleanupVideoStream(video);

                            const readerDiv = document.createElement('div');
                            readerDiv.id = 'reader';
                            readerDiv.style.cssText =
                                'width:100%;border-radius:10px;overflow:hidden;background:#111;min-height:260px;';
                            video.replaceWith(readerDiv);

                            html5QrCode = new Html5Qrcode('reader');
                            html5QrCode.start({
                                    facingMode: 'environment'
                                }, {
                                    fps: 12,
                                    qrbox: {
                                        width: 260,
                                        height: 160
                                    }
                                },
                                (decodedText) => {
                                    onCodeDetected(decodedText);
                                },
                                () => {}
                            ).then(() => {
                                if (statusEl()) statusEl().innerHTML =
                                    '<i class="fa-solid fa-circle" style="color:#22c55e;font-size:10px;"></i> Caméra active — pointez vers un code-barres';
                            }).catch((err) => {
                                if (statusEl()) statusEl().innerHTML =
                                    `<span style="color:var(--danger)"><i class="fa-solid fa-triangle-exclamation"></i> <strong>Échec du démarrage du scanner.</strong><br><small style="color:#94a3b8">${String(err)}</small></span>`;
                            });
                            return;
                        }
                        startCanvasScan();
                    };

                    const startCanvasScan = () => {
                        if (!window.BarcodeDetector) {
                            if (statusEl()) statusEl().innerHTML =
                                `<span style="color:var(--danger)"><i class="fa-solid fa-triangle-exclamation"></i> <strong>BarcodeDetector non supporté.</strong><br><small style="color:#94a3b8">Utilisez Chromium/Electron récent ou activez html5-qrcode.</small></span>`;
                            return;
                        }

                        let detector;
                        try {
                            detector = new BarcodeDetector({
                                formats: ['qr_code', 'ean_13', 'ean_8', 'code_128', 'code_39',
                                    'upc_a', 'upc_e', 'itf', 'pdf417'
                                ]
                            });
                        } catch (e) {
                            if (statusEl()) statusEl().innerHTML =
                                `<span style="color:var(--danger)"><i class="fa-solid fa-triangle-exclamation"></i> <strong>Initialisation scanner impossible.</strong><br><small style="color:#94a3b8">${String(e)}</small></span>`;
                            return;
                        }

                        if (statusEl()) statusEl().innerHTML =
                            '<i class="fa-solid fa-circle" style="color:#22c55e;font-size:10px;"></i> Caméra active — pointez vers un code-barres';
                        const tick = async () => {
                            if (video.readyState === video.HAVE_ENOUGH_DATA) {
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                                canvas.getContext('2d').drawImage(video, 0, 0);
                                try {
                                    const barcodes = await detector.detect(canvas);
                                    if (barcodes.length > 0) {
                                        onCodeDetected(barcodes[0].rawValue);
                                    }
                                } catch (e) {}
                            }
                            scanLoop = requestAnimationFrame(tick);
                        };
                        scanLoop = requestAnimationFrame(tick);
                    };

                    // Tentatives avec contraintes de plus en plus simples
                    const attempts = [{
                            video: {
                                width: {
                                    ideal: 1280
                                },
                                height: {
                                    ideal: 720
                                }
                            }
                        },
                        {
                            video: {
                                width: 640,
                                height: 480
                            }
                        },
                        {
                            video: {
                                width: {
                                    min: 320,
                                    max: 640
                                },
                                height: {
                                    min: 240,
                                    max: 480
                                }
                            }
                        },
                        {
                            video: true
                        } // contrainte minimale — dernier recours
                    ];

                    const getUserMediaWithTimeout = (constraints, timeoutMs = 5000) => {
                        return new Promise((resolve, reject) => {
                            let settled = false;
                            const timer = setTimeout(() => {
                                if (settled) return;
                                settled = true;
                                reject(new Error('TimeoutError'));
                            }, timeoutMs);

                            navigator.mediaDevices.getUserMedia(constraints)
                                .then(stream => {
                                    if (settled) {
                                        // Résolution tardive après timeout: libérer immédiatement la caméra.
                                        stream.getTracks().forEach(t => {
                                            t.stop();
                                            t.enabled = false;
                                        });
                                        return;
                                    }
                                    settled = true;
                                    clearTimeout(timer);
                                    resolve(stream);
                                })
                                .catch(err => {
                                    if (settled) return;
                                    settled = true;
                                    clearTimeout(timer);
                                    reject(err);
                                });
                        });
                    };

                    const tryGetCamera = (index) => {
                        if (index >= attempts.length) {
                            if (statusEl()) statusEl().innerHTML = `
                                        <span style="color:var(--danger)">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                            <strong>Impossible d'accéder à la caméra.</strong><br>
                                            <small style="color:#94a3b8">
                                                La caméra est probablement utilisée par un autre processus.<br>
                                                1. Fermez toute app utilisant la caméra (Teams, Zoom, Skype…)<br>
                                                2. Ouvrez le Gestionnaire des tâches et terminez les processus suspects<br>
                                                3. Redémarrez l'application
                                            </small><br><br>
                                            <button onclick="retryCamera()" style="background:var(--primary);color:#fff;border:none;padding:8px 20px;border-radius:8px;cursor:pointer;font-weight:600;">
                                                <i class="fa-solid fa-rotate-right"></i> Réessayer
                                            </button>
                                        </span>`;
                            return;
                        }

                        const currentToken = ++cameraAttemptToken;
                        if (statusEl()) statusEl().innerHTML =
                            `<i class="fa-solid fa-spinner fa-spin"></i> Activation de la caméra... (tentative ${index + 1}/${attempts.length})`;

                        getUserMediaWithTimeout(attempts[index], 5000)
                            .then(stream => {
                                if (currentSession !== scannerSessionId) {
                                    stream.getTracks().forEach(t => {
                                        t.stop();
                                        t.enabled = false;
                                    });
                                    return;
                                }

                                if (currentToken !== cameraAttemptToken) {
                                    stream.getTracks().forEach(t => {
                                        t.stop();
                                        t.enabled = false;
                                    });
                                    return;
                                }

                                mediaStream = stream;
                                activeCameraStream = stream;

                                let started = false;
                                const startIfReady = () => {
                                    if (started) return;
                                    if (currentSession !== scannerSessionId) return;
                                    if (currentToken !== cameraAttemptToken) return;
                                    started = true;
                                    if (statusEl()) statusEl().innerHTML =
                                        '<i class="fa-solid fa-spinner fa-spin"></i> Caméra détectée, initialisation du scanner...';
                                    video.play().catch(() => {});
                                    startScanLoop();
                                };

                                // Important: binder les handlers AVANT d'affecter srcObject
                                // pour éviter de rater l'événement sur desktop.
                                video.onloadedmetadata = startIfReady;
                                video.oncanplay = startIfReady;
                                video.srcObject = stream;

                                // Filet de sécurité: certains environnements desktop ne déclenchent
                                // pas correctement les événements media, on force après un court délai.
                                setTimeout(startIfReady, 800);

                                video.onerror = () => {
                                    cleanup();
                                    tryGetCamera(index + 1);
                                };
                            })
                            .catch(err => {
                                const msg = String(err && err.message ? err.message : err);
                                if (msg.includes('TimeoutError')) {
                                    cleanupVideoStream(video);
                                    setTimeout(() => tryGetCamera(index + 1), 250);
                                } else if (msg.includes('NotReadableError') || msg.includes(
                                        'Could not start video source')) {
                                    // Attendre 1500ms avec nettoyage complet avant nouvelle tentative
                                    cleanupVideoStream(video);
                                    setTimeout(() => tryGetCamera(index + 1), 1500);
                                } else if (msg.includes('OverconstrainedError')) {
                                    setTimeout(() => tryGetCamera(index + 1), 250);
                                } else if (msg.includes('NotAllowedError')) {
                                    if (statusEl()) statusEl().innerHTML =
                                        `<span style="color:var(--danger)"><i class="fa-solid fa-triangle-exclamation"></i> <strong>Accès caméra refusé.</strong><br><small style="color:#94a3b8">Paramètres Windows → Confidentialité → Caméra → Activez l'accès.</small></span>`;
                                } else if (msg.includes('NotFoundError')) {
                                    if (statusEl()) statusEl().innerHTML =
                                        `<span style="color:var(--danger)"><i class="fa-solid fa-video-slash"></i> <strong>Aucune caméra détectée.</strong></span>`;
                                } else {
                                    setTimeout(() => tryGetCamera(index + 1), 300);
                                }
                            });
                    };

                    window.retryCamera = () => {
                        cameraAttemptToken++;
                        if (statusEl()) statusEl().innerHTML =
                            '<i class="fa-solid fa-spinner fa-spin"></i> Tentative de reconnexion...';
                        setTimeout(() => tryGetCamera(0), 500);
                    };

                    if (typeof Html5Qrcode !== 'undefined') {
                        if (statusEl()) statusEl().innerHTML =
                            '<i class="fa-solid fa-spinner fa-spin"></i> Initialisation du scanner desktop...';

                        window.retryCamera = () => {
                            scannerSessionId++;
                            cameraAttemptToken++;
                            stopScannerAsync().then(() => {
                                if (statusEl()) statusEl().innerHTML =
                                    '<i class="fa-solid fa-spinner fa-spin"></i> Tentative de reconnexion...';
                                setTimeout(() => {
                                    startHtml5Scanner().then((ok) => {
                                        if (!ok) {
                                            tryGetCamera(0);
                                        }
                                    });
                                }, 300);
                            });
                        };

                        startHtml5Scanner().then((ok) => {
                            if (!ok) {
                                tryGetCamera(0);
                            }
                        });
                        return;
                    }

                    tryGetCamera(0);
                },
                willClose: () => {
                    scannerSessionId++;
                    cameraAttemptToken++;
                    stopScannerAsync();
                    const video = document.getElementById('scan-video');
                    if (video) {
                        cleanupVideoStream(video);
                    }
                    if (window.retryCamera) {
                        try {
                            delete window.retryCamera;
                        } catch (e) {
                            window.retryCamera = undefined;
                        }
                    }
                },
                didClose: () => {
                    stopScannerAsync();
                },
                didDestroy: () => {
                    stopScannerAsync();
                }
            });
        }

        function stopScanner() {
            stopScannerAsync();
        }

        // Desktop safety: couper la caméra si la fenêtre est masquée/fermée.
        window.addEventListener('pagehide', () => {
            stopScannerAsync();
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopScannerAsync();
            }
        });

        // Initial Sync
        updateCartUI();

        // Barcode Scanner (USB/Physical) Logic
        let barcodeBuffer = '';
        let lastKeyTime = Date.now();
        let lastScanProcessTime = 0; // Anti-doublon

        document.addEventListener('keydown', (e) => {
            const currentTime = Date.now();

            // Scanner speed detection: characters usually arrive very fast (< 50ms apart)
            // Increased threshold to 150ms for slower systems
            if (currentTime - lastKeyTime > 150) {
                barcodeBuffer = '';
            }

            if (e.key === 'Enter') {
                if (barcodeBuffer.length > 2) {
                    // Vérification du délai de 2 secondes pour l'anti-doublon
                    if (currentTime - lastScanProcessTime < 2000) {
                        console.log("Scan rejeté : trop rapide (anti-doublon)");
                        barcodeBuffer = '';
                        return;
                    }

                    processBarcode(barcodeBuffer);
                    lastScanProcessTime = currentTime;
                    barcodeBuffer = '';

                    // Empêcher la soumission de formulaire si le focus est accidentellement ailleurs
                    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                    }
                }
            } else if (e.key.length === 1) {
                barcodeBuffer += e.key;
            }

            lastKeyTime = currentTime;
        });

        // Fallback: Si le scanner simule juste une saisie dans la barre de recherche
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    const currentTime = Date.now();
                    const value = searchInput.value;

                    if (value.length > 2) {
                        // Vérification du délai de 2 secondes
                        if (currentTime - lastScanProcessTime < 2000) {
                            console.log("Scan manuel rejeté : trop rapide");
                            searchInput.value = '';
                            return;
                        }

                        processBarcode(value);
                        lastScanProcessTime = currentTime;
                        searchInput.value = '';
                        e.preventDefault();
                    }
                }
            });
        }

        function processBarcode(code) {
            const ref = code.trim().toLowerCase();
            const cards = document.querySelectorAll('.product-card');
            let found = false;
            let productName = '';

            cards.forEach(card => {
                if (card.dataset.ref === ref) {
                    found = true;
                    hasBeenScanned = true; // On active le mode filtrage au premier scan réussi
                    productName = card.querySelector('h3').textContent;
                    card.click();

                    // Visual feedback
                    card.style.transition = 'all 0.1s';
                    card.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        card.style.transform = '';
                    }, 100);
                }
            });

            if (found) {
                // Afficher le bouton de complément (PRD-) dès qu'on scanne un produit enrôlé
                document.getElementById('toggle-manual-btn').style.display = 'flex';

                // Appliquer le filtrage via searchProducts
                searchProducts();

                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    background: '#059669',
                    color: '#fff'
                });
                Toast.fire({
                    icon: 'success',
                    title: `Produit ajouté : ${productName}`
                });

                // Indicateur USB visuel
                const usbStatus = document.getElementById('usb-scanner-status');
                if (usbStatus) {
                    usbStatus.style.background = 'var(--accent)';
                    setTimeout(() => {
                        usbStatus.style.background = 'rgba(255,255,255,0.1)';
                    }, 500);
                }
            } else {
                console.log("Code barre non reconnu:", ref);
            }
        }

        // --- SESSION DE CAISSE ---
        function submitOpenSession(event) {
            event.preventDefault();
            const openingBalance = document.getElementById('opening_balance').value;

            Swal.fire({
                title: 'Initialisation de la caisse...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('employee.pos.session.open') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        opening_balance: openingBalance
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Succès!', data.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Erreur!', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Erreur!', 'Une erreur est survenue lors de l\'ouverture de caisse.', 'error');
                });
        }

        function showCloseSessionModal() {
            const expected = {{ $expectedClosingBalance ?? 0 }};

            Swal.fire({
                title: 'Clôture de la caisse',
                html: `
                            <div style="text-align: left; font-size: 14px;">
                                <p style="margin-bottom: 8px;">Fond initial : <b>{{ number_format($activeSession->opening_balance ?? 0, 0, ',', ' ') }} FCFA</b></p>
                                <p style="margin-bottom: 15px;">Ventes enregistrées : <b>{{ number_format(($expectedClosingBalance ?? 0) - ($activeSession->opening_balance ?? 0), 0, ',', ' ') }} FCFA</b></p>
                                <div style="border-top: 1px solid #eee; margin: 15px 0; padding-top: 15px;">
                                    <span style="font-size: 16px; font-weight: bold; color: var(--primary);">Total attendu en caisse : <b>${expected.toLocaleString()} FCFA</b></span>
                                </div>
                                <div style="margin-top: 20px;">
                                    <label for="actual_balance" style="display: block; font-weight: 600; margin-bottom: 8px; font-size: 12px; color: #64748b; text-transform: uppercase;">Montant physique compté (FCFA)</label>
                                    <input type="number" id="actual_balance" class="swal2-input" style="margin: 0; width: 100%; border-radius: 10px; font-size: 18px; font-weight: bold; text-align: center;" placeholder="Entrez le montant compté...">
                                </div>
                            </div>
                        `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirmer la clôture',
                cancelButtonText: 'Annuler',
                confirmButtonColor: '#ef4444',
                preConfirm: () => {
                    const actual = document.getElementById('actual_balance').value;
                    if (!actual || actual < 0) {
                        Swal.showValidationMessage('Veuillez entrer un montant compté valide !');
                        return false;
                    }
                    return actual;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const actualBalance = result.value;

                    Swal.fire({
                        title: 'Clôture en cours...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route('employee.pos.session.close') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                actual_closing_balance: actualBalance
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const session = data.session;
                                const diff = parseFloat(session.difference);
                                let diffText = '';

                                if (diff === 0) {
                                    diffText =
                                        '<span style="color: green; font-weight: bold;">Caisse équilibrée (0 FCFA d\'écart)</span>';
                                } else if (diff > 0) {
                                    diffText =
                                        `<span style="color: blue; font-weight: bold;">Excédent de caisse de +${diff.toLocaleString()} FCFA</span>`;
                                } else {
                                    diffText =
                                        `<span style="color: red; font-weight: bold;">Déficit de caisse de ${diff.toLocaleString()} FCFA</span>`;
                                }

                                Swal.fire({
                                    title: 'Caisse clôturée !',
                                    html: `
                                            <div style="text-align: left; font-size: 14px;">
                                                <p>Attendu : <b>${parseFloat(session.expected_closing_balance).toLocaleString()} FCFA</b></p>
                                                <p>Compté : <b>${parseFloat(session.actual_closing_balance).toLocaleString()} FCFA</b></p>
                                                <p style="margin-top:10px;">Résultat : ${diffText}</p>
                                            </div>
                                        `,
                                    icon: 'success',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: true
                                }).then(() => {
                                    // Soumettre le formulaire de déconnexion globale directement pour rediriger vers le login
                                    const logoutForm = document.createElement('form');
                                    logoutForm.method = 'POST';
                                    logoutForm.action = '{{ route('logout') }}';
                                    logoutForm.style.display = 'none';

                                    const csrfInput = document.createElement('input');
                                    csrfInput.type = 'hidden';
                                    csrfInput.name = '_token';
                                    csrfInput.value = '{{ csrf_token() }}';
                                    logoutForm.appendChild(csrfInput);

                                    const methodInput = document.createElement('input');
                                    methodInput.type = 'hidden';
                                    methodInput.name = '_method';
                                    methodInput.value = 'DELETE';
                                    logoutForm.appendChild(methodInput);

                                    document.body.appendChild(logoutForm);
                                    logoutForm.submit();
                                });
                            } else {
                                Swal.fire('Erreur!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Erreur!', 'Une erreur est survenue lors de la clôture.', 'error');
                        });
                }
            });
        }

        // --- CLIENTELE RAPIDE ---
        function quickAddCustomer() {
            Swal.fire({
                title: 'Enregistrer un nouveau client',
                html: `
                            <div style="text-align: left; font-size: 13px;">
                                <div style="margin-bottom: 12px;">
                                    <label style="display:block; font-weight:600; margin-bottom:5px; color:#64748b;">Nom complet *</label>
                                    <input type="text" id="cust-name" class="swal2-input" style="margin:0; width:100%; border-radius:8px;" placeholder="Ex: Jean Kouadio">
                                </div>
                                <div style="margin-bottom: 12px;">
                                    <label style="display:block; font-weight:600; margin-bottom:5px; color:#64748b;">Numéro de téléphone</label>
                                    <input type="text" id="cust-phone" class="swal2-input" style="margin:0; width:100%; border-radius:8px;" placeholder="Ex: 07 07 07 07 07">
                                </div>
                                <div style="margin-bottom: 12px;">
                                    <label style="display:block; font-weight:600; margin-bottom:5px; color:#64748b;">Adresse e-mail</label>
                                    <input type="email" id="cust-email" class="swal2-input" style="margin:0; width:100%; border-radius:8px;" placeholder="Ex: jean@email.com">
                                </div>
                                <div>
                                    <label style="display:block; font-weight:600; margin-bottom:5px; color:#64748b;">Adresse physique</label>
                                    <textarea id="cust-address" class="swal2-textarea" style="margin:0; width:100%; border-radius:8px; height:60px;" placeholder="Ex: Cocody Riviera"></textarea>
                                </div>
                            </div>
                        `,
                showCancelButton: true,
                confirmButtonText: 'Enregistrer',
                cancelButtonText: 'Annuler',
                confirmButtonColor: 'var(--primary)',
                preConfirm: () => {
                    const name = document.getElementById('cust-name').value;
                    const phone = document.getElementById('cust-phone').value;
                    const email = document.getElementById('cust-email').value;
                    const address = document.getElementById('cust-address').value;

                    if (!name.trim()) {
                        Swal.showValidationMessage('Le nom est requis !');
                        return false;
                    }

                    return {
                        name,
                        phone,
                        email,
                        address
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Enregistrement en cours...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    fetch('{{ route('employee.customers.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(result.value)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const c = data.customer;
                                // Ajouter l'option au select et la sélectionner
                                const select = document.getElementById('cart-customer-id');
                                const option = document.createElement('option');
                                option.value = c.id;
                                option.text = `${c.name} ${c.phone ? '(' + c.phone + ')' : ''}`;
                                select.add(option);
                                select.value = c.id;

                                Swal.fire('Succès!', data.message, 'success');
                            } else {
                                Swal.fire('Erreur!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Erreur!', 'Impossible d\'enregistrer le client.', 'error');
                        });
                }
            });
        }

        // --- SUSPENSION DE PANIER (HELD CARTS) ---
        function getHeldCarts() {
            return JSON.parse(localStorage.getItem('pos_held_carts')) || [];
        }

        function saveHeldCarts(carts) {
            localStorage.setItem('pos_held_carts', JSON.stringify(carts));
            updateHeldCartsCount();
        }

        function updateHeldCartsCount() {
            const count = getHeldCarts().length;
            const countEl = document.getElementById('held-carts-count');
            if (countEl) countEl.textContent = count;
        }

        function holdCurrentCart() {
            if (cart.length === 0) {
                Swal.fire('Oups!', 'Le panier est vide, impossible de le suspendre.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Suspendre le panier',
                input: 'text',
                inputLabel: 'Donner une référence ou le nom du client',
                inputPlaceholder: 'Ex: Client pull rouge, Table 4, etc.',
                showCancelButton: true,
                confirmButtonText: 'Mettre en attente',
                cancelButtonText: 'Annuler',
                confirmButtonColor: 'var(--primary)',
                inputValidator: (value) => {
                    if (!value.trim()) {
                        return 'Vous devez entrer un nom ou une description !';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const name = result.value.trim();
                    const heldCarts = getHeldCarts();

                    heldCarts.push({
                        id: 'HELD-' + Date.now(),
                        name: name,
                        items: cart,
                        customer_id: document.getElementById('cart-customer-id').value,
                        created_at: new Date().toISOString()
                    });

                    saveHeldCarts(heldCarts);

                    // Vider le panier actuel
                    cart = [];
                    saveCart();
                    updateCartUI();

                    // Réinitialiser la sélection de client
                    document.getElementById('cart-customer-id').value = '';

                    Swal.fire('Panier suspendu!', 'Vous pouvez maintenant servir un autre client.', 'success');
                }
            });
        }

        function showHeldCarts() {
            const heldCarts = getHeldCarts();
            if (heldCarts.length === 0) {
                Swal.fire('Information', 'Aucun panier n\'est en attente actuellement.', 'info');
                return;
            }

            let html = `
                        <div style="text-align: left; max-height: 400px; overflow-y: auto;">
                            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                <thead>
                                    <tr style="border-bottom: 2px solid #eee; color: #64748b;">
                                        <th style="padding: 8px 0; text-align: left;">Panier</th>
                                        <th style="padding: 8px 0; text-align: center;">Articles</th>
                                        <th style="padding: 8px 0; text-align: right; width: 150px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;

            heldCarts.forEach((hc, index) => {
                const itemsCount = hc.items.reduce((sum, item) => sum + item.qty, 0);
                const time = new Date(hc.created_at).toLocaleTimeString('fr-FR', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                html += `
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 12px 0;">
                                    <span style="font-weight: bold; color: var(--text);">${hc.name}</span><br>
                                    <small style="color: #94a3b8;">Mis en attente à ${time}</small>
                                </td>
                                <td style="padding: 12px 0; text-align: center; font-weight: 600;">${itemsCount}</td>
                                <td style="padding: 12px 0; text-align: right; white-space: nowrap;">
                                    <button onclick="restoreHeldCart('${hc.id}')" style="background: var(--success); color: white; border: none; border-radius: 6px; padding: 8px 12px; font-weight: bold; cursor: pointer; font-size: 11px; margin-right: 5px; display: inline-flex; align-items: center; gap: 5px;">
                                        <i class="fa-solid fa-arrow-rotate-left"></i> Reprendre
                                    </button>
                                    <button onclick="deleteHeldCart('${hc.id}')" style="background: var(--danger); color: white; border: none; border-radius: 6px; padding: 8px 10px; font-weight: bold; cursor: pointer; font-size: 11px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
            });

            html += `
                                </tbody>
                            </table>
                        </div>
                    `;

            Swal.fire({
                title: 'Paniers en attente',
                html: html,
                width: '550px',
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Fermer',
                cancelButtonColor: '#64748b'
            });
        }

        window.restoreHeldCart = function(id) {
            const heldCarts = getHeldCarts();
            const hcIndex = heldCarts.findIndex(c => c.id === id);

            if (hcIndex === -1) return;
            const hc = heldCarts[hcIndex];

            // Si le panier actuel n'est pas vide, demander confirmation
            if (cart.length > 0) {
                Swal.fire({
                    title: 'Remplacer le panier ?',
                    text: 'Votre panier actuel n\'est pas vide. Voulez-vous le remplacer par le panier suspendu ?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Oui, remplacer',
                    cancelButtonText: 'Non, fusionner',
                    showDenyButton: true,
                    denyButtonText: 'Annuler',
                    confirmButtonColor: 'var(--primary)',
                    cancelButtonColor: 'var(--success)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        loadCartData(hc, heldCarts, hcIndex);
                    } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                        // Fusionner
                        hc.items.forEach(item => {
                            const existing = cart.find(i => i.id === item.id);
                            if (existing) {
                                existing.qty = Math.min(existing.qty + item.qty, existing.stock);
                            } else {
                                cart.push(item);
                            }
                        });

                        // Supprimer de la liste d'attente
                        heldCarts.splice(hcIndex, 1);
                        saveHeldCarts(heldCarts);

                        saveCart();
                        updateCartUI();
                        Swal.fire('Fusionné!', 'Les paniers ont été fusionnés avec succès.', 'success');
                    }
                });
            } else {
                loadCartData(hc, heldCarts, hcIndex);
            }
        };

        function loadCartData(hc, heldCarts, index) {
            cart = hc.items;
            saveCart();
            updateCartUI();

            // Restaurer le client
            const select = document.getElementById('cart-customer-id');
            if (select) {
                select.value = hc.customer_id || '';
            }

            // Supprimer de la liste d'attente
            heldCarts.splice(index, 1);
            saveHeldCarts(heldCarts);

            Swal.fire('Restauré!', `Panier "${hc.name}" rechargé avec succès.`, 'success');
        }

        window.deleteHeldCart = function(id) {
            Swal.fire({
                title: 'Supprimer ce panier suspendu ?',
                text: 'Cette action est irréversible.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: 'var(--danger)',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    const heldCarts = getHeldCarts();
                    const filtered = heldCarts.filter(c => c.id !== id);
                    saveHeldCarts(filtered);

                    // Fermer ou rafraîchir le modal
                    Swal.close();
                    setTimeout(showHeldCarts, 300);
                }
            });
        };

        // Appeler updateHeldCartsCount et masquer le loader
        document.addEventListener('DOMContentLoaded', () => {
            updateHeldCartsCount();
        });

        function submitProfileUpdate(event) {
            event.preventDefault();

            const form = document.getElementById('profile-update-form');
            const formData = new FormData(form);

            // Convertir FormData en objet JSON simple
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            Swal.fire({
                title: 'Mise à jour...',
                text: 'Enregistrement de vos modifications en cours.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('{{ route('employee.profile.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Une erreur est survenue lors de la mise à jour.');
                        });
                    }
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        // Mettre à jour l'affichage dynamique du nom dans la sidebar/avatar
                        const sidebarName = document.getElementById('profile-display-name');
                        if (sidebarName) sidebarName.textContent = result.user.name;

                        const navbarName = document.querySelector('.cashier-name');
                        if (navbarName) navbarName.textContent = result.user.name;

                        // Nettoyer les champs de mot de passe
                        document.getElementById('prof_password').value = '';
                        document.getElementById('prof_password_confirm').value = '';

                        Swal.fire({
                            title: 'Profil mis à jour !',
                            text: result.message,
                            icon: 'success',
                            confirmButtonText: 'Super'
                        });
                    } else {
                        throw new Error(result.message || 'Mise à jour échouée.');
                    }
                })
                .catch(error => {
                    Swal.fire('Erreur !', error.message || error, 'error');
                });
        }
    </script>
@endpush
