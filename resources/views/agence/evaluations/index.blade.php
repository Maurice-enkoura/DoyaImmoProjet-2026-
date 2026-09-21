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
        <a href="{{ route('agence.historique') }}" class="ios-traited-banner">
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
                <div class="ios-summary__item">
                    <div class="ios-summary__value">{{ $negatifs }}</div>
                    <span class="ios-summary__label">
                        négatif{{ $negatifs > 1 ? 's' : '' }}
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
   AVIS REÇUS — Design épuré
   ═══════════════════════════════════════════════════════════ */

.ios-avis-page {
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ BANNIÈRE AVIS TRAITÉS ═══ */
.ios-traited-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 18px;
    background: #F5F5F7;
    border-radius: var(--radius);
    margin-bottom: 16px;
    text-decoration: none;
    color: var(--text-soft);
    transition: background-color .15s;
}
.ios-traited-banner:hover { background: #EFEFF4; }

.ios-traited-banner__left {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.ios-traited-banner__left i {
    font-size: 15px;
    flex-shrink: 0;
    color: var(--muted);
}
.ios-traited-banner__left span {
    font-size: 13.5px;
}
.ios-traited-banner__left strong {
    font-weight: 700;
    color: var(--ink);
}

.ios-traited-banner__arrow {
    font-size: 12px;
    color: var(--muted);
    flex-shrink: 0;
}

/* ═══ RÉSUMÉ ═══ */
.ios-summary {
    display: flex;
    align-items: center;
    gap: 28px;
    padding: 20px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
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
    font-family: var(--display);
    font-size: 26px;
    font-weight: 800;
    line-height: 1;
    color: var(--ink);
    letter-spacing: -.4px;
}
.ios-summary__value i {
    color: var(--muted);
    font-size: 18px;
}
.ios-summary__label {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}

.ios-summary__sep {
    width: 1px;
    height: 38px;
    background: var(--border);
    flex-shrink: 0;
}

/* ═══ SECTION HEADER ═══ */
.ios-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
    margin: 24px 0 8px;
    padding: 0 4px;
}
.ios-section-header:first-of-type { margin-top: 0; }
.ios-section-header__count {
    font-size: 11px;
    font-weight: 700;
    background: #F5F5F7;
    color: var(--text-soft);
    padding: 2px 8px;
    border-radius: 999px;
    text-transform: none;
    letter-spacing: 0;
}

/* ═══ LISTE AVIS ═══ */
.ios-avis-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

/* ═══ CARTE AVIS ═══ */
.ios-avis-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    transition: border-color .2s;
}
.ios-avis-card:hover { border-color: #d8d8d8; }

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
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
}
.ios-avis-card__client-body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.ios-avis-card__name {
    display: block;
    font-size: 14.5px;
    font-weight: 600;
    color: var(--ink);
    line-height: 1.2;
    letter-spacing: -.2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ios-avis-card__date {
    display: block;
    font-size: 12px;
    color: var(--muted);
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
    font-size: 14px;
}
.ios-stars .fa-star { color: #D8D8DD; }
.ios-stars .fa-star.is-on { color: var(--rust); }

.ios-avis-card__score {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -.2px;
}

.ios-avis-card__comment {
    font-size: 14px;
    line-height: 1.55;
    color: var(--text-soft);
    margin: 0 0 12px;
    flex: 1;
}

.ios-avis-card__bien {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 11px;
    background: #F5F5F7;
    border-radius: 999px;
    font-size: 12px;
    color: var(--text-soft);
    margin-bottom: 14px;
    align-self: flex-start;
}
.ios-avis-card__bien i {
    font-size: 11px;
    color: var(--muted);
}

.ios-avis-card__footer {
    padding-top: 14px;
    border-top: 1px solid var(--border);
    margin-top: auto;
}

.ios-avis-card__actions {
    display: flex;
    gap: 10px;
}

/* ═══ BOUTONS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 11px 18px;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
    flex: 1;
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
.ios-btn--gray {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--gray:hover {
    background: #F5F5F7;
    border-color: #d8d8d8;
    color: var(--ink);
}

/* ═══ EMPTY STATE ═══ */
.ios-empty {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
    margin-top: 20px;
}
.ios-empty__icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F5F5F7;
    border-radius: 50%;
    color: var(--muted);
    font-size: 26px;
}
.ios-empty__title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 6px;
    letter-spacing: -.2px;
}
.ios-empty__text {
    font-size: 13px;
    color: var(--muted);
    margin: 0 auto 18px;
    max-width: 360px;
    line-height: 1.55;
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
    padding: 6px 11px;
    border-radius: 8px;
    border: 1px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    font-size: 12px;
    min-width: 34px;
    text-align: center;
    background: #fff;
}
.ios-pagination a:hover { background: #F5F5F7; }
.ios-pagination .active span,
.ios-pagination [aria-current="page"] span,
.ios-pagination [aria-current="page"] {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.ios-pagination .disabled span { opacity: .45; cursor: not-allowed; }


/* ═══════════════════════════════════════════════════════════
   DESKTOP — Améliorations
   ═══════════════════════════════════════════════════════════ */
@media (min-width: 900px) {
    .ios-avis-page {
        max-width: 1100px;
        margin: 0 auto;
    }

    .ios-avis-list {
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
    }

    .ios-summary {
        padding: 22px 26px;
        gap: 32px;
    }
    .ios-summary__value { font-size: 28px; }
    .ios-summary__value i { font-size: 20px; }
    .ios-summary__label { font-size: 13px; }
    .ios-summary__sep { height: 44px; }

    .ios-section-header {
        font-size: 13px;
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
        background: transparent;
    }

    .ios-avis-list {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .ios-summary {
        padding: 14px 6px;
        gap: 0;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        justify-content: space-between;
    }
    .ios-summary__item {
        flex: 1;
        align-items: center;
        text-align: center;
        position: relative;
    }
    .ios-summary__item:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%; width: 0.5px;
        background: var(--ios-separator);
    }
    .ios-summary__value { font-size: 22px; gap: 5px; }
    .ios-summary__value i { font-size: 16px; }
    .ios-summary__label { font-size: 11px; }
    .ios-summary__sep { display: none; }

    .ios-traited-banner {
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .ios-avis-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .ios-avis-card__head {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    .ios-avis-card__rating {
        width: 100%;
        justify-content: space-between;
    }
    .ios-avis-card__avatar {
        width: 40px; height: 40px;
        background: #F5F5F7;
        border: none;
    }

    .ios-avis-card__actions {
        flex-direction: column;
        gap: 8px;
    }
    .ios-btn {
        width: 100%;
        flex: 0 0 auto;
        padding: 13px 18px;
        font-size: 14px;
        border-radius: 12px;
    }
    .ios-btn--gray {
        background: #F5F5F7;
        border: none;
        color: var(--text-soft);
    }

    .ios-empty {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .ios-pagination a,
    .ios-pagination span:not(.sr-only) {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        padding: 10px 14px;
        font-size: 13px;
        border-radius: 10px;
    }
}

@media (max-width: 480px) {
    .ios-summary { gap: 0; padding: 12px 6px; }
    .ios-summary__value { font-size: 20px; gap: 4px; }
    .ios-summary__value i { font-size: 14px; }
    .ios-summary__label { font-size: 10.5px; }

    .ios-avis-card { padding: 14px; }
    .ios-avis-card__avatar { width: 36px; height: 36px; font-size: 13px; }
    .ios-avis-card__name { font-size: 14px; }
    .ios-avis-card__date { font-size: 11.5px; }
    .ios-stars { font-size: 13px; }
    .ios-avis-card__score { font-size: 13px; }
    .ios-avis-card__comment { font-size: 13.5px; }
    .ios-avis-card__bien { font-size: 11.5px; padding: 4px 10px; }

    .ios-btn { font-size: 13.5px; padding: 12px 16px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush