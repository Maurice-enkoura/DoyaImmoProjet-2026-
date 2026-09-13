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

    {{-- ═══ MESSAGE ═══ --}}
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
   EN ATTENTE DE VALIDATION — Desktop + iOS natif
   ═══════════════════════════════════════════════════════════ */

.attente-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-gold:       #F5A623;
    --surface:      #F7F9FC;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;

    max-width: 640px;
    margin: 0 auto;
}

/* ═══ HERO ═══ */
.attente-hero {
    text-align: center;
    padding: 40px 24px 32px;
    background: linear-gradient(135deg, #FFF9F0 0%, #fff 60%);
    border: 1px solid var(--border);
    border-radius: 18px;
    margin-bottom: 24px;
    position: relative;
    overflow: hidden;
}
.attente-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--c-gold), #E8901A);
}

.attente-hero__icon {
    width: 88px;
    height: 88px;
    margin: 0 auto 20px;
    border-radius: 50%;
    background: linear-gradient(135deg, #FFF8E1, #FFECB3);
    color: var(--c-warning);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    box-shadow: 0 10px 30px rgba(245, 166, 35, .25);
    animation: pulse 3s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); box-shadow: 0 10px 30px rgba(245, 166, 35, .25); }
    50% { transform: scale(1.03); box-shadow: 0 14px 40px rgba(245, 166, 35, .35); }
}

.attente-hero__title {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 12px;
    line-height: 1.3;
    letter-spacing: -.4px;
}

.attente-hero__text {
    font-size: 15px;
    color: var(--text-soft);
    line-height: 1.65;
    margin: 0 auto;
    max-width: 460px;
}

/* ═══ SECTION TITLE ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    color: var(--muted);
    margin: 0 4px 10px;
    display: flex;
    align-items: center;
    gap: 7px;
    text-transform: uppercase;
    letter-spacing: .5px;
}
.ios-section-title i { color: var(--c-warning); font-size: 12px; }

/* ═══ LISTE iOS ═══ */
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
.ios-list__item--static { cursor: default; }

.ios-list__content { min-width: 0; }
.ios-list__title {
    font-size: 13px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 14.5px;
    font-weight: 700;
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
    padding: 5px 12px;
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    flex-shrink: 0;
}
.attente-status i { font-size: 10px; }

/* ═══ NOTE ═══ */
.attente-note {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: linear-gradient(135deg, #E8F0FE 0%, #F0F7FF 100%);
    border: 1px solid #BBDEFB;
    border-radius: 14px;
    margin-bottom: 24px;
}
.attente-note__icon {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #fff;
    color: #1565C0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(21, 101, 192, .15);
}
.attente-note__text {
    font-size: 13.5px;
    color: #0D47A1;
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
    transition: all .15s;
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
    border-color: var(--rust);
    color: var(--rust);
    background: var(--surface);
}
.ios-btn--ghost:active { transform: scale(.97); }

.ios-btn--danger {
    background: #fff;
    color: var(--c-danger);
    border-color: #FFCDD2;
}
.ios-btn--danger:hover {
    background: var(--c-danger-bg);
    border-color: var(--c-danger);
}
.ios-btn--danger:active { transform: scale(.97); }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .attente-page {
        background: transparent;
        padding: 8px 0;
        max-width: 100%;
    }

    /* Hero iOS */
    .attente-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 36px 20px 32px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        position: relative;
        overflow: hidden;
    }
    .attente-hero::before {
        height: 3px;
        background: linear-gradient(90deg, var(--c-gold), #E8901A);
        border-radius: 0;
    }
    .attente-hero__icon {
        width: 80px;
        height: 80px;
        font-size: 34px;
        margin-bottom: 16px;
        box-shadow: 0 8px 24px rgba(245, 166, 35, .3);
    }
    .attente-hero__title {
        font-size: 22px;
        letter-spacing: -.4px;
        margin-bottom: 10px;
    }
    .attente-hero__text {
        font-size: 14px;
        line-height: 1.55;
    }

    /* Section title iOS */
    .ios-section-title {
        font-size: 13px;
        text-transform: none;
        letter-spacing: 0;
        color: var(--ios-label-3);
        margin: 22px 4px 8px;
        font-weight: 600;
    }
    .ios-section-title i { font-size: 13px; }

    /* Liste iOS */
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
    .ios-list__item:last-child { border-bottom: none; }
    .ios-list__title {
        font-size: 15px;
        color: var(--ios-label);
        font-weight: 500;
        letter-spacing: -.2px;
    }
    .ios-list__value {
        font-size: 15px;
        font-weight: 600;
        color: var(--ios-label-3);
        max-width: 55%;
    }

    .attente-status {
        font-size: 11px;
        padding: 4px 10px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
    }

    /* Note iOS */
    .attente-note {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid #1565C0;
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .attente-note__icon {
        width: 36px;
        height: 36px;
        background: #E3F2FD;
        color: #1565C0;
        box-shadow: none;
        font-size: 15px;
    }
    .attente-note__text {
        font-size: 13.5px;
        color: var(--ios-label-2);
        line-height: 1.5;
        font-weight: 400;
    }

    /* Actions iOS */
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
        padding: 14px 20px;
        font-size: 15px;
        border-radius: 14px;
        letter-spacing: -.1px;
        font-weight: 600;
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--ios-blue);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-btn--ghost:active { background: #EFEFF4; }
    .ios-btn--danger {
        background: var(--ios-card);
        border: none;
        color: var(--c-danger);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-btn--danger:active { background: #FFEBEE; }
}

@media (max-width: 480px) {
    .attente-hero { padding: 30px 18px 26px; }
    .attente-hero__icon { width: 72px; height: 72px; font-size: 30px; }
    .attente-hero__title { font-size: 20px; }
    .attente-hero__text { font-size: 13.5px; }

    .ios-list__title { font-size: 14.5px; }
    .ios-list__value { font-size: 14.5px; }

    .attente-note__text { font-size: 13px; }
    .ios-btn { padding: 13px 18px; font-size: 14.5px; }
}
</style>
@endpush