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
                            <img src="{{ asset('storage/' . $firstImage->fichier) }}" 
                                 alt="{{ $bien->titre }}" 
                                 loading="lazy"
                                 onclick="openLightbox(0)"
                                 style="cursor:pointer;">
                        @else
                            <div class="gallery-placeholder">
                                <i class="fa-solid fa-image"></i>
                                <span>Aucune image</span>
                            </div>
                        @endif
                        
                        <!-- BADGE VEDETTE - en haut à GAUCHE -->
                        @if($bien->est_vedette && $bien->vedette_fin > now())
                            <div class="badge-vedette-detail">
                                <i class="fa-solid fa-star"></i> En vedette
                                <span style="font-size:10px;font-weight:400;opacity:0.8;margin-left:4px;">
                                    ({{ $bien->vedette_jours_restants }} jour(s) restant(s))
                                </span>
                            </div>
                        @endif
                        
                        <!-- BADGE STATUS - en haut à DROITE -->
                        <div class="bien-status {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                            {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                        </div>
                        
                        <!-- VUES - en bas à GAUCHE -->
                        <div class="bien-views">
                            <i class="fa-regular fa-eye"></i> {{ $bien->vues ?? 0 }} vues
                        </div>
                        
                        <!-- TYPE - en bas à DROITE -->
                        <div class="bien-type-on-image">
                            {{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}
                        </div>
                    </div>

                    <!-- Miniatures -->
                    @if($images->count() > 0 || $videos->count() > 0)
                        <div class="gallery-thumbnails">
                            @foreach($images as $index => $image)
                                <div class="thumbnail" onclick="changeMainImage('{{ asset('storage/' . $image->fichier) }}', {{ $index }})">
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
                        <i class="fa-solid fa-location-dot"></i> {{ $bien->adresse }} · {{ $bien->quartier->nom ?? $bien->quartier }}
                    </div>

                    <div class="bien-prix">
                        {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                        <span class="bien-contrat">{{ is_object($bien->type_contrat) && method_exists($bien->type_contrat, 'label') ? $bien->type_contrat->label() : $bien->type_contrat }}</span>
                        @if($bien->est_vedette && $bien->vedette_fin > now())
                            <span class="bien-vedette-tag">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif
                    </div>

                    <div class="bien-tags">
                        <span class="meta-pill"><i class="fa-solid fa-home"></i> {{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}</span>
                        @if($bien->parking_disponible)
                            <span class="meta-pill"><i class="fa-solid fa-car"></i> Parking</span>
                        @endif
                        @if($bien->est_meuble)
                            <span class="meta-pill"><i class="fa-solid fa-couch"></i> Meublé</span>
                        @endif
                        @if($bien->climatisation)
                            <span class="meta-pill"><i class="fa-solid fa-snowflake"></i> Climatisation</span>
                        @endif
                        @if($bien->piscine)
                            <span class="meta-pill"><i class="fa-solid fa-water"></i> Piscine</span>
                        @endif
                        @if($bien->jardin)
                            <span class="meta-pill"><i class="fa-solid fa-tree"></i> Jardin</span>
                        @endif
                        @if($bien->balcon)
                            <span class="meta-pill"><i class="fa-solid fa-umbrella"></i> Balcon</span>
                        @endif
                        @if($bien->ascenseur)
                            <span class="meta-pill"><i class="fa-solid fa-elevator"></i> Ascenseur</span>
                        @endif
                        @if($bien->securite)
                            <span class="meta-pill"><i class="fa-solid fa-shield"></i> Sécurité</span>
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
                            <span class="info-value">{{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Surface</span>
                            <span class="info-value">{{ $bien->surface }} m²</span>
                        </div>
                        
                        @if($bien->nombre_chambres > 0)
                        <div class="info-item">
                            <span class="info-label">Chambres</span>
                            <span class="info-value">{{ $bien->nombre_chambres }}</span>
                        </div>
                        @endif
                        
                        @if($bien->nombre_salles_bain > 0)
                        <div class="info-item">
                            <span class="info-label">Salles de bain</span>
                            <span class="info-value">{{ $bien->nombre_salles_bain }}</span>
                        </div>
                        @endif
                        
                        <div class="info-item">
                            <span class="info-label">Parking</span>
                            <span class="info-value">{{ $bien->parking_disponible ? ' Disponible' : 'Non disponible' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <span class="info-label">Meublé</span>
                            <span class="info-value">{{ $bien->est_meuble ? ' Oui' : 'Non' }}</span>
                        </div>
                        
                        @if($bien->climatisation)
                        <div class="info-item">
                            <span class="info-label">Climatisation</span>
                            <span class="info-value"> Oui</span>
                        </div>
                        @endif
                        
                        @if($bien->balcon)
                        <div class="info-item">
                            <span class="info-label">Balcon</span>
                            <span class="info-value"> Oui</span>
                        </div>
                        @endif
                        
                        @if($bien->jardin)
                        <div class="info-item">
                            <span class="info-label">Jardin</span>
                            <span class="info-value"> Oui</span>
                        </div>
                        @endif
                        
                        @if($bien->piscine)
                        <div class="info-item">
                            <span class="info-label">Piscine</span>
                            <span class="info-value"> Oui</span>
                        </div>
                        @endif
                        
                        @if($bien->ascenseur)
                        <div class="info-item">
                            <span class="info-label">Ascenseur</span>
                            <span class="info-value"> Oui</span>
                        </div>
                        @endif
                        
                        @if($bien->securite)
                        <div class="info-item">
                            <span class="info-label">Sécurité 24h/24</span>
                            <span class="info-value"> Oui</span>
                        </div>
                        @endif
                        
                        <div class="info-item">
                            <span class="info-label">Contrat</span>
                            <span class="info-value">{{ is_object($bien->type_contrat) && method_exists($bien->type_contrat, 'label') ? $bien->type_contrat->label() : $bien->type_contrat }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Publié le</span>
                            <span class="info-value">{{ $bien->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Statut</span>
                            <span class="info-value">{{ $bien->statut ? 'Disponible' : 'Indisponible' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Vues</span>
                            <span class="info-value">{{ $bien->vues ?? 0 }}</span>
                        </div>

                        <!-- ✅ ÉQUIPEMENTS -->
                        @php
                            $equipements = $bien->equipements;
                        @endphp
                        @if(count($equipements) > 0)
                        <div class="info-item full" style="border: 1px solid #C8E6C9; background: #E8F5E9;">
                            <span class="info-label" style="color:#1E7A47; font-weight:600;">
                                <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i> Équipements
                            </span>
                            <span class="info-value" style="color:#1E7A47; text-align:right;">
                                {{ implode(' · ', $equipements) }}
                            </span>
                        </div>
                        @endif
                        
                        @if($bien->est_vedette && $bien->vedette_fin > now())
                        <div class="info-item full" style="border: 2px solid #D4AF37; background: #FDF5E6;">
                            <span class="info-label" style="color:#D4AF37; font-weight:700;">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                            <span class="info-value" style="color:#B85C3A; font-weight:600;">
                                Jusqu'au {{ $bien->vedette_fin->format('d/m/Y') }}
                                ({{ $bien->vedette_jours_restants }} jour(s) restant(s))
                            </span>
                        </div>
                        @endif
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
                                    <i class="fa-solid fa-star" style="color:#D4AF37;"></i> 
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

                        <a href="{{ route('agences.public.show', $bien->agence->slug) }}" class="btn btn-ghost btn-block">
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
                                <a href="{{ route('particulier.signalements.create-bien', $bien->slug) }}" class="btn btn-ghost btn-sm btn-block report-btn">
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
                            @if($bienSimilaire->est_vedette && $bienSimilaire->vedette_fin > now())
                                <div class="badge-vedette-similar">
                                    <i class="fa-solid fa-star"></i> Vedette
                                </div>
                            @endif
                            <div class="similar-type-badge">{{ is_object($bienSimilaire->type_bien) && method_exists($bienSimilaire->type_bien, 'label') ? $bienSimilaire->type_bien->label() : $bienSimilaire->type_bien }}</div>
                            <div class="similar-status {{ $bienSimilaire->statut ? 'disponible' : 'indisponible' }}">
                                {{ $bienSimilaire->statut ? 'Disponible' : 'Indisponible' }}
                            </div>
                        </div>
                        <div class="bien-body">
                            <div class="bien-title">{{ $bienSimilaire->titre }}</div>
                            <div class="bien-price">{{ number_format($bienSimilaire->prix, 0, ',', ' ') }} FCFA</div>
                            <div class="bien-location">
                                <i class="fa-solid fa-location-dot"></i> {{ $bienSimilaire->quartier->nom ?? $bienSimilaire->quartier }}
                            </div>
                            <div class="bien-features">
                                <span class="meta-pill">
                                    <i class="fa-solid fa-vector-square"></i> {{ $bienSimilaire->surface }} m²
                                </span>
                                <span class="meta-pill">
                                    <i class="fa-solid fa-bed"></i> {{ $bienSimilaire->nombre_chambres }} ch.
                                </span>
                                @if($bienSimilaire->climatisation)
                                    <span class="meta-pill"><i class="fa-solid fa-snowflake"></i></span>
                                @endif
                                @if($bienSimilaire->piscine)
                                    <span class="meta-pill"><i class="fa-solid fa-water"></i></span>
                                @endif
                            </div>
                            <div class="bien-action">
                                <a href="{{ route('biens.show', $bienSimilaire->slug) }}" class="btn btn-ghost btn-sm btn-block">Voir</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- ============================================ -->
<!-- LIGHTBOX AVEC NAVIGATION < et > -->
<!-- ============================================ -->
<div id="lightbox" class="lightbox" onclick="closeLightboxOutside(event)">
    <button class="lightbox-close" onclick="closeLightbox()" aria-label="Fermer">
        <i class="fa-solid fa-xmark"></i>
    </button>
    
    <!-- Flèche précédent -->
    <button class="lightbox-nav lightbox-prev" id="lightboxPrev" onclick="lightboxPrev()" aria-label="Précédent">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    
    <!-- Flèche suivant -->
    <button class="lightbox-nav lightbox-next" id="lightboxNext" onclick="lightboxNext()" aria-label="Suivant">
        <i class="fa-solid fa-chevron-right"></i>
    </button>
    
    <div class="lightbox-content" id="lightboxContent">
        <img id="lightboxImage" src="" alt="Agrandir">
        <div class="lightbox-counter" id="lightboxCounter"></div>
    </div>
</div>

<style>
    /* ============================================
       LIGHTBOX
    ============================================ */
    .lightbox {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.95);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
        backdrop-filter: blur(8px);
    }

    .lightbox.active {
        display: flex;
        animation: lightboxFadeIn 0.3s ease;
    }

    @keyframes lightboxFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        background: none;
        border: none;
        color: #fff;
        font-size: 40px;
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s, transform 0.2s;
        z-index: 10;
        padding: 8px;
        line-height: 1;
    }

    .lightbox-close:hover {
        opacity: 1;
        transform: scale(1.1);
    }

    .lightbox-content {
        position: relative;
        max-width: 95vw;
        max-height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        touch-action: pan-y;
    }

    .lightbox-content img {
        max-width: 95vw;
        max-height: 85vh;
        object-fit: contain;
        border-radius: 4px;
        user-select: none;
        -webkit-user-select: none;
    }

    .lightbox-counter {
        position: absolute;
        bottom: -40px;
        left: 50%;
        transform: translateX(-50%);
        color: rgba(255,255,255,0.6);
        font-size: 14px;
        font-weight: 500;
        background: rgba(0,0,0,0.4);
        padding: 4px 16px;
        border-radius: 999px;
        backdrop-filter: blur(4px);
    }

    /* ============================================
       FLÈCHES DE NAVIGATION
    ============================================ */
    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.15);
        border: none;
        color: #fff;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
        z-index: 5;
    }

    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-50%) scale(1.05);
    }

    .lightbox-nav:active {
        transform: translateY(-50%) scale(0.95);
    }

    .lightbox-prev {
        left: 20px;
    }

    .lightbox-next {
        right: 20px;
    }

    .lightbox-nav.hidden {
        display: none;
    }

    /* ============================================
       RESPONSIVE LIGHTBOX
    ============================================ */
    @media (max-width: 768px) {
        .lightbox-nav {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }

        .lightbox-prev {
            left: 8px;
        }

        .lightbox-next {
            right: 8px;
        }

        .lightbox-close {
            top: 12px;
            right: 16px;
            font-size: 30px;
        }

        .lightbox-content img {
            max-height: 80vh;
        }
    }

    @media (max-width: 480px) {
        .lightbox-nav {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .lightbox-prev {
            left: 4px;
        }

        .lightbox-next {
            right: 4px;
        }

        .lightbox-close {
            top: 8px;
            right: 12px;
            font-size: 24px;
        }

        .lightbox-counter {
            font-size: 12px;
            padding: 2px 12px;
            bottom: -32px;
        }
    }

    /* ============================================
       BADGES SUR L'IMAGE EN DETAIL
    ============================================ */
    .gallery-main {
        position: relative;
        min-height: clamp(250px, 40vw, 400px);
        background: #F0F2F5;
    }

    .gallery-main img {
        width: 100%;
        height: clamp(250px, 40vw, 400px);
        object-fit: cover;
        cursor: pointer;
    }

    .badge-vedette-detail {
        position: absolute;
        top: 16px;
        left: 16px;
        z-index: 5;
        padding: 6px 18px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #D4AF37 0%, #E8951A 100%);
        box-shadow: 0 4px 16px rgba(212, 175, 55, 0.4);
        display: flex;
        align-items: center;
        gap: 6px;
        animation: pulseVedette 2s ease-in-out infinite;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .badge-vedette-detail i {
        font-size: 14px;
        color: #fff;
    }

    .gallery-main .bien-status {
        position: absolute;
        top: 16px;
        right: 16px;
        z-index: 5;
        padding: 6px 18px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .gallery-main .bien-status.disponible {
        background: #2A9D8F !important;
    }

    .gallery-main .bien-status.indisponible {
        background: #8A91A0 !important;
    }

    .bien-views {
        position: absolute;
        bottom: 16px;
        left: 16px;
        z-index: 5;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 500;
        color: #fff;
        background: rgba(0,0,0,0.6);
        border: 1px solid rgba(255,255,255,0.1);
    }

    .bien-type-on-image {
        position: absolute;
        bottom: 16px;
        right: 16px;
        z-index: 5;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        background: #B85C3A;
        border: 1px solid rgba(255,255,255,0.2);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    @keyframes pulseVedette {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.85; }
    }

    .badge-vedette-similar {
        position: absolute;
        top: 8px;
        left: 8px;
        z-index: 5;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: clamp(8px, 0.6vw, 9px);
        font-weight: 700;
        color: #fff;
        background: #D4AF37;
        display: flex;
        align-items: center;
        gap: 3px;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .badge-vedette-similar i {
        font-size: 8px;
        color: #fff;
    }

    .bien-vedette-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        color: #fff;
        background: #D4AF37;
        margin-left: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bien-vedette-tag i {
        font-size: 10px;
        color: #fff;
    }

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
        color: #B85C3A;
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
        background: #F0F2F5;
    }

    .gallery-main img {
        width: 100%;
        height: clamp(250px, 40vw, 400px);
        object-fit: cover;
        cursor: pointer;
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

    .thumbnail:hover { border-color: #B85C3A; }
    .thumbnail.active { border-color: #B85C3A; }

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
        background: #F5E6DF;
        color: #B85C3A;
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
        background: #F5E6DF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(18px, 1.8vw, 20px);
        font-weight: 700;
        color: #B85C3A;
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
        color: #B85C3A;
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
        color: #B85C3A;
    }

    /* ===== CTA ===== */
    .cta-card {
        background: #F5E6DF;
        border-color: rgba(184, 92, 58, 0.2);
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
        position: relative;
        background: #F0F2F5;
        display: flex;
        align-items: center;
        justify-content: center;
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
        z-index: 2;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: clamp(9px, 0.7vw, 10px);
        font-weight: 600;
        color: #fff;
        background: #B85C3A;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .similar-status {
        position: absolute;
        top: 8px;
        right: 8px;
        z-index: 2;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: clamp(9px, 0.7vw, 10px);
        font-weight: 600;
        color: #fff;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .similar-status.disponible { background: #2A9D8F !important; }
    .similar-status.indisponible { background: #8A91A0 !important; }

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
        color: #B85C3A;
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
        background: #B85C3A;
        color: #fff;
        border-color: #B85C3A;
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

        .badge-vedette-detail {
            font-size: 10px;
            padding: 4px 12px;
            top: 10px;
            left: 10px;
        }

        .badge-vedette-detail i {
            font-size: 10px;
        }

        .gallery-main .bien-status {
            font-size: 10px;
            padding: 4px 12px;
            top: 10px;
            right: 10px;
        }

        .bien-views {
            font-size: 10px;
            padding: 3px 10px;
            bottom: 10px;
            left: 10px;
        }

        .bien-type-on-image {
            font-size: 10px;
            padding: 3px 10px;
            bottom: 10px;
            right: 10px;
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

        .lightbox-nav {
            width: 28px;
            height: 28px;
            font-size: 12px;
        }

        .lightbox-prev {
            left: 4px;
        }

        .lightbox-next {
            right: 4px;
        }

        .badge-vedette-detail {
            font-size: 9px;
            padding: 3px 10px;
            top: 8px;
            left: 8px;
        }

        .badge-vedette-detail i {
            font-size: 9px;
        }

        .gallery-main .bien-status {
            font-size: 9px;
            padding: 3px 10px;
            top: 8px;
            right: 8px;
        }

        .bien-views {
            font-size: 9px;
            padding: 3px 8px;
            bottom: 8px;
            left: 8px;
        }

        .bien-type-on-image {
            font-size: 9px;
            padding: 3px 8px;
            bottom: 8px;
            right: 8px;
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

    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        .badge-vedette-detail {
            animation: none !important;
        }
        .lightbox.active {
            animation: none !important;
        }
    }
</style>

<script>
    // ============================================
    // LIGHTBOX - VERSION AVEC FLÈCHES
    // ============================================
    let lightboxImages = [];
    let currentLightboxIndex = 0;
    let isLightboxOpen = false;
    let touchStartX = 0;
    let touchEndX = 0;
    let totalImages = 0;

    // Initialisation immédiate avec les données PHP
    (function() {
        @php
            $imageUrls = [];
            foreach($images as $img) {
                $imageUrls[] = asset('storage/' . $img->fichier);
            }
        @endphp
        lightboxImages = {!! json_encode($imageUrls) !!};
        totalImages = lightboxImages.length;
    })();

    function openLightbox(index) {
        if (totalImages === 0) {
            return;
        }
        if (index < 0 || index >= totalImages) return;
        
        currentLightboxIndex = index;
        const lightbox = document.getElementById('lightbox');
        const image = document.getElementById('lightboxImage');
        const counter = document.getElementById('lightboxCounter');
        const prevBtn = document.getElementById('lightboxPrev');
        const nextBtn = document.getElementById('lightboxNext');
        
        if (!lightboxImages[currentLightboxIndex]) {
            return;
        }
        
        image.src = lightboxImages[currentLightboxIndex];
        image.alt = 'Photo du bien ' + (currentLightboxIndex + 1);
        
        if (totalImages > 1) {
            counter.textContent = (currentLightboxIndex + 1) + ' / ' + totalImages;
            counter.style.display = 'block';
            prevBtn.classList.toggle('hidden', currentLightboxIndex === 0);
            nextBtn.classList.toggle('hidden', currentLightboxIndex === totalImages - 1);
        } else {
            counter.style.display = 'none';
            prevBtn.classList.add('hidden');
            nextBtn.classList.add('hidden');
        }
        
        lightbox.classList.add('active');
        document.body.style.overflow = 'hidden';
        isLightboxOpen = true;
        
        preloadImages(currentLightboxIndex);
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('active');
        document.body.style.overflow = 'auto';
        isLightboxOpen = false;
    }

    function closeLightboxOutside(event) {
        if (event.target === event.currentTarget) {
            closeLightbox();
        }
    }

    function lightboxPrev() {
        if (currentLightboxIndex > 0) {
            openLightbox(currentLightboxIndex - 1);
        }
    }

    function lightboxNext() {
        if (currentLightboxIndex < totalImages - 1) {
            openLightbox(currentLightboxIndex + 1);
        }
    }

    function preloadImages(index) {
        if (index + 1 < totalImages && lightboxImages[index + 1]) {
            const img = new Image();
            img.src = lightboxImages[index + 1];
        }
        if (index - 1 >= 0 && lightboxImages[index - 1]) {
            const img = new Image();
            img.src = lightboxImages[index - 1];
        }
    }

    // ============================================
    // GESTION DU GLISSEMENT POUR LA LIGHTBOX
    // ============================================
    const lightboxContent = document.getElementById('lightboxContent');

    if (lightboxContent) {
        lightboxContent.addEventListener('touchstart', function(e) {
            if (e.touches.length === 1) {
                touchStartX = e.touches[0].clientX;
            }
        }, { passive: true });

        lightboxContent.addEventListener('touchmove', function(e) {
            if (e.touches.length === 1) {
                touchEndX = e.touches[0].clientX;
                if (Math.abs(touchEndX - touchStartX) > 10) {
                    e.preventDefault();
                }
            }
        }, { passive: false });

        lightboxContent.addEventListener('touchend', function(e) {
            if (touchStartX > 0 && touchEndX > 0) {
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 50) {
                    if (diff > 0) {
                        lightboxNext();
                    } else {
                        lightboxPrev();
                    }
                }
                touchStartX = 0;
                touchEndX = 0;
            }
        }, { passive: true });
    }

    // ============================================
    // CLAVIER
    // ============================================
    document.addEventListener('keydown', function(e) {
        if (!isLightboxOpen) return;
        
        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowRight') {
            lightboxNext();
        } else if (e.key === 'ArrowLeft') {
            lightboxPrev();
        }
    });

    // ============================================
    // CHANGER L'IMAGE PRINCIPALE (miniatures)
    // ============================================
    function changeMainImage(src, index) {
        const mainImage = document.getElementById('mainImage');
        const img = mainImage.querySelector('img');
        if (img) {
            img.src = src;
        } else {
            mainImage.innerHTML = `<img src="${src}" alt="Bien" loading="lazy">`;
        }
        
        document.querySelectorAll('.thumbnail').forEach(el => {
            el.classList.remove('active');
        });
        const thumbnails = document.querySelectorAll('.thumbnail');
        if (thumbnails[index]) {
            thumbnails[index].classList.add('active');
        }
    }

    // ============================================
    // LECTURE VIDÉO
    // ============================================
    function playVideo(src) {
        const mainImage = document.getElementById('mainImage');
        mainImage.innerHTML = `
            <video src="${src}" controls autoplay style="width:100%;height:100%;object-fit:cover;">
                Votre navigateur ne supporte pas la lecture de vidéos.
            </video>
        `;
        
        document.querySelectorAll('.thumbnail').forEach(el => {
            el.classList.remove('active');
        });
        if (event && event.target) {
            const thumb = event.target.closest('.thumbnail');
            if (thumb) thumb.classList.add('active');
        }
    }

    // ============================================
    // SHARE FUNCTIONS
    // ============================================
    function shareFacebook() {
        const url = encodeURIComponent(window.location.href);
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
    }

    function shareWhatsApp() {
        const url = encodeURIComponent(window.location.href);
        const text = encodeURIComponent("Découvrez ce bien immobilier sur DoyaImmo !");
        window.open(`https://wa.me/?text=${text}%20${url}`, '_blank', 'width=600,height=400');
    }

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
@endsection