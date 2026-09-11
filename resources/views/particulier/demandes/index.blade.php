@extends('layouts.dashboard')

@section('title', 'Mes besoins publiés — DoyaImmo')
@section('page_title', 'Mes besoins publiés')
@section('page_sub', 'Gérez vos demandes de logement actives')

@section('content')
<div class="view active besoins-page">

    {{-- ═══════════════════════════════════════════
         FLASH MESSAGES
    ═══════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="flash flash--success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flash flash--error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         HEADER
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
         GRILLE DES BESOINS
    ═══════════════════════════════════════════ --}}
    <div class="besoins-grid">
        @forelse($demandes as $demande)

            @php
                // Statut
                $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
                $statutLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);

                // Compteurs
                $totalOffres     = $demande->propositions->count();
                $offresEnAttente = $demande->propositions->where('statut.value', 'en_attente')->count();
                $offresAcceptees = $demande->propositions->where('statut.value', 'acceptee')->count();

                // Budget
                $isLocation  = is_object($demande->type_operation)
                                && $demande->type_operation->value === 'location';
                $budgetLabel = $isLocation ? 'F/mois' : 'F';

                // Highlight si nouvelles offres
                $hasNewOffers = $offresEnAttente > 0;
            @endphp

            <article class="besoin-card besoin-card--{{ $statutValue }} {{ $hasNewOffers ? 'has-offers' : '' }}">

                {{-- En-tête : statut + compteurs --}}
                <header class="besoin-card__top">
                    <span class="besoin-status besoin-status--{{ $statutValue }}">
                        <i class="fa-solid fa-circle"></i>
                        {{ $statutLabel }}
                    </span>

                    @if($totalOffres > 0)
                        <span class="offres-badge {{ $hasNewOffers ? 'offres-badge--new' : '' }}">
                            <i class="fa-regular fa-envelope"></i>
                            <strong>{{ $totalOffres }}</strong>
                            {{ $totalOffres > 1 ? 'offres' : 'offre' }}
                            @if($hasNewOffers)
                                <span class="pulse-dot"></span>
                            @endif
                        </span>
                    @else
                        <span class="offres-badge offres-badge--empty">
                            <i class="fa-regular fa-clock"></i>
                            En attente
                        </span>
                    @endif
                </header>

                {{-- Corps --}}
                <div class="besoin-card__body">

                    {{-- Type + Budget --}}
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

                    {{-- Compteurs détaillés (si plusieurs) --}}
                    @if($offresEnAttente > 0 || $offresAcceptees > 0)
                        <div class="besoin-card__counters">
                            @if($offresEnAttente > 0)
                                <span class="counter-chip counter-chip--warning">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $offresEnAttente }} en attente
                                </span>
                            @endif
                            @if($offresAcceptees > 0)
                                <span class="counter-chip counter-chip--success">
                                    <i class="fa-solid fa-check-circle"></i>
                                    {{ $offresAcceptees }} acceptée{{ $offresAcceptees > 1 ? 's' : '' }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Actions --}}
                <footer class="besoin-card__footer">
                    <a href="{{ route('particulier.demandes.show', $demande->slug) }}"
                       class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-eye"></i> Voir le détail
                    </a>

                    @if($totalOffres > 0)
                        <a href="{{ route('particulier.demandes.offres', $demande->slug) }}"
                           class="btn btn-rust btn-sm besoin-card__cta">
                            <i class="fa-solid fa-file-invoice"></i>
                            Voir les {{ $totalOffres }} offre{{ $totalOffres > 1 ? 's' : '' }}
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @else
                        <span class="btn btn-ghost btn-sm is-disabled">
                            <i class="fa-regular fa-hourglass-half"></i> Aucune offre pour l'instant
                        </span>
                    @endif
                </footer>
            </article>

        @empty

            {{-- ═══════════════════════════════════════════
                 EMPTY STATE
            ═══════════════════════════════════════════ --}}
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

    {{-- Pagination --}}
    @if($demandes->count() > 0)
        <div class="pagination-wrapper">
            {{ $demandes->links() }}
        </div>
    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE BESOINS PUBLIÉS — Particulier
   ═══════════════════════════════════════════════════════════ */

.besoins-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --surface:      #F7F9FC;
    --radius:       14px;
}

/* ═══ FLASH ═══ */
.flash {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-radius: 10px;
    margin-bottom: 16px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid;
}
.flash--success { background: var(--c-success-bg); border-color: #C8E6C9; color: var(--c-success); }
.flash--error   { background: var(--c-danger-bg);  border-color: #FFCDD2; color: var(--c-danger); }

/* ═══ HEADER ═══ */
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

/* ═══ GRILLE ═══ */
.besoins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 14px;
}

/* ═══ CARTE BESOIN ═══ */
.besoin-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 18px;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
    position: relative;
    overflow: hidden;
}
.besoin-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(0, 0, 0, .06);
    border-color: #d8d8d8;
}

