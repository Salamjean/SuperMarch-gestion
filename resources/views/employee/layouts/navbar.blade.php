<header class="topbar">
    <div class="topbar-brand">
        <i><i class="fa-solid fa-cart-shopping"></i></i>
        Supermarché <span>Pro</span>
    </div>

    <div class="topbar-center">
        <div style="display: flex; gap: 10px; align-items: center;">
            <div id="usb-scanner-status"
                style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 15px; border-radius: 12px; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-keyboard" style="color: var(--secondary);"></i>
                <span>LECTEUR USB PRÊT</span>
            </div>

            <!-- Indicateur de Connexion & Sync pour la caisse -->
            <div id="nav-offline-indicator" style="display: none; display: flex; gap: 10px; align-items: center;">
                <div id="connection-status-pill"
                    style="background: #059669; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 15px; border-radius: 12px; font-size: 13px; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-wifi" id="connection-status-icon"></i>
                    <span id="connection-status-text">EN LIGNE</span>
                </div>
                <button type="button" id="btn-manual-sync" onclick="triggerManualSync()"
                    style="background: #6366f1; border: 1px solid rgba(255,255,255,0.2); color: #fff; padding: 8px 15px; border-radius: 12px; font-size: 13px; display: none; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;"
                    onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1'">
                    <i class="fa-solid fa-arrow-rotate-right" id="sync-icon"></i>
                    <span>Synchroniser (<span id="sync-pending-count">0</span>)</span>
                </button>
            </div>
        </div>
    </div>

    <div class="topbar-right">
        <div class="cashier-info">
            <div class="cashier-name">{{ auth()->user()->name }}</div>
            <div class="cashier-role">Caissier Principal</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf @method('DELETE')
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-power-off"></i> Quitter
            </button>
        </form>
    </div>
</header>
