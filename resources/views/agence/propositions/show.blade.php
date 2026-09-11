@extends('layouts.dashboard-agence')

@section('title', 'Détail de l\'offre — DoyaImmo')
@section('page_title', 'Détail de l\'offre')
@section('page_sub', 'Informations complètes sur la proposition')

@section('content')
<div class="view active offre-detail">

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="offre-detail__back">
        <a href="{{ route('agence.propositions.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux offres
        </a>
    </div>

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

    @php
        $isVedette  = $proposition->bien && $proposition->bien->est_vedette;
        $statut     = $proposition->statut->value;
        $score      = $proposition->score_matching;
        $scoreClass = $score >= 80 ? 'excellent' : ($score >= 60 ? 'moyen' : 'faible');

        $prenom  = $proposition->particulier->user->prenom ?? '';
        $nom     = $proposition->particulier->user->nom    ?? '';
        $initial = strtoupper(mb_substr($prenom ?: 'C', 0, 1));

        $images     = $proposition->medias->where('type_media', 'image')->values();
        $videos     = $proposition->medias->where('type_media', 'video')->values();
        $bienImages = $proposition->bien->medias->where('type_media', 'image')->values();
        $bienVideos = $proposition->bien->medias->where('type_media', 'video')->values();

        // URLs pour la lightbox (proposition + bien)
        $lightboxImages = collect()
            ->merge($images->map(fn($m) => asset('storage/' . $m->fichier)))
            ->merge($bienImages->map(fn($m) => asset('storage/' . $m->fichier)))
            ->values()
            ->all();
    @endphp

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <header class="offre-hero {{ $isVedette ? 'is-vedette' : '' }}">
        <div class="offre-hero__main">
            <div class="offre-hero__badges">
                <span class="offre-status offre-status--{{ $statut }}">
                    <i class="fa-solid fa-circle"></i> {{ $proposition->statut->label() }}
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
                    Budget max
                    <strong>
                        {{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }}
                        {{ $proposition->demande->type_operation->value === 'location' ? 'F/mois' : 'F' }}
                    </strong>
                </span>
                <span class="offre-hero__meta-sep">•</span>
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    Envoyée le {{ $proposition->created_at->format('d/m/Y à H:i') }}
                </span>
            </div>
        </div>

        @if($score)
            <div class="offre-hero__score">
                <div class="score-ring score-ring--lg score-ring--{{ $scoreClass }}"
                     style="--score: {{ $score }};"
                     role="img" aria-label="Score de compatibilité : {{ $score }}%">
                    <svg viewBox="0 0 36 36" aria-hidden="true">
                        <circle class="score-ring__bg" cx="18" cy="18" r="16"/>
                        <circle class="score-ring__fill" cx="18" cy="18" r="16"/>
                    </svg>
                    <span class="score-ring__value">{{ $score }}<small>%</small></span>
                </div>
                <span class="offre-hero__score-label">Compatibilité</span>
                @if($proposition->niveau_matching)
                    <span class="score-label score-label--{{ $scoreClass }}">
                        {{ $proposition->niveau_matching }}
                    </span>
                @endif
            </div>
        @endif
    </header>

    {{-- ═══════════════════════════════════════════
         CARTE CLIENT
    ═══════════════════════════════════════════ --}}
    <section class="client-card">
        <div class="client-card__avatar">{{ $initial }}</div>

        <div class="client-card__info">
            <h3>{{ $prenom }} {{ $nom }}</h3>
            <div class="client-card__contact">
                @if($proposition->particulier->user->email ?? false)
                    <span><i class="fa-solid fa-envelope"></i> {{ $proposition->particulier->user->email }}</span>
                @endif
                @if($proposition->particulier->user->telephone ?? false)
                    <span><i class="fa-solid fa-phone"></i> {{ $proposition->particulier->user->telephone }}</span>
                @else
                    <span class="is-muted"><i class="fa-solid fa-phone"></i> Non renseigné</span>
                @endif
            </div>
        </div>

        <div class="client-card__prix">
            <span class="client-card__prix-label">Prix proposé</span>
            <span class="client-card__prix-value">
                {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                <small>FCFA</small>
            </span>
            @if($isVedette)
                <span class="client-card__vedette">
                    <i class="fa-solid fa-star"></i> Bien en vedette
                </span>
            @endif
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         MESSAGE
    ═══════════════════════════════════════════ --}}
    @if($proposition->message)
        <section class="info-block">
            <header class="info-block__head">
                <i class="fa-regular fa-message"></i>
                <h4>Votre message</h4>
            </header>
            <div class="info-block__body info-block__body--quote">
                {{ $proposition->message }}
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         PHOTOS DE LA PROPOSITION
    ═══════════════════════════════════════════ --}}
    @if($images->count() > 0)
        <section class="info-block">
            <header class="info-block__head">
                <i class="fa-regular fa-image"></i>
                <h4>Photos de la proposition</h4>
                <span class="info-block__count">{{ $images->count() }}</span>
            </header>

            <div class="media-grid">
                @foreach($images->take(6) as $index => $media)
                    @php $url = asset('storage/' . $media->fichier); @endphp
                    <div class="media-tile {{ $index === 0 ? 'media-tile--hero' : '' }}"
                         onclick="openLightbox('{{ $url }}', {{ $index }})">
                        <img src="{{ $url }}" alt="Photo de la proposition" loading="lazy">
                        @if($isVedette)
                            <span class="media-tile__badge">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif
                    </div>
                @endforeach

                @if($images->count() > 6)
                    <div class="media-tile media-tile--more"
                         onclick="openLightbox('{{ asset('storage/' . $images->skip(6)->first()->fichier) }}', 6)">
                        <span class="media-tile__more-count">+{{ $images->count() - 6 }}</span>
                        <span class="media-tile__more-label">photos</span>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         VIDÉOS DE LA PROPOSITION
    ═══════════════════════════════════════════ --}}
    @if($videos->count() > 0)
        <section class="info-block">
            <header class="info-block__head">
                <i class="fa-regular fa-circle-play"></i>
                <h4>Vidéos de la proposition</h4>
                <span class="info-block__count">{{ $videos->count() }}</span>
            </header>

            <div class="video-grid">
                @foreach($videos as $media)
                    <div class="video-tile">
                        <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata"></video>
                        @if($isVedette)
                            <span class="media-tile__badge">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         BIEN PROPOSÉ
    ═══════════════════════════════════════════ --}}
    <section class="info-block">
        <header class="info-block__head">
            <i class="fa-regular fa-building"></i>
            <h4>Bien proposé</h4>
            @if($isVedette)
                <span class="offre-badge offre-badge--vedette offre-badge--sm">
                    <i class="fa-solid fa-star"></i> Vedette
                </span>
            @endif
        </header>

        <div class="bien-grid">
            <div class="bien-grid__item">
                <span class="bien-grid__label">Titre</span>
                <span class="bien-grid__value">{{ $proposition->bien->titre }}</span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Type</span>
                <span class="bien-grid__value">{{ $proposition->bien->type_bien->label() }}</span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Contrat</span>
                <span class="bien-grid__value">{{ $proposition->bien->type_contrat->label() }}</span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Surface</span>
                <span class="bien-grid__value">{{ $proposition->bien->surface }} m²</span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Chambres</span>
                <span class="bien-grid__value">{{ $proposition->bien->nombre_chambres }}</span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Salles de bain</span>
                <span class="bien-grid__value">{{ $proposition->bien->nombre_salles_bain }}</span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Parking</span>
                <span class="bien-grid__value">
                    <i class="fa-solid {{ $proposition->bien->parking_disponible ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                    {{ $proposition->bien->parking_disponible ? 'Disponible' : 'Non' }}
                </span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Meublé</span>
                <span class="bien-grid__value">
                    <i class="fa-solid {{ $proposition->bien->est_meuble ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                    {{ $proposition->bien->est_meuble ? 'Oui' : 'Non' }}
                </span>
            </div>
            <div class="bien-grid__item bien-grid__item--wide">
                <span class="bien-grid__label">Adresse</span>
                <span class="bien-grid__value">{{ $proposition->bien->adresse }}</span>
            </div>
        </div>

        @php $equipementsBien = $proposition->bien->equipements ?? []; @endphp
        @if(count($equipementsBien) > 0)
            <div class="bien-equipements">
                <h5 class="bien-equipements__title">
                    <i class="fa-solid fa-cogs"></i> Équipements du bien
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

        @if($proposition->bien->description)
            <div class="bien-description">
                <h5 class="bien-description__title">
                    <i class="fa-regular fa-file-lines"></i> Description du bien
                </h5>
                <p>{{ $proposition->bien->description }}</p>
            </div>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════
         PHOTOS DU BIEN (plateforme)
    ═══════════════════════════════════════════ --}}
    @if($bienImages->count() > 0)
        <section class="info-block info-block--alt">
            <header class="info-block__head">
                <i class="fa-regular fa-image"></i>
                <h4>Photos du bien sur la plateforme</h4>
                <span class="info-block__count">{{ $bienImages->count() }}</span>
            </header>

            <div class="media-grid">
                @foreach($bienImages->take(6) as $index => $media)
                    @php
                        $url = asset('storage/' . $media->fichier);
                        $globalIndex = $images->count() + $index;
                    @endphp
                    <div class="media-tile {{ $index === 0 ? 'media-tile--hero' : '' }}"
                         onclick="openLightbox('{{ $url }}', {{ $globalIndex }})">
                        <img src="{{ $url }}" alt="Photo du bien" loading="lazy">
                    </div>
                @endforeach

                @if($bienImages->count() > 6)
                    <div class="media-tile media-tile--more"
                         onclick="openLightbox('{{ asset('storage/' . $bienImages->skip(6)->first()->fichier) }}', {{ $images->count() + 6 }})">
                        <span class="media-tile__more-count">+{{ $bienImages->count() - 6 }}</span>
                        <span class="media-tile__more-label">photos</span>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         VIDÉOS DU BIEN (plateforme)
    ═══════════════════════════════════════════ --}}
    @if($bienVideos->count() > 0)
        <section class="info-block info-block--alt">
            <header class="info-block__head">
                <i class="fa-regular fa-circle-play"></i>
                <h4>Vidéos du bien sur la plateforme</h4>
                <span class="info-block__count">{{ $bienVideos->count() }}</span>
            </header>

            <div class="video-grid">
                @foreach($bienVideos as $media)
                    <div class="video-tile">
                        <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata"></video>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         ACTIONS
    ═══════════════════════════════════════════ --}}
    <footer class="offre-actions">
        @if($statut === 'en_attente')
            <form action="{{ route('agence.propositions.annuler', $proposition) }}" method="POST" class="offre-actions__form">
                @csrf
                <button type="submit" class="btn btn-danger-soft"
                        onclick="return confirm('Annuler cette proposition ?')">
                    <i class="fa-solid fa-xmark"></i> Annuler
                </button>
            </form>
        @endif

        @if($isVedette)
            <span class="offre-actions__vedette">
                <i class="fa-solid fa-star" style="color:#F5A623;"></i> Bien en vedette
            </span>
        @endif

        <a href="{{ route('agence.propositions.index') }}" class="btn btn-ghost offre-actions__right">
            <i class="fa-solid fa-list"></i> Toutes mes offres
        </a>
    </footer>
</div>

{{-- ═══════════════════════════════════════════
     LIGHTBOX avec navigation ‹ ›
═══════════════════════════════════════════ --}}
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

<script>
    // Liste de toutes les images (proposition + bien) pour la navigation ‹ ›
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

        // Compteur "1 / N"
        if (LIGHTBOX_IMAGES.length > 1) {
            counter.textContent = (currentIndex + 1) + ' / ' + LIGHTBOX_IMAGES.length;
        } else {
            counter.textContent = '';
        }

        // Cacher les flèches s'il n'y a qu'une image
        const hide = LIGHTBOX_IMAGES.length <= 1;
        prev.style.display = hide ? 'none' : 'flex';
        next.style.display = hide ? 'none' : 'flex';
    }

    // Navigation clavier : ← → Escape
    document.addEventListener('keydown', function (e) {
        const lb = document.getElementById('lightbox');
        if (!lb.classList.contains('is-open')) return;

        if (e.key === 'Escape')     closeLightbox();
        if (e.key === 'ArrowLeft')  navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });
</script>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL OFFRE
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
    --surface:      #F7F9FC;
    --radius:       14px;
}

.offre-detail__back { margin-bottom: 16px; }

/* ═══ ALERTS ═══ */
.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-radius: 10px;
    margin-bottom: 16px;
    border-left: 4px solid;
    font-size: 13px;
    font-weight: 500;
}
.alert-success { background: var(--c-success-bg); color: var(--c-success); border-left-color: var(--c-success); }
.alert-error   { background: var(--c-danger-bg);  color: var(--c-danger);  border-left-color: var(--c-danger); }

