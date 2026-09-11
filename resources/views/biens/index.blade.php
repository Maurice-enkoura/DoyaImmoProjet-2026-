@extends('layouts.app')

@section('title', 'Biens immobiliers à Dakar — DoyaImmo')
@section('meta_description', 'Découvrez les biens immobiliers disponibles à Dakar : appartements, maisons, terrains et autres logements proposés sur DoyaImmo.')
@section('canonical', 'https://doyaimmo.com/biens')
@section('og_title', 'Biens immobiliers à Dakar — DoyaImmo')
@section('og_description', 'Découvrez les biens immobiliers disponibles à Dakar.')
@section('robots', 'index, follow')

@section('content')
<div class="biens-page">

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <header class="biens-hero">
        <div class="biens-hero__inner">
            <span class="eyebrow">
                <i class="fa-solid fa-building"></i> Biens disponibles
            </span>
            <h1 class="biens-hero__title">
                Trouvez le bien qu'il vous faut <br>
                <span>parmi {{ $biens->total() }} annonces</span>
            </h1>
            <p class="biens-hero__sub">
                Appartements, villas, terrains, bureaux — partout à Dakar.
            </p>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════
         FILTRES STICKY
    ═══════════════════════════════════════════ --}}
    <div class="filters-bar" id="filtersBar">
        <div class="filters-bar__inner">

            {{-- Recherche --}}
            <div class="search-input-wrapper">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
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

            {{-- Filtres desktop --}}
            <form action="{{ route('biens.index') }}" method="GET" id="searchForm" class="filters-form">
                <select name="quartier" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tous les quartiers</option>
                    @foreach($quartiers as $quartier)
                        <option value="{{ $quartier->id }}" {{ request('quartier') == $quartier->id ? 'selected' : '' }}>
                            {{ $quartier->nom }}
                        </option>
                    @endforeach
                </select>

                <select name="type_bien" class="filter-select" onchange="this.form.submit()">
                    <option value="">Tous les types</option>
                    @foreach($typesBien as $key => $label)
                        <option value="{{ $key }}" {{ request('type_bien') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select name="type_contrat" class="filter-select" onchange="this.form.submit()">
                    <option value="">Vente & Location</option>
                    <option value="vente" {{ request('type_contrat') == 'vente' ? 'selected' : '' }}>Vente</option>
                    <option value="location" {{ request('type_contrat') == 'location' ? 'selected' : '' }}>Location</option>
                </select>

                <select name="sort" class="filter-select" onchange="this.form.submit()">
                    <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                    <option value="prix_asc" {{ request('sort') == 'prix_asc' ? 'selected' : '' }}>Prix croissant</option>
                    <option value="prix_desc" {{ request('sort') == 'prix_desc' ? 'selected' : '' }}>Prix décroissant</option>
                    <option value="surface_desc" {{ request('sort') == 'surface_desc' ? 'selected' : '' }}>Surface</option>
                </select>
            </form>

            {{-- Toggle vue --}}
            <div class="view-toggle">
                <button type="button" class="view-btn active" id="gridViewBtn" onclick="setView('grid')" aria-label="Vue grille">
                    <i class="fa-solid fa-table-cells-large"></i>
                </button>
                <button type="button" class="view-btn" id="listViewBtn" onclick="setView('list')" aria-label="Vue liste">
                    <i class="fa-solid fa-list"></i>
                </button>
            </div>

            {{-- Bouton filtres mobile --}}
            <button type="button" class="mobile-filters-btn" onclick="toggleMobileFilters()">
                <i class="fa-solid fa-sliders"></i>
                Filtres
                @php
                    $activeCount = 0;
                    if(request('quartier')) $activeCount++;
                    if(request('type_bien')) $activeCount++;
                    if(request('type_contrat')) $activeCount++;
                    if(request('prix_min') || request('prix_max')) $activeCount++;
                @endphp
                @if($activeCount > 0)
                    <span class="mobile-filters-btn__count">{{ $activeCount }}</span>
                @endif
            </button>
        </div>

        {{-- Filtres actifs --}}
        @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'type_contrat', 'prix_min', 'prix_max']))
            <div class="active-filters">
                @if(request('search'))
                    <span class="tag">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        {{ request('search') }}
                        <button type="button" onclick="removeFilter('search')" aria-label="Retirer">&times;</button>
                    </span>
                @endif
                @if(request('quartier'))
                    @php $q = App\Models\Quartier::find(request('quartier')); @endphp
                    <span class="tag">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $q->nom ?? '' }}
                        <button type="button" onclick="removeFilter('quartier')" aria-label="Retirer">&times;</button>
                    </span>
                @endif
                @if(request('type_bien'))
                    <span class="tag">
                        <i class="fa-solid fa-house"></i>
                        {{ $typesBien[request('type_bien')] ?? '' }}
                        <button type="button" onclick="removeFilter('type_bien')" aria-label="Retirer">&times;</button>
                    </span>
                @endif
                @if(request('type_contrat'))
                    <span class="tag">
                        <i class="fa-solid fa-file-signature"></i>
                        {{ request('type_contrat') === 'vente' ? 'Vente' : 'Location' }}
                        <button type="button" onclick="removeFilter('type_contrat')" aria-label="Retirer">&times;</button>
                    </span>
                @endif
                @if(request('prix_min') || request('prix_max'))
                    <span class="tag">
                        <i class="fa-solid fa-money-bill"></i>
                        {{ request('prix_min') ? number_format(request('prix_min'), 0, ',', ' ') : '0' }}
                        –
                        {{ request('prix_max') ? number_format(request('prix_max'), 0, ',', ' ') : '∞' }} F
                        <button type="button" onclick="removeFilter('prix_min');removeFilter('prix_max')" aria-label="Retirer">&times;</button>
                    </span>
                @endif
                <a href="{{ route('biens.index') }}" class="tag tag--reset">
                    <i class="fa-solid fa-rotate"></i> Réinitialiser
                </a>
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════
         RÉSULTATS
    ═══════════════════════════════════════════ --}}
    <div class="biens-wrapper">

        <div class="results-bar">
            <span class="results-bar__count">
                <strong>{{ $biens->total() }}</strong>
                bien{{ $biens->total() > 1 ? 's' : '' }} trouvé{{ $biens->total() > 1 ? 's' : '' }}
            </span>
            <span class="results-bar__page">
                Page {{ $biens->currentPage() }} / {{ $biens->lastPage() }}
            </span>
        </div>

        @if($biens->count() > 0)
            <div class="biens-grid" id="biensContainer">
                @foreach($biens as $bien)
                    @php
                        $image = $bien->medias->where('type_media', 'image')->first();
                        $isVedette = $bien->est_vedette && $bien->vedette_fin > now();
                        $isNew = $bien->created_at->gt(now()->subHours(48));
                        $imagesCount = $bien->medias->where('type_media', 'image')->count();
                    @endphp

                    <article class="bien-card {{ $isVedette ? 'is-vedette' : '' }}">

                        {{-- Image --}}
                        <a href="{{ route('biens.show', $bien->slug) }}" class="bien-card__media">
                            @if($image)
                                <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $bien->titre }}" loading="lazy">
                            @else
                                <div class="bien-card__placeholder">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            @endif

                            {{-- Badge vedette OU nouveau (priorité vedette) --}}
                            @if($isVedette)
                                <span class="badge badge--vedette">
                                    <i class="fa-solid fa-star"></i> Vedette
                                </span>
                            @elseif($isNew)
                                <span class="badge badge--new">
                                    <i class="fa-solid fa-bolt"></i> Nouveau
                                </span>
                            @endif

                            {{-- Statut (toujours en haut à droite) --}}
                            <span class="badge badge--status {{ $bien->statut ? 'is-dispo' : 'is-indispo' }}">
                                {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                            </span>

                            {{-- Overlay hover --}}
                            <div class="bien-card__overlay">
                                <span class="bien-card__cta">
                                    <i class="fa-solid fa-eye"></i> Voir le bien
                                </span>
                            </div>

                            {{-- Compteur photos --}}
                            @if($imagesCount > 1)
                                <span class="bien-card__photos">
                                    <i class="fa-regular fa-images"></i> {{ $imagesCount }}
                                </span>
                            @endif
                        </a>

                        {{-- Corps --}}
                        <div class="bien-card__body">

                            <div class="bien-card__headline">
                                <h3 class="bien-card__title">
                                    <a href="{{ route('biens.show', $bien->slug) }}">
                                        {{ $bien->titre }}
                                    </a>
                                </h3>
                            </div>

                            <div class="bien-card__price">
                                {{ number_format($bien->prix, 0, ',', ' ') }}
                                <small>FCFA</small>
                            </div>

                            <div class="bien-card__location">
                                <i class="fa-solid fa-location-dot"></i>
                                {{ $bien->quartier->nom ?? $bien->quartier }}
                            </div>

                            <div class="bien-card__specs">
                                <span>
                                    <i class="fa-regular fa-square"></i>
                                    {{ $bien->surface }} m²
                                </span>
                                @if($bien->nombre_chambres)
                                    <span>
                                        <i class="fa-solid fa-bed"></i>
                                        {{ $bien->nombre_chambres }} ch.
                                    </span>
                                @endif
                                @if($bien->nombre_salles_bain)
                                    <span>
                                        <i class="fa-solid fa-bath"></i>
                                        {{ $bien->nombre_salles_bain }} sdb
                                    </span>
                                @endif
                            </div>

                            <div class="bien-card__footer">
                                <span class="bien-card__date">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $bien->created_at->diffForHumans() }}
                                </span>
                                <a href="{{ route('biens.show', $bien->slug) }}" class="bien-card__link">
                                    Voir <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- PAGINATION --}}
            @if($biens->hasPages())
                <div class="pagination-wrapper" id="paginationWrapper"
                     data-current-page="{{ $biens->currentPage() }}"
                     data-last-page="{{ $biens->lastPage() }}"
                     data-base-url="{{ $biens->url(1) }}"
                     data-total="{{ $biens->total() }}"
                     data-query="{{ http_build_query(request()->except('page')) }}">
                </div>
            @endif

        @else
            <div class="empty-state">
                <div class="empty-state__icon">
                    <i class="fa-regular fa-building"></i>
                </div>
                <h3>Aucun bien trouvé</h3>
                <p>
                    @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'type_contrat', 'prix_min', 'prix_max']))
                        Essayez de modifier vos filtres pour voir plus de résultats.
                    @else
                        Les biens publiés par les agences apparaîtront ici.
                    @endif
                </p>
                @if(request()->anyFilled(['search', 'quartier', 'type_bien', 'type_contrat', 'prix_min', 'prix_max']))
                    <a href="{{ route('biens.index') }}" class="btn btn-ghost">
                        <i class="fa-solid fa-rotate"></i> Voir tous les biens
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- CTA FINAL --}}
    @if($biens->count() > 0)
        <section class="final-cta">
            <div class="final-cta__inner">
                <div class="final-cta__icon">
                    <i class="fa-solid fa-bell"></i>
                </div>
                <div class="final-cta__content">
                    <h2>Vous ne trouvez pas votre bonheur ?</h2>
                    <p>Publiez votre recherche gratuitement et recevez des propositions des agences de Dakar.</p>
                </div>
                <div class="final-cta__actions">
                    <a href="{{ route('register.particulier') }}" class="btn btn-rust">
                        <i class="fa-solid fa-plus"></i> Publier ma recherche
                    </a>
                    <a href="{{ route('besoins.index') }}" class="btn btn-ghost">
                        <i class="fa-solid fa-eye"></i> Voir les besoins
                    </a>
                </div>
            </div>
        </section>
    @endif
