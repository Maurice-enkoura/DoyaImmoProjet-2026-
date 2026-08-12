@extends('layouts.dashboard-agence')

@section('title', 'Détail de l\'offre — DoyaImmo')
@section('page_title', 'Détail de l\'offre')
@section('page_sub', 'Informations complètes sur la proposition')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('agence.propositions.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux offres
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;{{ $proposition->bien && $proposition->bien->est_vedette ? 'border-color:#F5A623;border-width:2px;' : '' }}">
        <!-- En-tête -->
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:#FAFBFC;">
            <div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    <h3 style="font-family:var(--display);font-size:18px;font-weight:700;margin:0;">
                        {{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}
                    </h3>
                    @if($proposition->bien && $proposition->bien->est_vedette)
                        <span class="vedette-badge-header">
                            <i class="fa-solid fa-star"></i> Vedette
                        </span>
                    @endif
                </div>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">
                    Budget max : {{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }} F/mois
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                    <i class="fa-regular fa-calendar"></i> Envoyée le {{ $proposition->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
            <span class="status-pill status-{{ $proposition->statut->value }}">
                <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                {{ $proposition->statut->label() }}
            </span>
        </div>

        <!-- Corps -->
        <div style="padding:24px;">

            <!-- Client -->
            <div style="display:flex;align-items:center;gap:16px;padding:16px 20px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);margin-bottom:24px;flex-wrap:wrap;">
                <div style="width:56px;height:56px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;color:var(--rust);flex-shrink:0;">
                    {{ strtoupper(substr($proposition->particulier->user->prenom ?? 'C', 0, 1)) }}
                </div>
                <div style="flex:1;min-width:150px;">
                    <div style="font-weight:700;font-size:17px;">
                        {{ $proposition->particulier->user->prenom ?? '' }} {{ $proposition->particulier->user->nom ?? '' }}
                    </div>
                    <div style="font-size:13px;color:var(--muted);">
                        <i class="fa-solid fa-envelope"></i> {{ $proposition->particulier->user->email ?? '' }}
                    </div>
                    <div style="font-size:13px;color:var(--muted);">
                        <i class="fa-solid fa-phone"></i> {{ $proposition->particulier->user->telephone ?? 'Non renseigné' }}
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

            <!-- Message -->
            @if($proposition->message)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                        <i class="fa-regular fa-message" style="margin-right:8px;"></i>Votre message
                    </h4>
                    <div style="padding:14px 18px;background:#F7F9FC;border-radius:10px;font-size:14px;color:var(--text-soft);line-height:1.7;border:1px solid var(--border);">
                        {{ $proposition->message }}
                    </div>
                </div>
            @endif

            <!-- === PHOTOS DE LA PROPOSITION === -->
            @php
                $images = $proposition->medias->where('type_media', 'image');
            @endphp
            @if($images->count() > 0)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-regular fa-image" style="color:var(--rust);"></i> Photos ajoutées à la proposition
                    </h4>
                    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:8px;">
                        @foreach($images->take(6) as $index => $media)
                            @if($index === 0)
                                <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F7F9FC;cursor:pointer;border:1px solid var(--border);grid-column:span 2;grid-row:span 2;" 
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
                </div>
            @endif

            <!-- === VIDÉOS DE LA PROPOSITION === -->
            @php
                $videos = $proposition->medias->where('type_media', 'video');
            @endphp
            @if($videos->count() > 0)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Vidéos ajoutées à la proposition
                    </h4>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        @foreach($videos as $media)
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

            <!-- === PHOTOS DU BIEN (de la plateforme) === -->
            @php
                $bienImages = $proposition->bien->medias->where('type_media', 'image');
            @endphp
            @if($bienImages->count() > 0)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-regular fa-image" style="color:var(--rust);"></i> Photos du bien sur la plateforme
                    </h4>
                    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:8px;">
                        @foreach($bienImages->take(6) as $index => $media)
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
                                <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F7F9FC;cursor:pointer;border:1px solid var(--border);" 
                                     onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                    <img src="{{ asset('storage/' . $media->fichier) }}" 
                                         alt="Photo du bien" 
                                         style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            @endif
                        @endforeach
                        
                        @if($bienImages->count() > 6)
                            <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:var(--ink);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;border:1px solid var(--border);"
                                 onclick="openLightbox('{{ asset('storage/' . $bienImages->skip(6)->first()->fichier) }}')">
                                <span style="font-size:24px;font-weight:700;">+{{ $bienImages->count() - 6 }}</span>
                                <span style="font-size:11px;opacity:0.7;">photos</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- === VIDÉOS DU BIEN (de la plateforme) === -->
            @php
                $bienVideos = $proposition->bien->medias->where('type_media', 'video');
            @endphp
            @if($bienVideos->count() > 0)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Vidéos du bien sur la plateforme
                    </h4>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        @foreach($bienVideos as $media)
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

            <!-- Bien proposé -->
            <div style="margin-bottom:24px;">
                <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-regular fa-building" style="margin-right:8px;"></i>Bien proposé
                    @if($proposition->bien && $proposition->bien->est_vedette)
                        <span class="vedette-badge-small">
                            <i class="fa-solid fa-star"></i> Vedette
                        </span>
                    @endif
                </h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;border:1px solid var(--border);border-radius:10px;overflow:hidden;">
                    <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Titre</span>
                        <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->titre }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Type</span>
                        <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->type_bien->label() }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Contrat</span>
                        <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->type_contrat->label() }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Surface</span>
                        <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->surface }} m²</span>
                    </div>
                    <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Chambres</span>
                        <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->nombre_chambres }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Salles de bain</span>
                        <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->nombre_salles_bain }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Parking</span>
                        <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->parking_disponible ? ' Disponible' : '❌ Non' }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Meublé</span>
                        <span style="font-weight:500;font-size:13px;">{{ $proposition->bien->est_meuble ? ' Oui' : ' Non' }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#FAFBFC;grid-column:1/3;display:flex;justify-content:space-between;border-bottom:none;">
                        <span style="color:var(--muted);font-size:13px;">Adresse</span>
                        <span style="font-weight:500;font-size:13px;text-align:right;max-width:60%;">{{ $proposition->bien->adresse }}</span>
                    </div>
                </div>
            </div>

            <!-- Description -->
            @if($proposition->bien->description)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                        <i class="fa-regular fa-file-lines" style="margin-right:8px;"></i>Description du bien
                    </h4>
                    <div style="padding:14px 18px;background:#F7F9FC;border-radius:10px;font-size:14px;color:var(--text-soft);line-height:1.7;border:1px solid var(--border);">
                        {{ $proposition->bien->description }}
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div style="display:flex;gap:12px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border);">
                @if($proposition->statut->value === 'en_attente')
                    <form action="{{ route('agence.propositions.annuler', $proposition) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-ghost" style="color:#C62828;border-color:#FFCDD2;" onclick="return confirm('Annuler cette proposition ?')">
                            <i class="fa-solid fa-xmark"></i> Annuler
                        </button>
                    </form>
                @endif
                @if($proposition->bien && $proposition->bien->est_vedette)
                    <span style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;background:#FFF8E1;color:#E65100;border-radius:10px;font-size:13px;font-weight:600;">
                        <i class="fa-solid fa-star" style="color:#F5A623;"></i> Bien en vedette
                    </span>
                @endif
                <a href="{{ route('agence.propositions.index') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;">
                    <i class="fa-solid fa-list"></i> Toutes mes offres
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Lightbox -->
<div id="lightbox" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.92);z-index:9999;cursor:pointer;align-items:center;justify-content:center;padding:20px;" onclick="closeLightbox()">
    <span style="position:absolute;top:20px;right:30px;color:#fff;font-size:40px;cursor:pointer;opacity:0.7;transition:opacity 0.2s;font-family:sans-serif;" onclick="closeLightbox()">&times;</span>
    <img id="lightboxImage" src="" alt="Agrandir" style="max-width:95%;max-height:90vh;object-fit:contain;">
</div>

<script>
    function openLightbox(src) {
        const lightbox = document.getElementById('lightbox');
        const image = document.getElementById('lightboxImage');
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
        .vedette-badge-header {
            font-size: 10px;
            padding: 3px 10px;
        }
        .vedette-badge-small {
            font-size: 9px;
            padding: 1px 8px;
        }
    }

    @media (max-width: 480px) {
        [style*="grid-template-columns: repeat(3,1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
</style>
@endpush