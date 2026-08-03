@extends('layouts.dashboard')

@section('title', 'Mon profil — DoyaImmo')
@section('page_title', 'Mon profil')
@section('page_sub', 'Gérez vos informations personnelles')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mon profil</h2>
            <p>Gérez vos informations personnelles</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;">
        <!-- Carte de gauche - Photo et infos rapides -->
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;text-align:center;">
            <div style="width:100px;height:100px;border-radius:50%;background:var(--rust);color:#fff;display:flex;align-items:center;justify-content:center;font-size:40px;font-weight:700;margin:0 auto 16px;">
                {{ strtoupper(substr(Auth::user()->prenom, 0, 1) . substr(Auth::user()->nom, 0, 1)) }}
            </div>
            <h3 style="font-family:var(--display);font-size:18px;margin-bottom:4px;">
                {{ Auth::user()->prenom }} {{ Auth::user()->nom }}
            </h3>
            <p style="font-size:13px;color:var(--muted);">
                <i class="fa-regular fa-envelope"></i> {{ Auth::user()->email }}
            </p>
            <div style="margin-top:12px;padding:8px 16px;background:#E8F5E9;border-radius:999px;display:inline-block;font-size:12px;color:#1E7A47;">
                <i class="fa-regular fa-circle-check"></i> Compte vérifié
            </div>
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);text-align:left;">
                <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;">
                    <span style="color:var(--muted);">Membre depuis</span>
                    <span>{{ Auth::user()->created_at->format('d/m/Y') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;">
                    <span style="color:var(--muted);">Rôle</span>
                    <span>Client particulier</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:6px 0;font-size:13px;">
                    <span style="color:var(--muted);">Demandes publiées</span>
                    <span>{{ Auth::user()->particulier->demandes()->count() }}</span>
                </div>
            </div>
        </div>

        <!-- Carte de droite - Formulaire -->
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
            <h4 style="font-family:var(--display);font-size:16px;margin-bottom:16px;">
                <i class="fa-regular fa-user" style="margin-right:8px;"></i> Informations personnelles
            </h4>

            <form method="POST" action="{{ route('particulier.profil.update') }}">
                @csrf
                @method('PUT')

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">Prénom</label>
                        <input type="text" name="prenom" value="{{ old('prenom', Auth::user()->prenom) }}" 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">Nom</label>
                        <input type="text" name="nom" value="{{ old('nom', Auth::user()->nom) }}" 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                    </div>
                </div>

                <div style="margin-top:16px;">
                    <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">Email</label>
                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" 
                           style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                </div>

                <div style="margin-top:16px;">
                    <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', Auth::user()->telephone) }}" 
                           style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                </div>

                <div style="margin-top:16px;">
                    <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">Profession</label>
                    <input type="text" name="profession" value="{{ old('profession', Auth::user()->particulier->profession ?? '') }}" 
                           style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                </div>

                <div style="margin-top:16px;">
                    <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">Adresse</label>
                    <input type="text" name="adresse" value="{{ old('adresse', Auth::user()->particulier->adresse ?? '') }}" 
                           style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                </div>

                <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:12px;">
                    <button type="submit" class="btn btn-rust">
                        <i class="fa-solid fa-save"></i> Mettre à jour
                    </button>
                    <a href="{{ route('particulier.dashboard') }}" class="btn btn-ghost">
                        <i class="fa-solid fa-arrow-left"></i> Retour
                    </a>
                </div>
            </form>

            <!-- Section changement de mot de passe -->
            <div style="margin-top:24px;padding-top:20px;border-top:2px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;">
                    <i class="fa-solid fa-lock" style="margin-right:8px;"></i> Changer le mot de passe
                </h4>
                <form method="POST" action="{{ route('particulier.profil.password') }}">
                    @csrf
                    @method('PUT')
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">Nouveau mot de passe</label>
                            <input type="password" name="password" placeholder="8 caractères minimum" 
                                   style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                        </div>
                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">Confirmer</label>
                            <input type="password" name="password_confirmation" placeholder="Confirmer le mot de passe" 
                                   style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-ghost" style="margin-top:12px;">
                        <i class="fa-solid fa-key"></i> Changer le mot de passe
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media (max-width: 820px) {
        [style*="display:grid;grid-template-columns:1fr 2fr;gap:24px;"] {
            grid-template-columns: 1fr !important;
        }
        [style*="display:grid;grid-template-columns:1fr 1fr;gap:16px;"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush