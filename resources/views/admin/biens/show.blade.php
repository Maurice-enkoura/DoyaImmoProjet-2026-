@extends('layouts.admin')

@section('title', 'Détail du bien - DoyaImmo')
@section('page_title', 'Détail du bien')
@section('page_sub', $bien->titre)

@section('content')
<style>
    /* Styles pour la lightbox */
    .lightbox-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        padding: 20px;
    }

    .lightbox-overlay.open {
        display: flex;
    }

    .lightbox-content {
        max-width: 90vw;
        max-height: 90vh;
        position: relative;
    }

    .lightbox-content img {
        max-width: 100%;
        max-height: 90vh;
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
        top: -40px;
        right: 0;
        color: #fff;
        font-size: 30px;
        cursor: pointer;
        background: none;
        border: none;
        padding: 10px;
    }

    .lightbox-counter {
        position: absolute;
        bottom: -40px;
        left: 50%;
        transform: translateX(-50%);
        color: #fff;
        font-size: 14px;
        background: rgba(0, 0, 0, 0.6);
        padding: 4px 16px;
        border-radius: 20px;
    }

    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        color: #fff;
        font-size: 40px;
        cursor: pointer;
        background: rgba(0, 0, 0, 0.5);
        border: none;
        padding: 10px 20px;
        border-radius: 50%;
        transition: all 0.3s;
    }

    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.2);
    }

    .lightbox-nav.prev {
        left: 20px;
    }

    .lightbox-nav.next {
        right: 20px;
    }

    .media-grid-item {
        cursor: pointer;
        transition: transform 0.2s;
        position: relative;
    }

    .media-grid-item:hover {
        transform: scale(1.02);
    }

    .media-grid-item .play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
        font-size: 48px;
        opacity: 0.8;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        pointer-events: none;
    }

    .media-grid-item .video-duration {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
    }

    .lightbox-indicator {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 12px;
    }

    .lightbox-indicator .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transition: all 0.3s;
        cursor: pointer;
    }

    .lightbox-indicator .dot.active {
        background: #fff;
        transform: scale(1.3);
    }

    /* Style pour le badge vedette */
    .badge-vedette {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        background: #F5A623;
        color: #fff;
    }

    .badge-vedette i {
        font-size: 14px;
    }

    .vedette-info {
        margin-top: 8px;
        padding: 10px 14px;
        background: #FFF8E1;
        border-radius: 8px;
        border: 1px solid #FFE0B2;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .vedette-info .label {
        font-size: 12px;
        color: var(--muted);
    }

    .vedette-info .value {
        font-weight: 600;
        color: #E65100;
    }

    .vedette-info .progress {
        flex: 1;
        min-width: 100px;
    }

    .vedette-info .progress-bar {
        width: 100%;
        height: 6px;
        background: #E8ECF0;
        border-radius: 3px;
        overflow: hidden;
    }

    .vedette-info .progress-bar .fill {
        height: 100%;
        border-radius: 3px;
        background: #F5A623;
        transition: width 0.3s ease;
    }
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.biens.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux biens
    </a>
    @if($bien->est_vedette && $bien->vedette_fin > now())
    <span class="badge-vedette" style="margin-left:12px;">
        <i class="fa-solid fa-star"></i> En vedette
    </span>
    @endif
</div>

<div class="grid-2">
    <!-- Informations générales -->
    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
                <h3 style="font-size:18px;font-weight:700;">{{ $bien->titre }}</h3>
                @if($bien->est_vedette && $bien->vedette_fin > now())
                    @php
                        $joursRestants = $bien->vedette_fin->diffInDays(now(), false);
                        $joursRestants = max(0, ceil($joursRestants));
                    @endphp
                    <div style="font-size:13px;color:#F5A623;margin-top:4px;">
                        <i class="fa-solid fa-star"></i> 
                        @if($joursRestants === 0)
                            Dernier jour
                        @else
                            {{ $joursRestants }} jour(s) restant(s)
                        @endif
                        (fin: {{ $bien->vedette_fin->format('d/m/Y') }})
                    </div>
                @endif
            </div>
            <span class="status-pill {{ $bien->statut ? 'status-active' : 'status-inactif' }}">
                {{ $bien->statut ? ' ✅ Disponible' : ' ❌ Indisponible' }}
            </span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Type de bien</div>
                <div style="font-weight:600;">
                    {{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : ($bien->type_bien ?? 'N/A') }}
                </div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Type de contrat</div>
                <div style="font-weight:600;">
                    {{ is_object($bien->type_contrat) && method_exists($bien->type_contrat, 'label') ? $bien->type_contrat->label() : ($bien->type_contrat ?? 'N/A') }}
                </div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Prix</div>
                <div style="font-weight:600;color:var(--rust);font-size:18px;">
                    {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                </div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Surface</div>
                <div style="font-weight:600;">{{ $bien->surface }} m²</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Nombre de chambres</div>
                <div style="font-weight:600;">{{ $bien->nombre_chambres ?? 0 }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Salles de bain</div>
                <div style="font-weight:600;">{{ $bien->nombre_salles_bain ?? 0 }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Parking</div>
                <div style="font-weight:600;">
                    {{ $bien->parking_disponible ? ' ✅ Oui' : ' ❌ Non' }}
                </div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Meublé</div>
                <div style="font-weight:600;">
                    {{ $bien->est_meuble ? ' ✅ Oui' : ' ❌ Non' }}
                </div>
            </div>

            <!-- QUARTIER -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">Quartier</div>
                <div style="font-weight:600;">
                    @php
                    $quartierNom = 'Non renseigné';
                    $quartierVille = '';

                    if (is_object($bien->quartier) && method_exists($bien->quartier, '__toString')) {
                        $quartierNom = $bien->quartier->nom ?? 'Non renseigné';
                        $quartierVille = $bien->quartier->ville ?? '';
                    } elseif (is_string($bien->quartier) && !empty($bien->quartier)) {
                        $quartierNom = $bien->quartier;
                    } elseif (is_numeric($bien->quartier_id) && $bien->quartier_id > 0) {
                        $quartier = App\Models\Quartier::find($bien->quartier_id);
                        if ($quartier) {
                            $quartierNom = $quartier->nom;
                            $quartierVille = $quartier->ville;
                        }
                    }
                    @endphp
                    {{ $quartierNom }}
                    @if($quartierVille)
                    <span style="font-weight:400;color:var(--muted);font-size:13px;">
                        ({{ $quartierVille }})
                    </span>
                    @endif
                </div>
            </div>

            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">Adresse</div>
                <div style="font-weight:600;">{{ $bien->adresse ?? 'Non renseignée' }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">Vues</div>
                <div style="font-weight:600;">{{ $bien->vues ?? 0 }}</div>
            </div>
        </div>

        @if($bien->description)
        <div style="margin-top:16px;padding:12px 16px;background:#F7F9FC;border-radius:8px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Description</div>
            <div style="font-size:13px;color:var(--text-soft);line-height:1.6;">{{ $bien->description }}</div>
        </div>
        @endif

        <!-- Vedette Info -->
        @if($bien->est_vedette && $bien->vedette_fin > now())
        <div class="vedette-info">
            <i class="fa-solid fa-star" style="color:#F5A623;font-size:20px;"></i>
            <div>
                <div class="label">Statut</div>
                <div class="value">En vedette</div>
            </div>
            <div>
                <div class="label">Début</div>
                <div class="value">{{ $bien->vedette_debut->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="label">Fin</div>
                <div class="value">{{ $bien->vedette_fin->format('d/m/Y') }}</div>
            </div>
            <div>
                <div class="label">Jours restants</div>
                <div class="value">
                    @php
                        $jours = $bien->vedette_fin->diffInDays(now(), false);
                        $jours = max(0, ceil($jours));
                    @endphp
                    {{ $jours }} jour(s)
                </div>
            </div>
            <div class="progress">
                <div class="label">Progression</div>
                @php
                    $total = $bien->vedette_debut->diffInDays($bien->vedette_fin);
                    $ecoule = $bien->vedette_debut->diffInDays(now());
                    $pourcentage = $total > 0 ? round(($ecoule / $total) * 100) : 0;
                    $pourcentage = min(100, max(0, $pourcentage));
                @endphp
                <div class="progress-bar">
                    <div class="fill" style="width:{{ $pourcentage }}%;"></div>
                </div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;text-align:right;">
                    {{ $pourcentage }}%
                </div>
            </div>
        </div>
        @endif

        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Agence propriétaire</div>
            <div style="display:flex;align-items:center;gap:12px;">
                @if($bien->agence && $bien->agence->logo)
                <img src="{{ asset('storage/' . $bien->agence->logo) }}"
                    alt="{{ $bien->agence->nom_agence }}"
                    style="width:50px;height:50px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
                @else
                <div style="width:50px;height:50px;background:var(--gold-soft);border-radius:10px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:var(--gold);">
                    {{ $bien->agence ? Str::substr($bien->agence->nom_agence, 0, 2) : 'NA' }}
                </div>
                @endif
                <div>
                    <div style="font-weight:600;">{{ $bien->agence->nom_agence ?? 'N/A' }}</div>
                    <div style="font-size:12px;color:var(--muted);">
                        {{ $bien->agence->user->email ?? '' }}
                        @if($bien->agence && $bien->agence->statut_validation)
                        <span class="status-pill status-active" style="font-size:10px;padding:1px 10px;"> ✅ Validée</span>
                        @else
                        <span class="status-pill status-en_attente" style="font-size:10px;padding:1px 10px;"> ⏳ En attente</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($bien->agence && $bien->agence->adresse)
            <div style="font-size:12px;color:var(--muted);margin-top:4px;">
                <i class="fa-solid fa-location-dot"></i> {{ $bien->agence->adresse }}
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;">
            @if($bien->statut)
            <!-- ✅ CORRIGÉ : Utilisation du slug -->
            <form action="{{ route('admin.biens.desactiver', $bien->slug) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger" onclick="return confirm('Désactiver ce bien ?')">
                    <i class="fa-solid fa-eye-slash"></i> Désactiver
                </button>
            </form>
            @else
            <!-- ✅ CORRIGÉ : Utilisation du slug -->
            <form action="{{ route('admin.biens.activer', $bien->slug) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" onclick="return confirm('Activer ce bien ?')">
                    <i class="fa-solid fa-eye"></i> Activer
                </button>
            </form>
            @endif
            @if($bien->est_vedette && $bien->vedette_fin > now())
            <!-- ✅ CORRIGÉ : Utilisation du slug -->
            <form action="{{ route('admin.biens.vedette.retirer', $bien->slug) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-warning" style="background:#F5A623;color:#fff;border-color:#F5A623;" onclick="return confirm('Retirer ce bien de la vedette ?')">
                    <i class="fa-solid fa-star-half-stroke"></i> Retirer de la vedette
                </button>
            </form>
            @else
            <!-- ✅ CORRIGÉ : Utilisation du slug -->
            <button type="button" class="btn btn-ghost" onclick="openVedetteModal('{{ $bien->slug }}')">
                <i class="fa-solid fa-star" style="color:#F5A623;"></i> Mettre en vedette
            </button>
            @endif
            <!-- ✅ CORRIGÉ : Utilisation du slug -->
            <form action="{{ route('admin.biens.destroy', $bien->slug) }}" method="POST" onsubmit="return confirm('Supprimer ce bien définitivement ? Cette action est irréversible.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash-can"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    <!-- Photos et vidéos -->
    <div>
        <div class="panel" style="margin-bottom:20px;">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-image"></i> Photos du bien
                <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:8px;">
                    (Cliquez pour agrandir)
                </span>
            </h3>
            @php
            $photos = $bien->medias->where('type_media', 'image');
            $videos = $bien->medias->where('type_media', 'video');
            @endphp

            @if($photos->count() > 0)
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                @foreach($photos as $index => $media)
                <div class="media-grid-item"
                    onclick="openLightbox({{ $index }})"
                    style="border-radius:8px;overflow:hidden;aspect-ratio:1;border:1px solid var(--border);position:relative;">
                    <img src="{{ asset('storage/' . $media->fichier) }}"
                        alt="Photo {{ $index + 1 }}"
                        style="width:100%;height:100%;object-fit:cover;"
                        loading="lazy">
                </div>
                @endforeach
            </div>
            @else
            <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">
                <i class="fa-solid fa-image" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                Aucune photo disponible.
            </p>
            @endif
        </div>

        <!-- Vidéos -->
        @if($videos->count() > 0)
        <div class="panel">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-video"></i> Vidéos
                <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:8px;">
                    (Cliquez pour lire)
                </span>
            </h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                @foreach($videos as $media)
                <div class="media-grid-item"
                    onclick="openVideoLightbox('{{ asset('storage/' . $media->fichier) }}')"
                    style="border-radius:8px;overflow:hidden;border:1px solid var(--border);background:#000;position:relative;aspect-ratio:16/9;">
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);">
                        <i class="fa-solid fa-circle-play" style="font-size:48px;color:rgba(255,255,255,0.8);"></i>
                    </div>
                    <div style="position:absolute;bottom:8px;right:8px;background:rgba(0,0,0,0.7);color:#fff;padding:2px 10px;border-radius:4px;font-size:12px;">
                        <i class="fa-solid fa-video"></i> Vidéo
                    </div>
                    <video src="{{ asset('storage/' . $media->fichier) }}"
                        style="width:100%;height:100%;object-fit:cover;"
                        preload="metadata">
                    </video>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Propositions -->
        @if($bien->propositions && $bien->propositions->count() > 0)
        <div class="panel" style="margin-top:20px;">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-handshake"></i> Propositions ({{ $bien->propositions->count() }})
            </h3>
            <div style="max-height:300px;overflow-y:auto;">
                @foreach($bien->propositions->take(5) as $proposition)
                <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-weight:600;font-size:13px;">
                            {{ $proposition->agence->nom_agence ?? 'N/A' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <span class="status-pill status-{{ $proposition->statut }}">
                        {{ is_object($proposition->statut) && method_exists($proposition->statut, 'label') ? $proposition->statut->label() : ($proposition->statut ?? 'N/A') }}
                    </span>
                </div>
                @endforeach
                @if($bien->propositions->count() > 5)
                <div style="text-align:center;padding-top:8px;font-size:12px;color:var(--muted);">
                    + {{ $bien->propositions->count() - 5 }} autres propositions
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Lightbox pour les images -->
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox(event)">
    <button class="lightbox-close" onclick="closeLightbox(event)">&times;</button>
    <button class="lightbox-nav prev" onclick="changeLightboxImage(-1, event)">&#10094;</button>
    <button class="lightbox-nav next" onclick="changeLightboxImage(1, event)">&#10095;</button>
    <div class="lightbox-content" id="lightboxContent">
        <img id="lightboxImage" src="" alt="Agrandissement">
        <video id="lightboxVideo" controls style="display:none;max-width:90vw;max-height:85vh;border-radius:8px;background:#000;"></video>
    </div>
    <div class="lightbox-counter" id="lightboxCounter">1 / 1</div>
    <div class="lightbox-indicator" id="lightboxIndicator"></div>
</div>

@php
$mediaItems = [];
foreach($photos as $media) {
    $mediaItems[] = ['type' => 'image', 'url' => asset('storage/' . $media->fichier)];
}
foreach($videos as $media) {
    $mediaItems[] = ['type' => 'video', 'url' => asset('storage/' . $media->fichier)];
}
@endphp

<script>
    const mediaItems = @json($mediaItems);
    let currentIndex = 0;

    function openLightbox(index) {
        currentIndex = index;
        const overlay = document.getElementById('lightboxOverlay');
        const img = document.getElementById('lightboxImage');
        const video = document.getElementById('lightboxVideo');

        img.style.display = 'block';
        video.style.display = 'none';
        video.pause();

        updateLightboxContent();
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function openVideoLightbox(url) {
        const overlay = document.getElementById('lightboxOverlay');
        const img = document.getElementById('lightboxImage');
        const video = document.getElementById('lightboxVideo');

        img.style.display = 'none';
        video.style.display = 'block';
        video.src = url;
        video.load();
        video.play();

        document.getElementById('lightboxCounter').textContent = '▶ Vidéo';
        document.getElementById('lightboxIndicator').innerHTML = '';
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function updateLightboxContent() {
        const img = document.getElementById('lightboxImage');
        const video = document.getElementById('lightboxVideo');
        const counter = document.getElementById('lightboxCounter');
        const indicator = document.getElementById('lightboxIndicator');
        const item = mediaItems[currentIndex];

        if (!item) return;

        if (item.type === 'image') {
            img.style.display = 'block';
            video.style.display = 'none';
            video.pause();
            img.src = item.url;
            counter.textContent = `${currentIndex + 1} / ${mediaItems.length}`;
        } else {
            img.style.display = 'none';
            video.style.display = 'block';
            video.src = item.url;
            video.load();
            video.play();
            counter.textContent = `▶ Vidéo ${currentIndex + 1} / ${mediaItems.length}`;
        }

        let dots = '';
        for (let i = 0; i < mediaItems.length; i++) {
            const active = i === currentIndex ? 'active' : '';
            const typeIcon = mediaItems[i].type === 'video' ? '▶' : '●';
            dots += `<span class="dot ${active}" onclick="goToMedia(${i})">${typeIcon}</span>`;
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
        if (event && event.target !== event.currentTarget && event.target.className !== 'lightbox-close') return;

        const overlay = document.getElementById('lightboxOverlay');
        const video = document.getElementById('lightboxVideo');
        video.pause();
        video.src = '';
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function(e) {
        if (!document.getElementById('lightboxOverlay').classList.contains('open')) return;

        if (e.key === 'Escape') {
            closeLightbox(e);
        } else if (e.key === 'ArrowLeft') {
            changeLightboxImage(-1, e);
        } else if (e.key === 'ArrowRight') {
            changeLightboxImage(1, e);
        }
    });

    document.getElementById('lightboxContent').addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // ✅ CORRIGÉ : Utilisation du slug pour le modal
    function openVedetteModal(bienSlug) {
        // Créer un formulaire dynamique
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/biens/${bienSlug}/vedette`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);
        
        const duree = document.createElement('select');
        duree.name = 'duree';
        duree.innerHTML = `
            <option value="1">1 jour</option>
            <option value="3">3 jours</option>
            <option value="7" selected>7 jours</option>
            <option value="14">14 jours</option>
            <option value="30">30 jours</option>
        `;
        
        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;';
        wrapper.innerHTML = `
            <div style="background:#fff;border-radius:var(--radius);padding:32px;max-width:400px;width:90%;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <h3 style="font-family:var(--display);font-size:18px;">
                        <i class="fa-solid fa-star" style="color:#F5A623;"></i> Mettre en vedette
                    </h3>
                    <button onclick="this.closest('div[style]').remove()" style="background:none;border:none;font-size:24px;cursor:pointer;">&times;</button>
                </div>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-weight:600;margin-bottom:4px;">Durée (en jours)</label>
                    ${duree.outerHTML}
                </div>
                <div style="padding:12px 16px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;margin-bottom:16px;">
                    <p style="font-size:13px;color:#BF360C;margin:0;">
                        <i class="fa-solid fa-info-circle"></i> 
                        Le bien apparaîtra dans la section "À la une" de l'accueil.
                    </p>
                </div>
                <button type="submit" class="btn btn-rust" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-star"></i> Mettre en vedette
                </button>
            </div>
        `;
        
        form.appendChild(wrapper.querySelector('div'));
        document.body.appendChild(form);
        
        // Soumettre le formulaire
        form.onsubmit = function(e) {
            const dureeSelect = form.querySelector('select[name="duree"]');
            const dureeInput = document.createElement('input');
            dureeInput.type = 'hidden';
            dureeInput.name = 'duree';
            dureeInput.value = dureeSelect.value;
            form.appendChild(dureeInput);
        };
    }
</script>

<!-- Modal pour mettre en vedette (fallback) -->
<div id="vedetteModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:var(--radius);padding:32px;max-width:400px;width:90%;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-family:var(--display);font-size:18px;">
                <i class="fa-solid fa-star" style="color:#F5A623;"></i> Mettre en vedette
            </h3>
            <button onclick="closeVedetteModal()" style="background:none;border:none;font-size:24px;cursor:pointer;">&times;</button>
        </div>
        
        <form id="vedetteForm" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label for="duree" style="display:block;font-weight:600;margin-bottom:4px;">Durée (en jours)</label>
                <select name="duree" id="duree" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:14px;">
                    <option value="1">1 jour</option>
                    <option value="3">3 jours</option>
                    <option value="7" selected>7 jours</option>
                    <option value="14">14 jours</option>
                    <option value="30">30 jours</option>
                </select>
            </div>
            <div style="padding:12px 16px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;margin-bottom:16px;">
                <p style="font-size:13px;color:#BF360C;margin:0;">
                    <i class="fa-solid fa-info-circle"></i> 
                    Le bien apparaîtra dans la section "À la une" de l'accueil.
                </p>
            </div>
            <button type="submit" class="btn btn-rust" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-star"></i> Mettre en vedette
            </button>
        </form>
    </div>
</div>

<script>
    function closeVedetteModal() {
        document.getElementById('vedetteModal').style.display = 'none';
    }

    document.getElementById('vedetteModal').addEventListener('click', function(e) {
        if (e.target === this) closeVedetteModal();
    });
</script>
@endsection