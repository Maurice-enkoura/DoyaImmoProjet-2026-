@extends('layouts.app')

@section('title', 'Recherche — DoyaImmo')
@section('page_title', 'Recherche')
@section('page_sub', 'Trouvez des biens ou des agences immobilières')

@section('content')
<style>
    /* ===== CONTENEUR ===== */
    .search-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
        padding-top: 30px;
    }

    @media (min-width: 768px) {
        .search-container {
            padding: 0 24px;
            padding-top: 30px;
        }
    }

    @media (min-width: 1200px) {
        .search-container {
            padding: 0 40px;
            padding-top: 30px;
        }
    }

    /* ===== EN-TÊTE ===== */
    .search-header {
        margin-bottom: 30px;
    }

    .search-header h1 {
        font-family: var(--display);
        font-weight: 800;
        font-size: clamp(24px, 3.5vw, 36px);
        margin-bottom: 8px;
    }

    .search-header p {
        font-size: clamp(14px, 1vw, 16px);
        color: var(--muted);
    }

    .search-header .search-query {
        margin-top: 8px;
        font-size: clamp(13px, 0.9vw, 14px);
        color: var(--muted);
    }

    .search-header .search-query strong {
        color: var(--ink);
    }

    /* ===== FILTRES ===== */
    .search-filters {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: clamp(16px, 2vw, 20px) clamp(16px, 2vw, 24px);
        margin-bottom: 24px;
    }

    .search-filters .filter-grid {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .search-filters .filter-group {
        flex: 1;
        min-width: 140px;
    }

    .search-filters .filter-group label {
        display: block;
        font-size: clamp(10px, 0.7vw, 11px);
        font-weight: 600;
        color: var(--muted);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ===== IMPORTANT : Éviter le zoom sur iOS ===== */
    .search-filters .filter-group select,
    .search-filters .filter-group input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 16px !important;
        font-family: inherit;
        background: #fff;
        color: var(--ink);
        transition: border-color 0.2s, box-shadow 0.2s;
        -webkit-appearance: none;
        appearance: none;
    }

    .search-filters .filter-group select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 28px;
    }

    .search-filters .filter-group select:focus,
    .search-filters .filter-group input:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.1);
    }

    .search-filters .filter-group .filter-actions {
        display: flex;
        gap: 8px;
    }

    .search-filters .filter-group .filter-actions .btn {
        flex: 1;
        justify-content: center;
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
        margin-bottom: 12px;
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

    /* ===== ONGLETS ===== */
    .search-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        flex-wrap: wrap;
        border-bottom: 2px solid var(--border);
        padding-bottom: 12px;
    }

    .search-tabs .tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 999px;
        font-size: clamp(12px, 0.8vw, 13px);
        font-weight: 600;
        color: var(--muted);
        text-decoration: none;
        transition: all 0.2s;
        border: none;
        background: none;
        cursor: pointer;
    }

    .search-tabs .tab:hover {
        color: var(--ink);
        background: var(--border);
    }

    .search-tabs .tab.active {
        background: var(--rust);
        color: #fff;
    }

    .search-tabs .tab .badge {
        background: rgba(255,255,255,0.2);
        padding: 0 8px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11px);
        font-weight: 600;
    }

    .search-tabs .tab.active .badge {
        background: rgba(255,255,255,0.25);
    }

    .search-tabs .tab .badge.rust {
        background: var(--rust-soft);
        color: var(--rust);
    }

    .search-tabs .tab.active .badge.rust {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }

    /* ===== RÉSULTATS ===== */
    .search-results {
        margin-top: 8px;
    }

    .search-results .results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .search-results .results-count {
        font-size: clamp(13px, 0.9vw, 14px);
        color: var(--muted);
    }

    .search-results .results-count strong {
        color: var(--ink);
        font-weight: 700;
    }

    .search-results .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr));
        gap: 20px;
    }

    /* ===== CARTE RÉSULTAT - BIEN ===== */
    .result-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
    }

    .result-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
    }

    .result-card .result-image {
        height: clamp(160px, 22vw, 200px);
        background: #F0F2F5;
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .result-card .result-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .result-card .result-image .image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        color: var(--muted);
        font-size: 40px;
        opacity: 0.3;
    }

    .result-card .result-image .result-badge {
        position: absolute;
        top: clamp(8px, 1vw, 12px);
        right: clamp(8px, 1vw, 12px);
        padding: clamp(3px, 0.4vw, 4px) clamp(10px, 1.2vw, 14px);
        border-radius: 999px;
        font-size: clamp(10px, 0.8vw, 11px);
        font-weight: 600;
        color: #fff;
    }

    .result-card .result-image .result-badge.available {
        background: #1E7A47;
    }

    .result-card .result-image .result-badge.unavailable {
        background: var(--muted);
    }

    .result-card .result-image .result-type {
        position: absolute;
        bottom: clamp(8px, 1vw, 12px);
        left: clamp(8px, 1vw, 12px);
        padding: clamp(3px, 0.4vw, 4px) clamp(10px, 1.2vw, 14px);
        border-radius: 999px;
        font-size: clamp(10px, 0.8vw, 11px);
        font-weight: 600;
        color: #fff;
        background: rgba(0,0,0,0.6);
    }

    .result-card .result-body {
        padding: clamp(14px, 1.5vw, 16px) clamp(14px, 1.5vw, 18px) clamp(14px, 1.5vw, 18px);
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .result-card .result-body .result-type-icon {
        font-size: clamp(11px, 0.8vw, 12px);
        color: var(--muted);
        margin-bottom: 4px;
    }

    .result-card .result-body .result-title {
        font-weight: 600;
        font-size: clamp(14px, 1vw, 16px);
        color: var(--ink);
        margin-bottom: 2px;
        word-break: break-word;
    }

    .result-card .result-body .result-price {
        font-weight: 700;
        color: var(--rust);
        font-size: clamp(15px, 1.1vw, 17px);
        margin-bottom: 4px;
    }

    .result-card .result-body .result-location {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
        margin-bottom: 8px;
        word-break: break-word;
    }

    .result-card .result-body .result-location i {
        margin-right: 4px;
    }

    .result-card .result-body .result-features {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }

    .result-card .result-body .result-features .feature-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 12px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11px);
        color: var(--text-soft);
    }

    .result-card .result-body .result-features .feature-pill i {
        font-size: 10px;
    }

    .result-card .result-body .result-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: clamp(11px, 0.7vw, 12px);
        color: var(--muted);
        margin-bottom: 12px;
    }

    .result-card .result-body .result-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .result-card .result-body .result-actions {
        margin-top: auto;
    }

    /* ===== CARTE AGENCE ===== */
    .agency-card {
        text-align: center;
    }

    .agency-card .agency-logo {
        width: clamp(50px, 6vw, 60px);
        height: clamp(50px, 6vw, 60px);
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 12px;
        border: 2px solid var(--border);
        background: #F0F2F5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(18px, 2vw, 24px);
        font-weight: 700;
        color: var(--muted);
        flex-shrink: 0;
        overflow: hidden;
    }

    .agency-card .agency-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .agency-card .agency-stats {
        display: flex;
        justify-content: center;
        gap: 16px;
        font-size: clamp(11px, 0.7vw, 12px);
        color: var(--muted);
        margin: 8px 0;
        flex-wrap: wrap;
    }

    .agency-card .agency-rating {
        color: #F5A623;
        font-weight: 700;
        font-size: clamp(13px, 0.9vw, 15px);
        margin-bottom: 8px;
    }

    .agency-card .agency-description {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--text-soft);
        line-height: 1.5;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        grid-column: 1/-1;
        text-align: center;
        padding: clamp(40px, 5vw, 60px) 20px;
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        color: var(--muted);
    }

    .empty-state i {
        font-size: clamp(40px, 4vw, 48px);
        display: block;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    .empty-state h3 {
        font-size: clamp(16px, 1.2vw, 18px);
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--text-soft);
    }

    .empty-state p {
        font-size: clamp(13px, 0.9vw, 14px);
    }

    .empty-state .empty-actions {
        margin-top: 20px;
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* ===== PAGINATION ===== */
    .pagination-wrapper {
        margin-top: 30px;
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

    /* ===== BOUTONS ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: clamp(11px, 0.8vw, 13px);
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
        font-size: clamp(10px, 0.7vw, 12px);
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    /* ============================================
       RESPONSIVE - FILTRES MASQUÉS SUR MOBILE
    ============================================ */

    @media (max-width: 768px) {
        /* Afficher le bouton toggle sur mobile */
        .btn-filters-toggle {
            display: flex;
        }

        /* Masquer les filtres par défaut sur mobile */
        .search-filters .filter-grid {
            display: none;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
        }

        .search-filters .filter-grid.open {
            display: flex;
        }

        .search-filters .filter-group {
            width: 100%;
            min-width: unset;
        }

        .search-filters .filter-group .filter-actions {
            flex-direction: column;
        }

        .search-filters .filter-group .filter-actions .btn {
            width: 100%;
            justify-content: center;
        }

        .search-filters .filter-group select,
        .search-filters .filter-group input {
            font-size: 16px !important;
            padding: 10px 12px;
        }

        .search-results .results-grid {
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin: 0 auto;
        }

        .search-tabs {
            justify-content: center;
        }

        .search-tabs .tab {
            font-size: clamp(11px, 0.8vw, 12px);
            padding: 6px 14px;
        }
    }

    @media (max-width: 640px) {
        .search-container {
            padding: 0 12px;
            padding-top: 20px;
        }

        .search-filters {
            padding: 14px;
            border-radius: 12px;
        }

        .search-results .results-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .result-card .result-image {
            height: 140px;
        }

        .result-card .result-body {
            padding: 10px 12px 12px;
        }

        .result-card .result-body .result-title {
            font-size: 13px;
        }

        .result-card .result-body .result-price {
            font-size: 14px;
        }

        .result-card .result-body .result-features .feature-pill {
            font-size: 9px;
            padding: 1px 8px;
        }

        .search-header h1 {
            font-size: 22px;
        }

        .search-header p {
            font-size: 14px;
        }

        .pagination a,
        .pagination span {
            padding: 4px 8px;
            font-size: 11px;
            min-width: 28px;
        }

        .search-tabs {
            flex-wrap: wrap;
        }

        .search-tabs .tab {
            flex: 1;
            justify-content: center;
            font-size: 11px;
            padding: 6px 10px;
        }

        .agency-card .agency-logo {
            width: 50px;
            height: 50px;
            font-size: 18px;
        }

        .search-filters .filter-group select,
        .search-filters .filter-group input {
            font-size: 16px !important;
            padding: 10px 12px;
        }

        .btn-filters-toggle {
            font-size: 13px;
            padding: 8px 14px;
        }
    }

    @media (max-width: 460px) {
        .search-container {
            padding: 0 8px;
            padding-top: 16px;
        }

        .search-results .results-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .result-card .result-image {
            height: 180px;
        }

        .result-card .result-body {
            padding: 12px 14px 14px;
        }

        .result-card .result-body .result-title {
            font-size: 15px;
        }

        .result-card .result-body .result-price {
            font-size: 16px;
        }

        .search-header h1 {
            font-size: 20px;
        }

        .search-tabs .tab {
            font-size: 10px;
            padding: 5px 8px;
        }

        .search-filters {
            padding: 12px;
        }

        .search-filters .filter-group select,
        .search-filters .filter-group input {
            font-size: 16px !important;
            padding: 10px 12px;
        }

        .btn-filters-toggle {
            font-size: 12px;
            padding: 8px 12px;
        }
    }

    /* ===== ACCESSIBILITÉ ===== */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        .search-filters .filter-grid {
            transition: none !important;
        }
        .btn-filters-toggle .fa-chevron-down {
            transition: none !important;
        }
    }
</style>

<div class="search-container">
    <!-- En-tête -->
    <div class="search-header">
        <h1>Recherche immobilière</h1>
        <p>Recherchez des biens ou des agences immobilières à Dakar</p>
        @if($query)
            <div class="search-query">
                Résultats pour : <strong>"{{ $query }}"</strong>
            </div>
        @endif
    </div>

    <!-- Filtres -->
    <div class="search-filters">
        <!-- Bouton toggle pour mobile -->
        <button type="button" class="btn-filters-toggle" id="filtersToggle" onclick="toggleFilters()">
            <i class="fa-solid fa-sliders-h"></i> Filtres
            <span class="filters-count" id="filtersCount">
                @php
                    $count = 0;
                    if(request('type_bien')) $count++;
                    if(request('type_contrat')) $count++;
                    if(request('quartier')) $count++;
                @endphp
                @if($count > 0)
                    ({{ $count }})
                @endif
            </span>
            <i class="fa-solid fa-chevron-down" id="filtersArrow"></i>
        </button>

        <form action="{{ route('recherche') }}" method="GET" class="filter-grid" id="filterGrid">
            <div class="filter-group">
                <label for="q">Mot-clé</label>
                <input type="text" name="q" id="q" placeholder="Rechercher..." value="{{ request('q') }}">
            </div>

            <div class="filter-group">
                <label for="type_bien">Type de bien</label>
                <select name="type_bien" id="type_bien">
                    <option value="">Tous les types</option>
                    @foreach($typesBien as $key => $label)
                        <option value="{{ $key }}" {{ request('type_bien') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="type_contrat">Type de contrat</label>
                <select name="type_contrat" id="type_contrat">
                    <option value="">Tous les contrats</option>
                    @foreach($typesContrat as $key => $label)
                        <option value="{{ $key }}" {{ request('type_contrat') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="quartier">Quartier</label>
                <select name="quartier" id="quartier">
                    <option value="">Tous les quartiers</option>
                    @foreach($quartiers as $quartier)
                        <option value="{{ $quartier->id }}" {{ request('quartier') == $quartier->id ? 'selected' : '' }}>
                            {{ $quartier->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group" style="flex:0 0 auto;min-width:unset;">
                <label>&nbsp;</label>
                <div class="filter-actions">
                    <button type="submit" class="btn btn-rust">
                        <i class="fa-solid fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('recherche') }}" class="btn btn-ghost">
                        <i class="fa-solid fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Onglets -->
    <div class="search-tabs">
        <a href="{{ route('recherche', array_merge(request()->except('type'), ['type' => 'tous'])) }}" 
           class="tab {{ $type == 'tous' || !$type ? 'active' : '' }}">
            <i class="fa-solid fa-border-all"></i> Tous
            <span class="badge">{{ $counts['total'] }}</span>
        </a>
        <a href="{{ route('recherche', array_merge(request()->except('type'), ['type' => 'biens'])) }}" 
           class="tab {{ $type == 'biens' ? 'active' : '' }}">
            <i class="fa-solid fa-house"></i> Biens
            <span class="badge rust">{{ $counts['biens'] }}</span>
        </a>
        <a href="{{ route('recherche', array_merge(request()->except('type'), ['type' => 'agences'])) }}" 
           class="tab {{ $type == 'agences' ? 'active' : '' }}">
            <i class="fa-solid fa-building"></i> Agences
            <span class="badge rust">{{ $counts['agences'] }}</span>
        </a>
    </div>

    <!-- Résultats -->
    <div class="search-results">
        <div class="results-header">
            <div class="results-count">
                <strong>{{ $counts['total'] }}</strong> résultat(s) trouvé(s)
            </div>
        </div>

        <div class="results-grid">
            {{-- BIENS --}}
            @if(($type == 'tous' || !$type || $type == 'biens') && $biens->count() > 0)
                @foreach($biens as $bien)
                    <div class="result-card">
                        <div class="result-image">
                            @if($bien->medias && $bien->medias->where('type_media', 'image')->first())
                                <img src="{{ asset('storage/' . $bien->medias->where('type_media', 'image')->first()->fichier) }}" 
                                     alt="{{ $bien->titre }}" loading="lazy">
                            @else
                                <div class="image-placeholder">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            @endif
                            <span class="result-badge {{ $bien->statut ? 'available' : 'unavailable' }}">
                                {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                            </span>
                            <span class="result-type">
                                {{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}
                            </span>
                        </div>

                        <div class="result-body">
                            <div class="result-type-icon">
                                <span style="color:var(--rust);"><i class="fa-solid fa-house"></i> Bien</span>
                            </div>
                            <div class="result-title">{{ $bien->titre }}</div>
                            <div class="result-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                            <div class="result-location">
                                <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier->nom ?? $bien->quartier ?? 'Localisation non définie' }}
                            </div>

                            <div class="result-features">
                                <span class="feature-pill"><i class="fa-regular fa-vector-square"></i> {{ $bien->surface }} m²</span>
                                <span class="feature-pill"><i class="fa-regular fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                                <span class="feature-pill"><i class="fa-regular fa-bath"></i> {{ $bien->nombre_salles_bain }} sdb</span>
                                <span class="feature-pill">{{ is_object($bien->type_contrat) && method_exists($bien->type_contrat, 'label') ? $bien->type_contrat->label() : $bien->type_contrat }}</span>
                            </div>

                            <div class="result-meta">
                                <span><i class="fa-regular fa-building-columns"></i> {{ $bien->agence->nom_agence ?? 'Agence' }}</span>
                            </div>

                            <div class="result-actions">
                                <a href="{{ route('biens.show', $bien) }}" class="btn btn-rust btn-sm btn-block">
                                    <i class="fa-regular fa-eye"></i> Voir le détail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- AGENCES --}}
            @if(($type == 'tous' || !$type || $type == 'agences') && $agences->count() > 0)
                @foreach($agences as $agence)
                    <div class="result-card agency-card">
                        <div class="result-body" style="text-align:center;">
                            <div class="agency-logo" style="margin:0 auto 12px;">
                                @if($agence->logo)
                                    <img src="{{ asset('storage/' . $agence->logo) }}" alt="{{ $agence->nom_agence }}">
                                @else
                                    {{ strtoupper(substr($agence->nom_agence, 0, 2)) }}
                                @endif
                            </div>

                            <div class="result-type-icon">
                                <span style="color:#0D47A1;"><i class="fa-solid fa-building"></i> Agence</span>
                            </div>
                            <div class="result-title">{{ $agence->nom_agence }}</div>
                            <div class="result-location">
                                <i class="fa-solid fa-location-dot"></i> {{ $agence->quartier->nom ?? 'Localisation non définie' }}
                            </div>

                            @php
                                $note = $agence->evaluations->avg('note') ?? 0;
                            @endphp
                            <div class="agency-rating">
                                @if($note > 0)
                                    <i class="fa-solid fa-star"></i> {{ number_format($note, 1) }} / 5
                                    <span style="font-size:clamp(11px,0.7vw,12px);color:var(--muted);font-weight:400;">
                                        ({{ $agence->evaluations->count() }} avis)
                                    </span>
                                @else
                                    <span style="font-size:clamp(12px,0.8vw,13px);color:var(--muted);font-weight:400;">Aucun avis</span>
                                @endif
                            </div>

                            <div class="agency-stats">
                                <span><i class="fa-solid fa-house"></i> {{ $agence->biens->count() }} biens</span>
                                <span><i class="fa-solid fa-handshake"></i> {{ $agence->propositions->count() }} offres</span>
                            </div>

                            @if($agence->description)
                                <div class="agency-description">
                                    {{ Str::limit($agence->description, 100) }}
                                </div>
                            @endif

                            <div class="result-actions">
                                <a href="{{ route('agences.public.show', $agence) }}" class="btn btn-rust btn-sm btn-block">
                                    <i class="fa-regular fa-eye"></i> Voir l'agence
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- Empty state --}}
            @if($biens->count() == 0 && $agences->count() == 0)
                <div class="empty-state">
                    <i class="fa-regular fa-search"></i>
                    <h3>Aucun résultat trouvé</h3>
                    <p>Essayez de modifier vos critères de recherche pour trouver ce que vous cherchez.</p>
                    <div class="empty-actions">
                        <a href="{{ route('recherche') }}" class="btn btn-rust">
                            <i class="fa-solid fa-rotate"></i> Réinitialiser
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-ghost">
                            <i class="fa-solid fa-pen-to-square"></i> Publier un besoin
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{-- Pagination pour les biens --}}
        @if(($type == 'tous' || !$type || $type == 'biens') && $biens->hasPages())
            <div class="pagination-wrapper">
                {{ $biens->appends(request()->except('page'))->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// Toggle des filtres sur mobile
function toggleFilters() {
    const filterGrid = document.getElementById('filterGrid');
    const arrow = document.getElementById('filtersArrow');
    filterGrid.classList.toggle('open');
    arrow.classList.toggle('open');
}

// Fermer les filtres si on clique en dehors
document.addEventListener('click', function(e) {
    const filterGrid = document.getElementById('filterGrid');
    const toggleBtn = document.getElementById('filtersToggle');
    if (window.innerWidth <= 768) {
        if (!e.target.closest('.search-filters') && filterGrid.classList.contains('open')) {
            filterGrid.classList.remove('open');
            document.getElementById('filtersArrow').classList.remove('open');
        }
    }
});
</script>
@endsection