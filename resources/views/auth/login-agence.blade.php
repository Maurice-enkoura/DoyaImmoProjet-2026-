@extends('layouts.auth')

@section('title', 'Connexion Agence — DoyaImmo')

@section('content')
<div class="auth-shell">
    <div class="auth-visual">
        <div class="brand" style="display:flex; justify-content:space-between; align-items:center;">
            <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit;">
                <div class="brand-mark">D</div>
                <div class="brand-name" style="color:#fff;">Doya<span style="color:var(--gold)">Immo</span></div>
            </a>
            
            <!-- Bouton Accueil -->
            <a href="{{ route('home') }}" class="btn btn-ghost btn-sm" style="color:#fff; border-color:rgba(255,255,255,0.3);">
                <i class="fa-solid fa-house"></i> Accueil
            </a>
        </div>
        
        <!-- Statistiques -->
        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;gap:24px;">
            <!-- Citation -->
            <div>
                <p class="quote" style="font-size:20px;margin:0;">
                    « Depuis qu'on est sur DoyaImmo, on reçoit des demandes qualifiées chaque semaine, sans démarchage. »
                </p>
                <p class="quote-by" style="margin-top:8px;">— Teranga Immobilier, agence partenaire</p>
            </div>

            <!-- Statistiques dynamiques -->
            <div class="auth-stats" style="margin-top:0;">
                <div>
                    <b style="font-size:28px;">{{ $stats['agences'] ?? 0 }}+</b>
                    <span style="font-size:13px;color:#9AA1AB;">Agences actives</span>
                </div>
                <div>
                    <b style="font-size:28px;">{{ $stats['offres_moyenne'] ?? 42 }}</b>
                    <span style="font-size:13px;color:#9AA1AB;">Offres/mois en moyenne</span>
                </div>
                <div>
                    <b style="font-size:28px;">{{ number_format($stats['note_moyenne'] ?? 0, 1) }}★</b>
                    <span style="font-size:13px;color:#9AA1AB;">Note moyenne agences</span>
                </div>
            </div>
        </div>

        <!-- Footer visuel -->
        <div style="font-size:12px;color:#6A7280;margin-top:20px;">
            <i class="fa-regular fa-circle-check" style="color:var(--gold);"></i>
            {{ $stats['besoins'] ?? 0 }} besoins actifs en attente de réponse
        </div>
    </div>

    <div class="auth-form-side">
        <div class="auth-box">
            <div class="brand" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit;">
                    <div class="brand-mark">D</div>
                    <div class="brand-name">Doya<span style="color:var(--rust)">Immo</span></div>
                </a>
                
                <!-- Bouton Accueil -->
                <a href="{{ route('home') }}" class="btn btn-ghost btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
                    <i class="fa-solid fa-house"></i> Accueil
                </a>
            </div>

            <div class="role-toggle">
                <a href="{{ route('login') }}" class="{{ request('type') != 'agence' ? 'active' : '' }}">
                    <i class="fa-solid fa-user"></i> Espace client
                </a>
                <a href="{{ route('login.agence') }}" class="{{ request('type') == 'agence' ? 'active' : '' }}">
                    <i class="fa-solid fa-building"></i> Espace agence
                </a>
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
                        <a href="{{ route('agence.password.request') }}" style="color:var(--rust); font-weight:600;font-size:12px;">Oublié ?</a>
                    </label>
                    <div class="password-wrapper">
                        <input type="password" 
                               id="password" 
                               name="mot_de_passe" 
                               placeholder="••••••••" 
                               required>
                        <button type="button" class="toggle-password" aria-label="Afficher le mot de passe" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
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

@push('scripts')
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush
