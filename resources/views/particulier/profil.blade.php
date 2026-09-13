@extends('layouts.dashboard')

@section('title', 'Mon profil — DoyaImmo')
@section('page_title', 'Mon profil')
@section('page_sub', 'Gérez vos informations personnelles')

@section('content')
<div class="view active particulier-profil">

    @php
        $user = Auth::user();
        $prenom = $user->prenom ?? '';
        $nom = $user->nom ?? '';
        $initiales = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
        $telephone = $user->telephone ?? null;
        $verifie = $user->email_verified_at ? true : false;
    @endphp

    {{-- ═══ HERO ═══ --}}
    <div class="ios-profile-hero">
        <div class="ios-profile-hero__avatar">{{ $initiales }}</div>
        <div class="ios-profile-hero__info">
            <h2 class="ios-profile-hero__name">{{ $prenom }} {{ $nom }}</h2>
            @if($telephone)
                <div class="ios-profile-hero__phone">+221 {{ $telephone }}</div>
            @endif
            @if($verifie)
                <div class="ios-profile-hero__badge">
                    <i class="fa-solid fa-check"></i> Vérifié
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ MON COMPTE ═══ --}}
    <div class="ios-section-title">Mon compte</div>

    <div class="ios-list">
        <a href="{{ route('particulier.demandes.index') }}" class="ios-list__item">
            <div class="ios-list__icon ios-list__icon--blue">
                <i class="fa-solid fa-house-circle-check"></i>
            </div>
            <div class="ios-list__content">
                <span class="ios-list__title">Mes besoins publiés</span>
                <span class="ios-list__sub">Gérer mes annonces actives</span>
            </div>
            <i class="fa-solid fa-chevron-right ios-list__chevron"></i>
        </a>

        <a href="{{ route('particulier.propositions.index') }}" class="ios-list__item">
            <div class="ios-list__icon ios-list__icon--blue">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div class="ios-list__content">
                <span class="ios-list__title">Offres reçues</span>
                <span class="ios-list__sub">Propositions des agences</span>
            </div>
            <i class="fa-solid fa-chevron-right ios-list__chevron"></i>
        </a>

        <a href="{{ route('particulier.rendezvous.index') }}" class="ios-list__item">
            <div class="ios-list__icon ios-list__icon--blue">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="ios-list__content">
                <span class="ios-list__title">Mes rendez-vous</span>
                <span class="ios-list__sub">Visites planifiées</span>
            </div>
            <i class="fa-solid fa-chevron-right ios-list__chevron"></i>
        </a>

        <a href="{{ route('particulier.evaluations.index') }}" class="ios-list__item">
            <div class="ios-list__icon ios-list__icon--blue">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="ios-list__content">
                <span class="ios-list__title">Mes avis donnés</span>
                <span class="ios-list__sub">Évaluations d'agences</span>
            </div>
            <i class="fa-solid fa-chevron-right ios-list__chevron"></i>
        </a>

        <a href="{{ route('particulier.historique') }}" class="ios-list__item">
            <div class="ios-list__icon ios-list__icon--blue">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div class="ios-list__content">
                <span class="ios-list__title">Historique</span>
                <span class="ios-list__sub">Toutes mes activités passées</span>
            </div>
            <i class="fa-solid fa-chevron-right ios-list__chevron"></i>
        </a>
    </div>

    {{-- ═══ INFORMATIONS LÉGALES ═══ --}}
    <div class="ios-section-title">Informations légales</div>

    <div class="ios-list">
        <a href="#" class="ios-list__item">
            <div class="ios-list__icon ios-list__icon--gray">
                <i class="fa-solid fa-file-contract"></i>
            </div>
            <div class="ios-list__content">
                <span class="ios-list__title">Mentions légales</span>
                <span class="ios-list__sub">Éditeur · marche du service · données</span>
            </div>
            <i class="fa-solid fa-chevron-right ios-list__chevron"></i>
        </a>

        <a href="#" class="ios-list__item">
            <div class="ios-list__icon ios-list__icon--gray">
                <i class="fa-solid fa-file-lines"></i>
            </div>
            <div class="ios-list__content">
                <span class="ios-list__title">Conditions générales (CGU)</span>
                <span class="ios-list__sub">Règles d'utilisation</span>
            </div>
            <i class="fa-solid fa-chevron-right ios-list__chevron"></i>
        </a>

        <a href="#" class="ios-list__item">
            <div class="ios-list__icon ios-list__icon--gray">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="ios-list__content">
                <span class="ios-list__title">Politique de confidentialité</span>
                <span class="ios-list__sub">Tes données · tes droits</span>
            </div>
            <i class="fa-solid fa-chevron-right ios-list__chevron"></i>
        </a>
    </div>

    {{-- ═══ DÉCONNEXION ═══ --}}
    <form action="{{ route('logout') }}" method="POST" class="ios-logout">
        @csrf
        <button type="submit">
            <i class="fa-solid fa-right-from-bracket"></i>
            Se déconnecter
        </button>
    </form>

