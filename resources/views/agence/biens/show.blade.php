@extends('layouts.dashboard-agence')

@section('title', 'Détail du bien — DoyaImmo')
@section('page_title', 'Détail du bien')
@section('page_sub', 'Informations complètes sur votre bien')

@section('content')
<div class="view active bien-show">

    @php
        $images     = $bien->medias->where('type_media', 'image')->values();
        $videos     = $bien->medias->where('type_media', 'video')->values();
        $isDispo    = (bool) $bien->statut;
        $isVedette  = (bool) $bien->est_vedette;

        $heroImage = $images->first()
            ? asset('storage/' . $images->first()->fichier)
            : null;

        $lightboxImages = $images->map(fn($m) => asset('storage/' . $m->fichier))->values()->all();

        // Équipements supplémentaires
        $extraEquip = collect();
        if ($bien->climatisation) $extraEquip->push(['icon' => 'fa-snowflake', 'label' => 'Climatisation']);
        if ($bien->balcon)        $extraEquip->push(['icon' => 'fa-door-open', 'label' => 'Balcon']);
        if ($bien->jardin)        $extraEquip->push(['icon' => 'fa-tree', 'label' => 'Jardin']);
        if ($bien->piscine)       $extraEquip->push(['icon' => 'fa-water', 'label' => 'Piscine']);
        if ($bien->ascenseur)     $extraEquip->push(['icon' => 'fa-elevator', 'label' => 'Ascenseur']);
        if ($bien->securite)      $extraEquip->push(['icon' => 'fa-shield-halved', 'label' => 'Sécurité 24h/24']);
    @endphp

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="bien-show__back">
        <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes biens
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         HERO VISUEL
    ═══════════════════════════════════════════ --}}
    <header class="bien-hero {{ $isVedette ? 'is-vedette' : '' }}">

        {{-- Image hero --}}
        <div class="bien-hero__media"
             @if($heroImage) onclick="openLightbox(0)" @endif>
            @if($heroImage)
                <img src="{{ $heroImage }}" alt="{{ $bien->titre }}">
            @else
                <div class="bien-hero__placeholder">
                    <i class="fa-regular fa-image"></i>
                </div>
            @endif

            {{-- Badges --}}
            @if($isVedette)
                <span class="hero-badge hero-badge--gold">
                    <i class="fa-solid fa-star"></i> Vedette
                </span>
            @endif

            <span class="hero-badge hero-badge--status {{ $isDispo ? 'is-dispo' : 'is-indispo' }}">
                <i class="fa-solid fa-circle"></i>
                {{ $isDispo ? 'Disponible' : 'Indisponible' }}
            </span>

            {{-- Compteur médias --}}
            @if($images->count() > 1 || $videos->count() > 0)
                <span class="hero-badge hero-badge--count">
                    @if($images->count() > 0)
                        <i class="fa-regular fa-image"></i> {{ $images->count() }}
                    @endif
                    @if($videos->count() > 0)
                        <i class="fa-regular fa-circle-play"></i> {{ $videos->count() }}
                    @endif
                </span>
            @endif

            {{-- Overlay avec infos --}}
            <div class="bien-hero__overlay">
                <h1 class="bien-hero__title">{{ $bien->titre }}</h1>

                <div class="bien-hero__meta">
                    <span>
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $bien->quartier }}
                    </span>
                    <span class="bien-hero__sep">•</span>
                    <span>
                        <i class="fa-regular fa-square"></i>
                        {{ $bien->surface }} m²
                    </span>
                    @if($bien->nombre_chambres)
                        <span class="bien-hero__sep">•</span>
                        <span>
                            <i class="fa-solid fa-bed"></i>
                            {{ $bien->nombre_chambres }} ch.
                        </span>
                    @endif
                </div>

                <div class="bien-hero__price">
                    {{ number_format($bien->prix, 0, ',', ' ') }}
                    <small>FCFA</small>
                </div>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════
         STATS RAPIDES
    ═══════════════════════════════════════════ --}}
    <section class="quick-stats">
        <div class="quick-stat">
            <div class="quick-stat__icon">
                <i class="fa-regular fa-eye"></i>
            </div>
            <div class="quick-stat__content">
                <span class="quick-stat__value">{{ $bien->vues ?? 0 }}</span>
                <span class="quick-stat__label">vues</span>
            </div>
        </div>

        <div class="quick-stat">
            <div class="quick-stat__icon">
                <i class="fa-regular fa-calendar"></i>
            </div>
            <div class="quick-stat__content">
                <span class="quick-stat__value">{{ $bien->created_at->format('d/m/Y') }}</span>
                <span class="quick-stat__label">publié le</span>
            </div>
        </div>

        @if($isVedette && $bien->vedette_fin)
            <div class="quick-stat quick-stat--gold">
                <div class="quick-stat__icon">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="quick-stat__content">
                    <span class="quick-stat__value">{{ $bien->vedette_jours_restants ?? 0 }} jour(s)</span>
                    <span class="quick-stat__label">vedette restante</span>
                </div>
            </div>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════
         GALERIE
    ═══════════════════════════════════════════ --}}
    @if($images->count() > 0 || $videos->count() > 0)
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-images"></i>
                </span>
                <h4>Galerie</h4>
                <span class="panel__count">
                    {{ $images->count() }} photo{{ $images->count() > 1 ? 's' : '' }}
                    @if($videos->count() > 0)
                        · {{ $videos->count() }} vidéo{{ $videos->count() > 1 ? 's' : '' }}
                    @endif
                </span>
            </header>

            @if($images->count() > 0)
                <div class="gallery-grid">
                    @foreach($images as $index => $media)
                        @php $url = asset('storage/' . $media->fichier); @endphp
                        <div class="gallery-item {{ $index === 0 ? 'gallery-item--hero' : '' }}"
                             onclick="openLightbox({{ $index }})">
                            <img src="{{ $url }}" alt="Photo du bien" loading="lazy">
                            <div class="gallery-item__overlay">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($videos->count() > 0)
                <div class="video-section">
                    <h5 class="video-section__title">
                        <i class="fa-regular fa-circle-play"></i>
                        Vidéos
                    </h5>
                    <div class="video-grid">
                        @foreach($videos as $media)
                            <div class="video-tile">
                                <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata">
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         CARACTÉRISTIQUES
    ═══════════════════════════════════════════ --}}
    <section class="panel">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-regular fa-house"></i>
            </span>
            <h4>Caractéristiques</h4>
        </header>

        <div class="info-grid">
            <div class="info-grid__item">
                <span class="info-grid__label">Type</span>
                <span class="info-grid__value">{{ $bien->type_bien->label() }}</span>
            </div>
            <div class="info-grid__item">
                <span class="info-grid__label">Contrat</span>
                <span class="info-grid__value">{{ $bien->type_contrat->label() }}</span>
            </div>
            <div class="info-grid__item info-grid__item--accent">
                <span class="info-grid__label">Prix</span>
                <span class="info-grid__value info-grid__value--prix">
                    {{ number_format($bien->prix, 0, ',', ' ') }}
                    <small>FCFA</small>
                </span>
            </div>
            <div class="info-grid__item">
                <span class="info-grid__label">Surface</span>
                <span class="info-grid__value">{{ $bien->surface }} m²</span>
            </div>
            @if($bien->nombre_chambres > 0)
                <div class="info-grid__item">
                    <span class="info-grid__label">Chambres</span>
                    <span class="info-grid__value">{{ $bien->nombre_chambres }}</span>
                </div>
            @endif
            @if($bien->nombre_salles_bain > 0)
                <div class="info-grid__item">
                    <span class="info-grid__label">Salles de bain</span>
                    <span class="info-grid__value">{{ $bien->nombre_salles_bain }}</span>
                </div>
            @endif
            <div class="info-grid__item">
                <span class="info-grid__label">Parking</span>
                <span class="info-grid__value">
                    <i class="fa-solid {{ $bien->parking_disponible ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                    {{ $bien->parking_disponible ? 'Disponible' : 'Non' }}
                </span>
            </div>
            <div class="info-grid__item">
                <span class="info-grid__label">Meublé</span>
                <span class="info-grid__value">
                    <i class="fa-solid {{ $bien->est_meuble ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                    {{ $bien->est_meuble ? 'Oui' : 'Non' }}
                </span>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         ÉQUIPEMENTS
    ═══════════════════════════════════════════ --}}
    @php $equipements = $bien->equipements ?? []; @endphp
    @if(count($equipements) > 0 || $extraEquip->count() > 0)
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-solid fa-cogs"></i>
                </span>
                <h4>Équipements</h4>
                <span class="panel__count">
                    {{ count($equipements) + $extraEquip->count() }}
                </span>
            </header>

            <div class="equipements-grid">
                @foreach($equipements as $equipement)
                    <span class="equipement-pill">
                        <i class="fa-solid fa-check"></i>
                        {{ $equipement }}
                    </span>
                @endforeach

                @foreach($extraEquip as $eq)
                    <span class="equipement-pill">
                        <i class="fa-solid {{ $eq['icon'] }}"></i>
                        {{ $eq['label'] }}
                    </span>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         DESCRIPTION
    ═══════════════════════════════════════════ --}}
    @if($bien->description)
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-solid fa-align-left"></i>
                </span>
                <h4>Description</h4>
            </header>
            <blockquote class="quote-block">
                {{ $bien->description }}
            </blockquote>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         VEDETTE
    ═══════════════════════════════════════════ --}}
    @if($isVedette)
        <section class="panel panel--vedette">
            <header class="panel__head">
                <span class="panel__icon panel__icon--gold">
                    <i class="fa-solid fa-star"></i>
                </span>
                <h4>Statut vedette</h4>
            </header>

            <div class="vedette-info">
                <div class="vedette-info__icon">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="vedette-info__content">
                    <strong>
                        En vedette jusqu'au
                        {{ $bien->vedette_fin ? $bien->vedette_fin->format('d/m/Y') : 'Illimité' }}
                    </strong>
                    @if(($bien->vedette_jours_restants ?? 0) > 0)
                        <span>{{ $bien->vedette_jours_restants }} jour(s) restant(s)</span>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         ACTIONS
    ═══════════════════════════════════════════ --}}
    <footer class="bien-actions">
        <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost">
            <i class="fa-solid fa-list"></i> Tous mes biens
        </a>

        <div class="bien-actions__main">
            <a href="{{ route('agence.biens.edit', $bien->slug) }}" class="btn btn-ghost">
                <i class="fa-solid fa-pen"></i> Modifier
            </a>

            <form action="{{ route('agence.biens.activer', $bien->slug) }}" method="POST">
                @csrf
                <button type="submit" class="btn {{ $isDispo ? 'btn-ghost-danger' : 'btn-success' }}"
                        onclick="return confirm('{{ $isDispo ? 'Masquer ce bien ?' : 'Publier ce bien ?' }}')">
                    <i class="fa-solid {{ $isDispo ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                    {{ $isDispo ? 'Masquer' : 'Publier' }}
                </button>
            </form>

            @if(!$isVedette)
                <a href="{{ route('agence.biens.vedette.demander', $bien->slug) }}" class="btn btn-gold">
                    <i class="fa-solid fa-star"></i> Demander vedette
                </a>
            @endif
        </div>
    </footer>
</div>

{{-- ═══════════════════════════════════════════
     LIGHTBOX avec navigation
═══════════════════════════════════════════ --}}
<div id="lightbox" class="lightbox" onclick="closeLightbox(event)">
    <button type="button" class="lightbox__close" onclick="event.stopPropagation();closeLightbox()" aria-label="Fermer">
        &times;
    </button>

    <button type="button" class="lightbox__nav lightbox__nav--prev"
            onclick="event.stopPropagation();navigateLightbox(-1)" aria-label="Précédent">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <figure class="lightbox__figure" onclick="event.stopPropagation()">
        <img id="lightboxImage" src="" alt="Agrandir">
        <figcaption id="lightboxCounter" class="lightbox__counter"></figcaption>
    </figure>

    <button type="button" class="lightbox__nav lightbox__nav--next"
            onclick="event.stopPropagation();navigateLightbox(1)" aria-label="Suivant">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
</div>
@endsection


@push('scripts')
<script>
    const LIGHTBOX_IMAGES = @json($lightboxImages);
    let currentIndex = 0;

    function openLightbox(index) {
        if (LIGHTBOX_IMAGES.length === 0) return;
        currentIndex = (typeof index === 'number' && index >= 0 && index < LIGHTBOX_IMAGES.length) ? index : 0;
        renderLightbox();
        document.getElementById('lightbox').classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox(event) {
        if (event && event.target !== event.currentTarget) return;
        document.getElementById('lightbox').classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function navigateLightbox(dir) {
        if (LIGHTBOX_IMAGES.length === 0) return;
        currentIndex = (currentIndex + dir + LIGHTBOX_IMAGES.length) % LIGHTBOX_IMAGES.length;
        renderLightbox();
    }

    function renderLightbox() {
        const img     = document.getElementById('lightboxImage');
        const counter = document.getElementById('lightboxCounter');
        const prev    = document.querySelector('.lightbox__nav--prev');
        const next    = document.querySelector('.lightbox__nav--next');

        img.src = LIGHTBOX_IMAGES[currentIndex] || '';

        counter.textContent = LIGHTBOX_IMAGES.length > 1
            ? (currentIndex + 1) + ' / ' + LIGHTBOX_IMAGES.length
            : '';

        const hide = LIGHTBOX_IMAGES.length <= 1;
        prev.style.display = hide ? 'none' : 'flex';
        next.style.display = hide ? 'none' : 'flex';
    }

    document.addEventListener('keydown', function (e) {
        const lb = document.getElementById('lightbox');
        if (!lb.classList.contains('is-open')) return;
        if (e.key === 'Escape')     closeLightbox();
        if (e.key === 'ArrowLeft')  navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });

    // Swipe tactile
    let touchStartX = 0;
    document.getElementById('lightboxImage')?.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    document.getElementById('lightboxImage')?.addEventListener('touchend', e => {
        const diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) navigateLightbox(1);
            else navigateLightbox(-1);
        }
    }, { passive: true });
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL BIEN — Agence
   ═══════════════════════════════════════════════════════════ */

