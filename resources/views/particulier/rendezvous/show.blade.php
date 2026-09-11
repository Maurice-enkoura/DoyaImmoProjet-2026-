@extends('layouts.dashboard')

@section('title', 'Détail du rendez-vous — DoyaImmo')
@section('page_title', 'Détail du rendez-vous')
@section('page_sub', 'Informations complètes sur votre visite')

@section('content')
<div class="view active rdv-show">

    @php
        $statutValue = $rendezVous->statut->value;
        $bien        = $rendezVous->proposition->bien ?? null;
        $agence      = $rendezVous->agence ?? null;
        $agenceUser  = $agence->user ?? null;
        $telVisible  = in_array($statutValue, ['confirme', 'termine']);
        $hasTel      = $agenceUser && !empty($agenceUser->telephone);
        $isPhoneVisible = $rendezVous->isPhoneVisible();

        $images = $bien ? $bien->medias->where('type_media', 'image')->values() : collect();
        $videos = $bien ? $bien->medias->where('type_media', 'video')->values() : collect();

        $initial = strtoupper(mb_substr($agence->nom_agence ?? 'A', 0, 1));
        $lightboxImages = $images->map(fn($m) => asset('storage/' . $m->fichier))->values()->all();

        // Vérifie si déjà évalué
        $dejaEvalue = false;
        if ($statutValue === 'termine') {
            $dejaEvalue = \App\Models\Evaluation::where('particulier_id', Auth::user()->particulier->id)
                ->where('proposition_id', $rendezVous->proposition_id)
                ->exists();
        }
    @endphp

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="rdv-show__back">
        <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux rendez-vous
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         HERO : DATE + STATUT
    ═══════════════════════════════════════════ --}}
    <header class="rdv-hero rdv-hero--{{ $statutValue }}">

        <div class="rdv-hero__date">
            <span class="rdv-hero__day">{{ $rendezVous->date_visite->format('d') }}</span>
            <span class="rdv-hero__month">{{ strtoupper($rendezVous->date_visite->translatedFormat('M')) }}</span>
            <span class="rdv-hero__year">{{ $rendezVous->date_visite->format('Y') }}</span>
        </div>

        <div class="rdv-hero__main">
            <span class="rdv-hero__weekday">
                {{ $rendezVous->date_visite->translatedFormat('l') }}
            </span>
            <h1 class="rdv-hero__title">
                {{ $bien->titre ?? 'Visite' }}
                @if($bien && $bien->quartier)
                    <span class="rdv-hero__title-loc">— {{ $bien->quartier }}</span>
                @endif
            </h1>
            <div class="rdv-hero__meta">
                <span>
                    <i class="fa-regular fa-clock"></i>
                    {{ \Carbon\Carbon::parse($rendezVous->heure_visite)->format('H:i') }}
                </span>
                <span class="rdv-hero__meta-sep">•</span>
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    {{ $rendezVous->date_visite->format('d/m/Y') }}
                </span>
            </div>
        </div>

        <div class="rdv-hero__status">
            <span class="rdv-status rdv-status--{{ $statutValue }}">
                <i class="fa-solid fa-circle"></i>
                {{ $rendezVous->statut->label() }}
            </span>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════
         BANNIÈRE SÉCURITÉ
    ═══════════════════════════════════════════ --}}
    @if(!$isPhoneVisible)
        <div class="security-banner security-banner--warning">
            <div class="security-banner__icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="security-banner__content">
                <strong>Numéro masqué</strong>
                <p>Les coordonnées de l'agence seront visibles dès qu'elle aura confirmé votre rendez-vous.</p>
            </div>
        </div>
    @else
        <div class="security-banner security-banner--success">
            <div class="security-banner__icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="security-banner__content">
                <strong>Coordonnées visibles</strong>
                <p>Le rendez-vous est confirmé. Vous pouvez contacter l'agence pour toute question.</p>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         AGENCE
    ═══════════════════════════════════════════ --}}
    <section class="panel panel--agency">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-regular fa-building"></i>
            </span>
            <h4>L'agence</h4>
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

                <div class="agency-block__phone">
                    @if($telVisible)
                        @if($hasTel)
                            <a href="tel:{{ $agenceUser->telephone }}" class="phone-link">
                                <i class="fa-solid fa-phone"></i>
                                {{ $agenceUser->telephone }}
                            </a>
                            <span class="phone-check">
                                <i class="fa-solid fa-circle-check"></i>
                            </span>
                        @else
                            <span class="phone-muted">
                                <i class="fa-solid fa-phone"></i> Non renseigné
                            </span>
                        @endif
                    @else
                        <span class="phone-locked">
                            <i class="fa-solid fa-lock"></i>
                            En attente de confirmation
                        </span>
                    @endif
                </div>
            </div>

            <div class="agency-block__price">
                <span class="price-label">Prix proposé</span>
                <span class="price-value">
                    {{ number_format($rendezVous->proposition->prix_propose, 0, ',', ' ') }}
                    <small>FCFA</small>
                </span>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         GALERIE DU BIEN
    ═══════════════════════════════════════════ --}}
    @if($images->count() > 0 || $videos->count() > 0)
        <section class="panel panel--gallery">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-images"></i>
                </span>
                <h4>Photos du bien</h4>
                <span class="panel__count">{{ $images->count() + $videos->count() }}</span>
            </header>

            @if($images->count() > 0)
                <div class="gallery-grid">
                    @foreach($images as $index => $media)
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

            @if($videos->count() > 0)
                <div class="video-section">
                    <h5 class="video-section__title">
                        <i class="fa-regular fa-circle-play"></i>
                        Vidéos
                        <span class="video-section__count">{{ $videos->count() }}</span>
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
         DÉTAILS DU BIEN
    ═══════════════════════════════════════════ --}}
    <section class="panel panel--bien">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-regular fa-building"></i>
            </span>
            <h4>Détails du bien</h4>
        </header>

        <div class="bien-grid">
            <div class="bien-grid__item">
                <span class="bien-grid__label">Type</span>
                <span class="bien-grid__value">
                    {{ $bien->type_bien->label() ?? '—' }}
                </span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Contrat</span>
                <span class="bien-grid__value">
                    {{ $bien->type_contrat->label() ?? '—' }}
                </span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Surface</span>
                <span class="bien-grid__value">{{ $bien->surface ?? 0 }} m²</span>
            </div>
            <div class="bien-grid__item">
                <span class="bien-grid__label">Chambres</span>
                <span class="bien-grid__value">{{ $bien->nombre_chambres ?? 0 }}</span>
            </div>
            <div class="bien-grid__item bien-grid__item--wide">
                <span class="bien-grid__label">Adresse</span>
                <span class="bien-grid__value">{{ $bien->adresse ?? 'Non spécifiée' }}</span>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         ACTIONS
    ═══════════════════════════════════════════ --}}
    <footer class="rdv-actions">
        <div class="rdv-actions__left">

            @if($statutValue === 'planifie')
                <span class="state-pill state-pill--warning">
                    <i class="fa-solid fa-clock"></i>
                    En attente de confirmation
                </span>
                <form action="{{ route('particulier.rendezvous.annuler', $rendezVous) }}"
                      method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('Annuler ce rendez-vous ?')">
                        <i class="fa-solid fa-xmark"></i> Annuler le rendez-vous
                    </button>
                </form>

            @elseif($statutValue === 'confirme')
                @if($hasTel)
                    <a href="tel:{{ $agenceUser->telephone }}" class="btn btn-success">
                        <i class="fa-solid fa-phone"></i> Appeler l'agence
                    </a>
                @endif
                <form action="{{ route('particulier.rendezvous.annuler', $rendezVous) }}"
                      method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('Annuler ce rendez-vous ?')">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </button>
                </form>

            @elseif($statutValue === 'termine')
                @if(!$dejaEvalue)
                    <a href="{{ route('particulier.evaluations.create', $rendezVous->proposition) }}"
                       class="btn btn-rust">
                        <i class="fa-solid fa-star"></i> Évaluer l'agence
                    </a>
                @else
                    <span class="state-pill state-pill--success">
                        <i class="fa-solid fa-circle-check"></i>
                        Déjà évalué
                    </span>
                @endif
                @if($hasTel)
                    <a href="tel:{{ $agenceUser->telephone }}" class="btn btn-success">
                        <i class="fa-solid fa-phone"></i> Appeler
                    </a>
                @endif

            @elseif($statutValue === 'annule')
                <span class="state-pill state-pill--danger">
                    <i class="fa-solid fa-ban"></i>
                    Rendez-vous annulé
                </span>
            @endif
        </div>

        <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost rdv-actions__right">
            <i class="fa-solid fa-list"></i> Tous mes rendez-vous
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
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL RDV — Particulier
   ═══════════════════════════════════════════════════════════ */

.rdv-show {
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

.rdv-show__back { margin-bottom: 16px; }

/* ═══ HERO ═══ */
.rdv-hero {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 18px 22px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    position: relative;
    overflow: hidden;
}
.rdv-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--muted);
}
.rdv-hero--planifie::before { background: var(--c-warning); }
.rdv-hero--confirme::before { background: var(--c-info); }
.rdv-hero--termine::before  { background: var(--c-success); }
.rdv-hero--annule::before   { background: var(--c-danger); }

/* Bloc date */
.rdv-hero__date {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1px;
    width: 82px;
    padding: 10px 8px;
    border-radius: 12px;
    flex-shrink: 0;
    background: var(--surface);
}
.rdv-hero--planifie .rdv-hero__date { background: var(--c-warning-bg); }
.rdv-hero--confirme .rdv-hero__date { background: var(--c-info-bg); }
.rdv-hero--termine  .rdv-hero__date { background: var(--c-success-bg); }
.rdv-hero--annule   .rdv-hero__date { background: var(--c-danger-bg); }

.rdv-hero__day {
    font-family: var(--display);
    font-size: 30px;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.rdv-hero__month {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--text-soft);
}
.rdv-hero__year {
    font-size: 10px;
    font-weight: 600;
    color: var(--muted);
}

