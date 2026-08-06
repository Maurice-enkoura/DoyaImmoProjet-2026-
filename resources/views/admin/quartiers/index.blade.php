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

<div style="margin-top:20px;">
    {{ $quartiers->appends(request()->query())->links() }}
</div>
@endsection