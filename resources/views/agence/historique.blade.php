@extends('layouts.dashboard-agence')

@section('title', 'Historique — DoyaImmo')
@section('page_title', 'Historique')
@section('page_sub', 'Toute votre activité sur DoyaImmo')

@section('content')
<div class="view active">
    <!-- En-tête avec filtres -->
    <div class="page-header">
        <div>
            <h2>Historique</h2>
            <p class="sub">Toute votre activité sur DoyaImmo</p>
        </div>
        <div class="header-actions">
            <div class="filter-group">
                <select id="filterType" onchange="filterHistorique()" style="padding:8px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;background:#fff;font-family:inherit;cursor:pointer;">
                    <option value="all">Toutes les activités</option>
                    <option value="proposition"> Offres envoyées</option>
                    <option value="rendezvous"> Rendez-vous</option>
                    <option value="evaluation">⭐ Avis reçus</option>
                </select>
            </div>
            <button onclick="window.location.reload()" class="btn btn-ghost btn-sm" title="Actualiser">
                <i class="fa-solid fa-rotate"></i>
            </button>
        </div>
    </div>

    @if($activites->count() > 0)
        <div class="historique-timeline">
            @foreach($activites as $event)
                <div class="timeline-item" data-type="{{ $event['type'] }}">
                    <!-- Ligne de temps -->
                    <div class="timeline-line">
                        <div class="timeline-dot" style="background:
                            @if($event['type'] === 'proposition') #E65100;
                            @elseif($event['type'] === 'rendezvous') #0D47A1;
                            @elseif($event['type'] === 'evaluation') #1E7A47;
                            @else var(--muted);
                            @endif
                        ">
                            <i class="
                                @if($event['type'] === 'proposition') fa-solid fa-paper-plane
                                @elseif($event['type'] === 'rendezvous') fa-regular fa-calendar-check
                                @elseif($event['type'] === 'evaluation') fa-solid fa-star
                                @else fa-solid fa-circle
                                @endif
                            " style="color:#fff;font-size:12px;"></i>
                        </div>
                        @if(!$loop->last)
                            <div class="timeline-bar"></div>
                        @endif
                    </div>

                    <!-- Contenu -->
                    <div class="timeline-content">
                        <div class="event-header">
                            <div class="event-title">
                                <span class="event-icon" style="background:
                                    @if($event['type'] === 'proposition') #E65100;
                                    @elseif($event['type'] === 'rendezvous') #0D47A1;
                                    @elseif($event['type'] === 'evaluation') #1E7A47;
                                    @else var(--muted);
                                    @endif
                                ">
                                    <i class="
                                        @if($event['type'] === 'proposition') fa-solid fa-paper-plane
                                        @elseif($event['type'] === 'rendezvous') fa-regular fa-calendar-check
                                        @elseif($event['type'] === 'evaluation') fa-solid fa-star
                                        @else fa-solid fa-circle
                                        @endif
                                    " style="color:#fff;font-size:14px;"></i>
                                </span>
                                <span class="event-name">{{ $event['titre'] }}</span>
                            </div>
                            <div class="event-badge">
                                @if(isset($event['statut']))
                                    <span class="status-pill status-{{ $event['statut_class'] ?? 'default' }}">
                                        {{ $event['statut'] }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="event-body">
                            <p class="event-description">{{ $event['description'] }}</p>

                            <!-- ✅ AFFICHAGE DES IMAGES -->
                            @if(isset($event['medias']) && $event['medias']->count() > 0)
                                <div class="event-medias">
                                    <div class="media-grid">
                                        @foreach($event['medias']->take(4) as $media)
                                            <div class="media-item" onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                                <img src="{{ asset('storage/' . $media->fichier) }}" alt="Image du bien">
                                                <div class="media-overlay">
                                                    <i class="fa-regular fa-magnifying-glass-plus"></i>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($event['medias']->count() > 4)
                                            <div class="media-item media-more">
                                                <span>+{{ $event['medias']->count() - 4 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="media-count">
                                        <span><i class="fa-regular fa-image"></i> {{ $event['medias']->count() }} photo(s)</span>
                                    </div>
                                </div>
                            @else
                                <!-- ✅ PLACEHOLDER -->
                                <div class="event-medias">
                                    <div class="media-grid">
                                        <div class="media-item media-placeholder">
                                            <div class="media-placeholder-content">
                                                <i class="fa-solid fa-image"></i>
                                                <span>Aucune image</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- ✅ DÉTAILS -->
                            @if(isset($event['details']) && count($event['details']) > 0)
                                <div class="event-details">
                                    @foreach($event['details'] as $key => $value)
                                        <div class="detail-item">
                                            <span class="detail-label">{{ $key }}</span>
                                            <span class="detail-value">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="event-meta">
                                <span class="event-date">
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y à H:i') }}
                                </span>
                                <span class="event-type">
                                    <i class="fa-regular fa-tag"></i>
                                    @if($event['type'] === 'proposition') Offre
                                    @elseif($event['type'] === 'rendezvous') Rendez-vous
                                    @elseif($event['type'] === 'evaluation') Avis
                                    @else {{ ucfirst($event['type']) }}
                                    @endif
                                </span>
                            </div>

                            <div class="event-actions">
                                @if(isset($event['demande_link']))
                                    <!-- ✅ CORRIGÉ : Utilisation du slug dans le lien -->
                                    <a href="{{ $event['demande_link'] }}" class="btn btn-ghost btn-sm">
                                        <i class="fa-regular fa-file-lines"></i> Voir la demande
                                    </a>
                                @endif
                                @if(isset($event['link']))
                                    <a href="{{ $event['link'] }}" class="btn btn-ghost btn-sm">
                                        <i class="fa-solid fa-eye"></i> Voir les détails
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination avec gestion du nombre d'éléments par page -->
        <div class="pagination-wrapper">
            <div class="pagination-info">
                <span class="pagination-stats">
                    <i class="fa-solid fa-paper-plane" style="color:var(--rust);"></i>
                    Affichage de <strong>{{ $activites->firstItem() }}</strong> à <strong>{{ $activites->lastItem() }}</strong> 
                    sur <strong>{{ $activites->total() }}</strong> activités
                </span>
            </div>
            {{ $activites->appends(request()->query())->links() }}
            <div class="pagination-per-page">
                <span class="per-page-label">
                    <i class="fa-solid fa-paper-plane" style="color:var(--rust);font-size:12px;"></i>
                    Afficher :
                </span>
                <select id="perPage" class="per-page-select" onchange="changePerPage(this.value)">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span class="per-page-text">par page</span>
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fa-regular fa-clock"></i>
            <h3>Aucune activité</h3>
            <p>Commencez à publier des biens ou à répondre aux demandes pour voir votre activité apparaître ici.</p>
            <a href="{{ route('agence.biens.create') }}" class="btn btn-rust">
                <i class="fa-solid fa-plus"></i> Publier un bien
            </a>
        </div>
    @endif
</div>

<!-- Lightbox -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <img id="lightboxImage" class="lightbox-image" src="" alt="Agrandir">
</div>
@endsection

@push('scripts')
<script>
    function filterHistorique() {
        const filter = document.getElementById('filterType').value;
        const items = document.querySelectorAll('.timeline-item');
        
        items.forEach(item => {
            if (filter === 'all') {
                item.style.display = 'flex';
            } else {
                item.style.display = item.dataset.type === filter ? 'flex' : 'none';
            }
        });
    }

    function openLightbox(src) {
        const lightbox = document.getElementById('lightbox');
        const image = document.getElementById('lightboxImage');
        image.src = src;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
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
        align-items: center;
    }

    .filter-group select {
        padding: 8px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        background: #fff;
        font-family: inherit;
        cursor: pointer;
        min-width: 180px;
    }

    .filter-group select:focus {
        outline: none;
        border-color: var(--rust);
    }

    /* ===================== TIMELINE ===================== */
    .historique-timeline {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 24px;
        position: relative;
    }

    .timeline-item {
        display: flex;
        gap: 16px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .timeline-item:last-child {
        border-bottom: none;
    }

    .timeline-item:hover {
        background: #FAFBFC;
        margin: 0 -8px;
        padding-left: 8px;
        padding-right: 8px;
        border-radius: 8px;
    }

    /* ===================== TIMELINE LINE ===================== */
    .timeline-line {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex-shrink: 0;
        padding-top: 4px;
    }

    .timeline-dot {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        z-index: 2;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .timeline-bar {
        width: 2px;
        flex: 1;
        background: var(--border);
        margin: 4px 0;
        min-height: 20px;
    }

    .timeline-item:last-child .timeline-bar {
        display: none;
    }

    /* ===================== TIMELINE CONTENT ===================== */
    .timeline-content {
        flex: 1;
        min-width: 0;
    }

    .event-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 4px;
    }

    .event-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .event-icon {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .event-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--ink);
    }

    .event-badge {
        flex-shrink: 0;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    .status-default {
        background: #F7F9FC;
        color: var(--text-soft);
    }
    .status-success {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-warning {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-info {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-danger {
        background: #FFEBEE;
        color: #C62828;
    }

    .event-body {
        margin-top: 2px;
    }

    .event-description {
        font-size: 13px;
        color: var(--text-soft);
        margin: 0 0 8px;
        line-height: 1.6;
    }

    /* ===================== MEDIAS ===================== */
    .event-medias {
        margin: 8px 0 12px;
    }

    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 8px;
    }

    .media-item {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        aspect-ratio: 1;
        cursor: pointer;
        background: #F0F2F5;
        border: 1px solid var(--border);
        transition: transform 0.2s;
    }

    .media-item:hover {
        transform: scale(1.03);
        z-index: 2;
    }

    .media-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .media-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        color: #fff;
        font-size: 20px;
    }

    .media-item:hover .media-overlay {
        opacity: 1;
    }

    .media-more {
        background: var(--ink);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 18px;
        cursor: pointer;
    }

    .media-more:hover {
        transform: scale(1.03);
        z-index: 2;
    }

    .media-placeholder {
        background: #F7F9FC;
        border: 1px dashed var(--border);
        cursor: default;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .media-placeholder:hover {
        transform: none !important;
    }

    .media-placeholder-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        color: var(--muted);
        font-size: 12px;
    }

    .media-placeholder-content i {
        font-size: 28px;
        opacity: 0.3;
    }

    .media-count {
        display: flex;
        gap: 16px;
        padding: 6px 12px;
        background: #F7F9FC;
        border-radius: 6px;
        font-size: 12px;
        color: var(--muted);
        margin-top: 6px;
        border: 1px solid var(--border);
    }

    .media-count span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .media-count i {
        color: var(--rust);
    }

    /* ===================== EVENT DETAILS ===================== */
    .event-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px;
        margin-top: 8px;
        padding: 10px 14px;
        background: #F7F9FC;
        border-radius: 8px;
        border: 1px solid var(--border);
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }

    .detail-label {
        color: var(--muted);
        font-weight: 500;
    }

    .detail-value {
        color: var(--text-soft);
        font-weight: 500;
    }

    /* ===================== EVENT META ===================== */
    .event-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }

    .event-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .event-meta i {
        font-size: 12px;
    }

    /* ===================== EVENT ACTIONS ===================== */
    .event-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
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
        margin: 0 0 16px;
    }

    /* ===================== PAGINATION ===================== */
    .pagination-wrapper {
        margin-top: 30px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .pagination-info {
        width: 100%;
        text-align: center;
    }

    .pagination-stats {
        font-size: 13px;
        color: var(--text-soft);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .pagination-stats strong {
        color: var(--ink);
        font-weight: 700;
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

    .pagination-wrapper .pagination li {
        display: inline;
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

    .pagination-per-page {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-soft);
        border-top: 1px solid var(--border);
        padding-top: 12px;
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
    }

    .per-page-label {
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .per-page-select {
        padding: 5px 24px 5px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
        color: var(--ink);
        background: #fff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
        transition: border-color 0.3s;
    }

    .per-page-select:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }

    .per-page-text {
        color: var(--muted);
    }

    /* ===================== LIGHTBOX ===================== */
    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.92);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        cursor: pointer;
    }

    .lightbox.active {
        display: flex;
    }

    .lightbox-image {
        max-width: 95%;
        max-height: 90vh;
        object-fit: contain;
        cursor: default;
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: #fff;
        font-size: 40px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
        font-family: sans-serif;
        line-height: 1;
    }

    .lightbox-close:hover {
        opacity: 1;
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
            justify-content: stretch;
        }

        .filter-group select {
            width: 100%;
            min-width: unset;
        }

        .historique-timeline {
            padding: 12px 16px;
        }

        .timeline-item {
            padding: 10px 0;
        }

        .event-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .event-badge {
            align-self: flex-start;
        }

        .event-details {
            grid-template-columns: 1fr;
        }

        .event-meta {
            gap: 10px;
            font-size: 11px;
        }

        .media-grid {
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
        }

        .event-actions {
            flex-direction: column;
        }

        .event-actions .btn {
            justify-content: center;
        }

        .pagination-per-page {
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
        }
    }

    @media (max-width: 480px) {
        .timeline-dot {
            width: 28px;
            height: 28px;
        }

        .timeline-dot i {
            font-size: 10px;
        }

        .event-name {
            font-size: 13px;
        }

        .event-description {
            font-size: 12px;
        }

        .pagination-wrapper .pagination a,
        .pagination-wrapper .pagination span {
            padding: 6px 10px;
            font-size: 12px;
            min-width: 32px;
        }

        .media-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .media-count {
            flex-direction: column;
            gap: 4px;
            align-items: center;
        }

        .event-details {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush