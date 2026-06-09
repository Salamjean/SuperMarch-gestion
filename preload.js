const { contextBridge, ipcRenderer } = require("electron");

contextBridge.exposeInMainWorld("electronAPI", {
    // ── Impression ────────────────────────────────────────────────────────────
    printReceipt: (htmlContent) =>
        ipcRenderer.send("print-receipt", htmlContent),

    // ── Compatibilité ascendante (anciens handlers offline_sales) ─────────────
    saveOfflineSale: (saleData) =>
        ipcRenderer.invoke("save-offline-sale", saleData),
    getOfflineSales: () =>
        ipcRenderer.invoke("get-offline-sales"),
    deleteOfflineSales: (localIds) =>
        ipcRenderer.invoke("delete-offline-sales", localIds),

    // ── Handler générique IPC — permet d'appeler n'importe quel handler ───────
    // Utilisation : window.electronAPI.invoke('sqlite-pull', { ... })
    invoke: (channel, ...args) => {
        const allowed = [
            // Sync
            "sqlite-pull",
            "sqlite-push",
            "sqlite-pending-count",
            // Lecture
            "sqlite-get-products",
            "sqlite-get-all-products",
            "sqlite-get-categories",
            "sqlite-get-customers",
            "sqlite-search-customers",
            "sqlite-get-settings",
            "sqlite-get-user",
            "sqlite-get-sales",
            "sqlite-get-dashboard-stats",
            // Sessions de caisse
            "sqlite-get-active-session",
            "sqlite-open-session",
            "sqlite-close-session",
            "sqlite-get-session-expected-balance",
            // Écriture
            "sqlite-create-sale",
            "sqlite-refund-sale",
            "sqlite-create-customer",
            "sqlite-pay-debt",
            // Compat
            "save-offline-sale",
            "get-offline-sales",
            "delete-offline-sales",
        ];
        if (!allowed.includes(channel)) {
            console.error(`[preload] Canal IPC non autorisé : ${channel}`);
            return Promise.reject(new Error(`Canal non autorisé : ${channel}`));
        }
        return ipcRenderer.invoke(channel, ...args);
    },
});
