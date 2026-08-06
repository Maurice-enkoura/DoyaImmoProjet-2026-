@extends('layouts.admin')

@section('title', 'Détail du quartier — Administration DoyaImmo')
@section('page_title', 'Détail du quartier')
@section('page_sub', $quartier->nom)

@section('content')
<style>
    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr !important;
        }
        
        .panel {
            padding: 16px !important;
        }
        
        .stats-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        
        .stats-grid .stat-item {
            padding: 8px !important;
        }
    }
    
    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr !important;
        }
        
        .panel {
            padding: 12px !important;
        }
        
        .panel h3 {
            font-size: 16px !important;
        }
    }
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.quartiers.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux quartiers
    </a>
</div>

<div class="grid-2">
    <!-- Informations du quartier -->
    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
                <h3 style="font-size:18px;font-weight:700;">
                    <i class="fa-solid fa-location-dot" style="color:var(--rust);"></i> 
                    {{ $quartier->nom }}
                </h3>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">
                    <i class="fa-solid fa-city"></i> {{ $quartier->ville }}
                </div>
            </div>
            <span class="status-pill {{ $quartier->est_actif ? 'status-active' : 'status-inactif' }}">
                {{ $quartier->est_actif ? ' Actif' : ' Inactif' }}
            </span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">ID</div>
                <div style="font-weight:600;">#{{ $quartier->id }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Créé le</div>
                <div style="font-weight:600;">{{ $quartier->created_at->format('d/m/Y') }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Dernière mise à jour</div>
                <div style="font-weight:600;">{{ $quartier->updated_at->format('d/m/Y') }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Statut</div>
                <div style="font-weight:600;">
                    @if($quartier->est_actif)
                        <span style="color:#1E7A47;">Actif</span>
                    @else
                        <span style="color:#C62828;">Inactif</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.quartiers.edit', $quartier) }}" class="btn btn-rust">
                <i class="fa-solid fa-pen"></i> Modifier
            </a>
            <form action="{{ route('admin.quartiers.toggle', $quartier) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-ghost">
                    <i class="fa-solid {{ $quartier->est_actif ? 'fa-eye-slash' : 'fa-eye' }}"></i> 
                    {{ $quartier->est_actif ? 'Désactiver' : 'Activer' }}
                </button>
            </form>
            @php
                $hasRelations = ($quartier->demandes()->count() > 0 || $quartier->biens()->count() > 0 || $quartier->agences()->count() > 0);
            @endphp
            @if(!$hasRelations)
                <form action="{{ route('admin.quartiers.destroy', $quartier) }}" method="POST" onsubmit="return confirm('Supprimer définitivement ce quartier ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> Supprimer
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Statistiques -->
    <div>
        <div class="panel">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-chart-simple"></i> Statistiques
            </h3>
            <div class="stats-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div class="stat-item" style="padding:10px;background:#F7F9FC;border-radius:8px;text-align:center;">
                    <div style="font-size:24px;font-weight:700;color:var(--rust);">
                        {{ $stats['total_demandes'] ?? 0 }}
                    </div>
                    <div style="font-size:12px;color:var(--muted);">Total demandes</div>
                </div>
                <div class="stat-item" style="padding:10px;background:#F7F9FC;border-radius:8px;text-align:center;">
                    <div style="font-size:24px;font-weight:700;color:#E65100;">
                        {{ $stats['demandes_actives'] ?? 0 }}
                    </div>
                    <div style="font-size:12px;color:var(--muted);">Demandes actives</div>
                </div>
                <div class="stat-item" style="padding:10px;background:#F7F9FC;border-radius:8px;text-align:center;">
                    <div style="font-size:24px;font-weight:700;color:#0D47A1;">
                        {{ $stats['total_biens'] ?? 0 }}
                    </div>
                    <div style="font-size:12px;color:var(--muted);">Total biens</div>
                </div>
                <div class="stat-item" style="padding:10px;background:#F7F9FC;border-radius:8px;text-align:center;">
                    <div style="font-size:24px;font-weight:700;color:#1E7A47;">
                        {{ $stats['biens_disponibles'] ?? 0 }}
                    </div>
                    <div style="font-size:12px;color:var(--muted);">Biens disponibles</div>
                </div>
                <div class="stat-item" style="padding:10px;background:#F7F9FC;border-radius:8px;text-align:center;grid-column:span 2;">
                    <div style="font-size:24px;font-weight:700;color:#6A1B9A;">
                        {{ $stats['total_agences'] ?? 0 }}
                    </div>
                    <div style="font-size:12px;color:var(--muted);">Agences dans ce quartier</div>
                    <div style="font-size:12px;color:var(--muted);">
                        <span style="color:#1E7A47;">{{ $stats['agences_validees'] ?? 0 }} validées</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dernières demandes -->
        <div class="panel" style="margin-top:20px;">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-house-circle-check"></i> Dernières demandes
            </h3>
            @if(isset($demandesRecentes) && $demandesRecentes->count() > 0)
                @foreach($demandesRecentes as $demande)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-weight:600;font-size:13px;">
                                {{ $demande->type_operation ?? 'N/A' }} - {{ $demande->type_bien ?? 'N/A' }}
                            </div>
                            <div style="font-size:12px;color:var(--muted);">
                                {{ $demande->particulier->user->prenom ?? '' }} {{ $demande->particulier->user->nom ?? '' }}
                            </div>
                        </div>
                        <span class="status-pill status-{{ $demande->statut }}">
                            {{ $demande->statut }}
                        </span>
                    </div>
                @endforeach
            @else
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:10px 0;">
                    Aucune demande récente.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection