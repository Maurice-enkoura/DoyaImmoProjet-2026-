@extends('layouts.dashboard-agence')

@section('title', 'Avis reçus — DoyaImmo')
@section('page_title', 'Avis reçus')
@section('page_sub', 'Ce que pensent vos clients de votre service')

@section('content')
<div class="view active">
    <!-- En-tête avec statistiques -->
    <div class="page-header">
        <div>
            <h2>Avis reçus</h2>
            <p class="sub">Ce que pensent vos clients de votre service</p>
        </div>
        <div class="header-actions">
            <span class="avis-count">
                <i class="fa-regular fa-star"></i>
                {{ $evaluations->total() }} avis
            </span>
            @if($evaluations->count() > 0)
                <span class="avis-rating">
                    <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                    {{ number_format($evaluations->avg('note') ?? 0, 1) }} / 5
                </span>
            @endif
        </div>
    </div>

    <!-- Statistiques rapides -->
    @if($evaluations->count() > 0)
        <div class="stats-row">
            <div class="stat-box">
                <span class="stat-number">{{ $evaluations->total() }}</span>
                <span class="stat-label">Total avis</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">{{ number_format($evaluations->avg('note') ?? 0, 1) }}</span>
                <span class="stat-label">Note moyenne</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">{{ $evaluations->where('note', '>=', 4)->count() }}</span>
                <span class="stat-label">Avis positifs (4-5★)</span>
            </div>
            <div class="stat-box">
                <span class="stat-number">{{ $evaluations->where('note', '<', 3)->count() }}</span>
                <span class="stat-label">Avis négatifs (1-2★)</span>
            </div>
        </div>

        <!-- Filtres -->
        <div class="filter-bar">
            <select id="filterNote" onchange="filterAvis()" style="padding:8px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;font-family:inherit;cursor:pointer;">
                <option value="all">Toutes les notes</option>
                <option value="5">5 ★</option>
                <option value="4">4 ★</option>
                <option value="3">3 ★</option>
                <option value="2">2 ★</option>
                <option value="1">1 ★</option>
            </select>
            <select id="filterReponse" onchange="filterAvis()" style="padding:8px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;font-family:inherit;cursor:pointer;">
                <option value="all">Tous</option>
                <option value="repondu">Avec réponse</option>
                <option value="non_repondu">Sans réponse</option>
            </select>
        </div>

        <!-- Liste des avis -->
        <div class="avis-list">
            @foreach($evaluations as $evaluation)
                <div class="avis-card" data-note="{{ $evaluation->note }}" data-repondu="{{ $evaluation->reponse_agence ? 'oui' : 'non' }}">
                    <div class="avis-header">
                        <div class="avis-client">
                            <div class="client-avatar">
                                {{ strtoupper(substr($evaluation->particulier->user->prenom ?? 'C', 0, 1)) }}
                            </div>
                            <div>
                                <div class="client-name">
                                    {{ $evaluation->particulier->user->prenom ?? '' }} {{ $evaluation->particulier->user->nom ?? '' }}
                                </div>
                                <div class="client-date">
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ $evaluation->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="avis-rating">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star {{ $i <= $evaluation->note ? 'active' : '' }}"></i>
                            @endfor
                            <span class="rating-number">{{ $evaluation->note }}/5</span>
                        </div>
                    </div>

                    <div class="avis-body">
                        @if($evaluation->commentaire)
                            <p class="avis-commentaire">{{ $evaluation->commentaire }}</p>
                        @endif
                        @if($evaluation->proposition && $evaluation->proposition->bien)
                            <div class="avis-bien">
                                <i class="fa-regular fa-building"></i>
                                Concernant : {{ $evaluation->proposition->bien->titre ?? 'Bien' }}
                            </div>
                        @endif
                    </div>

                    <div class="avis-footer">
                        @if($evaluation->reponse_agence)
                            <div class="avis-reponse">
                                <div class="reponse-header">
                                    <span class="reponse-label">
                                        <i class="fa-regular fa-reply"></i> Votre réponse :
                                    </span>
                                    <span class="reponse-date">{{ $evaluation->date_reponse ? $evaluation->date_reponse->format('d/m/Y') : '' }}</span>
                                </div>
                                <p class="reponse-text">{{ $evaluation->reponse_agence }}</p>
                            </div>
                        @else
                            <div class="avis-actions">
                                <a href="{{ route('agence.evaluations.show', $evaluation) }}" class="btn btn-rust btn-sm">
                                    <i class="fa-solid fa-reply"></i> Répondre
                                </a>
                            </div>
                        @endif
                        <a href="{{ route('agence.evaluations.show', $evaluation) }}" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-eye"></i> Détails
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            {{ $evaluations->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fa-regular fa-star"></i>
            <h3>Aucun avis reçu</h3>
            <p>Vous n'avez pas encore reçu d'avis de vos clients.</p>
            <p style="font-size:13px;color:var(--muted);margin-top:4px;">
                Les avis apparaîtront ici une fois que vos clients auront terminé une visite.
            </p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function filterAvis() {
        const note = document.getElementById('filterNote').value;
        const reponse = document.getElementById('filterReponse').value;
        
        document.querySelectorAll('.avis-card').forEach(card => {
            let show = true;
            
            if (note !== 'all' && card.dataset.note !== note) {
                show = false;
            }
            
            if (reponse !== 'all') {
                if (reponse === 'repondu' && card.dataset.repondu !== 'oui') {
                    show = false;
                }
                if (reponse === 'non_repondu' && card.dataset.repondu !== 'non') {
                    show = false;
                }
            }
            
            card.style.display = show ? 'block' : 'none';
        });
    }
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
        align-items: center;
        flex-wrap: wrap;
    }

    .avis-count, .avis-rating {
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

    .avis-count i {
        color: var(--rust);
    }

    .avis-rating {
        background: #FFF8E1;
        border-color: #FFE0B2;
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

    /* ===================== FILTRES ===================== */
    .filter-bar {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .filter-bar select {
        padding: 8px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        background: #fff;
        font-family: inherit;
        cursor: pointer;
        min-width: 150px;
    }

    .filter-bar select:focus {
        outline: none;
        border-color: var(--rust);
    }

    /* ===================== AVIS LIST ===================== */
    .avis-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .avis-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        transition: box-shadow 0.2s;
    }

    .avis-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.04);
    }

    /* ===================== AVIS HEADER ===================== */
    .avis-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 10px;
    }

    .avis-client {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .client-avatar {
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

    .client-name {
        font-weight: 600;
        font-size: 14px;
    }

    .client-date {
        font-size: 12px;
        color: var(--muted);
    }

    .client-date i {
        margin-right: 4px;
    }

    .avis-rating {
        text-align: right;
        flex-shrink: 0;
    }

    .avis-rating .fa-star {
        color: #D4D8E0;
        font-size: 14px;
    }

    .avis-rating .fa-star.active {
        color: #F5A623;
    }

    .rating-number {
        display: block;
        font-weight: 600;
        font-size: 13px;
        color: var(--text-soft);
        margin-top: 2px;
    }

    /* ===================== AVIS BODY ===================== */
    .avis-body {
        margin: 8px 0 12px;
    }

    .avis-commentaire {
        font-size: 13.5px;
        color: var(--text-soft);
        line-height: 1.7;
        margin: 0 0 6px;
    }

    .avis-bien {
        font-size: 12px;
        color: var(--muted);
    }

    .avis-bien i {
        margin-right: 4px;
    }

    /* ===================== AVIS FOOTER ===================== */
    .avis-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--border);
    }

    .avis-reponse {
        flex: 1;
        padding: 10px 14px;
        background: #E3F2FD;
        border-radius: 8px;
        border-left: 3px solid #0D47A1;
    }

    .reponse-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        margin-bottom: 4px;
    }

    .reponse-label {
        font-weight: 600;
        color: #0D47A1;
    }

    .reponse-label i {
        margin-right: 4px;
    }

    .reponse-date {
        color: var(--muted);
    }

    .reponse-text {
        font-size: 13px;
        color: var(--text-soft);
        margin: 0;
        line-height: 1.6;
    }

    .avis-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
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

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .header-actions {
            justify-content: flex-start;
        }

        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .avis-header {
            flex-direction: column;
            align-items: stretch;
        }

        .avis-rating {
            text-align: left;
        }

        .avis-footer {
            flex-direction: column;
            align-items: stretch;
        }

        .avis-actions {
            justify-content: stretch;
        }

        .avis-actions .btn {
            flex: 1;
            justify-content: center;
        }

        .filter-bar {
            flex-direction: column;
        }

        .filter-bar select {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .stats-row {
            grid-template-columns: 1fr 1fr;
        }

        .stat-number {
            font-size: 18px;
        }

        .avis-card {
            padding: 14px 16px;
        }

        .avis-commentaire {
            font-size: 13px;
        }

        .reponse-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }
    }
</style>
@endpush