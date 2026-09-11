@extends('layouts.dashboard-agence')

@section('title', 'Avis reçus — DoyaImmo')
@section('page_title', 'Avis reçus')
@section('page_sub', 'Ce que pensent vos clients de votre service')

@section('content')
<div class="view active avis-page">

    @php
        $total       = $evaluations->total();
        $moyenne     = $evaluations->avg('note') ?? 0;
        $sansReponse = $evaluations->whereNull('reponse_agence')->count();
        $negatifs    = $evaluations->where('note', '<', 3)->count();
    @endphp

    {{-- ═══════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Avis reçus</h2>
            <p>Ce que pensent vos clients de votre service</p>
        </div>
    </div>

    @if($total > 0)

        {{-- ═══════════════════════════════════════════
             RÉSUMÉ — 3 chiffres, rien de plus
        ═══════════════════════════════════════════ --}}
        <section class="summary">
            <div class="summary__item">
                <div class="summary__value">
                    <i class="fa-solid fa-star"></i>
                    {{ number_format($moyenne, 1) }}
                </div>
                <span class="summary__label">sur 5</span>
            </div>

            <div class="summary__sep"></div>

            <div class="summary__item">
                <div class="summary__value">{{ $total }}</div>
                <span class="summary__label">avis reçus</span>
            </div>

            @if($sansReponse > 0)
                <div class="summary__sep"></div>
                <div class="summary__item summary__item--attention">
                    <div class="summary__value">{{ $sansReponse }}</div>
                    <span class="summary__label">
                        <i class="fa-solid fa-reply"></i> à répondre
                    </span>
                </div>
            @endif
        </section>

        {{-- ═══════════════════════════════════════════
             FILTRES — 3 boutons simples
        ═══════════════════════════════════════════ --}}
        <div class="filters" id="filters">
            <button type="button" class="filter-btn is-active" data-filter="all">
                Tous les avis
                <span class="filter-btn__count">{{ $total }}</span>
            </button>

            <button type="button" class="filter-btn" data-filter="sans_reponse">
                Sans réponse
                <span class="filter-btn__count">{{ $sansReponse }}</span>
            </button>

            @if($negatifs > 0)
                <button type="button" class="filter-btn" data-filter="negatifs">
                    Négatifs
                    <span class="filter-btn__count">{{ $negatifs }}</span>
                </button>
            @endif
        </div>

        {{-- Message si le filtre ne renvoie rien --}}
        <div class="filter-empty" id="filterEmpty" hidden>
            <i class="fa-solid fa-filter-circle-xmark"></i>
            <p>Aucun avis dans cette catégorie.</p>
        </div>

        {{-- ═══════════════════════════════════════════
             LISTE DES AVIS
        ═══════════════════════════════════════════ --}}
        <div class="avis-list" id="avisList">
            @foreach($evaluations as $evaluation)
                @php
                    $note       = (int) $evaluation->note;
                    $hasReponse = !empty($evaluation->reponse_agence);
                    $initial    = strtoupper(mb_substr($evaluation->particulier->user->prenom ?? 'C', 0, 1));

                    // Données pour les filtres
                    $tags = [];
                    if ($note < 3) $tags[] = 'negatifs';
                    if (!$hasReponse) $tags[] = 'sans_reponse';
                @endphp

                <article class="avis-card"
                         data-tags="{{ implode(' ', $tags) }}"
                         data-repondu="{{ $hasReponse ? '1' : '0' }}">

                    {{-- En-tête : client + étoiles --}}
                    <header class="avis-card__head">
                        <div class="avis-card__client">
                            <div class="avis-avatar">{{ $initial }}</div>
                            <div>
                                <strong class="avis-card__name">
                                    {{ $evaluation->particulier->user->prenom ?? '' }} {{ $evaluation->particulier->user->nom ?? '' }}
                                </strong>
                                <span class="avis-card__date">
                                    {{ $evaluation->created_at->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="avis-card__rating">
                            <div class="avis-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $note ? 'is-on' : '' }}"></i>
                                @endfor
                            </div>
                            <span class="avis-card__score">{{ $note }}.0</span>
                        </div>
                    </header>

                    {{-- Commentaire --}}
                    @if($evaluation->commentaire)
                        <p class="avis-card__comment">{{ $evaluation->commentaire }}</p>
                    @endif

                    {{-- Bien concerné --}}
                    @if($evaluation->proposition && $evaluation->proposition->bien)
                        <div class="avis-card__bien">
                            <i class="fa-regular fa-building"></i>
                            {{ $evaluation->proposition->bien->titre ?? 'Bien' }}
                        </div>
                    @endif

                    {{-- Réponse ou bouton répondre --}}
                    <footer class="avis-card__footer">
                        @if($hasReponse)
                            <div class="reponse">
                                <div class="reponse__head">
                                    <i class="fa-solid fa-reply"></i>
                                    <span>Vous avez répondu</span>
                                    @if($evaluation->date_reponse)
                                        <span class="reponse__date">
                                            · {{ $evaluation->date_reponse->translatedFormat('d F Y') }}
                                        </span>
                                    @endif
                                </div>
                                <p class="reponse__text">{{ $evaluation->reponse_agence }}</p>
                            </div>
                        @else
                            <div class="avis-card__actions">
                                <a href="{{ route('agence.evaluations.show', $evaluation) }}"
                                   class="btn btn-rust btn-sm">
                                    <i class="fa-solid fa-reply"></i> Répondre
                                </a>
                                <a href="{{ route('agence.evaluations.show', $evaluation) }}"
                                   class="btn btn-ghost btn-sm">
                                    <i class="fa-solid fa-eye"></i> Détails
                                </a>
                            </div>
                        @endif
                    </footer>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $evaluations->links() }}
        </div>

    @else
        {{-- ═══════════════════════════════════════════
             EMPTY STATE
        ═══════════════════════════════════════════ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-star"></i>
            </div>
            <h3>Aucun avis reçu pour le moment</h3>
            <p>Les avis apparaîtront ici une fois que vos clients auront terminé une visite.</p>
        </div>
    @endif
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const cards      = document.querySelectorAll('.avis-card');
        const emptyMsg   = document.getElementById('filterEmpty');
        let currentFilter = 'all';

        function applyFilter() {
            let visible = 0;

            cards.forEach(card => {
                const tags = (card.dataset.tags || '').split(' ');

                const show = currentFilter === 'all' || tags.includes(currentFilter);

                card.hidden = !show;
                if (show) visible++;
            });

            if (emptyMsg) emptyMsg.hidden = visible > 0;
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');
                currentFilter = btn.dataset.filter;
                applyFilter();
            });
        });
    });
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE AVIS — version simple
   ═══════════════════════════════════════════════════════════ */

