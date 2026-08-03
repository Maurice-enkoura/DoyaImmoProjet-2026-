@extends('layouts.dashboard')

@section('title', 'Offres reçues — DoyaImmo')
@section('page_title', 'Offres reçues')
@section('page_sub', 'Propositions pour votre besoin : ' . $demande->type_bien->label())

@section('content')
<div class="view active">
    <!-- Bouton retour -->
    <div class="back-action">
        <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes besoins
        </a>
    </div>

    <!-- En-tête du besoin -->
    <div class="demande-header">
        <div>
            <h3>{{ $demande->type_bien->label() }} — {{ $demande->zone_recherchee }}</h3>
            <p class="sub">
                <i class="fa-regular fa-coins"></i>
                Budget max : {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois
                <span class="separator">|</span>
                <i class="fa-regular fa-calendar"></i>
                Publié le {{ $demande->created_at->format('d/m/Y') }}
            </p>
        </div>
        <span class="status-badge status-{{ $demande->statut->value }}">
            <i class="fa-solid fa-circle" style="font-size:8px;"></i>
            {{ $demande->statut->label() }}
        </span>
    </div>

    <!-- Liste des offres -->
    @if($offres->count() > 0)
        <div class="offres-list">
            @foreach($offres as $offre)
                <div class="offre-card">
                    <div class="offre-header">
                        <div class="offre-agency">
                            <div class="agency-avatar">
                                {{ strtoupper(substr($offre->agence->nom_agence, 0, 1)) }}
                            </div>
                            <div>
                                <div class="agency-name">{{ $offre->agence->nom_agence }}</div>
                                <div class="agency-rating">
                                    <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                                    {{ number_format($offre->agence->note_moyenne, 1) }} / 5
                                    <span class="reviews">({{ $offre->agence->evaluations->count() }} avis)</span>
                                </div>
                            </div>
                        </div>
                        <div class="offre-price">
                            <div class="price-amount">{{ number_format($offre->prix_propose, 0, ',', ' ') }} FCFA</div>
                            <div class="price-label">Prix proposé</div>
                        </div>
                    </div>

                    <div class="offre-body">
                        <div class="offre-bien">
                            <span class="bien-title">{{ $offre->bien->titre ?? 'Bien' }}</span>
                            <span class="bien-location">
                                <i class="fa-solid fa-location-dot"></i> {{ $offre->bien->adresse ?? 'Adresse non spécifiée' }}
                            </span>
                        </div>
                        @if($offre->message)
                            <div class="offre-message">
                                <i class="fa-regular fa-message"></i>
                                {{ $offre->message }}
                            </div>
                        @endif
                    </div>

                    <div class="offre-footer">
                        <span class="offre-date">
                            <i class="fa-regular fa-clock"></i>
                            Reçue le {{ $offre->created_at->format('d/m/Y') }}
                        </span>
                        <div class="offre-actions">
                            <a href="{{ route('particulier.propositions.show', $offre) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i> Détails
                            </a>
                            @if($offre->statut->value === 'en_attente')
                                <form action="{{ route('particulier.propositions.selectionner', $offre) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-rust btn-sm">
                                        <i class="fa-solid fa-check"></i> Sélectionner
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $offres->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fa-regular fa-inbox"></i>
            <h3>Aucune offre reçue</h3>
            <p>Vous n'avez pas encore reçu d'offres pour ce besoin.</p>
            <p style="font-size:13px;color:var(--muted);margin-top:4px;">
                Les agences pourront vous contacter dès que vous aurez publié votre besoin.
            </p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* ===================== BACK ===================== */
    .back-action {
        margin-bottom: 20px;
    }

    /* ===================== DEMANDE HEADER ===================== */
    .demande-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 16px 20px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        margin-bottom: 24px;
    }

    .demande-header h3 {
        font-family: var(--display);
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }

    .demande-header .sub {
        font-size: 13px;
        color: var(--muted);
        margin: 4px 0 0;
    }

    .demande-header .sub .separator {
        margin: 0 8px;
        color: var(--border);
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        flex-shrink: 0;
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

    /* ===================== OFFRES LIST ===================== */
    .offres-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .offre-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: box-shadow 0.2s;
    }

    .offre-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    }

    .offre-header {
        padding: 14px 20px;
        background: #FAFBFC;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .offre-agency {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .agency-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--rust-soft);
        color: var(--rust);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }

    .agency-name {
        font-weight: 600;
        font-size: 15px;
    }

    .agency-rating {
        font-size: 13px;
        color: var(--text-soft);
    }

    .agency-rating .reviews {
        color: var(--muted);
        font-size: 12px;
    }

    .offre-price {
        text-align: right;
        flex-shrink: 0;
    }

    .price-amount {
        font-weight: 700;
        color: var(--rust);
        font-size: 18px;
    }

    .price-label {
        font-size: 12px;
        color: var(--muted);
    }

    .offre-body {
        padding: 14px 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .offre-bien {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .bien-title {
        font-weight: 500;
        font-size: 14px;
        color: var(--ink);
    }

    .bien-location {
        font-size: 13px;
        color: var(--muted);
    }

    .offre-message {
        padding: 8px 12px;
        background: #F7F9FC;
        border-radius: 8px;
        font-size: 13px;
        color: var(--text-soft);
        border-left: 3px solid var(--rust);
    }

    .offre-message i {
        margin-right: 6px;
        color: var(--rust);
    }

    .offre-footer {
        padding: 12px 20px;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        background: #FAFBFC;
    }

    .offre-date {
        font-size: 12px;
        color: var(--muted);
    }

    .offre-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
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

    /* ===================== EMPTY STATE ===================== */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
    }

    .empty-state i {
        font-size: 48px;
        display: block;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    .empty-state h3 {
        font-family: var(--display);
        font-size: 20px;
        font-weight: 600;
        color: var(--text-soft);
        margin: 0 0 8px;
    }

    .empty-state p {
        font-size: 14px;
        margin: 0;
    }

    /* ===================== PAGINATION ===================== */
    .pagination-wrapper {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }

    .pagination-wrapper .pagination {
        display: flex;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination-wrapper .pagination a,
    .pagination-wrapper .pagination span {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
        min-width: 40px;
        text-align: center;
    }

    .pagination-wrapper .pagination a:hover {
        background: var(--border);
        border-color: var(--border);
    }

    .pagination-wrapper .pagination .active span {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .pagination-wrapper .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .demande-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .offre-header {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .offre-agency {
            justify-content: center;
        }

        .offre-price {
            text-align: center;
        }

        .offre-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .offre-actions {
            justify-content: stretch;
        }

        .offre-actions .btn {
            flex: 1;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .demande-header h3 {
            font-size: 16px;
        }

        .demande-header .sub {
            font-size: 12px;
        }

        .price-amount {
            font-size: 16px;
        }

        .offre-bien {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .offre-actions .btn {
            font-size: 11px;
            padding: 4px 10px;
        }
    }
</style>
@endpush