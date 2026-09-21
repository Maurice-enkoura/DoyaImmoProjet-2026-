@extends('layouts.admin')

@section('title', 'Statistiques — Administration DoyaImmo')
@section('page_title', 'Statistiques et rapports')
@section('page_sub', 'Analyse détaillée de la plateforme DoyaImmo')

@section('content')
<div class="stats-page">

    {{-- ═══ ACTIONS ═══ --}}
    <div class="page-actions">
        <a href="{{ route('admin.statistiques', ['refresh' => 1]) }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-rotate"></i> Rafraîchir les stats
        </a>
    </div>

    <!-- ═══ STATS GLOBALES ═══ -->
    <div class="stats-grid-3">
        <div class="stat-card">
            <span class="stat-icon"><i class="fa-solid fa-users"></i></span>
            <div class="stat-value">{{ number_format($stats['users']['total'] ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-label">Utilisateurs</div>
            <div class="stat-sub">
                @php
                    $agencesCount      = $stats['users']['par_role']->where('role', 'agence')->first()->total ?? 0;
                    $particuliersCount = $stats['users']['par_role']->where('role', 'particulier')->first()->total ?? 0;
                    $adminsCount       = $stats['users']['par_role']->where('role', 'admin')->first()->total ?? 0;
                @endphp
                <span class="stat-sub__item stat-sub__item--success">{{ $agencesCount }} agences</span>
                <span class="stat-sub__item stat-sub__item--info">{{ $particuliersCount }} particuliers</span>
                <span class="stat-sub__item stat-sub__item--danger">{{ $adminsCount }} admins</span>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-icon"><i class="fa-solid fa-building"></i></span>
            <div class="stat-value">{{ number_format($stats['agences']['total'] ?? 0, 0, ',', ' ') }}</div>
            <div class="stat-label">Agences</div>
            <div class="stat-sub">
                <span class="stat-sub__item stat-sub__item--success">{{ $stats['agences']['validees'] ?? 0 }} validées</span>
                <span class="stat-sub__item stat-sub__item--warning">{{ $stats['agences']['en_attente'] ?? 0 }} en attente</span>
                <span class="stat-sub__item stat-sub__item--danger">{{ $stats['agences']['refusees'] ?? 0 }} refusées</span>
                <span class="stat-sub__item stat-sub__item--muted">{{ $stats['agences']['bloquees'] ?? 0 }} bloquées</span>
            </div>
        </div>

        <div class="stat-card">
            <span class="stat-icon"><i class="fa-solid fa-coins"></i></span>
            <div class="stat-value">{{ number_format($stats['abonnements']['revenus'] ?? 0, 0, ',', ' ') }} F</div>
            <div class="stat-label">Revenus des abonnements</div>
            <div class="stat-sub">
                <span class="stat-sub__item stat-sub__item--success">
                    {{ $stats['abonnements']['actifs'] ?? 0 }} actifs
                </span>
                <span class="stat-sub__item stat-sub__item--muted">
                    sur {{ $stats['abonnements']['total'] ?? 0 }} au total
                </span>
            </div>
        </div>
    </div>

    <!-- ═══ ACTIVITÉ ═══ -->
    <div class="stats-grid-3">
        <div class="stat-card stat-card--success">
            <div class="stat-value" style="color:#1E7A47;">{{ $stats['activite']['aujourd_hui'] ?? 0 }}</div>
            <div class="stat-label"><i class="fa-regular fa-calendar"></i> Aujourd'hui</div>
        </div>
        <div class="stat-card stat-card--info">
            <div class="stat-value" style="color:#0D47A1;">{{ $stats['activite']['cette_semaine'] ?? 0 }}</div>
            <div class="stat-label"><i class="fa-regular fa-calendar-days"></i> Cette semaine</div>
        </div>
        <div class="stat-card stat-card--warning">
            <div class="stat-value" style="color:#E65100;">{{ $stats['activite']['ce_mois'] ?? 0 }}</div>
            <div class="stat-label"><i class="fa-regular fa-calendar-check"></i> Ce mois</div>
        </div>
    </div>

    <!-- ═══ DEMANDES + PROPOSITIONS ═══ -->
    <div class="stats-grid-2">
        <div class="panel">
            <div class="panel-head">
                <h3><i class="fa-solid fa-house-circle-check"></i> Demandes</h3>
            </div>
            <div class="stat-grid">
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
            <div class="panel-sub">
                <strong>Types d'opération :</strong>
                @foreach($stats['demandes']['par_type_operation'] ?? [] as $item)
                    @php
                        $typeValue = is_object($item->type_operation)
                            ? $item->type_operation->value
                            : $item->type_operation;
                    @endphp
                    <span class="meta-pill">{{ $typeValue }}: {{ $item->total }}</span>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h3><i class="fa-solid fa-handshake"></i> Propositions</h3>
            </div>
            <div class="stat-grid">
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
                <div class="stat-item">
                    <span class="label">Terminées</span>
                    <span class="value" style="color:#0D47A1;">{{ $stats['propositions']['par_statut']->where('statut', 'terminee')->first()->total ?? 0 }}</span>
                </div>
            </div>
            <div class="panel-sub">
                <strong>Prix moyen :</strong> {{ number_format($stats['propositions']['prix_moyen'] ?? 0, 0, ',', ' ') }} FCFA
                <span class="sep">·</span>
                <strong>Min :</strong> {{ number_format($stats['propositions']['prix_min'] ?? 0, 0, ',', ' ') }} F
                <span class="sep">·</span>
                <strong>Max :</strong> {{ number_format($stats['propositions']['prix_max'] ?? 0, 0, ',', ' ') }} F
            </div>
        </div>
    </div>

    <!-- ═══ RENDEZ-VOUS (NOUVEAU) ═══ -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-calendar-check"></i> Rendez-vous</h3>
            @if(isset($stats['rendezvous']['taux_presence']))
                <span class="panel-badge panel-badge--success">
                    Taux de présence : {{ $stats['rendezvous']['taux_presence'] }}%
                </span>
            @endif
        </div>

        <div class="stat-grid stat-grid--6">
            <div class="stat-item">
                <span class="label">Total</span>
                <span class="value">{{ $stats['rendezvous']['total'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Planifiés</span>
                <span class="value" style="color:#E65100;">{{ $stats['rendezvous']['planifies'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Confirmés</span>
                <span class="value" style="color:#0D47A1;">{{ $stats['rendezvous']['confirmes'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Terminés</span>
                <span class="value" style="color:#1E7A47;">{{ $stats['rendezvous']['termines'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Annulés</span>
                <span class="value" style="color:#C62828;">{{ $stats['rendezvous']['annules'] ?? 0 }}</span>
            </div>
            <div class="stat-item">
                <span class="label">Client absent</span>
                <span class="value" style="color:#6C6C70;">{{ $stats['rendezvous']['client_absent'] ?? 0 }}</span>
            </div>
        </div>
    </div>

    <!-- ═══ BIENS + SIGNALEMENTS ═══ -->
    <div class="stats-grid-2">
        <div class="panel">
            <div class="panel-head">
                <h3><i class="fa-solid fa-house"></i> Biens immobiliers</h3>
            </div>
            <div class="stat-grid">
                <div class="stat-item">
                    <span class="label">Total</span>
                    <span class="value">{{ $stats['biens']['total'] ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="label">Disponibles</span>
                    <span class="value" style="color:#1E7A47;">{{ $stats['biens']['disponibles'] ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="label">En vedette</span>
                    <span class="value" style="color:#F5A623;">{{ $stats['biens']['en_vedette'] ?? 0 }}</span>
                </div>
            </div>
            <div class="panel-sub">
                <strong>Types :</strong>
                @foreach($stats['biens']['par_type'] ?? [] as $item)
                    @php
                        $typeValue = is_object($item->type_bien) ? $item->type_bien->value : $item->type_bien;
                    @endphp
                    <span class="meta-pill">{{ $typeValue }}: {{ $item->total }}</span>
                @endforeach
            </div>
            <div class="panel-sub">
                <strong>Prix moyen :</strong> {{ number_format($stats['biens']['prix_moyen'] ?? 0, 0, ',', ' ') }} FCFA
                <span class="sep">·</span>
                <strong>Surface moyenne :</strong> {{ number_format($stats['biens']['surface_moyenne'] ?? 0, 1) }} m²
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h3><i class="fa-solid fa-flag"></i> Signalements</h3>
            </div>
            <div class="stat-grid">
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
            <div class="panel-sub">
                <strong>Motifs :</strong>
                @foreach($stats['signalements']['par_motif'] ?? [] as $item)
                    <span class="meta-pill">{{ $item->motif }}: {{ $item->total }}</span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ═══ ÉVALUATIONS + ABONNEMENTS ═══ -->
    <div class="stats-grid-2">
        <div class="panel">
            <div class="panel-head">
                <h3><i class="fa-solid fa-star" style="color:#F5A623;"></i> Évaluations</h3>
            </div>
            <div class="stat-grid stat-grid--3">
                <div class="stat-item stat-item--centered stat-item--gold">
                    <span class="label">Total</span>
                    <span class="value value-lg">{{ $stats['evaluations']['total'] ?? 0 }}</span>
                </div>
                <div class="stat-item stat-item--centered stat-item--gold">
                    <span class="label">Moyenne</span>
                    <span class="value value-lg" style="color:#F5A623;">{{ number_format($stats['evaluations']['note_moyenne'] ?? 0, 1) }} ★</span>
                </div>
                <div class="stat-item stat-item--centered stat-item--gold">
                    <span class="label">Écart</span>
                    <span class="value value-sm" style="color:var(--muted);">
                        {{ number_format($stats['evaluations']['note_min'] ?? 0, 1) }} - {{ number_format($stats['evaluations']['note_max'] ?? 0, 1) }}
                    </span>
                </div>
            </div>
            <div class="panel-sub panel-sub--center">
                Répartition : 
                @foreach($stats['evaluations']['repartition_notes'] ?? [] as $item)
                    <span class="meta-pill">{{ $item->note }}★ ({{ $item->total }})</span>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <h3><i class="fa-solid fa-award"></i> Abonnements</h3>
            </div>
            <div class="stat-grid">
                <div class="stat-item">
                    <span class="label">Total</span>
                    <span class="value">{{ $stats['abonnements']['total'] ?? 0 }}</span>
                </div>
                <div class="stat-item">
                    <span class="label">Actifs</span>
                    <span class="value" style="color:#1E7A47;">{{ $stats['abonnements']['actifs'] ?? 0 }}</span>
                </div>
            </div>
            <div class="panel-sub">
                <strong>Par formule :</strong>
                @foreach($stats['abonnements']['par_formule'] ?? [] as $item)
                    @php
                        $formuleLabel = is_object($item->formule) && method_exists($item->formule, 'label')
                            ? $item->formule->label()
                            : ucfirst((string) $item->formule);
                    @endphp
                    <span class="meta-pill">{{ $formuleLabel }}: {{ $item->total }}</span>
                @endforeach
            </div>
            <div class="panel-sub">
                <strong>Revenus par formule :</strong>
                @foreach($stats['abonnements']['revenus_par_formule'] ?? [] as $item)
                    @php
                        $formuleLabel = is_object($item->formule) && method_exists($item->formule, 'label')
                            ? $item->formule->label()
                            : ucfirst((string) $item->formule);
                    @endphp
                    <span class="meta-pill meta-pill--success">
                        {{ $formuleLabel }}: {{ number_format($item->total, 0, ',', ' ') }} F
                    </span>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ═══ TOP AGENCES ═══ -->
    <div class="panel">
        <div class="panel-head">
            <h3><i class="fa-solid fa-trophy" style="color:#F5A623;"></i> Top 10 agences les mieux notées</h3>
        </div>
        @if(isset($stats['evaluations']['top_agences']) && $stats['evaluations']['top_agences']->count() > 0)
            @foreach($stats['evaluations']['top_agences'] as $index => $agence)
                <div class="top-agence-item">
                    <div class="rank
                        @if($index === 0) rank--gold
                        @elseif($index === 1) rank--silver
                        @elseif($index === 2) rank--bronze
                        @endif">
                        {{ $index + 1 }}
                    </div>
                    <div class="info">
                        <div class="name">{{ $agence->nom_agence }}</div>
                        <div class="details">{{ $agence->evaluations_count }} avis</div>
                    </div>
                    <div class="score">{{ number_format($agence->evaluations_avg_note ?? 0, 1) }} ★</div>
                </div>
            @endforeach
        @else
            <p class="empty-text">Aucune évaluation disponible.</p>
        @endif
    </div>

    <!-- ═══ EXPORT ═══ -->
    <div class="export-section">
        <form action="{{ route('admin.statistiques.export') }}" method="GET" class="export-form">
            <select name="type" class="export-select">
                <option value="global">Global</option>
                <option value="users">Utilisateurs</option>
                <option value="agences">Agences</option>
                <option value="propositions">Propositions</option>
                <option value="rendezvous">Rendez-vous</option>
            </select>

            <input type="hidden" name="format" value="csv">

            <button type="submit" class="btn btn-rust btn-sm">
                <i class="fa-solid fa-download"></i> Exporter (CSV)
            </button>
        </form>
    </div>

</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   STATISTIQUES — Admin
   ═══════════════════════════════════════════════════════════ */

.stats-page {
    --card-radius: 12px;
}

/* ═══ PAGE ACTIONS ═══ */
.page-actions {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 16px;
    gap: 8px;
    flex-wrap: wrap;
}

/* ═══ GRIDS ═══ */
.stats-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

.stats-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

/* ═══ STAT CARDS ═══ */
.stat-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--card-radius);
    padding: 20px 24px;
    text-align: center;
    transition: transform .15s, box-shadow .15s;
}
.stat-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(0,0,0,.05);
}

.stat-icon {
    font-size: 26px;
    margin-bottom: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    border-radius: 12px;
    background: #FFF8E1;
    color: var(--rust);
}
.stat-icon i { font-size: 22px; }

.stat-value {
    font-family: var(--display);
    font-weight: 800;
    font-size: 28px;
    color: var(--ink);
    line-height: 1.1;
    letter-spacing: -0.5px;
}
.stat-label {
    font-size: 13px;
    color: var(--muted);
    margin-top: 4px;
    font-weight: 500;
}

.stat-sub {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 6px;
    margin-top: 10px;
    font-size: 12px;
}
.stat-sub__item {
    padding: 2px 10px;
    border-radius: 999px;
    font-weight: 600;
}
.stat-sub__item--success { background: #E8F5E9; color: #1E7A47; }
.stat-sub__item--info    { background: #E3F2FD; color: #0D47A1; }
.stat-sub__item--danger  { background: #FFEBEE; color: #C62828; }
.stat-sub__item--warning { background: #FFF8E1; color: #E65100; }
.stat-sub__item--muted   { background: #EFEFF4; color: #6C6C70; }

.stat-card--success { background: linear-gradient(135deg, #E8F5E9, #F1F8F4); }
.stat-card--info    { background: linear-gradient(135deg, #E3F2FD, #F1F7FE); }
.stat-card--warning { background: linear-gradient(135deg, #FFF8E1, #FFFDF5); }

/* ═══ PANELS ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--card-radius);
    padding: 20px 22px;
    margin-bottom: 20px;
}

.panel-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}
.panel-head h3 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
    display: flex;
    align-items: center;
    gap: 8px;
}
.panel-head h3 i { color: var(--rust); font-size: 14px; }

.panel-badge {
    font-size: 11.5px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
}
.panel-badge--success { background: #E8F5E9; color: #1E7A47; }

.panel-sub {
    margin-top: 12px;
    font-size: 12.5px;
    color: var(--text-soft);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
}
.panel-sub--center { justify-content: center; }
.panel-sub strong { color: var(--ink); font-weight: 600; }
.panel-sub .sep { color: var(--border); }

/* ═══ STAT GRID ═══ */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 8px;
}
.stat-grid--3 { grid-template-columns: repeat(3, 1fr); }
.stat-grid--6 { grid-template-columns: repeat(6, 1fr); }

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    background: #F7F9FC;
    border-radius: 8px;
    border-left: 4px solid var(--rust);
    min-height: 44px;
}
.stat-item--centered {
    flex-direction: column;
    justify-content: center;
    text-align: center;
    gap: 2px;
}
.stat-item--gold { border-left-color: #F5A623; }

.stat-item .label {
    font-size: 12.5px;
    color: var(--text-soft);
    font-weight: 500;
}
.stat-item .value {
    font-family: var(--display);
    font-weight: 800;
    font-size: 15px;
    color: var(--ink);
}
.stat-item .value-lg { font-size: 20px; }
.stat-item .value-sm { font-size: 14px; }

/* ═══ TOP AGENCES ═══ */
.top-agence-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 4px;
    border-bottom: 1px solid var(--border);
}
.top-agence-item:last-child { border-bottom: none; }

.top-agence-item .rank {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #EFEFF4;
    color: var(--text-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-weight: 800;
    font-size: 13px;
    flex-shrink: 0;
}
.top-agence-item .rank--gold   { background: linear-gradient(135deg, #F5A623, #E8901A); color: #fff; }
.top-agence-item .rank--silver { background: linear-gradient(135deg, #C0C0C0, #9E9E9E); color: #fff; }
.top-agence-item .rank--bronze { background: linear-gradient(135deg, #CD7F32, #A5682A); color: #fff; }

.top-agence-item .info { flex: 1; min-width: 0; }
.top-agence-item .info .name {
    font-weight: 600;
    font-size: 13.5px;
    color: var(--ink);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.top-agence-item .info .details {
    font-size: 11.5px;
    color: var(--muted);
    margin-top: 2px;
}
.top-agence-item .score {
    font-family: var(--display);
    font-weight: 800;
    color: #F5A623;
    font-size: 16px;
    flex-shrink: 0;
}

/* ═══ META PILL ═══ */
.meta-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    background: #F0F2F5;
    color: var(--text-soft);
}
.meta-pill--success { background: #E8F5E9; color: #1E7A47; }

/* ═══ EMPTY TEXT ═══ */
.empty-text {
    color: var(--muted);
    font-size: 13px;
    text-align: center;
    padding: 20px 0;
    margin: 0;
}

/* ═══ EXPORT ═══ */
.export-section {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
}
.export-form {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items: center;
}
.export-select {
    padding: 8px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    background: #fff;
    font-family: inherit;
    cursor: pointer;
}
.export-select:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 3px var(--rust-soft);
}

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all .2s;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; border-color: #9A4523; color: #fff; }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: var(--border); color: var(--ink); }
.btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 6px; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 1024px) {
    .stats-grid-3 { grid-template-columns: repeat(2, 1fr); }
    .stat-grid--6 { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 768px) {
    .stats-grid-3 { grid-template-columns: 1fr 1fr; gap: 10px; }
    .stats-grid-2 { grid-template-columns: 1fr; gap: 14px; }

    .stat-card { padding: 14px 16px; }
    .stat-value { font-size: 22px; }
    .stat-label { font-size: 12px; }
    .stat-icon { width: 44px; height: 44px; }
    .stat-icon i { font-size: 18px; }

    .panel { padding: 16px; }
    .panel-head { flex-direction: column; align-items: flex-start; gap: 8px; }
    .panel-head h3 { font-size: 14px; }

    .stat-grid--3,
    .stat-grid--6 { grid-template-columns: repeat(2, 1fr); }

    .stat-item { padding: 8px 10px; font-size: 12px; min-height: 38px; }
    .stat-item .label { font-size: 11.5px; }
    .stat-item .value { font-size: 14px; }
    .stat-item .value-lg { font-size: 18px; }

    .top-agence-item .rank { width: 28px; height: 28px; font-size: 12px; }
    .top-agence-item .score { font-size: 14px; }

    .export-section { justify-content: stretch; }
    .export-form { width: 100%; flex-direction: column; align-items: stretch; }
    .export-select, .btn { width: 100%; justify-content: center; }
}

@media (max-width: 480px) {
    .stats-grid-3 { grid-template-columns: 1fr; }
    .stat-grid--3,
    .stat-grid--6 { grid-template-columns: 1fr; }

    .stat-value { font-size: 20px; }
    .stat-label { font-size: 11.5px; }
    .stat-sub { font-size: 11px; }
    .stat-sub__item { padding: 2px 8px; }
}

@media (prefers-reduced-motion: reduce) {
    * { transition-duration: 0.01ms !important; }
}
</style>
@endpush