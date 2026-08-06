@extends('layouts.admin')

@section('title', 'Tableau de bord - DoyaImmo')
@section('page_title', 'Tableau de bord')
@section('page_sub', 'Vue d\'ensemble de la plateforme DoyaImmo')

@section('content')
<style>
    /* ==================== RESPONSIVE ==================== */
    
    /* Tablettes et petits écrans */
    @media (max-width: 1024px) {
        .kpi-grid {
            grid-template-columns: repeat(3, 1fr) !important;
            gap: 12px !important;
        }
        
        .kpi-card {
            padding: 16px !important;
        }
        
        .kpi-value {
            font-size: 22px !important;
        }
    }
    
    /* Mobiles */
    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
        }
        
        .kpi-card {
            padding: 12px 14px !important;
        }
        
        .kpi-value {
            font-size: 18px !important;
        }
        
        .kpi-label {
            font-size: 11px !important;
        }
        
        .kpi-ic {
            width: 28px !important;
            height: 28px !important;
            font-size: 12px !important;
        }
        
        .kpi-trend {
            font-size: 10px !important;
        }
        
        .grid-2 {
            grid-template-columns: 1fr !important;
        }
        
        .panel {
            padding: 14px 16px !important;
        }
        
        .panel-head {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 8px !important;
        }
        
        .table-wrap {
            overflow-x: auto !important;
        }
        
        .table-wrap table {
            font-size: 12px !important;
            min-width: 500px !important;
        }
        
        .table-wrap th,
        .table-wrap td {
            padding: 8px 12px !important;
        }
        
        .cell-main {
            font-size: 12px !important;
        }
        
        .cell-sub {
            font-size: 10px !important;
        }
        
        .btn-sm {
            padding: 3px 8px !important;
            font-size: 10px !important;
        }
        
        .status-pill {
            font-size: 10px !important;
            padding: 2px 10px !important;
        }
    }
    
    /* Très petits écrans */
    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        
        .kpi-card {
            padding: 10px 12px !important;
        }
        
        .kpi-value {
            font-size: 16px !important;
        }
        
        .kpi-label {
            font-size: 10px !important;
        }
        
        .kpi-ic {
            width: 24px !important;
            height: 24px !important;
            font-size: 10px !important;
        }
        
        .kpi-top {
            margin-bottom: 4px !important;
        }
        
        .section-head {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 8px !important;
        }
        
        .section-head h2 {
            font-size: 16px !important;
        }
        
        .panel {
            padding: 10px 12px !important;
        }
        
        .panel-head h3 {
            font-size: 13px !important;
        }
        
        .table-wrap table {
            font-size: 11px !important;
            min-width: 400px !important;
        }
        
        .table-wrap th,
        .table-wrap td {
            padding: 6px 8px !important;
        }
        
        .btn-sm {
            padding: 2px 6px !important;
            font-size: 9px !important;
        }
        
        .status-pill {
            font-size: 9px !important;
            padding: 1px 8px !important;
        }
        
        .see-all {
            font-size: 11px !important;
        }
        
        /* Rendre les boutons d'action plus accessibles sur mobile */
        td .btn-sm {
            min-height: 30px !important;
            min-width: 30px !important;
        }
    }
    
    /* Amélioration du défilement sur mobile */
    .table-wrap {
        -webkit-overflow-scrolling: touch;
        overflow-x: auto;
    }
    
    /* Ajustement des cartes KPI */
    .kpi-card {
        transition: transform 0.2s;
    }
    
    .kpi-card:active {
        transform: scale(0.98);
    }
    
    /* Amélioration des touches sur mobile */
    .btn-sm {
        min-height: 32px;
        min-width: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Ajustement des badges */
    .status-pill {
        white-space: nowrap;
    }
</style>

<!-- KPI Cards -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-top">
            <div class="kpi-ic" style="background:#E3F2FD;color:#0D47A1;">
                <i class="fa-solid fa-users"></i>
            </div>
            <span class="kpi-trend trend-up">+{{ $stats['users_evolution'] ?? 0 }}%</span>
        </div>
        <div class="kpi-value">{{ $stats['total_users'] ?? 0 }}</div>
        <div class="kpi-label">Utilisateurs</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-top">
            <div class="kpi-ic" style="background:#E8F5E9;color:#1E7A47;">
                <i class="fa-solid fa-building"></i>
            </div>
            <span class="kpi-trend trend-up">+{{ $stats['agences_evolution'] ?? 0 }}%</span>
        </div>
        <div class="kpi-value">{{ $stats['agences']['total'] ?? 0 }}</div>
        <div class="kpi-label">Agences</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            <span style="color:#E65100;">{{ $stats['agences']['en_attente'] ?? 0 }} en attente</span>
            @if(isset($stats['agences']['validees']))
                <span style="color:#1E7A47;margin-left:8px;">{{ $stats['agences']['validees'] ?? 0 }} validées</span>
            @endif
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-top">
            <div class="kpi-ic" style="background:#FFF8E1;color:#E65100;">
                <i class="fa-solid fa-house"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $stats['biens']['total'] ?? 0 }}</div>
        <div class="kpi-label">Biens publiés</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            <span style="color:#1E7A47;">{{ $stats['biens']['disponibles'] ?? 0 }} disponibles</span>
            <span style="color:#C62828;margin-left:8px;">{{ ($stats['biens']['total'] ?? 0) - ($stats['biens']['disponibles'] ?? 0) }} indisponibles</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-top">
            <div class="kpi-ic" style="background:#F3E5F5;color:#6A1B9A;">
                <i class="fa-solid fa-house-circle-check"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $stats['demandes']['total'] ?? 0 }}</div>
        <div class="kpi-label">Demandes</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            <span style="color:#E65100;">{{ $stats['demandes']['en_attente'] ?? 0 }} en attente</span>
            <span style="color:#0D47A1;margin-left:8px;">{{ $stats['demandes']['en_cours'] ?? 0 }} en cours</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-top">
            <div class="kpi-ic" style="background:#E8F5E9;color:#1E7A47;">
                <i class="fa-solid fa-handshake"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $stats['propositions']['total'] ?? 0 }}</div>
        <div class="kpi-label">Propositions</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            <span style="color:#E65100;">{{ $stats['propositions']['en_attente'] ?? 0 }} en attente</span>
            <span style="color:#1E7A47;margin-left:8px;">{{ $stats['propositions']['acceptees'] ?? 0 }} acceptées</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-top">
            <div class="kpi-ic" style="background:#FFF3E0;color:#E65100;">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $stats['rendezvous']['planifies'] ?? 0 }}</div>
        <div class="kpi-label">Rendez-vous planifiés</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            <span style="color:#0D47A1;">{{ $stats['rendezvous']['confirmes'] ?? 0 }} confirmés</span>
            <span style="color:#C62828;margin-left:8px;">{{ $stats['rendezvous']['annules'] ?? 0 }} annulés</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-top">
            <div class="kpi-ic" style="background:#E8F5E9;color:#1E7A47;">
                <i class="fa-solid fa-award"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $stats['abonnements']['actifs'] ?? 0 }}</div>
        <div class="kpi-label">Abonnements actifs</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            <span style="color:#C62828;">{{ $stats['abonnements']['expires'] ?? 0 }} expirés</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-top">
            <div class="kpi-ic" style="background:#FFEBEE;color:#C62828;">
                <i class="fa-solid fa-flag"></i>
            </div>
        </div>
        <div class="kpi-value">{{ $stats['signalements']['en_attente'] ?? 0 }}</div>
        <div class="kpi-label">Signalements en attente</div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
            <span>Total: {{ $stats['signalements']['total'] ?? 0 }}</span>
            <span style="color:#1E7A47;margin-left:8px;">{{ $stats['signalements']['traites'] ?? 0 }} traités</span>
        </div>
    </div>
</div>

<!-- Deux colonnes -->
<div class="grid-2">
    <!-- Dernières inscriptions -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-user-plus" style="color:var(--rust);margin-right:8px;"></i>Dernières inscriptions</h3>
            <a href="{{ route('admin.utilisateurs.index') }}" class="see-all">Voir tous →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniersUtilisateurs ?? [] as $user)
                        <tr>
                            <td>
                                <div class="cell-main">{{ $user->prenom }} {{ $user->nom }}</div>
                                <div class="cell-sub">{{ $user->email }}</div>
                            </td>
                            <td>
                                <span class="status-pill 
                                    @if($user->role === 'admin') status-active
                                    @elseif($user->role === 'agence') status-confirme
                                    @else status-en_attente
                                    @endif">
                                    @if($user->role === 'admin') Administrateur
                                    @elseif($user->role === 'agence') Agence
                                    @else Particulier
                                    @endif
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:20px;">Aucun utilisateur récent</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Derniers signalements -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-flag" style="color:var(--rust);margin-right:8px;"></i>Signalements récents</h3>
            <a href="{{ route('admin.signalements.index') }}" class="see-all">Voir tous →</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Signalement</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($derniersSignalements ?? [] as $signalement)
                        <tr>
                            <td>
                                <div class="cell-main">{{ $signalement->motif_label ?? $signalement->motif }}</div>
                                <div class="cell-sub">{{ Str::limit($signalement->description, 50) }}</div>
                            </td>
                            <td>
                                <span class="status-pill status-{{ $signalement->statut }}">
                                    {{ $signalement->statut_label ?? $signalement->statut }}
                                </span>
                            </td>
                            <td>{{ $signalement->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--muted);padding:20px;">Aucun signalement récent</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Agences en attente -->
<div class="panel" style="margin-top:24px;">
    <div class="panel-head">
        <h3><i class="fa-solid fa-clock" style="color:var(--rust);margin-right:8px;"></i>Agences en attente de validation</h3>
        <a href="{{ route('admin.agences.index', ['filtre' => 'en_attente']) }}" class="see-all">Voir toutes →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Agence</th>
                    <th>Propriétaire</th>
                    <th>Quartier</th>
                    <th>Date</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($agencesEnAttenteList ?? [] as $agence)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($agence->logo)
                                    <img src="{{ asset('storage/' . $agence->logo) }}" 
                                         alt="{{ $agence->nom_agence }}" 
                                         style="width:30px;height:30px;object-fit:cover;border-radius:50%;border:1px solid var(--border);">
                                @endif
                                <div class="cell-main">{{ $agence->nom_agence }}</div>
                            </div>
                        </td>
                        <td>{{ $agence->user->prenom ?? '' }} {{ $agence->user->nom ?? '' }}</td>
                        <td>
                            @php
                                $quartierNom = 'N/A';
                                if ($agence->quartier) {
                                    if (is_object($agence->quartier)) {
                                        $quartierNom = $agence->quartier->nom ?? 'N/A';
                                    } elseif (is_string($agence->quartier)) {
                                        $quartierNom = $agence->quartier;
                                    }
                                }
                            @endphp
                            {{ $quartierNom }}
                        </td>
                        <td>{{ $agence->created_at->format('d/m/Y') }}</td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                                <a href="{{ route('admin.agences.show', $agence) }}" class="btn btn-sm btn-ghost" title="Voir">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.agences.valider', $agence) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Valider" onclick="return confirm('Valider cette agence ?')">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.agences.refuser', $agence) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" title="Refuser" onclick="return confirm('Refuser cette agence ?')">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:30px;">Aucune agence en attente de validation</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection