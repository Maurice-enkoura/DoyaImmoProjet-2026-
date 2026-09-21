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

    {{-- ═══ STATS ═══ --}}
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

    {{-- ═══ TABS ═══ --}}
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

    {{-- ═══ COMPTEUR ═══ --}}
    <div class="besoins-count">
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

            <article class="besoin-card {{ $estCompatible ? 'is-compatible' : '' }}"
                     data-compatible="{{ $estCompatible ? 'true' : 'false' }}">

                {{-- Badge compatibilité --}}
                @if($estCompatible)
                    <div class="compat-badge">
                        <strong>{{ $score }}%</strong>
                        <span>{{ $niveau ?? 'Correspondance' }}</span>
                    </div>
                @elseif($onglet === 'toutes')
                    <div class="compat-badge compat-badge--none">
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
                                <span class="stat-chip">
                                    <i class="fa-regular fa-envelope"></i>
                                    <strong>{{ $nbOffres }}</strong>
                                    offre{{ $nbOffres > 1 ? 's' : '' }}
                                </span>
                            @endif
                            @if($nbEnAttente > 0)
                                <span class="stat-chip">
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
   BESOINS DISPONIBLES — Design épuré
   ═══════════════════════════════════════════════════════════ */

.besoins-page {
    --c-success:    #1E7A47;
    --c-success-bg: #F1F8F4;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --c-info:       #1E40AF;
    --c-info-bg:    #EFF6FF;
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ STATS ═══ */
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
}
.besoins-stat__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.5px;
    line-height: 1;
}
.besoins-stat__label {
    font-size: 11.5px;
    font-weight: 500;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .3px;
}

/* ═══ TABS ═══ */
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
    padding: 9px 16px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-soft);
    text-decoration: none;
    transition: border-color .15s, background-color .15s, color .15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.besoins-tab:hover { border-color: #d8d8d8; }
.besoins-tab i { font-size: 12px; color: var(--muted); }
.besoins-tab.is-active {
    background: #F5F5F7;
    border-color: #d8d8d8;
    color: var(--ink);
    font-weight: 600;
}
.besoins-tab.is-active i { color: var(--rust); }

.besoins-tab__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 20px;
    padding: 0 7px;
    background: #F5F5F7;
    color: var(--text-soft);
    border-radius: 999px;
    font-family: var(--display);
    font-size: 11px;
    font-weight: 700;
}
.besoins-tab.is-active .besoins-tab__count {
    background: #fff;
    color: var(--ink);
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
    background: #F5F5F7;
    border: 1px solid transparent;
    border-radius: 10px;
    transition: border-color .15s;
}
.filters__field:focus-within { border-color: var(--rust); background: #fff; }
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
    padding: 7px 14px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 999px;
    font-family: inherit;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--text-soft);
    cursor: pointer;
    transition: border-color .15s, background-color .15s, color .15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.quick-filter:hover { border-color: #d8d8d8; }
.quick-filter.is-active {
    background: #F5F5F7;
    color: var(--ink);
    border-color: #d8d8d8;
    font-weight: 600;
}
.quick-filter i { font-size: 11px; color: var(--muted); }

.quick-filter__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 20px;
    padding: 0 7px;
    background: #F5F5F7;
    color: var(--text-soft);
    border-radius: 999px;
    font-family: var(--display);
    font-size: 11px;
    font-weight: 700;
}
.quick-filter.is-active .quick-filter__count {
    background: #fff;
    color: var(--ink);
}

/* ═══ COMPTEUR ═══ */
.besoins-count {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    background: #F5F5F7;
    border-radius: 10px;
    font-size: 13px;
    color: var(--text-soft);
    margin-bottom: 14px;
}
.besoins-count strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
}
.besoins-count span { color: var(--muted); }

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
    transition: border-color .2s;
}
.besoin-card:hover { border-color: #d8d8d8; }

/* Badge compatibilité */
.compat-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 500;
    border-bottom: 1px solid var(--border);
    background: #F5F5F7;
    color: var(--text-soft);
}
.compat-badge strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
}
.compat-badge span { color: var(--muted); }

.compat-badge--none {
    justify-content: center;
    color: var(--muted);
}

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
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.25;
}

.besoin-card__budget {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
}
.besoin-card__budget small {
    font-size: 10.5px;
    font-weight: 500;
    opacity: .7;
    margin-left: 2px;
}

.besoin-card__meta { display: flex; flex-wrap: wrap; gap: 5px; }
.meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 9px;
    border-radius: 999px;
    background: #F5F5F7;
    font-size: 11.5px;
    color: var(--text-soft);
    font-weight: 500;
}
.meta-pill i { font-size: 10px; color: var(--muted); }

.bien-match {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: #F5F5F7;
    border-radius: 10px;
}
.bien-match i { color: var(--muted); font-size: 16px; flex-shrink: 0; }
.bien-match__info { min-width: 0; display: flex; flex-direction: column; gap: 1px; }
.bien-match__info strong {
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.bien-match__info span { font-size: 11px; color: var(--muted); }

.equipements-row { display: flex; flex-wrap: wrap; gap: 4px; }
.equip-tag {
    display: inline-flex;
    align-items: center;
    padding: 3px 9px;
    background: #F5F5F7;
    color: var(--text-soft);
    border-radius: 999px;
    font-size: 11px;
    font-weight: 500;
}
.equip-tag--more {
    color: var(--muted);
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
.besoin-card__date i { font-size: 11px; }

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
    font-weight: 500;
    background: #F5F5F7;
    color: var(--text-soft);
}
.stat-chip i { font-size: 10px; color: var(--muted); }
.stat-chip strong {
    font-family: var(--display);
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
}

.besoin-card__actions { display: flex; gap: 6px; flex-shrink: 0; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
.btn-ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: #F5F5F7;
    border-color: #d8d8d8;
    color: var(--ink);
}

/* ═══ EMPTY ═══ */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 56px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
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
.empty-state h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--text);
}
.empty-state p {
    color: var(--muted);
    font-size: 13px;
    margin: 0 auto 16px;
    max-width: 400px;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper { margin-top: 22px; display: flex; justify-content: center; }
.pagination-wrapper .pagination,
.pagination-wrapper nav {
    display: flex; gap: 5px; justify-content: center;
    list-style: none; padding: 0; margin: 0; flex-wrap: wrap;
}
.pagination-wrapper a,
.pagination-wrapper span:not(.sr-only) {
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
.pagination-wrapper a:hover { background: #F5F5F7; }
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
        padding: 14px 6px;
        margin-bottom: 14px;
        grid-template-columns: none;
    }
    .besoins-stat {
        flex: 1;
        padding: 0 4px;
        background: transparent;
        border: none;
        border-radius: 0;
        gap: 3px;
    }
    .besoins-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%; width: 0.5px;
        background: var(--ios-separator);
    }
    .besoins-stat__value { font-size: 22px; }
    .besoins-stat__label {
        font-size: 11px;
        color: var(--muted);
        text-transform: none;
        letter-spacing: 0;
    }

    /* ─── TABS ─── */
    .besoins-tabs {
        gap: 8px;
        margin: 0 -16px 14px;
        padding: 0 16px 2px;
    }
    .besoins-tab {
        padding: 9px 16px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-size: 13.5px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .besoins-tab.is-active {
        background: var(--rust);
        color: #fff;
        box-shadow: none;
    }
    .besoins-tab__count {
        background: #EFEFF4;
        color: var(--text-soft);
    }
    .besoins-tab.is-active .besoins-tab__count {
        background: rgba(255,255,255,.25);
        color: #fff;
    }

    /* ─── FILTRES masqués ─── */
    .filters { display: none; }

    /* ─── QUICK FILTERS ─── */
    .quick-filters {
        gap: 8px;
        margin: 0 -16px 14px;
        padding: 0 16px 2px;
    }
    .quick-filter {
        padding: 8px 15px;
        background: var(--ios-card);
        border: none;
        font-size: 13px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .quick-filter.is-active {
        background: var(--rust);
        color: #fff;
    }
    .quick-filter.is-active .quick-filter__count {
        background: rgba(255,255,255,.25);
        color: #fff;
    }

    /* ─── COMPTEUR ─── */
    .besoins-count {
        padding: 0 4px;
        background: transparent;
        margin-bottom: 12px;
        font-size: 13px;
        color: var(--muted);
    }

    /* ─── CARTES ─── */
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
    }

    .compat-badge {
        padding: 10px 16px;
        font-size: 12.5px;
        background: #F5F5F7;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .compat-badge strong { font-size: 14px; }

    .compat-badge--none {
        background: #F5F5F7;
        color: var(--muted);
    }

    .besoin-card__body { padding: 14px 16px; gap: 10px; }
    .besoin-card__type { font-size: 16px; }
    .besoin-card__budget { font-size: 18px; }

    .meta-pill {
        background: #F5F5F7;
        font-size: 12px;
        padding: 4px 10px;
    }
    .meta-pill i { font-size: 11px; }

    .bien-match {
        background: #F5F5F7;
        border-radius: 12px;
        padding: 12px 14px;
    }
    .bien-match i { font-size: 16px; }
    .bien-match__info strong { font-size: 13.5px; }
    .bien-match__info span { font-size: 11.5px; }

    .equipements-row { gap: 6px; }
    .equip-tag {
        background: #F5F5F7;
        padding: 4px 11px;
        font-size: 12px;
    }

    .besoin-card__desc { font-size: 13px; }
    .besoin-card__date { font-size: 12px; }

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
        background: #F5F5F7;
        padding: 5px 11px;
        font-size: 12px;
    }

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
    }
    .besoin-card__actions .btn-rust {
        background: var(--rust);
        box-shadow: none;
    }
    .besoin-card__actions .btn-ghost {
        background: #F5F5F7;
        border: none;
        color: var(--text-soft);
    }

    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon {
        background: #F5F5F7;
        color: var(--muted);
    }
    .empty-state h3 { font-size: 16px; }
    .empty-state p { font-size: 13px; }

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

    .besoin-card__type { font-size: 15px; }
    .besoin-card__budget { font-size: 17px; }
    .besoin-card__body { padding: 12px 14px; }
    .besoin-card__foot { padding: 10px 14px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush