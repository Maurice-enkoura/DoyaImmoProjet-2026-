@extends('layouts.dashboard')

@section('title', 'Détail de l\'offre — DoyaImmo')
@section('page_title', 'Détail de l\'offre')
@section('page_sub', 'Informations complètes sur la proposition')

@section('content')
<div class="view active offre-show">

    @php
        $bien     = $proposition->bien;
        $agence   = $proposition->agence;
        $agenceUser = $agence->user ?? null;
        $isVedette = $bien && $bien->est_vedette;

        // Statut
        $statutValue = is_object($proposition->statut) ? $proposition->statut->value : $proposition->statut;
        $statusLabels = [
            'en_attente' => 'En attente',
            'acceptee'   => 'Acceptée',
            'refusee'    => 'Refusée',
            'terminee'   => 'Terminée',
        ];
        $statutLabel = $statusLabels[$statutValue] ?? $statutValue;

        // Score
        $score = (int) ($proposition->score_matching ?? 0);
        $scoreClass = $score >= 80 ? 'excellent' : ($score >= 60 ? 'moyen' : 'faible');
        $scoreLabel = $score >= 80 ? 'Excellent match' : ($score >= 60 ? 'Bon match' : 'Match faible');

        // Opération
        $isLocation = is_object($proposition->demande->type_operation)
            && $proposition->demande->type_operation->value === 'location';
        $budgetLabel = $isLocation ? 'F/mois' : 'F';

        // Médias (proposition en priorité, sinon bien)
        $propMedias = $proposition->medias ?? collect();
        $propImages = $propMedias->where('type_media', 'image')->values();
        $propVideos = $propMedias->where('type_media', 'video')->values();

        if ($propImages->count() === 0 && $propVideos->count() === 0 && $bien) {
            $bienMedias = $bien->medias ?? collect();
            $propImages = $bienMedias->where('type_media', 'image')->values();
            $propVideos = $bienMedias->where('type_media', 'video')->values();
        }

        $lightboxImages = $propImages->map(fn($m) => asset('storage/' . $m->fichier))->values()->all();

        $equipementsBien = $bien->equipements ?? [];

        $initial = strtoupper(mb_substr($agence->nom_agence ?? 'A', 0, 1));
    @endphp

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="offre-show__back">
        <a href="{{ route('particulier.propositions.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux offres
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         ALERTS
    ═══════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <header class="offre-hero offre-hero--{{ $statutValue }} {{ $isVedette ? 'is-vedette' : '' }}">

        <div class="offre-hero__main">
            <div class="offre-hero__badges">
                <span class="offre-status offre-status--{{ $statutValue }}">
                    <i class="fa-solid fa-circle"></i> {{ $statutLabel }}
                </span>
                @if($isVedette)
                    <span class="offre-badge offre-badge--vedette">
                        <i class="fa-solid fa-star"></i> Bien en vedette
                    </span>
                @endif
            </div>

            <h1 class="offre-hero__title">
                {{ is_object($proposition->demande->type_bien) ? $proposition->demande->type_bien->label() : $proposition->demande->type_bien }}
                <span class="offre-hero__title-sep">—</span>
                {{ $proposition->demande->zone_recherchee }}
            </h1>

            <div class="offre-hero__meta">
                <span>
                    <i class="fa-solid fa-wallet"></i>
                    Budget max
                    <strong>{{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }} {{ $budgetLabel }}</strong>
                </span>
                <span class="offre-hero__meta-sep">•</span>
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    Reçue le {{ $proposition->created_at->format('d/m/Y à H:i') }}
                </span>
            </div>
        </div>

        @if($score)
            <div class="offre-hero__score">
                <div class="score-ring score-ring--lg score-ring--{{ $scoreClass }}"
                     style="--score: {{ $score }};"
                     role="img"
                     aria-label="Score de compatibilité : {{ $score }}%">
                    <svg viewBox="0 0 36 36" aria-hidden="true">
                        <circle class="score-ring__bg" cx="18" cy="18" r="16"/>
                        <circle class="score-ring__fill" cx="18" cy="18" r="16"/>
                    </svg>
                    <span class="score-ring__value">{{ $score }}<small>%</small></span>
                </div>
                <span class="offre-hero__score-label">Compatibilité</span>
                <span class="score-label score-label--{{ $scoreClass }}">{{ $scoreLabel }}</span>
            </div>
        @endif
    </header>

    {{-- ═══════════════════════════════════════════
         AGENCE + PRIX
    ═══════════════════════════════════════════ --}}
    <section class="panel panel--agency">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-regular fa-building"></i>
            </span>
            <h4>Agence</h4>
        </header>

        <div class="agency-block">
            <div class="agency-block__avatar">{{ $initial }}</div>

            <div class="agency-block__info">
                <h3 class="agency-block__name">{{ $agence->nom_agence ?? 'Agence' }}</h3>

                <div class="agency-block__rating">
                    <span class="rating-stars">
                        <i class="fa-solid fa-star"></i>
                        <strong>{{ number_format($agence->note_moyenne ?? 0, 1) }}</strong>
                        <span class="rating-max">/ 5</span>
                    </span>
                    <span class="rating-reviews">
                        ({{ $agence->evaluations->count() ?? 0 }} avis)
                    </span>
                </div>

                <div class="agency-block__contact">
                    @if($agence->adresse)
                        <span><i class="fa-solid fa-location-dot"></i> {{ $agence->adresse }}</span>
                    @endif
                    @if($agenceUser && $agenceUser->telephone)
                        <a href="tel:{{ $agenceUser->telephone }}" class="contact-link">
                            <i class="fa-solid fa-phone"></i> {{ $agenceUser->telephone }}
                        </a>
                    @endif
                    @if($agenceUser && $agenceUser->email)
                        <a href="mailto:{{ $agenceUser->email }}" class="contact-link">
                            <i class="fa-solid fa-envelope"></i> {{ $agenceUser->email }}
                        </a>
                    @endif
                </div>
            </div>

            <div class="agency-block__price">
                <span class="price-label">Prix proposé</span>
                <span class="price-value">
                    {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                    <small>FCFA</small>
                </span>
                @if($isVedette)
                    <span class="price-vedette">
                        <i class="fa-solid fa-star"></i> Bien en vedette
                    </span>
                @endif
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         MÉDIAS
    ═══════════════════════════════════════════ --}}
    @if($propImages->count() > 0 || $propVideos->count() > 0)
        <section class="panel panel--gallery">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-images"></i>
                </span>
                <h4>Médias du bien</h4>
                <span class="panel__count">{{ $propImages->count() + $propVideos->count() }}</span>
            </header>

            @if($propImages->count() > 0)
                <div class="gallery-grid">
                    @foreach($propImages as $index => $media)
                        @php $url = asset('storage/' . $media->fichier); @endphp
                        <div class="gallery-item"
                             onclick="openLightbox('{{ $url }}', {{ $index }})">
                            <img src="{{ $url }}" alt="Photo du bien" loading="lazy">
                            <div class="gallery-item__overlay">
                                <i class="fa-solid fa-magnifying-glass-plus"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($propVideos->count() > 0)
                <div class="video-section">
                    <h5 class="video-section__title">
                        <i class="fa-regular fa-circle-play"></i>
                        Vidéos
                        <span class="video-section__count">{{ $propVideos->count() }}</span>
                    </h5>
                    <div class="video-grid">
                        @foreach($propVideos as $media)
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
    @else
        <section class="panel panel--gallery">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-images"></i>
                </span>
                <h4>Médias du bien</h4>
            </header>
            <div class="empty-gallery">
                <i class="fa-regular fa-image"></i>
                <span>Aucun média disponible pour ce bien</span>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         MESSAGE DE L'AGENCE
    ═══════════════════════════════════════════ --}}
    @if($proposition->message)
        <section class="panel panel--message">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-message"></i>
                </span>
                <h4>Message de l'agence</h4>
            </header>
            <blockquote class="message-block">
                {{ $proposition->message }}
            </blockquote>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         CARACTÉRISTIQUES DU BIEN
    ═══════════════════════════════════════════ --}}
    @if($bien)
        <section class="panel panel--bien">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-list-check"></i>
                </span>
                <h4>Caractéristiques du bien</h4>
                @if($isVedette)
                    <span class="offre-badge offre-badge--vedette offre-badge--sm">
                        <i class="fa-solid fa-star"></i> Vedette
                    </span>
                @endif
            </header>

            <div class="bien-grid">
                <div class="bien-grid__item">
                    <span class="bien-grid__label">Titre</span>
                    <span class="bien-grid__value">{{ $bien->titre ?? '—' }}</span>
                </div>
                <div class="bien-grid__item">
                    <span class="bien-grid__label">Type</span>
                    <span class="bien-grid__value">
                        {{ is_object($bien->type_bien) ? $bien->type_bien->label() : ($bien->type_bien ?? '—') }}
                    </span>
                </div>
                <div class="bien-grid__item">
                    <span class="bien-grid__label">Contrat</span>
                    <span class="bien-grid__value">
                        {{ is_object($bien->type_contrat) ? $bien->type_contrat->label() : ($bien->type_contrat ?? '—') }}
                    </span>
                </div>
                <div class="bien-grid__item">
                    <span class="bien-grid__label">Surface</span>
                    <span class="bien-grid__value">{{ $bien->surface ?? '—' }} m²</span>
                </div>
                <div class="bien-grid__item">
                    <span class="bien-grid__label">Chambres</span>
                    <span class="bien-grid__value">{{ $bien->nombre_chambres ?? '—' }}</span>
                </div>
                <div class="bien-grid__item">
                    <span class="bien-grid__label">Salles de bain</span>
                    <span class="bien-grid__value">{{ $bien->nombre_salles_bain ?? '—' }}</span>
                </div>
                <div class="bien-grid__item">
                    <span class="bien-grid__label">Parking</span>
                    <span class="bien-grid__value">
                        <i class="fa-solid {{ $bien->parking_disponible ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                        {{ $bien->parking_disponible ? 'Disponible' : 'Non' }}
                    </span>
                </div>
                <div class="bien-grid__item">
                    <span class="bien-grid__label">Meublé</span>
                    <span class="bien-grid__value">
                        <i class="fa-solid {{ $bien->est_meuble ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                        {{ $bien->est_meuble ? 'Oui' : 'Non' }}
                    </span>
                </div>
                <div class="bien-grid__item bien-grid__item--wide">
                    <span class="bien-grid__label">Adresse</span>
                    <span class="bien-grid__value">{{ $bien->adresse ?? 'Non spécifiée' }}</span>
                </div>
            </div>

            @if(count($equipementsBien) > 0)
                <div class="bien-equipements">
                    <h5 class="bien-equipements__title">
                        <i class="fa-solid fa-cogs"></i> Équipements
                    </h5>
                    <div class="bien-equipements__list">
                        @foreach($equipementsBien as $equipement)
                            <span class="equipement-pill">
                                <i class="fa-solid fa-check-circle"></i> {{ $equipement }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($bien->description)
                <div class="bien-description">
                    <h5 class="bien-description__title">
                        <i class="fa-regular fa-file-lines"></i> Description
                    </h5>
                    <p>{{ $bien->description }}</p>
                </div>
            @endif
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         ACTIONS
    ═══════════════════════════════════════════ --}}
    <footer class="offre-actions">
        <div class="offre-actions__main">
            @if($statutValue === 'en_attente')
                <form action="{{ route('particulier.propositions.selectionner', $proposition) }}"
                      method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-rust"
                            onclick="return confirm('✅ Sélectionner cette offre ? Cela clôturera les autres offres en attente.')">
                        <i class="fa-solid fa-check"></i> Choisir cette agence
                    </button>
                </form>

                <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-calendar-plus"></i> Planifier une visite
                </a>

                <form action="{{ route('particulier.propositions.refuser', $proposition) }}"
                      method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('❌ Refuser cette offre ?')">
                        <i class="fa-solid fa-times"></i> Refuser
                    </button>
                </form>

            @elseif($statutValue === 'acceptee')
                <span class="state-pill state-pill--success">
                    <i class="fa-solid fa-circle-check"></i> Offre acceptée
                </span>
                <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-rust">
                    <i class="fa-solid fa-calendar-check"></i> Planifier la visite
                </a>

            @elseif($statutValue === 'refusee')
                <span class="state-pill state-pill--muted">
                    <i class="fa-regular fa-eye-slash"></i> Offre ignorée
                </span>

            @elseif($statutValue === 'terminee')
                <span class="state-pill state-pill--info">
                    <i class="fa-solid fa-check-double"></i> Offre terminée
                </span>
            @endif
        </div>

        <a href="{{ route('particulier.signalements.create-proposition', $proposition) }}"
           class="btn btn-ghost-danger offre-actions__report">
            <i class="fa-solid fa-flag"></i> Signaler
        </a>
    </footer>
