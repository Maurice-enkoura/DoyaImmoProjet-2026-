@extends('layouts.dashboard-agence')

@section('title', 'Faire une offre — DoyaImmo')
@section('page_title', 'Faire une offre')
@section('page_sub', 'Proposez un bien à un client')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('agence.demandes.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux besoins
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
        <!-- Informations du besoin -->
        <div style="padding:16px;background:#F7F9FC;border-radius:10px;margin-bottom:24px;border:1px solid var(--border);">
            <h3 style="font-family:var(--display);font-size:16px;margin-bottom:6px;">
                {{ $demande->type_bien->label() }} — {{ $demande->zone_recherchee }}
            </h3>
            <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:13px;color:var(--muted);">
                <span><i class="fa-solid fa-money-bill"></i> Budget: {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois</span>
                <span><i class="fa-solid fa-house"></i> {{ $demande->type_operation->label() }}</span>
                <span><i class="fa-regular fa-calendar"></i> Publié {{ $demande->created_at->diffForHumans() }}</span>
            </div>
            <p style="font-size:13px;color:var(--text-soft);margin-top:8px;">{{ $demande->description }}</p>
        </div>

        <form method="POST" action="{{ route('agence.propositions.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="demande_id" value="{{ $demande->id }}">

            <!-- Sélection du bien -->
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    Sélectionnez un bien à proposer <span style="color:#C62828;">*</span>
                </label>
                <select name="bien_id" class="form-control @error('bien_id') is-invalid @enderror" 
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;background:#fff;" required>
                    <option value="">Choisissez un bien...</option>
                    @foreach($biens as $bien)
                        <option value="{{ $bien->id }}" {{ old('bien_id') == $bien->id ? 'selected' : '' }}>
                            {{ $bien->titre }} — {{ number_format($bien->prix, 0, ',', ' ') }} FCFA ({{ $bien->surface }} m²)
                        </option>
                    @endforeach
                </select>
                @error('bien_id')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                @if($biens->count() == 0)
                    <div style="margin-top:8px;padding:10px;background:#FFF8E1;border-radius:8px;font-size:13px;color:#E65100;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Vous n'avez pas encore de biens. 
                        <a href="{{ route('agence.biens.create') }}" style="color:var(--rust);font-weight:600;">Publiez un bien</a> d'abord.
                    </div>
                @endif
            </div>

            <!-- Prix proposé -->
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    Prix proposé (FCFA) <span style="color:#C62828;">*</span>
                </label>
                <input type="number" name="prix_propose" value="{{ old('prix_propose') }}" 
                       placeholder="Ex: {{ number_format($demande->budget_maximum, 0, ',', ' ') }}" 
                       style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                @error('prix_propose')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Budget maximum du client: {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois
                </div>
            </div>

            <!-- Message -->
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    Message au client <span style="color:#C62828;">*</span>
                </label>
                <textarea name="message" rows="4" 
                          placeholder="Bonjour, nous disposons d'un bien correspondant à votre recherche..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;" required>{{ old('message') }}</textarea>
                @error('message')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Minimum 10 caractères
                </div>
            </div>

            <!-- Upload Photos -->
            <div style="margin-top:20px;padding:16px;background:#F7F9FC;border-radius:10px;border:1px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;">
                    <i class="fa-regular fa-image" style="color:var(--rust);"></i> Photos du bien à proposer
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à 5 photos depuis votre galerie (format JPG, PNG, JPEG - max 5 Mo chacune)
                </p>
                <div id="photoContainer">
                    <div class="photo-upload-item" style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <input type="file" name="proposition_images[]" accept="image/*" 
                               style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
                        <button type="button" class="btn btn-ghost btn-sm remove-photo" style="color:#C62828;display:none;" onclick="removePropositionPhoto(this)">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addPropositionPhoto()" style="margin-top:8px;">
                    <i class="fa-solid fa-plus"></i> Ajouter une photo
                </button>
                <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                    <span id="propositionPhotoCount">1</span>/5 photos ajoutées
                </div>
                @error('proposition_images.*')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Upload Videos -->
            <div style="margin-top:16px;padding:16px;background:#F7F9FC;border-radius:10px;border:1px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;">
                    <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Vidéos du bien à proposer
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à 2 vidéos (format MP4, MOV, AVI - max 20 Mo chacune)
                </p>
                <div id="videoContainer">
                    <div class="video-upload-item" style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <input type="file" name="proposition_videos[]" accept="video/*" 
                               style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
                        <button type="button" class="btn btn-ghost btn-sm remove-video" style="color:#C62828;display:none;" onclick="removePropositionVideo(this)">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addPropositionVideo()" style="margin-top:8px;">
                    <i class="fa-solid fa-plus"></i> Ajouter une vidéo
                </button>
                <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                    <span id="propositionVideoCount">1</span>/2 vidéos ajoutées
                </div>
                @error('proposition_videos.*')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Boutons -->
            <div style="display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border);margin-top:20px;">
                <button type="submit" class="btn btn-rust">
                    <i class="fa-solid fa-paper-plane"></i> Envoyer la proposition
                </button>
                <a href="{{ route('agence.demandes.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Gestion des photos de proposition
    let propositionPhotoCount = 1;
    const maxPropositionPhotos = 5;

    function addPropositionPhoto() {
        if (propositionPhotoCount >= maxPropositionPhotos) {
            alert('Vous ne pouvez pas ajouter plus de ' + maxPropositionPhotos + ' photos.');
            return;
        }
        
        const container = document.getElementById('photoContainer');
        const newItem = document.createElement('div');
        newItem.className = 'photo-upload-item';
        newItem.style.cssText = 'display:flex;align-items:center;gap:12px;margin-bottom:8px;';
        newItem.innerHTML = `
            <input type="file" name="proposition_images[]" accept="image/*" 
                   style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
            <button type="button" class="btn btn-ghost btn-sm remove-photo" style="color:#C62828;" onclick="removePropositionPhoto(this)">
                <i class="fa-solid fa-times"></i>
            </button>
        `;
        container.appendChild(newItem);
        propositionPhotoCount++;
        document.getElementById('propositionPhotoCount').textContent = propositionPhotoCount;
        updatePropositionButtons();
    }

    function removePropositionPhoto(button) {
        if (propositionPhotoCount <= 1) {
            alert('Vous devez garder au moins un champ pour les photos.');
            return;
        }
        button.closest('.photo-upload-item').remove();
        propositionPhotoCount--;
        document.getElementById('propositionPhotoCount').textContent = propositionPhotoCount;
        updatePropositionButtons();
    }

    // Gestion des vidéos de proposition
    let propositionVideoCount = 1;
    const maxPropositionVideos = 2;

    function addPropositionVideo() {
        if (propositionVideoCount >= maxPropositionVideos) {
            alert('Vous ne pouvez pas ajouter plus de ' + maxPropositionVideos + ' vidéos.');
            return;
        }
        
        const container = document.getElementById('videoContainer');
        const newItem = document.createElement('div');
        newItem.className = 'video-upload-item';
        newItem.style.cssText = 'display:flex;align-items:center;gap:12px;margin-bottom:8px;';
        newItem.innerHTML = `
            <input type="file" name="proposition_videos[]" accept="video/*" 
                   style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
            <button type="button" class="btn btn-ghost btn-sm remove-video" style="color:#C62828;" onclick="removePropositionVideo(this)">
                <i class="fa-solid fa-times"></i>
            </button>
        `;
        container.appendChild(newItem);
        propositionVideoCount++;
        document.getElementById('propositionVideoCount').textContent = propositionVideoCount;
        updatePropositionButtons();
    }

    function removePropositionVideo(button) {
        if (propositionVideoCount <= 1) {
            alert('Vous devez garder au moins un champ pour les vidéos.');
            return;
        }
        button.closest('.video-upload-item').remove();
        propositionVideoCount--;
        document.getElementById('propositionVideoCount').textContent = propositionVideoCount;
        updatePropositionButtons();
    }

    function updatePropositionButtons() {
        // Photos
        document.querySelectorAll('#photoContainer .remove-photo').forEach(btn => {
            btn.style.display = propositionPhotoCount <= 1 ? 'none' : 'inline-flex';
        });
        
        // Vidéos
        document.querySelectorAll('#videoContainer .remove-video').forEach(btn => {
            btn.style.display = propositionVideoCount <= 1 ? 'none' : 'inline-flex';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        updatePropositionButtons();
    });
</script>
@endsection

@push('styles')
<style>
    .form-control.is-invalid {
        border-color: #C62828 !important;
    }
    .photo-upload-item input[type="file"],
    .video-upload-item input[type="file"] {
        background: #fff;
        cursor: pointer;
    }
    .photo-upload-item input[type="file"]:hover,
    .video-upload-item input[type="file"]:hover {
        border-color: var(--rust);
    }
</style>
@endpush