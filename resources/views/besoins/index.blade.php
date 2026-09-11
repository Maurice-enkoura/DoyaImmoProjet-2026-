@extends('layouts.app')

@section('title', 'Besoins immobiliers à Dakar — DoyaImmo')
@section('meta_description', 'Consultez les besoins immobiliers des particuliers à Dakar : recherche d\'appartement, maison, terrain ou local commercial. Publiez votre besoin gratuitement.')
@section('canonical', 'https://doyaimmo.com/besoins')
@section('og_title', 'Besoins immobiliers à Dakar — DoyaImmo')
@section('og_description', 'Consultez les besoins immobiliers des particuliers à Dakar. Les agences reçoivent chaque jour des demandes qualifiées.')
@section('robots', 'index, follow')

@section('content')
<div class="besoins-page">

    @php
        $nbBesoins = $demandes->total();
        $nbAgences = \App\Models\Agence::where('statut_validation', true)->count();
        $nbOffres  = \App\Models\Proposition::count();
        $hasFilters = request()->anyFilled(['search', 'quartier', 'type_bien', 'budget', 'type_operation', 'sort']);
    @endphp

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <section class="hero">
        <div class="hero__content">
            <span class="hero__eyebrow">
                <i class="fa-solid fa-bullseye"></i>
                Besoins publiés
            </span>

            <h1 class="hero__title">
                Des clients cherchent <br>
                un bien à Dakar maintenant
            </h1>

            <p class="hero__sub">
                Parcourez les demandes publiées par les particuliers et proposez vos biens en quelques clics.
            </p>

            <div class="hero__stats">
                <div class="hero__stat">
                    <span class="hero__stat-value">{{ $nbBesoins }}</span>
                    <span class="hero__stat-label">Besoins actifs</span>
                </div>
                <span class="hero__stat-sep"></span>
                <div class="hero__stat">
                    <span class="hero__stat-value">{{ $nbAgences }}</span>
                    <span class="hero__stat-label">Agences inscrites</span>
                </div>
                <span class="hero__stat-sep"></span>
                <div class="hero__stat">
                    <span class="hero__stat-value">{{ $nbOffres }}</span>
                    <span class="hero__stat-label">Offres envoyées</span>
                </div>
            </div>

            <div class="hero__actions">
                <a href="#besoins-list" class="btn btn-rust">
                    <i class="fa-solid fa-magnifying-glass"></i> Voir les besoins
                </a>
                @guest
                    <a href="{{ route('register.agence') }}" class="btn btn-ghost">
                        <i class="fa-solid fa-briefcase"></i> Je suis une agence
                    </a>
                @endguest
                @auth
                    @if(auth()->user()->isAgence())
                        <a href="{{ route('agence.dashboard') }}" class="btn btn-ghost">
                            <i class="fa-solid fa-gauge"></i> Mon tableau de bord
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         RECHERCHE
    ═══════════════════════════════════════════ --}}
    <section class="search-section">
        <form action="{{ route('besoins.index') }}" method="GET" id="searchForm">
            <div class="search-bar">
                <div class="search-bar__input">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text"
                           name="search"
                           id="searchInput"
                           value="{{ request('search') }}"
                           placeholder="Rechercher un besoin (quartier, type...)"
                           autocomplete="off">
                    <div id="autocompleteResults" class="autocomplete"></div>
                </div>

                <button type="button" class="btn-filters" onclick="toggleFilters()">
                    <i class="fa-solid fa-sliders"></i>
                    <span>Filtres</span>
                    @php
                        $count = 0;
                        if(request('quartier')) $count++;
                        if(request('type_bien')) $count++;
                        if(request('budget')) $count++;
                        if(request('type_operation')) $count++;
                    @endphp
                    @if($count > 0)
                        <span class="btn-filters__count">{{ $count }}</span>
                    @endif
                </button>
            </div>

            <div class="filters" id="searchFilters">
                <div class="filters__grid">
                    <div class="filters__field">
                        <label>Quartier</label>
                        <select name="quartier">
                            <option value="">Tous les quartiers</option>
                            @foreach($quartiers as $quartier)
                                <option value="{{ $quartier->id }}" {{ request('quartier') == $quartier->id ? 'selected' : '' }}>
                                    {{ $quartier->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filters__field">
                        <label>Type de bien</label>
                        <select name="type_bien">
                            <option value="">Tous les types</option>
                            @foreach($typesBien as $key => $label)
                                <option value="{{ $key }}" {{ request('type_bien') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filters__field">
                        <label>Budget max</label>
                        <select name="budget">
                            <option value="">Tous les budgets</option>
                            <option value="0-100000" {{ request('budget') == '0-100000' ? 'selected' : '' }}>Moins de 100 000 F</option>
                            <option value="100000-300000" {{ request('budget') == '100000-300000' ? 'selected' : '' }}>100 000 – 300 000 F</option>
                            <option value="300000-500000" {{ request('budget') == '300000-500000' ? 'selected' : '' }}>300 000 – 500 000 F</option>
                            <option value="500000-1000000" {{ request('budget') == '500000-1000000' ? 'selected' : '' }}>500 000 – 1 000 000 F</option>
                            <option value="1000000+" {{ request('budget') == '1000000+' ? 'selected' : '' }}>Plus de 1 000 000 F</option>
                        </select>
                    </div>

                    <div class="filters__field">
                        <label>Opération</label>
                        <select name="type_operation">
                            <option value="">Toutes</option>
                            <option value="achat" {{ request('type_operation') == 'achat' ? 'selected' : '' }}>Achat</option>
                            <option value="location" {{ request('type_operation') == 'location' ? 'selected' : '' }}>Location</option>
                        </select>
                    </div>

                    <div class="filters__field">
                        <label>Trier par</label>
                        <select name="sort">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récents</option>
                            <option value="budget_asc" {{ request('sort') == 'budget_asc' ? 'selected' : '' }}>Budget croissant</option>
                            <option value="budget_desc" {{ request('sort') == 'budget_desc' ? 'selected' : '' }}>Budget décroissant</option>
                            <option value="propositions" {{ request('sort') == 'propositions' ? 'selected' : '' }}>Moins d'offres</option>
                        </select>
                    </div>
                </div>

                <div class="filters__actions">
                    <button type="submit" class="btn btn-rust">
                        <i class="fa-solid fa-magnifying-glass"></i> Rechercher
                    </button>
                    @if($hasFilters)
                        <a href="{{ route('besoins.index') }}" class="btn btn-ghost">
                            <i class="fa-solid fa-rotate"></i> Réinitialiser
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($hasFilters)
            <div class="active-filters">
                <span class="active-filters__label">Filtres actifs :</span>

                @if(request('search'))
                    <span class="active-tag">
                        <i class="fa-solid fa-magnifying-glass"></i> {{ request('search') }}
                        <a href="#" onclick="event.preventDefault();removeFilter('search')">&times;</a>
                    </span>
                @endif

                @if(request('quartier'))
                    @php $quartier = App\Models\Quartier::find(request('quartier')); @endphp
                    <span class="active-tag">
                        <i class="fa-solid fa-location-dot"></i> {{ $quartier->nom ?? '' }}
                        <a href="#" onclick="event.preventDefault();removeFilter('quartier')">&times;</a>
                    </span>
                @endif

                @if(request('type_bien'))
                    <span class="active-tag">
                        <i class="fa-solid fa-house"></i> {{ $typesBien[request('type_bien')] ?? '' }}
                        <a href="#" onclick="event.preventDefault();removeFilter('type_bien')">&times;</a>
                    </span>
                @endif

                @if(request('budget'))
                    <span class="active-tag">
                        <i class="fa-solid fa-money-bill"></i>
                        @php
                            $parts = explode('-', request('budget'));
                            if (count($parts) == 2) {
                                echo number_format($parts[0], 0, ',', ' ') . ' – ' . number_format($parts[1], 0, ',', ' ') . ' F';
                            } else {
                                echo str_replace('+', '+ ', request('budget')) . ' F';
                            }
                        @endphp
                        <a href="#" onclick="event.preventDefault();removeFilter('budget')">&times;</a>
                    </span>
                @endif

                @if(request('type_operation'))
                    <span class="active-tag">
                        <i class="fa-solid fa-handshake"></i> {{ request('type_operation') === 'achat' ? 'Achat' : 'Location' }}
                        <a href="#" onclick="event.preventDefault();removeFilter('type_operation')">&times;</a>
                    </span>
                @endif
            </div>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════
         BANNIÈRE AGENCES
    ═══════════════════════════════════════════ --}}
    @guest
        <section class="agency-banner">
            <div class="agency-banner__icon">
                <i class="fa-solid fa-briefcase"></i>
            </div>
            <div class="agency-banner__content">
                <strong>Vous êtes une agence immobilière ?</strong>
                <p>Recevez ces besoins par email dès leur publication et proposez vos biens en priorité.</p>
            </div>
            <a href="{{ route('register.agence') }}" class="btn btn-rust">
                <i class="fa-solid fa-user-plus"></i> Créer mon compte
            </a>
        </section>
    @endguest

    {{-- ═══════════════════════════════════════════
         LISTE
    ═══════════════════════════════════════════ --}}
    <section class="results" id="besoins-list">
        <header class="results__head">
            <h2 class="results__title">
                {{ $nbBesoins }} besoin{{ $nbBesoins > 1 ? 's' : '' }} trouvé{{ $nbBesoins > 1 ? 's' : '' }}
            </h2>
            @if($hasFilters)
                <span class="results__subtitle">
                    <i class="fa-solid fa-filter"></i> Filtres appliqués
                </span>
            @endif
        </header>

        @if($demandes->count() > 0)
            <div class="besoins-grid">
                @foreach($demandes as $demande)
                    @php
                        $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
                        $statutLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);

                        $isNew = $demande->created_at->gt(now()->subHours(48));
                        $nbOffres = $demande->propositions->count();
                    @endphp

                    <article class="besoin-card {{ $isNew ? 'is-new' : '' }}">

                        <header class="besoin-card__head">
                            @if($isNew)
                                <span class="besoin-card__badge besoin-card__badge--new">
                                    <i class="fa-solid fa-bolt"></i> Nouveau
                                </span>
                            @endif

                            <span class="offre-status offre-status--{{ $statutValue }}">
                                <i class="fa-solid fa-circle"></i>
                                {{ $statutLabel }}
                            </span>
                        </header>

                        <div class="besoin-card__body">

                            <div class="besoin-card__headline">
                                <h3 class="besoin-card__type">
                                    {{ $demande->type_bien->label() }}
                                </h3>
                                <div class="besoin-card__budget">
                                    {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                                    <small>F</small>
                                </div>
                            </div>

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

                            @if($demande->description)
                                <p class="besoin-card__desc">
                                    {{ Str::limit($demande->description, 110) }}
                                </p>
                            @endif
                        </div>

                        <footer class="besoin-card__foot">
                            <div class="besoin-card__stats">
                                @if($nbOffres > 0)
                                    <span class="stat-chip">
                                        <i class="fa-regular fa-comment-dots"></i>
                                        {{ $nbOffres }} offre{{ $nbOffres > 1 ? 's' : '' }}
                                    </span>
                                @else
                                    <span class="stat-chip stat-chip--empty">
                                        <i class="fa-regular fa-circle"></i>
                                        Aucune offre
                                    </span>
                                @endif

                                <span class="stat-chip stat-chip--time">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $demande->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <a href="{{ route('besoins.show', $demande->slug) }}" class="btn btn-rust btn-sm">
                                Voir le besoin <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </footer>
                    </article>
                @endforeach
            </div>

            {{-- PAGINATION --}}
            @if($demandes->hasPages())
                <div class="pagination-wrapper" id="paginationWrapper"
                     data-current-page="{{ $demandes->currentPage() }}"
                     data-last-page="{{ $demandes->lastPage() }}"
                     data-base-url="{{ $demandes->url(1) }}"
                     data-total="{{ $demandes->total() }}"
                     data-query="{{ http_build_query(request()->except('page')) }}">
                </div>
            @endif

        @else
            <div class="empty-state">
                <div class="empty-state__icon">
                    <i class="fa-regular fa-inbox"></i>
                </div>
                <h3>Aucun besoin trouvé</h3>
                <p>
                    @if($hasFilters)
                        Essayez de modifier vos filtres pour voir plus de résultats.
                    @else
                        Les besoins publiés par les particuliers apparaîtront ici.
                    @endif
                </p>
                @if($hasFilters)
                    <a href="{{ route('besoins.index') }}" class="btn btn-ghost">
                        <i class="fa-solid fa-rotate"></i> Voir tous les besoins
                    </a>
                @endif
            </div>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════
         CTA FINAL (agences non connectées)
    ═══════════════════════════════════════════ --}}
    @guest
        <section class="final-cta">
            <div class="final-cta__content">
                <h2>Recevez ces besoins en priorité</h2>
                <p>
                    Créez votre compte agence gratuitement et recevez chaque nouveau besoin
                    publié à Dakar directement par email.
                </p>

                <div class="final-cta__actions">
                    <a href="{{ route('register.agence') }}" class="btn btn-rust">
                        <i class="fa-solid fa-briefcase"></i> Créer mon compte agence
                    </a>
                    <a href="{{ route('register.particulier') }}" class="btn btn-ghost">
                        <i class="fa-solid fa-user"></i> Je suis un particulier
                    </a>
                </div>
            </div>
        </section>
    @endguest
