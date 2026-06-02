<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin — SuperMarché Pro</title>
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

        .top-stripe {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: #ffc300;
        }

        .page {
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            animation: fadeUp .5s ease both;
        }

        /* Card */
        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 40px 36px 36px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .22);
        }

        /* Header */
        .card-header {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            margin-bottom: 28px;
        }

        .icon-wrap {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background: #004d99;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #ffffff;
        }

        .card-header h1 {
            font-size: 22px;
            font-weight: 800;
            color: #004d99;
            text-align: center;
        }

        .card-header p {
            font-size: 13px;
            color: #7a94aa;
            text-align: center;
            margin-top: -6px;
        }

        /* Form */
        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #004d99;
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
            color: #7a94aa;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 1.5px solid #d0dce8;
            border-radius: 10px;
            font-family: inherit;
            font-size: 14px;
            color: #003366;
            background: #f5f9ff;
            transition: border-color .2s ease, box-shadow .2s ease;
            outline: none;
        }

        input:focus {
            border-color: #004d99;
            box-shadow: 0 0 0 3px rgba(0, 77, 153, .12);
            background: #ffffff;
        }

        .error-msg {
            font-size: 12px;
            color: #dc2626;
            margin-top: 5px;
        }

        /* Checkbox */
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
        }

        .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #004d99;
            cursor: pointer;
        }

        .remember label {
            margin: 0;
            font-weight: 400;
            color: #5a7a99;
            font-size: 13px;
            cursor: pointer;
        }

        /* Submit */
        .btn-submit {
            width: 100%;
            padding: 13px 0;
            border-radius: 10px;
            border: none;
            background: #004d99;
            color: #ffffff;
            font-family: inherit;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s ease, transform .15s ease;
        }

        .btn-submit:hover {
            background: #003d7a;
            transform: translateY(-1px);
        }

        .btn-submit:active {
            transform: none;
        }

        /* Back link */
        .back-link {
            margin-top: 18px;
            font-size: 13px;
            color: rgba(255, 255, 255, .7);
            text-align: center;
        }

        .back-link a {
            color: #ffc300;
            font-weight: 600;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
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
        <div class="card">

            <div class="card-header">
                <div class="icon-wrap">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h1>Espace Administrateur</h1>
                <p>Connectez-vous pour accéder au tableau de bord</p>
            </div>

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="admin@supermarche.com" autocomplete="email" autofocus>
                    </div>
                    @error('email')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••"
                            autocomplete="current-password">
                    </div>
                    @error('password')
                        <p class="error-msg">{{ $message }}</p>
                    @enderror
                </div>

                <div class="remember">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Se souvenir de moi</label>
                </div>

                <button type="submit" class="btn-submit">
                    Se connecter <i class="fa-solid fa-arrow-right" style="margin-left:6px"></i>
                </button>
            </form>
        </div>

        <p class="back-link">
            <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left" style="margin-right:4px"></i>Retour à
                l'accueil</a>
        </p>
    </main>
</body>

</html>