</div>
@endsection


@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════
   VIEW TOGGLE
═══════════════════════════════════════════════════════════ */
function setView(view) {
    const container = document.getElementById('biensContainer');
    if (!container) return;
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');

    if (view === 'list') {
        container.classList.add('list-view');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
        localStorage.setItem('biensView', 'list');
    } else {
        container.classList.remove('list-view');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        localStorage.setItem('biensView', 'grid');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    setView(localStorage.getItem('biensView') === 'list' ? 'list' : 'grid');
});

/* ═══════════════════════════════════════════════════════════
   FILTRES
═══════════════════════════════════════════════════════════ */
function removeFilter(name) {
    const url = new URL(window.location.href);
    url.searchParams.delete(name);
    window.location.href = url.toString();
}

function toggleMobileFilters() {
    document.getElementById('filtersBar').classList.toggle('is-open-mobile');
}

/* ═══════════════════════════════════════════════════════════
   AUTOCOMPLETE
═══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInput');
    const results = document.getElementById('autocompleteResults');
    if (!input || !results) return;

    let debounceTimer;

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            results.style.display = 'none';
            return;
        }

        debounceTimer = setTimeout(() => {
            fetch(`/api/search/autocomplete?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.length) {
                        results.style.display = 'none';
                        return;
                    }
                    results.innerHTML = data.map(item => `
                        <div class="autocomplete-item" onclick="window.location.href='${item.url || '#'}'">
                            <i class="${item.icon || 'fa-solid fa-circle'} item-icon"></i>
                            <div class="item-content">
                                <div class="item-title">${item.label}</div>
                                <div class="item-desc">${item.description || ''}</div>
                            </div>
                            <span class="item-type">${item.type || ''}</span>
                        </div>
                    `).join('');
                    results.style.display = 'block';
                })
                .catch(() => results.style.display = 'none');
        }, 300);
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.search-input-wrapper')) {
            results.style.display = 'none';
        }
    });
});

/* ═══════════════════════════════════════════════════════════
   PAGINATION JS
═══════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('paginationWrapper');
    if (!wrapper) return;

    const currentPage = parseInt(wrapper.dataset.currentPage, 10);
    const lastPage    = parseInt(wrapper.dataset.lastPage, 10);
    const baseUrl     = wrapper.dataset.baseUrl;
    const queryString = wrapper.dataset.query;
    const total       = parseInt(wrapper.dataset.total, 10) || 0;

    if (lastPage <= 1) return;

    const buildUrl = (page) => {
        const url = new URL(baseUrl, window.location.origin);
        url.searchParams.set('page', page);
        if (queryString) {
            const params = new URLSearchParams(queryString);
            params.forEach((v, k) => { if (k !== 'page') url.searchParams.set(k, v); });
        }
        return url.toString();
    };

    const pages = [];
    const maxVisible = 5;
    if (lastPage <= maxVisible + 2) {
        for (let i = 1; i <= lastPage; i++) pages.push(i);
    } else {
        pages.push(1);
        if (currentPage > 3) pages.push('...');
        const start = Math.max(2, currentPage - 1);
        const end   = Math.min(lastPage - 1, currentPage + 1);
        for (let i = start; i <= end; i++) pages.push(i);
        if (currentPage < lastPage - 2) pages.push('...');
        pages.push(lastPage);
    }

    let html = '<nav class="pagination" aria-label="Pagination">';
    html += '<ul class="pagination-list">';

    if (currentPage > 1) {
        html += `<li><a href="${buildUrl(currentPage - 1)}" rel="prev" class="pagination-link">
                    <i class="fa-solid fa-chevron-left"></i><span class="pagination-label">Précédent</span>
                 </a></li>`;
    } else {
        html += `<li class="is-disabled"><span class="pagination-link">
                    <i class="fa-solid fa-chevron-left"></i><span class="pagination-label">Précédent</span>
                 </span></li>`;
    }

    pages.forEach(p => {
        if (p === '...') {
            html += `<li class="is-disabled"><span class="pagination-link pagination-link--dots">…</span></li>`;
        } else if (p === currentPage) {
            html += `<li class="is-active"><span class="pagination-link">${p}</span></li>`;
        } else {
            html += `<li><a href="${buildUrl(p)}" class="pagination-link">${p}</a></li>`;
        }
    });

    if (currentPage < lastPage) {
        html += `<li><a href="${buildUrl(currentPage + 1)}" rel="next" class="pagination-link">
                    <span class="pagination-label">Suivant</span><i class="fa-solid fa-chevron-right"></i>
                 </a></li>`;
    } else {
        html += `<li class="is-disabled"><span class="pagination-link">
                    <span class="pagination-label">Suivant</span><i class="fa-solid fa-chevron-right"></i>
                 </span></li>`;
    }

    html += '</ul>';

    const firstItem = (currentPage - 1) * 12 + 1;
    const lastItem  = Math.min(currentPage * 12, total);
    if (total) {
        html += `<div class="pagination-info">
                    <strong>${firstItem}</strong> – <strong>${lastItem}</strong> sur <strong>${total}</strong>
                 </div>`;
    }

    html += '</nav>';
    wrapper.innerHTML = html;
});
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE BIENS
   ═══════════════════════════════════════════════════════════ */

.biens-page {
    --c-success:    #1E7A47;
    --c-warning:    #E65100;
    --c-danger:     #C62828;
    --c-info:       #0D47A1;
    --c-gold:       #D4AF37;
    --c-teal:       #0E7A7A;
    --surface:      #F7F9FC;
    --radius:       14px;
    --container-max: 1440px;
    --container-pad: clamp(20px, 3vw, 48px);
}

/* ═══ HERO ═══ */
.biens-hero {
    background: linear-gradient(135deg, #FFFBF7 0%, #fff 55%);
    padding: 44px var(--container-pad) 36px;
    border-bottom: 1px solid var(--border);
    position: relative;
    overflow: hidden;
}
.biens-hero::before {
    content: '';
    position: absolute;
    top: -50%; right: -15%;
    width: 480px; height: 480px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(181, 80, 42, .08), transparent 65%);
    pointer-events: none;
}
.biens-hero__inner {
    max-width: var(--container-max);
    margin: 0 auto;
    position: relative;
}
.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--rust);
    text-transform: uppercase;
    letter-spacing: .6px;
    background: rgba(181, 80, 42, .1);
    padding: 5px 12px;
    border-radius: 999px;
    margin-bottom: 14px;
}
.eyebrow i { font-size: 10px; }

.biens-hero__title {
    font-family: var(--display);
    font-size: clamp(24px, 3.6vw, 38px);
    font-weight: 800;
    line-height: 1.15;
    color: var(--ink);
    margin: 0 0 10px;
    letter-spacing: -.02em;
    max-width: 700px;
}
.biens-hero__title span { color: var(--rust); }
.biens-hero__sub {
    font-size: 14.5px;
    color: var(--text-soft);
    margin: 0;
    max-width: 540px;
}

