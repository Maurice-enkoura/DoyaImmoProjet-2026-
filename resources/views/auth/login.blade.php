@extends('layouts.auth')

@section('title', 'Connexion — DoyaImmo')

@section('content')
<div class="auth-shell">
    <div class="auth-visual">
        <div class="brand">
            <div class="brand-mark">D</div>
            <div class="brand-name" style="color:#fff;">Doya<span style="color:var(--gold)">Immo</span></div>
        </div>
        <div>
            <p class="quote">« En 3 jours j'ai reçu 4 propositions correspondant exactement à ce que je cherchais. »</p>
            <p class="quote-by">— Fatou N., cliente à Almadies</p>
        </div>
        <div class="auth-stats">
            <div><b>1 200+</b><span>Besoins publiés</span></div>
            <div><b>180+</b><span>Agences inscrites</span></div>
            <div><b>4.6★</b><span>Satisfaction moyenne</span></div>
        </div>
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span style="color:var(--rust)">Immo</span></div>
            </div>

            <div class="role-toggle">
                <a href="{{ route('login') }}" class="active">Espace client</a>
                <a href="{{ route('login') }}?type=agence">Espace agence</a>
            </div>

            <h1>Content de vous revoir</h1>
            <p class="sub">Connectez-vous pour suivre vos demandes et vos rendez-vous.</p>

            <!-- Flash messages -->
            @if(session('error'))
                <div class="flash-message flash-error" style="margin-bottom:16px;">
                    <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="flash-message flash-error" style="margin-bottom:16px;">
                    <i class="fa-solid fa-exclamation-circle"></i> 
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="hidden" name="type" value="{{ request('type', 'particulier') }}">

                <div class="field">
                    <label for="email">Adresse email</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="vous@exemple.com" 
                           required 
                           autofocus>
                    @error('email')
                        <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                    @enderror
                </div>

                <div class="field">
                    <label style="display:flex; justify-content:space-between;">
                        Mot de passe 
                        <a href="#" style="color:var(--rust); font-weight:600;font-size:12px;">Oublié ?</a>
                    </label>
                    <input type="password" 
                           id="password" 
                           name="mot_de_passe" 
                           placeholder="••••••••" 
                           required>
                    @error('mot_de_passe')
                        <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                    @enderror
                </div>

                <label class="check-row" style="margin-bottom:20px;">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Rester connecté sur cet appareil
                </label>

                <button type="submit" class="btn btn-rust btn-block btn-lg">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Se connecter
                </button>
            </form>

            <div class="auth-divider">ou</div>
            <button class="btn btn-ghost btn-block" onclick="alert('Fonctionnalité à venir')">
                <i class="fa-brands fa-google"></i> Continuer avec Google
            </button>

            <p class="auth-foot-link">
                Pas encore de compte ? 
                <a href="{{ route('register') }}">Créer un compte client</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .auth-divider {
        display: flex;
        align-items: center;
        gap: 16px;
        margin: 20px 0;
        color: var(--muted);
        font-size: 13px;
    }
    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: var(--border);
    }
    .field {
        margin-bottom: 16px;
    }
    .field label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-soft);
        margin-bottom: 4px;
    }
    .field input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        font-family: inherit;
        transition: border 0.2s;
    }
    .field input:focus {
        outline: none;
        border-color: var(--rust);
    }
    .field input.is-invalid {
        border-color: #C62828;
    }
    .check-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-soft);
        cursor: pointer;
    }
    .check-row input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
    }
    .flash-message {
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .flash-error {
        background: #FFEBEE;
        color: #C62828;
        border: 1px solid #FFCDD2;
    }
    .flash-success {
        background: #E8F5E9;
        color: #1E7A47;
        border: 1px solid #C8E6C9;
    }
    .flash-info {
        background: #E3F2FD;
        color: #0D47A1;
        border: 1px solid #BBDEFB;
    }
</style>
@endpush