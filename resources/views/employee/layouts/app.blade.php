<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Interface Caisse') — SuperMarché Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>
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

            .topbar, .app-content, .swal2-container, .no-print {
                display: none !important;
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
        }

        #receipt-print {
            display: none;
        }
    </style>
    @stack('styles')
</head>

<body>

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

    <!-- Scripts pour SQLite (Electron) / IndexedDB (Navigateur) et Synchronisation Offline -->
    <script>
        // Initialisation de la base de données locale (SQLite sous Electron, IndexedDB sous navigateur standard)
        let isElectron = typeof window.electronAPI !== 'undefined';
        let db;

        if (!isElectron) {
            // Uniquement si on n'est pas sous Electron (fallback navigateur standard)
            try {
                const request = indexedDB.open("supermarche_pos_db", 1);

                request.onupgradeneeded = function (event) {
                    const dbInstance = event.target.result;
                    if (!dbInstance.objectStoreNames.contains("offline_sales")) {
                        dbInstance.createObjectStore("offline_sales", {
                            keyPath: "localId",
                            autoIncrement: true
                        });
                    }
                };

                request.onsuccess = function (event) {
                    db = event.target.result;
                    updateOfflineCountUI().catch(err => console.error(err));
                    checkAndSyncOfflineSales().catch(err => console.error(err));
                };

                request.onerror = function (event) {
                    console.error("Erreur d'initialisation IndexedDB :", event.target.errorCode);
                };
            } catch (e) {
                console.error("Impossible d'initialiser IndexedDB dans ce navigateur :", e);
            }
        } else {
            // Sous Electron, on attend la fin du chargement du DOM pour initialiser l'UI et lancer la synchro
            document.addEventListener('DOMContentLoaded', () => {
                updateOfflineCountUI().catch(err => console.error(err));
                checkAndSyncOfflineSales().catch(err => console.error(err));
            });
        }

        // Enregistrer une vente en local (fallback hors-ligne)
        async function saveOfflineSale(saleData) {
            if (isElectron) {
                try {
                    const result = await window.electronAPI.saveOfflineSale(saleData);
                    await updateOfflineCountUI();
                    return result.localId;
                } catch (err) {
                    console.error("Erreur d'écriture SQLite via Electron :", err);
                    throw err;
                }
            }

            return new Promise((resolve, reject) => {
                try {
                    if (!db) {
                        reject("La base IndexedDB n'est pas disponible.");
                        return;
                    }
                    const transaction = db.transaction(["offline_sales"], "readwrite");
                    const store = transaction.objectStore("offline_sales");
                    const req = store.add(saleData);

                    req.onsuccess = function (e) {
                        updateOfflineCountUI().catch(err => console.error(err));
                        resolve(e.target.result);
                    };

                    req.onerror = function (e) {
                        reject(e.target.error);
                    };
                } catch (ex) {
                    reject(e);
                }
            });
        }

        // Récupérer toutes les ventes en local
        async function getOfflineSales() {
            if (isElectron) {
                try {
                    return await window.electronAPI.getOfflineSales() || [];
                } catch (err) {
                    console.error("Erreur de lecture SQLite via Electron :", err);
                    return [];
                }
            }

            return new Promise((resolve, reject) => {
                try {
                    if (!db) return resolve([]);
                    const transaction = db.transaction(["offline_sales"], "readonly");
                    const store = transaction.objectStore("offline_sales");
                    const req = store.getAll();

                    req.onsuccess = function (e) {
                        resolve(e.target.result || []);
                    };

                    req.onerror = function (e) {
                        reject(e.target.error);
                    };
                } catch (ex) {
                    resolve([]);
                }
            });
        }

        // Supprimer des ventes locales par ID après synchronisation réussie
        async function deleteOfflineSales(localIds) {
            if (isElectron) {
                try {
                    await window.electronAPI.deleteOfflineSales(localIds);
                    await updateOfflineCountUI();
                    return;
                } catch (err) {
                    console.error("Erreur de suppression SQLite via Electron :", err);
                    throw err;
                }
            }

            return new Promise((resolve, reject) => {
                try {
                    if (!db || localIds.length === 0) return resolve();
                    const transaction = db.transaction(["offline_sales"], "readwrite");
                    const store = transaction.objectStore("offline_sales");

                    localIds.forEach(id => {
                        store.delete(id);
                    });

                    transaction.oncomplete = function () {
                        updateOfflineCountUI().catch(err => console.error(err));
                        resolve();
                    };

                    transaction.onerror = function (e) {
                        reject(e.target.error);
                    };
                } catch (ex) {
                    reject(ex);
                }
            });
        }

        // Mettre à jour l'indicateur visuel du nombre de ventes en attente de synchro
        async function updateOfflineCountUI() {
            try {
                const sales = await getOfflineSales();
                const count = Array.isArray(sales) ? sales.length : 0;
                const syncBtn = document.getElementById('btn-manual-sync');
                const countContainer = document.getElementById('sync-pending-count');

                if (countContainer) countContainer.textContent = count;

                if (syncBtn) {
                    if (count > 0) {
                        syncBtn.style.display = 'flex';
                    } else {
                        syncBtn.style.display = 'none';
                    }
                }
            } catch (e) {
                console.error("Erreur de mise à jour UI offline :", e);
            }
        }

        // Synchroniser automatiquement en arrière-plan ou manuel
        let isSyncing = false;
        async function checkAndSyncOfflineSales(isManual = false) {
            if (isSyncing) return;
            if (!navigator.onLine) {
                if (isManual) {
                    Swal.fire("Hors-ligne", "Impossible de synchroniser sans connexion internet.", "warning");
                }
                return;
            }

            try {
                const sales = await getOfflineSales();
                if (sales.length === 0) {
                    if (isManual) {
                        Swal.fire("Synchronisation", "Aucune donnée hors-ligne à synchroniser.", "info");
                    }
                    return;
                }

                isSyncing = true;
                const syncIcon = document.getElementById('sync-icon');
                if (syncIcon) syncIcon.classList.add('fa-spin');

                const response = await fetch('{{ route('employee.pos.sync') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        sales: sales
                    })
                });

                if (!response.ok) {
                    throw new Error("Erreur de synchronisation HTTP");
                }

                const result = await response.json();
                if (result.success && result.syncedLocalIds && result.syncedLocalIds.length > 0) {
                    await deleteOfflineSales(result.syncedLocalIds);
                    if (isManual) {
                        Swal.fire({
                            title: "Succès !",
                            text: `${result.syncedLocalIds.length} vente(s) ont été synchronisées avec la base de données.`,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                }

                if (result.errors && result.errors.length > 0) {
                    console.error("Erreurs pdt la synchro :", result.errors);
                }
            } catch (err) {
                console.error("Erreur lors de la synchronisation automatique :", err);
                if (isManual) {
                    Swal.fire("Erreur", "La synchronisation a échoué. Réessayez plus tard.", "error");
                }
            } finally {
                isSyncing = false;
                const syncIcon = document.getElementById('sync-icon');
                if (syncIcon) syncIcon.classList.remove('fa-spin');
                updateOfflineCountUI();
            }
        }

        // Fonction déclenchée par le bouton de synchronisation manuelle
        function triggerManualSync() {
            checkAndSyncOfflineSales(true);
        }

        // Détection réelle de la connexion : d'abord via navigator.onLine (très rapide), puis via un ping externe uniquement si nécessaire
        async function checkActualConnection() {
            // Si le navigateur lui-même dit qu'on est offline, on s'arrête là
            if (!navigator.onLine) {
                return false;
            }

            try {
                // Pour savoir si la CAISSE a vraiment accès à Internet, on ping un serveur public distant (par ex: Cloudflare)
                // au lieu de pinguer le serveur local localhost/127.0.0.1 qui répondra TOUJOURS même en mode avion.
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 1500);

                // On utilise mode: 'no-cors' pour bypasser les restrictions mais obtenir l'état de la connexion TCP/IP
                await fetch('https://1.1.1.1', {
                    method: 'GET',
                    mode: 'no-cors',
                    cache: 'no-store',
                    signal: controller.signal
                });

                clearTimeout(timeoutId);
                return true;
            } catch (e) {
                // Si l'appel externe échoue (erreur de DNS ou connexion coupée), on est réellement déconnecté d'Internet !
                return false;
            }
        }

        async function updateConnectionPill() {
            const pill = document.getElementById('connection-status-pill');
            const icon = document.getElementById('connection-status-icon');
            const text = document.getElementById('connection-status-text');

            const isReallyOnline = await checkActualConnection();

            if (isReallyOnline) {
                if (pill) {
                    pill.style.background = '#059669';
                    pill.style.borderColor = 'rgba(255,255,255,0.2)';
                }
                if (icon) {
                    icon.className = 'fa-solid fa-wifi';
                }
                if (text) text.textContent = 'EN LIGNE';

                // Lancer une synchronisation dès le retour en ligne
                setTimeout(() => {
                    checkAndSyncOfflineSales().catch(err => console.error(err));
                }, 2000);
            } else {
                if (pill) {
                    pill.style.background = '#dc2626';
                    pill.style.borderColor = 'rgba(255,255,255,0.3)';
                }
                if (icon) {
                    icon.className = 'fa-solid fa-wifi-slash';
                }
                if (text) text.textContent = 'HORS-LIGNE';
            }
        }

        window.addEventListener('online', () => {
            updateConnectionPill();
        });
        window.addEventListener('offline', () => {
            updateConnectionPill();
        });

        // Un intervalle régulier pour faire un ping et actualiser le statut réel
        setInterval(() => {
            updateConnectionPill();
        }, 5000);

        // Initialisation de la pill de connexion
        setTimeout(() => {
            updateConnectionPill();
            const viewCaisse = document.getElementById('view-caisse');
            if (viewCaisse && viewCaisse.style.display !== 'none') {
                const offInd = document.getElementById('nav-offline-indicator');
                if (offInd) offInd.style.display = 'flex';
            }
        }, 1000);

        // Synchronisation automatique toutes les 60 secondes si internet revient
        setInterval(() => {
            if (navigator.onLine) {
                checkAndSyncOfflineSales();
            }
        }, 60000);
    </script>

</body>

</html>