.avis-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #F5A623;
    --surface:      #F7F9FC;
    --radius:       14px;
}

/* ═══ HEADER ═══ */
.page-header {
    margin-bottom: 18px;
}
.page-header__left h2 {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.2;
}
.page-header__left p {
    font-size: 13px;
    color: var(--muted);
    margin: 4px 0 0;
}

/* ═══════════════════════════════════════════
   RÉSUMÉ — 3 chiffres
   ═══════════════════════════════════════════ */
.summary {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 18px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.summary__item {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.summary__value {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-family: var(--display);
    font-size: 26px;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.summary__value i {
    color: var(--c-gold);
    font-size: 20px;
}
.summary__label {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.summary__label i { margin-right: 3px; }

.summary__item--attention .summary__value { color: var(--c-warning); }
.summary__item--attention .summary__label { color: var(--c-warning); }

.summary__sep {
    width: 1px;
    height: 34px;
    background: var(--border);
    flex-shrink: 0;
}

/* ═══════════════════════════════════════════
   FILTRES — 3 boutons
   ═══════════════════════════════════════════ */
.filters {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    background: #fff;
    border: 1.5px solid var(--border);
    cursor: pointer;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
}
.filter-btn:hover {
    border-color: var(--rust);
    transform: translateY(-1px);
}
.filter-btn.is-active {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 10px rgba(180, 83, 42, .22);
}
.filter-btn__count {
    background: rgba(0, 0, 0, .08);
    padding: 1px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    min-width: 22px;
    text-align: center;
}
.filter-btn.is-active .filter-btn__count {
    background: rgba(255, 255, 255, .28);
    color: #fff;
}

/* Message filtre vide */
.filter-empty {
    text-align: center;
    padding: 40px 20px;
    background: #fff;
    border: 1px dashed var(--border);
    border-radius: var(--radius);
    color: var(--muted);
    margin-bottom: 16px;
}
.filter-empty i {
    font-size: 30px;
    opacity: .4;
    display: block;
    margin-bottom: 10px;
}
.filter-empty p {
    margin: 0;
    font-size: 13.5px;
}

/* ═══════════════════════════════════════════
   LISTE AVIS
   ═══════════════════════════════════════════ */
.avis-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.avis-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    transition: transform .2s ease, box-shadow .2s ease;
}
.avis-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, .05);
}
.avis-card[hidden] { display: none; }

/* En-tête */
.avis-card__head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 14px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.avis-card__client {
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 0;
}
.avis-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    flex-shrink: 0;
}
.avis-card__name {
    display: block;
    font-family: var(--display);
    font-size: 14.5px;
    font-weight: 700;
    color: var(--text);
    line-height: 1.2;
}
.avis-card__date {
    display: block;
    font-size: 12px;
    color: var(--muted);
    margin-top: 2px;
}

