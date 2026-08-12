@extends('layouts.admin')

@section('title', 'Gestion des demandes — Administration DoyaImmo')
@section('page_title', 'Demandes immobilières')
@section('page_sub', 'Gérez les demandes des particuliers')

@section('content')
<div class="section-head">
    <div>
        <h2>Demandes</h2>
        <p>{{ $demandes->total() }} demandes publiées sur la plateforme</p>
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
        <div class="kpi-value" style="color:#0D47A1;">{{ $stats['en_cours'] ?? 0 }}</div>
        <div class="kpi-label">En cours</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['terminees'] ?? 0 }}</div>
        <div class="kpi-label">Terminées</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['annulees'] ?? 0 }}</div>
        <div class="kpi-label">Annulées</div>
    </div>
</div>

<!-- Filtres -->
<div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <a href="{{ route('admin.demandes.index') }}" class="btn btn-sm {{ !request('filtre') ? 'btn-rust' : 'btn-ghost' }}">Toutes</a>
    <a href="{{ route('admin.demandes.index', ['filtre' => 'en_attente']) }}" class="btn btn-sm {{ request('filtre') === 'en_attente' ? 'btn-rust' : 'btn-ghost' }}">En attente</a>
    <a href="{{ route('admin.demandes.index', ['filtre' => 'en_cours']) }}" class="btn btn-sm {{ request('filtre') === 'en_cours' ? 'btn-rust' : 'btn-ghost' }}">En cours</a>
    <a href="{{ route('admin.demandes.index', ['filtre' => 'terminee']) }}" class="btn btn-sm {{ request('filtre') === 'terminee' ? 'btn-rust' : 'btn-ghost' }}">Terminées</a>
    <a href="{{ route('admin.demandes.index', ['filtre' => 'annulee']) }}" class="btn btn-sm {{ request('filtre') === 'annulee' ? 'btn-rust' : 'btn-ghost' }}">Annulées</a>
    
    <!-- Recherche -->
    <form action="{{ route('admin.demandes.index') }}" method="GET" style="display:flex;gap:8px;margin-left:auto;">
        <input type="text" name="search" placeholder="Rechercher..." 
               value="{{ request('search') }}" 
               style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;min-width:200px;">
        <button type="submit" class="btn btn-sm btn-ghost">
            <i class="fa-solid fa-search"></i>
        </button>
        @if(request('search') || request('filtre'))
            <a href="{{ route('admin.demandes.index') }}" class="btn btn-sm btn-ghost">
                <i class="fa-solid fa-times"></i> Réinitialiser
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Demande</th>
                <th>Particulier</th>
                <th>Type</th>
                <th>Budget max</th>
                <th>Zone</th>
                <th>Statut</th>
                <th>Propositions</th>
                <th>Date</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($demandes as $demande)
                <tr>
                    <td>
                        <div>
                            <div class="cell-main">
                                {{ $demande->type_operation_label ?? 'N/A' }} - {{ $demande->type_bien_label ?? 'N/A' }}
                            </div>
                            <div class="cell-sub">
                                <i class="fa-solid fa-location-dot"></i> {{ $demande->quartier->nom ?? 'N/A' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div style="width:32px;height:32px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--rust);">
                                {{ strtoupper(substr($demande->particulier->user->prenom ?? 'U', 0, 1)) }}{{ strtoupper(substr($demande->particulier->user->nom ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="cell-main">{{ $demande->particulier->user->prenom ?? '' }} {{ $demande->particulier->user->nom ?? '' }}</div>
                                <div class="cell-sub">{{ $demande->particulier->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="meta-pill">{{ $demande->type_bien_label ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--rust);">
                            {{ number_format($demande->budget_maximum ?? 0, 0, ',', ' ') }} FCFA
                        </div>
                    </td>
                    <td>
                        <span class="meta-pill">{{ $demande->zone_recherchee ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <span class="status-pill status-{{ $demande->statut }}">
                            {{ $demande->statut_label ?? $demande->statut }}
                        </span>
                    </td>
                    <td>
                        <span style="font-weight:600;">{{ $demande->propositions->count() }}</span>
                        <span style="font-size:11px;color:var(--muted);">offres</span>
                    </td>
                    <td>{{ $demande->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                            <a href="{{ route('admin.demandes.show', $demande) }}" class="btn btn-sm btn-ghost" title="Voir">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <form action="{{ route('admin.demandes.destroy', $demande) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer définitivement cette demande ?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-solid fa-house-circle-exclamation" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Aucune demande trouvée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ==================== PAGINATION RÉORGANISÉE ==================== -->
@if($demandes->hasPages())
<div class="pagination-container">
    <nav class="pagination-nav" aria-label="Pagination des demandes">
        {{-- Informations de pagination --}}
        <div class="pagination-info">
            <span class="pagination-stats">
                <i class="fa-solid fa-paper-plane" style="color:var(--rust);"></i>
                Affichage de <strong>{{ $demandes->firstItem() }}</strong> à <strong>{{ $demandes->lastItem() }}</strong> 
                sur <strong>{{ $demandes->total() }}</strong> demandes
            </span>
        </div>

        {{-- Liens de pagination --}}
        <ul class="pagination">
            {{-- Lien "Précédent" --}}
            @if($demandes->onFirstPage())
                <li class="disabled" aria-disabled="true">
                    <span><i class="fa-solid fa-chevron-left"></i> Précédent</span>
                </li>
            @else
                <li>
                    <a href="{{ $demandes->previousPageUrl() }}" rel="prev" aria-label="Page précédente">
                        <i class="fa-solid fa-chevron-left"></i> Précédent
                    </a>
                </li>
            @endif

            {{-- Éléments de pagination --}}
            @php
                $currentPage = $demandes->currentPage();
                $lastPage = $demandes->lastPage();
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
                            <a href="{{ $demandes->url($page) }}" aria-label="Page {{ $page }}">
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
            @if($demandes->hasMorePages())
                <li>
                    <a href="{{ $demandes->nextPageUrl() }}" rel="next" aria-label="Page suivante">
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
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-en_cours {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-terminee {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-annulee {
        background: #FFEBEE;
        color: #C62828;
    }

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
            grid-template-columns: repeat(3, 1fr);
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
        [style*="margin-left:auto;"] {
            margin-left: 0 !important;
            width: 100%;
        }
        [style*="margin-left:auto;"] input {
            min-width: 0 !important;
            flex: 1;
        }

        .table-wrap table {
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