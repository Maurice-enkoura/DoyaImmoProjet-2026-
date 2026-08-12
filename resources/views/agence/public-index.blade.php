{{-- resources/views/agence/public-index.blade.php --}}
@extends('layouts.app')

@section('title', 'Agences immobilières — DoyaImmo')

@section('content')
<style>
    /* ===== CONTENEUR ===== */
    .agencies-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    @media (min-width: 768px) {
        .agencies-container {
            padding: 0 24px;
        }
    }

    @media (min-width: 1200px) {
        .agencies-container {
            padding: 0 40px;
        }
    }

    /* ===== PAGE HEAD ===== */
    .page-head {
        padding: 20px 0 10px;
    }

    .page-head .eyebrow {
        display: inline-block;
        font-size: clamp(10px, 0.7vw, 11px);
        font-weight: 600;
        color: var(--rust);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .page-head .h-hero {
        font-family: var(--display);
        font-weight: 800;
        font-size: clamp(26px, 3.5vw, 32px);
        line-height: 1.2;
        margin-bottom: 8px;
    }

    .page-head .lead {
        font-size: clamp(14px, 1vw, 16px);
        color: var(--text-soft);
        line-height: 1.7;
        max-width: 600px;
    }

    /* ===== FILTER SECTION ===== */
    .filter-section {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: clamp(16px, 2vw, 20px) clamp(16px, 2vw, 24px);
        margin-bottom: 24px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    .filter-section .filter-grid {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: flex-end;
    }

    .filter-section .filter-group {
        flex: 1;
        min-width: 140px;
    }

    .filter-section .filter-group label {
        display: block;
        font-size: clamp(10px, 0.7vw, 11px);
        font-weight: 600;
        color: var(--muted);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ===== IMPORTANT : Éviter le zoom sur iOS ===== */
    .filter-section .filter-group select,
    .filter-section .filter-group input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 16px !important;
        font-family: inherit;
        background: #fff;
        color: var(--ink);
        transition: border-color 0.3s;
        -webkit-appearance: none;
        appearance: none;
    }

    .filter-section .filter-group select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 28px;
    }

    .filter-section .filter-group select:focus,
    .filter-section .filter-group input:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.1);
    }

    .filter-section .filter-group.filter-actions-group {
        flex: 0 0 auto;
        min-width: unset;
    }

    .filter-section .filter-group.filter-actions-group .btn {
        width: 100%;
        justify-content: center;
    }

    /* ===== RESULTS COUNT ===== */
    .results-count {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
        margin-bottom: 16px;
        text-align: center;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    /* ===== AGENCIES GRID ===== */
    .agencies-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr));
        gap: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .agency-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        padding: clamp(20px, 2vw, 24px);
        text-align: center;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    /* ✅ Carte agence avec biens en vedette */
    .agency-card.has-vedette {
        border-color: #F5A623;
        border-width: 2px;
    }

    .agency-card.has-vedette::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: var(--radius);
        background: linear-gradient(135deg, rgba(245, 166, 35, 0.04), transparent);
        pointer-events: none;
        z-index: 0;
    }

    .agency-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
    }

    .agency-logo {
        width: clamp(60px, 8vw, 80px);
        height: clamp(60px, 8vw, 80px);
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 12px;
        border: 2px solid var(--border);
        background: #F0F2F5;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(20px, 2.5vw, 28px);
        font-weight: 700;
        color: var(--muted);
        flex-shrink: 0;
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .agency-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .agency-name {
        font-family: var(--display);
        font-weight: 700;
        font-size: clamp(16px, 1.2vw, 18px);
        margin-bottom: 4px;
        word-break: break-word;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-wrap: wrap;
        gap: 6px;
        position: relative;
        z-index: 1;
    }

    .agency-location {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
        margin-bottom: 8px;
        word-break: break-word;
        position: relative;
        z-index: 1;
    }

    .agency-stats {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin: 10px 0;
        font-size: clamp(12px, 0.8vw, 13px);
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    .agency-stats span {
        display: flex;
        align-items: center;
        gap: 4px;
        color: var(--text-soft);
    }

    .agency-rating {
        color: #F5A623;
        font-weight: 700;
        font-size: clamp(14px, 1vw, 16px);
        margin-bottom: 10px;
        position: relative;
        z-index: 1;
    }

    .agency-description {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--text-soft);
        line-height: 1.6;
        margin-bottom: 14px;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        flex: 1;
        position: relative;
        z-index: 1;
    }

    .agency-actions {
        display: flex;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: auto;
        position: relative;
        z-index: 1;
    }

    .agency-actions .btn {
        flex: 1;
        min-width: 100px;
        justify-content: center;
    }

    /* ===== BADGES ===== */
    .badge-verified {
        display: inline-block;
        background: #E8F5E9;
        color: #1E7A47;
        padding: 2px 12px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11px);
        font-weight: 600;
        margin-left: 6px;
    }

    .badge-pending {
        display: inline-block;
        background: #FFF8E1;
        color: #E65100;
        padding: 2px 12px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11px);
        font-weight: 600;
        margin-left: 6px;
    }

    /* ✅ Badge Vedette sur l'agence */
    .badge-vedette-agency {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        background: #F5A623;
        box-shadow: 0 2px 8px rgba(245, 166, 35, 0.3);
        margin-left: 4px;
        animation: pulseVedette 2s ease-in-out infinite;
        position: relative;
        z-index: 1;
    }

    .badge-vedette-agency i {
        font-size: 9px;
    }

    /* ✅ Tag Vedette sur la carte */
    .vedette-tag-card {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        background: #F5A623;
        display: flex;
        align-items: center;
        gap: 4px;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(245, 166, 35, 0.3);
        animation: pulseVedette 2s ease-in-out infinite;
    }

    .vedette-tag-card i {
        font-size: 9px;
    }

    @keyframes pulseVedette {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
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
        margin: 0;
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

    /* ===== EMPTY STATE ===== */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
    }

    .empty-state i {
        font-size: 48px;
        display: block;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    .empty-state h3 {
        font-size: clamp(16px, 1.2vw, 18px);
        font-weight: 600;
        margin-bottom: 8px;
        color: var(--ink);
    }

    .empty-state p {
        font-size: clamp(13px, 0.9vw, 14px);
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

    /* ============================================
       RESPONSIVE
    ============================================ */

    @media (max-width: 820px) {
        .filter-section .filter-grid {
            gap: 10px;
        }

        .filter-section .filter-group {
            min-width: 120px;
        }

        .agencies-grid {
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 240px), 1fr));
            gap: 16px;
        }
    }

    @media (max-width: 640px) {
        .agencies-container {
            padding: 0 12px;
        }

        .page-head {
            padding: 16px 0 8px;
        }

        .filter-section {
            padding: 14px;
            border-radius: 10px;
        }

        .filter-section .filter-grid {
            flex-direction: column;
            gap: 8px;
        }

        .filter-section .filter-group {
            width: 100%;
            min-width: unset;
        }

        .filter-section .filter-group.filter-actions-group {
            width: 100%;
        }

        .filter-section .filter-group.filter-actions-group .btn {
            width: 100%;
            justify-content: center;
        }

        .filter-section .filter-group select,
        .filter-section .filter-group input {
            font-size: 16px !important;
            padding: 10px 12px;
        }

        .agencies-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .agency-card {
            padding: 14px 12px 16px;
        }

        .agency-logo {
            width: 50px;
            height: 50px;
            font-size: 16px;
        }

        .agency-name {
            font-size: 14px;
        }

        .agency-stats {
            gap: 10px;
            font-size: 11px;
            flex-wrap: wrap;
        }

        .agency-actions .btn {
            font-size: 10px;
            padding: 5px 10px;
            min-width: 80px;
        }

        .agency-description {
            font-size: 11px;
            -webkit-line-clamp: 2;
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

        .results-count {
            font-size: 11px;
            margin-bottom: 12px;
        }

        .badge-verified,
        .badge-pending {
            font-size: 9px;
            padding: 1px 8px;
        }

        .vedette-tag-card {
            font-size: 9px;
            padding: 3px 10px;
            top: 8px;
            right: 8px;
        }

        .vedette-tag-card i {
            font-size: 8px;
        }

        .badge-vedette-agency {
            font-size: 9px;
            padding: 1px 8px;
        }
    }

    @media (max-width: 460px) {
        .agencies-container {
            padding: 0 8px;
        }

        .agencies-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .agency-card {
            padding: 16px 14px 18px;
            max-width: 100%;
        }

        .agency-logo {
            width: 60px;
            height: 60px;
            font-size: 20px;
        }

        .agency-name {
            font-size: 16px;
        }

        .agency-actions .btn {
            font-size: 11px;
            padding: 6px 12px;
            min-width: 100px;
        }

        .filter-section {
            padding: 12px;
        }

        .filter-section .filter-group select,
        .filter-section .filter-group input {
            font-size: 16px !important;
            padding: 10px 12px;
        }

        .filter-section .filter-group select {
            padding-right: 26px;
        }
    }

    /* ===== ACCESSIBILITÉ ===== */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        .badge-vedette-agency,
        .vedette-tag-card {
            animation: none !important;
        }
    }
</style>

<div class="agencies-container" style="padding-top:30px;">
    <div class="page-head">
        <span class="eyebrow">Agences immobilières</span>
        <h1 class="h-hero">Nos agences partenaires</h1>
        <p class="lead">
            Découvrez les agences immobilières de confiance à Dakar. Chaque agence est vérifiée pour vous garantir un service de qualité.
        </p>
    </div>

    <!-- Filtres -->
    <div class="filter-section">
        <form action="{{ route('agences.public.index') }}" method="GET" class="filter-grid">
            <div class="filter-group">
                <label for="quartier">Quartier</label>
                <select name="quartier" id="quartier" onchange="this.form.submit()">
                    <option value="">Tous les quartiers</option>
                    @foreach($quartiers as $quartier)
                        <option value="{{ $quartier->id }}" {{ request('quartier') == $quartier->id ? 'selected' : '' }}>
                            {{ $quartier->nom }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="search">Rechercher</label>
                <input type="text" name="search" id="search" placeholder="Nom de l'agence..." value="{{ request('search') }}" onchange="this.form.submit()">
            </div>

            <div class="filter-group filter-actions-group">
                <label>&nbsp;</label>
                <a href="{{ route('agences.public.index') }}" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-times"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Résultats -->
    <div class="results-count">
        {{ $agences->total() }} agence(s) trouvée(s)
    </div>

    <div class="agencies-grid">
        @forelse($agences as $agence)
            @php
                $hasVedette = $agence->biens()->where('est_vedette', true)->exists();
            @endphp
            <div class="agency-card {{ $hasVedette ? 'has-vedette' : '' }}">
                @if($hasVedette)
                    <div class="vedette-tag-card">
                        <i class="fa-solid fa-star"></i> Vedette
                    </div>
                @endif

                <div class="agency-logo">
                    @if($agence->logo)
                        <img src="{{ asset('storage/' . $agence->logo) }}" alt="{{ $agence->nom_agence }}">
                    @else
                        {{ strtoupper(substr($agence->nom_agence, 0, 2)) }}
                    @endif
                </div>

                <div class="agency-name">
                    {{ $agence->nom_agence }}
                    @if($hasVedette)
                        <span class="badge-vedette-agency">
                            <i class="fa-solid fa-star"></i> Vedette
                        </span>
                    @endif
                    @if($agence->statut_validation)
                        <span class="badge-verified"> Validée</span>
                    @else
                        <span class="badge-pending"> En attente</span>
                    @endif
                </div>

                <div class="agency-location">
                    <i class="fa-solid fa-location-dot"></i>
                    {{ $agence->quartier->nom ?? 'Localisation non définie' }}
                </div>

                <div class="agency-rating">
                    @php
                        $note = $agence->evaluations->avg('note') ?? 0;
                    @endphp
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
                    @if($hasVedette)
                        <span style="color:#F5A623;">
                            <i class="fa-solid fa-star"></i> En vedette
                        </span>
                    @endif
                </div>

                @if($agence->description)
                    <div class="agency-description">
                        {{ Str::limit($agence->description, 120) }}
                    </div>
                @endif

                <div class="agency-actions">
                    <a href="{{ route('agences.public.show', $agence) }}" class="btn btn-rust btn-sm">
                        <i class="fa-solid fa-eye"></i> Voir l'agence
                    </a>
                    <a href="{{ route('agences.public.show', $agence) }}#contact" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-envelope"></i> Contacter
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa-solid fa-building-circle-exclamation"></i>
                <h3>Aucune agence trouvée</h3>
                <p>Aucune agence ne correspond à vos critères de recherche.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper">
        {{ $agences->appends(request()->query())->links() }}
    </div>
</div>
@endsection