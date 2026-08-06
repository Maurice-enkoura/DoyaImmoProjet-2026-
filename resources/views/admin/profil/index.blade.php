@extends('layouts.admin')

@section('title', 'Mon profil — Administration DoyaImmo')
@section('page_title', 'Mon profil')
@section('page_sub', 'Gérez vos informations personnelles')

@section('content')
<style>
    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--rust-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 700;
        color: var(--rust);
        flex-shrink: 0;
    }

    .profile-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
    }

    .profile-card .card-title {
        font-family: var(--display);
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
    }

    .field-group {
        margin-bottom: 16px;
    }

    .field-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-soft);
        margin-bottom: 4px;
    }

    .field-group input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .field-group input:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.1);
    }

    .field-group .input-error {
        border-color: var(--red);
    }

    .field-group .error-message {
        color: var(--red);
        font-size: 12px;
        margin-top: 4px;
    }

    .field-group .help-text {
        color: var(--muted);
        font-size: 12px;
        margin-top: 4px;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="profile-grid">
    <!-- Informations personnelles -->
    <div class="profile-card">
        <div class="card-title">
            <i class="fa-solid fa-user" style="color:var(--rust);margin-right:8px;"></i>
            Informations personnelles
        </div>

        <form action="{{ route('admin.profil.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label for="nom">Nom</label>
                <input type="text" name="nom" id="nom" 
                       value="{{ old('nom', $user->nom) }}"
                       class="{{ $errors->has('nom') ? 'input-error' : '' }}">
                @error('nom')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-group">
                <label for="prenom">Prénom</label>
                <input type="text" name="prenom" id="prenom" 
                       value="{{ old('prenom', $user->prenom) }}"
                       class="{{ $errors->has('prenom') ? 'input-error' : '' }}">
                @error('prenom')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" 
                       value="{{ old('email', $user->email) }}"
                       class="{{ $errors->has('email') ? 'input-error' : '' }}">
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-group">
                <label for="telephone">Téléphone</label>
                <input type="text" name="telephone" id="telephone" 
                       value="{{ old('telephone', $user->telephone) }}"
                       class="{{ $errors->has('telephone') ? 'input-error' : '' }}">
                @error('telephone')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-group" style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-rust" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-save"></i> Mettre à jour le profil
                </button>
            </div>
        </form>
    </div>

    <!-- Changement de mot de passe -->
    <div class="profile-card">
        <div class="card-title">
            <i class="fa-solid fa-lock" style="color:var(--rust);margin-right:8px;"></i>
            Changer le mot de passe
        </div>

        <form action="{{ route('admin.profil.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="field-group">
                <label for="current_password">Mot de passe actuel</label>
                <input type="password" name="current_password" id="current_password" 
                       class="{{ $errors->has('current_password') ? 'input-error' : '' }}">
                @error('current_password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-group">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" name="password" id="password" 
                       class="{{ $errors->has('password') ? 'input-error' : '' }}">
                <div class="help-text">
                    Minimum 8 caractères, avec majuscule, minuscule, chiffre et symbole.
                </div>
                @error('password')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="field-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" name="password_confirmation" id="password_confirmation">
            </div>

            <div class="field-group" style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-rust" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-key"></i> Changer le mot de passe
                </button>
            </div>
        </form>

        <div style="margin-top:16px;padding:12px 16px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;">
            <div style="display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-info-circle" style="color:#E65100;"></i>
                <span style="font-size:13px;color:#BF360C;">
                    Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe régulièrement.
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Statistiques de l'admin -->
<div style="margin-top:24px;">
    <div class="profile-card">
        <div class="card-title">
            <i class="fa-solid fa-chart-simple" style="color:var(--rust);margin-right:8px;"></i>
            Statistiques de votre compte
        </div>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px;">
            <div style="padding:12px;background:#F7F9FC;border-radius:8px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:var(--rust);">{{ $user->created_at->format('d/m/Y') }}</div>
                <div style="font-size:12px;color:var(--muted);">Membre depuis</div>
            </div>
            <div style="padding:12px;background:#F7F9FC;border-radius:8px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:var(--rust);">{{ $user->notifications()->count() }}</div>
                <div style="font-size:12px;color:var(--muted);">Notifications totales</div>
            </div>
            <div style="padding:12px;background:#F7F9FC;border-radius:8px;text-align:center;">
                <div style="font-size:24px;font-weight:700;color:#E65100;">{{ $notificationsCount ?? 0 }}</div>
                <div style="font-size:12px;color:var(--muted);">Notifications non lues</div>
            </div>
        </div>
    </div>
</div>
@endsection