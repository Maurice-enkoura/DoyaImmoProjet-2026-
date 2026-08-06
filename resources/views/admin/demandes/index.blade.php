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

<div style="margin-top:20px;">
    {{ $demandes->appends(request()->query())->links() }}
</div>
@endsection