.bien-show {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #F5A623;
    --c-gold-bg:    #FFF8E1;
    --surface:      #F7F9FC;
    --radius:       14px;
}

.bien-show__back { margin-bottom: 16px; }

/* ═══ HERO ═══ */
.bien-hero {
    position: relative;
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 14px;
    border: 1px solid var(--border);
    background: var(--surface);
}
.bien-hero.is-vedette {
    border-color: var(--c-gold);
    box-shadow: 0 0 0 1px rgba(245, 166, 35, .25);
}

.bien-hero__media {
    position: relative;
    aspect-ratio: 21 / 9;
    cursor: zoom-in;
    overflow: hidden;
}
.bien-hero__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
}
.bien-hero__media:hover img { transform: scale(1.03); }

.bien-hero__placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted);
    font-size: 64px;
    opacity: .3;
}

/* Badges hero */
.hero-badge {
    position: absolute;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    z-index: 2;
    backdrop-filter: blur(6px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
}
.hero-badge i { font-size: 10px; }

.hero-badge--gold {
    top: 16px;
    left: 16px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
}
.hero-badge--status {
    top: 16px;
    right: 16px;
    background: rgba(255, 255, 255, .95);
}
.hero-badge--status i { font-size: 6px; }
.hero-badge--status.is-dispo { color: var(--c-success); }
.hero-badge--status.is-indispo { color: var(--c-danger); }

.hero-badge--count {
    top: 16px;
    right: 16px;
    margin-top: 0;
}
.hero-badge--status + .hero-badge--count {
    top: 56px;
    background: rgba(0, 0, 0, .65);
    color: #fff;
}
.hero-badge--count i { font-size: 11px; }

/* Overlay infos */
.bien-hero__overlay {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 60px 26px 22px;
    background: linear-gradient(to top, rgba(0, 0, 0, .85) 0%, rgba(0, 0, 0, .5) 60%, transparent 100%);
    color: #fff;
    z-index: 1;
}

.bien-hero__title {
    font-family: var(--display);
    font-size: 26px;
    font-weight: 800;
    margin: 0 0 10px;
    color: #fff;
    line-height: 1.2;
    text-shadow: 0 2px 12px rgba(0, 0, 0, .4);
}

.bien-hero__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13.5px;
    color: rgba(255, 255, 255, .9);
    margin-bottom: 14px;
}
.bien-hero__meta i {
    margin-right: 5px;
    font-size: 12px;
    opacity: .85;
}
.bien-hero__sep { opacity: .5; }

.bien-hero__price {
    display: inline-block;
    padding: 8px 18px;
    background: rgba(255, 255, 255, .15);
    backdrop-filter: blur(8px);
    border-radius: 12px;
    font-family: var(--display);
    font-size: 22px;
    font-weight: 800;
    color: #fff;
    border: 1px solid rgba(255, 255, 255, .25);
}
.bien-hero__price small {
    font-size: 13px;
    font-weight: 600;
    opacity: .85;
    margin-left: 3px;
}

/* ═══ STATS RAPIDES ═══ */
.quick-stats {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.quick-stat {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    flex: 1;
    min-width: 140px;
}
.quick-stat--gold {
    background: linear-gradient(135deg, #FFFBF0, var(--c-gold-bg));
    border-color: #FFE0B2;
}
.quick-stat__icon {
    width: 36px; height: 36px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    background: var(--surface);
    color: var(--rust);
    font-size: 15px;
    flex-shrink: 0;
}
.quick-stat--gold .quick-stat__icon {
    background: rgba(245, 166, 35, .15);
    color: var(--c-gold);
}
.quick-stat__content {
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 0;
}
.quick-stat__value {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 800;
    color: var(--text);
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.quick-stat--gold .quick-stat__value { color: var(--c-warning); }
.quick-stat__label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}

/* ═══ PANNEAUX ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 22px;
    margin-bottom: 14px;
}
.panel--vedette {
    background: linear-gradient(135deg, #FFFBF0 0%, #fff 60%);
    border-color: #FFE0B2;
}

.panel__head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}
.panel__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px; height: 32px;
    border-radius: 9px;
    background: var(--c-warning-bg);
    color: var(--rust);
    font-size: 14px;
    flex-shrink: 0;
}
.panel__icon--gold {
    background: var(--c-gold-bg);
    color: var(--c-gold);
}
.panel__head h4 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    flex: 1;
}
.panel__count {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: var(--c-warning-bg);
    color: var(--rust);
    border-radius: 999px;
}

/* ═══ GALERIE ═══ */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
}
.gallery-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    background: var(--surface);
    border: 1px solid var(--border);
    transition: transform .2s ease, box-shadow .2s ease;
}
.gallery-item:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 16px rgba(0, 0, 0, .1);
}
.gallery-item--hero {
    grid-column: span 2;
    grid-row: span 2;
    aspect-ratio: auto;
}
.gallery-item img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.gallery-item__overlay {
    position: absolute;
    inset: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(0, 0, 0, .4);
    color: #fff;
    font-size: 20px;
    opacity: 0;
    transition: opacity .2s;
}
.gallery-item:hover .gallery-item__overlay { opacity: 1; }

