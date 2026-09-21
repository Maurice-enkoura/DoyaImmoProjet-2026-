@extends('layouts.dashboard')

@section('title', 'Mes besoins publiés — DoyaImmo')
@section('page_title', 'Mes besoins publiés')
@section('page_sub', 'Gérez vos demandes de logement actives')

@section('content')
<div class="view active besoins-page">

    {{-- ═══════════════════════════════════════════
         HEADER DESKTOP
    ═══════════════════════════════════════════ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Mes besoins publiés</h2>
            <p>Gérez vos demandes de logement actives</p>
        </div>
        <div class="page-header__actions">
            <a href="{{ route('particulier.historique') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-clock-rotate-left"></i> Historique
            </a>
            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust btn-sm">
                <i class="fa-solid fa-plus"></i> Nouveau besoin
            </a>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         STATS BAR (mobile + desktop)
    ═══════════════════════════════════════════ --}}
    @php
        $totalDemandes  = $demandes->total() ?? $demandes->count();
        $totalOffresAll = $demandes->sum(fn($d) => $d->propositions->count());
        $totalAttente   = $demandes->sum(fn($d) => $d->propositions->where('statut.value', 'en_attente')->count());
        $totalActives   = $demandes->where('statut.value', 'en_cours')->count()
                        + $demandes->where('statut.value', 'en_attente')->count();
    @endphp

    <div class="besoins-stats">
        <div class="besoins-stat">
            <span class="besoins-stat__value">{{ $totalDemandes }}</span>
            <span class="besoins-stat__label">Besoins</span>
        </div>
        <div class="besoins-stat">
            <span class="besoins-stat__value">{{ $totalActives }}</span>
            <span class="besoins-stat__label">Actifs</span>
        </div>
        <div class="besoins-stat">
            <span class="besoins-stat__value">{{ $totalOffresAll }}</span>
            <span class="besoins-stat__label">Offres</span>
        </div>
        <div class="besoins-stat">
            <span class="besoins-stat__value">{{ $totalAttente }}</span>
            <span class="besoins-stat__label">En attente</span>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         FILTRES (pills)
    ═══════════════════════════════════════════ --}}
    @if($demandes->count() > 0)
        <div class="besoins-filters" id="besoinsFilters">
            <button type="button" class="besoins-filter is-active" data-filter="all">
                Tous
            </button>
            <button type="button" class="besoins-filter" data-filter="en_cours">
                Actifs
            </button>
            <button type="button" class="besoins-filter" data-filter="with_offers">
                Avec offres
            </button>
            <button type="button" class="besoins-filter" data-filter="terminee">
                Terminés
            </button>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         LISTE DES BESOINS
    ═══════════════════════════════════════════ --}}
    <div class="besoins-grid" id="besoinsGrid">
        @forelse($demandes as $demande)

            @php
                $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
                $statutLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);

                $totalOffres     = $demande->propositions->count();
                $offresEnAttente = $demande->propositions->where('statut.value', 'en_attente')->count();
                $offresAcceptees = $demande->propositions->where('statut.value', 'acceptee')->count();

                $isLocation  = is_object($demande->type_operation)
                                && $demande->type_operation->value === 'location';
                $budgetLabel = $isLocation ? 'F/mois' : 'F';

                $hasNewOffers = $offresEnAttente > 0;

                $dataStatus = $statutValue;
                $dataOffers = $totalOffres > 0 ? 'yes' : 'no';
            @endphp

            <article class="besoin-card"
                     data-status="{{ $dataStatus }}"
                     data-offers="{{ $dataOffers }}">

                {{-- Top : badge statut + chevron iOS --}}
                <header class="besoin-card__top">
                    <span class="besoin-status besoin-status--{{ $statutValue }}">
                        {{ $statutLabel }}
                    </span>

                    @if($totalOffres > 0)
                        <span class="offres-badge">
                            <i class="fa-regular fa-envelope"></i>
                            <strong>{{ $totalOffres }}</strong>
                            {{ $totalOffres > 1 ? 'offres' : 'offre' }}
                        </span>
                    @else
                        <span class="offres-badge offres-badge--empty">
                            En attente
                        </span>
                    @endif
                </header>

                {{-- Titre + Budget --}}
                <div class="besoin-card__headline">
                    <h3 class="besoin-card__type">{{ $demande->type_bien->label() }}</h3>
                    <div class="besoin-card__budget">
                        {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                        <small>{{ $budgetLabel }}</small>
                    </div>
                </div>

                {{-- Meta --}}
                <div class="besoin-card__meta">
                    <span class="meta-item">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $demande->zone_recherchee }}
                    </span>
                    @if($demande->surface_minimum)
                        <span class="meta-item">
                            <i class="fa-regular fa-square"></i>
                            {{ $demande->surface_minimum }} m²
                        </span>
                    @endif
                    <span class="meta-item">
                        <i class="fa-regular fa-clock"></i>
                        {{ $demande->created_at->diffForHumans() }}
                    </span>
                </div>

                {{-- Description --}}
                @if($demande->description)
                    <p class="besoin-card__desc">
                        {{ Str::limit($demande->description, 100) }}
                    </p>
                @endif

                {{-- Compteurs détaillés --}}
                @if($offresEnAttente > 0 || $offresAcceptees > 0)
                    <div class="besoin-card__counters">
                        @if($offresEnAttente > 0)
                            <span class="counter-chip counter-chip--warning">
                                {{ $offresEnAttente }} en attente
                            </span>
                        @endif
                        @if($offresAcceptees > 0)
                            <span class="counter-chip counter-chip--success">
                                {{ $offresAcceptees }} acceptée{{ $offresAcceptees > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Actions (desktop) --}}
                <footer class="besoin-card__footer">
                    <a href="{{ route('particulier.demandes.show', $demande->slug) }}"
                       class="btn btn-ghost btn-sm">
                        Voir le détail
                    </a>

                    @if($totalOffres > 0)
                        <a href="{{ route('particulier.demandes.offres', $demande->slug) }}"
                           class="btn btn-rust btn-sm besoin-card__cta">
                            Voir les {{ $totalOffres }} offre{{ $totalOffres > 1 ? 's' : '' }}
                        </a>
                    @else
                        <span class="btn btn-ghost btn-sm is-disabled">
                            Aucune offre
                        </span>
                    @endif
                </footer>

                {{-- Liens iOS (mobile) --}}
                <div class="besoin-card__ios-links">
                    <a href="{{ route('particulier.demandes.show', $demande->slug) }}" class="besoin-card__ios-link">
                        <i class="fa-solid fa-eye"></i>
                        <span>Voir le détail</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    @if($totalOffres > 0)
                        <a href="{{ route('particulier.demandes.offres', $demande->slug) }}" class="besoin-card__ios-link besoin-card__ios-link--rust">
                            <i class="fa-solid fa-file-invoice"></i>
                            <span>Voir les {{ $totalOffres }} offre{{ $totalOffres > 1 ? 's' : '' }}</span>
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    @endif
                </div>
            </article>

        @empty

            {{-- EMPTY STATE --}}
            <div class="empty-state">
                <div class="empty-state__icon">
                    <i class="fa-regular fa-house-circle-check"></i>
                </div>
                <h3>Aucun besoin actif</h3>
                <p>Publiez votre premier besoin pour recevoir des propositions d'agences.</p>
                <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust">
                    <i class="fa-solid fa-plus"></i> Publier un besoin
                </a>
            </div>
        @endforelse
    </div>

    {{-- Empty state filtré (JS) --}}
    <div class="ios-filter-empty" id="filterEmpty" style="display:none;">
        <div class="ios-filter-empty__icon">
            <i class="fa-regular fa-face-meh"></i>
        </div>
        <h4>Aucun besoin dans ce filtre</h4>
        <p>Essayez un autre filtre pour voir vos besoins.</p>
    </div>

    {{-- Pagination --}}
    @if($demandes->count() > 0 && method_exists($demandes, 'links'))
        <div class="pagination-wrapper">
            {{ $demandes->links() }}
        </div>
    @endif

