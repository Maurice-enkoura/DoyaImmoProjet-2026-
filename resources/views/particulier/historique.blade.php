@extends('layouts.dashboard')

@section('title', 'Historique — DoyaImmo')
@section('page_title', 'Historique')
@section('page_sub', 'Toute votre activité sur DoyaImmo')

@section('content')
<div class="view active historique-page">

    @php
        // Compteurs par type
        $countAll = $historique->count();
        $countDemande = collect($historique->items())->where('type', 'demande')->count();
        $countProposition = collect($historique->items())->where('type', 'proposition')->count();
        $countRdv = collect($historique->items())->where('type', 'rendezvous')->count();
        $countEval = collect($historique->items())->where('type', 'evaluation')->count();

        $current = request('type', '');
        $tabs = [
            ''            => ['label' => 'Tout',         'icon' => 'fa-list',          'count' => $countAll],
            'demande'     => ['label' => 'Demandes',     'icon' => 'fa-house',         'count' => $countDemande],
            'proposition' => ['label' => 'Propositions', 'icon' => 'fa-file-invoice',  'count' => $countProposition],
            'rendezvous'  => ['label' => 'RDV',          'icon' => 'fa-calendar-days', 'count' => $countRdv],
            'evaluation'  => ['label' => 'Avis',         'icon' => 'fa-star',          'count' => $countEval],
        ];
    @endphp

    {{-- ═══ HEADER DESKTOP ═══ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Historique</h2>
            <p>Toute votre activité sur DoyaImmo</p>
        </div>
        <button onclick="window.location.reload()" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-rotate"></i> Actualiser
        </button>
    </div>

    {{-- ═══ STATS INLINE (mobile) ═══ --}}
    @if($countAll > 0)
        <div class="histo-stats">
            <div class="histo-stat">
                <span class="histo-stat__value">{{ $countAll }}</span>
                <span class="histo-stat__label">Total</span>
            </div>
            <div class="histo-stat">
                <span class="histo-stat__value">{{ $countDemande }}</span>
                <span class="histo-stat__label">Demandes</span>
            </div>
            <div class="histo-stat {{ $countProposition > 0 ? 'is-highlight' : '' }}">
                <span class="histo-stat__value">{{ $countProposition }}</span>
                <span class="histo-stat__label">Offres</span>
            </div>
            <div class="histo-stat">
                <span class="histo-stat__value">{{ $countRdv }}</span>
                <span class="histo-stat__label">RDV</span>
            </div>
        </div>
    @endif

    {{-- ═══ FILTRES PILLS ═══ --}}
    @if($countAll > 0)
        <nav class="filter-tabs" aria-label="Filtrer l'historique">
            @foreach($tabs as $key => $meta)
                @if($meta['count'] > 0 || $key === '')
                    <a href="{{ route('particulier.historique', $key ? ['type' => $key] : []) }}"
                       class="filter-tab {{ $current === $key ? 'is-active' : '' }}">
                        <i class="fa-solid {{ $meta['icon'] }}"></i>
                        <span>{{ $meta['label'] }}</span>
                        <span class="filter-tab__count">{{ $meta['count'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    @endif

    @if($historique->count() > 0)

        {{-- ═══ TIMELINE ═══ --}}
        <div class="historique-timeline">
            @foreach($historique as $event)
                <div class="timeline-item" data-type="{{ $event['type'] }}">

                    {{-- Dot + ligne --}}
                    <div class="timeline-line">
                        <div class="timeline-dot" style="background: {{ $event['color'] }};">
                            <i class="{{ $event['icon'] }}"></i>
                        </div>
                        @if(!$loop->last)
                            <div class="timeline-bar"></div>
                        @endif
                    </div>

                    {{-- Contenu --}}
                    <div class="timeline-content">

                        {{-- En-tête : titre + badge statut --}}
                        <header class="event-header">
                            <h3 class="event-name">{{ $event['title'] }}</h3>
                            @if(isset($event['status']))
                                <span class="status-pill status-{{ $event['status_class'] ?? 'default' }}">
                                    {{ $event['status'] }}
                                </span>
                            @endif
                        </header>

                        {{-- Description --}}
                        @if($event['description'])
                            <p class="event-description">{{ $event['description'] }}</p>
                        @endif

                        {{-- Médias --}}
                        @if(isset($event['medias']) && $event['medias']->count() > 0)
                            @php
                                $images = $event['medias']->where('type_media', 'image');
                                $videos = $event['medias']->where('type_media', 'video');
                            @endphp

                            @if($images->count() > 0)
                                <div class="event-medias">
                                    <div class="media-grid">
                                        @foreach($images->take(4) as $media)
                                            <div class="media-item" onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                                <img src="{{ asset('storage/' . $media->fichier) }}" alt="Média" loading="lazy">
                                                <div class="media-overlay">
                                                    <i class="fa-solid fa-magnifying-glass-plus"></i>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($images->count() > 4)
                                            <div class="media-item media-more">
                                                <span>+{{ $images->count() - 4 }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($videos->count() > 0)
                                <div class="media-videos">
                                    @foreach($videos->take(2) as $media)
                                        <div class="media-video">
                                            <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata"></video>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="media-count">
                                <span><i class="fa-regular fa-image"></i> {{ $images->count() }} photo{{ $images->count() > 1 ? 's' : '' }}</span>
                                @if($videos->count() > 0)
                                    <span><i class="fa-regular fa-circle-play"></i> {{ $videos->count() }} vidéo{{ $videos->count() > 1 ? 's' : '' }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Proposition associée --}}
                        @if(isset($event['proposition']))
                            <a href="{{ $event['proposition']['link'] }}" class="event-sub-card">
                                <div class="event-sub-card__icon">
                                    <i class="fa-regular fa-file-invoice"></i>
                                </div>
                                <div class="event-sub-card__body">
                                    <span class="event-sub-card__label">Proposition</span>
                                    <span class="event-sub-card__value">
                                        <strong>{{ $event['proposition']['agence'] }}</strong>
                                        <span class="event-sub-card__sep">·</span>
                                        {{ $event['proposition']['prix'] }}
                                    </span>
                                </div>
                                <span class="status-pill status-{{ $event['proposition']['statut_class'] }}">
                                    {{ $event['proposition']['statut'] }}
                                </span>
                                <i class="fa-solid fa-chevron-right event-sub-card__chevron"></i>
                            </a>
                        @endif

                        {{-- Évaluation associée --}}
                        @if(isset($event['evaluation']))
                            <a href="{{ $event['evaluation']['link'] }}" class="event-sub-card">
                                <div class="event-sub-card__icon event-sub-card__icon--gold">
                                    <i class="fa-solid fa-star"></i>
                                </div>
                                <div class="event-sub-card__body">
                                    <span class="event-sub-card__label">Avis donné</span>
                                    <span class="event-sub-card__stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star {{ $i <= $event['evaluation']['note'] ? 'is-on' : '' }}"></i>
                                        @endfor
                                        <strong>{{ $event['evaluation']['note'] }}/5</strong>
                                    </span>
                                    @if($event['evaluation']['commentaire'])
                                        <span class="event-sub-card__comment">
                                            "{{ Str::limit($event['evaluation']['commentaire'], 60) }}"
                                        </span>
                                    @endif
                                </div>
                                <i class="fa-solid fa-chevron-right event-sub-card__chevron"></i>
                            </a>
                        @endif

                        {{-- Détails --}}
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

                        {{-- Meta : date + type --}}
                        <div class="event-meta">
                            <span class="event-date">
                                <i class="fa-regular fa-calendar"></i>
                                {{ $event['date'] }}
                            </span>
                            <span class="event-type">
                                <i class="fa-regular fa-tag"></i>
                                @if($event['type'] === 'demande') Demande
                                @elseif($event['type'] === 'proposition') Proposition
                                @elseif($event['type'] === 'rendezvous') Rendez-vous
                                @elseif($event['type'] === 'evaluation') Avis
                                @else {{ ucfirst($event['type']) }}
                                @endif
                            </span>
                        </div>

                        {{-- Actions --}}
                        @if(isset($event['link']))
                            <a href="{{ $event['link'] }}" class="event-cta">
                                <span>Voir les détails</span>
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $historique->appends(request()->query())->links() }}
        </div>

    @else

        {{-- ═══ EMPTY STATE ═══ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-clock"></i>
            </div>
            <h3>Aucune activité</h3>
            <p>Commencez à publier des demandes pour voir votre activité apparaître ici.</p>
            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust">
                <i class="fa-solid fa-plus"></i> Publier un besoin
            </a>
        </div>
    @endif
</div>

{{-- ═══ LIGHTBOX ═══ --}}
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <button type="button" class="lightbox-close" onclick="closeLightbox()" aria-label="Fermer">&times;</button>
    <img id="lightboxImage" class="lightbox-image" src="" alt="Agrandir" onclick="event.stopPropagation()">
</div>
@endsection


@push('scripts')
<script>
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
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeLightbox();
    });
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   HISTORIQUE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.historique-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #F5A623;
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ HEADER DESKTOP ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
}
.page-header__left h2 {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
}
.page-header__left p {
    font-size: 14px;
    color: var(--muted);
    margin: 4px 0 0;
}

/* ═══ STATS INLINE (mobile) ═══ */
.histo-stats { display: none; }

/* ═══ FILTRES ═══ */
.filter-tabs { display: none; }

/* ═══ TIMELINE ═══ */
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
    transition: background .2s;
}
.timeline-item:last-child { border-bottom: none; }
.timeline-item:hover {
    background: #FAFBFC;
    margin: 0 -8px;
    padding-left: 8px;
    padding-right: 8px;
    border-radius: 8px;
}

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
    box-shadow: 0 2px 8px rgba(0,0,0,.1);
    color: #fff;
    font-size: 12px;
}
.timeline-bar {
    width: 2px;
    flex: 1;
    background: var(--border);
    margin: 4px 0;
    min-height: 20px;
}
.timeline-item:last-child .timeline-bar { display: none; }

