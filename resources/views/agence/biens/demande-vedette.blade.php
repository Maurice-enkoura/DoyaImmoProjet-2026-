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

    {{-- ═══ ACTIONS FIXES ═══ --}}
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
   DEMANDE DE MISE EN VEDETTE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.vedette-page {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #F5A623;
    --c-gold-bg:    #FFF8E1;
    --surface:      #F7F9FC;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 22px 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ios-section-title:first-of-type { margin-top: 0; }
.ios-section-title i { color: var(--rust); font-size: 15px; }

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
    border-radius: 16px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    flex-wrap: wrap;
}
.vedette-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--c-gold), #E8901A);
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
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--ink);
    line-height: 1.3;
}
.vedette-hero__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: var(--muted);
}
.vedette-hero__meta i { color: var(--rust); font-size: 11px; margin-right: 4px; opacity: .85; }
.vedette-hero__dot { opacity: .5; }

.vedette-hero__badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 8px;
    padding: 4px 11px;
    background: var(--c-success-bg);
    color: var(--c-success);
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
}
.vedette-hero__badge i { font-size: 10px; color: var(--c-gold); }

/* ═══ DÉJÀ EN VEDETTE ═══ */
.vedette-already {
    background: linear-gradient(135deg, #FFFBF0 0%, #fff 60%);
    border: 1px solid #FFE0B2;
    border-radius: 16px;
    padding: 40px 24px;
    text-align: center;
}
.vedette-already__icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 18px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    box-shadow: 0 8px 24px rgba(245, 166, 35, .35);
    animation: pulse 3s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); box-shadow: 0 8px 24px rgba(245, 166, 35, .35); }
    50% { transform: scale(1.04); box-shadow: 0 12px 32px rgba(245, 166, 35, .45); }
}
.vedette-already h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    color: var(--c-warning);
    margin: 0 0 8px;
}
.vedette-already p {
    font-size: 13.5px;
    color: var(--text-soft);
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
    border: 1.5px solid var(--border);
    border-radius: 16px;
    padding: 20px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    margin: 0;
    transition: all .2s ease;
    position: relative;
    overflow: hidden;
}
.tarif-card:hover {
    border-color: var(--c-gold);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(245, 166, 35, .15);
}
.tarif-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--c-gold), #E8901A);
}

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
    background: linear-gradient(135deg, #FFFBF0 0%, #FFF8E1 100%);
    border: 1px solid #FFE0B2;
    border-radius: 14px;
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
    color: #5D4037;
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
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 12.5px;
    font-weight: 800;
    box-shadow: 0 2px 6px rgba(245, 166, 35, .3);
}

/* ═══ CONTACT ═══ */
.contact-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px 22px;
    text-align: center;
    margin-bottom: 20px;
}
.contact-card__text {
    font-size: 13.5px;
    color: var(--text-soft);
    margin: 0 0 16px;
    line-height: 1.55;
}
.contact-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 16px;
}
.contact-card__phone {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border-radius: 999px;
    font-size: 13.5px;
    font-weight: 700;
    font-family: var(--display);
}
.contact-card__phone i { font-size: 13px; }

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
    transition: all .15s;
    white-space: nowrap;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181,80,42,.2);
}
.ios-btn--primary:hover { background: #9A4523; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(181,80,42,.3); }
.ios-btn--primary:active { transform: scale(.97); }
.ios-btn--success {
    background: linear-gradient(135deg, #25D366, #1DA851);
    color: #fff;
    border-color: #25D366;
}
.ios-btn--success:hover { background: #1DA851; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(37, 211, 102, .3); }
.ios-btn--success:active { transform: scale(.97); }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--ghost:active { transform: scale(.97); }

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

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 768px) {
    .tarifs-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 480px) {
    .tarifs-grid { grid-template-columns: 1fr; }
    .contact-actions { grid-template-columns: 1fr; }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .vedette-page { background: transparent; padding-top: 4px; }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 6px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-sub {
        font-size: 13px;
        color: var(--ios-label-3);
        margin: 0 4px 16px;
    }

    /* ─── HERO BIEN iOS ─── */
    .vedette-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .vedette-hero::before {
        left: 0; right: 0;
        height: 3px;
        border-radius: 0;
    }
    .vedette-hero__media {
        width: 68px;
        height: 68px;
        border-radius: 14px;
        border: none;
    }
    .vedette-hero__title {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
        color: var(--ios-label);
    }
    .vedette-hero__meta {
        font-size: 13.5px;
        color: var(--ios-label-3);
    }
    .vedette-hero__badge {
        font-size: 11.5px;
        padding: 5px 12px;
        margin-top: 10px;
    }

    /* ─── DÉJÀ EN VEDETTE iOS ─── */
    .vedette-already {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 36px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .vedette-already__icon {
        width: 68px;
        height: 68px;
        font-size: 28px;
        margin-bottom: 16px;
    }
    .vedette-already h3 {
        font-size: 17px;
        letter-spacing: -.3px;
        color: var(--c-warning);
    }
    .vedette-already p {
        font-size: 13.5px;
        color: var(--ios-label-3);
    }

    /* ─── TARIFS iOS ─── */
    .tarifs-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .tarif-card {
        background: var(--ios-card);
        border: 1.5px solid transparent;
        border-radius: var(--ios-radius-lg);
        padding: 16px 18px;
        flex-direction: row;
        align-items: center;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        position: relative;
        overflow: visible;
    }
    .tarif-card::before {
        content: '';
        position: absolute;
        left: 0; top: 20%;
        bottom: 20%;
        width: 3px;
        height: auto;
        border-radius: 0 3px 3px 0;
        background: linear-gradient(180deg, var(--c-gold), #E8901A);
    }
    .tarif-card:hover {
        border-color: transparent;
        transform: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .tarif-card:active {
        background: #FFFBF0;
    }

    /* Réorganiser le contenu en ligne */
    .tarif-card__days {
        flex-direction: column;
        align-items: center;
        gap: 0;
        min-width: 48px;
        margin: 0;
    }
    .tarif-card__num {
        font-size: 24px;
        color: var(--c-warning);
    }
    .tarif-card__unit {
        font-size: 10.5px;
        color: var(--ios-label-3);
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
        background: linear-gradient(135deg, var(--c-gold), #E8901A);
        border: none;
        box-shadow: 0 4px 12px rgba(245, 166, 35, .3);
    }
    .tarif-card__btn:active {
        transform: scale(.95);
        box-shadow: 0 2px 6px rgba(245, 166, 35, .4);
    }

    /* ─── HOWTO iOS ─── */
    .howto-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .howto-list li {
        font-size: 14px;
        color: var(--ios-label-2);
        padding-left: 36px;
        line-height: 1.5;
        margin-bottom: 14px;
    }
    .howto-list li::before {
        width: 24px;
        height: 24px;
        font-size: 12px;
        background: linear-gradient(135deg, var(--c-gold), #E8901A);
    }

    /* ─── CONTACT iOS ─── */
    .contact-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 20px 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .contact-card__text {
        font-size: 14px;
        color: var(--ios-label-2);
        margin-bottom: 16px;
    }
    .contact-actions {
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 16px;
    }
    .contact-actions .ios-btn {
        padding: 14px 18px;
        font-size: 15px;
        border-radius: 14px;
    }
    .contact-card__phone {
        background: #EFEFF4;
        color: var(--ios-label);
        padding: 12px 18px;
        font-size: 15px;
        border-radius: 12px;
        font-family: var(--display);
    }
    .contact-card__phone i { color: var(--rust); }

    /* ─── ACTIONS FIXES ─── */
    .ios-form-actions {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(242, 242, 247, 0.95);
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        border-top: 0.5px solid var(--ios-separator);
        padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
        margin: 0;
        gap: 10px;
        z-index: 100;
        border-radius: 0;
    }
    .ios-form-actions .ios-btn {
        width: 100%;
        padding: 14px 18px;
        font-size: 15px;
        border-radius: 14px;
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--ios-blue);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-btn--ghost:active { background: #EFEFF4; }

    /* Padding bottom */
    .vedette-page {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }
}

@media (max-width: 480px) {
    .vedette-hero { padding: 14px; gap: 12px; }
    .vedette-hero__media { width: 60px; height: 60px; }
    .vedette-hero__title { font-size: 15px; }

    .tarif-card { padding: 14px 16px; }
    .tarif-card__num { font-size: 22px; }
    .tarif-card__price { font-size: 17px; }
}
</style>
@endpush