/* Vidéos */
.video-section { margin-top: 14px; }
.video-section__title {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--muted);
    margin: 0 0 10px;
}
.video-section__title i { color: var(--rust); font-size: 13px; }

.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 10px;
}
.video-tile {
    border-radius: 10px;
    overflow: hidden;
    background: #000;
    border: 1px solid var(--border);
}
.video-tile video {
    width: 100%;
    aspect-ratio: 16 / 9;
    object-fit: cover;
    display: block;
}

/* ═══ GRILLE INFOS ═══ */
.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.info-grid__item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 10px 14px;
    background: var(--surface);
    border-radius: 8px;
    border: 1px solid transparent;
    transition: border-color .2s;
}
.info-grid__item:hover { border-color: var(--border); }
.info-grid__item--accent {
    background: var(--c-warning-bg);
    border-color: #FFE0B2;
}
.info-grid__label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}
.info-grid__value {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text);
}
.info-grid__value--prix {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 800;
    color: var(--rust);
}
.info-grid__value--prix small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}
.info-grid__value .is-yes { color: var(--c-success); margin-right: 4px; }
.info-grid__value .is-no  { color: var(--c-danger);  margin-right: 4px; }

/* ═══ ÉQUIPEMENTS ═══ */
.equipements-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.equipement-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 13px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border: 1px solid #BBDEFB;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}
.equipement-pill i { font-size: 11px; }

