@extends('layouts.dashboard-agence')

@section('title', 'Mes offres — DoyaImmo')
@section('page_title', 'Mes offres')
@section('page_sub', 'Suivez vos propositions envoyées')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mes offres envoyées</h2>
            <p>Suivez le statut de chaque proposition envoyée à un client</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
            <span style="font-size:13px;color:var(--muted);">
                <i class="fa-solid fa-file-invoice"></i> {{ $propositions->total() }} offres
            </span>
            @if($propositions->where('statut.value', 'en_attente')->count() > 0)
                <span style="font-size:13px;color:#E65100;background:#FFF8E1;padding:4px 12px;border-radius:999px;">
                    <i class="fa-regular fa-clock"></i> {{ $propositions->where('statut.value', 'en_attente')->count() }} en attente
                </span>
            @endif
        </div>
    </div>

    <!-- ✅ Filtres par statut -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
        <a href="{{ route('agence.propositions.index') }}" class="filter-btn {{ !request('statut') ? 'active' : '' }}">
            <i class="fa-solid fa-list"></i> Tous
        </a>
        <a href="{{ route('agence.propositions.index', ['statut' => 'en_attente']) }}" class="filter-btn {{ request('statut') == 'en_attente' ? 'active' : '' }}">
            <i class="fa-regular fa-clock" style="color:#E65100;"></i> En attente
        </a>
        <a href="{{ route('agence.propositions.index', ['statut' => 'acceptee']) }}" class="filter-btn {{ request('statut') == 'acceptee' ? 'active' : '' }}">
            <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i> Acceptées
        </a>
        <a href="{{ route('agence.propositions.index', ['statut' => 'refusee']) }}" class="filter-btn {{ request('statut') == 'refusee' ? 'active' : '' }}">
            <i class="fa-solid fa-times-circle" style="color:#C62828;"></i> Refusées
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($propositions->count() > 0)
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;min-width:700px;">
                <thead>
                    <tr style="background:#FAFBFC;border-bottom:1px solid var(--border);">
                        <th style="padding:12px 16px;text-align:left;">Bien proposé</th>
                        <th style="padding:12px 16px;text-align:left;">Client</th>
                        <th style="padding:12px 16px;text-align:left;">Montant</th>
                        <th style="padding:12px 16px;text-align:left;">Score</th>
                        <th style="padding:12px 16px;text-align:left;">Date d'envoi</th>
                        <th style="padding:12px 16px;text-align:left;">Statut</th>
                        <th style="padding:12px 16px;text-align:left;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($propositions as $proposition)
                        @php
                            $isVedette = $proposition->bien && $proposition->bien->est_vedette;
                            $statusColors = [
                                'en_attente' => ['bg' => '#FFF8E1', 'color' => '#E65100'],
                                'acceptee' => ['bg' => '#E8F5E9', 'color' => '#1E7A47'],
                                'refusee' => ['bg' => '#FFEBEE', 'color' => '#C62828'],
                                'terminee' => ['bg' => '#E3F2FD', 'color' => '#0D47A1'],
                            ];
                            $statusInfo = $statusColors[$proposition->statut->value] ?? $statusColors['en_attente'];
                        @endphp
                        <tr style="border-bottom:1px solid var(--border);{{ $isVedette ? 'background:#FFFDF5;' : '' }}">
                            <td style="padding:12px 16px;">
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <span style="font-weight:600;font-size:13px;">{{ $proposition->bien->titre ?? 'Bien' }}</span>
                                    @if($isVedette)
                                        <span class="vedette-tag-table">
                                            <i class="fa-solid fa-star"></i> Vedette
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size:12px;color:var(--muted);">
                                    {{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}
                                    @if($proposition->demande->surface_minimum)
                                        <span style="margin-left:8px;">
                                            <i class="fa-regular fa-square"></i> {{ $proposition->demande->surface_minimum }} m²
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="font-size:13px;font-weight:500;">
                                    {{ $proposition->particulier->user->prenom ?? 'Client' }} {{ $proposition->particulier->user->nom ?? '' }}
                                </div>
                                <div style="font-size:12px;color:var(--muted);">
                                    {{ $proposition->particulier->user->email ?? '' }}
                                </div>
                            </td>
                            <td style="padding:12px 16px;font-weight:700;color:var(--rust);">
                                {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                            </td>
                            <td style="padding:12px 16px;">
                                @if($proposition->score_matching)
                                    <span style="font-weight:600;color:{{ $proposition->score_matching >= 80 ? '#1E7A47' : ($proposition->score_matching >= 60 ? '#E65100' : '#C62828') }};">
                                        {{ $proposition->score_matching }}%
                                    </span>
                                    @if($proposition->niveau_matching)
                                        <span style="font-size:10px;color:var(--muted);display:block;">
                                            {{ $proposition->niveau_matching }}
                                        </span>
                                    @endif
                                @else
                                    <span style="color:var(--muted);font-size:12px;">Non évalué</span>
                                @endif
                            </td>
                            <td style="padding:12px 16px;font-size:13px;color:var(--text-soft);">
                                {{ $proposition->created_at->format('d/m/Y à H:i') }}
                            </td>
                            <td style="padding:12px 16px;">
                                <span class="status-pill" style="background:{{ $statusInfo['bg'] }};color:{{ $statusInfo['color'] }};border:1px solid {{ $statusInfo['color'] }}20;">
                                    <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                                    {{ $proposition->statut->label() }}
                                </span>
                            </td>
                            <td style="padding:12px 16px;">
                                <a href="{{ route('agence.propositions.show', $proposition) }}" class="btn btn-ghost btn-sm">
                                    <i class="fa-solid fa-eye"></i> Détail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ✅ PAGINATION -->
        <div style="margin-top:30px;">
            {{ $propositions->appends(request()->query())->links() }}
        </div>

    @else
        <div style="text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
            <i class="fa-solid fa-file-invoice" style="font-size:40px;display:block;margin-bottom:16px;opacity:0.3;"></i>
            @if(request('statut'))
                <p style="font-size:16px;font-weight:600;color:var(--text-soft);">
                    Aucune offre avec le statut "{{ request('statut') }}"
                </p>
                <p style="font-size:13px;">Essayez de modifier vos filtres.</p>
                <a href="{{ route('agence.propositions.index') }}" class="btn btn-ghost" style="margin-top:12px;">
                    <i class="fa-solid fa-rotate"></i> Réinitialiser les filtres
                </a>
            @else
                <p style="font-size:16px;">Aucune offre envoyée.</p>
                <a href="{{ route('agence.demandes.index') }}" class="btn btn-rust" style="margin-top:16px;display:inline-flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-search"></i> Voir les besoins disponibles
                </a>
            @endif
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    /* ===================== SECTION HEAD ===================== */
    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .section-head h2 {
        font-family: var(--display);
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .section-head p {
        font-size: 14px;
        color: var(--muted);
        margin: 4px 0 0;
    }

    /* ===================== FILTRES ===================== */
    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-soft);
        background: #fff;
        border: 1px solid var(--border);
        text-decoration: none;
        transition: all 0.2s;
    }

    .filter-btn:hover {
        background: var(--border);
    }

    .filter-btn.active {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .filter-btn.active i {
        color: #fff !important;
    }

    /* ===================== STATUS PILL ===================== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

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
        background: #E3F2FD;
        color: #0D47A1;
    }

    /* ===================== VEDETTE TAG ===================== */
    .vedette-tag-table {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        background: #F5A623;
        box-shadow: 0 2px 6px rgba(245, 166, 35, 0.25);
        animation: pulseVedette 2s ease-in-out infinite;
    }

    .vedette-tag-table i {
        font-size: 9px;
    }

    @keyframes pulseVedette {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    /* ===================== ALERTS ===================== */
    .alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        border-left: 4px solid;
    }

    .alert-success {
        background: #E8F5E9;
        color: #1E7A47;
        border-left-color: #1E7A47;
    }

    .alert-error {
        background: #FFEBEE;
        color: #C62828;
        border-left-color: #C62828;
    }

    /* ===================== BOUTONS ===================== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
    }

    .btn-rust {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
        border-color: #9A4523;
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
        padding: 6px 14px;
        font-size: 12.5px;
    }

    /* ===================== PAGINATION ===================== */
    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pagination a, .pagination span {
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

    .pagination a:hover {
        background: var(--border);
    }

    .pagination .active span {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ============================================
       RESPONSIVE
    ============================================ */
    @media (max-width: 768px) {
        .section-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .filter-btn {
            font-size: 12px;
            padding: 5px 12px;
        }

        table {
            font-size: 12px;
        }

        table th,
        table td {
            padding: 8px 10px !important;
        }

        .vedette-tag-table {
            font-size: 9px;
            padding: 1px 8px;
        }
    }

    @media (max-width: 600px) {
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .filter-btn {
            font-size: 11px;
            padding: 4px 10px;
        }

        .filter-btn i {
            font-size: 10px;
        }

        .pagination a, .pagination span {
            padding: 6px 10px;
            font-size: 12px;
            min-width: 32px;
        }
    }
</style>
@endpush