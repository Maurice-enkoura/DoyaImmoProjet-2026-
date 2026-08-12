@extends('layouts.admin')

@section('title', 'Tableau de bord - DoyaImmo')
@section('page_title', 'Tableau de bord')
@section('page_sub', 'Vue d\'ensemble de la plateforme DoyaImmo')

@section('content')
<style>
    /* ==================== KPI GRID ==================== */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .kpi-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 24px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .kpi-ic {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }

    .kpi-trend {
        font-size: 11.5px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 20px;
    }

    .kpi-trend.trend-up {
        color: var(--green);
        background: var(--green-soft);
    }

    .kpi-trend.trend-down {
        color: var(--red);
        background: var(--red-soft);
    }

    .kpi-value {
        font-family: var(--display);
        font-weight: 700;
        font-size: 28px;
        color: var(--ink);
    }

    .kpi-label {
        font-size: 13px;
        color: var(--muted);
    }

    .kpi-sub {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }

    /* ==================== GRID ==================== */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    /* ==================== PANEL ==================== */
    .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border);
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

    .see-all {
        font-size: 12.5px;
        color: var(--rust);
        text-decoration: none;
        font-weight: 600;
    }

    .see-all:hover {
        text-decoration: underline;
    }

    /* ==================== TABLE ==================== */
    .table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 500px;
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

    .table-wrap tbody tr:last-child td {
        border-bottom: none;
    }

    .cell-main {
        font-weight: 500;
        color: var(--ink);
    }

    .cell-sub {
        font-size: 12px;
        color: var(--muted);
    }

    /* ==================== STATUS PILL ==================== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
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

    .status-refusee {
        background: #FFEBEE;
        color: #C62828;
    }

    .status-traite {
        background: #E8F5E9;
        color: #1E7A47;
    }

    .status-rejete {
        background: #FFEBEE;
        color: #C62828;
    }

    /* ==================== BOUTONS ==================== */
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
        min-height: 32px;
        min-width: 32px;
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

    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 1024px) {
        .kpi-grid {
            grid-template-columns: repeat(3, 1fr);
        }
        .kpi-value {
            font-size: 22px;
        }
    }

    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        .kpi-card {
            padding: 14px 16px;
        }
        .kpi-value {
            font-size: 18px;
        }
        .kpi-label {
            font-size: 11px;
        }
        .kpi-ic {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }
        .grid-2 {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .panel {
            padding: 0;
        }
        .panel-head {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
            padding: 12px 16px;
        }
        .table-wrap table {
            font-size: 12px;
            min-width: 400px;
        }
        .table-wrap th,
        .table-wrap td {
            padding: 8px 12px;
        }
        .cell-main {
            font-size: 12px;
        }
        .cell-sub {
            font-size: 10px;
        }
        .status-pill {
            font-size: 10px;
            padding: 2px 10px;
        }
        .btn-sm {
            padding: 3px 8px;
            font-size: 10px;
        }
    }

    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .kpi-card {
            padding: 10px 12px;
        }
        .kpi-value {
            font-size: 16px;
        }
        .kpi-label {
            font-size: 10px;
        }
        .kpi-ic {
            width: 24px;
            height: 24px;
            font-size: 10px;
        }
        .kpi-top {
            margin-bottom: 4px;
        }
        .panel-head h3 {
            font-size: 13px;
        }
        .table-wrap table {
            font-size: 11px;
            min-width: 350px;
        }
        .table-wrap th,
        .table-wrap td {
            padding: 6px 10px;
        }
        .see-all {
            font-size: 11px;
        }
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
        <div class="kpi-sub">
            <span style="color:#E65100;">{{ $stats['agences']['en_attente'] ?? 0 }} en attente</span>
            <span style="color:#C62828;margin-left:8px;">{{ $stats['agences']['refusees'] ?? 0 }} refusées</span>
            <span style="color:#1E7A47;margin-left:8px;">{{ $stats['agences']['validees'] ?? 0 }} validées</span>
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
        <div class="kpi-sub">
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
        <div class="kpi-sub">
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
        <div class="kpi-sub">
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
        <div class="kpi-sub">
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
        <div class="kpi-sub">
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
        <div class="kpi-sub">
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
            <h3><i class="fa-solid fa-user-plus"></i> Dernières inscriptions</h3>
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
                                <div class="cell-main">{{ $user->prenom ?? '' }} {{ $user->nom ?? '' }}</div>
                                <div class="cell-sub">{{ $user->email ?? '' }}</div>
                            </td>
                            <td>
                                @php
                                    $role = $user->role ?? 'particulier';
                                    $roleClass = match($role) {
                                        'admin' => 'status-active',
                                        'agence' => 'status-confirme',
                                        default => 'status-en_attente'
                                    };
                                    $roleLabel = match($role) {
                                        'admin' => 'Administrateur',
                                        'agence' => 'Agence',
                                        default => 'Particulier'
                                    };
                                @endphp
                                <span class="status-pill {{ $roleClass }}">{{ $roleLabel }}</span>
                            </td>
                            <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '-' }}</td>
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
            <h3><i class="fa-solid fa-flag"></i> Signalements récents</h3>
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
                                <div class="cell-main">{{ $signalement->motif_label ?? $signalement->motif ?? 'Signalement' }}</div>
                                <div class="cell-sub">{{ Str::limit($signalement->description ?? '', 50) }}</div>
                            </td>
                            <td>
                                @php
                                    $statut = $signalement->statut ?? 'en_attente';
                                    $statutClass = match($statut) {
                                        'traite' => 'status-traite',
                                        'rejete' => 'status-rejete',
                                        default => 'status-en_attente'
                                    };
                                    $statutLabel = match($statut) {
                                        'traite' => 'Traité',
                                        'rejete' => 'Rejeté',
                                        default => 'En attente'
                                    };
                                @endphp
                                <span class="status-pill {{ $statutClass }}">{{ $statutLabel }}</span>
                            </td>
                            <td>{{ $signalement->created_at ? $signalement->created_at->format('d/m/Y') : '-' }}</td>
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
        <h3><i class="fa-solid fa-clock"></i> Agences en attente de validation</h3>
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
                                <div class="cell-main">{{ $agence->nom_agence ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>{{ ($agence->user->prenom ?? '') . ' ' . ($agence->user->nom ?? '') }}</td>
                        <td>
                            @php
                                $quartierNom = 'N/A';
                                if ($agence->quartier) {
                                    $quartierNom = is_object($agence->quartier) ? ($agence->quartier->nom ?? 'N/A') : $agence->quartier;
                                }
                            @endphp
                            {{ $quartierNom }}
                        </td>
                        <td>{{ $agence->created_at ? $agence->created_at->format('d/m/Y') : '-' }}</td>
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
                                    <input type="hidden" name="motif" value="Motif non spécifié">
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

<!-- Agences refusées -->
<!-- Agences refusées -->
@if(isset($agencesRefuseesList) && $agencesRefuseesList->count() > 0)
<div class="panel" style="margin-top:16px;">
    <div class="panel-head">
        <h3><i class="fa-solid fa-times-circle" style="color:#C62828;"></i> Agences refusées</h3>
        <a href="{{ route('admin.agences.index', ['filtre' => 'refusees']) }}" class="see-all">Voir toutes →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Agence</th>
                    <th>Propriétaire</th>
                    <th>Motif</th>
                    <th>Date refus</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($agencesRefuseesList as $agence)
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                @if($agence->logo)
                                    <img src="{{ asset('storage/' . $agence->logo) }}" 
                                         alt="{{ $agence->nom_agence }}" 
                                         style="width:30px;height:30px;object-fit:cover;border-radius:50%;border:1px solid var(--border);">
                                @endif
                                <div class="cell-main">{{ $agence->nom_agence ?? 'N/A' }}</div>
                            </div>
                        </td>
                        <td>{{ ($agence->user->prenom ?? '') . ' ' . ($agence->user->nom ?? '') }}</td>
                        <td>
                            <span class="status-pill status-refusee">Refusée</span>
                            <div class="cell-sub" style="margin-top:2px;font-size:11px;color:#C62828;">
                                {{ Str::limit($agence->motif_refus ?? 'Motif non spécifié', 50) }}
                            </div>
                        </td>
                        {{-- ✅ Utilisation de l'accesseur --}}
                        <td>{{ $agence->date_refus_formatee }}</td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                                <a href="{{ route('admin.agences.show', $agence) }}" class="btn btn-sm btn-ghost" title="Voir">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.agences.reactiver', $agence) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Réactiver" onclick="return confirm('Réactiver cette agence ? Elle sera remise en attente de validation.')">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection