@extends('layouts.admin')

@section('title', 'Gestion des rendez-vous — Administration DoyaImmo')
@section('page_title', 'Rendez-vous')
@section('page_sub', 'Consultez les rendez-vous programmés')

@section('content')
<div class="section-head">
    <div>
        <h2>Rendez-vous</h2>
        <p>{{ $rendezVous->total() }} rendez-vous sur la plateforme</p>
    </div>
</div>

<!-- Statistiques -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="kpi-label">Total</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#E65100;">{{ $stats['planifies'] ?? 0 }}</div>
        <div class="kpi-label">Planifiés</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#0D47A1;">{{ $stats['confirmes'] ?? 0 }}</div>
        <div class="kpi-label">Confirmés</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['termines'] ?? 0 }}</div>
        <div class="kpi-label">Terminés</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['annules'] ?? 0 }}</div>
        <div class="kpi-label">Annulés</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#6C6C70;">{{ $stats['client_absent'] ?? 0 }}</div>
        <div class="kpi-label">Client absent</div>
    </div>
</div>

<!-- Filtres -->
<div class="filters-bar">
    <div class="filters-bar__tabs">
        <a href="{{ route('admin.rendezvous.index', array_filter(['search' => request('search')])) }}"
           class="btn btn-sm {{ !request('filtre') ? 'btn-rust' : 'btn-ghost' }}">
            Tous
        </a>
        <a href="{{ route('admin.rendezvous.index', array_filter(['filtre' => 'planifie', 'search' => request('search')])) }}"
           class="btn btn-sm {{ request('filtre') === 'planifie' ? 'btn-rust' : 'btn-ghost' }}">
            Planifiés
        </a>
        <a href="{{ route('admin.rendezvous.index', array_filter(['filtre' => 'confirme', 'search' => request('search')])) }}"
           class="btn btn-sm {{ request('filtre') === 'confirme' ? 'btn-rust' : 'btn-ghost' }}">
            Confirmés
        </a>
        <a href="{{ route('admin.rendezvous.index', array_filter(['filtre' => 'termine', 'search' => request('search')])) }}"
           class="btn btn-sm {{ request('filtre') === 'termine' ? 'btn-rust' : 'btn-ghost' }}">
            Terminés
        </a>
        <a href="{{ route('admin.rendezvous.index', array_filter(['filtre' => 'annule', 'search' => request('search')])) }}"
           class="btn btn-sm {{ request('filtre') === 'annule' ? 'btn-rust' : 'btn-ghost' }}">
            Annulés
        </a>
        <a href="{{ route('admin.rendezvous.index', array_filter(['filtre' => 'client_absent', 'search' => request('search')])) }}"
           class="btn btn-sm {{ request('filtre') === 'client_absent' ? 'btn-rust' : 'btn-ghost' }}">
            Client absent
        </a>
    </div>

    <!-- Recherche -->
    <form action="{{ route('admin.rendezvous.index') }}" method="GET" class="filters-bar__search">
        @if(request('filtre'))
            <input type="hidden" name="filtre" value="{{ request('filtre') }}">
        @endif

        <input type="text" name="search"
               placeholder="Rechercher agence, client, bien, code..."
               value="{{ request('search') }}"
               class="filters-bar__input">

        <button type="submit" class="btn btn-sm btn-ghost" title="Rechercher">
            <i class="fa-solid fa-search"></i>
        </button>

        @if(request('search') || request('filtre'))
            <a href="{{ route('admin.rendezvous.index') }}" class="btn btn-sm btn-ghost" title="Réinitialiser">
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
                <th>Particulier</th>
                <th>Bien / Proposition</th>
                <th>Date & Heure</th>
                <th>Code</th>
                <th>Statut</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rendezVous as $rdv)
                @php
                    $statutValue = is_object($rdv->statut)
                        ? $rdv->statut->value
                        : (string) $rdv->statut;
                    $statutLabel = is_object($rdv->statut)
                        ? $rdv->statut->label()
                        : ucfirst($statutValue);

                    $agence = $rdv->agence;
                    $particulierUser = $rdv->particulier->user ?? null;
                    $bien   = $rdv->proposition->bien ?? null;
                    $prix   = $rdv->proposition->prix_propose ?? 0;
                @endphp

                <tr>
                    {{-- Agence --}}
                    <td>
                        <div class="cell-user">
                            @if($agence && $agence->logo)
                                <img src="{{ asset('storage/' . $agence->logo) }}"
                                     alt="{{ $agence->nom_agence }}"
                                     class="cell-user__avatar">
                            @else
                                <div class="cell-user__avatar cell-user__avatar--fallback">
                                    {{ $agence ? Str::substr($agence->nom_agence, 0, 2) : 'NA' }}
                                </div>
                            @endif
                            <div>
                                <div class="cell-main">{{ $agence->nom_agence ?? 'N/A' }}</div>
                                <div class="cell-sub">{{ $agence->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Particulier --}}
                    <td>
                        <div>
                            <div class="cell-main">
                                {{ $particulierUser->prenom ?? '' }} {{ $particulierUser->nom ?? '' }}
                            </div>
                            <div class="cell-sub">{{ $particulierUser->email ?? '' }}</div>
                        </div>
                    </td>

                    {{-- Bien --}}
                    <td>
                        <div>
                            <div class="cell-main">{{ $bien->titre ?? 'N/A' }}</div>
                            <div class="cell-sub">
                                <i class="fa-solid fa-tag"></i>
                                {{ number_format($prix, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                    </td>

                    {{-- Date & Heure --}}
                    <td>
                        <div>
                            <div style="font-weight:600;font-size:14px;">
                                {{ $rdv->date_visite ? \Carbon\Carbon::parse($rdv->date_visite)->format('d/m/Y') : 'N/A' }}
                            </div>
                            <div style="font-size:12px;color:var(--muted);">
                                <i class="fa-solid fa-clock"></i>
                                {{ $rdv->heure_visite ?? 'N/A' }}
                            </div>
                        </div>
                    </td>

                    {{-- Code --}}
                    <td>
                        @if($rdv->code_visite)
                            <div class="code-badge">
                                <i class="fa-solid fa-key"></i>
                                <strong>{{ $rdv->code_visite }}</strong>
                            </div>
                            @if($rdv->code_valide)
                                <div class="code-validated">
                                    <i class="fa-solid fa-circle-check"></i> Validé
                                </div>
                            @endif
                        @else
                            <span style="color:var(--muted);font-size:12px;">—</span>
                        @endif
                    </td>

                    {{-- Statut --}}
                    <td>
                        <span class="status-pill status-{{ $statutValue }}">
                            {{ $statutLabel }}
                        </span>
                    </td>

                    {{-- Actions --}}
                    <td style="text-align:center;">
                        <a href="{{ route('admin.rendezvous.show', $rdv) }}"
                           class="btn btn-sm btn-ghost" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="table-empty">
                        <i class="fa-solid fa-calendar-xmark"></i>
                        Aucun rendez-vous trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
@if($rendezVous->hasPages())
<div class="pagination-container">
    <nav class="pagination-nav" aria-label="Pagination des rendez-vous">
        <div class="pagination-info">
            <span class="pagination-stats">
                <i class="fa-solid fa-calendar-check" style="color:var(--rust);"></i>
                Affichage de <strong>{{ $rendezVous->firstItem() }}</strong> à
                <strong>{{ $rendezVous->lastItem() }}</strong>
                sur <strong>{{ $rendezVous->total() }}</strong> rendez-vous
            </span>
        </div>

        <ul class="pagination">
            @if($rendezVous->onFirstPage())
                <li class="disabled"><span><i class="fa-solid fa-chevron-left"></i> Précédent</span></li>
            @else
                <li><a href="{{ $rendezVous->previousPageUrl() }}" rel="prev"><i class="fa-solid fa-chevron-left"></i> Précédent</a></li>
            @endif

            @php
                $currentPage = $rendezVous->currentPage();
                $lastPage = $rendezVous->lastPage();
                $window = 2;
            @endphp

            @foreach(range(1, $lastPage) as $page)
                @if($page == 1 || $page == $lastPage || abs($page - $currentPage) <= $window)
                    @if($page == $currentPage)
                        <li class="active"><span>{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $rendezVous->url($page) }}">{{ $page }}</a></li>
                    @endif
                @elseif($page == $currentPage - $window - 1 || $page == $currentPage + $window + 1)
                    <li class="disabled"><span>&hellip;</span></li>
                @endif
            @endforeach

            @if($rendezVous->hasMorePages())
                <li><a href="{{ $rendezVous->nextPageUrl() }}" rel="next">Suivant <i class="fa-solid fa-chevron-right"></i></a></li>
            @else
                <li class="disabled"><span>Suivant <i class="fa-solid fa-chevron-right"></i></span></li>
            @endif
        </ul>

        <div class="pagination-per-page">
            <span class="per-page-label">
                <i class="fa-solid fa-calendar-check" style="color:var(--rust);font-size:12px;"></i>
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
    .status-planifie      { background: #FFF8E1; color: #E65100; }
    .status-confirme      { background: #E3F2FD; color: #0D47A1; }
    .status-termine       { background: #E8F5E9; color: #1E7A47; }
    .status-annule        { background: #FFEBEE; color: #C62828; }
    .status-client_absent { background: #EFEFF4; color: #6C6C70; }

    /* ===== CODE BADGE ===== */
    .code-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        background: #FFF9F5;
        border: 1px solid #FFE0D0;
        border-radius: 6px;
        font-size: 12.5px;
        color: var(--rust);
    }
    .code-badge i { font-size: 11px; }
    .code-badge strong {
        font-family: monospace;
        letter-spacing: 1px;
        font-size: 13px;
    }
    .code-validated {
        font-size: 10.5px;
        color: #1E7A47;
        margin-top: 3px;
        display: flex;
        align-items: center;
        gap: 3px;
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
    .table-wrap tbody tr:hover { background: #FAFBFD; }

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
        .section-head { flex-direction: column; align-items: stretch; gap: 12px; }

        .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .kpi-card { padding: 12px 16px; }
        .kpi-value { font-size: 20px; }

        .filters-bar { flex-direction: column; align-items: stretch; }
        .filters-bar__search { margin-left: 0; width: 100%; }
        .filters-bar__input { min-width: 0; flex: 1; }

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
    }

    @media (max-width: 480px) {
        .kpi-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
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