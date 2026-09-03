@extends('layouts.app')

@section('title', 'Parcourir les besoins — DoyaImmo')

@section('content')
<div class="wrap page-head">
    <div class="page-head-inner">
        <span class="eyebrow">Besoins publiés</span>
        <h1 class="h-section">
            Découvrez ce que recherchent les clients à Dakar
        </h1>
    </div>
</div>

<div class="wrap section" style="padding-top:20px;">
    <!-- Barre de recherche avancée -->
    <div class="search-container">
        <form action="{{ route('besoins.index') }}" method="GET" id="searchForm">
            <div class="search-grid">
                <!-- Recherche principale -->
                <div class="search-main">
                    <div class="search-input-wrapper">
                        <i class="fa-solid fa-search search-icon"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput" 
                               value="{{ request('search') }}" 
                               placeholder="Rechercher un besoin..." 
                               class="search-input"
                               autocomplete="off">
                        <div id="autocompleteResults" class="autocomplete-results"></div>
                    </div>
                </div>

                <!-- Bouton pour afficher/masquer les filtres sur mobile -->
                <button type="button" class="btn-filters-toggle" id="filtersToggle" onclick="toggleFilters()">
                    <i class="fa-solid fa-sliders-h"></i> Filtres
                    <span class="filters-count" id="filtersCount">
                        @php
                            $count = 0;
                            if(request('quartier')) $count++;
                            if(request('type_bien')) $count++;
                            if(request('budget')) $count++;
                            if(request('type_operation')) $count++;
                            if(request('sort') && request('sort') != 'recent') $count++;
                        @endphp
                        @if($count > 0)
                            ({{ $count }})
                        @endif
                    </span>
                    <i class="fa-solid fa-chevron-down" id="filtersArrow"></i>
                </button>

                <!-- Filtres -->
                <div class="search-filters" id="searchFilters">
                    <div class="filter-group">
                        <label class="filter-label">Quartier</label>
                        <select name="quartier" class="filter-select">
                            <option value="">Tous les quartiers</option>
                            @foreach($quartiers as $quartier)
                                <option value="{{ $quartier->id }}" {{ request('quartier') == $quartier->id ? 'selected' : '' }}>
                                    {{ $quartier->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Type de bien</label>
                        <select name="type_bien" class="filter-select">
                            <option value="">Tous les types</option>
                            @foreach($typesBien as $key => $label)
                                <option value="{{ $key }}" {{ request('type_bien') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Budget max</label>
                        <select name="budget" class="filter-select">
                            <option value="">Tous les budgets</option>
                            <option value="0-100000" {{ request('budget') == '0-100000' ? 'selected' : '' }}>Moins de 100 000 F</option>
                            <option value="100000-300000" {{ request('budget') == '100000-300000' ? 'selected' : '' }}>100 000 – 300 000 F</option>
                            <option value="300000-500000" {{ request('budget') == '300000-500000' ? 'selected' : '' }}>300 000 – 500 000 F</option>
                            <option value="500000-1000000" {{ request('budget') == '500000-1000000' ? 'selected' : '' }}>500 000 – 1 000 000 F</option>
                            <option value="1000000+" {{ request('budget') == '1000000+' ? 'selected' : '' }}>Plus de 1 000 000 F</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Type d'opération</label>
                        <select name="type_operation" class="filter-select">
                            <option value="">Tous</option>
                            <option value="achat" {{ request('type_operation') == 'achat' ? 'selected' : '' }}>Achat</option>
                            <option value="location" {{ request('type_operation') == 'location' ? 'selected' : '' }}>Location</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Tri</label>
                        <select name="sort" class="filter-select">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                            <option value="budget_asc" {{ request('sort') == 'budget_asc' ? 'selected' : '' }}>Budget croissant</option>
                            <option value="budget_desc" {{ request('sort') == 'budget_desc' ? 'selected' : '' }}>Budget décroissant</option>
                            <option value="propositions" {{ request('sort') == 'propositions' ? 'selected' : '' }}>Plus de propositions</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-rust btn-search">
                            <i class="fa-solid fa-search"></i> Rechercher
                        </button>
                        @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'budget', 'type_operation', 'sort']))
                            <a href="{{ route('besoins.index') }}" class="btn btn-ghost btn-reset">
                                <i class="fa-solid fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <!-- Filtres actifs -->
        @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'budget', 'type_operation']))
            <div class="active-filters">
                <span class="active-filters-label">Filtres actifs :</span>
                @if(request('search'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-search"></i> {{ request('search') }}
                        <a href="#" onclick="removeFilter('search')">&times;</a>
                    </span>
                @endif
                @if(request('quartier'))
                    @php
                        $quartier = App\Models\Quartier::find(request('quartier'));
                    @endphp
                    <span class="filter-tag">
                        <i class="fa-solid fa-location-dot"></i> {{ $quartier ? $quartier->nom : '' }}
                        <a href="#" onclick="removeFilter('quartier')">&times;</a>
                    </span>
                @endif
                @if(request('type_bien'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-home"></i> {{ $typesBien[request('type_bien')] ?? '' }}
                        <a href="#" onclick="removeFilter('type_bien')">&times;</a>
                    </span>
                @endif
                @if(request('budget'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-money-bill"></i> 
                        @php
                            $budget = request('budget');
                            $parts = explode('-', $budget);
                            if (count($parts) == 2) {
                                echo number_format($parts[0], 0, ',', ' ') . ' - ' . number_format($parts[1], 0, ',', ' ') . ' F';
                            } else {
                                echo str_replace('+', '+ ', $budget) . ' F';
                            }
                        @endphp
                        <a href="#" onclick="removeFilter('budget')">&times;</a>
                    </span>
                @endif
                @if(request('type_operation'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-handshake"></i> {{ request('type_operation') == 'achat' ? 'Achat' : 'Location' }}
                        <a href="#" onclick="removeFilter('type_operation')">&times;</a>
                    </span>
                @endif
            </div>
        @endif
    </div>

    <p class="results-count">{{ $demandes->total() }} besoins actuellement publiés</p>

    <!-- Liste des demandes -->
    <div class="besoin-wrapper">
        <div class="besoin-grid" id="besoinsContainer">
            @forelse($demandes as $demande)
                <div class="besoin-card">
                    <div class="besoin-body">
                        <div class="besoin-top">
                            <div class="besoin-type">{{ $demande->type_bien->label() }}</div>
                            <div class="besoin-budget">{{ number_format($demande->budget_maximum, 0, ',', ' ') }} F</div>
                        </div>
                        <div class="besoin-meta">
                            <span class="meta-pill"><i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}</span>
                            <span class="meta-pill"><i class="fa-solid fa-house"></i> {{ $demande->type_operation->label() }}</span>
                            <span class="meta-pill"><i class="fa-regular fa-message"></i> {{ $demande->propositions->count() }} offres</span>
                        </div>
                        <div class="status-badge status-{{ $demande->statut->value }}">
                            <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                            {{ $demande->statut->label() }}
                        </div>
                        <p class="besoin-desc">{{ Str::limit($demande->description, 80) }}</p>
                        <div class="besoin-foot">
                            <span class="posted">Publié {{ $demande->created_at->diffForHumans() }}</span>
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <a href="{{ route('besoins.show', $demande->slug) }}" class="btn btn-ghost btn-sm">Voir</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fa-solid fa-inbox"></i>
                    <p>Aucun besoin trouvé pour le moment.</p>
                    <a href="{{ route('register.particulier') }}" class="btn btn-rust">Publier un besoin</a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $demandes->appends(request()->query())->links() }}
    </div>
</div>

<style>
    /* ===== PAGE HEAD ===== */
    .page-head {
        padding: 20px 0 10px;
    }

    .page-head-inner {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    @media (min-width: 768px) {
        .page-head-inner {
            padding: 0 24px;
        }
    }

    @media (min-width: 1200px) {
        .page-head-inner {
            padding: 0 40px;
        }
    }

    .h-section {
        font-family: var(--display);
        font-weight: 800;
        font-size: clamp(22px, 3vw, 30px);
        line-height: 1.2;
    }

    .eyebrow {
        display: inline-block;
        font-size: clamp(10px, 0.7vw, 11px);
        font-weight: 600;
        color: var(--rust);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    /* ===== SEARCH CONTAINER ===== */
    .search-container {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: clamp(14px, 1.5vw, 20px);
        margin-bottom: 20px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    /* ===== SEARCH GRID ===== */
    .search-grid {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .search-main {
        width: 100%;
    }

    .search-input-wrapper {
        position: relative;
        width: 100%;
    }

    .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-size: 14px;
        pointer-events: none;
        z-index: 2;
    }

    .search-input {
        width: 100%;
        padding: 10px 16px 10px 42px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: clamp(13px, 0.9vw, 14px);
        font-family: inherit;
        background: #F7F9FC;
        transition: all 0.3s ease;
        -webkit-appearance: none;
        appearance: none;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--rust);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }

    .search-input::placeholder {
        color: var(--muted);
        font-size: clamp(12px, 0.8vw, 13px);
    }

    /* ===== BOUTON TOGGLE FILTRES ===== */
    .btn-filters-toggle {
        display: none;
        width: 100%;
        padding: 10px 16px;
        background: #F7F9FC;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-soft);
        cursor: pointer;
        transition: all 0.3s ease;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-family: inherit;
    }

    .btn-filters-toggle:hover {
        background: var(--border);
    }

    .btn-filters-toggle .filters-count {
        color: var(--rust);
        font-weight: 700;
    }

    .btn-filters-toggle .fa-chevron-down {
        transition: transform 0.3s ease;
        font-size: 12px;
    }

    .btn-filters-toggle .fa-chevron-down.open {
        transform: rotate(180deg);
    }

    /* ===== AUTOCOMPLETE ===== */
    .autocomplete-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 10px;
        margin-top: 4px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        z-index: 1000;
        display: none;
        max-height: 300px;
        overflow-y: auto;
    }

    .autocomplete-item {
        padding: 8px 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .autocomplete-item:last-child {
        border-bottom: none;
    }

    .autocomplete-item:hover {
        background: #F7F9FC;
    }

    .autocomplete-item .item-icon {
        color: var(--rust);
        width: 18px;
        font-size: 13px;
        flex-shrink: 0;
    }

    .autocomplete-item .item-content {
        flex: 1;
        min-width: 0;
    }

    .autocomplete-item .item-title {
        font-weight: 600;
        font-size: 13px;
        color: var(--ink);
    }

    .autocomplete-item .item-desc {
        font-size: 11px;
        color: var(--muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .autocomplete-item .item-type {
        font-size: 9px;
        text-transform: uppercase;
        color: var(--muted);
        background: var(--border);
        padding: 1px 8px;
        border-radius: 999px;
        flex-shrink: 0;
        font-weight: 600;
    }

    /* ===== FILTERS ===== */
    .search-filters {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        transition: all 0.3s ease;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .filter-label {
        font-size: clamp(10px, 0.7vw, 11px);
        color: var(--muted);
        font-weight: 500;
    }

    .filter-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: clamp(12px, 0.8vw, 13px);
        background: #fff;
        font-family: inherit;
        color: var(--ink);
        -webkit-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        cursor: pointer;
        transition: border-color 0.3s;
        padding-right: 28px;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--rust);
    }

    .filter-actions {
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }

    .btn-search {
        flex: 1;
        justify-content: center;
        min-width: 90px;
        padding: 8px 14px;
    }

    .btn-reset {
        flex-shrink: 0;
        padding: 8px 12px;
    }

    /* ===== ACTIVE FILTERS ===== */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid var(--border);
        align-items: center;
    }

    .active-filters-label {
        font-size: clamp(10px, 0.7vw, 11px);
        color: var(--muted);
        margin-right: 2px;
    }

    .filter-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--rust-soft);
        color: var(--rust);
        padding: 3px 10px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11px);
    }

    .filter-tag a {
        color: var(--rust);
        text-decoration: none;
        font-weight: 700;
        margin-left: 2px;
        font-size: 14px;
        line-height: 1;
    }

    .filter-tag a:hover {
        color: #9A4523;
    }

    /* ===== RESULTS COUNT ===== */
    .results-count {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
        margin-bottom: 16px;
        text-align: center;
    }

    /* ===== BESOIN WRAPPER ===== */
    .besoin-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    @media (min-width: 768px) {
        .besoin-wrapper {
            padding: 0 24px;
        }
    }

    @media (min-width: 1200px) {
        .besoin-wrapper {
            padding: 0;
        }
    }

    /* ===== BESOIN GRID ===== */
    .besoin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr));
        gap: 16px;
        margin: 0 auto;
    }

    .besoin-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }

    .besoin-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
    }

    .besoin-body {
        padding: 14px 16px 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .besoin-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
        gap: 8px;
    }

    .besoin-type {
        font-family: var(--display);
        font-weight: 700;
        font-size: clamp(14px, 1vw, 15px);
        color: var(--ink);
        line-height: 1.2;
        flex: 1;
    }

    .besoin-budget {
        font-weight: 700;
        color: var(--rust);
        font-size: clamp(13px, 0.9vw, 14px);
        white-space: nowrap;
        flex-shrink: 0;
    }

    .besoin-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 6px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        background: var(--border);
        padding: 2px 8px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11px);
        color: var(--text-soft);
    }

    .meta-pill i {
        font-size: 9px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11px);
        font-weight: 600;
        margin: 4px 0 6px;
        align-self: flex-start;
    }

    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }

    .status-en_cours {
        background: #E3F2FD;
        color: #0D47A1;
    }

    .status-terminee {
        background: #E8F5E9;
        color: #1E7A47;
    }

    .status-annulee {
        background: #FFEBEE;
        color: #C62828;
    }

    .besoin-desc {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--text-soft);
        line-height: 1.5;
        margin-bottom: 10px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
    }

    .besoin-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 10px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 6px;
        margin-top: auto;
    }

    .posted {
        font-size: clamp(10px, 0.7vw, 11px);
        color: var(--muted);
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 40px 20px;
        color: var(--muted);
    }

    .empty-state i {
        font-size: 32px;
        display: block;
        margin-bottom: 12px;
        opacity: 0.3;
    }

    .empty-state p {
        font-size: clamp(14px, 0.9vw, 15px);
        margin-bottom: 14px;
    }

    /* ===== PAGINATION ===== */
    .pagination-wrapper {
        max-width: 1200px;
        margin: 32px auto 0;
        padding: 0 16px;
    }

    @media (min-width: 768px) {
        .pagination-wrapper {
            padding: 0 24px;
        }
    }

    @media (min-width: 1200px) {
        .pagination-wrapper {
            padding: 0;
        }
    }

    .pagination {
        display: flex;
        gap: 5px;
        justify-content: center;
        list-style: none;
        padding: 0;
        flex-wrap: wrap;
    }

    .pagination li {
        display: inline;
    }

    .pagination a,
    .pagination span {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: clamp(11px, 0.8vw, 12px);
        transition: all 0.2s;
        min-width: 30px;
        text-align: center;
    }

    .pagination a:hover {
        background: var(--border);
    }

    .pagination .active span {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ===== BUTTONS ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: clamp(11px, 0.8vw, 12.5px);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
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
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }

    .btn-ghost:hover {
        background: var(--border);
        color: var(--ink);
    }

    .btn-sm {
        padding: 4px 10px;
        font-size: clamp(10px, 0.7vw, 11px);
        border-radius: 6px;
    }

    /* ============================================
       RESPONSIVE
    ============================================ */

    /* Tablette */
    @media (max-width: 820px) {
        .search-filters {
            grid-template-columns: 1fr 1fr;
        }

        .filter-actions {
            grid-column: 1 / -1;
        }

        .besoin-grid {
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr));
            gap: 14px;
        }
    }

    /* Mobile - FILTRES MASQUÉS PAR DÉFAUT */
    @media (max-width: 640px) {
        .page-head {
            padding: 16px 0 8px;
        }

        .search-container {
            padding: 12px;
            border-radius: 10px;
        }

        .btn-filters-toggle {
            display: flex;
        }

        .search-filters {
            display: none;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
        }

        .search-filters.open {
            display: grid;
        }

        .filter-actions {
            grid-column: 1 / -1;
            flex-direction: row;
        }

        .btn-search {
            flex: 1;
            justify-content: center;
            padding: 8px 12px;
            font-size: 12px;
        }

        .btn-reset {
            padding: 8px 12px;
        }

        .active-filters {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .besoin-wrapper {
            padding: 0 12px;
        }

        .besoin-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .besoin-body {
            padding: 10px 12px 12px;
        }

        .besoin-type {
            font-size: 13px;
        }

        .besoin-budget {
            font-size: 12px;
        }

        .besoin-desc {
            font-size: 11.5px;
            -webkit-line-clamp: 2;
        }

        .besoin-foot {
            padding-top: 8px;
        }

        .posted {
            font-size: 9px;
        }

        .pagination a,
        .pagination span {
            padding: 4px 8px;
            font-size: 11px;
            min-width: 28px;
        }

        .pagination-wrapper {
            padding: 0 12px;
        }

        .search-input {
            padding: 8px 12px 8px 36px;
            font-size: 14px;
        }

        .search-icon {
            left: 12px;
            font-size: 13px;
        }

        .filter-select {
            padding: 7px 10px;
            font-size: 13px;
            padding-right: 26px;
        }

        .search-input::placeholder {
            font-size: 12px;
        }

        .btn-filters-toggle {
            font-size: 13px;
            padding: 8px 14px;
        }
    }

    /* Petit mobile */
    @media (max-width: 460px) {
        .search-container {
            padding: 10px;
        }

        .search-filters {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .filter-actions {
            flex-direction: column;
        }

        .btn-search {
            width: 100%;
        }

        .btn-reset {
            width: 100%;
            justify-content: center;
        }

        .besoin-wrapper {
            padding: 0 8px;
        }

        .besoin-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .besoin-body {
            padding: 10px 12px 12px;
        }

        .status-badge {
            font-size: 10px;
            padding: 2px 8px;
        }

        .meta-pill {
            font-size: 9px;
            padding: 1px 6px;
        }

        .besoin-type {
            font-size: 14px;
        }

        .besoin-budget {
            font-size: 13px;
        }

        .btn-filters-toggle {
            font-size: 13px;
            padding: 8px 14px;
        }
    }

    /* ===== ACCESSIBILITÉ ===== */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        .search-filters {
            transition: none !important;
        }
        .btn-filters-toggle .fa-chevron-down {
            transition: none !important;
        }
    }
</style>

<script>
function removeFilter(name) {
    const url = new URL(window.location.href);
    url.searchParams.delete(name);
    window.location.href = url.toString();
}

// Toggle des filtres sur mobile
function toggleFilters() {
    const filters = document.getElementById('searchFilters');
    const arrow = document.getElementById('filtersArrow');
    filters.classList.toggle('open');
    arrow.classList.toggle('open');
}

// Autocomplétion en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const resultsContainer = document.getElementById('autocompleteResults');
    let debounceTimer;

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultsContainer.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/api/search/autocomplete?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        resultsContainer.style.display = 'none';
                        return;
                    }

                    resultsContainer.innerHTML = data.map(item => `
                        <div class="autocomplete-item" onclick="window.location.href='${item.url || '#'}'">
                            <i class="${item.icon || 'fa-solid fa-circle'} item-icon"></i>
                            <div class="item-content">
                                <div class="item-title">${item.label}</div>
                                <div class="item-desc">${item.description || ''}</div>
                            </div>
                            <span class="item-type">${item.type || ''}</span>
                        </div>
                    `).join('');

                    resultsContainer.style.display = 'block';
                })
                .catch(() => {
                    resultsContainer.style.display = 'none';
                });
        }, 300);
    });

    // Fermer l'autocomplétion en cliquant ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-input-wrapper')) {
            resultsContainer.style.display = 'none';
        }
    });
});

// Fermer les filtres si on clique en dehors
document.addEventListener('click', function(e) {
    const filters = document.getElementById('searchFilters');
    const toggleBtn = document.getElementById('filtersToggle');
    if (window.innerWidth <= 640) {
        if (!e.target.closest('.search-container') && filters && filters.classList.contains('open')) {
            filters.classList.remove('open');
            document.getElementById('filtersArrow').classList.remove('open');
        }
    }
});
</script>
@endsection