/* Bande colorée statut en haut */
.besoin-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 18px;
    right: 18px;
    height: 3px;
    border-radius: 0 0 4px 4px;
    background: var(--muted);
}
.besoin-card--en_attente::before { background: var(--c-warning); }
.besoin-card--en_cours::before   { background: var(--c-info); }
.besoin-card--terminee::before   { background: var(--c-success); }
.besoin-card--annulee::before    { background: var(--c-danger); }

/* Highlight si nouvelles offres */
.besoin-card.has-offers {
    border-color: rgba(180, 83, 42, .35);
    background: linear-gradient(135deg, #FFFBF7 0%, #fff 45%);
}

/* ═══ En-tête ═══ */
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
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.besoin-status i { font-size: 6px; }
.besoin-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.besoin-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.besoin-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.besoin-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.besoin-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.besoin-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }

/* Badge offres */
.offres-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    background: var(--surface);
    color: var(--text-soft);
    border: 1px solid var(--border);
    position: relative;
    white-space: nowrap;
}
.offres-badge i { font-size: 11px; }
.offres-badge strong {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 800;
    color: var(--text);
}
.offres-badge--new {
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-color: #FFE0B2;
}
.offres-badge--new strong { color: var(--c-warning); }
.offres-badge--new i { color: var(--c-warning); }
.offres-badge--empty {
    color: var(--muted);
    opacity: .85;
    font-style: italic;
    font-weight: 500;
}

/* Point pulsé si nouvelles offres */
.pulse-dot {
    display: inline-block;
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: var(--c-warning);
    margin-left: 3px;
    position: relative;
}
.pulse-dot::after {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    background: var(--c-warning);
    opacity: .35;
    animation: pulseDot 1.8s ease-in-out infinite;
}
@keyframes pulseDot {
    0%, 100% { transform: scale(1); opacity: .35; }
    50%      { transform: scale(1.6); opacity: 0; }
}

/* ═══ Corps ═══ */
.besoin-card__body {
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 1;
}

.besoin-card__headline {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
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

/* Meta */
.besoin-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 14px;
    font-size: 12.5px;
    color: var(--muted);
}
.meta-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.meta-item i {
    font-size: 11px;
    color: var(--rust);
    opacity: .8;
}

/* Description */
.besoin-card__desc {
    font-size: 12.5px;
    color: var(--text-soft);
    line-height: 1.55;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Compteurs détaillés */
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
    border: 1px solid;
}
.counter-chip i { font-size: 10px; }
.counter-chip--warning {
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-color: #FFE0B2;
}
.counter-chip--success {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-color: #C8E6C9;
}

/* ═══ Footer ═══ */
.besoin-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 14px;
    padding-top: 12px;
    border-top: 1px dashed var(--border);
    flex-wrap: wrap;
}
.besoin-card__cta { margin-left: auto; }

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

.btn.is-disabled {
    opacity: .65;
    cursor: not-allowed;
    pointer-events: none;
    font-style: italic;
}

/* ═══ EMPTY STATE ═══ */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 64px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 76px;
    height: 76px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--c-warning-bg);
    border-radius: 50%;
    color: var(--rust);
    font-size: 30px;
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
    margin: 0 auto 18px;
    max-width: 400px;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper {
    margin-top: 24px;
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
    .page-header__left h2 { font-size: 19px; }
    .page-header__left p  { font-size: 13px; }
    .page-header__actions { width: 100%; }
    .page-header__actions .btn { flex: 1; justify-content: center; }

    .besoins-grid {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .besoin-card { padding: 14px 16px; }
    .besoin-card::before { left: 16px; right: 16px; }

    .besoin-card__footer {
        flex-direction: column;
        align-items: stretch;
    }
    .besoin-card__footer .btn,
    .besoin-card__footer .is-disabled {
        width: 100%;
        justify-content: center;
    }
    .besoin-card__cta { margin-left: 0; }
}

@media (max-width: 480px) {
    .besoin-card { padding: 12px 14px; }
    .besoin-card::before { left: 14px; right: 14px; }

    .besoin-card__type { font-size: 15.5px; }
    .besoin-card__budget { font-size: 16px; }

    .besoin-card__meta { font-size: 12px; gap: 5px 12px; }
    .meta-item i { font-size: 10px; }

    .besoin-card__desc { font-size: 12px; }

    .offres-badge { font-size: 11px; padding: 3px 10px; }
    .offres-badge strong { font-size: 12px; }

    .btn { font-size: 12px; padding: 7px 13px; }
    .btn-sm { font-size: 11.5px; padding: 6px 11px; }
}
</style>
@endpush