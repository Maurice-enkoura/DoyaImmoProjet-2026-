@extends('layouts.dashboard-agence')

@section('title', 'Détail du bien — DoyaImmo')
@section('page_title', 'Détail du bien')
@section('page_sub', 'Informations complètes sur votre bien')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes biens
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;{{ $bien->est_vedette ? 'border-color:#F5A623;border-width:2px;' : '' }}">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:#FAFBFC;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <h3 style="font-family:var(--display);font-size:18px;font-weight:700;margin:0;">{{ $bien->titre }}</h3>
                @if($bien->est_vedette)
                    <span class="vedette-badge-header">
                        <i class="fa-solid fa-star"></i> Vedette
                    </span>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <span style="font-size:13px;color:var(--muted);">
                    <i class="fa-regular fa-eye"></i> {{ $bien->vues ?? 0 }} vues
                </span>
                <span class="status-pill status-{{ $bien->statut ? 'disponible' : 'indisponible' }}">
                    {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                </span>
            </div>
        </div>

        <div style="padding:24px;">

            <!-- Galerie d'images -->
            @php
                $images = $bien->medias->where('type_media', 'image');
                $imagesArray = $images->values()->toArray();
            @endphp
            @if($images->count() > 0)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-regular fa-image" style="color:var(--rust);"></i> Photos du bien 
                        <span style="font-size:12px;color:var(--muted);font-weight:400;">({{ $images->count() }} photos)</span>
                    </h4>
                    
                    <!-- Grille d'images -->
                    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(150px, 1fr));gap:10px;">
                        @foreach($images as $index => $media)
                            <div style="border-radius:10px;overflow:hidden;border:1px solid var(--border);background:#F7F9FC;aspect-ratio:1/1;cursor:pointer;position:relative;" onclick="openLightbox({{ $index }})">
                                <img src="{{ asset('storage/' . $media->fichier) }}" 
                                     alt="Photo du bien" 
                                     loading="lazy"
                                     style="width:100%;height:100%;object-fit:cover;display:block;transition:transform 0.3s;">
                                <div style="position:absolute;bottom:8px;left:8px;padding:2px 10px;border-radius:999px;font-size:10px;font-weight:600;color:#fff;background:rgba(0,0,0,0.6);">
                                    {{ $bien->type_bien->label() }}
                                </div>
                                @if($bien->est_vedette)
                                    <div style="position:absolute;top:8px;left:8px;padding:2px 10px;border-radius:999px;font-size:10px;font-weight:600;color:#fff;background:#F5A623;display:flex;align-items:center;gap:4px;">
                                        <i class="fa-solid fa-star" style="font-size:9px;"></i> Vedette
                                    </div>
                                @endif
                                <div style="position:absolute;bottom:8px;right:8px;padding:2px 8px;border-radius:999px;font-size:10px;font-weight:600;color:#fff;background:rgba(0,0,0,0.5);">
                                    {{ $index + 1 }}/{{ $images->count() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div style="margin-bottom:24px;padding:30px;background:#F7F9FC;border-radius:10px;text-align:center;color:var(--muted);border:1px dashed var(--border);">
                    <i class="fa-regular fa-image" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                    <span>Aucune photo disponible pour ce bien</span>
                </div>
            @endif

            <!-- Vidéos -->
            @php
                $videos = $bien->medias->where('type_media', 'video');
            @endphp
            @if($videos->count() > 0)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Vidéos du bien
                    </h4>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:10px;">
                        @foreach($videos as $media)
                            <div style="border-radius:10px;overflow:hidden;background:#000;border:1px solid var(--border);position:relative;aspect-ratio:16/9;">
                                <video src="{{ asset('storage/' . $media->fichier) }}" 
                                       style="width:100%;height:100%;object-fit:cover;display:block;"
                                       controls>
                                </video>
                                <div style="position:absolute;bottom:8px;left:8px;padding:2px 10px;border-radius:999px;font-size:10px;font-weight:600;color:#fff;background:rgba(0,0,0,0.7);z-index:2;">
                                    {{ $bien->type_bien->label() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Informations du bien -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:24px;">
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Type</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->type_bien->label() }}</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Contrat</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->type_contrat->label() }}</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Prix</span>
                    <span style="font-weight:700;font-size:13px;color:var(--rust);">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Surface</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->surface }} m²</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Chambres</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->nombre_chambres }}</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Salles de bain</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->nombre_salles_bain }}</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Parking</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->parking_disponible ? ' Disponible' : ' Non disponible' }}</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Meublé</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->est_meuble ? ' Oui' : ' Non' }}</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Vues</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->vues ?? 0 }}</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Publié le</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->created_at->format('d/m/Y') }}</span>
                </div>
                @if($bien->est_vedette)
                    <div style="padding:10px 14px;background:#FFF8E1;border-radius:8px;display:flex;justify-content:space-between;grid-column:span 2;">
                        <span style="color:#E65100;font-size:13px;font-weight:600;">
                            <i class="fa-solid fa-star" style="color:#F5A623;"></i> En vedette
                        </span>
                        <span style="font-weight:500;font-size:13px;color:#E65100;">
                            Jusqu'au {{ $bien->vedette_fin ? $bien->vedette_fin->format('d/m/Y') : 'Illimité' }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Description -->
            @if($bien->description)
                <div style="margin-bottom:20px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">Description</h4>
                    <div style="padding:14px 18px;background:#F7F9FC;border-radius:10px;font-size:14px;color:var(--text-soft);line-height:1.7;border:1px solid var(--border);">
                        {{ $bien->description }}
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div style="display:flex;gap:10px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border);">
                <!-- ✅ CORRIGÉ : Utilisation du slug -->
                <a href="{{ route('agence.biens.edit', $bien->slug) }}" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
                <!-- ✅ CORRIGÉ : Utilisation du slug -->
                <form action="{{ route('agence.biens.activer', $bien->slug) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm {{ $bien->statut ? 'btn-ghost' : 'btn-rust' }}">
                        <i class="fa-solid {{ $bien->statut ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                        {{ $bien->statut ? 'Masquer' : 'Publier' }}
                    </button>
                </form>
                <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;">
                    <i class="fa-solid fa-list"></i> Tous mes biens
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ==================== LIGHTBOX AVEC NAVIGATION ==================== -->
<div id="lightbox" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.95);z-index:9999;padding:20px;align-items:center;justify-content:center;" onclick="closeLightbox(event)">
    <span style="position:absolute;top:20px;right:30px;color:#fff;font-size:40px;cursor:pointer;opacity:0.8;transition:opacity 0.3s;font-family:sans-serif;z-index:10;" onclick="closeLightbox(event)">&times;</span>
    
    <!-- Flèche gauche -->
    <button id="lightboxPrev" style="position:absolute;left:20px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.3);color:#fff;font-size:28px;padding:16px 20px;border-radius:50%;cursor:pointer;transition:all 0.3s;z-index:10;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);" onclick="event.stopPropagation(); navigateLightbox(-1)">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    
    <!-- Image -->
    <img id="lightboxImage" src="" alt="Agrandir" style="max-width:90%;max-height:85vh;object-fit:contain;cursor:default;border-radius:8px;box-shadow:0 8px 32px rgba(0,0,0,0.5);">
    
    <!-- Flèche droite -->
    <button id="lightboxNext" style="position:absolute;right:20px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.3);color:#fff;font-size:28px;padding:16px 20px;border-radius:50%;cursor:pointer;transition:all 0.3s;z-index:10;display:flex;align-items:center;justify-content:center;backdrop-filter:blur(4px);" onclick="event.stopPropagation(); navigateLightbox(1)">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    <!-- Compteur -->
    <div id="lightboxCounter" style="position:absolute;bottom:30px;left:50%;transform:translateX(-50%);color:#fff;font-size:14px;font-weight:500;background:rgba(0,0,0,0.6);padding:8px 20px;border-radius:999px;z-index:10;backdrop-filter:blur(4px);">
        1 / 1
    </div>
