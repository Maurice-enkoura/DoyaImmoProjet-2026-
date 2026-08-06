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

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:#FAFBFC;">
            <div>
                <h3 style="font-family:var(--display);font-size:18px;font-weight:700;margin:0;">{{ $bien->titre }}</h3>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">
                    <i class="fa-solid fa-location-dot"></i> {{ $bien->adresse }} · {{ $bien->quartier }}
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:12px;">
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
            @endphp
            @if($images->count() > 0)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-regular fa-image" style="color:var(--rust);"></i> Photos du bien
                    </h4>
                    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:8px;">
                        @foreach($images->take(6) as $index => $media)
                            @if($index === 0)
                                <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F7F9FC;cursor:pointer;border:1px solid var(--border);grid-column:span 2;grid-row:span 2;position:relative;" 
                                     onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                    <img src="{{ asset('storage/' . $media->fichier) }}" 
                                         alt="Photo du bien" 
                                         style="width:100%;height:100%;object-fit:cover;">
                                    <!-- Type de bien sur l'image -->
                                    <div style="position:absolute;bottom:12px;left:12px;padding:4px 14px;border-radius:999px;font-size:11px;font-weight:600;color:#fff;background:rgba(0,0,0,0.7);z-index:2;">
                                        {{ $bien->type_bien->label() }}
                                    </div>
                                </div>
                            @elseif($index < 6)
                                <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:#F7F9FC;cursor:pointer;border:1px solid var(--border);position:relative;" 
                                     onclick="openLightbox('{{ asset('storage/' . $media->fichier) }}')">
                                    <img src="{{ asset('storage/' . $media->fichier) }}" 
                                         alt="Photo du bien" 
                                         style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            @endif
                        @endforeach
                        
                        @if($images->count() > 6)
                            <div style="border-radius:10px;overflow:hidden;aspect-ratio:1;background:var(--ink);color:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;border:1px solid var(--border);position:relative;"
                                 onclick="openLightbox('{{ asset('storage/' . $images->skip(6)->first()->fichier) }}')">
                                <span style="font-size:24px;font-weight:700;">+{{ $images->count() - 6 }}</span>
                                <span style="font-size:11px;opacity:0.7;">photos</span>
                            </div>
                        @endif
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
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        @foreach($videos as $media)
                            <div style="border-radius:10px;overflow:hidden;width:240px;background:#000;border:1px solid var(--border);position:relative;">
                                <video src="{{ asset('storage/' . $media->fichier) }}" 
                                       style="width:100%;height:140px;object-fit:cover;display:block;"
                                       controls>
                                </video>
                                <div style="position:absolute;bottom:8px;left:8px;padding:3px 12px;border-radius:999px;font-size:10px;font-weight:600;color:#fff;background:rgba(0,0,0,0.7);z-index:2;">
                                    {{ $bien->type_bien->label() }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Informations du bien -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Type</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->type_bien->label() }}</span>
                </div>
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Contrat</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->type_contrat->label() }}</span>
                </div>
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Prix</span>
                    <span style="font-weight:700;font-size:13px;color:var(--rust);">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</span>
                </div>
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Surface</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->surface }} m²</span>
                </div>
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Chambres</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->nombre_chambres }}</span>
                </div>
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Salles de bain</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->nombre_salles_bain }}</span>
                </div>
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Parking</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->parking_disponible ? ' Disponible' : ' Non disponible' }}</span>
                </div>
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Meublé</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->est_meuble ? ' Oui' : ' Non' }}</span>
                </div>
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Vues</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->vues ?? 0 }}</span>
                </div>
                <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Publié le</span>
                    <span style="font-weight:500;font-size:13px;">{{ $bien->created_at->format('d/m/Y') }}</span>
                </div>
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
            <div style="display:flex;gap:12px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border);">
                <a href="{{ route('agence.biens.edit', $bien) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
                <form action="{{ route('agence.biens.activer', $bien) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn {{ $bien->statut ? 'btn-ghost' : 'btn-rust' }}">
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
    .status-disponible {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-indisponible {
        background: #FFEBEE;
        color: #C62828;
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
    }

    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
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
    }

    @media (max-width: 480px) {
        [style*="grid-template-columns: repeat(3,1fr)"] {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }
</style>
@endpush