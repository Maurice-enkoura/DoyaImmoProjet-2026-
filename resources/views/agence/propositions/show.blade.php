@extends('layouts.dashboard-agence')

@section('title', 'Détail de l\'offre — DoyaImmo')
@section('page_title', 'Détail de l\'offre')
@section('page_sub', 'Informations complètes sur la proposition')

@section('content')
<div class="view active offre-detail">

    @php
        use App\Enums\StatutPropositionEnum;

        $bien       = $proposition->bien;
        $isVedette  = $bien && $bien->est_vedette;
        $statut     = is_object($proposition->statut) ? $proposition->statut->value : $proposition->statut;
        $statutLabel = is_object($proposition->statut) ? $proposition->statut->label() : ucfirst($proposition->statut);
        $score      = (int) ($proposition->score_matching ?? 0);

        $prenom  = $proposition->particulier->user->prenom ?? '';
        $nom     = $proposition->particulier->user->nom    ?? '';
        $initial = strtoupper(mb_substr($prenom ?: 'C', 0, 1));

        // ✅ Téléphone client (robuste)
        $clientTelephone = $proposition->particulier->user->telephone
            ?? $proposition->particulier->telephone
            ?? null;

        // ✅ Médias
        $images     = $proposition->medias->where('type_media', 'image')->values();
        $videos     = $proposition->medias->where('type_media', 'video')->values();
        $bienImages = $bien ? $bien->medias->where('type_media', 'image')->values() : collect();
        $bienVideos = $bien ? $bien->medias->where('type_media', 'video')->values() : collect();

        $lightboxImages = collect()
            ->merge($images->map(fn($m) => asset('storage/' . $m->fichier)))
            ->merge($bienImages->map(fn($m) => asset('storage/' . $m->fichier)))
            ->values()
            ->all();

        $hasConditions = $proposition->caution_mois !== null
            || $proposition->avance_loyer_mois !== null
            || $proposition->honoraires !== null
            || $proposition->charges !== null
            || $proposition->disponibilite !== null
            || $proposition->conditions_particulieres !== null;

        // ✅ RDV lié
        $rendezVous = $proposition->rendezVous->sortByDesc('created_at')->first();
        $hasRdv     = $rendezVous !== null;
        $rdvStatut  = $rendezVous && is_object($rendezVous->statut)
            ? $rendezVous->statut->value
            : ($rendezVous->statut ?? null);
        $rdvStatutLabel = $rendezVous && is_object($rendezVous->statut)
            ? $rendezVous->statut->label()
            : ucfirst((string) $rdvStatut);

        // ✅ Peut-on annuler ?
        $peutAnnuler = in_array($statut, ['en_attente', 'acceptee']) && !$hasRdv;

        // ✅ Proposition réactivée ?
        $estReactivee = $statut === 'en_attente'
            && $proposition->updated_at->gt($proposition->created_at->copy()->addMinute());
    @endphp

    {{-- ═══ HERO ═══ --}}
    <header class="offre-hero">
        <div class="offre-hero__badges">
            <span class="offre-status offre-status--{{ $statut }}">
                {{ $statutLabel }}
            </span>

            @if($estReactivee)
                <span class="offre-badge">
                    <i class="fa-solid fa-rotate-right"></i> Réactivée
                </span>
            @endif

            @if($isVedette)
                <span class="offre-badge">
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
                <strong>{{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }} {{ $proposition->demande->type_operation->value === 'location' ? 'F/mois' : 'F' }}</strong>
            </span>
            <span class="offre-hero__meta-sep">•</span>
            <span>
                <i class="fa-regular fa-calendar"></i>
                Envoyée le {{ $proposition->created_at->format('d/m/Y') }}
            </span>
        </div>

        @if($score)
            <div class="offre-hero__score">
                <div class="score-ring score-ring--lg" style="--score: {{ $score }};">
                    <svg viewBox="0 0 36 36" aria-hidden="true">
                        <circle class="score-ring__bg" cx="18" cy="18" r="16"/>
                        <circle class="score-ring__fill" cx="18" cy="18" r="16"/>
                    </svg>
                    <span class="score-ring__value">{{ $score }}<small>%</small></span>
                </div>
                <div class="offre-hero__score-text">
                    <span class="score-label">
                        {{ $score >= 80 ? 'Excellent match' : ($score >= 60 ? 'Bon match' : 'Match faible') }}
                    </span>
                    <span class="offre-hero__score-hint">Compatibilité</span>
                </div>
            </div>
        @endif
    </header>

    {{-- ═══ RDV PLANIFIÉ ═══ --}}
    @if($hasRdv)
        <div class="ios-section-title">
            <i class="fa-regular fa-calendar-check"></i> Rendez-vous planifié
        </div>

        <div class="rdv-card">
            <div class="rdv-card__header">
                <span class="rdv-status rdv-status--{{ $rdvStatut }}">
                    {{ $rdvStatutLabel }}
                </span>
                <span class="rdv-card__code">
                    <i class="fa-solid fa-key"></i>
                    Code : <strong>{{ $rendezVous->code_visite ?? '—' }}</strong>
                </span>
            </div>

            <div class="rdv-card__body">
                <div class="rdv-info">
                    <i class="fa-regular fa-calendar"></i>
                    <div>
                        <span class="rdv-info__label">Date de visite</span>
                        <span class="rdv-info__value">
                            {{ $rendezVous->date_visite ? $rendezVous->date_visite->format('d/m/Y') : '—' }}
                        </span>
                    </div>
                </div>
                <div class="rdv-info">
                    <i class="fa-regular fa-clock"></i>
                    <div>
                        <span class="rdv-info__label">Heure</span>
                        <span class="rdv-info__value">{{ $rendezVous->heure_visite ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('agence.rendezvous.show', $rendezVous) }}" class="btn-rust btn-block">
                <i class="fa-solid fa-eye"></i> Voir le rendez-vous
            </a>
        </div>
    @endif

    {{-- ═══ CLIENT ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-user"></i> Client
    </div>

    <div class="client-card">
        <div class="client-card__avatar">{{ $initial }}</div>

        <div class="client-card__info">
            <h3>{{ $prenom }} {{ $nom }}</h3>

            @if($hasRdv && $rdvStatut === 'confirme' && $clientTelephone)
                {{-- ✅ RDV confirmé : téléphone visible --}}
                <a href="tel:{{ preg_replace('/\s+/', '', $clientTelephone) }}" class="client-card__phone">
                    <i class="fa-solid fa-phone"></i>
                    {{ $clientTelephone }}
                </a>
            @elseif($hasRdv && $rdvStatut === 'confirme' && !$clientTelephone)
                <p class="client-card__note">Téléphone non renseigné</p>
            @else
                <p class="client-card__note">
                    <i class="fa-solid fa-shield-halved"></i>
                    Les coordonnées seront visibles après confirmation du rendez-vous
                </p>
            @endif
        </div>
    </div>

    {{-- ═══ PRIX PROPOSÉ ═══ --}}
    <div class="prix-card">
        <span class="prix-card__label">Prix proposé</span>
        <span class="prix-card__value">
            {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
            <small>FCFA</small>
        </span>
    </div>

    {{-- ═══ CONDITIONS COMMERCIALES ═══ --}}
    @if($hasConditions)
        <div class="ios-section-title">
            <i class="fa-solid fa-file-signature"></i> Conditions commerciales
        </div>

        <div class="ios-list">
            @if($proposition->disponibilite)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Disponibilité</span>
                    </div>
                    <span class="ios-list__value">{{ $proposition->disponibilite_label }}</span>
                </div>
            @endif

            @if($proposition->caution_mois !== null)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Caution</span>
                    </div>
                    <span class="ios-list__value">{{ $proposition->caution_label ?? 'Aucune' }}</span>
                </div>
            @endif

            @if($proposition->avance_loyer_mois !== null)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Avance de loyer</span>
                    </div>
                    <span class="ios-list__value">{{ $proposition->avance_label ?? 'Aucune' }}</span>
                </div>
            @endif

            @if($proposition->honoraires !== null)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Honoraires</span>
                    </div>
                    <span class="ios-list__value">{{ $proposition->honoraires_formate }}</span>
                </div>
            @endif

            @if($proposition->charges !== null)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Charges</span>
                    </div>
                    <span class="ios-list__value">{{ $proposition->charges_formate }}</span>
                </div>
            @endif
        </div>

        @if($proposition->cout_entree_total > 0)
            <div class="conditions-total">
                <div class="conditions-total__left">
                    <i class="fa-solid fa-coins"></i>
                    <span class="conditions-total__label">Coût total à l'entrée</span>
                </div>
                <span class="conditions-total__value">
                    {{ $proposition->cout_entree_total_formate }}
                </span>
            </div>
        @endif

        @if($proposition->conditions_particulieres)
            <blockquote class="quote-block quote-block--italic">
                {{ $proposition->conditions_particulieres }}
            </blockquote>
        @endif
    @endif

    {{-- ═══ MESSAGE ═══ --}}
    @if($proposition->message)
        <div class="ios-section-title">
            <i class="fa-regular fa-message"></i> Message envoyé
        </div>
        <blockquote class="quote-block">
            {{ $proposition->message }}
        </blockquote>
    @endif

    {{-- ═══ PHOTOS PROPOSITION ═══ --}}
    @if($images->count() > 0)
        <div class="ios-section-title">
            <i class="fa-regular fa-image"></i> Photos de la proposition
            <span class="ios-section-count">{{ $images->count() }}</span>
        </div>

        <div class="gallery-grid">
            @foreach($images as $index => $media)
                @php $url = asset('storage/' . $media->fichier); @endphp
                <div class="gallery-item" onclick="openLightbox('{{ $url }}', {{ $index }})">
                    <img src="{{ $url }}" alt="Photo" loading="lazy">
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ VIDÉOS PROPOSITION ═══ --}}
    @if($videos->count() > 0)
        <div class="ios-section-title">
            <i class="fa-regular fa-circle-play"></i> Vidéos de la proposition
            <span class="ios-section-count">{{ $videos->count() }}</span>
        </div>

        <div class="video-grid">
            @foreach($videos as $media)
                <div class="video-tile">
                    <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata"></video>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ BIEN PROPOSÉ ═══ --}}
    @if($bien)
        <div class="ios-section-title">
            <i class="fa-regular fa-building"></i> Bien proposé
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
                    {{ $bien->parking_disponible ? 'Disponible' : 'Non' }}
                </span>
            </div>

            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Meublé</span>
                </div>
                <span class="ios-list__value">
                    {{ $bien->est_meuble ? 'Oui' : 'Non' }}
                </span>
            </div>
        </div>

        @if($bien->adresse)
            <div class="address-card">
                <i class="fa-solid fa-location-dot"></i>
                <span>{{ $bien->adresse }}</span>
            </div>
        @endif

        @php $equipementsBien = $bien->equipements ?? []; @endphp
        @if(count($equipementsBien) > 0)
            <div class="ios-section-title">
                <i class="fa-solid fa-cogs"></i> Équipements
                <span class="ios-section-count">{{ count($equipementsBien) }}</span>
            </div>

            <div class="equipements-grid">
                @foreach($equipementsBien as $equipement)
                    <span class="equipement-pill">
                        <i class="fa-solid fa-check"></i> {{ $equipement }}
                    </span>
                @endforeach
            </div>
        @endif

        @if($bien->description)
            <div class="ios-section-title">
                <i class="fa-regular fa-file-lines"></i> Description du bien
            </div>
            <blockquote class="quote-block quote-block--italic">
                {{ $bien->description }}
            </blockquote>
        @endif

        @if($bienImages->count() > 0)
            <div class="ios-section-title">
                <i class="fa-regular fa-image"></i> Photos du bien sur la plateforme
                <span class="ios-section-count">{{ $bienImages->count() }}</span>
            </div>

            <div class="gallery-grid">
                @foreach($bienImages as $index => $media)
                    @php
                        $url = asset('storage/' . $media->fichier);
                        $globalIndex = $images->count() + $index;
                    @endphp
                    <div class="gallery-item" onclick="openLightbox('{{ $url }}', {{ $globalIndex }})">
                        <img src="{{ $url }}" alt="Photo du bien" loading="lazy">
                    </div>
                @endforeach
            </div>
        @endif

        @if($bienVideos->count() > 0)
            <div class="ios-section-title">
                <i class="fa-regular fa-circle-play"></i> Vidéos du bien sur la plateforme
                <span class="ios-section-count">{{ $bienVideos->count() }}</span>
            </div>

            <div class="video-grid">
                @foreach($bienVideos as $media)
                    <div class="video-tile">
                        <video src="{{ asset('storage/' . $media->fichier) }}" controls preload="metadata"></video>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- ═══ ACTIONS FIXES ═══ --}}
    <div class="ios-form-actions">
        <a href="{{ route('agence.propositions.index') }}" class="ios-btn ios-btn--ghost">
            <i class="fa-solid fa-list"></i>
            Toutes mes offres
        </a>

        @if($peutAnnuler)
            <form action="{{ route('agence.propositions.annuler', $proposition) }}"
                  method="POST"
                  class="ios-form-actions__form">
                @csrf
                <button type="submit" class="ios-btn ios-btn--danger"
                        onclick="return confirm('⚠️ Annuler cette proposition ?\n\n{{ $statut === 'acceptee' ? 'La demande du particulier repassera en attente.' : 'Cette offre sera retirée.' }}')">
                    <i class="fa-solid fa-xmark"></i>
                    Annuler l'offre
                </button>
            </form>
        @endif
    </div>
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
   DÉTAIL OFFRE (Agence) — Design épuré
   ═══════════════════════════════════════════════════════════ */

.offre-detail {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #F1F8F4;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --c-info:       #1E40AF;
    --c-info-bg:    #EFF6FF;
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    color: var(--ink);
    margin: 22px 0 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: -.2px;
}
.ios-section-title i { color: var(--muted); font-size: 14px; }
.ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 9px;
    background: #F5F5F7;
    color: var(--text-soft);
    border-radius: 999px;
}

/* ═══ HERO ═══ */
.offre-hero {
    padding: 20px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
}

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
    color: var(--ink);
    letter-spacing: -.3px;
}
.offre-hero__title-sep {
    color: var(--border);
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
}
.offre-hero__meta strong { color: var(--ink); font-weight: 600; }
.offre-hero__meta-sep { color: var(--border); }

.offre-hero__score {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
}
.offre-hero__score-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.offre-hero__score-hint {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}

/* ═══ SCORE RING ═══ */
.score-ring {
    position: relative;
    width: 60px;
    height: 60px;
}
.score-ring--lg { width: 64px; height: 64px; }
.score-ring svg {
    transform: rotate(-90deg);
    width: 100%; height: 100%;
    display: block;
}
.score-ring circle { fill: none; stroke-width: 3; }
.score-ring__bg { stroke: var(--border); }
.score-ring__fill {
    stroke-linecap: round;
    stroke: var(--rust);
    stroke-dasharray: calc(var(--score, 0) * 1.005) 100.5;
    transition: stroke-dasharray .6s ease;
}

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
    color: var(--ink);
}
.score-ring--lg .score-ring__value { font-size: 17px; }
.score-ring__value small { font-size: 9px; margin-left: 1px; font-weight: 700; color: var(--muted); }
.score-ring--lg .score-ring__value small { font-size: 10px; }

.score-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
}

/* ═══ STATUTS ═══ */
.offre-status {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
    background: #F5F5F7;
    color: var(--text-soft);
}
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.offre-status--terminee   { background: var(--c-info-bg);    color: var(--c-info); }

.offre-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 500;
    white-space: nowrap;
    background: #F5F5F7;
    color: var(--text-soft);
}
.offre-badge i { font-size: 10px; color: var(--muted); }

/* ═══ RDV CARD ═══ */
.rdv-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 18px;
    margin-bottom: 14px;
}

.rdv-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.rdv-status {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    background: #F5F5F7;
    color: var(--text-soft);
}
.rdv-status--planifie { background: var(--c-warning-bg); color: var(--c-warning); }
.rdv-status--confirme { background: var(--c-info-bg);    color: var(--c-info); }
.rdv-status--termine  { background: var(--c-success-bg); color: var(--c-success); }
.rdv-status--annule   { background: var(--c-danger-bg);  color: var(--c-danger); }

.rdv-card__code {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--text-soft);
    padding: 5px 11px;
    background: #F5F5F7;
    border-radius: 8px;
}
.rdv-card__code i { color: var(--muted); font-size: 11px; }
.rdv-card__code strong {
    font-family: monospace;
    font-size: 14px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: 1.5px;
}

.rdv-card__body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 14px;
}
.rdv-info {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    background: #F5F5F7;
    border-radius: 10px;
}
.rdv-info > i {
    width: 34px; height: 34px;
    border-radius: 8px;
    background: #fff;
    color: var(--muted);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex-shrink: 0;
}
.rdv-info__label {
    display: block;
    font-size: 10.5px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}
.rdv-info__value {
    display: block;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    margin-top: 2px;
}

/* ═══ CLIENT CARD ═══ */
.client-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
}
.client-card__avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 18px;
    font-weight: 700;
    flex-shrink: 0;
}
.client-card__info { flex: 1; min-width: 0; }
.client-card__info h3 {
    font-family: var(--display);
    font-size: 15.5px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
    letter-spacing: -.2px;
}

.client-card__note {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin: 6px 0 0;
    font-size: 12.5px;
    color: var(--muted);
    line-height: 1.5;
}
.client-card__note i {
    font-size: 11px;
    color: var(--muted);
}

.client-card__phone {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-soft);
    text-decoration: none;
    transition: color .15s;
}
.client-card__phone:hover { color: var(--rust); }
.client-card__phone i { font-size: 11px; color: var(--muted); }

/* ═══ PRIX CARD ═══ */
.prix-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.prix-card__label {
    font-size: 12.5px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
}
.prix-card__value {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
    letter-spacing: -.4px;
}
.prix-card__value small {
    font-size: 13px;
    font-weight: 600;
    opacity: .7;
    margin-left: 3px;
}

/* ═══ CONDITIONS ═══ */
.conditions-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 14px 18px;
    background: #F5F5F7;
    border-radius: 12px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.conditions-total__left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
}
.conditions-total__left > i {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: #fff;
    color: var(--muted);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.conditions-total__label {
    display: block;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink);
}
.conditions-total__value {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    letter-spacing: -.4px;
}

/* ═══ LISTE iOS ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
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
.ios-list__item--static { cursor: default; }
.ios-list__content { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.ios-list__title {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink);
    text-align: right;
    flex-shrink: 0;
}

/* ═══ ADRESSE ═══ */
.address-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    font-size: 13.5px;
    color: var(--text-soft);
}
.address-card i {
    width: 34px; height: 34px;
    border-radius: 9px;
    background: #F5F5F7;
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}

/* ═══ QUOTES ═══ */
.quote-block {
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    line-height: 1.7;
    color: var(--text-soft);
    margin: 0 0 14px;
    background: #fff;
    border: 1px solid var(--border);
    border-left: 3px solid #d8d8d8;
}
.quote-block--italic { font-style: italic; }

/* ═══ GALERIE ═══ */
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 8px;
    margin-bottom: 14px;
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

/* ═══ VIDÉOS ═══ */
.video-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 10px;
    margin-bottom: 14px;
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

/* ═══ ÉQUIPEMENTS ═══ */
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
    padding: 5px 12px;
    background: #F5F5F7;
    color: var(--text-soft);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
}
.equipement-pill i { font-size: 10px; color: var(--muted); }

/* ═══ ACTIONS ═══ */
.ios-form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    margin-top: 20px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.ios-form-actions__form { flex: 1; margin: 0; }
.ios-form-actions__form .ios-btn { width: 100%; }
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
    flex: 1;
}
.btn-block { display: inline-flex; width: 100%; justify-content: center; }
.btn-rust {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 18px;
    background: var(--rust);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background-color .15s;
}
.btn-rust:hover { background: #9A4523; color: #fff; }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
    flex: 0 0 auto;
    padding: 12px 18px;
}
.ios-btn--ghost:hover {
    background: #F5F5F7;
    border-color: #d8d8d8;
    color: var(--ink);
}
.ios-btn--danger {
    background: #fff;
    color: var(--c-danger);
    border-color: var(--border);
}
.ios-btn--danger:hover { background: var(--c-danger-bg); border-color: #f5c6c6; }

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
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: sans-serif;
    z-index: 2;
    transition: background-color .2s;
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
    transition: background-color .2s;
}
.lightbox__nav:hover { background: rgba(255, 255, 255, .28); }
.lightbox__nav--prev { left: 24px; }
.lightbox__nav--next { right: 24px; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .offre-detail { background: transparent; padding-top: 4px; }

    .ios-section-title {
        font-size: 15px;
        margin: 20px 4px 10px;
        color: var(--ink);
    }
    .ios-section-title i { font-size: 13px; }

    .offre-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .offre-hero__badges { gap: 6px; margin-bottom: 8px; }
    .offre-status { font-size: 11.5px; padding: 3px 10px; }
    .offre-badge { font-size: 11px; padding: 3px 9px; }

    .offre-hero__title {
        font-size: 20px;
        letter-spacing: -.3px;
        margin-bottom: 8px;
    }
    .offre-hero__meta {
        font-size: 13px;
        gap: 6px;
    }
    .offre-hero__meta-sep { display: none; }

    .offre-hero__score {
        gap: 12px;
        padding-top: 14px;
        margin-top: 14px;
        border-top: 0.5px solid var(--ios-separator);
    }
    .score-ring--lg { width: 58px; height: 58px; }
    .score-ring--lg .score-ring__value { font-size: 15px; }
    .score-label { font-size: 14px; }
    .offre-hero__score-hint {
        font-size: 12px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 400;
    }

    .rdv-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .rdv-card__body {
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }
    .rdv-info { background: #F5F5F7; padding: 10px; gap: 8px; }
    .rdv-info > i { width: 32px; height: 32px; border: none; background: #fff; }
    .rdv-info__value { font-size: 13.5px; }

    .client-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
    }
    .client-card__avatar { width: 48px; height: 48px; font-size: 18px; background: #F5F5F7; border: none; }
    .client-card__info h3 { font-size: 16px; }
    .client-card__note { font-size: 13px; margin-top: 6px; }
    .client-card__phone { font-size: 13.5px; }

    .prix-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    .prix-card__label { font-size: 12px; text-transform: none; letter-spacing: 0; }
    .prix-card__value { font-size: 24px; align-self: flex-end; }

    .conditions-total {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 14px 16px;
        background: #F5F5F7;
        border-radius: 14px;
    }
    .conditions-total__left > i { background: #fff; border: none; }
    .conditions-total__value { font-size: 20px; align-self: flex-end; }

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
        font-size: 14px;
    }
    .ios-list__item:last-child { border-bottom: none; }
    .ios-list__title {
        font-size: 14px;
        color: var(--ink);
        font-weight: 500;
    }
    .ios-list__value {
        font-size: 14px;
        font-weight: 600;
        color: var(--muted);
    }

    .address-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
        font-size: 13.5px;
        gap: 10px;
    }
    .address-card i { background: #F5F5F7; }

    .quote-block {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid #d8d8d8;
        border-radius: 12px;
        padding: 16px 18px;
        font-size: 13.5px;
        line-height: 1.65;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
    }

    .gallery-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .gallery-item { border-radius: 12px; border: none; }

    .video-grid { grid-template-columns: 1fr; gap: 10px; margin-bottom: 12px; }
    .video-tile { border-radius: 12px; border: none; }

    .equipements-grid { gap: 8px; margin-bottom: 12px; }
    .equipement-pill {
        background: var(--ios-card);
        color: var(--text-soft);
        padding: 7px 13px;
        font-size: 12.5px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .ios-form-actions {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(242, 242, 247, 0.95);
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        border-top: 0.5px solid var(--ios-separator);
        padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
        margin: 0;
        gap: 10px;
        z-index: 100;
        border-radius: 0;
        flex-wrap: nowrap;
    }
    .ios-form-actions__form { flex: 1; }
    .ios-btn {
        padding: 13px 18px;
        font-size: 14.5px;
        border-radius: 12px;
        flex: 1;
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--text-soft);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex: 0 0 auto;
    }
    .ios-btn--danger {
        background: var(--ios-card);
        border: none;
        color: var(--c-danger);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .offre-detail {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }

    .lightbox__nav { width: 44px; height: 44px; font-size: 17px; }
    .lightbox__nav--prev { left: 12px; }
    .lightbox__nav--next { right: 12px; }
    .lightbox__close { top: 14px; right: 14px; width: 40px; height: 40px; font-size: 22px; }
}

@media (max-width: 480px) {
    .offre-hero { padding: 16px; }
    .offre-hero__title { font-size: 19px; }

    .score-ring--lg { width: 54px; height: 54px; }
    .score-ring--lg .score-ring__value { font-size: 14px; }
    .score-ring--lg .score-ring__value small { font-size: 9px; }

    .client-card__avatar { width: 44px; height: 44px; font-size: 17px; }
    .client-card__info h3 { font-size: 15px; }

    .prix-card__value { font-size: 22px; }
    .conditions-total__value { font-size: 18px; }

    .rdv-card__body { grid-template-columns: 1fr; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush