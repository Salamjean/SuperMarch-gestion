<aside class="mg-sidebar">
    <nav class="mg-sidebar-nav">
        <a href="{{ route('magasinier.dashboard') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-gauge-high"></i>
            <span>Tableau de bord</span>
        </a>

        <a href="{{ route('magasinier.categories.index') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.categories.*') ? 'active' : '' }}">
            <i class="fa-solid fa-tags"></i>
            <span>Categories</span>
        </a>

        <a href="{{ route('magasinier.products.index') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.products.*') ? 'active' : '' }}">
            <i class="fa-solid fa-box"></i>
            <span>Produits</span>
        </a>

        <a href="{{ route('magasinier.suppliers.index') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.suppliers.*') ? 'active' : '' }}">
            <i class="fa-solid fa-truck"></i>
            <span>Fournisseurs</span>
        </a>

        <a href="{{ route('magasinier.restock-requests.index') }}"
            class="mg-nav-link {{ request()->routeIs('magasinier.restock-requests.*') ? 'active' : '' }}">
            <i class="fa-solid fa-truck-loading"></i>
            <span>Réapprovisionnements</span>
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
