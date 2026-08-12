@extends('layouts.admin')

@section('title', 'Gestion des quartiers — Administration DoyaImmo')
@section('page_title', 'Gestion des quartiers')
@section('page_sub', 'Gérez les quartiers de Dakar')

@section('content')
<style>
    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 768px) {
        .section-head {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        
        .section-head > div:first-child {
            text-align: center;
        }
        
        .section-head > div:last-child {
            display: flex;
            flex-direction: column !important;
            gap: 8px !important;
        }
        
        .section-head > div:last-child a {
            width: 100% !important;
            justify-content: center !important;
        }
        
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
        }
        
        .kpi-card {
            padding: 12px !important;
        }
        
        .kpi-value {
            font-size: 20px !important;
        }
        
        .table-wrap {
            overflow-x: auto !important;
        }
        
        .table-wrap table {
            min-width: 500px !important;
            font-size: 12px !important;
        }
        
        .table-wrap th,
        .table-wrap td {
            padding: 8px 10px !important;
        }
        
        .btn-sm {
            padding: 4px 8px !important;
            font-size: 11px !important;
        }
    }
    
    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        
        .kpi-card {
            padding: 10px !important;
        }
        
        .kpi-value {
            font-size: 16px !important;
        }
        
        .kpi-label {
            font-size: 10px !important;
        }
        
        .table-wrap table {
            min-width: 400px !important;
            font-size: 11px !important;
        }
        
        .table-wrap th,
        .table-wrap td {
            padding: 6px 8px !important;
        }
        
        .status-pill {
            font-size: 10px !important;
            padding: 2px 8px !important;
        }
        
        .section-head h2 {
            font-size: 16px !important;
        }
    }
</style>

<div class="section-head">
    <div>
        <h2>Quartiers</h2>
        <p>{{ $stats['total'] ?? 0 }} quartiers au total</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('admin.quartiers.create') }}" class="btn btn-rust btn-sm">
            <i class="fa-solid fa-plus"></i> Nouveau quartier
        </a>
        <a href="{{ route('admin.quartiers.export') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-download"></i> Exporter
        </a>
    </div>
</div>

<!-- Statistiques -->
<div class="kpi-grid" style="margin-bottom:24px;">
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="kpi-label">Total quartiers</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['actifs'] ?? 0 }}</div>
        <div class="kpi-label">Actifs</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['inactifs'] ?? 0 }}</div>
        <div class="kpi-label">Inactifs</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value">{{ count($stats['villes'] ?? []) }}</div>
        <div class="kpi-label">Villes</div>
    </div>
</div>

<!-- Filtres et recherche -->
<div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <form action="{{ route('admin.quartiers.index') }}" method="GET" style="display:flex;gap:8px;flex-wrap:wrap;flex:1;">
        <input type="text" name="search" placeholder="Rechercher un quartier..." 
               value="{{ request('search') }}" 
               style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;flex:1;min-width:150px;">
        
        <select name="ville" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            <option value="">Toutes les villes</option>
            @foreach($stats['villes'] ?? [] as $ville)
                <option value="{{ $ville }}" {{ request('ville') == $ville ? 'selected' : '' }}>
                    {{ $ville }}
                </option>
            @endforeach
        </select>
        
        <select name="est_actif" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            <option value="">Tous les statuts</option>
            <option value="1" {{ request('est_actif') === '1' ? 'selected' : '' }}>Actifs</option>
            <option value="0" {{ request('est_actif') === '0' ? 'selected' : '' }}>Inactifs</option>
        </select>
        
        <button type="submit" class="btn btn-sm btn-ghost">
            <i class="fa-solid fa-search"></i> Filtrer
        </button>
        @if(request('search') || request('ville') || request('est_actif'))
            <a href="{{ route('admin.quartiers.index') }}" class="btn btn-sm btn-ghost">
                <i class="fa-solid fa-times"></i> Réinitialiser
            </a>
        @endif
    </form>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Ville</th>
                <th>Demandes</th>
                <th>Biens</th>
                <th>Statut</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quartiers as $quartier)
                <tr>
                    <td>
                        <div class="cell-main">{{ $quartier->nom }}</div>
                        <div class="cell-sub">ID: #{{ $quartier->id }}</div>
                    </td>
                    <td>{{ $quartier->ville }}</td>
                    <td>
                        <span style="font-weight:600;">{{ $quartier->demandes_count ?? $quartier->demandes()->count() }}</span>
                        <span style="font-size:11px;color:var(--muted);">demandes</span>
                    </td>
                    <td>
                        <span style="font-weight:600;">{{ $quartier->biens_count ?? $quartier->biens()->count() }}</span>
                        <span style="font-size:11px;color:var(--muted);">biens</span>
                    </td>
                    <td>
                        @if($quartier->est_actif)
                            <span class="status-pill status-active"> Actif</span>
                        @else
                            <span class="status-pill status-inactif"> Inactif</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                            <a href="{{ route('admin.quartiers.show', $quartier) }}" class="btn btn-sm btn-ghost" title="Voir">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.quartiers.edit', $quartier) }}" class="btn btn-sm btn-ghost" title="Modifier">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <form action="{{ route('admin.quartiers.toggle', $quartier) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-ghost" title="{{ $quartier->est_actif ? 'Désactiver' : 'Activer' }}">
                                    <i class="fa-solid {{ $quartier->est_actif ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>
                            @php
                                $hasRelations = ($quartier->demandes()->count() > 0 || $quartier->biens()->count() > 0 || $quartier->agences()->count() > 0);
                            @endphp
                            @if(!$hasRelations)
                                <form action="{{ route('admin.quartiers.destroy', $quartier) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer définitivement ce quartier ?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-solid fa-location-dot" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Aucun quartier trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ==================== PAGINATION RÉORGANISÉE ==================== -->
