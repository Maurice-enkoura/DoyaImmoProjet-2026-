@extends('layouts.dashboard-agence')

@section('title', 'Rendez-vous — DoyaImmo')
@section('page_title', 'Rendez-vous')
@section('page_sub', 'Vos visites planifiées avec les clients')

@section('content')
<div class="view active">
    <!-- En-tête -->
    <div class="page-header">
        <div>
            <h2>Rendez-vous</h2>
            <p class="sub">Gérez vos visites planifiées avec les clients</p>
        </div>
        <div class="header-actions">
            <span class="rdv-count">
                <i class="fa-regular fa-calendar"></i>
                {{ $rendezVous->total() }} rendez-vous
            </span>
        </div>
    </div>

    <!-- Filtres rapides -->
    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all" onclick="filterRdv('all')">Tous</button>
        <button class="filter-tab" data-filter="planifie" onclick="filterRdv('planifie')">Planifiés</button>
        <button class="filter-tab" data-filter="confirme" onclick="filterRdv('confirme')">Confirmés</button>
        <button class="filter-tab" data-filter="termine" onclick="filterRdv('termine')">Terminés</button>
        <button class="filter-tab" data-filter="annule" onclick="filterRdv('annule')">Annulés</button>
    </div>

    @if($rendezVous->count() > 0)
        <div class="rdv-list">
            @foreach($rendezVous as $rdv)
                @php
                    $phoneVisible = $rdv->isPhoneVisible();
                @endphp
                <div class="rdv-card" data-statut="{{ $rdv->statut->value }}">
                    <!-- Icône statut -->
                    <div class="rdv-icon">
                        @if($rdv->statut->value === 'planifie')
                            <i class="fa-regular fa-clock" style="color:#E65100;"></i>
                        @elseif($rdv->statut->value === 'confirme')
                            <i class="fa-regular fa-circle-check" style="color:#0D47A1;"></i>
                        @elseif($rdv->statut->value === 'termine')
                            <i class="fa-regular fa-circle-check" style="color:#1E7A47;"></i>
                        @elseif($rdv->statut->value === 'annule')
                            <i class="fa-regular fa-circle-xmark" style="color:#C62828;"></i>
                        @endif
                    </div>

                    <!-- Date -->
                    <div class="rdv-date">
                        <span class="day">{{ $rdv->date_visite->format('d') }}</span>
                        <span class="month">{{ $rdv->date_visite->format('M') }}</span>
                        <span class="year">{{ $rdv->date_visite->format('Y') }}</span>
                    </div>

                    <!-- Infos -->
                    <div class="rdv-info">
                        <div class="rdv-title">
                            {{ $rdv->proposition->bien->titre ?? 'Visite' }}
                            @if($rdv->proposition->bien)
                                <span class="rdv-location">
                                    <i class="fa-regular fa-location-dot"></i>
                                    {{ $rdv->proposition->bien->quartier }}
                                </span>
                            @endif
                        </div>
                        <div class="rdv-details">
                            <span class="rdv-client">
                                <i class="fa-regular fa-user"></i>
                                {{ $rdv->particulier->user->prenom ?? '' }} {{ $rdv->particulier->user->nom ?? '' }}
                            </span>
                            <span class="rdv-time">
                                <i class="fa-regular fa-clock"></i>
                                {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                            </span>
                            <!-- Téléphone avec masquage conditionnel -->
                            <span class="rdv-phone">
                                <i class="fa-solid fa-phone"></i>
                                @if($phoneVisible)
                                    <span class="phone-visible">
                                        {{ $rdv->particulier_phone_formatted }}
                                    </span>
                                @else
                                    <span class="phone-hidden">
                                        <i class="fa-solid fa-lock" style="font-size:10px;"></i>
                                        Masqué
                                    </span>
                                @endif
                            </span>
                            @if($rdv->proposition->bien)
                                <span class="rdv-address">
                                    <i class="fa-regular fa-location-dot"></i>
                                    {{ $rdv->proposition->bien->adresse }}
                                </span>
                            @endif
                        </div>
                        <!-- Indicateur de visibilité -->
                        @if(!$phoneVisible)
                            <div class="phone-notice">
                                <i class="fa-solid fa-info-circle"></i>
                                Confirmez le rendez-vous pour voir le numéro
                            </div>
                        @else
                            <div class="phone-notice visible">
                                <i class="fa-solid fa-check-circle"></i>
                                Numéro visible
                            </div>
                        @endif
                    </div>

                    <!-- Statut -->
                    <div class="rdv-status">
                        <span class="status-pill status-{{ $rdv->statut->value }}">
                            <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                            {{ $rdv->statut->label() }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div class="rdv-actions">
                        @if($rdv->statut->value === 'planifie')
                            <form action="{{ route('agence.rendezvous.update', $rdv) }}" method="POST" class="action-form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="statut" value="confirme">
                                <button type="submit" class="btn btn-rust btn-sm" title="Confirmer le rendez-vous pour voir les coordonnées">
                                    <i class="fa-solid fa-check"></i> Confirmer
                                </button>
                            </form>
                        @endif

                        @if($rdv->statut->value === 'confirme' || $rdv->statut->value === 'planifie')
                            <form action="{{ route('agence.rendezvous.update', $rdv) }}" method="POST" class="action-form">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="statut" value="termine">
                                <button type="submit" class="btn btn-ghost btn-sm">
                                    <i class="fa-solid fa-check-double"></i> Terminer
                                </button>
                            </form>
                        @endif

                        @if($rdv->statut->value === 'planifie' || $rdv->statut->value === 'confirme')
                            <button class="btn btn-ghost btn-sm btn-danger" onclick="annulerRdv({{ $rdv->id }})">
                                <i class="fa-solid fa-xmark"></i> Annuler
                            </button>
                            <form id="annuler-{{ $rdv->id }}" action="{{ route('agence.rendezvous.update', $rdv) }}" method="POST" style="display:none;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="statut" value="annule">
                            </form>
                        @endif

                        <!-- Bouton Détails -->
                        <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-eye"></i> Détails
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $rendezVous->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fa-regular fa-calendar-days"></i>
            <h3>Aucun rendez-vous</h3>
            <p>Vous n'avez pas encore de visites planifiées avec vos clients.</p>
            <p style="font-size:13px;color:var(--muted);margin-top:4px;">
                Les rendez-vous apparaîtront ici une fois qu'un client aura accepté une proposition.
            </p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // ===================== FILTRE =====================
    function filterRdv(statut) {
        // Mettre à jour les onglets
        document.querySelectorAll('.filter-tab').forEach(tab => {
            tab.classList.remove('active');
            if (tab.dataset.filter === statut) {
                tab.classList.add('active');
            }
        });

        // Filtrer les cartes
        document.querySelectorAll('.rdv-card').forEach(card => {
            if (statut === 'all') {
                card.style.display = 'flex';
            } else {
                card.style.display = card.dataset.statut === statut ? 'flex' : 'none';
            }
        });
    }

    // ===================== ANNULER =====================
    function annulerRdv(id) {
        if (confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?')) {
            document.getElementById('annuler-' + id).submit();
        }
    }

    // ===================== INIT =====================
    document.addEventListener('DOMContentLoaded', function() {
        // Appliquer le filtre par défaut
        filterRdv('all');
    });
</script>
@endpush

@push('styles')
<style>
    /* ===================== PAGE HEADER ===================== */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }

    .page-header h2 {
        font-family: var(--display);
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .page-header .sub {
        font-size: 14px;
        color: var(--muted);
        margin: 4px 0 0;
    }

    .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .rdv-count {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #F7F9FC;
        border-radius: 10px;
        font-size: 13px;
        color: var(--text-soft);
        border: 1px solid var(--border);
    }

    .rdv-count i {
        color: var(--rust);
    }

    /* ===================== FILTRES ===================== */
    .filter-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-tab {
        padding: 6px 18px;
        border: 1px solid var(--border);
        border-radius: 20px;
        background: #fff;
        cursor: pointer;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
        font-family: inherit;
        color: var(--text-soft);
    }

    .filter-tab:hover {
        border-color: var(--rust);
        color: var(--rust);
    }

    .filter-tab.active {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    /* ===================== LISTE ===================== */
    .rdv-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .rdv-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: box-shadow 0.2s;
        flex-wrap: wrap;
    }

    .rdv-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    }

    /* ===================== ICÔNE ===================== */
    .rdv-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #F7F9FC;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        border: 1px solid var(--border);
    }

    /* ===================== DATE ===================== */
    .rdv-date {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: #F7F9FC;
        border-radius: 10px;
        padding: 4px 12px;
        min-width: 48px;
        flex-shrink: 0;
        border: 1px solid var(--border);
    }

    .rdv-date .day {
        font-family: var(--display);
        font-weight: 700;
        font-size: 18px;
        line-height: 1.2;
        color: var(--ink);
    }

    .rdv-date .month {
        font-size: 10px;
        text-transform: uppercase;
        color: var(--muted);
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .rdv-date .year {
        font-size: 9px;
        color: var(--muted);
        opacity: 0.6;
    }

    /* ===================== INFOS ===================== */
    .rdv-info {
        flex: 1;
        min-width: 180px;
    }

    .rdv-title {
        font-weight: 600;
        font-size: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .rdv-location {
        font-weight: 400;
        font-size: 13px;
        color: var(--muted);
    }

    .rdv-location i {
        margin-right: 4px;
    }

    .rdv-details {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 4px;
        font-size: 13px;
        color: var(--text-soft);
    }

    .rdv-details span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .rdv-details i {
        color: var(--muted);
        font-size: 12px;
        width: 14px;
        text-align: center;
    }

    /* ===================== TÉLÉPHONE ===================== */
    .rdv-phone .phone-visible {
        color: var(--text-soft);
    }

    .rdv-phone .phone-hidden {
        color: var(--muted);
        font-size: 12px;
        font-weight: 400;
    }

    .rdv-phone .phone-hidden i {
        color: var(--muted);
        font-size: 10px;
    }

    .phone-notice {
        font-size: 11px;
        color: var(--muted);
        margin-top: 4px;
        display: flex;
        align-items: center;
        gap: 4px;
        opacity: 0.7;
    }

    .phone-notice i {
        font-size: 11px;
    }

    .phone-notice.visible {
        color: #1E7A47;
        opacity: 1;
    }

    .phone-notice.visible i {
        color: #1E7A47;
    }

    /* ===================== STATUT ===================== */
    .rdv-status {
        flex-shrink: 0;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-planifie {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-confirme {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-annule {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-termine {
        background: #E8F5E9;
        color: #1E7A47;
    }

    /* ===================== ACTIONS ===================== */
    .rdv-actions {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
        flex-wrap: wrap;
    }

    .action-form {
        display: inline;
        margin: 0;
        padding: 0;
    }

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

    .btn-danger {
        color: #C62828;
        border-color: #FFCDD2;
    }

    .btn-danger:hover {
        background: #FFEBEE;
        border-color: #EF9A9A;
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
        color: var(--border);
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
    @media (max-width: 820px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .rdv-count {
            align-self: flex-start;
        }

        .rdv-card {
            flex-direction: column;
            align-items: stretch;
            padding: 14px 16px;
            gap: 12px;
        }

        .rdv-icon {
            display: none;
        }

        .rdv-date {
            flex-direction: row;
            gap: 8px;
            padding: 6px 12px;
            min-width: auto;
            align-self: flex-start;
        }

        .rdv-date .day {
            font-size: 16px;
        }

        .rdv-date .year {
            display: none;
        }

        .rdv-info {
            min-width: auto;
        }

        .rdv-title {
            font-size: 14px;
        }

        .rdv-details {
            gap: 8px;
            font-size: 12px;
            flex-direction: column;
        }

        .rdv-status {
            align-self: flex-start;
        }

        .rdv-actions {
            width: 100%;
            justify-content: stretch;
        }

        .rdv-actions .btn {
            flex: 1;
            justify-content: center;
        }

        .filter-tabs {
            gap: 6px;
            justify-content: center;
        }

        .filter-tab {
            font-size: 12px;
            padding: 4px 14px;
        }

        .phone-notice {
            font-size: 10px;
        }
    }

    @media (max-width: 480px) {
        .rdv-actions {
            flex-wrap: wrap;
        }

        .rdv-actions .btn {
            flex: 1 1 calc(50% - 4px);
            justify-content: center;
            font-size: 11px;
            padding: 6px 10px;
        }

        .rdv-title {
            font-size: 13px;
        }

        .rdv-location {
            font-size: 12px;
        }

        .rdv-date .day {
            font-size: 14px;
        }

        .rdv-date .month {
            font-size: 9px;
        }

        .filter-tabs {
            justify-content: center;
        }

        .rdv-details span {
            font-size: 11px;
        }

        .rdv-phone .phone-hidden {
            font-size: 11px;
        }
    }
</style>
@endpush