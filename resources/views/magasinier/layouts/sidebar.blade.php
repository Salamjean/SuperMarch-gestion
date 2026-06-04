<aside class="mg-sidebar">
    <nav class="mg-sidebar-nav">
        <a href="{{ route('magasinier.dashboard') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Tableau de bord</span>
        </a>

        @php
            $prodActive = request()->routeIs('magasinier.products.*') && !request()->routeIs('magasinier.products.threshold') && !request()->routeIs('magasinier.products.restock-grid');
        @endphp

        <a href="{{ route('magasinier.products.index') }}"
            class="mg-nav-link {{ $prodActive ? 'active' : '' }}">
            <i class="fa-solid fa-box"></i>
            <span>Produits</span>
        </a>

        <a href="{{ route('magasinier.products.threshold') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.products.threshold') ? 'active' : '' }}"
            style="display: flex; justify-content: space-between; align-items: center;">
            <span style="display: flex; align-items: center; gap: 12px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Seuil Atteint</span>
            </span>
            @if (isset($productsAtThresholdCount) && $productsAtThresholdCount > 0)
                <span style="font-size: 10.5px; font-weight: 800; padding: 2px 7.5px; border-radius: 999px; min-width: 20px; text-align: center;
                    background: {{ request()->routeIs('magasinier.products.threshold') ? 'var(--danger)' : 'var(--secondary)' }};
                    color: {{ request()->routeIs('magasinier.products.threshold') ? '#ffffff' : 'var(--primary)' }};">
                    {{ $productsAtThresholdCount }}
                </span>
            @endif
        </a>

        <a href="{{ route('magasinier.restock-requests.index') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.restock-requests.*') ? 'active' : '' }}">
            <i class="fa-solid fa-truck-loading"></i>
            <span>Réapprovisionnements</span>
        </a>

        <a href="{{ route('magasinier.products.restock-grid') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.products.restock-grid') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Réappro. Rapide</span>
        </a>

        <a href="{{ route('magasinier.suppliers.index') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.suppliers.*') ? 'active' : '' }}">
            <i class="fa-solid fa-truck"></i>
            <span>Fournisseurs</span>
        </a>

        <a href="{{ route('magasinier.categories.index') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.categories.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tags"></i>
            <span>Categories</span>
        </a>

        <a href="{{ route('magasinier.profile.show') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.profile.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear"></i>
            <span>Mon Profil</span>
        </a>
    </nav>

    <div class="mg-sidebar-footer">
        <div class="mg-clock" id="mg-live-clock">00:00:00</div>
        <div class="mg-date" id="mg-live-date">--</div>
    </div>
</aside>
