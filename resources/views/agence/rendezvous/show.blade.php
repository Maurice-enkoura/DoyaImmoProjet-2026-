@extends('layouts.dashboard-agence')

@section('title', 'Détail du rendez-vous — DoyaImmo')
@section('page_title', 'Détail du rendez-vous')
@section('page_sub', 'Informations complètes sur la visite')

@section('content')
<div class="view active ios-rdv-show">

    @php
        $statutValue = $rendezVous->statut->value;
        $isPhoneVisible = $rendezVous->isPhoneVisible();
        $bien     = $rendezVous->proposition->bien ?? null;
        $client   = $rendezVous->particulier->user ?? null;
        $agence   = $rendezVous->agence ?? null;
        $images   = $bien ? $bien->medias->where('type_media', 'image')->values() : collect();
        $videos   = $bien ? $bien->medias->where('type_media', 'video')->values() : collect();

        $lightboxImages = $images->map(fn($m) => asset('storage/' . $m->fichier))->values()->all();
        $initial = strtoupper(mb_substr($client->prenom ?? 'C', 0, 1));
    @endphp

    {{-- ═══ HERO ═══ --}}
    <header class="ios-rdv-hero ios-rdv-hero--{{ $statutValue }}">
        <div class="ios-rdv-hero__date">
            <span class="ios-rdv-hero__day">{{ $rendezVous->date_visite->format('d') }}</span>
            <span class="ios-rdv-hero__month">{{ strtoupper($rendezVous->date_visite->translatedFormat('M')) }}</span>
            <span class="ios-rdv-hero__year">{{ $rendezVous->date_visite->format('Y') }}</span>
        </div>

        <div class="ios-rdv-hero__main">
            <span class="ios-rdv-hero__weekday">
                {{ $rendezVous->date_visite->translatedFormat('l') }}
            </span>
            <h2 class="ios-rdv-hero__title">
                {{ $bien->titre ?? 'Visite' }}
                @if($bien && $bien->quartier)
                    <span class="ios-rdv-hero__title-loc">— {{ $bien->quartier }}</span>
                @endif
            </h2>
            <div class="ios-rdv-hero__meta">
                <span>
                    <i class="fa-regular fa-clock"></i>
                    {{ \Carbon\Carbon::parse($rendezVous->heure_visite)->format('H:i') }}
                </span>
                <span class="ios-rdv-hero__meta-sep">•</span>
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    {{ $rendezVous->date_visite->format('d/m/Y') }}
                </span>
            </div>
        </div>

        <div class="ios-rdv-hero__status">
            <span class="ios-badge ios-badge--{{ $statutValue }}">
                <i class="fa-solid fa-circle"></i>
                {{ $rendezVous->statut->label() }}
            </span>
        </div>
    </header>

    {{-- ═══ BANNIÈRE SÉCURITÉ ═══ --}}
    @if(!$isPhoneVisible)
        <div class="ios-banner ios-banner--warning">
            <div class="ios-banner__icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="ios-banner__body">
                <strong>Numéros masqués</strong>
                <p>Les numéros sont masqués tant que le rendez-vous n'est pas confirmé.</p>
            </div>
        </div>
    @else
        <div class="ios-banner ios-banner--success">
            <div class="ios-banner__icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="ios-banner__body">
                <strong>Coordonnées visibles</strong>
                <p>Le rendez-vous est confirmé. Les numéros sont visibles.</p>
            </div>
        </div>
    @endif

    {{-- ═══ CONTENU 2 COLONNES (desktop) ═══ --}}
    <div class="ios-rdv-layout">

        {{-- ═══ COLONNE PRINCIPALE ═══ --}}
        <div class="ios-rdv-layout__main">

            {{-- ═══ CLIENT ═══ --}}
            <div class="ios-section-header">Client</div>

            <div class="ios-group">
                <div class="ios-cell">
                    <div class="ios-client-avatar">{{ $initial }}</div>
                    <div class="ios-cell__body">
                        <span class="ios-cell__label ios-cell__label--strong">
                            {{ $client->prenom ?? '' }} {{ $client->nom ?? '' }}
                        </span>
                        <span class="ios-cell__sublabel">Particulier</span>
                    </div>
                </div>

                <div class="ios-cell">
                    <div class="ios-cell__icon ios-cell__icon--green">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div class="ios-cell__body">
                        <span class="ios-cell__label">Téléphone</span>
                    </div>
                    <span class="ios-cell__value">
                        @if($isPhoneVisible)
                            @if($client && $client->telephone)
                                <a href="tel:{{ $client->telephone }}" class="ios-cell__value--link">
                                    {{ $rendezVous->particulier_phone_formatted }}
                                </a>
                            @else
                                <span class="ios-cell__value--muted">Non renseigné</span>
                            @endif
                        @else
                            <span class="ios-cell__value--muted">
                                <i class="fa-solid fa-lock" style="font-size:11px;margin-right:4px;"></i>Masqué
                            </span>
                        @endif
                    </span>
                </div>

                <div class="ios-cell">
                    <div class="ios-cell__icon ios-cell__icon--blue">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="ios-cell__body">
                        <span class="ios-cell__label">Email</span>
                    </div>
                    <span class="ios-cell__value">{{ $client->email ?? 'Non renseigné' }}</span>
                </div>
            </div>

            {{-- ═══ BIEN ═══ --}}
            <div class="ios-section-header">Bien à visiter</div>

            <div class="ios-group">
                <div class="ios-cell ios-cell--stacked">
                    <div class="ios-cell__icon ios-cell__icon--orange">
                        <i class="fa-regular fa-building"></i>
                    </div>
                    <div class="ios-cell__body">
                        <span class="ios-cell__label ios-cell__label--strong">
                            {{ $bien->titre ?? 'Non spécifié' }}
                        </span>
                        @if($bien)
                            <span class="ios-cell__sublabel">
                                {{ $bien->type_bien->label() ?? '—' }}
                                @if($bien->surface) · {{ $bien->surface }} m² @endif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="ios-cell">
                    <div class="ios-cell__icon ios-cell__icon--red">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="ios-cell__body">
                        <span class="ios-cell__label">Adresse</span>
                    </div>
                    <span class="ios-cell__value">{{ $bien->adresse ?? '—' }}</span>
                </div>

                <div class="ios-cell">
                    <div class="ios-cell__icon ios-cell__icon--gray">
                        <i class="fa-solid fa-map-pin"></i>
                    </div>
                    <div class="ios-cell__body">
                        <span class="ios-cell__label">Quartier</span>
                    </div>
                    <span class="ios-cell__value">{{ $bien->quartier ?? '—' }}</span>
                </div>

                <div class="ios-cell">
                    <div class="ios-cell__icon ios-cell__icon--rust">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div class="ios-cell__body">
                        <span class="ios-cell__label">Prix proposé</span>
                    </div>
                    <span class="ios-cell__value ios-cell__value--price">
                        {{ number_format($rendezVous->proposition->prix_propose, 0, ',', ' ') }}
                        <small>FCFA</small>
                    </span>
                </div>
            </div>

            {{-- ═══ GALERIE ═══ --}}
            @if($images->count() > 0 || $videos->count() > 0)
                <div class="ios-section-header">
                    Galerie
                    <span class="ios-section-header__count">{{ $images->count() + $videos->count() }}</span>
                </div>

                @if($images->count() > 0)
                    <div class="ios-gallery">
                        @foreach($images as $index => $media)
                            @php $url = asset('storage/' . $media->fichier); @endphp
                            <div class="ios-gallery__item" onclick="openLightbox('{{ $url }}', {{ $index }})">
                                <img src="{{ $url }}" alt="Photo du bien" loading="lazy">
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($videos->count() > 0)
                    <div class="ios-video-list">
                        @foreach($videos as $media)
                            <div class="ios-video-tile">
                                <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata">
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="ios-section-header">Galerie</div>
                <div class="ios-empty ios-empty--compact">
                    <div class="ios-empty__icon">
                        <i class="fa-regular fa-image"></i>
                    </div>
                    <p class="ios-empty__text">Aucune photo ou vidéo disponible</p>
                </div>
            @endif
        </div>

        {{-- ═══ COLONNE LATÉRALE ═══ --}}
        <aside class="ios-rdv-layout__side">

            {{-- ═══ AGENCE ═══ --}}
            <div class="ios-section-header">Agence</div>

            <div class="ios-group">
                <div class="ios-cell ios-cell--stacked">
                    <div class="ios-cell__icon ios-cell__icon--blue">
                        <i class="fa-regular fa-building-columns"></i>
                    </div>
                    <div class="ios-cell__body">
                        <span class="ios-cell__label ios-cell__label--strong">
                            {{ $agence->nom_agence ?? 'Mon agence' }}
                        </span>
                        @if($agence && $agence->adresse)
                            <span class="ios-cell__sublabel">{{ $agence->adresse }}</span>
                        @endif
                    </div>
                </div>

                @if($agence && $agence->user && $agence->user->telephone)
                    <div class="ios-cell">
                        <div class="ios-cell__icon ios-cell__icon--green">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="ios-cell__body">
                            <span class="ios-cell__label">Téléphone</span>
                        </div>
                        <span class="ios-cell__value">{{ $agence->user->telephone }}</span>
                    </div>
                @endif

                @if($agence && $agence->user && $agence->user->email)
                    <div class="ios-cell">
                        <div class="ios-cell__icon ios-cell__icon--blue">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="ios-cell__body">
                            <span class="ios-cell__label">Email</span>
                        </div>
                        <span class="ios-cell__value">{{ $agence->user->email }}</span>
                    </div>
                @endif
            </div>

            {{-- ═══ ACTIONS ═══ --}}
            <div class="ios-section-header">Actions</div>

            <div class="ios-actions-stack">
                @if($statutValue === 'planifie')
                    <form action="{{ route('agence.rendezvous.confirmer', $rendezVous) }}" method="POST">
                        @csrf
                        <button type="submit" class="ios-btn ios-btn--primary ios-btn--full">
                            <i class="fa-solid fa-check"></i> Confirmer le rendez-vous
                        </button>
                    </form>
                    <form action="{{ route('agence.rendezvous.annuler', $rendezVous) }}" method="POST">
                        @csrf
                        <button type="submit" class="ios-btn ios-btn--red ios-btn--full"
                                onclick="return confirm('Annuler ce rendez-vous ?')">
                            <i class="fa-solid fa-xmark"></i> Annuler
                        </button>
                    </form>

                @elseif($statutValue === 'confirme')
                    @if($client && $client->telephone)
                        <a href="tel:{{ $client->telephone }}" class="ios-btn ios-btn--green ios-btn--full">
                            <i class="fa-solid fa-phone"></i> Appeler le client
                        </a>
                    @endif
                    <form action="{{ route('agence.rendezvous.termine', $rendezVous) }}" method="POST">
                        @csrf
                        <button type="submit" class="ios-btn ios-btn--primary ios-btn--full"
                                onclick="return confirm('Marquer ce rendez-vous comme terminé ?')">
                            <i class="fa-solid fa-check-double"></i> Terminer la visite
                        </button>
                    </form>
                    <form action="{{ route('agence.rendezvous.annuler', $rendezVous) }}" method="POST">
                        @csrf
                        <button type="submit" class="ios-btn ios-btn--red ios-btn--full"
                                onclick="return confirm('Annuler ce rendez-vous ?')">
                            <i class="fa-solid fa-xmark"></i> Annuler
                        </button>
                    </form>

                @elseif($statutValue === 'termine')
                    <div class="ios-state-pill ios-state-pill--success">
                        <i class="fa-solid fa-check-circle"></i> Visite terminée
                    </div>
                    @if($client && $client->telephone)
                        <a href="tel:{{ $client->telephone }}" class="ios-btn ios-btn--green ios-btn--full">
                            <i class="fa-solid fa-phone"></i> Appeler le client
                        </a>
                    @endif

                @elseif($statutValue === 'annule')
                    <div class="ios-state-pill ios-state-pill--danger">
                        <i class="fa-solid fa-ban"></i> Rendez-vous annulé
                    </div>
                @endif

                <a href="{{ route('agence.rendezvous.index') }}" class="ios-btn ios-btn--gray ios-btn--full">
                    <i class="fa-solid fa-list"></i> Tous mes rendez-vous
                </a>
            </div>
        </aside>
    </div>

    {{-- ═══ LIGHTBOX ═══ --}}
    <div id="lightbox" class="ios-lightbox" onclick="closeLightbox()">
        <button type="button" class="ios-lightbox__close"
                onclick="event.stopPropagation();closeLightbox()" aria-label="Fermer">
            <i class="fa-solid fa-xmark"></i>
        </button>

        <figure class="ios-lightbox__figure" onclick="event.stopPropagation()">
            <img id="lightboxImage" src="" alt="Agrandir">
            <figcaption id="lightboxCounter" class="ios-lightbox__counter"></figcaption>
        </figure>
    </div>

</div>
@endsection


@push('scripts')
<script>
    const LIGHTBOX_IMAGES = @json($lightboxImages);
    let currentIndex = 0;
    let touchStartX = 0;
    let touchEndX = 0;

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

        img.src = LIGHTBOX_IMAGES[currentIndex] || '';
        counter.textContent = LIGHTBOX_IMAGES.length > 1
            ? (currentIndex + 1) + ' / ' + LIGHTBOX_IMAGES.length
            : '';
    }

    document.addEventListener('keydown', function (e) {
        const lb = document.getElementById('lightbox');
        if (!lb.classList.contains('is-open')) return;
        if (e.key === 'Escape')     closeLightbox();
        if (e.key === 'ArrowLeft')  navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });

    const lbEl = document.getElementById('lightbox');
    lbEl.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; });
    lbEl.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        const diff = touchEndX - touchStartX;
        if (Math.abs(diff) > 50) navigateLightbox(diff > 0 ? -1 : 1);
    });
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DÉTAIL RDV — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.ios-rdv-show {
    --ios-blue:      #007AFF;
    --ios-green:     #34C759;
    --ios-red:       #FF3B30;
    --ios-orange:    #FF9500;
    --ios-gray:      #8E8E93;
    --ios-gray-3:    #C7C7CC;
    --ios-gray-5:    #E5E5EA;
    --ios-gray-6:    #F2F2F7;

    --ios-bg:        #F2F2F7;
    --ios-card:      #FFFFFF;
    --ios-label:     #000000;
    --ios-label-2:   rgba(60, 60, 67, 0.6);
    --ios-label-3:   rgba(60, 60, 67, 0.3);
    --ios-separator: rgba(60, 60, 67, 0.29);

    --ios-radius:    10px;
    --ios-radius-lg: 12px;

    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text",
                 "Helvetica Neue", Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;

    background: var(--ios-bg);
    padding-bottom: 40px;
}

/* ═══ HERO CARD ═══ */
.ios-rdv-hero {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    flex-wrap: wrap;
}
.ios-rdv-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--ios-gray);
}
.ios-rdv-hero--planifie::before { background: var(--ios-orange); }
.ios-rdv-hero--confirme::before { background: var(--ios-blue); }
.ios-rdv-hero--termine::before  { background: var(--ios-green); }
.ios-rdv-hero--annule::before   { background: var(--ios-red); }

.ios-rdv-hero__date {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0;
    width: 72px;
    padding: 10px 6px;
    border-radius: 12px;
    flex-shrink: 0;
    background: var(--ios-gray-6);
}
.ios-rdv-hero--planifie .ios-rdv-hero__date { background: rgba(255, 149, 0, 0.12); }
.ios-rdv-hero--confirme .ios-rdv-hero__date { background: rgba(0, 122, 255, 0.12); }
.ios-rdv-hero--termine  .ios-rdv-hero__date { background: rgba(52, 199, 89, 0.12); }
.ios-rdv-hero--annule   .ios-rdv-hero__date { background: rgba(255, 59, 48, 0.12); }

.ios-rdv-hero__day {
    font-size: 28px;
    font-weight: 700;
    line-height: 1;
    color: var(--ios-label);
    letter-spacing: -0.5px;
}
.ios-rdv-hero__month {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--ios-label-2);
    margin-top: 2px;
}
.ios-rdv-hero__year {
    font-size: 10px;
    font-weight: 500;
    color: var(--ios-label-3);
}

.ios-rdv-hero__main { flex: 1; min-width: 0; }
.ios-rdv-hero__weekday {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--ios-blue);
    margin-bottom: 3px;
}
.ios-rdv-hero__title {
    font-size: 17px;
    font-weight: 600;
    margin: 0 0 4px;
    color: var(--ios-label);
    line-height: 1.25;
    letter-spacing: -0.3px;
}
.ios-rdv-hero__title-loc {
    color: var(--ios-label-2);
    font-weight: 400;
    font-size: 15px;
}
.ios-rdv-hero__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    font-size: 13px;
    color: var(--ios-label-2);
    letter-spacing: -0.1px;
}
.ios-rdv-hero__meta i {
    margin-right: 4px;
    font-size: 12px;
    opacity: 0.7;
}
.ios-rdv-hero__meta-sep { opacity: 0.4; }

.ios-rdv-hero__status { flex-shrink: 0; }

/* ═══ BADGE ═══ */
.ios-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 100px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
}
.ios-badge i { font-size: 5px; }
.ios-badge--planifie { background: rgba(255, 149, 0, 0.15); color: #C93400; }
.ios-badge--confirme { background: rgba(0, 122, 255, 0.15); color: #0040DD; }
.ios-badge--annule   { background: rgba(255, 59, 48, 0.15); color: #D70015; }
.ios-badge--termine  { background: rgba(52, 199, 89, 0.15); color: #007A33; }

/* ═══ BANNIÈRE ═══ */
.ios-banner {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    border-radius: var(--ios-radius-lg);
    margin-bottom: 20px;
}
.ios-banner__icon {
    flex-shrink: 0;
    width: 34px;
    height: 34px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    color: #fff;
}
.ios-banner__body { min-width: 0; }
.ios-banner__body strong {
    display: block;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 2px;
    letter-spacing: -0.2px;
}
.ios-banner__body p {
    font-size: 13px;
    margin: 0;
    line-height: 1.45;
    opacity: 0.85;
}
.ios-banner--warning {
    background: rgba(255, 149, 0, 0.12);
    color: #8B4B15;
}
.ios-banner--warning .ios-banner__icon { background: var(--ios-orange); }

.ios-banner--success {
    background: rgba(52, 199, 89, 0.12);
    color: #175C35;
}
.ios-banner--success .ios-banner__icon { background: var(--ios-green); }

/* ═══ LAYOUT 2 COLONNES (desktop) ═══ */
.ios-rdv-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 20px;
    align-items: start;
}
.ios-rdv-layout__main { min-width: 0; }
.ios-rdv-layout__side { min-width: 0; }

/* ═══ SECTION HEADER ═══ */
.ios-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--ios-label-2);
    margin: 24px 0 6px;
    padding: 0 4px;
}
.ios-section-header:first-child { margin-top: 0; }
.ios-rdv-layout__side .ios-section-header:first-child { margin-top: 0; }
.ios-section-header__count {
    font-size: 12px;
    font-weight: 500;
    background: var(--ios-gray-5);
    color: var(--ios-label-2);
    padding: 2px 8px;
    border-radius: 100px;
    text-transform: none;
    letter-spacing: 0;
}

/* ═══ GROUPED LIST ═══ */
.ios-group {
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    overflow: hidden;
    margin-bottom: 4px;
}

.ios-cell {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 16px;
    min-height: 44px;
    position: relative;
    background: var(--ios-card);
    transition: background 0.1s;
}
.ios-cell + .ios-cell::before {
    content: '';
    position: absolute;
    top: 0;
    left: 16px;
    right: 0;
    height: 0.5px;
    background: var(--ios-separator);
}
.ios-cell--stacked { align-items: flex-start; }

.ios-cell__icon {
    width: 29px;
    height: 29px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 15px;
    color: #fff;
}
.ios-cell__icon--blue   { background: var(--ios-blue); }
.ios-cell__icon--green  { background: var(--ios-green); }
.ios-cell__icon--orange { background: var(--ios-orange); }
.ios-cell__icon--red    { background: var(--ios-red); }
.ios-cell__icon--gray   { background: var(--ios-gray); }
.ios-cell__icon--rust   { background: #B4532A; }

.ios-cell__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.ios-cell__label {
    font-size: 15px;
    font-weight: 400;
    color: var(--ios-label);
    letter-spacing: -0.2px;
}
.ios-cell__label--strong { font-weight: 600; }
.ios-cell__sublabel {
    font-size: 13px;
    color: var(--ios-label-2);
    letter-spacing: -0.1px;
}
.ios-cell__value {
    font-size: 15px;
    color: var(--ios-label-2);
    text-align: right;
    letter-spacing: -0.2px;
    word-break: break-word;
    flex-shrink: 0;
    max-width: 55%;
}
.ios-cell__value--link {
    color: var(--ios-blue);
    text-decoration: none;
}
.ios-cell__value--price {
    font-weight: 600;
    font-size: 17px;
    color: var(--ios-label);
    letter-spacing: -0.4px;
}
.ios-cell__value--price small {
    font-size: 13px;
    font-weight: 400;
    color: var(--ios-label-2);
    margin-left: 2px;
}
.ios-cell__value--muted {
    font-style: italic;
    color: var(--ios-label-3);
    font-size: 14px;
}

/* ═══ CLIENT AVATAR ═══ */
.ios-client-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #B4532A, #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 600;
    flex-shrink: 0;
    letter-spacing: -0.3px;
}

/* ═══ GALERIE ═══ */
.ios-gallery {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    overflow: hidden;
    margin-bottom: 4px;
}
.ios-gallery__item {
    position: relative;
    aspect-ratio: 1;
    cursor: pointer;
    background: var(--ios-gray-6);
    overflow: hidden;
}
.ios-gallery__item:active { opacity: 0.7; }
.ios-gallery__item img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}

/* ═══ VIDÉOS ═══ */
.ios-video-list {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}
.ios-video-tile {
    border-radius: var(--ios-radius-lg);
    overflow: hidden;
    background: #000;
}
.ios-video-tile video {
    width: 100%;
    aspect-ratio: 16 / 9;
    object-fit: cover;
    display: block;
}

/* ═══ EMPTY ═══ */
.ios-empty {
    text-align: center;
    padding: 40px 20px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    margin-bottom: 4px;
}
.ios-empty--compact { padding: 32px 20px; }
.ios-empty__icon {
    font-size: 44px;
    color: var(--ios-gray-3);
    margin-bottom: 10px;
}
.ios-empty__text {
    font-size: 14px;
    color: var(--ios-label-2);
    margin: 0;
    line-height: 1.5;
}

/* ═══ ACTIONS STACK ═══ */
.ios-actions-stack {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 4px;
}

/* ═══ BOUTONS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 14px 20px;
    border-radius: var(--ios-radius-lg);
    font-size: 15px;
    font-weight: 600;
    font-family: inherit;
    letter-spacing: -0.3px;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: opacity 0.1s, transform 0.1s;
    white-space: nowrap;
    min-height: 48px;
    width: 100%;
}
.ios-btn:active { transform: scale(0.98); opacity: 0.85; }

.ios-btn--primary { background: #B4532A; color: #fff; }
.ios-btn--green   { background: var(--ios-green); color: #fff; }
.ios-btn--red     { background: var(--ios-red); color: #fff; }
.ios-btn--gray    { background: var(--ios-gray-5); color: var(--ios-blue); }
.ios-btn--full    { width: 100%; }

/* ═══ STATE PILL ═══ */
.ios-state-pill {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 20px;
    border-radius: var(--ios-radius-lg);
    font-size: 15px;
    font-weight: 600;
    min-height: 48px;
}
.ios-state-pill--success {
    background: rgba(52, 199, 89, 0.12);
    color: #175C35;
}
.ios-state-pill--danger {
    background: rgba(255, 59, 48, 0.12);
    color: #8B1A1A;
}

