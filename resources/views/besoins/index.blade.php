@extends('layouts.app')

@section('title', 'Parcourir les besoins — DoyaImmo')

@section('content')
<div class="wrap page-head">
    <span class="eyebrow">Besoins publiés</span>
    <h1 class="h-section" style="font-family:var(--display); font-weight:800; font-size:30px;">
        Découvrez ce que recherchent les clients à Dakar
    </h1>
</div>

<div class="wrap section" style="padding-top:20px;">
    <!-- Barre de recherche avancée -->
    <div class="search-container" style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:24px;">
        <form action="{{ route('besoins.index') }}" method="GET" id="searchForm">
            <div class="search-grid">
                <!-- Recherche principale -->
                <div class="search-main">
                    <div class="search-input-wrapper" style="position:relative;">
                        <i class="fa-solid fa-search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--muted);"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput" 
                               value="{{ request('search') }}" 
                               placeholder="Rechercher un besoin (quartier, type, description...)" 
                               style="width:100%;padding:12px 16px 12px 44px;border:1px solid var(--border);border-radius:12px;font-size:14px;font-family:inherit;"
                               autocomplete="off">
                        <div id="autocompleteResults" style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid var(--border);border-radius:12px;margin-top:4px;box-shadow:0 8px 32px rgba(0,0,0,0.1);z-index:1000;display:none;max-height:400px;overflow-y:auto;"></div>
                    </div>
                </div>

                <!-- Filtres -->
                <div class="search-filters" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:12px;margin-top:12px;">
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
                        <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Budget max</label>
                        <select name="budget" class="filter-select" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;">
                            <option value="">Tous les budgets</option>
                            <option value="0-100000" {{ request('budget') == '0-100000' ? 'selected' : '' }}>Moins de 100 000 F</option>
                            <option value="100000-300000" {{ request('budget') == '100000-300000' ? 'selected' : '' }}>100 000 – 300 000 F</option>
                            <option value="300000-500000" {{ request('budget') == '300000-500000' ? 'selected' : '' }}>300 000 – 500 000 F</option>
                            <option value="500000-1000000" {{ request('budget') == '500000-1000000' ? 'selected' : '' }}>500 000 – 1 000 000 F</option>
                            <option value="1000000+" {{ request('budget') == '1000000+' ? 'selected' : '' }}>Plus de 1 000 000 F</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Type d'opération</label>
                        <select name="type_operation" class="filter-select" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;">
                            <option value="">Tous</option>
                            <option value="achat" {{ request('type_operation') == 'achat' ? 'selected' : '' }}>Achat</option>
                            <option value="location" {{ request('type_operation') == 'location' ? 'selected' : '' }}>Location</option>
                        </select>
                    </div>

                    <div>
                        <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Tri</label>
                        <select name="sort" class="filter-select" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                            <option value="budget_asc" {{ request('sort') == 'budget_asc' ? 'selected' : '' }}>Budget croissant</option>
                            <option value="budget_desc" {{ request('sort') == 'budget_desc' ? 'selected' : '' }}>Budget décroissant</option>
                            <option value="propositions" {{ request('sort') == 'propositions' ? 'selected' : '' }}>Plus de propositions</option>
                        </select>
                    </div>

                    <div style="display:flex;align-items:flex-end;gap:8px;">
                        <button type="submit" class="btn btn-rust" style="flex:1;justify-content:center;">
                            <i class="fa-solid fa-search"></i> Rechercher
                        </button>
                        @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'budget', 'type_operation', 'sort']))
                            <a href="{{ route('besoins.index') }}" class="btn btn-ghost" style="flex-shrink:0;">
                                <i class="fa-solid fa-times"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>

        <!-- Filtres actifs -->
        @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'budget', 'type_operation']))
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
                        <a href="#" onclick="removeFilter('budget')" style="color:inherit;text-decoration:none;margin-left:6px;">&times;</a>
                    </span>
                @endif
                @if(request('type_operation'))
                    <span class="filter-tag">
                        <i class="fa-solid fa-handshake"></i> {{ request('type_operation') == 'achat' ? 'Achat' : 'Location' }}
                        <a href="#" onclick="removeFilter('type_operation')" style="color:inherit;text-decoration:none;margin-left:6px;">&times;</a>
                    </span>
                @endif
            </div>
        @endif
    </div>

    <p class="results-count">{{ $demandes->total() }} besoins actuellement publiés</p>

    <!-- Liste des demandes -->
    <div class="besoin-grid" id="besoinsContainer">
        @forelse($demandes as $demande)
            <div class="besoin-card">
                <div class="tier-bar tier-{{ $demande->statut->value === 'en_attente' ? 'prem' : 'std' }}"></div>
                <div class="besoin-body">
                    <div class="besoin-top">
                        <div class="besoin-type">{{ $demande->type_bien->label() }}</div>
                        <div class="besoin-budget">{{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois</div>
                    </div>
                    <div class="besoin-meta">
                        <span class="meta-pill"><i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}</span>
                        <span class="meta-pill"><i class="fa-solid fa-house"></i> {{ $demande->type_operation->label() }}</span>
                        <span class="meta-pill">{{ $demande->propositions->count() }} offres reçues</span>
                    </div>
                    <p class="besoin-desc">{{ Str::limit($demande->description, 100) }}</p>
                    <div class="besoin-foot">
                        <span class="posted">Publié {{ $demande->created_at->diffForHumans() }}</span>
                        <a href="{{ route('besoins.show', $demande) }}" class="btn btn-ghost btn-sm">Voir le détail</a>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--muted);">
                <i class="fa-solid fa-inbox" style="font-size:40px;display:block;margin-bottom:16px;"></i>
                <p style="font-size:16px;">Aucun besoin trouvé pour le moment.</p>
                <a href="{{ route('register.particulier') }}" class="btn btn-rust" style="margin-top:16px;">Publier un besoin</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top:40px;">
        {{ $demandes->appends(request()->query())->links() }}
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

    // Fermer l'autocomplétion en cliquant ailleurs
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-input-wrapper')) {
            resultsContainer.style.display = 'none';
        }
    });
});
</script>

<style>
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
    .autocomplete-item:hover {
        background: #F7F9FC;
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