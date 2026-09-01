<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser votre mot de passe — Espace Agence — DoyaImmo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --rust: #B85C3A;
            --gold: #D4AF37;
            --ink: #1A1A2E;
            --text-soft: #4A4A6A;
            --muted: #8A8AA0;
            --border: #E8E8F0;
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
            gap: 12px;
            font-weight: 700;
            font-size: 20px;
            letter-spacing: -0.5px;
        }

        .brand-mark {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--rust);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 22px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .brand-name {
            color: #fff;
        }

        .brand-name span {
            color: var(--gold);
        }

        .quote {
            color: rgba(255,255,255,0.85);
            font-size: 18px;
            line-height: 1.7;
            max-width: 320px;
            margin: 0;
            font-weight: 400;
        }

        .auth-stats {
            display: flex;
            gap: 40px;
            color: rgba(255,255,255,0.7);
        }

        .auth-stats b {
            display: block;
            font-size: 26px;
            color: #fff;
            font-weight: 800;
        }

        .auth-stats span {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

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

        .auth-box .brand-mark {
            background: var(--rust);
            box-shadow: none;
        }

        .auth-box .brand-name {
            color: var(--ink);
        }

        .auth-box .brand-name span {
            color: var(--rust);
        }

        .auth-box h1 {
            font-size: 26px;
            font-weight: 700;
            color: var(--ink);
            margin: 0 0 8px;
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
            margin-bottom: 6px;
        }

        .field input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s;
            background: #FAFBFC;
        }

        .field input:focus {
            outline: none;
            border-color: var(--rust);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(184, 92, 58, 0.15);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            border-radius: 10px;
            font-size: 15px;
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

        .btn-rust {
            background: var(--rust);
            color: #fff;
        }

        .btn-rust:hover {
            background: #9A4523;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(184, 92, 58, 0.35);
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #FFEBEE;
            color: #C62828;
            border: 1px solid #FFCDD2;
        }

        @media (max-width: 820px) {
            .auth-shell {
                flex-direction: column;
                max-width: 420px;
                margin: 20px;
            }

            .auth-visual {
                min-height: 220px;
                padding: 28px 24px;
            }

            .quote {
                font-size: 15px;
            }

            .auth-stats {
                gap: 24px;
            }

            .auth-stats b {
                font-size: 22px;
            }

            .auth-form-side {
                padding: 32px 24px;
            }
        }

        @media (max-width: 480px) {
            .auth-visual {
                min-height: 180px;
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
                font-size: 22px;
            }

            .brand-mark {
                width: 34px;
                height: 34px;
                font-size: 18px;
            }
        }
    </style>
</head>
<body>

<div class="auth-shell">
    <!-- Panneau visuel -->
    <div class="auth-visual">
        <div class="brand">
            <div class="brand-mark">D</div>
            <div class="brand-name">Doya<span>Immo</span></div>
        </div>
        <div>
            <p class="quote">Créez un nouveau mot de passe pour sécuriser votre espace agence.</p>
        </div>
        <div class="auth-stats">
            <div>
                <b></b>
                <span>Chiffré</span>
            </div>
            <div>
                <b></b>
                <span>Sécurisé</span>
            </div>
        </div>
    </div>

    <!-- Panneau formulaire -->
    <div class="auth-form-side">
        <div class="auth-box">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span>Immo</span></div>
            </div>
            <h1>Nouveau mot de passe</h1>
            <p class="sub">Choisissez un mot de passe sécurisé pour votre compte agence.</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('agence.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="field">
                    <label for="email">Email professionnel</label>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly style="background-color: #f0f0f0; color: #888; cursor: not-allowed;">
                </div>

                <div class="field">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required minlength="8">
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                </div>

                <button class="btn btn-rust btn-block" type="submit">
                    <i class="fa-solid fa-key"></i> Réinitialiser le mot de passe
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>