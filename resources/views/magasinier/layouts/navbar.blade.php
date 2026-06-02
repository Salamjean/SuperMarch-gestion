<header class="mg-topbar">
    <div class="mg-topbar-brand">
        <i class="fa-solid fa-cart-shopping"></i>
        Supermarche <span>Pro</span>
    </div>

    <div class="mg-topbar-center">
        <div class="mg-page-context">@yield('page_title', 'Espace Magasinier')</div>
    </div>

    <div class="mg-topbar-right">
        <div class="mg-user-info">
            <div class="mg-user-name">{{ auth()->user()->name }}</div>
            <div class="mg-user-role">Magasinier</div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="mg-topbar-logout-btn">
                <i class="fa-solid fa-power-off"></i>
                <span>Quitter</span>
            </button>
        </form>
    </div>
</header>
