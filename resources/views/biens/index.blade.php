@extends('layouts.app')

@section('title', 'Parcourir les biens — DoyaImmo')

@section('content')
<div class="wrap page-head">
    <div class="page-head-inner">
        <span class="eyebrow">Biens disponibles</span>
        <h1 class="h-section">
            Découvrez les biens immobiliers à Dakar
        </h1>
    </div>
</div>

<div class="wrap section" style="padding-top:20px;">
    <!-- Barre de recherche avancée -->
    <div class="search-container">
        <form action="{{ route('biens.index') }}" method="GET" id="searchForm">
            <div class="search-grid">
                <!-- Recherche principale -->
                <div class="search-main">
                    <div class="search-input-wrapper">
                        <i class="fa-solid fa-search search-icon"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput" 
                               value="{{ request('search') }}" 
                               placeholder="Rechercher un bien..." 
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
                            if(request('type_contrat')) $count++;
                            if(request('prix_min') || request('prix_max')) $count++;
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
                        <label class="filter-label">Contrat</label>
                        <select name="type_contrat" class="filter-select">
                            <option value="">Tous les contrats</option>
                            <option value="vente" {{ request('type_contrat') == 'vente' ? 'selected' : '' }}>Vente</option>
                            <option value="location" {{ request('type_contrat') == 'location' ? 'selected' : '' }}>Location</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Prix min</label>
                        <input type="number" name="prix_min" value="{{ request('prix_min') }}" placeholder="Min" class="filter-input">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Prix max</label>
                        <input type="number" name="prix_max" value="{{ request('prix_max') }}" placeholder="Max" class="filter-input">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Tri</label>
                        <select name="sort" class="filter-select">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                            <option value="prix_asc" {{ request('sort') == 'prix_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="prix_desc" {{ request('sort') == 'prix_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            <option value="surface_desc" {{ request('sort') == 'surface_desc' ? 'selected' : '' }}>Plus grandes surfaces</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-rust btn-search">
                            <i class="fa-solid fa-search"></i> Rechercher
                        </button>
                        @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'type_contrat', 'prix_min', 'prix_max', 'sort']))
                            <a href="{{ route('biens.index') }}" class="btn btn-ghost btn-reset">
                                <i class="fa-solid fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <!-- Filtres actifs -->
        @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'type_contrat', 'prix_min', 'prix_max']))
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
                @if(request('type_contrat'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-file-signature"></i> {{ request('type_contrat') == 'vente' ? 'Vente' : 'Location' }}
                        <a href="#" onclick="removeFilter('type_contrat')">&times;</a>
                    </span>
                @endif
                @if(request('prix_min') || request('prix_max'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-money-bill"></i> 
                        {{ request('prix_min') ? number_format(request('prix_min'), 0, ',', ' ') : '0' }} - 
                        {{ request('prix_max') ? number_format(request('prix_max'), 0, ',', ' ') : '∞' }} F
                        <a href="#" onclick="removeFilter('prix_min');removeFilter('prix_max')">&times;</a>
                    </span>
                @endif
            </div>
        @endif
    </div>

    <p class="results-count">{{ $biens->total() }} biens disponibles</p>

    <!-- Liste des biens -->
    <div class="biens-wrapper">
        <div class="biens-grid" id="biensContainer">
            @forelse($biens as $bien)
                <div class="bien-card">
                    <div class="bien-image">
                        @php
                            $image = $bien->medias->where('type_media', 'image')->first();
                        @endphp
                        @if($image)
                            <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $bien->titre }}" loading="lazy">
                        @else
                            <div class="image-placeholder">
                                <i class="fa-solid fa-image"></i>
                                <span>Aucune image</span>
                            </div>
                        @endif
                        <div class="bien-type-badge">{{ $bien->type_bien->label() }}</div>
                        <div class="media-badge">
                            @php
                                $imagesCount = $bien->medias->where('type_media', 'image')->count();
                                $videosCount = $bien->medias->where('type_media', 'video')->count();
                            @endphp
                            @if($imagesCount > 0)
                                <span><i class="fa-regular fa-image"></i> {{ $imagesCount }}</span>
                            @endif
                            @if($videosCount > 0)
                                <span><i class="fa-regular fa-circle-play"></i> {{ $videosCount }}</span>
                            @endif
                        </div>
                        <div class="bien-status {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                            {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                        </div>
                    </div>
                    <div class="bien-body">
                        <div class="bien-title">{{ $bien->titre }}</div>
                        <div class="bien-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                        <div class="bien-location">
                            <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}
                        </div>
                        <div class="bien-type-tags">
                            <span class="meta-pill">
                                <i class="fa-solid fa-home"></i> {{ $bien->type_bien->label() }}
                            </span>
                            <span class="meta-pill">
                                <i class="fa-solid fa-tag"></i> {{ $bien->type_contrat->label() }}
                            </span>
                        </div>
                        <div class="bien-infos">
                            <span><i class="fa-solid fa-vector-square"></i> {{ $bien->surface }} m²</span>
                            <span><i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                            <span><i class="fa-solid fa-bath"></i> {{ $bien->nombre_salles_bain }} sdb</span>
                        </div>
                        <div class="bien-features">
                            @if($bien->parking_disponible)
                                <span class="meta-pill"><i class="fa-solid fa-car"></i> Parking</span>
                            @endif
                            @if($bien->est_meuble)
                                <span class="meta-pill"><i class="fa-solid fa-couch"></i> Meublé</span>
                            @endif
                        </div>
                        <div class="bien-action">
                            <a href="{{ route('biens.show', $bien) }}" class="btn btn-rust btn-sm btn-block">Voir le détail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fa-solid fa-building"></i>
                    <p>Aucun bien disponible pour le moment.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
        {{ $biens->appends(request()->query())->links() }}
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
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
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

    .filter-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: clamp(12px, 0.8vw, 13px);
        font-family: inherit;
        color: var(--ink);
        transition: border-color 0.3s;
        -webkit-appearance: none;
        appearance: none;
    }

    .filter-input:focus {
        outline: none;
        border-color: var(--rust);
    }

    .filter-input::placeholder {
        color: var(--muted);
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

    /* ===== BIENS WRAPPER ===== */
    .biens-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    @media (min-width: 768px) {
        .biens-wrapper {
            padding: 0 24px;
        }
    }

    @media (min-width: 1200px) {
        .biens-wrapper {
            padding: 0;
        }
    }

    /* ===== BIENS GRID ===== */
    .biens-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr));
        gap: 20px;
        margin: 0 auto;
    }

    .bien-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }

    .bien-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }

    .bien-image {
        height: clamp(160px, 22vw, 200px);
        background: #E8ECF0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .image-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: var(--muted);
        opacity: 0.5;
    }

    .image-placeholder i {
        font-size: 32px;
        margin-bottom: 4px;
    }

    .image-placeholder span {
        font-size: 11px;
    }

    .bien-type-badge {
        position: absolute;
        bottom: 10px;
        left: 10px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: clamp(9px, 0.7vw, 10px);
        font-weight: 600;
        color: #fff;
        background: rgba(0, 0, 0, 0.7);
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .bien-status {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: clamp(9px, 0.7vw, 10px);
        font-weight: 600;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .bien-status.disponible {
        background: #1E7A47;
    }

    .bien-status.indisponible {
        background: var(--muted);
    }

    .media-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        display: flex;
        gap: 6px;
        font-size: clamp(9px, 0.7vw, 10px);
        color: #fff;
        background: rgba(0,0,0,0.6);
        padding: 3px 8px;
        border-radius: 4px;
    }

    .media-badge span {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .bien-body {
        padding: 14px 16px 16px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .bien-title {
        font-weight: 700;
        font-size: clamp(14px, 1vw, 16px);
        margin-bottom: 2px;
        color: var(--ink);
        line-height: 1.3;
        word-break: break-word;
    }

    .bien-price {
        font-weight: 700;
        color: var(--rust);
        font-size: clamp(15px, 1.1vw, 17px);
        margin-bottom: 3px;
    }

    .bien-location {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
        margin-bottom: 6px;
        word-break: break-word;
    }

    .bien-location i {
        font-size: 11px;
        margin-right: 3px;
    }

    .bien-type-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 6px;
    }

    .bien-infos {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 6px;
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--text-soft);
    }

    .bien-infos span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .bien-infos span i {
        font-size: 11px;
    }

    .bien-features {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-top: 6px;
    }

    .bien-action {
        margin-top: 12px;
    }

    /* ===== META PILL ===== */
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: clamp(9px, 0.7vw, 10px);
        color: var(--text-soft);
    }

    .meta-pill i {
        font-size: 10px;
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
    }

    /* ===== PAGINATION ===== */
    .pagination-container {
        max-width: 1200px;
        margin: 32px auto 0;
        padding: 0 16px;
        display: flex;
        justify-content: center;
    }

    @media (min-width: 768px) {
        .pagination-container {
            padding: 0 24px;
        }
    }

    @media (min-width: 1200px) {
        .pagination-container {
            padding: 0;
        }
    }

    .pagination {
        display: flex;
        gap: 5px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination li {
        display: inline;
    }

    .pagination a, 
    .pagination span {
        display: inline-block;
        padding: clamp(5px, 0.5vw, 8px) clamp(8px, 0.8vw, 14px);
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: clamp(11px, 0.8vw, 13px);
        transition: all 0.2s;
        min-width: clamp(28px, 3vw, 40px);
        text-align: center;
    }

    .pagination a:hover {
        background: var(--border);
        border-color: var(--border);
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
        padding: 6px 12px;
        font-size: clamp(10px, 0.7vw, 11.5px);
        border-radius: 6px;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
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

        .biens-grid {
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr));
            gap: 16px;
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

        .biens-wrapper {
            padding: 0 12px;
        }

        .biens-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .bien-image {
            height: 140px;
        }

        .bien-body {
            padding: 10px 12px 12px;
        }

        .bien-title {
            font-size: 13px;
        }

        .bien-price {
            font-size: 14px;
        }

        .bien-location {
            font-size: 11px;
        }

        .bien-infos {
            font-size: 11px;
            gap: 6px;
        }

        .meta-pill {
            font-size: 9px;
            padding: 1px 8px;
        }

        .bien-type-badge {
            font-size: 8px;
            padding: 2px 8px;
        }

        .bien-status {
            font-size: 8px;
            padding: 2px 8px;
        }

        .media-badge {
            font-size: 8px;
            padding: 2px 6px;
        }

        .pagination a, 
        .pagination span {
            padding: 4px 8px;
            font-size: 11px;
            min-width: 28px;
        }

        .pagination-container {
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

        .filter-select, .filter-input {
            padding: 7px 10px;
            font-size: 13px;
        }

        .filter-select {
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

        .biens-wrapper {
            padding: 0 8px;
        }

        .biens-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .bien-image {
            height: 180px;
        }

        .bien-body {
            padding: 12px 14px 14px;
        }

        .bien-title {
            font-size: 15px;
        }

        .bien-price {
            font-size: 16px;
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