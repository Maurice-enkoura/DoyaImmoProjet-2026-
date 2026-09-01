@extends('layouts.dashboard-agence')

@section('title', 'Tableau de bord — DoyaImmo')
@section('page_title', 'Tableau de bord')
@section('page_sub', 'Vue d\'ensemble de votre activité')

@section('content')
<div class="dashboard-wrapper">

    <!-- ============================================
         BANDEAU D'ACCUEIL
         ============================================ -->
    <div class="welcome-band">
        <div class="welcome-left">
            <div class="welcome-avatar">
                <span>{{ substr(Auth::user()->prenom ?? 'A', 0, 1) }}{{ substr(Auth::user()->nom ?? 'G', 0, 1) }}</span>
            </div>
            <div class="welcome-text">
                <h1>Bonjour, {{ Auth::user()->prenom ?? 'Agence' }}</h1>
                <p>Voici ce qui se passe aujourd'hui dans votre agence</p>
            </div>
        </div>
        <div class="welcome-right">
            <span class="date-badge">
                <i class="fa-regular fa-calendar"></i>
                {{ now()->format('d/m/Y') }}
            </span>
            @if($abonnementActuel)
                <span class="plan-badge">
                    {{ $abonnementActuel->formule->label() }}
                </span>
            @endif
        </div>
    </div>

    <!-- ============================================
         ALERTES
         ============================================ -->
    @if(!$abonnementActuel)
        <div class="alert-simple alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Abonnement requis</strong>
                <span>Souscrivez un abonnement pour publier des biens</span>
            </div>
            <a href="{{ route('agence.abonnement') }}">Voir les offres →</a>
        </div>
    @elseif($offresRestantes <= 0 && $limiteOffres !== PHP_INT_MAX)
        <div class="alert-simple alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <strong>Quota d'offres atteint</strong>
                <span>Vous avez utilisé toutes vos offres ce mois-ci</span>
            </div>
            <a href="{{ route('agence.abonnement') }}">Upgrader →</a>
        </div>
    @endif

    <!-- ============================================
         STATISTIQUES
         ============================================ -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-inbox"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['besoins_disponibles'] ?? 0 }}</div>
                <div class="stat-label">Besoins disponibles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div>
                <div class="stat-value">{{ $stats['offres_envoyees'] ?? 0 }}</div>
                <div class="stat-label">Offres envoyées</div>
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
                <div class="stat-value">{{ number_format($stats['note_moyenne'] ?? 0, 1) }}</div>
                <div class="stat-label">Note moyenne</div>
            </div>
        </div>
    </div>

    <!-- ============================================
         SECTION PRINCIPALE : ABONNEMENT + RENDEZ-VOUS + ACTIVITÉ
         ============================================ -->
    <div class="dashboard-main">

        <!-- COLONNE GAUCHE -->
        <div class="dashboard-col-left">

            <!-- CARTE ABONNEMENT -->
            <div class="card">
                <div class="card-header">
                    <span class="card-label">ABONNEMENT</span>
                    @if($abonnementActuel)
                        <span class="badge-status active">Actif</span>
                    @else
                        <span class="badge-status inactive">Inactif</span>
                    @endif
                </div>
                <div class="card-body">
                    <h3 class="card-title">{{ $abonnementActuel ? $abonnementActuel->formule->label() : 'Aucun abonnement' }}</h3>

                    @if($abonnementActuel)
                        <div class="card-date">
                            <i class="fa-regular fa-calendar"></i>
                            Valable jusqu'au <strong>{{ $abonnementActuel->date_fin->format('d/m/Y') }}</strong>
                            <span class="days-badge">1 mois</span>
                        </div>

                        <div class="offres-block">
                            <div class="offres-label">Offres restantes</div>
                            <div class="offres-numbers">
                                <span class="offres-value {{ $offresRestantes > 0 ? '' : 'zero' }}">
                                    {{ $limiteOffres === PHP_INT_MAX ? '∞' : $offresRestantes }}
                                </span>
                                <span class="offres-total">/ {{ $limiteOffres === PHP_INT_MAX ? '∞' : $limiteOffres }}</span>
                                @if($limiteOffres !== PHP_INT_MAX)
                                    <span class="offres-pourcent">{{ $pourcentageOffres }}%</span>
                                @endif
                            </div>
                        </div>

                        @if($limiteOffres > 0 && $limiteOffres !== PHP_INT_MAX)
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:{{ $pourcentageOffres }}%;background:{{ $pourcentageOffres > 80 ? '#C62828' : ($pourcentageOffres > 60 ? '#D4AF37' : 'var(--rust)') }};"></div>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fa-regular fa-clock"></i>
                            <p>Vous n'avez pas d'abonnement actif</p>
                            <span>Souscrivez un plan pour démarrer</span>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('agence.abonnement') }}" class="btn-primary">
                        {{ $abonnementActuel ? 'Gérer mon abonnement' : 'Souscrire un abonnement' }}
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- CARTE RENDEZ-VOUS -->
            <div class="card">
                <div class="card-header">
                    <span class="card-label">RENDEZ-VOUS</span>
                    <a href="{{ route('agence.rendezvous.index') }}" class="card-link">Voir tout →</a>
                </div>
                <div class="card-body">
                    @if($prochainsRendezVous->count() > 0)
                        @foreach($prochainsRendezVous->take(3) as $rdv)
                            <div class="rdv-item">
                                <div class="rdv-info">
                                    <div class="rdv-title">{{ $rdv->proposition->bien->titre ?? 'Visite' }}</div>
                                    <div class="rdv-desc">{{ $rdv->particulier->user->prenom ?? '' }} · {{ $rdv->date_visite->format('d/m/Y') }} à {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}</div>
                                </div>
                                <span class="rdv-status {{ $rdv->statut->value === 'confirme' ? 'confirmed' : 'planned' }}">
                                    {{ $rdv->statut->label() }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fa-regular fa-calendar-plus"></i>
                            <p>Aucun rendez-vous</p>
                            <span>Les rendez-vous apparaîtront ici</span>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('agence.rendezvous.index') }}" class="btn-secondary">
                        Voir tous les rendez-vous
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- COLONNE DROITE : ACTIVITÉ RÉCENTE + AVIS -->
        <div class="dashboard-col-right">

            <!-- ACTIVITÉ RÉCENTE -->
            <div class="card">
                <div class="card-header">
                    <span class="card-label">ACTIVITÉ RÉCENTE</span>
                    <a href="{{ route('agence.historique') }}" class="card-link">Voir tout →</a>
                </div>
                <div class="card-body">
                    @if($derniersBesoins->count() > 0 || $prochainsRendezVous->count() > 0)
                        @foreach($derniersBesoins->take(3) as $besoin)
                            <div class="activity-item">
                                <div class="activity-dot"></div>
                                <div class="activity-content">
                                    <div class="activity-title">{{ $besoin->type_bien->label() }}</div>
                                    <div class="activity-desc">{{ $besoin->zone_recherchee }} · {{ number_format($besoin->budget_maximum, 0, ',', ' ') }} F</div>
                                </div>
                                <span class="activity-time">{{ $besoin->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach

                        @foreach($prochainsRendezVous->take(2) as $rdv)
                            <div class="activity-item">
                                <div class="activity-dot" style="background:var(--rust);"></div>
                                <div class="activity-content">
                                    <div class="activity-title">{{ $rdv->proposition->bien->titre ?? 'Visite' }}</div>
                                    <div class="activity-desc">{{ $rdv->particulier->user->prenom ?? '' }} · {{ $rdv->date_visite->format('d/m/Y') }}</div>
                                </div>
                                <span class="activity-time">{{ $rdv->date_visite->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <i class="fa-regular fa-inbox"></i>
                            <p>Aucune activité récente</p>
                            <span>Commencez à publier des biens</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- DERNIERS AVIS -->
            <div class="card">
                <div class="card-header">
                    <span class="card-label">DERNIERS AVIS</span>
                    <a href="{{ route('agence.evaluations.index') }}" class="card-link">Voir tout →</a>
                </div>
                <div class="card-body">
                    @forelse($derniersAvis->take(3) as $avis)
                        <div class="avis-item">
                            <div class="avis-avatar">
                                {{ substr($avis->particulier->user->prenom ?? 'C', 0, 1) }}
                            </div>
                            <div class="avis-content">
                                <div class="avis-name">{{ $avis->particulier->user->prenom ?? 'Client' }}</div>
                                <div class="avis-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star" style="color:{{ $i <= $avis->note ? '#F5A623' : '#D4D8E0' }};font-size:11px;"></i>
                                    @endfor
                                </div>
                            </div>
                            <span class="avis-time">{{ $avis->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fa-regular fa-comment"></i>
                            <p>Aucun avis reçu</p>
                            <span>Les avis apparaîtront ici</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================
         ACTIONS RAPIDES
         ============================================ -->
    <div class="quick-actions">
        <a href="{{ route('agence.biens.create') }}" class="quick-action primary">
            <i class="fa-solid fa-plus-circle"></i>
            <div>
                <span class="qa-title">Publier un bien</span>
                <span class="qa-desc">Ajouter une nouvelle propriété</span>
            </div>
        </a>
        <a href="{{ route('agence.demandes.index') }}" class="quick-action">
            <i class="fa-solid fa-search"></i>
            <div>
                <span class="qa-title">Besoins</span>
                <span class="qa-desc">Voir les demandes clients</span>
            </div>
        </a>
        <a href="{{ route('agence.propositions.index') }}" class="quick-action">
            <i class="fa-solid fa-file-invoice"></i>
            <div>
                <span class="qa-title">Mes offres</span>
                <span class="qa-desc">Gérer vos propositions</span>
            </div>
        </a>
        <a href="{{ route('agence.rendezvous.index') }}" class="quick-action">
            <i class="fa-solid fa-calendar-plus"></i>
            <div>
                <span class="qa-title">Rendez-vous</span>
                <span class="qa-desc">Voir vos rendez-vous</span>
            </div>
        </a>
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

.plan-badge {
    font-size: 12px;
    font-weight: 700;
    padding: 4px 14px;
    border-radius: 20px;
    background: var(--rust);
    color: #fff;
}

/* ============================================
   ALERTES
   ============================================ */
.alert-simple {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 16px;
    border: 1px solid transparent;
    flex-wrap: wrap;
}

.alert-simple i {
    font-size: 18px;
    flex-shrink: 0;
}

.alert-simple div {
    flex: 1;
}

.alert-simple strong {
    display: block;
    font-size: 13px;
    font-weight: 700;
}

.alert-simple span {
    font-size: 13px;
    opacity: 0.85;
}

.alert-simple a {
    color: inherit;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    padding: 4px 12px;
    background: rgba(255,255,255,0.25);
    border-radius: 6px;
    white-space: nowrap;
}

.alert-simple.alert-danger {
    background: #FFEBEE;
    border-color: #FFCDD2;
    color: #C62828;
}

.alert-simple.alert-warning {
    background: #FFF8E1;
    border-color: #FFE0B2;
    color: #E65100;
}

/* ============================================
   STATISTIQUES
   ============================================ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 16px;
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

/* ============================================
   SECTION PRINCIPALE
   ============================================ */
.dashboard-main {
    display: grid;
    grid-template-columns: 320px 1fr;
    gap: 16px;
    margin-bottom: 16px;
}

.dashboard-col-left {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.dashboard-col-right {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* ============================================
   CARTES
   ============================================ */
.card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.card-header {
    padding: 12px 16px;
    background: #FAFBFC;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--muted);
}

.card-link {
    font-size: 12px;
    color: var(--rust);
    text-decoration: none;
    font-weight: 600;
}

.card-link:hover {
    text-decoration: underline;
}

.card-body {
    padding: 14px 16px 12px;
    flex: 1;
}

.card-title {
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--ink);
}

.card-footer {
    padding: 10px 16px 14px;
    border-top: 1px solid var(--border);
    margin-top: auto;
}

/* ============================================
   BADGES
   ============================================ */
.badge-status {
    font-size: 12px;
    font-weight: 600;
    padding: 2px 14px;
    border-radius: 20px;
}

.badge-status.active {
    background: #E8F5E9;
    color: #1E7A47;
}

.badge-status.inactive {
    background: #FFEBEE;
    color: #C62828;
}

.days-badge {
    background: var(--border);
    padding: 1px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-soft);
    margin-left: 6px;
}

/* ============================================
   ABONNEMENT
   ============================================ */
.card-date {
    font-size: 13px;
    color: var(--text-soft);
    margin-bottom: 12px;
}

.card-date i {
    margin-right: 6px;
}

.offres-block {
    background: #F7F9FC;
    border-radius: 10px;
    padding: 10px 14px;
    border: 1px solid var(--border);
}

.offres-label {
    font-size: 12px;
    color: var(--muted);
}

.offres-numbers {
    display: flex;
    align-items: baseline;
    gap: 6px;
    margin-top: 2px;
}

.offres-value {
    font-weight: 700;
    font-size: 20px;
    color: var(--ink);
}

.offres-value.zero {
    color: #C62828;
}

.offres-total {
    font-size: 14px;
    color: var(--muted);
}

.offres-pourcent {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    margin-left: auto;
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: var(--border);
    border-radius: 999px;
    overflow: hidden;
    margin-top: 10px;
}

.progress-fill {
    height: 100%;
    border-radius: 999px;
}

/* ============================================
   BOUTONS
   ============================================ */
.btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px 0;
    background: var(--rust);
    color: #fff;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
}

.btn-primary:hover {
    background: #9A4523;
}

.btn-primary i {
    transition: transform 0.2s;
}

.btn-primary:hover i {
    transform: translateX(4px);
}

.btn-secondary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px 0;
    background: #F7F9FC;
    color: var(--text-soft);
    border: 1px solid var(--border);
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
}

.btn-secondary:hover {
    background: var(--border);
}

.btn-secondary i {
    transition: transform 0.2s;
}

.btn-secondary:hover i {
    transform: translateX(4px);
}

/* ============================================
   RENDEZ-VOUS
   ============================================ */
.rdv-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
}

.rdv-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
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
    font-size: 12px;
    color: var(--muted);
}

.rdv-status {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 12px;
    border-radius: 20px;
    flex-shrink: 0;
}

.rdv-status.confirmed {
    background: #E8F5E9;
    color: #1E7A47;
}

.rdv-status.planned {
    background: #FFF8E1;
    color: #E65100;
}

/* ============================================
   ACTIVITÉ
   ============================================ */
.activity-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
}

.activity-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.activity-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #D4AF37;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-title {
    font-weight: 600;
    font-size: 13px;
    color: var(--ink);
}