</div>
@endsection


@push('scripts')
<script>
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
            params.forEach((value, key) => {
                if (key !== 'page') url.searchParams.set(key, value);
            });
        }
        return url.toString();
    };

    const getPages = () => {
        const pages = [];
        const maxVisible = 5;

        if (lastPage <= maxVisible + 2) {
            for (let i = 1; i <= lastPage; i++) pages.push(i);
        } else {
            pages.push(1);
            if (currentPage > 3) pages.push('...');
            const start = Math.max(2, currentPage - 1);
            const end = Math.min(lastPage - 1, currentPage + 1);
            for (let i = start; i <= end; i++) pages.push(i);
            if (currentPage < lastPage - 2) pages.push('...');
            pages.push(lastPage);
        }

        return pages;
    };

    const pages = getPages();
    let html = '<nav class="pagination-nav" role="navigation" aria-label="Pagination">';
    html += '<ul class="pagination-list">';

    // Précédent
    if (currentPage > 1) {
        html += `<li class="pagination-item">
                    <a href="${buildUrl(currentPage - 1)}" rel="prev" class="pagination-link">
                        <i class="fa-solid fa-chevron-left"></i>
                        <span class="pagination-label">Précédent</span>
                    </a>
                </li>`;
    } else {
        html += `<li class="pagination-item is-disabled">
                    <span class="pagination-link">
                        <i class="fa-solid fa-chevron-left"></i>
                        <span class="pagination-label">Précédent</span>
                    </span>
                </li>`;
    }

    // Pages
    pages.forEach(p => {
        if (p === '...') {
            html += `<li class="pagination-item is-disabled">
                        <span class="pagination-link pagination-link--dots">…</span>
                    </li>`;
        } else if (p === currentPage) {
            html += `<li class="pagination-item is-active" aria-current="page">
                        <span class="pagination-link">${p}</span>
                    </li>`;
        } else {
            html += `<li class="pagination-item">
                        <a href="${buildUrl(p)}" class="pagination-link">${p}</a>
                    </li>`;
        }
    });

    // Suivant
    if (currentPage < lastPage) {
        html += `<li class="pagination-item">
                    <a href="${buildUrl(currentPage + 1)}" rel="next" class="pagination-link">
                        <span class="pagination-label">Suivant</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>`;
    } else {
        html += `<li class="pagination-item is-disabled">
                    <span class="pagination-link">
                        <span class="pagination-label">Suivant</span>
                        <i class="fa-solid fa-chevron-right"></i>
                    </span>
                </li>`;
    }

    html += '</ul>';

    // Info résultats
    const firstItem = (currentPage - 1) * 12 + 1;
    const lastItem  = Math.min(currentPage * 12, total);
    if (total) {
        html += `<div class="pagination-info">
                    <span class="pagination-info__text">
                        <strong>${firstItem}</strong> – <strong>${lastItem}</strong> sur <strong>${total}</strong> résultats
                    </span>
                 </div>`;
    }

    html += '</nav>';
    wrapper.innerHTML = html;
});