/* ═══ HERO ═══ */
.offre-hero {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 22px 26px;
    background: linear-gradient(135deg, #fff 0%, #FAFBFC 100%);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 18px;
    overflow: hidden;
}
.offre-hero.is-vedette {
    background: linear-gradient(135deg, #FFFDF5 0%, #fff 60%);
    border-color: #F5A623;
}
.offre-hero__main { position: relative; min-width: 0; flex: 1; }

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
    color: var(--text);
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
    font-weight: 600;
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
.score-ring--lg { width: 84px; height: 84px; }
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
.score-ring--lg .score-ring__value { font-size: 21px; }
.score-ring--excellent .score-ring__value { color: var(--c-success); }
.score-ring--moyen     .score-ring__value { color: var(--c-warning); }
.score-ring--faible    .score-ring__value { color: var(--c-danger); }
.score-ring__value small { font-size: 9px; margin-left: 1px; font-weight: 700; }
.score-ring--lg .score-ring__value small { font-size: 12px; }

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
    background: linear-gradient(135deg, #F5A623, #E8901A);
    color: #fff;
    box-shadow: 0 2px 6px rgba(245, 166, 35, .3);
}
.offre-badge--sm { padding: 2px 9px; font-size: 10px; margin-left: 8px; }

/* ═══ CARTE CLIENT ═══ */
.client-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 18px 22px;
    background: #fff;
    border: 1px solid var(--border);
    border-left: 4px solid var(--rust);
    border-radius: var(--radius);
    margin-bottom: 18px;
    flex-wrap: wrap;
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

.client-card__info { flex: 1; min-width: 160px; }
.client-card__info h3 {
    font-family: var(--display);
    font-size: 16.5px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--text);
}
.client-card__contact {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 16px;
    font-size: 12.5px;
    color: var(--muted);
}
.client-card__contact i {
    margin-right: 5px;
    font-size: 11px;
    opacity: .8;
}
.client-card__contact .is-muted { opacity: .6; font-style: italic; }

.client-card__prix {
    text-align: right;
    flex-shrink: 0;
    padding-left: 20px;
    border-left: 1px dashed var(--border);
}
.client-card__prix-label {
    display: block;
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
    margin-bottom: 2px;
}
.client-card__prix-value {
    display: block;
    font-family: var(--display);
    font-size: 22px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
}
.client-card__prix-value small {
    font-size: 12px;
    font-weight: 600;
    opacity: .7;
    margin-left: 3px;
}
.client-card__vedette {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 6px;
    padding: 2px 9px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: #E8901A;
    background: #FFF8E1;
    border-radius: 999px;
}

/* ═══ BLOCS INFO ═══ */
.info-block {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    margin-bottom: 18px;
}
.info-block--alt { background: #FAFBFC; }

.info-block__head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}
.info-block__head i { color: var(--rust); font-size: 15px; }
.info-block__head h4 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    flex: 1;
}
.info-block__count {
    font-size: 11px;
    font-weight: 700;
    padding: 2px 9px;
    background: var(--c-warning-bg);
    color: var(--rust);
    border-radius: 999px;
}

.info-block__body {
    font-size: 14px;
    color: var(--text-soft);
    line-height: 1.7;
}
.info-block__body--quote {
    padding: 14px 18px;
    background: var(--surface);
    border-radius: 10px;
    border-left: 3px solid var(--rust);
    font-style: italic;
}

/* ═══ GRILLE MÉDIAS ═══ */
.media-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    grid-auto-rows: 1fr;
    gap: 8px;
}
.media-tile {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    background: var(--surface);
    border: 1px solid var(--border);
    transition: transform .2s ease, box-shadow .2s ease;
}
.media-tile:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 16px rgba(0, 0, 0, .1);
}
.media-tile--hero {
    grid-column: span 2;
    grid-row: span 2;
    aspect-ratio: auto;
}
.media-tile img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.media-tile__badge {
    position: absolute;
    top: 8px; left: 8px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    color: #fff;
    background: linear-gradient(135deg, #F5A623, #E8901A);
    z-index: 2;
}
.media-tile__badge i { font-size: 8px; }

.media-tile--more {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: var(--ink, #1a1a1a);
    color: #fff;
    gap: 2px;
}
.media-tile__more-count { font-size: 22px; font-weight: 700; font-family: var(--display); }
.media-tile__more-label { font-size: 10px; opacity: .7; }

/* ═══ GRILLE VIDÉOS ═══ */
.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 12px;
}
.video-tile {
    position: relative;
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

/* ═══ GRILLE BIEN ═══ */
.bien-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
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

/* ═══ ÉQUIPEMENTS ═══ */
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
.equipement-pill i { font-size: 9px; }

/* ═══ DESCRIPTION ═══ */
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
    gap: 12px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    flex-wrap: wrap;
}
.offre-actions__form { display: inline; }
.offre-actions__vedette {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: #FFF8E1;
    color: #E65100;
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 600;
}
.offre-actions__right { margin-left: auto; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
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
.btn-sm { padding: 6px 14px; font-size: 12px; }

/* ═══ LIGHTBOX avec navigation ‹ › ═══ */
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
    .media-grid { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 768px) {
    .offre-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 18px;
    }
    .offre-hero__title { font-size: 19px; }
    .offre-hero__score {
        align-self: flex-start;
        flex-direction: row;
        gap: 14px;
        align-items: center;
    }

    .client-card { padding: 16px; gap: 14px; }
    .client-card__prix {
        width: 100%;
        text-align: left;
        padding-left: 0;
        padding-top: 12px;
        border-left: none;
        border-top: 1px dashed var(--border);
    }

    .info-block { padding: 16px; }

    .media-grid { grid-template-columns: repeat(3, 1fr); }
    .media-tile--hero { grid-column: span 2; grid-row: span 2; }

    .bien-grid { grid-template-columns: 1fr; }
    .bien-grid__item--wide {
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    .offre-actions__right { margin-left: 0; width: 100%; justify-content: center; }

    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 12px; right: 12px; width: 38px; height: 38px; font-size: 22px; }
}

@media (max-width: 480px) {
    .offre-hero { padding: 16px; }
    .offre-hero__title { font-size: 17px; }

    .score-ring--lg { width: 68px; height: 68px; }
    .score-ring--lg .score-ring__value { font-size: 17px; }
    .score-ring--lg .score-ring__value small { font-size: 10px; }

    .client-card__avatar { width: 46px; height: 46px; font-size: 18px; }
    .client-card__info h3 { font-size: 15px; }
    .client-card__prix-value { font-size: 19px; }

    .media-grid { grid-template-columns: repeat(2, 1fr); gap: 6px; }
    .media-tile--hero { grid-column: span 2; grid-row: span 2; }

    .video-grid { grid-template-columns: 1fr; }

    .info-block { padding: 14px; }
    .info-block__head h4 { font-size: 13.5px; }

    .btn { font-size: 11.5px; padding: 7px 13px; }
}
</style>
@endpush