.activity-desc {
    font-size: 12px;
    color: var(--muted);
}

.activity-time {
    font-size: 11px;
    color: var(--muted);
    flex-shrink: 0;
}

/* ============================================
   AVIS
   ============================================ */
.avis-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 0;
    border-bottom: 1px solid var(--border);
}

.avis-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.avis-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
    color: var(--text-soft);
    flex-shrink: 0;
}

.avis-content {
    flex: 1;
    min-width: 0;
}

.avis-name {
    font-weight: 600;
    font-size: 13px;
    color: var(--ink);
}

.avis-stars {
    display: flex;
    gap: 2px;
}

.avis-time {
    font-size: 11px;
    color: var(--muted);
    flex-shrink: 0;
}

/* ============================================
   EMPTY STATE
   ============================================ */
.empty-state {
    text-align: center;
    padding: 16px 0;
    color: var(--muted);
}

.empty-state i {
    font-size: 28px;
    display: block;
    margin-bottom: 6px;
    opacity: 0.3;
}

.empty-state p {
    font-weight: 600;
    color: var(--text-soft);
    margin: 0;
    font-size: 14px;
}

.empty-state span {
    font-size: 13px;
}

/* ============================================
   ACTIONS RAPIDES
   ============================================ */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-top: 4px;
}

.quick-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    text-decoration: none;
    color: var(--ink);
}

.quick-action:hover {
    border-color: var(--rust);
    background: #FAFBFC;
}

.quick-action i {
    font-size: 20px;
    color: var(--muted);
    flex-shrink: 0;
    width: 36px;
    text-align: center;
}

.quick-action.primary i {
    color: var(--rust);
}

.quick-action .qa-title {
    display: block;
    font-weight: 600;
    font-size: 13px;
    color: var(--ink);
}

.quick-action .qa-desc {
    font-size: 12px;
    color: var(--muted);
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 1024px) {
    .dashboard-main {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 820px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .quick-actions {
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
    
    .quick-actions {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    
    .quick-action {
        padding: 10px 12px;
        gap: 10px;
        flex-direction: column;
        text-align: center;
    }
    
    .quick-action i {
        width: auto;
        font-size: 18px;
    }
    
    .quick-action .qa-desc {
        display: none;
    }
    
    .quick-action .qa-title {
        font-size: 11px;
    }
    
    .dashboard-main {
        gap: 12px;
    }
    
    .offres-value {
        font-size: 18px;
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
    
    .quick-actions {
        grid-template-columns: 1fr 1fr;
        gap: 6px;
    }
    
    .quick-action {
        padding: 8px 10px;
    }
    
    .quick-action i {
        font-size: 16px;
    }
    
    .quick-action .qa-title {
        font-size: 10px;
    }
}
</style>
@endpush