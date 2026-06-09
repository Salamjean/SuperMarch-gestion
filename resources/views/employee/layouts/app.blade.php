<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Interface Caisse') — SuperMarché Pro</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/qrcode.min.js') }}"></script>
    <script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
    <style>
        :root {
            --primary: #004d99;
            --primary-light: #0066cc;
            --secondary: #ffc300;
            --accent: #059669;
            --success: #10b981;
            --danger: #e11d48;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2eaf3;
        }

        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .swal2-input {
            max-width: 90% !important;
            box-sizing: border-box !important;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .topbar-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .scan-btn {
            background: var(--secondary);
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 800;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(255, 195, 0, 0.3);
            transition: all 0.3s;
        }

        .scan-btn:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 195, 0, 0.4);
        }

        /* ── Topbar ── */
        .topbar {
            height: 65px;
            background: var(--primary);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 100;
            border-bottom: 4px solid var(--secondary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .topbar-brand {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .topbar-brand span {
            color: var(--secondary);
        }

        .topbar-brand i {
            background: var(--secondary);
            color: var(--primary);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cashier-info {
            text-align: right;
            color: #fff;
        }

        .cashier-name {
            font-size: 14px;
            font-weight: 700;
        }

        .cashier-role {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 8px 15px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: var(--danger);
            border-color: var(--danger);
        }

        /* ── Main Layout ── */
        .app-content {
            height: calc(100vh - 65px);
            display: grid;
            grid-template-columns: 240px 1fr 380px;
            overflow: hidden;
            background: var(--bg);
        }

        /* ── Sidebar ── */
        .sidebar {
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 25px 0;
            display: flex;
            flex-direction: column;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 25px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 14.5px;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .nav-item:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .nav-item.active {
            background: #f0f7ff;
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .nav-item i {
            width: 20px;
            font-size: 16px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: 20px 25px;
            border-top: 1px solid var(--border);
        }

        .clock {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .date {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── POS Central (Products) ── */
        .pos-center {
            display: flex;
            flex-direction: column;
            padding: 25px;
            background: #f8fafc;
            border-right: 1px solid var(--border);
            height: 100%;
            overflow: hidden;
        }

        .search-area {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            align-items: center;
        }

        .toggle-manual-btn {
            padding: 12px 20px;
            border-radius: 12px;
            border: 1px solid var(--primary);
            background: #fff;
            color: var(--primary);
            font-weight: 600;
            cursor: pointer;
            display: none;
            /* Masqué par défaut */
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .toggle-manual-btn.active {
            background: var(--primary);
            color: #fff;
        }

        .search-wrap {
            flex: 1;
            position: relative;
        }

        .search-wrap i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .search-input {
            width: 100%;
            padding: 15px 15px 15px 48px;
            border-radius: 15px;
            border: 1px solid var(--border);
            background: #fff;
            font-size: 15px;
            font-family: inherit;
            outline: none;
            transition: all 0.2s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 4px 15px rgba(0, 77, 153, 0.1);
        }

        .category-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .cat-badge {
            padding: 10px 20px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s;
        }

        .cat-badge.active {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
            overflow-y: auto;
            padding-right: 5px;
            scrollbar-gutter: stable;
            align-content: start;
            /* Évite l'étirement vertical des cartes */
            flex: 1;
            min-height: 0;
        }

        .product-card {
            background: #fff;
            border-radius: 18px;
            padding: 12px;
            border: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: auto;
            /* Laisse la hauteur s'adapter au contenu */
            min-height: 175px;
            position: relative;
        }

        .product-card.selected {
            border-color: var(--primary);
            background: #f0f7ff;
            box-shadow: 0 5px 15px rgba(0, 77, 153, 0.08);
        }

        .selected-indicator {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 24px;
            height: 24px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .product-card.selected .selected-indicator {
            display: flex;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            border-color: var(--primary);
        }

        .product-img {
            width: 100%;
            height: 110px;
            /* Hauteur fixe pour l'image */
            background: #f1f5f9;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-info {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-top: 5px;
        }

        .product-info h3 {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--text);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.2;
            flex: 1;
        }

        .product-price {
            font-size: 14px;
            font-weight: 800;
            color: var(--primary);
            white-space: nowrap;
        }

        .pos-right {
            width: 380px;
            min-width: 380px;
            max-width: 380px;
            background: #fff;
            display: flex;
            flex-direction: column;
            box-shadow: -5px 0 20px rgba(0, 0, 0, 0.02);
            height: 100%;
            flex-shrink: 0;
            overflow: hidden;
        }

        .cart-header {
            padding: 25px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-header h2 {
            font-size: 18px;
            font-weight: 800;
        }

        .cart-header .count {
            background: #f1f5f9;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 10px 20px;
            scrollbar-gutter: stable;
            min-height: 0;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 0;
            border-bottom: 1px dotted var(--border);
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .item-meta {
            font-size: 12px;
            color: var(--text-muted);
        }

        .item-qty {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            padding: 5px;
            border-radius: 10px;
        }

        .qty-btn {
            width: 26px;
            height: 26px;
            border-radius: 8px;
            border: none;
            background: #fff;
            color: var(--primary);
            cursor: pointer;
            font-size: 14px;
            font-weight: 800;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .qty-val {
            font-size: 14px;
            font-weight: 700;
            min-width: 20px;
            text-align: center;
        }

        .item-price-wrap {
            text-align: right;
            min-width: 100px;
        }

        .item-price {
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
        }

        .item-qty-total {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .remove-item {
            color: #cbd5e1;
            cursor: pointer;
            padding: 5px;
            transition: all 0.2s;
            font-size: 14px;
        }

        .remove-item:hover {
            color: var(--danger);
            transform: scale(1.2);
        }

        .cart-footer {
            padding: 25px;
            background: #f8fafc;
            border-top: 1px solid var(--border);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px dashed var(--border);
            font-size: 22px;
            font-weight: 800;
            color: var(--primary);
        }

        .checkout-btn {
            width: 100%;
            padding: 18px;
            background: var(--accent);
            color: #fff;
            border: none;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 20px;
            box-shadow: 0 10px 25px rgba(5, 150, 105, 0.2);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .checkout-btn:hover {
            background: #047857;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(5, 150, 105, 0.3);
        }

        .empty-cart {
            height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            text-align: center;
            gap: 15px;
        }

        .empty-cart i {
            font-size: 40px;
            opacity: 0.3;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ── Print Styles (Receipt) ── */
        @media print {
            body {
                background: white !important;
                color: black !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
            }

            .topbar,
            .app-content,
            .swal2-container,
            .no-print {
                display: none !important;
            }

            @if (($storeSettings->invoice_format ?? 'ticket') === 'ticket')
                @page {
                    size: 80mm auto;
                    margin: 0;
                }
                html, body {
                    width: 80mm !important;
                }
                #receipt-print {
                    display: block !important;
                    width: 80mm;
                    padding: 5mm;
                    background: white !important;
                    color: black !important;
                    font-family: Arial, Helvetica, sans-serif !important;
                    font-weight: normal !important;
                }
                #receipt-print-a4 {
                    display: none !important;
                }
            @else
                @page {
                    size: A4;
                    margin: 15mm;
                }
                html, body {
                    width: auto !important;
                }
                #receipt-print-a4 {
                    display: block !important;
                    width: 100% !important;
                    background: white !important;
                    color: black !important;
                    font-family: 'Inter', sans-serif !important;
                }
                #receipt-print {
                    display: none !important;
                }
            @endif
        }

        #receipt-print,
        #receipt-print-a4 {
            display: none;
        }
    </style>
    @stack('styles')
</head>

<body class="print-format-{{ $storeSettings->invoice_format ?? 'ticket' }}">

    <!-- Topbar -->
    @include('employee.layouts.navbar')

    <div class="app-content">
        <!-- Sidebar -->
        @include('employee.layouts.sidebar')

        <!-- Main Content -->
        @yield('content')
    </div>

    @stack('print')
    @stack('scripts')

    <script>
        // ════════════════════════════════════════════════════════════════════════
        //  SYNC MANAGER — SQLite ↔ MySQL
        //  Détecte si on est sous Electron, gère le mode offline complet
        //  et synchronise au retour de connexion.
        // ════════════════════════════════════════════════════════════════════════

        const isElectron = typeof window.electronAPI !== 'undefined';
        const BASE_URL    = window.location.origin; // http://127.0.0.1:8000 ou https://fescads.com

        // ── Détection de connexion ─────────────────────────────────────────────

        async function checkActualConnection() {
            if (!navigator.onLine) return false;
            try {
                const controller = new AbortController();
                const timeoutId  = setTimeout(() => controller.abort(), 1500);
                await fetch('https://1.1.1.1', { method: 'GET', mode: 'no-cors', cache: 'no-store', signal: controller.signal });
                clearTimeout(timeoutId);
                return true;
            } catch (e) {
                return false;
            }
        }

        let _isOnline = true; // État courant (mis à jour par updateConnectionPill)

        async function updateConnectionPill() {
            const pill = document.getElementById('connection-status-pill');
            const icon = document.getElementById('connection-status-icon');
            const text = document.getElementById('connection-status-text');

            const wasOnline = _isOnline;
            _isOnline = await checkActualConnection();

            if (_isOnline) {
                if (pill) { pill.style.background = '#059669'; pill.style.borderColor = 'rgba(255,255,255,0.2)'; }
                if (icon) icon.className = 'fa-solid fa-wifi';
                if (text) text.textContent = 'EN LIGNE';
                // Lancer une sync au retour en ligne
                if (!wasOnline) {
                    console.log('🌐 Connexion rétablie → synchronisation automatique...');
                    setTimeout(() => triggerManualSync(false), 2000);
                }
            } else {
                if (pill) { pill.style.background = '#dc2626'; pill.style.borderColor = 'rgba(255,255,255,0.3)'; }
                if (icon) icon.className = 'fa-solid fa-wifi-slash';
                if (text) text.textContent = 'HORS-LIGNE';
            }

            // Afficher/cacher le bouton sync selon le nb d'opérations en attente
            await updatePendingCount();
        }

        // ── Compteur d'opérations en attente ──────────────────────────────────

        async function updatePendingCount() {
            try {
                let count = 0;
                if (isElectron) {
                    count = await window.electronAPI.invoke('sqlite-pending-count') || 0;
                } else {
                    // Fallback IndexedDB
                    const sales = await getOfflineSales();
                    count = Array.isArray(sales) ? sales.length : 0;
                }
                const countEl  = document.getElementById('sync-pending-count');
                const syncBtn  = document.getElementById('btn-manual-sync');
                if (countEl) countEl.textContent = count;
                if (syncBtn) syncBtn.style.display = (count > 0 && _isOnline) ? 'flex' : 'none';
            } catch (e) {
                console.error('updatePendingCount error:', e);
            }
        }

        // Alias pour compatibilité avec l'ancien code
        async function updateOfflineCountUI() { await updatePendingCount(); }

        // ── Pull : télécharger MySQL → SQLite ─────────────────────────────────

        async function pullFromServer() {
            if (!isElectron) return;
            try {
                console.log('📥 Pull depuis le serveur...');
                const result = await window.electronAPI.invoke('sqlite-pull', {
                    baseUrl: BASE_URL,
                    sessionCookie: document.cookie
                });
                if (result.success) {
                    console.log('✅ Pull réussi :', result.stats);
                } else {
                    console.warn('⚠️ Pull échoué :', result.message);
                }
                return result;
            } catch (e) {
                console.error('Erreur pull:', e);
            }
        }

        // ── Push : SQLite → MySQL ─────────────────────────────────────────────

        let isSyncing = false;

        async function checkAndSyncOfflineSales(isManual = false) {
            if (isSyncing) return;
            if (!_isOnline) {
                if (isManual) Swal.fire('Hors-ligne', 'Impossible de synchroniser sans connexion internet.', 'warning');
                return;
            }

            isSyncing = true;
            const syncIcon = document.getElementById('sync-icon');
            if (syncIcon) syncIcon.classList.add('fa-spin');

            try {
                let result;

                if (isElectron) {
                    // ── Nouveau chemin via IPC sqlite-push ────────────────────
                    result = await window.electronAPI.invoke('sqlite-push', {
                        baseUrl: BASE_URL,
                        sessionCookie: document.cookie
                    });

                    if (result.success) {
                        if (result.synced > 0 && isManual) {
                            Swal.fire({
                                title: 'Synchronisation réussie !',
                                text: `${result.synced} opération(s) ont été envoyées au serveur.`,
                                icon: 'success', timer: 2500, showConfirmButton: false
                            });
                        } else if (result.synced === 0 && isManual) {
                            Swal.fire('Synchronisation', 'Aucune donnée hors-ligne à synchroniser.', 'info');
                        }
                    } else {
                        throw new Error(result.message || 'Erreur inconnue');
                    }
                } else {
                    // ── Ancien chemin via IndexedDB → route Laravel pos.sync ──
                    const sales = await getOfflineSales();
                    if (sales.length === 0) {
                        if (isManual) Swal.fire('Synchronisation', 'Aucune donnée hors-ligne à synchroniser.', 'info');
                        return;
                    }

                    const response = await fetch('{{ route('employee.pos.sync') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ sales })
                    });

                    if (!response.ok) throw new Error('Erreur HTTP ' + response.status);
                    result = await response.json();

                    if (result.success && result.syncedLocalIds?.length > 0) {
                        await deleteOfflineSales(result.syncedLocalIds);
                        if (isManual) {
                            Swal.fire({
                                title: 'Succès !',
                                text: `${result.syncedLocalIds.length} vente(s) synchronisées.`,
                                icon: 'success', timer: 2000, showConfirmButton: false
                            });
                        }
                    }
                }

                if (result?.errors?.length > 0) console.error('Erreurs sync:', result.errors);

            } catch (err) {
                console.error('Erreur synchronisation:', err);
                if (isManual) Swal.fire('Erreur', 'La synchronisation a échoué. Réessayez plus tard.', 'error');
            } finally {
                isSyncing = false;
                if (syncIcon) syncIcon.classList.remove('fa-spin');
                await updatePendingCount();
            }
        }

        function triggerManualSync(showFeedback = true) {
            checkAndSyncOfflineSales(showFeedback);
        }

        // ── Fallback IndexedDB (navigateur standard sans Electron) ────────────

        let _idb;
        if (!isElectron) {
            try {
                const req = indexedDB.open('supermarche_pos_db', 1);
                req.onupgradeneeded = (e) => {
                    const d = e.target.result;
                    if (!d.objectStoreNames.contains('offline_sales')) {
                        d.createObjectStore('offline_sales', { keyPath: 'localId', autoIncrement: true });
                    }
                };
                req.onsuccess = (e) => {
                    _idb = e.target.result;
                    updateOfflineCountUI();
                    checkAndSyncOfflineSales();
                };
            } catch (e) { console.error('IndexedDB init failed:', e); }
        }

        async function saveOfflineSale(saleData) {
            if (isElectron) {
                const result = await window.electronAPI.invoke('sqlite-create-sale', saleData);
                await updatePendingCount();
                return result;
            }
            return new Promise((resolve, reject) => {
                if (!_idb) return reject('IndexedDB non disponible');
                const tx  = _idb.transaction(['offline_sales'], 'readwrite');
                const req = tx.objectStore('offline_sales').add(saleData);
                req.onsuccess = (e) => { updateOfflineCountUI(); resolve({ success: true, localId: e.target.result }); };
                req.onerror   = (e) => reject(e.target.error);
            });
        }

        async function getOfflineSales() {
            if (isElectron) return await window.electronAPI.invoke('get-offline-sales') || [];
            return new Promise((resolve) => {
                if (!_idb) return resolve([]);
                const req = _idb.transaction(['offline_sales'], 'readonly').objectStore('offline_sales').getAll();
                req.onsuccess = (e) => resolve(e.target.result || []);
                req.onerror   = () => resolve([]);
            });
        }

        async function deleteOfflineSales(localIds) {
            if (isElectron) { await window.electronAPI.invoke('delete-offline-sales', localIds); await updatePendingCount(); return; }
            return new Promise((resolve) => {
                if (!_idb || !localIds.length) return resolve();
                const tx = _idb.transaction(['offline_sales'], 'readwrite');
                const st = tx.objectStore('offline_sales');
                localIds.forEach(id => st.delete(id));
                tx.oncomplete = () => { updateOfflineCountUI(); resolve(); };
            });
        }

        // ── Checkout unifié (en-ligne OU hors-ligne) ──────────────────────────
        // Cette fonction est appelée depuis le dashboard POS à la place du fetch direct.
        // Elle détecte la connexion et choisit le bon chemin.

        window.unifiedCheckout = async function(salePayload, activeSessionId, userId) {
            if (_isOnline) {
                // ── Mode EN LIGNE : appel Laravel normal ──
                const response = await fetch('{{ route('employee.pos.checkout') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(salePayload)
                });
                if (!response.ok) {
                    const err = await response.json().catch(() => ({ message: 'Erreur réseau' }));
                    throw new Error(err.message || 'Erreur HTTP ' + response.status);
                }
                return await response.json();
            } else {
                // ── Mode HORS-LIGNE : IPC SQLite ──
                if (!isElectron) throw new Error('Mode hors-ligne uniquement disponible dans l\'application desktop.');

                const result = await window.electronAPI.invoke('sqlite-create-sale', {
                    userId:         userId,
                    cashSessionId:  activeSessionId,
                    customerId:     salePayload.customer_id || null,
                    totalAmount:    salePayload.total_amount,
                    amountReceived: salePayload.amount_received,
                    changeAmount:   salePayload.change_amount,
                    paymentMethod:  salePayload.payment_method || 'cash',
                    items:          (salePayload.items || []).map(i => ({ id: i.id, qty: i.qty, price: i.price })),
                    createdAt:      new Date().toISOString()
                });

                await updatePendingCount();
                return result;
            }
        };

        // ── Initialisation ────────────────────────────────────────────────────

        window.addEventListener('online',  () => updateConnectionPill());
        window.addEventListener('offline', () => updateConnectionPill());
        setInterval(() => updateConnectionPill(), 10000);  // Ping toutes les 10s
        setInterval(() => { if (_isOnline) checkAndSyncOfflineSales(); }, 60000); // Auto-sync toutes les 60s

        document.addEventListener('DOMContentLoaded', async () => {
            await updateConnectionPill();

            // Afficher l'indicateur de connexion dans la navbar
            const offInd = document.getElementById('nav-offline-indicator');
            if (offInd) offInd.style.display = 'flex';

            // Pull initial si on est en ligne ou sur le serveur local de dev (recharger les données fraîches)
            const isLocalServer = BASE_URL.includes('127.0.0.1') || BASE_URL.includes('localhost');
            if (isElectron && (_isOnline || isLocalServer)) {
                console.log('🚀 Pull initial des données au démarrage...');
                await pullFromServer();
            }
        });
    </script>

</body>

</html>