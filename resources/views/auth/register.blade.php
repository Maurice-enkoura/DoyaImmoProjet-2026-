@extends('layouts.auth')

@section('title', 'Créer un compte — DoyaImmo')

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

            <div class="auth-header">
                <h1>Créer mon compte</h1>
                <p class="sub">Choisissez le type de compte qui vous correspond</p>
            </div>

            <!-- Choix des rôles -->
            <div class="choice-container">
                <a href="{{ route('register.particulier') }}" class="choice-card choice-client">
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

                <a href="{{ route('register.agence') }}" class="choice-card choice-agence">
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
    /* ===================== AUTH HEADER ===================== */
    .auth-header {
        text-align: center;
        margin-bottom: 32px;
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

    /* ===================== CHOIX DES RÔLES ===================== */
    .choice-container {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 32px;
    }

    .choice-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 18px 22px;
        border: 2px solid var(--border);
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.25s ease;
        background: #fff;
        position: relative;
        cursor: pointer;
    }

    .choice-card:hover {
        border-color: var(--rust);
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(181, 80, 42, 0.10);
    }

    .choice-card:active {
        transform: translateY(0);
    }

    .choice-card .choice-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 12px;
        font-size: 20px;
        flex-shrink: 0;
        transition: background 0.25s;
    }

    .choice-client .choice-icon {
        background: var(--rust-soft);
        color: var(--rust);
    }

    .choice-client:hover .choice-icon {
        background: var(--rust);
        color: #fff;
    }

    .choice-agence .choice-icon {
        background: rgba(14, 122, 122, 0.12);
        color: var(--teal);
    }

    .choice-agence:hover .choice-icon {
        background: var(--teal);
        color: #fff;
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
        transition: color 0.25s;
    }

    .choice-client:hover .choice-title {
        color: var(--rust);
    }

    .choice-agence:hover .choice-title {
        color: var(--teal);
    }

    .choice-card .choice-desc {
        font-size: 13px;
        color: var(--muted);
    }

    .choice-card .choice-arrow {
        color: var(--muted);
        font-size: 14px;
        transition: all 0.25s;
        opacity: 0;
        transform: translateX(-8px);
    }

    .choice-card:hover .choice-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    .choice-client:hover .choice-arrow {
        color: var(--rust);
    }

    .choice-agence:hover .choice-arrow {
        color: var(--teal);
    }

    /* ===================== BADGE "POPULAIRE" ===================== */
    .choice-card .badge {
        position: absolute;
        top: -8px;
        right: -6px;
        background: var(--rust);
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        padding: 2px 10px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }

    .choice-card .badge.badge-teal {
        background: var(--teal);
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 480px) {
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

        .auth-header h1 {
            font-size: 20px;
        }
    }

    @media (max-width: 360px) {
        .choice-card {
            flex-wrap: wrap;
        }

        .choice-card .choice-arrow {
            opacity: 1;
            transform: none;
            margin-left: auto;
        }
    }
</style>
@endpush