@extends('layouts.dashboard-agence')

@section('title', 'Besoins disponibles — DoyaImmo')
@section('page_title', 'Besoins disponibles')
@section('page_sub', 'Parcourez les demandes de logement publiées par les clients')

@section('content')
<div class="view active besoins-page">

    @php
        $totalDemandes = $stats['total'] ?? 0;
        $totalCompat   = $stats['compatibles'] ?? 0;
        $totalNonCompat = max(0, $totalDemandes - $totalCompat);
    @endphp

    {{-- ═══════════════════════════════════════════
         EN-TÊTE : Stats + Tabs
    ═══════════════════════════════════════════ --}}

    {{-- Stats (mobile inline / desktop cards) --}}
    <div class="besoins-stats">
        <div class="besoins-stat">
            <span class="besoins-stat__value">{{ $totalCompat }}</span>
            <span class="besoins-stat__label">Compatibles</span>
        </div>
        <div class="besoins-stat">
            <span class="besoins-stat__value">{{ $totalDemandes }}</span>
            <span class="besoins-stat__label">Total</span>
        </div>
        <div class="besoins-stat">
            <span class="besoins-stat__value">{{ $totalNonCompat }}</span>
            <span class="besoins-stat__label">Sans match</span>
        </div>
    </div>

    {{-- Tabs (mobile + desktop) --}}
    <nav class="besoins-tabs">
        <a href="{{ route('agence.demandes.index', array_merge(request()->query(), ['onglet' => 'compatibles'])) }}"
           class="besoins-tab {{ $onglet === 'compatibles' ? 'is-active' : '' }}">
            <i class="fa-solid fa-robot"></i>
            <span>Compatibles</span>
            <span class="besoins-tab__count">{{ $totalCompat }}</span>
        </a>

        <a href="{{ route('agence.demandes.index', array_merge(request()->query(), ['onglet' => 'toutes'])) }}"
           class="besoins-tab {{ $onglet === 'toutes' ? 'is-active' : '' }}">
            <i class="fa-solid fa-list"></i>
            <span>Toutes</span>
            <span class="besoins-tab__count">{{ $totalDemandes }}</span>
        </a>
    </nav>

    {{-- ═══ FILTRES ═══ --}}
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
                <option value="">Tous types</option>
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
            <input type="text" id="filterSearch" placeholder="Rechercher..." value="{{ request('search') }}">
        </div>

        <button type="button" class="btn btn-rust" onclick="applyFilters()">
            <i class="fa-solid fa-filter"></i> Filtrer
        </button>

        <button type="button" class="btn btn-ghost" onclick="resetFilters()">
            <i class="fa-solid fa-rotate"></i>
        </button>
    </div>

    {{-- ═══ QUICK FILTERS ═══ --}}
    @if($onglet === 'toutes' && $demandes->count() > 0)
        <div class="quick-filters" id="quickFilters">
            <button type="button" class="quick-filter is-active" data-filter="all">
                Tout <span class="quick-filter__count">{{ $totalDemandes }}</span>
            </button>
            <button type="button" class="quick-filter" data-filter="compatible">
                <i class="fa-solid fa-check"></i> Compatibles
            </button>
            <button type="button" class="quick-filter" data-filter="non-compatible">
                Non compatibles
            </button>
        </div>
    @endif

    {{-- ═══ COMPTEUR RÉSULTATS ═══ --}}
    <div class="besoins-count">
        <i class="fa-solid fa-circle-info"></i>
        <strong>{{ $demandes->total() }}</strong>
        {{ $demandes->total() > 1 ? 'résultats' : 'résultat' }}
        @if($onglet === 'compatibles')
            <span>· compatibles avec vos biens</span>
        @else
            <span>· toutes les demandes</span>
        @endif
    </div>

    {{-- ═══ GRILLE ═══ --}}
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

                $nbOffres    = $demande->propositions->count();
                $nbEnAttente = $demande->propositions->where('statut', 'en_attente')->count();
            @endphp

            <article class="besoin-card besoin-card--{{ $scoreClass }} {{ $estCompatible ? 'is-compatible' : '' }}"
                     data-compatible="{{ $estCompatible ? 'true' : 'false' }}">

                {{-- Badge compatibilité --}}
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

                    <div class="besoin-card__headline">
                        <h3 class="besoin-card__type">{{ $demande->type_bien->label() }}</h3>
                        <div class="besoin-card__budget">
                            {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                            <small>{{ $budgetLabel }}</small>
                        </div>
                    </div>

                    <div class="besoin-card__meta">
                        <span class="meta-pill">
                            <i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}
                        </span>
                        <span class="meta-pill">
                            <i class="fa-solid fa-handshake"></i> {{ $demande->type_operation->label() }}
                        </span>
                        @if($demande->surface_minimum)
                            <span class="meta-pill">
                                <i class="fa-regular fa-square"></i> {{ $demande->surface_minimum }} m²
                            </span>
                        @endif
                        @if($demande->nombre_chambres)
                            <span class="meta-pill">
                                <i class="fa-solid fa-bed"></i> {{ $demande->nombre_chambres }} ch.
                            </span>
                        @endif
                    </div>

                    @if($estCompatible && $bien)
                        <div class="bien-match">
                            <i class="fa-solid fa-building"></i>
                            <div class="bien-match__info">
                                <strong>{{ $bien->titre ?? 'Bien correspondant' }}</strong>
                                <span>Votre bien qui correspond</span>
                            </div>
                        </div>
                    @endif

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

                    @if($demande->description)
                        <p class="besoin-card__desc">
                            {{ Str::limit($demande->description, 110) }}
                        </p>
                    @endif

                    <div class="besoin-card__date">
                        <i class="fa-regular fa-clock"></i>
                        Publiée {{ $demande->created_at->diffForHumans() }}
                    </div>
                </div>

                <footer class="besoin-card__foot">

                    @if($nbOffres > 0 || $nbEnAttente > 0)
                        <div class="besoin-card__stats">
                            @if($nbOffres > 0)
                                <span class="stat-chip stat-chip--offres">
                                    <i class="fa-regular fa-envelope"></i>
                                    <strong>{{ $nbOffres }}</strong>
                                    offre{{ $nbOffres > 1 ? 's' : '' }}
                                </span>
                            @endif
                            @if($nbEnAttente > 0)
                                <span class="stat-chip stat-chip--attente">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $nbEnAttente }} en attente
                                </span>
                            @endif
                        </div>
                    @endif

                    <div class="besoin-card__actions">
                        <a href="{{ route('agence.demandes.show', $demande->slug) }}" class="btn btn-ghost">
                            <i class="fa-solid fa-eye"></i> Voir
                        </a>
                        <a href="{{ route('agence.propositions.create', $demande->slug) }}" class="btn btn-rust">
                            <i class="fa-solid fa-paper-plane"></i> Faire une offre
                        </a>
                    </div>
                </footer>
            </article>

        @empty
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

    @if($demandes->count() > 0)
        <div class="pagination-wrapper">
            {{ $demandes->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection


@push('scripts')
<script>
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

    document.getElementById('filterSearch')?.addEventListener('keypress', e => {
        if (e.key === 'Enter') applyFilters();
    });

    document.addEventListener('DOMContentLoaded', function () {
        const wrap = document.getElementById('quickFilters');
        if (!wrap) return;

        const cards = document.querySelectorAll('#besoinGrid .besoin-card');
        const buttons = wrap.querySelectorAll('.quick-filter');

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
   BESOINS DISPONIBLES
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

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ STATS : cards visibles partout ═══ */
.besoins-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}
.besoins-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 16px 12px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    position: relative;
    transition: transform .15s, box-shadow .15s;
}
.besoins-stat:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0,0,0,.05);
}
.besoins-stat.is-highlight {
    border-color: var(--c-success);
    background: linear-gradient(135deg, #F0FBF4, #fff);
}
.besoins-stat__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.5px;
    line-height: 1;
}
.besoins-stat.is-highlight .besoins-stat__value { color: var(--c-success); }
.besoins-stat__label {
    font-size: 11.5px;
    font-weight: 500;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .3px;
}

/* ═══ TABS : visibles partout ═══ */
.besoins-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.besoins-tabs::-webkit-scrollbar { display: none; }

.besoins-tab {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-soft);
    text-decoration: none;
    transition: all .2s;
    white-space: nowrap;
    flex-shrink: 0;
}
.besoins-tab:hover {
    border-color: var(--rust);
    color: var(--rust);
}
.besoins-tab i { font-size: 13px; opacity: .85; }
.besoins-tab.is-active {
    background: var(--rust);
    border-color: var(--rust);
    color: #fff;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .22);
}
.besoins-tab__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 22px;
    padding: 0 8px;
    background: rgba(0, 0, 0, .06);
    color: inherit;
    border-radius: 999px;
    font-family: var(--display);
    font-size: 11.5px;
    font-weight: 800;
}
.besoins-tab.is-active .besoins-tab__count {
    background: rgba(255,255,255,.28);
    color: #fff;
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

/* ═══ QUICK FILTERS ═══ */
.quick-filters {
    display: flex;
    gap: 8px;
    margin-bottom: 14px;
    overflow-x: auto;
    scrollbar-width: none;
    padding-bottom: 2px;
}
.quick-filters::-webkit-scrollbar { display: none; }
.quick-filter {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 999px;
    font-family: inherit;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-soft);
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.quick-filter:hover { border-color: var(--rust); color: var(--rust); }
.quick-filter.is-active {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
}
.quick-filter__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 20px;
    padding: 0 7px;
    background: var(--surface);
    color: var(--text-soft);
    border-radius: 999px;
    font-family: var(--display);
    font-size: 11px;
    font-weight: 800;
}
.quick-filter.is-active .quick-filter__count {
    background: rgba(255,255,255,.3);
    color: #fff;
}
.quick-filter i { font-size: 11px; }

/* ═══ COMPTEUR ═══ */
.besoins-count {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    background: var(--surface);
    border-radius: 10px;
    font-size: 13px;
    color: var(--text-soft);
    margin-bottom: 14px;
}
.besoins-count i { color: var(--rust); font-size: 13px; }
.besoins-count strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 800;
    color: var(--ink);
}
.besoins-count span { opacity: .7; }

/* ═══ GRILLE ═══ */
.besoins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 14px;
}

/* ═══ CARTE ═══ */
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
.besoin-card.is-compatible { border-left: 3px solid var(--c-success); }
.besoin-card--excellent.is-compatible { border-left-color: var(--c-success); }
.besoin-card--bon.is-compatible       { border-left-color: var(--c-info); }
.besoin-card--moyen.is-compatible     { border-left-color: var(--c-warning); }
.besoin-card--faible.is-compatible    { border-left-color: var(--c-danger); }

/* Badge compatibilité */
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
.compat-badge span { font-weight: 500; opacity: .85; }

.compat-badge--excellent { background: linear-gradient(135deg, #F0FBF4, #E8F5E9); color: var(--c-success); }
.compat-badge--bon       { background: linear-gradient(135deg, #F0F7FF, #E3F2FD); color: var(--c-info); }
.compat-badge--moyen     { background: linear-gradient(135deg, #FFFBF0, #FFF8E1); color: var(--c-warning); }
.compat-badge--faible    { background: linear-gradient(135deg, #FFF5F5, #FFEBEE); color: var(--c-danger); }
.compat-badge--none      { background: var(--surface); color: var(--muted); justify-content: center; }

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

.besoin-card__meta { display: flex; flex-wrap: wrap; gap: 5px; }
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

.bien-match {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: var(--c-success-bg);
    border-radius: 10px;
    border: 1px solid #C8E6C9;
}
.bien-match i { color: var(--c-success); font-size: 16px; flex-shrink: 0; }
.bien-match__info { min-width: 0; display: flex; flex-direction: column; gap: 1px; }
.bien-match__info strong {
    font-size: 13px;
    font-weight: 700;
    color: var(--c-success);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.bien-match__info span { font-size: 11px; color: #175C35; opacity: .85; }

.equipements-row { display: flex; flex-wrap: wrap; gap: 4px; }
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

.besoin-card__date {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    color: var(--muted);
    margin-top: auto;
}
.besoin-card__date i { font-size: 11px; opacity: .8; }

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

.besoin-card__stats { display: flex; gap: 6px; flex-wrap: wrap; }

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

.besoin-card__actions { display: flex; gap: 6px; flex-shrink: 0; }

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
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
.pagination-wrapper .pagination,
.pagination-wrapper nav {
    display: flex; gap: 5px; justify-content: center;
    list-style: none; padding: 0; margin: 0; flex-wrap: wrap;
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
.pagination-wrapper a:hover { background: var(--border); border-color: var(--rust); color: var(--rust); }
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] { background: var(--rust); color: #fff; border-color: var(--rust); }
.pagination-wrapper .disabled span { opacity: .45; cursor: not-allowed; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .besoins-page { background: transparent; padding-top: 4px; }

    /* ─── STATS INLINE ─── */
    .besoins-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        grid-template-columns: none;
    }
    .besoins-stat {
        flex: 1;
        padding: 0 4px;
        background: transparent;
        border: none;
        border-radius: 0;
        gap: 2px;
    }
    .besoins-stat:hover { transform: none; box-shadow: none; }
    .besoins-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%; width: 0.5px;
        background: var(--ios-separator);
    }
    .besoins-stat__value { font-size: 22px; }
    .besoins-stat__label { font-size: 11px; color: var(--ios-label-3); text-transform: none; letter-spacing: 0; }

    /* ─── TABS PILLS iOS ─── */
    .besoins-tabs {
        gap: 8px;
        margin: 0 -16px 14px;
        padding: 0 16px 2px;
    }
    .besoins-tab {
        padding: 10px 16px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-size: 14px;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .besoins-tab:active { transform: scale(.95); }
    .besoins-tab.is-active {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .besoins-tab__count {
        background: #EFEFF4;
        color: var(--ios-label-2);
        font-size: 11.5px;
    }
    .besoins-tab.is-active .besoins-tab__count {
        background: rgba(255,255,255,.3);
        color: #fff;
    }

    /* ─── FILTRES masqués ─── */
    .filters { display: none; }

    /* ─── QUICK FILTERS iOS ─── */
    .quick-filters {
        gap: 8px;
        margin: 0 -16px 14px;
        padding: 0 16px 2px;
    }
    .quick-filter {
        padding: 9px 16px;
        background: var(--ios-card);
        border: none;
        font-size: 13px;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .quick-filter:active { transform: scale(.95); }
    .quick-filter.is-active {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .quick-filter.is-active .quick-filter__count {
        background: rgba(255,255,255,.3);
        color: #fff;
    }

    /* ─── COMPTEUR ─── */
    .besoins-count {
        padding: 0 4px;
        background: transparent;
        margin-bottom: 12px;
        font-size: 13px;
        color: var(--ios-label-3);
    }
    .besoins-count i { display: none; }

    /* ─── CARTES iOS ─── */
    .besoins-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .besoin-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: hidden;
        transition: transform .15s ease;
    }
    .besoin-card:hover { transform: none; }
    .besoin-card:active { transform: scale(.985); }

    .besoin-card.is-compatible { border-left: none; position: relative; }
    .besoin-card.is-compatible::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: var(--ios-radius-lg) var(--ios-radius-lg) 0 0;
        z-index: 2;
    }
    .besoin-card--excellent.is-compatible::before { background: var(--c-success); }
    .besoin-card--bon.is-compatible::before       { background: var(--c-info); }
    .besoin-card--moyen.is-compatible::before     { background: var(--c-warning); }
    .besoin-card--faible.is-compatible::before    { background: var(--c-danger); }

    .compat-badge {
        padding: 10px 16px;
        font-size: 12.5px;
        gap: 7px;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .compat-badge strong { font-size: 15px; }

    .compat-badge--excellent,
    .compat-badge--bon,
    .compat-badge--moyen,
    .compat-badge--faible { background: transparent; }
    .compat-badge--none {
        background: #EFEFF4;
        color: var(--ios-label-3);
        justify-content: center;
        font-weight: 500;
    }

    .besoin-card__body { padding: 14px 16px; gap: 10px; }
    .besoin-card__type { font-size: 17px; color: var(--ios-label); }
    .besoin-card__budget { font-size: 20px; }

    .meta-pill {
        background: #EFEFF4;
        border: none;
        color: var(--ios-label-2);
        font-size: 12.5px;
        padding: 4px 11px;
    }
    .meta-pill i { color: var(--rust); font-size: 11px; }

    .bien-match {
        background: linear-gradient(135deg, #F0FBF4, #E8F5E9);
        border: none;
        border-radius: 12px;
        padding: 12px 14px;
    }
    .bien-match i { font-size: 18px; }
    .bien-match__info strong { font-size: 14px; }
    .bien-match__info span { font-size: 12px; }

    .equipements-row { gap: 6px; }
    .equip-tag {
        background: #EFEFF4;
        border: none;
        color: var(--ios-label-2);
        padding: 4px 11px;
        font-size: 12px;
    }

    .besoin-card__desc { font-size: 13.5px; color: var(--ios-label-2); }
    .besoin-card__date { font-size: 12.5px; color: var(--ios-label-3); }

    .besoin-card__foot {
        padding: 12px 16px;
        background: transparent;
        border-top: 0.5px solid var(--ios-separator);
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }
    .besoin-card__stats { justify-content: flex-start; }
    .stat-chip {
        background: #EFEFF4;
        border: none;
        padding: 5px 11px;
        font-size: 12px;
    }
    .stat-chip--offres { background: var(--c-info-bg); color: var(--c-info); }
    .stat-chip--attente { background: var(--c-warning-bg); color: var(--c-warning); }

    .besoin-card__actions {
        flex-direction: column;
        align-items: stretch;
        width: 100%;
        gap: 8px;
    }
    .besoin-card__actions .btn {
        width: 100%;
        justify-content: center;
        padding: 12px 18px;
        font-size: 14px;
        border-radius: 12px;
        font-weight: 600;
    }
    .besoin-card__actions .btn-rust {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    }
    .besoin-card__actions .btn-ghost {
        background: var(--ios-card);
        border: 1px solid #E5E5EA;
        color: var(--ios-blue);
    }
    .besoin-card__actions .btn-ghost:active { background: #EFEFF4; }

    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon {
        background: #EFEFF4;
        color: var(--ios-label-3);
    }
    .empty-state h3 { font-size: 17px; }
    .empty-state p { font-size: 13.5px; color: var(--ios-label-3); }

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

    .besoin-card__type { font-size: 16px; }
    .besoin-card__budget { font-size: 19px; }
    .besoin-card__body { padding: 12px 14px; }
    .besoin-card__foot { padding: 10px 14px; }
}
</style>
@endpush