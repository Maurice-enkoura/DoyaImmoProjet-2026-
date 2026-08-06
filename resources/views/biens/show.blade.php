@extends('layouts.app')

@section('title', $bien->titre . ' — DoyaImmo')

@section('content')
<div class="wrap section" style="padding-top:36px;">
    <div class="detail-container">
        <!-- Navigation et partage -->
        <div class="bien-nav">
            <a href="{{ route('biens.index') }}" class="bien-nav-back">
                <i class="fa-solid fa-arrow-left"></i> Retour aux biens
            </a>
            <div class="bien-nav-share">
                <span class="share-label">Partager</span>
                <button onclick="shareFacebook()" class="share-btn share-fb" title="Partager sur Facebook">
                    <i class="fa-brands fa-facebook-f"></i>
                </button>
                <button onclick="shareWhatsApp()" class="share-btn share-wa" title="Partager sur WhatsApp">
                    <i class="fa-brands fa-whatsapp"></i>
                </button>
                <button onclick="copyLink()" class="share-btn share-link" title="Copier le lien">
                    <i class="fa-solid fa-link"></i>
                </button>
            </div>
        </div>

        <div class="detail-grid">
            <!-- Colonne gauche -->
            <div class="detail-main">
                <!-- Galerie -->
                <div class="gallery-container">
                    <div id="mainImage" class="gallery-main">
                        @php
                            $images = $bien->medias->where('type_media', 'image');
                            $videos = $bien->medias->where('type_media', 'video');
                            $firstImage = $images->first();
                        @endphp
                        @if($firstImage)
                            <img src="{{ asset('storage/' . $firstImage->fichier) }}" alt="{{ $bien->titre }}" loading="lazy">
                        @else
                            <div class="gallery-placeholder">
                                <i class="fa-solid fa-image"></i>
                                <span>Aucune image</span>
                            </div>
                        @endif
                        <div class="bien-status {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                            {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                        </div>
                        <div class="bien-views">
                            <i class="fa-regular fa-eye"></i> {{ $bien->vues ?? 0 }} vues
                        </div>
                        <div class="bien-type-on-image">
                            {{ $bien->type_bien->label() }}
                        </div>
                    </div>

                    <!-- Miniatures -->
                    @if($images->count() > 0 || $videos->count() > 0)
                        <div class="gallery-thumbnails">
                            @foreach($images as $image)
                                <div class="thumbnail" onclick="changeMainImage('{{ asset('storage/' . $image->fichier) }}')">
                                    <img src="{{ asset('storage/' . $image->fichier) }}" alt="" loading="lazy">
                                </div>
                            @endforeach
                            @foreach($videos as $video)
                                <div class="thumbnail thumbnail-video" onclick="playVideo('{{ asset('storage/' . $video->fichier) }}')">
                                    <video src="{{ asset('storage/' . $video->fichier) }}"></video>
                                    <div class="thumbnail-play">
                                        <i class="fa-solid fa-circle-play"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Description -->
                <div class="panel">
                    <h1 class="bien-title">{{ $bien->titre }}</h1>
                    <div class="bien-location">
                        <i class="fa-solid fa-location-dot"></i> {{ $bien->adresse }} · {{ $bien->quartier }}
                    </div>

                    <div class="bien-prix">
                        {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                        <span class="bien-contrat">{{ $bien->type_contrat->label() }}</span>
                    </div>

                    <div class="bien-tags">
                        <span class="meta-pill"><i class="fa-solid fa-home"></i> {{ $bien->type_bien->label() }}</span>
                        @if($bien->parking_disponible)
                            <span class="meta-pill"><i class="fa-solid fa-car"></i> Parking</span>
                        @endif
                        @if($bien->est_meuble)
                            <span class="meta-pill"><i class="fa-solid fa-couch"></i> Meublé</span>
                        @endif
                    </div>

                    <h3 class="section-title">Description</h3>
                    <p class="bien-description">{{ $bien->description }}</p>
                </div>

                <!-- Caractéristiques -->
                <div class="panel">
                    <h3 class="section-title">Caractéristiques</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Type de bien</span>
                            <span class="info-value">{{ $bien->type_bien->label() }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Surface</span>
                            <span class="info-value">{{ $bien->surface }} m²</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Chambres</span>
                            <span class="info-value">{{ $bien->nombre_chambres }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Salles de bain</span>
                            <span class="info-value">{{ $bien->nombre_salles_bain }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Parking</span>
                            <span class="info-value">{{ $bien->parking_disponible ? 'Disponible' : 'Non disponible' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Meublé</span>
                            <span class="info-value">{{ $bien->est_meuble ? 'Oui' : 'Non' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Contrat</span>
                            <span class="info-value">{{ $bien->type_contrat->label() }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Publié le</span>
                            <span class="info-value">{{ $bien->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Statut</span>
                            <span class="info-value">{{ $bien->statut ? 'Disponible' : 'Indisponible' }}</span>
                        </div>
                        <div class="info-item full">
                            <span class="info-label">Vues</span>
                            <span class="info-value">{{ $bien->vues ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite -->
            <div class="detail-sidebar">
                <div class="sticky-box">
                    <!-- Agence -->
                    <div class="panel agency-card">
                        <h3 class="section-title">Agence</h3>
                        
                        <div class="agency-header">
                            <div class="agency-avatar">
                                {{ substr($bien->agence->nom_agence, 0, 1) }}
                            </div>
                            <div>
                                <div class="agency-name">{{ $bien->agence->nom_agence }}</div>
                                <div class="agency-rating">
                                    <i class="fa-solid fa-star" style="color:#F5A623;"></i> 
                                    {{ number_format($bien->agence->note_moyenne, 1) }} / 5
                                    ({{ $bien->agence->evaluations->count() }} avis)
                                </div>
                            </div>
                        </div>

                        <div class="agency-address">
                            <i class="fa-solid fa-location-dot"></i> {{ $bien->agence->adresse }}
                        </div>

                        <div class="agency-contact">
                            @if($bien->agence->user->telephone)
                                <div>
                                    <i class="fa-solid fa-phone"></i>
                                    <a href="tel:{{ $bien->agence->user->telephone }}">{{ $bien->agence->user->telephone }}</a>
                                </div>
                            @endif
                            @if($bien->agence->user->email)
                                <div>
                                    <i class="fa-solid fa-envelope"></i>
                                    <a href="mailto:{{ $bien->agence->user->email }}">{{ $bien->agence->user->email }}</a>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('agences.public.show', $bien->agence) }}" class="btn btn-ghost btn-block">
                            <i class="fa-solid fa-building"></i> Voir le profil
                        </a>
                    </div>

                    <!-- CTA -->
                    <div class="panel cta-card">
                        <div class="cta-content">
                            <h3>Vous cherchez un logement ?</h3>
                            <p>Publiez vos critères et recevez des propositions sur mesure des agences.</p>
                            
                            @auth
                                @if(auth()->user()->isParticulier())
                                    <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust btn-block">
                                        <i class="fa-solid fa-plus"></i> Publier un besoin
                                    </a>
                                @elseif(auth()->user()->isAgence())
                                    <a href="{{ route('agence.dashboard') }}" class="btn btn-ghost btn-block">
                                        <i class="fa-solid fa-gauge"></i> Tableau de bord
                                    </a>
                                @endif
                            @else
                                <div class="cta-buttons">
                                    <a href="{{ route('register.particulier') }}" class="btn btn-rust btn-block">
                                        <i class="fa-solid fa-user-plus"></i> Créer un compte
                                    </a>
                                    <p class="cta-free">Gratuit · En 2 minutes</p>
                                </div>
                            @endauth
                        </div>
                    </div>

                    <!-- Signalement -->
                    @auth
                        @if(auth()->user()->isParticulier())
                            <div class="panel report-card">
                                <h3>Signaler ce bien</h3>
                                <p>Vous avez remarqué une anomalie ou une information incorrecte ?</p>
                                <a href="{{ route('particulier.signalements.create-bien', $bien) }}" class="btn btn-ghost btn-sm btn-block report-btn">
                                    <i class="fa-solid fa-flag"></i> Signaler ce bien
                                </a>
                            </div>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Biens similaires -->
        @if($biensSimilaires->count() > 0)
        <div class="similar-section">
            <h2 class="similar-title">Biens similaires</h2>
            <div class="biens-grid">
                @foreach($biensSimilaires as $bienSimilaire)
                    <div class="bien-card">
                        <div class="bien-image">
                            @php
                                $simImage = $bienSimilaire->medias->where('type_media', 'image')->first();
                            @endphp
                            @if($simImage)
                                <img src="{{ asset('storage/' . $simImage->fichier) }}" alt="{{ $bienSimilaire->titre }}" loading="lazy">
                            @else
                                <div class="image-placeholder">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                            <div class="similar-type-badge">{{ $bienSimilaire->type_bien->label() }}</div>
                            <div class="similar-status {{ $bienSimilaire->statut ? 'disponible' : 'indisponible' }}">
                                {{ $bienSimilaire->statut ? 'Disponible' : 'Indisponible' }}
                            </div>
                        </div>
                        <div class="bien-body">
                            <div class="bien-title">{{ $bienSimilaire->titre }}</div>
                            <div class="bien-price">{{ number_format($bienSimilaire->prix, 0, ',', ' ') }} FCFA</div>
                            <div class="bien-location">
                                <i class="fa-solid fa-location-dot"></i> {{ $bienSimilaire->quartier }}
                            </div>
                            <div class="bien-features">
                                <span class="meta-pill">
                                    <i class="fa-solid fa-vector-square"></i> {{ $bienSimilaire->surface }} m²
                                </span>
                                <span class="meta-pill">
                                    <i class="fa-solid fa-bed"></i> {{ $bienSimilaire->nombre_chambres }} ch.
                                </span>
                            </div>
                            <div class="bien-action">
                                <a href="{{ route('biens.show', $bienSimilaire) }}" class="btn btn-ghost btn-sm btn-block">Voir</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Changer l'image principale
    function changeMainImage(src) {
        const mainImage = document.getElementById('mainImage');
        mainImage.innerHTML = `<img src="${src}" alt="Bien" loading="lazy">`;
        
        document.querySelectorAll('.thumbnail').forEach(el => {
            el.classList.remove('active');
        });
        if (event && event.target) {
            event.target.closest('.thumbnail').classList.add('active');
        }
    }

    // Lire une vidéo
    function playVideo(src) {
        const mainImage = document.getElementById('mainImage');
        mainImage.innerHTML = `
            <video src="${src}" controls autoplay>
                Votre navigateur ne supporte pas la lecture de vidéos.
            </video>
        `;
        
        document.querySelectorAll('.thumbnail').forEach(el => {
            el.classList.remove('active');
        });
        if (event && event.target) {
            event.target.closest('.thumbnail').classList.add('active');
        }
    }

    // Partager sur Facebook
    function shareFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
    }

    // Partager sur WhatsApp
    function shareWhatsApp() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent("Découvrez ce bien immobilier sur DoyaImmo !");
        window.open(`https://wa.me/?text=${text}%20${url}`, '_blank', 'width=600,height=400');
    }

    // Copier le lien
    function copyLink() {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(window.location.href).then(() => {
                showToast('Lien copié dans le presse-papier !');
            }).catch(() => {
                fallbackCopy();
            });
        } else {
            fallbackCopy();
        }
    }

    function fallbackCopy() {
        const input = document.createElement('input');
        input.value = window.location.href;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('Lien copié dans le presse-papier !');
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--ink);
            color: #fff;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            z-index: 9999;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            animation: fadeInUp 0.3s ease;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.animation = 'fadeOutDown 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
@endpush

@push('styles')
<style>
    /* ===== CONTENEUR ===== */
    .detail-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    @media (min-width: 768px) {
        .detail-container {
            padding: 0 24px;
        }
    }

    @media (min-width: 1200px) {
        .detail-container {
            padding: 0 40px;
        }
    }

    /* ===== NAVIGATION ===== */
    .bien-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
    }

    .bien-nav-back {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: color 0.2s;
    }

    .bien-nav-back:hover {
        color: var(--rust);
    }

    .bien-nav-share {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .share-label {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
        margin-right: 4px;
    }

    .share-btn {
        border: none;
        border-radius: 50%;
        width: clamp(32px, 3vw, 36px);
        height: clamp(32px, 3vw, 36px);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
        font-size: clamp(12px, 1vw, 14px);
    }

    .share-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .share-fb { background: #1877F2; color: #fff; }
    .share-wa { background: #25D366; color: #fff; }
    .share-link { background: var(--border); color: var(--text-soft); }

    /* ===== DETAIL GRID ===== */
    .detail-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: clamp(20px, 3vw, 30px);
        align-items: start;
    }

    .detail-main {
        min-width: 0;
    }

    .detail-sidebar {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sticky-box {
        position: sticky;
        top: 100px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    /* ===== GALLERIE ===== */
    .gallery-container {
        background: #E8ECF0;
        border-radius: var(--radius);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .gallery-main {
        position: relative;
        min-height: clamp(250px, 40vw, 400px);
        background: #E8ECF0;
    }

    .gallery-main img {
        width: 100%;
        height: clamp(250px, 40vw, 400px);
        object-fit: cover;
    }

    .gallery-main video {
        width: 100%;
        height: clamp(250px, 40vw, 400px);
        object-fit: cover;
        background: #000;
    }

    .gallery-placeholder {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: clamp(250px, 40vw, 400px);
        color: var(--muted);
    }

    .gallery-placeholder i {
        font-size: 48px;
        opacity: 0.3;
        margin-bottom: 8px;
    }

    .bien-status {
        position: absolute;
        top: clamp(12px, 1.5vw, 16px);
        right: clamp(12px, 1.5vw, 16px);
        padding: clamp(4px, 0.5vw, 6px) clamp(12px, 1.5vw, 16px);
        border-radius: 999px;
        font-size: clamp(10px, 0.8vw, 12px);
        font-weight: 600;
        color: #fff;
    }

    .bien-status.disponible { background: #1E7A47; }
    .bien-status.indisponible { background: var(--muted); }

    .bien-views {
        position: absolute;
        bottom: clamp(12px, 1.5vw, 16px);
        left: clamp(12px, 1.5vw, 16px);
        padding: clamp(3px, 0.4vw, 4px) clamp(10px, 1.2vw, 14px);
        border-radius: 999px;
        font-size: clamp(10px, 0.8vw, 12px);
        font-weight: 500;
        color: #fff;
        background: rgba(0,0,0,0.6);
    }

    .bien-type-on-image {
        position: absolute;
        bottom: clamp(12px, 1.5vw, 16px);
        right: clamp(12px, 1.5vw, 16px);
        padding: clamp(3px, 0.4vw, 4px) clamp(10px, 1.2vw, 14px);
        border-radius: 999px;
        font-size: clamp(10px, 0.8vw, 11px);
        font-weight: 600;
        color: #fff;
        background: rgba(0,0,0,0.7);
    }

    .gallery-thumbnails {
        display: flex;
        gap: 8px;
        padding: clamp(10px, 1vw, 12px) clamp(12px, 1.5vw, 16px);
        background: #fff;
        border-top: 1px solid var(--border);
        overflow-x: auto;
        scrollbar-width: thin;
        -webkit-overflow-scrolling: touch;
    }

    .gallery-thumbnails::-webkit-scrollbar {
        height: 4px;
    }

    .gallery-thumbnails::-webkit-scrollbar-thumb {
        background: var(--border);
        border-radius: 10px;
    }

    .thumbnail {
        width: clamp(56px, 6vw, 70px);
        height: clamp(56px, 6vw, 70px);
        border-radius: 8px;
        overflow: hidden;
        border: 2px solid transparent;
        cursor: pointer;
        flex-shrink: 0;
        transition: border 0.2s;
        position: relative;
    }

    .thumbnail:hover { border-color: var(--rust); }
    .thumbnail.active { border-color: var(--rust); }

    .thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .thumbnail-video {
        background: #000;
    }

    .thumbnail-video video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.6;
    }

    .thumbnail-play {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
        font-size: clamp(18px, 2vw, 20px);
        opacity: 0.8;
    }

    /* ===== PANELS ===== */
    .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: clamp(16px, 2vw, 20px) clamp(16px, 2vw, 24px);
        margin-bottom: 20px;
    }

    .panel:last-child {
        margin-bottom: 0;
    }

    /* ===== BIEN ===== */
    .bien-title {
        font-family: var(--display);
        font-weight: 800;
        font-size: clamp(22px, 3vw, 28px);
        margin-bottom: 6px;
        word-break: break-word;
    }

    .bien-location {
        font-size: clamp(13px, 0.9vw, 14px);
        color: var(--muted);
        margin-bottom: 14px;
        word-break: break-word;
    }

    .bien-prix {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: var(--rust-soft);
        color: var(--rust);
        padding: clamp(6px, 0.6vw, 8px) clamp(14px, 1.5vw, 16px);
        border-radius: 12px;
        font-weight: 700;
        font-size: clamp(16px, 1.8vw, 18px);
        margin-bottom: 12px;
        flex-wrap: wrap;
    }

    .bien-contrat {
        font-weight: 400;
        font-size: clamp(11px, 0.8vw, 13px);
        color: var(--text-soft);
        background: rgba(255,255,255,0.6);
        padding: 2px 12px;
        border-radius: 999px;
    }

    .bien-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }

    .bien-description {
        font-size: clamp(13px, 0.9vw, 14px);
        line-height: 1.8;
        color: var(--text-soft);
    }

    .section-title {
        font-family: var(--display);
        font-size: clamp(15px, 1.1vw, 16px);
        margin-bottom: 12px;
    }

    /* ===== INFO GRID ===== */
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 4px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 12px;
        background: #F7F9FC;
        border-radius: 6px;
        font-size: clamp(12px, 0.8vw, 13px);
        gap: 8px;
    }

    .info-item.full {
        grid-column: 1 / -1;
    }

    .info-label {
        color: var(--muted);
        flex-shrink: 0;
    }

    .info-value {
        font-weight: 500;
        text-align: right;
        word-break: break-word;
    }

    /* ===== AGENCE ===== */
    .agency-card .section-title {
        margin-bottom: 16px;
    }

    .agency-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }

    .agency-avatar {
        width: clamp(40px, 4vw, 48px);
        height: clamp(40px, 4vw, 48px);
        border-radius: 50%;
        background: var(--rust-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(18px, 1.8vw, 20px);
        font-weight: 700;
        color: var(--rust);
        flex-shrink: 0;
    }

    .agency-name {
        font-weight: 600;
        font-size: clamp(14px, 1vw, 15px);
    }

    .agency-rating {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--text-soft);
    }

    .agency-address {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--text-soft);
        margin-bottom: 12px;
        word-break: break-word;
    }

    .agency-contact {
        background: #F7F9FC;
        border-radius: 10px;
        padding: 12px 14px;
        margin-bottom: 14px;
        border: 1px solid var(--border);
    }

    .agency-contact div {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 4px 0;
        word-break: break-word;
    }

    .agency-contact i {
        color: var(--rust);
        width: 16px;
        flex-shrink: 0;
    }

    .agency-contact a {
        color: var(--ink);
        text-decoration: none;
        font-weight: 500;
        font-size: clamp(12px, 0.8vw, 13px);
    }

    .agency-contact a:hover {
        color: var(--rust);
    }

    /* ===== CTA ===== */
    .cta-card {
        background: var(--rust-soft);
        border-color: rgba(181, 80, 42, 0.2);
    }

    .cta-content {
        text-align: center;
    }

    .cta-content h3 {
        font-family: var(--display);
        font-size: clamp(15px, 1.1vw, 16px);
        margin-bottom: 6px;
        color: var(--ink);
    }

    .cta-content p {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--text-soft);
        line-height: 1.6;
        margin-bottom: 14px;
    }

    .cta-buttons {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .cta-free {
        font-size: 11px;
        color: var(--muted);
        margin: 0;
    }

    /* ===== SIGNALEMENT ===== */
    .report-card h3 {
        font-family: var(--display);
        font-size: clamp(13px, 0.9vw, 14px);
        margin-bottom: 8px;
    }

    .report-card p {
        font-size: clamp(11px, 0.7vw, 12px);
        color: var(--muted);
        margin-bottom: 12px;
    }

    .report-btn {
        color: #C62828;
        border-color: #FFCDD2;
    }

    .report-btn:hover {
        background: #FFEBEE;
        border-color: #EF9A9A;
    }

    /* ===== BIENS SIMILAIRES ===== */
    .similar-section {
        margin-top: clamp(32px, 4vw, 48px);
    }

    .similar-title {
        font-family: var(--display);
        font-size: clamp(20px, 2.5vw, 22px);
        margin-bottom: 20px;
    }

    .biens-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 220px), 1fr));
        gap: 16px;
    }

    .bien-card {
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s;
        display: flex;
        flex-direction: column;
    }

    .bien-card:hover {
        transform: translateY(-3px);
    }

    .bien-image {
        background: #E8ECF0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        position: relative;
        overflow: hidden;
        height: clamp(140px, 18vw, 160px);
        flex-shrink: 0;
    }

    .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        opacity: 0.3;
    }

    .image-placeholder i {
        font-size: 32px;
    }

    .similar-type-badge {
        position: absolute;
        bottom: 8px;
        left: 8px;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: clamp(9px, 0.7vw, 10px);
        font-weight: 600;
        color: #fff;
        background: rgba(0,0,0,0.7);
    }

    .similar-status {
        position: absolute;
        top: 8px;
        right: 8px;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: clamp(9px, 0.7vw, 10px);
        font-weight: 600;
        color: #fff;
    }

    .similar-status.disponible { background: #1E7A47; }
    .similar-status.indisponible { background: var(--muted); }

    .bien-body {
        padding: 12px 14px 14px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .bien-title {
        font-weight: 600;
        font-size: clamp(13px, 0.9vw, 14px);
        margin-bottom: 2px;
        word-break: break-word;
    }

    .bien-price {
        font-weight: 700;
        color: var(--rust);
        font-size: clamp(13px, 0.9vw, 14px);
    }

    .bien-location {
        font-size: clamp(11px, 0.7vw, 12px);
        color: var(--muted);
        margin-bottom: 6px;
        word-break: break-word;
    }

    .bien-features {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 8px;
    }

    .bien-action {
        margin-top: auto;
    }

    /* ===== META PILL ===== */
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11px);
        color: var(--text-soft);
    }

    .meta-pill i {
        font-size: 10px;
    }

    /* ===== BUTTONS ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: clamp(11px, 0.8vw, 12.5px);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
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
        background: var(--border);
        color: var(--ink);
    }

    .btn-sm {
        padding: 4px 10px;
        font-size: clamp(10px, 0.7vw, 11px);
        border-radius: 6px;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    /* ============================================
       RESPONSIVE
    ============================================ */

    @media (max-width: 900px) {
        .detail-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }

        .sticky-box {
            position: static;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .detail-sidebar {
            gap: 0;
        }

        .gallery-main {
            min-height: 300px;
        }

        .gallery-main img,
        .gallery-main video {
            height: 300px;
        }

        .gallery-placeholder {
            height: 300px;
        }

        .info-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 640px) {
        .bien-nav {
            flex-direction: column;
            align-items: stretch;
            gap: 12px;
        }

        .bien-nav-share {
            justify-content: flex-start;
        }

        .sticky-box {
            grid-template-columns: 1fr;
            gap: 14px;
        }

        .gallery-main {
            min-height: 250px;
        }

        .gallery-main img,
        .gallery-main video {
            height: 250px;
        }

        .gallery-placeholder {
            height: 250px;
        }

        .gallery-thumbnails {
            padding: 8px 12px;
        }

        .thumbnail {
            width: 56px;
            height: 56px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .info-item.full {
            grid-column: 1;
        }

        .bien-title {
            font-size: 20px;
        }

        .panel {
            padding: 14px 16px;
            border-radius: 12px;
        }

        .biens-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .similar-title {
            font-size: 18px;
        }

        .bien-image {
            height: 140px;
        }

        .similar-type-badge {
            font-size: 9px;
            padding: 2px 8px;
        }

        .similar-status {
            font-size: 9px;
            padding: 2px 8px;
        }

        .bien-views {
            font-size: 10px;
            padding: 3px 10px;
        }

        .bien-status {
            font-size: 10px;
            padding: 4px 12px;
        }

        .bien-type-on-image {
            font-size: 10px;
            padding: 3px 10px;
        }
    }

    @media (max-width: 460px) {
        .biens-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .bien-image {
            height: 180px;
        }

        .gallery-main {
            min-height: 200px;
        }

        .gallery-main img,
        .gallery-main video {
            height: 200px;
        }

        .gallery-placeholder {
            height: 200px;
        }

        .thumbnail {
            width: 48px;
            height: 48px;
        }

        .bien-title {
            font-size: 18px;
        }

        .bien-prix {
            font-size: 15px;
        }

        .share-btn {
            width: 30px;
            height: 30px;
            font-size: 11px;
        }
    }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateX(-50%) translateY(20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    @keyframes fadeOutDown {
        from { opacity: 1; transform: translateX(-50%) translateY(0); }
        to { opacity: 0; transform: translateX(-50%) translateY(20px); }
    }
</style>
@endpush