/* ═══ FILTRES STICKY ═══ */
.filters-bar {
    position: sticky;
    top: 0;
    z-index: 90;
    background: rgba(255, 255, 255, .96);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
    padding: 12px var(--container-pad);
    margin-bottom: 20px;
}
.filters-bar__inner {
    max-width: var(--container-max);
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.search-input-wrapper {
    position: relative;
    flex: 1;
    min-width: 200px;
    max-width: 320px;
}
.search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--muted);
    font-size: 14px;
    pointer-events: none;
}
.search-input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 13.5px;
    font-family: inherit;
    background: #fff;
    transition: all .2s;
    -webkit-appearance: none;
    appearance: none;
}
.search-input:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 4px rgba(181, 80, 42, .08);
}
.search-input::placeholder { color: var(--muted); }

.filters-form {
    display: flex;
    gap: 8px;
    flex: 1;
    flex-wrap: wrap;
}
.filter-select {
    padding: 10px 34px 10px 14px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    font-family: inherit;
    color: var(--ink);
    background: #fff;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    -webkit-appearance: none;
    appearance: none;
    cursor: pointer;
    transition: border-color .2s;
    min-width: 140px;
    flex: 1;
}
.filter-select:hover { border-color: #d8d8d8; }
.filter-select:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 4px rgba(181, 80, 42, .08);
}

.view-toggle {
    display: flex;
    gap: 2px;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 10px;
    padding: 3px;
}
.view-btn {
    width: 34px; height: 34px;
    display: flex; align-items: center; justify-content: center;
    border: none; background: transparent;
    border-radius: 7px;
    color: var(--muted);
    cursor: pointer;
    transition: all .2s;
    font-size: 13px;
}
.view-btn:hover { background: var(--border); color: var(--ink); }
.view-btn.active {
    background: var(--rust);
    color: #fff;
    box-shadow: 0 2px 6px rgba(181, 80, 42, .25);
}

.mobile-filters-btn {
    display: none;
    padding: 10px 16px;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    cursor: pointer;
    font-family: inherit;
    align-items: center;
    gap: 7px;
    transition: all .2s;
}
.mobile-filters-btn:hover { border-color: var(--rust); color: var(--rust); }
.mobile-filters-btn__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    background: var(--rust);
    color: #fff;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 800;
}

.active-filters {
    max-width: var(--container-max);
    margin: 10px auto 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}
.tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px 4px 12px;
    background: rgba(181, 80, 42, .1);
    color: var(--rust);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}
.tag i { font-size: 10px; }
.tag button {
    background: rgba(181, 80, 42, .15);
    border: none;
    width: 16px; height: 16px;
    border-radius: 50%;
    color: var(--rust);
    font-size: 12px;
    line-height: 1;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .15s;
}
.tag button:hover { background: rgba(181, 80, 42, .3); }
.tag--reset {
    background: transparent;
    border: 1.5px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    padding: 4px 12px;
    transition: all .2s;
}
.tag--reset:hover { border-color: var(--rust); color: var(--rust); }

.autocomplete-results {
    position: absolute;
    top: calc(100% + 6px);
    left: 0; right: 0;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, .1);
    z-index: 1000;
    display: none;
    max-height: 320px;
    overflow-y: auto;
    padding: 6px;
}
.autocomplete-item {
    padding: 10px 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: 8px;
    transition: background .15s;
}
.autocomplete-item:hover { background: var(--surface); }
.autocomplete-item .item-icon { color: var(--rust); width: 18px; font-size: 13px; flex-shrink: 0; }
.autocomplete-item .item-content { flex: 1; min-width: 0; }
.autocomplete-item .item-title { font-weight: 600; font-size: 13px; color: var(--ink); }
.autocomplete-item .item-desc { font-size: 11.5px; color: var(--muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.autocomplete-item .item-type {
    font-size: 9.5px;
    text-transform: uppercase;
    color: var(--muted);
    background: var(--border);
    padding: 2px 8px;
    border-radius: 999px;
    flex-shrink: 0;
    font-weight: 700;
    letter-spacing: .3px;
}

/* ═══ WRAPPER ═══ */
.biens-wrapper {
    max-width: var(--container-max);
    margin: 0 auto;
    padding: 0 var(--container-pad);
}

.results-bar {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 16px;
    gap: 12px;
    flex-wrap: wrap;
}
.results-bar__count {
    font-size: 14px;
    color: var(--text-soft);
}
.results-bar__count strong {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 800;
    color: var(--ink);
}
.results-bar__page {
    font-size: 12.5px;
    color: var(--muted);
}

/* ═══ GRILLE ═══ */
.biens-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr));
    gap: 20px;
}

/* ═══ CARTE ═══ */
.bien-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .25s ease, box-shadow .25s ease, border-color .25s;
}
.bien-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 32px rgba(0, 0, 0, .08);
    border-color: #d8d8d8;
}
.bien-card.is-vedette {
    border-color: var(--c-gold);
    box-shadow: 0 0 0 1px rgba(212, 175, 55, .15);
}

.bien-card__media {
    position: relative;
    aspect-ratio: 4 / 3;
    background: var(--surface);
    overflow: hidden;
    display: block;
    text-decoration: none;
}
.bien-card__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .4s ease;
}
.bien-card:hover .bien-card__media img { transform: scale(1.06); }

.bien-card__placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted);
    font-size: 40px;
    opacity: .25;
}

.badge {
    position: absolute;
    z-index: 3;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
    backdrop-filter: blur(6px);
}
.badge i { font-size: 9px; }

.badge--vedette {
    top: 12px; left: 12px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
}
.badge--new {
    top: 12px; left: 12px;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
}
.badge--status {
    top: 12px; right: 12px;
    background: rgba(255, 255, 255, .95);
    color: var(--c-success);
}
.badge--status.is-indispo { color: var(--muted); }

.bien-card__photos {
    position: absolute;
    bottom: 12px; left: 12px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: rgba(0, 0, 0, .7);
    color: #fff;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    z-index: 3;
    backdrop-filter: blur(4px);
}

.bien-card__overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(181, 80, 42, .7), rgba(140, 60, 30, .5));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity .3s ease;
    z-index: 2;
}
.bien-card:hover .bien-card__overlay { opacity: 1; }

.bien-card__cta {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 11px 22px;
    background: #fff;
    color: var(--rust);
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
    transform: translateY(8px);
    transition: transform .3s ease;
}
.bien-card:hover .bien-card__cta { transform: translateY(0); }
.bien-card__cta i { font-size: 12px; }

.bien-card__body {
    padding: 14px 16px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.bien-card__headline { min-width: 0; }
.bien-card__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.bien-card__title a {
    color: var(--ink);
    text-decoration: none;
    transition: color .2s;
}
.bien-card__title a:hover { color: var(--rust); }

.bien-card__price {
    font-family: var(--display);
    font-size: 19px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
}
.bien-card__price small {
    font-size: 11.5px;
    font-weight: 700;
    opacity: .75;
    margin-left: 3px;
}

.bien-card__location {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--muted);
}
.bien-card__location i {
    color: var(--rust);
    font-size: 11px;
    opacity: .8;
}

.bien-card__specs {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 14px;
    padding-top: 10px;
    border-top: 1px dashed var(--border);
    font-size: 12px;
    color: var(--text-soft);
}
.bien-card__specs span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.bien-card__specs i {
    color: var(--rust);
    font-size: 10px;
    opacity: .8;
}

.bien-card__footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-top: auto;
    padding-top: 10px;
}
.bien-card__date {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    color: var(--muted);
}
.bien-card__date i { font-size: 10px; }

.bien-card__link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 700;
    color: var(--rust);
    text-decoration: none;
    transition: gap .2s;
}
.bien-card__link:hover { gap: 9px; }
.bien-card__link i { font-size: 10px; }

/* ═══ VUE LISTE ═══ */
.biens-grid.list-view {
    grid-template-columns: 1fr;
    gap: 12px;
}
.biens-grid.list-view .bien-card {
    flex-direction: row;
}
.biens-grid.list-view .bien-card__media {
    width: 260px;
    min-width: 260px;
    aspect-ratio: 1;
}
.biens-grid.list-view .bien-card__body {
    padding: 16px 20px;
}
.biens-grid.list-view .bien-card__title { font-size: 17px; white-space: normal; }
.biens-grid.list-view .bien-card__price { font-size: 21px; }
.biens-grid.list-view .bien-card__footer {
    padding-top: 12px;
    border-top: 1px dashed var(--border);
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper {
    margin-top: 32px;
    display: flex;
    justify-content: center;
    width: 100%;
}

.pagination {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 14px;
    width: 100%;
    max-width: 720px;
}
.pagination-list {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
    padding: 6px;
    margin: 0;
    flex-wrap: wrap;
    justify-content: center;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
}
.pagination-list li { display: inline-flex; }

.pagination-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    min-width: 38px;
    height: 38px;
    padding: 0 12px;
    border-radius: 9px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    text-decoration: none;
    background: transparent;
    border: none;
    font-family: inherit;
    transition: all .15s ease;
    white-space: nowrap;
}
.pagination-link:hover {
    background: var(--surface);
    color: var(--rust);
}
li.is-active .pagination-link {
    background: var(--rust);
    color: #fff;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    font-weight: 700;
}
li.is-disabled .pagination-link {
    opacity: .4;
    cursor: not-allowed;
    color: var(--muted);
}
.pagination-link--dots {
    pointer-events: none;
    color: var(--muted);
    min-width: 32px;
}
.pagination-link i { font-size: 11px; }

.pagination-info {
    font-size: 12.5px;
    color: var(--muted);
    text-align: center;
}
.pagination-info strong {
    color: var(--ink);
    font-weight: 700;
    font-family: var(--display);
}

/* ═══ EMPTY STATE ═══ */
.empty-state {
    text-align: center;
    padding: 70px 24px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 76px;
    height: 76px;
    margin: 0 auto 18px;
    border-radius: 50%;
    background: rgba(181, 80, 42, .1);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--ink);
}
.empty-state p {
    font-size: 13.5px;
    color: var(--muted);
    margin: 0 auto 18px;
    max-width: 400px;
}

/* ═══ CTA FINAL ═══ */
.final-cta {
    max-width: var(--container-max);
    margin: 48px auto 20px;
    padding: 0 var(--container-pad);
}
.final-cta__inner {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 28px 32px;
    background: linear-gradient(135deg, #F5E6DF 0%, #FFFBF7 100%);
    border: 1px solid rgba(181, 80, 42, .2);
    border-radius: 20px;
    flex-wrap: wrap;
}
.final-cta__icon {
    width: 56px; height: 56px;
    border-radius: 16px;
    background: #fff;
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
    box-shadow: 0 6px 18px rgba(181, 80, 42, .15);
}
.final-cta__content {
    flex: 1;
    min-width: 240px;
}
.final-cta__content h2 {
    font-family: var(--display);
    font-size: 19px;
    font-weight: 800;
    margin: 0 0 4px;
    color: var(--ink);
    letter-spacing: -.02em;
}
.final-cta__content p {
    font-size: 13.5px;
    color: var(--text-soft);
    margin: 0;
    line-height: 1.5;
}
.final-cta__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    flex-shrink: 0;
}

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 13px;
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
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
}
.btn-ghost {
    background: transparent;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: #fff;
    border-color: var(--rust);
    color: var(--rust);
}

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .filters-form { flex: 1 0 100%; order: 3; }
    .search-input-wrapper { max-width: none; flex: 1; }
}

@media (max-width: 768px) {
    .biens-page { --container-pad: 16px; }

    .biens-hero { padding: 32px 16px 24px; }
    .biens-hero__title { font-size: 22px; }
    .biens-hero__sub { font-size: 13.5px; }

    .filters-bar {
        padding: 10px 16px;
        top: 0;
    }
    .filters-bar__inner { gap: 8px; }

    .filters-form { display: none; }
    .filters-bar.is-open-mobile .filters-form {
        display: flex;
        flex: 1 0 100%;
        flex-direction: column;
    }
    .filter-select { width: 100%; }

    .search-input-wrapper { max-width: none; flex: 1; min-width: 0; }
    .search-input { font-size: 14px; }

    .mobile-filters-btn { display: inline-flex; }
    .view-toggle { display: none; }

    .biens-grid:not(.list-view) {
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .biens-grid.list-view .bien-card__media {
        width: 140px;
        min-width: 140px;
    }
    .biens-grid.list-view .bien-card__title { font-size: 14px; }
    .biens-grid.list-view .bien-card__price { font-size: 16px; }
    .biens-grid.list-view .bien-card__specs { display: none; }

    .final-cta__inner { padding: 22px 20px; flex-direction: column; text-align: center; }
    .final-cta__icon { margin: 0 auto; }
    .final-cta__actions { width: 100%; flex-direction: column; }
    .final-cta__actions .btn { width: 100%; }

    .pagination-list { padding: 5px; gap: 3px; }
    .pagination-link {
        min-width: 34px;
        height: 34px;
        padding: 0 9px;
        font-size: 12px;
    }
    .pagination-label { display: none; }
}

@media (max-width: 480px) {
    .biens-page { --container-pad: 14px; }

    .biens-hero { padding: 26px 14px 20px; }
    .biens-hero__title { font-size: 20px; }

    .tag { font-size: 11px; padding: 3px 8px 3px 10px; }

    .bien-card__title { font-size: 14px; }
    .bien-card__price { font-size: 17px; }

    .pagination-list { padding: 4px; gap: 2px; }
    .pagination-link {
        min-width: 30px;
        height: 30px;
        padding: 0 7px;
        font-size: 11.5px;
        border-radius: 8px;
    }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
}
</style>
@endpush