@extends('layouts.auth')

@section('title', 'Créer un compte — DoyaImmo')

@section('content')
<div class="auth-shell" style="opacity:0;">
    <!-- Section gauche - Visuelle -->
    <div class="auth-visual">
        <div class="brand" style="display:flex; justify-content:space-between; align-items:center;">
            <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none; color:inherit;">
                <div class="brand-mark">D</div>
                <div class="brand-name"style="color:#fff;">Doya<span style="color:var(--rust)">Immo</span></div>
            </a>
        </div>

        <!-- Statistiques -->
        <div style="flex:1;display:flex;flex-direction:column;justify-content:center;gap:20px;">
            <!-- Citation -->
            <div>
                <p class="quote" style="font-size:18px;margin:0;max-width:400px;">
                    « Publier ma recherche m'a pris 3 minutes. Le lendemain j'avais déjà deux propositions. »
                </p>
                <p class="quote-by" style="margin-top:6px;font-size:13px;color:#8A91A0;">— Moussa D., client à Plateau</p>
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
                    <b style="font-size:24px;">{{ $stats['delai_moyen'] ?? '48h' }}</b>
                    <span style="font-size:12px;color:#9AA1AB;">Délai moyen</span>
                </div>
            </div>
        </div>

        <!-- Footer visuel -->
        <div class="auth-visual-footer">
            <i class="fa-regular fa-circle-check"></i>
            {{ $stats['clients'] ?? 0 }} clients déjà inscrits sur DoyaImmo
        </div>
    </div>

    <!-- Section droite - Formulaire -->
    <div class="auth-form-side">
        <div class="auth-box">
            <!-- En-tête avec marque et bouton Accueil -->
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

            <div class="auth-header">
                <h1>Créer mon compte</h1>
                <p class="sub">Choisissez le type de compte qui vous correspond</p>
            </div>

            <!-- Choix des rôles -->
            <div class="choice-container">
                <a href="{{ route('register.particulier') }}" class="choice-card">
                    <div class="choice-icon">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <div class="choice-content">
                        <span class="choice-title">Client</span>
                        <span class="choice-desc">Trouver un logement à Dakar</span>
                    </div>
                    <div class="choice-arrow">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </a>

                <a href="{{ route('register.agence') }}" class="choice-card">
                    <div class="choice-icon">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <div class="choice-content">
                        <span class="choice-title">Agence</span>
                        <span class="choice-desc">Proposer des biens immobiliers</span>
                    </div>
                    <div class="choice-arrow">
                        <i class="fa-solid fa-arrow-right"></i>
                    </div>
                </a>
            </div>

            <!-- Lien de connexion -->
            <p class="auth-foot-link">
                Déjà inscrit ? <a href="{{ route('login') }}">Se connecter</a>
            </p>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* ═══ AUTH HEADER ═══ */
    .auth-header {
        text-align: center;
        margin-bottom: 24px;
    }

    .auth-header h1 {
        font-family: var(--display);
        font-weight: 700;
        font-size: 24px;
        margin-bottom: 4px;
    }

    .auth-header .sub {
        font-size: 14px;
        color: var(--muted);
    }

    /* ═══ CHOIX DES RÔLES ═══ */
    .choice-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-bottom: 24px;
    }

    .choice-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 20px;
        border: 1px solid var(--border);
        border-radius: 14px;
        text-decoration: none;
        transition: border-color .15s, background-color .15s;
        background: #fff;
        position: relative;
        cursor: pointer;
    }

    .choice-card:hover {
        border-color: var(--rust);
        background: #FAFBFC;
    }

    .choice-card .choice-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        font-size: 18px;
        flex-shrink: 0;
        background: #F5F5F7;
        color: var(--muted);
    }

    .choice-card .choice-content {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .choice-card .choice-title {
        font-family: var(--display);
        font-size: 16px;
        font-weight: 700;
        color: var(--ink);
        line-height: 1.3;
    }

    .choice-card .choice-desc {
        font-size: 13px;
        color: var(--muted);
    }

    .choice-card .choice-arrow {
        color: var(--muted);
        font-size: 14px;
        flex-shrink: 0;
    }

    .choice-card:hover .choice-arrow {
        color: var(--rust);
    }

    /* ═══ RESPONSIVE ═══ */
    @media (max-width: 480px) {
        .auth-header {
            margin-bottom: 20px;
        }

        .auth-header h1 {
            font-size: 20px;
        }

        .choice-container {
            gap: 10px;
            margin-bottom: 20px;
        }

        .choice-card {
            padding: 14px 16px;
            gap: 12px;
        }

        .choice-card .choice-icon {
            width: 40px;
            height: 40px;
            font-size: 16px;
            border-radius: 10px;
        }

        .choice-card .choice-title {
            font-size: 15px;
        }

        .choice-card .choice-desc {
            font-size: 12px;
        }

        .choice-card .choice-arrow {
            font-size: 12px;
        }
    }
</style>
@endpush