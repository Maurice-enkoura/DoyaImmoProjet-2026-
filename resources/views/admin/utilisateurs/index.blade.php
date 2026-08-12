@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs — Administration DoyaImmo')
@section('page_title', 'Gestion des utilisateurs')
@section('page_sub', 'Gérez tous les utilisateurs de la plateforme')

@section('content')
<div class="section-head">
    <div>
        <h2>Utilisateurs</h2>
        <p>{{ $users->total() }} utilisateurs inscrits sur la plateforme</p>
    </div>
    <div>
        <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-rust">
            <i class="fa-solid fa-user-plus"></i> Ajouter un utilisateur
        </a>
    </div>
</div>

<!-- Filtres et recherche -->
<div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <!-- Filtres par rôle -->
    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-sm {{ !request('role') ? 'btn-rust' : 'btn-ghost' }}">
        <i class="fa-solid fa-users"></i> Tous
    </a>
    <a href="{{ route('admin.utilisateurs.index', ['role' => 'admin']) }}" class="btn btn-sm {{ request('role') === 'admin' ? 'btn-rust' : 'btn-ghost' }}">
        <i class="fa-solid fa-user-shield"></i> Admins
    </a>
    <a href="{{ route('admin.utilisateurs.index', ['role' => 'agence']) }}" class="btn btn-sm {{ request('role') === 'agence' ? 'btn-rust' : 'btn-ghost' }}">
        <i class="fa-solid fa-building"></i> Agences
    </a>
    <a href="{{ route('admin.utilisateurs.index', ['role' => 'particulier']) }}" class="btn btn-sm {{ request('role') === 'particulier' ? 'btn-rust' : 'btn-ghost' }}">
        <i class="fa-solid fa-user"></i> Particuliers
    </a>
    
    <!-- Recherche -->
    <form action="{{ route('admin.utilisateurs.index') }}" method="GET" style="display:flex;gap:8px;margin-left:auto;">
        <input type="text" name="search" placeholder="Rechercher par nom, email..." 
               value="{{ request('search') }}" 
               style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;min-width:200px;">
        <button type="submit" class="btn btn-sm btn-ghost">
            <i class="fa-solid fa-search"></i>
        </button>
        @if(request('search') || request('role'))
            <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-sm btn-ghost">
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
                <th>Utilisateur</th>
                <th>Email / Téléphone</th>
                <th>Rôle</th>
                <th>Abonnement</th>
                <th>Statut</th>
                <th>Inscrit le</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:var(--rust);">
                                {{ strtoupper(substr($user->prenom ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->nom ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="cell-main">{{ $user->prenom }} {{ $user->nom }}</div>
                                <div class="cell-sub">
                                    @if($user->isAgence() && $user->agence)
                                        <i class="fa-solid fa-building"></i> {{ $user->agence->nom_agence ?? 'N/A' }}
                                    @elseif($user->isParticulier())
                                        <i class="fa-solid fa-user"></i> Particulier
                                    @elseif($user->isAdmin())
                                        <i class="fa-solid fa-user-shield"></i> Administrateur
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13px;">{{ $user->email }}</div>
                        <div style="font-size:12px;color:var(--muted);">{{ $user->telephone ?? 'Pas de téléphone' }}</div>
                    </td>
                    <td>
                        <span class="status-pill 
                            @if($user->isAdmin()) status-active
                            @elseif($user->isAgence()) status-confirme
                            @else status-en_attente
                            @endif">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td>
                        @if($user->isAgence() && $user->agence)
                            @php
                                $abonnement = $user->agence->abonnements()->where('statut', true)->where('date_fin', '>', now())->first();
                            @endphp
                            @if($abonnement)
                                <span class="status-pill status-active">
                                    <i class="fa-solid fa-crown"></i> 
                                    {{ is_object($abonnement->formule) ? $abonnement->formule->label() : ucfirst($abonnement->formule) }}
                                </span>
                                <div style="font-size:10px;color:var(--muted);">
                                    Expire le {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}
                                </div>
                            @else
                                <span class="status-pill status-inactif">
                                    <i class="fa-solid fa-circle-xmark"></i> Aucun
                                </span>
                            @endif
                        @else
                            <span style="font-size:12px;color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($user->email_verified_at)
                            <span class="status-pill status-active">Vérifié</span>
                        @else
                            <span class="status-pill status-en_attente">Non vérifié</span>
                        @endif
                        @if($user->isAgence() && $user->agence && isset($user->agence->bloque) && $user->agence->bloque)
                            <span class="status-pill status-annule" style="margin-top:4px;display:block;">Bloqué</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                            <!-- Voir - accessible pour tous -->
                            <a href="{{ route('admin.utilisateurs.show', $user) }}" class="btn btn-sm btn-ghost" title="Voir">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            
                            <!-- PAS de bouton MODIFIER pour les admins -->
                            
                            <!-- Bloquer/Débloquer - PAS pour les admins -->
                            @if(!$user->isAdmin() && $user->isAgence())
                                @if(isset($user->agence->bloque) && $user->agence->bloque)
                                    <form action="{{ route('admin.utilisateurs.toggle-block', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Débloquer">
                                            <i class="fa-solid fa-unlock"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.utilisateurs.toggle-block', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" title="Bloquer" onclick="return confirm('Bloquer cet utilisateur ?')">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif
                            
                            <!-- Supprimer - PAS pour les admins -->
                            @if(!$user->isAdmin())
                                <form action="{{ route('admin.utilisateurs.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-solid fa-users" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Aucun utilisateur trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ==================== PAGINATION RÉORGANISÉE ==================== -->
@if($users->hasPages())
<div class="pagination-container">
    <nav class="pagination-nav" aria-label="Pagination des utilisateurs">
        {{-- Informations de pagination --}}
        <div class="pagination-info">
            <span class="pagination-stats">
                <i class="fa-solid fa-paper-plane" style="color:var(--rust);"></i>
                Affichage de <strong>{{ $users->firstItem() }}</strong> à <strong>{{ $users->lastItem() }}</strong> 
                sur <strong>{{ $users->total() }}</strong> utilisateurs
            </span>
        </div>

        {{-- Liens de pagination --}}
        <ul class="pagination">
            {{-- Lien "Précédent" --}}
            @if($users->onFirstPage())
                <li class="disabled" aria-disabled="true">
                    <span><i class="fa-solid fa-chevron-left"></i> Précédent</span>
                </li>
            @else
                <li>
                    <a href="{{ $users->previousPageUrl() }}" rel="prev" aria-label="Page précédente">
                        <i class="fa-solid fa-chevron-left"></i> Précédent
                    </a>
                </li>
            @endif

            {{-- Éléments de pagination --}}
            @php
                $currentPage = $users->currentPage();
                $lastPage = $users->lastPage();
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
                            <a href="{{ $users->url($page) }}" aria-label="Page {{ $page }}">
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
            @if($users->hasMorePages())
                <li>
                    <a href="{{ $users->nextPageUrl() }}" rel="next" aria-label="Page suivante">
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
@endsection

@push('styles')
<style>
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
    .status-confirme {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-annule {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-inactif {
        background: #F5F5F5;
        color: var(--muted);
    }

    .btn-success {
        background: #1E7A47;
        color: #fff;
        border: none;
    }
    .btn-success:hover {
        background: #145c35;
        color: #fff;
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
        .section-head > div:last-child {
            display: flex;
            justify-content: stretch;
        }
        .section-head .btn {
            width: 100%;
            justify-content: center;
        }

        .table-wrap table {
            font-size: 12px;
        }
        .table-wrap thead th,
        .table-wrap tbody td {
            padding: 8px 10px;
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
    }

    @media (max-width: 480px) {
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

        /* Actions sur mobile */
        td:last-child .btn {
            padding: 4px 8px;
            font-size: 11px;
        }
        td:last-child [style*="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;"] {
            gap: 3px !important;
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