/* ═══ LIGHTBOX ═══ */
.ios-lightbox {
    display: none;
    position: fixed;
    inset: 0;
    background: #000;
    z-index: 9999;
    align-items: center;
    justify-content: center;
}
.ios-lightbox.is-open { display: flex; }
.ios-lightbox__figure {
    margin: 0;
    max-width: 100vw;
    max-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
}
.ios-lightbox__figure img {
    max-width: 100vw;
    max-height: 90vh;
    object-fit: contain;
}
.ios-lightbox__counter {
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 14px;
    font-weight: 500;
    padding: 6px 14px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border-radius: 100px;
}
.ios-lightbox__close {
    position: absolute;
    top: 20px; right: 20px;
    width: 36px; height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #fff;
    border: none;
    font-size: 17px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2;
}
.ios-lightbox__close:active { opacity: 0.6; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

/* Desktop : conteneur large mais lisible */
@media (min-width: 821px) {
    .ios-rdv-show {
        max-width: 1100px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }
}

/* Tablette : 1 colonne */
@media (max-width: 1100px) and (min-width: 821px) {
    .ios-rdv-layout {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}

/* Mobile iOS */
@media (max-width: 820px) {
    .ios-rdv-show {
        margin: 0 -16px;
        padding: 0 16px 40px;
    }

    .ios-rdv-layout {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .ios-rdv-layout__main,
    .ios-rdv-layout__side {
        display: contents;
    }
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