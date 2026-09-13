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

        $dejaEvalue = false;
        if ($statutValue === 'termine') {
            $dejaEvalue = \App\Models\Evaluation::where('particulier_id', Auth::user()->particulier->id)
                ->where('proposition_id', $rendezVous->proposition_id)
                ->exists();
        }

        // Aujourd'hui / Demain
        $dateRdv = \Carbon\Carbon::parse($rendezVous->date_visite);
        $isToday = $dateRdv->isToday();
        $isTomorrow = $dateRdv->isTomorrow();
    @endphp

    {{-- ═══ HERO : DATE + STATUT ═══ --}}
    <header class="rdv-hero rdv-hero--{{ $statutValue }} {{ $isToday ? 'is-today' : '' }}">

        <div class="rdv-hero__date">
            <span class="rdv-hero__day">{{ $dateRdv->format('d') }}</span>
            <span class="rdv-hero__month">{{ strtoupper($dateRdv->translatedFormat('M')) }}</span>
            <span class="rdv-hero__year">{{ $dateRdv->format('Y') }}</span>
        </div>

        <div class="rdv-hero__main">
            @if($isToday)
                <span class="rdv-hero__weekday rdv-hero__weekday--today">Aujourd'hui</span>
            @elseif($isTomorrow)
                <span class="rdv-hero__weekday rdv-hero__weekday--soon">Demain</span>
            @else
                <span class="rdv-hero__weekday">
                    {{ $dateRdv->translatedFormat('l') }}
                </span>
            @endif

            <h1 class="rdv-hero__title">
                {{ $bien->titre ?? 'Visite' }}
            </h1>

            @if($bien && $bien->quartier)
                <div class="rdv-hero__address">
                    <i class="fa-solid fa-location-dot"></i>
                    {{ $bien->quartier }}
                </div>
            @endif

            <div class="rdv-hero__meta">
                <span>
                    <i class="fa-regular fa-clock"></i>
                    {{ \Carbon\Carbon::parse($rendezVous->heure_visite)->format('H:i') }}
                </span>
                <span class="rdv-hero__meta-sep">•</span>
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    {{ $dateRdv->format('d/m/Y') }}
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

    {{-- ═══ BANNIÈRE SÉCURITÉ ═══ --}}
    @if(!$isPhoneVisible)
        <div class="security-banner security-banner--warning">
            <div class="security-banner__icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="security-banner__content">
                <strong>Numéro masqué</strong>
                <p>Les coordonnées de l'agence seront visibles dès confirmation.</p>
            </div>
        </div>
    @else
        <div class="security-banner security-banner--success">
            <div class="security-banner__icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="security-banner__content">
                <strong>Coordonnées visibles</strong>
                <p>Le rendez-vous est confirmé. Vous pouvez contacter l'agence.</p>
            </div>
        </div>
    @endif

    {{-- ═══ AGENCE ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-building"></i> L'agence
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

                {{-- Contact --}}
                @if($telVisible)
                    @if($hasTel)
                        <a href="tel:{{ $agenceUser->telephone }}" class="agency-phone">
                            <i class="fa-solid fa-phone"></i>
                            {{ $agenceUser->telephone }}
                            <i class="fa-solid fa-circle-check agency-phone__check"></i>
                        </a>
                    @else
                        <div class="agency-phone agency-phone--muted">
                            <i class="fa-solid fa-phone"></i> Non renseigné
                        </div>
                    @endif
                @else
                    <div class="agency-phone agency-phone--locked">
                        <i class="fa-solid fa-lock"></i>
                        En attente de confirmation
                    </div>
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

    {{-- ═══ GALERIE ═══ --}}
    @if($images->count() > 0 || $videos->count() > 0)
        <div class="ios-section-title">
            <i class="fa-regular fa-images"></i> Photos du bien
            <span class="ios-section-count">{{ $images->count() + $videos->count() }}</span>
        </div>

        @if($images->count() > 0)
            <div class="gallery-grid">
                @foreach($images as $index => $media)
                    @php $url = asset('storage/' . $media->fichier); @endphp
                    <div class="gallery-item" onclick="openLightbox('{{ $url }}', {{ $index }})">
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
    @endif

    {{-- ═══ DÉTAILS DU BIEN ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-building"></i> Détails du bien
    </div>

    <div class="ios-list">
        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Type</span>
            </div>
            <span class="ios-list__value">
                {{ $bien->type_bien->label() ?? '—' }}
            </span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Contrat</span>
            </div>
            <span class="ios-list__value">
                {{ $bien->type_contrat->label() ?? '—' }}
            </span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Surface</span>
            </div>
            <span class="ios-list__value">{{ $bien->surface ?? 0 }} m²</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Chambres</span>
            </div>
            <span class="ios-list__value">{{ $bien->nombre_chambres ?? 0 }}</span>
        </div>
    </div>

    {{-- Adresse --}}
    @if($bien && $bien->adresse)
        <div class="panel panel--address">
            <div class="address-block">
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ $bien->adresse }}</span>
            </div>
        </div>
    @endif

    {{-- ═══ ACTIONS ═══ --}}
    <footer class="rdv-actions">
        <div class="rdv-actions__left">

            @if($statutValue === 'planifie')
                <span class="state-pill state-pill--warning">
                    <i class="fa-solid fa-clock"></i>
                    En attente de confirmation
                </span>
                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-list"></i> Tous mes rendez-vous
                </a>
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
                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-list"></i> Tous mes rendez-vous
                </a>
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
                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-list"></i> Tous mes rendez-vous
                </a>

            @elseif($statutValue === 'annule')
                <span class="state-pill state-pill--danger">
                    <i class="fa-solid fa-ban"></i>
                    Rendez-vous annulé
                </span>
                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-list"></i> Tous mes rendez-vous
                </a>
            @endif
        </div>
    </footer>
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
   DÉTAIL RENDEZ-VOUS — iOS NATIVE
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

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

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
    flex-wrap: wrap;
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

.rdv-hero__main { flex: 1; min-width: 0; }

.rdv-hero__weekday {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--rust);
    background: var(--rust-soft);
    margin-bottom: 6px;
}
.rdv-hero__weekday--today {
    background: var(--c-danger);
    color: #fff;
}
.rdv-hero__weekday--soon {
    background: var(--c-warning);
    color: #fff;
}

.rdv-hero__title {
    font-family: var(--display);
    font-size: 19px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
    line-height: 1.25;
    letter-spacing: -.2px;
}
.rdv-hero__address {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 4px;
}
.rdv-hero__address i { color: var(--rust); font-size: 11px; }

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

/* ═══ STATUT ═══ */
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
.agency-block__info { flex: 1; min-width: 180px; }
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

.agency-phone {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
    text-decoration: none;
    transition: color .2s;
}
.agency-phone:hover { color: var(--rust); }
.agency-phone i:first-child { color: var(--rust); font-size: 11px; }
.agency-phone__check { color: var(--c-success) !important; font-size: 11px !important; }
.agency-phone--muted,
.agency-phone--locked {
    font-style: italic;
    font-weight: 500;
    color: var(--muted);
}
.agency-phone--locked i { color: var(--rust); font-size: 10px; }

.agency-block__price {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-top: 14px;
    border-top: 1px dashed var(--border);
    width: 100%;
}
.price-label {
    font-size: 12px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
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
.video-section { margin-top: 8px; margin-bottom: 14px; }
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
    margin-top: 8px;
}
.rdv-actions__left {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    flex: 1;
}
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
.state-pill--warning { background: var(--c-warning-bg); color: var(--c-warning); border-color: #FFE0B2; }
.state-pill--success { background: var(--c-success-bg); color: var(--c-success); border-color: #C8E6C9; }
.state-pill--danger  { background: var(--c-danger-bg);  color: var(--c-danger);  border-color: #FFCDD2; }

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
.btn-success { background: #25D366; color: #fff; border-color: #25D366; }
.btn-success:hover { background: #1DA851; color: #fff; border-color: #1DA851; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(37, 211, 102, .25); }
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
.lightbox__nav:hover { background: rgba(255, 255, 255, .28); transform: translateY(-50%) scale(1.06); }
.lightbox__nav--prev { left: 24px; }
.lightbox__nav--next { right: 24px; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .rdv-show { background: transparent; padding-top: 4px; }

    /* ─── HERO iOS ─── */
    .rdv-hero {
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
    .rdv-hero::before {
        left: 0; right: 0;
        height: 3px;
        border-radius: 0;
    }

    /* Date horizontal iOS */
    .rdv-hero__date {
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
        width: 100%;
        padding: 14px 16px;
        border-radius: 14px;
        background: #EFEFF4 !important;
    }
    .rdv-hero--planifie .rdv-hero__date { background: #FFF4EC !important; }
    .rdv-hero--confirme .rdv-hero__date { background: #E8F0FE !important; }
    .rdv-hero--termine  .rdv-hero__date { background: #E8F5E9 !important; }
    .rdv-hero--annule   .rdv-hero__date { background: #FFEBEE !important; }

    .rdv-hero__day {
        font-size: 26px;
        letter-spacing: -.5px;
    }
    .rdv-hero__month { font-size: 12px; }
    .rdv-hero__year  { font-size: 11px; }

    /* Badge Aujourd'hui / Demain */
    .rdv-hero__weekday {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 3px 10px;
        font-weight: 600;
    }

    .rdv-hero__title {
        font-size: 22px;
        letter-spacing: -.4px;
        color: var(--ios-label);
        font-weight: 700;
        margin: 4px 0 6px;
    }

    .rdv-hero__address {
        font-size: 14px;
        color: var(--ios-label-3);
        margin-bottom: 6px;
    }

    .rdv-hero__meta {
        font-size: 13.5px;
        color: var(--ios-label-3);
        gap: 6px;
    }
    .rdv-hero__meta i { color: var(--ios-label-3); }
    .rdv-hero__meta-sep { display: none; }

    .rdv-hero__status {
        align-self: flex-start;
        padding-top: 12px;
        border-top: 0.5px solid var(--ios-separator);
        width: 100%;
    }
    .rdv-status {
        font-size: 11px;
        text-transform: none;
        letter-spacing: 0;
        padding: 5px 12px;
        font-weight: 600;
    }

    /* ─── BANNIÈRE SÉCURITÉ iOS ─── */
    .security-banner {
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px 16px;
        margin-bottom: 12px;
        gap: 12px;
    }
    .security-banner__icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        font-size: 15px;
    }
    .security-banner__content strong { font-size: 14px; font-weight: 700; }
    .security-banner__content p { font-size: 13px; }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-count {
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

    /* ─── AGENCE iOS ─── */
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
    .agency-block__rating { font-size: 13px; margin-bottom: 8px; }
    .rating-stars strong { font-size: 14px; }

    .agency-phone {
        padding: 10px 14px;
        background: #F5F5F7;
        border-radius: 10px;
        font-size: 14px;
        width: 100%;
        justify-content: center;
    }
    .agency-phone:hover { color: var(--text); }
    .agency-phone--locked,
    .agency-phone--muted {
        background: #F5F5F7;
        padding: 10px 14px;
        border-radius: 10px;
        justify-content: center;
    }

    /* Prix iOS */
    .agency-block__price {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 14px;
        border-top: none;
        margin-top: 14px;
        gap: 10px;
    }
    .price-label {
        font-size: 14px;
        color: var(--ios-label-3);
        text-transform: none;
        letter-spacing: 0;
        font-weight: 500;
    }
    .price-value {
        font-size: 24px;
        letter-spacing: -.5px;
    }

    /* ─── GALERIE iOS ─── */
    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .gallery-item {
        border-radius: 12px;
        border: none;
    }
    .gallery-item__overlay { display: none; }

    .video-section__title {
        font-size: 14px;
        text-transform: none;
        letter-spacing: 0;
        color: var(--ios-label-2);
        font-weight: 600;
    }
    .video-grid { grid-template-columns: 1fr; gap: 10px; }
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
        text-align: right;
    }

    /* ─── ADRESSE iOS ─── */
    .panel--address { padding: 14px 16px; margin-bottom: 12px; }
    .address-block { font-size: 14px; gap: 10px; }
    .address-block i {
        width: 34px;
        height: 34px;
        border-radius: 9px;
    }

    /* ─── ACTIONS iOS ─── */
    .rdv-actions {
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
    .rdv-actions__left {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
        width: 100%;
    }
    .rdv-actions__left .btn,
    .rdv-actions__left form,
    .rdv-actions__left .inline-form,
    .rdv-actions__left .state-pill {
        width: 100%;
        justify-content: center;
    }
    .rdv-actions__left .inline-form .btn { width: 100%; justify-content: center; }

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
        border: 1px solid #E5E5EA;
        color: var(--ios-blue);
    }
    .btn-ghost:active { background: #EFEFF4; }
    .btn-danger-soft {
        background: rgba(198, 40, 40, .08);
        border: none;
        color: var(--c-danger);
    }
    .btn-success {
        background: linear-gradient(135deg, #25D366, #1DA851);
        border: none;
        box-shadow: 0 4px 12px rgba(37, 211, 102, .25);
    }

    /* ─── LIGHTBOX iOS ─── */
    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 14px; right: 14px; width: 40px; height: 40px; font-size: 22px; }
}

@media (max-width: 480px) {
    .rdv-hero { padding: 16px; }
    .rdv-hero__title { font-size: 20px; }
    .rdv-hero__day { font-size: 24px; }

    .agency-block__name { font-size: 17px; }
    .price-value { font-size: 22px; }

    .gallery-grid { gap: 6px; }
}
</style>
@endpush