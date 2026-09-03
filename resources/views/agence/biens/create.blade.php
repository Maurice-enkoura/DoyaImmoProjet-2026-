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

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:clamp(16px, 2vw, 24px);">
        @if($errors->any())
            <div style="padding:12px 16px;background:#FFEBEE;border-radius:10px;border:1px solid #FFCDD2;margin-bottom:16px;">
                <ul style="margin:0;padding:0 0 0 16px;color:#C62828;font-size:13px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agence.biens.store') }}" enctype="multipart/form-data" id="bienForm">
            @csrf

            <!-- Informations principales -->
            <div style="margin-bottom:20px;">
                <h4 style="font-family:var(--display);font-size:clamp(14px, 1vw, 15px);margin-bottom:12px;color:var(--text-soft);">
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
                <h4 style="font-family:var(--display);font-size:clamp(14px, 1vw, 15px);margin-bottom:12px;color:var(--text-soft);">
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
                <h4 style="font-family:var(--display);font-size:clamp(14px, 1vw, 15px);margin-bottom:12px;color:var(--text-soft);">
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
                            Surface (m²)
                        </label>
                        <input type="number" step="0.01" name="surface" id="surface" value="{{ old('surface') }}" placeholder="Ex: 150"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                        @error('surface')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <small style="font-size:11px;color:var(--muted);">Optionnel</small>
                    </div>
                </div>
            </div>

            <!-- Équipements -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:clamp(14px, 1vw, 15px);margin-bottom:12px;color:var(--text-soft);">
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

                <div id="terrainNote" style="display:none;margin-top:8px;padding:8px 12px;background:#FFF8E1;border-radius:8px;font-size:12px;color:#E65100;">
                    <i class="fa-solid fa-info-circle"></i> Les équipements ne sont pas applicables pour un terrain.
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:clamp(14px, 1vw, 15px);margin-bottom:8px;color:var(--text-soft);">
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

            <!-- Photos - Sélection multiple -->
            <div style="margin-top:20px;padding:clamp(14px, 1.5vw, 20px);background:#F7F9FC;border-radius:10px;border:2px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:clamp(13px, 0.9vw, 14px);margin-bottom:8px;">
                    <i class="fa-regular fa-image" style="color:var(--rust);"></i> Photos du bien
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à <strong>10 photos</strong> (format JPG, PNG, JPEG, WebP - max 5 Mo chacune)
                </p>
                <input type="file" name="images[]" accept="image/*" multiple 
                       id="imageInput"
                       style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
                
                <!-- Prévisualisation des photos -->
                <div id="imagePreviewContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-top:12px;display:none;"></div>
                <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                    <span id="imageCount">0</span>/10 photos sélectionnées
                </div>
                @error('images.*')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
                @error('images')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Vidéos - Sélection multiple -->
            <div style="margin-top:16px;padding:clamp(14px, 1.5vw, 20px);background:#F7F9FC;border-radius:10px;border:2px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:clamp(13px, 0.9vw, 14px);margin-bottom:8px;">
                    <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Vidéos du bien
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à <strong>1 vidéo</strong> (format MP4, MOV, AVI - max 20 Mo)
                </p>
                <input type="file" name="videos[]" accept="video/*" multiple 
                       id="videoInput"
                       style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
                
                <!-- Prévisualisation des vidéos -->
                <div id="videoPreviewContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-top:12px;display:none;"></div>
                <div style="font-size:11px;color:var(--muted);margin-top:8px;">
                    <span id="videoCount">0</span>/1 vidéo sélectionnée
                </div>
                @error('videos.*')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
                @error('videos')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Boutons -->
            <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:12px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-rust" id="submitBtn" style="padding:10px 24px;border:none;border-radius:10px;background:var(--rust);color:#fff;font-weight:600;cursor:pointer;transition:background 0.2s;display:inline-flex;align-items:center;gap:8px;font-size:14px;">
                    <i class="fa-solid fa-plus"></i> Publier le bien
                </button>
                <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost" style="padding:10px 24px;border-radius:10px;border:1px solid var(--border);background:transparent;color:var(--text-soft);font-weight:600;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:8px;font-size:14px;text-decoration:none;">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // ==================== GESTION DES CHAMPS CONDITIONNELS ====================
    document.getElementById('type_bien')?.addEventListener('change', function() {
        const type = this.value;
        const equipementsContainer = document.getElementById('equipementsContainer');
        const terrainNote = document.getElementById('terrainNote');
        const chambres = document.getElementById('nombre_chambres');
        const sdb = document.getElementById('nombre_salles_bain');
        const surface = document.getElementById('surface');
        
        if (type === 'terrain') {
            equipementsContainer.style.display = 'none';
            terrainNote.style.display = 'block';
            chambres.value = 0;
            chambres.disabled = true;
            sdb.value = 0;
            sdb.disabled = true;
            surface.placeholder = 'Optionnel (ex: 500)';
        } else {
            equipementsContainer.style.display = 'grid';
            terrainNote.style.display = 'none';
            chambres.disabled = false;
            sdb.disabled = false;
            surface.placeholder = 'Ex: 150';
        }
    });

    // ==================== PRÉVISUALISATION DES IMAGES ====================
    document.getElementById('imageInput')?.addEventListener('change', function(e) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        container.style.display = 'grid';
        
        const files = Array.from(this.files);
        
        if (files.length === 0) {
            container.style.display = 'none';
            document.getElementById('imageCount').textContent = '0';
            return;
        }
        
        // ✅ Limiter à 10 photos
        if (files.length > 10) {
            alert('⚠️ Vous ne pouvez sélectionner que 10 photos maximum.');
            this.value = '';
            container.style.display = 'none';
            document.getElementById('imageCount').textContent = '0';
            return;
        }
        
        document.getElementById('imageCount').textContent = files.length;
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const div = document.createElement('div');
                div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);aspect-ratio:1/1;background:#F0F2F5;';
                div.innerHTML = `
                    <img src="${event.target.result}" alt="Aperçu ${index + 1}" style="width:100%;height:100%;object-fit:cover;">
                    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.6);color:#fff;font-size:10px;padding:2px 6px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                    </div>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // ==================== PRÉVISUALISATION DES VIDÉOS ====================
    document.getElementById('videoInput')?.addEventListener('change', function(e) {
        const container = document.getElementById('videoPreviewContainer');
        container.innerHTML = '';
        container.style.display = 'grid';
        
        const files = Array.from(this.files);
        
        if (files.length === 0) {
            container.style.display = 'none';
            document.getElementById('videoCount').textContent = '0';
            return;
        }
        
        // ✅ Limiter à 1 vidéo
        if (files.length > 1) {
            alert('⚠️ Vous ne pouvez sélectionner qu\'1 vidéo maximum.');
            this.value = '';
            container.style.display = 'none';
            document.getElementById('videoCount').textContent = '0';
            return;
        }
        
        document.getElementById('videoCount').textContent = files.length;
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const div = document.createElement('div');
                div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);background:#000;aspect-ratio:16/9;';
                div.innerHTML = `
                    <video src="${event.target.result}" style="width:100%;height:100%;object-fit:cover;" muted></video>
                    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:32px;opacity:0.5;pointer-events:none;">
                        <i class="fa-solid fa-play"></i>
                    </div>
                    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,0,0,0.6);color:#fff;font-size:10px;padding:2px 6px;text-align:center;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        ${file.name.length > 15 ? file.name.substring(0, 15) + '...' : file.name}
                    </div>
                `;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });

    // ==================== SOUMISSION DU FORMULAIRE ====================
    document.getElementById('bienForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publication en cours...';
        btn.disabled = true;
    });

    // ==================== INITIALISATION ====================
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('type_bien')?.dispatchEvent(new Event('change'));
    });
</script>
@endsection

@push('styles')
<style>
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

    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
    }
    .btn-ghost:hover {
        background: var(--border);
    }
    .btn-rust:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    /* ✅ RESPONSIVE */
    @media (max-width: 768px) {
        [style*="display:grid;grid-template-columns:1fr 1fr;gap:16px;"] {
            grid-template-columns: 1fr !important;
        }
        [style*="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;"] {
            grid-template-columns: 1fr !important;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr))"] {
            grid-template-columns: repeat(auto-fill,minmax(80px,1fr)) !important;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr))"] {
            grid-template-columns: repeat(auto-fill,minmax(140px,1fr)) !important;
        }
    }

    @media (max-width: 480px) {
        .btn-rust, .btn-ghost {
            width: 100% !important;
            justify-content: center !important;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr))"] {
            grid-template-columns: repeat(auto-fill,minmax(70px,1fr)) !important;
            gap: 8px !important;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr))"] {
            grid-template-columns: 1fr !important;
        }
        [style*="margin-top:24px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:12px;flex-wrap:wrap;"] {
            flex-direction: column !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
@endpush