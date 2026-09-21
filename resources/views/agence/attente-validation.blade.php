@extends('layouts.dashboard-agence')

@section('title', 'En attente de validation — DoyaImmo')
@section('page_title', 'En attente de validation')
@section('page_sub', 'Votre agence est en cours de validation')

@section('content')
<div class="view active attente-page">

    {{-- ═══ HERO ═══ --}}
    <section class="attente-hero">
        <div class="attente-hero__icon">
            <i class="fa-regular fa-clock"></i>
        </div>

        <h1 class="attente-hero__title">
            Agence en cours de validation
        </h1>

        <p class="attente-hero__text">
            Votre demande d'inscription a bien été enregistrée.
            Nos équipes vérifient vos documents dans les plus brefs délais.
        </p>
    </section>

    {{-- ═══ INFOS ═══ --}}
    <div class="ios-section-title">
        <i class="fa-solid fa-circle-info"></i> Informations
    </div>

    <div class="ios-list">
        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Délai moyen</span>
            </div>
            <span class="ios-list__value">48 h</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Agence</span>
            </div>
            <span class="ios-list__value">
                {{ $agence->nom_agence ?? 'Non renseigné' }}
            </span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Statut</span>
            </div>
            <span class="attente-status">
                <i class="fa-solid fa-clock"></i>
                En attente
            </span>
        </div>
    </div>

    {{-- ═══ NOTE ═══ --}}
    <div class="attente-note">
        <div class="attente-note__icon">
            <i class="fa-regular fa-envelope"></i>
        </div>
        <div class="attente-note__text">
            Une notification vous sera envoyée dès que votre agence sera validée.
        </div>
    </div>

    {{-- ═══ ACTIONS ═══ --}}
    <div class="attente-actions">
        <a href="{{ route('home') }}" class="ios-btn ios-btn--ghost">
            <i class="fa-solid fa-house"></i>
            Retour à l'accueil
        </a>

        <form action="{{ route('logout') }}" method="POST" class="attente-actions__form">
            @csrf
            <button type="submit" class="ios-btn ios-btn--danger">
                <i class="fa-solid fa-right-from-bracket"></i>
                Se déconnecter
            </button>
        </form>
    </div>

</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   EN ATTENTE DE VALIDATION — Design épuré
   ═══════════════════════════════════════════════════════════ */

.attente-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --surface:      #F7F9FC;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;

    max-width: 640px;
    margin: 0 auto;
}

/* ═══ HERO ═══ */
.attente-hero {
    text-align: center;
    padding: 36px 24px 30px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 18px;
    margin-bottom: 24px;
}

.attente-hero__icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 34px;
}

.attente-hero__title {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 12px;
    line-height: 1.3;
    letter-spacing: -.3px;
}

.attente-hero__text {
    font-size: 14.5px;
    color: var(--text-soft);
    line-height: 1.65;
    margin: 0 auto;
    max-width: 460px;
}

/* ═══ SECTION TITLE ═══ */
.ios-section-title {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--muted);
    margin: 0 4px 10px;
    display: flex;
    align-items: center;
    gap: 7px;
    text-transform: uppercase;
    letter-spacing: .5px;
}
.ios-section-title i { color: var(--muted); font-size: 12px; }

/* ═══ LISTE ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 20px;
}
.ios-list__item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px 20px;
    border-bottom: 1px solid var(--border);
}
.ios-list__item:last-child { border-bottom: none; }

.ios-list__content { min-width: 0; }
.ios-list__title {
    font-size: 13px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
    text-align: right;
    flex-shrink: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 60%;
}

/* Statut pill */
.attente-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    flex-shrink: 0;
}
.attente-status i { font-size: 10px; }

/* ═══ NOTE ═══ */
.attente-note {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: #F5F5F7;
    border-radius: 14px;
    margin-bottom: 24px;
}
.attente-note__icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #fff;
    color: var(--muted);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.attente-note__text {
    font-size: 13.5px;
    color: var(--text-soft);
    line-height: 1.55;
    font-weight: 500;
}

/* ═══ ACTIONS ═══ */
.attente-actions {
    display: flex;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.attente-actions__form { flex: 1; margin: 0; }

.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 13px 22px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
    flex: 1;
}
.ios-btn i { font-size: 13px; }

.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--ghost:hover {
    background: #F5F5F7;
    border-color: #d8d8d8;
    color: var(--ink);
}

.ios-btn--danger {
    background: #fff;
    color: var(--c-danger);
    border-color: var(--border);
}
.ios-btn--danger:hover {
    background: var(--c-danger-bg);
    border-color: #f5c6c6;
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .attente-page {
        background: transparent;
        padding: 8px 0;
        max-width: 100%;
    }

    .attente-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 32px 20px 28px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .attente-hero__icon {
        width: 72px;
        height: 72px;
        font-size: 30px;
        margin-bottom: 16px;
        background: #F5F5F7;
    }
    .attente-hero__title {
        font-size: 20px;
        margin-bottom: 10px;
    }
    .attente-hero__text {
        font-size: 14px;
        line-height: 1.55;
    }

    .ios-section-title {
        font-size: 12.5px;
        margin: 22px 4px 8px;
        color: var(--muted);
    }

    .ios-list {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 16px;
    }
    .ios-list__item {
        padding: 14px 16px;
        gap: 12px;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .ios-list__title {
        font-size: 14px;
        color: var(--muted);
        font-weight: 500;
    }
    .ios-list__value {
        font-size: 14px;
        font-weight: 600;
        color: var(--ink);
        max-width: 55%;
    }

    .attente-status {
        font-size: 11.5px;
        padding: 4px 10px;
    }

    .attente-note {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid #d8d8d8;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .attente-note__icon {
        width: 34px;
        height: 34px;
        background: #F5F5F7;
        border: none;
        font-size: 14px;
    }
    .attente-note__text {
        font-size: 13.5px;
        color: var(--text-soft);
        font-weight: 400;
    }

    .attente-actions {
        flex-direction: column-reverse;
        gap: 10px;
        padding-top: 4px;
        border-top: none;
        margin-bottom: 8px;
    }
    .attente-actions__form { flex: none; width: 100%; }

    .ios-btn {
        width: 100%;
        padding: 13px 18px;
        font-size: 14.5px;
        border-radius: 12px;
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--text-soft);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-btn--danger {
        background: var(--ios-card);
        border: none;
        color: var(--c-danger);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
}

@media (max-width: 480px) {
    .attente-hero { padding: 28px 18px 24px; }
    .attente-hero__icon { width: 66px; height: 66px; font-size: 28px; }
    .attente-hero__title { font-size: 19px; }
    .attente-hero__text { font-size: 13.5px; }

    .ios-list__title { font-size: 13.5px; }
    .ios-list__value { font-size: 13.5px; }

    .attente-note__text { font-size: 13px; }
    .ios-btn { padding: 12px 16px; font-size: 14px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush