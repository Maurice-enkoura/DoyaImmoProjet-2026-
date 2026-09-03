@extends('layouts.auth')

@section('title', 'Inscription Agence — DoyaImmo')

@section('content')
<div class="auth-shell" style="opacity:0;">
    <!-- Section gauche - Visuelle -->
    <div class="auth-visual">
        <div class="brand">
            <div class="brand-mark">D</div>
            <div class="brand-name" style="color:#fff;">Doya<span style="color:var(--gold)">Immo</span></div>
        </div>
        
        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;gap:24px;">
            <div>
                <p class="quote" style="font-size:20px;margin:0;">
                    « On touche des clients qu'on n'aurait jamais eus par le bouche-à-oreille. »
                </p>
                <p class="quote-by" style="margin-top:8px;">— Agence partenaire, Almadies</p>
            </div>

            <div class="auth-stats" style="margin-top:0;">
                <div>
                    <b style="font-size:28px;">{{ $stats['offres_gratuites'] ?? 5 }}</b>
                    <span style="font-size:13px;color:#9AA1AB;">Offres gratuites / mois</span>
                </div>
                <div>
                    <b style="font-size:28px;">{{ $stats['frais_inscription'] ?? '0 F' }}</b>
                    <span style="font-size:13px;color:#9AA1AB;">Frais d'inscription</span>
                </div>
                <div>
                    <b style="font-size:28px;">{{ $stats['delai_validation'] ?? '48h' }}</b>
                    <span style="font-size:13px;color:#9AA1AB;">Validation moyenne</span>
                </div>
            </div>
        </div>

        <div style="font-size:12px;color:#6A7280;margin-top:20px;">
            <i class="fa-regular fa-circle-check" style="color:var(--gold);"></i>
            {{ $stats['agences'] ?? 0 }} agences déjà inscrites sur DoyaImmo
        </div>
    </div>

    <!-- Section droite - Formulaire -->
    <div class="auth-form-side">
        <div class="auth-box" style="max-width:480px;">
            <div class="brand">
                <div class="brand-mark">D</div>
                <div class="brand-name">Doya<span style="color:var(--rust)">Immo</span></div>
            </div>
            
            <div class="role-toggle">
                <a href="{{ route('register.particulier') }}">Espace client</a>
                <a href="{{ route('register.agence') }}" class="active">Espace agence</a>
            </div>

            <h1>Inscription de l'agence</h1>
            <p class="sub">Complétez les informations ci-dessous pour rejoindre notre réseau d'agences partenaires.</p>

            <!-- Affichage des erreurs -->
            @if($errors->any())
                <div style="padding:12px 16px;background:#FFEBEE;border-radius:10px;border:1px solid #FFCDD2;margin-bottom:16px;">
                    <ul style="margin:0;padding:0 0 0 16px;color:#C62828;font-size:13px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.agence') }}" enctype="multipart/form-data" id="registerForm">
                @csrf

                <!-- Section 1 : Identité du responsable -->
                <div class="form-group">
                    <div class="form-label">
                        <span class="label-icon">
                            <i class="fas fa-user-circle"></i>
                        </span>
                        <span>Responsable légal</span>
                    </div>
                    
                    <div class="row">
                        <div class="col">
                            <div class="field">
                                <label for="nom">Nom <span class="required">*</span></label>
                                <input type="text" id="nom" name="nom" value="{{ old('nom') }}" placeholder="Nom du responsable" required>
                                @error('nom') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="field">
                                <label for="prenom">Prénom <span class="required">*</span></label>
                                <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" placeholder="Prénom du responsable" required>
                                @error('prenom') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2 : Agence -->
                <div class="form-group">
                    <div class="form-label">
                        <span class="label-icon">
                            <i class="fas fa-building"></i>
                        </span>
                        <span>Agence</span>
                    </div>

                    <div class="field">
                        <label for="nom_agence">Dénomination sociale <span class="required">*</span></label>
                        <input type="text" id="nom_agence" name="nom_agence" value="{{ old('nom_agence') }}" placeholder="Teranga Immobilier" required>
                        @error('nom_agence') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="field">
                                <label for="email">Adresse email <span class="required">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="contact@agence.sn" required>
                                @error('email') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="field">
                                <label for="telephone">Téléphone <span class="required">*</span></label>
                                <input type="tel" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="+221 33 123 45 67" required>
                                @error('telephone') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="field">
                                <label for="adresse">Adresse <span class="required">*</span></label>
                                <input type="text" id="adresse" name="adresse" value="{{ old('adresse') }}" placeholder="Rue, numéro, Dakar" required>
                                @error('adresse') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="col">
                            <div class="field">
                                <label for="quartier">Quartier <span class="required">*</span></label>
                                <input type="text" id="quartier" name="quartier" value="{{ old('quartier') }}" placeholder="Almadies, Ngor, Mermoz" required>
                                @error('quartier') <span class="error-message">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label for="description">Présentation</label>
                        <textarea id="description" name="description" rows="3" placeholder="Décrivez votre agence, ses spécialités, son expérience...">{{ old('description') }}</textarea>
                        @error('description') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Section 3 : Sécurité -->
                <div class="form-group">
                    <div class="form-label">
                        <span class="label-icon">
                            <i class="fas fa-lock"></i>
                        </span>
                        <span>Sécurité</span>
                    </div>

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
                        <label for="mot_de_passe_confirmation">Confirmation <span class="required">*</span></label>
                        <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" placeholder="Confirmer le mot de passe" required>
                        @error('mot_de_passe_confirmation') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Section 4 : Documents -->
                <div class="form-group documents-group">
                    <div class="form-label">
                        <span class="label-icon">
                            <i class="fas fa-file-alt"></i>
                        </span>
                        <span>Pièces justificatives</span>
                    </div>

                    <div class="document-grid">
                        <div class="document-item">
                            <label for="rccm" class="document-label">
                                <i class="fas fa-file-pdf"></i>
                                <div>
                                    <span>Registre de Commerce</span>
                                    <small>RCCM · PDF, JPEG</small>
                                </div>
                                <span class="required">*</span>
                            </label>
                            <input type="file" id="rccm" name="rccm" accept=".pdf,.jpg,.jpeg,.png" required>
                            @error('rccm') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="document-item">
                            <label for="ninea" class="document-label">
                                <i class="fas fa-file-invoice"></i>
                                <div>
                                    <span>NINEA</span>
                                    <small>Numéro d'identification</small>
                                </div>
                                <span class="required">*</span>
                            </label>
                            <input type="file" id="ninea" name="ninea" accept=".pdf,.jpg,.jpeg,.png" required>
                            @error('ninea') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="document-item">
                            <label for="piece_identite" class="document-label">
                                <i class="fas fa-id-card"></i>
                                <div>
                                    <span>Pièce d'identité</span>
                                    <small>Responsable légal</small>
                                </div>
                                <span class="required">*</span>
                            </label>
                            <input type="file" id="piece_identite" name="piece_identite" accept=".pdf,.jpg,.jpeg,.png" required>
                            @error('piece_identite') <span class="error-message">{{ $message }}</span> @enderror
                        </div>

                        <div class="document-item">
                            <label for="logo" class="document-label">
                                <i class="fas fa-image"></i>
                                <div>
                                    <span>Logo</span>
                                    <small>Optionnel · PNG, JPEG</small>
                                </div>
                            </label>
                            <input type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png">
                            @error('logo') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="document-help">
                        <i class="fas fa-info-circle"></i>
                        <span>Formats acceptés : PDF, JPEG, PNG — Poids maximum : 5 Mo</span>
                    </div>
                </div>

                <!-- Conditions -->
                <div class="conditions">
                    <label class="checkbox">
                        <input type="checkbox" name="conditions" value="1" required>
                        <span>
                            Je certifie l'exactitude des informations fournies et j'accepte les 
                            <a href="#" class="link">Conditions d'utilisation</a>
                        </span>
                        <span class="required">*</span>
                    </label>
                    @error('conditions') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-paper-plane"></i>
                    Soumettre la demande
                </button>

                <div class="form-footer">
                    <i class="fas fa-shield-alt"></i>
                    <span>Vos données sont sécurisées et confidentielles</span>
                </div>
            </form>

            <p class="auth-foot-link">Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a></p>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ===================== FORMULAIRES ===================== */
    .form-group {
        margin-bottom: 28px;
        padding-bottom: 28px;
        border-bottom: 1px solid var(--border);
    }

    .form-group:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 10px;
        font-family: var(--display);
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: 16px;
        letter-spacing: 0.3px;
    }

    .form-label .label-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--rust-soft);
        color: var(--rust);
        font-size: 14px;
    }

    /* ===================== CHAMPS ===================== */
    .row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .col {
        display: flex;
        flex-direction: column;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 4px;
        margin-bottom: 12px;
    }

    .field label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-soft);
    }

    .field input,
    .field textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        font-family: inherit;
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .field input:focus,
    .field textarea:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }

    .field input::placeholder,
    .field textarea::placeholder {
        color: #B8C0CC;
    }

    .field textarea {
        resize: vertical;
        min-height: 80px;
    }

    .field-hint {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
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

    .field input.is-invalid,
    .field textarea.is-invalid {
        border-color: #C62828;
    }

    .field input.is-invalid:focus,
    .field textarea.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(198, 40, 40, 0.08);
    }

    /* ===================== MOT DE PASSE ===================== */
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
        font-size: 15px;
        transition: color 0.2s;
    }

    .toggle-password:hover {
        color: var(--text);
    }

    /* ===================== DOCUMENTS ===================== */
    .documents-group {
        background: #FAFBFC;
        padding: 16px 20px 20px;
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    .documents-group .form-label {
        margin-bottom: 12px;
    }

    .document-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .document-item {
        position: relative;
    }

    .document-label {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 13px;
    }

    .document-label:hover {
        border-color: var(--rust);
        background: rgba(181, 80, 42, 0.02);
    }

    .document-label i {
        font-size: 20px;
        color: var(--rust);
        width: 24px;
        text-align: center;
    }

    .document-label div {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .document-label span {
        font-weight: 600;
        color: var(--text);
    }

    .document-label small {
        font-size: 11px;
        color: var(--muted);
    }

    .document-item input[type="file"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .document-item.has-file .document-label {
        border-color: #1E7A47;
        background: rgba(30, 122, 71, 0.04);
    }

    .document-help {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        font-size: 12px;
        color: var(--muted);
    }

    .document-help i {
        color: var(--rust);
    }

    /* ===================== CONDITIONS ===================== */
    .conditions {
        margin: 24px 0 20px;
    }

    .checkbox {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13px;
        color: var(--text-soft);
        cursor: pointer;
    }

    .checkbox input[type="checkbox"] {
        width: 18px;
        height: 18px;
        min-width: 18px;
        margin-top: 1px;
        cursor: pointer;
        accent-color: var(--rust);
    }

    .checkbox .link {
        color: var(--rust);
        font-weight: 600;
        text-decoration: none;
    }

    .checkbox .link:hover {
        text-decoration: underline;
    }

    /* ===================== BOUTON ===================== */
    .btn-submit {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 14px 28px;
        border: none;
        border-radius: 12px;
        background: var(--rust);
        color: #fff;
        font-size: 15px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        transition: background 0.2s;
    }

    .btn-submit:hover {
        background: #9A4523;
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-submit i {
        font-size: 16px;
    }

    .form-footer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 12px;
        font-size: 12px;
        color: var(--muted);
    }

    .form-footer i {
        color: var(--rust);
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 600px) {
        .row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .document-grid {
            grid-template-columns: 1fr;
        }

        .auth-box {
            max-width: 100%;
        }

        .documents-group {
            padding: 12px 14px 16px;
        }

        .document-label {
            padding: 10px 12px;
        }
    }

    @media (max-width: 480px) {
        .form-label {
            font-size: 13px;
        }

        .btn-submit {
            font-size: 14px;
            padding: 12px 20px;
        }

        .field input,
        .field textarea {
            font-size: 16px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ==================== TOGGLE PASSWORD ====================
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const wrapper = this.closest('.password-wrapper');
            const input = wrapper.querySelector('input');
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

    // ==================== FILE UPLOAD INDICATOR ====================
    document.querySelectorAll('.document-item input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
            const item = this.closest('.document-item');
            if (this.files.length > 0) {
                item.classList.add('has-file');
            } else {
                item.classList.remove('has-file');
            }
        });
    });

    // ==================== AUTO-CAPITALIZATION ====================
    ['nom', 'prenom', 'nom_agence', 'quartier'].forEach(id => {
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