.timeline-content { flex: 1; min-width: 0; }

/* ═══ EN-TÊTE EVENT ═══ */
.event-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 6px;
}
.event-name {
    font-family: var(--display);
    font-weight: 600;
    font-size: 14px;
    color: var(--ink);
    margin: 0;
}

.status-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.status-default { background: #F7F9FC; color: var(--text-soft); }
.status-success { background: #E8F5E9; color: #1E7A47; }
.status-warning { background: #FFF8E1; color: #E65100; }
.status-info    { background: #E3F2FD; color: #0D47A1; }
.status-danger  { background: #FFEBEE; color: #C62828; }

/* ═══ DESCRIPTION ═══ */
.event-description {
    font-size: 13px;
    color: var(--text-soft);
    margin: 0 0 10px;
    line-height: 1.6;
}

/* ═══ MÉDIAS ═══ */
.event-medias { margin: 8px 0 10px; }

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
    transition: transform .2s;
}
.media-item:hover { transform: scale(1.03); z-index: 2; }
.media-item img {
    width: 100%; height: 100%;
    object-fit: cover;
}
.media-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.3);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity .2s;
    color: #fff;
    font-size: 20px;
}
.media-item:hover .media-overlay { opacity: 1; }

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
.media-more:hover { transform: scale(1.03); z-index: 2; }

.media-videos {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 8px;
    margin-top: 8px;
}
.media-video {
    border-radius: 8px;
    overflow: hidden;
    background: #000;
    border: 1px solid var(--border);
}
.media-video video {
    width: 100%;
    height: 140px;
    object-fit: cover;
    display: block;
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
.media-count span { display: flex; align-items: center; gap: 4px; }
.media-count i { color: var(--rust); }

/* ═══ SUB-CARD (proposition / évaluation) ═══ */
.event-sub-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    background: var(--surface);
    border-radius: 10px;
    border: 1px solid var(--border);
    margin-top: 8px;
    text-decoration: none;
    color: inherit;
    transition: all .2s;
}
.event-sub-card:hover {
    border-color: var(--rust);
    background: #FFFBF7;
}
.event-sub-card__icon {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: var(--c-info-bg);
    color: var(--c-info);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.event-sub-card__icon--gold {
    background: #FFF8E1;
    color: var(--c-gold);
}
.event-sub-card__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.event-sub-card__label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}
.event-sub-card__value {
    font-size: 13px;
    color: var(--text);
    font-weight: 500;
}
.event-sub-card__value strong { font-weight: 700; }
.event-sub-card__sep { color: var(--muted); margin: 0 3px; }

.event-sub-card__stars {
    display: flex;
    align-items: center;
    gap: 3px;
    font-size: 13px;
}
.event-sub-card__stars .fa-star { color: #D4D8E0; }
.event-sub-card__stars .fa-star.is-on { color: var(--c-gold); }
.event-sub-card__stars strong {
    font-family: var(--display);
    font-weight: 800;
    color: var(--text);
    margin-left: 6px;
}
.event-sub-card__comment {
    font-size: 12px;
    color: var(--muted);
    font-style: italic;
    margin-top: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.event-sub-card__chevron {
    color: var(--muted);
    font-size: 12px;
    opacity: .5;
    flex-shrink: 0;
}

/* ═══ DÉTAILS ═══ */
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
.detail-label { color: var(--muted); font-weight: 500; }
.detail-value { color: var(--text-soft); font-weight: 500; }

/* ═══ META ═══ */
.event-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    font-size: 12px;
    color: var(--muted);
    margin-top: 8px;
}
.event-meta span { display: flex; align-items: center; gap: 4px; }
.event-meta i { font-size: 12px; }

/* ═══ CTA Détails ═══ */
.event-cta {
    display: none;
}

/* ═══ EMPTY ═══ */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
}
.empty-state__icon {
    width: 76px;
    height: 76px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--surface);
    border-radius: 50%;
    color: var(--muted);
    font-size: 30px;
    opacity: .5;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 18px;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 8px;
}
.empty-state p {
    font-size: 13.5px;
    color: var(--muted);
    margin: 0 auto 18px;
    max-width: 400px;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper {
    margin-top: 22px;
    display: flex;
    justify-content: center;
}
.pagination-wrapper .pagination,
.pagination-wrapper nav {
    display: flex;
    gap: 5px;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
}
.pagination-wrapper a,
.pagination-wrapper span:not(.sr-only) {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    font-size: 12.5px;
    transition: all .2s;
    min-width: 36px;
    text-align: center;
    background: #fff;
}
.pagination-wrapper a:hover { background: var(--border); border-color: var(--rust); color: var(--rust); }
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] { background: var(--rust); color: #fff; border-color: var(--rust); }
.pagination-wrapper .disabled span { opacity: .45; cursor: not-allowed; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; color: #fff; border-color: #9A4523; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(180, 83, 42, .25); }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: var(--surface); border-color: var(--rust); color: var(--rust); }
.btn-sm { padding: 6px 12px; font-size: 12px; }

/* ═══ LIGHTBOX ═══ */
.lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .92);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
    cursor: pointer;
}
.lightbox.active { display: flex; }
.lightbox-image {
    max-width: 95%;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 6px;
    cursor: default;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
}
.lightbox-close {
    position: absolute;
    top: 20px; right: 24px;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .12);
    border: none;
    color: #fff;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: sans-serif;
    transition: background .2s;
}
.lightbox-close:hover { background: rgba(255, 255, 255, .25); }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .historique-page { background: transparent; padding-top: 4px; }

    /* ─── HEADER masqué ─── */
    .page-header { display: none; }

    /* ─── STATS INLINE ─── */
    .histo-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .histo-stat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        position: relative;
        padding: 0 4px;
    }
    .histo-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%;
        width: 0.5px;
        background: var(--ios-separator);
    }
    .histo-stat__value {
        font-family: var(--display);
        font-size: 22px;
        font-weight: 800;
        color: var(--ios-label);
        letter-spacing: -.5px;
        line-height: 1;
    }
    .histo-stat.is-highlight .histo-stat__value { color: var(--rust); }
    .histo-stat__label {
        font-size: 11px;
        font-weight: 500;
        color: var(--ios-label-3);
        margin-top: 2px;
    }

    /* ─── FILTRES PILLS iOS ─── */
    .filter-tabs {
        display: flex;
        gap: 8px;
        margin: 0 -16px 14px;
        padding: 0 16px 2px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .filter-tabs::-webkit-scrollbar { display: none; }
    .filter-tab {
        scroll-snap-align: start;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: var(--ios-label-2);
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: all .15s ease;
        -webkit-tap-highlight-color: transparent;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .filter-tab:active { transform: scale(.95); }
    .filter-tab i { font-size: 12px; opacity: .85; }
    .filter-tab.is-active {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .filter-tab.is-active i { opacity: 1; }
    .filter-tab__count {
        background: rgba(0, 0, 0, .07);
        padding: 1px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        min-width: 22px;
        text-align: center;
    }
    .filter-tab.is-active .filter-tab__count {
        background: rgba(255, 255, 255, .3);
        color: #fff;
    }

    /* ─── TIMELINE iOS ─── */
    .historique-timeline {
        background: transparent;
        border: none;
        border-radius: 0;
        padding: 0;
    }

    .timeline-item {
        display: block;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: transform .15s ease;
        position: relative;
        overflow: hidden;
    }
    .timeline-item:hover {
        background: var(--ios-card);
        transform: none;
        margin: 0 0 12px 0;
        padding: 16px;
        border-radius: var(--ios-radius-lg);
    }
    .timeline-item:active { transform: scale(.985); }
    .timeline-item:last-child { margin-bottom: 0; }

    /* Barre colorée en haut */
    .timeline-item::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: var(--ios-radius-lg) var(--ios-radius-lg) 0 0;
    }

    /* Ligne masquée sur mobile */
    .timeline-line { display: none; }

    .timeline-content { width: 100%; }

    /* En-tête */
    .event-header {
        gap: 10px;
        margin-bottom: 8px;
        align-items: flex-start;
    }
    .event-name {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
        color: var(--ios-label);
        flex: 1;
    }
    .status-pill {
        font-size: 10.5px;
        padding: 4px 10px;
        font-weight: 600;
        text-transform: none;
        flex-shrink: 0;
    }

    .event-description {
        font-size: 13.5px;
        color: var(--ios-label-2);
        margin-bottom: 12px;
        line-height: 1.55;
    }

    /* Médias iOS */
    .event-medias { margin: 10px 0; }
    .media-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
    }
    .media-item { border-radius: 10px; border: none; }
    .media-overlay { display: none; }
    .media-more { background: var(--rust); }

    .media-videos { grid-template-columns: 1fr; gap: 8px; }
    .media-video { border-radius: 10px; border: none; }
    .media-video video { height: 160px; }

    .media-count {
        background: #EFEFF4;
        border: none;
        border-radius: 10px;
        font-size: 12.5px;
        padding: 8px 12px;
        color: var(--ios-label-3);
    }

    /* Sub-card iOS */
    .event-sub-card {
        background: #EFEFF4;
        border: none;
        border-radius: 12px;
        padding: 12px 14px;
        margin-top: 10px;
        gap: 10px;
    }
    .event-sub-card:hover { background: #EFEFF4; border: none; }
    .event-sub-card:active { background: #E5E5EA; }

    .event-sub-card__icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        font-size: 15px;
    }
    .event-sub-card__label {
        font-size: 11px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 500;
        color: var(--ios-label-3);
    }
    .event-sub-card__value {
        font-size: 14px;
        font-weight: 500;
        color: var(--ios-label);
    }
    .event-sub-card__value strong { font-weight: 700; }
    .event-sub-card__stars { font-size: 14px; gap: 3px; }
    .event-sub-card__stars strong { font-size: 14px; margin-left: 5px; }
    .event-sub-card__comment { font-size: 12.5px; }
    .event-sub-card__chevron { font-size: 13px; opacity: .4; }

    /* Détails iOS */
    .event-details {
        grid-template-columns: 1fr;
        background: #EFEFF4;
        border: none;
        border-radius: 10px;
        padding: 12px 14px;
        gap: 8px;
    }
    .detail-item {
        font-size: 13px;
        padding: 3px 0;
    }
    .detail-label { font-size: 13px; color: var(--ios-label-3); font-weight: 500; }
    .detail-value { font-size: 13px; color: var(--ios-label); font-weight: 600; }

    /* Meta iOS */
    .event-meta {
        gap: 12px;
        font-size: 12.5px;
        color: var(--ios-label-3);
        margin-top: 12px;
        padding-top: 12px;
        border-top: 0.5px solid var(--ios-separator);
    }
    .event-meta i { font-size: 12px; color: var(--ios-label-3); }
    .event-meta span { gap: 5px; }

    /* CTA Détails iOS */
    .event-cta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 14px;
        margin-top: 10px;
        background: #EFEFF4;
        border-radius: 12px;
        text-decoration: none;
        color: var(--ios-blue);
        font-size: 15px;
        font-weight: 500;
        transition: background .15s;
        -webkit-tap-highlight-color: transparent;
    }
    .event-cta:active { background: #E5E5EA; }
    .event-cta i {
        font-size: 13px;
        opacity: .5;
        color: var(--ios-label-3);
    }

    /* ─── EMPTY iOS ─── */
    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon {
        width: 68px;
        height: 68px;
        background: #EFEFF4;
        color: var(--ios-label-3);
        font-size: 28px;
        opacity: 1;
    }
    .empty-state h3 {
        font-size: 17px;
        letter-spacing: -.3px;
    }
    .empty-state p {
        font-size: 13.5px;
        color: var(--ios-label-3);
    }

    /* ─── Pagination iOS ─── */
    .pagination-wrapper {
        margin-top: 20px;
        padding: 0 16px;
    }
    .pagination-wrapper a,
    .pagination-wrapper span:not(.sr-only) {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        padding: 10px 14px;
        font-size: 13px;
        border-radius: 10px;
    }

    /* ─── LIGHTBOX iOS ─── */
    .lightbox-close {
        top: 14px;
        right: 14px;
        width: 40px;
        height: 40px;
        font-size: 22px;
    }
}

@media (max-width: 480px) {
    .histo-stat__value { font-size: 20px; }
    .histo-stat__label { font-size: 10.5px; }

    .event-name { font-size: 15px; }
    .event-description { font-size: 13px; }

    .media-grid { grid-template-columns: repeat(3, 1fr); gap: 4px; }
    .media-video video { height: 140px; }

    .timeline-item { padding: 14px; }
}
</style>
@endpush