@extends('layouts.app')

@section('title', 'Parcourir les biens — DoyaImmo')

@section('content')
<div class="wrap page-head">
    <span class="eyebrow">Biens disponibles</span>
    <h1 class="h-section" style="font-family:var(--display); font-weight:800; font-size:30px;">
        Découvrez les biens immobiliers à Dakar
    </h1>
</div>

<div class="wrap section" style="padding-top:20px;">
    <!-- Barre de recherche avancée -->
    <div class="search-container" style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:24px;">
        <form action="{{ route('biens.index') }}" method="GET" id="searchForm">
            <div class="search-grid">
                <!-- Recherche principale -->
                <div class="search-main">
                    <div class="search-input-wrapper" style="position:relative;">
                        <i class="fa-solid fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput" 
                               value="{{ request('search') }}" 
                               placeholder="Rechercher un bien (titre, quartier, adresse...)" 
                               style="width:100%;padding:12px 16px 12px 44px;border:1px solid var(--border);border-radius:12px;font-size:14px;font-family:inherit;"
                               autocomplete="off">
                        <div id="autocompleteResults" style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid var(--border);border-radius:12px;margin-top:4px;box-shadow:0 8px 32px rgba(0,0,0,0.1);z-index:1000;display:none;max-height:400px;overflow-y:auto;"></div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="search-filters" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:12px;margin-top:12px;">
                    <div>
                        <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Quartier</label>
                        <select name="quartier" class="filter-select" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;">
                            <option value="">Tous les quartiers</option>
                            @foreach($quartiers as $quartier)
                                <option value="{{ $quartier->id }}" {{ request('quartier') == $quartier->id ? 'selected' : '' }}>
                                    {{ $quartier->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Type de bien</label>
                        <select name="type_bien" class="filter-select" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;">
                            <option value="">Tous les types</option>
                            @foreach($typesBien as $key => $label)
                                <option value="{{ $key }}" {{ request('type_bien') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Contrat</label>
                        <select name="type_contrat" class="filter-select" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;">
                            <option value="">Tous les contrats</option>
                            <option value="vente" {{ request('type_contrat') == 'vente' ? 'selected' : '' }}>Vente</option>
                            <option value="location" {{ request('type_contrat') == 'location' ? 'selected' : '' }}>Location</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Prix min</label>
                        <input type="number" name="prix_min" value="{{ request('prix_min') }}" placeholder="Min" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;">
                    </div>

                    <div>
                        <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Prix max</label>
                        <input type="number" name="prix_max" value="{{ request('prix_max') }}" placeholder="Max" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;">
                    </div>

                    <div>
                        <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Tri</label>
                        <select name="sort" class="filter-select" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                            <option value="prix_asc" {{ request('sort') == 'prix_asc' ? 'selected' : '' }}>Prix croissant</option>
                            <option value="prix_desc" {{ request('sort') == 'prix_desc' ? 'selected' : '' }}>Prix décroissant</option>
                            <option value="surface_desc" {{ request('sort') == 'surface_desc' ? 'selected' : '' }}>Plus grandes surfaces</option>
                        </select>
                    </div>

                    <div style="display:flex;align-items:flex-end;gap:8px;">
                        <button type="submit" class="btn btn-rust" style="flex:1;justify-content:center;">
                            <i class="fa-solid fa-search"></i> Rechercher
                        </button>
                        @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'type_contrat', 'prix_min', 'prix_max', 'sort']))
                            <a href="{{ route('biens.index') }}" class="btn btn-ghost" style="flex-shrink:0;">
                                <i class="fa-solid fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <!-- Filtres actifs -->
        @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'type_contrat', 'prix_min', 'prix_max']))
            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:12px;padding-top:12px;border-top:1px solid var(--border);">
                <span style="font-size:12px;color:var(--muted);margin-right:8px;">Filtres actifs :</span>
                @if(request('search'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-search"></i> {{ request('search') }}
                        <a href="#" onclick="removeFilter('search')" style="color:inherit;text-decoration:none;margin-left:6px;">&times;</a>
                    </span>
                @endif
                @if(request('quartier'))
                    @php
                        $quartier = App\Models\Quartier::find(request('quartier'));
                    @endphp
                    <span class="filter-tag">
                        <i class="fa-solid fa-location-dot"></i> {{ $quartier ? $quartier->nom : '' }}
                        <a href="#" onclick="removeFilter('quartier')" style="color:inherit;text-decoration:none;margin-left:6px;">&times;</a>
                    </span>
                @endif
                @if(request('type_bien'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-home"></i> {{ $typesBien[request('type_bien')] ?? '' }}
                        <a href="#" onclick="removeFilter('type_bien')" style="color:inherit;text-decoration:none;margin-left:6px;">&times;</a>
                    </span>
                @endif
                @if(request('type_contrat'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-file-signature"></i> {{ request('type_contrat') == 'vente' ? 'Vente' : 'Location' }}
                        <a href="#" onclick="removeFilter('type_contrat')" style="color:inherit;text-decoration:none;margin-left:6px;">&times;</a>
                    </span>
                @endif
                @if(request('prix_min') || request('prix_max'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-money-bill"></i> 
                        {{ request('prix_min') ? number_format(request('prix_min'), 0, ',', ' ') : '0' }} - 
                        {{ request('prix_max') ? number_format(request('prix_max'), 0, ',', ' ') : '∞' }} F
                        <a href="#" onclick="removeFilter('prix_min');removeFilter('prix_max')" style="color:inherit;text-decoration:none;margin-left:6px;">&times;</a>
                    </span>
                @endif
            </div>
        @endif
    </div>

    <p class="results-count">{{ $biens->total() }} biens disponibles</p>

    <!-- Liste des biens -->
    <div class="biens-grid" id="biensContainer">
        @forelse($biens as $bien)
            <div class="bien-card">
                <div class="bien-image">
                    @if($bien->medias->first())
                        <img src="{{ asset('storage/' . $bien->medias->first()->fichier) }}" alt="{{ $bien->titre }}">
                    @else
                        <i class="fa-solid fa-image" style="font-size:32px;opacity:0.3;"></i>
                        <span style="position:absolute;bottom:8px;right:12px;font-size:11px;background:rgba(0,0,0,0.6);color:#fff;padding:2px 10px;border-radius:4px;">
                            Aucune image
                        </span>
                    @endif
                    @if($bien->medias->count() > 1)
                        <span style="position:absolute;bottom:8px;right:12px;font-size:11px;background:rgba(0,0,0,0.6);color:#fff;padding:2px 10px;border-radius:4px;">
                            +{{ $bien->medias->count() - 1 }} photos
                        </span>
                    @endif
                </div>
                <div class="bien-body">
                    <div class="bien-title">{{ $bien->titre }}</div>
                    <div class="bien-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                    <div style="font-size:13px;color:var(--muted);margin:4px 0;">
                        <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}
                    </div>
                    <div class="bien-infos">
                        <span><i class="fa-solid fa-vector-square"></i> {{ $bien->surface }} m²</span>
                        <span><i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                        <span><i class="fa-solid fa-bath"></i> {{ $bien->nombre_salles_bain }} sdb</span>
                        <span><i class="fa-solid fa-tag"></i> {{ $bien->type_contrat->label() }}</span>
                    </div>
                    <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                        <span class="meta-pill">{{ $bien->type_bien->label() }}</span>
                        @if($bien->parking_disponible)
                            <span class="meta-pill"><i class="fa-solid fa-car"></i> Parking</span>
                        @endif
                        @if($bien->est_meuble)
                            <span class="meta-pill"><i class="fa-solid fa-couch"></i> Meublé</span>
                        @endif
                    </div>
                    <div style="margin-top:14px;">
                        <a href="{{ route('biens.show', $bien) }}" class="btn btn-rust btn-sm btn-block">Voir le détail</a>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--muted);">
                <i class="fa-solid fa-building" style="font-size:40px;display:block;margin-bottom:16px;"></i>
                <p style="font-size:16px;">Aucun bien disponible pour le moment.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top:40px;">
        {{ $biens->appends(request()->query())->links() }}
    </div>
</div>

<script>
function removeFilter(name) {
    const url = new URL(window.location.href);
    url.searchParams.delete(name);
    window.location.href = url.toString();
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
                        <div class="autocomplete-item" style="padding:10px 16px;cursor:pointer;display:flex;align-items:center;gap:12px;border-bottom:1px solid var(--border);transition:background 0.2s;" 
                             onmouseover="this.style.background='#F7F9FC'" 
                             onmouseout="this.style.background='transparent'"
                             onclick="window.location.href='${item.url || '#'}'">
                            <i class="${item.icon}" style="color:var(--rust);width:20px;"></i>
                            <div style="flex:1;">
                                <div style="font-weight:600;font-size:14px;">${item.label}</div>
                                <div style="font-size:12px;color:var(--muted);">${item.description || ''}</div>
                            </div>
                            <span style="font-size:11px;text-transform:uppercase;color:var(--muted);background:var(--border);padding:2px 10px;border-radius:999px;">${item.type}</span>
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
</script>

<style>
    .biens-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
    }
    .bien-card {
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .bien-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.06);
    }
    .bien-image {
        height: 200px;
        background: #E8ECF0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        font-size: 14px;
        position: relative;
        overflow: hidden;
    }
    .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .bien-body {
        padding: 16px 20px 20px;
    }
    .bien-title {
        font-family: var(--display);
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 6px;
    }
    .bien-price {
        font-weight: 700;
        color: var(--rust);
        font-size: 16px;
    }
    .bien-infos {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 10px;
        font-size: 13px;
        color: var(--text-soft);
    }
    .bien-infos span {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .filter-tag {
        display: inline-flex;
        align-items: center;
        background: var(--rust-soft);
        color: var(--rust);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        gap: 6px;
    }
    .filter-tag a {
        color: var(--rust);
        text-decoration: none;
        font-weight: 700;
    }
    .filter-tag a:hover {
        color: #9A4523;
    }
    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        list-style: none;
        padding: 0;
    }
    .pagination li {
        display: inline;
    }
    .pagination a, .pagination span {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
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

    @media (max-width: 640px) {
        .search-filters {
            grid-template-columns: 1fr 1fr !important;
        }
    }
</style>
@endsection