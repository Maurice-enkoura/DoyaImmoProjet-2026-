@extends('layouts.dashboard-agence')

@section('title', 'Avis reçus — DoyaImmo')
@section('page_title', 'Avis reçus')
@section('page_sub', 'Ce que pensent vos clients de votre service')

@section('content')
<div class="view active ios-avis-page">

    @php
        $total       = $evaluations->total();
        $moyenne     = $evaluations->avg('note') ?? 0;
        $sansReponse = $evaluations->whereNull('reponse_agence')->count();
        $negatifs    = $evaluations->where('note', '<', 3)->count();
        $avisTraites = $avisTraites ?? 0;
    @endphp

    {{-- ═══ BANNIÈRE : AVIS TRAITÉS ═══ --}}
    @if($avisTraites > 0)
        <a href="{{ route('agence.historique') }}"
           class="ios-traited-banner">
            <div class="ios-traited-banner__left">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>
                    <strong>{{ $avisTraites }}</strong>
                    {{ $avisTraites > 1 ? 'avis déjà traités' : 'avis déjà traité' }}
                </span>
            </div>
            <i class="fa-solid fa-chevron-right ios-traited-banner__arrow"></i>
        </a>
    @endif

    @if($total > 0)

        {{-- ═══ RÉSUMÉ ═══ --}}
        <div class="ios-summary">
            <div class="ios-summary__item">
                <div class="ios-summary__value">
                    <i class="fa-solid fa-star"></i>
                    {{ number_format($moyenne, 1) }}
                </div>
                <span class="ios-summary__label">sur 5</span>
            </div>

            <div class="ios-summary__sep"></div>

            <div class="ios-summary__item">
                <div class="ios-summary__value">{{ $total }}</div>
                <span class="ios-summary__label">
                    {{ $total > 1 ? 'avis à traiter' : 'avis à traiter' }}
                </span>
            </div>

            @if($negatifs > 0)
                <div class="ios-summary__sep"></div>
                <div class="ios-summary__item ios-summary__item--attention">
                    <div class="ios-summary__value">{{ $negatifs }}</div>
                    <span class="ios-summary__label">
                        <i class="fa-solid fa-exclamation"></i> négatif{{ $negatifs > 1 ? 's' : '' }}
                    </span>
                </div>
            @endif
        </div>

        {{-- ═══ LISTE DES AVIS ═══ --}}
        <div class="ios-section-header">
            À traiter
            <span class="ios-section-header__count">{{ $total }}</span>
        </div>

        <div class="ios-avis-list">
            @foreach($evaluations as $evaluation)
                @php
                    $note    = (int) $evaluation->note;
                    $initial = strtoupper(mb_substr($evaluation->particulier->user->prenom ?? 'C', 0, 1));
                @endphp

                <article class="ios-avis-card">

                    {{-- En-tête --}}
                    <header class="ios-avis-card__head">
                        <div class="ios-avis-card__client">
                            <div class="ios-avis-card__avatar">{{ $initial }}</div>
                            <div class="ios-avis-card__client-body">
                                <strong class="ios-avis-card__name">
                                    {{ $evaluation->particulier->user->prenom ?? '' }}
                                    {{ $evaluation->particulier->user->nom ?? '' }}
                                </strong>
                                <span class="ios-avis-card__date">
                                    {{ $evaluation->created_at->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="ios-avis-card__rating">
                            <div class="ios-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $note ? 'is-on' : '' }}"></i>
                                @endfor
                            </div>
                            <span class="ios-avis-card__score">{{ $note }}.0</span>
                        </div>
                    </header>

                    {{-- Commentaire --}}
                    @if($evaluation->commentaire)
                        <p class="ios-avis-card__comment">{{ $evaluation->commentaire }}</p>
                    @endif

                    {{-- Bien concerné --}}
                    @if($evaluation->proposition && $evaluation->proposition->bien)
                        <div class="ios-avis-card__bien">
                            <i class="fa-regular fa-building"></i>
                            {{ $evaluation->proposition->bien->titre ?? 'Bien' }}
                        </div>
                    @endif

                    {{-- Actions --}}
                    <footer class="ios-avis-card__footer">
                        <div class="ios-avis-card__actions">
                            <a href="{{ route('agence.evaluations.show', $evaluation) }}"
                               class="ios-btn ios-btn--primary">
                                <i class="fa-solid fa-reply"></i> Répondre
                            </a>
                            <a href="{{ route('agence.evaluations.show', $evaluation) }}"
                               class="ios-btn ios-btn--gray">
                                <i class="fa-solid fa-eye"></i> Détails
                            </a>
                        </div>
                    </footer>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="ios-pagination">
            {{ $evaluations->links() }}
        </div>

    @else

        {{-- ═══ EMPTY STATE ═══ --}}
        <div class="ios-empty">
            <div class="ios-empty__icon">
                <i class="fa-regular fa-star"></i>
            </div>
            <h3 class="ios-empty__title">
                @if($avisTraites > 0)
                    Tous les avis sont traités
                @else
                    Aucun avis reçu
                @endif
            </h3>
            <p class="ios-empty__text">
                @if($avisTraites > 0)
                    Bravo ! Vous avez répondu à tous les avis de vos clients.
                @else
                    Les avis apparaîtront ici une fois que vos clients auront terminé une visite.
                @endif
            </p>

            @if($avisTraites > 0)
                <a href="{{ route('agence.historique') }}" class="ios-btn ios-btn--gray">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Voir l'historique
                </a>
            @endif
        </div>

    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   AVIS REÇUS — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.ios-avis-page {
    --ios-blue:      #007AFF;
    --ios-green:     #34C759;
    --ios-red:       #FF3B30;
    --ios-orange:    #FF9500;
    --ios-yellow:    #FFCC00;
    --ios-gray:      #8E8E93;
    --ios-gray-3:    #C7C7CC;
    --ios-gray-5:    #E5E5EA;
    --ios-gray-6:    #F2F2F7;

    --ios-bg:        #F2F2F7;
    --ios-card:      #FFFFFF;
    --ios-label:     #000000;
    --ios-label-2:   rgba(60, 60, 67, 0.6);
    --ios-label-3:   rgba(60, 60, 67, 0.3);
    --ios-separator: rgba(60, 60, 67, 0.29);

    --ios-radius:    10px;
    --ios-radius-lg: 12px;

    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text",
                 "Helvetica Neue", Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;

    background: var(--ios-bg);
    padding-bottom: 40px;
}

/* ═══ BANNIÈRE AVIS TRAITÉS ═══ */
.ios-traited-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 18px;
    background: rgba(0, 122, 255, 0.08);
    border-radius: var(--ios-radius-lg);
    margin-bottom: 16px;
    text-decoration: none;
    color: #0040DD;
    transition: transform 0.15s;
}
.ios-traited-banner:hover { background: rgba(0, 122, 255, 0.12); }
.ios-traited-banner:active { transform: scale(0.99); }

.ios-traited-banner__left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.ios-traited-banner__left i {
    font-size: 15px;
    flex-shrink: 0;
}
.ios-traited-banner__left span {
    font-size: 13.5px;
    letter-spacing: -0.1px;
}
.ios-traited-banner__left strong {
    font-weight: 700;
}

.ios-traited-banner__arrow {
    font-size: 12px;
    opacity: 0.6;
    flex-shrink: 0;
}

/* ═══ RÉSUMÉ ═══ */
.ios-summary {
    display: flex;
    align-items: center;
    gap: 28px;
    padding: 22px 26px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.ios-summary__item {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.ios-summary__value {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 28px;
    font-weight: 700;
    line-height: 1;
    color: var(--ios-label);
    letter-spacing: -0.5px;
}
.ios-summary__value i {
    color: var(--ios-yellow);
    font-size: 22px;
}
.ios-summary__label {
    font-size: 13px;
    color: var(--ios-label-2);
    font-weight: 500;
    letter-spacing: -0.1px;
}
.ios-summary__label i { margin-right: 3px; }

.ios-summary__item--attention .ios-summary__value { color: var(--ios-orange); }
.ios-summary__item--attention .ios-summary__label { color: var(--ios-orange); }

.ios-summary__sep {
    width: 1px;
    height: 38px;
    background: var(--ios-separator);
    flex-shrink: 0;
}

/* ═══ SECTION HEADER ═══ */
.ios-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--ios-label-2);
    margin: 24px 0 8px;
    padding: 0 4px;
}
.ios-section-header:first-of-type { margin-top: 0; }
.ios-section-header__count {
    font-size: 12px;
    font-weight: 500;
    background: var(--ios-gray-5);
    color: var(--ios-label-2);
    padding: 2px 8px;
    border-radius: 100px;
    text-transform: none;
    letter-spacing: 0;
}

/* ═══ LISTE AVIS : grille sur desktop ═══ */
.ios-avis-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

/* ═══ CARTE AVIS ═══ */
.ios-avis-card {
    display: flex;
    flex-direction: column;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    padding: 18px 20px;
    transition: transform 0.15s, box-shadow 0.15s;
}
.ios-avis-card:hover {
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
}
.ios-avis-card:active { transform: scale(0.99); }

.ios-avis-card__head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.ios-avis-card__client {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.ios-avis-card__avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, #B4532A, #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 600;
    flex-shrink: 0;
    letter-spacing: -0.3px;
}
.ios-avis-card__client-body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.ios-avis-card__name {
    display: block;
    font-size: 15px;
    font-weight: 600;
    color: var(--ios-label);
    line-height: 1.2;
    letter-spacing: -0.2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ios-avis-card__date {
    display: block;
    font-size: 12.5px;
    color: var(--ios-label-2);
    letter-spacing: -0.1px;
}

.ios-avis-card__rating {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}
.ios-stars {
    display: inline-flex;
    gap: 2px;
    font-size: 15px;
}
.ios-stars .fa-star { color: var(--ios-gray-3); }
.ios-stars .fa-star.is-on { color: var(--ios-yellow); }

.ios-avis-card__score {
    font-size: 15px;
    font-weight: 700;
    color: var(--ios-label);
    letter-spacing: -0.3px;
}

.ios-avis-card__comment {
    font-size: 14.5px;
    line-height: 1.55;
    color: var(--ios-label);
    margin: 0 0 12px;
    letter-spacing: -0.1px;
    flex: 1;
}

.ios-avis-card__bien {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: var(--ios-gray-6);
    border-radius: 100px;
    font-size: 12.5px;
    color: var(--ios-label-2);
    margin-bottom: 14px;
    letter-spacing: -0.1px;
    align-self: flex-start;
}
.ios-avis-card__bien i {
    font-size: 11px;
    color: #B4532A;
}

.ios-avis-card__footer {
    padding-top: 14px;
    border-top: 0.5px solid var(--ios-separator);
    margin-top: auto;
}

.ios-avis-card__actions {
    display: flex;
    gap: 10px;
}

/* ═══ BOUTONS iOS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 12px 18px;
    border-radius: var(--ios-radius);
    font-size: 15px;
    font-weight: 600;
    font-family: inherit;
    letter-spacing: -0.2px;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: opacity 0.1s, transform 0.1s;
    white-space: nowrap;
    min-height: 44px;
    flex: 1;
}
.ios-btn:active { transform: scale(0.98); opacity: 0.85; }

.ios-btn--primary { background: #B4532A; color: #fff; }
.ios-btn--primary:hover { background: #9A4523; }
.ios-btn--gray    { background: var(--ios-gray-5); color: var(--ios-blue); }
.ios-btn--gray:hover { background: #D8D8DD; }
.ios-btn--full    { width: 100%; flex: 1 1 100%; }

/* ═══ EMPTY STATE ═══ */
.ios-empty {
    text-align: center;
    padding: 70px 20px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    margin-top: 20px;
}
.ios-empty__icon {
    font-size: 52px;
    color: var(--ios-gray-3);
    margin-bottom: 16px;
}
.ios-empty__icon i.fa-star { color: var(--ios-yellow); opacity: 0.6; }
.ios-empty__title {
    font-size: 18px;
    font-weight: 600;
    color: var(--ios-label);
    margin: 0 0 8px;
    letter-spacing: -0.3px;
}
.ios-empty__text {
    font-size: 14px;
    color: var(--ios-label-2);
    margin: 0 auto 22px;
    max-width: 360px;
    line-height: 1.55;
    letter-spacing: -0.1px;
}

/* ═══ PAGINATION ═══ */
.ios-pagination {
    margin-top: 24px;
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 5px;
}
.ios-pagination .pagination,
.ios-pagination nav {
    display: flex;
    gap: 5px;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
}
.ios-pagination a,
.ios-pagination span:not(.sr-only) {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid var(--ios-separator);
    color: var(--ios-label-2);
    text-decoration: none;
    font-size: 13px;
    transition: all 0.2s;
    min-width: 36px;
    text-align: center;
    background: var(--ios-card);
    letter-spacing: -0.1px;
}
.ios-pagination a:hover {
    background: var(--ios-gray-6);
    color: var(--ios-blue);
}
.ios-pagination .active span,
.ios-pagination [aria-current="page"] span,
.ios-pagination [aria-current="page"] {
    background: #B4532A;
    color: #fff;
    border-color: #B4532A;
}
.ios-pagination .disabled span {
    opacity: 0.4;
    cursor: not-allowed;
}

/* ═══════════════════════════════════════════════════════════
   DESKTOP — Améliorations
   ═══════════════════════════════════════════════════════════ */
@media (min-width: 900px) {
    .ios-avis-page {
        max-width: 1100px;
        margin: 0 auto;
    }

    /* Liste en grille 2 colonnes sur desktop */
    .ios-avis-list {
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    /* Ajustement du résumé */
    .ios-summary {
        padding: 24px 28px;
        gap: 32px;
    }
    .ios-summary__value { font-size: 30px; }
    .ios-summary__value i { font-size: 24px; }
    .ios-summary__label { font-size: 13.5px; }
    .ios-summary__sep { height: 44px; }

    /* Header section plus aéré */
    .ios-section-header {
        font-size: 13.5px;
        margin: 28px 0 10px;
    }
}

/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .ios-avis-page {
        margin: 0 -16px;
        padding: 0 16px 40px;
        max-width: none;
    }

    .ios-avis-list {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .ios-summary {
        padding: 16px 18px;
        gap: 16px;
    }
    .ios-summary__value { font-size: 24px; }
    .ios-summary__value i { font-size: 18px; }
    .ios-summary__label { font-size: 12px; }
    .ios-summary__sep { height: 30px; }

    .ios-avis-card { padding: 14px; }
    .ios-avis-card__head { flex-direction: column; align-items: flex-start; gap: 10px; }
    .ios-avis-card__rating { width: 100%; justify-content: space-between; }

    /* Actions en pleine largeur sur mobile */
    .ios-avis-card__actions {
        flex-direction: column;
        gap: 8px;
    }
    .ios-btn { width: 100%; flex: 0 0 auto; }
}

@media (max-width: 480px) {
    .ios-summary { gap: 12px; padding: 14px; }
    .ios-summary__value { font-size: 22px; gap: 5px; }
    .ios-summary__value i { font-size: 16px; }
    .ios-summary__label { font-size: 11px; }
    .ios-summary__sep { height: 26px; }

    .ios-avis-card { padding: 12px; }
    .ios-avis-card__avatar { width: 38px; height: 38px; font-size: 14px; }
    .ios-avis-card__name { font-size: 14px; }
    .ios-avis-card__date { font-size: 12px; }
    .ios-stars { font-size: 13px; }
    .ios-avis-card__score { font-size: 13px; }
    .ios-avis-card__comment { font-size: 13.5px; }
    .ios-avis-card__bien { font-size: 12px; padding: 4px 10px; }

    .ios-btn { font-size: 14px; padding: 11px 16px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush