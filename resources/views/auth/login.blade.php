@extends('layouts.auth')

@section('title', 'Connexion — DoyaImmo')

@section('content')
<div class="auth-shell" style="opacity:0;">
    <!-- Visual Side -->
    <div class="auth-visual">
        <div class="brand" style="display:flex; justify-content:space-between; align-items:center;">
            <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit;">
                <div class="brand-mark">D</div>
                <div class="brand-name" style="color:#fff;">Doya<span style="color:var(--rust)">Immo</span></div>
            </a>
            
            
        </div>
        
        <!-- Statistiques -->
        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;gap:20px;">
            <!-- Citation -->
            <div>
                <p class="quote" style="font-size:18px;margin:0;max-width:400px;">
                    « En 3 jours j'ai reçu 4 propositions correspondant exactement à ce que je cherchais. »
                </p>
                <p class="quote-by" style="margin-top:6px;font-size:13px;color:#8A91A0;">— Fatou N., cliente à Almadies</p>
            </div>

            <!-- Statistiques dynamiques -->
            <div class="auth-stats" style="margin-top:0;">
                <div>
                    <b style="font-size:24px;">{{ $stats['besoins'] ?? 0 }}+</b>
                    <span style="font-size:12px;color:#9AA1AB;">Besoins publiés</span>
                </div>
                <div>
                    <b style="font-size:24px;">{{ $stats['agences'] ?? 0 }}+</b>
                    <span style="font-size:12px;color:#9AA1AB;">Agences inscrites</span>
                </div>
                <div>
                    <b style="font-size:24px;">{{ number_format($stats['note_moyenne'] ?? 0, 1) }}★</b>
                    <span style="font-size:12px;color:#9AA1AB;">Satisfaction moyenne</span>
                </div>
            </div>
        </div>

        <!-- Footer visuel -->
        <div class="auth-visual-footer">
            <i class="fa-regular fa-circle-check"></i>
            Plus de {{ $stats['clients'] ?? 0 }} clients satisfaits à Dakar
        </div>
    </div>

    <!-- Form Side -->
    <div class="auth-form-side">
        <div class="auth-box">
            <div class="brand" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
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
                <a href="{{ route('login') }}?type=agence" class="{{ request('type') == 'agence' ? 'active' : '' }}">
                    <i class="fa-solid fa-building"></i> Espace agence
                </a>
            </div>

            <h1>Content de vous revoir</h1>
            <p class="sub">Connectez-vous pour suivre vos demandes et vos rendez-vous.</p>

            <!-- Flash messages -->
            @if(session('error'))
                <div class="flash-message flash-error" style="margin-bottom:14px;">
                    <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="flash-message flash-error" style="margin-bottom:14px;">
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
                        <a href="{{ route('password.request') }}" style="color:var(--rust); font-weight:600;font-size:12px;">Oublié ?</a>
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

                <label class="check-row" style="margin-bottom:18px;">
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