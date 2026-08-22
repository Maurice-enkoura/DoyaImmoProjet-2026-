@extends('layouts.dashboard-agence')

@section('title', 'Tableau de bord — DoyaImmo')
@section('page_title', 'Tableau de bord')
@section('page_sub', 'Vue d\'ensemble de votre activité')

@section('content')
<div class="view active">

    @php
        //$limite = $abonnementActuel ? $abonnementActuel->formule->limiteBiens() : 0;
        $limite = $abonnementActuel ? $abonnementActuel->formule->limiteOffres() : 0;

        $estIllimite = $limite === PHP_INT_MAX;
        $pct = $estIllimite ? 0 : min(100, max(0, $pourcentageOffres ?? 0));
    @endphp

    <!-- ==================== ALERTES ==================== -->
    @if(!$peutEnvoyerOffres && $abonnementActuel && !$estIllimite)
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <strong>Quota atteint.</strong>
                <span>Vous avez utilisé toutes vos offres. <a href="{{ route('agence.abonnement') }}">Passez à un abonnement supérieur</a></span>
            </div>
        </div>
    @endif

    @if(!$abonnementActuel)
        <div class="alert alert-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Aucun abonnement actif.</strong>
                <span>Souscrivez un abonnement pour publier des biens. <a href="{{ route('agence.abonnement') }}">Voir les offres</a></span>
            </div>
        </div>
    @endif

    <!-- ==================== STATISTIQUES ==================== -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--teal-soft); color:var(--teal);">
                <i class="fa-solid fa-inbox"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['besoins_disponibles'] ?? 0 }}</div>
                <div class="stat-label">Besoins disponibles</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--gold-soft); color:#8A6414;">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['offres_envoyees'] ?? 0 }}</div>
                <div class="stat-label">Offres envoyées</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--rust-soft); color:var(--rust);">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['rendezvous_a_venir'] ?? 0 }}</div>
                <div class="stat-label">Rendez-vous</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--green-soft); color:#1E7A47;">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ number_format($stats['note_moyenne'] ?? 0, 1) }}</div>
                <div class="stat-label">Note moyenne</div>
            </div>
        </div>
    </div>

    <!-- ==================== ABONNEMENT & OFFRES ==================== -->
    <div class="subscription-row">
        <div class="sub-card">
            <div class="sub-label">Abonnement</div>
            <div class="sub-plan">
                @if($abonnementActuel)
                    {{ $abonnementActuel->formule->label() }}
                    <span class="sub-badge">Actif</span>
                @else
                    <span style="color:#C62828;">Aucun</span>
                    <span class="sub-badge sub-badge--off">Inactif</span>
                @endif
            </div>
            @if($abonnementActuel)
                <div class="sub-date">Valable jusqu'au {{ $abonnementActuel->date_fin->format('d/m/Y') }}</div>
            @endif
            <a href="{{ route('agence.abonnement') }}" class="btn btn-ghost btn-sm">
                {{ $abonnementActuel ? 'Gérer' : 'Souscrire' }}
            </a>
        </div>

        <div class="sub-card">
            <div class="sub-label">Offres restantes</div>
            <div class="sub-plan">
                @if($estIllimite)
                    <span class="sub-value">Illimité</span>
                @else
                    <span class="sub-value" style="color:{{ $offresRestantes > 0 ? 'var(--ink)' : '#C62828' }};">
                        {{ $offresRestantes }}
                    </span>
                    <span class="sub-value-total">/ {{ $limite }}</span>
                @endif
            </div>
            @if($abonnementActuel && !$estIllimite)
                <div class="progress-bar">
                    <div class="progress-fill" style="width:{{ $pct }}%;background:{{ $pct > 80 ? '#C62828' : ($pct > 60 ? '#D4AF37' : 'var(--rust)') }};"></div>
                </div>
                <div class="progress-label">{{ $pct }}% utilisé</div>
            @endif
            @if($estIllimite)
                <div class="sub-date">
                    <i class="fa-regular fa-circle-check" style="color:#1E7A47;"></i> Offres illimitées
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== ACTIVITÉ RÉCENTE ==================== -->
    <div class="activity-grid">
        <!-- Besoins récents -->
        <div class="activity-card">
            <div class="activity-header">
                <h3>Besoins récents</h3>
                <a href="{{ route('agence.demandes.index') }}">Voir tout</a>
            </div>
            @forelse($derniersBesoins as $besoin)
                <div class="activity-item">
                    <div class="activity-dot" style="background:#D4AF37;"></div>
                    <div class="activity-main">
                        <div class="activity-title">{{ $besoin->type_bien->label() }}</div>
                        <div class="activity-desc">{{ $besoin->zone_recherchee }} · {{ number_format($besoin->budget_maximum, 0, ',', ' ') }} F</div>
                    </div>
                    <span class="activity-time">{{ $besoin->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="empty-state">Aucun besoin récent</div>
            @endforelse
        </div>

        <!-- Prochains rendez-vous -->
        <div class="activity-card">
            <div class="activity-header">
                <h3>Prochains rendez-vous</h3>
                <a href="{{ route('agence.rendezvous.index') }}">Voir tout</a>
            </div>
            @forelse($prochainsRendezVous as $rdv)
                <div class="activity-item">
                    <div class="activity-dot" style="background:var(--rust);"></div>
                    <div class="activity-main">
                        <div class="activity-title">{{ $rdv->proposition->bien->titre ?? 'Visite' }}</div>
                        <div class="activity-desc">{{ $rdv->particulier->user->prenom ?? '' }} · {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}</div>
                    </div>
                    <span class="activity-time">{{ $rdv->date_visite->format('d/m') }}</span>
                </div>
            @empty
                <div class="empty-state">Aucun rendez-vous</div>
            @endforelse
        </div>

        <!-- Derniers avis -->
        <div class="activity-card">
            <div class="activity-header">
                <h3>Derniers avis</h3>
                <a href="{{ route('agence.evaluations.index') }}">Voir tout</a>
            </div>
            @forelse($derniersAvis as $avis)
                <div class="activity-item">
                    <div class="activity-dot" style="background:#1E7A47;"></div>
                    <div class="activity-main">
                        <div class="activity-title">{{ $avis->particulier->user->prenom ?? '' }}</div>
                        <div class="activity-desc">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star" style="color:{{ $i <= $avis->note ? '#F5A623' : '#D4D8E0' }};font-size:12px;"></i>
                            @endfor
                        </div>
                    </div>
                    <span class="activity-time">{{ $avis->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="empty-state">Aucun avis reçu</div>
            @endforelse
        </div>
    </div>

    <!-- ==================== ACTIONS RAPIDES ==================== -->
    <div class="quick-actions">
        <a href="{{ route('agence.biens.create') }}" class="btn btn-rust">
            <i class="fa-solid fa-plus"></i> Publier un bien
        </a>
        <a href="{{ route('agence.demandes.index') }}" class="btn btn-ghost">
            <i class="fa-solid fa-search"></i> Voir les besoins
        </a>
        <a href="{{ route('agence.propositions.index') }}" class="btn btn-ghost">
            <i class="fa-solid fa-file-invoice"></i> Mes offres
        </a>
        <a href="{{ route('agence.abonnement') }}" class="btn btn-ghost">
            <i class="fa-solid fa-crown"></i> Abonnement
        </a>
    </div>

</div>
@endsection

@push('styles')
<style>
    /* ===================== STATISTIQUES ===================== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 20px;
    }

    @media (max-width: 820px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .subscription-row {
            grid-template-columns: 1fr !important;
        }
        .activity-grid {
            grid-template-columns: 1fr !important;
        }
        .quick-actions {
            flex-direction: column !important;
            align-items: stretch !important;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: #fff;
        border-radius: var(--radius);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid var(--border);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .stat-info {
        flex: 1;
        min-width: 0;
    }

    .stat-value {
        font-family: var(--display);
        font-weight: 700;
        font-size: 22px;
        line-height: 1.2;
        color: var(--ink);
    }

    .stat-label {
        font-size: 12.5px;
        color: var(--muted);
    }

    /* ===================== ABONNEMENT ===================== */
    .subscription-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }

    .sub-card {
        background: #fff;
        border-radius: var(--radius);
        padding: 18px 20px;
        border: 1px solid var(--border);
    }

    .sub-label {
        font-size: 11.5px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .sub-plan {
        font-family: var(--display);
        font-weight: 700;
        font-size: 19px;
        color: var(--ink);
        margin-bottom: 2px;
        display: flex;
        align-items: baseline;
        gap: 10px;
    }

    .sub-value {
        font-size: 22px;
    }

    .sub-value-total {
        font-size: 13px;
        font-weight: 400;
        color: var(--muted);
    }

    .sub-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 12px;
        border-radius: 999px;
        background: #E8F5E9;
        color: #1E7A47;
    }

    .sub-badge--off {
        background: #FFEBEE;
        color: #C62828;
    }

    .sub-date {
        font-size: 12.5px;
        color: var(--muted);
        margin-bottom: 10px;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: var(--border);
        border-radius: 999px;
        overflow: hidden;
        margin-top: 10px;
    }

    .progress-fill {
        height: 100%;
        border-radius: 999px;
    }

    .progress-label {
        font-size: 11.5px;
        color: var(--muted);
        margin-top: 6px;
        margin-bottom: 10px;
    }

    /* ===================== ALERTES ===================== */
    .alert {
        padding: 12px 16px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        font-size: 13px;
    }

    .alert-warning {
        background: #FFF8E1;
        border: 1px solid #FFE0B2;
        color: #E65100;
    }

    .alert-danger {
        background: #FFEBEE;
        border: 1px solid #FFCDD2;
        color: #C62828;
    }

    .alert a {
        color: var(--rust);
        font-weight: 600;
        text-decoration: none;
    }

    .alert a:hover {
        text-decoration: underline;
    }

    /* ===================== ACTIVITÉ ===================== */
    .activity-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .activity-card {
        background: #fff;
        border-radius: var(--radius);
        padding: 18px 20px;
        border: 1px solid var(--border);
    }

    .activity-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border);
    }

    .activity-header h3 {
        font-family: var(--display);
        font-size: 14px;
        font-weight: 700;
        margin: 0;
        color: var(--ink);
    }

    .activity-header a {
        font-size: 12px;
        color: var(--rust);
        text-decoration: none;
        font-weight: 600;
    }

    .activity-header a:hover {
        text-decoration: underline;
    }

    .activity-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 0;
        border-bottom: 1px solid var(--border);
    }

    .activity-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .activity-item:first-child {
        padding-top: 0;
    }

    .activity-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .activity-main {
        min-width: 0;
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        font-size: 13px;
        color: var(--ink);
    }

    .activity-desc {
        font-size: 12px;
        color: var(--muted);
        margin-top: 1px;
    }

    .activity-time {
        font-size: 11px;
        color: var(--muted);
        flex-shrink: 0;
        white-space: nowrap;
    }

    .empty-state {
        padding: 16px 0;
        text-align: center;
        font-size: 13px;
        color: var(--muted);
    }

    /* ===================== ACTIONS RAPIDES ===================== */
    .quick-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 4px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.15s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-rust {
        background: var(--rust);
        color: #fff;
    }

    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
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
        padding: 7px 14px;
        font-size: 12.5px;
        margin-top: 4px;
    }
</style>
@endpush