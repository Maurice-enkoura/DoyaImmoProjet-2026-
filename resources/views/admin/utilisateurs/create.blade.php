@extends('layouts.admin')

@section('title', 'Créer un utilisateur — Administration DoyaImmo')
@section('page_title', 'Créer un utilisateur')
@section('page_sub', 'Ajouter un nouvel utilisateur à la plateforme')

@section('content')
<div style="margin-bottom:20px;">
    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux utilisateurs
    </a>
</div>

<div class="panel" style="max-width:700px;margin:0 auto;">
    @if(session('error'))
        <div class="flash-message flash-error" style="margin-bottom:16px;">
            <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.utilisateurs.store') }}" method="POST">
        @csrf

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <!-- Nom -->
            <div>
                <label for="nom" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                    Nom <span style="color:var(--red);">*</span>
                </label>
                <input type="text" name="nom" id="nom" value="{{ old('nom') }}" 
                       style="width:100%;padding:8px 12px;border:1px solid {{ $errors->has('nom') ? 'var(--red)' : 'var(--border)' }};border-radius:8px;font-size:13px;"
                       required>
                @error('nom')
                    <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Prénom -->
            <div>
                <label for="prenom" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                    Prénom <span style="color:var(--red);">*</span>
                </label>
                <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" 
                       style="width:100%;padding:8px 12px;border:1px solid {{ $errors->has('prenom') ? 'var(--red)' : 'var(--border)' }};border-radius:8px;font-size:13px;"
                       required>
                @error('prenom')
                    <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div style="grid-column:span 2;">
                <label for="email" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                    Email <span style="color:var(--red);">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                       style="width:100%;padding:8px 12px;border:1px solid {{ $errors->has('email') ? 'var(--red)' : 'var(--border)' }};border-radius:8px;font-size:13px;"
                       required>
                @error('email')
                    <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Téléphone -->
            <div style="grid-column:span 2;">
                <label for="telephone" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                    Téléphone
                </label>
                <input type="text" name="telephone" id="telephone" value="{{ old('telephone') }}" 
                       style="width:100%;padding:8px 12px;border:1px solid {{ $errors->has('telephone') ? 'var(--red)' : 'var(--border)' }};border-radius:8px;font-size:13px;">
                @error('telephone')
                    <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Rôle -->
            <div style="grid-column:span 2;">
                <label for="role" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                    Rôle <span style="color:var(--red);">*</span>
                </label>
                <select name="role" id="role" style="width:100%;padding:8px 12px;border:1px solid {{ $errors->has('role') ? 'var(--red)' : 'var(--border)' }};border-radius:8px;font-size:13px;" required>
                    <option value="">Sélectionner un rôle</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" {{ old('role') == $role->value ? 'selected' : '' }}>
                            {{ $role->label() }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Champs supplémentaires pour agence -->
            <div id="agenceFields" style="grid-column:span 2;display:none;padding:16px;background:#F7F9FC;border-radius:8px;">
                <h4 style="font-weight:600;margin-bottom:8px;font-size:14px;">
                    <i class="fa-solid fa-building"></i> Informations de l'agence
                </h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div style="grid-column:span 2;">
                        <label for="nom_agence" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                            Nom de l'agence <span style="color:var(--red);">*</span>
                        </label>
                        <input type="text" name="nom_agence" id="nom_agence" value="{{ old('nom_agence') }}" 
                               style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
                    </div>
                    <div style="grid-column:span 2;">
                        <label for="adresse" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                            Adresse
                        </label>
                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}" 
                               style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
                    </div>
                </div>
            </div>

            <!-- Champs supplémentaires pour admin -->
            <div id="adminFields" style="grid-column:span 2;display:none;padding:16px;background:#F7F9FC;border-radius:8px;">
                <h4 style="font-weight:600;margin-bottom:8px;font-size:14px;">
                    <i class="fa-solid fa-user-shield"></i> Informations administrateur
                </h4>
                <div>
                    <label for="fonction" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                        Fonction
                    </label>
                    <input type="text" name="fonction" id="fonction" value="{{ old('fonction', 'Administrateur') }}" 
                           style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
                </div>
            </div>

            <!-- Mot de passe -->
            <div style="grid-column:span 2;">
                <label for="mot_de_passe" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                    Mot de passe <span style="color:var(--red);">*</span>
                    <span style="font-weight:400;color:var(--muted);font-size:11px;">(minimum 8 caractères)</span>
                </label>
                <input type="password" name="mot_de_passe" id="mot_de_passe" 
                       style="width:100%;padding:8px 12px;border:1px solid {{ $errors->has('mot_de_passe') ? 'var(--red)' : 'var(--border)' }};border-radius:8px;font-size:13px;"
                       required>
                @error('mot_de_passe')
                    <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirmation mot de passe -->
            <div style="grid-column:span 2;">
                <label for="mot_de_passe_confirmation" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                    Confirmer le mot de passe <span style="color:var(--red);">*</span>
                </label>
                <input type="password" name="mot_de_passe_confirmation" id="mot_de_passe_confirmation" 
                       style="width:100%;padding:8px 12px;border:1px solid {{ $errors->has('mot_de_passe') ? 'var(--red)' : 'var(--border)' }};border-radius:8px;font-size:13px;"
                       required>
            </div>
        </div>

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;">
            <button type="submit" class="btn btn-rust">
                <i class="fa-solid fa-check"></i> Créer l'utilisateur
            </button>
            <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>

<script>
    // Afficher/masquer les champs selon le rôle sélectionné
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const agenceFields = document.getElementById('agenceFields');
        const adminFields = document.getElementById('adminFields');

        function toggleFields() {
            const role = roleSelect.value;
            agenceFields.style.display = role === 'agence' ? 'block' : 'none';
            adminFields.style.display = role === 'admin' ? 'block' : 'none';
        }

        roleSelect.addEventListener('change', toggleFields);
        toggleFields();
    });
</script>
@endsection