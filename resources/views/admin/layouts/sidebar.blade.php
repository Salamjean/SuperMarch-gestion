<aside class="sidebar" id="sidebar">

    <!-- Logo -->
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">🛒</div>
        <div class="sidebar-logo-text">
            <span class="sidebar-logo-name" style="font-size: 13.5px; text-transform: uppercase; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 170px; display: inline-block;">{{ $storeSettings->store_name }}</span>
            <span class="sidebar-logo-role">Administration</span>
        </div>
    </div>

    <hr class="sidebar-divider">

    <!-- Navigation -->
    <nav class="sidebar-nav">
        @php
            $prodOpen = request()->routeIs('admin.products.*') && !request()->routeIs('admin.products.threshold') && !request()->routeIs('admin.products.restock-grid');
            $salesOpen = request()->routeIs('admin.sales.*');
            $cashOpen = request()->routeIs('admin.cash-sessions.*');
            $restockOpen = request()->routeIs('admin.restock-requests.*');
            $custOpen = request()->routeIs('admin.customers.*') || request()->routeIs('admin.debt-payments.index');
            $supOpen = request()->routeIs('admin.suppliers.*');
            $catOpen = request()->routeIs('admin.categories.*');
            $empOpen = request()->routeIs('admin.employees.*');
        @endphp

        <p class="sidebar-section-label">Activité Quotidienne</p>

        {{-- Tableau de bord --}}
        <a href="{{ route('admin.dashboard') }}"
            class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            <span>Tableau de bord</span>
        </a>

        {{-- Historique des Ventes --}}
        <a href="{{ route('admin.sales.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">
            <i class="fa-solid fa-receipt"></i>
            <span>Ventes & Factures</span>
        </a>

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
                <a href="{{ route('admin.products.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i> Liste des Produits
                </a>
                <a href="{{ route('admin.products.create') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.products.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus"></i> Ajouter un Produit
                </a>
            </div>
        </div>

        {{-- Seuil Atteint --}}
        <a href="{{ route('admin.products.threshold') }}"
            class="sidebar-link {{ request()->routeIs('admin.products.threshold') ? 'active' : '' }}"
            style="display: flex; justify-content: space-between; align-items: center; gap: 8px;">
            <span style="display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>Seuil Atteint</span>
            </span>
            @if (isset($productsAtThresholdCount) && $productsAtThresholdCount > 0)
                <span style="font-size: 10.5px; font-weight: 800; padding: 2px 7.5px; border-radius: 999px; min-width: 20px; text-align: center;
                    background: {{ request()->routeIs('admin.products.threshold') ? '#dc2626' : 'var(--yellow)' }};
                    color: {{ request()->routeIs('admin.products.threshold') ? '#ffffff' : 'var(--blue-dark)' }};">
                    {{ $productsAtThresholdCount }}
                </span>
            @endif
        </a>

        {{-- Sessions de Caisse --}}
        <a href="{{ route('admin.cash-sessions.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.cash-sessions.*') ? 'active' : '' }}">
            <i class="fa-solid fa-vault"></i>
            <span>Sessions de Caisse</span>
        </a>

        <p class="sidebar-section-label">Gestion & Logistique</p>

        {{-- Demandes de Réapprovisionnement --}}
        <a href="{{ route('admin.restock-requests.index') }}"
            class="sidebar-link {{ request()->routeIs('admin.restock-requests.*') ? 'active' : '' }}">
            <i class="fa-solid fa-truck-loading"></i>
            <span>Réapprovisionnements</span>
        </a>

        {{-- Réapprovisionnement Rapide --}}
        <a href="{{ route('admin.products.restock-grid') }}"
            class="sidebar-link {{ request()->routeIs('admin.products.restock-grid') ? 'active' : '' }}">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Réappro. Rapide</span>
        </a>

        {{-- Dropdown Gestion Clientèle --}}
        <div class="sidebar-dropdown {{ $custOpen ? 'open' : '' }}">
            <button class="sidebar-dropdown-toggle" onclick="toggleDropdown(this)">
                <span class="sidebar-dropdown-left">
                    <i class="fa-solid fa-id-card"></i>
                    <span>Gestion Clientèle</span>
                </span>
                <i class="fa-solid fa-chevron-right sidebar-chevron"></i>
            </button>
            <div class="sidebar-dropdown-menu">
                <a href="{{ route('admin.customers.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.customers.index') || request()->routeIs('admin.customers.show') || request()->routeIs('admin.customers.edit') || request()->routeIs('admin.customers.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-users"></i> Liste des Clients
                </a>
                <a href="{{ route('admin.debt-payments.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.debt-payments.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-hand-holding-dollar"></i> Encaissements Crédits
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
                <a href="{{ route('admin.suppliers.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.suppliers.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i> Liste Fournisseurs
                </a>
                <a href="{{ route('admin.suppliers.create') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.suppliers.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus"></i> Ajouter Fournisseur
                </a>
            </div>
        </div>

        <p class="sidebar-section-label">Configuration</p>

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
                <a href="{{ route('admin.categories.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i> Liste Catégories
                </a>
                <a href="{{ route('admin.categories.create') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-plus"></i> Ajouter Catégorie
                </a>
            </div>
        </div>

        {{-- Dropdown Employés --}}
        <div class="sidebar-dropdown {{ $empOpen ? 'open' : '' }}">
            <button class="sidebar-dropdown-toggle" onclick="toggleDropdown(this)">
                <span class="sidebar-dropdown-left">
                    <i class="fa-solid fa-users"></i>
                    <span>Employés</span>
                </span>
                <i class="fa-solid fa-chevron-right sidebar-chevron"></i>
            </button>
            <div class="sidebar-dropdown-menu">
                <a href="{{ route('admin.employees.index') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.employees.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-list"></i> Liste Employés
                </a>
                <a href="{{ route('admin.employees.create') }}"
                    class="sidebar-sub-link {{ request()->routeIs('admin.employees.create') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-plus"></i> Ajouter Employé
                </a>
            </div>
        </div>

        {{-- Paramètres Boutique --}}
        <a href="{{ route('admin.settings.edit') }}"
            class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <i class="fa-solid fa-store"></i>
            <span>Paramètres Boutique</span>
        </a>

        {{-- Mon Profil --}}
        <a href="{{ route('admin.profile.show') }}"
            class="sidebar-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
            <i class="fa-solid fa-user-gear"></i>
            <span>Mon Profil</span>
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
