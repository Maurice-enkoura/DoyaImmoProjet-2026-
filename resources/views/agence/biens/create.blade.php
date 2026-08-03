@extends('layouts.dashboard-agence')

@section('title', 'Publier un bien — DoyaImmo')
@section('page_title', 'Publier un bien')
@section('page_sub', 'Ajoutez un nouveau bien immobilier')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes biens
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
        <form method="POST" action="{{ route('agence.biens.store') }}" enctype="multipart/form-data" id="bienForm">
            @csrf

            <!-- Informations principales -->
            <div style="margin-bottom:20px;">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-info-circle" style="color:var(--rust);"></i> Informations principales
                </h4>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Titre <span style="color:#C62828;">*</span>
                        </label>
                        <input type="text" name="titre" value="{{ old('titre') }}" placeholder="Ex: Magnifique villa à Ngor"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('titre')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Type de bien <span style="color:#C62828;">*</span>
                        </label>
                        <select name="type_bien" id="type_bien" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                            <option value="">Sélectionnez un type</option>
                            @foreach(\App\Enums\TypeBienEnum::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('type_bien') == $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_bien')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Type de contrat <span style="color:#C62828;">*</span>
                        </label>
                        <select name="type_contrat" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                            <option value="">Sélectionnez un contrat</option>
                            @foreach(\App\Enums\TypeContratEnum::cases() as $type)
                                <option value="{{ $type->value }}" {{ old('type_contrat') == $type->value ? 'selected' : '' }}>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_contrat')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Prix (FCFA) <span style="color:#C62828;">*</span>
                        </label>
                        <input type="number" name="prix" value="{{ old('prix') }}" placeholder="Ex: 250000000"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('prix')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Localisation -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-location-dot" style="color:var(--rust);"></i> Localisation
                </h4>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Quartier <span style="color:#C62828;">*</span>
                        </label>
                        <input type="text" name="quartier" value="{{ old('quartier') }}" placeholder="Ex: Almadies, Ngor, Mermoz"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('quartier')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Adresse <span style="color:#C62828;">*</span>
                        </label>
                        <input type="text" name="adresse" value="{{ old('adresse') }}" placeholder="Ex: Rue des Almadies, Dakar"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('adresse')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Caractéristiques -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-sliders-h" style="color:var(--rust);"></i> Caractéristiques
                </h4>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Chambres <span style="color:#C62828;">*</span>
                        </label>
                        <input type="number" name="nombre_chambres" id="nombre_chambres" value="{{ old('nombre_chambres', 0) }}" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('nombre_chambres')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Salles de bain <span style="color:#C62828;">*</span>
                        </label>
                        <input type="number" name="nombre_salles_bain" id="nombre_salles_bain" value="{{ old('nombre_salles_bain', 0) }}" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('nombre_salles_bain')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Surface (m²) <span style="color:#C62828;">*</span>
                        </label>
                        <input type="number" step="0.01" name="surface" value="{{ old('surface') }}" placeholder="Ex: 150"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('surface')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Équipements -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-cogs" style="color:var(--rust);"></i> Équipements
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Sélectionnez les équipements disponibles dans ce bien.
                </p>

                <div id="equipementsContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="parking_disponible" value="1" {{ old('parking_disponible') ? 'checked' : '' }}>
                        <i class="fa-solid fa-car" style="color:var(--muted);"></i> Parking
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="est_meuble" value="1" {{ old('est_meuble') ? 'checked' : '' }}>
                        <i class="fa-solid fa-couch" style="color:var(--muted);"></i> Meublé
                    </label>
                </div>

                <!-- Note pour les terrains -->
                <div id="terrainNote" style="display:none;margin-top:8px;padding:8px 12px;background:#FFF8E1;border-radius:8px;font-size:12px;color:#E65100;">
                    <i class="fa-solid fa-info-circle"></i> Les équipements ne sont pas applicables pour un terrain.
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:8px;color:var(--text-soft);">
                    <i class="fa-solid fa-align-left" style="color:var(--rust);"></i> Description <span style="color:#C62828;">*</span>
                </h4>
                <textarea name="description" rows="4" placeholder="Décrivez votre bien en détail..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;" required>{{ old('description') }}</textarea>
                @error('description')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Minimum 20 caractères
                </div>
            </div>

            <!-- Photos -->
            <div style="margin-top:20px;padding:16px;background:#F7F9FC;border-radius:10px;border:1px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;">
                    <i class="fa-regular fa-image" style="color:var(--rust);"></i> Photos du bien
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à 10 photos (format JPG, PNG, JPEG - max 5 Mo chacune)
                </p>
                <div id="photoContainer">
                    <div class="photo-upload-item" style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <input type="file" name="images[]" accept="image/*" 
                               style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
                        <button type="button" class="btn btn-ghost btn-sm remove-photo" style="color:#C62828;display:none;" onclick="removePhoto(this)">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addPhoto()" style="margin-top:8px;">
                    <i class="fa-solid fa-plus"></i> Ajouter une photo
                </button>
                <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                    <span id="photoCount">1</span>/10 photos ajoutées
                </div>
                @error('images.*')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Vidéos -->
            <div style="margin-top:16px;padding:16px;background:#F7F9FC;border-radius:10px;border:1px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;">
                    <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Vidéos du bien
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à 3 vidéos (format MP4, MOV, AVI - max 20 Mo chacune)
                </p>
                <div id="videoContainer">
                    <div class="video-upload-item" style="display:flex;align-items:center;gap:12px;margin-bottom:8px;">
                        <input type="file" name="videos[]" accept="video/*" 
                               style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
                        <button type="button" class="btn btn-ghost btn-sm remove-video" style="color:#C62828;display:none;" onclick="removeVideo(this)">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-ghost btn-sm" onclick="addVideo()" style="margin-top:8px;">
                    <i class="fa-solid fa-plus"></i> Ajouter une vidéo
                </button>
                <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                    <span id="videoCount">1</span>/3 vidéos ajoutées
                </div>
                @error('videos.*')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Boutons -->
            <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:12px;">
                <button type="submit" class="btn btn-rust" id="submitBtn">
                    <i class="fa-solid fa-plus"></i> Publier le bien
                </button>
                <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Gestion des champs conditionnels selon le type de bien
    document.getElementById('type_bien')?.addEventListener('change', function() {
        const type = this.value;
        const equipementsContainer = document.getElementById('equipementsContainer');
        const terrainNote = document.getElementById('terrainNote');
        const chambres = document.getElementById('nombre_chambres');
        const sdb = document.getElementById('nombre_salles_bain');
        
        if (type === 'terrain') {
            // Cacher les équipements pour un terrain
            equipementsContainer.style.display = 'none';
            terrainNote.style.display = 'block';
            chambres.value = 0;
            chambres.disabled = true;
            sdb.value = 0;
            sdb.disabled = true;
        } else {
            equipementsContainer.style.display = 'grid';
            terrainNote.style.display = 'none';
            chambres.disabled = false;
            sdb.disabled = false;
        }
    });

    // Gestion des photos
    let photoCount = 1;
    const maxPhotos = 10;

    function addPhoto() {
        if (photoCount >= maxPhotos) {
            alert('Vous ne pouvez pas ajouter plus de ' + maxPhotos + ' photos.');
            return;
        }
        
        const container = document.getElementById('photoContainer');
        const newItem = document.createElement('div');
        newItem.className = 'photo-upload-item';
        newItem.style.cssText = 'display:flex;align-items:center;gap:12px;margin-bottom:8px;';
        newItem.innerHTML = `
            <input type="file" name="images[]" accept="image/*" 
                   style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
            <button type="button" class="btn btn-ghost btn-sm remove-photo" style="color:#C62828;" onclick="removePhoto(this)">
                <i class="fa-solid fa-times"></i>
            </button>
        `;
        container.appendChild(newItem);
        photoCount++;
        document.getElementById('photoCount').textContent = photoCount;
        updateButtons();
    }

    function removePhoto(button) {
        if (photoCount <= 1) {
            alert('Vous devez garder au moins un champ pour les photos.');
            return;
        }
        button.closest('.photo-upload-item').remove();
        photoCount--;
        document.getElementById('photoCount').textContent = photoCount;
        updateButtons();
    }

    // Gestion des vidéos
    let videoCount = 1;
    const maxVideos = 3;

    function addVideo() {
        if (videoCount >= maxVideos) {
            alert('Vous ne pouvez pas ajouter plus de ' + maxVideos + ' vidéos.');
            return;
        }
        
        const container = document.getElementById('videoContainer');
        const newItem = document.createElement('div');
        newItem.className = 'video-upload-item';
        newItem.style.cssText = 'display:flex;align-items:center;gap:12px;margin-bottom:8px;';
        newItem.innerHTML = `
            <input type="file" name="videos[]" accept="video/*" 
                   style="flex:1;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
            <button type="button" class="btn btn-ghost btn-sm remove-video" style="color:#C62828;" onclick="removeVideo(this)">
                <i class="fa-solid fa-times"></i>
            </button>
        `;
        container.appendChild(newItem);
        videoCount++;
        document.getElementById('videoCount').textContent = videoCount;
        updateButtons();
    }

    function removeVideo(button) {
        if (videoCount <= 1) {
            alert('Vous devez garder au moins un champ pour les vidéos.');
            return;
        }
        button.closest('.video-upload-item').remove();
        videoCount--;
        document.getElementById('videoCount').textContent = videoCount;
        updateButtons();
    }

    function updateButtons() {
        document.querySelectorAll('.remove-photo').forEach(btn => {
            btn.style.display = photoCount <= 1 ? 'none' : 'inline-flex';
        });
        document.querySelectorAll('.remove-video').forEach(btn => {
            btn.style.display = videoCount <= 1 ? 'none' : 'inline-flex';
        });
    }

    // Soumission du formulaire
    document.getElementById('bienForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publication en cours...';
        btn.disabled = true;
    });

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        updateButtons();
        document.getElementById('type_bien')?.dispatchEvent(new Event('change'));
    });
</script>
@endsection

@push('styles')
<style>
    .photo-upload-item input[type="file"],
    .video-upload-item input[type="file"] {
        background: #fff;
        cursor: pointer;
    }
    .photo-upload-item input[type="file"]:hover,
    .video-upload-item input[type="file"]:hover {
        border-color: var(--rust);
    }

    label:has(input[type="checkbox"]) {
        transition: all 0.2s;
        cursor: pointer;
    }

    label:has(input[type="checkbox"]:checked) {
        background: var(--rust-soft);
        border-color: var(--rust);
    }

    label:has(input[type="checkbox"]:checked) i {
        color: var(--rust) !important;
    }

    @media (max-width: 768px) {
        [style*="display:grid;grid-template-columns:1fr 1fr;gap:16px;"] {
            grid-template-columns: 1fr !important;
        }
        [style*="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush