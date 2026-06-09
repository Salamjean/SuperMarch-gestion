import { app, BrowserWindow, shell, session, ipcMain } from "electron";
import path from "path";
import fs from "fs";
import { fileURLToPath } from "url";
import { DatabaseSync } from "node:sqlite";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// ─── Configuration ────────────────────────────────────────────────────────────
const REMOTE_BASE_URL = "https://fescads.com";
const LOCAL_DEV_URL   = "http://127.0.0.1:8000";
// Clé API partagée avec Laravel pour sécuriser /api/sync/*
const SYNC_API_KEY    = "supermarche-sync-secret-2026";

// ─── Permissions caméra / micro ───────────────────────────────────────────────
function setupPermissions() {
    session.defaultSession.setPermissionRequestHandler(
        (webContents, permission, callback) => {
            const allowed = ["media", "camera", "microphone", "videoCapture", "audioCapture"];
            callback(allowed.includes(permission));
        },
    );
    session.defaultSession.setPermissionCheckHandler(
        (webContents, permission) => {
            const allowed = ["media", "camera", "microphone", "videoCapture", "audioCapture"];
            return allowed.includes(permission);
        },
    );
    session.defaultSession.setDevicePermissionHandler((details) => {
        if (details.deviceType === "hid" || details.deviceType === "serial") return false;
        return true;
    });
}

// ─── Fenêtre principale ────────────────────────────────────────────────────────
function createWindow() {
    const win = new BrowserWindow({
        width: 1200,
        height: 800,
        show: false,
        title: "SuperMarché Pro",
        backgroundColor: "#0b0f19", // Évite le flash blanc initial au démarrage
        webPreferences: {
            contextIsolation: true,
            nodeIntegration: false,
            webSecurity: false,
            allowRunningInsecureContent: true,
            preload: path.join(__dirname, "preload.js"),
        },
    });

    win.maximize();
    win.show();
    win.setMenuBarVisibility(false);

    // Charger immédiatement le splash screen local (affichage instantané)
    win.loadFile(path.join(__dirname, "public", "loading.html"));

    // Vérification asynchrone de la connexion après l'affichage du splash screen
    setTimeout(async () => {
        try {
            // Test de ping rapide vers la route API du serveur en ligne fescads.com
            const onlineCheck = await fetch(`${REMOTE_BASE_URL}/api/sync/ping`);
            if (onlineCheck.ok) {
                console.log("Connexion internet active. Chargement de fescads.com...");
                win.loadURL(REMOTE_BASE_URL);
                return;
            }
        } catch (e) {
            console.log("Erreur ping distant au démarrage (mode offline probable) :", e.message);
        }

        // Si hors-ligne, attendre que le serveur local réponde (jusqu'à 15 secondes)
        console.log("Tentative de redirection vers le serveur local...");
        for (let attempt = 1; attempt <= 30; attempt++) {
            try {
                const response = await fetch(LOCAL_DEV_URL);
                if (response) {
                    console.log(`Serveur local détecté (tentative ${attempt}). Redirection vers`, LOCAL_DEV_URL);
                    win.loadURL(LOCAL_DEV_URL);
                    return;
                }
            } catch (err) {
                // Attendre 500ms avant la prochaine tentative
                await new Promise(resolve => setTimeout(resolve, 500));
            }
        }

        // Si le serveur local n'est pas lancé, afficher la page hors-ligne
        win.loadFile(path.join(__dirname, "public", "offline.html"));
    }, 2500);

    // 🌐 Gestion du mode hors connexion
    win.webContents.on(
        "did-fail-load",
        async (event, errorCode, errorDescription, validatedURL, isMainFrame) => {
            // Uniquement si c'est la page principale (pas un asset comme une image ou un script)
            if (isMainFrame && (validatedURL.startsWith(REMOTE_BASE_URL) || validatedURL.startsWith(LOCAL_DEV_URL))) {
                console.log(`Échec du chargement de la page (${validatedURL}) :`, errorDescription);

                if (validatedURL.startsWith(REMOTE_BASE_URL)) {
                    console.log("Échec du serveur en ligne. Chargement du splash screen local d'attente...");
                    win.loadFile(path.join(__dirname, "public", "loading.html"));

                    // Boucle de vérification du serveur local (jusqu'à 30 tentatives = 15 secondes)
                    for (let attempt = 1; attempt <= 30; attempt++) {
                        try {
                            const response = await fetch(LOCAL_DEV_URL);
                            if (response) {
                                console.log(`Serveur local détecté (tentative ${attempt}). Redirection vers`, LOCAL_DEV_URL);
                                win.loadURL(LOCAL_DEV_URL);
                                return;
                            }
                        } catch (err) {
                            // Attendre 500ms avant la prochaine tentative
                            await new Promise(resolve => setTimeout(resolve, 500));
                        }
                    }
                }

                console.log("Le serveur local n'a pas répondu ou a échoué. Chargement de la page hors-ligne.");
                win.loadFile(path.join(__dirname, "public", "offline.html"));
            }
        }
    );

    // 🔥 Liens externes dans le navigateur
    win.webContents.setWindowOpenHandler(({ url }) => {
        shell.openExternal(url);
        return { action: "deny" };
    });

    // 🖨️ Impression silencieuse des reçus
    const activePrintWindows = new Set();
    ipcMain.on("print-receipt", (event, htmlContent) => {
        let printWindow = new BrowserWindow({
            show: false,
            webPreferences: { nodeIntegration: false, contextIsolation: true },
        });
        activePrintWindows.add(printWindow);
        printWindow.loadURL(`data:text/html;charset=utf-8,${encodeURIComponent(htmlContent)}`);
        printWindow.webContents.on("did-finish-load", () => {
            setTimeout(() => {
                printWindow.webContents.print(
                    { silent: true, printBackground: true, margins: { marginType: "none" } },
                    (success, failureReason) => {
                        if (!success) {
                            console.error("Erreur d'impression silencieuse :", failureReason);
                            event.reply("print-error", failureReason);
                        }
                        printWindow.close();
                        activePrintWindows.delete(printWindow);
                    },
                );
            }, 300);
        });
    });
}

// ─── Base de données SQLite locale ────────────────────────────────────────────
let db;

function initDatabase() {
    try {
        let dbPath;
        if (app.isPackaged) {
            const userDataPath = app.getPath("userData");
            const dbDir = path.join(userDataPath, "database");
            if (!fs.existsSync(dbDir)) {
                fs.mkdirSync(dbDir, { recursive: true });
            }
            dbPath = path.join(dbDir, "database_local.sqlite");
        } else {
            dbPath = path.join(__dirname, "database", "database_local.sqlite");
        }
        console.log("SQLite locale initialisée :", dbPath);
        db = new DatabaseSync(dbPath);

        // Activer les clés étrangères
        db.exec("PRAGMA foreign_keys = ON;");

        // ─── Création de toutes les tables (miroir MySQL) ──────────────────────

        db.exec(`
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                role TEXT DEFAULT 'employee',
                phone TEXT,
                address TEXT,
                gender TEXT,
                login_code TEXT,
                is_blocked INTEGER DEFAULT 0,
                deleted_at TEXT,
                created_at TEXT,
                updated_at TEXT
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                slug TEXT UNIQUE,
                description TEXT,
                color TEXT DEFAULT '#004d99',
                is_active INTEGER DEFAULT 1,
                created_by INTEGER,
                created_at TEXT,
                updated_at TEXT
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS suppliers (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                email TEXT,
                phone TEXT,
                contact_person TEXT,
                address TEXT,
                city TEXT,
                website TEXT,
                is_active INTEGER DEFAULT 1,
                notes TEXT,
                created_at TEXT,
                updated_at TEXT
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                slug TEXT UNIQUE,
                reference TEXT UNIQUE,
                qr_code TEXT,
                category_name TEXT,
                supplier_id INTEGER,
                price REAL DEFAULT 0,
                stock INTEGER DEFAULT 0,
                stock_threshold INTEGER DEFAULT 5,
                image TEXT,
                description TEXT,
                is_active INTEGER DEFAULT 1,
                created_by INTEGER,
                created_at TEXT,
                updated_at TEXT
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS customers (
                id INTEGER PRIMARY KEY,
                name TEXT NOT NULL,
                phone TEXT,
                email TEXT,
                address TEXT,
                loyalty_points INTEGER DEFAULT 0,
                debt_balance REAL DEFAULT 0,
                is_credit_blocked INTEGER DEFAULT 0,
                created_at TEXT,
                updated_at TEXT
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS settings (
                id INTEGER PRIMARY KEY,
                store_name TEXT DEFAULT 'SUPERMARCHÉ PRO',
                phone TEXT DEFAULT '+225 07 00 00 00 00',
                address TEXT DEFAULT 'Abidjan, Cocody Riviera Palmeraie',
                email TEXT,
                invoice_footer TEXT,
                invoice_format TEXT DEFAULT 'ticket',
                created_at TEXT,
                updated_at TEXT
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS cash_sessions (
                id INTEGER PRIMARY KEY,
                user_id INTEGER NOT NULL,
                opening_balance REAL NOT NULL,
                expected_closing_balance REAL,
                actual_closing_balance REAL,
                difference REAL,
                opened_at TEXT,
                closed_at TEXT,
                status TEXT DEFAULT 'open',
                created_at TEXT,
                updated_at TEXT,
                synced INTEGER DEFAULT 0
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS sales (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                local_id INTEGER,
                user_id INTEGER NOT NULL,
                cash_session_id INTEGER,
                customer_id INTEGER,
                total_amount REAL NOT NULL,
                amount_received REAL,
                change_amount REAL,
                payment_method TEXT DEFAULT 'cash',
                reference TEXT UNIQUE,
                status TEXT DEFAULT 'completed',
                refunded_amount REAL DEFAULT 0,
                created_at TEXT,
                updated_at TEXT,
                synced INTEGER DEFAULT 0
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS sale_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                sale_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL,
                unit_price REAL NOT NULL,
                subtotal REAL NOT NULL,
                returned_quantity INTEGER DEFAULT 0,
                created_at TEXT,
                updated_at TEXT
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS debt_payments (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                customer_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                cash_session_id INTEGER,
                amount REAL NOT NULL,
                reference TEXT UNIQUE,
                payment_method TEXT DEFAULT 'cash',
                created_at TEXT,
                updated_at TEXT,
                synced INTEGER DEFAULT 0
            );
        `);

        db.exec(`
            CREATE TABLE IF NOT EXISTS restock_requests (
                id INTEGER PRIMARY KEY,
                product_id INTEGER,
                user_id INTEGER,
                status TEXT DEFAULT 'pending',
                quantity_requested INTEGER,
                quantity_received INTEGER DEFAULT 0,
                created_at TEXT,
                updated_at TEXT,
                synced INTEGER DEFAULT 0
            );
        `);

        // Table de tracking pour les opérations hors-ligne à synchroniser
        db.exec(`
            CREATE TABLE IF NOT EXISTS sync_queue (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                entity_type TEXT NOT NULL,
                entity_local_id INTEGER NOT NULL,
                operation TEXT NOT NULL,
                payload TEXT NOT NULL,
                created_at TEXT DEFAULT (datetime('now')),
                synced INTEGER DEFAULT 0,
                sync_error TEXT
            );
        `);

        console.log("✅ Toutes les tables SQLite initialisées.");

        // ─── Enregistrement des handlers IPC ──────────────────────────────────
        registerIpcHandlers();

    } catch (e) {
        console.error("Erreur d'initialisation SQLite :", e);
    }
}

// ─── Helpers ──────────────────────────────────────────────────────────────────
function now() {
    return new Date().toISOString().replace("T", " ").substring(0, 19);
}

function generateRef(prefix) {
    return prefix + "-" + Math.random().toString(36).substring(2, 10).toUpperCase();
}

// ─── Handlers IPC ─────────────────────────────────────────────────────────────
function registerIpcHandlers() {

    // ── Lecture des données de référence ──────────────────────────────────────

    ipcMain.handle("sqlite-get-products", async () => {
        try {
            return db.prepare("SELECT * FROM products WHERE is_active = 1").all();
        } catch (e) { console.error(e); return []; }
    });

    ipcMain.handle("sqlite-get-all-products", async () => {
        try {
            return db.prepare("SELECT * FROM products WHERE is_active = 1").all();
        } catch (e) { console.error(e); return []; }
    });

    ipcMain.handle("sqlite-get-categories", async () => {
        try {
            return db.prepare("SELECT * FROM categories WHERE is_active = 1").all();
        } catch (e) { console.error(e); return []; }
    });

    ipcMain.handle("sqlite-get-customers", async () => {
        try {
            return db.prepare("SELECT * FROM customers ORDER BY name").all();
        } catch (e) { console.error(e); return []; }
    });

    ipcMain.handle("sqlite-search-customers", async (event, query) => {
        try {
            const q = `%${query}%`;
            return db.prepare("SELECT * FROM customers WHERE name LIKE ? OR phone LIKE ? ORDER BY name LIMIT 10").all(q, q);
        } catch (e) { console.error(e); return []; }
    });

    ipcMain.handle("sqlite-get-settings", async () => {
        try {
            return db.prepare("SELECT * FROM settings LIMIT 1").get();
        } catch (e) { console.error(e); return null; }
    });

    ipcMain.handle("sqlite-get-user", async (event, userId) => {
        try {
            return db.prepare("SELECT id, name, email, role, phone, address, gender FROM users WHERE id = ?").get(userId);
        } catch (e) { console.error(e); return null; }
    });

    // ── Session de caisse ──────────────────────────────────────────────────────

    ipcMain.handle("sqlite-get-active-session", async (event, userId) => {
        try {
            return db.prepare("SELECT * FROM cash_sessions WHERE user_id = ? AND status = 'open' LIMIT 1").get(userId ?? null);
        } catch (e) { console.error(e); return null; }
    });

    ipcMain.handle("sqlite-open-session", async (event, { userId, openingBalance }) => {
        try {
            const existing = db.prepare("SELECT id FROM cash_sessions WHERE user_id = ? AND status = 'open'").get(userId ?? null);
            if (existing) return { success: false, message: "Une session de caisse est déjà active.", session: existing };

            const ts = now();
            const stmt = db.prepare(`
                INSERT INTO cash_sessions (user_id, opening_balance, opened_at, status, created_at, updated_at, synced)
                VALUES (?, ?, ?, 'open', ?, ?, 0)
            `);
            const res = stmt.run(userId ?? null, openingBalance, ts, ts, ts);
            const session = db.prepare("SELECT * FROM cash_sessions WHERE id = ?").get(Number(res.lastInsertRowid));

            // Ajouter dans la sync_queue
            db.prepare("INSERT INTO sync_queue (entity_type, entity_local_id, operation, payload, created_at) VALUES (?,?,?,?,?)")
              .run("cash_session", Number(res.lastInsertRowid), "create", JSON.stringify(session), ts);

            return { success: true, message: "Caisse ouverte avec succès.", session };
        } catch (e) { console.error(e); return { success: false, message: e.message }; }
    });

    ipcMain.handle("sqlite-close-session", async (event, { userId, actualClosingBalance }) => {
        try {
            const session = db.prepare("SELECT * FROM cash_sessions WHERE user_id = ? AND status = 'open'").get(userId);
            if (!session) return { success: false, message: "Aucune session active à clôturer." };

            const totalSales = db.prepare(
                "SELECT COALESCE(SUM(total_amount),0) as total FROM sales WHERE cash_session_id = ? AND status = 'completed'"
            ).get(session.id).total;

            const totalRepayments = db.prepare(
                "SELECT COALESCE(SUM(amount),0) as total FROM debt_payments WHERE cash_session_id = ?"
            ).get(session.id).total;

            const expected = session.opening_balance + totalSales + totalRepayments;
            const difference = actualClosingBalance - expected;
            const ts = now();

            db.prepare(`
                UPDATE cash_sessions SET
                    expected_closing_balance = ?,
                    actual_closing_balance = ?,
                    difference = ?,
                    closed_at = ?,
                    status = 'closed',
                    updated_at = ?,
                    synced = 0
                WHERE id = ?
            `).run(expected, actualClosingBalance, difference, ts, ts, session.id);

            const updated = db.prepare("SELECT * FROM cash_sessions WHERE id = ?").get(session.id);

            // Ajouter dans la sync_queue
            db.prepare("INSERT INTO sync_queue (entity_type, entity_local_id, operation, payload, created_at) VALUES (?,?,?,?,?)")
              .run("cash_session", session.id, "close", JSON.stringify(updated), ts);

            return { success: true, message: "Caisse clôturée avec succès.", session: updated };
        } catch (e) { console.error(e); return { success: false, message: e.message }; }
    });

    // ── Ventes ────────────────────────────────────────────────────────────────

    ipcMain.handle("sqlite-create-sale", async (event, saleData) => {
        try {
            const userId = saleData.userId || saleData.user_id;
            const cashSessionId = saleData.cashSessionId || saleData.cash_session_id;
            const customerId = saleData.customerId || saleData.customer_id;
            const totalAmount = saleData.totalAmount !== undefined ? saleData.totalAmount : saleData.total_amount;
            const amountReceived = saleData.amountReceived !== undefined ? saleData.amountReceived : saleData.amount_received;
            const changeAmount = saleData.changeAmount !== undefined ? saleData.changeAmount : saleData.change_amount;
            const paymentMethod = saleData.paymentMethod || saleData.payment_method;
            const createdAt = saleData.createdAt || saleData.created_at;
            const items = saleData.items;

            // Vérifier session caisse
            const session = db.prepare("SELECT id FROM cash_sessions WHERE id = ? AND status = 'open'").get(cashSessionId);
            if (!session) return { success: false, message: "Aucune session de caisse ouverte." };

            // Vérifier stock
            for (const item of items) {
                const product = db.prepare("SELECT stock, name FROM products WHERE id = ?").get(item.id);
                if (!product) return { success: false, message: `Produit introuvable: ${item.id}` };
                if (product.stock < item.qty) return { success: false, message: `Stock insuffisant pour: ${product.name}` };
            }

            // Si crédit, vérifier client non bloqué
            if (paymentMethod === "credit" && customerId) {
                const cust = db.prepare("SELECT is_credit_blocked, name FROM customers WHERE id = ?").get(customerId);
                if (cust && cust.is_credit_blocked) {
                    return { success: false, message: `Le client ${cust.name} est bloqué pour les achats à crédit.` };
                }
            }

            const ts = createdAt || now();
            const reference = generateRef("SAL-OFF");

            const saleStmt = db.prepare(`
                INSERT INTO sales (user_id, cash_session_id, customer_id, total_amount, amount_received,
                    change_amount, payment_method, reference, status, created_at, updated_at, synced)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'completed', ?, ?, 0)
            `);
            const saleRes = saleStmt.run(
                userId, cashSessionId, customerId || null, totalAmount,
                amountReceived, changeAmount, paymentMethod, reference, ts, ts
            );
            const saleId = Number(saleRes.lastInsertRowid);

            const itemStmt = db.prepare(`
                INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            `);

            const lowStockAlerts = [];
            for (const item of items) {
                itemStmt.run(saleId, item.id, item.qty, item.price, item.price * item.qty, ts, ts);

                // Décrémenter le stock local
                db.prepare("UPDATE products SET stock = stock - ?, updated_at = ? WHERE id = ?")
                  .run(item.qty, ts, item.id);

                // Vérifier seuil de stock
                const product = db.prepare("SELECT name, stock, stock_threshold FROM products WHERE id = ?").get(item.id);
                if (product && product.stock <= product.stock_threshold) {
                    lowStockAlerts.push({ name: product.name, current_stock: product.stock });
                }
            }

            // Gérer la dette client (crédit)
            if (customerId && paymentMethod === "credit") {
                const debt = Math.max(0, totalAmount - amountReceived);
                if (debt > 0) {
                    db.prepare("UPDATE customers SET debt_balance = debt_balance + ?, updated_at = ? WHERE id = ?")
                      .run(debt, ts, customerId);
                }
            }

            // Récupérer la vente complète avec items
            const sale = db.prepare("SELECT * FROM sales WHERE id = ?").get(saleId);
            const saleItems = db.prepare(`
                SELECT si.*, p.name as product_name FROM sale_items si
                LEFT JOIN products p ON p.id = si.product_id
                WHERE si.sale_id = ?
            `).all(saleId);

            const saleWithItems = { ...sale, items: saleItems };

            // Ajouter dans la sync_queue
            db.prepare("INSERT INTO sync_queue (entity_type, entity_local_id, operation, payload, created_at) VALUES (?,?,?,?,?)")
              .run("sale", saleId, "create", JSON.stringify({ sale: saleWithItems, items }), ts);

            return { success: true, message: "Vente enregistrée avec succès.", sale: saleWithItems, low_stock_alerts: lowStockAlerts };
        } catch (e) {
            console.error("Erreur création vente SQLite:", e);
            return { success: false, message: e.message };
        }
    });

    ipcMain.handle("sqlite-get-sales", async (event, userId) => {
        try {
            const sales = db.prepare(`
                SELECT s.*, c.name as customer_name FROM sales s
                LEFT JOIN customers c ON c.id = s.customer_id
                WHERE s.user_id = ?
                ORDER BY s.created_at DESC LIMIT 50
            `).all(userId);

            for (const sale of sales) {
                sale.items = db.prepare(`
                    SELECT si.*, p.name as product_name FROM sale_items si
                    LEFT JOIN products p ON p.id = si.product_id
                    WHERE si.sale_id = ?
                `).all(sale.id);
            }
            return sales;
        } catch (e) { console.error(e); return []; }
    });

    ipcMain.handle("sqlite-refund-sale", async (event, { saleId, userId }) => {
        try {
            const sale = db.prepare("SELECT * FROM sales WHERE id = ?").get(saleId);
            if (!sale) return { success: false, message: "Vente introuvable." };
            if (sale.status === "returned") return { success: false, message: "Cette vente a déjà été remboursée." };

            const items = db.prepare("SELECT * FROM sale_items WHERE sale_id = ?").all(saleId);
            const ts = now();

            for (const item of items) {
                db.prepare("UPDATE products SET stock = stock + ?, updated_at = ? WHERE id = ?")
                  .run(item.quantity, ts, item.product_id);
                db.prepare("UPDATE sale_items SET returned_quantity = quantity, updated_at = ? WHERE id = ?")
                  .run(ts, item.id);
            }

            if (sale.customer_id && sale.payment_method === "credit") {
                const debt = Math.max(0, sale.total_amount - (sale.amount_received || 0));
                if (debt > 0) {
                    db.prepare("UPDATE customers SET debt_balance = MAX(0, debt_balance - ?), updated_at = ? WHERE id = ?")
                      .run(debt, ts, sale.customer_id);
                }
            }

            db.prepare("UPDATE sales SET status = 'returned', refunded_amount = ?, updated_at = ?, synced = 0 WHERE id = ?")
              .run(sale.total_amount, ts, saleId);

            db.prepare("INSERT INTO sync_queue (entity_type, entity_local_id, operation, payload, created_at) VALUES (?,?,?,?,?)")
              .run("sale", saleId, "refund", JSON.stringify({ saleId }), ts);

            return { success: true, message: "La vente a été annulée et les articles remis en stock." };
        } catch (e) { console.error(e); return { success: false, message: e.message }; }
    });

    // ── Clients ───────────────────────────────────────────────────────────────

    ipcMain.handle("sqlite-create-customer", async (event, customerData) => {
        try {
            const { name, phone, email, address } = customerData;
            const ts = now();
            const res = db.prepare(`
                INSERT INTO customers (name, phone, email, address, loyalty_points, debt_balance, created_at, updated_at)
                VALUES (?, ?, ?, ?, 0, 0, ?, ?)
            `).run(name, phone || null, email || null, address || null, ts, ts);
            const customer = db.prepare("SELECT * FROM customers WHERE id = ?").get(Number(res.lastInsertRowid));

            db.prepare("INSERT INTO sync_queue (entity_type, entity_local_id, operation, payload, created_at) VALUES (?,?,?,?,?)")
              .run("customer", customer.id, "create", JSON.stringify(customer), ts);

            return { success: true, message: "Client enregistré avec succès.", customer };
        } catch (e) { console.error(e); return { success: false, message: e.message }; }
    });

    // ── Paiements de dettes ───────────────────────────────────────────────────

    ipcMain.handle("sqlite-pay-debt", async (event, { customerId, userId, cashSessionId, amount }) => {
        try {
            const customer = db.prepare("SELECT * FROM customers WHERE id = ?").get(customerId);
            if (!customer) return { success: false, message: "Client introuvable." };

            const amountToPay = Math.min(customer.debt_balance, amount);
            if (amountToPay <= 0) return { success: false, message: "Ce client n'a pas de dette active." };

            const ts = now();
            const reference = generateRef("PAY");

            db.prepare("UPDATE customers SET debt_balance = debt_balance - ?, updated_at = ? WHERE id = ?")
              .run(amountToPay, ts, customerId);

            const res = db.prepare(`
                INSERT INTO debt_payments (customer_id, user_id, cash_session_id, amount, reference, payment_method, created_at, updated_at, synced)
                VALUES (?, ?, ?, ?, ?, 'cash', ?, ?, 0)
            `).run(customerId, userId, cashSessionId || null, amountToPay, reference, ts, ts);

            const payment = db.prepare("SELECT * FROM debt_payments WHERE id = ?").get(Number(res.lastInsertRowid));
            const updatedCustomer = db.prepare("SELECT * FROM customers WHERE id = ?").get(customerId);

            db.prepare("INSERT INTO sync_queue (entity_type, entity_local_id, operation, payload, created_at) VALUES (?,?,?,?,?)")
              .run("debt_payment", payment.id, "create", JSON.stringify(payment), ts);

            return {
                success: true,
                message: `Encaissement de ${amountToPay.toFixed(0)} FCFA enregistré avec succès.`,
                payment,
                new_debt_balance: updatedCustomer.debt_balance
            };
        } catch (e) { console.error(e); return { success: false, message: e.message }; }
    });

    // ── Statistiques dashboard ─────────────────────────────────────────────────

    ipcMain.handle("sqlite-get-dashboard-stats", async (event, userId) => {
        try {
            const today = now().substring(0, 10);

            const todaySales = db.prepare(`
                SELECT COALESCE(SUM(total_amount),0) as revenue, COUNT(*) as count
                FROM sales WHERE user_id = ? AND date(created_at) = ? AND status = 'completed'
            `).get(userId, today);

            const totalSales = db.prepare(`
                SELECT COALESCE(SUM(total_amount),0) as revenue, COUNT(*) as count
                FROM sales WHERE user_id = ? AND status = 'completed'
            `).get(userId);

            const topProducts = db.prepare(`
                SELECT si.product_id, p.name as product_name,
                    SUM(si.quantity) as total_qty, SUM(si.subtotal) as total_subtotal
                FROM sale_items si
                JOIN sales s ON s.id = si.sale_id
                LEFT JOIN products p ON p.id = si.product_id
                WHERE s.user_id = ? AND s.status = 'completed'
                GROUP BY si.product_id ORDER BY total_qty DESC LIMIT 5
            `).all(userId);

            const days = ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"];
            const weeklySales = [];
            for (let i = 6; i >= 0; i--) {
                const d = new Date();
                d.setDate(d.getDate() - i);
                const dateStr = d.toISOString().substring(0, 10);
                const dayRevenue = db.prepare(`
                    SELECT COALESCE(SUM(total_amount),0) as total FROM sales
                    WHERE user_id = ? AND date(created_at) = ? AND status = 'completed'
                `).get(userId, dateStr);
                weeklySales.push({
                    day: days[d.getDay()],
                    date: `${String(d.getDate()).padStart(2,"0")}/${String(d.getMonth()+1).padStart(2,"0")}`,
                    revenue: dayRevenue.total
                });
            }

            return {
                todayRevenue: todaySales.revenue,
                todayCount: todaySales.count,
                todayAverage: todaySales.count > 0 ? todaySales.revenue / todaySales.count : 0,
                totalRevenue: totalSales.revenue,
                totalCount: totalSales.count,
                totalAverage: totalSales.count > 0 ? totalSales.revenue / totalSales.count : 0,
                topProducts,
                weeklySales
            };
        } catch (e) { console.error(e); return {}; }
    });

    // ─── Synchronisation ──────────────────────────────────────────────────────

    /**
     * PULL : télécharge toutes les données depuis MySQL et remplit le SQLite
     */
    ipcMain.handle("sqlite-pull", async (event, { baseUrl, sessionCookie }) => {
        try {
            console.log("🔄 Début du PULL depuis", baseUrl);
            const headers = {
                "Content-Type": "application/json",
                "X-Sync-Key": SYNC_API_KEY,
                "Cookie": sessionCookie || ""
            };

            const response = await fetch(`${baseUrl}/api/sync/pull`, {
                method: "GET",
                headers
            });

            if (!response.ok) {
                const text = await response.text();
                throw new Error(`Erreur serveur ${response.status}: ${text}`);
            }

            const data = await response.json();
            console.log("✅ Données reçues du serveur :", Object.keys(data));

            // Insérer/remplacer chaque entité dans SQLite
            const ts = now();

            // Users
            if (data.users) {
                const stmt = db.prepare(`
                    INSERT OR REPLACE INTO users (id, name, email, password, role, phone, address, gender, login_code, is_blocked, deleted_at, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                `);
                for (const u of data.users) {
                    stmt.run(
                        u.id,
                        u.name,
                        u.email,
                        u.password || "",
                        u.role || "employee",
                        u.phone ?? null,
                        u.address ?? null,
                        u.gender ?? null,
                        u.login_code ?? null,
                        u.deleted_at ? 1 : 0,
                        u.deleted_at ?? null,
                        u.created_at ?? null,
                        u.updated_at ?? null
                    );
                }
            }

            // Categories
            if (data.categories) {
                const stmt = db.prepare(`
                    INSERT OR REPLACE INTO categories (id, name, slug, description, color, is_active, created_by, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                `);
                for (const c of data.categories) {
                    stmt.run(
                        c.id,
                        c.name,
                        c.slug ?? null,
                        c.description ?? null,
                        c.color ?? "#004d99",
                        c.is_active ? 1 : 0,
                        c.created_by ?? null,
                        c.created_at ?? null,
                        c.updated_at ?? null
                    );
                }
            }

            // Suppliers
            if (data.suppliers) {
                const stmt = db.prepare(`
                    INSERT OR REPLACE INTO suppliers (id, name, email, phone, contact_person, address, city, website, is_active, notes, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                `);
                for (const s of data.suppliers) {
                    stmt.run(
                        s.id,
                        s.name,
                        s.email ?? null,
                        s.phone ?? null,
                        s.contact_person ?? null,
                        s.address ?? null,
                        s.city ?? null,
                        s.website ?? null,
                        s.is_active ? 1 : 0,
                        s.notes ?? null,
                        s.created_at ?? null,
                        s.updated_at ?? null
                    );
                }
            }

            // Products
            if (data.products) {
                const stmt = db.prepare(`
                    INSERT OR REPLACE INTO products (id, name, slug, reference, qr_code, category_name, supplier_id, price, stock, stock_threshold, image, description, is_active, created_by, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                `);
                for (const p of data.products) {
                    stmt.run(
                        p.id,
                        p.name,
                        p.slug ?? null,
                        p.reference ?? null,
                        p.qr_code ?? null,
                        p.category_name ?? null,
                        p.supplier_id ?? null,
                        p.price ?? 0,
                        p.stock ?? 0,
                        p.stock_threshold ?? 5,
                        p.image ?? null,
                        p.description ?? null,
                        p.is_active ? 1 : 0,
                        p.created_by ?? null,
                        p.created_at ?? null,
                        p.updated_at ?? null
                    );
                }
            }

            // Customers
            if (data.customers) {
                const stmt = db.prepare(`
                    INSERT OR REPLACE INTO customers (id, name, phone, email, address, loyalty_points, debt_balance, is_credit_blocked, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                `);
                for (const c of data.customers) {
                    stmt.run(
                        c.id,
                        c.name,
                        c.phone ?? null,
                        c.email ?? null,
                        c.address ?? null,
                        c.loyalty_points ?? 0,
                        c.debt_balance ?? 0,
                        c.is_credit_blocked ? 1 : 0,
                        c.created_at ?? null,
                        c.updated_at ?? null
                    );
                }
            }

            // Settings
            if (data.settings) {
                const s = data.settings;
                const existing = db.prepare("SELECT id FROM settings LIMIT 1").get();
                if (existing) {
                    db.prepare(`
                        UPDATE settings SET store_name=?, phone=?, address=?, email=?, invoice_footer=?, invoice_format=?, updated_at=?
                        WHERE id=?
                    `).run(s.store_name, s.phone, s.address, s.email ?? null, s.invoice_footer ?? null, s.invoice_format ?? "ticket", ts, existing.id);
                } else {
                    db.prepare(`
                        INSERT INTO settings (store_name, phone, address, email, invoice_footer, invoice_format, created_at, updated_at)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    `).run(s.store_name, s.phone, s.address, s.email ?? null, s.invoice_footer ?? null, s.invoice_format ?? "ticket", ts, ts);
                }
            }

            // Cash sessions + sales existantes (pour l'historique)
            if (data.cash_sessions) {
                const stmt = db.prepare(`
                    INSERT OR REPLACE INTO cash_sessions (id, user_id, opening_balance, expected_closing_balance, actual_closing_balance, difference, opened_at, closed_at, status, created_at, updated_at, synced)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
                `);
                for (const cs of data.cash_sessions) {
                    stmt.run(
                        cs.id,
                        cs.user_id,
                        cs.opening_balance,
                        cs.expected_closing_balance ?? null,
                        cs.actual_closing_balance ?? null,
                        cs.difference ?? null,
                        cs.opened_at ?? null,
                        cs.closed_at ?? null,
                        cs.status ?? 'open',
                        cs.created_at ?? null,
                        cs.updated_at ?? null
                    );
                }
            }

            if (data.sales) {
                const saleStmt = db.prepare(`
                    INSERT OR REPLACE INTO sales (id, user_id, cash_session_id, customer_id, total_amount, amount_received, change_amount, payment_method, reference, status, refunded_amount, created_at, updated_at, synced)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
                `);
                const itemStmt = db.prepare(`
                    INSERT OR REPLACE INTO sale_items (id, sale_id, product_id, quantity, unit_price, subtotal, returned_quantity, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                `);
                for (const sale of data.sales) {
                    saleStmt.run(
                        sale.id,
                        sale.user_id,
                        sale.cash_session_id ?? null,
                        sale.customer_id ?? null,
                        sale.total_amount,
                        sale.amount_received ?? 0,
                        sale.change_amount ?? 0,
                        sale.payment_method ?? 'cash',
                        sale.reference ?? null,
                        sale.status ?? 'completed',
                        sale.refunded_amount ?? 0,
                        sale.created_at ?? null,
                        sale.updated_at ?? null
                    );
                    if (sale.items) {
                        for (const item of sale.items) {
                            itemStmt.run(
                                item.id,
                                item.sale_id,
                                item.product_id,
                                item.quantity,
                                item.unit_price,
                                item.subtotal,
                                item.returned_quantity ?? 0,
                                item.created_at ?? null,
                                item.updated_at ?? null
                            );
                        }
                    }
                }
            }

            // Debt payments
            if (data.debt_payments) {
                const stmt = db.prepare(`
                    INSERT OR REPLACE INTO debt_payments (id, customer_id, user_id, cash_session_id, amount, reference, payment_method, created_at, updated_at, synced)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
                `);
                for (const dp of data.debt_payments) {
                    stmt.run(
                        dp.id,
                        dp.customer_id,
                        dp.user_id,
                        dp.cash_session_id ?? null,
                        dp.amount,
                        dp.reference ?? null,
                        dp.payment_method ?? 'cash',
                        dp.created_at ?? null,
                        dp.updated_at ?? null
                    );
                }
            }

            return { success: true, message: "Données synchronisées depuis le serveur avec succès.", stats: {
                users: data.users?.length || 0,
                categories: data.categories?.length || 0,
                products: data.products?.length || 0,
                customers: data.customers?.length || 0,
                sales: data.sales?.length || 0,
            }};
        } catch (e) {
            console.error("Erreur PULL:", e);
            return { success: false, message: "Erreur lors du téléchargement : " + e.message };
        }
    });

    /**
     * PUSH : envoie les opérations hors-ligne (sync_queue) vers MySQL
     */
    ipcMain.handle("sqlite-push", async (event, { baseUrl, sessionCookie }) => {
        try {
            console.log("🔄 Début du PUSH vers", baseUrl);

            const pending = db.prepare("SELECT * FROM sync_queue WHERE synced = 0 ORDER BY id ASC").all();
            if (pending.length === 0) {
                return { success: true, message: "Aucune donnée hors-ligne à synchroniser.", synced: 0 };
            }

            const payload = pending.map(row => ({
                queue_id: row.id,
                entity_type: row.entity_type,
                entity_local_id: row.entity_local_id,
                operation: row.operation,
                data: JSON.parse(row.payload),
                created_at: row.created_at
            }));

            const response = await fetch(`${baseUrl}/api/sync/push`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Sync-Key": SYNC_API_KEY,
                    "Cookie": sessionCookie || ""
                },
                body: JSON.stringify({ operations: payload })
            });

            if (!response.ok) {
                const text = await response.text();
                throw new Error(`Erreur serveur ${response.status}: ${text}`);
            }

            const result = await response.json();
            const syncedIds = result.synced_queue_ids || [];
            const ts = now();

            // Marquer comme synchronisés dans la queue
            for (const qid of syncedIds) {
                db.prepare("UPDATE sync_queue SET synced = 1 WHERE id = ?").run(qid);
            }

            // Marquer les entités correspondantes
            const syncedRows = pending.filter(r => syncedIds.includes(r.id));
            for (const row of syncedRows) {
                if (row.entity_type === "sale") {
                    db.prepare("UPDATE sales SET synced = 1, updated_at = ? WHERE id = ?").run(ts, row.entity_local_id);
                } else if (row.entity_type === "customer") {
                    // Le client a été créé en ligne, on peut récupérer son vrai ID si nécessaire
                } else if (row.entity_type === "debt_payment") {
                    db.prepare("UPDATE debt_payments SET synced = 1, updated_at = ? WHERE id = ?").run(ts, row.entity_local_id);
                } else if (row.entity_type === "cash_session") {
                    db.prepare("UPDATE cash_sessions SET synced = 1, updated_at = ? WHERE id = ?").run(ts, row.entity_local_id);
                }
            }

            // Enregistrer les erreurs de sync
            const errors = result.errors || [];
            for (const err of errors) {
                db.prepare("UPDATE sync_queue SET sync_error = ? WHERE id = ?").run(err.message, err.queue_id);
            }

            return {
                success: true,
                message: `${syncedIds.length} opération(s) synchronisée(s) avec succès.`,
                synced: syncedIds.length,
                errors: errors
            };
        } catch (e) {
            console.error("Erreur PUSH:", e);
            return { success: false, message: "Erreur lors de la synchronisation : " + e.message };
        }
    });

    /**
     * État de la sync_queue (nb d'opérations en attente)
     */
    ipcMain.handle("sqlite-pending-count", async () => {
        try {
            const result = db.prepare("SELECT COUNT(*) as count FROM sync_queue WHERE synced = 0").get();
            return result.count;
        } catch (e) { return 0; }
    });

    ipcMain.handle("sqlite-get-session-expected-balance", async (event, sessionId) => {
        try {
            const totalSales = db.prepare(
                "SELECT COALESCE(SUM(total_amount),0) as total FROM sales WHERE cash_session_id = ? AND status = 'completed'"
            ).get(sessionId).total;

            const totalRepayments = db.prepare(
                "SELECT COALESCE(SUM(amount),0) as total FROM debt_payments WHERE cash_session_id = ?"
            ).get(sessionId).total;

            const session = db.prepare("SELECT * FROM cash_sessions WHERE id = ?").get(sessionId);
            if (!session) return { success: false, message: "Session introuvable dans SQLite." };

            const expected = session.opening_balance + totalSales + totalRepayments;
            return {
                success: true,
                openingBalance: session.opening_balance,
                totalSales,
                totalRepayments,
                expectedClosingBalance: expected
            };
        } catch (e) {
            console.error("sqlite-get-session-expected-balance error:", e);
            return { success: false, message: e.message };
        }
    });

    // Compatibilité ascendante avec les anciens handlers offline_sales
    ipcMain.handle("save-offline-sale", async (event, saleData) => {
        // Redirigé vers la nouvelle table sync_queue
        const ts = now();
        const res = db.prepare("INSERT INTO sync_queue (entity_type, entity_local_id, operation, payload, created_at) VALUES (?,?,?,?,?)")
          .run("sale_legacy", 0, "create", JSON.stringify(saleData), ts);
        return { success: true, localId: Number(res.lastInsertRowid) };
    });

    ipcMain.handle("get-offline-sales", async () => {
        try {
            const rows = db.prepare("SELECT * FROM sync_queue WHERE entity_type = 'sale' AND synced = 0").all();
            return rows.map(r => {
                try {
                    const d = JSON.parse(r.payload);
                    d.localId = r.id;
                    return d;
                } catch { return null; }
            }).filter(Boolean);
        } catch { return []; }
    });

    ipcMain.handle("delete-offline-sales", async (event, localIds) => {
        if (!Array.isArray(localIds) || localIds.length === 0) return { success: true };
        const stmt = db.prepare("UPDATE sync_queue SET synced = 1 WHERE id = ?");
        for (const id of localIds) stmt.run(id);
        return { success: true };
    });
}

// ─── Démarrage Electron ────────────────────────────────────────────────────────
app.whenReady().then(() => {
    setupPermissions();
    initDatabase();
    createWindow();
});
