@extends('layouts.dashboard-agence')

@section('title', 'Historique — DoyaImmo')
@section('page_title', 'Historique')
@section('page_sub', 'Toute votre activité sur DoyaImmo')

@section('content')
<div class="view active ios-historique-page">

    @php
        // Compteurs par type
        $counts = [
            'all'         => $activites->total(),
            'proposition' => $activites->where('type', 'proposition')->count(),
            'rendezvous'  => $activites->where('type', 'rendezvous')->count(),
            'evaluation'  => $activites->where('type', 'evaluation')->count(),
        ];
    @endphp

    {{-- ═══ HEADER DESKTOP ═══ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Historique</h2>
            <p>
                <strong>{{ $counts['all'] }}</strong>
                activité{{ $counts['all'] > 1 ? 's' : '' }} au total
            </p>
        </div>
        <div class="page-header__actions">
            <select id="filterType" class="ios-select-desktop" onchange="filterHistorique()">
                <option value="all">Toutes les activités</option>
                <option value="proposition">Offres envoyées ({{ $counts['proposition'] }})</option>
                <option value="rendezvous">Rendez-vous ({{ $counts['rendezvous'] }})</option>
                <option value="evaluation">Avis reçus ({{ $counts['evaluation'] }})</option>
            </select>
            <button onclick="window.location.reload()" class="ios-btn ios-btn--ghost ios-btn--sm" title="Actualiser">
                <i class="fa-solid fa-rotate"></i> Actualiser
            </button>
        </div>
    </div>

    {{-- ═══ LARGE TITLE iOS (mobile) ═══ --}}
    <h1 class="ios-large-title">Historique</h1>
    <p class="ios-subtitle">Toute votre activité sur DoyaImmo</p>

    @if($activites->count() > 0)

        {{-- ═══ FILTRES iOS — segmented control ═══ --}}
        <div class="ios-segmented" id="filters">
            <button type="button" class="ios-segmented__btn is-active" data-filter="all">
                Tous
                <span class="ios-segmented__count">{{ $counts['all'] }}</span>
            </button>

            <button type="button" class="ios-segmented__btn" data-filter="proposition">
                Offres
                <span class="ios-segmented__count">{{ $counts['proposition'] }}</span>
            </button>

            <button type="button" class="ios-segmented__btn" data-filter="rendezvous">
                RDV
                <span class="ios-segmented__count">{{ $counts['rendezvous'] }}</span>
            </button>

            <button type="button" class="ios-segmented__btn" data-filter="evaluation">
                Avis
                <span class="ios-segmented__count">{{ $counts['evaluation'] }}</span>
            </button>
        </div>

        {{-- Message filtre vide --}}
        <div class="ios-empty ios-empty--compact" id="filterEmpty" hidden>
            <div class="ios-empty__icon">
                <i class="fa-solid fa-filter-circle-xmark"></i>
            </div>
            <p class="ios-empty__text">Aucune activité dans cette catégorie.</p>
        </div>

        {{-- ═══ LISTE DES ACTIVITÉS — timeline iOS ═══ --}}
        <div class="ios-section-header">
            Activités
            <span class="ios-section-header__count">{{ $counts['all'] }}</span>
        </div>

        <div class="ios-timeline" id="timeline">
            @foreach($activites as $event)
                @php
                    $typeMeta = [
                        'proposition' => ['class' => 'proposition', 'icon' => 'fa-paper-plane',         'label' => 'Offre envoyée'],
                        'rendezvous'  => ['class' => 'rendezvous',  'icon' => 'fa-calendar-check',     'label' => 'Rendez-vous'],
                        'evaluation'  => ['class' => 'evaluation',  'icon' => 'fa-star',               'label' => 'Avis reçu'],
                    ];
                    $meta = $typeMeta[$event['type']] ?? ['class' => 'default', 'icon' => 'fa-circle', 'label' => ucfirst($event['type'])];
                @endphp

                <article class="ios-timeline-item" data-type="{{ $event['type'] }}">

                    {{-- Dot timeline --}}
                    <div class="ios-timeline-dot ios-timeline-dot--{{ $meta['class'] }}">
                        <i class="fa-solid {{ $meta['icon'] }}"></i>
                    </div>

                    {{-- Carte --}}
                    <div class="ios-timeline-card">

                        {{-- Header --}}
                        <header class="ios-timeline-card__head">
                            <div class="ios-timeline-card__title">
                                {{ $event['titre'] }}
                            </div>
                            @if(isset($event['statut']))
                                <span class="ios-badge ios-badge--{{ $event['statut_class'] ?? 'default' }}">
                                    {{ $event['statut'] }}
                                </span>
                            @endif
                        </header>

                        {{-- Description --}}
                        <p class="ios-timeline-card__desc">{{ $event['description'] }}</p>

                        {{-- Médias (miniatures) --}}
                        @if(isset($event['medias']) && $event['medias']->count() > 0)
                            <div class="ios-timeline-gallery">
                                @foreach($event['medias']->take(4) as $media)
                                    <div class="ios-timeline-gallery__item"
                                         onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                        <img src="{{ asset('storage/' . $media->fichier) }}" alt="Image du bien" loading="lazy">
                                    </div>
                                @endforeach
                                @if($event['medias']->count() > 4)
                                    <div class="ios-timeline-gallery__more">
                                        +{{ $event['medias']->count() - 4 }}
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Détails --}}
                        @if(isset($event['details']) && count($event['details']) > 0)
                            <div class="ios-timeline-details">
                                @foreach($event['details'] as $key => $value)
                                    <div class="ios-timeline-details__row">
                                        <span class="ios-timeline-details__label">{{ $key }}</span>
                                        <span class="ios-timeline-details__value">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Meta + Actions (inline sur desktop) --}}
                        <div class="ios-timeline-footer">
                            <div class="ios-timeline-meta">
                                <span>
                                    <i class="fa-regular fa-calendar"></i>
                                    {{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y à H:i') }}
                                </span>
                                <span class="ios-timeline-meta__type">
                                    {{ $meta['label'] }}
                                </span>
                            </div>

                            @if(isset($event['link']) || isset($event['demande_link']))
                                <div class="ios-timeline-actions">
                                    @if(isset($event['demande_link']))
                                        <a href="{{ $event['demande_link'] }}" class="ios-btn ios-btn--gray">
                                            <i class="fa-regular fa-file-lines"></i> Voir la demande
                                        </a>
                                    @endif
                                    @if(isset($event['link']))
                                        <a href="{{ $event['link'] }}" class="ios-btn ios-btn--gray">
                                            <i class="fa-solid fa-eye"></i> Voir les détails
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- ═══ PAGINATION iOS ═══ --}}
        <div class="ios-pagination-wrapper">
            <p class="ios-pagination-stats">
                <strong>{{ $activites->firstItem() }}</strong> à
                <strong>{{ $activites->lastItem() }}</strong>
                sur <strong>{{ $activites->total() }}</strong> activités
            </p>

            <div class="ios-pagination">
                {{ $activites->appends(request()->query())->links() }}
            </div>

            <div class="ios-per-page">
                <span>Afficher :</span>
                <select id="perPage" class="ios-per-page__select" onchange="changePerPage(this.value)">
                    <option value="10"  {{ request('per_page') == 10  ? 'selected' : '' }}>10</option>
                    <option value="25"  {{ request('per_page') == 25  ? 'selected' : '' }}>25</option>
                    <option value="50"  {{ request('per_page') == 50  ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
                <span>par page</span>
            </div>
        </div>

    @else

        {{-- ═══ EMPTY STATE ═══ --}}
        <div class="ios-empty">
            <div class="ios-empty__icon">
                <i class="fa-regular fa-clock"></i>
            </div>
            <h3 class="ios-empty__title">Aucune activité</h3>
            <p class="ios-empty__text">
                Commencez à publier des biens ou à répondre aux demandes pour voir votre activité.
            </p>
            <a href="{{ route('agence.biens.create') }}" class="ios-btn ios-btn--primary">
                <i class="fa-solid fa-plus"></i> Publier un bien
            </a>
        </div>

    @endif
</div>

{{-- ═══ LIGHTBOX ═══ --}}
<div id="lightbox" class="ios-lightbox" onclick="closeLightbox()">
    <button type="button" class="ios-lightbox__close"
            onclick="event.stopPropagation();closeLightbox()" aria-label="Fermer">
        <i class="fa-solid fa-xmark"></i>
    </button>
    <img id="lightboxImage" class="ios-lightbox__img" src="" alt="Agrandir" onclick="event.stopPropagation()">
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ─── Filtres ───
        const filterBtns = document.querySelectorAll('.ios-segmented__btn');
        const items      = document.querySelectorAll('.ios-timeline-item');
        const emptyMsg   = document.getElementById('filterEmpty');
        let currentFilter = 'all';

        function applyFilter() {
            let visible = 0;
            items.forEach(item => {
                const show = currentFilter === 'all' || item.dataset.type === currentFilter;
                item.hidden = !show;
                if (show) visible++;
            });
            if (emptyMsg) emptyMsg.hidden = visible > 0;
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('is-active'));
                btn.classList.add('is-active');
                currentFilter = btn.dataset.filter;
                applyFilter();
            });
        });

        // ─── Desktop select (compat) ───
        const desktopSelect = document.getElementById('filterType');
        if (desktopSelect) {
            desktopSelect.addEventListener('change', () => {
                currentFilter = desktopSelect.value;
                filterBtns.forEach(b => {
                    b.classList.toggle('is-active', b.dataset.filter === currentFilter);
                });
                applyFilter();
            });
        }
    });

    function openLightbox(src) {
        const lightbox = document.getElementById('lightbox');
        const image = document.getElementById('lightboxImage');
        image.src = src;
        lightbox.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
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

.ios-historique-page {
    --ios-blue:      #007AFF;
    --ios-green:     #34C759;
    --ios-red:       #FF3B30;
    --ios-orange:    #FF9500;
    --ios-yellow:    #FFCC00;
    --ios-gray:      #8E8E93;
    --ios-gray-3:    #C7C7CC;
    --ios-gray-5:    #E5E5EA;
    --ios-gray-6:    #F2F2F7;

    --ios-bg:        #F2F2F7;
    --ios-card:      #FFFFFF;
    --ios-label:     #000000;
    --ios-label-2:   rgba(60, 60, 67, 0.6);
    --ios-label-3:   rgba(60, 60, 67, 0.3);
    --ios-separator: rgba(60, 60, 67, 0.29);

    --ios-radius:    10px;
    --ios-radius-lg: 12px;

    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text",
                 "Helvetica Neue", Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;

    background: var(--ios-bg);
    padding-bottom: 40px;
}

/* ═══ HEADER DESKTOP ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.page-header__left h2 {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.2;
}
.page-header__left p {
    font-size: 13.5px;
    color: var(--muted);
    margin: 4px 0 0;
}
.page-header__left p strong {
    color: var(--text);
    font-weight: 700;
}
.page-header__actions {
    display: flex;
    gap: 10px;
    align-items: center;
}
.ios-select-desktop {
    padding: 10px 36px 10px 14px;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-size: 13.5px;
    background: #fff;
    font-family: inherit;
    cursor: pointer;
    min-width: 220px;
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath fill='%238A91A0' d='M6 8L2 4h8z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 10px;
    -webkit-appearance: none;
    appearance: none;
}
.ios-select-desktop:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 3px var(--rust-soft);
}

/* ═══ LARGE TITLE (mobile) ═══ */
.ios-large-title,
.ios-subtitle { display: none; }

/* ═══ SEGMENTED CONTROL ═══ */
.ios-segmented {
    display: flex;
    gap: 0;
    padding: 3px;
    background: var(--ios-gray-5);
    border-radius: 12px;
    margin-bottom: 22px;
    overflow-x: auto;
    scrollbar-width: none;
    max-width: 640px;
}
.ios-segmented::-webkit-scrollbar { display: none; }

.ios-segmented__btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 9px 16px;
    border-radius: 9px;
    font-size: 13.5px;
    font-weight: 500;
    color: var(--ios-label);
    background: transparent;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: all 0.15s;
    white-space: nowrap;
    letter-spacing: -0.1px;
}
.ios-segmented__btn.is-active {
    background: var(--ios-card);
    font-weight: 600;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
}
.ios-segmented__count {
    font-size: 11px;
    font-weight: 600;
    color: var(--ios-label-2);
    padding: 1px 7px;
    background: rgba(120, 120, 128, 0.16);
    border-radius: 100px;
    min-width: 20px;
    text-align: center;
}
.ios-segmented__btn.is-active .ios-segmented__count {
    color: var(--ios-label);
}

/* ═══ SECTION HEADER ═══ */
.ios-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--ios-label-2);
    margin: 24px 0 10px;
    padding: 0 4px;
}
.ios-section-header:first-of-type { margin-top: 0; }
.ios-section-header__count {
    font-size: 12px;
    font-weight: 500;
    background: var(--ios-gray-5);
    color: var(--ios-label-2);
    padding: 2px 8px;
    border-radius: 100px;
    text-transform: none;
    letter-spacing: 0;
}

/* ═══ TIMELINE ═══ */
.ios-timeline {
    position: relative;
    padding-left: 0;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.ios-timeline-item {
    display: flex;
    gap: 14px;
    position: relative;
}
.ios-timeline-item[hidden] { display: none; }

/* Dot vertical */
.ios-timeline-dot {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #fff;
    font-size: 14px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    margin-top: 16px;
    z-index: 2;
}
.ios-timeline-dot--proposition { background: var(--ios-orange); }
.ios-timeline-dot--rendezvous  { background: var(--ios-blue); }
.ios-timeline-dot--evaluation  { background: var(--ios-green); }
.ios-timeline-dot--default     { background: var(--ios-gray); }

.ios-timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 17.5px;
    top: 52px;
    bottom: -16px;
    width: 1px;
    background: var(--ios-separator);
    z-index: 1;
}

/* Carte */
.ios-timeline-card {
    flex: 1;
    min-width: 0;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    padding: 18px 20px;
    transition: transform 0.15s, box-shadow 0.15s;
}
.ios-timeline-card:hover {
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
    transform: translateY(-1px);
}
.ios-timeline-card:active { transform: scale(0.99); }

.ios-timeline-card__head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}
.ios-timeline-card__title {
    font-size: 15.5px;
    font-weight: 600;
    color: var(--ios-label);
    letter-spacing: -0.2px;
    line-height: 1.3;
    flex: 1;
    min-width: 0;
}

/* Badge */
.ios-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 11px;
    border-radius: 100px;
    font-size: 10.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
    flex-shrink: 0;
}
.ios-badge--default { background: var(--ios-gray-5); color: var(--ios-label-2); }
.ios-badge--success { background: rgba(52, 199, 89, 0.15); color: #007A33; }
.ios-badge--warning { background: rgba(255, 149, 0, 0.15); color: #C93400; }
.ios-badge--info    { background: rgba(0, 122, 255, 0.15); color: #0040DD; }
.ios-badge--danger  { background: rgba(255, 59, 48, 0.15); color: #D70015; }

/* Description */
.ios-timeline-card__desc {
    font-size: 14px;
    line-height: 1.55;
    color: var(--ios-label-2);
    margin: 0 0 12px;
    letter-spacing: -0.1px;
}

/* Galerie */
.ios-timeline-gallery {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 6px;
    margin-bottom: 12px;
    max-width: 400px;
}
.ios-timeline-gallery__item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    background: var(--ios-gray-6);
    cursor: pointer;
    transition: transform 0.15s;
}
.ios-timeline-gallery__item:hover { transform: scale(1.03); }
.ios-timeline-gallery__item:active { opacity: 0.7; }
.ios-timeline-gallery__item img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.ios-timeline-gallery__more {
    aspect-ratio: 1;
    border-radius: 10px;
    background: var(--ios-label);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 600;
}

/* Détails */
.ios-timeline-details {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px 20px;
    padding: 12px 16px;
    background: var(--ios-gray-6);
    border-radius: 10px;
    margin-bottom: 12px;
}
.ios-timeline-details__row {
    display: flex;
    justify-content: space-between;
    gap: 8px;
    font-size: 13px;
    min-width: 0;
}
.ios-timeline-details__label {
    color: var(--ios-label-3);
    font-weight: 500;
    white-space: nowrap;
}
.ios-timeline-details__value {
    color: var(--ios-label);
    font-weight: 500;
    text-align: right;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Footer : meta + actions côte à côte sur desktop */
.ios-timeline-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    padding-top: 12px;
    margin-top: 4px;
    border-top: 0.5px solid var(--ios-separator);
}

/* Meta */
.ios-timeline-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 13px;
    color: var(--ios-label-2);
    flex-wrap: wrap;
}
.ios-timeline-meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.ios-timeline-meta i {
    font-size: 11.5px;
    opacity: 0.7;
}
.ios-timeline-meta__type {
    padding: 3px 10px;
    background: var(--ios-gray-6);
    border-radius: 100px;
    font-size: 11.5px;
    font-weight: 500;
    color: var(--ios-label-2);
}

/* Actions */
.ios-timeline-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

/* ═══ BOUTONS iOS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 12px 18px;
    border-radius: var(--ios-radius-lg);
    font-size: 15px;
    font-weight: 600;
    font-family: inherit;
    letter-spacing: -0.2px;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: opacity 0.1s, transform 0.1s;
    white-space: nowrap;
    min-height: 44px;
}
.ios-btn:active { transform: scale(0.98); opacity: 0.85; }

.ios-btn--primary { background: #B4532A; color: #fff; }
.ios-btn--primary:hover { background: #9A4523; }
.ios-btn--gray    { background: var(--ios-gray-5); color: var(--ios-blue); }
.ios-btn--gray:hover { background: #D8D8DD; }
.ios-btn--ghost   { background: transparent; color: var(--ios-blue); border: 1px solid var(--border); }
.ios-btn--full    { width: 100%; }
.ios-btn--sm      { padding: 8px 14px; font-size: 13px; min-height: 40px; }

/* ═══ EMPTY STATE ═══ */
.ios-empty {
    text-align: center;
    padding: 70px 20px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    margin-top: 20px;
}
.ios-empty--compact {
    padding: 36px 20px;
    margin-top: 0;
    margin-bottom: 16px;
}
.ios-empty__icon {
    font-size: 52px;
    color: var(--ios-gray-3);
    margin-bottom: 16px;
}
.ios-empty__title {
    font-size: 18px;
    font-weight: 600;
    color: var(--ios-label);
    margin: 0 0 8px;
    letter-spacing: -0.3px;
}
.ios-empty__text {
    font-size: 14px;
    color: var(--ios-label-2);
    margin: 0 auto 22px;
    max-width: 360px;
    line-height: 1.55;
    letter-spacing: -0.1px;
}

/* ═══ PAGINATION ═══ */
.ios-pagination-wrapper {
    margin-top: 28px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
}
.ios-pagination-stats {
    font-size: 13px;
    color: var(--ios-label-2);
    text-align: center;
    margin: 0;
    letter-spacing: -0.1px;
}
.ios-pagination-stats strong {
    color: var(--ios-label);
    font-weight: 600;
}

.ios-pagination {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 5px;
}
.ios-pagination .pagination,
.ios-pagination nav {
    display: flex;
    gap: 5px;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
}
.ios-pagination a,
.ios-pagination span:not(.sr-only) {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid var(--ios-separator);
    color: var(--ios-label-2);
    text-decoration: none;
    font-size: 13px;
    transition: all 0.2s;
    min-width: 36px;
    text-align: center;
    background: var(--ios-card);
    letter-spacing: -0.1px;
}
.ios-pagination a:hover {
    background: var(--ios-gray-6);
    color: var(--ios-blue);
}
.ios-pagination .active span,
.ios-pagination [aria-current="page"] span,
.ios-pagination [aria-current="page"] {
    background: #B4532A;
    color: #fff;
    border-color: #B4532A;
}
.ios-pagination .disabled span {
    opacity: 0.4;
    cursor: not-allowed;
}

.ios-per-page {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--ios-label-2);
    padding-top: 14px;
    border-top: 1px solid var(--ios-separator);
    flex-wrap: wrap;
    justify-content: center;
    width: 100%;
}
.ios-per-page__select {
    padding: 7px 30px 7px 12px;
    border: 1px solid var(--ios-separator);
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    color: var(--ios-label);
    background: var(--ios-card);
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 10px center;
    cursor: pointer;
    -webkit-appearance: none;
    appearance: none;
}
.ios-per-page__select:focus {
    outline: none;
    border-color: var(--ios-blue);
    box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
}

/* ═══ LIGHTBOX ═══ */
.ios-lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: #000;
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.ios-lightbox.is-open { display: flex; }
.ios-lightbox__img {
    max-width: 100vw;
    max-height: 90vh;
    object-fit: contain;
}
.ios-lightbox__close {
    position: absolute;
    top: 20px; right: 20px;
    width: 36px; height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #fff;
    border: none;
    font-size: 17px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}
.ios-lightbox__close:active { opacity: 0.6; }

/* ═══════════════════════════════════════════════════════════
   DESKTOP : conteneur large
   ═══════════════════════════════════════════════════════════ */
@media (min-width: 1000px) {
    .ios-historique-page {
        max-width: 1100px;
        margin: 0 auto;
        padding: 8px 0 60px;
    }
}

@media (max-width: 999px) and (min-width: 821px) {
    .ios-historique-page {
        max-width: 900px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .ios-historique-page {
        margin: 0 -16px;
        padding: 0 16px 40px;
        max-width: none;
    }

    /* HEADER desktop masqué */
    .page-header { display: none; }

    /* LARGE TITLE iOS */
    .ios-large-title {
        display: block;
        font-size: 34px;
        font-weight: 700;
        letter-spacing: -0.6px;
        color: var(--ios-label);
        margin: 0 0 4px;
        padding: 0 4px;
        line-height: 1.15;
    }
    .ios-subtitle {
        display: block;
        font-size: 15px;
        color: var(--ios-label-3);
        margin: 0 0 20px;
        padding: 0 4px;
        letter-spacing: -0.2px;
    }

    /* Segmented : scroll horizontal */
    .ios-segmented {
        max-width: none;
        margin-bottom: 20px;
    }
    .ios-segmented__btn {
        padding: 8px 12px;
        font-size: 13px;
    }

    /* Timeline mobile : dot + carte serrés */
    .ios-timeline {
        gap: 12px;
    }
    .ios-timeline-item {
        gap: 10px;
    }
    .ios-timeline-dot {
        width: 32px;
        height: 32px;
        font-size: 13px;
        margin-top: 12px;
    }
    .ios-timeline-item:not(:last-child)::before {
        left: 15.5px;
        top: 46px;
        bottom: -12px;
    }

    .ios-timeline-card {
        padding: 16px;
    }

    .ios-timeline-gallery {
        grid-template-columns: repeat(3, 1fr);
        max-width: none;
    }

    .ios-timeline-details {
        grid-template-columns: 1fr;
        gap: 6px;
    }

    /* Footer en colonne sur mobile */
    .ios-timeline-footer {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }

    .ios-timeline-actions {
        flex-direction: column;
        gap: 8px;
    }
    .ios-timeline-actions .ios-btn {
        width: 100%;
    }

    .ios-btn {
        min-height: 46px;
    }
}

@media (max-width: 480px) {
    .ios-large-title { font-size: 30px; }
    .ios-subtitle { font-size: 14px; }

    .ios-segmented__btn { font-size: 12px; padding: 7px 10px; }
    .ios-segmented__count { font-size: 10px; }

    .ios-timeline-card { padding: 14px; }
    .ios-timeline-card__title { font-size: 14px; }
    .ios-timeline-card__desc { font-size: 13px; }

    .ios-timeline-gallery { grid-template-columns: repeat(2, 1fr); }

    .ios-timeline-dot { width: 28px; height: 28px; font-size: 12px; }
    .ios-timeline-item:not(:last-child)::before { left: 13.5px; top: 42px; }

    .ios-btn { font-size: 14px; padding: 11px 16px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush