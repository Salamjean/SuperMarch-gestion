import { app, BrowserWindow, shell, session, ipcMain } from "electron";
import path from "path";
import { fileURLToPath } from "url";
import { DatabaseSync } from "node:sqlite";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Nécessaire pour que getUserMedia fonctionne sur http://127.0.0.1
app.commandLine.appendSwitch(
    "unsafely-treat-insecure-origin-as-secure",
    "http://127.0.0.1:8000,http://fescads.com",
);
app.commandLine.appendSwitch("enable-features", "WebRTCPipeWireCapturer");

function setupPermissions() {
    // Autorise les demandes de permissions caméra / micro
    session.defaultSession.setPermissionRequestHandler(
        (webContents, permission, callback) => {
            const allowed = [
                "media",
                "camera",
                "microphone",
                "videoCapture",
                "audioCapture",
            ];
            callback(allowed.includes(permission));
        },
    );

    // Vérifie les permissions caméra / micro
    session.defaultSession.setPermissionCheckHandler(
        (webContents, permission) => {
            const allowed = [
                "media",
                "camera",
                "microphone",
                "videoCapture",
                "audioCapture",
            ];
            return allowed.includes(permission);
        },
    );

    // Electron v20+ : autorise l'accès aux périphériques média (caméra)
    session.defaultSession.setDevicePermissionHandler((details) => {
        if (details.deviceType === "hid" || details.deviceType === "serial")
            return false;
        return true; // autorise caméra, micro, etc.
    });
}

function createWindow() {
    const win = new BrowserWindow({
        width: 1200,
        height: 800,
        show: false,
        webPreferences: {
            contextIsolation: true,
            nodeIntegration: false,
            webSecurity: false, // requis pour getUserMedia sur http://127.0.0.1
            allowRunningInsecureContent: true,
            preload: path.join(__dirname, "preload.js"),
        },
    });

    win.maximize();
    win.show();
    win.setMenuBarVisibility(false); // Cache la barre de menu pour le look POS

    win.loadURL("https://fescads.com");

    // 🌐 Gestion du mode hors connexion (évite l'écran blanc si pas d'internet au démarrage)
    win.webContents.on(
        "did-fail-load",
        async (event, errorCode, errorDescription, validatedURL) => {
            // Si le chargement de l'URL distante fescads.com échoue
            if (validatedURL.startsWith("https://fescads.com")) {
                console.log("Échec du chargement de fescads.com :", errorDescription);

                // Tentative 1 : Secours sur le serveur local de dev si actif
                try {
                    const response = await fetch("http://127.0.0.1:8000", { method: "HEAD" });
                    if (response.ok) {
                        console.log("Serveur local détecté actif. Redirection vers http://127.0.0.1:8000");
                        win.loadURL("http://127.0.0.1:8000");
                        return;
                    }
                } catch (err) {
                    // Le serveur local de dev ne tourne pas
                }

                // Tentative 2 : Chargement de la page locale magnifique de secours hors-ligne
                console.log("Chargement de la page de secours hors ligne locale.");
                win.loadFile(path.join(__dirname, "public", "offline.html"));
            }
        }
    );

    // 🔥 Ouvre les liens externes dans le navigateur
    win.webContents.setWindowOpenHandler(({ url }) => {
        shell.openExternal(url);
        return { action: "deny" };
    });

    // Stockage global des fenêtres d'impression actives pour éviter la garbage collection précoce d'Electron
    const activePrintWindows = new Set();

    // Écoute de l'événement d'impression silencieuse
    ipcMain.on("print-receipt", (event, htmlContent) => {
        // Crée une fenêtre invisible pour charger et imprimer le reçu
        let printWindow = new BrowserWindow({
            show: false,
            webPreferences: {
                nodeIntegration: false,
                contextIsolation: true,
            },
        });

        // Ajoute à notre Set global pour maintenir la référence active et empêcher la GC
        activePrintWindows.add(printWindow);

        // Charge le code html du reçu
        printWindow.loadURL(
            `data:text/html;charset=utf-8,${encodeURIComponent(htmlContent)}`,
        );

        printWindow.webContents.on("did-finish-load", () => {
            // Un petit délai permet à Chromium d'effectuer le layout/calcul des styles et le rendu complet
            // pour éviter d'imprimer une page blanche (blank page)
            setTimeout(() => {
                // Imprime sans dialogue (silencieux) sur l'imprimante par défaut
                printWindow.webContents.print(
                    {
                        silent: true,
                        printBackground: true,
                        margins: { marginType: "none" }, // Optimisé pour les imprimantes thermiques
                    },
                    (success, failureReason) => {
                        if (!success) {
                            console.error(
                                "Erreur lors de l'impression silencieuse :",
                                failureReason,
                            );
                            // Si l'impression silencieuse échoue, on peut proposer l'impression classique
                            event.reply("print-error", failureReason);
                        }
                        // Ferme et libère la fenêtre
                        printWindow.close();
                        activePrintWindows.delete(printWindow);
                    },
                );
            }, 300); // 300ms de délai garantit le rendu complet avant l'impression
        });
    });

}

let db;

function initDatabase() {
    try {
        // Enregistre le fichier de base de données SQLite local directement dans le dossier "database" du projet
        // pour qu'il soit visible et inspectable directement dans l'arborescence de VS Code.
        const dbPath = path.join(
            __dirname,
            "database",
            "database_local.sqlite",
        );
        console.log("Local SQLite database initialized at:", dbPath);
        db = new DatabaseSync(dbPath);

        // Crée la table pour stocker les ventes locales
        db.exec(`
            CREATE TABLE IF NOT EXISTS offline_sales (
                localId INTEGER PRIMARY KEY AUTOINCREMENT,
                sale_data TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        `);

        // Gestionnaires IPC pour interagir avec SQLite
        ipcMain.handle("save-offline-sale", async (event, saleData) => {
            try {
                const stmt = db.prepare(
                    "INSERT INTO offline_sales (sale_data) VALUES (?)",
                );
                const res = stmt.run(JSON.stringify(saleData));
                return { success: true, localId: Number(res.lastInsertRowid) };
            } catch (e) {
                console.error("Erreur de sauvegarde locale SQLite :", e);
                throw e;
            }
        });

        ipcMain.handle("get-offline-sales", async (event) => {
            try {
                const stmt = db.prepare(
                    "SELECT localId, sale_data FROM offline_sales",
                );
                const rows = stmt.all();
                return rows
                    .map((row) => {
                        try {
                            const data = JSON.parse(row.sale_data);
                            data.localId = Number(row.localId);
                            return data;
                        } catch (parseErr) {
                            console.error(
                                "Erreur de parsing JSON pour la vente locale",
                                row.localId,
                                parseErr,
                            );
                            return null;
                        }
                    })
                    .filter((item) => item !== null);
            } catch (e) {
                console.error("Erreur de récupération locale SQLite :", e);
                return [];
            }
        });

        ipcMain.handle("delete-offline-sales", async (event, localIds) => {
            try {
                if (!Array.isArray(localIds) || localIds.length === 0)
                    return { success: true };
                const stmt = db.prepare(
                    "DELETE FROM offline_sales WHERE localId = ?",
                );
                for (const id of localIds) {
                    stmt.run(id);
                }
                return { success: true };
            } catch (e) {
                console.error("Erreur de suppression locale SQLite :", e);
                throw e;
            }
        });
    } catch (e) {
        console.error("Erreur d'initialisation de la base SQLite locale :", e);
    }
}

app.whenReady().then(() => {
    setupPermissions();
    initDatabase();
    createWindow();
});
