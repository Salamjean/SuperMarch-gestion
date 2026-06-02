const { contextBridge, ipcRenderer } = require("electron");

contextBridge.exposeInMainWorld("electronAPI", {
    printReceipt: (htmlContent) =>
        ipcRenderer.send("print-receipt", htmlContent),
    saveOfflineSale: (saleData) =>
        ipcRenderer.invoke("save-offline-sale", saleData),
    getOfflineSales: () => ipcRenderer.invoke("get-offline-sales"),
    deleteOfflineSales: (localIds) =>
        ipcRenderer.invoke("delete-offline-sales", localIds),
});
