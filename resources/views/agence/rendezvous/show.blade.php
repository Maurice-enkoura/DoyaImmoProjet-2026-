@extends('layouts.dashboard-agence')

@section('title', 'Détail du rendez-vous — DoyaImmo')
@section('page_title', 'Détail du rendez-vous')
@section('page_sub', 'Informations complètes sur la visite')

@section('content')
<div class="view active ios-rdv-show">

    @php
        $statutValue = is_object($rendezVous->statut)
            ? $rendezVous->statut->value
            : (string) $rendezVous->statut;
        $statutLabel = is_object($rendezVous->statut)
            ? $rendezVous->statut->label()
            : ucfirst($statutValue);

        $dateVisite = \Carbon\Carbon::parse($rendezVous->date_visite);
        $heureVisite = $rendezVous->heure_visite
            ? \Carbon\Carbon::parse($rendezVous->heure_visite)->format('H:i')
            : '—';

        $proposition = $rendezVous->proposition;
        $bien        = $proposition->bien ?? null;
        $client      = $rendezVous->particulier->user ?? null;
        $agence      = $rendezVous->agence ?? null;

        // ✅ Téléphone client TOUJOURS visible dès qu'il y a un RDV
        $hasTel = $client && !empty($client->telephone);

        $images = $bien ? $bien->medias->where('type_media', 'image')->values() : collect();
        $videos = $bien ? $bien->medias->where('type_media', 'video')->values() : collect();

        $lightboxImages = $images->map(fn($m) => asset('storage/' . $m->fichier))->values()->all();
        $initial = strtoupper(mb_substr($client->prenom ?? 'C', 0, 1));

        // ✅ Fenêtre de validation : 24h avant la visite
        $dateHeureVisite = \Carbon\Carbon::parse($rendezVous->date_visite)
            ->setTimeFromTimeString($rendezVous->heure_visite ?? '00:00:00');
        $fenetreValidationOuverte = now()->greaterThanOrEqualTo(
            $dateHeureVisite->copy()->subHours(24)
        );

        $peutValiderCode = $statutValue === 'confirme'
            && !$rendezVous->code_valide
            && $fenetreValidationOuverte;

        $peutMarquerAbsent = $statutValue === 'confirme'
            && !$rendezVous->code_valide;
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
    @if(session('info'))
        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    {{-- ═══ HERO ═══ --}}
    <header class="ios-rdv-hero">
        <div class="ios-rdv-hero__date">
            <span class="ios-rdv-hero__day">{{ $dateVisite->format('d') }}</span>
            <span class="ios-rdv-hero__month">{{ strtoupper($dateVisite->translatedFormat('M')) }}</span>
            <span class="ios-rdv-hero__year">{{ $dateVisite->format('Y') }}</span>
        </div>

        <div class="ios-rdv-hero__main">
            <span class="ios-rdv-hero__weekday">
                {{ $dateVisite->translatedFormat('l') }}
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
                    {{ $heureVisite }}
                </span>
                <span class="ios-rdv-hero__meta-sep">•</span>
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    {{ $dateVisite->format('d/m/Y') }}
                </span>
            </div>
        </div>

        <div class="ios-rdv-hero__status">
            <span class="ios-badge ios-badge--{{ $statutValue }}">
                {{ $statutLabel }}
            </span>
        </div>
    </header>

    {{-- ═══ BANNIÈRE STATUT ═══ --}}
    @if($statutValue === 'client_absent')
        <div class="ios-banner ios-banner--muted">
            <div class="ios-banner__icon">
                <i class="fa-solid fa-user-slash"></i>
            </div>
            <div class="ios-banner__body">
                <strong>Client absent</strong>
                <p>
                    Vous avez signalé que le client n'est pas venu à ce rendez-vous.
                    Le particulier a été notifié et peut choisir un nouveau créneau.
                </p>
            </div>
        </div>
    @elseif($rendezVous->code_valide)
        <div class="ios-banner ios-banner--success">
            <div class="ios-banner__icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="ios-banner__body">
                <strong>Visite validée</strong>
                <p>Le code de visite a été utilisé. La visite est marquée comme terminée.</p>
            </div>
        </div>
    @endif

    {{-- ═══ CONTENU 2 COLONNES ═══ --}}
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

                {{-- Téléphone — TOUJOURS visible --}}
                <div class="ios-cell">
                    <div class="ios-cell__icon ios-cell__icon--green">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div class="ios-cell__body">
                        <span class="ios-cell__label">Téléphone</span>
                    </div>
                    <span class="ios-cell__value">
                        @if($hasTel)
                            <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="ios-cell__value--link">
                                {{ $client->telephone }}
                            </a>
                        @else
                            <span class="ios-cell__value--muted">Non renseigné</span>
                        @endif
                    </span>
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
                                {{ is_object($bien->type_bien) ? $bien->type_bien->label() : ($bien->type_bien ?? '—') }}
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
                        {{ $proposition ? number_format($proposition->prix_propose, 0, ',', ' ') : '—' }}
                        <small>FCFA</small>
                    </span>
                </div>
            </div>

            {{-- ═══ CODE DE VISITE ═══ --}}
            @if($peutValiderCode)
                <div class="ios-section-header">Code de visite</div>

                <form action="{{ route('agence.rendezvous.valider-code', $rendezVous) }}"
                      method="POST" class="ios-code-form">
                    @csrf
                    <div class="ios-code-form__icon">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div class="ios-code-form__body">
                        <label for="code-{{ $rendezVous->id }}" class="ios-code-form__label">
                            Valider la visite avec le code
                        </label>
                        <p class="ios-code-form__hint">
                            Demandez le code à 6 chiffres au client pour marquer la visite comme terminée.
                        </p>
                        <div class="ios-code-form__input-wrapper">
                            <input
                                type="text"
                                id="code-{{ $rendezVous->id }}"
                                name="code_visite"
                                class="ios-code-form__input"
                                placeholder="000000"
                                maxlength="6"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                autocomplete="off"
                                required
                            >
                            <button type="submit" class="ios-code-form__btn">
                                Valider
                            </button>
                        </div>
                    </div>
                </form>

            @elseif($rendezVous->code_valide)
                <div class="ios-section-header">Code de visite</div>

                <div class="ios-group">
                    <div class="ios-cell">
                        <div class="ios-cell__icon ios-cell__icon--green">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div class="ios-cell__body">
                            <span class="ios-cell__label ios-cell__label--strong">
                                Visite validée
                            </span>
                            <span class="ios-cell__sublabel">
                                Code validé le
                                {{ $rendezVous->code_valide_le ? $rendezVous->code_valide_le->format('d/m/Y à H:i') : '—' }}
                            </span>
                        </div>
                    </div>
                </div>

            @elseif($statutValue === 'confirme' && !$fenetreValidationOuverte)
                <div class="ios-section-header">Code de visite</div>

                <div class="ios-group">
                    <div class="ios-cell">
                        <div class="ios-cell__icon ios-cell__icon--orange">
                            <i class="fa-solid fa-hourglass-half"></i>
                        </div>
                        <div class="ios-cell__body">
                            <span class="ios-cell__label ios-cell__label--strong">
                                En attente de la visite
                            </span>
                            <span class="ios-cell__sublabel">
                                La saisie du code sera possible 24h avant la visite.
                            </span>
                        </div>
                    </div>
                </div>
            @endif

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
                    <div class="ios-video-list" style="margin-top: 8px;">
                        @foreach($videos as $media)
                            <div class="ios-video-tile">
                                <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata">
                                    Votre navigateur ne supporte pas la vidéo.
                                </video>
                            </div>
                        @endforeach
                    </div>
                @endif
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
            </div>

            {{-- ═══ ACTIONS ═══ --}}
            <div class="ios-section-header">Actions</div>

            <div class="ios-actions-stack">

                @if($statutValue === 'planifie')
                    <form action="{{ route('agence.rendezvous.confirmer', $rendezVous) }}" method="POST">
                        @csrf
                        <button type="submit" class="ios-btn ios-btn--primary ios-btn--full">
                            Confirmer le rendez-vous
                        </button>
                    </form>

                    @if($hasTel)
                        <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="ios-btn ios-btn--green ios-btn--full">
                            <i class="fa-solid fa-phone"></i> Appeler le client
                        </a>
                    @endif

                    <form action="{{ route('agence.rendezvous.annuler', $rendezVous) }}" method="POST">
                        @csrf
                        <button type="submit" class="ios-btn ios-btn--red ios-btn--full"
                                onclick="return confirm('Annuler ce rendez-vous ?\n\nToute la sélection sera réinitialisée.')">
                            Annuler
                        </button>
                    </form>

                @elseif($statutValue === 'confirme')
                    @if($hasTel)
                        <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="ios-btn ios-btn--green ios-btn--full">
                            <i class="fa-solid fa-phone"></i> Appeler le client
                        </a>
                    @endif

                    @if($peutMarquerAbsent)
                        <form action="{{ route('agence.rendezvous.absent', $rendezVous) }}" method="POST">
                            @csrf
                            <button type="submit" class="ios-btn ios-btn--gray ios-btn--full"
                                    onclick="return confirm('Marquer le client comme ABSENT ?\n\nLe RDV passera à CLIENT_ABSENT.\nLe particulier pourra replanifier.')">
                                Client absent
                            </button>
                        </form>
                    @endif

                    @if(!$rendezVous->code_valide)
                        <form action="{{ route('agence.rendezvous.annuler', $rendezVous) }}" method="POST">
                            @csrf
                            <button type="submit" class="ios-btn ios-btn--red ios-btn--full"
                                    onclick="return confirm('Annuler ce rendez-vous ?\n\nToute la sélection sera réinitialisée.')">
                                Annuler
                            </button>
                        </form>
                    @endif

                @elseif($statutValue === 'termine')
                    <div class="ios-state-pill ios-state-pill--success">
                        Visite terminée
                    </div>

                    @if($hasTel)
                        <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="ios-btn ios-btn--green ios-btn--full">
                            <i class="fa-solid fa-phone"></i> Appeler le client
                        </a>
                    @endif

                @elseif($statutValue === 'annule')
                    <div class="ios-state-pill ios-state-pill--danger">
                        Rendez-vous annulé
                    </div>

                @elseif($statutValue === 'client_absent')
                    <div class="ios-state-pill ios-state-pill--muted">
                        Client absent
                    </div>
                @endif

                <a href="{{ route('agence.rendezvous.index') }}" class="ios-btn ios-btn--gray ios-btn--full">
                    Tous mes rendez-vous
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
    if (lbEl) {
        lbEl.addEventListener('touchstart', e => { touchStartX = e.changedTouches[0].screenX; });
        lbEl.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchEndX - touchStartX;
            if (Math.abs(diff) > 50) navigateLightbox(diff > 0 ? -1 : 1);
        });
    }
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DÉTAIL RDV (Agence) — Design épuré
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
    /* ✅ CORRECTION : empêcher le débordement horizontal */
    max-width: 100%;
    overflow-x: hidden;
}

/* ═══ ALERTS ═══ */
.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 14px;
    border-left: 3px solid;
    font-size: 13.5px;
    font-weight: 500;
    word-break: break-word;
}
.alert-success { background: rgba(52, 199, 89, 0.12); color: #175C35; border-left-color: var(--ios-green); }
.alert-error   { background: rgba(255, 59, 48, 0.12); color: #8B1A1A; border-left-color: var(--ios-red); }
.alert-info    { background: rgba(0, 122, 255, 0.12); color: #0040DD; border-left-color: var(--ios-blue); }

/* ═══ HERO ═══ */
.ios-rdv-hero {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    margin-bottom: 20px;
    flex-wrap: wrap;
    /* ✅ CORRECTION */
    max-width: 100%;
    overflow: hidden;
}
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
    color: var(--ios-label-2);
    margin-bottom: 3px;
}
.ios-rdv-hero__title {
    font-size: 17px;
    font-weight: 600;
    margin: 0 0 4px;
    color: var(--ios-label);
    line-height: 1.25;
    letter-spacing: -0.3px;
    word-break: break-word;
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
    background: var(--ios-gray-5);
    color: var(--ios-label-2);
}
.ios-badge--planifie      { background: rgba(255, 149, 0, 0.15); color: #C93400; }
.ios-badge--confirme      { background: rgba(0, 122, 255, 0.15); color: #0040DD; }
.ios-badge--annule        { background: rgba(255, 59, 48, 0.15); color: #D70015; }
.ios-badge--termine       { background: rgba(52, 199, 89, 0.15); color: #007A33; }
.ios-badge--client_absent { background: rgba(142, 142, 147, 0.15); color: #6C6C70; }

/* ═══ BANNIÈRE ═══ */
.ios-banner {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    border-radius: var(--ios-radius-lg);
    margin-bottom: 20px;
    background: var(--ios-gray-6);
    /* ✅ CORRECTION */
    max-width: 100%;
    overflow: hidden;
    word-break: break-word;
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
    background: var(--ios-gray);
}
.ios-banner__body { min-width: 0; flex: 1; }
.ios-banner__body strong {
    display: block;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 2px;
    letter-spacing: -0.2px;
    color: var(--ios-label);
}
.ios-banner__body p {
    font-size: 13px;
    margin: 0;
    line-height: 1.45;
    color: var(--ios-label-2);
}
.ios-banner--warning { background: rgba(255, 149, 0, 0.1); }
.ios-banner--warning .ios-banner__icon { background: var(--ios-orange); }
.ios-banner--success { background: rgba(52, 199, 89, 0.1); }
.ios-banner--success .ios-banner__icon { background: var(--ios-green); }
.ios-banner--muted { background: rgba(142, 142, 147, 0.1); }
.ios-banner--muted .ios-banner__icon { background: var(--ios-gray); }

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
    /* ✅ CORRECTION */
    max-width: 100%;
}
.ios-cell {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 16px;
    min-height: 44px;
    position: relative;
    background: var(--ios-card);
    /* ✅ CORRECTION */
    max-width: 100%;
    overflow: hidden;
    flex-wrap: wrap;
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
    background: var(--ios-gray);
}
.ios-cell__icon--blue   { background: var(--ios-blue); }
.ios-cell__icon--green  { background: var(--ios-green); }
.ios-cell__icon--orange { background: var(--ios-orange); }
.ios-cell__icon--red    { background: var(--ios-red); }
.ios-cell__icon--gray   { background: var(--ios-gray); }
.ios-cell__icon--rust   { background: var(--rust); }

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
    word-break: break-word;
}
.ios-cell__label--strong { font-weight: 600; }
.ios-cell__sublabel {
    font-size: 13px;
    color: var(--ios-label-2);
    letter-spacing: -0.1px;
    word-break: break-word;
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
    background: var(--ios-gray-6);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    font-weight: 600;
    flex-shrink: 0;
    letter-spacing: -0.3px;
    border: 1px solid var(--ios-separator);
}

/* ═══ FORMULAIRE CODE ═══ */
.ios-code-form {
    display: flex;
    gap: 14px;
    padding: 18px;
    background: var(--ios-card);
    border: 1px solid var(--ios-separator);
    border-radius: var(--ios-radius-lg);
    margin-bottom: 4px;
    /* ✅ CORRECTION */
    max-width: 100%;
    overflow: hidden;
    flex-wrap: wrap;
}
.ios-code-form__icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: var(--ios-gray-6);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.ios-code-form__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.ios-code-form__label {
    font-size: 15px;
    font-weight: 600;
    color: var(--ios-label);
    letter-spacing: -.2px;
}
.ios-code-form__hint {
    font-size: 13px;
    color: var(--ios-label-2);
    margin: 0;
    line-height: 1.5;
    word-break: break-word;
}
.ios-code-form__input-wrapper {
    display: flex;
    gap: 10px;
    margin-top: 4px;
    /* ✅ CORRECTION */
    flex-wrap: wrap;
}
.ios-code-form__input {
    flex: 1;
    min-width: 0;
    padding: 12px 16px;
    font-family: inherit;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 8px;
    color: var(--ios-label);
    background: var(--ios-gray-6);
    border: 1px solid var(--ios-separator);
    border-radius: 12px;
    text-align: center;
    transition: border-color .15s;
    outline: none;
    /* ✅ CORRECTION : empêcher le débordement */
    max-width: 100%;
    box-sizing: border-box;
}
.ios-code-form__input::placeholder {
    color: var(--ios-label-3);
    letter-spacing: 8px;
    font-weight: 600;
}
.ios-code-form__input:focus {
    border-color: var(--rust);
}
.ios-code-form__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 22px;
    background: var(--rust);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-family: inherit;
    font-size: 14.5px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color .15s;
    white-space: nowrap;
}
.ios-code-form__btn:hover { background: #9A4523; }

/* ═══ GALERIE ═══ */
.ios-gallery {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    overflow: hidden;
    margin-bottom: 4px;
    /* ✅ CORRECTION */
    max-width: 100%;
}
.ios-gallery__item {
    position: relative;
    aspect-ratio: 1;
    cursor: pointer;
    background: var(--ios-gray-6);
    overflow: hidden;
}
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
    transition: background-color .15s, transform .1s;
    white-space: nowrap;
    min-height: 48px;
    width: 100%;
    /* ✅ CORRECTION */
    max-width: 100%;
    box-sizing: border-box;
}
.ios-btn:not(:disabled):active { transform: scale(0.98); }

.ios-btn--primary { background: var(--rust); color: #fff; }
.ios-btn--primary:hover { background: #9A4523; }

.ios-btn--green   { background: var(--ios-green); color: #fff; }
.ios-btn--green:hover { background: #28A745; }

.ios-btn--red     { background: var(--ios-red); color: #fff; }
.ios-btn--red:hover { background: #D62D25; }

.ios-btn--gray {
    background: var(--ios-gray-5);
    color: var(--ios-blue);
}
.ios-btn--gray:hover { background: #D9D9DE; }

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
    background: var(--ios-gray-6);
    color: var(--ios-label-2);
    /* ✅ CORRECTION */
    max-width: 100%;
    box-sizing: border-box;
}
.ios-state-pill--success {
    background: rgba(52, 199, 89, 0.12);
    color: #175C35;
}
.ios-state-pill--danger {
    background: rgba(255, 59, 48, 0.12);
    color: #8B1A1A;
}
.ios-state-pill--muted {
    background: rgba(142, 142, 147, 0.12);
    color: #48484A;
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

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

.ios-rdv-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 20px;
    align-items: start;
    /* ✅ CORRECTION */
    max-width: 100%;
}
.ios-rdv-layout__main { min-width: 0; max-width: 100%; }
.ios-rdv-layout__side { min-width: 0; max-width: 100%; }

@media (min-width: 821px) {
    .ios-rdv-show {
        max-width: 1100px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }
}

@media (max-width: 1100px) and (min-width: 821px) {
    .ios-rdv-layout {
        grid-template-columns: 1fr;
        gap: 16px;
    }
}

@media (max-width: 820px) {
    .ios-rdv-show {
        /* ✅ CORRECTION : plus de marges négatives */
        margin: 0;
        padding: 0 0 40px;
        width: 100%;
        overflow-x: hidden;
    }

    .ios-rdv-layout {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .ios-rdv-layout__main,
    .ios-rdv-layout__side {
        display: contents;
    }

    .ios-code-form {
        flex-direction: column;
        gap: 12px;
        padding: 16px;
    }
    .ios-code-form__icon {
        width: 38px;
        height: 38px;
        font-size: 17px;
    }
    .ios-code-form__input-wrapper {
        flex-direction: column;
    }
    .ios-code-form__input {
        padding: 14px;
        font-size: 24px;
        letter-spacing: 10px;
        /* ✅ CORRECTION : largeur fixe pour éviter le débordement */
        width: 100%;
        max-width: 100%;
    }
    .ios-code-form__input::placeholder {
        font-size: 24px;
        letter-spacing: 10px;
    }
    .ios-code-form__btn {
        width: 100%;
        padding: 14px 20px;
    }

    /* ✅ CORRECTION : ajuster la galerie sur mobile */
    .ios-gallery {
        grid-template-columns: repeat(3, 1fr);
        width: 100%;
    }

    /* ✅ CORRECTION : ajuster le hero sur mobile */
    .ios-rdv-hero {
        padding: 14px;
    }
    .ios-rdv-hero__date {
        width: 64px;
        padding: 8px 4px;
    }
    .ios-rdv-hero__day {
        font-size: 24px;
    }
    .ios-rdv-hero__title {
        font-size: 16px;
    }

    /* ✅ CORRECTION : ajuster les cellules sur mobile */
    .ios-cell {
        padding: 11px 14px;
        flex-wrap: wrap;
    }
    .ios-cell__value {
        max-width: 60%;
        font-size: 14px;
    }
    .ios-cell__label {
        font-size: 14px;
    }
    .ios-cell__sublabel {
        font-size: 12px;
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