/* ═══════════════════════════════════════════════════════════
   TOGGLE FILTRES + AUTRES
═══════════════════════════════════════════════════════════ */
function toggleFilters() {
    document.getElementById('searchFilters').classList.toggle('is-open');
}

function removeFilter(name) {
    const url = new URL(window.location.href);
    url.searchParams.delete(name);
    window.location.href = url.toString();
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
                        <div class="autocomplete__item" onclick="window.location.href='${item.url || '#'}'">
                            <i class="${item.icon || 'fa-solid fa-circle'}"></i>
                            <div>
                                <strong>${item.label}</strong>
                                <span>${item.description || ''}</span>
                            </div>
                        </div>
                    `).join('');
                    results.style.display = 'block';
                })
                .catch(() => results.style.display = 'none');
        }, 300);
    });

    document.addEventListener('click', e => {
        if (!e.target.closest('.search-bar__input')) {
            results.style.display = 'none';
        }
    });
});

/* ═══════════════════════════════════════════════════════════
   SMOOTH SCROLL
═══════════════════════════════════════════════════════════ */
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE BESOINS PUBLICS
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
    --c-gold:       #D4AF37;
    --surface:      #F7F9FC;
    --radius:       14px;

    padding-bottom: 40px;
}

/* ═══ HERO ═══ */
.hero {
    background: linear-gradient(135deg, #FFFBF7 0%, #fff 55%);
    padding: 40px 24px 44px;
    position: relative;
    overflow: hidden;
    border-bottom: 1px solid var(--border);
}
.hero::before {
    content: '';
    position: absolute;
    top: -50%; right: -15%;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(181, 80, 42, .08), transparent 65%);
    pointer-events: none;
}

.hero__content {
    max-width: 1200px;
    margin: 0 auto;
    position: relative;
}

.hero__eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--rust);
    background: rgba(181, 80, 42, .1);
    padding: 5px 12px;
    border-radius: 999px;
    margin-bottom: 16px;
}
.hero__eyebrow i { font-size: 10px; }

.hero__title {
    font-family: var(--display);
    font-size: clamp(26px, 4vw, 42px);
    font-weight: 800;
    line-height: 1.15;
    color: var(--ink);
    margin: 0 0 14px;
    letter-spacing: -.02em;
    max-width: 700px;
}

.hero__sub {
    font-size: clamp(14px, 1.1vw, 16px);
    color: var(--text-soft);
    margin: 0 0 24px;
    max-width: 560px;
    line-height: 1.6;
}

.hero__stats {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}
.hero__stat {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.hero__stat-value {
    font-family: var(--display);
    font-size: clamp(22px, 2.4vw, 28px);
    font-weight: 800;
    color: var(--rust);
    line-height: 1;
}
.hero__stat-label {
    font-size: 11.5px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}
.hero__stat-sep {
    width: 1px;
    height: 34px;
    background: var(--border);
    flex-shrink: 0;
}

.hero__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* ═══ RECHERCHE ═══ */
.search-section {
    max-width: 1200px;
    margin: -22px auto 28px;
    padding: 0 24px;
    position: relative;
    z-index: 5;
}

.search-bar {
    display: flex;
    gap: 10px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 8px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .06);
    margin-bottom: 12px;
}

.search-bar__input {
    position: relative;
    flex: 1;
    display: flex;
    align-items: center;
}
.search-bar__input i {
    position: absolute;
    left: 14px;
    color: var(--muted);
    font-size: 14px;
    pointer-events: none;
}
.search-bar__input input {
    width: 100%;
    padding: 12px 16px 12px 40px;
    border: none;
    background: transparent;
    font-size: 14px;
    font-family: inherit;
    color: var(--ink);
    outline: none;
}
.search-bar__input input::placeholder { color: var(--muted); }

.btn-filters {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 18px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-soft);
    cursor: pointer;
    font-family: inherit;
    transition: all .2s;
    flex-shrink: 0;
}
.btn-filters:hover { background: #EDF0F5; border-color: #d8d8d8; }
.btn-filters__count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: var(--rust);
    color: #fff;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    font-family: var(--display);
}

/* Autocomplete */
.autocomplete {
    display: none;
    position: absolute;
    top: calc(100% + 4px);
    left: 0; right: 0;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 12px 32px rgba(0, 0, 0, .1);
    z-index: 100;
    max-height: 320px;
    overflow-y: auto;
}
.autocomplete__item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    cursor: pointer;
    transition: background .15s;
    border-bottom: 1px solid var(--border);
}
.autocomplete__item:last-child { border-bottom: none; }
.autocomplete__item:hover { background: var(--surface); }
.autocomplete__item i { color: var(--rust); width: 18px; text-align: center; flex-shrink: 0; }
.autocomplete__item strong { display: block; font-size: 13px; font-weight: 600; color: var(--ink); }
.autocomplete__item span { display: block; font-size: 11.5px; color: var(--muted); }

/* Filtres */
.filters {
    display: none;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 18px 20px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, .04);
}
.filters.is-open { display: block; }

.filters__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 12px;
    margin-bottom: 14px;
}

.filters__field {
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.filters__field label {
    font-size: 11.5px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .3px;
    font-weight: 700;
}
.filters__field select {
    padding: 10px 34px 10px 14px;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 13.5px;
    font-family: inherit;
    color: var(--ink);
    background: #fff;
    -webkit-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    cursor: pointer;
    transition: border-color .2s;
}
.filters__field select:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 3px rgba(181, 80, 42, .1);
}

.filters__actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding-top: 14px;
    border-top: 1px solid var(--border);
}

/* Filtres actifs */
.active-filters {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-top: 12px;
    padding: 12px 16px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
}
.active-filters__label {
    font-size: 12px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .3px;
    margin-right: 4px;
}
.active-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: rgba(181, 80, 42, .1);
    color: var(--rust);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}
.active-tag i { font-size: 10px; }
.active-tag a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    color: var(--rust);
    text-decoration: none;
    font-size: 14px;
    line-height: 1;
    border-radius: 50%;
    transition: background .2s;
}
.active-tag a:hover { background: rgba(181, 80, 42, .2); }

/* ═══ BANNIÈRE AGENCE ═══ */
.agency-banner {
    max-width: 1200px;
    margin: 0 auto 24px;
    padding: 18px 22px;
    background: linear-gradient(135deg, #F0F7FF 0%, #fff 70%);
    border: 1px solid #BBDEFB;
    border-radius: 14px;
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.agency-banner__icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--c-info-bg);
    color: var(--c-info);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.agency-banner__content {
    flex: 1;
    min-width: 200px;
}
.agency-banner__content strong {
    display: block;
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    color: var(--c-info);
    margin-bottom: 2px;
}
.agency-banner__content p {
    font-size: 13px;
    color: #4A6C9C;
    margin: 0;
    line-height: 1.5;
}

/* ═══ RÉSULTATS ═══ */
.results {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 24px;
}

.results__head {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 12px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.results__title {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
}
.results__subtitle {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.results__subtitle i { font-size: 11px; }

/* ═══ GRILLE BESOINS ═══ */
.besoins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

/* ═══ CARTE BESOIN ═══ */
.besoin-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
}
.besoin-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(0, 0, 0, .07);
    border-color: #d8d8d8;
}
.besoin-card.is-new {
    border-color: rgba(181, 80, 42, .35);
    background: linear-gradient(135deg, #FFFBF7 0%, #fff 50%);
}

.besoin-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 12px 16px 10px;
    flex-wrap: wrap;
}

.besoin-card__badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.besoin-card__badge--new {
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    box-shadow: 0 2px 6px rgba(181, 80, 42, .3);
    animation: pulseNew 2.2s ease-in-out infinite;
}
.besoin-card__badge--new i { font-size: 9px; }

@keyframes pulseNew {
    0%, 100% { opacity: 1; }
    50%      { opacity: .85; }
}

.besoin-card__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 4px 16px 14px;
}

.besoin-card__headline {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
}
.besoin-card__type {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
    line-height: 1.25;
}
.besoin-card__budget {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
    white-space: nowrap;
}
.besoin-card__budget small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}

.besoin-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.besoin-card__desc {
    font-size: 12.5px;
    line-height: 1.55;
    color: var(--text-soft);
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

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
    flex-wrap: wrap;
    gap: 6px;
}

.stat-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    background: var(--c-info-bg);
    color: var(--c-info);
    font-size: 11px;
    font-weight: 600;
    border: 1px solid #BBDEFB;
}
.stat-chip i { font-size: 10px; }
.stat-chip--empty {
    background: var(--surface);
    color: var(--muted);
    border-color: var(--border);
}
.stat-chip--time {
    background: transparent;
    color: var(--muted);
    border: none;
    padding-left: 0;
}

/* ═══ STATUTS ═══ */
.offre-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}
.offre-status i { font-size: 5px; }
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.offre-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }

/* Meta pills */
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

/* ═══ EMPTY STATE ═══ */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: var(--c-warning-bg);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
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
    margin: 0 0 18px;
    max-width: 400px;
    margin-inline: auto;
}

/* ═══ CTA FINAL ═══ */
.final-cta {
    max-width: 1200px;
    margin: 44px auto 0;
    padding: 0 24px;
}
.final-cta__content {
    background: linear-gradient(135deg, #F5E6DF 0%, #FFFBF7 100%);
    border: 1px solid rgba(181, 80, 42, .2);
    border-radius: 18px;
    padding: 36px 32px;
    text-align: center;
}
.final-cta h2 {
    font-family: var(--display);
    font-size: clamp(20px, 2.4vw, 26px);
    font-weight: 800;
    margin: 0 0 8px;
    color: var(--ink);
    letter-spacing: -.02em;
}
.final-cta p {
    font-size: clamp(13px, 1vw, 14.5px);
    color: var(--text-soft);
    margin: 0 auto 22px;
    max-width: 520px;
    line-height: 1.6;
}
.final-cta__actions {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper {
    margin-top: 32px;
    display: flex;
    justify-content: center;
    width: 100%;
}

.pagination-nav {
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

.pagination-item { display: inline-flex; }

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

.pagination-item.is-active .pagination-link {
    background: var(--rust);
    color: #fff;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    font-weight: 700;
}

.pagination-item.is-disabled .pagination-link {
    opacity: .4;
    cursor: not-allowed;
    color: var(--muted);
}

.pagination-item.is-disabled .pagination-link:hover {
    background: transparent;
    color: var(--muted);
}

.pagination-link--dots {
    pointer-events: none;
    color: var(--muted);
    min-width: 32px;
}

.pagination-link i { font-size: 11px; }

.pagination-info { text-align: center; }
.pagination-info__text {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.pagination-info__text strong {
    color: var(--ink);
    font-weight: 700;
    font-family: var(--display);
}

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 11px 18px;
    border-radius: 10px;
    font-size: 13.5px;
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
    background: var(--surface);
    border-color: var(--rust);
    color: var(--rust);
}
.btn-sm { padding: 7px 13px; font-size: 12px; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .hero { padding: 32px 20px 38px; }
    .search-section { padding: 0 20px; margin-top: -20px; }
    .results { padding: 0 20px; }
    .final-cta { padding: 0 20px; }
    .agency-banner { margin-left: 20px; margin-right: 20px; }
}

@media (max-width: 768px) {
    .hero { padding: 26px 16px 32px; }
    .hero__title { font-size: 24px; }
    .hero__stats { gap: 14px; }
    .hero__stat-sep { height: 28px; }
    .hero__stat-value { font-size: 20px; }
    .hero__actions .btn { flex: 1; }

    .search-section { padding: 0 16px; margin-top: -18px; }
    .search-bar { flex-direction: column; padding: 6px; }
    .search-bar__input { padding: 4px 0; }
    .btn-filters { width: 100%; justify-content: center; }

    .filters__grid { grid-template-columns: 1fr 1fr; }
    .filters__actions { flex-direction: column; }
    .filters__actions .btn { width: 100%; }

    .agency-banner {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
        padding: 20px;
        margin-left: 16px;
        margin-right: 16px;
    }
    .agency-banner__icon { margin: 0 auto; }
    .agency-banner .btn { width: 100%; }

    .results { padding: 0 16px; }
    .results__title { font-size: 17px; }

    .besoins-grid { grid-template-columns: 1fr; gap: 12px; }

    .besoin-card__foot { flex-direction: column; align-items: stretch; }
    .besoin-card__foot .btn { width: 100%; justify-content: center; }

    .final-cta { padding: 0 16px; margin-top: 32px; }
    .final-cta__content { padding: 26px 20px; }
    .final-cta__actions { flex-direction: column; }
    .final-cta__actions .btn { width: 100%; }

    .active-filters { padding: 10px 12px; gap: 6px; }
    .active-tag { font-size: 11px; padding: 3px 8px; }

    .pagination-list { padding: 5px; gap: 3px; }
    .pagination-link {
        min-width: 34px;
        height: 34px;
        padding: 0 9px;
        font-size: 12px;
    }
    .pagination-label { display: none; }
    .pagination-info__text { font-size: 11.5px; }
}

@media (max-width: 480px) {
    .hero { padding: 22px 14px 28px; }
    .hero__title { font-size: 22px; }
    .hero__sub { font-size: 13.5px; }
    .hero__stat-value { font-size: 18px; }
    .hero__stat-label { font-size: 10.5px; }

    .search-section { padding: 0 14px; }
    .filters__grid { grid-template-columns: 1fr; }

    .results { padding: 0 14px; }

    .besoin-card__body { padding: 4px 14px 12px; }
    .besoin-card__head { padding: 10px 14px 8px; }
    .besoin-card__foot { padding: 10px 14px; }

    .besoin-card__type,
    .besoin-card__budget { font-size: 15.5px; }

    .meta-pill { font-size: 11px; padding: 3px 9px; }

    .pagination-list { padding: 4px; gap: 2px; }
    .pagination-link {
        min-width: 30px;
        height: 30px;
        padding: 0 7px;
        font-size: 11.5px;
        border-radius: 8px;
    }
    .pagination-link--dots { min-width: 24px; }
    .pagination-info__text { font-size: 11px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
    .besoin-card__badge--new { animation: none !important; }
}
</style>
@endpush