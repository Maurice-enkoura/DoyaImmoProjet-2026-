@extends('layouts.dashboard-agence')

@section('title', 'Détail du rendez-vous — DoyaImmo')
@section('page_title', 'Détail du rendez-vous')
@section('page_sub', 'Informations complètes sur la visite')

@section('content')
<div class="view active rdv-show">

    @php
        $statutValue = $rendezVous->statut->value;
        $isPhoneVisible = $rendezVous->isPhoneVisible();
        $bien     = $rendezVous->proposition->bien ?? null;
        $client   = $rendezVous->particulier->user ?? null;
        $agence   = $rendezVous->agence ?? null;
        $images   = $bien ? $bien->medias->where('type_media', 'image')->values() : collect();
        $videos   = $bien ? $bien->medias->where('type_media', 'video')->values() : collect();

        // Tableau complet des URLs d'images pour la lightbox avec navigation ‹ ›
        $lightboxImages = $images->map(fn($m) => asset('storage/' . $m->fichier))->values()->all();

        $initial = strtoupper(mb_substr($client->prenom ?? 'C', 0, 1));
    @endphp

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="rdv-show__back">
        <a href="{{ route('agence.rendezvous.index') }}" class="btn btn-ghost btn-sm">
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
                <strong>Numéros masqués</strong>
                <p>Les numéros de téléphone sont masqués tant que le rendez-vous n'est pas confirmé par l'agence.</p>
            </div>
        </div>
    @else
        <div class="security-banner security-banner--success">
            <div class="security-banner__icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="security-banner__content">
                <strong>Coordonnées visibles</strong>
                <p>Le rendez-vous est confirmé. Les numéros de téléphone sont maintenant visibles des deux côtés.</p>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         CLIENT + BIEN (2 colonnes)
    ═══════════════════════════════════════════ --}}
    <div class="rdv-grid-2">

        {{-- ═══ CLIENT ═══ --}}
        <section class="panel panel--client">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-user"></i>
                </span>
                <h4>Client</h4>
            </header>

            <div class="client-block">
                <div class="client-block__avatar">{{ $initial }}</div>
                <div class="client-block__main">
                    <strong>{{ $client->prenom ?? '' }} {{ $client->nom ?? '' }}</strong>
                    <span class="client-block__role">Particulier</span>
                </div>
            </div>

            <ul class="info-list">
                <li class="info-list__item">
                    <span class="info-list__label">
                        <i class="fa-solid fa-phone"></i> Téléphone
                    </span>
                    <span class="info-list__value">
                        @if($isPhoneVisible)
                            @if($client && $client->telephone)
                                <a href="tel:{{ $client->telephone }}" class="info-list__phone">
                                    {{ $rendezVous->particulier_phone_formatted }}
                                </a>
                                <i class="fa-solid fa-circle-check info-list__check"></i>
                            @else
                                <span class="info-list__muted">Non renseigné</span>
                            @endif
                        @else
                            <span class="info-list__locked">
                                <i class="fa-solid fa-lock"></i> Masqué
                            </span>
                        @endif
                    </span>
                </li>

                <li class="info-list__item">
                    <span class="info-list__label">
                        <i class="fa-solid fa-envelope"></i> Email
                    </span>
                    <span class="info-list__value">
                        {{ $client->email ?? 'Non renseigné' }}
                    </span>
                </li>
            </ul>
        </section>

        {{-- ═══ BIEN ═══ --}}
        <section class="panel panel--bien">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-building"></i>
                </span>
                <h4>Bien à visiter</h4>
            </header>

            <div class="bien-block">
                <h3 class="bien-block__title">{{ $bien->titre ?? 'Non spécifié' }}</h3>
                @if($bien)
                    <div class="bien-block__meta">
                        <span><i class="fa-solid fa-tag"></i> {{ $bien->type_bien->label() ?? '—' }}</span>
                        @if($bien->surface)
                            <span><i class="fa-regular fa-square"></i> {{ $bien->surface }} m²</span>
                        @endif
                    </div>
                @endif
            </div>

            <ul class="info-list">
                <li class="info-list__item">
                    <span class="info-list__label">
                        <i class="fa-solid fa-location-dot"></i> Adresse
                    </span>
                    <span class="info-list__value">
                        {{ $bien->adresse ?? 'Non spécifiée' }}
                    </span>
                </li>

                <li class="info-list__item">
                    <span class="info-list__label">
                        <i class="fa-solid fa-map-pin"></i> Quartier
                    </span>
                    <span class="info-list__value">
                        {{ $bien->quartier ?? 'Non spécifié' }}
                    </span>
                </li>

                <li class="info-list__item info-list__item--accent">
                    <span class="info-list__label">
                        <i class="fa-solid fa-wallet"></i> Prix proposé
                    </span>
                    <span class="info-list__value info-list__value--prix">
                        {{ number_format($rendezVous->proposition->prix_propose, 0, ',', ' ') }}
                        <small>FCFA</small>
                    </span>
                </li>
            </ul>
        </section>
    </div>

    {{-- ═══════════════════════════════════════════
         GALERIE DU BIEN
    ═══════════════════════════════════════════ --}}
    @if($images->count() > 0 || $videos->count() > 0)
        <section class="panel panel--gallery">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-images"></i>
                </span>
                <h4>Galerie du bien</h4>
                <span class="panel__count">
                    {{ $images->count() + $videos->count() }}
                </span>
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

            @if($images->count() > 0 || $videos->count() > 0)
                <div class="media-counter">
                    @if($images->count() > 0)
                        <span class="media-counter__item">
                            <i class="fa-regular fa-image"></i>
                            {{ $images->count() }} photo(s)
                        </span>
                    @endif
                    @if($videos->count() > 0)
                        <span class="media-counter__item">
                            <i class="fa-regular fa-circle-play"></i>
                            {{ $videos->count() }} vidéo(s)
                        </span>
                    @endif
                </div>
            @endif
        </section>
    @else
        <section class="panel panel--gallery">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-images"></i>
                </span>
                <h4>Galerie du bien</h4>
            </header>
            <div class="empty-gallery">
                <i class="fa-regular fa-image"></i>
                <span>Aucune photo ou vidéo disponible pour ce bien</span>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         MON AGENCE (bandeau compact)
    ═══════════════════════════════════════════ --}}
    <section class="agency-bar">
        <div class="agency-bar__icon">
            <i class="fa-regular fa-building-columns"></i>
        </div>
        <div class="agency-bar__info">
            <strong>{{ $agence->nom_agence ?? 'Mon agence' }}</strong>
            <span class="agency-bar__contact">
                @if($agence && $agence->user && $agence->user->telephone)
                    <span><i class="fa-solid fa-phone"></i> {{ $agence->user->telephone }}</span>
                @endif
                @if($agence && $agence->user && $agence->user->email)
                    <span><i class="fa-solid fa-envelope"></i> {{ $agence->user->email }}</span>
                @endif
                @if($agence && $agence->adresse)
                    <span><i class="fa-solid fa-location-dot"></i> {{ $agence->adresse }}</span>
                @endif
            </span>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         ACTIONS
    ═══════════════════════════════════════════ --}}
    <footer class="rdv-actions">
        <div class="rdv-actions__left">
            @if($statutValue === 'planifie')
                <form action="{{ route('agence.rendezvous.confirmer', $rendezVous) }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-rust">
                        <i class="fa-solid fa-check"></i> Confirmer le rendez-vous
                    </button>
                </form>
                <form action="{{ route('agence.rendezvous.annuler', $rendezVous) }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('Annuler ce rendez-vous ?')">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </button>
                </form>

            @elseif($statutValue === 'confirme')
                @if($client && $client->telephone)
                    <a href="tel:{{ $client->telephone }}" class="btn btn-success">
                        <i class="fa-solid fa-phone"></i> Appeler le client
                    </a>
                @endif
                <form action="{{ route('agence.rendezvous.termine', $rendezVous) }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-rust"
                            onclick="return confirm('Marquer ce rendez-vous comme terminé ?')">
                        <i class="fa-solid fa-check-double"></i> Terminer la visite
                    </button>
                </form>
                <form action="{{ route('agence.rendezvous.annuler', $rendezVous) }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('Annuler ce rendez-vous ?')">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </button>
                </form>

            @elseif($statutValue === 'termine')
                <span class="state-pill state-pill--success">
                    <i class="fa-solid fa-check-circle"></i> Visite terminée
                </span>
                @if($client && $client->telephone)
                    <a href="tel:{{ $client->telephone }}" class="btn btn-success">
                        <i class="fa-solid fa-phone"></i> Appeler le client
                    </a>
                @endif

            @elseif($statutValue === 'annule')
                <span class="state-pill state-pill--danger">
                    <i class="fa-solid fa-ban"></i> Rendez-vous annulé
                </span>
            @endif
        </div>

        <a href="{{ route('agence.rendezvous.index') }}" class="btn btn-ghost rdv-actions__right">
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
   PAGE DÉTAIL RENDEZ-VOUS
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
    --surface:      #F7F9FC;
    --radius:       14px;
}

