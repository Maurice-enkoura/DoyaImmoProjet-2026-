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
        <!-- ✅ AFFICHAGE DES ERREURS DE VALIDATION -->
        @if($errors->any())
            <div style="padding:12px 16px;background:#FFEBEE;border-radius:10px;border:1px solid #FFCDD2;margin-bottom:16px;">
                <strong style="color:#C62828;">Veuillez corriger les erreurs suivantes :</strong>
                <ul style="margin:8px 0 0;padding:0 0 0 16px;color:#C62828;font-size:13px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- ✅ AFFICHAGE DES MESSAGES FLASH -->
        @if(session('error'))
            <div style="padding:12px 16px;background:#FFEBEE;border-radius:10px;border:1px solid #FFCDD2;margin-bottom:16px;">
                <i class="fa-solid fa-exclamation-circle" style="color:#C62828;"></i>
                <span style="color:#C62828;font-size:13px;">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div style="padding:12px 16px;background:#E8F5E9;border-radius:10px;border:1px solid #C8E6C9;margin-bottom:16px;">
                <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i>
                <span style="color:#1E7A47;font-size:13px;">{{ session('success') }}</span>
            </div>
        @endif

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
                        <option value="{{ $bien->id }}" {{ old('bien_id') == $bien->id ? 'selected' : '' }} data-prix="{{ $bien->prix }}" data-titre="{{ $bien->titre }}">
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

            <!-- ✅ OPTION 2: Ajout de médias (photos/vidéos) avec prévisualisation -->
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
                            <i class="fa-regular fa-image"></i> Photos (jusqu'à 10)
                        </button>
                        <button type="button" id="btnVideos" class="btn btn-ghost btn-sm" onclick="selectMediaType('videos')">
                            <i class="fa-regular fa-circle-play"></i> Vidéos (jusqu'à 1)
                        </button>
                    </div>

                    <!-- Conteneur Photos avec prévisualisation -->
                    <div id="photosContainer" style="display:block;">
                        <div style="padding:12px;background:#fff;border-radius:8px;border:1px solid var(--border);">
                            <!-- Zone de drop / upload -->
                            <div id="photoDropZone" style="border:2px dashed var(--border);border-radius:8px;padding:20px;text-align:center;cursor:pointer;transition:all 0.3s;margin-bottom:12px;" 
                                 onclick="document.getElementById('photoInput').click()"
                                 ondragover="event.preventDefault();this.style.borderColor='var(--rust)';this.style.background='var(--rust-soft)';"
                                 ondrop="event.preventDefault();handlePhotoDrop(event);this.style.borderColor='var(--border)';this.style.background='transparent';"
                                 ondragleave="this.style.borderColor='var(--border)';this.style.background='transparent';">
                                <i class="fa-regular fa-cloud-arrow-up" style="font-size:32px;color:var(--muted);display:block;margin-bottom:8px;"></i>
                                <p style="margin:0;color:var(--muted);font-size:13px;">
                                    <strong>Cliquez ou glissez-déposez</strong> vos photos ici
                                </p>
                                <p style="margin:4px 0 0;color:var(--muted);font-size:11px;">
                                    Formats: JPG, PNG, JPEG, WebP (max 5 Mo)
                                </p>
                                <input type="file" id="photoInput" name="proposition_images[]" accept="image/*" multiple 
                                       style="display:none;" onchange="handlePhotoFiles(this.files)">
                            </div>

                            <!-- Prévisualisation des photos -->
                            <div id="photoPreviewContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-top:12px;"></div>

                            <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                                <span id="propositionPhotoCount">0</span>/10 photos sélectionnées
                            </div>
                        </div>
                        @error('proposition_images.*')
                            <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <!-- Conteneur Vidéos avec prévisualisation -->
                    <div id="videosContainer" style="display:none;">
                        <div style="padding:12px;background:#fff;border-radius:8px;border:1px solid var(--border);">
                            <!-- Zone de drop / upload vidéo -->
                            <div id="videoDropZone" style="border:2px dashed var(--border);border-radius:8px;padding:20px;text-align:center;cursor:pointer;transition:all 0.3s;margin-bottom:12px;" 
                                 onclick="document.getElementById('videoInput').click()"
                                 ondragover="event.preventDefault();this.style.borderColor='var(--rust)';this.style.background='var(--rust-soft)';"
                                 ondrop="event.preventDefault();handleVideoDrop(event);this.style.borderColor='var(--border)';this.style.background='transparent';"
                                 ondragleave="this.style.borderColor='var(--border)';this.style.background='transparent';">
                                <i class="fa-regular fa-cloud-arrow-up" style="font-size:32px;color:var(--muted);display:block;margin-bottom:8px;"></i>
                                <p style="margin:0;color:var(--muted);font-size:13px;">
                                    <strong>Cliquez ou glissez-déposez</strong> votre vidéo ici
                                </p>
                                <p style="margin:4px 0 0;color:var(--muted);font-size:11px;">
                                    Formats: MP4, MOV, AVI (max 20 Mo)
                                </p>
                                <input type="file" id="videoInput" name="proposition_videos[]" accept="video/*" multiple 
                                       style="display:none;" onchange="handleVideoFiles(this.files)">
                            </div>

                            <!-- Prévisualisation des vidéos -->
                            <div id="videoPreviewContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-top:12px;"></div>

                            <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                                <span id="propositionVideoCount">0</span>/1 vidéo sélectionnée
                            </div>
                        </div>
                        @error('proposition_videos.*')
                            <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- ✅ Prix proposé -->
            <div id="prixSection" style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    Prix proposé (FCFA) <span style="color:#C62828;">*</span>
                </label>
                <input type="number" name="prix_propose" id="prixPropose" value="{{ old('prix_propose') }}" 
                       placeholder="Ex: {{ number_format($demande->budget_maximum, 0, ',', ' ') }}" 
                       style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;"
                       required>
                @error('prix_propose')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Budget maximum du client: {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois
                </div>
            </div>

            <!-- ✅ Message - Toujours visible -->
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    Message au client <span style="color:#C62828;">*</span>
                </label>
                <textarea name="message" id="message" rows="4" 
                          placeholder="Bonjour, nous disposons d'un bien correspondant à votre recherche..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;" required>{{ old('message') }}</textarea>
                @error('message')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
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
    // VARIABLES GLOBALES
    // ============================================
    let selectedPhotos = [];
    let selectedVideos = [];
    let mediaType = 'photos';

    // ============================================
    // TOGGLE CHAMPS EN FONCTION DU BIEN SÉLECTIONNÉ
    // ============================================
    function toggleMediaFields() {
        const bienSelect = document.getElementById('bienSelect');
        const mediaFields = document.getElementById('mediaFields');
        const prixSection = document.getElementById('prixSection');
        const prixInput = document.getElementById('prixPropose');

        if (bienSelect.value !== '') {
            mediaFields.style.opacity = '0.4';
            mediaFields.style.pointerEvents = 'none';
            prixSection.style.display = 'none';
            prixInput.removeAttribute('required');
            
            const selectedOption = bienSelect.options[bienSelect.selectedIndex];
            const bienTitre = selectedOption.getAttribute('data-titre') || '';
            const messageField = document.getElementById('message');
            if (messageField.value === '' || messageField.value === 'Bonjour, nous disposons d\'un bien correspondant à votre recherche...') {
                messageField.value = `Bonjour, nous disposons du bien "${bienTitre}" qui correspond à votre recherche. N'hésitez pas à nous contacter pour plus d'informations.`;
            }

            selectedPhotos = [];
            selectedVideos = [];
            updatePhotoPreview();
            updateVideoPreview();
            document.getElementById('propositionPhotoCount').textContent = 0;
            document.getElementById('propositionVideoCount').textContent = 0;
        } else {
            mediaFields.style.opacity = '1';
            mediaFields.style.pointerEvents = 'auto';
            prixSection.style.display = 'block';
            prixInput.setAttribute('required', 'required');
            
            const messageField = document.getElementById('message');
            if (messageField.value === '') {
                messageField.value = 'Bonjour, nous disposons d\'un bien correspondant à votre recherche...';
            }
        }
    }

    // ============================================
    // SELECTION PHOTOS / VIDEOS (exclusif)
    // ============================================
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
    }

    // ============================================
    // GESTION DES PHOTOS
    // ============================================
    const maxPhotos = 10;

    function handlePhotoFiles(files) {
        const fileArray = Array.from(files);
        const currentCount = selectedPhotos.length;

        if (currentCount + fileArray.length > maxPhotos) {
            alert('Vous ne pouvez pas sélectionner plus de ' + maxPhotos + ' photos.');
            return;
        }

        fileArray.forEach(file => {
            if (file.size > 5 * 1024 * 1024) {
                alert('La photo "' + file.name + '" dépasse 5 Mo.');
                return;
            }
            if (!file.type.startsWith('image/')) {
                alert('Le fichier "' + file.name + '" n\'est pas une image.');
                return;
            }
            selectedPhotos.push(file);
        });

        updatePhotoPreview();
        document.getElementById('propositionPhotoCount').textContent = selectedPhotos.length;
        document.getElementById('photoInput').value = '';
    }

    function handlePhotoDrop(e) {
        const files = e.dataTransfer.files;
        handlePhotoFiles(files);
    }

    function removePhoto(index) {
        selectedPhotos.splice(index, 1);
        updatePhotoPreview();
        document.getElementById('propositionPhotoCount').textContent = selectedPhotos.length;
    }

    function updatePhotoPreview() {
        const container = document.getElementById('photoPreviewContainer');
        container.innerHTML = '';

        if (selectedPhotos.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'grid';

        selectedPhotos.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);aspect-ratio:1/1;background:#F0F2F5;';
                div.innerHTML = `
                    <img src="${e.target.result}" alt="Aperçu ${index + 1}" style="width:100%;height:100%;object-fit:cover;">
                    <button type="button" onclick="removePhoto(${index})" 
                            style="position:absolute;top:6px;right:6px;width:24px;height:24px;border-radius:50%;border:none;background:rgba(0,0,0,0.7);color:#fff;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;">
                        <i class="fa-solid fa-times"></i>
                    </button>
                    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.6);color:#fff;font-size:10px;padding:2px 6px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                    </div>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // ============================================
    // GESTION DES VIDÉOS
    // ============================================
    const maxVideos = 1;

    function handleVideoFiles(files) {
        const fileArray = Array.from(files);
        const currentCount = selectedVideos.length;

        if (currentCount + fileArray.length > maxVideos) {
            alert('Vous ne pouvez pas sélectionner plus de ' + maxVideos + ' vidéo.');
            return;
        }

        fileArray.forEach(file => {
            if (file.size > 20 * 1024 * 1024) {
                alert('La vidéo "' + file.name + '" dépasse 20 Mo.');
                return;
            }
            if (!file.type.startsWith('video/')) {
                alert('Le fichier "' + file.name + '" n\'est pas une vidéo.');
                return;
            }
            selectedVideos.push(file);
        });

        updateVideoPreview();
        document.getElementById('propositionVideoCount').textContent = selectedVideos.length;
        document.getElementById('videoInput').value = '';
    }

    function handleVideoDrop(e) {
        const files = e.dataTransfer.files;
        handleVideoFiles(files);
    }

    function removeVideo(index) {
        selectedVideos.splice(index, 1);
        updateVideoPreview();
        document.getElementById('propositionVideoCount').textContent = selectedVideos.length;
    }

    function updateVideoPreview() {
        const container = document.getElementById('videoPreviewContainer');
        container.innerHTML = '';

        if (selectedVideos.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'grid';

        selectedVideos.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);background:#000;aspect-ratio:16/9;';
                div.innerHTML = `
                    <video src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;" muted></video>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:32px;opacity:0.5;pointer-events:none;">
                        <i class="fa-solid fa-play"></i>
                    </div>
                    <button type="button" onclick="removeVideo(${index})" 
                            style="position:absolute;top:6px;right:6px;width:24px;height:24px;border-radius:50%;border:none;background:rgba(0,0,0,0.7);color:#fff;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;">
                        <i class="fa-solid fa-times"></i>
                    </button>
                    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.6);color:#fff;font-size:10px;padding:2px 6px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                    </div>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }

    // ============================================
    // 🔥 SOUMISSION DU FORMULAIRE - CORRIGÉ
    // ============================================
    document.getElementById('propositionForm').addEventListener('submit', function(e) {
        const bienSelect = document.getElementById('bienSelect');
        const prixInput = document.getElementById('prixPropose');
        const messageInput = document.getElementById('message');
        
        // ✅ Vérifier le message
        if (!messageInput.value || messageInput.value.trim().length < 10) {
            e.preventDefault();
            alert('Le message doit contenir au moins 10 caractères.');
            messageInput.focus();
            return false;
        }
        
        // Cas 1: Aucun bien sélectionné ET aucun média
        if (bienSelect.value === '' && selectedPhotos.length === 0 && selectedVideos.length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner un bien existant OU ajouter des photos/vidéos.');
            return false;
        }

        // Cas 2: Un bien est sélectionné ET des médias sont ajoutés
        if (bienSelect.value !== '' && (selectedPhotos.length > 0 || selectedVideos.length > 0)) {
            e.preventDefault();
            alert('Vous ne pouvez pas sélectionner un bien ET ajouter des médias en même temps.');
            return false;
        }

        // ✅ Cas 3: Aucun bien sélectionné MAIS des médias sont ajoutés - Le prix est obligatoire
        if (bienSelect.value === '' && (selectedPhotos.length > 0 || selectedVideos.length > 0)) {
            const prix = prixInput.value;
            if (!prix || prix <= 0) {
                e.preventDefault();
                alert('Veuillez saisir un prix proposé.');
                prixInput.focus();
                return false;
            }
        }

        // ✅ Cas 4: Un bien est sélectionné - Le prix n'est pas obligatoire
        if (bienSelect.value !== '' && selectedPhotos.length === 0 && selectedVideos.length === 0) {
            prixInput.removeAttribute('required');
        }

        // ✅ Tout est bon - le formulaire se soumet
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Envoi en cours...';
        btn.disabled = true;
        return true;
    });

    // ============================================
    // INITIALISATION
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        toggleMediaFields();
        selectMediaType('photos');
        updatePhotoPreview();
        updateVideoPreview();
    });
</script>
@endsection

@push('styles')
<style>
    .form-control.is-invalid {
        border-color: #C62828 !important;
    }
    #mediaFields {
        transition: opacity 0.3s ease;
    }
    #photoPreviewContainer, #videoPreviewContainer {
        transition: all 0.3s ease;
    }
    #photoDropZone:hover, #videoDropZone:hover {
        border-color: var(--rust);
        background: var(--rust-soft);
    }
    .btn-ghost:hover {
        background: var(--border);
    }
    #prixSection {
        transition: all 0.3s ease;
    }
</style>
@endpush