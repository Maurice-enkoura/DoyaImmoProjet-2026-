@extends('layouts.admin')

@section('title', 'Détail de la proposition — Administration DoyaImmo')
@section('page_title', 'Détail de la proposition')
@section('page_sub', 'Proposition de ' . ($proposition->agence->nom_agence ?? 'N/A'))

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
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.propositions.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux propositions
    </a>
    <a href="{{ route('admin.demandes.show', $proposition->demande_id) }}" class="btn btn-ghost btn-sm" style="margin-left:8px;">
        <i class="fa-solid fa-file"></i> Voir la demande
    </a>
</div>

<div class="grid-2">
    <!-- Informations de la proposition -->
    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <h3 style="font-size:18px;font-weight:700;">
                Proposition #{{ $proposition->id }}
                <span style="font-size:13px;font-weight:400;color:var(--muted);">
                    du {{ $proposition->created_at->format('d/m/Y') }}
                </span>
            </h3>
            <span class="status-pill status-{{ $proposition->statut }}">
                {{ $proposition->statut_label ?? $proposition->statut }}
            </span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">Agence</div>
                <div style="display:flex;align-items:center;gap:12px;margin-top:4px;">
                    @if($proposition->agence && $proposition->agence->logo)
                        <img src="{{ asset('storage/' . $proposition->agence->logo) }}" 
                             alt="{{ $proposition->agence->nom_agence }}" 
                             style="width:40px;height:40px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
                    @else
                        <div style="width:40px;height:40px;border-radius:8px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:var(--gold);">
                            {{ $proposition->agence ? Str::substr($proposition->agence->nom_agence, 0, 2) : 'NA' }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight:600;">{{ $proposition->agence->nom_agence ?? 'N/A' }}</div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-envelope"></i> {{ $proposition->agence->user->email ?? '' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-phone"></i> {{ $proposition->agence->user->telephone ?? 'Non renseigné' }}
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">Demande associée</div>
                <div style="font-weight:600;margin-top:4px;">
                    {{ $proposition->demande->type_operation_label ?? ($proposition->demande->type_operation ?? 'N/A') }} - 
                    {{ $proposition->demande->type_bien_label ?? ($proposition->demande->type_bien ?? 'N/A') }}
                </div>
                <div style="font-size:12px;color:var(--muted);">
                    <i class="fa-solid fa-user"></i> 
                    {{ $proposition->demande->particulier->user->prenom ?? '' }} {{ $proposition->demande->particulier->user->nom ?? '' }}
                    <span style="margin-left:12px;">
                        <i class="fa-solid fa-envelope"></i> {{ $proposition->demande->particulier->user->email ?? '' }}
                    </span>
                </div>
            </div>

            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Prix proposé</div>
                <div style="font-weight:600;color:var(--rust);font-size:18px;">
                    {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                </div>
            </div>

            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Date de la proposition</div>
                <div style="font-weight:600;">{{ $proposition->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">Bien proposé</div>
                <div style="font-weight:600;margin-top:4px;">
                    {{ $proposition->bien->titre ?? 'N/A' }}
                    @if($proposition->bien)
                        <span class="meta-pill" style="font-size:10px;background:#E3F2FD;color:#0D47A1;">
                            {{ $proposition->bien->type_contrat_label ?? $proposition->bien->type_contrat }}
                        </span>
                    @endif
                </div>
                <div style="font-size:12px;color:var(--muted);">
                    <i class="fa-solid fa-location-dot"></i> {{ $proposition->bien->quartier_nom ?? 'N/A' }}
                    <span style="margin-left:12px;">
                        <i class="fa-solid fa-money-bill"></i> {{ number_format($proposition->bien->prix ?? 0, 0, ',', ' ') }} FCFA
                    </span>
                    <span style="margin-left:12px;">
                        <i class="fa-solid fa-ruler-combined"></i> {{ $proposition->bien->surface ?? 0 }} m²
                    </span>
                </div>
            </div>

            @if($proposition->message)
                <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                    <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Message de l'agence</div>
                    <div style="font-size:13px;color:var(--text-soft);line-height:1.6;">{{ $proposition->message }}</div>
                </div>
            @endif
        </div>

        <!-- Note : Lecture seule -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;">
                <i class="fa-solid fa-info-circle" style="color:#E65100;"></i>
                <span style="font-size:13px;color:var(--text-soft);">
                    <strong>Lecture seule</strong> — Les propositions ne peuvent pas être modifiées par l'administrateur.
                </span>
            </div>
        </div>
    </div>

    <!-- Photos du bien proposé -->
    <div>
        <div class="panel" style="margin-bottom:20px;">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-image"></i> Photos du bien proposé
                <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:8px;">
                    (Cliquez pour agrandir)
                </span>
            </h3>
            @php
                $photos = $proposition->bien->medias->where('type_media', 'image') ?? collect();
                $videos = $proposition->bien->medias->where('type_media', 'video') ?? collect();
                $allMedia = collect();
                foreach($photos as $media) {
                    $allMedia->push(['type' => 'image', 'url' => asset('storage/' . $media->fichier)]);
                }
                foreach($videos as $media) {
                    $allMedia->push(['type' => 'video', 'url' => asset('storage/' . $media->fichier)]);
                }
                $mediaJson = json_encode($allMedia);
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
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0;">
                    <i class="fa-solid fa-image" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    Aucune photo disponible.
                </p>
            @endif
        </div>

        <!-- Vidéos du bien -->
        @if($videos->count() > 0)
            <div class="panel">
                <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                    <i class="fa-solid fa-video"></i> Vidéos du bien
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

        <!-- Lien vers le bien -->
        <div style="margin-top:20px;">
            <a href="{{ route('admin.biens.show', $proposition->bien_id) }}" class="btn btn-rust" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-house"></i> Voir le bien en détail
            </a>
        </div>
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

<script>
    let mediaItems = @json($allMedia);
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

        // Mettre à jour les indicateurs
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

    // Navigation au clavier
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

    // Empêcher la propagation des clics sur le contenu
    document.getElementById('lightboxContent').addEventListener('click', function(e) {
        e.stopPropagation();
    });
</script>
@endsection