/* Contenu central */
.rdv-hero__main { flex: 1; min-width: 0; }
.rdv-hero__weekday {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--rust);
    margin-bottom: 4px;
}
.rdv-hero__title {
    font-family: var(--display);
    font-size: 19px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
    line-height: 1.25;
}
.rdv-hero__title-loc {
    color: var(--muted);
    font-weight: 400;
    font-size: 16px;
}
.rdv-hero__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13px;
    color: var(--muted);
}
.rdv-hero__meta i {
    margin-right: 5px;
    font-size: 12px;
    opacity: .8;
}
.rdv-hero__meta-sep { opacity: .4; }

.rdv-hero__status { flex-shrink: 0; }

/* ═══ STATUT PILL ═══ */
.rdv-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 13px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.rdv-status i { font-size: 6px; }
.rdv-status--planifie { background: var(--c-warning-bg); color: var(--c-warning); }
.rdv-status--confirme { background: var(--c-info-bg);    color: var(--c-info); }
.rdv-status--termine  { background: var(--c-success-bg); color: var(--c-success); }
.rdv-status--annule   { background: var(--c-danger-bg);  color: var(--c-danger); }

/* ═══ BANNIÈRE SÉCURITÉ ═══ */
.security-banner {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 14px 18px;
    border-radius: 12px;
    margin-bottom: 14px;
    border: 1px solid;
}
.security-banner__icon {
    flex-shrink: 0;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}
.security-banner__content { min-width: 0; }
.security-banner__content strong {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
    margin-bottom: 2px;
}
.security-banner__content p {
    font-size: 13px;
    margin: 0;
    line-height: 1.5;
}

.security-banner--warning {
    background: var(--c-warning-bg);
    border-color: #FFE0B2;
    color: var(--c-warning);
}
.security-banner--warning .security-banner__icon {
    background: rgba(230, 81, 0, .12);
    color: var(--c-warning);
}
.security-banner--warning p { color: #8B4B15; }

.security-banner--success {
    background: var(--c-success-bg);
    border-color: #C8E6C9;
    color: var(--c-success);
}
.security-banner--success .security-banner__icon {
    background: rgba(30, 122, 71, .12);
    color: var(--c-success);
}
.security-banner--success p { color: #175C35; }

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
    box-shadow: 0 6px 16px rgba(180, 83, 42, .2);
}

.agency-block__info {
    flex: 1;
    min-width: 180px;
}
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
    margin-bottom: 5px;
    font-size: 13px;
    flex-wrap: wrap;
}
.rating-stars {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.rating-stars i {
    color: var(--c-gold);
    font-size: 13px;
}
.rating-stars strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 800;
    color: var(--text);
}
.rating-max {
    color: var(--muted);
    font-size: 12px;
    font-weight: 500;
}
.rating-reviews {
    color: var(--muted);
    font-size: 12px;
}

.agency-block__phone {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
}
.phone-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--text);
    font-weight: 700;
    text-decoration: none;
    transition: color .2s;
}
.phone-link:hover { color: var(--rust); }
.phone-link i {
    color: var(--rust);
    font-size: 11px;
}
.phone-check {
    color: var(--c-success);
    font-size: 11px;
}
.phone-muted,
.phone-locked {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--muted);
    font-style: italic;
    font-size: 12px;
}
.phone-locked i {
    color: var(--rust);
    font-size: 10px;
}

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
    font-size: 20px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
}
.price-value small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
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

/* ═══ VIDÉOS ═══ */
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

/* ═══ ACTIONS ═══ */
.rdv-actions {
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
.rdv-actions__left {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.rdv-actions__right { margin-left: auto; }
.inline-form { display: inline; }

.state-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid;
}
.state-pill--warning {
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-color: #FFE0B2;
}
.state-pill--success {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-color: #C8E6C9;
}
.state-pill--danger {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border-color: #FFCDD2;
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
.btn-success {
    background: #25D366;
    color: #fff;
    border-color: #25D366;
}
.btn-success:hover {
    background: #1DA851;
    color: #fff;
    border-color: #1DA851;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 211, 102, .25);
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
}

@media (max-width: 768px) {
    .rdv-hero {
        flex-wrap: wrap;
        padding: 16px;
        gap: 14px;
    }
    .rdv-hero__date {
        width: 70px;
        padding: 8px 6px;
    }
    .rdv-hero__day { font-size: 24px; }
    .rdv-hero__main { flex-basis: calc(100% - 84px); }
    .rdv-hero__title { font-size: 16.5px; }
    .rdv-hero__title-loc { font-size: 14px; }
    .rdv-hero__status { width: 100%; }

    .panel { padding: 16px; }

    .agency-block { gap: 12px; }
    .agency-block__avatar { width: 46px; height: 46px; font-size: 18px; }
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

    .rdv-actions { flex-direction: column; align-items: stretch; }
    .rdv-actions__left { flex-direction: column; align-items: stretch; }
    .rdv-actions__left .btn,
    .rdv-actions__left form,
    .rdv-actions__left .inline-form { width: 100%; }
    .rdv-actions__left .inline-form .btn { width: 100%; justify-content: center; }
    .rdv-actions__left .state-pill { justify-content: center; }
    .rdv-actions__right { margin-left: 0; width: 100%; justify-content: center; }

    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 12px; right: 12px; width: 38px; height: 38px; font-size: 22px; }
}

@media (max-width: 480px) {
    .rdv-hero { padding: 14px; }
    .rdv-hero__date { width: 60px; }
    .rdv-hero__day { font-size: 20px; }
    .rdv-hero__month { font-size: 10px; }
    .rdv-hero__year { font-size: 9px; }
    .rdv-hero__title { font-size: 15px; }
    .rdv-hero__main { flex-basis: calc(100% - 74px); }

    .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 6px; }

    .security-banner { padding: 12px 14px; gap: 10px; }
    .security-banner__icon { width: 32px; height: 32px; font-size: 14px; }

    .panel { padding: 14px; }
    .agency-block__name { font-size: 15px; }
    .price-value { font-size: 18px; }

    .btn { font-size: 12px; padding: 8px 14px; }
}
</style>
@endpush