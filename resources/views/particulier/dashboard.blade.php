@extends('layouts.dashboard')

@section('title', 'Tableau de bord — DoyaImmo')
@section('page_title', 'Tableau de bord')
@section('page_sub', 'Suivez vos besoins et vos échanges avec les agences')

@section('content')
<div class="dashboard-wrapper">

    <!-- ============================================
         BANDEAU D'ACCUEIL
         ============================================ -->
    <div class="welcome-band">
        <div class="welcome-left">
            <div class="welcome-avatar">
                <span>{{ substr(Auth::user()->prenom ?? 'C', 0, 1) }}{{ substr(Auth::user()->nom ?? 'L', 0, 1) }}</span>
            </div>
            <div class="welcome-text">
                <h1>Bonjour, {{ Auth::user()->prenom ?? 'Client' }}</h1>
                <p>Retrouvez ici toutes vos informations</p>
            </div>
        </div>
        <div class="welcome-right">
            <span class="date-badge">
                <i class="fa-regular fa-calendar"></i>
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
    </div>

    <!-- ============================================
         STATISTIQUES SIMPLES
         ============================================ -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-house-circle-check"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_demandes'] ?? 0 }}</div>
                <div class="stat-label">Besoins publiés</div>
                @if(($stats['actifs'] ?? 0) > 0)
                    <span class="stat-sub"> {{ $stats['actifs'] }} actif(s)</span>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_offres'] ?? 0 }}</div>
                <div class="stat-label">Offres reçues</div>
                @if(($stats['nouvelles_offres'] ?? 0) > 0)
                    <span class="stat-sub" style="color:#1E7A47;"> +{{ $stats['nouvelles_offres'] }} nouvelles</span>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['rendezvous_a_venir'] ?? 0 }}</div>
                <div class="stat-label">Rendez-vous</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['total_evaluations'] ?? 0 }}</div>
                <div class="stat-label">Avis donnés</div>
            </div>
        </div>
    </div>

    <!-- ============================================
         ZONE DES ACTIONS PRINCIPALES (GROS BOUTONS)
         ============================================ -->
    <div class="actions-big">
        <a href="{{ route('particulier.demandes.create') }}" class="action-big primary">
            <i class="fa-solid fa-plus-circle"></i>
            <div>
                <span class="ab-title">Publier un besoin</span>
                <span class="ab-desc">Je cherche un logement</span>
            </div>
            <span class="ab-arrow">→</span>
        </a>
        <a href="{{ route('particulier.demandes.index') }}" class="action-big">
            <i class="fa-solid fa-list"></i>
            <div>
                <span class="ab-title">Mes besoins</span>
                <span class="ab-desc">Voir mes annonces</span>
            </div>
            <span class="ab-arrow">→</span>
        </a>
        <a href="{{ route('particulier.propositions.index') }}" class="action-big">
            <i class="fa-solid fa-file-invoice"></i>
            <div>
                <span class="ab-title">Mes offres</span>
                <span class="ab-desc">Les propositions des agences</span>
            </div>
            <span class="ab-arrow">→</span>
        </a>
        <a href="{{ route('particulier.rendezvous.index') }}" class="action-big">
            <i class="fa-solid fa-calendar-plus"></i>
            <div>
                <span class="ab-title">Mes rendez-vous</span>
                <span class="ab-desc">Visites à venir</span>
            </div>
            <span class="ab-arrow">→</span>
        </a>
    </div>

    <!-- ============================================
         MES BESOINS ACTIFS + MES PROCHAINS RENDEZ-VOUS (côte à côte)
         ============================================ -->
    <div class="two-columns">

        <!-- COLONNE GAUCHE : BESOINS ACTIFS -->
        <div class="col-card">
            <div class="col-header">
                <h3> Mes besoins actifs</h3>
                <a href="{{ route('particulier.demandes.index') }}" class="see-all">Voir tout →</a>
            </div>

            @forelse($derniersBesoins as $demande)
                <div class="besoin-item">
                    <div>
                        <div class="besoin-title">{{ $demande->type_bien->label() }}</div>
                        <div class="besoin-infos">
                            <span> {{ $demande->zone_recherchee }}</span>
                            <span> {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F</span>
                            <span> {{ $demande->propositions->count() }} offre(s)</span>
                        </div>
                    </div>
                    <div class="besoin-right">
                        <span class="status-badge status-{{ $demande->statut_value }}">
                            {{ $demande->statut_label }}
                        </span>
                        <a href="{{ route('particulier.demandes.show', $demande) }}" class="btn-small">Voir</a>
                    </div>
                </div>
            @empty
                <div class="empty-box">
                    <i class="fa-regular fa-inbox"></i>
                    <p>Vous n'avez pas encore de besoin actif</p>
                    <a href="{{ route('particulier.demandes.create') }}" class="btn-primary">
                        ➕ Publier mon premier besoin
                    </a>
                </div>
            @endforelse
        </div>

        <!-- COLONNE DROITE : RENDEZ-VOUS -->
        <div class="col-card">
            <div class="col-header">
                <h3> Mes prochains rendez-vous</h3>
                <a href="{{ route('particulier.rendezvous.index') }}" class="see-all">Voir tout →</a>
            </div>

            @forelse($prochainsRendezVous as $rdv)
                <div class="rdv-item">
                    <div class="rdv-date">
                        <span class="rdv-day">{{ $rdv->date_visite->format('d') }}</span>
                        <span class="rdv-month">{{ $rdv->date_visite->format('M') }}</span>
                    </div>
                    <div class="rdv-info">
                        <div class="rdv-title">{{ $rdv->proposition->bien->titre ?? 'Visite' }}</div>
                        <div class="rdv-desc">
                            <span> {{ $rdv->agence->nom_agence }}</span>
                            <span> {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}</span>
                        </div>
                    </div>
                    <div class="rdv-status">
                        <span class="status-badge status-{{ $rdv->statut_value }}">
                            {{ $rdv->statut_label }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="empty-box">
                    <i class="fa-regular fa-calendar"></i>
                    <p>Aucun rendez-vous à venir</p>
                    <span style="font-size:13px;color:var(--muted);">Les rendez-vous apparaîtront ici</span>
                </div>
            @endforelse
        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
/* ============================================
   GLOBAL
   ============================================ */
.dashboard-wrapper {
    max-width: 100%;
}

/* ============================================
   BANDEAU D'ACCUEIL
   ============================================ */
.welcome-band {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px;
    background: #fff;
    border-radius: 12px;
    border: 1px solid var(--border);
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
}

.welcome-left {
    display: flex;
    align-items: center;
    gap: 14px;
}

.welcome-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--rust);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    flex-shrink: 0;
}

.welcome-text h1 {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
}

.welcome-text p {
    font-size: 13px;
    color: var(--muted);
    margin: 0;
}

.welcome-right {
    display: flex;
    align-items: center;
    gap: 10px;
}

.date-badge {
    font-size: 13px;
    color: var(--text-soft);
    background: var(--border);
    padding: 4px 14px;
    border-radius: 20px;
}

/* ============================================
   STATISTIQUES
   ============================================ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}

.stat-card {
    background: #fff;
    border-radius: 12px;
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    border: 1px solid var(--border);
}

.stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: #F7F9FC;
    color: var(--text-soft);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

.stat-value {
    font-weight: 700;
    font-size: 20px;
    line-height: 1.2;
    color: var(--ink);
}

.stat-label {
    font-size: 12px;
    color: var(--muted);
}

.stat-sub {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-soft);
}

/* ============================================
   GROS BOUTONS D'ACTIONS
   ============================================ */
.actions-big {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 24px;
}

.action-big {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    text-decoration: none;
    color: var(--ink);
    transition: all 0.2s;
}

.action-big:hover {
    border-color: var(--rust);
    background: #FAFBFC;
    transform: translateY(-2px);
}

.action-big.primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}

.action-big.primary:hover {
    background: #9A4523;
}

.action-big i {
    font-size: 22px;
    flex-shrink: 0;
    width: 32px;
    text-align: center;
}

.action-big.primary i {
    color: #fff;
}

.action-big .ab-title {
    display: block;
    font-weight: 600;
    font-size: 14px;
}

.action-big .ab-desc {
    font-size: 12px;
    color: var(--muted);
}

.action-big.primary .ab-desc {
    color: rgba(255,255,255,0.8);
}

.action-big .ab-arrow {
    margin-left: auto;
    font-size: 18px;
    color: var(--muted);
}

.action-big.primary .ab-arrow {
    color: #fff;
}

/* ============================================
   DEUX COLONNES (BESOINS + RENDEZ-VOUS côte à côte)
   ============================================ */
.two-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.col-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.col-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 16px 12px;
    border-bottom: 1px solid var(--border);
    background: #FAFBFC;
}

.col-header h3 {
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
}

.see-all {
    font-size: 12px;
    color: var(--rust);
    text-decoration: none;
    font-weight: 600;
}

.see-all:hover {
    text-decoration: underline;
}

/* ============================================
   BESOINS (dans la colonne)
   ============================================ */
.besoin-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 16px;
    border-bottom: 1px solid var(--border);
}

.besoin-item:last-child {
    border-bottom: none;
}

.besoin-title {
    font-weight: 600;
    font-size: 14px;
    color: var(--ink);
}

.besoin-infos {
    display: flex;
    gap: 12px;
    font-size: 12px;
    color: var(--muted);
    margin-top: 2px;
    flex-wrap: wrap;
}

.besoin-infos span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.besoin-right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.btn-small {
    padding: 3px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    color: var(--rust);
    background: var(--rust-soft);
    text-decoration: none;
    transition: all 0.2s;
}

.btn-small:hover {
    background: var(--rust);
    color: #fff;
}

/* ============================================
   RENDEZ-VOUS (dans la colonne)
   ============================================ */
.rdv-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 16px;
    border-bottom: 1px solid var(--border);
}

.rdv-item:last-child {
    border-bottom: none;
}

.rdv-date {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #F7F9FC;
    border-radius: 8px;
    padding: 2px 12px;
    min-width: 42px;
    border: 1px solid var(--border);
    flex-shrink: 0;
}

.rdv-day {
    font-weight: 700;
    font-size: 16px;
    line-height: 1.2;
}

.rdv-month {
    font-size: 8px;
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
    font-size: 13px;
    color: var(--ink);
}

.rdv-desc {
    display: flex;
    gap: 10px;
    font-size: 11px;
    color: var(--muted);
    flex-wrap: wrap;
}

.rdv-desc span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.rdv-status {
    flex-shrink: 0;
}

/* ============================================
   STATUS BADGES
   ============================================ */
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
.status-terminee {
    background: #E8F5E9;
    color: #1E7A47;
}

/* ============================================
   EMPTY BOX
   ============================================ */
.empty-box {
    text-align: center;
    padding: 24px 16px;
    color: var(--muted);
}

.empty-box i {
    font-size: 28px;
    display: block;
    margin-bottom: 6px;
    opacity: 0.3;
}

.empty-box p {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-soft);
    margin: 0 0 8px;
}

.empty-box span {
    font-size: 12px;
    color: var(--muted);
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    background: var(--rust);
    color: #fff;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
}

.btn-primary:hover {
    background: #9A4523;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 1024px) {
    .actions-big {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 820px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .two-columns {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .actions-big {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    
    .welcome-band {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
        padding: 14px;
    }
    
    .welcome-left {
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-right {
        justify-content: center;
    }
}

@media (max-width: 600px) {
    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .stat-card {
        padding: 10px 12px;
        gap: 10px;
    }
    
    .stat-icon {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
    
    .stat-value {
        font-size: 16px;
    }
    
    .stat-label {
        font-size: 10px;
    }
    
    .stat-sub {
        font-size: 10px;
    }
    
    .actions-big {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .action-big {
        padding: 12px 14px;
        gap: 10px;
    }
    
    .action-big .ab-desc {
        display: none;
    }
    
    .action-big i {
        font-size: 18px;
        width: 24px;
    }
    
    .action-big .ab-arrow {
        font-size: 14px;
    }
    
    .two-columns {
        gap: 10px;
    }
    
    .besoin-item {
        flex-direction: column;
        align-items: stretch;
        gap: 6px;
    }
    
    .besoin-right {
        justify-content: flex-start;
    }
    
    .rdv-item {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .rdv-status {
        width: 100%;
        margin-left: 54px;
    }
}

@media (max-width: 400px) {
    .stats-grid {
        grid-template-columns: 1fr 1fr;
        gap: 6px;
    }
    
    .stat-card {
        padding: 8px 10px;
        gap: 8px;
    }
    
    .stat-icon {
        width: 26px;
        height: 26px;
        font-size: 10px;
    }
    
    .stat-value {
        font-size: 14px;
    }
    
    .action-big {
        padding: 10px 12px;
    }
    
    .action-big .ab-title {
        font-size: 13px;
    }
}
</style>
@endpush