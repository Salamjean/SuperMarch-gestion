<aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">🛒</div>
        <div class="sidebar-logo-text">
            <span class="sidebar-logo-name">Supermarché <strong>Pro</strong></span>
            <span class="sidebar-logo-role">Administration</span>
        </div>
    </div>

    <hr class="sidebar-divider">

    <!-- Navigation -->
    <nav class="sidebar-nav">
        <p class="sidebar-section-label">Menu</p>

        {{-- Tableau de bord --}}
        <a href="{{ route('admin.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            <span>Tableau de bord</span>
        </a>

        {{-- Dropdown Employés --}}
        @php
            $empOpen = request()->routeIs('admin.employees.*');
            $catOpen = request()->routeIs('admin.categories.*');
            $supOpen = request()->routeIs('admin.suppliers.*');
            $prodOpen = request()->routeIs('admin.products.*');
        @endphp

        <div class="sidebar-dropdown {{ $empOpen ? 'open' : '' }}">
            <button class="sidebar-dropdown-toggle" onclick="toggleDropdown(this)">
                <span class="sidebar-dropdown-left">
                    <i class="fa-solid fa-users"></i>
                    <span>Employés</span>
                </span>
                <i class="fa-solid fa-chevron-right sidebar-chevron"></i>
            </button>
            <div class="sidebar-dropdown-menu">
                <a href="{{ route('admin.employees.create') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.employees.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-plus"></i> Ajouter
                </a>
                <a href="{{ route('admin.employees.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.employees.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i> Liste
                </a>
            </div>
        </div>

        {{-- Dropdown Catégories --}}
        <div class="sidebar-dropdown {{ $catOpen ? 'open' : '' }}">
            <button class="sidebar-dropdown-toggle" onclick="toggleDropdown(this)">
                <span class="sidebar-dropdown-left">
                    <i class="fa-solid fa-tags"></i>
                    <span>Catégories</span>
                </span>
                <i class="fa-solid fa-chevron-right sidebar-chevron"></i>
            </button>
            <div class="sidebar-dropdown-menu">
                <a href="{{ route('admin.categories.create') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </a>
                <a href="{{ route('admin.categories.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i> Liste
                </a>
            </div>
        </div>

        {{-- Dropdown Fournisseurs --}}
        <div class="sidebar-dropdown {{ $supOpen ? 'open' : '' }}">
            <button class="sidebar-dropdown-toggle" onclick="toggleDropdown(this)">
                <span class="sidebar-dropdown-left">
                    <i class="fa-solid fa-truck"></i>
                    <span>Fournisseurs</span>
                </span>
                <i class="fa-solid fa-chevron-right sidebar-chevron"></i>
            </button>
            <div class="sidebar-dropdown-menu">
                <a href="{{ route('admin.suppliers.create') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.suppliers.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </a>
                <a href="{{ route('admin.suppliers.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.suppliers.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i> Liste
                </a>
            </div>
        </div>

        {{-- Dropdown Produits --}}
        <div class="sidebar-dropdown {{ $prodOpen ? 'open' : '' }}">
            <button class="sidebar-dropdown-toggle" onclick="toggleDropdown(this)">
                <span class="sidebar-dropdown-left">
                    <i class="fa-solid fa-box"></i>
                    <span>Produits</span>
                </span>
                <i class="fa-solid fa-chevron-right sidebar-chevron"></i>
            </button>
            <div class="sidebar-dropdown-menu">
                <a href="{{ route('admin.products.create') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus"></i> Ajouter
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i> Liste
                </a>
            </div>
        </div>

        {{-- Demandes de Réapprovisionnement --}}
        <a href="{{ route('admin.restock-requests.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.restock-requests.*') ? 'active' : '' }}">
            <i class="fa-solid fa-truck-loading"></i>
            <span>Réapprovisionnements</span>
        </a>

        <p class="sidebar-section-label">Audit & CRM</p>

        {{-- Historique des Ventes --}}
        <a href="{{ route('admin.sales.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt"></i>
            <span>Ventes & Factures</span>
        </a>

        {{-- Sessions de Caisse --}}
        <a href="{{ route('admin.cash-sessions.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.cash-sessions.*') ? 'active' : '' }}">
            <i class="fa-solid fa-vault"></i>
            <span>Sessions de Caisse</span>
        </a>

        {{-- Gestion Clientèle --}}
        <a href="{{ route('admin.customers.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
            <i class="fa-solid fa-id-card"></i>
            <span>Gestion Clientèle</span>
        </a>

    </nav>

    <!-- Footer sidebar -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-user">
            <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <p class="sidebar-footer-name">{{ auth()->user()->name }}</p>
                <p class="sidebar-footer-email">{{ auth()->user()->login_code ?? auth()->user()->email }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="sidebar-logout" title="Déconnexion">
                <i class="fa-solid fa-right-from-bracket"></i>
            </button>
        </form>
    </div>

</aside>
