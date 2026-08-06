@extends('layouts.app')

@section('title', $demande->type_bien->label() . ' — ' . $demande->zone_recherchee . ' — DoyaImmo')

@section('content')
<div class="wrap section" style="padding-top:36px;">
    <div class="detail-container">
        <a href="{{ route('besoins.index') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Retour aux besoins
        </a>

        <div class="detail-grid">
            <!-- Colonne gauche - Infos principales -->
            <div class="detail-main">
                <div class="detail-head">
                    <div class="detail-head-left">
                        <span class="meta-pill location-pill">
                            <i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}, Dakar
                        </span>
                        <h1 class="detail-title">
                            {{ $demande->type_bien->label() }}
                        </h1>
                    </div>
                    <div class="detail-head-right">
                        <span class="offer-count">{{ $demande->propositions->count() }} offres reçues</span>
                        <span class="status-badge status-{{ $demande->statut->value }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ $demande->statut->label() }}
                        </span>
                    </div>
                </div>

                <div class="panel description-panel">
                    <h3 class="panel-title">Description du besoin</h3>
                    <p class="panel-description">
                        {{ $demande->description }}
                    </p>
                </div>

                <div class="panel info-panel">
                    <h3 class="panel-title">Informations</h3>
                    <div class="info-grid">
                        <div class="info-row">
                            <span class="info-label">Type de bien</span>
                            <span class="info-value">{{ $demande->type_bien->label() }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Zone recherchée</span>
                            <span class="info-value">{{ $demande->zone_recherchee }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Budget maximum</span>
                            <span class="info-value">{{ number_format($demande->budget_maximum, 0, ',', ' ') }} FCFA / mois</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Type de contrat</span>
                            <span class="info-value">{{ $demande->type_operation->label() }}</span>
                        </div>
                        @if($demande->nombre_chambres)
                        <div class="info-row">
                            <span class="info-label">Nombre de chambres</span>
                            <span class="info-value">{{ $demande->nombre_chambres }}</span>
                        </div>
                        @endif
                        @if($demande->surface_minimum)
                        <div class="info-row">
                            <span class="info-label">Surface minimum</span>
                            <span class="info-value">{{ $demande->surface_minimum }} m²</span>
                        </div>
                        @endif
                        @if($demande->date_entree_souhaitee)
                        <div class="info-row">
                            <span class="info-label">Disponibilité souhaitée</span>
                            <span class="info-value">{{ $demande->date_entree_souhaitee->format('d/m/Y') }}</span>
                        </div>
                        @endif
                        <div class="info-row">
                            <span class="info-label">Statut</span>
                            <span class="info-value">{{ $demande->statut->label() }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Publié le</span>
                            <span class="info-value">{{ $demande->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite - Actions -->
            <div class="detail-sidebar">
                <div class="panel sticky-box">
                    <h3 class="panel-title">Vous êtes une agence ?</h3>
                    <p class="sidebar-text">
                        Connectez-vous à votre espace agence pour envoyer une offre correspondant à ce besoin.
                    </p>
                    @auth
                        @if(auth()->user()->isAgence())
                            <a href="{{ route('agence.propositions.create', $demande) }}" class="btn btn-rust btn-block">
                                <i class="fa-solid fa-paper-plane"></i> Envoyer une offre
                            </a>
                        @elseif(auth()->user()->isParticulier())
                            <p class="sidebar-small">
                                Vous êtes un particulier. <a href="{{ route('login') }}">Connectez-vous</a> en tant qu'agence pour faire une offre.
                            </p>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-rust btn-block">Se connecter</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-rust btn-block">Se connecter</a>
                        <a href="{{ route('register.agence') }}" class="btn btn-ghost btn-block" style="margin-top:10px;">
                            <i class="fa-solid fa-building"></i> Créer un compte agence
                        </a>
                    @endauth
                </div>

                @if($demande->propositions->count() > 0 && auth()->check() && auth()->user()->isParticulier())
                <div class="panel propositions-panel">
                    <h3 class="panel-title">Propositions reçues</h3>
                    @foreach($demande->propositions->take(3) as $proposition)
                        <div class="proposition-item">
                            <div class="proposition-agency">{{ $proposition->agence->nom_agence }}</div>
                            <div class="proposition-price">{{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA</div>
                            <div class="proposition-status">{{ $proposition->statut->label() }}</div>
                        </div>
                    @endforeach
                    @if($demande->propositions->count() > 3)
                        <div class="proposition-more">
                            + {{ $demande->propositions->count() - 3 }} autres propositions
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ===== CONTENEUR ===== */
    .detail-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    @media (min-width: 768px) {
        .detail-container {
            padding: 0 24px;
        }
    }

    @media (min-width: 1200px) {
        .detail-container {
            padding: 0 40px;
        }
    }

    /* ===== BACK LINK ===== */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: clamp(13px, 0.9vw, 14px);
        color: var(--muted);
        text-decoration: none;
        margin-bottom: 20px;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: var(--rust);
    }

    .back-link i {
        font-size: 12px;
    }

    /* ===== DETAIL GRID ===== */
    .detail-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 30px;
        align-items: start;
    }

    .detail-main {
        min-width: 0;
    }

    .detail-sidebar {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* ===== DETAIL HEAD ===== */
    .detail-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .detail-head-left {
        flex: 1;
        min-width: 0;
    }

    .detail-head-right {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
        flex-shrink: 0;
    }

    .location-pill {
        display: inline-flex !important;
        margin-bottom: 8px;
    }

    .detail-title {
        font-family: var(--display);
        font-weight: 800;
        font-size: clamp(24px, 3vw, 28px);
        line-height: 1.2;
        margin-top: 6px;
        word-break: break-word;
    }

    /* ===== OFFER COUNT & STATUS ===== */
    .offer-count {
        background: var(--teal-soft);
        color: var(--teal);
        font-size: clamp(11px, 0.8vw, 12.5px);
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: clamp(11px, 0.8vw, 12px);
        font-weight: 600;
        white-space: nowrap;
    }

    .status-badge i {
        font-size: 6px;
    }

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

    /* ===== PANELS ===== */
    .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: clamp(16px, 2vw, 24px);
        margin-bottom: 20px;
    }

    .panel:last-child {
        margin-bottom: 0;
    }

    .panel-title {
        font-family: var(--display);
        font-size: clamp(15px, 1.1vw, 16px);
        margin-bottom: 12px;
        font-weight: 600;
        color: var(--ink);
    }

    .panel-description {
        font-size: clamp(13px, 0.9vw, 14px);
        line-height: 1.8;
        color: var(--text-soft);
    }

    /* ===== INFO GRID ===== */
    .info-grid {
        display: flex;
        flex-direction: column;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
        font-size: clamp(12px, 0.8vw, 13.5px);
        gap: 12px;
        flex-wrap: wrap;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: var(--muted);
        flex-shrink: 0;
    }

    .info-value {
        font-weight: 600;
        text-align: right;
        word-break: break-word;
    }

    /* ===== SIDEBAR ===== */
    .sticky-box {
        position: sticky;
        top: 100px;
    }

    .sidebar-text {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--text-soft);
        line-height: 1.7;
        margin-bottom: 16px;
    }

    .sidebar-small {
        font-size: clamp(11px, 0.7vw, 12px);
        color: var(--muted);
        text-align: center;
        margin-top: 8px;
    }

    .sidebar-small a {
        color: var(--rust);
        text-decoration: none;
        font-weight: 600;
    }

    .sidebar-small a:hover {
        text-decoration: underline;
    }

    /* ===== PROPOSITIONS ===== */
    .propositions-panel {
        margin-top: 0;
    }

    .proposition-item {
        padding: 10px 0;
        border-bottom: 1px solid var(--border);
    }

    .proposition-item:last-child {
        border-bottom: none;
    }

    .proposition-agency {
        font-weight: 600;
        font-size: clamp(13px, 0.9vw, 14px);
        color: var(--ink);
    }

    .proposition-price {
        font-size: clamp(13px, 0.9vw, 14px);
        color: var(--rust);
        font-weight: 700;
    }

    .proposition-status {
        font-size: clamp(11px, 0.7vw, 12px);
        color: var(--muted);
    }

    .proposition-more {
        text-align: center;
        margin-top: 10px;
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
    }

    /* ===== BUTTONS ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: clamp(12px, 0.8vw, 13.5px);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        white-space: nowrap;
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

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 3px 10px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11.5px);
        color: var(--text-soft);
    }

    .meta-pill i {
        font-size: 10px;
    }

    /* ============================================
       RESPONSIVE
    ============================================ */

    /* Tablette */
    @media (max-width: 900px) {
        .detail-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .detail-sidebar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .sticky-box {
            position: static;
        }

        .propositions-panel {
            margin-top: 0;
        }

        .detail-head-right {
            flex-direction: row;
            align-items: center;
            gap: 8px;
        }
    }

    /* Mobile */
    @media (max-width: 640px) {
        .detail-container {
            padding: 0 12px;
        }

        .detail-head {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .detail-head-right {
            flex-direction: row;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
        }

        .detail-title {
            font-size: 22px;
        }

        .panel {
            padding: 14px 16px;
            border-radius: 12px;
        }

        .info-row {
            padding: 8px 0;
            font-size: 12px;
        }

        .info-value {
            text-align: right;
        }

        .detail-sidebar {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .btn {
            padding: 8px 14px;
            font-size: 12px;
        }

        .offer-count {
            font-size: 11px;
            padding: 3px 10px;
        }

        .status-badge {
            font-size: 11px;
            padding: 2px 10px;
        }

        .back-link {
            font-size: 12px;
            margin-bottom: 14px;
        }

        .proposition-item {
            padding: 8px 0;
        }

        .proposition-agency {
            font-size: 13px;
        }

        .proposition-price {
            font-size: 13px;
        }

        .proposition-status {
            font-size: 11px;
        }
    }

    /* Petit mobile */
    @media (max-width: 380px) {
        .detail-container {
            padding: 0 8px;
        }

        .detail-title {
            font-size: 19px;
        }

        .panel {
            padding: 12px 12px;
        }

        .panel-title {
            font-size: 14px;
        }

        .info-row {
            font-size: 11px;
            padding: 6px 0;
            flex-direction: column;
            gap: 2px;
        }

        .info-value {
            text-align: left;
        }

        .detail-head-right {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn {
            padding: 8px 12px;
            font-size: 11.5px;
        }
    }

    /* ===== ACCESSIBILITÉ ===== */
    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
@endpush