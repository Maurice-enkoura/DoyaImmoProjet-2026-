@extends('layouts.app')

@php
    $titreSEO = $bien->titre . ' — DoyaImmo';
    $descriptionSEO = $bien->description
        ? strip_tags(substr($bien->description, 0, 155)) . '...'
        : 'Découvrez ce bien immobilier à ' . ($bien->quartier->nom ?? 'Dakar') . '. Prix, caractéristiques et photos disponibles sur DoyaImmo.';
    $urlCanonique = 'https://doyaimmo.com/biens/' . $bien->slug;
    $isVedette = $bien->est_vedette && $bien->vedette_fin > now();
    $isDispo   = (bool) $bien->statut;

    $images = $bien->medias->where('type_media', 'image')->values();
    $videos = $bien->medias->where('type_media', 'video')->values();
    $firstImage = $images->first();
@endphp

@section('title', $titreSEO)
@section('meta_description', $descriptionSEO)
@section('canonical', $urlCanonique)
@section('og_title', $titreSEO)
@section('og_description', $descriptionSEO)
@section('robots', 'index, follow')

@section('content')
<div class="bien-show">

    @php
        $heroImage = $firstImage ? asset('storage/' . $firstImage->fichier) : null;
        $agence     = $bien->agence;
        $agenceUser = $agence->user ?? null;
        $hasTel     = $agenceUser && !empty($agenceUser->telephone);
        $whatsapp   = $hasTel ? preg_replace('/[^0-9]/', '', $agenceUser->telephone) : null;
    @endphp

    {{-- ═══════════════════════════════════════════
         NAV + PARTAGE
    ═══════════════════════════════════════════ --}}
    <nav class="bien-nav">
        <a href="{{ route('biens.index') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Retour aux biens
        </a>

        <div class="share-group">
            <span class="share-group__label">Partager</span>
            <button type="button" class="share-btn share-btn--fb" onclick="shareFacebook()" aria-label="Partager sur Facebook">
                <i class="fa-brands fa-facebook-f"></i>
            </button>
            <button type="button" class="share-btn share-btn--wa" onclick="shareWhatsApp()" aria-label="Partager sur WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
            </button>
            <button type="button" class="share-btn share-btn--link" onclick="copyLink()" aria-label="Copier le lien">
                <i class="fa-solid fa-link"></i>
            </button>
        </div>
    </nav>

    {{-- ═══════════════════════════════════════════
         GALERIE
    ═══════════════════════════════════════════ --}}
    <section class="gallery">
        <div class="gallery__main" id="mainImage">
            @if($heroImage)
                <img src="{{ $heroImage }}"
                     alt="{{ $bien->titre }}"
                     loading="eager"
                     onclick="openLightbox(0)">
            @else
                <div class="gallery__placeholder">
                    <i class="fa-regular fa-image"></i>
                    <span>Aucune image disponible</span>
                </div>
            @endif

            @if($isVedette)
                <span class="hero-badge hero-badge--vedette">
                    <i class="fa-solid fa-star"></i> Vedette
                    @if($bien->vedette_jours_restants > 0)
                        <small>· {{ $bien->vedette_jours_restants }} j</small>
                    @endif
                </span>
            @endif

            <span class="hero-badge hero-badge--status {{ $isDispo ? 'is-dispo' : 'is-indispo' }}">
                <i class="fa-solid fa-circle"></i>
                {{ $isDispo ? 'Disponible' : 'Indisponible' }}
            </span>

            <span class="hero-badge hero-badge--type">
                {{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}
            </span>

            <span class="hero-badge hero-badge--views">
                <i class="fa-regular fa-eye"></i> {{ $bien->vues ?? 0 }}
            </span>

            @if($images->count() > 1)
                <button type="button" class="gallery__counter" onclick="openLightbox(0)">
                    <i class="fa-solid fa-expand"></i>
                    <span>1 / {{ $images->count() }}</span>
                </button>
            @endif
        </div>

        @if($images->count() > 1 || $videos->count() > 0)
            <div class="gallery__thumbs">
                @foreach($images as $index => $image)
                    <button type="button"
                            class="thumb {{ $index === 0 ? 'is-active' : '' }}"
                            onclick="changeMainImage('{{ asset('storage/' . $image->fichier) }}', {{ $index }}, this)">
                        <img src="{{ asset('storage/' . $image->fichier) }}" alt="" loading="lazy">
                    </button>
                @endforeach

                @foreach($videos as $video)
                    <button type="button"
                            class="thumb thumb--video"
                            onclick="playVideo('{{ asset('storage/' . $video->fichier) }}', this)">
                        <video src="{{ asset('storage/' . $video->fichier) }}" preload="metadata"></video>
                        <span class="thumb__play">
                            <i class="fa-solid fa-circle-play"></i>
                        </span>
                    </button>
                @endforeach
            </div>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════
         CORPS : CONTENU + SIDEBAR
    ═══════════════════════════════════════════ --}}
    <div class="bien-show__grid">

        {{-- ═══ COLONNE PRINCIPALE ═══ --}}
        <div class="bien-show__main">

            {{-- HEADLINE --}}
            <section class="headline">
                <h1 class="headline__title">{{ $bien->titre }}</h1>

                <div class="headline__location">
                    <i class="fa-solid fa-location-dot"></i>
                    {{ $bien->adresse }}@if($bien->quartier) · {{ $bien->quartier->nom ?? $bien->quartier }}@endif
                </div>

                <div class="headline__row">
                    <div class="headline__price">
                        {{ number_format($bien->prix, 0, ',', ' ') }}
                        <small>FCFA</small>
                    </div>

                    <span class="headline__contract">
                        {{ is_object($bien->type_contrat) && method_exists($bien->type_contrat, 'label') ? $bien->type_contrat->label() : $bien->type_contrat }}
                    </span>

                    @if($isVedette)
                        <span class="headline__vedette">
                            <i class="fa-solid fa-star"></i> Vedette
                        </span>
                    @endif
                </div>

                <div class="headline__pills">
                    <span class="pill">
                        <i class="fa-regular fa-square"></i> {{ $bien->surface }} m²
                    </span>
                    @if($bien->nombre_chambres > 0)
                        <span class="pill">
                            <i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.
                        </span>
                    @endif
                    @if($bien->nombre_salles_bain > 0)
                        <span class="pill">
                            <i class="fa-solid fa-bath"></i> {{ $bien->nombre_salles_bain }} sdb
                        </span>
                    @endif
                    @if($bien->parking_disponible)
                        <span class="pill"><i class="fa-solid fa-car"></i> Parking</span>
                    @endif
                    @if($bien->est_meuble)
                        <span class="pill"><i class="fa-solid fa-couch"></i> Meublé</span>
                    @endif
                </div>
            </section>

            {{-- DESCRIPTION --}}
            @if($bien->description)
                <section class="panel">
                    <header class="panel__head">
                        <span class="panel__icon">
                            <i class="fa-solid fa-align-left"></i>
                        </span>
                        <h2>Description</h2>
                    </header>
                    <p class="panel__text">{{ $bien->description }}</p>
                </section>
            @endif

            {{-- CARACTÉRISTIQUES --}}
            <section class="panel">
                <header class="panel__head">
                    <span class="panel__icon">
                        <i class="fa-regular fa-list-check"></i>
                    </span>
                    <h2>Caractéristiques</h2>
                </header>

                <div class="info-grid">
                    <div class="info-grid__item">
                        <span class="info-grid__label">Type de bien</span>
                        <span class="info-grid__value">
                            {{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}
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
                            {{ $bien->parking_disponible ? 'Oui' : 'Non' }}
                        </span>
                    </div>
                    <div class="info-grid__item">
                        <span class="info-grid__label">Meublé</span>
                        <span class="info-grid__value">
                            <i class="fa-solid {{ $bien->est_meuble ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                            {{ $bien->est_meuble ? 'Oui' : 'Non' }}
                        </span>
                    </div>

                    @if($bien->climatisation)
                        <div class="info-grid__item">
                            <span class="info-grid__label">Climatisation</span>
                            <span class="info-grid__value"><i class="fa-solid fa-circle-check is-yes"></i> Oui</span>
                        </div>
                    @endif
                    @if($bien->balcon)
                        <div class="info-grid__item">
                            <span class="info-grid__label">Balcon</span>
                            <span class="info-grid__value"><i class="fa-solid fa-circle-check is-yes"></i> Oui</span>
                        </div>
                    @endif
                    @if($bien->jardin)
                        <div class="info-grid__item">
                            <span class="info-grid__label">Jardin</span>
                            <span class="info-grid__value"><i class="fa-solid fa-circle-check is-yes"></i> Oui</span>
                        </div>
                    @endif
                    @if($bien->piscine)
                        <div class="info-grid__item">
                            <span class="info-grid__label">Piscine</span>
                            <span class="info-grid__value"><i class="fa-solid fa-circle-check is-yes"></i> Oui</span>
                        </div>
                    @endif
                    @if($bien->ascenseur)
                        <div class="info-grid__item">
                            <span class="info-grid__label">Ascenseur</span>
                            <span class="info-grid__value"><i class="fa-solid fa-circle-check is-yes"></i> Oui</span>
                        </div>
                    @endif
                    @if($bien->securite)
                        <div class="info-grid__item">
                            <span class="info-grid__label">Sécurité 24h/24</span>
                            <span class="info-grid__value"><i class="fa-solid fa-circle-check is-yes"></i> Oui</span>
                        </div>
                    @endif

                    <div class="info-grid__item">
                        <span class="info-grid__label">Contrat</span>
                        <span class="info-grid__value">
                            {{ is_object($bien->type_contrat) && method_exists($bien->type_contrat, 'label') ? $bien->type_contrat->label() : $bien->type_contrat }}
                        </span>
                    </div>
                    <div class="info-grid__item">
                        <span class="info-grid__label">Publié le</span>
                        <span class="info-grid__value">{{ $bien->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-grid__item">
                        <span class="info-grid__label">Vues</span>
                        <span class="info-grid__value">{{ $bien->vues ?? 0 }}</span>
                    </div>
                </div>
            </section>

            {{-- ÉQUIPEMENTS --}}
            @php $equipements = $bien->equipements ?? []; @endphp
            @if(count($equipements) > 0)
                <section class="panel">
                    <header class="panel__head">
                        <span class="panel__icon">
                            <i class="fa-solid fa-cogs"></i>
                        </span>
                        <h2>Équipements</h2>
                        <span class="panel__count">{{ count($equipements) }}</span>
                    </header>

                    <div class="equipements">
                        @foreach($equipements as $equipement)
                            <span class="equipement">
                                <i class="fa-solid fa-check"></i>
                                {{ $equipement }}
                            </span>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- VEDETTE --}}
            @if($isVedette)
                <section class="panel panel--vedette">
                    <header class="panel__head">
                        <span class="panel__icon panel__icon--gold">
                            <i class="fa-solid fa-star"></i>
                        </span>
                        <h2>Bien en vedette</h2>
                    </header>

                    <div class="vedette-info">
                        <div class="vedette-info__icon">
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <div class="vedette-info__content">
                            <strong>Ce bien est mis en avant par l'agence</strong>
                            @if($bien->vedette_fin)
                                <span>
                                    Jusqu'au {{ $bien->vedette_fin->format('d/m/Y') }}
                                    @if($bien->vedette_jours_restants > 0)
                                        · {{ $bien->vedette_jours_restants }} jour(s) restant(s)
                                    @endif
                                </span>
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        </div>

        {{-- ═══ SIDEBAR ═══ --}}
        <aside class="bien-show__sidebar">
            <div class="sidebar__sticky">

                {{-- AGENCE --}}
                <section class="panel agency-card">
                    <header class="panel__head">
                        <span class="panel__icon">
                            <i class="fa-regular fa-building"></i>
                        </span>
                        <h3>Agence</h3>
                    </header>

                    <div class="agency-card__head">
                        <div class="agency-card__avatar">
                            {{ strtoupper(mb_substr($agence->nom_agence, 0, 1)) }}
                        </div>
                        <div class="agency-card__info">
                            <h4>{{ $agence->nom_agence }}</h4>
                            <div class="agency-card__rating">
                                <i class="fa-solid fa-star"></i>
                                <strong>{{ number_format($agence->note_moyenne ?? 0, 1) }}</strong>
                                <span>/ 5 ({{ $agence->evaluations->count() }} avis)</span>
                            </div>
                        </div>
                    </div>

                    @if($agence->adresse)
                        <div class="agency-card__address">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $agence->adresse }}
                        </div>
                    @endif

                    @if($hasTel)
                        <div class="agency-card__contacts">
                            <a href="tel:{{ $agenceUser->telephone }}" class="contact-row">
                                <i class="fa-solid fa-phone"></i>
                                <span>{{ $agenceUser->telephone }}</span>
                            </a>
                            @if($agenceUser->email ?? false)
                                <a href="mailto:{{ $agenceUser->email }}" class="contact-row">
                                    <i class="fa-solid fa-envelope"></i>
                                    <span>{{ $agenceUser->email }}</span>
                                </a>
                            @endif
                        </div>
                    @endif

                    <div class="agency-card__actions">
                        @if($hasTel)
                            <a href="tel:{{ $agenceUser->telephone }}" class="btn btn-success btn-block">
                                <i class="fa-solid fa-phone"></i> Appeler l'agence
                            </a>
                            <a href="https://wa.me/{{ $whatsapp }}"
                               target="_blank" rel="noopener"
                               class="btn btn-whatsapp btn-block">
                                <i class="fa-brands fa-whatsapp"></i> WhatsApp
                            </a>
                        @endif

                        <a href="{{ route('agences.public.show', $agence->slug) }}"
                           class="btn btn-ghost btn-block">
                            <i class="fa-solid fa-building"></i> Voir le profil
                        </a>
                    </div>
                </section>

                {{-- CTA --}}
                <section class="panel cta-card">
                    <div class="cta-card__icon">
                        <i class="fa-solid fa-magnifying-glass-location"></i>
                    </div>
                    <h3 class="cta-card__title">Vous cherchez un bien ?</h3>
                    <p class="cta-card__text">
                        Publiez vos critères et recevez des propositions sur mesure des agences.
                    </p>

                    @auth
                        @if(auth()->user()->isParticulier())
                            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust btn-block">
                                <i class="fa-solid fa-plus"></i> Publier un besoin
                            </a>
                        @elseif(auth()->user()->isAgence())
                            <a href="{{ route('agence.dashboard') }}" class="btn btn-ghost btn-block">
                                <i class="fa-solid fa-gauge"></i> Tableau de bord
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register.particulier') }}" class="btn btn-rust btn-block">
                            <i class="fa-solid fa-user-plus"></i> Créer un compte gratuit
                        </a>
                        <p class="cta-card__free">Gratuit · En 2 minutes</p>
                    @endauth
                </section>

                {{-- SIGNALEMENT --}}
                @auth
                    @if(auth()->user()->isParticulier())
                        <section class="panel report-card">
                            <h3 class="report-card__title">
                                <i class="fa-solid fa-flag"></i> Signaler ce bien
                            </h3>
                            <p class="report-card__text">
                                Vous avez remarqué une anomalie ou une information incorrecte ?
                            </p>
                            <a href="{{ route('particulier.signalements.create-bien', $bien->slug) }}"
                               class="btn btn-ghost-danger btn-block btn-sm">
                                <i class="fa-solid fa-flag"></i> Signaler
                            </a>
                        </section>
                    @endif
                @endauth
            </div>
        </aside>
    </div>

    {{-- ═══════════════════════════════════════════
         BIENS SIMILAIRES
    ═══════════════════════════════════════════ --}}
    @if($biensSimilaires->count() > 0)
        <section class="similar-section">
            <header class="similar-section__head">
                <h2>Biens similaires</h2>
                <a href="{{ route('biens.index') }}" class="see-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </header>

            <div class="similar-grid">
                @foreach($biensSimilaires as $sim)
                    @php
                        $simImage = $sim->medias->where('type_media', 'image')->first();
                        $simIsVedette = $sim->est_vedette && $sim->vedette_fin > now();
                    @endphp

                    <a href="{{ route('biens.show', $sim->slug) }}" class="similar-card">
                        <div class="similar-card__media">
                            @if($simImage)
                                <img src="{{ asset('storage/' . $simImage->fichier) }}" alt="{{ $sim->titre }}" loading="lazy">
                            @else
                                <div class="similar-card__placeholder">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            @endif

                            @if($simIsVedette)
                                <span class="similar-badge similar-badge--vedette">
                                    <i class="fa-solid fa-star"></i> Vedette
                                </span>
                            @endif

                            <span class="similar-badge similar-badge--status {{ $sim->statut ? 'is-dispo' : 'is-indispo' }}">
                                <i class="fa-solid fa-circle"></i>
                                {{ $sim->statut ? 'Disponible' : 'Indisponible' }}
                            </span>
                        </div>

                        <div class="similar-card__body">
                            <h3 class="similar-card__title">{{ $sim->titre }}</h3>
                            <div class="similar-card__price">
                                {{ number_format($sim->prix, 0, ',', ' ') }}
                                <small>FCFA</small>
                            </div>
                            <div class="similar-card__location">
                                <i class="fa-solid fa-location-dot"></i>
                                {{ $sim->quartier->nom ?? $sim->quartier }}
                            </div>
                            <div class="similar-card__specs">
                                <span><i class="fa-regular fa-square"></i> {{ $sim->surface }} m²</span>
                                @if($sim->nombre_chambres)
                                    <span><i class="fa-solid fa-bed"></i> {{ $sim->nombre_chambres }} ch.</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</div>

{{-- ═══════════════════════════════════════════
     LIGHTBOX
═══════════════════════════════════════════ --}}
<div id="lightbox" class="lightbox" onclick="closeLightboxOutside(event)">
    <button type="button" class="lightbox__close" onclick="event.stopPropagation();closeLightbox()" aria-label="Fermer">
        <i class="fa-solid fa-xmark"></i>
    </button>

    <button type="button" class="lightbox__nav lightbox__nav--prev" id="lightboxPrev"
            onclick="event.stopPropagation();lightboxPrev()" aria-label="Précédent">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <figure class="lightbox__figure" onclick="event.stopPropagation()">
        <img id="lightboxImage" src="" alt="Agrandir">
        <figcaption id="lightboxCounter" class="lightbox__counter"></figcaption>
    </figure>

    <button type="button" class="lightbox__nav lightbox__nav--next" id="lightboxNext"
            onclick="event.stopPropagation();lightboxNext()" aria-label="Suivant">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
</div>
@endsection


@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════
   LIGHTBOX
═══════════════════════════════════════════════════════════ */
const LIGHTBOX_IMAGES = @json($images->map(fn($m) => asset('storage/' . $m->fichier))->values());
let currentIndex = 0;
let isLightboxOpen = false;

function openLightbox(index) {
    if (LIGHTBOX_IMAGES.length === 0) return;
    currentIndex = (typeof index === 'number' && index >= 0 && index < LIGHTBOX_IMAGES.length) ? index : 0;
    renderLightbox();
    document.getElementById('lightbox').classList.add('is-open');
    document.body.style.overflow = 'hidden';
    isLightboxOpen = true;
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('is-open');
    document.body.style.overflow = '';
    isLightboxOpen = false;
}

