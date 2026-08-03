@extends('layouts.dashboard')

@section('title', 'Tableau de bord — DoyaImmo')
@section('page_title', 'Tableau de bord')
@section('page_sub', 'Suivez vos besoins et vos échanges avec les agences')

@section('content')
<div class="view active">
    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-ic" style="background:var(--teal-soft); color:var(--teal);">
                    <i class="fa-solid fa-house-circle-check"></i>
                </div>
                <span class="kpi-trend">{{ $stats['actifs'] ?? 0 }} actifs</span>
            </div>
            <div class="kpi-value">{{ $stats['total_demandes'] }}</div>
            <div class="kpi-label">Besoins publiés</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-ic" style="background:var(--gold-soft); color:#8A6414;">
                    <i class="fa-solid fa-file-invoice"></i>
                </div>
                @if(($stats['nouvelles_offres'] ?? 0) > 0)
                    <span class="kpi-trend trend-up">+{{ $stats['nouvelles_offres'] }} nouvelles</span>
                @endif
            </div>
            <div class="kpi-value">{{ $stats['total_offres'] }}</div>
            <div class="kpi-label">Offres reçues</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-ic" style="background:var(--rust-soft); color:var(--rust);">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $stats['rendezvous_a_venir'] }}</div>
            <div class="kpi-label">Rendez-vous à venir</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-ic" style="background:var(--green-soft); color:#1E7A47;">
                    <i class="fa-solid fa-star"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $stats['total_evaluations'] }}</div>
            <div class="kpi-label">Avis donnés</div>
        </div>
    </div>

    <div class="grid-2">
        <!-- Mes besoins actifs -->
        <div class="panel">
            <div class="panel-head">
                <h3>Mes besoins actifs</h3>
                <a href="{{ route('particulier.demandes.index') }}" class="see-all">Voir tout →</a>
            </div>
            <div class="besoins-list">
                @forelse($derniersBesoins as $demande)
                    <div class="besoin-item">
                        <div class="besoin-info">
                            <div class="besoin-title">
                                {{ $demande->type_bien->label() }}
                                <span class="besoin-location">{{ $demande->zone_recherchee }}</span>
                            </div>
                            <div class="besoin-meta">
                                <span class="meta-pill">
                                    <i class="fa-regular fa-message"></i> {{ $demande->propositions->count() }} offres
                                </span>
                                <span class="meta-pill">
                                    <i class="fa-regular fa-coins"></i> {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F
                                </span>
                            </div>
                        </div>
                        <div class="besoin-status">
                            <span class="status-badge status-{{ $demande->statut->value }}">
                                <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                                {{ $demande->statut->label() }}
                            </span>
                            <a href="{{ route('particulier.demandes.show', $demande) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-small">
                        <i class="fa-regular fa-inbox"></i>
                        <p>Aucun besoin actif</p>
                        <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust btn-sm">
                            <i class="fa-solid fa-plus"></i> Publier un besoin
                        </a>
                    </div>
                @endforelse
            </div>
            <div class="panel-footer">
                <a href="{{ route('particulier.demandes.create') }}" class="btn btn-ghost btn-sm btn-block">
                    <i class="fa-solid fa-plus"></i> Nouveau besoin
                </a>
            </div>
        </div>

        <!-- Prochains rendez-vous -->
        <div class="panel">
            <div class="panel-head">
                <h3>Prochains rendez-vous</h3>
                <a href="{{ route('particulier.rendezvous.index') }}" class="see-all">Voir tout →</a>
            </div>
            <div class="rdv-list">
                @forelse($prochainsRendezVous as $rdv)
                    <div class="rdv-item">
                        <div class="rdv-date-box">
                            <span class="rdv-day">{{ $rdv->date_visite->format('d') }}</span>
                            <span class="rdv-month">{{ $rdv->date_visite->format('M') }}</span>
                        </div>
                        <div class="rdv-info">
                            <div class="rdv-title">
                                {{ $rdv->proposition->bien->titre ?? 'Visite' }}
                            </div>
                            <div class="rdv-details">
                                <span class="rdv-agency">
                                    <i class="fa-regular fa-building"></i> {{ $rdv->agence->nom_agence }}
                                </span>
                                <span class="rdv-time">
                                    <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="rdv-status">
                            <span class="status-badge status-{{ $rdv->statut->value }}">
                                <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                                {{ $rdv->statut->label() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-small">
                        <i class="fa-regular fa-calendar"></i>
                        <p>Aucun rendez-vous à venir</p>
                        <span style="font-size:12px;color:var(--muted);">Les rendez-vous apparaîtront ici une fois confirmés</span>
                    </div>
                @endforelse
            </div>
            <div class="panel-footer">
                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost btn-sm btn-block">
                    <i class="fa-regular fa-calendar"></i> Voir tous les rendez-vous
                </a>
            </div>
        </div>
    </div>

    <!-- Dernières offres reçues -->
    @if(isset($dernieresOffres) && $dernieresOffres->count() > 0)
        <div class="panel offres-panel">
            <div class="panel-head">
                <h3>Dernières offres reçues</h3>
                <a href="{{ route('particulier.propositions.index') }}" class="see-all">Voir tout →</a>
            </div>
            <div class="offres-list">
                @foreach($dernieresOffres as $offre)
                    <div class="offre-item">
                        <div class="offre-agency">
                            <div class="agency-avatar-sm">
                                {{ strtoupper(substr($offre->agence->nom_agence, 0, 1)) }}
                            </div>
                            <div>
                                <div class="offre-agency-name">{{ $offre->agence->nom_agence }}</div>
                                <div class="offre-demand">
                                    {{ $offre->demande->type_bien->label() }} — {{ $offre->demande->zone_recherchee }}
                                </div>
                            </div>
                        </div>
                        <div class="offre-right">
                            <div class="offre-price">{{ number_format($offre->prix_propose, 0, ',', ' ') }} FCFA</div>
                            <span class="status-badge status-{{ $offre->statut->value }}">
                                <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                                {{ $offre->statut->label() }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* ===================== KPI ===================== */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }

    .kpi-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 24px;
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
        color: var(--green);
        background: var(--green-soft);
        padding: 2px 10px;
        border-radius: 20px;
    }

    .kpi-value {
        font-family: var(--display);
        font-weight: 700;
        font-size: 28px;
    }

    .kpi-label {
        font-size: 13px;
        color: var(--muted);
    }

    /* ===================== GRID ===================== */
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 24px;
    }

    @media (max-width: 820px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }

    /* ===================== PANEL ===================== */
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

    .panel-footer {
        padding: 12px 20px;
        border-top: 1px solid var(--border);
        background: #FAFBFC;
    }

    /* ===================== BESOINS ===================== */
    .besoins-list {
        padding: 8px 0;
        flex: 1;
    }

    .besoin-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .besoin-item:last-child {
        border-bottom: none;
    }

    .besoin-item:hover {
        background: #F7F9FC;
    }

    .besoin-title {
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .besoin-location {
        font-weight: 400;
        font-size: 13px;
        color: var(--muted);
    }

    .besoin-meta {
        display: flex;
        gap: 12px;
        margin-top: 2px;
        flex-wrap: wrap;
    }

    .besoin-status {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    /* ===================== RENDEZ-VOUS ===================== */
    .rdv-list {
        padding: 8px 0;
        flex: 1;
    }

    .rdv-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 10px 20px;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .rdv-item:last-child {
        border-bottom: none;
    }

    .rdv-item:hover {
        background: #F7F9FC;
    }

    .rdv-date-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: #F7F9FC;
        border-radius: 8px;
        padding: 4px 10px;
        min-width: 44px;
        border: 1px solid var(--border);
        flex-shrink: 0;
    }

    .rdv-day {
        font-family: var(--display);
        font-weight: 700;
        font-size: 16px;
        line-height: 1.2;
    }

    .rdv-month {
        font-size: 9px;
        text-transform: uppercase;
        color: var(--muted);
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .rdv-info {
        flex: 1;
        min-width: 0;
    }

    .rdv-title {
        font-weight: 600;
        font-size: 14px;
    }

    .rdv-details {
        display: flex;
        gap: 12px;
        font-size: 12px;
        color: var(--muted);
        flex-wrap: wrap;
    }

    .rdv-details span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .rdv-status {
        flex-shrink: 0;
    }

    /* ===================== OFFRES ===================== */
    .offres-panel {
        margin-top: 0;
    }

    .offres-list {
        padding: 8px 0;
    }

    .offre-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 20px;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
        flex-wrap: wrap;
        gap: 8px;
    }

    .offre-item:last-child {
        border-bottom: none;
    }

    .offre-item:hover {
        background: #F7F9FC;
    }

    .offre-agency {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .agency-avatar-sm {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--rust-soft);
        color: var(--rust);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        flex-shrink: 0;
    }

    .offre-agency-name {
        font-weight: 600;
        font-size: 14px;
    }

    .offre-demand {
        font-size: 12px;
        color: var(--muted);
    }

    .offre-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .offre-price {
        font-weight: 700;
        color: var(--rust);
        font-size: 15px;
    }

    /* ===================== STATUS BADGES ===================== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Demandes */
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-en_cours {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-terminee {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-annulee {
        background: #FFEBEE;
        color: #C62828;
    }

    /* Rendez-vous */
    .status-planifie {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-confirme {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-termine {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-annule {
        background: #FFEBEE;
        color: #C62828;
    }

    /* Propositions */
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-acceptee {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-refusee {
        background: #FFEBEE;
        color: #C62828;
    }

    /* ===================== META PILL ===================== */
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 11px;
        color: var(--text-soft);
    }

    .meta-pill i {
        font-size: 10px;
    }

    /* ===================== EMPTY STATE ===================== */
    .empty-state-small {
        text-align: center;
        padding: 30px 20px;
        color: var(--muted);
    }

    .empty-state-small i {
        font-size: 32px;
        display: block;
        margin-bottom: 8px;
        opacity: 0.3;
    }

    .empty-state-small p {
        font-size: 13px;
        margin: 0 0 8px;
    }

    /* ===================== BOUTONS ===================== */
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
        padding: 4px 12px;
        font-size: 12px;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr;
        }

        .kpi-value {
            font-size: 22px;
        }

        .besoin-item {
            flex-direction: column;
            align-items: stretch;
            gap: 8px;
        }

        .besoin-status {
            justify-content: flex-start;
        }

        .rdv-item {
            flex-wrap: wrap;
            gap: 10px;
        }

        .rdv-status {
            width: 100%;
            margin-left: 58px;
        }

        .offre-item {
            flex-direction: column;
            align-items: stretch;
        }

        .offre-right {
            justify-content: space-between;
        }

        .offre-agency {
            flex-wrap: wrap;
        }
    }
</style>
@endpush