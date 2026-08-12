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

        <form method="POST" action="{{ route('agence.propositions.store') }}" enctype="multipart/form-data" id="propositionForm">
            @csrf
            <input type="hidden" name="demande_id" value="{{ $demande->id }}">

            <!-- ✅ OPTION 1: Sélection d'un bien existant -->
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    <i class="fa-regular fa-building"></i> Sélectionner un bien existant
                </label>
                <select name="bien_id" id="bienSelect" class="form-control @error('bien_id') is-invalid @enderror" 
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;background:#fff;" onchange="toggleMediaFields()">
                    <option value="">-- Aucun bien sélectionné --</option>
                    @foreach($biens as $bien)
                        <option value="{{ $bien->id }}" {{ old('bien_id') == $bien->id ? 'selected' : '' }}>
                            {{ $bien->titre }} — {{ number_format($bien->prix, 0, ',', ' ') }} FCFA ({{ $bien->surface }} m²)
                        </option>
                    @endforeach
                </select>
                @error('bien_id')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> 
                    @if($biens->count() > 0)
                        Sélectionnez un bien existant <strong>OU</strong> ajoutez des photos/vidéos ci-dessous.
                    @else
                        <span style="color:#E65100;">Vous n'avez pas encore de biens. Utilisez l'option "Ajouter des médias" ci-dessous.</span>
                    @endif
                </div>
            </div>

            <!-- Séparateur -->
            <div style="display:flex;align-items:center;gap:16px;margin:16px 0;">
                <hr style="flex:1;border:none;border-top:1px solid var(--border);">
                <span style="font-size:12px;color:var(--muted);font-weight:600;text-transform:uppercase;">OU</span>
                <hr style="flex:1;border:none;border-top:1px solid var(--border);">
            </div>

            <!-- ✅ OPTION 2: Ajout de médias (photos/vidéos) -->
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    <i class="fa-regular fa-images"></i> Ajouter des médias (photos ou vidéos)
                </label>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Choisissez d'ajouter <strong>soit des photos, soit des vidéos</strong> (pas les deux).
                    <span style="display:block;font-size:11px;color:#E65100;margin-top:4px;">
                        ⚠️ Cette option n'est disponible que si vous n'avez pas sélectionné de bien existant.
                    </span>
                </p>

                <div id="mediaFields" style="padding:16px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);">
                    <!-- Sélecteur Photos / Vidéos -->
                    <div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap;">
                        <button type="button" id="btnPhotos" class="btn btn-rust btn-sm" onclick="selectMediaType('photos')">
                            <i class="fa-regular fa-image"></i> Photos (jusqu'à 5)
                        </button>
                        <button type="button" id="btnVideos" class="btn btn-ghost btn-sm" onclick="selectMediaType('videos')">
                            <i class="fa-regular fa-circle-play"></i> Vidéos (jusqu'à 2)
                        </button>
                    </div>

                    <!-- Conteneur Photos -->
                    <div id="photosContainer" style="display:block;">
                        <div style="padding:12px;background:#fff;border-radius:8px;border:1px solid var(--border);">
                            <div id="photoContainer">
                                <div class="photo-upload-item" style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                                    <input type="file" name="proposition_images[]" accept="image/*" 
                                           style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
                                    <button type="button" class="btn btn-ghost btn-sm remove-photo" style="color:#C62828;display:none;" onclick="removePropositionPhoto(this)">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="addPropositionPhoto()" style="margin-top:8px;">
                                <i class="fa-solid fa-plus"></i> Ajouter une photo
                            </button>
                            <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                                <span id="propositionPhotoCount">1</span>/5 photos
                            </div>
                        </div>
                        @error('proposition_images.*')
                            <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Conteneur Vidéos -->
                    <div id="videosContainer" style="display:none;">
                        <div style="padding:12px;background:#fff;border-radius:8px;border:1px solid var(--border);">
                            <div id="videoContainer">
                                <div class="video-upload-item" style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                                    <input type="file" name="proposition_videos[]" accept="video/*" 
                                           style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
                                    <button type="button" class="btn btn-ghost btn-sm remove-video" style="color:#C62828;display:none;" onclick="removePropositionVideo(this)">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-ghost btn-sm" onclick="addPropositionVideo()" style="margin-top:8px;">
                                <i class="fa-solid fa-plus"></i> Ajouter une vidéo
                            </button>
                            <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                                <span id="propositionVideoCount">1</span>/2 vidéos
                            </div>
                        </div>
                        @error('proposition_videos.*')
                            <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Prix proposé -->
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    Prix proposé (FCFA) <span style="color:#C62828;">*</span>
                </label>
                <input type="number" name="prix_propose" id="prixPropose" value="{{ old('prix_propose') }}" 
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
                <textarea name="message" id="message" rows="4" 
                          placeholder="Bonjour, nous disposons d'un bien correspondant à votre recherche..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;" required>{{ old('message') }}</textarea>
                @error('message')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Minimum 10 caractères
                </div>
            </div>

            <!-- Boutons -->
            <div style="display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border);margin-top:20px;">
                <button type="submit" class="btn btn-rust" id="submitBtn">
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
    // ============================================
    // TOGGLE CHAMPS EN FONCTION DU BIEN SÉLECTIONNÉ
    // ============================================
    function toggleMediaFields() {
        const bienSelect = document.getElementById('bienSelect');
        const mediaFields = document.getElementById('mediaFields');
        const photoInputs = document.querySelectorAll('#photosContainer input[type="file"]');
        const videoInputs = document.querySelectorAll('#videosContainer input[type="file"]');

        if (bienSelect.value !== '') {
            mediaFields.style.opacity = '0.4';
            mediaFields.style.pointerEvents = 'none';
            photoInputs.forEach(input => input.disabled = true);
            videoInputs.forEach(input => input.disabled = true);
            
            document.querySelectorAll('#photoContainer .photo-upload-item:not(:first-child)').forEach(el => el.remove());
            document.querySelectorAll('#videoContainer .video-upload-item:not(:first-child)').forEach(el => el.remove());
            propositionPhotoCount = 1;
            propositionVideoCount = 1;
            document.getElementById('propositionPhotoCount').textContent = 1;
            document.getElementById('propositionVideoCount').textContent = 1;
        } else {
            mediaFields.style.opacity = '1';
            mediaFields.style.pointerEvents = 'auto';
            photoInputs.forEach(input => input.disabled = false);
            videoInputs.forEach(input => input.disabled = false);
        }
    }

    // ============================================
    // SELECTION PHOTOS / VIDEOS (exclusif)
    // ============================================
    let mediaType = 'photos';

    function selectMediaType(type) {
        mediaType = type;
        const photosContainer = document.getElementById('photosContainer');
        const videosContainer = document.getElementById('videosContainer');
        const btnPhotos = document.getElementById('btnPhotos');
        const btnVideos = document.getElementById('btnVideos');

        if (type === 'photos') {
            photosContainer.style.display = 'block';
            videosContainer.style.display = 'none';
            btnPhotos.className = 'btn btn-rust btn-sm';
            btnVideos.className = 'btn btn-ghost btn-sm';
        } else {
            photosContainer.style.display = 'none';
            videosContainer.style.display = 'block';
            btnPhotos.className = 'btn btn-ghost btn-sm';
            btnVideos.className = 'btn btn-rust btn-sm';
        }

        document.querySelectorAll('#photosContainer input[type="file"]').forEach(el => {
            el.disabled = (type === 'videos');
        });
        document.querySelectorAll('#videosContainer input[type="file"]').forEach(el => {
            el.disabled = (type === 'photos');
        });
    }

    // ============================================
    // GESTION DES PHOTOS
    // ============================================
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
                   style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
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

    // ============================================
    // GESTION DES VIDÉOS
    // ============================================
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
                   style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
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
        document.querySelectorAll('#photoContainer .remove-photo').forEach(btn => {
            btn.style.display = propositionPhotoCount <= 1 ? 'none' : 'inline-flex';
        });
        document.querySelectorAll('#videoContainer .remove-video').forEach(btn => {
            btn.style.display = propositionVideoCount <= 1 ? 'none' : 'inline-flex';
        });
    }

    // ============================================
    // VALIDATION AVANT SOUMISSION (CORRIGÉE)
    // ============================================
    document.getElementById('propositionForm').addEventListener('submit', function(e) {
        e.preventDefault(); // ✅ Bloque la soumission par défaut pour debug
        
        const bienSelect = document.getElementById('bienSelect');
        const photoInputs = document.querySelectorAll('#photoContainer input[type="file"]');
        const videoInputs = document.querySelectorAll('#videoContainer input[type="file"]');
        
        let hasPhotos = false;
        let hasVideos = false;
        
        photoInputs.forEach(input => {
            if (input.files && input.files.length > 0) hasPhotos = true;
        });
        
        videoInputs.forEach(input => {
            if (input.files && input.files.length > 0) hasVideos = true;
        });

        // Cas 1: Aucun bien sélectionné ET aucun média
        if (bienSelect.value === '' && !hasPhotos && !hasVideos) {
            alert('Veuillez sélectionner un bien existant OU ajouter des photos/vidéos.');
            return false;
        }

        // Cas 2: Un bien est sélectionné ET des médias sont ajoutés
        if (bienSelect.value !== '' && (hasPhotos || hasVideos)) {
            alert('Vous ne pouvez pas sélectionner un bien ET ajouter des médias en même temps.');
            return false;
        }

        // ✅ Cas 3: Un bien est sélectionné
        if (bienSelect.value !== '' && !hasPhotos && !hasVideos) {
            // Soumettre le formulaire normalement
            this.submit();
            return true;
        }

        // ✅ Cas 4: Aucun bien sélectionné MAIS des médias sont ajoutés
        if (bienSelect.value === '' && (hasPhotos || hasVideos)) {
            // Soumettre le formulaire normalement
            this.submit();
            return true;
        }

        // Fallback
        alert('Veuillez vérifier votre sélection.');
        return false;
    });

    // ============================================
    // INITIALISATION
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        updatePropositionButtons();
        toggleMediaFields();
        selectMediaType('photos');
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
    #mediaFields {
        transition: opacity 0.3s ease;
    }
</style>
@endpush