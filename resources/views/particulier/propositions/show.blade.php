@extends('layouts.dashboard')

@section('title', 'Détail de l\'offre — DoyaImmo')
@section('page_title', 'Détail de l\'offre')
@section('page_sub', 'Informations complètes sur la proposition')

@section('content')
<div class="view active">
    <!-- Bouton retour -->
    <div style="margin-bottom:20px;">
        <a href="{{ route('particulier.propositions.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux offres
        </a>
    </div>

    <!-- Carte principale -->
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">

        <!-- En-tête -->
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:#FAFBFC;">
            <div>
                <h3 style="font-family:var(--display);font-size:18px;font-weight:700;margin:0;">
                    {{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}
                </h3>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">
                    Budget max : {{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }} F/mois
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                    <i class="fa-regular fa-calendar"></i> Reçue le {{ $proposition->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
            <span class="status-pill status-{{ $proposition->statut_value }}">
                <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                {{ $proposition->statut_label }}
            </span>
        </div>

        <!-- Corps -->
        <div style="padding:24px;">

            <!-- ✅ 1. Galerie photos (médias de la proposition) -->
            @php
                // Récupérer les médias de la proposition avec sécurité
                $propositionMedias = $proposition->medias ?? collect();
                $images = $propositionMedias->where('type_media', 'image');
                $videos = $propositionMedias->where('type_media', 'video');
                
                // Si la proposition n'a pas de médias, essayer de prendre ceux du bien
                if ($images->count() == 0 && $videos->count() == 0 && $proposition->bien) {
                    $bienMedias = $proposition->bien->medias ?? collect();
                    $images = $bienMedias->where('type_media', 'image');
                    $videos = $bienMedias->where('type_media', 'video');
                }
            @endphp

            @if(($images && $images->count() > 0) || ($videos && $videos->count() > 0))
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-regular fa-images" style="margin-right:8px;"></i>
                        Médias du bien
                        @if($images && $images->count() > 0)
                            <span style="font-size:12px;color:var(--muted);font-weight:400;">({{ $images->count() }} photos)</span>
                        @endif
                    </h4>
                    
                    <!-- Photos -->
                    @if($images && $images->count() > 0)
                        <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:8px;">
                            @foreach($images->take(6) as $index => $media)
                                @if($index === 0)
                                    <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F7F9FC;cursor:pointer;border:1px solid var(--border);grid-column:span 2;grid-row:span 2;" 
                                         onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                        <img src="{{ asset('storage/' . $media->fichier) }}" 
                                             alt="Photo du bien" 
                                             style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                @elseif($index < 6)
                                    <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F7F9FC;cursor:pointer;border:1px solid var(--border);" 
                                         onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                        <img src="{{ asset('storage/' . $media->fichier) }}" 
                                             alt="Photo du bien" 
                                             style="width:100%;height:100%;object-fit:cover;">
                                    </div>
                                @endif
                            @endforeach
                            
                            @if($images->count() > 6)
                                <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:var(--ink);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;border:1px solid var(--border);"
                                     onclick="openLightbox('{{ asset('storage/' . $images->skip(6)->first()->fichier) }}')">
                                    <span style="font-size:24px;font-weight:700;">+{{ $images->count() - 6 }}</span>
                                    <span style="font-size:11px;opacity:0.7;">photos</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Vidéos -->
                    @if($videos && $videos->count() > 0)
                        <div style="margin-top:12px;">
                            <h4 style="font-family:var(--display);font-size:13px;margin-bottom:10px;color:var(--text-soft);">
                                <i class="fa-regular fa-circle-play" style="margin-right:8px;"></i>Vidéos
                            </h4>
                            <div style="display:flex;gap:12px;flex-wrap:wrap;">
                                @foreach($videos->take(2) as $media)
                                    <div style="border-radius:10px;overflow:hidden;width:240px;background:#000;border:1px solid var(--border);">
                                        <video src="{{ asset('storage/' . $media->fichier) }}" 
                                               style="width:100%;height:140px;object-fit:cover;display:block;"
                                               controls>
                                        </video>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div style="margin-bottom:24px;padding:40px;background:#F7F9FC;border-radius:10px;text-align:center;color:var(--muted);border:1px dashed var(--border);">
                    <i class="fa-regular fa-image" style="font-size:32px;display:block;margin-bottom:8px;opacity:0.3;"></i>
                    <span>Aucun média disponible pour ce bien</span>
                </div>
            @endif

            <!-- 2. Agence -->
            <div style="display:flex;align-items:center;gap:16px;padding:16px 20px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);margin-bottom:24px;flex-wrap:wrap;">
                <div style="width:56px;height:56px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:var(--rust);flex-shrink:0;">
                    {{ strtoupper(substr($proposition->agence->nom_agence, 0, 1)) }}
                </div>
                <div style="flex:1;min-width:150px;">
                    <div style="font-weight:700;font-size:17px;">{{ $proposition->agence->nom_agence }}</div>
                    <div style="font-size:13px;color:var(--text-soft);">
                        <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                        {{ number_format($proposition->agence->note_moyenne, 1) }} / 5
                        <span style="color:var(--muted);">({{ $proposition->agence->evaluations->count() }} avis)</span>
                    </div>
                    <div style="font-size:13px;color:var(--muted);">
                        <i class="fa-solid fa-location-dot"></i> {{ $proposition->agence->adresse }}
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-weight:700;color:var(--rust);font-size:22px;">
                        {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                    </div>
                    <div style="font-size:12px;color:var(--muted);">Prix proposé</div>
                </div>
            </div>

            <!-- 3. Message -->
            @if($proposition->message)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                        <i class="fa-regular fa-message" style="margin-right:8px;"></i>Message de l'agence
                    </h4>
                    <div style="padding:14px 18px;background:#F7F9FC;border-radius:10px;font-size:14px;color:var(--text-soft);line-height:1.7;border:1px solid var(--border);">
                        {{ $proposition->message }}
                    </div>
                </div>
            @endif

            <!-- 4. Caractéristiques -->
            @if($proposition->bien)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-regular fa-list-check" style="margin-right:8px;"></i>Caractéristiques du bien
                    </h4>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0;border:1px solid var(--border);border-radius:10px;overflow:hidden;">
                        <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Titre</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->titre ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Type</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->type_bien->label() ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Contrat</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->type_contrat->label() ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Surface</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->surface ?? 'N/A' }} m²</span>
                        </div>
                        <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Chambres</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->nombre_chambres ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Salles de bain</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->nombre_salles_bain ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Parking</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->parking_disponible ? ' Disponible' : ' Non' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Meublé</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->est_meuble ? ' Oui' : ' Non' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#FAFBFC;grid-column:1/3;display:flex;justify-content:space-between;border-bottom:none;">
                            <span style="color:var(--muted);font-size:13px;">Adresse</span>
                            <span style="font-weight:500;font-size:13px;text-align:right;max-width:60%;">{{ $proposition->bien->adresse ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- 5. Description -->
            @if($proposition->bien && $proposition->bien->description)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                        <i class="fa-regular fa-file-lines" style="margin-right:8px;"></i>Description du bien
                    </h4>
                    <div style="padding:14px 18px;background:#F7F9FC;border-radius:10px;font-size:14px;color:var(--text-soft);line-height:1.7;border:1px solid var(--border);">
                        {{ $proposition->bien->description }}
                    </div>
                </div>
            @endif

            <!-- 6. Actions -->
            <div style="display:flex;gap:12px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border);">
                @if($proposition->statut_value === 'en_attente')
                    <form action="{{ route('particulier.propositions.selectionner', $proposition) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-rust">
                            <i class="fa-solid fa-check"></i> Choisir cette agence
                        </button>
                    </form>
                    <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-ghost">
                        <i class="fa-solid fa-calendar"></i> Planifier une visite
                    </a>
                @elseif($proposition->statut_value === 'acceptee')
                    <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-rust">
                        <i class="fa-solid fa-calendar-check"></i> Planifier la visite
                    </a>
                @endif
                <a href="{{ route('particulier.signalements.create-proposition', $proposition) }}" class="btn btn-ghost btn-sm" style="color:#C62828;border-color:#FFCDD2;margin-left:auto;">
                    <i class="fa-solid fa-flag"></i> Signaler
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.92);z-index:9999;cursor:pointer;align-items:center;justify-content:center;padding:20px;" onclick="closeLightbox()">
    <span style="position:absolute;top:20px;right:30px;color:#fff;font-size:40px;cursor:pointer;opacity:0.7;transition:opacity 0.2s;" onclick="closeLightbox()">&times;</span>
    <img id="lightboxImage" src="" alt="Agrandir" style="max-width:95%;max-height:90vh;object-fit:contain;">
</div>
@endsection

@push('scripts')
<script>
    let lightboxImages = [];
    let currentLightboxIndex = 0;

    function openLightbox(src) {
        const lightbox = document.getElementById('lightbox');
        const image = document.getElementById('lightboxImage');
        
        // Récupérer toutes les images
        @if(isset($images) && $images && $images->count() > 0)
            lightboxImages = [
                @foreach($images as $media)
                    '{{ asset('storage/' . $media->fichier) }}',
                @endforeach
            ];
        @endif
        
        // Trouver l'index de l'image cliquée
        currentLightboxIndex = lightboxImages.indexOf(src);
        if (currentLightboxIndex === -1) currentLightboxIndex = 0;
        
        image.src = src;
        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        const lightbox = document.getElementById('lightbox');
        lightbox.style.display = 'none';
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
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-acceptee {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-refusee {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-terminee {
        background: #E3F2FD;
        color: #0D47A1;
    }

    @media (max-width: 1024px) {
        [style*="grid-template-columns: repeat(6,1fr)"] {
            grid-template-columns: repeat(4, 1fr) !important;
        }
        [style*="grid-column: span 2; grid-row: span 2;"] {
            grid-column: span 2 !important;
            grid-row: span 2 !important;
        }
    }

    @media (max-width: 768px) {
        [style*="grid-template-columns: repeat(6,1fr)"] {
            grid-template-columns: repeat(3, 1fr) !important;
        }
        [style*="grid-column: span 2; grid-row: span 2;"] {
            grid-column: span 1 !important;
            grid-row: span 1 !important;
        }
        [style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        [style*="grid-column: 1/3"] {
            grid-column: 1 !important;
        }
    }

    @media (max-width: 480px) {
        [style*="grid-template-columns: repeat(3,1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
</style>
@endpush