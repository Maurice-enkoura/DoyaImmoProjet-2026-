@extends('layouts.dashboard-agence')

@section('title', 'Historique — DoyaImmo')
@section('page_title', 'Historique des activités')
@section('page_sub', 'Toutes vos activités en un seul endroit')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Historique des activités</h2>
            <p>Consultez l'historique complet de vos activités</p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('agence.dashboard') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    @if($activites->count() > 0)
        <!-- Liste des activités -->
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
            @foreach($activites as $activite)
                <div style="display:flex;align-items:center;gap:16px;padding:14px 20px;border-bottom:1px solid var(--border);transition:background 0.2s;">
                    
                    <!-- Icône selon le type -->
                    <div style="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:16px;
                        @if($activite['type'] === 'proposition') background:#FFF3E0;color:#E65100;
                        @elseif($activite['type'] === 'rendezvous') background:#E3F2FD;color:#0D47A1;
                        @elseif($activite['type'] === 'evaluation') background:#E8F5E9;color:#1E7A47;
                        @else background:#F5F5F5;color:var(--muted);
                        @endif
                    ">
                        @if($activite['type'] === 'proposition')
                            <i class="fa-solid fa-paper-plane"></i>
                        @elseif($activite['type'] === 'rendezvous')
                            <i class="fa-regular fa-calendar-check"></i>
                        @elseif($activite['type'] === 'evaluation')
                            <i class="fa-solid fa-star"></i>
                        @else
                            <i class="fa-solid fa-circle"></i>
                        @endif
                    </div>

                    <!-- Informations -->
                    <div style="flex:1;min-width:150px;">
                        <div style="font-weight:600;font-size:15px;color:var(--ink);">
                            {{ $activite['titre'] }}
                        </div>
                        <div style="font-size:13px;color:var(--muted);">
                            {{ $activite['description'] }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                            <i class="fa-regular fa-clock"></i> 
                            {{ \Carbon\Carbon::parse($activite['date'])->format('d/m/Y à H:i') }}
                        </div>
                    </div>

                    <!-- Statut -->
                    <div style="flex-shrink:0;">
                        @if($activite['type'] === 'evaluation')
                            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:600;
                                @if($activite['statut_class'] === 'success') background:#E8F5E9;color:#1E7A47;
                                @else background:#F5F5F5;color:var(--muted);
                                @endif
                            ">
                                <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                                {{ $activite['statut'] }}
                            </span>
                        @else
                            <span class="status-pill status-{{ $activite['statut_class'] }}">
                                <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                                {{ $activite['statut'] }}
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- ==================== PAGINATION RÉORGANISÉE ==================== -->
        @if($activites->hasPages())
        <div class="pagination-container">
            <nav class="pagination-nav" aria-label="Pagination de l'historique">
                {{-- Informations de pagination --}}
                <div class="pagination-info">
                    <span class="pagination-stats">
                        <i class="fa-solid fa-paper-plane" style="color:var(--rust);"></i>
                        Affichage de <strong>{{ $activites->firstItem() }}</strong> à <strong>{{ $activites->lastItem() }}</strong> 
                        sur <strong>{{ $activites->total() }}</strong> activités
                    </span>
                </div>

                {{-- Liens de pagination --}}
                <ul class="pagination">
                    {{-- Lien "Précédent" --}}
                    @if($activites->onFirstPage())
                        <li class="disabled" aria-disabled="true">
                            <span><i class="fa-solid fa-chevron-left"></i> Précédent</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $activites->previousPageUrl() }}" rel="prev" aria-label="Page précédente">
                                <i class="fa-solid fa-chevron-left"></i> Précédent
                            </a>
                        </li>
                    @endif

                    {{-- Éléments de pagination --}}
                    @php
                        $currentPage = $activites->currentPage();
                        $lastPage = $activites->lastPage();
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
                                    <a href="{{ $activites->url($page) }}" aria-label="Page {{ $page }}">
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
                    @if($activites->hasMorePages())
                        <li>
                            <a href="{{ $activites->nextPageUrl() }}" rel="next" aria-label="Page suivante">
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
    @else
        <!-- Message vide -->
        <div style="text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
            <i class="fa-regular fa-clock" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.3;"></i>
            <p style="font-size:16px;font-weight:600;color:var(--text-soft);">Aucune activité</p>
            <p style="font-size:13px;max-width:400px;margin:4px auto 16px;">
                Vous n'avez pas encore d'activités. Commencez à publier des biens ou à répondre aux demandes.
            </p>
            <a href="{{ route('agence.biens.index') }}" class="btn btn-rust">
                <i class="fa-solid fa-plus"></i> Publier un bien
            </a>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* ===== STATUS PILLS ===== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-success {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-warning {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-info {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-danger {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-default {
        background: #F5F5F5;
        color: var(--muted);
    }

    /* ===== BUTTONS ===== */
    .btn-rust {
        background: var(--rust);
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
    }
    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border: 1px solid var(--border);
        padding: 8px 18px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-ghost:hover {
        background: var(--border);
    }
    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
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

    @media (max-width: 640px) {
        .section-head {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px;
        }
        .section-head .btn {
            justify-content: center;
        }

        /* Responsive activité cards */
        [style*="display:flex;align-items:center;gap:16px;padding:14px 20px;border-bottom:1px solid var(--border);"] {
            flex-direction: column;
            align-items: stretch !important;
            gap: 8px !important;
        }
        [style*="width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;"] {
            align-self: center !important;
        }
        [style*="flex-shrink:0;"] {
            align-self: center !important;
        }

        /* Responsive pagination */
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
    }

    @media (max-width: 400px) {
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