@extends('layouts.app')

@section('title', 'Parcourir les biens — DoyaImmo')

@section('content')
<!-- ==================== PAGE HEAD ==================== -->
<div class="wrap page-head">
    <div class="page-head-inner">
        <span class="eyebrow">Biens disponibles</span>
        <h1 class="h-section">
            Découvrez les biens immobiliers à Dakar
        </h1>
    </div>
</div>

<div class="wrap section" style="padding-top:20px;">
    <!-- ==================== BIENS EN VEDETTE ==================== -->
    @if(isset($biensVedette) && $biensVedette->count() > 0)
    <div class="vedette-section">
        <div class="vedette-header">
            <span class="eyebrow">⭐ À la une</span>
            <h2 class="h-section" style="font-size: clamp(18px, 2vw, 22px);">Biens en vedette</h2>
        </div>
        <div class="vedette-grid">
            @foreach($biensVedette as $bien)
                <div class="vedette-card">
                    <div class="vedette-image">
                        @php
                            $image = $bien->medias->where('type_media', 'image')->first();
                        @endphp
                        @if($image)
                            <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $bien->titre }}" loading="lazy">
                        @else
                            <div class="image-placeholder">
                                <i class="fa-solid fa-image"></i>
                            </div>
                        @endif
                        <!-- BADGE VEDETTE - en haut à GAUCHE -->
                        <span class="vedette-badge">
                            <i class="fa-solid fa-star"></i> Vedette
                        </span>
                        <!-- BADGE STATUS - en haut à DROITE -->
                        <span class="bien-status {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                            {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                        </span>
                    </div>
                    <div class="vedette-body">
                        <h3 class="vedette-title">{{ $bien->titre }}</h3>
                        <div class="vedette-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                        <div class="vedette-location">
                            <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier->nom ?? $bien->quartier }}
                        </div>
                        <div class="vedette-date">
                            <i class="fa-regular fa-clock"></i>
                            Publié {{ $bien->created_at->diffForHumans() }}
                        </div>
                        <div class="vedette-features">
                            <span><i class="fa-solid fa-vector-square"></i> {{ $bien->surface }} m²</span>
                            <span><i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                            <span><i class="fa-solid fa-bath"></i> {{ $bien->nombre_salles_bain }} sdb</span>
                        </div>
                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                        <a href="{{ route('biens.show', $bien->slug) }}" class="btn btn-rust btn-sm btn-block">
                            <i class="fa-regular fa-eye"></i> Voir le bien
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- ==================== BARRE DE RECHERCHE + TOGGLE ==================== -->
    <div class="search-container">
        <!-- Barre supérieure avec recherche + toggle -->
        <div class="search-top-bar">
            <div class="search-main">
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-search search-icon"></i>
                    <input type="text" 
                           name="search" 
                           id="searchInput" 
                           form="searchForm"
                           value="{{ request('search') }}" 
                           placeholder="Rechercher un bien..." 
                           class="search-input"
                           autocomplete="off">
                    <div id="autocompleteResults" class="autocomplete-results"></div>
                </div>
            </div>

            <!-- Toggle Liste / Grille -->
            <div class="view-toggle">
                <button type="button" class="view-btn active" id="gridViewBtn" onclick="setView('grid')" title="Vue grille">
                    <i class="fa-solid fa-table-cells-large"></i>
                </button>
                <button type="button" class="view-btn" id="listViewBtn" onclick="setView('list')" title="Vue liste">
                    <i class="fa-solid fa-list"></i>
                </button>
            </div>

            <!-- Bouton filtres mobile -->
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
        </div>

        <!-- Filtres -->
        <form action="{{ route('biens.index') }}" method="GET" id="searchForm">
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

    <!-- ==================== LISTE DES BIENS (GRID / LIST) ==================== -->
    <div class="biens-wrapper">
        <div class="biens-grid" id="biensContainer">
            @forelse($biens as $bien)
                <div class="bien-card {{ $bien->est_vedette && $bien->vedette_fin > now() ? 'vedette-card' : '' }}">
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
                        
                        <!-- BADGE VEDETTE - en haut à GAUCHE -->
                        @if($bien->est_vedette && $bien->vedette_fin > now())
                            <span class="badge-vedette">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif
                        
                        <!-- BADGE TYPE - en bas à GAUCHE -->
                        <div class="bien-type-badge">{{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}</div>
                        
                        <!-- BADGE STATUS - en haut à DROITE (ne chevauche pas Vedette) -->
                        <div class="bien-status {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                            {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                        </div>
                        
                        <!-- MEDIA BADGE - en bas à DROITE -->
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
                    </div>
                    <div class="bien-body">
                        <div class="bien-title">{{ $bien->titre }}</div>
                        <div class="bien-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                        <div class="bien-location">
                            <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier->nom ?? $bien->quartier }}
                        </div>
                        <div class="bien-date">
                            <i class="fa-regular fa-clock"></i>
                            Publié {{ $bien->created_at->diffForHumans() }}
                        </div>
                        <div class="bien-type-tags">
                            <span class="meta-pill">
                                <i class="fa-solid fa-home"></i> {{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}
                            </span>
                            <span class="meta-pill">
                                <i class="fa-solid fa-tag"></i> {{ is_object($bien->type_contrat) && method_exists($bien->type_contrat, 'label') ? $bien->type_contrat->label() : $bien->type_contrat }}
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
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <a href="{{ route('biens.show', $bien->slug) }}" class="btn btn-rust btn-sm btn-block">Voir le détail</a>
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

    <!-- ==================== PAGINATION ==================== -->
    @if($biens->hasPages())
    <div class="pagination-container">
        <nav class="pagination-nav" aria-label="Pagination des biens">
            <div class="pagination-info">
                <span class="pagination-stats">
                    Affichage de <strong>{{ $biens->firstItem() }}</strong> à <strong>{{ $biens->lastItem() }}</strong> 
                    sur <strong>{{ $biens->total() }}</strong> biens
                </span>
            </div>

            <ul class="pagination">
                @if($biens->onFirstPage())
                    <li class="disabled" aria-disabled="true">
                        <span>&laquo; Précédent</span>
                    </li>
                @else
                    <li>
                        <a href="{{ $biens->previousPageUrl() }}" rel="prev" aria-label="Page précédente">
                            &laquo; Précédent
                        </a>
                    </li>
                @endif

                @php
                    $currentPage = $biens->currentPage();
                    $lastPage = $biens->lastPage();
                    $window = 2;
                @endphp

                @foreach(range(1, $lastPage) as $page)
                    @if($page == 1 || $page == $lastPage || abs($page - $currentPage) <= $window)
                        @if($page == $currentPage)
                            <li class="active" aria-current="page">
                                <span>{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $biens->url($page) }}" aria-label="Page {{ $page }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @elseif($page == $currentPage - $window - 1 || $page == $currentPage + $window + 1)
                        <li class="disabled" aria-disabled="true">
                            <span>&hellip;</span>
                        </li>
                    @endif
                @endforeach

                @if($biens->hasMorePages())
                    <li>
                        <a href="{{ $biens->nextPageUrl() }}" rel="next" aria-label="Page suivante">
                            Suivant &raquo;
                        </a>
                    </li>
                @else
                    <li class="disabled" aria-disabled="true">
                        <span>Suivant &raquo;</span>
                    </li>
                @endif
            </ul>

            <div class="pagination-per-page">
                <label for="perPage" class="per-page-label">Afficher :</label>
                <select id="perPage" class="per-page-select" onchange="changePerPage(this.value)">
                    <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12</option>
                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                    <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
                    <option value="96" {{ request('per_page') == 96 ? 'selected' : '' }}>96</option>
                </select>
                <span class="per-page-text">par page</span>
            </div>
        </nav>
    </div>
    @endif
</div>

<style>
/* ===== PAGE HEAD ===== */
.page-head { padding: 20px 0 10px; }
.page-head-inner { max-width: 1200px; margin: 0 auto; padding: 0 16px; }
@media (min-width: 768px) { .page-head-inner { padding: 0 24px; } }
@media (min-width: 1200px) { .page-head-inner { padding: 0 40px; } }

.h-section { font-family: var(--display); font-weight: 800; font-size: clamp(22px, 3vw, 30px); line-height: 1.2; }
.eyebrow { display: inline-block; font-size: clamp(10px, 0.7vw, 11px); font-weight: 600; color: var(--rust); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }

/* ===== SEARCH TOP BAR ===== */
.search-top-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}

.search-main { flex: 1; }
.search-input-wrapper { position: relative; }
.search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--muted); font-size: 14px; pointer-events: none; z-index: 2; }
.search-input { width: 100%; padding: 10px 16px 10px 42px; border: 1px solid var(--border); border-radius: 10px; font-size: clamp(13px, 0.9vw, 14px); font-family: inherit; background: #F7F9FC; transition: all 0.3s ease; -webkit-appearance: none; appearance: none; }
.search-input:focus { outline: none; border-color: var(--rust); background: #fff; box-shadow: 0 0 0 3px rgba(184, 92, 58, 0.08); }
.search-input::placeholder { color: var(--muted); font-size: clamp(12px, 0.8vw, 13px); }

/* ===== VIEW TOGGLE ===== */
.view-toggle {
    display: flex;
    gap: 4px;
    background: #F7F9FC;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 4px;
}

.view-btn {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    border-radius: 6px;
    color: var(--muted);
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
}

.view-btn:hover {
    background: var(--border);
    color: var(--ink);
}

.view-btn.active {
    background: var(--rust);
    color: #fff;
    box-shadow: 0 2px 8px rgba(184, 92, 58, 0.25);
}

/* ===== FILTERS TOGGLE ===== */
.btn-filters-toggle {
    display: none;
    padding: 8px 16px;
    background: #F7F9FC;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    cursor: pointer;
    transition: all 0.3s ease;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-family: inherit;
}

.btn-filters-toggle:hover { background: var(--border); }
.btn-filters-toggle .filters-count { color: var(--rust); font-weight: 700; }
.btn-filters-toggle .fa-chevron-down { transition: transform 0.3s ease; font-size: 12px; }
.btn-filters-toggle .fa-chevron-down.open { transform: rotate(180deg); }

/* ===== AUTOCOMPLETE ===== */
.autocomplete-results { position: absolute; top: 100%; left: 0; right: 0; background: #fff; border: 1px solid var(--border); border-radius: 10px; margin-top: 4px; box-shadow: 0 8px 24px rgba(0,0,0,0.1); z-index: 1000; display: none; max-height: 300px; overflow-y: auto; }
.autocomplete-item { padding: 8px 14px; cursor: pointer; display: flex; align-items: center; gap: 10px; border-bottom: 1px solid var(--border); transition: background 0.2s; }
.autocomplete-item:last-child { border-bottom: none; }
.autocomplete-item:hover { background: #F7F9FC; }
.autocomplete-item .item-icon { color: var(--rust); width: 18px; font-size: 13px; flex-shrink: 0; }
.autocomplete-item .item-content { flex: 1; min-width: 0; }
.autocomplete-item .item-title { font-weight: 600; font-size: 13px; color: var(--ink); }
.autocomplete-item .item-desc { font-size: 11px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.autocomplete-item .item-type { font-size: 9px; text-transform: uppercase; color: var(--muted); background: var(--border); padding: 1px 8px; border-radius: 999px; flex-shrink: 0; font-weight: 600; }

/* ===== FILTERS ===== */
.search-filters { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 10px; transition: all 0.3s ease; }
.filter-group { display: flex; flex-direction: column; gap: 3px; }
.filter-label { font-size: clamp(10px, 0.7vw, 11px); color: var(--muted); font-weight: 500; }
.filter-select { width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: clamp(12px, 0.8vw, 13px); background: #fff; font-family: inherit; color: var(--ink); -webkit-appearance: none; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; cursor: pointer; transition: border-color 0.3s; padding-right: 28px; }
.filter-select:focus { outline: none; border-color: var(--rust); }
.filter-input { width: 100%; padding: 8px 12px; border: 1px solid var(--border); border-radius: 8px; font-size: clamp(12px, 0.8vw, 13px); font-family: inherit; color: var(--ink); transition: border-color 0.3s; -webkit-appearance: none; appearance: none; }
.filter-input:focus { outline: none; border-color: var(--rust); }
.filter-input::placeholder { color: var(--muted); }
.filter-actions { display: flex; align-items: flex-end; gap: 8px; }
.btn-search { flex: 1; justify-content: center; min-width: 90px; padding: 8px 14px; }
.btn-reset { flex-shrink: 0; padding: 8px 12px; }

/* ===== ACTIVE FILTERS ===== */
.active-filters { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--border); align-items: center; }
.active-filters-label { font-size: clamp(10px, 0.7vw, 11px); color: var(--muted); margin-right: 2px; }
.filter-tag { display: inline-flex; align-items: center; gap: 4px; background: var(--rust-soft); color: var(--rust); padding: 3px 10px; border-radius: 999px; font-size: clamp(10px, 0.7vw, 11px); }
.filter-tag a { color: var(--rust); text-decoration: none; font-weight: 700; margin-left: 2px; font-size: 14px; line-height: 1; }
.filter-tag a:hover { color: #9A4523; }

/* ===== RESULTS COUNT ===== */
.results-count { font-size: clamp(12px, 0.8vw, 13px); color: var(--muted); margin-bottom: 16px; text-align: center; }

/* ===== BIENS WRAPPER ===== */
.biens-wrapper { max-width: 1200px; margin: 0 auto; padding: 0 16px; }
@media (min-width: 768px) { .biens-wrapper { padding: 0 24px; } }
@media (min-width: 1200px) { .biens-wrapper { padding: 0; } }

/* ===== BIENS GRID ===== */
.biens-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr)); gap: 20px; margin: 0 auto; }
.bien-card { background: #fff; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column; position: relative; z-index: 1; }
.bien-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.06); }

/* ===== IMAGE CONTAINER ===== */
.bien-image { 
    position: relative;
    height: clamp(160px, 22vw, 200px); 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    overflow: hidden; 
    flex-shrink: 0; 
    background: #F0F2F5;
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
.image-placeholder i { font-size: 32px; margin-bottom: 4px; }
.image-placeholder span { font-size: 11px; }

/* ============================================================
   BADGES SUR L'IMAGE - POSITIONNEMENT CORRIGÉ
   ============================================================ */

/* ✅ BADGE VEDETTE - en haut à GAUCHE */
.badge-vedette { 
    position: absolute; 
    top: 10px; 
    left: 10px; 
    z-index: 6;
    padding: 4px 12px; 
    border-radius: 999px; 
    font-size: 10px; 
    font-weight: 700; 
    color: #FFFFFF !important;
    background: #D4AF37 !important;
    display: flex; 
    align-items: center; 
    gap: 4px; 
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3); 
    animation: pulseVedette 2s ease-in-out infinite; 
    border: 1px solid rgba(255,255,255,0.2);
}
.badge-vedette i { font-size: 10px; color: #FFFFFF !important; }

/* ✅ BADGE TYPE - en bas à GAUCHE */
.bien-type-badge { 
    position: absolute; 
    bottom: 10px; 
    left: 10px; 
    z-index: 5;
    padding: 4px 12px; 
    border-radius: 999px; 
    font-size: clamp(9px, 0.7vw, 10px); 
    font-weight: 700; 
    color: #FFFFFF !important;
    background: var(--rust) !important;
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    border: 1px solid rgba(255,255,255,0.2);
}

/* ✅ BADGE STATUS - en haut à DROITE (ne chevauche pas Vedette) */
.bien-status { 
    position: absolute; 
    top: 10px; 
    right: 10px; 
    z-index: 6;
    padding: 4px 12px; 
    border-radius: 999px; 
    font-size: clamp(9px, 0.7vw, 10px); 
    font-weight: 700; 
    color: #FFFFFF !important;
    text-transform: uppercase; 
    letter-spacing: 0.5px; 
    border: 1px solid rgba(255,255,255,0.2);
}
.bien-status.disponible { 
    background: var(--teal) !important;
}
.bien-status.indisponible { 
    background: #8A91A0 !important; 
}

/* ✅ MEDIA BADGE - en bas à DROITE */
.media-badge { 
    position: absolute; 
    bottom: 10px; 
    right: 10px; 
    z-index: 5;
    display: flex; 
    gap: 6px; 
    font-size: clamp(9px, 0.7vw, 10px); 
    color: #FFFFFF !important;
    background: rgba(0,0,0,0.6) !important;
    padding: 3px 10px; 
    border-radius: 999px; 
    border: 1px solid rgba(255,255,255,0.15);
}
.media-badge span { display: flex; align-items: center; gap: 4px; }
.media-badge span i { color: #FFFFFF !important; }

/* ============================================================
   FIN BADGES
   ============================================================ */

/* ===== BIEN BODY ===== */
.bien-body { padding: 14px 16px 16px; flex: 1; display: flex; flex-direction: column; position: relative; z-index: 1; }
.bien-title { font-weight: 700; font-size: clamp(14px, 1vw, 16px); margin-bottom: 2px; color: var(--ink); line-height: 1.3; word-break: break-word; }
.bien-price { font-weight: 700; color: #B85C3A; font-size: clamp(15px, 1.1vw, 17px); margin-bottom: 3px; }
.bien-location { font-size: clamp(12px, 0.8vw, 13px); color: var(--muted); margin-bottom: 6px; word-break: break-word; }
.bien-location i { font-size: 11px; margin-right: 3px; }

/* ===== DATE DE PUBLICATION ===== */
.bien-date {
    font-size: clamp(11px, 0.7vw, 12px);
    color: var(--muted);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.bien-date i {
    font-size: 11px;
    color: var(--muted);
}

.bien-type-tags { display: flex; flex-wrap: wrap; gap: 4px; margin-bottom: 6px; }
.bien-infos { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 6px; font-size: clamp(12px, 0.8vw, 13px); color: var(--text-soft); }
.bien-infos span { display: flex; align-items: center; gap: 4px; }
.bien-infos span i { font-size: 11px; }
.bien-features { display: flex; flex-wrap: wrap; gap: 4px; margin-top: 6px; }
.bien-action { margin-top: 12px; }
.meta-pill { display: inline-flex; align-items: center; gap: 4px; background: var(--border); padding: 2px 10px; border-radius: 999px; font-size: clamp(9px, 0.7vw, 10px); color: var(--text-soft); }
.meta-pill i { font-size: 10px; }

/* ===== ANIMATION VEDETTE ===== */
@keyframes pulseVedette { 
    0%, 100% { opacity: 1; } 
    50% { opacity: 0.85; } 
}

/* ============================================================
   VUE LISTE - IMAGES AGRANDIES
   ============================================================ */

/* ===== VUE LISTE DESKTOP ===== */
.biens-grid.list-view {
    grid-template-columns: 1fr;
    gap: 16px;
}

.biens-grid.list-view .bien-card {
    flex-direction: row;
    align-items: stretch;
    height: auto;
}

.biens-grid.list-view .bien-image {
    width: 380px;
    min-width: 380px;
    height: 280px;
    min-height: 280px;
}

.biens-grid.list-view .bien-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.biens-grid.list-view .bien-body {
    flex: 1;
    padding: 16px 20px;
}

.biens-grid.list-view .bien-title {
    font-size: 18px;
}

.biens-grid.list-view .bien-price {
    font-size: 20px;
}

.biens-grid.list-view .bien-location {
    font-size: 14px;
}

.biens-grid.list-view .bien-date {
    font-size: 13px;
}

.biens-grid.list-view .bien-infos {
    font-size: 14px;
    gap: 12px;
    margin-top: 8px;
}

.biens-grid.list-view .bien-action {
    margin-top: auto;
    display: flex;
    justify-content: flex-end;
}

.biens-grid.list-view .bien-action .btn {
    width: auto;
    min-width: 160px;
}

/* ===== VUE LISTE TABLETTE ===== */
@media (max-width: 768px) {
    .biens-grid.list-view {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .biens-grid.list-view .bien-card {
        flex-direction: row;
        align-items: stretch;
        height: auto;
        padding: 0;
        border-radius: 12px;
        border: 1px solid var(--border);
        background: #fff;
        overflow: hidden;
    }

    .biens-grid.list-view .bien-image {
        width: 180px;
        min-width: 180px;
        height: 180px;
        min-height: 180px;
        border-radius: 0;
        overflow: hidden;
        position: relative;
        flex-shrink: 0;
        margin: 0;
    }

    .biens-grid.list-view .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        position: absolute !important;
        top: 0;
        left: 0;
    }

    .biens-grid.list-view .bien-image .image-placeholder {
        display: none !important;
    }

    .biens-grid.list-view .bien-type-badge {
        position: absolute;
        bottom: 6px;
        left: 6px;
        font-size: 7px;
        padding: 3px 8px;
        color: #FFFFFF !important;
        background: #B85C3A !important;
        border-radius: 4px;
        z-index: 5;
        display: block !important;
        font-weight: 700;
        letter-spacing: 0.3px;
        border: 1px solid rgba(255,255,255,0.15);
    }

    .biens-grid.list-view .bien-status {
        position: absolute;
        top: 6px;
        right: 6px;
        font-size: 7px;
        padding: 3px 8px;
        border-radius: 4px;
        z-index: 5;
        display: block !important;
        font-weight: 700;
        letter-spacing: 0.3px;
        border: 1px solid rgba(255,255,255,0.15);
    }
    .biens-grid.list-view .bien-status.disponible {
        background: #2A9D8F !important;
        color: #FFFFFF !important;
    }
    .biens-grid.list-view .bien-status.indisponible {
        background: #8A91A0 !important;
        color: #FFFFFF !important;
    }

    .biens-grid.list-view .media-badge {
        position: absolute;
        bottom: 6px;
        right: 6px;
        font-size: 7px;
        padding: 3px 8px;
        z-index: 5;
        display: flex !important;
        color: #FFFFFF !important;
        background: rgba(0,0,0,0.6) !important;
        border-radius: 4px;
        border: 1px solid rgba(255,255,255,0.15);
        gap: 4px;
    }
    .biens-grid.list-view .media-badge span i {
        color: #FFFFFF !important;
    }

    .biens-grid.list-view .badge-vedette {
        position: absolute;
        top: 6px;
        left: 6px;
        font-size: 7px;
        padding: 3px 8px;
        border-radius: 4px;
        z-index: 5;
        display: flex !important;
        color: #FFFFFF !important;
        background: #D4AF37 !important;
        border: 1px solid rgba(255,255,255,0.15);
    }
    .biens-grid.list-view .badge-vedette i {
        color: #FFFFFF !important;
    }

    .biens-grid.list-view .bien-body {
        flex: 1;
        padding: 10px 12px 10px 14px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 180px;
        background: #fff;
    }

    .biens-grid.list-view .bien-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 2px;
        line-height: 1.2;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        color: var(--ink);
    }

    .biens-grid.list-view .bien-price {
        font-size: 14px;
        font-weight: 700;
        color: #B85C3A;
        margin-bottom: 2px;
    }

    .biens-grid.list-view .bien-location {
        font-size: 11px;
        color: var(--muted);
        margin-bottom: 2px;
    }

    .biens-grid.list-view .bien-date {
        font-size: 10px;
        margin-bottom: 3px;
    }

    .biens-grid.list-view .bien-type-tags,
    .biens-grid.list-view .bien-infos,
    .biens-grid.list-view .bien-features {
        display: none !important;
    }

    .biens-grid.list-view .bien-action {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 4px;
    }

    .biens-grid.list-view .bien-action .btn {
        width: auto;
        min-width: auto;
        padding: 4px 12px;
        font-size: 10px;
        border-radius: 6px;
    }
}

/* ===== VUE LISTE PETIT MOBILE ===== */
@media (max-width: 480px) {
    .biens-grid.list-view .bien-image {
        width: 150px;
        min-width: 150px;
        height: 150px;
        min-height: 150px;
    }

    .biens-grid.list-view .bien-body {
        padding: 8px 10px 8px 12px;
        min-height: 150px;
    }

    .biens-grid.list-view .bien-title {
        font-size: 12px;
    }

    .biens-grid.list-view .bien-price {
        font-size: 13px;
    }

    .biens-grid.list-view .bien-location {
        font-size: 10px;
    }

    .biens-grid.list-view .bien-date {
        font-size: 9px;
    }

    .biens-grid.list-view .bien-action .btn {
        font-size: 9px;
        padding: 3px 10px;
    }

    .biens-grid.list-view .bien-type-badge,
    .biens-grid.list-view .bien-status,
    .biens-grid.list-view .media-badge,
    .biens-grid.list-view .badge-vedette {
        font-size: 6px;
        padding: 2px 6px;
    }
}

/* ===== PAGINATION ===== */
.pagination-container { max-width: 1200px; margin: 32px auto 0; padding: 0 16px; }
@media (min-width: 768px) { .pagination-container { padding: 0 24px; } }
@media (min-width: 1200px) { .pagination-container { padding: 0; } }
.pagination-nav { background: #fff; border: 1px solid var(--border); border-radius: 12px; padding: 16px 20px; display: flex; flex-direction: column; gap: 16px; align-items: center; }
.pagination-info { width: 100%; text-align: center; }
.pagination-stats { font-size: clamp(12px, 0.8vw, 14px); color: var(--text-soft); }
.pagination-stats strong { color: var(--ink); font-weight: 700; }
.pagination { display: flex; gap: 4px; list-style: none; padding: 0; margin: 0; flex-wrap: wrap; justify-content: center; }
.pagination li { display: inline; }
.pagination a, .pagination span { display: inline-flex; align-items: center; justify-content: center; padding: clamp(6px, 0.5vw, 8px) clamp(10px, 0.8vw, 14px); border-radius: 8px; border: 1px solid var(--border); color: var(--text-soft); text-decoration: none; font-size: clamp(11px, 0.8vw, 13px); transition: all 0.2s ease; min-width: clamp(32px, 3vw, 40px); min-height: clamp(32px, 3vw, 40px); text-align: center; background: #fff; font-weight: 500; }
.pagination a:hover { background: var(--border); border-color: var(--border); color: var(--ink); transform: translateY(-1px); }
.pagination .active span { background: #B85C3A; color: #fff; border-color: #B85C3A; box-shadow: 0 2px 8px rgba(184, 92, 58, 0.25); }
.pagination .disabled span { opacity: 0.5; cursor: not-allowed; background: #f7f7f7; }
.pagination .disabled span:hover { transform: none; }
.pagination a[rel="prev"], .pagination a[rel="next"] { font-weight: 600; gap: 4px; }
.pagination-per-page { display: flex; align-items: center; gap: 6px; font-size: clamp(12px, 0.8vw, 13px); color: var(--text-soft); border-top: 1px solid var(--border); padding-top: 14px; width: 100%; justify-content: center; flex-wrap: wrap; }
.per-page-label { font-weight: 500; }
.per-page-select { padding: 4px 24px 4px 10px; border: 1px solid var(--border); border-radius: 6px; font-size: clamp(12px, 0.8vw, 13px); font-family: inherit; color: var(--ink); background: #fff; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 8px center; cursor: pointer; -webkit-appearance: none; appearance: none; transition: border-color 0.3s; }
.per-page-select:focus { outline: none; border-color: #B85C3A; }
.per-page-text { color: var(--muted); }

/* ===== BOUTONS ===== */
.btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; border-radius: 8px; font-size: clamp(11px, 0.8vw, 12.5px); font-weight: 600; text-decoration: none; transition: all 0.2s; border: 1px solid transparent; cursor: pointer; font-family: inherit; white-space: nowrap; }
.btn-rust { background: #B85C3A; color: #fff; border-color: #B85C3A; }
.btn-rust:hover { background: #9A4523; border-color: #9A4523; color: #fff; }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: var(--border); color: var(--ink); }
.btn-sm { padding: 6px 12px; font-size: clamp(10px, 0.7vw, 11.5px); border-radius: 6px; }
.btn-block { width: 100%; justify-content: center; }

/* ===== BADGE VEDETTE SUR CARTE ===== */
.bien-card.vedette-card { border: 2px solid #D4AF37; position: relative; }
.bien-card.vedette-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; border-radius: 12px; background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), transparent); pointer-events: none; z-index: 0; }

/* ===== VEDETTE SECTION ===== */
.vedette-section { background: linear-gradient(135deg, #FDF5E6 0%, #FFF8E1 100%); border: 2px solid #D4AF37; border-radius: 16px; padding: 20px 20px 24px; margin-bottom: 24px; max-width: 1200px; margin-left: auto; margin-right: auto; }
.vedette-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.vedette-header .eyebrow { margin-bottom: 0; background: #D4AF37; color: #fff; padding: 4px 16px; border-radius: 999px; }
.vedette-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px; }

/* ===== VEDETTE CARD ===== */
.vedette-card { background: #fff; border-radius: 12px; border: 1px solid var(--border); overflow: hidden; transition: transform 0.2s, box-shadow 0.2s; display: flex; flex-direction: column; }
.vedette-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }

/* ===== VEDETTE IMAGE ===== */
.vedette-image { position: relative; height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; background: #F0F2F5; }
.vedette-image img { width: 100%; height: 100%; object-fit: cover; display: block; }
.vedette-image .image-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 28px; opacity: 0.3; }

/* ✅ BADGE VEDETTE - en haut à GAUCHE (section vedette) */
.vedette-badge { 
    position: absolute; 
    top: 8px; 
    left: 8px; 
    z-index: 6;
    padding: 2px 12px; 
    border-radius: 999px; 
    font-size: 10px; 
    font-weight: 700; 
    color: #fff; 
    background: #D4AF37; 
    display: flex; 
    align-items: center; 
    gap: 4px; 
    animation: pulseVedette 2s ease-in-out infinite; 
}
.vedette-badge i { font-size: 10px; color: #fff; }

/* ✅ BADGE STATUS - en haut à DROITE (section vedette) */
.vedette-image .bien-status {
    position: absolute;
    top: 8px;
    right: 8px;
    z-index: 6;
    padding: 2px 12px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.vedette-image .bien-status.disponible {
    background: #2A9D8F !important;
}
.vedette-image .bien-status.indisponible {
    background: #8A91A0 !important;
}

.vedette-body { padding: 12px 14px 14px; flex: 1; display: flex; flex-direction: column; }
.vedette-title { font-weight: 700; font-size: 14px; margin-bottom: 2px; color: var(--ink); line-height: 1.3; word-break: break-word; }
.vedette-price { font-weight: 700; color: #B85C3A; font-size: 15px; margin-bottom: 3px; }
.vedette-location { font-size: 12px; color: var(--muted); margin-bottom: 4px; word-break: break-word; }
.vedette-location i { font-size: 11px; margin-right: 3px; }

/* ===== DATE DE PUBLICATION - VEDETTE ===== */
.vedette-date {
    font-size: 11px;
    color: var(--muted);
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 4px;
}
.vedette-date i {
    font-size: 10px;
    color: var(--muted);
}

.vedette-features { display: flex; flex-wrap: wrap; gap: 8px; font-size: 11px; color: var(--text-soft); margin-bottom: 10px; }
.vedette-features span { display: flex; align-items: center; gap: 4px; }
.vedette-features span i { font-size: 10px; }
.vedette-body .btn { margin-top: auto; }

/* ===== RESPONSIVE ===== */
@media (max-width: 640px) { 
    .vedette-section { padding: 16px; border-radius: 12px; } 
    .vedette-grid { grid-template-columns: 1fr 1fr; gap: 12px; } 
    .vedette-image { height: 120px; } 
    .vedette-title { font-size: 13px; } 
    .vedette-price { font-size: 14px; } 
    .vedette-location { font-size: 11px; } 
    .vedette-features { font-size: 10px; gap: 4px; } 
}
@media (max-width: 460px) { 
    .vedette-grid { grid-template-columns: 1fr; } 
    .vedette-image { height: 160px; } 
    .vedette-title { font-size: 15px; } 
    .vedette-price { font-size: 16px; } 
}

/* ===== RESPONSIVE GENERAL ===== */
@media (max-width: 820px) {
    .search-filters { grid-template-columns: 1fr 1fr; }
    .filter-actions { grid-column: 1 / -1; }
    .biens-grid { grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr)); gap: 16px; }
    .pagination-nav { padding: 14px 16px; }
}

@media (max-width: 640px) {
    .page-head { padding: 16px 0 8px; }
    .search-container { padding: 12px; border-radius: 10px; }
    .search-top-bar { flex-wrap: wrap; }
    .btn-filters-toggle { display: flex; width: 100%; }
    .search-filters { display: none; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--border); }
    .search-filters.open { display: grid; }
    .filter-actions { grid-column: 1 / -1; flex-direction: row; }
    .btn-search { flex: 1; justify-content: center; padding: 8px 12px; font-size: 12px; }
    .btn-reset { padding: 8px 12px; }
    .active-filters { flex-direction: column; align-items: flex-start; gap: 4px; }
    .biens-wrapper { padding: 0 12px; }
    .biens-grid:not(.list-view) { grid-template-columns: 1fr 1fr; gap: 12px; }
    .bien-image { height: 140px; }
    .bien-body { padding: 10px 12px 12px; }
    .bien-title { font-size: 13px; }
    .bien-price { font-size: 14px; }
    .bien-location { font-size: 11px; }
    .bien-date { font-size: 10px; }
    .bien-infos { font-size: 11px; gap: 6px; }
    .meta-pill { font-size: 9px; padding: 1px 8px; }
    .view-toggle { width: auto; }
    .view-btn { width: 36px; height: 36px; }
    .search-input { padding: 8px 12px 8px 36px; font-size: 14px; }
    .search-icon { left: 12px; font-size: 13px; }
    .filter-select, .filter-input { padding: 7px 10px; font-size: 13px; }
    .filter-select { padding-right: 26px; }
    .search-input::placeholder { font-size: 12px; }
    .btn-filters-toggle { font-size: 13px; padding: 8px 14px; }
}

@media (max-width: 460px) {
    .search-container { padding: 10px; }
    .search-filters { grid-template-columns: 1fr; gap: 8px; }
    .filter-actions { flex-direction: column; }
    .btn-search { width: 100%; }
    .btn-reset { width: 100%; justify-content: center; }
    .biens-wrapper { padding: 0 8px; }
    .biens-grid:not(.list-view) { grid-template-columns: 1fr; gap: 12px; }
    .bien-image { height: 180px; }
    .bien-body { padding: 12px 14px 14px; }
    .bien-title { font-size: 15px; }
    .bien-price { font-size: 16px; }
    .bien-date { font-size: 11px; }
    .btn-filters-toggle { font-size: 13px; padding: 8px 14px; }
    .pagination a, .pagination span { padding: 3px 6px; font-size: 10px; min-width: 24px; min-height: 24px; border-radius: 6px; }
    .pagination-nav { padding: 10px; gap: 10px; }
    .pagination a[rel="prev"], .pagination a[rel="next"] { font-size: 10px; padding: 3px 8px; }
    .per-page-select { font-size: 12px; padding: 3px 20px 3px 8px; }
}

@media (prefers-reduced-motion: reduce) {
    * { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
    .badge-vedette { animation: none !important; }
    .vedette-badge { animation: none !important; }
}
</style>

<script>
function removeFilter(name) {
    const url = new URL(window.location.href);
    url.searchParams.delete(name);
    window.location.href = url.toString();
}

function toggleFilters() {
    const filters = document.getElementById('searchFilters');
    const arrow = document.getElementById('filtersArrow');
    filters.classList.toggle('open');
    arrow.classList.toggle('open');
}

function changePerPage(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('per_page', value);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

function setView(view) {
    const container = document.getElementById('biensContainer');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (view === 'grid') {
        container.classList.remove('list-view');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        localStorage.setItem('biensView', 'grid');
    } else {
        container.classList.add('list-view');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        localStorage.setItem('biensView', 'list');
    }
}

// Charger la vue sauvegardée
document.addEventListener('DOMContentLoaded', function() {
    const savedView = localStorage.getItem('biensView');
    if (savedView === 'list') {
        setView('list');
    } else {
        setView('grid');
    }
});

// Autocomplete
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

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-input-wrapper')) {
            resultsContainer.style.display = 'none';
        }
    });
});

document.addEventListener('click', function(e) {
    const filters = document.getElementById('searchFilters');
    if (window.innerWidth <= 640) {
        if (!e.target.closest('.search-container') && filters && filters.classList.contains('open')) {
            filters.classList.remove('open');
            document.getElementById('filtersArrow').classList.remove('open');
        }
    }
});
</script>
@endsection