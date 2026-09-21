@extends('layouts.dashboard')

@section('title', 'Détail du rendez-vous — DoyaImmo')
@section('page_title', 'Détail du rendez-vous')
@section('page_sub', 'Informations complètes sur votre visite')

@section('content')
<div class="view active rdv-show">

    @php
        use App\Enums\StatutRendezVousEnum;

        $statutValue = is_object($rendezVous->statut)
            ? $rendezVous->statut->value
            : (string) $rendezVous->statut;
        $statutLabel = is_object($rendezVous->statut)
            ? $rendezVous->statut->label()
            : ucfirst($statutValue);

        $bien        = $rendezVous->proposition->bien ?? null;
        $agence      = $rendezVous->agence ?? null;
        $agenceUser  = $agence->user ?? null;

        // ✅ Téléphone visible dès qu'un RDV existe (sauf annulé/client absent)
        $isPhoneVisible = !in_array($statutValue, [
            StatutRendezVousEnum::ANNULE->value,
            StatutRendezVousEnum::CLIENT_ABSENT->value,
        ]);
        $hasTel = $agenceUser && !empty($agenceUser->telephone);

        // ✅ Nettoyer le numéro pour le lien tel:
        $telClean = $hasTel ? preg_replace('/\s+/', '', $agenceUser->telephone) : '';

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

        $dateRdv    = \Carbon\Carbon::parse($rendezVous->date_visite);
        $isToday    = $dateRdv->isToday();
        $isTomorrow = $dateRdv->isTomorrow();

        $dateHeureVisite = \Carbon\Carbon::parse($rendezVous->date_visite)
            ->setTimeFromTimeString($rendezVous->heure_visite ?? '00:00:00');

        $codeVisible = $statutValue === 'confirme'
            && !$rendezVous->code_valide
            && now()->greaterThanOrEqualTo($dateHeureVisite->copy()->subHours(24));

        $propositionStatut = $rendezVous->proposition
            ? (is_object($rendezVous->proposition->statut)
                ? $rendezVous->proposition->statut->value
                : (string) $rendezVous->proposition->statut)
            : null;

        $aRdvActif = $rendezVous->proposition
            ? $rendezVous->proposition->rendezVous()
                ->whereIn('statut', [
                    StatutRendezVousEnum::PLANIFIE->value,
                    StatutRendezVousEnum::CONFIRME->value,
                ])
                ->where('id', '!=', $rendezVous->id)
                ->exists()
            : false;

        $peutReplanifier = in_array($statutValue, ['client_absent', 'annule'])
            && $propositionStatut
            && in_array($propositionStatut, ['acceptee', 'en_attente'])
            && !$aRdvActif;
    @endphp


    {{-- ═══ HERO : DATE + STATUT ═══ --}}
    <header class="rdv-hero rdv-hero--{{ $statutValue }}">

        <div class="rdv-hero__date">
            <span class="rdv-hero__day">{{ $dateRdv->format('d') }}</span>
            <span class="rdv-hero__month">{{ strtoupper($dateRdv->translatedFormat('M')) }}</span>
            <span class="rdv-hero__year">{{ $dateRdv->format('Y') }}</span>
        </div>

        <div class="rdv-hero__main">
            <span class="rdv-hero__weekday">
                {{ $isToday ? 'Aujourd\'hui' : ($isTomorrow ? 'Demain' : $dateRdv->translatedFormat('l')) }}
            </span>

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
                {{ $statutLabel }}
            </span>
        </div>
    </header>

    {{-- ═══ ALERTE ANNULÉ ═══ --}}
    @if($statutValue === 'annule')
        <div class="rdv-alert rdv-alert--annule">
            <i class="fa-solid fa-ban"></i>
            <div>
                <strong>Rendez-vous annulé</strong>
                <p>Ce rendez-vous a été annulé. Vous pouvez choisir un nouveau créneau de visite.</p>
            </div>
        </div>
    @endif

    {{-- ═══ ALERTE CLIENT ABSENT ═══ --}}
    @if($statutValue === 'client_absent')
        <div class="rdv-alert rdv-alert--absent">
            <i class="fa-solid fa-user-slash"></i>
            <div>
                <strong>Vous n'êtes pas venu à cette visite</strong>
                <p>L'agence a signalé votre absence. Vous pouvez choisir un nouveau créneau de visite.</p>
            </div>
        </div>
    @endif

    {{-- ═══ CODE DE VISITE ═══ --}}
    @if($codeVisible)
        <div class="rdv-code">
            <div class="rdv-code__header">
                <i class="fa-solid fa-shield-halved"></i>
                <div>
                    <h3 class="rdv-code__title">Code de visite</h3>
                    <p class="rdv-code__subtitle">Présentez ce code à l'agence le jour J</p>
                </div>
            </div>
            <div class="rdv-code__digits">
                @foreach(str_split($rendezVous->code_visite ?? '------') as $digit)
                    <span class="rdv-code__digit">{{ $digit }}</span>
                @endforeach
            </div>
            <div class="rdv-code__footer">
                <span>Ce code sera requis par l'agence pour valider votre visite.</span>
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
                    <i class="fa-solid fa-star"></i>
                    <strong>{{ number_format($agence->note_moyenne ?? 0, 1) }}</strong>
                    <span class="rating-max">/ 5</span>
                    <span class="rating-reviews">({{ $agence->evaluations->count() ?? 0 }} avis)</span>
                </div>

                {{-- ✅ Téléphone agence — visible dès qu'un RDV existe --}}
                @if($isPhoneVisible && $hasTel)
                    <a href="tel:{{ $telClean }}" class="agency-phone">
                        <i class="fa-solid fa-phone"></i>
                        {{ $agenceUser->telephone }}
                    </a>
                @elseif($isPhoneVisible && !$hasTel)
                    <div class="agency-phone agency-phone--muted">
                        <i class="fa-solid fa-phone"></i>
                        Numéro non renseigné
                    </div>
                @else
                    <div class="agency-phone agency-phone--muted">
                        <i class="fa-solid fa-lock"></i>
                        Numéro indisponible pour ce rendez-vous
                    </div>
                @endif
            </div>
        </div>

        <div class="agency-block__price">
            <span class="price-label">Prix proposé</span>
            <span class="price-value">
                {{ $rendezVous->proposition
                    ? number_format($rendezVous->proposition->prix_propose, 0, ',', ' ')
                    : '—' }}
                <small>FCFA</small>
            </span>
        </div>
    </div>

    {{-- ═══ CARTE + ITINÉRAIRE ═══ --}}
    @if($bien && ($bien->adresse || $bien->quartier))
        <div class="ios-section-title">
            <i class="fa-solid fa-map-location-dot"></i> Localisation
        </div>

        <div class="rdv-map-card">
            <div class="rdv-map-info">
                @if($bien->quartier)
                    <div class="rdv-map-info__row">
                        <i class="fa-solid fa-location-dot"></i>
                        <div>
                            <span class="rdv-map-info__label">Quartier</span>
                            <span class="rdv-map-info__value">{{ $bien->quartier }}</span>
                        </div>
                    </div>
                @endif

                @if($bien->adresse)
                    <div class="rdv-map-info__row">
                        <i class="fa-solid fa-house"></i>
                        <div>
                            <span class="rdv-map-info__label">Adresse</span>
                            <span class="rdv-map-info__value">{{ $bien->adresse }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div id="rdvMap"
                 class="rdv-map"
                 data-adresse="{{ $bien->adresse }}"
                 data-quartier="{{ $bien->quartier }}"
                 data-titre="{{ $bien->titre }}"></div>

            <div class="rdv-map-actions">
                <a href="{{ $bien->google_maps_url }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="rdv-map-btn rdv-map-btn--primary">
                    <i class="fa-solid fa-diamond-turn-right"></i>
                    Ouvrir l'itinéraire
                </a>

                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($bien->adresse_complete) }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="rdv-map-btn rdv-map-btn--ghost">
                    <i class="fa-solid fa-map"></i>
                    Voir sur Google Maps
                </a>
            </div>
        </div>
    @endif

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
            <span class="ios-list__value">{{ $bien->surface ?? 0 }} m²</span>
        </div>

        <div class="ios-list__item">
            <span class="ios-list__title">Chambres</span>
            <span class="ios-list__value">{{ $bien->nombre_chambres ?? 0 }}</span>
        </div>
    </div>

    {{-- ═══ ACTIONS ═══ --}}
    <footer class="rdv-actions">
        <div class="rdv-actions__left">

            @if($statutValue === 'planifie')
                <span class="state-pill state-pill--warning">
                    En attente de confirmation
                </span>

                @if($isPhoneVisible && $hasTel)
                    <a href="tel:{{ $telClean }}" class="btn btn-rust">
                        <i class="fa-solid fa-phone"></i> Appeler l'agence
                    </a>
                @endif

                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                    Tous mes rendez-vous
                </a>

                <form action="{{ route('particulier.rendezvous.annuler', $rendezVous) }}"
                      method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('Annuler ce rendez-vous ?')">
                        Annuler
                    </button>
                </form>

            @elseif($statutValue === 'confirme')
                @if($isPhoneVisible && $hasTel)
                    <a href="tel:{{ $telClean }}" class="btn btn-rust">
                        <i class="fa-solid fa-phone"></i> Appeler l'agence
                    </a>
                @endif

                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                    Tous mes rendez-vous
                </a>

                <form action="{{ route('particulier.rendezvous.annuler', $rendezVous) }}"
                      method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('Annuler ce rendez-vous ?')">
                        Annuler
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
                        Déjà évalué
                    </span>
                @endif

                @if($hasTel)
                    <a href="tel:{{ $telClean }}" class="btn btn-ghost">
                        <i class="fa-solid fa-phone"></i> Appeler
                    </a>
                @endif

                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                    Tous mes rendez-vous
                </a>

            @elseif($statutValue === 'client_absent')
                <span class="state-pill state-pill--warning">
                    Visite non honorée
                </span>

                @if($peutReplanifier)
                    <a href="{{ route('particulier.rendezvous.create', $rendezVous->proposition_id) }}"
                       class="btn btn-rust">
                        Choisir un nouveau créneau
                    </a>
                @endif

                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                    Tous mes rendez-vous
                </a>

            @elseif($statutValue === 'annule')
                <span class="state-pill state-pill--danger">
                    Rendez-vous annulé
                </span>

                @if($peutReplanifier)
                    <a href="{{ route('particulier.rendezvous.create', $rendezVous->proposition_id) }}"
                       class="btn btn-rust">
                        Choisir un nouveau créneau
                    </a>
                @endif

                <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost">
                    Tous mes rendez-vous
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
    /* ═══ LIGHTBOX ═══ */
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

    /* ═══ CARTE LEAFLET ═══ */
    (function() {
        const mapEl = document.getElementById('rdvMap');
        if (!mapEl) return;

        const adresse  = mapEl.dataset.adresse  || '';
        const quartier = mapEl.dataset.quartier || '';
        const titre    = mapEl.dataset.titre    || 'Bien';

        const query = [adresse, quartier, 'Dakar', 'Sénégal']
            .filter(Boolean)
            .join(', ');

        if (!query) return;

        if (!document.querySelector('link[href*="leaflet"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            link.crossOrigin = '';
            document.head.appendChild(link);
        }

        function initMap() {
            const map = L.map('rdvMap', {
                scrollWheelZoom: false,
                zoomControl: true,
            }).setView([14.6928, -17.4467], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap',
                maxZoom: 19,
            }).addTo(map);

            const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q='
                + encodeURIComponent(query);

            fetch(url, { headers: { 'Accept-Language': 'fr' } })
            .then(r => r.json())
            .then(results => {
                if (!results || results.length === 0) {
                    addMarker(map, 14.6928, -17.4467, titre);
                    return;
                }
                const lat = parseFloat(results[0].lat);
                const lng = parseFloat(results[0].lon);
                map.setView([lat, lng], 16);
                addMarker(map, lat, lng, titre);
            })
            .catch(() => addMarker(map, 14.6928, -17.4467, titre));
        }

        function addMarker(map, lat, lng, title) {
            const icon = L.divIcon({
                className: 'rdv-marker',
                html: '<div class="rdv-marker__pin"></div>',
                iconSize: [24, 24],
                iconAnchor: [12, 24],
                popupAnchor: [0, -24],
            });

            L.marker([lat, lng], { icon })
                .addTo(map)
                .bindPopup('<strong>' + title + '</strong>')
                .openPopup();
        }

        if (typeof L === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.crossOrigin = '';
            script.onload = initMap;
            document.head.appendChild(script);
        } else {
            initMap();
        }
    })();
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DÉTAIL RENDEZ-VOUS — Design épuré
   ═══════════════════════════════════════════════════════════ */

.rdv-show {
    --surface: #F7F9FC;
    --radius: 14px;
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
    flex-wrap: wrap;
}
.rdv-hero__date {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1px;
    width: 76px;
    padding: 10px 8px;
    border-radius: 12px;
    flex-shrink: 0;
    background: #F5F5F7;
}
.rdv-hero__day {
    font-family: var(--display);
    font-size: 28px;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.rdv-hero__month {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
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
    padding: 2px 9px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 600;
    color: var(--muted);
    background: #F5F5F7;
    margin-bottom: 6px;
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
.rdv-hero__address i { color: var(--muted); font-size: 11px; }

.rdv-hero__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13px;
    color: var(--muted);
}
.rdv-hero__meta i { margin-right: 5px; font-size: 12px; }
.rdv-hero__meta-sep { opacity: .4; }

.rdv-hero__status { flex-shrink: 0; }

/* ═══ STATUT ═══ */
.rdv-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
    background: #F5F5F7;
    color: #4A5260;
}
.rdv-status--planifie      { background: #FFF8E1; color: #A05A00; }
.rdv-status--confirme      { background: #EFF6FF; color: #1E40AF; }
.rdv-status--termine       { background: #F1F8F4; color: #1E7A47; }
.rdv-status--annule        { background: #FEF2F2; color: #C62828; }
.rdv-status--client_absent { background: #F5F5F7; color: #6C6C70; }

/* ═══ ALERTES MÉTIER ═══ */
.rdv-alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 12px;
    margin-bottom: 14px;
}
.rdv-alert > i {
    font-size: 18px;
    flex-shrink: 0;
    margin-top: 2px;
}
.rdv-alert > div strong {
    display: block;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 3px;
}
.rdv-alert > div p {
    margin: 0;
    font-size: 13px;
    line-height: 1.5;
    opacity: .85;
}
.rdv-alert--absent {
    background: #FFFBF0;
    color: #A05A00;
    border-left: 3px solid #E65100;
}
.rdv-alert--annule {
    background: #FEF2F2;
    color: #C62828;
    border-left: 3px solid #C62828;
}

/* ═══ CODE DE VISITE ═══ */
.rdv-code {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 20px;
    margin-bottom: 16px;
}
.rdv-code__header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.rdv-code__header > i {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #F5F5F7;
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.rdv-code__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 2px;
    color: var(--text);
}
.rdv-code__subtitle {
    font-size: 12.5px;
    color: var(--muted);
    margin: 0;
}
.rdv-code__digits {
    display: flex;
    justify-content: center;
    gap: 8px;
    flex-wrap: wrap;
    padding: 16px;
    background: #F9FAFB;
    border-radius: 12px;
    margin-bottom: 12px;
}
.rdv-code__digit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 54px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-family: var(--display);
    font-size: 28px;
    font-weight: 800;
    color: var(--text);
}
.rdv-code__footer {
    display: flex;
    justify-content: center;
    font-size: 12.5px;
    color: var(--muted);
    text-align: center;
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
.ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    background: #F5F5F7;
    color: var(--muted);
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
    background: #F5F5F7;
    color: var(--text);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    flex-shrink: 0;
    border: 1px solid var(--border);
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
    gap: 6px;
    margin-bottom: 6px;
    font-size: 13px;
    flex-wrap: wrap;
}
.agency-block__rating i { color: #F5A623; font-size: 13px; }
.agency-block__rating strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
}
.rating-max { color: var(--muted); font-size: 12px; }
.rating-reviews { color: var(--muted); font-size: 12px; }

.agency-phone {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13.5px;
    font-weight: 500;
    color: var(--text);
    text-decoration: none;
    transition: color .2s;
}
.agency-phone:hover { color: var(--rust); }
.agency-phone i { color: var(--muted); font-size: 12px; }
.agency-phone--muted {
    color: var(--muted);
    font-style: italic;
    font-weight: 400;
}

.agency-block__price {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
    width: 100%;
}
.price-label {
    font-size: 13px;
    color: var(--muted);
    font-weight: 500;
}
.price-value {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    color: var(--text);
    white-space: nowrap;
    line-height: 1.2;
}
.price-value small {
    font-size: 12px;
    font-weight: 500;
    color: var(--muted);
    margin-left: 3px;
}

/* ═══ CARTE ═══ */
.rdv-map-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 14px;
}
.rdv-map-info {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border);
}
.rdv-map-info__row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}
.rdv-map-info__row > i {
    width: 30px;
    height: 30px;
    border-radius: 8px;
    background: #F5F5F7;
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
}
.rdv-map-info__label {
    display: block;
    font-size: 11px;
    color: var(--muted);
    font-weight: 500;
    margin-bottom: 2px;
}
.rdv-map-info__value {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
    line-height: 1.35;
}
.rdv-map {
    width: 100%;
    height: 280px;
    background: #F5F5F7;
    z-index: 1;
}
.rdv-map-actions {
    display: flex;
    gap: 8px;
    padding: 14px 18px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.rdv-map-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
    flex: 1;
    min-width: 140px;
}
.rdv-map-btn--primary {
    background: var(--rust);
    color: #fff;
    border: 1px solid var(--rust);
}
.rdv-map-btn--primary:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
.rdv-map-btn--ghost {
    background: transparent;
    color: var(--text-soft);
    border: 1px solid var(--border);
}
.rdv-map-btn--ghost:hover {
    background: #F5F5F7;
}

/* Marqueur simple */
.rdv-marker { background: transparent; border: none; }
.rdv-marker__pin {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--rust);
    border: 3px solid #fff;
    box-shadow: 0 0 0 1px var(--rust);
}

.leaflet-container { font-family: inherit; z-index: 1; }
.leaflet-pane,
.leaflet-top,
.leaflet-bottom { z-index: 1 !important; }

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

.video-section { margin-top: 8px; margin-bottom: 14px; }
.video-section__title {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 600;
    color: var(--muted);
    margin: 0 0 10px;
}
.video-section__title i { color: var(--muted); font-size: 13px; }
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

/* ═══ LISTE iOS ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
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
.ios-list__item:last-child { border-bottom: none; }
.ios-list__title {
    font-size: 13px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
    text-align: right;
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
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    background: #F5F5F7;
    color: var(--text-soft);
}
.state-pill--warning { background: #FFF8E1; color: #A05A00; }
.state-pill--success { background: #F1F8F4; color: #1E7A47; }
.state-pill--danger  { background: #FEF2F2; color: #C62828; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .15s ease;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
.btn-ghost {
    background: transparent;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: #F5F5F7;
    color: var(--text);
}
.btn-danger-soft {
    background: transparent;
    color: #C62828;
    border-color: var(--border);
}
.btn-danger-soft:hover {
    background: #FEF2F2;
    border-color: #FECACA;
}

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
    line-height: 1;
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
   📱 MOBILE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .rdv-show { background: transparent; padding-top: 4px; }

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
    }
    .rdv-hero__date {
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
        width: 100%;
        padding: 14px 16px;
        border-radius: 14px;
        background: #F5F5F7;
    }
    .rdv-hero__day { font-size: 24px; }
    .rdv-hero__month { font-size: 12px; }
    .rdv-hero__year  { font-size: 11px; }

    .rdv-hero__weekday {
        font-size: 11px;
        padding: 3px 10px;
        font-weight: 500;
    }

    .rdv-hero__title {
        font-size: 20px;
        letter-spacing: -.3px;
        margin: 4px 0 6px;
    }

    .rdv-hero__address {
        font-size: 13.5px;
        margin-bottom: 6px;
    }

    .rdv-hero__meta {
        font-size: 13.5px;
        gap: 6px;
    }
    .rdv-hero__meta-sep { display: none; }

    .rdv-hero__status {
        align-self: flex-start;
        padding-top: 12px;
        border-top: 0.5px solid var(--ios-separator);
        width: 100%;
    }
    .rdv-status {
        font-size: 11.5px;
        padding: 5px 12px;
        font-weight: 600;
    }

    /* Alerte iOS */
    .rdv-alert {
        padding: 14px 16px;
        border-radius: 14px;
        gap: 10px;
        margin-bottom: 12px;
    }
    .rdv-alert > i { font-size: 16px; }
    .rdv-alert > div strong { font-size: 14px; }
    .rdv-alert > div p { font-size: 12.5px; }

    /* Code iOS */
    .rdv-code {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .rdv-code__header { gap: 12px; margin-bottom: 16px; }
    .rdv-code__header > i {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #F5F5F7;
        font-size: 18px;
    }
    .rdv-code__title { font-size: 15px; }
    .rdv-code__subtitle { font-size: 12px; }
    .rdv-code__digits {
        gap: 6px;
        padding: 14px 8px;
        border-radius: 14px;
        background: #F5F5F7;
        margin-bottom: 12px;
    }
    .rdv-code__digit {
        width: 40px;
        height: 50px;
        font-size: 24px;
        border-radius: 10px;
    }
    .rdv-code__footer { font-size: 12px; }

    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; }
    .ios-section-count { font-size: 11px; padding: 3px 10px; }

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
    .agency-block__name { font-size: 18px; letter-spacing: -.2px; }
    .agency-block__rating { font-size: 13px; margin-bottom: 10px; }

    .agency-phone {
        padding: 11px 14px;
        background: #F5F5F7;
        border-radius: 10px;
        font-size: 14px;
        width: 100%;
        justify-content: center;
        color: var(--text);
        font-weight: 500;
    }
    .agency-phone:hover { color: var(--text); }
    .agency-phone--muted {
        background: #F5F5F7;
        padding: 11px 14px;
        border-radius: 10px;
        justify-content: center;
        color: var(--muted);
    }

    .agency-block__price {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 14px;
        border-top: none;
        margin-top: 14px;
        gap: 10px;
    }
    .price-label { font-size: 14px; }
    .price-value { font-size: 22px; letter-spacing: -.3px; }

    /* Carte iOS */
    .rdv-map-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
    }
    .rdv-map-info {
        padding: 14px 16px;
        gap: 10px;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .rdv-map-info__row > i {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #F5F5F7;
        font-size: 13px;
    }
    .rdv-map-info__label { font-size: 11.5px; }
    .rdv-map-info__value { font-size: 15px; font-weight: 500; }

    .rdv-map { height: 240px; }

    .rdv-map-actions {
        padding: 12px 16px;
        border-top: 0.5px solid var(--ios-separator);
        gap: 8px;
        flex-direction: column;
    }
    .rdv-map-btn {
        padding: 13px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        min-width: 100%;
        flex: 1;
    }

    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .gallery-item { border-radius: 12px; border: none; }

    .video-section__title { font-size: 14px; }
    .video-grid { grid-template-columns: 1fr; gap: 10px; }
    .video-tile { border-radius: 12px; border: none; }

    .ios-list {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
    }
    .ios-list__item {
        padding: 14px 16px;
        gap: 12px;
        border-bottom: 0.5px solid var(--ios-separator);
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
    }

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
        padding: 13px 18px;
        font-size: 14px;
        border-radius: 12px;
        font-weight: 600;
    }
    .btn-ghost {
        background: #F5F5F7;
        border: none;
        color: var(--ios-blue);
    }
    .btn-danger-soft {
        background: transparent;
        border: 1px solid #FECACA;
        color: #C62828;
    }

    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 14px; right: 14px; width: 40px; height: 40px; font-size: 22px; }
}

@media (max-width: 480px) {
    .rdv-hero { padding: 16px; }
    .rdv-hero__title { font-size: 19px; }
    .rdv-hero__day { font-size: 22px; }

    .agency-block__name { font-size: 17px; }
    .price-value { font-size: 20px; }

    .gallery-grid { gap: 6px; }

    .rdv-code__digit {
        width: 34px;
        height: 44px;
        font-size: 22px;
        border-radius: 9px;
    }
    .rdv-code__digits { gap: 5px; padding: 12px 6px; }
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