/* Rating */
.avis-card__rating {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.avis-stars {
    display: inline-flex;
    gap: 3px;
    font-size: 15px;
}
.avis-stars .fa-star { color: #D4D8E0; }
.avis-stars .fa-star.is-on { color: var(--c-gold); }

.avis-card__score {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 800;
    color: var(--text);
}

/* Commentaire */
.avis-card__comment {
    font-size: 14px;
    line-height: 1.65;
    color: var(--text-soft);
    margin: 0 0 10px;
}

/* Bien concerné */
.avis-card__bien {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 11px;
    background: var(--surface);
    border-radius: 999px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 12px;
}
.avis-card__bien i {
    font-size: 11px;
    color: var(--rust);
}

/* Pied de carte */
.avis-card__footer {
    padding-top: 12px;
    border-top: 1px dashed var(--border);
}

/* Réponse */
.reponse {
    padding: 12px 14px;
    background: var(--c-info-bg);
    border-radius: 10px;
    border-left: 3px solid var(--c-info);
}
.reponse__head {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: var(--c-info);
}
.reponse__head i { font-size: 10px; }
.reponse__date {
    font-weight: 500;
    text-transform: none;
    letter-spacing: 0;
    color: var(--muted);
}
.reponse__text {
    font-size: 13.5px;
    line-height: 1.6;
    color: var(--text-soft);
    margin: 0;
}

/* Actions */
.avis-card__actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* ═══════════════════════════════════════════
   EMPTY STATE
   ═══════════════════════════════════════════ */
.empty-state {
    text-align: center;
    padding: 70px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #FFF8E1, #FFECB3);
    border-radius: 50%;
    color: var(--c-gold);
    font-size: 28px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
}
.empty-state p {
    color: var(--muted);
    font-size: 13.5px;
    margin: 0;
    max-width: 380px;
    margin-inline: auto;
}

/* ═══════════════════════════════════════════
   BOUTONS
   ═══════════════════════════════════════════ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523;
    color: #fff;
    border-color: #9A4523;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(180, 83, 42, .25);
}
.btn-ghost {
    background: transparent;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: var(--surface);
    border-color: var(--rust);
    color: var(--rust);
}
.btn-sm { padding: 6px 12px; font-size: 12px; }

/* ═══════════════════════════════════════════
   PAGINATION
   ═══════════════════════════════════════════ */
.pagination-wrapper {
    margin-top: 22px;
    display: flex;
    justify-content: center;
}
.pagination-wrapper .pagination,
.pagination-wrapper nav {
    display: flex;
    gap: 5px;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
}
.pagination-wrapper a,
.pagination-wrapper span:not(.sr-only) {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    font-size: 12.5px;
    transition: all .2s;
    min-width: 36px;
    text-align: center;
    background: #fff;
}
.pagination-wrapper a:hover {
    background: var(--border);
    border-color: var(--rust);
    color: var(--rust);
}
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.pagination-wrapper .disabled span {
    opacity: .45;
    cursor: not-allowed;
}

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 768px) {
    .summary {
        padding: 14px 18px;
        gap: 16px;
    }
    .summary__value { font-size: 22px; }
    .summary__value i { font-size: 17px; }
    .summary__label { font-size: 11.5px; }
    .summary__sep { height: 28px; }

    .avis-card { padding: 16px; }
    .avis-card__head { flex-direction: column; align-items: flex-start; gap: 10px; }
    .avis-card__actions { flex-direction: column; }
    .avis-card__actions .btn { width: 100%; justify-content: center; }
}

@media (max-width: 480px) {
    .summary {
        gap: 12px;
        padding: 12px 14px;
    }
    .summary__value { font-size: 19px; gap: 5px; }
    .summary__value i { font-size: 15px; }
    .summary__label { font-size: 11px; }

    .filter-btn {
        font-size: 12px;
        padding: 8px 13px;
    }

    .avis-avatar { width: 38px; height: 38px; font-size: 14px; }
    .avis-card__name { font-size: 13.5px; }
    .avis-stars { font-size: 13px; }
    .avis-card__comment { font-size: 13px; }
}
</style>
@endpush