</div>

{{-- ═══════════════════════════════════════════
     LIGHTBOX
═══════════════════════════════════════════ --}}
<div id="lightbox" class="lightbox" onclick="closeLightboxOutside(event)">
    <button type="button" class="lightbox__close" onclick="closeLightbox()" aria-label="Fermer">&times;</button>

    <button type="button" class="lightbox__nav lightbox__nav--prev" onclick="prevImage()" aria-label="Précédent">
        <i class="fa-solid fa-chevron-left"></i>
    </button>

    <div class="lightbox__figure">
        <img id="lightboxImage" src="" alt="Agrandir">
        <div id="lightboxCounter" class="lightbox__counter"></div>
    </div>

    <button type="button" class="lightbox__nav lightbox__nav--next" onclick="nextImage()" aria-label="Suivant">
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

    function closeLightboxOutside(event) {
        if (event.target === event.currentTarget) closeLightbox();
    }

    function prevImage() {
        if (currentIndex > 0) {
            currentIndex--;
            renderLightbox();
        }
    }

    function nextImage() {
        if (currentIndex < LIGHTBOX_IMAGES.length - 1) {
            currentIndex++;
            renderLightbox();
        }
    }

    function renderLightbox() {
        const img     = document.getElementById('lightboxImage');
        const counter = document.getElementById('lightboxCounter');
        const prev    = document.querySelector('.lightbox__nav--prev');
        const next    = document.querySelector('.lightbox__nav--next');

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

    document.addEventListener('keydown', function (e) {
        const lb = document.getElementById('lightbox');
        if (!lb.classList.contains('is-open')) return;
        if (e.key === 'Escape')     closeLightbox();
        if (e.key === 'ArrowLeft')  prevImage();
        if (e.key === 'ArrowRight') nextImage();
    });

    // Swipe tactile
    let touchStartX = 0;
    document.getElementById('lightboxImage')?.addEventListener('touchstart', function (e) {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    document.getElementById('lightboxImage')?.addEventListener('touchend', function (e) {
        const diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) {
            if (diff > 0) nextImage();
            else prevImage();
        }
    }, { passive: true });
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL OFFRE — Particulier
   ═══════════════════════════════════════════════════════════ */

.offre-show {
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
}

.offre-show__back { margin-bottom: 16px; }

/* ═══ ALERTS ═══ */
.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-radius: 10px;
    margin-bottom: 14px;
    border-left: 4px solid;
    font-size: 13px;
    font-weight: 500;
}
.alert-success { background: var(--c-success-bg); color: var(--c-success); border-left-color: var(--c-success); }
.alert-error   { background: var(--c-danger-bg);  color: var(--c-danger);  border-left-color: var(--c-danger); }

/* ═══ HERO ═══ */
.offre-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 20px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    flex-wrap: wrap;
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
.offre-hero--en_attente::before { background: var(--c-warning); }
.offre-hero--acceptee::before   { background: var(--c-success); }
.offre-hero--refusee::before    { background: var(--c-danger); }
.offre-hero--terminee::before   { background: var(--c-info); }

.offre-hero.is-vedette {
    background: linear-gradient(135deg, #FFFDF5 0%, #fff 60%);
    border-color: var(--c-gold);
}

.offre-hero__main { flex: 1; min-width: 0; }

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
    margin: 0 0 10px;
    color: var(--text);
    line-height: 1.25;
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
.offre-hero__meta i { font-size: 12px; margin-right: 5px; opacity: .8; }
.offre-hero__meta strong { color: var(--text); font-weight: 600; }
.offre-hero__meta-sep { opacity: .4; }

.offre-hero__score {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
}
.offre-hero__score-label {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
}

/* ═══ SCORE RING ═══ */
.score-ring {
    position: relative;
    width: 60px;
    height: 60px;
}
.score-ring--lg { width: 88px; height: 88px; }
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
.score-ring--lg .score-ring__value { font-size: 22px; }
.score-ring--excellent .score-ring__value { color: var(--c-success); }
.score-ring--moyen     .score-ring__value { color: var(--c-warning); }
.score-ring--faible    .score-ring__value { color: var(--c-danger); }
.score-ring__value small { font-size: 9px; margin-left: 1px; font-weight: 700; }
.score-ring--lg .score-ring__value small { font-size: 13px; }

.score-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    text-align: center;
}
.score-label--excellent { color: var(--c-success); }
.score-label--moyen     { color: var(--c-warning); }
.score-label--faible    { color: var(--c-danger); }

/* ═══ STATUT / BADGES ═══ */
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
.offre-badge--sm { padding: 2px 9px; font-size: 10px; margin-left: 8px; }

/* ═══ PANNEAUX ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 22px;
    margin-bottom: 14px;
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
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: var(--c-warning-bg);
    color: var(--rust);
    font-size: 14px;
    flex-shrink: 0;
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

/* ═══ BLOC AGENCE ═══ */
.agency-block {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}

.agency-block__avatar {
    width: 56px;
    height: 56px;
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
    box-shadow: 0 6px 16px rgba(180, 83, 42, .2);
}

.agency-block__info { flex: 1; min-width: 200px; }
.agency-block__name {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--text);
}

