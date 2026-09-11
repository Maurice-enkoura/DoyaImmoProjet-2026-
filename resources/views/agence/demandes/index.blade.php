@extends('layouts.dashboard-agence')

@section('title', 'Besoins disponibles — DoyaImmo')
@section('page_title', 'Besoins disponibles')
@section('page_sub', 'Parcourez les demandes de logement publiées par les clients')

@section('content')
<div class="view active besoins-page">

    {{-- ═══════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Besoins disponibles</h2>
            <p>Parcourez les demandes publiées par les clients</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         ONGLETS
    ═══════════════════════════════════════════ --}}
    <nav class="tabs" aria-label="Filtrer par type">
        <a href="{{ route('agence.demandes.index', array_merge(request()->query(), ['onglet' => 'compatibles'])) }}"
           class="tab {{ $onglet === 'compatibles' ? 'is-active' : '' }}">
            <i class="fa-solid fa-robot"></i>
            <span>Compatibles</span>
            <span class="tab__count">{{ $stats['compatibles'] ?? 0 }}</span>
        </a>

        <a href="{{ route('agence.demandes.index', array_merge(request()->query(), ['onglet' => 'toutes'])) }}"
           class="tab {{ $onglet === 'toutes' ? 'is-active' : '' }}">
            <i class="fa-solid fa-list"></i>
            <span>Toutes les demandes</span>
            <span class="tab__count">{{ $stats['total'] ?? 0 }}</span>
        </a>
    </nav>

    {{-- ═══════════════════════════════════════════
         FILTRES
    ═══════════════════════════════════════════ --}}
    <div class="filters">
        <div class="filters__field">
            <i class="fa-solid fa-location-dot"></i>
            <select id="filterZone">
                <option value="">Toutes les zones</option>
                @foreach($zones as $zone)
                    <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>
                        {{ $zone }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filters__field">
            <i class="fa-solid fa-building"></i>
            <select id="filterType">
                <option value="">Tous types de bien</option>
                @foreach(\App\Enums\TypeBienEnum::cases() as $type)
                    <option value="{{ $type->value }}" {{ request('type_bien') == $type->value ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filters__field">
            <i class="fa-solid fa-handshake"></i>
            <select id="filterOperation">
                <option value="">Toutes opérations</option>
                @foreach(\App\Enums\TypeOperationEnum::cases() as $type)
                    <option value="{{ $type->value }}" {{ request('type_operation') == $type->value ? 'selected' : '' }}>
                        {{ $type->label() }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filters__field filters__field--grow">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="filterSearch" placeholder="Rechercher une demande..." value="{{ request('search') }}">
        </div>

        <button type="button" class="btn btn-rust" onclick="applyFilters()">
            <i class="fa-solid fa-filter"></i> Filtrer
        </button>

        <button type="button" class="btn btn-ghost" onclick="resetFilters()">
            <i class="fa-solid fa-rotate"></i> Réinitialiser
        </button>
    </div>

    {{-- ═══════════════════════════════════════════
         RÉSUMÉ (onglet "toutes")
    ═══════════════════════════════════════════ --}}
    @if($onglet === 'toutes' && $demandes->count() > 0)
        <div class="summary-banner">
            <div class="summary-banner__info">
                <span class="summary-pill summary-pill--success">
                    <i class="fa-solid fa-circle-check"></i>
                    {{ $stats['compatibles'] ?? 0 }} compatible{{ ($stats['compatibles'] ?? 0) > 1 ? 's' : '' }}
                </span>
                <span class="summary-pill summary-pill--muted">
                    <i class="fa-solid fa-circle-xmark"></i>
                    {{ ($stats['total'] ?? 0) - ($stats['compatibles'] ?? 0) }} sans correspondance
                </span>
            </div>

            <div class="summary-banner__actions" id="summaryFilters">
                <button type="button" class="summary-btn is-active" data-filter="all">
                    Tout voir
                </button>
                <button type="button" class="summary-btn" data-filter="compatible">
                    Compatibles
                </button>
                <button type="button" class="summary-btn" data-filter="non-compatible">
                    Non compatibles
                </button>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         GRILLE DE DEMANDES
    ═══════════════════════════════════════════ --}}
    <div class="besoins-grid" id="besoinGrid">
        @forelse($demandes as $item)

            @php
                $demande   = $onglet === 'compatibles' ? $item->demande : $item;
                $score     = $onglet === 'compatibles' ? $item->score : ($item->score ?? 0);
                $niveau    = $onglet === 'compatibles' ? $item->niveau : ($item->niveau ?? null);
                $bien      = $onglet === 'compatibles' ? $item->bien : ($item->bien ?? null);

                $estCompatible = $score > 0;
                $scoreClass = $score >= 80 ? 'excellent' : ($score >= 60 ? 'bon' : ($score >= 40 ? 'moyen' : 'faible'));

                $isLocation  = is_object($demande->type_operation) && $demande->type_operation->value === 'location';
                $budgetLabel = $isLocation ? 'F/mois' : 'F';

                $equipementsDemande = $demande->equipements ?? [];

                $nbOffres = $demande->propositions->count();
                $nbEnAttente = $demande->propositions->where('statut', 'en_attente')->count();
            @endphp

            <article class="besoin-card besoin-card--{{ $scoreClass }} {{ $estCompatible ? 'is-compatible' : '' }}"
                     data-compatible="{{ $estCompatible ? 'true' : 'false' }}">

                {{-- Badge compatibilité en haut --}}
                @if($estCompatible)
                    <div class="compat-badge compat-badge--{{ $scoreClass }}">
                        <i class="fa-solid fa-robot"></i>
                        <strong>{{ $score }}%</strong>
                        <span>{{ $niveau ?? 'Correspondance' }}</span>
                    </div>
                @elseif($onglet === 'toutes')
                    <div class="compat-badge compat-badge--none">
                        <i class="fa-solid fa-circle-xmark"></i>
                        <span>Non compatible</span>
                    </div>
                @endif

                <div class="besoin-card__body">

                    {{-- Type + budget --}}
                    <div class="besoin-card__headline">
                        <h3 class="besoin-card__type">
                            {{ $demande->type_bien->label() }}
                        </h3>
                        <div class="besoin-card__budget">
                            {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                            <small>{{ $budgetLabel }}</small>
                        </div>
                    </div>

                    {{-- Meta --}}
                    <div class="besoin-card__meta">
                        <span class="meta-pill">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $demande->zone_recherchee }}
                        </span>
                        <span class="meta-pill">
                            <i class="fa-solid fa-handshake"></i>
                            {{ $demande->type_operation->label() }}
                        </span>
                        @if($demande->surface_minimum)
                            <span class="meta-pill">
                                <i class="fa-regular fa-square"></i>
                                {{ $demande->surface_minimum }} m²
                            </span>
                        @endif
                        @if($demande->nombre_chambres)
                            <span class="meta-pill">
                                <i class="fa-solid fa-bed"></i>
                                {{ $demande->nombre_chambres }} ch.
                            </span>
                        @endif
                    </div>

                    {{-- Bien associé --}}
                    @if($estCompatible && $bien)
                        <div class="bien-match">
                            <i class="fa-solid fa-building"></i>
                            <div class="bien-match__info">
                                <strong>{{ $bien->titre ?? 'Bien correspondant' }}</strong>
                                <span>Votre bien qui correspond à cette demande</span>
                            </div>
                        </div>
                    @endif

                    {{-- Équipements souhaités --}}
                    @if(count($equipementsDemande) > 0)
                        <div class="equipements-row">
                            @foreach(array_slice($equipementsDemande, 0, 4) as $equipement)
                                <span class="equip-tag">{{ $equipement }}</span>
                            @endforeach
                            @if(count($equipementsDemande) > 4)
                                <span class="equip-tag equip-tag--more">
                                    +{{ count($equipementsDemande) - 4 }}
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Description --}}
                    @if($demande->description)
                        <p class="besoin-card__desc">
                            {{ Str::limit($demande->description, 110) }}
                        </p>
                    @endif

                    {{-- Date --}}
                    <div class="besoin-card__date">
                        <i class="fa-regular fa-clock"></i>
                        Publiée {{ $demande->created_at->diffForHumans() }}
                    </div>
                </div>

                {{-- Footer --}}
                <footer class="besoin-card__foot">

                    <div class="besoin-card__stats">
                        @if($nbOffres > 0)
                            <span class="stat-chip stat-chip--offres">
                                <i class="fa-regular fa-envelope"></i>
                                <strong>{{ $nbOffres }}</strong>
                                {{ $nbOffres > 1 ? 'offres' : 'offre' }}
                            </span>
                        @endif
                        @if($nbEnAttente > 0)
                            <span class="stat-chip stat-chip--attente">
                                <i class="fa-regular fa-clock"></i>
                                {{ $nbEnAttente }} en attente
                            </span>
                        @endif
                    </div>

                    <div class="besoin-card__actions">
                        <a href="{{ route('agence.demandes.show', $demande->slug) }}"
                           class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-eye"></i> Voir
                        </a>

                        <a href="{{ route('agence.propositions.create', $demande->slug) }}"
                           class="btn btn-rust btn-sm">
                            <i class="fa-solid fa-paper-plane"></i> Faire une offre
                        </a>
                    </div>
                </footer>
            </article>

        @empty

            {{-- ═══════════════════════════════════════════
                 EMPTY STATE
            ═══════════════════════════════════════════ --}}
            <div class="empty-state">
                <div class="empty-state__icon">
                    <i class="fa-solid {{ $onglet === 'compatibles' ? 'fa-robot' : 'fa-inbox' }}"></i>
                </div>

                @if($onglet === 'compatibles')
                    <h3>Aucune demande compatible</h3>
                    <p>Publiez des biens dans les zones où il y a des demandes pour voir des correspondances apparaître.</p>
                    <a href="{{ route('agence.biens.create') }}" class="btn btn-rust">
                        <i class="fa-solid fa-plus"></i> Publier un bien
                    </a>
                @else
                    <h3>Aucune demande disponible</h3>
                    <p>Revenez plus tard, de nouvelles demandes seront publiées par les particuliers.</p>
                    <button type="button" class="btn btn-ghost" onclick="resetFilters()">
                        <i class="fa-solid fa-rotate"></i> Réinitialiser les filtres
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($demandes->count() > 0)
        <div class="pagination-wrapper">
            {{ $demandes->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection


@push('scripts')
<script>
    /* ═══════════════════════════════════════════
       FILTRES (URL)
    ═══════════════════════════════════════════ */
    function applyFilters() {
        const zone      = document.getElementById('filterZone').value;
        const type      = document.getElementById('filterType').value;
        const operation = document.getElementById('filterOperation').value;
        const search    = document.getElementById('filterSearch').value;
        const onglet    = '{{ $onglet }}';

        const params = new URLSearchParams({ onglet });
        if (zone)      params.set('zone', zone);
        if (type)      params.set('type_bien', type);
        if (operation) params.set('type_operation', operation);
        if (search)    params.set('search', search);

        window.location.href = '{{ route("agence.demandes.index") }}?' + params.toString();
    }

    function resetFilters() {
        window.location.href = '{{ route("agence.demandes.index") }}?onglet={{ $onglet }}';
    }

    document.getElementById('filterSearch')?.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') applyFilters();
    });

    /* ═══════════════════════════════════════════
       FILTRE RAPIDE "compatible / non compatible"
    ═══════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', function () {
        const summaryFilters = document.getElementById('summaryFilters');
        if (!summaryFilters) return;

        const cards = document.querySelectorAll('#besoinGrid .besoin-card');
        const buttons = summaryFilters.querySelectorAll('.summary-btn');

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');

                const filter = btn.dataset.filter;

                cards.forEach(card => {
                    const isCompatible = card.dataset.compatible === 'true';

                    const show =
                        filter === 'all' ||
                        (filter === 'compatible' && isCompatible) ||
                        (filter === 'non-compatible' && !isCompatible);

                    card.style.display = show ? '' : 'none';
                });
            });
        });
    });
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE BESOINS DISPONIBLES — Agence
   ═══════════════════════════════════════════════════════════ */

.besoins-page {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --surface:      #F7F9FC;
    --radius:       14px;
}

/* ═══ HEADER ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 16px;
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

/* ═══ TABS ═══ */
.tabs {
    display: flex;
    gap: 4px;
    margin-bottom: 16px;
    border-bottom: 1px solid var(--border);
    overflow-x: auto;
    scrollbar-width: thin;
}
.tabs::-webkit-scrollbar { height: 3px; }
.tabs::-webkit-scrollbar-thumb { background: var(--border); border-radius: 999px; }

.tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 18px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--muted);
    text-decoration: none;
    border-bottom: 2.5px solid transparent;
    transition: all .2s;
    white-space: nowrap;
    margin-bottom: -1px;
}
.tab:hover { color: var(--text); }
.tab.is-active {
    color: var(--rust);
    border-bottom-color: var(--rust);
}
.tab i { font-size: 13px; }

.tab__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 26px;
    padding: 2px 8px;
    background: var(--surface);
    color: var(--text-soft);
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    font-family: var(--display);
}
.tab.is-active .tab__count {
    background: var(--rust-soft);
    color: var(--rust);
}

/* ═══ FILTRES ═══ */
.filters {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    align-items: center;
}

.filters__field {
    position: relative;
    display: flex;
    align-items: center;
    min-width: 140px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    transition: border-color .2s;
}
.filters__field:hover { border-color: #d8d8d8; }
.filters__field:focus-within { border-color: var(--rust); }

.filters__field i {
    position: absolute;
    left: 12px;
    color: var(--muted);
    font-size: 12px;
    pointer-events: none;
    z-index: 1;
}

.filters__field select,
.filters__field input {
    width: 100%;
    padding: 10px 14px 10px 34px;
    border: none;
    background: transparent;
    font-size: 13px;
    font-family: inherit;
    color: var(--text);
    cursor: pointer;
    outline: none;
    border-radius: 10px;
}

.filters__field input { cursor: text; }
.filters__field--grow { flex: 1; min-width: 200px; }

/* ═══ RÉSUMÉ ═══ */
.summary-banner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    flex-wrap: wrap;
}

.summary-banner__info {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.summary-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid;
}
.summary-pill i { font-size: 11px; }
.summary-pill--success {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-color: #C8E6C9;
}
.summary-pill--muted {
    background: var(--surface);
    color: var(--muted);
    border-color: var(--border);
}

.summary-banner__actions {
    display: flex;
    gap: 4px;
    flex-shrink: 0;
}

.summary-btn {
    padding: 7px 14px;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 600;
    background: transparent;
    color: var(--text-soft);
    border: 1.5px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s;
    white-space: nowrap;
}
.summary-btn:hover {
    background: var(--surface);
    border-color: var(--border);
}
.summary-btn.is-active {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}

/* ═══ GRILLE ═══ */
.besoins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 14px;
}

/* ═══ CARTE BESOIN ═══ */
.besoin-card {
    position: relative;
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
}
.besoin-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(0, 0, 0, .07);
    border-color: #d8d8d8;
}

/* Bordure gauche colorée si compatible */
.besoin-card.is-compatible { border-left: 3px solid var(--c-success); }
.besoin-card--excellent.is-compatible { border-left-color: var(--c-success); }
.besoin-card--bon.is-compatible       { border-left-color: var(--c-info); }
.besoin-card--moyen.is-compatible     { border-left-color: var(--c-warning); }
.besoin-card--faible.is-compatible    { border-left-color: var(--c-danger); }

