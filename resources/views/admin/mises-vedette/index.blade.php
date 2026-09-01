@extends('layouts.admin')

@section('title', 'Mises en vedette — Administration')
@section('page_title', 'Mises en vedette')
@section('page_sub', 'Gérez les demandes de mise en vedette des agences')

@section('content')
<style>
    .filters-bar {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }
    .filters-bar .filter-group {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }
    .filters-bar .filter-group label {
        font-size: 13px;
        color: var(--text-soft);
        font-weight: 600;
    }
    .filters-bar select,
    .filters-bar input {
        padding: 6px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        background: #fff;
        font-family: inherit;
    }
    .filters-bar select:focus,
    .filters-bar input:focus {
        outline: none;
        border-color: var(--rust);
    }
    .badge-count {
        background: #FFF8E1;
        color: #E65100;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-count.active {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .badge-count.expire {
        background: #FFEBEE;
        color: #C62828;
    }
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
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-actif {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-expire {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-annule {
        background: #F5F5F5;
        color: #757575;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
    }
    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }
    .btn-ghost:hover {
        background: var(--border);
    }
    .btn-sm {
        padding: 4px 10px;
        font-size: 11px;
    }
    .btn-success {
        background: #1E7A47;
        color: #fff;
        border-color: #1E7A47;
    }
    .btn-success:hover {
        background: #145A35;
        color: #fff;
    }
    .btn-danger {
        background: #C62828;
        color: #fff;
        border-color: #C62828;
    }
    .btn-danger:hover {
        background: #B71C1C;
        color: #fff;
    }
    .btn-warning {
        background: #F5A623;
        color: #fff;
        border-color: #F5A623;
    }
    .btn-warning:hover {
        background: #E0951A;
        color: #fff;
    }
    .table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .table-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 700px;
    }
    .table-wrap thead {
        background: #FAFBFC;
        border-bottom: 1px solid var(--border);
    }
    .table-wrap th {
        padding: 10px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--muted);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .table-wrap td {
        padding: 10px 16px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }
    .table-wrap tbody tr:hover {
        background: #F7F9FC;
    }
    .cell-main {
        font-weight: 500;
        color: var(--ink);
    }
    .cell-sub {
        font-size: 12px;
        color: var(--muted);
    }
    .avatar-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid var(--border);
    }
    .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }
    .panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 8px;
    }
    .panel-head h3 {
        font-family: var(--display);
        font-size: 15px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .panel-head h3 i {
        color: var(--rust);
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: var(--muted);
    }
    .empty-state i {
        font-size: 40px;
        display: block;
        margin-bottom: 12px;
        opacity: 0.3;
    }
    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        padding: 16px 20px;
        border-top: 1px solid var(--border);
        list-style: none;
    }
    .pagination a, .pagination span {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: 13px;
    }
    .pagination .active span {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }
    .pagination a:hover {
        background: var(--border);
    }
    @media (max-width: 768px) {
        .filters-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .filters-bar .filter-group {
            flex-wrap: wrap;
        }
        .filters-bar select,
        .filters-bar input {
            flex: 1;
            min-width: 120px;
        }
        .panel-head {
            flex-direction: column;
            align-items: flex-start;
        }
        .table-wrap table {
            font-size: 12px;
            min-width: 500px;
        }
        .table-wrap th,
        .table-wrap td {
            padding: 8px 12px;
        }
    }
</style>

<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mises en vedette</h2>
            <p>Gérez les demandes de mise en vedette des agences</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <span class="badge-count">
                <i class="fa-solid fa-clock"></i> En attente: {{ $stats['en_attente'] ?? 0 }}
            </span>
            <span class="badge-count active">
                <i class="fa-solid fa-check-circle"></i> Actives: {{ $stats['actif'] ?? 0 }}
            </span>
            <span class="badge-count expire">
                <i class="fa-solid fa-circle-exclamation"></i> Expirées: {{ $stats['expire'] ?? 0 }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="flash-message flash-success">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flash-message flash-error">
            <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Filtres -->
    <div class="filters-bar">
        <form method="GET" action="{{ route('admin.mises-vedette.index') }}" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;width:100%;">
            <div class="filter-group">
                <label for="statut">Statut</label>
                <select name="statut" id="statut" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    <option value="en_attente" {{ request('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                    <option value="actif" {{ request('statut') == 'actif' ? 'selected' : '' }}>Actif</option>
                    <option value="expire" {{ request('statut') == 'expire' ? 'selected' : '' }}>Expiré</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="search">Rechercher</label>
                <input type="text" name="search" id="search" placeholder="Agence ou bien..." value="{{ request('search') }}" onchange="this.form.submit()">
            </div>
            <button type="submit" class="btn btn-ghost btn-sm">Filtrer</button>
            @if(request()->has('statut') || request()->has('search'))
                <a href="{{ route('admin.mises-vedette.index') }}" class="btn btn-ghost btn-sm">Réinitialiser</a>
            @endif
        </form>
    </div>

    <!-- Liste -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-list"></i> Toutes les demandes</h3>
            <span style="font-size:13px;color:var(--muted);">{{ $mises->total() }} demande(s)</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Bien</th>
                        <th>Agence</th>
                        <th>Durée</th>
                        <th>Montant</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Statut</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mises as $mise)
                        <tr>
                            <td>
                                <div class="cell-main">
                                    @if($mise->bien)
                                        {{ $mise->bien->titre ?? 'N/A' }}
                                    @else
                                        <span style="color:var(--muted);">Bien supprimé</span>
                                    @endif
                                </div>
                                <div class="cell-sub">
                                    @if($mise->bien)
                                        {{ $mise->bien->quartier ?? '' }}
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($mise->agence && $mise->agence->logo)
                                        <img src="{{ asset('storage/' . $mise->agence->logo) }}" class="avatar-sm">
                                    @else
                                        <div class="avatar-sm" style="background:#F0F2F5;display:flex;align-items:center;justify-content:center;font-weight:600;color:var(--muted);">
                                            {{ $mise->agence ? substr($mise->agence->nom_agence, 0, 2) : 'N/A' }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="cell-main">{{ $mise->agence->nom_agence ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $mise->duree }} jour{{ $mise->duree > 1 ? 's' : '' }}</td>
                            <td><strong>{{ number_format($mise->montant, 0, ',', ' ') }} FCFA</strong></td>
                            <td>
                                @if($mise->date_debut)
                                    {{ $mise->date_debut->format('d/m/Y') }}
                                @else
                                    <span class="cell-sub">-</span>
                                @endif
                            </td>
                            <td>
                                @if($mise->date_fin)
                                    {{ $mise->date_fin->format('d/m/Y') }}
                                    @if($mise->estActive())
                                        <span style="font-size:10px;color:#1E7A47;display:block;">
                                            {{ $mise->getJoursRestants() }} jour(s) restant(s)
                                        </span>
                                    @endif
                                @else
                                    <span class="cell-sub">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statutClass = match($mise->statut) {
                                        'en_attente' => 'status-en_attente',
                                        'actif' => 'status-actif',
                                        'expire' => 'status-expire',
                                        'annule' => 'status-annule',
                                        default => 'status-en_attente'
                                    };
                                    $statutLabel = match($mise->statut) {
                                        'en_attente' => 'En attente',
                                        'actif' => 'Actif',
                                        'expire' => 'Expiré',
                                        'annule' => 'Annulé',
                                        default => ucfirst($mise->statut)
                                    };
                                    $statutIcon = match($mise->statut) {
                                        'en_attente' => 'fa-clock',
                                        'actif' => 'fa-check-circle',
                                        'expire' => 'fa-circle-exclamation',
                                        'annule' => 'fa-ban',
                                        default => 'fa-circle'
                                    };
                                @endphp
                                <span class="status-pill {{ $statutClass }}">
                                    <i class="fa-solid {{ $statutIcon }}"></i>
                                    {{ $statutLabel }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                                    <a href="{{ route('admin.mises-vedette.show', $mise) }}" class="btn btn-sm btn-ghost" title="Voir">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    @if($mise->statut === 'en_attente')
                                        <form action="{{ route('admin.mises-vedette.valider', $mise) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Valider" onclick="return confirm('Valider cette mise en vedette ? Le bien sera mis en avant.')">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.mises-vedette.annuler', $mise) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Annuler" onclick="return confirm('Annuler cette demande ?')">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($mise->statut === 'actif')
                                        <form action="{{ route('admin.mises-vedette.annuler', $mise) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-warning" title="Désactiver" onclick="return confirm('Désactiver cette mise en vedette ? Le bien ne sera plus en avant.')">
                                                <i class="fa-solid fa-eye-slash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="fa-regular fa-star"></i>
                                    <p>Aucune demande de mise en vedette</p>
                                    <span style="font-size:13px;">Les demandes des agences apparaîtront ici</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">
            {{ $mises->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection