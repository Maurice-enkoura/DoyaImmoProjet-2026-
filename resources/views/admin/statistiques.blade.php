@extends('layouts.admin')

@section('title', 'Statistiques — Administration DoyaImmo')
@section('page_title', 'Statistiques et rapports')
@section('page_sub', 'Analyse détaillée de la plateforme DoyaImmo')

@section('content')
<style>
    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 1024px) {
        .stats-grid-3 {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
    
    @media (max-width: 768px) {
        .stats-grid-3 {
            grid-template-columns: 1fr 1fr !important;
            gap: 10px !important;
        }
        
        .stats-grid-2 {
            grid-template-columns: 1fr !important;
        }
        
        .stat-card {
            padding: 14px 16px !important;
        }
        
        .stat-card .stat-value {
            font-size: 22px !important;
        }
        
        .stat-card .stat-label {
            font-size: 12px !important;
        }
        
        .panel {
            padding: 14px 16px !important;
        }
        
        .panel-head {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }
        
        .panel-head h3 {
            font-size: 15px !important;
        }
        
        .stat-item {
            padding: 8px !important;
            font-size: 12px !important;
        }
    }
    
    @media (max-width: 480px) {
        .stats-grid-3 {
            grid-template-columns: 1fr !important;
        }
        
        .stat-card {
            padding: 10px 12px !important;
        }
        
        .stat-card .stat-value {
            font-size: 18px !important;
        }
        
        .stat-card .stat-label {
            font-size: 11px !important;
        }
        
        .panel {
            padding: 10px 12px !important;
        }
        
        .stats-grid-2 {
            gap: 8px !important;
        }
    }
    
    .stats-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stats-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }
    
    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 24px;
        text-align: center;
    }
    
    .stat-card .stat-value {
        font-family: var(--display);
        font-weight: 700;
        font-size: 28px;
        color: var(--ink);
    }
    
    .stat-card .stat-label {
        font-size: 13px;
        color: var(--muted);
        margin-top: 4px;
    }
    
    .stat-card .stat-icon {
        font-size: 28px;
        margin-bottom: 8px;
        display: block;
    }
    
    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        background: #F7F9FC;
        border-radius: 6px;
        border-left: 4px solid var(--rust);
    }
    
    .stat-item .label {
        font-size: 13px;
        color: var(--text-soft);
    }
    
    .stat-item .value {
        font-weight: 600;
        font-size: 14px;
    }
    
    .top-agence-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
    }
    
    .top-agence-item .rank {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: var(--rust-soft);
        color: var(--rust);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
    }
    
    .top-agence-item .rank.gold { background: #FFF8E1; color: #E65100; }
    .top-agence-item .rank.silver { background: #F5F5F5; color: #757575; }
    .top-agence-item .rank.bronze { background: #FFF3E0; color: #BF360C; }
    
    .top-agence-item .info {
        flex: 1;
    }
    
    .top-agence-item .info .name {
        font-weight: 600;
        font-size: 13px;
    }
    
    .top-agence-item .info .details {
        font-size: 12px;
        color: var(--muted);
    }
    
    .top-agence-item .score {
        font-weight: 700;
        color: #F5A623;
        font-size: 16px;
    }
    
    .meta-pill {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 11px;
        background: #F0F0F0;
        color: #555;
    }
</style>

<!-- Statistiques globales -->
<div class="stats-grid-3">
    <div class="stat-card">
        <span class="stat-icon"></span>
        <div class="stat-value">{{ $stats['users']['total'] ?? 0 }}</div>
        <div class="stat-label">Utilisateurs</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            @php
                $agencesCount = $stats['users']['par_role']->where('role', 'agence')->first()->total ?? 0;
                $particuliersCount = $stats['users']['par_role']->where('role', 'particulier')->first()->total ?? 0;
                $adminsCount = $stats['users']['par_role']->where('role', 'admin')->first()->total ?? 0;
            @endphp
            <span style="color:#1E7A47;">{{ $agencesCount }} agences</span>
            <span style="margin-left:8px;color:#0D47A1;">{{ $particuliersCount }} particuliers</span>
            <span style="margin-left:8px;color:#C62828;">{{ $adminsCount }} admins</span>
        </div>
    </div>
    
    <div class="stat-card">
        <span class="stat-icon"></span>
        <div class="stat-value">{{ $stats['agences']['total'] ?? 0 }}</div>
        <div class="stat-label">Agences</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            <span style="color:#1E7A47;">{{ $stats['agences']['validees'] ?? 0 }} validées</span>
            <span style="margin-left:8px;color:#E65100;">{{ $stats['agences']['en_attente'] ?? 0 }} en attente</span>
        </div>
    </div>
    
    <div class="stat-card">
        <span class="stat-icon"></span>
        <div class="stat-value">{{ number_format($stats['abonnements']['revenus'] ?? 0, 0, ',', ' ') }} F</div>
        <div class="stat-label">Revenus des abonnements</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            {{ $stats['abonnements']['actifs'] ?? 0 }} abonnements actifs
        </div>
    </div>
</div>

<!-- Activité -->
<div class="stats-grid-3" style="margin-bottom:24px;">
    <div class="stat-card" style="background:#E8F5E9;">
        <div class="stat-value" style="color:#1E7A47;">{{ $stats['activite']['aujourd_hui'] ?? 0 }}</div>
        <div class="stat-label"> Aujourd'hui</div>
    </div>
    <div class="stat-card" style="background:#E3F2FD;">
        <div class="stat-value" style="color:#0D47A1;">{{ $stats['activite']['cette_semaine'] ?? 0 }}</div>
        <div class="stat-label"> Cette semaine</div>
    </div>
    <div class="stat-card" style="background:#FFF8E1;">
        <div class="stat-value" style="color:#E65100;">{{ $stats['activite']['ce_mois'] ?? 0 }}</div>
        <div class="stat-label">Ce mois</div>
    </div>
</div>

<!-- Deux colonnes -->
<div class="stats-grid-2">
    <!-- Demandes -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-house-circle-check" style="color:var(--rust);margin-right:8px;"></i> Demandes</h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div class="stat-item">
                <span class="label">Total</span>
                <span class="value">{{ $stats['demandes']['total'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">En attente</span>
                <span class="value" style="color:#E65100;">{{ $stats['demandes']['par_statut']->where('statut', 'en_attente')->first()->total ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">En cours</span>
                <span class="value" style="color:#0D47A1;">{{ $stats['demandes']['par_statut']->where('statut', 'en_cours')->first()->total ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Terminées</span>
                <span class="value" style="color:#1E7A47;">{{ $stats['demandes']['par_statut']->where('statut', 'terminee')->first()->total ?? 0 }}</span>
            </div>
        </div>
        <div style="margin-top:12px;font-size:12px;color:var(--muted);">
            <strong>Types d'opération :</strong>
            @foreach($stats['demandes']['par_type_operation'] ?? [] as $item)
                <span style="margin-right:12px;">{{ $item->type_operation }}: {{ $item->total }}</span>
            @endforeach
        </div>
    </div>

    <!-- Propositions -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-handshake" style="color:var(--rust);margin-right:8px;"></i> Propositions</h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div class="stat-item">
                <span class="label">Total</span>
                <span class="value">{{ $stats['propositions']['total'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">En attente</span>
                <span class="value" style="color:#E65100;">{{ $stats['propositions']['par_statut']->where('statut', 'en_attente')->first()->total ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Acceptées</span>
                <span class="value" style="color:#1E7A47;">{{ $stats['propositions']['par_statut']->where('statut', 'acceptee')->first()->total ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Refusées</span>
                <span class="value" style="color:#C62828;">{{ $stats['propositions']['par_statut']->where('statut', 'refusee')->first()->total ?? 0 }}</span>
            </div>
        </div>
        <div style="margin-top:12px;font-size:12px;color:var(--muted);">
            <strong>Prix moyen :</strong> {{ number_format($stats['propositions']['prix_moyen'] ?? 0, 0, ',', ' ') }} FCFA
            <span style="margin-left:12px;"><strong>Min :</strong> {{ number_format($stats['propositions']['prix_min'] ?? 0, 0, ',', ' ') }} F</span>
            <span style="margin-left:12px;"><strong>Max :</strong> {{ number_format($stats['propositions']['prix_max'] ?? 0, 0, ',', ' ') }} F</span>
        </div>
    </div>
</div>

<!-- Biens et Signalements -->
<div class="stats-grid-2">
    <!-- Biens -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-house" style="color:var(--rust);margin-right:8px;"></i> Biens immobiliers</h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div class="stat-item">
                <span class="label">Total</span>
                <span class="value">{{ $stats['biens']['total'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Disponibles</span>
                <span class="value" style="color:#1E7A47;">{{ $stats['biens']['disponibles'] ?? 0 }}</span>
            </div>
        </div>
        <div style="margin-top:12px;">
            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">
                <strong>Types de biens :</strong>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                @foreach($stats['biens']['par_type'] ?? [] as $item)
                    <span class="meta-pill">{{ $item->type_bien }}: {{ $item->total }}</span>
                @endforeach
            </div>
        </div>
        <div style="margin-top:8px;font-size:12px;color:var(--muted);">
            <strong>Prix moyen :</strong> {{ number_format($stats['biens']['prix_moyen'] ?? 0, 0, ',', ' ') }} FCFA
            <span style="margin-left:12px;"><strong>Surface moyenne :</strong> {{ number_format($stats['biens']['surface_moyenne'] ?? 0, 1) }} m²</span>
        </div>
    </div>

    <!-- Signalements -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-flag" style="color:var(--rust);margin-right:8px;"></i> Signalements</h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div class="stat-item">
                <span class="label">Total</span>
                <span class="value">{{ $stats['signalements']['total'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">En attente</span>
                <span class="value" style="color:#E65100;">{{ $stats['signalements']['par_statut']->where('statut', 'en_attente')->first()->total ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Traités</span>
                <span class="value" style="color:#1E7A47;">{{ $stats['signalements']['par_statut']->where('statut', 'traite')->first()->total ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Rejetés</span>
                <span class="value" style="color:#C62828;">{{ $stats['signalements']['par_statut']->where('statut', 'rejete')->first()->total ?? 0 }}</span>
            </div>
        </div>
        <div style="margin-top:12px;font-size:12px;color:var(--muted);">
            <strong>Motifs :</strong>
            @foreach($stats['signalements']['par_motif'] ?? [] as $item)
                <span style="margin-right:12px;">{{ $item->motif }}: {{ $item->total }}</span>
            @endforeach
        </div>
    </div>
</div>

<!-- Évaluations et Abonnements -->
<div class="stats-grid-2">
    <!-- Évaluations -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-star" style="color:#F5A623;margin-right:8px;"></i> Évaluations</h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
            <div class="stat-item" style="flex-direction:column;text-align:center;border-left-color:#F5A623;">
                <span class="label">Total</span>
                <span class="value" style="font-size:20px;">{{ $stats['evaluations']['total'] ?? 0 }}</span>
            </div>
            <div class="stat-item" style="flex-direction:column;text-align:center;border-left-color:#F5A623;">
                <span class="label">Moyenne</span>
                <span class="value" style="font-size:20px;color:#F5A623;">{{ number_format($stats['evaluations']['note_moyenne'] ?? 0, 1) }} ★</span>
            </div>
            <div class="stat-item" style="flex-direction:column;text-align:center;border-left-color:#F5A623;">
                <span class="label">Écart</span>
                <span class="value" style="font-size:16px;color:var(--muted);">
                    {{ number_format($stats['evaluations']['note_min'] ?? 0, 1) }} - {{ number_format($stats['evaluations']['note_max'] ?? 0, 1) }}
                </span>
            </div>
        </div>
        <div style="margin-top:8px;font-size:12px;color:var(--muted);text-align:center;">
            Répartition des notes : 
            @foreach($stats['evaluations']['repartition_notes'] ?? [] as $item)
                <span style="margin:0 4px;">{{ $item->note }}★ ({{ $item->total }})</span>
            @endforeach
        </div>
    </div>

    <!-- Abonnements CORRIGÉ -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-award" style="color:var(--rust);margin-right:8px;"></i> Abonnements</h3>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div class="stat-item">
                <span class="label">Total</span>
                <span class="value">{{ $stats['abonnements']['total'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Actifs</span>
                <span class="value" style="color:#1E7A47;">{{ $stats['abonnements']['actifs'] ?? 0 }}</span>
            </div>
        </div>
        <div style="margin-top:12px;">
            <div style="font-size:12px;color:var(--muted);margin-bottom:4px;">
                <strong>Par formule :</strong>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
                @foreach($stats['abonnements']['par_formule'] ?? [] as $item)
                    @php
                        $formuleLabel = is_object($item->formule) && method_exists($item->formule, 'label') 
                            ? $item->formule->label() 
                            : ucfirst($item->formule);
                    @endphp
                    <span class="meta-pill">{{ $formuleLabel }}: {{ $item->total }}</span>
                @endforeach
            </div>
        </div>
        <div style="margin-top:8px;font-size:12px;color:var(--muted);">
            <strong>Revenus par formule :</strong>
            @foreach($stats['abonnements']['revenus_par_formule'] ?? [] as $item)
                @php
                    $formuleLabel = is_object($item->formule) && method_exists($item->formule, 'label') 
                        ? $item->formule->label() 
                        : ucfirst($item->formule);
                @endphp
                <span style="margin-right:12px;">{{ $formuleLabel }}: {{ number_format($item->total, 0, ',', ' ') }} F</span>
            @endforeach
        </div>
    </div>
</div>

<!-- Top agences -->
<div class="panel" style="margin-top:20px;">
    <div class="panel-head">
        <h3><i class="fa-solid fa-trophy" style="color:#F5A623;margin-right:8px;"></i> Top 10 agences les mieux notées</h3>
    </div>
    @if(isset($stats['evaluations']['top_agences']) && $stats['evaluations']['top_agences']->count() > 0)
        @foreach($stats['evaluations']['top_agences'] as $index => $agence)
            <div class="top-agence-item">
                <div class="rank 
                    @if($index === 0) gold
                    @elseif($index === 1) silver
                    @elseif($index === 2) bronze
                    @endif">
                    {{ $index + 1 }}
                </div>
                <div class="info">
                    <div class="name">{{ $agence->nom_agence }}</div>
                    <div class="details">{{ $agence->evaluations_count }} avis</div>
                </div>
                <div class="score">{{ number_format($agence->evaluations_avg_note, 1) }} ★</div>
            </div>
        @endforeach
    @else
        <p style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0;">
            Aucune évaluation disponible.
        </p>
    @endif
</div>

<!-- Export -->
<div style="margin-top:20px;display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end;">
    <form action="{{ route('admin.statistiques.export') }}" method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <select name="type" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            <option value="users">Utilisateurs</option>
            <option value="agences">Agences</option>
            <option value="demandes">Demandes</option>
            <option value="biens">Biens</option>
            <option value="propositions">Propositions</option>
            <option value="abonnements">Abonnements</option>
        </select>
        <select name="format" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            <option value="csv">CSV</option>
            <option value="excel">Excel</option>
            <option value="pdf">PDF</option>
        </select>
        <button type="submit" class="btn btn-rust btn-sm">
            <i class="fa-solid fa-download"></i> Exporter
        </button>
    </form>
</div>
@endsection