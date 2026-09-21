@extends('layouts.dashboard')

@section('title', 'Détail de l\'offre — DoyaImmo')
@section('page_title', 'Détail de l\'offre')
@section('page_sub', 'Informations complètes sur la proposition')

@section('content')
<div class="view active offre-show">

    @php
        use App\Enums\StatutPropositionEnum;
        use App\Enums\StatutRendezVousEnum;

        $bien       = $proposition->bien;
        $agence     = $proposition->agence;
        $agenceUser = $agence->user ?? null;
        $isVedette  = $bien && $bien->est_vedette;

        $statutValue = is_object($proposition->statut)
            ? $proposition->statut->value
            : (string) $proposition->statut;
        $statutLabel = is_object($proposition->statut)
            ? $proposition->statut->label()
            : ucfirst($statutValue);

        $hasRdvActif = $proposition->rendezVous()
            ->whereIn('statut', [
                StatutRendezVousEnum::PLANIFIE->value,
                StatutRendezVousEnum::CONFIRME->value,
            ])
            ->exists();

        $hasAnyRdv = $proposition->rendezVous()->exists();

        $score = (int) ($proposition->score_matching ?? 0);
        $scoreClass = $score >= 80 ? 'excellent' : ($score >= 60 ? 'moyen' : 'faible');
        $scoreLabel = $score >= 80 ? 'Excellent match' : ($score >= 60 ? 'Bon match' : 'Match faible');

        $isLocation = is_object($proposition->demande->type_operation)
            && $proposition->demande->type_operation->value === 'location';
        $budgetLabel = $isLocation ? 'F/mois' : 'F';

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

        $noteAgence = (float) ($agence->note_moyenne ?? 0);
        $nbAvis = $agence->evaluations->count() ?? 0;

        $quartierBien = $bien->quartier ?? null;

        $hasConditions = $proposition->caution_mois !== null
            || $proposition->avance_loyer_mois !== null
            || $proposition->honoraires !== null
            || $proposition->charges !== null
            || $proposition->disponibilite !== null
            || $proposition->conditions_particulieres !== null;
    @endphp

    {{-- ═══ HERO ═══ --}}
    <header class="offre-hero">

        <div class="offre-hero__badges">
            <span class="offre-status offre-status--{{ $statutValue }}">
                {{ $statutLabel }}
            </span>
            @if($isVedette)
                <span class="offre-badge offre-badge--vedette">
                    En vedette
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
                <strong>{{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }} {{ $budgetLabel }}</strong>
            </span>
            <span class="offre-hero__meta-sep">•</span>
            <span>
                <i class="fa-regular fa-calendar"></i>
                Reçue le {{ $proposition->created_at->format('d/m/Y') }}
            </span>
        </div>

        @if($score)
            <div class="offre-hero__score">
                <div class="score-ring score-ring--lg score-ring--{{ $scoreClass }}"
                     style="--score: {{ $score }};"
                     role="img"
                     aria-label="Score : {{ $score }}%">
                    <svg viewBox="0 0 36 36" aria-hidden="true">
                        <circle class="score-ring__bg" cx="18" cy="18" r="16"/>
                        <circle class="score-ring__fill" cx="18" cy="18" r="16"/>
                    </svg>
                    <span class="score-ring__value">{{ $score }}<small>%</small></span>
                </div>
                <div class="offre-hero__score-text">
                    <span class="score-label score-label--{{ $scoreClass }}">{{ $scoreLabel }}</span>
                    <span class="offre-hero__score-hint">Compatibilité</span>
                </div>
            </div>
        @endif
    </header>

    {{-- ═══ AGENCE + PRIX ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-building"></i> Agence
    </div>

    <div class="panel panel--agency">
        <div class="agency-block">
            <div class="agency-block__avatar">{{ $initial }}</div>

            <div class="agency-block__info">
                <h3 class="agency-block__name">{{ $agence->nom_agence ?? 'Agence' }}</h3>

                <div class="agency-block__rating">
                    <i class="fa-solid fa-star"></i>
                    <strong>{{ number_format($noteAgence, 1) }}</strong>
                    <span class="rating-max">/ 5</span>
                    <span class="rating-reviews">({{ $nbAvis }} avis)</span>
                </div>

                @if($agence->adresse)
                    <div class="agency-block__contact">
                        <span>
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $agence->adresse }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <div class="agency-block__price">
            <div class="price-info">
                <span class="price-label">Prix proposé</span>
                @if($quartierBien)
                    <span class="price-quartier">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $quartierBien }}
                    </span>
                @endif
            </div>
            <span class="price-value">
                {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                <small>FCFA</small>
            </span>
        </div>
    </div>

    {{-- ═══ CONDITIONS COMMERCIALES ═══ --}}
    @if($hasConditions)
        <div class="ios-section-title">
            <i class="fa-solid fa-file-signature"></i> Conditions commerciales
        </div>

        <div class="panel panel--conditions">
            @if($proposition->caution_mois !== null)
                <div class="conditions-recap">
                    <div class="recap-card recap-card--info">
                        <div class="recap-card__icon">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div class="recap-card__body">
                            <span class="recap-card__label">Caution demandée</span>
                            <span class="recap-card__value">
                                {{ $proposition->caution_mois > 0 ? $proposition->caution_mois . ' mois' : 'Aucune' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="conditions-grid">
                @if($proposition->disponibilite)
                    <div class="condition-item">
                        <span class="condition-item__label">Disponibilité</span>
                        <span class="condition-item__value">{{ $proposition->disponibilite_label }}</span>
                    </div>
                @endif

                @if($proposition->caution_mois !== null)
                    <div class="condition-item">
                        <span class="condition-item__label">Caution</span>
                        <span class="condition-item__value">{{ $proposition->caution_label ?? 'Aucune' }}</span>
                    </div>
                @endif

                @if($proposition->avance_loyer_mois !== null)
                    <div class="condition-item">
                        <span class="condition-item__label">Avance loyer</span>
                        <span class="condition-item__value">{{ $proposition->avance_label ?? 'Aucune' }}</span>
                    </div>
                @endif

                @if($proposition->honoraires !== null)
                    <div class="condition-item">
                        <span class="condition-item__label">Honoraires</span>
                        <span class="condition-item__value">{{ $proposition->honoraires_formate }}</span>
                    </div>
                @endif

                @if($proposition->charges !== null)
                    <div class="condition-item">
                        <span class="condition-item__label">Charges</span>
                        <span class="condition-item__value">{{ $proposition->charges_formate }}</span>
                    </div>
                @endif
            </div>

            @if($proposition->cout_entree_total > 0)
                <div class="conditions-total">
                    <div class="conditions-total__left">
                        <i class="fa-solid fa-coins"></i>
                        <div>
                            <span class="conditions-total__label">Coût total à l'entrée</span>
                        </div>
                    </div>
                    <span class="conditions-total__value">
                        {{ $proposition->cout_entree_total_formate }}
                    </span>
                </div>
            @endif

            @if($proposition->conditions_particulieres)
                <div class="conditions-notes">
                    <span class="conditions-notes__label">Conditions particulières</span>
                    <p class="conditions-notes__text">{{ $proposition->conditions_particulieres }}</p>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══ GALERIE ═══ --}}
    @if($propImages->count() > 0 || $propVideos->count() > 0)
        <div class="ios-section-title">
            <i class="fa-regular fa-images"></i> Médias du bien
            <span class="ios-section-count">{{ $propImages->count() + $propVideos->count() }}</span>
        </div>

        @if($propImages->count() > 0)
            <div class="gallery-grid">
                @foreach($propImages as $index => $media)
                    @php $url = asset('storage/' . $media->fichier); @endphp
                    <div class="gallery-item" onclick="openLightbox('{{ $url }}', {{ $index }})">
                        <img src="{{ $url }}" alt="Photo" loading="lazy">
                    </div>
                @endforeach
            </div>
        @endif

        @if($propVideos->count() > 0)
            <div class="video-section">
                <h5 class="video-section__title">
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
    @else
        <div class="ios-section-title">
            <i class="fa-regular fa-images"></i> Médias du bien
        </div>
        <div class="empty-gallery">
            <i class="fa-regular fa-image"></i>
            <span>Aucun média disponible pour ce bien</span>
        </div>
    @endif

    {{-- ═══ MESSAGE ═══ --}}
    @if($proposition->message)
        <div class="ios-section-title">
            <i class="fa-regular fa-message"></i> Message de l'agence
        </div>
        <blockquote class="message-block">
            {{ $proposition->message }}
        </blockquote>
    @endif

    {{-- ═══ CARACTÉRISTIQUES DU BIEN ═══ --}}
    @if($bien)
        <div class="ios-section-title">
            <i class="fa-regular fa-list-check"></i> Caractéristiques du bien
        </div>

        <div class="ios-list">
            <div class="ios-list__item">
                <span class="ios-list__title">Titre</span>
                <span class="ios-list__value">{{ $bien->titre ?? '—' }}</span>
            </div>

            <div class="ios-list__item">
                <span class="ios-list__title">Type</span>
                <span class="ios-list__value">
                    {{ is_object($bien->type_bien) ? $bien->type_bien->label() : ($bien->type_bien ?? '—') }}
                </span>
            </div>

            <div class="ios-list__item">
                <span class="ios-list__title">Contrat</span>
                <span class="ios-list__value">
                    {{ is_object($bien->type_contrat) ? $bien->type_contrat->label() : ($bien->type_contrat ?? '—') }}
                </span>
            </div>

            <div class="ios-list__item">
                <span class="ios-list__title">Surface</span>
                <span class="ios-list__value">{{ $bien->surface ?? '—' }} m²</span>
            </div>

            <div class="ios-list__item">
                <span class="ios-list__title">Chambres</span>
                <span class="ios-list__value">{{ $bien->nombre_chambres ?? '—' }}</span>
            </div>

            <div class="ios-list__item">
                <span class="ios-list__title">Salles de bain</span>
                <span class="ios-list__value">{{ $bien->nombre_salles_bain ?? '—' }}</span>
            </div>

            <div class="ios-list__item">
                <span class="ios-list__title">Parking</span>
                <span class="ios-list__value">{{ $bien->parking_disponible ? 'Oui' : 'Non' }}</span>
            </div>

            <div class="ios-list__item">
                <span class="ios-list__title">Meublé</span>
                <span class="ios-list__value">{{ $bien->est_meuble ? 'Oui' : 'Non' }}</span>
            </div>
        </div>

        @if($bien->adresse)
            <div class="panel panel--address">
                <div class="address-block">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ $bien->adresse }}</span>
                </div>
            </div>
        @endif

        @if(count($equipementsBien) > 0)
            <div class="ios-section-title">
                <i class="fa-solid fa-cogs"></i> Équipements
                <span class="ios-section-count">{{ count($equipementsBien) }}</span>
            </div>
            <div class="equipements-grid">
                @foreach($equipementsBien as $equipement)
                    <span class="equipement-pill">{{ $equipement }}</span>
                @endforeach
            </div>
        @endif

        @if($bien->description)
            <div class="ios-section-title">
                <i class="fa-regular fa-file-lines"></i> Description
            </div>
            <blockquote class="quote-block">
                {{ $bien->description }}
            </blockquote>
        @endif
    @endif

    {{-- ═══ ACTIONS ═══ --}}
    <footer class="offre-actions">
        <div class="offre-actions__main">

            @if($statutValue === 'en_attente')
                <form action="{{ route('particulier.propositions.choisir-et-planifier', $proposition) }}"
                      method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-rust"
                            onclick="return confirm('Choisir cette agence et planifier une visite ?\n\nLes autres offres seront automatiquement refusées.')">
                        Choisir et planifier
                    </button>
                </form>

                <form action="{{ route('particulier.propositions.refuser', $proposition) }}"
                      method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('Refuser cette offre ?')">
                        Refuser
                    </button>
                </form>

            @elseif($statutValue === 'acceptee')
                <span class="state-pill state-pill--success">
                    Offre acceptée
                </span>

                @if($hasRdvActif)
                    <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                        Voir la visite planifiée
                    </a>
                @else
                    <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-rust">
                        Planifier la visite
                    </a>

                    <form action="{{ route('particulier.propositions.annuler-selection', $proposition) }}"
                          method="POST" class="inline-form">
                        @csrf
                        <button type="submit" class="btn btn-ghost-danger"
                                onclick="return confirm('Annuler l\'acceptation ?\n\nLa demande repassera en attente.\nLes offres refusées automatiquement seront réactivées.\nVos refus volontaires resteront bloqués.')">
                            Annuler l'acceptation
                        </button>
                    </form>
                @endif

            @elseif($statutValue === 'refusee')
                <span class="state-pill state-pill--muted">
                    Offre ignorée
                </span>

            @elseif($statutValue === 'terminee')
                <span class="state-pill state-pill--info">
                    Offre terminée
                </span>
            @endif
        </div>

        <a href="{{ route('particulier.signalements.create-proposition', $proposition) }}"
           class="btn btn-ghost-danger offre-actions__report">
            Signaler
        </a>
    </footer>
</div>

{{-- ═══ LIGHTBOX ═══ --}}
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
        if (currentIndex > 0) { currentIndex--; renderLightbox(); }
    }

    function nextImage() {
        if (currentIndex < LIGHTBOX_IMAGES.length - 1) { currentIndex++; renderLightbox(); }
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
   DÉTAIL OFFRE — Design épuré
   ═══════════════════════════════════════════════════════════ */

.offre-show {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #F1F8F4;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --c-info:       #1E40AF;
    --c-info-bg:    #EFF6FF;
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
.ios-section-title i { color: var(--muted); font-size: 15px; }
.ios-section-title .ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    background: #F5F5F7;
    color: var(--muted);
    border-radius: 999px;
}

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
}

.offre-hero__badges {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
    width: 100%;
}

.offre-hero__title {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 10px;
    color: var(--text);
    line-height: 1.25;
    width: 100%;
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
    width: 100%;
}
.offre-hero__meta i { font-size: 12px; margin-right: 5px; }
.offre-hero__meta strong { color: var(--text); font-weight: 600; }
.offre-hero__meta-sep { opacity: .4; }

.offre-hero__score {
    display: flex;
    align-items: center;
    gap: 14px;
}

/* ═══ SCORE RING ═══ */
.score-ring {
    position: relative;
    width: 60px;
    height: 60px;
}
.score-ring--lg { width: 80px; height: 80px; }
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
.score-ring--lg .score-ring__value { font-size: 20px; }
.score-ring--excellent .score-ring__value { color: var(--c-success); }
.score-ring--moyen     .score-ring__value { color: var(--c-warning); }
.score-ring--faible    .score-ring__value { color: var(--c-danger); }
.score-ring__value small { font-size: 9px; margin-left: 1px; font-weight: 700; }
.score-ring--lg .score-ring__value small { font-size: 12px; }

.offre-hero__score-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.offre-hero__score-hint {
    font-size: 11px;
    font-weight: 500;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
}

.score-label {
    font-size: 12px;
    font-weight: 700;
}
.score-label--excellent { color: var(--c-success); }
.score-label--moyen     { color: var(--c-warning); }
.score-label--faible    { color: var(--c-danger); }

/* ═══ BADGES ═══ */
.offre-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    background: #F5F5F7;
    color: #4A5260;
}
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.offre-status--terminee   { background: var(--c-info-bg);    color: var(--c-info); }

.offre-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}
.offre-badge--vedette {
    background: #FFF8E1;
    color: #A05A00;
}

/* ═══ PANEL ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 22px;
    margin-bottom: 14px;
}

/* ═══ AGENCE ═══ */
.agency-block {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 14px;
}

.agency-block__avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    flex-shrink: 0;
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
    gap: 6px;
    margin-bottom: 6px;
    font-size: 13px;
    flex-wrap: wrap;
}
.agency-block__rating i { color: var(--c-gold); font-size: 13px; }
.agency-block__rating strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
}
.rating-max { color: var(--muted); font-size: 12px; }
.rating-reviews { color: var(--muted); font-size: 12px; }

.agency-block__contact {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 14px;
    font-size: 12.5px;
    color: var(--text-soft);
}
.agency-block__contact span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.agency-block__contact i { color: var(--muted); font-size: 11px; }

/* Prix + quartier */
.agency-block__price {
    text-align: right;
    flex-shrink: 0;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
    margin-top: 4px;
}
.price-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
    min-width: 0;
}
.price-label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
    display: block;
}
.price-quartier {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--text-soft);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}
.price-quartier i {
    color: var(--muted);
    font-size: 11px;
    flex-shrink: 0;
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

/* ═══ CONDITIONS ═══ */
.conditions-recap {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 16px;
}

.recap-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 12px;
    background: #F5F5F7;
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
    color: var(--muted);
}

.recap-card__body { min-width: 0; }
.recap-card__label {
    display: block;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--muted);
    margin-bottom: 2px;
}
.recap-card__value {
    display: block;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    line-height: 1.2;
}

.conditions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
    margin-bottom: 16px;
}

.condition-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 12px 14px;
    background: #F5F5F7;
    border-radius: 10px;
}
.condition-item__label {
    font-size: 11px;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
}
.condition-item__value {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
}

.conditions-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 18px;
    background: #F9FAFB;
    border: 1px solid var(--border);
    border-radius: 12px;
    margin-bottom: 16px;
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
    background: var(--rust);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.conditions-total__label {
    display: block;
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    color: var(--text);
    line-height: 1.2;
}
.conditions-total__value {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
}

.conditions-notes {
    padding-top: 16px;
    border-top: 1px solid var(--border);
}
.conditions-notes__label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-soft);
    text-transform: uppercase;
    letter-spacing: .4px;
    margin-bottom: 8px;
}
.conditions-notes__text {
    font-size: 13.5px;
    color: var(--text-soft);
    line-height: 1.7;
    margin: 0;
    padding: 12px 16px;
    background: #F9FAFB;
    border-radius: 10px;
    border-left: 3px solid var(--rust);
    font-style: italic;
}

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
    background: #F5F5F7;
    border: 1px solid var(--border);
}
.gallery-item img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}

.video-section { margin-top: 8px; }
.video-section__title {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
    margin: 0 0 10px;
}
.video-section__count {
    padding: 1px 8px;
    border-radius: 999px;
    background: #F5F5F7;
    color: var(--muted);
    font-size: 10.5px;
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

.empty-gallery {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 30px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
    color: var(--muted);
    font-size: 13px;
    margin-bottom: 14px;
}
.empty-gallery i { font-size: 28px; opacity: .4; }

/* ═══ MESSAGE ═══ */
.message-block {
    padding: 16px 20px;
    border-radius: 12px;
    background: #F9FAFB;
    font-size: 14px;
    line-height: 1.75;
    color: var(--text);
    font-style: italic;
    margin: 0 0 14px;
    border-left: 3px solid var(--rust);
}

/* ═══ LISTE iOS ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 14px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
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
.ios-list__title {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
    text-align: right;
    flex-shrink: 0;
}

/* Adresse */
.panel--address {
    padding: 14px 18px;
    margin-bottom: 14px;
}
.address-block {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: var(--text);
}
.address-block i {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: #F5F5F7;
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

/* Équipements */
.equipements-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 14px;
}
.equipement-pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 13px;
    background: #F5F5F7;
    color: var(--text);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
}

/* Quote */
.quote-block {
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    line-height: 1.75;
    color: var(--text);
    margin: 0 0 14px;
    border-left: 3px solid var(--rust);
    background: #F9FAFB;
    font-style: italic;
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
    margin-top: 8px;
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
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    background: #F5F5F7;
    color: var(--text-soft);
}
.state-pill--success { background: var(--c-success-bg); color: var(--c-success); }
.state-pill--muted   { background: #F5F5F7;             color: var(--muted); }
.state-pill--info    { background: var(--c-info-bg);    color: var(--c-info); }

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
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; border-color: #9A4523; color: #fff; }

.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: #F5F5F7; color: var(--text); }

.btn-danger-soft { background: transparent; color: var(--c-danger); border-color: var(--border); }
.btn-danger-soft:hover { background: var(--c-danger-bg); border-color: #FECACA; }

.btn-ghost-danger { background: transparent; color: var(--c-danger); border-color: var(--border); }
.btn-ghost-danger:hover { background: var(--c-danger-bg); border-color: #FECACA; }

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
}
.lightbox__counter {
    color: #fff;
    font-size: 13px;
    font-weight: 600;
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
}
.lightbox__close:hover { background: rgba(255, 255, 255, .25); }

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
}
.lightbox__nav:hover { background: rgba(255, 255, 255, .28); }
.lightbox__nav--prev { left: 24px; }
.lightbox__nav--next { right: 24px; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .offre-show { background: transparent; padding-top: 4px; }

    .offre-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 20px;
        margin-bottom: 12px;
        flex-direction: column;
        align-items: stretch;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .offre-hero__badges { gap: 6px; margin-bottom: 6px; }
    .offre-status {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
    }
    .offre-badge { font-size: 11px; padding: 4px 10px; text-transform: none; letter-spacing: 0; }

    .offre-hero__title {
        font-size: 22px;
        letter-spacing: -.4px;
        color: var(--ios-label);
        font-weight: 700;
        margin: 0 0 8px;
    }
    .offre-hero__title-sep { color: var(--ios-label-3); }

    .offre-hero__meta {
        font-size: 13.5px;
        color: var(--ios-label-3);
        gap: 8px;
    }
    .offre-hero__meta strong { color: var(--ios-label); }
    .offre-hero__meta-sep { display: none; }

    .offre-hero__score {
        gap: 12px;
        padding-top: 14px;
        border-top: 0.5px solid var(--ios-separator);
        margin-top: 4px;
    }
    .score-ring--lg { width: 72px; height: 72px; }
    .score-ring--lg .score-ring__value { font-size: 18px; }
    .score-label { font-size: 14px; font-weight: 700; }
    .offre-hero__score-hint {
        font-size: 12px;
        color: var(--ios-label-3);
        text-transform: none;
        letter-spacing: 0;
        font-weight: 400;
    }

    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; color: var(--muted); }
    .ios-section-title .ios-section-count {
        font-size: 11px;
        padding: 3px 10px;
    }

    .panel {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .agency-block {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 0;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .agency-block__avatar {
        width: 52px;
        height: 52px;
        font-size: 20px;
        background: #F5F5F7;
    }
    .agency-block__info { width: 100%; }
    .agency-block__name { font-size: 18px; font-weight: 700; letter-spacing: -.2px; }
    .agency-block__rating { font-size: 13px; }
    .agency-block__contact {
        gap: 8px 14px;
        font-size: 13px;
        margin-top: 8px;
    }

    .agency-block__price {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 14px;
        margin-top: 14px;
        border-top: none;
        width: 100%;
        text-align: left;
    }
    .price-info { gap: 6px; }
    .price-label {
        font-size: 13px;
        color: var(--ios-label-3);
        text-transform: none;
        letter-spacing: 0;
        font-weight: 500;
    }
    .price-quartier {
        font-size: 13px;
        color: var(--ios-label-2);
        font-weight: 500;
    }
    .price-quartier i { color: var(--muted); font-size: 12px; }
    .price-value {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -.5px;
    }

    .conditions-recap { grid-template-columns: 1fr 1fr; gap: 8px; }
    .recap-card { padding: 12px; gap: 10px; background: #F5F5F7; }
    .recap-card__icon { width: 36px; height: 36px; font-size: 14px; border-radius: 9px; }
    .recap-card__label { font-size: 10px; }
    .recap-card__value { font-size: 14px; }

    .conditions-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
    .condition-item {
        background: #EFEFF4;
        padding: 10px 12px;
        border-radius: 10px;
    }
    .condition-item__label { font-size: 10.5px; text-transform: none; letter-spacing: 0; }
    .condition-item__value { font-size: 13.5px; }

    .conditions-total {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 14px 16px;
        border-radius: 14px;
        background: #F9FAFB;
        border: 1px solid var(--ios-separator);
    }
    .conditions-total__left > i {
        width: 38px; height: 38px;
    }
    .conditions-total__label { font-size: 14px; }
    .conditions-total__value {
        font-size: 22px;
        text-align: left;
        align-self: flex-end;
    }

    .conditions-notes { border-top: 0.5px solid var(--ios-separator); }
    .conditions-notes__label { font-size: 12px; text-transform: none; letter-spacing: 0; color: var(--ios-label-2); }
    .conditions-notes__text {
        background: #F9FAFB;
        border-left: 3px solid var(--rust);
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 13.5px;
    }

    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .gallery-item { border-radius: 12px; border: none; }

    .video-section__title { font-size: 13px; color: var(--ios-label-2); font-weight: 600; }
    .video-grid { grid-template-columns: 1fr; gap: 10px; }
    .video-tile { border-radius: 12px; border: none; }

    .empty-gallery {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 32px 20px;
        font-size: 13.5px;
        color: var(--ios-label-3);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .message-block {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid var(--rust);
        border-radius: 12px;
        padding: 16px 18px;
        font-size: 14px;
        line-height: 1.65;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

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
    }
    .ios-list__value {
        font-size: 15px;
        font-weight: 500;
        color: var(--ios-label-3);
        text-align: right;
    }

    .panel--address { padding: 14px 16px; }
    .address-block { font-size: 14px; gap: 10px; }
    .address-block i {
        width: 34px; height: 34px;
        background: #F5F5F7;
    }

    .equipements-grid { gap: 8px; }
    .equipement-pill {
        background: #F5F5F7;
        color: var(--ios-label);
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 500;
    }

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
    }

    .offre-actions {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
        margin-bottom: 8px;
    }
    .offre-actions__main {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    .offre-actions__main .btn,
    .offre-actions__main form,
    .offre-actions__main .inline-form,
    .offre-actions__main .state-pill {
        width: 100%;
        justify-content: center;
    }
    .offre-actions__main .inline-form .btn { width: 100%; justify-content: center; }
    .offre-actions__report { margin-left: 0; width: 100%; justify-content: center; }

    .state-pill {
        padding: 14px 16px;
        font-size: 14px;
        border-radius: 12px;
        justify-content: center;
    }

    .btn {
        padding: 12px 18px;
        font-size: 14px;
        border-radius: 12px;
        font-weight: 600;
    }
    .btn-rust {
        background: var(--rust);
        border: none;
    }
    .btn-ghost {
        background: #F5F5F7;
        border: none;
        color: var(--ios-blue);
    }
    .btn-ghost-danger {
        background: transparent;
        border: 1px solid #FECACA;
        color: var(--c-danger);
    }
    .btn-danger-soft {
        background: transparent;
        border: 1px solid #FECACA;
        color: var(--c-danger);
    }

    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 14px; right: 14px; width: 40px; height: 40px; font-size: 22px; }
}

@media (max-width: 480px) {
    .offre-hero { padding: 16px; }
    .offre-hero__title { font-size: 20px; }

    .score-ring--lg { width: 64px; height: 64px; }
    .score-ring--lg .score-ring__value { font-size: 16px; }
    .score-ring--lg .score-ring__value small { font-size: 10px; }

    .conditions-recap { grid-template-columns: 1fr; }
    .conditions-grid { grid-template-columns: 1fr; }
    .conditions-total__value { font-size: 20px; }

    .panel { padding: 14px; }

    .agency-block__name { font-size: 17px; }
    .price-value { font-size: 22px; }

    .gallery-grid { gap: 6px; }
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