.rdv-show__back { margin-bottom: 16px; }

/* ═══ HERO ═══ */
.rdv-hero {
    display: flex;
    align-items: center;
    gap: 20px;
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
    top: 0;
    left: 0;
    right: 0;
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
    width: 84px;
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

/* Statut à droite */
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
.rdv-status--annule   { background: var(--c-danger-bg);  color: var(--c-danger); }
.rdv-status--termine  { background: var(--c-success-bg); color: var(--c-success); }

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

/* ═══ GRID 2 COLONNES ═══ */
.rdv-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}

/* ═══ PANNEAUX ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    margin-bottom: 14px;
}
.panel--gallery { margin-bottom: 14px; }

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

/* ═══ BLOC CLIENT ═══ */
.client-block {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: var(--surface);
    border-radius: 10px;
    margin-bottom: 14px;
}
.client-block__avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    flex-shrink: 0;
}
.client-block__main {
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 0;
}
.client-block__main strong {
    font-size: 14.5px;
    font-weight: 700;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.client-block__role {
    font-size: 11.5px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}

/* ═══ BLOC BIEN ═══ */
.bien-block {
    padding: 12px 14px;
    background: var(--surface);
    border-radius: 10px;
    margin-bottom: 14px;
}
.bien-block__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
    line-height: 1.3;
}
.bien-block__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 5px 14px;
    font-size: 12.5px;
    color: var(--muted);
}
.bien-block__meta i {
    margin-right: 4px;
    opacity: .8;
}

/* ═══ LISTE INFOS ═══ */
.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.info-list__item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 9px 12px;
    border-radius: 8px;
    transition: background .15s;
}
.info-list__item:hover { background: var(--surface); }
.info-list__item--accent {
    background: var(--c-warning-bg);
    margin-top: 6px;
}
.info-list__item--accent:hover { background: var(--c-warning-bg); }

.info-list__label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
    white-space: nowrap;
    flex-shrink: 0;
}
.info-list__label i {
    font-size: 11px;
    color: var(--rust);
    width: 12px;
    text-align: center;
}

.info-list__value {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text);
    text-align: right;
    min-width: 0;
    word-break: break-word;
}
.info-list__value--prix {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 800;
    color: var(--rust);
}
.info-list__value--prix small {
    font-size: 10.5px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}

.info-list__phone {
    color: var(--text);
    text-decoration: none;
    font-weight: 700;
    transition: color .2s;
}
.info-list__phone:hover { color: var(--rust); }

.info-list__check {
    color: var(--c-success);
    font-size: 11px;
}

.info-list__muted {
    color: var(--muted);
    font-style: italic;
    font-weight: 400;
    opacity: .85;
}

.info-list__locked {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: var(--muted);
    font-style: italic;
    font-weight: 500;
    font-size: 12.5px;
}
.info-list__locked i {
    font-size: 10px;
    color: var(--rust);
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

/* ═══ COMPTEUR MÉDIAS ═══ */
.media-counter {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    padding: 10px 14px;
    background: var(--surface);
    border-radius: 10px;
    margin-top: 12px;
    font-size: 12.5px;
    color: var(--muted);
}
.media-counter__item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.media-counter__item i { color: var(--rust); }

/* ═══ GALERIE VIDE ═══ */
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
.empty-gallery i {
    font-size: 28px;
    opacity: .4;
}

/* ═══ BANDEAU AGENCE ═══ */
.agency-bar {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.agency-bar__icon {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    background: var(--c-info-bg);
    color: var(--c-info);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    flex-shrink: 0;
}
.agency-bar__info {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
    flex: 1;
}
.agency-bar__info strong {
    font-family: var(--display);
    font-size: 14.5px;
    font-weight: 700;
    color: var(--text);
}
.agency-bar__contact {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 16px;
    font-size: 12.5px;
    color: var(--muted);
}
.agency-bar__contact i {
    margin-right: 5px;
    font-size: 11px;
    opacity: .8;
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
    box-shadow: 0 4px 10px rgba(180, 83, 42, .25);
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
    box-shadow: 0 4px 10px rgba(37, 211, 102, .25);
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

@media (max-width: 900px) {
    .rdv-grid-2 {
        grid-template-columns: 1fr;
    }
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
    .gallery-grid { grid-template-columns: repeat(3, 1fr); }
    .video-grid { grid-template-columns: 1fr; }

    .rdv-actions { flex-direction: column; align-items: stretch; }
    .rdv-actions__left { flex-direction: column; align-items: stretch; }
    .rdv-actions__left .btn,
    .rdv-actions__left form,
    .rdv-actions__left .inline-form { width: 100%; }
    .rdv-actions__left .inline-form .btn { width: 100%; justify-content: center; }
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
    .info-list__item { padding: 8px 10px; }
    .info-list__value { font-size: 12.5px; }
    .info-list__value--prix { font-size: 14.5px; }

    .security-banner { padding: 12px 14px; gap: 10px; }
    .security-banner__icon { width: 32px; height: 32px; font-size: 14px; }

    .agency-bar { padding: 12px 14px; gap: 10px; }
    .agency-bar__icon { width: 36px; height: 36px; font-size: 15px; }

    .btn { font-size: 12px; padding: 7px 13px; }
}
</style>
@endpush