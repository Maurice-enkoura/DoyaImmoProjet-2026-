@extends('layouts.dashboard-agence')

@section('title', 'Détail de l\'offre — DoyaImmo')
@section('page_title', 'Détail de l\'offre')
@section('page_sub', 'Informations complètes sur la proposition')

@section('content')
<div class="view active offre-detail">

    @php
        $isVedette  = $proposition->bien && $proposition->bien->est_vedette;
        $statut     = is_object($proposition->statut) ? $proposition->statut->value : $proposition->statut;
        $statutLabel = is_object($proposition->statut) ? $proposition->statut->label() : ucfirst($proposition->statut);
        $score      = $proposition->score_matching;
        $scoreClass = $score >= 80 ? 'excellent' : ($score >= 60 ? 'moyen' : 'faible');

        $prenom  = $proposition->particulier->user->prenom ?? '';
        $nom     = $proposition->particulier->user->nom    ?? '';
        $initial = strtoupper(mb_substr($prenom ?: 'C', 0, 1));

        $images     = $proposition->medias->where('type_media', 'image')->values();
        $videos     = $proposition->medias->where('type_media', 'video')->values();
        $bienImages = $proposition->bien->medias->where('type_media', 'image')->values();
        $bienVideos = $proposition->bien->medias->where('type_media', 'video')->values();

        $lightboxImages = collect()
            ->merge($images->map(fn($m) => asset('storage/' . $m->fichier)))
            ->merge($bienImages->map(fn($m) => asset('storage/' . $m->fichier)))
            ->values()
            ->all();

        $hasConditions = $proposition->caution_mois !== null
            || $proposition->avance_loyer_mois !== null
            || $proposition->honoraires !== null
            || $proposition->charges !== null
            || $proposition->disponibilite !== null
            || $proposition->conditions_particulieres !== null;
    @endphp

    {{-- ═══ ALERTS ═══ --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ═══ HERO ═══ --}}
    <header class="offre-hero {{ $isVedette ? 'is-vedette' : '' }}">

        <div class="offre-hero__badges">
            <span class="offre-status offre-status--{{ $statut }}">
                <i class="fa-solid fa-circle"></i> {{ $statutLabel }}
            </span>
            @if($isVedette)
                <span class="offre-badge offre-badge--vedette">
                    <i class="fa-solid fa-star"></i> Vedette
                </span>
            @endif
        </div>

        <h1 class="offre-hero__title">
            {{ $proposition->demande->type_bien->label() }}
            <span class="offre-hero__title-sep">—</span>
            {{ $proposition->demande->zone_recherchee }}
        </h1>

        <div class="offre-hero__meta">
            <span>
                <i class="fa-solid fa-wallet"></i>
                <strong>{{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }} {{ $proposition->demande->type_operation->value === 'location' ? 'F/mois' : 'F' }}</strong>
            </span>
            <span class="offre-hero__meta-sep">•</span>
            <span>
                <i class="fa-regular fa-calendar"></i>
                Envoyée le {{ $proposition->created_at->format('d/m/Y') }}
            </span>
        </div>

        @if($score)
            <div class="offre-hero__score">
                <div class="score-ring score-ring--lg score-ring--{{ $scoreClass }}"
                     style="--score: {{ $score }};">
                    <svg viewBox="0 0 36 36" aria-hidden="true">
                        <circle class="score-ring__bg" cx="18" cy="18" r="16"/>
                        <circle class="score-ring__fill" cx="18" cy="18" r="16"/>
                    </svg>
                    <span class="score-ring__value">{{ $score }}<small>%</small></span>
                </div>
                <div class="offre-hero__score-text">
                    <span class="score-label score-label--{{ $scoreClass }}">
                        {{ $score >= 80 ? 'Excellent match' : ($score >= 60 ? 'Bon match' : 'Match faible') }}
                    </span>
                    <span class="offre-hero__score-hint">Compatibilité</span>
                </div>
            </div>
        @endif
    </header>

    {{-- ═══ CLIENT (coordonnées masquées) ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-user"></i> Client
    </div>

    <div class="client-card">
        <div class="client-card__avatar">{{ $initial }}</div>

        <div class="client-card__info">
            <h3>{{ $prenom }} {{ $nom }}</h3>
            <p class="client-card__note">
                <i class="fa-solid fa-shield-halved"></i>
                 Les coordonnées seront visibles après confirmation du rendez-vous
            </p>
        </div>
    </div>

    {{-- Prix proposé --}}
    <div class="prix-card">
        <span class="prix-card__label">Prix proposé</span>
        <span class="prix-card__value">
            {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
            <small>FCFA</small>
        </span>
        @if($isVedette)
            <span class="prix-card__vedette">
                <i class="fa-solid fa-star"></i> Bien en vedette
            </span>
        @endif
    </div>

    {{-- ═══ CONDITIONS COMMERCIALES ═══ --}}
    @if($hasConditions)
        <div class="ios-section-title">
            <i class="fa-solid fa-file-signature"></i> Conditions commerciales
        </div>

        {{-- Récap caution --}}
        @if($proposition->caution_mois !== null)
            <div class="conditions-recap">
                <div class="recap-card recap-card--info">
                    <div class="recap-card__icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="recap-card__body">
                        <span class="recap-card__label">À l'avance</span>
                        <span class="recap-card__value">
                            {{ $proposition->caution_mois > 0 ? $proposition->caution_mois . ' mois' : 'Aucune' }}
                        </span>
                    </div>
                </div>

                <div class="recap-card recap-card--success">
                    <div class="recap-card__icon">
                        <i class="fa-solid fa-rotate-left"></i>
                    </div>
                    <div class="recap-card__body">
                        <span class="recap-card__label">À la sortie</span>
                        <span class="recap-card__value">
                            {{ $proposition->caution_mois > 0 ? $proposition->caution_mois . ' mois' : '—' }}
                        </span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Détails --}}
        <div class="ios-list">
            @if($proposition->disponibilite)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Disponibilité</span>
                    </div>
                    <span class="ios-list__value">
                        {{ $proposition->disponibilite_label }}
                    </span>
                </div>
            @endif

            @if($proposition->caution_mois !== null)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Caution</span>
                    </div>
                    <span class="ios-list__value">
                        {{ $proposition->caution_label ?? 'Aucune' }}
                    </span>
                </div>
            @endif

            @if($proposition->avance_loyer_mois !== null)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Avance de loyer</span>
                    </div>
                    <span class="ios-list__value">
                        {{ $proposition->avance_label ?? 'Aucune' }}
                    </span>
                </div>
            @endif

            @if($proposition->honoraires !== null)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Honoraires</span>
                    </div>
                    <span class="ios-list__value">
                        {{ $proposition->honoraires_formate }}
                    </span>
                </div>
            @endif

            @if($proposition->charges !== null)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Charges</span>
                    </div>
                    <span class="ios-list__value">
                        {{ $proposition->charges_formate }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Coût total --}}
        @if($proposition->cout_entree_total > 0)
            <div class="conditions-total">
                <div class="conditions-total__left">
                    <i class="fa-solid fa-coins"></i>
                    <div>
                        <span class="conditions-total__label">Coût total à l'entrée</span>
                        <span class="conditions-total__hint">Caution + Avance + Honoraires + Charges</span>
                    </div>
                </div>
                <span class="conditions-total__value">
                    {{ $proposition->cout_entree_total_formate }}
                </span>
            </div>
        @endif

        {{-- Notes --}}
        @if($proposition->conditions_particulieres)
            <blockquote class="quote-block quote-block--rust">
                {{ $proposition->conditions_particulieres }}
            </blockquote>
        @endif
    @endif

    {{-- ═══ MESSAGE ═══ --}}
    @if($proposition->message)
        <div class="ios-section-title">
            <i class="fa-regular fa-message"></i> Message envoyé
        </div>
        <blockquote class="quote-block">
            {{ $proposition->message }}
        </blockquote>
    @endif

    {{-- ═══ PHOTOS PROPOSITION ═══ --}}
    @if($images->count() > 0)
        <div class="ios-section-title">
            <i class="fa-regular fa-image"></i> Photos de la proposition
            <span class="ios-section-count">{{ $images->count() }}</span>
        </div>

        <div class="gallery-grid">
            @foreach($images as $index => $media)
                @php $url = asset('storage/' . $media->fichier); @endphp
                <div class="gallery-item" onclick="openLightbox('{{ $url }}', {{ $index }})">
                    <img src="{{ $url }}" alt="Photo" loading="lazy">
                    @if($isVedette)
                        <span class="gallery-item__badge">
                            <i class="fa-solid fa-star"></i>
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ VIDÉOS PROPOSITION ═══ --}}
    @if($videos->count() > 0)
        <div class="ios-section-title">
            <i class="fa-regular fa-circle-play"></i> Vidéos de la proposition
            <span class="ios-section-count">{{ $videos->count() }}</span>
        </div>

        <div class="video-grid">
            @foreach($videos as $media)
                <div class="video-tile">
                    <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata"></video>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ BIEN PROPOSÉ ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-building"></i> Bien proposé
        @if($isVedette)
            <span class="ios-section-count ios-section-count--gold">
                <i class="fa-solid fa-star"></i> Vedette
            </span>
        @endif
    </div>

    <div class="ios-list">
        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Titre</span>
            </div>
            <span class="ios-list__value">{{ $proposition->bien->titre }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Type</span>
            </div>
            <span class="ios-list__value">{{ $proposition->bien->type_bien->label() }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Contrat</span>
            </div>
            <span class="ios-list__value">{{ $proposition->bien->type_contrat->label() }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Surface</span>
            </div>
            <span class="ios-list__value">{{ $proposition->bien->surface }} m²</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Chambres</span>
            </div>
            <span class="ios-list__value">{{ $proposition->bien->nombre_chambres }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Salles de bain</span>
            </div>
            <span class="ios-list__value">{{ $proposition->bien->nombre_salles_bain }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Parking</span>
            </div>
            <span class="ios-list__value">
                <i class="fa-solid {{ $proposition->bien->parking_disponible ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                {{ $proposition->bien->parking_disponible ? 'Disponible' : 'Non' }}
            </span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Meublé</span>
            </div>
            <span class="ios-list__value">
                <i class="fa-solid {{ $proposition->bien->est_meuble ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                {{ $proposition->bien->est_meuble ? 'Oui' : 'Non' }}
            </span>
        </div>
    </div>

    {{-- Adresse --}}
    @if($proposition->bien->adresse)
        <div class="address-card">
            <i class="fa-solid fa-location-dot"></i>
            <span>{{ $proposition->bien->adresse }}</span>
        </div>
    @endif

    {{-- Équipements --}}
    @php $equipementsBien = $proposition->bien->equipements ?? []; @endphp
    @if(count($equipementsBien) > 0)
        <div class="ios-section-title">
            <i class="fa-solid fa-cogs"></i> Équipements
            <span class="ios-section-count">{{ count($equipementsBien) }}</span>
        </div>

        <div class="equipements-grid">
            @foreach($equipementsBien as $equipement)
                <span class="equipement-pill">
                    <i class="fa-solid fa-check-circle"></i> {{ $equipement }}
                </span>
            @endforeach
        </div>
    @endif

    {{-- Description --}}
    @if($proposition->bien->description)
        <div class="ios-section-title">
            <i class="fa-regular fa-file-lines"></i> Description du bien
        </div>
        <blockquote class="quote-block quote-block--rust">
            {{ $proposition->bien->description }}
        </blockquote>
    @endif

    {{-- ═══ PHOTOS BIEN PLATEFORME ═══ --}}
    @if($bienImages->count() > 0)
        <div class="ios-section-title">
            <i class="fa-regular fa-image"></i> Photos du bien sur la plateforme
            <span class="ios-section-count">{{ $bienImages->count() }}</span>
        </div>

        <div class="gallery-grid">
            @foreach($bienImages as $index => $media)
                @php
                    $url = asset('storage/' . $media->fichier);
                    $globalIndex = $images->count() + $index;
                @endphp
                <div class="gallery-item" onclick="openLightbox('{{ $url }}', {{ $globalIndex }})">
                    <img src="{{ $url }}" alt="Photo du bien" loading="lazy">
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ VIDÉOS BIEN PLATEFORME ═══ --}}
    @if($bienVideos->count() > 0)
        <div class="ios-section-title">
            <i class="fa-regular fa-circle-play"></i> Vidéos du bien sur la plateforme
            <span class="ios-section-count">{{ $bienVideos->count() }}</span>
        </div>

        <div class="video-grid">
            @foreach($bienVideos as $media)
                <div class="video-tile">
                    <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata"></video>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ ACTIONS FIXES ═══ --}}
    <div class="ios-form-actions">
        <a href="{{ route('agence.propositions.index') }}" class="ios-btn ios-btn--ghost">
            <i class="fa-solid fa-list"></i>
            Toutes mes offres
        </a>

        @if($statut === 'en_attente')
            <form action="{{ route('agence.propositions.annuler', $proposition) }}" method="POST" class="ios-form-actions__form">
                @csrf
                <button type="submit" class="ios-btn ios-btn--danger"
                        onclick="return confirm('Annuler cette proposition ?')">
                    <i class="fa-solid fa-xmark"></i>
                    Annuler
                </button>
            </form>
        @endif
    </div>
</div>

{{-- ═══ LIGHTBOX ═══ --}}
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
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

    function openLightbox(src, index) {
        if (typeof index === 'number' && index >= 0 && index < LIGHTBOX_IMAGES.length) {
            currentIndex = index;
        } else {
            const found = LIGHTBOX_IMAGES.indexOf(src);
            currentIndex = found >= 0 ? found : 0;
        }
        renderLightbox();
        document.getElementById('lightbox').classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function navigateLightbox(direction) {
        if (LIGHTBOX_IMAGES.length === 0) return;
        currentIndex = (currentIndex + direction + LIGHTBOX_IMAGES.length) % LIGHTBOX_IMAGES.length;
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
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DÉTAIL OFFRE (Agence) — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.offre-detail {
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

/* ═══ ALERTS ═══ */
.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 14px;
    border-left: 4px solid;
    font-size: 13.5px;
    font-weight: 500;
}
.alert-success { background: var(--c-success-bg); color: var(--c-success); border-left-color: var(--c-success); }
.alert-error   { background: var(--c-danger-bg);  color: var(--c-danger);  border-left-color: var(--c-danger); }

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
.ios-section-count--gold {
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
}

/* ═══ HERO ═══ */
.offre-hero {
    padding: 20px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    position: relative;
    overflow: hidden;
}
.offre-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--muted);
}
.offre-hero.is-vedette::before {
    background: linear-gradient(90deg, var(--c-gold), #E8901A);
}
.offre-hero.is-vedette {
    background: linear-gradient(135deg, #FFFDF5 0%, #fff 60%);
}

.offre-hero__badges {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.offre-hero__title {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    line-height: 1.25;
    margin: 0 0 10px;
    color: var(--ink);
    letter-spacing: -.3px;
}
.offre-hero__title-sep {
    color: var(--muted);
    font-weight: 400;
    margin: 0 4px;
}

.offre-hero__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 13px;
    color: var(--muted);
}
.offre-hero__meta i {
    font-size: 12px;
    margin-right: 5px;
    opacity: .8;
}
.offre-hero__meta strong { color: var(--ink); font-weight: 600; }
.offre-hero__meta-sep { opacity: .4; }

.offre-hero__score {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 0.5px dashed var(--border);
}
.offre-hero__score-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.offre-hero__score-hint {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}

/* ═══ SCORE RING ═══ */
.score-ring {
    position: relative;
    width: 60px;
    height: 60px;
}
.score-ring--lg { width: 72px; height: 72px; }
.score-ring svg {
    transform: rotate(-90deg);
    width: 100%; height: 100%;
    display: block;
}
.score-ring circle { fill: none; stroke-width: 3; }
.score-ring__bg { stroke: var(--border); }
.score-ring__fill {
    stroke-linecap: round;
    stroke-dasharray: calc(var(--score, 0) * 1.005) 100.5;
    transition: stroke-dasharray .6s ease;
}
.score-ring--excellent .score-ring__fill { stroke: var(--c-success); }
.score-ring--moyen     .score-ring__fill { stroke: var(--c-warning); }
.score-ring--faible    .score-ring__fill { stroke: var(--c-danger); }

.score-ring__value {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-weight: 800;
    line-height: 1;
    font-size: 14px;
}
.score-ring--lg .score-ring__value { font-size: 18px; }
.score-ring--excellent .score-ring__value { color: var(--c-success); }
.score-ring--moyen     .score-ring__value { color: var(--c-warning); }
.score-ring--faible    .score-ring__value { color: var(--c-danger); }
.score-ring__value small { font-size: 9px; margin-left: 1px; font-weight: 700; }
.score-ring--lg .score-ring__value small { font-size: 11px; }

.score-label {
    font-size: 12px;
    font-weight: 700;
}
.score-label--excellent { color: var(--c-success); }
.score-label--moyen     { color: var(--c-warning); }
.score-label--faible    { color: var(--c-danger); }

/* ═══ STATUTS ═══ */
.offre-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.offre-status i { font-size: 6px; }
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.offre-status--terminee   { background: var(--c-info-bg);    color: var(--c-info); }

.offre-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}
.offre-badge--vedette {
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    box-shadow: 0 2px 6px rgba(245, 166, 35, .3);
}

/* ═══ CLIENT CARD ═══ */
.client-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-left: 3px solid var(--rust);
    border-radius: var(--radius);
    margin-bottom: 14px;
}
.client-card__avatar {
    width: 54px;
    height: 54px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    flex-shrink: 0;
}
.client-card__info { flex: 1; min-width: 0; }
.client-card__info h3 {
    font-family: var(--display);
    font-size: 16.5px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
}

/* ═══ NOTE CLIENT (coordonnées masquées) ═══ */
.client-card__note {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 6px 0 0;
    font-size: 12.5px;
    color: var(--muted);
    font-style: italic;
    line-height: 1.5;
}
.client-card__note i {
    font-size: 12px;
    color: var(--rust);
    opacity: .8;
    font-style: normal;
}

/* ═══ PRIX CARD ═══ */
.prix-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 16px 20px;
    background: linear-gradient(135deg, #FFF9F5 0%, #fff 60%);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.prix-card__label {
    font-size: 12.5px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
}
.prix-card__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
    letter-spacing: -.4px;
}
.prix-card__value small {
    font-size: 13px;
    font-weight: 600;
    opacity: .7;
    margin-left: 3px;
}
.prix-card__vedette {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    background: #FFF8E1;
    color: #E8901A;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.prix-card__vedette i { font-size: 10px; color: var(--c-gold); }

/* ═══ CONDITIONS ═══ */
.conditions-recap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 14px;
}
.recap-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid transparent;
}
.recap-card--info {
    background: var(--c-info-bg);
    border-color: #BBDEFB;
}
.recap-card--success {
    background: var(--c-success-bg);
    border-color: #C8E6C9;
}
.recap-card__icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
}
.recap-card--info    .recap-card__icon { color: var(--c-info); }
.recap-card--success .recap-card__icon { color: var(--c-success); }
.recap-card__body { min-width: 0; }
.recap-card__label {
    display: block;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    opacity: .75;
    margin-bottom: 2px;
}
.recap-card--info    .recap-card__label { color: var(--c-info); }
.recap-card--success .recap-card__label { color: var(--c-success); }
.recap-card__value {
    display: block;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.2;
}