@if($quartiers->hasPages())
<div class="pagination-container">
    <nav class="pagination-nav" aria-label="Pagination des quartiers">
        {{-- Informations de pagination --}}
        <div class="pagination-info">
            <span class="pagination-stats">
                <i class="fa-solid fa-paper-plane" style="color:var(--rust);"></i>
                Affichage de <strong>{{ $quartiers->firstItem() }}</strong> à <strong>{{ $quartiers->lastItem() }}</strong> 
                sur <strong>{{ $quartiers->total() }}</strong> quartiers
            </span>
        </div>

        {{-- Liens de pagination --}}
        <ul class="pagination">
            {{-- Lien "Précédent" --}}
            @if($quartiers->onFirstPage())
                <li class="disabled" aria-disabled="true">
                    <span><i class="fa-solid fa-chevron-left"></i> Précédent</span>
                </li>
            @else
                <li>
                    <a href="{{ $quartiers->previousPageUrl() }}" rel="prev" aria-label="Page précédente">
                        <i class="fa-solid fa-chevron-left"></i> Précédent
                    </a>
                </li>
            @endif

            {{-- Éléments de pagination --}}
            @php
                $currentPage = $quartiers->currentPage();
                $lastPage = $quartiers->lastPage();
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
                            <a href="{{ $quartiers->url($page) }}" aria-label="Page {{ $page }}">
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

            {{-- Lien "Suivant" --}}
            @if($quartiers->hasMorePages())
                <li>
                    <a href="{{ $quartiers->nextPageUrl() }}" rel="next" aria-label="Page suivante">
                        Suivant <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="disabled" aria-disabled="true">
                    <span>Suivant <i class="fa-solid fa-chevron-right"></i></span>
                </li>
            @endif
        </ul>

        {{-- Sélecteur de nombre d'éléments par page --}}
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

@push('styles')
<style>
    /* ===== KPI CARDS ===== */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
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

    /* ===== STATUS PILLS ===== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-active {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-inactif {
        background: #F5F5F5;
        color: var(--muted);
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
    }

    .table-wrap tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .table-wrap tbody tr:hover {
        background: #FAFBFD;
    }

    .cell-main {
        font-weight: 600;
        color: var(--ink);
    }
    .cell-sub {
        font-size: 12px;
        color: var(--muted);
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
        padding: 5px 12px;
        font-size: 12px;
        border-radius: 6px;
    }

    .btn-danger {
        background: #C62828;
        color: #fff;
        border: none;
    }
    .btn-danger:hover {
        background: #9A1E1E;
        color: #fff;
    }

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

    /* ============================================
       PAGINATION RÉORGANISÉE AVEC PAPER PLANE
    ============================================ */
    .pagination-container {
        margin-top: 24px;
    }

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

    .pagination-info {
        width: 100%;
        text-align: center;
    }

    .pagination-stats {
        font-size: clamp(12px, 0.8vw, 14px);
        color: var(--text-soft);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .pagination-stats strong {
        color: var(--ink);
        font-weight: 700;
    }

    .pagination-stats .fa-paper-plane {
        font-size: 14px;
    }

    /* ===== PAGINATION LINKS ===== */
    .pagination {
        display: flex;
        gap: 4px;
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: clamp(6px, 0.5vw, 8px) clamp(10px, 0.8vw, 14px);
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: clamp(12px, 0.8vw, 13px);
        transition: all 0.2s ease;
        min-width: clamp(32px, 3vw, 40px);
        min-height: clamp(32px, 3vw, 40px);
        text-align: center;
        background: #fff;
        font-weight: 500;
    }

    .pagination a:hover {
        background: var(--border);
        border-color: var(--border);
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

    .pagination .disabled span:hover {
        transform: none;
        background: #f7f7f7;
    }

    .pagination a[rel="prev"],
    .pagination a[rel="next"] {
        font-weight: 600;
        padding: clamp(6px, 0.5vw, 8px) clamp(12px, 0.8vw, 16px);
    }

    .pagination a[rel="prev"] i,
    .pagination a[rel="next"] i,
    .pagination .disabled span i {
        font-size: 11px;
    }

    /* ===== PER PAGE SELECTOR ===== */
    .pagination-per-page {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: clamp(12px, 0.8vw, 13px);
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

    .per-page-label .fa-paper-plane {
        font-size: 13px;
    }

    .per-page-select {
        padding: 5px 24px 5px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: clamp(12px, 0.8vw, 13px);
        font-family: inherit;
        color: var(--ink);
        background: #fff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
        transition: border-color 0.3s;
    }

    .per-page-select:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }

    .per-page-text {
        color: var(--muted);
    }

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
        .kpi-card {
            padding: 12px 16px;
        }
        .kpi-value {
            font-size: 20px;
        }

        /* Filtres responsive */
        [style*="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;"] {
            flex-direction: column;
            align-items: stretch !important;
        }
        [style*="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;"] form {
            flex-direction: column !important;
        }
        [style*="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;"] input,
        [style*="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;"] select {
            width: 100% !important;
            min-width: 0 !important;
        }

        .table-wrap table {
            min-width: 500px;
            font-size: 12px;
        }
        .table-wrap thead th,
        .table-wrap tbody td {
            padding: 8px 10px;
        }

        /* Pagination responsive */
        .pagination-nav {
            padding: 14px 16px;
            gap: 14px;
        }

        .pagination a,
        .pagination span {
            padding: 4px 8px;
            font-size: 11px;
            min-width: 28px;
            min-height: 28px;
            border-radius: 6px;
        }

        .pagination a[rel="prev"],
        .pagination a[rel="next"] {
            font-size: 11px;
            padding: 4px 10px;
        }

        .pagination a[rel="prev"] i,
        .pagination a[rel="next"] i {
            font-size: 10px;
        }

        .pagination-per-page {
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            padding-top: 12px;
        }

        .per-page-select {
            font-size: 12px;
            padding: 4px 20px 4px 10px;
        }

        .pagination-stats {
            font-size: 12px;
        }

        .pagination-stats .fa-paper-plane {
            font-size: 12px;
        }

        /* Actions sur mobile */
        td:last-child .btn {
            padding: 4px 8px;
            font-size: 11px;
        }
        td:last-child [style*="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;"] {
            gap: 3px !important;
        }
    }

    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .kpi-card {
            padding: 10px 14px;
        }
        .kpi-value {
            font-size: 18px;
        }

        .pagination a,
        .pagination span {
            padding: 3px 6px;
            font-size: 10px;
            min-width: 24px;
            min-height: 24px;
        }

        .pagination a[rel="prev"],
        .pagination a[rel="next"] {
            font-size: 10px;
            padding: 3px 8px;
        }

        .pagination-nav {
            padding: 10px 12px;
        }

        .pagination-per-page {
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        .pagination a,
        .pagination span {
            transition: none !important;
        }
        .btn {
            transition: none !important;
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
@endsection