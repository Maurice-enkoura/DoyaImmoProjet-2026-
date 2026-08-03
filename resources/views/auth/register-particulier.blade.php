@extends('layouts.auth')

@section('title', 'Créer un compte client — DoyaImmo')

@section('content')
<div class="auth-shell">
    <!-- Section gauche - Visuelle -->
    <div class="auth-visual">
        <div class="brand">
            <div class="brand-mark">D</div>
            <div class="brand-name" style="color:#fff;">Doya<span style="color:var(--gold)">Immo</span></div>
        </div>
        <div>
            <p class="quote">« Publier ma recherche m'a pris 3 minutes. Le lendemain j'avais déjà deux propositions. »</p>
            <p class="quote-by">— Moussa D., client à Plateau</p>
        </div>
        <div class="auth-stats">
            <div><b>1 200+</b><span>Besoins publiés</span></div>
            <div><b>180+</b><span>Agences inscrites</span></div>
            <div><b>48h</b><span>Délai moyen de 1ère offre</span></div>
        </div>
    </div>

    <!-- Section droite - Formulaire -->
    <div class="auth-form-side">
        <div class="auth-box">
            <!-- En-tête -->
            <div class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span style="color:var(--rust)">Immo</span></div>
            </div>
            
            <!-- Toggle rôles -->
            <div class="role-toggle">
                <a href="{{ route('register.particulier') }}" class="active">Espace client</a>
                <a href="{{ route('register.agence') }}">Espace agence</a>
            </div>

            <h1>Créer mon compte client</h1>
            <p class="sub">Gratuit — publiez votre recherche de logement en quelques minutes.</p>

            <form method="POST" action="{{ route('register.particulier') }}" id="registerForm">
                @csrf

                <!-- Étape 1 : Informations personnelles -->
                <div class="form-section">
                    <h3 class="section-title">
                        <span class="step-badge">1</span> Informations personnelles
                    </h3>
                    
                    <div class="form-grid">
                        <div class="field">
                            <label for="nom">Nom <span class="required">*</span></label>
                            <input type="text" id="nom" name="nom" value="{{ old('nom') }}" placeholder="Votre nom" required>
                            @error('nom') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label for="prenom">Prénom <span class="required">*</span></label>
                            <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" placeholder="Votre prénom" required>
                            @error('prenom') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Étape 2 : Coordonnées -->
                <div class="form-section">
                    <h3 class="section-title">
                        <span class="step-badge">2</span> Coordonnées
                    </h3>
                    
                    <div class="field">
                        <label for="email">Adresse email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="exemple@email.com" required>
                        @error('email') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="telephone">Numéro de téléphone <span class="required">*</span></label>
                        <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="+221 77 123 45 67" required>
                        @error('telephone') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Étape 3 : Sécurité -->
                <div class="form-section">
                    <h3 class="section-title">
                        <span class="step-badge">3</span> Sécurité
                    </h3>
                    
                    <div class="field">
                        <label for="mot_de_passe">Mot de passe <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="mot_de_passe" name="mot_de_passe" placeholder="8 caractères minimum" required>
                            <button type="button" class="toggle-password" aria-label="Afficher le mot de passe">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <span class="field-hint">Minimum 8 caractères, incluant une majuscule et un chiffre</span>
                        @error('mot_de_passe') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="mot_de_passe_confirmation">Confirmer le mot de passe <span class="required">*</span></label>
                        <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" placeholder="Confirmer votre mot de passe" required>
                    </div>

                    <!-- Indicateur de force du mot de passe -->
                    <div class="password-strength" style="display:none;margin-top:8px;">
                        <div style="height:4px;border-radius:2px;background:var(--border);overflow:hidden;">
                            <div id="strengthBar" style="height:100%;width:0%;transition:width 0.3s;border-radius:2px;"></div>
                        </div>
                        <span id="strengthText" style="font-size:11px;color:var(--muted);display:block;margin-top:4px;">Force : Faible</span>
                    </div>
                </div>

                <!-- Étape 4 : Informations complémentaires -->
                <div class="form-section optional-section">
                    <h3 class="section-title">
                        <span class="step-badge">4</span> Informations complémentaires
                        <span class="optional-badge">Optionnel</span>
                    </h3>

                    <div class="form-grid">
                        <div class="field">
                            <label for="profession">Profession</label>
                            <input type="text" id="profession" name="profession" value="{{ old('profession') }}" placeholder="Votre profession">
                        </div>

                        <div class="field">
                            <label for="adresse">Adresse</label>
                            <input type="text" id="adresse" name="adresse" value="{{ old('adresse') }}" placeholder="Votre adresse">
                        </div>
                    </div>
                </div>

                <!-- Conditions -->
                <div class="field conditions-field">
                    <label class="check-row">
                        <input type="checkbox" name="conditions" required>
                        <span>J'accepte les <a href="#" class="link">Conditions d'utilisation</a> et la <a href="#" class="link">Politique de confidentialité</a></span>
                        <span class="required">*</span>
                    </label>
                </div>

                <!-- Actions -->
                <button type="submit" class="btn btn-rust btn-block btn-lg" id="submitBtn">
                    <i class="fas fa-user-plus"></i> Créer mon compte
                </button>
                
                <p style="text-align:center;margin-top:12px;font-size:12px;color:var(--muted);">
                    <i class="fas fa-lock" style="margin-right:4px;"></i>
                    Vos données sont sécurisées et ne seront jamais partagées
                </p>
            </form>

            <p class="auth-foot-link">Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Toggle password visibility ---
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.password-wrapper').querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // --- Password strength meter ---
    const passwordInput = document.getElementById('mot_de_passe');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const strengthContainer = document.querySelector('.password-strength');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        
        if (password.length === 0) {
            strengthContainer.style.display = 'none';
            return;
        }

        strengthContainer.style.display = 'block';
        
        let strength = 0;
        let label = 'Faible';
        let color = '#C62828';

        // Longueur
        if (password.length >= 8) strength += 1;
        if (password.length >= 12) strength += 1;

        // Majuscule
        if (/[A-Z]/.test(password)) strength += 1;
        
        // Minuscule
        if (/[a-z]/.test(password)) strength += 1;
        
        // Chiffre
        if (/[0-9]/.test(password)) strength += 1;
        
        // Caractère spécial
        if (/[^A-Za-z0-9]/.test(password)) strength += 1;

        let percentage = Math.min((strength / 6) * 100, 100);

        if (strength <= 2) {
            label = 'Faible';
            color = '#C62828';
        } else if (strength <= 4) {
            label = 'Moyen';
            color = '#F5A623';
        } else if (strength <= 5) {
            label = 'Fort';
            color = '#4A90D9';
        } else {
            label = 'Très fort';
            color = '#1E7A47';
        }

        strengthBar.style.width = percentage + '%';
        strengthBar.style.background = color;
        strengthText.textContent = 'Force : ' + label;
        strengthText.style.color = color;
    });

    // --- Form validation ---
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');

    form.addEventListener('submit', function(e) {
        const password = document.getElementById('mot_de_passe').value;
        const confirm = document.getElementById('mot_de_passe_confirmation').value;
        const conditions = document.querySelector('input[name="conditions"]');

        // Vérifier que les mots de passe correspondent
        if (password !== confirm) {
            e.preventDefault();
            alert('Les mots de passe ne correspondent pas.');
            return;
        }

        // Vérifier la longueur du mot de passe
        if (password.length < 8) {
            e.preventDefault();
            alert('Le mot de passe doit contenir au moins 8 caractères.');
            return;
        }

        // Vérifier les conditions
        if (!conditions.checked) {
            e.preventDefault();
            alert('Vous devez accepter les conditions d\'utilisation.');
            return;
        }

        // Désactiver le bouton pour éviter les doubles soumissions
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
    });

    // --- Auto-capitalisation des noms ---
    document.getElementById('nom').addEventListener('blur', function() {
        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
    });

    document.getElementById('prenom').addEventListener('blur', function() {
        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
    });
});
</script>
@endpush

@push('styles')
<style>
    /* Styles spécifiques au formulaire d'inscription client */
    .form-section {
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--border);
    }

    .form-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: var(--display);
        font-size: 15px;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 16px;
    }

    .step-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--rust);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        flex-shrink: 0;
    }

    .optional-badge {
        font-size: 11px;
        font-weight: 500;
        color: var(--muted);
        background: var(--border);
        padding: 2px 10px;
        border-radius: 20px;
        margin-left: auto;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    @media (max-width: 480px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .field {
        margin-bottom: 0;
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
        transition: all 0.2s;
        background: #fff;
    }

    .field input:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.1);
    }

    .field input.is-invalid {
        border-color: #C62828;
    }

    .field input.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(198, 40, 40, 0.1);
    }

    .field input::placeholder {
        color: #B0B8C4;
    }

    .required {
        color: var(--rust);
        font-weight: 600;
        margin-left: 2px;
    }

    .error-message {
        display: block;
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    .field-hint {
        display: block;
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }

    .password-wrapper {
        position: relative;
    }

    .password-wrapper input {
        padding-right: 44px;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--muted);
        cursor: pointer;
        padding: 4px;
        font-size: 16px;
    }

    .toggle-password:hover {
        color: var(--text);
    }

    .conditions-field {
        margin: 20px 0;
    }

    .check-row {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        color: var(--text-soft);
        cursor: pointer;
    }

    .check-row input[type="checkbox"] {
        width: 18px;
        height: 18px;
        min-width: 18px;
        margin-top: 2px;
        cursor: pointer;
        accent-color: var(--rust);
    }

    .check-row .link {
        color: var(--rust);
        font-weight: 600;
        text-decoration: none;
    }

    .check-row .link:hover {
        text-decoration: underline;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-rust {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .btn-rust:hover {
        background: #9A4523;
        border-color: #9A4523;
        color: #fff;
    }

    .btn-rust:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    .btn-lg {
        padding: 14px 28px;
        font-size: 15px;
    }

    .auth-foot-link {
        text-align: center;
        margin-top: 24px;
        font-size: 14px;
        color: var(--text-soft);
    }

    .auth-foot-link a {
        color: var(--rust);
        text-decoration: none;
        font-weight: 600;
    }

    .auth-foot-link a:hover {
        text-decoration: underline;
    }

    /* Password strength animation */
    @keyframes strengthPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .password-strength {
        animation: strengthPulse 0.3s ease;
    }
</style>
@endpush