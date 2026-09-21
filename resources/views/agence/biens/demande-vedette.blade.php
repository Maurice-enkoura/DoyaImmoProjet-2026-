@extends('layouts.dashboard-agence')

@section('title', 'Demande de mise en vedette — DoyaImmo')
@section('page_title', 'Demande de mise en vedette')
@section('page_sub', 'Mettez votre bien en avant')

@section('content')
<div class="view active vedette-page">

    @php
        $image = $bien->medias->where('type_media', 'image')->first();
    @endphp

    {{-- ═══ HERO BIEN ═══ --}}
    <section class="vedette-hero">
        <div class="vedette-hero__media">
            @if($image)
                <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $bien->titre }}">
            @else
                <div class="vedette-hero__placeholder">
                    <i class="fa-regular fa-image"></i>
                </div>
            @endif
        </div>

        <div class="vedette-hero__body">
            <h1 class="vedette-hero__title">{{ $bien->titre }}</h1>
            <div class="vedette-hero__meta">
                <span><i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}</span>
                <span class="vedette-hero__dot">·</span>
                <span>{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</span>
            </div>
            @if($bien->est_vedette)
                <span class="vedette-hero__badge">
                    <i class="fa-solid fa-star"></i> Déjà en vedette
                </span>
            @endif
        </div>
    </section>

    @if($bien->est_vedette)

        {{-- ═══ DÉJÀ EN VEDETTE ═══ --}}
        <div class="vedette-already">
            <div class="vedette-already__icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <h3>Ce bien est déjà en vedette</h3>
            <p>Attendez la fin de la période actuelle pour faire une nouvelle demande.</p>
            <a href="{{ route('agence.biens.show', $bien->slug) }}" class="ios-btn ios-btn--primary">
                <i class="fa-solid fa-eye"></i> Voir le bien
            </a>
        </div>

    @else

        {{-- ═══ SECTION TARIFS ═══ --}}
        <div class="ios-section-title">
            <i class="fa-solid fa-tags"></i> Choisissez une durée
        </div>
        <p class="ios-section-sub">
            Plus la durée est longue, plus votre bien sera visible longtemps.
        </p>

        <div class="tarifs-grid">
            @foreach($tarifs as $jours => $prix)
                <form action="{{ route('agence.biens.vedette.store', $bien->slug) }}" method="POST" class="tarif-card">
                    @csrf
                    <input type="hidden" name="duree" value="{{ $jours }}">

                    <div class="tarif-card__days">
                        <span class="tarif-card__num">{{ $jours }}</span>
                        <span class="tarif-card__unit">jour{{ $jours > 1 ? 's' : '' }}</span>
                    </div>

                    <div class="tarif-card__price">
                        {{ number_format($prix, 0, ',', ' ') }}
                        <small>FCFA</small>
                    </div>

                    <div class="tarif-card__hint">mise en vedette</div>

                    <button type="submit" class="ios-btn ios-btn--primary tarif-card__btn">
                        <i class="fa-solid fa-star"></i> Demander
                    </button>
                </form>
            @endforeach
        </div>

        {{-- ═══ COMMENT ÇA MARCHE ═══ --}}
        <div class="ios-section-title">
            <i class="fa-solid fa-circle-info"></i> Comment ça marche ?
        </div>

        <div class="howto-card">
            <ol class="howto-list">
                <li>Vous faites une demande de mise en vedette</li>
                <li>Vous contactez DoyaImmo pour finaliser le paiement</li>
                <li>Une fois le paiement confirmé, votre bien sera mis en vedette</li>
                <li>La vedette expire automatiquement à la fin de la période choisie</li>
            </ol>
        </div>

        {{-- ═══ CONTACT ═══ --}}
        <div class="ios-section-title">
            <i class="fa-solid fa-headset"></i> Finaliser votre demande
        </div>

    @endif

    {{-- ═══ ACTIONS FIXES (desktop uniquement) ═══ --}}
    <div class="ios-form-actions">
        <a href="{{ route('agence.biens.show', $bien->slug) }}" class="ios-btn ios-btn--ghost">
            <i class="fa-solid fa-arrow-left"></i> Retour au bien
        </a>
    </div>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DEMANDE DE MISE EN VEDETTE — Design épuré
   ═══════════════════════════════════════════════════════════ */

.vedette-page {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    color: var(--ink);
    margin: 22px 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: -.2px;
}
.ios-section-title:first-of-type { margin-top: 0; }
.ios-section-title i { color: var(--muted); font-size: 14px; }

.ios-section-sub {
    font-size: 13px;
    color: var(--muted);
    margin: 0 0 16px;
    line-height: 1.5;
}

/* ═══ HERO BIEN ═══ */
.vedette-hero {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.vedette-hero__media {
    width: 72px;
    height: 72px;
    border-radius: 12px;
    overflow: hidden;
    background: var(--surface);
    border: 1px solid var(--border);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.vedette-hero__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.vedette-hero__placeholder {
    color: var(--muted);
    font-size: 26px;
    opacity: .4;
}

.vedette-hero__body { flex: 1; min-width: 0; }
.vedette-hero__title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--ink);
    line-height: 1.3;
    letter-spacing: -.2px;
}
.vedette-hero__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: var(--muted);
}
.vedette-hero__meta i { color: var(--muted); font-size: 11px; margin-right: 4px; }
.vedette-hero__dot { color: var(--border); }

.vedette-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 8px;
    padding: 4px 11px;
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
}
.vedette-hero__badge i { font-size: 10px; }

/* ═══ DÉJÀ EN VEDETTE ═══ */
.vedette-already {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 40px 24px;
    text-align: center;
}
.vedette-already__icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
}
.vedette-already h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 6px;
    letter-spacing: -.2px;
}
.vedette-already p {
    font-size: 13px;
    color: var(--muted);
    margin: 0 0 20px;
    max-width: 400px;
    margin-inline: auto;
    line-height: 1.55;
}

/* ═══ TARIFS ═══ */
.tarifs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.tarif-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    margin: 0;
    transition: border-color .15s;
}
.tarif-card:hover { border-color: #d8d8d8; }

.tarif-card__days {
    display: inline-flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 2px;
}
.tarif-card__num {
    font-family: var(--display);
    font-size: 26px;
    font-weight: 800;
    color: var(--ink);
    line-height: 1;
    letter-spacing: -.5px;
}
.tarif-card__unit {
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
}

.tarif-card__price {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.2;
    letter-spacing: -.3px;
}
.tarif-card__price small {
    font-size: 11px;
    font-weight: 600;
    opacity: .75;
    margin-left: 3px;
}

.tarif-card__hint {
    font-size: 11.5px;
    color: var(--muted);
    margin-bottom: 8px;
}

.tarif-card__btn {
    width: 100%;
    padding: 11px 14px;
    font-size: 13px;
    border-radius: 10px;
    margin-top: auto;
}

/* ═══ HOWTO ═══ */
.howto-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 22px;
    margin-bottom: 20px;
}
.howto-list {
    list-style: none;
    padding: 0;
    margin: 0;
    counter-reset: step;
}
.howto-list li {
    position: relative;
    padding-left: 38px;
    margin-bottom: 12px;
    font-size: 13.5px;
    color: var(--text-soft);
    line-height: 1.55;
    counter-increment: step;
}
.howto-list li:last-child { margin-bottom: 0; }
.howto-list li::before {
    content: counter(step);
    position: absolute;
    left: 0; top: 0;
    width: 26px; height: 26px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--ink);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 12px;
    font-weight: 700;
}

/* ═══ BOUTONS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.ios-btn--primary:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
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

/* ═══ ACTIONS ═══ */
.ios-form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    margin-top: 20px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.ios-form-actions .ios-btn { flex: 1; }

/* ═══ RESPONSIVE ═══ */
@media (max-width: 768px) {
    .tarifs-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 480px) {
    .tarifs-grid { grid-template-columns: 1fr; }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .vedette-page {
        background: transparent;
        padding-top: 4px;
        /* ✅ Plus de padding-bottom car pas de barre fixe */
    }

    /* SECTION TITLES */
    .ios-section-title {
        font-size: 15px;
        margin: 20px 4px 6px;
        color: var(--ink);
    }
    .ios-section-title i { font-size: 14px; }
    .ios-section-sub { font-size: 13px; margin: 0 4px 16px; }

    /* HERO BIEN */
    .vedette-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .vedette-hero__media {
        width: 68px;
        height: 68px;
        border-radius: 14px;
        border: none;
    }
    .vedette-hero__title {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.2px;
    }
    .vedette-hero__meta {
        font-size: 13px;
        color: var(--muted);
    }
    .vedette-hero__badge {
        font-size: 11.5px;
        padding: 5px 12px;
        margin-top: 10px;
        background: #F5F5F7;
        color: var(--text-soft);
    }

    /* DÉJÀ EN VEDETTE */
    .vedette-already {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 36px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .vedette-already__icon {
        width: 64px;
        height: 64px;
        font-size: 26px;
        background: #F5F5F7;
    }
    .vedette-already h3 {
        font-size: 16px;
        color: var(--ink);
    }
    .vedette-already p {
        font-size: 13px;
        color: var(--muted);
    }

    /* TARIFS */
    .tarifs-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .tarif-card {
        background: var(--ios-card);
        border: 1px solid transparent;
        border-radius: var(--ios-radius-lg);
        padding: 16px 18px;
        flex-direction: row;
        align-items: center;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .tarif-card:hover { border-color: transparent; }

    .tarif-card__days {
        flex-direction: column;
        align-items: center;
        gap: 0;
        min-width: 48px;
        margin: 0;
    }
    .tarif-card__num {
        font-size: 24px;
        color: var(--ink);
    }
    .tarif-card__unit {
        font-size: 10.5px;
        color: var(--muted);
    }
    .tarif-card__price {
        font-size: 18px;
        flex: 1;
        text-align: left;
    }
    .tarif-card__hint { display: none; }

    .tarif-card__btn {
        width: auto;
        padding: 10px 16px;
        font-size: 13.5px;
        border-radius: 12px;
        margin: 0;
        flex-shrink: 0;
        background: var(--rust);
        border: none;
    }
    .tarif-card__btn:hover {
        background: #9A4523;
    }

    /* HOWTO */
    .howto-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .howto-list li {
        font-size: 14px;
        color: var(--text-soft);
        padding-left: 36px;
        line-height: 1.5;
        margin-bottom: 14px;
    }
    .howto-list li::before {
        width: 24px;
        height: 24px;
        font-size: 12px;
        background: #F5F5F7;
        color: var(--ink);
    }

    /* ✅ ACTIONS MASQUÉES EN MOBILE (le layout a déjà un bouton retour natif) */
    .ios-form-actions { display: none; }
}

@media (max-width: 480px) {
    .vedette-hero { padding: 14px; gap: 12px; }
    .vedette-hero__media { width: 60px; height: 60px; }
    .vedette-hero__title { font-size: 14.5px; }

    .tarif-card { padding: 14px 16px; }
    .tarif-card__num { font-size: 22px; }
    .tarif-card__price { font-size: 17px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush