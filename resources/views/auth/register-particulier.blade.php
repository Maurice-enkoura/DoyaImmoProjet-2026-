@extends('layouts.auth')

@section('title', 'Créer un compte client — DoyaImmo')

@section('content')
<div class="auth-shell" style="opacity:0;">
    <!-- Section gauche - Visuelle -->
    <div class="auth-visual">
        <div class="brand" style="display:flex; justify-content:space-between; align-items:center;">
            <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit;">
                <div class="brand-mark">D</div>
                <div class="brand-name" style="color:#fff;">Doya<span style="color:var(--rust)">Immo</span></div>
            </a>
        </div>

        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;gap:20px;">
            <div>
                <p class="quote" style="font-size:18px;margin:0;max-width:400px;">
                    « Publier ma recherche m'a pris 3 minutes. Le lendemain j'avais déjà deux propositions. »
                </p>
                <p class="quote-by" style="margin-top:6px;font-size:13px;color:#8A91A0;">— Moussa D., client à Plateau</p>
            </div>

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
                    <b style="font-size:24px;">{{ $stats['delai_moyen'] ?? '48h' }}</b>
                    <span style="font-size:12px;color:#9AA1AB;">Délai moyen</span>
                </div>
            </div>
        </div>

        <div class="auth-visual-footer">
            <i class="fa-regular fa-circle-check"></i>
            {{ $stats['clients'] ?? 0 }} clients déjà inscrits sur DoyaImmo
        </div>
    </div>

    <!-- Section droite - Formulaire -->
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
                <a href="{{ route('register.particulier') }}" class="active">
                    <i class="fa-solid fa-user"></i> Espace client
                </a>
                <a href="{{ route('register.agence') }}">
                    <i class="fa-solid fa-building"></i> Espace agence
                </a>
            </div>

            <h1>Créer mon compte client</h1>
            <p class="sub">Gratuit — publiez votre recherche de logement en quelques minutes.</p>

            <form method="POST" action="{{ route('register.particulier') }}" id="registerForm">
                @csrf

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
                </div>

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

                <div class="field conditions-field">
                    <label class="check-row">
                        <input type="checkbox" name="conditions" required>
                        <span>J'accepte les <a href="{{ route('cgu') }}" class="link">Conditions d'utilisation</a> et la <a href="{{ route('mentions-legales') }}" class="link">Mentions Légales</a></span>
                        <span class="required">*</span>
                    </label>
                    @error('conditions') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-rust btn-block btn-lg" id="submitBtn">
                    <i class="fas fa-user-plus"></i> Créer mon compte
                </button>

                <p style="text-align:center;margin-top:10px;font-size:12px;color:var(--muted);">
                    <i class="fas fa-lock" style="margin-right:4px;"></i>
                    Vos données sont sécurisées
                </p>
            </form>

            <p class="auth-foot-link">Déjà un compte ? <a href="{{ route('login') }}">Se connecter</a></p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ═══ TOGGLE PASSWORD ═══
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

    // ═══ FORM VALIDATION ═══
    const form = document.getElementById('registerForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('mot_de_passe').value;
            const confirm = document.getElementById('mot_de_passe_confirmation').value;
            const conditions = document.querySelector('input[name="conditions"]');

            let hasError = false;

            // Reset tous les messages d'erreur JS précédents
            document.querySelectorAll('.js-error-message').forEach(el => el.remove());

            if (password !== confirm) {
                e.preventDefault();
                showJsError('mot_de_passe_confirmation', 'Les mots de passe ne correspondent pas.');
                hasError = true;
            }

            if (password.length < 8) {
                e.preventDefault();
                showJsError('mot_de_passe', 'Le mot de passe doit contenir au moins 8 caractères.');
                hasError = true;
            }

            if (!conditions.checked) {
                e.preventDefault();
                showJsError('conditions', 'Vous devez accepter les conditions d\'utilisation.');
                hasError = true;
            }

            if (!hasError) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
            }
        });
    }

    function showJsError(fieldName, message) {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (!field) return;

        // Retire l'ancien message JS si présent
        const existing = field.parentElement.querySelector('.js-error-message');
        if (existing) existing.remove();

        const errorEl = document.createElement('span');
        errorEl.className = 'error-message js-error-message';
        errorEl.textContent = message;

        // Insertion après le champ (ou après le wrapper)
        const target = field.closest('.password-wrapper') || field;
        target.insertAdjacentElement('afterend', errorEl);
    }

    // ═══ AUTO-CAPITALISATION ═══
    ['nom', 'prenom'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('blur', function() {
                if (this.value.length > 0) {
                    this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1).toLowerCase();
                }
            });
        }
    });
});
</script>
@endpush