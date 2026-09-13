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

        $statutValue = is_object($proposition->statut) ? $proposition->statut->value : $proposition->statut;
        $statusLabels = [
            'en_attente' => 'En attente',
            'acceptee'   => 'Acceptée',
            'refusee'    => 'Refusée',
            'terminee'   => 'Terminée',
        ];
        $statutLabel = $statusLabels[$statutValue] ?? $statutValue;

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
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ═══ HERO ═══ --}}
    <header class="offre-hero offre-hero--{{ $statutValue }} {{ $isVedette ? 'is-vedette' : '' }}">

        <div class="offre-hero__badges">
            <span class="offre-status offre-status--{{ $statutValue }}">
                <i class="fa-solid fa-circle"></i> {{ $statutLabel }}
            </span>
            @if($isVedette)
                <span class="offre-badge offre-badge--vedette">
                    <i class="fa-solid fa-star"></i> En vedette
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

        {{-- Score ring --}}
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

    {{-- ═══ CONDITIONS COMMERCIALES ═══ --}}
    @if($hasConditions)
        <div class="ios-section-title">
            <i class="fa-solid fa-file-signature"></i> Conditions commerciales
        </div>

        <div class="panel panel--conditions">
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
            <div class="conditions-grid">
                @if($proposition->disponibilite)
                    <div class="condition-item">
                        <span class="condition-item__label">
                            <i class="fa-regular fa-calendar"></i> Disponibilité
                        </span>
                        <span class="condition-item__value">
                            {{ $proposition->disponibilite_label }}
                        </span>
                    </div>
                @endif

                @if($proposition->caution_mois !== null)
                    <div class="condition-item">
                        <span class="condition-item__label">
                            <i class="fa-solid fa-shield-halved"></i> Caution
                        </span>
                        <span class="condition-item__value">
                            {{ $proposition->caution_label ?? 'Aucune' }}
                        </span>
                    </div>
                @endif

                @if($proposition->avance_loyer_mois !== null)
                    <div class="condition-item">
                        <span class="condition-item__label">
                            <i class="fa-solid fa-money-bill-wave"></i> Avance loyer
                        </span>
                        <span class="condition-item__value">
                            {{ $proposition->avance_label ?? 'Aucune' }}
                        </span>
                    </div>
                @endif

                @if($proposition->honoraires !== null)
                    <div class="condition-item">
                        <span class="condition-item__label">
                            <i class="fa-solid fa-hand-holding-dollar"></i> Honoraires
                        </span>
                        <span class="condition-item__value">
                            {{ $proposition->honoraires_formate }}
                        </span>
                    </div>
                @endif

                @if($proposition->charges !== null)
                    <div class="condition-item">
                        <span class="condition-item__label">
                            <i class="fa-solid fa-receipt"></i> Charges
                        </span>
                        <span class="condition-item__value">
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
                <div class="conditions-notes">
                    <span class="conditions-notes__label">
                        <i class="fa-regular fa-file-lines"></i> Conditions particulières
                    </span>
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
                <span class="ios-list__value">{{ $bien->titre ?? '—' }}</span>
            </div>

            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Type</span>
                </div>
                <span class="ios-list__value">
                    {{ is_object($bien->type_bien) ? $bien->type_bien->label() : ($bien->type_bien ?? '—') }}
                </span>
            </div>

            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Contrat</span>
                </div>
                <span class="ios-list__value">
                    {{ is_object($bien->type_contrat) ? $bien->type_contrat->label() : ($bien->type_contrat ?? '—') }}
                </span>
            </div>

            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Surface</span>
                </div>
                <span class="ios-list__value">{{ $bien->surface ?? '—' }} m²</span>
            </div>

            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Chambres</span>
                </div>
                <span class="ios-list__value">{{ $bien->nombre_chambres ?? '—' }}</span>
            </div>

            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Salles de bain</span>
                </div>
                <span class="ios-list__value">{{ $bien->nombre_salles_bain ?? '—' }}</span>
            </div>

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

        {{-- Adresse --}}
        @if($bien->adresse)
            <div class="panel panel--address">
                <div class="address-block">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>{{ $bien->adresse }}</span>
                </div>
            </div>
        @endif

        {{-- Équipements --}}
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
        @if($bien->description)
            <div class="ios-section-title">
                <i class="fa-regular fa-file-lines"></i> Description
            </div>
            <blockquote class="quote-block quote-block--rust">
                {{ $bien->description }}
            </blockquote>
        @endif
    @endif

    {{-- ═══ ACTIONS ═══ --}}
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
   DÉTAIL OFFRE — iOS NATIVE
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
    padding: 11px 16px;
    border-radius: 10px;
    margin-bottom: 14px;
    border-left: 4px solid;
    font-size: 13px;
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
.ios-section-title .ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: var(--c-warning-bg);
    color: var(--rust);
    border-radius: 999px;
}
.ios-section-title .ios-section-count--gold {
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
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
.offre-hero__meta i { font-size: 12px; margin-right: 5px; opacity: .8; }
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
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding-top: 14px;
    padding-left: 0;
    border-left: none;
    border-top: 1px dashed var(--border);
    margin-top: 4px;
}
.price-label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
    display: block;
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
    border: 1px solid transparent;
}
.recap-card--info { background: var(--c-info-bg); border-color: #BBDEFB; }
.recap-card--success { background: var(--c-success-bg); border-color: #C8E6C9; }

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
    background: var(--surface);
    border-radius: 10px;
    border: 1px solid var(--border);
}
.condition-item__label {
    font-size: 11px;
    font-weight: 600;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.condition-item__label i { color: var(--rust); font-size: 11px; opacity: .85; }
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
    background: linear-gradient(135deg, #FFFBF7 0%, #FFF 100%);
    border: 1px solid rgba(181, 80, 42, .2);
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
.conditions-total__hint {
    display: block;
    font-size: 11px;
    color: var(--muted);
    margin-top: 2px;
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
    border-top: 1px dashed var(--border);
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
.conditions-notes__label i { color: var(--rust); font-size: 12px; }
.conditions-notes__text {
    font-size: 13.5px;
    color: var(--text-soft);
    line-height: 1.7;
    margin: 0;
    padding: 12px 16px;
    background: var(--surface);
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
    background: #fff;
    font-size: 14px;
    line-height: 1.75;
    color: var(--text);
    font-style: italic;
    margin: 0 0 14px;
    border-left: 4px solid var(--rust);
    border-top: 1px solid var(--border);
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
}

/* ═══ LISTE iOS (caractéristiques) ═══ */
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
    color: var(--text);
    text-align: right;
    flex-shrink: 0;
}
.ios-list__value .is-yes { color: var(--c-success); margin-right: 4px; }
.ios-list__value .is-no  { color: var(--c-danger);  margin-right: 4px; }

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
    border-radius: 9px;
    background: var(--c-warning-bg);
    color: var(--rust);
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

/* Quote */
.quote-block {
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    line-height: 1.75;
    color: var(--text);
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
    gap: 7px;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid;
}
.state-pill--success { background: var(--c-success-bg); color: var(--c-success); border-color: #C8E6C9; }
.state-pill--muted   { background: var(--surface); color: var(--muted); border-color: var(--border); }
.state-pill--info    { background: var(--c-info-bg); color: var(--c-info); border-color: #BBDEFB; }

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
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; color: #fff; border-color: #9A4523; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(180, 83, 42, .25); }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: var(--surface); border-color: var(--rust); color: var(--rust); }
.btn-danger-soft { background: var(--c-danger-bg); color: var(--c-danger); border-color: #FFCDD2; }
.btn-danger-soft:hover { background: var(--c-danger); color: #fff; border-color: var(--c-danger); }
.btn-ghost-danger { background: transparent; color: var(--c-danger); border-color: #FFCDD2; }
.btn-ghost-danger:hover { background: var(--c-danger-bg); border-color: var(--c-danger); }
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
.lightbox__nav:hover { background: rgba(255, 255, 255, .28); transform: translateY(-50%) scale(1.06); }
.lightbox__nav--prev { left: 24px; }
.lightbox__nav--next { right: 24px; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .offre-show { background: transparent; padding-top: 4px; }

    /* ─── HERO iOS ─── */
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
        overflow: hidden;
    }
    .offre-hero::before {
        left: 0; right: 0;
        height: 3px;
        border-radius: 0;
    }
    .offre-hero.is-vedette {
        background: linear-gradient(135deg, #FFFDF5 0%, #fff 40%);
        border: none;
    }
    .offre-hero__badges { gap: 6px; margin-bottom: 6px; }
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
    .offre-hero__title-sep { color: var(--ios-label-3); }

    .offre-hero__meta {
        font-size: 13.5px;
        color: var(--ios-label-3);
        gap: 8px;
    }
    .offre-hero__meta i { color: var(--ios-label-3); }
    .offre-hero__meta strong { color: var(--ios-label); }
    .offre-hero__meta-sep { display: none; }

    /* Score inline */
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

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-title .ios-section-count {
        font-size: 11px;
        padding: 3px 10px;
    }

    /* ─── PANEL iOS ─── */
    .panel {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    /* ─── AGENCE ─── */
    .agency-block {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 0;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .agency-block__avatar { width: 52px; height: 52px; font-size: 20px; }
    .agency-block__info { width: 100%; }
    .agency-block__name { font-size: 18px; font-weight: 700; letter-spacing: -.2px; }
    .agency-block__rating { font-size: 13px; }
    .rating-stars strong { font-size: 14px; }
    .agency-block__contact {
        gap: 8px 14px;
        font-size: 13px;
        margin-top: 8px;
    }
    .contact-link { font-weight: 500; }

    /* Prix en bas, style iOS */
    .agency-block__price {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding-top: 14px;
        padding-left: 0;
        margin-top: 14px;
        border-left: none;
        border-top: none;
        width: 100%;
        text-align: left;
    }
    .price-label {
        font-size: 13px;
        color: var(--ios-label-3);
        text-transform: none;
        letter-spacing: 0;
        font-weight: 500;
    }
    .price-value {
        font-size: 24px;
        font-weight: 800;
        letter-spacing: -.5px;
    }
    .price-vedette { display: none; } /* déjà dans le hero */

    /* ─── CONDITIONS iOS ─── */
    .conditions-recap { grid-template-columns: 1fr 1fr; gap: 8px; }
    .recap-card { padding: 12px; gap: 10px; }
    .recap-card__icon { width: 36px; height: 36px; font-size: 14px; border-radius: 9px; }
    .recap-card__label { font-size: 10px; }
    .recap-card__value { font-size: 14px; }

    .conditions-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
    .condition-item {
        background: #EFEFF4;
        border: none;
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
        background: linear-gradient(135deg, #FFF4EC, #FFE8D9);
        border: none;
    }
    .conditions-total__left > i {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, var(--rust), #d4754a);
        box-shadow: 0 4px 10px rgba(181, 80, 42, .25);
    }
    .conditions-total__label { font-size: 14px; }
    .conditions-total__value {
        font-size: 22px;
        letter-spacing: -.4px;
        text-align: left;
        align-self: flex-end;
    }

    .conditions-notes { border-top: 0.5px solid var(--ios-separator); }
    .conditions-notes__label { font-size: 12px; text-transform: none; letter-spacing: 0; color: var(--ios-label-2); }
    .conditions-notes__text {
        background: #FFF9F5;
        border-left: 3px solid var(--rust);
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 13.5px;
    }

    /* ─── GALERIE iOS ─── */
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
    }
    .gallery-item { border-radius: 12px; border: none; }
    .gallery-item__overlay { display: none; }

    .video-section__title { font-size: 13px; text-transform: none; letter-spacing: 0; color: var(--ios-label-2); font-weight: 600; }
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

    /* ─── MESSAGE iOS ─── */
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
        text-align: right;
    }
    .ios-list__value .is-yes { color: var(--c-success); }
    .ios-list__value .is-no  { color: var(--c-danger); }

    /* Adresse iOS */
    .panel--address { padding: 14px 16px; }
    .address-block { font-size: 14px; gap: 10px; }
    .address-block i {
        width: 34px; height: 34px;
        border-radius: 9px;
        background: var(--c-warning-bg);
    }

    /* ─── ÉQUIPEMENTS ─── */
    .equipements-grid { gap: 8px; }
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

    /* ─── QUOTE ─── */
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

    /* ─── ACTIONS iOS ─── */
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
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    }
    .btn-ghost {
        background: var(--ios-card);
        border: none;
        color: var(--ios-blue);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .btn-ghost:active { background: #EFEFF4; }
    .btn-danger-soft { border: none; }
    .btn-ghost-danger { border: none; background: rgba(198, 40, 40, .08); }

    /* ─── LIGHTBOX iOS ─── */
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
</style>
@endpush