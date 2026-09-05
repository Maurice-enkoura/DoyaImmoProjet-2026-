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

    @if(session('success'))
        <div style="padding:12px 16px;background:#E8F5E9;border-radius:10px;border:1px solid #C8E6C9;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i>
            <span style="color:#1E7A47;font-size:13px;">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="padding:12px 16px;background:#FFEBEE;border-radius:10px;border:1px solid #FFCDD2;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-exclamation-circle" style="color:#C62828;"></i>
            <span style="color:#C62828;font-size:13px;">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Carte principale -->
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;{{ $proposition->bien && $proposition->bien->est_vedette ? 'border-color:#F5A623;border-width:2px;' : '' }}">

        <!-- En-tête -->
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:#FAFBFC;">
            <div>
                <h3 style="font-family:var(--display);font-size:18px;font-weight:700;margin:0;">
                    {{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}
                </h3>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">
                    Budget max : {{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }}
                    @if($proposition->demande->type_operation->value === 'location')
                        F/mois
                    @else
                        F
                    @endif
                    @if($proposition->bien && $proposition->bien->est_vedette)
                        <span style="margin-left:8px;color:#F5A623;font-weight:600;">
                            <i class="fa-solid fa-star"></i> Bien en vedette
                        </span>
                    @endif
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                    <i class="fa-regular fa-calendar"></i> Reçue le {{ $proposition->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                @if($proposition->score_matching)
                    <span style="font-size:12px;color:var(--muted);">
                        Score de compatibilité : 
                        <span style="font-weight:700;color:{{ $proposition->score_matching >= 80 ? '#1E7A47' : ($proposition->score_matching >= 60 ? '#E65100' : '#C62828') }};">
                            {{ $proposition->score_matching }}%
                        </span>
                    </span>
                @endif
                <span class="status-pill status-{{ $proposition->statut_value }}">
                    <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                    {{ $proposition->statut_label }}
                </span>
            </div>
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
                                    <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F7F9FC;cursor:pointer;border:1px solid var(--border);grid-column:span 2;grid-row:span 2;position:relative;" 
                                         onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                        <img src="{{ asset('storage/' . $media->fichier) }}" 
                                             alt="Photo du bien" 
                                             style="width:100%;height:100%;object-fit:cover;">
                                        @if($proposition->bien && $proposition->bien->est_vedette)
                                            <div style="position:absolute;top:8px;left:8px;padding:2px 10px;border-radius:999px;font-size:9px;font-weight:600;color:#fff;background:#F5A623;z-index:3;display:flex;align-items:center;gap:3px;">
                                                <i class="fa-solid fa-star"></i> Vedette
                                            </div>
                                        @endif
                                    </div>
                                @elseif($index < 6)
                                    <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F7F9FC;cursor:pointer;border:1px solid var(--border);position:relative;" 
                                         onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                        <img src="{{ asset('storage/' . $media->fichier) }}" 
                                             alt="Photo du bien" 
                                             style="width:100%;height:100%;object-fit:cover;">
                                        @if($proposition->bien && $proposition->bien->est_vedette)
                                            <div style="position:absolute;top:8px;left:8px;padding:2px 10px;border-radius:999px;font-size:9px;font-weight:600;color:#fff;background:#F5A623;z-index:3;display:flex;align-items:center;gap:3px;">
                                                <i class="fa-solid fa-star"></i> Vedette
                                            </div>
                                        @endif
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
                                    <div style="border-radius:10px;overflow:hidden;width:240px;background:#000;border:1px solid var(--border);position:relative;">
                                        <video src="{{ asset('storage/' . $media->fichier) }}" 
                                               style="width:100%;height:140px;object-fit:cover;display:block;"
                                               controls>
                                        </video>
                                        @if($proposition->bien && $proposition->bien->est_vedette)
                                            <div style="position:absolute;top:8px;left:8px;padding:2px 10px;border-radius:999px;font-size:9px;font-weight:600;color:#fff;background:#F5A623;z-index:3;display:flex;align-items:center;gap:3px;">
                                                <i class="fa-solid fa-star"></i> Vedette
                                            </div>
                                        @endif
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
                    <div style="font-size:13px;color:var(--muted);margin-top:4px;display:flex;gap:12px;flex-wrap:wrap;">
                        @if($proposition->agence->user->telephone)
                            <span>
                                <i class="fa-solid fa-phone"></i> 
                                <a href="tel:{{ $proposition->agence->user->telephone }}" style="color:var(--text-soft);text-decoration:none;">
                                    {{ $proposition->agence->user->telephone }}
                                </a>
                            </span>
                        @endif
                        @if($proposition->agence->user->email)
                            <span>
                                <i class="fa-solid fa-envelope"></i> 
                                <a href="mailto:{{ $proposition->agence->user->email }}" style="color:var(--text-soft);text-decoration:none;">
                                    {{ $proposition->agence->user->email }}
                                </a>
                            </span>
                        @endif
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-weight:700;color:var(--rust);font-size:22px;">
                        {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                    </div>
                    <div style="font-size:12px;color:var(--muted);">Prix proposé</div>
                    @if($proposition->bien && $proposition->bien->est_vedette)
                        <div style="font-size:11px;color:#F5A623;font-weight:600;margin-top:4px;">
                            <i class="fa-solid fa-star"></i> Bien en vedette
                        </div>
                    @endif
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
                        @if($proposition->bien && $proposition->bien->est_vedette)
                            <span class="vedette-badge-small">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif
                    </h4>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;">
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Titre</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->titre ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Type</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->type_bien->label() ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Contrat</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->type_contrat->label() ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Surface</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->surface ?? 'N/A' }} m²</span>
                        </div>
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Chambres</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->nombre_chambres ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Salles de bain</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->nombre_salles_bain ?? 'N/A' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Parking</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->parking_disponible ? ' Disponible' : ' Non' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Meublé</span>
                            <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->est_meuble ? ' Oui' : ' Non' }}</span>
                        </div>
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;grid-column:1/3;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Adresse</span>
                            <span style="font-weight:500;font-size:13px;text-align:right;max-width:60%;">{{ $proposition->bien->adresse ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ✅ Équipements du bien -->
            @php
                $equipementsBien = $proposition->bien->equipements ?? [];
            @endphp
            @if(count($equipementsBien) > 0)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                        <i class="fa-solid fa-cogs" style="margin-right:8px;"></i>Équipements du bien
                    </h4>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;padding:12px 14px;background:#F7F9FC;border-radius:8px;border:1px solid var(--border);">
                        @foreach($equipementsBien as $equipement)
                            <span class="meta-pill" style="background:#E3F2FD;color:#0D47A1;border:1px solid #BBDEFB;">
                                <i class="fa-solid fa-check-circle" style="color:#0D47A1;"></i> {{ $equipement }}
                            </span>
                        @endforeach
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
                        <button type="submit" class="btn btn-rust" onclick="return confirm(' Sélectionner cette offre ? Cela clôturera les autres offres en attente.')">
                            <i class="fa-solid fa-check"></i> Choisir cette agence
                        </button>
                    </form>
                    <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-ghost">
                        <i class="fa-solid fa-calendar"></i> Planifier une visite
                    </a>
                    <form action="{{ route('particulier.propositions.refuser', $proposition) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-ghost" style="color:#C62828;border-color:#FFCDD2;" onclick="return confirm(' Refuser cette offre ?')">
                            <i class="fa-solid fa-times"></i> Refuser
                        </button>
                    </form>
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

<script>
    let lightboxImages = [];
    let currentLightboxIndex = 0;

    function openLightbox(src) {
        const lightbox = document.getElementById('lightbox');
        const image = document.getElementById('lightboxImage');
        
        @if(isset($images) && $images && $images->count() > 0)
            lightboxImages = [
                @foreach($images as $media)
                    '{{ asset('storage/' . $media->fichier) }}',
                @endforeach
            ];
        @endif
        
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
@endsection

@push('styles')
<style>
    /* ===================== STATUS PILL ===================== */
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

    /* ===================== VEDETTE BADGE ===================== */
    .vedette-badge-small {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        background: #F5A623;
        margin-left: 8px;
        box-shadow: 0 2px 6px rgba(245, 166, 35, 0.25);
    }

    .vedette-badge-small i {
        font-size: 9px;
    }

    /* ===================== META PILL ===================== */
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 11px;
        color: var(--text-soft);
    }

    /* ===================== BOUTONS ===================== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
    }
    .btn-rust {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }
    .btn-rust:hover {
        background: #9A4523;
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
        padding: 6px 14px;
        font-size: 12px;
    }

    /* ===================== RESPONSIVE ===================== */
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
        [style*="grid-template-columns: 1fr 1fr;gap:4px;"] {
            grid-template-columns: 1fr !important;
        }
        [style*="grid-column: 1/3"] {
            grid-column: 1 !important;
        }
        [style*="max-width:60%"] {
            max-width: 100% !important;
            text-align: left !important;
            margin-top: 4px;
        }
        [style*="display:flex;align-items:center;gap:16px;"] {
            flex-direction: column;
            align-items: center !important;
            text-align: center;
        }
        [style*="text-align:right;"] {
            text-align: center !important;
        }
        [style*="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;"] {
            flex-direction: column;
            align-items: stretch !important;
        }
        [style*="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;"] .btn {
            justify-content: center !important;
        }
        [style*="width:240px"] {
            width: 100% !important;
        }
    }

    @media (max-width: 480px) {
        [style*="grid-template-columns: repeat(3,1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .status-pill {
            font-size: 11px;
            padding: 3px 10px;
        }
        .btn-sm {
            font-size: 11px;
            padding: 4px 10px;
        }
        .meta-pill {
            font-size: 10px;
            padding: 1px 8px;
        }
    }
</style>
@endpush