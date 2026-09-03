@extends('layouts.dashboard')

@section('title', 'Mes besoins publiés — DoyaImmo')
@section('page_title', 'Mes besoins publiés')
@section('page_sub', 'Gérez vos demandes de logement actives')

@section('content')
<div class="view active">
    <div class="page-header">
        <div>
            <h2>Mes besoins publiés</h2>
            <p class="sub">Gérez vos demandes de logement actives</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('particulier.historique') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-clock-rotate-left"></i> Voir historique
            </a>
            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust btn-sm">
                <i class="fa-solid fa-plus"></i> Nouveau besoin
            </a>
        </div>
    </div>

    <div class="besoin-grid">
        @forelse($demandes as $demande)
            <div class="besoin-card">
                <div class="besoin-body">
                    <div class="besoin-top">
                        <div class="besoin-type">{{ $demande->type_bien->label() }}</div>
                        <div class="besoin-budget">{{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois</div>
                    </div>
                    
                    <div class="besoin-meta">
                        <span class="meta-pill"><i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}</span>
                        <span class="meta-pill"><i class="fa-regular fa-calendar"></i> {{ $demande->created_at->format('d/m/Y') }}</span>
                    </div>

                    <!-- Statut avec badge -->
                    <div class="status-badge status-{{ $demande->statut_value }}">
                        <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                        {{ $demande->statut_label }}
                    </div>

                    <p class="besoin-desc">
                        {{ Str::limit($demande->description, 120) }}
                    </p>

                    <!-- ✅ NOMBRE D'OFFRES SUR LA CARTE -->
                    <div style="display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap;">
                        <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 14px;border-radius:999px;font-size:13px;font-weight:600;background:#E3F2FD;color:#0D47A1;border:1px solid #BBDEFB;">
                            <i class="fa-regular fa-envelope"></i>
                            {{ $demande->propositions->count() }} offre(s) reçue(s)
                        </span>
                        @if($demande->propositions->where('statut', 'en_attente')->count() > 0)
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 14px;border-radius:999px;font-size:13px;font-weight:600;background:#FFF8E1;color:#E65100;border:1px solid #FFE0B2;">
                                <i class="fa-regular fa-clock"></i>
                                {{ $demande->propositions->where('statut', 'en_attente')->count() }} en attente
                            </span>
                        @endif
                        @if($demande->propositions->where('statut', 'acceptee')->count() > 0)
                            <span style="display:inline-flex;align-items:center;gap:6px;padding:4px 14px;border-radius:999px;font-size:13px;font-weight:600;background:#E8F5E9;color:#1E7A47;border:1px solid #C8E6C9;">
                                <i class="fa-regular fa-check-circle"></i>
                                {{ $demande->propositions->where('statut', 'acceptee')->count() }} acceptée(s)
                            </span>
                        @endif
                    </div>
                    
                    <div class="besoin-foot">
                        <span class="posted">
                            <i class="fa-regular fa-clock"></i> {{ $demande->created_at->diffForHumans() }}
                        </span>
                        <div class="besoin-actions">
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <a href="{{ route('particulier.demandes.show', $demande->slug) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i> Voir
                            </a>
                            @if($demande->propositions->count() > 0)
                                <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                <a href="{{ route('particulier.demandes.offres', $demande->slug) }}" class="btn btn-rust btn-sm">
                                    <i class="fa-solid fa-file-invoice"></i> Voir offres
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fa-regular fa-house-circle-check"></i>
                <h3>Aucun besoin actif</h3>
                <p>Vous n'avez pas encore publié de besoin de logement actif.</p>
                <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust">
                    <i class="fa-solid fa-plus"></i> Publier un besoin
                </a>
            </div>
        @endforelse
    </div>

    <div class="pagination-wrapper">
        {{ $demandes->links() }}
    </div>
</div>
@endsection

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

    /* ===================== STATS ===================== */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
    }

    .stat-box {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 12px 16px;
        text-align: center;
    }

    .stat-number {
        display: block;
        font-family: var(--display);
        font-weight: 700;
        font-size: 22px;
        color: var(--ink);
    }

    .stat-label {
        font-size: 12px;
        color: var(--muted);
    }

    /* ===================== GRID ===================== */
    .besoin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    /* ===================== CARTE ===================== */
    .besoin-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        position: relative;
    }

    .besoin-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }

    .besoin-body {
        padding: 16px 18px 18px;
    }

    .besoin-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 6px;
    }

    .besoin-type {
        font-family: var(--display);
        font-weight: 700;
        font-size: 15px;
        color: var(--ink);
    }

    .besoin-budget {
        font-weight: 700;
        color: var(--rust);
        font-size: 14px;
    }

    .besoin-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 10px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11.5px;
        color: var(--text-soft);
    }

    .meta-pill i {
        font-size: 11px;
    }

    /* ===================== STATUS BADGE ===================== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 10px;
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

    .besoin-desc {
        font-size: 13px;
        color: var(--text-soft);
        margin: 10px 0 14px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.6;
    }

    /* ===================== OFFRE COUNTER ===================== */
    .offre-counter {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .offre-counter .badge-offre {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
    }

    .badge-offre.total {
        background: #E3F2FD;
        color: #0D47A1;
        border: 1px solid #BBDEFB;
    }

    .badge-offre.attente {
        background: #FFF8E1;
        color: #E65100;
        border: 1px solid #FFE0B2;
    }

    .badge-offre.acceptee {
        background: #E8F5E9;
        color: #1E7A47;
        border: 1px solid #C8E6C9;
    }

    .besoin-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 8px;
    }

    .posted {
        font-size: 12px;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .besoin-actions {
        display: flex;
        gap: 6px;
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
        grid-column: 1 / -1;
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
        margin: 0 0 16px;
    }

    /* ===================== PAGINATION ===================== */
    .pagination-wrapper {
        margin-top: 30px;
        display: flex;
        justify-content: center;
    }

    .pagination {
        display: flex;
        gap: 6px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination li {
        display: inline;
    }

    .pagination a,
    .pagination span {
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
        border-color: var(--border);
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

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .page-header .btn {
            justify-content: center;
        }

        .stats-row {
            grid-template-columns: repeat(3, 1fr);
        }

        .besoin-grid {
            grid-template-columns: 1fr;
        }

        .besoin-foot {
            flex-direction: column;
            align-items: stretch;
        }

        .besoin-actions {
            justify-content: stretch;
        }

        .besoin-actions .btn {
            flex: 1;
            justify-content: center;
        }

        .offre-counter {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .stat-number {
            font-size: 18px;
        }

        .besoin-top {
            flex-direction: column;
            gap: 4px;
        }

        .besoin-type {
            font-size: 14px;
        }

        .besoin-budget {
            font-size: 13px;
        }

        .pagination a,
        .pagination span {
            padding: 6px 10px;
            font-size: 12px;
            min-width: 32px;
        }

        .besoin-actions .btn {
            font-size: 11px;
            padding: 4px 10px;
        }

        .offre-counter .badge-offre {
            font-size: 11px;
            padding: 3px 10px;
        }
    }
</style>
@endpush