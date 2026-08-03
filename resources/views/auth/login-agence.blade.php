@extends('layouts.auth')

@section('title', 'Connexion Agence — DoyaImmo')

@section('content')
<div class="auth-shell">
    <div class="auth-visual">
        <div class="brand">
            <div class="brand-mark">D</div>
            <div class="brand-name" style="color:#fff;">Doya<span style="color:var(--gold)">Immo</span></div>
        </div>
        <div>
            <p class="quote">« Depuis qu'on est sur DoyaImmo, on reçoit des demandes qualifiées chaque semaine, sans démarchage. »</p>
            <p class="quote-by">— Teranga Immobilier, agence partenaire</p>
        </div>
        <div class="auth-stats">
            <div><b>180+</b><span>Agences actives</span></div>
            <div><b>42</b><span>Offres envoyées / mois en moyenne</span></div>
            <div><b>4.6★</b><span>Note moyenne agences</span></div>
        </div>
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span style="color:var(--rust)">Immo</span></div>
            </div>

            <div class="role-toggle">
                <a href="{{ route('login') }}">Espace client</a>
                <a href="{{ route('login.agence') }}" class="active">Espace agence</a>
            </div>

            <h1>Espace agence</h1>
            <p class="sub">Connectez-vous pour gérer vos besoins, offres et rendez-vous.</p>

            <!-- Flash messages -->
            @if(session('error'))
                <div class="flash-message flash-error" style="margin-bottom:16px;">
                    <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="flash-message flash-info" style="margin-bottom:16px;">
                    <i class="fa-solid fa-info-circle"></i> {{ session('info') }}
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
                <input type="hidden" name="type" value="agence">

                <div class="field">
                    <label for="email">Email professionnel</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           placeholder="contact@votreagence.sn" 
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

            <div style="margin-top:16px;padding:12px;background:#FFF8E1;border-radius:10px;border:1px solid #FFE0B2;">
                <p style="font-size:12px;color:#E65100;margin:0;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-clock"></i>
                    <span>Votre agence doit être validée par un administrateur avant de pouvoir publier des biens.</span>
                </p>
            </div>

            <p class="auth-foot-link" style="margin-top:20px;">
                Votre agence n'est pas encore inscrite ? 
                <a href="{{ route('register.agence') }}">Créer un compte agence</a>
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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
    .flash-info {
        background: #E3F2FD;
        color: #0D47A1;
        border: 1px solid #BBDEFB;
    }
    .flash-success {
        background: #E8F5E9;
        color: #1E7A47;
        border: 1px solid #C8E6C9;
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
</style>
@endpush