.agency-block__rating {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
    font-size: 13px;
    flex-wrap: wrap;
}
.rating-stars { display: inline-flex; align-items: center; gap: 4px; }
.rating-stars i { color: var(--c-gold); font-size: 13px; }
.rating-stars strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 800;
    color: var(--text);
}
.rating-max { color: var(--muted); font-size: 12px; font-weight: 500; }
.rating-reviews { color: var(--muted); font-size: 12px; }

.agency-block__contact {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 14px;
    font-size: 12.5px;
    color: var(--text-soft);
}
.agency-block__contact span,
.agency-block__contact a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.agency-block__contact i { color: var(--rust); font-size: 11px; }
.contact-link {
    color: var(--text-soft);
    text-decoration: none;
    transition: color .2s;
}
.contact-link:hover { color: var(--rust); }

.agency-block__price {
    text-align: right;
    flex-shrink: 0;
    padding-left: 20px;
    border-left: 1px dashed var(--border);
}
.price-label {
    display: block;
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
    margin-bottom: 3px;
}
.price-value {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
}
.price-value small {
    font-size: 12px;
    font-weight: 600;
    opacity: .7;
    margin-left: 3px;
}
.price-vedette {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 6px;
    padding: 3px 10px;
    background: #FFF8E1;
    color: #E8901A;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.price-vedette i { font-size: 9px; }

/* ═══ GALERIE ═══ */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 8px;
    margin-bottom: 12px;
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
.gallery-item__overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, .4);
    color: #fff;
    font-size: 20px;
    opacity: 0;
    transition: opacity .2s;
}
.gallery-item:hover .gallery-item__overlay { opacity: 1; }

/* Vidéos */
.video-section { margin-top: 8px; }
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
.video-section__count {
    padding: 1px 8px;
    border-radius: 999px;
    background: var(--surface);
    color: var(--text-soft);
    font-size: 10.5px;
    letter-spacing: 0;
}
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

/* Galerie vide */
.empty-gallery {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 30px 20px;
    background: var(--surface);
    border-radius: 10px;
    border: 1px dashed var(--border);
    color: var(--muted);
    font-size: 13px;
}
.empty-gallery i { font-size: 28px; opacity: .4; }

/* ═══ MESSAGE ═══ */
.message-block {
    position: relative;
    padding: 16px 20px 16px 24px;
    border-radius: 12px;
    background: var(--surface);
    font-size: 14px;
    line-height: 1.75;
    color: var(--text);
    font-style: italic;
    margin: 0;
    border-left: 4px solid var(--rust);
}

/* ═══ GRILLE BIEN ═══ */
.bien-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
}
.bien-grid__item {
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding: 10px 14px;
    background: var(--surface);
    border-radius: 8px;
    border: 1px solid transparent;
    transition: border-color .2s;
}
.bien-grid__item:hover { border-color: var(--border); }
.bien-grid__item--wide {
    grid-column: 1 / -1;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
}
.bien-grid__label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}
.bien-grid__value {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text);
}
.bien-grid__item--wide .bien-grid__value {
    font-weight: 500;
    color: var(--text-soft);
}
.bien-grid__value .is-yes { color: var(--c-success); margin-right: 4px; }
.bien-grid__value .is-no  { color: var(--c-danger);  margin-right: 4px; }

/* Équipements */
.bien-equipements {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px dashed var(--border);
}
.bien-equipements__title {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    margin: 0 0 10px;
    color: var(--text-soft);
}
.bien-equipements__title i { color: var(--rust); margin-right: 6px; }
.bien-equipements__list {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.equipement-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border: 1px solid #BBDEFB;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
}
.equipement-pill i { font-size: 10px; }

/* Description */
.bien-description {
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px dashed var(--border);
}
.bien-description__title {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    margin: 0 0 8px;
    color: var(--text-soft);
}
.bien-description__title i { color: var(--rust); margin-right: 6px; }
.bien-description p {
    font-size: 13.5px;
    line-height: 1.7;
    color: var(--text-soft);
    margin: 0;
}

/* ═══ ACTIONS ═══ */
.offre-actions {
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
.offre-actions__main {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.offre-actions__report { margin-left: auto; }
.inline-form { display: inline; }

.state-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid;
}
.state-pill--success {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-color: #C8E6C9;
}
.state-pill--muted {
    background: var(--surface);
    color: var(--muted);
    border-color: var(--border);
}
.state-pill--info {
    background: var(--c-info-bg);
    color: var(--c-info);
    border-color: #BBDEFB;
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
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523;
    color: #fff;
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
.btn-danger-soft {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border-color: #FFCDD2;
}
.btn-danger-soft:hover {
    background: var(--c-danger);
    color: #fff;
    border-color: var(--c-danger);
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
.btn-sm { padding: 6px 13px; font-size: 12px; }

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
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
    max-width: 92vw;
    max-height: 92vh;
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
    line-height: 1;
    cursor: pointer;
    transition: background .2s, transform .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: sans-serif;
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

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .bien-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .offre-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 18px;
        gap: 14px;
    }
    .offre-hero__title { font-size: 18px; }
    .offre-hero__score {
        flex-direction: row;
        align-items: center;
        gap: 14px;
        align-self: stretch;
    }

    .panel { padding: 16px; }

    .agency-block { gap: 12px; }
    .agency-block__avatar { width: 48px; height: 48px; font-size: 18px; }
    .agency-block__price {
        width: 100%;
        text-align: left;
        padding-left: 0;
        padding-top: 12px;
        border-left: none;
        border-top: 1px dashed var(--border);
    }

    .gallery-grid { grid-template-columns: repeat(3, 1fr); }
    .video-grid { grid-template-columns: 1fr; }

    .bien-grid { grid-template-columns: 1fr; }
    .bien-grid__item--wide {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .offre-actions { flex-direction: column; align-items: stretch; }
    .offre-actions__main { flex-direction: column; align-items: stretch; }
    .offre-actions__main .btn,
    .offre-actions__main form,
    .offre-actions__main .inline-form,
    .offre-actions__main .state-pill { width: 100%; justify-content: center; }
    .offre-actions__main .inline-form .btn { width: 100%; justify-content: center; }
    .offre-actions__report { margin-left: 0; width: 100%; justify-content: center; }

    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 12px; right: 12px; width: 38px; height: 38px; font-size: 22px; }
}

@media (max-width: 480px) {
    .offre-hero { padding: 14px; }
    .offre-hero__title { font-size: 16px; }

    .score-ring--lg { width: 68px; height: 68px; }
    .score-ring--lg .score-ring__value { font-size: 17px; }
    .score-ring--lg .score-ring__value small { font-size: 10px; }

    .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 6px; }

    .agency-block__avatar { width: 44px; height: 44px; font-size: 16px; }
    .agency-block__name { font-size: 15px; }
    .price-value { font-size: 19px; }

    .panel { padding: 14px; }
    .panel__head h4 { font-size: 13.5px; }

    .message-block { padding: 14px 16px 14px 20px; font-size: 13px; }

    .btn { font-size: 12px; padding: 8px 14px; }
}
</style>
@endpush