function closeLightboxOutside(e) {
    if (e.target === e.currentTarget) closeLightbox();
}

function lightboxPrev() {
    if (currentIndex > 0) {
        currentIndex--;
        renderLightbox();
    }
}

function lightboxNext() {
    if (currentIndex < LIGHTBOX_IMAGES.length - 1) {
        currentIndex++;
        renderLightbox();
    }
}

function renderLightbox() {
    const img     = document.getElementById('lightboxImage');
    const counter = document.getElementById('lightboxCounter');
    const prev    = document.getElementById('lightboxPrev');
    const next    = document.getElementById('lightboxNext');

    img.src = LIGHTBOX_IMAGES[currentIndex] || '';

    if (LIGHTBOX_IMAGES.length > 1) {
        counter.textContent = (currentIndex + 1) + ' / ' + LIGHTBOX_IMAGES.length;
        counter.style.display = 'block';
        prev.style.display = currentIndex > 0 ? 'flex' : 'none';
        next.style.display = currentIndex < LIGHTBOX_IMAGES.length - 1 ? 'flex' : 'none';
    } else {
        counter.style.display = 'none';
        prev.style.display = 'none';
        next.style.display = 'none';
    }
}

/* ═══════════════════════════════════════════════════════════
   CHANGEMENT IMAGE PRINCIPALE
═══════════════════════════════════════════════════════════ */
function changeMainImage(src, index, el) {
    const main = document.getElementById('mainImage');
    const img = main.querySelector('img');

    if (img) {
        img.src = src;
        img.setAttribute('onclick', `openLightbox(${index})`);
    } else {
        main.innerHTML = `<img src="${src}" alt="Bien" loading="eager" onclick="openLightbox(${index})">`;
    }

    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('is-active'));
    if (el) el.classList.add('is-active');
}

