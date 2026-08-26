<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié — Espace Agence — DoyaImmo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ==================== AUTH SHELL ==================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --rust: #B85C3A;
            --rust-soft: #F5E6DF;
            --gold: #D4AF37;
            --gold-soft: #FDF5E6;
            --teal: #2A9D8F;
            --teal-soft: #E6F4F2;
            --ink: #1A1A2E;
            --text-soft: #4A4A6A;
            --muted: #8A8AA0;
            --border: #E8E8F0;
            --green: #2E7D32;
            --green-soft: #E8F5E9;
            --radius: 16px;
            --display: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            font-family: var(--display);
            background: #F7F9FC;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-shell {
            display: flex;
            max-width: 1000px;
            width: 100%;
            background: #fff;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0,0,0,0.08);
            border: 1px solid var(--border);
        }

        /* ==================== VISUAL ==================== */
        .auth-visual {
            flex: 1;
            background: var(--ink);
            padding: 40px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 480px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            font-size: 18px;
        }

        .brand-mark {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--gold);
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 16px;
        }

        .brand-name {
            color: #fff;
        }

        .quote {
            color: rgba(255,255,255,0.7);
            font-size: 18px;
            line-height: 1.6;
            max-width: 300px;
            margin: 0;
        }

        .auth-stats {
            display: flex;
            gap: 40px;
            color: rgba(255,255,255,0.6);
        }

        .auth-stats b {
            display: block;
            font-size: 24px;
            color: #fff;
            font-weight: 700;
        }

        .auth-stats span {
            font-size: 12px;
        }

        /* ==================== FORM ==================== */
        .auth-form-side {
            flex: 1;
            padding: 48px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-box {
            width: 100%;
            max-width: 360px;
        }

        .auth-box .brand {
            margin-bottom: 24px;
        }

        .auth-box h1 {
            font-family: var(--display);
            font-size: 24px;
            font-weight: 700;
            color: var(--ink);
            margin: 0 0 6px;
        }

        .auth-box .sub {
            color: var(--muted);
            font-size: 14px;
            margin-bottom: 28px;
            line-height: 1.6;
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-soft);
            margin-bottom: 4px;
        }

        .field input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: border 0.2s;
            background: #FAFBFC;
        }

        .field input:focus {
            outline: none;
            border-color: var(--rust);
            background: #fff;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            text-decoration: none;
        }

        .btn-block {
            width: 100%;
        }

        .btn-lg {
            padding: 14px 28px;
            font-size: 15px;
        }

        .btn-rust {
            background: var(--rust);
            color: #fff;
        }

        .btn-rust:hover {
            background: #9A4523;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(184, 92, 58, 0.3);
        }

        .auth-foot-link {
            text-align: center;
            font-size: 13px;
            color: var(--muted);
            margin-top: 24px;
        }

        .auth-foot-link a {
            color: var(--rust);
            font-weight: 600;
            text-decoration: none;
        }

        .auth-foot-link a:hover {
            text-decoration: underline;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 820px) {
            .auth-shell {
                flex-direction: column;
                max-width: 420px;
                margin: 20px;
            }

            .auth-visual {
                min-height: 200px;
                padding: 28px 24px;
            }

            .quote {
                font-size: 15px;
            }

            .auth-stats {
                gap: 24px;
            }

            .auth-form-side {
                padding: 32px 24px;
            }
        }

        @media (max-width: 480px) {
            .auth-visual {
                min-height: 160px;
                padding: 20px;
            }

            .quote {
                font-size: 13px;
            }

            .auth-stats b {
                font-size: 18px;
            }

            .auth-form-side {
                padding: 24px 16px;
            }

            .auth-box h1 {
                font-size: 20px;
            }

            .field input {
                padding: 10px 14px;
                font-size: 13px;
            }

            .btn-lg {
                padding: 12px 20px;
                font-size: 14px;
            }
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: var(--green-soft);
            color: var(--green);
            border: 1px solid #C8E6C9;
        }

        .alert-danger {
            background: #FFEBEE;
            color: #C62828;
            border: 1px solid #FFCDD2;
        }
    </style>
</head>
<body>

<div class="auth-shell">
    <div class="auth-visual">
        <div class="brand">
            <div class="brand-mark">D</div>
            <div class="brand-name">Doya<span style="color:var(--gold)">Immo</span></div>
        </div>
        <div>
            <p class="quote">Pas de souci, ça arrive à tout le monde. On vous renvoie l'accès en 2 minutes.</p>
        </div>
        <div class="auth-stats">
            <div><b>2 min</b><span>Délai moyen de réception</span></div>
            <div><b>100%</b><span>Sécurisé</span></div>
        </div>
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span style="color:var(--rust)">Immo</span></div>
            </div>
            <h1>Mot de passe oublié</h1>
            <p class="sub">Indiquez l'email professionnel de votre agence, nous vous enverrons un lien de réinitialisation.</p>

            @if(session('status'))
                <div class="alert alert-success">
                    <i class="fa-solid fa-check-circle"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('agence.password.email') }}">
                @csrf
                <div class="field">
                    <label for="email">Email professionnel</label>
                    <input type="email" id="email" name="email" placeholder="contact@votreagence.sn" value="{{ old('email') }}" required autofocus>
                </div>
                <button class="btn btn-rust btn-block btn-lg" type="submit">
                    <i class="fa-solid fa-paper-plane"></i> Envoyer le lien de réinitialisation
                </button>
            </form>

            <p class="auth-foot-link">
                Vous vous souvenez de votre mot de passe ?
                <a href="{{ route('login.agence') }}">Retour à la connexion</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>