/* ═══ DESCRIPTION ═══ */
.quote-block {
    position: relative;
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    line-height: 1.75;
    color: var(--text);
    margin: 0;
    border-left: 4px solid var(--rust);
    background: linear-gradient(135deg, rgba(180, 83, 42, .05), rgba(180, 83, 42, .02));
    font-style: italic;
}

/* ═══ VEDETTE ═══ */
.vedette-info {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    background: var(--c-gold-bg);
    border-radius: 12px;
    border: 1px solid #FFE0B2;
}
.vedette-info__icon {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(245, 166, 35, .3);
}
.vedette-info__content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.vedette-info__content strong {
    font-size: 14px;
    font-weight: 700;
    color: var(--c-warning);
}
.vedette-info__content span {
    font-size: 12px;
    color: var(--c-warning);
    opacity: .85;
}

/* ═══ ACTIONS ═══ */
.bien-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    flex-wrap: wrap;
}
.bien-actions__main {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-left: auto;
}

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust); color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523; color: #fff;
    border-color: #9A4523;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(180, 83, 42, .25);
}
.btn-ghost {
    background: transparent;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: var(--surface);
    border-color: var(--rust);
    color: var(--rust);
}
.btn-ghost-danger {
    background: transparent;
    color: var(--c-danger);
    border-color: #FFCDD2;
}
.btn-ghost-danger:hover {
    background: var(--c-danger-bg);
    border-color: var(--c-danger);
}
.btn-success {
    background: var(--c-success);
    color: #fff;
    border-color: var(--c-success);
}
.btn-success:hover {
    background: #156A3B;
    color: #fff;
    border-color: #156A3B;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(30, 122, 71, .25);
}
.btn-gold {
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    border-color: var(--c-gold);
}
.btn-gold:hover {
    background: linear-gradient(135deg, #E8901A, #D57E15);
    color: #fff;
    border-color: #E8901A;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(245, 166, 35, .3);
}
.btn-sm { padding: 7px 14px; font-size: 12px; }

/* ═══ LIGHTBOX ═══ */
.lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .95);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: fadeIn .2s ease;
}
.lightbox.is-open { display: flex; }

.lightbox__figure {
    margin: 0;
    max-width: 92vw;
    max-height: 92vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}
