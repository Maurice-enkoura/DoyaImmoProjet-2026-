@extends('layouts.admin')

@section('title', 'Détail de la proposition — Administration DoyaImmo')
@section('page_title', 'Détail de la proposition')
@section('page_sub', 'Proposition de ' . ($proposition->agence->nom_agence ?? 'N/A'))

@section('content')
<div class="proposition-show">

    @php
        use App\Enums\StatutRendezVousEnum;

        $agence    = $proposition->agence;
        $demande   = $proposition->demande;
        $bien      = $proposition->bien;
        $particulier = $demande->particulier ?? null;
        $particulierUser = $particulier->user ?? null;
        $agenceUser = $agence->user ?? null;

        // Statut sécurisé
        $statutValue = is_object($proposition->statut)
            ? $proposition->statut->value
            : (string) $proposition->statut;
        $statutLabel = is_object($proposition->statut)
            ? $proposition->statut->label()
            : ucfirst($statutValue);

        // RDV actif ?
        $rendezVous = $proposition->rendezVous
            ? $proposition->rendezVous->sortByDesc('created_at')->first()
            : null;
        $hasRdv = $rendezVous !== null;
        $rdvStatutValue = $rendezVous && is_object($rendezVous->statut)
            ? $rendezVous->statut->value
            : ($rendezVous->statut ?? null);
        $rdvStatutLabel = $rendezVous && is_object($rendezVous->statut)
            ? $rendezVous->statut->label()
            : ucfirst((string) $rdvStatutValue);

        // Médias (proposition > bien)
        $propMedias = $proposition->medias ?? collect();
        $propImages = $propMedias->where('type_media', 'image')->values();
        $propVideos = $propMedias->where('type_media', 'video')->values();

        if ($propImages->count() === 0 && $propVideos->count() === 0 && $bien) {
            $bienMedias = $bien->medias ?? collect();
            $propImages = $bienMedias->where('type_media', 'image')->values();
            $propVideos = $bienMedias->where('type_media', 'video')->values();
        }

        // Fusion pour la lightbox
        $allMedia = collect();
        foreach ($propImages as $m) {
            $allMedia->push(['type' => 'image', 'url' => asset('storage/' . $m->fichier)]);
        }
        foreach ($propVideos as $m) {
            $allMedia->push(['type' => 'video', 'url' => asset('storage/' . $m->fichier)]);
        }
        $mediaJson = json_encode($allMedia);

        // Équipements
        $equipements = $bien->equipements ?? [];

        // Conditions commerciales présentes ?
        $hasConditions = $proposition->caution_mois !== null
            || $proposition->avance_loyer_mois !== null
            || $proposition->honoraires !== null
            || $proposition->charges !== null
            || $proposition->disponibilite !== null
            || $proposition->conditions_particulieres !== null;
    @endphp

    {{-- ═══ ACTIONS EN HAUT ═══ --}}
    <div class="page-actions">
        <a href="{{ route('admin.propositions.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux propositions
        </a>
        @if($demande)
            <a href="{{ route('admin.demandes.show', $demande->id) }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-file"></i> Voir la demande
            </a>
        @endif
        @if($bien)
            <a href="{{ route('admin.biens.show', $bien->id) }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-house"></i> Voir le bien
            </a>
        @endif
    </div>

    <div class="grid-2">

        {{-- ═══ COLONNE GAUCHE : INFOS PROPOSITION ═══ --}}
        <div>

            {{-- Carte principale --}}
            <div class="panel">
                <div class="panel-head">
                    <h3>
                        Proposition #{{ $proposition->id }}
                        <span class="panel-head__meta">
                            du {{ $proposition->created_at->format('d/m/Y à H:i') }}
                        </span>
                    </h3>
                    <span class="status-pill status-{{ $statutValue }}">
                        {{ $statutLabel }}
                    </span>
                </div>

                <div class="info-grid">

                    {{-- Agence --}}
                    <div class="info-block info-block--full">
                        <div class="info-block__label">Agence</div>
                        <div class="info-agency">
                            @if($agence && $agence->logo)
                                <img src="{{ asset('storage/' . $agence->logo) }}"
                                     alt="{{ $agence->nom_agence }}"
                                     class="info-agency__logo">
                            @else
                                <div class="info-agency__logo info-agency__logo--fallback">
                                    {{ $agence ? Str::substr($agence->nom_agence, 0, 2) : 'NA' }}
                                </div>
                            @endif
                            <div>
                                <div class="info-agency__name">{{ $agence->nom_agence ?? 'N/A' }}</div>
                                @if($agenceUser)
                                    <div class="info-agency__contact">
                                        <i class="fa-solid fa-envelope"></i> {{ $agenceUser->email ?? '—' }}
                                    </div>
                                    <div class="info-agency__contact">
                                        <i class="fa-solid fa-phone"></i> {{ $agenceUser->telephone ?? 'Non renseigné' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Demande --}}
                    @if($demande)
                        <div class="info-block info-block--full">
                            <div class="info-block__label">Demande associée</div>
                            <div class="info-block__value">
                                {{ is_object($demande->type_operation) ? $demande->type_operation->label() : ($demande->type_operation ?? 'N/A') }}
                                <span class="info-block__sep">·</span>
                                {{ is_object($demande->type_bien) ? $demande->type_bien->label() : ($demande->type_bien ?? 'N/A') }}
                            </div>
                            @if($particulierUser)
                                <div class="info-block__sub">
                                    <i class="fa-solid fa-user"></i>
                                    {{ $particulierUser->prenom ?? '' }} {{ $particulierUser->nom ?? '' }}
                                    <span class="info-block__sep">·</span>
                                    <i class="fa-solid fa-envelope"></i> {{ $particulierUser->email ?? '—' }}
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Prix --}}
                    <div class="info-block">
                        <div class="info-block__label">Prix proposé</div>
                        <div class="info-block__value info-block__value--price">
                            {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="info-block">
                        <div class="info-block__label">Reçue le</div>
                        <div class="info-block__value">
                            {{ $proposition->created_at->format('d/m/Y') }}
                        </div>
                    </div>

                    {{-- Bien proposé --}}
                    @if($bien)
                        <div class="info-block info-block--full">
                            <div class="info-block__label">Bien proposé</div>
                            <div class="info-block__value">
                                {{ $bien->titre ?? 'N/A' }}
                                <span class="meta-pill meta-pill--info">
                                    {{ is_object($bien->type_contrat) ? $bien->type_contrat->label() : ($bien->type_contrat ?? '—') }}
                                </span>
                            </div>
                            <div class="info-block__sub">
                                <i class="fa-solid fa-location-dot"></i>
                                {{ $bien->quartier ?? ($bien->adresse ?? 'N/A') }}
                                <span class="info-block__sep">·</span>
                                <i class="fa-solid fa-money-bill"></i>
                                {{ number_format($bien->prix ?? 0, 0, ',', ' ') }} FCFA
                                <span class="info-block__sep">·</span>
                                <i class="fa-solid fa-ruler-combined"></i>
                                {{ $bien->surface ?? 0 }} m²
                            </div>
                        </div>
                    @endif

                    {{-- Message --}}
                    @if($proposition->message)
                        <div class="info-block info-block--full">
                            <div class="info-block__label">Message de l'agence</div>
                            <blockquote class="info-block__quote">
                                {{ $proposition->message }}
                            </blockquote>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Conditions commerciales --}}
            @if($hasConditions)
                <div class="panel">
                    <h3 class="panel-title">
                        <i class="fa-solid fa-file-signature"></i> Conditions commerciales
                    </h3>

                    {{-- ✅ UNE SEULE CARD : Caution demandée --}}
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
                                <span class="condition-item__label">
                                    <i class="fa-regular fa-calendar"></i> Disponibilité
                                </span>
                                <span class="condition-item__value">{{ $proposition->disponibilite_label }}</span>
                            </div>
                        @endif

                        @if($proposition->caution_mois !== null)
                            <div class="condition-item">
                                <span class="condition-item__label">
                                    <i class="fa-solid fa-shield-halved"></i> Caution
                                </span>
                                <span class="condition-item__value">{{ $proposition->caution_label ?? 'Aucune' }}</span>
                            </div>
                        @endif

                        @if($proposition->avance_loyer_mois !== null)
                            <div class="condition-item">
                                <span class="condition-item__label">
                                    <i class="fa-solid fa-money-bill-wave"></i> Avance loyer
                                </span>
                                <span class="condition-item__value">{{ $proposition->avance_label ?? 'Aucune' }}</span>
                            </div>
                        @endif

                        @if($proposition->honoraires !== null)
                            <div class="condition-item">
                                <span class="condition-item__label">
                                    <i class="fa-solid fa-hand-holding-dollar"></i> Honoraires
                                </span>
                                <span class="condition-item__value">{{ $proposition->honoraires_formate }}</span>
                            </div>
                        @endif

                        @if($proposition->charges !== null)
                            <div class="condition-item">
                                <span class="condition-item__label">
                                    <i class="fa-solid fa-receipt"></i> Charges
                                </span>
                                <span class="condition-item__value">{{ $proposition->charges_formate }}</span>
                            </div>
                        @endif
                    </div>

                    @if($proposition->cout_entree_total > 0)
                        <div class="conditions-total">
                            <span class="conditions-total__label">Coût total à l'entrée</span>
                            <span class="conditions-total__value">{{ $proposition->cout_entree_total_formate }}</span>
                        </div>
                    @endif

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

            {{-- Caractéristiques du bien --}}
            @if($bien)
                <div class="panel">
                    <h3 class="panel-title">
                        <i class="fa-regular fa-building"></i> Caractéristiques du bien
                    </h3>

                    <div class="characteristics">
                        <div class="char-item">
                            <span class="char-item__label">Type</span>
                            <span class="char-item__value">
                                {{ is_object($bien->type_bien) ? $bien->type_bien->label() : ($bien->type_bien ?? '—') }}
                            </span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Contrat</span>
                            <span class="char-item__value">
                                {{ is_object($bien->type_contrat) ? $bien->type_contrat->label() : ($bien->type_contrat ?? '—') }}
                            </span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Surface</span>
                            <span class="char-item__value">{{ $bien->surface ?? 0 }} m²</span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Chambres</span>
                            <span class="char-item__value">{{ $bien->nombre_chambres ?? '—' }}</span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Salles de bain</span>
                            <span class="char-item__value">{{ $bien->nombre_salles_bain ?? '—' }}</span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Parking</span>
                            <span class="char-item__value">
                                <i class="fa-solid {{ $bien->parking_disponible ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                                {{ $bien->parking_disponible ? 'Oui' : 'Non' }}
                            </span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Meublé</span>
                            <span class="char-item__value">
                                <i class="fa-solid {{ $bien->est_meuble ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                                {{ $bien->est_meuble ? 'Oui' : 'Non' }}
                            </span>
                        </div>
                    </div>

                    @if($bien->description)
                        <blockquote class="description-block">
                            {{ $bien->description }}
                        </blockquote>
                    @endif

                    @if(count($equipements) > 0)
                        <div class="equipements">
                            <div class="equipements__title">
                                <i class="fa-solid fa-cogs"></i> Équipements
                            </div>
                            <div class="equipements__grid">
                                @foreach($equipements as $equipement)
                                    <span class="equipement-pill">
                                        <i class="fa-solid fa-check-circle"></i> {{ $equipement }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- RDV lié --}}
            @if($hasRdv)
                <div class="panel panel--rdv">
                    <h3 class="panel-title">
                        <i class="fa-regular fa-calendar-check"></i> Rendez-vous lié
                    </h3>

                    <div class="rdv-card">
                        <div class="rdv-card__header">
                            <span class="rdv-badge rdv-badge--{{ $rdvStatutValue }}">
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
                                    <span class="rdv-info__label">Date</span>
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
                            <div class="rdv-info">
                                <i class="fa-solid fa-circle-check"></i>
                                <div>
                                    <span class="rdv-info__label">Code validé</span>
                                    <span class="rdv-info__value">{{ $rendezVous->code_valide ? 'Oui' : 'Non' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Note lecture seule --}}
            <div class="readonly-note">
                <i class="fa-solid fa-info-circle"></i>
                <span>
                    <strong>Lecture seule</strong> — Les propositions ne peuvent pas être modifiées par l'administrateur.
                </span>
            </div>
        </div>

        {{-- ═══ COLONNE DROITE : MÉDIAS ═══ --}}
        <div>

            {{-- Photos --}}
            @if($propImages->count() > 0)
                <div class="panel">
                    <h3 class="panel-title">
                        <i class="fa-solid fa-image"></i> Photos
                        <span class="panel-title__count">{{ $propImages->count() }}</span>
                    </h3>
                    <div class="media-grid">
                        @foreach($propImages as $index => $media)
                            <div class="media-grid-item" onclick="openLightbox({{ $index }})">
                                <img src="{{ asset('storage/' . $media->fichier) }}"
                                     alt="Photo {{ $index + 1 }}"
                                     loading="lazy">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Vidéos --}}
            @if($propVideos->count() > 0)
                <div class="panel">
                    <h3 class="panel-title">
                        <i class="fa-solid fa-video"></i> Vidéos
                        <span class="panel-title__count">{{ $propVideos->count() }}</span>
                    </h3>
                    <div class="media-video-grid">
                        @foreach($propVideos as $media)
                            @php
                                $videoIndex = $propImages->count() + $loop->index;
                            @endphp
                            <div class="media-grid-item media-grid-item--video"
                                 onclick="openLightbox({{ $videoIndex }})">
                                <video src="{{ asset('storage/' . $media->fichier) }}"
                                       preload="metadata"></video>
                                <div class="media-grid-item__play">
                                    <i class="fa-solid fa-circle-play"></i>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($propImages->count() === 0 && $propVideos->count() === 0)
                <div class="panel">
                    <div class="empty-media">
                        <i class="fa-regular fa-image"></i>
                        <p>Aucun média disponible pour cette proposition.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══ LIGHTBOX ═══ --}}
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox(event)">
    <button class="lightbox-close" onclick="closeLightbox(event)" aria-label="Fermer">&times;</button>
    <button class="lightbox-nav prev" onclick="changeLightboxImage(-1, event)" aria-label="Précédent">&#10094;</button>
    <button class="lightbox-nav next" onclick="changeLightboxImage(1, event)" aria-label="Suivant">&#10095;</button>

    <div class="lightbox-content" id="lightboxContent">
        <img id="lightboxImage" src="" alt="Agrandissement">
        <video id="lightboxVideo" controls></video>
    </div>

    <div class="lightbox-counter" id="lightboxCounter">1 / 1</div>
    <div class="lightbox-indicator" id="lightboxIndicator"></div>
</div>
@endsection


@push('scripts')
<script>
    const mediaItems = @json($allMedia);
    let currentIndex = 0;

    function openLightbox(index) {
        currentIndex = index;
        const overlay = document.getElementById('lightboxOverlay');

        updateLightboxContent();
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function updateLightboxContent() {
        const img     = document.getElementById('lightboxImage');
        const video   = document.getElementById('lightboxVideo');
        const counter = document.getElementById('lightboxCounter');
        const indicator = document.getElementById('lightboxIndicator');
        const item    = mediaItems[currentIndex];

        if (!item) return;

        // Reset
        video.pause();
        video.src = '';

        if (item.type === 'image') {
            img.style.display = 'block';
            video.style.display = 'none';
            img.src = item.url;
            counter.textContent = (currentIndex + 1) + ' / ' + mediaItems.length;
        } else {
            img.style.display = 'none';
            video.style.display = 'block';
            video.src = item.url;
            video.load();
            video.play();
            counter.textContent = '▶ Vidéo ' + (currentIndex + 1) + ' / ' + mediaItems.length;
        }

        // Dots
        let dots = '';
        for (let i = 0; i < mediaItems.length; i++) {
            const active = i === currentIndex ? 'active' : '';
            const icon = mediaItems[i].type === 'video' ? '▶' : '●';
            dots += '<span class="dot ' + active + '" onclick="goToMedia(' + i + ')">' + icon + '</span>';
        }
        indicator.innerHTML = dots;
    }

    function changeLightboxImage(direction, event) {
        if (event) event.stopPropagation();
        if (mediaItems.length === 0) return;
        currentIndex = (currentIndex + direction + mediaItems.length) % mediaItems.length;
        updateLightboxContent();
    }

    function goToMedia(index) {
        currentIndex = index;
        updateLightboxContent();
    }

    function closeLightbox(event) {
        if (event && event.target !== event.currentTarget
            && !event.target.classList.contains('lightbox-close')) {
            return;
        }

        const overlay = document.getElementById('lightboxOverlay');
        const video = document.getElementById('lightboxVideo');
        video.pause();
        video.src = '';
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function (e) {
        const overlay = document.getElementById('lightboxOverlay');
        if (!overlay.classList.contains('open')) return;

        if (e.key === 'Escape')     closeLightbox(e);
        if (e.key === 'ArrowLeft')  changeLightboxImage(-1, e);
        if (e.key === 'ArrowRight') changeLightboxImage(1, e);
    });

    document.getElementById('lightboxContent').addEventListener('click', function (e) {
        e.stopPropagation();
    });
</script>
@endpush


@push('styles')
<style>
/* ═══ PAGE ACTIONS ═══ */
.page-actions {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

/* ═══ GRID 2 COLONNES ═══ */
.grid-2 {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 20px;
}

/* ═══ PANELS ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px 22px;
    margin-bottom: 16px;
}
.panel--rdv { border-left: 4px solid var(--c-info, #0D47A1); }

.panel-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}
.panel-head h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
}
.panel-head__meta {
    font-size: 12.5px;
    font-weight: 400;
    color: var(--muted);
    margin-left: 8px;
}

.panel-title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 16px;
    color: var(--ink);
    display: flex;
    align-items: center;
    gap: 8px;
}
.panel-title i { color: var(--rust); font-size: 14px; }
.panel-title__count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: var(--c-warning-bg, #FFF8E1);
    color: var(--rust);
    border-radius: 999px;
}

/* ═══ STATUS PILLS ═══ */
.status-pill {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
}
.status-en_attente { background: #FFF8E1; color: #E65100; }
.status-acceptee   { background: #E8F5E9; color: #1E7A47; }
.status-refusee    { background: #FFEBEE; color: #C62828; }
.status-terminee   { background: #E3F2FD; color: #0D47A1; }

.meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 600;
    background: var(--surface, #F7F9FC);
    color: var(--text-soft);
    border: 1px solid var(--border);
    margin-left: 6px;
}
.meta-pill--info { background: #E3F2FD; color: #0D47A1; border-color: #BBDEFB; }

/* ═══ INFO GRID ═══ */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.info-block {
    padding: 12px 14px;
    background: #F7F9FC;
    border-radius: 10px;
    border: 1px solid #EEF1F5;
}
.info-block--full { grid-column: span 2; }
.info-block__label {
    font-size: 11px;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 6px;
}
.info-block__value {
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.4;
}
.info-block__value--price {
    font-family: var(--display);
    font-size: 18px;
    color: var(--rust);
}
.info-block__sub {
    font-size: 12.5px;
    color: var(--text-soft);
    margin-top: 4px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
}
.info-block__sub i { color: var(--rust); opacity: 0.7; font-size: 11px; }
.info-block__sep { color: var(--muted); }
.info-block__quote {
    margin: 8px 0 0;
    padding: 10px 14px;
    background: #FFF9F5;
    border-left: 3px solid var(--rust);
    border-radius: 8px;
    font-size: 13px;
    font-style: italic;
    color: var(--text-soft);
    line-height: 1.6;
}

/* Agence info */
.info-agency {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 4px;
}
.info-agency__logo {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid var(--border);
    flex-shrink: 0;
}
.info-agency__logo--fallback {
    background: var(--gold-soft, #FFF8E1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    color: var(--gold, #F5A623);
}
.info-agency__name {
    font-weight: 700;
    font-size: 14px;
    color: var(--ink);
    margin-bottom: 3px;
}
.info-agency__contact {
    font-size: 12px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 2px;
}
.info-agency__contact i { color: var(--rust); font-size: 11px; }

/* ═══ CONDITIONS ═══ */
.conditions-recap {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
    margin-bottom: 14px;
}
.recap-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 12px;
    border: 1px solid transparent;
}
.recap-card--info { background: #E3F2FD; border-color: #BBDEFB; }
.recap-card__icon {
    width: 40px; height: 40px;
    border-radius: 10px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
    color: #0D47A1;
}
.recap-card__body { min-width: 0; }
.recap-card__label {
    display: block;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: #0D47A1;
    opacity: .75;
    margin-bottom: 2px;
}
.recap-card__value {
    display: block;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.2;
}

.conditions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 10px;
    margin-bottom: 14px;
}
.condition-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 10px 12px;
    background: #F7F9FC;
    border-radius: 9px;
    border: 1px solid #EEF1F5;
}
.condition-item__label {
    font-size: 11px;
    color: var(--muted);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}
.condition-item__label i { color: var(--rust); font-size: 11px; }
.condition-item__value {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
}

.conditions-total {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: linear-gradient(135deg, #FFF9F5, #FFF);
    border: 1px solid #FFE0D0;
    border-radius: 10px;
    margin-bottom: 14px;
}
.conditions-total__label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
}
.conditions-total__value {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 800;
    color: var(--rust);
}

.conditions-notes {
    padding-top: 14px;
    border-top: 1px dashed var(--border);
}
.conditions-notes__label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-soft);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 8px;
}
.conditions-notes__label i { color: var(--rust); }
.conditions-notes__text {
    margin: 0;
    padding: 10px 14px;
    background: #F7F9FC;
    border-left: 3px solid var(--rust);
    border-radius: 8px;
    font-size: 13px;
    font-style: italic;
    color: var(--text-soft);
    line-height: 1.65;
}

/* ═══ CARACTÉRISTIQUES ═══ */
.characteristics {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    margin-bottom: 14px;
}
.char-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    background: #F7F9FC;
    border-radius: 8px;
    border: 1px solid #EEF1F5;
}
.char-item__label {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.char-item__value {
    font-size: 13px;
    font-weight: 600;
    color: var(--ink);
    text-align: right;
}
.char-item__value .is-yes { color: #1E7A47; margin-right: 4px; }
.char-item__value .is-no  { color: #C62828; margin-right: 4px; }

.description-block {
    margin: 0 0 14px;
    padding: 12px 16px;
    background: linear-gradient(135deg, rgba(180, 83, 42, .05), rgba(180, 83, 42, .02));
    border-left: 3px solid var(--rust);
    border-radius: 8px;
    font-size: 13px;
    font-style: italic;
    color: var(--text-soft);
    line-height: 1.7;
}

/* ═══ ÉQUIPEMENTS ═══ */
.equipements {
    padding-top: 14px;
    border-top: 1px dashed var(--border);
}
.equipements__title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-soft);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 10px;
}
.equipements__title i { color: var(--rust); }
.equipements__grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.equipement-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 12px;
    background: #E3F2FD;
    color: #0D47A1;
    border: 1px solid #BBDEFB;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}
.equipement-pill i { font-size: 10px; }

/* ═══ RDV CARD ═══ */
.rdv-card {
    background: #F7F9FC;
    border: 1px solid #EEF1F5;
    border-radius: 10px;
    padding: 14px 16px;
}
.rdv-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.rdv-badge {
    display: inline-flex;
    align-items: center;
    padding: 3px 11px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.rdv-badge--planifie      { background: #FFF8E1; color: #E65100; }
.rdv-badge--confirme      { background: #E3F2FD; color: #0D47A1; }
.rdv-badge--termine       { background: #E8F5E9; color: #1E7A47; }
.rdv-badge--annule        { background: #FFEBEE; color: #C62828; }
.rdv-badge--client_absent { background: #EFEFF4; color: #6C6C70; }

.rdv-card__code {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--text-soft);
    padding: 4px 11px;
    background: #fff;
    border-radius: 8px;
    border: 1px solid var(--border);
}
.rdv-card__code i { color: var(--rust); font-size: 11px; }
.rdv-card__code strong {
    font-family: monospace;
    font-size: 14px;
    color: var(--rust);
    letter-spacing: 1px;
}

.rdv-card__body {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
}
.rdv-info {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #EEF1F5;
}
.rdv-info > i {
    color: var(--rust);
    font-size: 14px;
    flex-shrink: 0;
}
.rdv-info__label {
    display: block;
    font-size: 10.5px;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.rdv-info__value {
    display: block;
    font-size: 13px;
    font-weight: 700;
    color: var(--ink);
}

/* ═══ READONLY NOTE ═══ */
.readonly-note {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #FFF8E1;
    border: 1px solid #FFE0B2;
    border-radius: 10px;
    font-size: 13px;
    color: var(--text-soft);
}
.readonly-note i { color: #E65100; font-size: 16px; flex-shrink: 0; }
.readonly-note strong { color: #E65100; }

/* ═══ MEDIA GRID ═══ */
.media-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.media-video-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
}
.media-grid-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    border: 1px solid var(--border);
    background: #F7F9FC;
    transition: transform 0.2s, box-shadow 0.2s;
}
.media-grid-item:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}
.media-grid-item img,
.media-grid-item video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.media-grid-item--video {
    aspect-ratio: 16 / 9;
    background: #000;
}
.media-grid-item__play {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(0, 0, 0, 0.35);
    pointer-events: none;
}
.media-grid-item__play i {
    font-size: 48px;
    color: rgba(255, 255, 255, 0.9);
}

.empty-media {
    text-align: center;
    padding: 40px 20px;
    color: var(--muted);
}
.empty-media i {
    font-size: 40px;
    opacity: 0.4;
    display: block;
    margin-bottom: 12px;
}
.empty-media p { margin: 0; font-size: 13px; }

/* ═══ LIGHTBOX ═══ */
.lightbox-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.94);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.lightbox-overlay.open { display: flex; }

.lightbox-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.lightbox-content img {
    max-width: 90vw;
    max-height: 85vh;
    object-fit: contain;
    border-radius: 8px;
}
.lightbox-content video {
    max-width: 90vw;
    max-height: 85vh;
    border-radius: 8px;
    background: #000;
}

.lightbox-close {
    position: absolute;
    top: 16px;
    right: 24px;
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    border: none;
    font-size: 26px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    font-family: sans-serif;
    z-index: 2;
}
.lightbox-close:hover { background: rgba(255, 255, 255, 0.25); }

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    border: none;
    font-size: 22px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    z-index: 2;
}
.lightbox-nav:hover { background: rgba(255, 255, 255, 0.28); }
.lightbox-nav.prev { left: 20px; }
.lightbox-nav.next { right: 20px; }

.lightbox-counter {
    position: absolute;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    padding: 5px 16px;
    background: rgba(255, 255, 255, 0.12);
    border-radius: 999px;
}

.lightbox-indicator {
    position: absolute;
    bottom: 60px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 8px;
}
.lightbox-indicator .dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 8px;
    color: transparent;
    cursor: pointer;
    transition: all 0.3s;
}
.lightbox-indicator .dot.active {
    background: #fff;
    transform: scale(1.4);
}

/* ═══════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════ */
@media (max-width: 992px) {
    .grid-2 { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
    .info-grid { grid-template-columns: 1fr; }
    .info-block--full { grid-column: span 1; }
    .conditions-recap { grid-template-columns: 1fr; }
    .conditions-grid { grid-template-columns: 1fr 1fr; }
    .characteristics { grid-template-columns: 1fr; }
    .rdv-card__body { grid-template-columns: 1fr; }
    .media-grid { grid-template-columns: repeat(2, 1fr); }
    .page-actions { flex-direction: column; }
    .page-actions .btn { width: 100%; justify-content: center; }
}
</style>
@endpush