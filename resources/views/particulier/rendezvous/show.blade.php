@extends('layouts.dashboard')

@section('title', 'Détail du rendez-vous — DoyaImmo')
@section('page_title', 'Détail du rendez-vous')
@section('page_sub', 'Informations complètes sur votre visite')

@section('content')
<div class="view active">
    <!-- Bouton retour -->
    <div class="back-action">
        <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux rendez-vous
        </a>
    </div>

    <!-- Carte principale -->
    <div class="rdv-detail-card">
        <!-- En-tête -->
        <div class="rdv-detail-header">
            <div>
                <h3>{{ $rendezVous->proposition->bien->titre ?? 'Visite' }}</h3>
                <p class="sub">
                    <i class="fa-regular fa-calendar"></i>
                    {{ $rendezVous->date_visite->format('l d/m/Y') }}
                    à {{ \Carbon\Carbon::parse($rendezVous->heure_visite)->format('H:i') }}
                </p>
            </div>
            <span class="status-pill status-{{ $rendezVous->statut->value }}">
                <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                {{ $rendezVous->statut->label() }}
            </span>
        </div>

        <!-- Corps -->
        <div class="rdv-detail-body">
            <!-- Section Galerie -->
            <div class="gallery-section">
                <h4 class="section-title">
                    <i class="fa-regular fa-images" style="color:var(--rust);"></i>
                    Galerie du bien
                </h4>
                
                @php
                    $images = $rendezVous->proposition->bien->medias->where('type_media', 'image');
                    $videos = $rendezVous->proposition->bien->medias->where('type_media', 'video');
                @endphp

                @if($images->count() > 0 || $videos->count() > 0)
                    <!-- Images -->
                    @if($images->count() > 0)
                        <div class="gallery-grid">
                            @foreach($images as $media)
                                <div class="gallery-item" onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                    <img src="{{ asset('storage/' . $media->fichier) }}" alt="Photo du bien">
                                    <div class="gallery-overlay">
                                        <i class="fa-regular fa-magnifying-glass-plus"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Vidéos -->
                    @if($videos->count() > 0)
                        <div class="videos-section">
                            <h5 class="video-title">
                                <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i>
                                Vidéos ({{ $videos->count() }})
                            </h5>
                            <div class="videos-grid">
                                @foreach($videos as $media)
                                    <div class="video-item">
                                        <video src="{{ asset('storage/' . $media->fichier) }}" controls>
                                            Votre navigateur ne supporte pas la lecture de vidéos.
                                        </video>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Compteur -->
                    <div class="media-counter">
                        <span>
                            <i class="fa-regular fa-image"></i>
                            {{ $images->count() }} photo(s)
                        </span>
                        <span>
                            <i class="fa-regular fa-circle-play"></i>
                            {{ $videos->count() }} vidéo(s)
                        </span>
                    </div>
                @else
                    <div class="no-media">
                        <i class="fa-regular fa-image"></i>
                        <span>Aucune photo ou vidéo disponible</span>
                    </div>
                @endif
            </div>

            <!-- Informations du rendez-vous -->
            <div class="info-grid-2">
                <div class="info-card">
                    <div class="info-card-label">Date</div>
                    <div class="info-card-value">{{ $rendezVous->date_visite->format('d/m/Y') }}</div>
                    <div class="info-card-sub">{{ $rendezVous->date_visite->format('l') }}</div>
                </div>
                <div class="info-card">
                    <div class="info-card-label">Heure</div>
                    <div class="info-card-value">{{ \Carbon\Carbon::parse($rendezVous->heure_visite)->format('H:i') }}</div>
                    <div class="info-card-sub">Durée estimée : 1h</div>
                </div>
            </div>

            <!-- Agence -->
            <div class="agency-card">
                <div class="agency-avatar">
                    {{ strtoupper(substr($rendezVous->agence->nom_agence, 0, 1)) }}
                </div>
                <div class="agency-info">
                    <div class="agency-name">{{ $rendezVous->agence->nom_agence }}</div>
                    <div class="agency-rating">
                        <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                        {{ number_format($rendezVous->agence->note_moyenne, 1) }} / 5
                        <span class="agency-reviews">({{ $rendezVous->agence->evaluations->count() }} avis)</span>
                    </div>
                    <div class="agency-phone">
                        <i class="fa-solid fa-phone"></i> {{ $rendezVous->agence->user->telephone ?? 'Non renseigné' }}
                    </div>
                </div>
                <div class="agency-price">
                    <div class="price-amount">{{ number_format($rendezVous->proposition->prix_propose, 0, ',', ' ') }} FCFA</div>
                    <div class="price-label">Prix proposé</div>
                </div>
            </div>

            <!-- Détails du bien -->
            <div class="details-section">
                <h4 class="section-title">
                    <i class="fa-regular fa-building" style="color:var(--rust);"></i>
                    Détails du bien
                </h4>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Type</span>
                        <span class="detail-value">{{ $rendezVous->proposition->bien->type_bien->label() ?? '-' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Surface</span>
                        <span class="detail-value">{{ $rendezVous->proposition->bien->surface ?? 0 }} m²</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Chambres</span>
                        <span class="detail-value">{{ $rendezVous->proposition->bien->nombre_chambres ?? 0 }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Contrat</span>
                        <span class="detail-value">{{ $rendezVous->proposition->bien->type_contrat->label() ?? '-' }}</span>
                    </div>
                    <div class="detail-item full-width">
                        <span class="detail-label">Adresse</span>
                        <span class="detail-value">{{ $rendezVous->proposition->bien->adresse ?? 'Non spécifiée' }}</span>
                    </div>
                </div>
            </div>

            <!-- Message de sécurité -->
            @if(!$rendezVous->isPhoneVisible())
                <div class="security-notice">
                    <i class="fa-solid fa-shield-halved"></i>
                    <div>
                        <strong>Numéros masqués</strong>
                        <p>Les numéros de téléphone sont masqués tant que le rendez-vous n'est pas confirmé par l'agence.</p>
                    </div>
                </div>
            @else
                <div class="security-notice success">
                    <i class="fa-solid fa-check-circle"></i>
                    <div>
                        <strong>Coordonnées visibles</strong>
                        <p>Le rendez-vous est confirmé. Les numéros de téléphone sont maintenant visibles des deux côtés.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="rdv-detail-footer">
            @if($rendezVous->statut->value === 'planifie')
                <form action="{{ route('particulier.rendezvous.confirmer', $rendezVous) }}" method="POST" class="action-form">
                    @csrf
                    <button type="submit" class="btn btn-rust">
                        <i class="fa-solid fa-check"></i> Confirmer le rendez-vous
                    </button>
                </form>
                <form action="{{ route('particulier.rendezvous.annuler', $rendezVous) }}" method="POST" class="action-form">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-danger" onclick="return confirm('Annuler ce rendez-vous ?')">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </button>
                </form>
            @elseif($rendezVous->statut->value === 'confirme')
                <form action="{{ route('particulier.rendezvous.annuler', $rendezVous) }}" method="POST" class="action-form">
                    @csrf
                    <button type="submit" class="btn btn-ghost btn-danger" onclick="return confirm('Annuler ce rendez-vous ?')">
                        <i class="fa-solid fa-xmark"></i> Annuler
                    </button>
                </form>
            @elseif($rendezVous->statut->value === 'termine')
                <a href="{{ route('particulier.evaluations.create', $rendezVous->agence) }}" class="btn btn-rust">
                    <i class="fa-solid fa-star"></i> Évaluer l'agence
                </a>
            @endif
            <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;">
                <i class="fa-solid fa-list"></i> Tous mes rendez-vous
            </a>
        </div>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
    <img id="lightboxImage" class="lightbox-image" src="" alt="Agrandir">
</div>
@endsection

@push('scripts')
<script>
    // ===================== LIGHTBOX =====================
    function openLightbox(src) {
        const lightbox = document.getElementById('lightbox');
        const image = document.getElementById('lightboxImage');
        image.src = src;
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });
</script>
@endpush

@push('styles')
<style>
    /* ===================== BACK ===================== */
    .back-action {
        margin-bottom: 20px;
    }

    /* ===================== CARTE ===================== */
    .rdv-detail-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    /* ===================== HEADER ===================== */
    .rdv-detail-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        background: #FAFBFC;
    }

    .rdv-detail-header h3 {
        font-family: var(--display);
        font-size: 18px;
        font-weight: 700;
        margin: 0 0 4px;
    }

    .rdv-detail-header .sub {
        font-size: 13px;
        color: var(--muted);
        margin: 0;
    }

    .rdv-detail-header .sub i {
        margin-right: 4px;
    }

    /* ===================== STATUS ===================== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-planifie {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-confirme {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-annule {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-termine {
        background: #E8F5E9;
        color: #1E7A47;
    }

    /* ===================== BODY ===================== */
    .rdv-detail-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ===================== GALERIE ===================== */
    .gallery-section {
        border-bottom: 1px solid var(--border);
        padding-bottom: 20px;
    }

    .section-title {
        font-family: var(--display);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-soft);
    }

    .section-title i {
        color: var(--rust);
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        margin-bottom: 12px;
    }

    .gallery-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        aspect-ratio: 1;
        cursor: pointer;
        background: #F0F2F5;
        border: 1px solid var(--border);
        transition: transform 0.2s;
    }

    .gallery-item:hover {
        transform: scale(1.03);
        z-index: 2;
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
        color: #fff;
        font-size: 24px;
    }

    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }

    /* ===================== VIDEOS ===================== */
    .videos-section {
        margin-top: 8px;
    }

    .video-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-soft);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .videos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 12px;
    }

    .video-item {
        border-radius: 10px;
        overflow: hidden;
        background: #000;
        border: 1px solid var(--border);
    }

    .video-item video {
        width: 100%;
        height: 180px;
        object-fit: cover;
        display: block;
    }

    /* ===================== MEDIA COUNTER ===================== */
    .media-counter {
        display: flex;
        gap: 20px;
        padding: 10px 14px;
        background: #F7F9FC;
        border-radius: 10px;
        font-size: 13px;
        color: var(--muted);
        border: 1px solid var(--border);
    }

    .media-counter span {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .media-counter i {
        color: var(--rust);
    }

    /* ===================== NO MEDIA ===================== */
    .no-media {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 30px;
        background: #F7F9FC;
        border-radius: 10px;
        color: var(--muted);
        border: 1px dashed var(--border);
    }

    .no-media i {
        font-size: 32px;
        margin-bottom: 8px;
        opacity: 0.3;
    }

    .no-media span {
        font-size: 13px;
    }

    /* ===================== INFO CARDS ===================== */
    .info-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        border-bottom: 1px solid var(--border);
        padding-bottom: 20px;
    }

    .info-card {
        padding: 14px 18px;
        background: #F7F9FC;
        border-radius: 10px;
        border: 1px solid var(--border);
    }

    .info-card-label {
        font-size: 12px;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-card-value {
        font-weight: 700;
        font-size: 18px;
        color: var(--ink);
    }

    .info-card-sub {
        font-size: 12px;
        color: var(--muted);
    }

    /* ===================== AGENCE ===================== */
    .agency-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 14px 18px;
        background: #F7F9FC;
        border-radius: 10px;
        border: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .agency-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: var(--rust-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 700;
        color: var(--rust);
        flex-shrink: 0;
    }

    .agency-info {
        flex: 1;
        min-width: 120px;
    }

    .agency-name {
        font-weight: 600;
        font-size: 15px;
    }

    .agency-rating {
        font-size: 13px;
        color: var(--text-soft);
    }

    .agency-reviews {
        color: var(--muted);
        font-size: 12px;
    }

    .agency-phone {
        font-size: 12px;
        color: var(--muted);
    }

    .agency-price {
        text-align: right;
        flex-shrink: 0;
    }

    .price-amount {
        font-weight: 700;
        color: var(--rust);
        font-size: 16px;
    }

    .price-label {
        font-size: 12px;
        color: var(--muted);
    }

    /* ===================== DETAILS ===================== */
    .details-section {
        border-bottom: 1px solid var(--border);
        padding-bottom: 20px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 14px;
        background: #F7F9FC;
        border-radius: 8px;
        font-size: 13px;
    }

    .detail-item.full-width {
        grid-column: 1 / -1;
    }

    .detail-label {
        color: var(--muted);
    }

    .detail-value {
        font-weight: 500;
    }

    /* ===================== SECURITY NOTICE ===================== */
    .security-notice {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 16px;
        background: #FFF8E1;
        border: 1px solid #FFE0B2;
        border-radius: 10px;
    }

    .security-notice i {
        font-size: 20px;
        color: #E65100;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .security-notice strong {
        display: block;
        font-size: 13px;
        color: var(--ink);
    }

    .security-notice p {
        font-size: 13px;
        color: var(--text-soft);
        margin: 0;
    }

    .security-notice.success {
        background: #E8F5E9;
        border-color: #C8E6C9;
    }

    .security-notice.success i {
        color: #1E7A47;
    }

    /* ===================== FOOTER ===================== */
    .rdv-detail-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        background: #FAFBFC;
    }

    .action-form {
        display: inline;
        margin: 0;
        padding: 0;
    }

    /* ===================== BOUTONS ===================== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
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

    .btn-danger {
        color: #C62828;
        border-color: #FFCDD2;
    }

    .btn-danger:hover {
        background: #FFEBEE;
        border-color: #EF9A9A;
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }

    /* ===================== LIGHTBOX ===================== */
    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.92);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        cursor: pointer;
    }

    .lightbox.active {
        display: flex;
    }

    .lightbox-image {
        max-width: 95%;
        max-height: 90vh;
        object-fit: contain;
        cursor: default;
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        color: #fff;
        font-size: 40px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s;
        font-family: sans-serif;
        line-height: 1;
    }

    .lightbox-close:hover {
        opacity: 1;
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .info-grid-2 {
            grid-template-columns: 1fr;
        }

        .details-grid {
            grid-template-columns: 1fr;
        }

        .detail-item.full-width {
            grid-column: 1;
        }

        .rdv-detail-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .rdv-detail-body {
            padding: 16px 18px;
        }

        .rdv-detail-footer {
            flex-direction: column;
        }

        .rdv-detail-footer .btn {
            justify-content: center;
        }

        .agency-card {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .agency-price {
            text-align: center;
        }

        .gallery-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }

        .videos-grid {
            grid-template-columns: 1fr;
        }

        .video-item video {
            height: 150px;
        }

        .media-counter {
            flex-direction: column;
            gap: 4px;
            align-items: center;
        }

        .security-notice {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .rdv-detail-header h3 {
            font-size: 16px;
        }

        .rdv-detail-header .sub {
            font-size: 12px;
        }

        .info-card-value {
            font-size: 16px;
        }

        .price-amount {
            font-size: 14px;
        }

        .gallery-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }

        .gallery-item {
            aspect-ratio: 1;
        }

        .video-item video {
            height: 120px;
        }

        .detail-item {
            font-size: 12px;
            padding: 6px 10px;
        }

        .security-notice i {
            font-size: 28px;
        }
    }
</style>
@endpush