.lightbox__figure img {
    max-width: 92vw;
    max-height: 82vh;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .5);
}
.lightbox__counter {
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: .5px;
    padding: 5px 14px;
    background: rgba(255, 255, 255, .12);
    border-radius: 999px;
}

.lightbox__close {
    position: absolute;
    top: 20px; right: 24px;
    width: 42px; height: 42px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .12);
    color: #fff;
    border: none;
    font-size: 26px;
    line-height: 1;
    cursor: pointer;
    transition: background .2s, transform .2s;
    display: flex; align-items: center; justify-content: center;
    font-family: sans-serif;
    z-index: 2;
}
.lightbox__close:hover {
    background: rgba(255, 255, 255, .25);
    transform: scale(1.05);
}

.lightbox__nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 52px; height: 52px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .12);
    color: #fff;
    border: none;
    font-size: 20px;
    cursor: pointer;
    transition: background .2s, transform .2s;
    display: flex; align-items: center; justify-content: center;
    z-index: 2;
}
.lightbox__nav:hover {
    background: rgba(255, 255, 255, .28);
    transform: translateY(-50%) scale(1.06);
}
.lightbox__nav--prev { left: 24px; }
.lightbox__nav--next { right: 24px; }

@keyframes fadeIn {
    from { opacity: 0; }
    to   { opacity: 1; }
}

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .info-grid { grid-template-columns: repeat(2, 1fr); }
    .gallery-grid { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 768px) {
    .bien-hero__media { aspect-ratio: 16 / 9; }
    .bien-hero__title { font-size: 20px; }
    .bien-hero__price { font-size: 18px; padding: 6px 14px; }
    .bien-hero__overlay { padding: 50px 18px 16px; }

    .quick-stat { padding: 8px 12px; min-width: 0; }
    .quick-stat__icon { width: 32px; height: 32px; font-size: 13px; }
    .quick-stat__value { font-size: 14px; }

    .panel { padding: 16px; }

    .gallery-grid { grid-template-columns: repeat(3, 1fr); }
    .gallery-item--hero { grid-column: span 2; grid-row: span 2; }

    .video-grid { grid-template-columns: 1fr; }

    .info-grid { grid-template-columns: 1fr; }

    .bien-actions {
        flex-direction: column;
        align-items: stretch;
    }
    .bien-actions .btn,
    .bien-actions form { width: 100%; }
    .bien-actions form .btn { width: 100%; justify-content: center; }
    .bien-actions__main { margin-left: 0; flex-direction: column; align-items: stretch; width: 100%; }
    .bien-actions__main .btn { width: 100%; justify-content: center; }

    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 12px; right: 12px; width: 38px; height: 38px; font-size: 22px; }
}

@media (max-width: 480px) {
    .bien-hero__media { aspect-ratio: 4 / 3; }
    .bien-hero__title { font-size: 17px; }
    .bien-hero__meta { font-size: 12px; gap: 6px; }
    .bien-hero__price { font-size: 16px; padding: 5px 12px; }
    .bien-hero__overlay { padding: 40px 14px 12px; }
    .hero-badge { font-size: 10px; padding: 4px 10px; }
    .hero-badge--gold { top: 12px; left: 12px; }
    .hero-badge--status { top: 12px; right: 12px; }

    .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 6px; }
    .gallery-item--hero { grid-column: span 2; grid-row: span 2; }

    .panel { padding: 14px; }
    .panel__head h4 { font-size: 14px; }

    .quote-block { padding: 12px 14px; font-size: 13px; }

    .vedette-info { padding: 12px 14px; gap: 10px; }
    .vedette-info__icon { width: 36px; height: 36px; font-size: 15px; }
    .vedette-info__content strong { font-size: 13px; }
    .vedette-info__content span { font-size: 11.5px; }

    .btn { font-size: 12px; padding: 8px 14px; }
}
</style>
@endpush