@extends('layouts.admin')

@section('title', 'Gestion des propositions — Administration DoyaImmo')
@section('page_title', 'Propositions')
@section('page_sub', 'Consultez les propositions des agences')

@section('content')
<div class="section-head">
    <div>
        <h2>Propositions</h2>
        <p>{{ $propositions->total() }} propositions sur la plateforme</p>
    </div>
</div>

<!-- Statistiques -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="kpi-label">Total</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#E65100;">{{ $stats['en_attente'] ?? 0 }}</div>
        <div class="kpi-label">En attente</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['acceptees'] ?? 0 }}</div>
        <div class="kpi-label">Acceptées</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['refusees'] ?? 0 }}</div>
        <div class="kpi-label">Refusées</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#0D47A1;">{{ $stats['terminees'] ?? 0 }}</div>
        <div class="kpi-label">Terminées</div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-bar">
    <div class="filters-bar__tabs">
        <a href="{{ route('admin.propositions.index', array_filter(['search' => request('search')])) }}"
           class="btn btn-sm {{ !request('filtre') ? 'btn-rust' : 'btn-ghost' }}">
            Toutes
        </a>
        <a href="{{ route('admin.propositions.index', array_filter(['filtre' => 'en_attente', 'search' => request('search')])) }}"
           class="btn btn-sm {{ request('filtre') === 'en_attente' ? 'btn-rust' : 'btn-ghost' }}">
            En attente
        </a>
        <a href="{{ route('admin.propositions.index', array_filter(['filtre' => 'acceptee', 'search' => request('search')])) }}"
           class="btn btn-sm {{ request('filtre') === 'acceptee' ? 'btn-rust' : 'btn-ghost' }}">
            Acceptées
        </a>
        <a href="{{ route('admin.propositions.index', array_filter(['filtre' => 'refusee', 'search' => request('search')])) }}"
           class="btn btn-sm {{ request('filtre') === 'refusee' ? 'btn-rust' : 'btn-ghost' }}">
            Refusées
        </a>
        <a href="{{ route('admin.propositions.index', array_filter(['filtre' => 'terminee', 'search' => request('search')])) }}"
           class="btn btn-sm {{ request('filtre') === 'terminee' ? 'btn-rust' : 'btn-ghost' }}">
            Terminées
        </a>
    </div>

    <!-- Recherche -->
    <form action="{{ route('admin.propositions.index') }}" method="GET" class="filters-bar__search">
        @if(request('filtre'))
            <input type="hidden" name="filtre" value="{{ request('filtre') }}">
        @endif

        <input type="text" name="search" placeholder="Rechercher une agence, un bien, un client..."
               value="{{ request('search') }}"
               class="filters-bar__input">

        <button type="submit" class="btn btn-sm btn-ghost" title="Rechercher">
            <i class="fa-solid fa-search"></i>
        </button>

        @if(request('search') || request('filtre'))
            <a href="{{ route('admin.propositions.index') }}" class="btn btn-sm btn-ghost" title="Réinitialiser">
                <i class="fa-solid fa-times"></i>
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Agence</th>
                <th>Demande</th>
                <th>Bien proposé</th>
                <th>Prix proposé</th>
                <th>Statut</th>
                <th>Date</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($propositions as $proposition)
                <tr>
                    <td>
                        <div class="cell-user">
                            @if($proposition->agence && $proposition->agence->logo)
                                <img src="{{ asset('storage/' . $proposition->agence->logo) }}"
                                     alt="{{ $proposition->agence->nom_agence }}"
                                     class="cell-user__avatar">
                            @else
                                <div class="cell-user__avatar cell-user__avatar--fallback">
                                    {{ $proposition->agence ? Str::substr($proposition->agence->nom_agence, 0, 2) : 'NA' }}
                                </div>
                            @endif
                            <div>
                                <div class="cell-main">{{ $proposition->agence->nom_agence ?? 'N/A' }}</div>
                                <div class="cell-sub">{{ $proposition->agence->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="cell-main">
                                {{ is_object($proposition->demande->type_operation) ? $proposition->demande->type_operation->label() : ($proposition->demande->type_operation ?? 'N/A') }}
                            </div>
                            <div class="cell-sub">
                                <i class="fa-solid fa-user"></i>
                                {{ $proposition->demande->particulier->user->prenom ?? '' }}
                                {{ $proposition->demande->particulier->user->nom ?? '' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $bien = $proposition->bien;
                            $titre = $bien->titre ?? 'N/A';
                            $contrat = is_object($bien->type_contrat)
                                ? $bien->type_contrat->label()
                                : ($bien->type_contrat ?? '');
                            $quartierNom = $bien->quartier_nom ?? ($bien->quartier ?? 'N/A');
                        @endphp
                        <div>
                            <div class="cell-main">
                                {{ $titre }}
                                @if($contrat && $contrat !== 'N/A')
                                    <span class="meta-pill" style="background:#E3F2FD;color:#0D47A1;">
                                        {{ $contrat }}
                                    </span>
                                @endif
                            </div>
                            <div class="cell-sub">
                                <i class="fa-solid fa-location-dot"></i> {{ $quartierNom }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--rust);">
                            {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                        </div>
                    </td>
                    <td>
                        @php
                            $statutValue = is_object($proposition->statut)
                                ? $proposition->statut->value
                                : (string) $proposition->statut;
                            $statutLabel = is_object($proposition->statut)
                                ? $proposition->statut->label()
                                : ucfirst($statutValue);
                        @endphp
                        <span class="status-pill status-{{ $statutValue }}">
                            {{ $statutLabel }}
                        </span>
                    </td>
                    <td>{{ $proposition->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.propositions.show', $proposition) }}"
                           class="btn btn-sm btn-ghost" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="table-empty">
                        <i class="fa-solid fa-handshake"></i>
                        Aucune proposition trouvée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($propositions->hasPages())
<div class="pagination-container">
    <nav class="pagination-nav" aria-label="Pagination des propositions">
        <div class="pagination-info">
            <span class="pagination-stats">
                <i class="fa-solid fa-paper-plane" style="color:var(--rust);"></i>
                Affichage de <strong>{{ $propositions->firstItem() }}</strong> à <strong>{{ $propositions->lastItem() }}</strong>
                sur <strong>{{ $propositions->total() }}</strong> propositions
            </span>
        </div>

        <ul class="pagination">
            @if($propositions->onFirstPage())
                <li class="disabled" aria-disabled="true">
                    <span><i class="fa-solid fa-chevron-left"></i> Précédent</span>
                </li>
            @else
                <li>
                    <a href="{{ $propositions->previousPageUrl() }}" rel="prev" aria-label="Page précédente">
                        <i class="fa-solid fa-chevron-left"></i> Précédent
                    </a>
                </li>
            @endif

            @php
                $currentPage = $propositions->currentPage();
                $lastPage = $propositions->lastPage();
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
                            <a href="{{ $propositions->url($page) }}" aria-label="Page {{ $page }}">
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

            @if($propositions->hasMorePages())
                <li>
                    <a href="{{ $propositions->nextPageUrl() }}" rel="next" aria-label="Page suivante">
                        Suivant <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="disabled" aria-disabled="true">
                    <span>Suivant <i class="fa-solid fa-chevron-right"></i></span>
                </li>
            @endif
        </ul>

        <div class="pagination-per-page">
            <span class="per-page-label">
                <i class="fa-solid fa-paper-plane" style="color:var(--rust);font-size:12px;"></i>
                Afficher :
            </span>
            <select id="perPage" class="per-page-select" onchange="changePerPage(this.value)">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="per-page-text">par page</span>
        </div>
    </nav>
</div>
@endif
@endsection


@push('styles')
<style>
    /* ===== SECTION HEAD ===== */
    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .section-head h2 {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }
    .section-head p {
        font-size: 13px;
        color: var(--muted);
        margin: 2px 0 0 0;
    }

    /* ===== KPI CARDS ===== */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .kpi-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        text-align: center;
    }
    .kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--ink);
    }
    .kpi-label {
        font-size: 13px;
        color: var(--muted);
        margin-top: 2px;
    }

    /* ===== FILTERS BAR ===== */
    .filters-bar {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 16px;
    }
    .filters-bar__tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }
    .filters-bar__search {
        display: flex;
        gap: 8px;
        margin-left: auto;
        align-items: center;
    }
    .filters-bar__input {
        padding: 6px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        min-width: 220px;
        font-family: inherit;
    }
    .filters-bar__input:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px var(--rust-soft);
    }

    /* ===== STATUS PILLS ===== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }
    .status-en_attente { background: #FFF8E1; color: #E65100; }
    .status-acceptee   { background: #E8F5E9; color: #1E7A47; }
    .status-refusee    { background: #FFEBEE; color: #C62828; }
    .status-terminee   { background: #E3F2FD; color: #0D47A1; }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 10px;
        color: var(--text-soft);
    }

    /* ===== TABLE ===== */
    .table-wrap {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow-x: auto;
    }
    .table-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .table-wrap thead th {
        background: #F7F9FC;
        text-align: left;
        padding: 12px 16px;
        font-weight: 600;
        color: var(--text-soft);
        border-bottom: 1px solid var(--border);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .table-wrap tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .table-wrap tbody tr:hover {
        background: #FAFBFD;
    }
    .table-empty {
        text-align: center;
        padding: 40px;
        color: var(--muted);
    }
    .table-empty i {
        font-size: 32px;
        display: block;
        margin-bottom: 12px;
        opacity: 0.5;
    }

    .cell-main { font-weight: 600; color: var(--ink); }
    .cell-sub  { font-size: 12px; color: var(--muted); }

    /* Cellule utilisateur (avatar + infos) */
    .cell-user {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .cell-user__avatar {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border-radius: 50%;
        border: 1px solid var(--border);
        flex-shrink: 0;
    }
    .cell-user__avatar--fallback {
        background: var(--gold-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        color: var(--gold);
        border: none;
    }

    /* ===== BUTTONS ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        white-space: nowrap;
    }
    .btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
    .btn-rust:hover { background: #9A4523; border-color: #9A4523; color: #fff; }

    .btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
    .btn-ghost:hover { background: var(--border); color: var(--ink); }

    .btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 6px; }

    /* ===== PAGINATION ===== */
    .pagination-container { margin-top: 24px; }
    .pagination-nav {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        align-items: center;
    }
    .pagination-info { width: 100%; text-align: center; }
    .pagination-stats {
        font-size: 13px;
        color: var(--text-soft);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .pagination-stats strong { color: var(--ink); font-weight: 700; }

    .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        justify-content: center;
    }
    .pagination li { display: inline; }
    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: 7px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s ease;
        min-width: 36px;
        min-height: 36px;
        text-align: center;
        background: #fff;
        font-weight: 500;
    }
    .pagination a:hover {
        background: var(--border);
        color: var(--ink);
        transform: translateY(-1px);
    }
    .pagination .active span {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
        box-shadow: 0 2px 8px rgba(181, 80, 42, 0.25);
    }
    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f7f7f7;
    }

    .pagination-per-page {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-soft);
        border-top: 1px solid var(--border);
        padding-top: 14px;
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
    }
    .per-page-label {
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .per-page-select {
        padding: 5px 24px 5px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
        color: var(--ink);
        background: #fff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
    }
    .per-page-select:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }
    .per-page-text { color: var(--muted); }

    /* ============================================
       RESPONSIVE
    ============================================ */
    @media (max-width: 768px) {
        .section-head {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }
        .kpi-card { padding: 12px 16px; }
        .kpi-value { font-size: 20px; }

        .filters-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .filters-bar__search {
            margin-left: 0;
            width: 100%;
        }
        .filters-bar__input {
            min-width: 0;
            flex: 1;
        }

        .table-wrap table { font-size: 12px; }
        .table-wrap thead th,
        .table-wrap tbody td { padding: 8px 10px; }

        .pagination-nav { padding: 14px 16px; gap: 14px; }
        .pagination a,
        .pagination span {
            padding: 5px 9px;
            font-size: 11px;
            min-width: 30px;
            min-height: 30px;
            border-radius: 6px;
        }
        .pagination-per-page {
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
        }
    }

    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .kpi-card { padding: 10px 14px; }
        .kpi-value { font-size: 18px; }

        .pagination a,
        .pagination span {
            padding: 4px 7px;
            font-size: 10px;
            min-width: 26px;
            min-height: 26px;
        }
        .pagination-nav { padding: 10px 12px; }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
@endpush


@push('scripts')
<script>
    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }
</script>
@endpush