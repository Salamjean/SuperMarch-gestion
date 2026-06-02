<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarché Pro — Gestion</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background-color: #004d99;
            overflow: hidden;
        }

        /* ── Page layout ── */
        .page {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            gap: 0;
        }

        /* ── Top stripe accent ── */
        .top-stripe {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: #ffc300;
        }

        /* ── Logo area ── */
        .logo-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            margin-bottom: 10px;
            animation: fadeDown .5s ease both;
        }

        .logo-icon {
            width: 76px;
            height: 76px;
            border-radius: 20px;
            background: #ffc300;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .25);
        }

        .app-title {
            font-size: 38px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -.02em;
            text-align: center;
        }

        .app-title span {
            color: #ffc300;
        }

        .app-subtitle {
            font-size: 14px;
            color: rgba(255, 255, 255, .65);
            font-weight: 400;
            text-align: center;
            margin-top: 2px;
        }

        /* ── Divider ── */
        .divider {
            width: 48px;
            height: 3px;
            background: #ffc300;
            border-radius: 999px;
            margin: 28px 0;
            animation: fadeDown .5s ease .1s both;
        }

        /* ── Cards ── */
        .cards {
            display: flex;
            gap: 20px;
            animation: fadeUp .55s ease .2s both;
        }

        .login-card {
            width: 240px;
            background: #ffffff;
            border-radius: 16px;
            padding: 32px 24px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            text-decoration: none;
            color: #004d99;
            box-shadow: 0 6px 24px rgba(0, 0, 0, .18);
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .login-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(0, 0, 0, .25);
        }

        /* Card icon */
        .card-icon {
            width: 62px;
            height: 62px;
            border-radius: 14px;
            background: #004d99;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            color: #ffffff;
            flex-shrink: 0;
        }

        .login-card:hover .card-icon {
            background: #ffc300;
            color: #004d99;
        }

        .card-icon {
            transition: background .25s ease, color .25s ease;
        }

        /* Card text */
        .card-label {
            font-size: 17px;
            font-weight: 700;
            color: #004d99;
            text-align: center;
        }

        .card-desc {
            font-size: 12.5px;
            color: #5a7a99;
            text-align: center;
            line-height: 1.55;
        }

        /* Button */
        .card-btn {
            margin-top: 4px;
            width: 100%;
            padding: 11px 0;
            border-radius: 10px;
            border: 2px solid #004d99;
            background: transparent;
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 600;
            color: #004d99;
            cursor: pointer;
            transition: background .2s ease, color .2s ease;
            letter-spacing: .01em;
        }

        .login-card:hover .card-btn {
            background: #004d99;
            color: #ffffff;
        }

        .card-btn i {
            margin-left: 6px;
        }

        /* ── Footer ── */
        .footer {
            position: fixed;
            bottom: 18px;
            font-size: 12px;
            color: rgba(255, 255, 255, .45);
            animation: fadeUp .55s ease .4s both;
        }

        .footer span {
            color: #ffc300;
        }

        /* ── Animations ── */
        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>

    <div class="top-stripe"></div>

    <main class="page">

        <!-- Logo -->
        <div class="logo-area">
            <div class="logo-icon">🛒</div>
            <div>
                <h1 class="app-title">Supermarché <span>Pro</span></h1>
                <p class="app-subtitle">Plateforme de gestion intégrée</p>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Cards -->
        <div class="cards">

            <!-- Admin -->
            <a href="{{ route('login') }}" class="login-card">
                <div class="card-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <p class="card-label">Administrateur</p>
                    <p class="card-desc">Gestion des stocks,<br>ventes &amp; rapports</p>
                </div>
                <button class="card-btn">
                    Se connecter <i class="fa-solid fa-arrow-right"></i>
                </button>
            </a>

            <!-- Employé -->
            <a href="{{ route('login') }}" class="login-card">
                <div class="card-icon">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div>
                    <p class="card-label">Employé</p>
                    <p class="card-desc">Caisse, inventaire<br>&amp; opérations courantes</p>
                </div>
                <button class="card-btn">
                    Se connecter <i class="fa-solid fa-arrow-right"></i>
                </button>
            </a>

        </div>

    </main>

    <footer class="footer">
        &copy; {{ date('Y') }} <span>SuperMarché Pro</span> — Tous droits réservés
    </footer>

</body>

</html>