/* ═══════════════════════════════════════════════════════════
   VIDÉO
═══════════════════════════════════════════════════════════ */
function playVideo(src, el) {
    const main = document.getElementById('mainImage');
    main.innerHTML = `
        <video src="${src}" controls autoplay playsinline
               style="width:100%;height:100%;object-fit:cover;"></video>
    `;

    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('is-active'));
    if (el) el.classList.add('is-active');
}

/* ═══════════════════════════════════════════════════════════
   SWIPE + CLAVIER
═══════════════════════════════════════════════════════════ */
let touchStartX = 0;
document.getElementById('lightboxImage')?.addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].screenX;
}, { passive: true });
document.getElementById('lightboxImage')?.addEventListener('touchend', e => {
    const diff = touchStartX - e.changedTouches[0].screenX;
    if (Math.abs(diff) > 50) {
        if (diff > 0) lightboxNext();
        else lightboxPrev();
    }
}, { passive: true });

document.addEventListener('keydown', e => {
    if (!isLightboxOpen) return;
    if (e.key === 'Escape')     closeLightbox();
    if (e.key === 'ArrowRight') lightboxNext();
    if (e.key === 'ArrowLeft')  lightboxPrev();
});

/* ═══════════════════════════════════════════════════════════
   SHARE
═══════════════════════════════════════════════════════════ */
function shareFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent("Découvrez ce bien immobilier sur DoyaImmo !");
    window.open(`https://wa.me/?text=${text}%20${url}`, '_blank', 'width=600,height=400');
}

function copyLink() {
    const url = window.location.href;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => showToast('🔗 Lien copié !'));
    } else {
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('🔗 Lien copié !');
    }
}

function showToast(message) {
    const t = document.createElement('div');
    t.className = 'toast';
    t.textContent = message;
    document.body.appendChild(t);
    setTimeout(() => t.classList.add('is-out'), 2500);
    setTimeout(() => t.remove(), 2800);
}
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL BIEN — Publique
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
    --c-gold:       #D4AF37;
    --c-gold-bg:    #FDF5E6;
    --c-whatsapp:   #25D366;
    --surface:      #F7F9FC;
    --radius:       16px;

    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 24px 40px;
}

/* ═══ NAV ═══ */
.bien-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--muted);
    text-decoration: none;
    transition: color .2s;
    font-weight: 500;
}
.back-link:hover { color: #B85C3A; }
.back-link i { font-size: 12px; }

.share-group {
    display: flex;
    align-items: center;
    gap: 8px;
}
.share-group__label {
    font-size: 12.5px;
    color: var(--muted);
    margin-right: 4px;
    font-weight: 500;
}

.share-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    font-size: 14px;
}
.share-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
}
.share-btn--fb   { background: #1877F2; color: #fff; }
.share-btn--wa   { background: var(--c-whatsapp); color: #fff; }
.share-btn--link { background: var(--border); color: var(--text-soft); }

/* ═══ GALERIE ═══ */
.gallery {
    background: #fff;
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 22px;
    border: 1px solid var(--border);
}

.gallery__main {
    position: relative;
    aspect-ratio: 16 / 10;
    background: #F0F2F5;
    overflow: hidden;
}
.gallery__main img {
    width: 100%; height: 100%;
    object-fit: cover;
    cursor: zoom-in;
    display: block;
    transition: transform .3s ease;
}
.gallery__main:hover img { transform: scale(1.02); }

.gallery__placeholder {
    width: 100%; height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    gap: 10px;
}
.gallery__placeholder i { font-size: 48px; opacity: .3; }
.gallery__placeholder span { font-size: 14px; }

.hero-badge {
    position: absolute;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    z-index: 2;
    backdrop-filter: blur(6px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
}
.hero-badge i { font-size: 10px; }
.hero-badge small {
    font-size: 10px;
    opacity: .9;
    font-weight: 600;
}

.hero-badge--vedette {
    top: 16px;
    left: 16px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    animation: pulseVedette 2.2s ease-in-out infinite;
}
@keyframes pulseVedette {
    0%, 100% { opacity: 1; }
    50%      { opacity: .85; }
}

.hero-badge--status {
    top: 16px;
    right: 16px;
    background: rgba(255, 255, 255, .95);
}
.hero-badge--status i { font-size: 6px; }
.hero-badge--status.is-dispo   { color: var(--c-success); }
.hero-badge--status.is-indispo { color: var(--muted); }

.hero-badge--type {
    bottom: 16px;
    left: 16px;
    background: rgba(0, 0, 0, .75);
    color: #fff;
}

.hero-badge--views {
    bottom: 16px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, .6);
    color: #fff;
    text-transform: none;
    letter-spacing: 0;
    font-weight: 600;
}

.gallery__counter {
    position: absolute;
    bottom: 16px;
    right: 16px;
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 14px;
    background: rgba(0, 0, 0, .7);
    color: #fff;
    border: none;
    border-radius: 999px;
    font-size: 12.5px;
    font-weight: 600;
    cursor: pointer;
    backdrop-filter: blur(6px);
    font-family: inherit;
    transition: background .2s, transform .2s;
}
.gallery__counter:hover {
    background: rgba(0, 0, 0, .85);
    transform: scale(1.04);
}
.gallery__counter i { font-size: 11px; }

.gallery__thumbs {
    display: flex;
    gap: 8px;
    padding: 12px;
    overflow-x: auto;
    scrollbar-width: thin;
    background: #fff;
    border-top: 1px solid var(--border);
}
.gallery__thumbs::-webkit-scrollbar { height: 4px; }
.gallery__thumbs::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 999px;
}

.thumb {
    flex-shrink: 0;
    width: 70px;
    height: 70px;
    border-radius: 10px;
    overflow: hidden;
    border: 2px solid transparent;
    cursor: pointer;
    background: var(--surface);
    transition: border-color .2s, transform .2s;
    padding: 0;
    position: relative;
}
.thumb:hover { transform: scale(1.04); }
.thumb.is-active { border-color: #B85C3A; }

.thumb img,
.thumb video {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}

.thumb--video video { opacity: .55; }
.thumb__play {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 22px;
    pointer-events: none;
}

/* ═══ GRID ═══ */
.bien-show__grid {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(280px, 1fr);
    gap: 22px;
    align-items: start;
}

.bien-show__main { min-width: 0; }
.bien-show__sidebar { min-width: 0; }

.sidebar__sticky {
    position: sticky;
    top: 90px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* ═══ HEADLINE ═══ */
.headline {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 22px 24px;
    margin-bottom: 16px;
}

.headline__title {
    font-family: var(--display);
    font-size: 26px;
    font-weight: 800;
    margin: 0 0 8px;
    color: var(--ink);
    line-height: 1.25;
    word-break: break-word;
}

.headline__location {
    font-size: 13.5px;
    color: var(--muted);
    margin-bottom: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.headline__location i { color: #B85C3A; }

.headline__row {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}

.headline__price {
    font-family: var(--display);
    font-size: 26px;
    font-weight: 800;
    color: #B85C3A;
    line-height: 1.1;
    white-space: nowrap;
}
.headline__price small {
    font-size: 14px;
    font-weight: 700;
    opacity: .8;
    margin-left: 3px;
}

.headline__contract {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    background: var(--surface);
    color: var(--text-soft);
    border: 1px solid var(--border);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
}

.headline__vedette {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    background: var(--c-gold-bg);
    color: #B8860B;
    border: 1px solid var(--c-gold);
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.headline__vedette i { color: var(--c-gold); font-size: 10px; }

.headline__pills {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 999px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--text-soft);
}
.pill i { font-size: 11px; color: #B85C3A; opacity: .8; }

/* ═══ PANNEAUX ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px 24px;
    margin-bottom: 16px;
}
.panel:last-child { margin-bottom: 0; }

.panel__head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
}
.panel__icon {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: #F5E6DF;
    color: #B85C3A;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.panel__icon--gold {
    background: var(--c-gold-bg);
    color: var(--c-gold);
}
.panel__head h2,
.panel__head h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
    flex: 1;
    min-width: 0;
}
.panel__count {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: #F5E6DF;
    color: #B85C3A;
    border-radius: 999px;
}

.panel__text {
    font-size: 14px;
    line-height: 1.75;
    color: var(--text-soft);
    margin: 0;
    white-space: pre-line;
}

/* ═══ INFO GRID ═══ */
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
    border-radius: 10px;
    border: 1px solid transparent;
    transition: border-color .2s;
}
.info-grid__item:hover { border-color: var(--border); }

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
    color: var(--ink);
}
.info-grid__value .is-yes { color: var(--c-success); margin-right: 4px; }
.info-grid__value .is-no  { color: var(--muted);     margin-right: 4px; }

/* ═══ ÉQUIPEMENTS ═══ */
.equipements {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.equipement {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 13px;
    background: var(--c-success-bg);
    color: var(--c-success);
    border: 1px solid #C8E6C9;
    border-radius: 999px;
    font-size: 12.5px;
    font-weight: 600;
}
.equipement i { font-size: 10px; }

/* ═══ VEDETTE ═══ */
.panel--vedette {
    background: linear-gradient(135deg, var(--c-gold-bg) 0%, #fff 60%);
    border-color: var(--c-gold);
}
.vedette-info {
    display: flex;
    align-items: center;
    gap: 14px;
}
.vedette-info__icon {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(212, 175, 55, .3);
}
.vedette-info__content {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.vedette-info__content strong {
    font-size: 14px;
    font-weight: 700;
    color: #8B6508;
}
.vedette-info__content span {
    font-size: 12.5px;
    color: #A47A0F;
}

/* ═══ AGENCE ═══ */
.agency-card__head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
}
.agency-card__avatar {
    width: 48px; height: 48px;
    border-radius: 50%;
    background: #F5E6DF;
    color: #B85C3A;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    flex-shrink: 0;
}
.agency-card__info { min-width: 0; }
.agency-card__info h4 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 3px;
    color: var(--ink);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.agency-card__rating {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: var(--text-soft);
}
.agency-card__rating i { color: var(--c-gold); font-size: 11px; }
.agency-card__rating strong {
    font-family: var(--display);
    font-weight: 800;
    color: var(--ink);
}

.agency-card__address {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 12px;
    background: var(--surface);
    border-radius: 10px;
    font-size: 12.5px;
    color: var(--text-soft);
    margin-bottom: 10px;
    line-height: 1.5;
}
.agency-card__address i {
    color: #B85C3A;
    margin-top: 2px;
    flex-shrink: 0;
}

.agency-card__contacts {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 12px;
}
.contact-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: var(--surface);
    border-radius: 10px;
    font-size: 12.5px;
    color: var(--ink);
    text-decoration: none;
    transition: background .2s;
    word-break: break-word;
}
.contact-row:hover { background: #F0F4F8; }
.contact-row i {
    color: #B85C3A;
    width: 16px;
    text-align: center;
    flex-shrink: 0;
}

.agency-card__actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* ═══ CTA ═══ */
.cta-card {
    background: linear-gradient(135deg, #F5E6DF 0%, #FDF5F2 100%);
    border-color: rgba(184, 92, 58, .2);
    text-align: center;
}
.cta-card__icon {
    width: 48px; height: 48px;
    margin: 0 auto 10px;
    border-radius: 50%;
    background: rgba(184, 92, 58, .15);
    color: #B85C3A;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}
.cta-card__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--ink);
}
.cta-card__text {
    font-size: 12.5px;
    color: var(--text-soft);
    line-height: 1.55;
    margin: 0 0 14px;
}
.cta-card__free {
    font-size: 11px;
    color: var(--muted);
    margin: 8px 0 0;
}

/* ═══ SIGNALEMENT ═══ */
.report-card { text-align: center; padding: 16px 20px; }
.report-card__title {
    font-family: var(--display);
    font-size: 13.5px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--c-danger);
}
.report-card__title i { font-size: 12px; }
.report-card__text {
    font-size: 11.5px;
    color: var(--muted);
    margin: 0 0 12px;
    line-height: 1.5;
}

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 16px;
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
    background: #B85C3A;
    color: #fff;
    border-color: #B85C3A;
}
.btn-rust:hover {
    background: #9A4523;
    color: #fff;
    border-color: #9A4523;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(184, 92, 58, .25);
}
.btn-ghost {
    background: transparent;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: var(--surface);
    border-color: #B85C3A;
    color: #B85C3A;
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
.btn-whatsapp {
    background: var(--c-whatsapp);
    color: #fff;
    border-color: var(--c-whatsapp);
}
.btn-whatsapp:hover {
    background: #1DA851;
    color: #fff;
    border-color: #1DA851;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(37, 211, 102, .25);
}
.btn-block { width: 100%; }
.btn-sm { padding: 8px 12px; font-size: 12px; }

/* ═══ BIENS SIMILAIRES ═══ */
.similar-section {
    margin-top: 40px;
}
.similar-section__head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.similar-section__head h2 {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
}
.see-all {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #B85C3A;
    text-decoration: none;
    transition: gap .2s;
}
.see-all:hover { gap: 9px; }
.see-all i { font-size: 11px; }

.similar-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 14px;
}

.similar-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform .2s, box-shadow .2s, border-color .2s;
}
.similar-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(0, 0, 0, .07);
    border-color: #d8d8d8;
}

.similar-card__media {
    position: relative;
    aspect-ratio: 4 / 3;
    background: var(--surface);
    overflow: hidden;
}
.similar-card__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
}
.similar-card:hover .similar-card__media img { transform: scale(1.05); }

.similar-card__placeholder {
    width: 100%; height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    font-size: 32px;
    opacity: .3;
}

.similar-badge {
    position: absolute;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
    z-index: 2;
    box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
}
.similar-badge i { font-size: 9px; }

.similar-badge--vedette {
    top: 8px;
    left: 8px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
}
.similar-badge--status {
    top: 8px;
    right: 8px;
    background: rgba(255, 255, 255, .95);
}
.similar-badge--status i { font-size: 5px; }
.similar-badge--status.is-dispo   { color: var(--c-success); }
.similar-badge--status.is-indispo { color: var(--muted); }

.similar-card__body {
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.similar-card__title {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.similar-card__price {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 800;
    color: #B85C3A;
    line-height: 1.1;
}
.similar-card__price small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}
.similar-card__location {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: var(--muted);
}
.similar-card__location i {
    color: #B85C3A;
    font-size: 11px;
    opacity: .8;
}
.similar-card__specs {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 12px;
    font-size: 11.5px;
    color: var(--text-soft);
    padding-top: 6px;
    border-top: 1px dashed var(--border);
}
.similar-card__specs span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.similar-card__specs i {
    color: #B85C3A;
    font-size: 10px;
    opacity: .8;
}

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
    border-radius: 6px;
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
    top: 20px;
    right: 24px;
    width: 42px; height: 42px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .12);
    color: #fff;
    border: none;
    font-size: 20px;
    cursor: pointer;
    transition: background .2s, transform .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
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
    transition: background .2s, transform .2s;
    display: flex;
    align-items: center;
    justify-content: center;
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

/* ═══ TOAST ═══ */
.toast {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--ink);
    color: #fff;
    padding: 12px 22px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 600;
    z-index: 9999;
    box-shadow: 0 8px 32px rgba(0, 0, 0, .25);
    animation: toastIn .3s ease;
    pointer-events: none;
}
@keyframes toastIn {
    from { opacity: 0; transform: translateX(-50%) translateY(20px); }
    to   { opacity: 1; transform: translateX(-50%) translateY(0); }
}
.toast.is-out {
    animation: toastOut .3s ease forwards;
}
@keyframes toastOut {
    to { opacity: 0; transform: translateX(-50%) translateY(20px); }
}

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .bien-show__grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .sidebar__sticky {
        position: static;
    }

    .info-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .bien-show { padding: 18px 16px 40px; }

    .gallery__main { aspect-ratio: 4 / 3; }

    .hero-badge { font-size: 10px; padding: 5px 11px; }
    .hero-badge--vedette { top: 10px; left: 10px; }
    .hero-badge--status  { top: 10px; right: 10px; }
    .hero-badge--type    { bottom: 10px; left: 10px; }
    .hero-badge--views   { display: none; }

    .gallery__counter {
        bottom: 10px;
        right: 10px;
        padding: 6px 12px;
        font-size: 11.5px;
    }

    .gallery__thumbs { padding: 10px; }
    .thumb { width: 60px; height: 60px; }

    .headline { padding: 18px 20px; }
    .headline__title { font-size: 21px; }
    .headline__price { font-size: 22px; }
    .headline__price small { font-size: 12px; }

    .panel { padding: 16px 20px; }
    .panel__head h2,
    .panel__head h3 { font-size: 15px; }

    .info-grid { grid-template-columns: 1fr; }

    .similar-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    .similar-card__body { padding: 10px 12px; }
    .similar-card__title { font-size: 13px; }
    .similar-card__price { font-size: 14px; }

    .lightbox__nav {
        width: 40px; height: 40px;
        font-size: 16px;
    }
    .lightbox__nav--prev { left: 10px; }
    .lightbox__nav--next { right: 10px; }
    .lightbox__close {
        top: 12px; right: 12px;
        width: 36px; height: 36px;
        font-size: 18px;
    }
}