.conditions-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 18px;
    background: linear-gradient(135deg, #FFF4EC 0%, #FFE8D9 100%);
    border: 1px solid #FFD4B8;
    border-radius: 14px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.conditions-total__left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}
.conditions-total__left > i {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
}
.conditions-total__label {
    display: block;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.2;
}
.conditions-total__hint {
    display: block;
    font-size: 11.5px;
    color: var(--muted);
    margin-top: 2px;
}
.conditions-total__value {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    letter-spacing: -.4px;
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
.ios-list__value .is-yes { color: var(--c-success); margin-right: 4px; }
.ios-list__value .is-no  { color: var(--c-danger);  margin-right: 4px; }

/* ═══ ADRESSE ═══ */
.address-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    font-size: 14px;
    color: var(--ink);
}
.address-card i {
    width: 36px; height: 36px;
    border-radius: 9px;
    background: var(--c-warning-bg);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}

/* ═══ QUOTES ═══ */
.quote-block {
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
}
.quote-block--rust {
    background: linear-gradient(135deg, rgba(180, 83, 42, .06), rgba(180, 83, 42, .02));
    font-style: italic;
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
    transition: transform .2s, box-shadow .2s;
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
.gallery-item__badge {
    position: absolute;
    top: 8px; left: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    font-size: 10px;
    box-shadow: 0 2px 6px rgba(245, 166, 35, .4);
    z-index: 2;
}

/* ═══ VIDÉOS ═══ */
.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 10px;
    margin-bottom: 14px;
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
    gap: 5px;
    padding: 6px 13px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border: 1px solid #BBDEFB;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}
.equipement-pill i { font-size: 10px; }

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
.ios-form-actions__form .ios-btn { width: 100%; }
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
    flex: 0 0 auto;
    padding: 12px 18px;
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--danger {
    background: #fff;
    color: var(--c-danger);
    border-color: #FFCDD2;
}
.ios-btn--danger:hover { background: var(--c-danger-bg); border-color: var(--c-danger); }
.ios-btn--danger:active { transform: scale(.97); }

/* ═══ LIGHTBOX ═══ */
.lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, .94);
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
    top: 20px; right: 24px;
    width: 42px; height: 42px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .12);
    color: #fff;
    border: none;
    font-size: 26px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
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
    display: flex;
    align-items: center;
    justify-content: center;
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

    .offre-detail { background: transparent; padding-top: 4px; }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }

    /* ─── HERO iOS ─── */
    .offre-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 20px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .offre-hero::before {
        left: 0; right: 0;
        height: 3px;
        border-radius: 0;
    }
    .offre-hero.is-vedette {
        background: linear-gradient(135deg, #FFFDF5 0%, #fff 40%);
    }
    .offre-hero__badges { gap: 6px; margin-bottom: 8px; }
    .offre-status {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
    }
    .offre-status i { font-size: 7px; }
    .offre-badge { font-size: 11px; padding: 4px 10px; text-transform: none; letter-spacing: 0; }

    .offre-hero__title {
        font-size: 22px;
        letter-spacing: -.4px;
        color: var(--ios-label);
        font-weight: 700;
        margin: 0 0 8px;
    }
    .offre-hero__meta {
        font-size: 13.5px;
        color: var(--ios-label-3);
        gap: 6px;
    }
    .offre-hero__meta i { color: var(--ios-label-3); }
    .offre-hero__meta strong { color: var(--ios-label); }
    .offre-hero__meta-sep { display: none; }

    .offre-hero__score {
        gap: 12px;
        padding-top: 14px;
        margin-top: 14px;
        border-top: 0.5px solid var(--ios-separator);
    }
    .score-ring--lg { width: 64px; height: 64px; }
    .score-ring--lg .score-ring__value { font-size: 16px; }
    .score-label { font-size: 14px; font-weight: 700; }
    .offre-hero__score-hint {
        font-size: 12px;
        color: var(--ios-label-3);
        text-transform: none;
        letter-spacing: 0;
        font-weight: 400;
    }

    /* ─── CLIENT iOS ─── */
    .client-card {
        background: var(--ios-card);
        border: none;
        border-left: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
        position: relative;
        overflow: hidden;
    }
    .client-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: var(--rust);
        border-radius: 0 3px 3px 0;
    }
    .client-card__avatar { width: 52px; height: 52px; font-size: 20px; }
    .client-card__info h3 { font-size: 17px; font-weight: 700; letter-spacing: -.2px; margin: 0; }
    .client-card__note {
        font-size: 13px;
        margin-top: 6px;
        color: var(--ios-label-3);
    }

    /* ─── PRIX iOS ─── */
    .prix-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    .prix-card__label {
        font-size: 12px;
        text-transform: none;
        letter-spacing: 0;
        color: var(--ios-label-3);
        font-weight: 500;
    }
    .prix-card__value {
        font-size: 26px;
        letter-spacing: -.5px;
        align-self: flex-end;
    }

    /* ─── CONDITIONS iOS ─── */
    .conditions-recap { grid-template-columns: 1fr 1fr; gap: 8px; }
    .recap-card { padding: 12px; gap: 10px; }
    .recap-card__icon { width: 36px; height: 36px; font-size: 14px; border-radius: 9px; }
    .recap-card__label { font-size: 10px; }
    .recap-card__value { font-size: 14px; }

    .conditions-total {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
    }
    .conditions-total__left > i {
        width: 38px; height: 38px;
    }
    .conditions-total__label { font-size: 14px; }
    .conditions-total__value {
        font-size: 22px;
        align-self: flex-end;
        letter-spacing: -.4px;
    }

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

    /* ─── ADRESSE iOS ─── */
    .address-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
        font-size: 14px;
        gap: 10px;
    }
    .address-card i {
        width: 34px; height: 34px;
        border-radius: 9px;
    }

    /* ─── QUOTES iOS ─── */
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
    .quote-block--rust {
        background: var(--ios-card);
    }

    /* ─── GALERIE iOS ─── */
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .gallery-item { border-radius: 12px; border: none; }

    /* ─── VIDÉOS iOS ─── */
    .video-grid { grid-template-columns: 1fr; gap: 10px; margin-bottom: 12px; }
    .video-tile { border-radius: 12px; border: none; }

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
    .equipement-pill i { color: var(--c-success); font-size: 12px; }

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

    .offre-detail {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }

    /* ─── LIGHTBOX iOS ─── */
    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 14px; right: 14px; width: 40px; height: 40px; font-size: 22px; }
}

@media (max-width: 480px) {
    .offre-hero { padding: 16px; }
    .offre-hero__title { font-size: 20px; }

    .score-ring--lg { width: 58px; height: 58px; }
    .score-ring--lg .score-ring__value { font-size: 15px; }
    .score-ring--lg .score-ring__value small { font-size: 10px; }

    .client-card__avatar { width: 46px; height: 46px; font-size: 18px; }
    .client-card__info h3 { font-size: 16px; }

    .prix-card__value { font-size: 24px; }

    .conditions-recap { grid-template-columns: 1fr; }
    .conditions-total__value { font-size: 20px; }

    .ios-section-title { font-size: 16px; }
}
</style>
@endpush