</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PROFIL PARTICULIER — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.particulier-profil {
    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
    --rust: #B5502A;
}

/* ═══ HERO ═══ */
.ios-profile-hero {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 24px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    margin-bottom: 16px;
}
.ios-profile-hero__avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FFE4D0, #FFD1B0);
    color: #8B4513;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 24px;
    font-weight: 700;
    flex-shrink: 0;
}
.ios-profile-hero__info {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
}
.ios-profile-hero__name {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    color: var(--ink);
    margin: 0;
    letter-spacing: -.3px;
}
.ios-profile-hero__phone {
    font-size: 14px;
    color: var(--text-soft);
    font-weight: 500;
}
.ios-profile-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    background: #E8F5E9;
    color: #1E7A47;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
    width: fit-content;
    margin-top: 2px;
}
.ios-profile-hero__badge i {
    font-size: 9px;
    background: #1E7A47;
    color: #fff;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
    padding: 0 4px;
    margin: 24px 0 8px;
}

/* ═══ LIST GROUPÉE iOS — TOUT ARRONDI ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    overflow: hidden;
    margin-bottom: 8px;
}
.ios-list__item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    border-bottom: 1px solid var(--border);
    text-decoration: none;
    color: inherit;
    transition: background .15s;
}
.ios-list__item:last-child { border-bottom: none; }
.ios-list__item:hover { background: var(--surface); }
.ios-list__item:active { background: #F5F5F7; }

.ios-list__icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.ios-list__icon--blue { background: #E3F2FD; color: #0D47A1; }
.ios-list__icon--gray { background: #F2F2F7; color: #8E8E93; }

.ios-list__content {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.ios-list__title {
    font-family: var(--display);
    font-size: 14.5px;
    font-weight: 600;
    color: var(--ink);
    letter-spacing: -.2px;
}
.ios-list__sub {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 400;
    line-height: 1.4;
}
.ios-list__chevron {
    color: #C7C7CC;
    font-size: 13px;
    flex-shrink: 0;
}

/* ═══ LOGOUT ═══ */
.ios-logout {
    margin-top: 24px;
    margin-bottom: 16px;
}
.ios-logout button {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 15px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    font-family: inherit;
    font-size: 15px;
    font-weight: 600;
    color: var(--c-danger);
    cursor: pointer;
    transition: all .15s;
}
.ios-logout button:hover {
    background: var(--c-danger-bg);
    border-color: #FFCDD2;
}
.ios-logout button:active { transform: scale(.98); }
.ios-logout button i { font-size: 14px; }

/* ═══════════════════════════════════════════════════════════
   MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .particulier-profil {
        background: transparent;
        padding-top: 4px;
    }

    /* Hero */
    .ios-profile-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 20px 18px;
        margin-bottom: 12px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-profile-hero__avatar {
        width: 60px;
        height: 60px;
        font-size: 22px;
    }
    .ios-profile-hero__name {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -.5px;
        color: var(--ios-label);
    }
    .ios-profile-hero__phone {
        font-size: 13.5px;
        color: var(--ios-label-3);
    }
    .ios-profile-hero__badge {
        font-size: 11px;
        padding: 2px 9px;
        margin-top: 4px;
    }

    /* Section titles */
    .ios-section-title {
        font-size: 13px;
        font-weight: 600;
        text-transform: none;
        letter-spacing: -.1px;
        color: var(--ios-label-3);
        padding: 0 4px;
        margin: 22px 0 8px;
    }

    /* Listes iOS — coins bien arrondis */
    .ios-list {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: hidden;
        margin-bottom: 12px;
    }
    .ios-list__item {
        padding: 14px 16px;
        gap: 13px;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .ios-list__icon {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        font-size: 14px;
    }
    .ios-list__title {
        font-size: 15px;
        font-weight: 500;
        letter-spacing: -.1px;
        color: var(--ios-label);
    }
    .ios-list__sub {
        font-size: 13px;
        color: var(--ios-label-3);
    }
    .ios-list__chevron {
        color: #C7C7CC;
        font-size: 13px;
        opacity: .8;
    }

    /* Logout */
    .ios-logout {
        margin: 24px 0 calc(32px + env(safe-area-inset-bottom, 0px));
    }
    .ios-logout button {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        font-size: 16px;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        padding: 16px;
    }
}

@media (max-width: 480px) {
    .ios-profile-hero__avatar { width: 56px; height: 56px; font-size: 20px; }
    .ios-profile-hero__name { font-size: 19px; }
}
</style>
@endpush