</div>

<script>
    // ============================================
    // LIGHTBOX AVEC NAVIGATION
    // ============================================
    let currentLightboxIndex = 0;
    let lightboxImages = [];

    @if($images->count() > 0)
        lightboxImages = [
            @foreach($images as $media)
                '{{ asset('storage/' . $media->fichier) }}',
            @endforeach
        ];
    @endif

    function openLightbox(index) {
        if (lightboxImages.length === 0) return;
        
        currentLightboxIndex = index;
        const lightbox = document.getElementById('lightbox');
        const image = document.getElementById('lightboxImage');
        const prevBtn = document.getElementById('lightboxPrev');
        const nextBtn = document.getElementById('lightboxNext');
        const counter = document.getElementById('lightboxCounter');
        
        // Mettre à jour l'image
        image.src = lightboxImages[currentLightboxIndex];
        
        // Mettre à jour le compteur
        counter.textContent = (currentLightboxIndex + 1) + ' / ' + lightboxImages.length;
        
        // Afficher/masquer les flèches
        prevBtn.style.display = currentLightboxIndex === 0 ? 'none' : 'flex';
        nextBtn.style.display = currentLightboxIndex === lightboxImages.length - 1 ? 'none' : 'flex';
        
        // Afficher la lightbox
        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox(event) {
        // Fermer uniquement si on clique sur le fond ou sur la croix
        if (event) {
            const target = event.target;
            if (target.id !== 'lightbox' && target.id !== 'lightboxImage' && 
                target.tagName !== 'BUTTON' && !target.closest('button')) {
                // Si on clique sur l'image, ne pas fermer
                if (target.id === 'lightboxImage') return;
            }
            if (target.id !== 'lightbox' && target.id !== 'lightboxImage' && 
                target.tagName !== 'BUTTON' && !target.closest('button')) {
                // Si on clique sur le fond ou la croix
                if (target.id === 'lightbox' || target.tagName === 'SPAN') {
                    // Fermer
                } else {
                    return;
                }
            }
        }
        
        const lightbox = document.getElementById('lightbox');
        lightbox.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function navigateLightbox(direction) {
        const newIndex = currentLightboxIndex + direction;
        
        if (newIndex < 0 || newIndex >= lightboxImages.length) return;
        
        currentLightboxIndex = newIndex;
        const image = document.getElementById('lightboxImage');
        const prevBtn = document.getElementById('lightboxPrev');
        const nextBtn = document.getElementById('lightboxNext');
        const counter = document.getElementById('lightboxCounter');
        
        // Mettre à jour l'image avec animation
        image.style.opacity = '0.5';
        setTimeout(() => {
            image.src = lightboxImages[currentLightboxIndex];
            image.style.opacity = '1';
        }, 150);
        
        // Mettre à jour le compteur
        counter.textContent = (currentLightboxIndex + 1) + ' / ' + lightboxImages.length;
        
        // Afficher/masquer les flèches
        prevBtn.style.display = currentLightboxIndex === 0 ? 'none' : 'flex';
        nextBtn.style.display = currentLightboxIndex === lightboxImages.length - 1 ? 'none' : 'flex';
    }

    // ============================================
    // NAVIGATION AU CLAVIER
    // ============================================
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.style.display === 'flex') {
            if (e.key === 'Escape') {
                closeLightbox(e);
            }
            if (e.key === 'ArrowRight') {
                e.preventDefault();
                navigateLightbox(1);
            }
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                navigateLightbox(-1);
            }
        }
    });

    // ============================================
    // TOUCH POUR LA LIGHTBOX
    // ============================================
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.style.display === 'flex') {
            touchStartX = e.changedTouches[0].screenX;
        }
    });

    document.addEventListener('touchend', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.style.display === 'flex') {
            touchEndX = e.changedTouches[0].screenX;
            const diff = touchStartX - touchEndX;
            if (Math.abs(diff) > 50) {
                if (diff > 0) {
                    navigateLightbox(1);
                } else {
                    navigateLightbox(-1);
                }
            }
        }
    });

    // ============================================
    // PREVENT SCROLL ON LIGHTBOX
    // ============================================
    document.addEventListener('wheel', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (lightbox.style.display === 'flex') {
            e.preventDefault();
        }
    }, { passive: false });
</script>
@endsection

@push('styles')
<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-disponible {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-indisponible {
        background: #FFEBEE;
        color: #C62828;
    }

    .vedette-badge-header {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        background: #F5A623;
        box-shadow: 0 2px 8px rgba(245, 166, 35, 0.3);
    }

    .vedette-badge-header i {
        font-size: 10px;
    }

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

    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }

    .btn-ghost:hover {
        background: var(--border);
    }

    .btn-rust {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
        border-color: #9A4523;
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }

    /* Lightbox buttons hover */
    #lightboxPrev:hover,
    #lightboxNext:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-50%) scale(1.1);
    }

    #lightboxPrev:active,
    #lightboxNext:active {
        transform: translateY(-50%) scale(0.95);
    }

    /* Responsive */
    @media (max-width: 768px) {
        [style*="grid-template-columns:1fr 1fr;gap:10px;"] {
            grid-template-columns: 1fr !important;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fill, minmax(150px, 1fr));gap:10px;"] {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)) !important;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:10px;"] {
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)) !important;
        }
        #lightboxPrev,
        #lightboxNext {
            font-size: 20px !important;
            padding: 12px 16px !important;
        }
        #lightboxPrev {
            left: 10px !important;
        }
        #lightboxNext {
            right: 10px !important;
        }
    }

    @media (max-width: 480px) {
        [style*="display:grid;grid-template-columns:repeat(auto-fill, minmax(150px, 1fr));gap:10px;"] {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)) !important;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:10px;"] {
            grid-template-columns: 1fr !important;
        }
        .vedette-badge-header {
            font-size: 10px;
            padding: 3px 10px;
        }
        #lightboxPrev,
        #lightboxNext {
            font-size: 16px !important;
            padding: 10px 14px !important;
        }
        #lightboxPrev {
            left: 6px !important;
        }
        #lightboxNext {
            right: 6px !important;
        }
        #lightboxCounter {
            font-size: 12px !important;
            padding: 6px 16px !important;
        }
    }
</style>
@endpush