</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   BESOINS PUBLIÉS — Design épuré
   ═══════════════════════════════════════════════════════════ */

.besoins-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #F1F8F4;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --c-info:       #1E40AF;
    --c-info-bg:    #EFF6FF;
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ HEADER DESKTOP ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 20px;
    flex-wrap: wrap;
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
    font-size: 13.5px;
    color: var(--muted);
    margin: 4px 0 0;
}
.page-header__actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* ═══ STATS BAR — caché desktop, visible mobile ═══ */
.besoins-stats { display: none; }

/* ═══ FILTRES ═══ */
.besoins-filters { display: none; }

/* ═══ GRILLE ═══ */
.besoins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 14px;
}

/* ═══ CARTE ═══ */
.besoin-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 18px;
    transition: border-color .15s;
    position: relative;
}
.besoin-card:hover {
    border-color: #d8d8d8;
}

/* Pas de barre colorée en haut : neutre */
.besoin-card.has-offers {
    border-color: rgba(180, 83, 42, .35);
}

/* ═══ EN-TÊTE ═══ */
.besoin-card__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}

.besoin-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    background: #F5F5F7;
    color: #4A5260;
}
.besoin-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.besoin-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.besoin-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.besoin-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.besoin-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.besoin-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }

.offres-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    background: #F5F5F7;
    color: var(--text-soft);
    white-space: nowrap;
}
.offres-badge i { font-size: 11px; color: var(--muted); }
.offres-badge strong {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    color: var(--text);
}
.offres-badge--empty {
    color: var(--muted);
    font-style: italic;
    font-weight: 500;
}

/* ═══ CORPS ═══ */
.besoin-card__headline {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 8px;
}
.besoin-card__type {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.25;
}
.besoin-card__budget {
    font-family: var(--display);
    font-size: 18px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
}
.besoin-card__budget small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}

.besoin-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 14px;
    font-size: 12.5px;
    color: var(--muted);
    margin-bottom: 8px;
}
.meta-item { display: inline-flex; align-items: center; gap: 5px; }
.meta-item i { font-size: 11px; color: var(--muted); }

.besoin-card__desc {
    font-size: 12.5px;
    color: var(--text-soft);
    line-height: 1.55;
    margin: 0 0 8px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.besoin-card__counters {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding-top: 4px;
}
.counter-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    background: #F5F5F7;
    color: var(--text-soft);
}
.counter-chip--warning { background: var(--c-warning-bg); color: var(--c-warning); }
.counter-chip--success { background: var(--c-success-bg); color: var(--c-success); }

/* ═══ FOOTER ═══ */
.besoin-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.besoin-card__cta { margin-left: auto; }

.besoin-card__ios-links { display: none; }

/* ═══ BOUTONS ═══ */
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
    transition: background-color .15s, border-color .15s;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; border-color: #9A4523; color: #fff; }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: #F5F5F7; color: var(--text); }
.btn-sm { padding: 6px 12px; font-size: 12px; }
.btn.is-disabled { opacity: .65; cursor: not-allowed; pointer-events: none; font-style: italic; }

/* ═══ EMPTY ═══ */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 64px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 72px; height: 72px;
    margin: 0 auto 16px;
    display: flex; align-items: center; justify-content: center;
    background: #F5F5F7;
    border-radius: 50%;
    color: var(--muted);
    font-size: 28px;
}
.empty-state h3 { font-family: var(--display); font-size: 17px; font-weight: 700; margin: 0 0 6px; color: var(--text); }
.empty-state p { color: var(--muted); font-size: 13.5px; margin: 0 auto 18px; max-width: 400px; }

/* ═══ PAGINATION ═══ */
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
.pagination-wrapper .pagination,
.pagination-wrapper nav { display: flex; gap: 5px; justify-content: center; list-style: none; padding: 0; margin: 0; flex-wrap: wrap; }
.pagination-wrapper a,
.pagination-wrapper span:not(.sr-only) {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    font-size: 12.5px;
    transition: background-color .15s;
    min-width: 36px;
    text-align: center;
    background: #fff;
}
.pagination-wrapper a:hover { background: #F5F5F7; }
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] { background: var(--rust); color: #fff; border-color: var(--rust); }
.pagination-wrapper .disabled span { opacity: .45; cursor: not-allowed; }

.ios-filter-empty { display: none; text-align: center; padding: 40px 20px; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .besoins-page {
        background: transparent;
        padding-top: 4px;
    }

    /* ─── HEADER DESKTOP masqué ─── */
    .page-header { display: none; }

    /* ─── STATS BAR iOS ─── */
    .besoins-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .besoins-stat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        position: relative;
        padding: 0 4px;
    }
    .besoins-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%;
        width: 0.5px;
        background: var(--ios-separator);
    }
    .besoins-stat__value {
        font-family: var(--display);
        font-size: 22px;
        font-weight: 800;
        color: var(--ios-label);
        letter-spacing: -.5px;
        line-height: 1;
    }
    .besoins-stat__label {
        font-size: 11px;
        font-weight: 500;
        color: var(--ios-label-3);
        margin-top: 2px;
    }

    /* ─── FILTRES PILLS ─── */
    .besoins-filters {
        display: flex;
        gap: 8px;
        padding: 0 0 12px;
        margin: 0 -16px;
        padding-left: 16px;
        padding-right: 16px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .besoins-filters::-webkit-scrollbar { display: none; }

    .besoins-filter {
        scroll-snap-align: start;
        flex: 0 0 auto;
        padding: 9px 16px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: var(--ios-label-2);
        cursor: pointer;
        transition: background-color .15s, color .15s;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        -webkit-tap-highlight-color: transparent;
        white-space: nowrap;
    }
    .besoins-filter:active { transform: scale(.98); }
    .besoins-filter.is-active {
        background: var(--rust);
        color: #fff;
    }

    /* ─── GRILLE → LISTE ─── */
    .besoins-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    /* ─── CARTES iOS ─── */
    .besoin-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: transform .15s ease;
        overflow: visible;
    }
    .besoin-card:hover { border: none; }
    .besoin-card:active { transform: scale(.99); }

    /* Top */
    .besoin-card__top {
        gap: 8px;
        margin-bottom: 12px;
    }

    .besoin-status {
        font-size: 11px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
    }

    .offres-badge {
        font-size: 12px;
        padding: 4px 10px;
    }
    .offres-badge strong { font-size: 13px; }

    /* Headline */
    .besoin-card__type {
        font-size: 17px;
        font-weight: 700;
        letter-spacing: -.2px;
    }
    .besoin-card__budget {
        font-size: 18px;
        letter-spacing: -.3px;
    }

    /* Meta */
    .besoin-card__meta {
        font-size: 13px;
        gap: 6px 12px;
        color: var(--ios-label-2);
    }
    .meta-item i { font-size: 11px; }

    /* Description */
    .besoin-card__desc {
        font-size: 13px;
        line-height: 1.5;
        color: var(--ios-label-2);
        -webkit-line-clamp: 2;
    }

    /* Compteurs */
    .counter-chip {
        font-size: 11.5px;
        padding: 3px 10px;
    }

    /* ─── ACTIONS : on cache les btns, on montre les liens iOS ─── */
    .besoin-card__footer { display: none; }

    .besoin-card__ios-links {
        display: flex;
        flex-direction: column;
        margin: 14px -16px -16px;
        border-top: 0.5px solid var(--ios-separator);
    }
    .besoin-card__ios-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        text-decoration: none;
        color: var(--ios-label);
        font-size: 15px;
        font-weight: 500;
        transition: background .15s;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .besoin-card__ios-link:last-child { border-bottom: none; }
    .besoin-card__ios-link:active { background: #F5F5F7; }

    .besoin-card__ios-link i:first-child {
        width: 20px;
        text-align: center;
        font-size: 15px;
        color: var(--ios-label-3);
        flex-shrink: 0;
    }
    .besoin-card__ios-link span {
        flex: 1;
        min-width: 0;
    }
    .besoin-card__ios-link i:last-child {
        color: #C7C7CC;
        font-size: 13px;
        flex-shrink: 0;
        opacity: .8;
    }

    .besoin-card__ios-link--rust {
        color: var(--rust);
        font-weight: 600;
    }
    .besoin-card__ios-link--rust i:first-child { color: var(--rust); }

    /* ─── Empty state iOS ─── */
    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon {
        width: 64px;
        height: 64px;
        font-size: 26px;
        background: #F5F5F7;
    }

    .ios-filter-empty {
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 40px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        text-align: center;
    }
    .ios-filter-empty__icon {
        width: 56px;
        height: 56px;
        margin: 0 auto 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F5F5F7;
        border-radius: 50%;
        color: var(--ios-label-3);
        font-size: 22px;
    }
    .ios-filter-empty h4 {
        font-family: var(--display);
        font-size: 16px;
        font-weight: 600;
        color: var(--ios-label);
        margin: 0 0 4px;
    }
    .ios-filter-empty p {
        font-size: 13px;
        color: var(--ios-label-3);
        margin: 0;
    }

    /* ─── PAGINATION iOS ─── */
    .pagination-wrapper { margin-top: 20px; }
    .pagination-wrapper a,
    .pagination-wrapper span:not(.sr-only) {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        padding: 10px 14px;
        font-size: 13px;
        border-radius: 10px;
    }
}

@media (max-width: 480px) {
    .besoins-stat__value { font-size: 20px; }
    .besoins-stat__label { font-size: 10.5px; }

    .besoin-card { padding: 14px; }
    .besoin-card__type { font-size: 16px; }
    .besoin-card__budget { font-size: 17px; }
    .besoin-card__ios-links { margin: 12px -14px -14px; }
    .besoin-card__ios-link { padding: 13px 14px; font-size: 14px; }
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


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    /* ═══════════════════════════════════════════
       FILTRES PILLS
    ═══════════════════════════════════════════ */
    const filters = document.querySelectorAll('.besoins-filter');
    const cards = document.querySelectorAll('.besoin-card');
    const filterEmpty = document.getElementById('filterEmpty');
    const grid = document.getElementById('besoinsGrid');

    filters.forEach(btn => {
        btn.addEventListener('click', () => {
            const filter = btn.dataset.filter;

            filters.forEach(f => f.classList.remove('is-active'));
            btn.classList.add('is-active');

            let visibleCount = 0;
            cards.forEach(card => {
                const status = card.dataset.status;
                const hasOffers = card.dataset.offers === 'yes';
                let show = false;

                if (filter === 'all') {
                    show = true;
                } else if (filter === 'en_cours') {
                    show = status === 'en_cours' || status === 'en_attente';
                } else if (filter === 'with_offers') {
                    show = hasOffers;
                } else if (filter === 'terminee') {
                    show = status === 'terminee';
                }

                if (show) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleCount === 0 && cards.length > 0) {
                if (filterEmpty) filterEmpty.style.display = 'block';
                if (grid) grid.style.display = 'none';
            } else {
                if (filterEmpty) filterEmpty.style.display = 'none';
                if (grid) grid.style.display = '';
            }
        });
    });

});
</script>
@endpush