@media (max-width: 480px) {
    .bien-show { padding: 14px 14px 40px; }

    .bien-nav { flex-direction: column; align-items: stretch; }
    .share-group { justify-content: flex-start; }
    .share-group__label { font-size: 12px; }

    .headline { padding: 16px 18px; }
    .headline__title { font-size: 18px; }
    .headline__price { font-size: 20px; }
    .headline__price small { font-size: 11px; }
    .headline__contract { font-size: 10.5px; padding: 4px 10px; }
    .headline__vedette  { font-size: 10px; padding: 4px 10px; }

    .pill { font-size: 11.5px; padding: 4px 10px; }

    .panel { padding: 14px 16px; }
    .panel__head { padding-bottom: 12px; margin-bottom: 14px; }
    .panel__head h2,
    .panel__head h3 { font-size: 14px; }

    .panel__text { font-size: 13px; }

    .info-grid__item { padding: 9px 12px; }
    .info-grid__value { font-size: 13px; }

    .agency-card__avatar { width: 42px; height: 42px; font-size: 17px; }
    .agency-card__info h4 { font-size: 14px; }

    .similar-grid { grid-template-columns: 1fr; gap: 10px; }

    .btn { font-size: 12.5px; padding: 9px 14px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
    .hero-badge--vedette { animation: none !important; }
}
</style>
@endpush