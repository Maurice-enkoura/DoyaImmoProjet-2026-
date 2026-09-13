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

        $extraEquip = collect();
        if ($bien->climatisation) $extraEquip->push(['icon' => 'fa-snowflake', 'label' => 'Climatisation']);
        if ($bien->balcon)        $extraEquip->push(['icon' => 'fa-door-open', 'label' => 'Balcon']);
        if ($bien->jardin)        $extraEquip->push(['icon' => 'fa-tree', 'label' => 'Jardin']);
        if ($bien->piscine)       $extraEquip->push(['icon' => 'fa-water', 'label' => 'Piscine']);
        if ($bien->ascenseur)     $extraEquip->push(['icon' => 'fa-elevator', 'label' => 'Ascenseur']);
        if ($bien->securite)      $extraEquip->push(['icon' => 'fa-shield-halved', 'label' => 'Sécurité 24h/24']);
    @endphp

    {{-- ═══ HERO VISUEL ═══ --}}
    <header class="bien-hero {{ $isVedette ? 'is-vedette' : '' }}">

        <div class="bien-hero__media"
             @if($heroImage) onclick="openLightbox(0)" @endif>
            @if($heroImage)
                <img src="{{ $heroImage }}" alt="{{ $bien->titre }}">
            @else
                <div class="bien-hero__placeholder">
                    <i class="fa-regular fa-image"></i>
                </div>
            @endif

            @if($isVedette)
                <span class="hero-badge hero-badge--gold">
                    <i class="fa-solid fa-star"></i> Vedette
                </span>
            @endif

            <span class="hero-badge hero-badge--status {{ $isDispo ? 'is-dispo' : 'is-indispo' }}">
                <i class="fa-solid fa-circle"></i>
                {{ $isDispo ? 'Disponible' : 'Indisponible' }}
            </span>

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

    {{-- ═══ STATS RAPIDES ═══ --}}
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
                    <span class="quick-stat__value">{{ $bien->vedette_jours_restants ?? 0 }} j</span>
                    <span class="quick-stat__label">vedette</span>
                </div>
            </div>
        @endif
    </section>

    {{-- ═══ GALERIE ═══ --}}
    @if($images->count() > 0 || $videos->count() > 0)
        <div class="ios-section-title">
            <i class="fa-regular fa-images"></i> Galerie
            <span class="ios-section-count">
                {{ $images->count() }}
                @if($videos->count() > 0)
                    · {{ $videos->count() }} vidéo
                @endif
            </span>
        </div>

        @if($images->count() > 0)
            <div class="gallery-grid">
                @foreach($images as $index => $media)
                    @php $url = asset('storage/' . $media->fichier); @endphp
                    <div class="gallery-item" onclick="openLightbox({{ $index }})">
                        <img src="{{ $url }}" alt="Photo du bien" loading="lazy">
                    </div>
                @endforeach
            </div>
        @endif

        @if($videos->count() > 0)
            <div class="video-section">
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
    @endif

    {{-- ═══ CARACTÉRISTIQUES ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-house"></i> Caractéristiques
    </div>

    <div class="ios-list">
        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Type</span>
            </div>
            <span class="ios-list__value">{{ $bien->type_bien->label() }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Contrat</span>
            </div>
            <span class="ios-list__value">{{ $bien->type_contrat->label() }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Prix</span>
            </div>
            <span class="ios-list__value ios-list__value--rust">
                {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
            </span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Surface</span>
            </div>
            <span class="ios-list__value">{{ $bien->surface }} m²</span>
        </div>

        @if($bien->nombre_chambres > 0)
            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Chambres</span>
                </div>
                <span class="ios-list__value">{{ $bien->nombre_chambres }}</span>
            </div>
        @endif

        @if($bien->nombre_salles_bain > 0)
            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Salles de bain</span>
                </div>
                <span class="ios-list__value">{{ $bien->nombre_salles_bain }}</span>
            </div>
        @endif

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Parking</span>
            </div>
            <span class="ios-list__value">
                <i class="fa-solid {{ $bien->parking_disponible ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                {{ $bien->parking_disponible ? 'Oui' : 'Non' }}
            </span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Meublé</span>
            </div>
            <span class="ios-list__value">
                <i class="fa-solid {{ $bien->est_meuble ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                {{ $bien->est_meuble ? 'Oui' : 'Non' }}
            </span>
        </div>
    </div>

    {{-- ═══ ÉQUIPEMENTS ═══ --}}
    @php $equipements = $bien->equipements ?? []; @endphp
    @if(count($equipements) > 0 || $extraEquip->count() > 0)
        <div class="ios-section-title">
            <i class="fa-solid fa-cogs"></i> Équipements
            <span class="ios-section-count">{{ count($equipements) + $extraEquip->count() }}</span>
        </div>

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
    @endif

    {{-- ═══ DESCRIPTION ═══ --}}
    @if($bien->description)
        <div class="ios-section-title">
            <i class="fa-solid fa-align-left"></i> Description
        </div>
        <blockquote class="quote-block">
            {{ $bien->description }}
        </blockquote>
    @endif

    {{-- ═══ VEDETTE ═══ --}}
    @if($isVedette)
        <div class="ios-section-title">
            <i class="fa-solid fa-star"></i> Statut vedette
        </div>

        <div class="vedette-card">
            <div class="vedette-card__icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="vedette-card__body">
                <strong>
                    En vedette jusqu'au
                    {{ $bien->vedette_fin ? $bien->vedette_fin->format('d/m/Y') : 'Illimité' }}
                </strong>
                @if(($bien->vedette_jours_restants ?? 0) > 0)
                    <span>{{ $bien->vedette_jours_restants }} jour(s) restant(s)</span>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══ ACTIONS FIXES ═══ --}}
    <div class="ios-form-actions">
        <a href="{{ route('agence.biens.edit', $bien->slug) }}" class="ios-btn ios-btn--ghost">
            <i class="fa-solid fa-pen"></i> Modifier
        </a>

        @if($isDispo)
            <form action="{{ route('agence.biens.desactiver', $bien->slug) }}" method="POST" class="ios-form-actions__form">
                @csrf
                <button type="submit" class="ios-btn ios-btn--danger"
                        onclick="return confirm('Masquer ce bien ?')">
                    <i class="fa-solid fa-eye-slash"></i> Masquer
                </button>
            </form>
        @else
            <form action="{{ route('agence.biens.activer', $bien->slug) }}" method="POST" class="ios-form-actions__form">
                @csrf
                <button type="submit" class="ios-btn ios-btn--success"
                        onclick="return confirm('Publier ce bien ?')">
                    <i class="fa-solid fa-eye"></i> Publier
                </button>
            </form>
        @endif

        @if(!$isVedette)
            <a href="{{ route('agence.biens.vedette.demander', $bien->slug) }}" class="ios-btn ios-btn--gold">
                <i class="fa-solid fa-star"></i> Vedette
            </a>
        @endif
    </div>
</div>

{{-- ═══ LIGHTBOX ═══ --}}
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
   DÉTAIL BIEN (Agence) — iOS NATIVE
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

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 22px 0 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ios-section-title i { color: var(--rust); font-size: 15px; }
.ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: var(--c-warning-bg);
    color: var(--rust);
    border-radius: 999px;
}

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
    top: 16px; left: 16px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
}
.hero-badge--status {
    top: 16px; right: 16px;
    background: rgba(255, 255, 255, .95);
}
.hero-badge--status i { font-size: 6px; }
.hero-badge--status.is-dispo { color: var(--c-success); }
.hero-badge--status.is-indispo { color: var(--c-danger); }

.hero-badge--count {
    top: 56px; right: 16px;
    background: rgba(0, 0, 0, .65);
    color: #fff;
}
.hero-badge--count i { font-size: 11px; }

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
    color: var(--ink);
    line-height: 1.2;
}
.quick-stat--gold .quick-stat__value { color: var(--c-warning); }
.quick-stat__label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}

/* ═══ GALERIE ═══ */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 8px;
    margin-bottom: 14px;
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
.gallery-item img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}

/* Vidéos */
.video-section { margin-bottom: 14px; }
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

/* ═══ LISTE iOS ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    margin-bottom: 14px;
}
.ios-list__item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    font-size: 13.5px;
}
.ios-list__item--static { cursor: default; }
.ios-list__content { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.ios-list__title {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    text-align: right;
    flex-shrink: 0;
}
.ios-list__value--rust { color: var(--rust); }
.ios-list__value .is-yes { color: var(--c-success); margin-right: 4px; }
.ios-list__value .is-no  { color: var(--c-danger);  margin-right: 4px; }

/* ═══ ÉQUIPEMENTS ═══ */
.equipements-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 14px;
}
.equipement-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 13px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border: 1px solid #BBDEFB;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}
.equipement-pill i { font-size: 11px; }

/* ═══ QUOTE ═══ */
.quote-block {
    position: relative;
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    line-height: 1.75;
    color: var(--ink);
    margin: 0 0 14px;
    border-left: 4px solid var(--rust);
    background: #fff;
    border-top: 1px solid var(--border);
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
    font-style: italic;
}

/* ═══ VEDETTE ═══ */
.vedette-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    background: var(--c-gold-bg);
    border-radius: 12px;
    border: 1px solid #FFE0B2;
    margin-bottom: 14px;
}
.vedette-card__icon {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(245, 166, 35, .3);
}
.vedette-card__body {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.vedette-card__body strong {
    font-size: 14px;
    font-weight: 700;
    color: var(--c-warning);
}
.vedette-card__body span {
    font-size: 12px;
    color: var(--c-warning);
    opacity: .85;
}

/* ═══ ACTIONS ═══ */
.ios-form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    margin-top: 20px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.ios-form-actions__form { flex: 1; margin: 0; }
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
    flex: 1;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181,80,42,.2);
}
.ios-btn--primary:hover { background: #9A4523; transform: translateY(-1px); }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--danger {
    background: #fff;
    color: var(--c-danger);
    border-color: #FFCDD2;
}
.ios-btn--danger:hover { background: var(--c-danger-bg); border-color: var(--c-danger); }
.ios-btn--success {
    background: var(--c-success);
    color: #fff;
    border-color: var(--c-success);
}
.ios-btn--success:hover { background: #156A3B; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(30, 122, 71, .25); }
.ios-btn--gold {
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    border-color: var(--c-gold);
}
.ios-btn--gold:hover { background: linear-gradient(135deg, #E8901A, #D57E15); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(245, 166, 35, .3); }

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
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-family: sans-serif;
    z-index: 2;
    transition: all .2s;
}
.lightbox__close:hover { background: rgba(255, 255, 255, .25); transform: scale(1.05); }
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
    display: flex; align-items: center; justify-content: center;
    z-index: 2;
    transition: all .2s;
}
.lightbox__nav:hover { background: rgba(255, 255, 255, .28); transform: translateY(-50%) scale(1.06); }
.lightbox__nav--prev { left: 24px; }
.lightbox__nav--next { right: 24px; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .bien-show { background: transparent; padding-top: 4px; }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-count { font-size: 11px; padding: 3px 10px; }

    /* ─── HERO iOS ─── */
    .bien-hero {
        border-radius: var(--ios-radius-lg);
        border: none;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .bien-hero__media { aspect-ratio: 16/9; }
    .bien-hero.is-vedette {
        border: none;
        box-shadow: 0 0 0 2px rgba(245, 166, 35, .3), 0 1px 3px rgba(0,0,0,.04);
    }

    .hero-badge {
        font-size: 10.5px;
        padding: 5px 12px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
    }
    .hero-badge--gold { top: 12px; left: 12px; }
    .hero-badge--status { top: 12px; right: 12px; }
    .hero-badge--count { top: 52px; right: 12px; }

    .bien-hero__overlay {
        padding: 60px 18px 18px;
    }
    .bien-hero__title {
        font-size: 22px;
        letter-spacing: -.4px;
        margin-bottom: 8px;
    }
    .bien-hero__meta {
        font-size: 13px;
        gap: 6px;
        margin-bottom: 12px;
    }
    .bien-hero__meta i { font-size: 12px; }
    .bien-hero__price {
        font-size: 20px;
        padding: 7px 16px;
        border-radius: 10px;
    }
    .bien-hero__price small { font-size: 12px; }

    /* ─── STATS iOS ─── */
    .quick-stats {
        gap: 8px;
        margin-bottom: 12px;
    }
    .quick-stat {
        background: var(--ios-card);
        border: none;
        border-radius: 14px;
        padding: 12px 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        min-width: 0;
    }
    .quick-stat__icon {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: var(--rust-soft);
        color: var(--rust);
        font-size: 15px;
    }
    .quick-stat--gold {
        background: linear-gradient(135deg, #FFFBF0, var(--c-gold-bg));
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .quick-stat--gold .quick-stat__icon {
        background: linear-gradient(135deg, var(--c-gold), #E8901A);
        color: #fff;
    }
    .quick-stat__value {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.1px;
    }
    .quick-stat__label {
        font-size: 11px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 500;
    }

    /* ─── GALERIE iOS ─── */
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .gallery-item { border-radius: 12px; border: none; }

    .video-grid { grid-template-columns: 1fr; gap: 10px; margin-bottom: 12px; }
    .video-tile { border-radius: 12px; border: none; }

    /* ─── LISTE iOS ─── */
    .ios-list {
        display: block;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
    }
    .ios-list__item {
        padding: 14px 16px;
        gap: 12px;
        border-bottom: 0.5px solid var(--ios-separator);
        font-size: 15px;
    }
    .ios-list__item:last-child { border-bottom: none; }
    .ios-list__title {
        font-size: 15px;
        color: var(--ios-label);
        font-weight: 500;
        letter-spacing: -.2px;
    }
    .ios-list__value {
        font-size: 15px;
        font-weight: 600;
        color: var(--ios-label-3);
    }
    .ios-list__value--rust { color: var(--rust); }

    /* ─── ÉQUIPEMENTS iOS ─── */
    .equipements-grid { gap: 8px; margin-bottom: 12px; }
    .equipement-pill {
        background: var(--ios-card);
        color: var(--ios-label);
        border: none;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .equipement-pill i { font-size: 12px; color: var(--c-success); }

    /* ─── QUOTE iOS ─── */
    .quote-block {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid var(--rust);
        border-radius: 12px;
        padding: 16px 18px;
        font-size: 14px;
        line-height: 1.65;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
    }

    /* ─── VEDETTE iOS ─── */
    .vedette-card {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid var(--c-gold);
        border-radius: 12px;
        padding: 14px 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .vedette-card__icon {
        width: 40px; height: 40px;
        font-size: 17px;
    }

    /* ─── ACTIONS FIXES ─── */
    .ios-form-actions {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(242, 242, 247, 0.95);
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        border-top: 0.5px solid var(--ios-separator);
        padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
        margin: 0;
        gap: 10px;
        z-index: 100;
        border-radius: 0;
        flex-wrap: nowrap;
    }
    .ios-form-actions__form { flex: 1; }
    .ios-btn {
        padding: 14px 18px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 14px;
        flex: 1;
        letter-spacing: -.1px;
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--ios-blue);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex: 0 0 auto;
        padding: 14px 16px;
    }
    .ios-btn--ghost:active { background: #EFEFF4; }
    .ios-btn--danger {
        background: var(--ios-card);
        border: none;
        color: var(--c-danger);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-btn--danger:active { background: #FFEBEE; }
    .ios-btn--success {
        background: linear-gradient(135deg, var(--c-success), #2E9958);
        border: none;
        box-shadow: 0 4px 12px rgba(30, 122, 71, .3);
    }
    .ios-btn--gold {
        background: linear-gradient(135deg, var(--c-gold), #E8901A);
        border: none;
        box-shadow: 0 4px 12px rgba(245, 166, 35, .3);
    }

    /* Padding bottom */
    .bien-show {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }

    /* ─── LIGHTBOX iOS ─── */
    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 14px; right: 14px; width: 40px; height: 40px; font-size: 22px; }
}

@media (max-width: 480px) {
    .bien-hero__title { font-size: 20px; }
    .bien-hero__price { font-size: 18px; }
    .bien-hero__overlay { padding: 50px 16px 16px; }

    .quick-stat { padding: 10px 12px; gap: 8px; }
    .quick-stat__icon { width: 32px; height: 32px; font-size: 13px; }
    .quick-stat__value { font-size: 14px; }

    .ios-section-title { font-size: 16px; }
}
</style>
@endpush