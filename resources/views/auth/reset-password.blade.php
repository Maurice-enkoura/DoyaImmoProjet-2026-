<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser votre mot de passe — DoyaImmo</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="auth-shell">
    <div class="auth-visual">
        <div class="brand">
            <div class="brand-mark">D</div>
            <div class="brand-name">Doya<span style="color:var(--gold)">Immo</span></div>
        </div>
        <div>
            <p class="quote">Créez un nouveau mot de passe pour sécuriser votre compte.</p>
        </div>
        <div class="auth-stats">
            <div><b></b><span>Chiffré</span></div>
            <div><b></b><span>Sécurisé</span></div>
        </div>
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span style="color:var(--rust)">Immo</span></div>
            </div>
            <h1>Nouveau mot de passe</h1>
            <p class="sub">Choisissez un mot de passe sécurisé pour votre compte.</p>

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="field">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly>
                </div>

                <div class="field">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required minlength="8">
                </div>

                <div class="field">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                </div>

                <button class="btn btn-rust btn-block btn-lg" type="submit">
                    <i class="fa-solid fa-key"></i> Réinitialiser le mot de passe
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>