<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — SuperMarché Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .swal2-input {
            max-width: 90% !important;
            box-sizing: border-box !important;
        }

        :root {
            --blue: #004d99;
            --blue-dark: #003d7a;
            --blue-light: #e8f1fb;
            --yellow: #ffc300;
            --yellow-dark: #e6b000;
            --primary: #004d99;
            --primary-light: #1a6bbf;
            --sidebar-w: 270px;
            --navbar-h: 58px;
            --text: #1a2840;
            --text-muted: #7a94aa;
            --border: #e2eaf3;
            --bg: #f3f7fc;
        }

        html,
        body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        /* ── Layout shell ── */
        .app-shell {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ══════════════════════════════
           SIDEBAR
        ══════════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-width: var(--sidebar-w);
            background: var(--blue);
            display: flex;
            flex-direction: column;
            height: 100vh;
            position: relative;
            z-index: 100;
            transition: transform .3s ease;
        }

        /* Logo */
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 20px 18px 16px;
        }

        .sidebar-logo-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: var(--yellow);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .sidebar-logo-name {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #ffffff;
            line-height: 1.2;
        }

        .sidebar-logo-name strong {
            color: var(--yellow);
        }

        .sidebar-logo-role {
            display: block;
            font-size: 10.5px;
            color: rgba(255, 255, 255, .5);
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        /* Divider */
        .sidebar-divider {
            border: none;
            border-top: 1px solid rgba(255, 255, 255, .1);
            margin: 0 16px 12px;
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            padding: 0 12px 25px;
            overflow-y: auto;
            scrollbar-width: none;        /* Firefox */
            -ms-overflow-style: none;     /* IE/Edge */
        }

        .sidebar-nav::-webkit-scrollbar {
            display: none;                /* Chrome, Safari */
        }

        .sidebar-section-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .4);
            padding: 18px 16px 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 11px 16px;
            border-radius: 12px;
            text-decoration: none;
            color: rgba(255, 255, 255, .8);
            font-size: 13.5px;
            font-weight: 500;
            margin-bottom: 4px;
            transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
        }

        .sidebar-link i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
            font-size: 13px;
            transition: all .25s ease;
            flex-shrink: 0;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, .08);
            color: #ffffff;
            padding-left: 19px;
            border-left-color: rgba(255, 255, 255, 0.3);
        }

        .sidebar-link:hover i {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.05);
        }

        .sidebar-link.active {
            background: var(--yellow);
            color: var(--blue-dark);
            font-weight: 700;
            padding-left: 19px;
            border-left-color: var(--yellow-dark);
            box-shadow: 0 4px 15px rgba(255, 195, 0, 0.2);
        }

        .sidebar-link.active i {
            color: var(--blue-dark);
        }

        /* Sidebar footer */
        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, .1);
            padding: 14px 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-footer-user {
            display: flex;
            align-items: center;
            gap: 9px;
            flex: 1;
            min-width: 0;
        }

        .sidebar-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--yellow);
            color: var(--blue-dark);
            font-size: 14px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .sidebar-footer-name {
            font-size: 12.5px;
            font-weight: 600;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-footer-email {
            font-size: 11px;
            color: rgba(255, 255, 255, .45);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-logout {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, .2);
            background: transparent;
            color: rgba(255, 255, 255, .7);
            cursor: pointer;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background .2s ease, color .2s ease;
        }

        .sidebar-logout:hover {
            background: rgba(255, 255, 255, .15);
            color: #ffffff;
        }

        /* ══════════════════════════════
           MAIN AREA
        ══════════════════════════════ */
        .main-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* ── NAVBAR ── */
        .navbar {
            height: var(--navbar-h);
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            gap: 16px;
            flex-shrink: 0;
        }

        .navbar-toggle {
            display: none;
            border: none;
            background: transparent;
            font-size: 18px;
            color: var(--blue);
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 8px;
        }

        .navbar-toggle:hover {
            background: var(--blue-light);
        }

        .navbar-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--blue);
            flex: 1;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .navbar-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 999px;
            background: var(--blue-light);
            color: var(--blue);
            font-size: 12px;
            font-weight: 700;
        }

        .navbar-username {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--text);
        }

        /* ── CONTENT ── */
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 28px 32px;
        }

        /* ══════════════════════════════
           RESPONSIVE (mobile)
         ══════════════════════════════ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(26, 40, 64, 0.4);
            z-index: 99;
            backdrop-filter: blur(2px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.open {
            display: block;
            opacity: 1;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: 0;
                top: 0;
                bottom: 0;
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .navbar-toggle {
                display: flex;
            }

            .main-content {
                padding: 20px 16px;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                padding: 0 12px;
            }
            .navbar-badge {
                display: none !important;
            }
            .navbar-username {
                font-size: 0 !important;
            }
            .navbar-username i {
                font-size: 20px !important;
                margin-right: 0 !important;
            }
        }

        /* ══════════════════════════════
           UTILITIES
        ══════════════════════════════ */
        .card {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
            border: 1px solid var(--border);
        }

        .card-body {
            padding: 28px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--blue);
            margin-bottom: 6px;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text);
            background: #f8fbff;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(0, 77, 153, .1);
            background: #ffffff;
        }

        .error-msg {
            font-size: 12px;
            color: #dc2626;
            margin-top: 5px;
        }

        .alert-success {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #ecfdf5;
            border: 1.5px solid #6ee7b7;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 13px;
            color: #065f46;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 11px 22px;
            border-radius: 10px;
            border: none;
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .15s;
        }

        .btn:active {
            transform: none;
        }

        .btn-primary {
            background: var(--blue);
            color: #ffffff;
        }

        .btn-primary:hover {
            background: var(--blue-dark);
            transform: translateY(-1px);
        }

        .btn-yellow {
            background: var(--yellow);
            color: var(--blue-dark);
        }

        .btn-yellow:hover {
            background: var(--yellow-dark);
            transform: translateY(-1px);
        }

        /* ══════════════════════════════
           SIDEBAR DROPDOWN
        ══════════════════════════════ */
        /* ══════════════════════════════
           SIDEBAR DROPDOWN
         ══════════════════════════════ */
        .sidebar-dropdown {
            margin-bottom: 4px;
        }

        .sidebar-dropdown-toggle {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 16px;
            border-radius: 12px;
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 3px solid transparent;
        }

        .sidebar-dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            padding-left: 19px;
            border-left-color: rgba(255, 255, 255, 0.3);
        }

        .sidebar-dropdown-toggle:hover .sidebar-dropdown-left i {
            background: rgba(255, 255, 255, 0.15);
            transform: scale(1.05);
        }

        .sidebar-dropdown.open .sidebar-dropdown-toggle {
            background: rgba(255, 255, 255, 0.06);
            color: #ffffff;
            border-left-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar-dropdown-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-dropdown-left i {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.07);
            font-size: 13px;
            transition: all .25s ease;
            flex-shrink: 0;
        }

        .sidebar-chevron {
            font-size: 11px;
            transition: transform .25s ease;
            color: rgba(255, 255, 255, 0.5);
            margin-right: 2px;
        }

        .sidebar-dropdown.open .sidebar-chevron {
            transform: rotate(90deg);
            color: #ffffff;
        }

        @keyframes dropdownSlide {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .sidebar-dropdown-menu {
            display: none;
            flex-direction: column;
            padding: 4px 0 4px 14px;
        }

        .sidebar-dropdown.open .sidebar-dropdown-menu {
            display: flex;
            animation: dropdownSlide 0.2s ease forwards;
        }

        .sidebar-sub-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            border-radius: 8px;
            text-decoration: none;
            color: rgba(255, 255, 255, .68);
            font-size: 13px;
            font-weight: 500;
            transition: all .2s ease;
            border-left: 2px solid rgba(255, 255, 255, 0.15);
            margin-bottom: 2px;
            margin-left: 14px;
        }

        .sidebar-sub-link i {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            transition: all .2s ease;
        }

        .sidebar-sub-link:hover {
            background: rgba(255, 255, 255, .05);
            color: #ffffff;
            border-left-color: var(--yellow);
            padding-left: 17px;
        }

        .sidebar-sub-link:hover i {
            color: #ffffff;
        }

        .sidebar-sub-link.active {
            background: var(--yellow);
            color: var(--blue-dark);
            font-weight: 700;
            border-left-color: var(--yellow-dark);
            padding-left: 17px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        @stack('styles')
    </style>
    @stack('styles')
</head>

<body>
    <div class="app-shell">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        {{-- Sidebar --}}
        @include('admin.layouts.sidebar')

        <div class="main-area">

            {{-- Navbar --}}
            @include('admin.layouts.navbar')

            {{-- Page content --}}
            <main class="main-content">
                @yield('content')
            </main>

        </div>
    </div>

    <script>
        function toggleDropdown(btn) {
            btn.closest('.sidebar-dropdown').classList.toggle('open');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('open');
                    sidebarOverlay.classList.toggle('open');
                });

                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    sidebarOverlay.classList.remove('open');
                });
            }
        });
    </script>

    @stack('scripts')

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Succès !',
                text: "{{ session('success') }}",
                confirmButtonColor: '#004d99',
                timer: 3000,
                timerProgressBar: true
            });
        </script>
    @endif
</body>

</html>
