<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Interface Magasinier')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo/icon.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: #004d99;
            --primary-light: #0066cc;
            --secondary: #ffc300;
            --danger: #e11d48;
            --ok: #0f766e;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --border: #e2eaf3;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .mg-app {
            height: calc(100vh - 65px);
            display: grid;
            grid-template-columns: 240px 1fr;
            overflow: hidden;
            background: var(--bg);
        }

        .mg-sidebar {
            background: #fff;
            border-right: 1px solid var(--border);
            padding: 25px 0;
            display: flex;
            flex-direction: column;
        }

        .mg-sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 25px 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 14px;
        }

        .mg-sidebar-logo i {
            background: var(--secondary);
            color: var(--primary);
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .mg-logo-title {
            font-weight: 700;
            line-height: 1.2;
            color: var(--primary);
        }

        .mg-logo-subtitle {
            font-size: 11px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .mg-sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .mg-nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 25px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 14.5px;
            transition: all 0.2s;
            border-left: 4px solid transparent;
        }

        .mg-nav-link:hover {
            background: #f1f5f9;
            color: var(--primary);
        }

        .mg-nav-link.active {
            background: #f0f7ff;
            color: var(--primary);
            border-left-color: var(--primary);
        }

        .mg-nav-link i {
            width: 20px;
            font-size: 16px;
        }

        .mg-main {
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 0;
        }

        .mg-topbar {
            height: 65px;
            background: var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 30px;
            border-bottom: 4px solid var(--secondary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            color: #fff;
            position: relative;
        }

        .mg-topbar-brand {
            font-size: 18px;
            font-weight: 800;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .mg-topbar-brand span {
            color: var(--secondary);
        }

        .mg-topbar-brand i {
            background: var(--secondary);
            color: var(--primary);
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .mg-topbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-left: auto;
            z-index: 2;
        }

        .mg-topbar-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
            max-width: 40vw;
        }

        .mg-user-info {
            text-align: right;
        }

        .mg-user-name {
            font-size: 14px;
            font-weight: 700;
            line-height: 1.1;
            color: #fff;
        }

        .mg-user-role {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 2px;
        }

        .mg-page-context {
            color: rgba(255, 255, 255, 0.9);
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid rgba(255, 255, 255, 0.22);
            background: rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            padding: 8px 14px;
            text-align: center;
        }

        .mg-logout-btn {
            border: none;
            background: var(--secondary);
            color: var(--primary);
            border: 1px solid #e6b000;
            border-radius: 9px;
            padding: 10px 14px;
            cursor: pointer;
            font-weight: 600;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .mg-logout-btn:hover {
            background: #e6b000;
        }

        .mg-topbar-logout-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 8px 15px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .mg-topbar-logout-btn:hover {
            background: var(--danger);
            border-color: var(--danger);
        }

        .mg-content {
            padding: 25px;
            overflow: auto;
            min-height: 0;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(2, 24, 53, 0.04);
        }

        .mg-sidebar-footer {
            margin-top: auto;
            padding: 20px 25px;
            border-top: 1px solid var(--border);
        }

        .mg-clock {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary);
        }

        .mg-date {
            font-size: 12px;
            color: var(--muted);
        }

        @media (max-width: 980px) {
            body {
                overflow: auto;
            }

            .mg-app {
                grid-template-columns: 1fr;
                height: auto;
            }

            .mg-sidebar {
                border-right: none;
                border-bottom: 1px solid var(--border);
                padding: 14px 0 10px;
            }

            .mg-topbar {
                padding: 0 16px;
                gap: 12px;
            }

            .mg-topbar-brand {
                font-size: 15px;
                gap: 8px;
            }

            .mg-topbar-brand i {
                width: 30px;
                height: 30px;
                font-size: 14px;
            }

            .mg-sidebar-logo {
                padding-bottom: 12px;
                margin-bottom: 10px;
            }

            .mg-sidebar-nav {
                flex-direction: row;
                gap: 8px;
                overflow-x: auto;
                padding: 0 12px;
                scrollbar-width: thin;
            }

            .mg-nav-link {
                border-left: none;
                border-radius: 10px;
                padding: 10px 12px;
                font-size: 12px;
                white-space: nowrap;
            }

            .mg-nav-link.active {
                border-left-color: transparent;
            }

            .mg-sidebar-footer {
                display: none;
            }

            .mg-topbar-center,
            .mg-user-role {
                display: none;
            }

            .mg-user-name {
                font-size: 13px;
            }

            .mg-topbar-logout-btn {
                padding: 7px 10px;
                font-size: 12px;
            }

            .mg-topbar-logout-btn span {
                display: none;
            }

            .mg-content {
                padding: 14px;
            }
        }

        /* ── Unified List & Data Table Styles (Admin Alignment) ── */
        .list-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .list-title {
            font-size: 17px;
            font-weight: 800;
            color: #004d99;
            margin: 0 0 3px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .list-sub {
            font-size: 12.5px;
            color: #7a94aa;
            margin: 0;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13.5px;
        }

        .data-table thead tr {
            background: #f5f9ff;
            border-bottom: 1.5px solid #e2eaf3;
        }

        .data-table th {
            padding: 11px 16px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #7a94aa;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #f0f4f8;
            transition: background .15s;
        }

        .data-table tbody tr:hover {
            background: #f8fbff;
        }

        .data-table td {
            padding: 12px 16px;
            vertical-align: middle;
        }

        .td-id {
            color: #a0b5c8;
            font-size: 12px;
            font-weight: 600;
        }

        .td-name {
            font-weight: 600;
            color: #1a2e44;
        }

        .td-muted {
            color: #7a94aa;
        }

        .td-actions {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .badge-green {
            background: #e8f9f0;
            color: #1a9e5a;
        }

        .badge-gray {
            background: #f0f4f8;
            color: #7a94aa;
        }

        .badge-blue {
            background: #eef4ff;
            color: #004d99;
            border: 1px solid rgba(0,77,153,0.1);
        }

        .btn-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            border: 1px solid #e2eaf3;
            background: #fff;
            color: #004d99;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s;
        }

        .btn-icon:hover {
            background: #eef4ff;
        }

        .btn-icon-red {
            color: #e11d48;
        }

        .btn-icon-red:hover {
            background: #fff1f2;
            border-color: #ffd0d0;
        }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #a0b5c8;
            font-size: 14px;
        }

        .empty-state i {
            font-size: 36px;
            margin-bottom: 12px;
            display: block;
        }

    </style>
    @stack('styles')
</head>

<body>
    @php
        $frDays = [
            'Sunday' => 'Dimanche',
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
        ];

        $frMonths = [
            1 => 'Janvier',
            2 => 'Fevrier',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Aout',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Decembre',
        ];

        $now = now();
        $todayLabel =
            ($frDays[$now->englishDayOfWeek] ?? $now->englishDayOfWeek) .
            ' ' .
            $now->format('d') .
            ' ' .
            ($frMonths[(int) $now->format('n')] ?? $now->format('F')) .
            ' ' .
            $now->format('Y');
    @endphp

    @include('magasinier.layouts.navbar')

    <div class="mg-app">
        @include('magasinier.layouts.sidebar')

        <main class="mg-main">
            <section class="mg-content">
                @yield('content')
            </section>
        </main>
    </div>

    <script>
        (function() {
            const clockEl = document.getElementById('mg-live-clock');
            const dateEl = document.getElementById('mg-live-date');
            if (!clockEl || !dateEl) return;

            const defaultDate = @json($todayLabel);

            function updateClock() {
                const now = new Date();
                clockEl.textContent = now.toLocaleTimeString('fr-FR', {
                    hour12: false
                });
                dateEl.textContent = defaultDate;
            }

            updateClock();
            setInterval(updateClock, 1000);
        })();
    </script>
    @stack('scripts')
</body>

</html>