/* ═══ BADGE COMPATIBILITÉ ═══ */
.compat-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    border-bottom: 1px solid var(--border);
}
.compat-badge i { font-size: 13px; }
.compat-badge strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 800;
}
.compat-badge span {
    font-weight: 500;
    opacity: .85;
}

.compat-badge--excellent {
    background: linear-gradient(135deg, #F0FBF4, #E8F5E9);
    color: var(--c-success);
}
.compat-badge--bon {
    background: linear-gradient(135deg, #F0F7FF, #E3F2FD);
    color: var(--c-info);
}
.compat-badge--moyen {
    background: linear-gradient(135deg, #FFFBF0, #FFF8E1);
    color: var(--c-warning);
}
.compat-badge--faible {
    background: linear-gradient(135deg, #FFF5F5, #FFEBEE);
    color: var(--c-danger);
}
.compat-badge--none {
    background: var(--surface);
    color: var(--muted);
    justify-content: center;
}

/* ═══ CORPS ═══ */
.besoin-card__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 14px 16px;
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
    font-size: 17px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
}
.besoin-card__budget small {
    font-size: 10.5px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}

/* ═══ META ═══ */
.besoin-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    background: var(--surface);
    border: 1px solid var(--border);
    font-size: 11.5px;
    color: var(--text-soft);
    font-weight: 500;
}
.meta-pill i { font-size: 10px; color: var(--rust); opacity: .8; }

/* ═══ BIEN MATCH ═══ */
.bien-match {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: var(--c-success-bg);
    border-radius: 10px;
    border: 1px solid #C8E6C9;
}
.bien-match i {
    color: var(--c-success);
    font-size: 16px;
    flex-shrink: 0;
}
.bien-match__info {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.bien-match__info strong {
    font-size: 13px;
    font-weight: 700;
    color: var(--c-success);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.bien-match__info span {
    font-size: 11px;
    color: #175C35;
    opacity: .85;
}

/* ═══ ÉQUIPEMENTS ═══ */
.equipements-row {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}
.equip-tag {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border: 1px solid #BBDEFB;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
}
.equip-tag--more {
    background: var(--surface);
    color: var(--muted);
    border-color: var(--border);
}

/* ═══ DESCRIPTION ═══ */
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

/* ═══ DATE ═══ */
.besoin-card__date {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    color: var(--muted);
    margin-top: auto;
}
.besoin-card__date i { font-size: 11px; opacity: .8; }

/* ═══ FOOTER ═══ */
.besoin-card__foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: #FAFBFC;
    flex-wrap: wrap;
}

.besoin-card__stats {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid;
}
.stat-chip i { font-size: 10px; }
.stat-chip--offres {
    background: var(--c-info-bg);
    color: var(--c-info);
    border-color: #BBDEFB;
}
.stat-chip--offres strong {
    font-family: var(--display);
    font-size: 12px;
    font-weight: 800;
}
.stat-chip--attente {
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-color: #FFE0B2;
}

.besoin-card__actions {
    display: flex;
    gap: 6px;
    flex-shrink: 0;
}

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
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
    max-width: 420px;
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

    .filters { padding: 12px; gap: 8px; }
    .filters__field { min-width: 100%; }
    .filters__field--grow { min-width: 100%; }
    .filters .btn { flex: 1; justify-content: center; }

    .summary-banner { flex-direction: column; align-items: stretch; gap: 10px; }
    .summary-banner__actions { justify-content: stretch; }
    .summary-btn { flex: 1; text-align: center; justify-content: center; }

    .besoins-grid { grid-template-columns: 1fr; gap: 10px; }

    .besoin-card__headline { flex-direction: column; align-items: flex-start; gap: 4px; }
    .besoin-card__foot { flex-direction: column; align-items: stretch; }
    .besoin-card__stats { justify-content: center; }
    .besoin-card__actions { width: 100%; }
    .besoin-card__actions .btn { flex: 1; justify-content: center; }
}

@media (max-width: 480px) {
    .tab { font-size: 12.5px; padding: 9px 14px; gap: 6px; }
    .tab__count { font-size: 10.5px; padding: 1px 7px; min-width: 22px; }

    .besoin-card__body { padding: 12px 14px; }
    .besoin-card__type { font-size: 15.5px; }
    .besoin-card__budget { font-size: 15px; }

    .besoin-card__foot { padding: 10px 14px; }

    .compat-badge { font-size: 11px; padding: 7px 12px; gap: 6px; }
    .compat-badge strong { font-size: 13px; }

    .btn { font-size: 12px; padding: 7px 13px; }
    .btn-sm { font-size: 11.5px; padding: 6px 11px; }
}
</style>
@endpush