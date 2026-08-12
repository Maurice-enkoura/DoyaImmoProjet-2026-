@extends('layouts.dashboard-agence')

@section('title', 'Modifier le bien — DoyaImmo')
@section('page_title', 'Modifier le bien')
@section('page_sub', 'Modifiez les informations de votre bien')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes biens
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
        @if($errors->any())
            <div style="padding:12px 16px;background:#FFEBEE;border-radius:10px;border:1px solid #FFCDD2;margin-bottom:16px;">
                <ul style="margin:0;padding:0 0 0 16px;color:#C62828;font-size:13px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div style="padding:12px 16px;background:#E8F5E9;border-radius:10px;border:1px solid #C8E6C9;margin-bottom:16px;color:#1E7A47;">
                <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('agence.biens.update', $bien) }}" enctype="multipart/form-data" id="bienForm">
            @csrf
            @method('PUT')

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
                        <input type="text" name="titre" value="{{ old('titre', $bien->titre) }}" 
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
                                <option value="{{ $type->value }}" {{ old('type_bien', $bien->type_bien->value) == $type->value ? 'selected' : '' }}>
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
                                <option value="{{ $type->value }}" {{ old('type_contrat', $bien->type_contrat->value) == $type->value ? 'selected' : '' }}>
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
                        <input type="number" name="prix" value="{{ old('prix', $bien->prix) }}" 
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
                        <input type="text" name="quartier" value="{{ old('quartier', $bien->quartier) }}" 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('quartier')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Adresse <span style="color:#C62828;">*</span>
                        </label>
                        <input type="text" name="adresse" value="{{ old('adresse', $bien->adresse) }}" 
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
                        <input type="number" name="nombre_chambres" id="nombre_chambres" value="{{ old('nombre_chambres', $bien->nombre_chambres) }}" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('nombre_chambres')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Salles de bain <span style="color:#C62828;">*</span>
                        </label>
                        <input type="number" name="nombre_salles_bain" id="nombre_salles_bain" value="{{ old('nombre_salles_bain', $bien->nombre_salles_bain) }}" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('nombre_salles_bain')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Surface (m²) <span style="color:#C62828;">*</span>
                        </label>
                        <input type="number" step="0.01" name="surface" value="{{ old('surface', $bien->surface) }}" 
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
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;{{ old('parking_disponible', $bien->parking_disponible) ? 'background:var(--rust-soft);border-color:var(--rust);' : '' }}">
                        <input type="checkbox" name="parking_disponible" value="1" {{ old('parking_disponible', $bien->parking_disponible) ? 'checked' : '' }}>
                        <i class="fa-solid fa-car" style="color:{{ old('parking_disponible', $bien->parking_disponible) ? 'var(--rust)' : 'var(--muted)' }};"></i> Parking
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;{{ old('est_meuble', $bien->est_meuble) ? 'background:var(--rust-soft);border-color:var(--rust);' : '' }}">
                        <input type="checkbox" name="est_meuble" value="1" {{ old('est_meuble', $bien->est_meuble) ? 'checked' : '' }}>
                        <i class="fa-solid fa-couch" style="color:{{ old('est_meuble', $bien->est_meuble) ? 'var(--rust)' : 'var(--muted)' }};"></i> Meublé
                    </label>
                </div>

                <div id="terrainNote" style="display:none;margin-top:8px;padding:8px 12px;background:#FFF8E1;border-radius:8px;font-size:12px;color:#E65100;">
                    <i class="fa-solid fa-info-circle"></i> Les équipements ne sont pas applicables pour un terrain.
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:8px;color:var(--text-soft);">
                    <i class="fa-solid fa-align-left" style="color:var(--rust);"></i> Description <span style="color:#C62828;">*</span>
                </h4>
                <textarea name="description" rows="4" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;" required>{{ old('description', $bien->description) }}</textarea>
                @error('description')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Photos existantes -->
            @php
                $images = $bien->medias->where('type_media', 'image');
            @endphp
            @if($images->count() > 0)
                <div style="margin-top:20px;padding:16px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;">
                        <i class="fa-regular fa-image" style="color:var(--rust);"></i> Photos actuelles
                    </h4>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;">
                        @foreach($images as $media)
                            <div style="position:relative;width:100px;height:100px;border-radius:8px;overflow:hidden;border:1px solid var(--border);">
                                <img src="{{ asset('storage/' . $media->fichier) }}" 
                                     alt="Photo" 
                                     style="width:100%;height:100%;object-fit:cover;">
                                <a href="{{ route('agence.medias.destroy', $media) }}" 
                                   onclick="return confirm('Supprimer cette photo ?')"
                                   style="position:absolute;top:4px;right:4px;width:24px;height:24px;border-radius:50%;border:none;background:rgba(0,0,0,0.7);color:#fff;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;text-decoration:none;">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <p style="font-size:11px;color:var(--muted);margin-top:8px;">
                        <i class="fa-regular fa-info-circle"></i> Cliquez sur la croix pour supprimer une photo
                    </p>
                </div>
            @endif

            <!-- Vidéos existantes -->
            @php
                $videos = $bien->medias->where('type_media', 'video');
            @endphp
            @if($videos->count() > 0)
                <div style="margin-top:16px;padding:16px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;">
                        <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Vidéos actuelles
                    </h4>
                    <div style="display:flex;flex-wrap:wrap;gap:12px;">
                        @foreach($videos as $media)
                            <div style="position:relative;width:160px;height:100px;border-radius:8px;overflow:hidden;border:1px solid var(--border);background:#000;">
                                <video src="{{ asset('storage/' . $media->fichier) }}" 
                                       style="width:100%;height:100%;object-fit:cover;"
                                       muted>
                                </video>
                                <a href="{{ route('agence.medias.destroy', $media) }}" 
                                   onclick="return confirm('Supprimer cette vidéo ?')"
                                   style="position:absolute;top:4px;right:4px;width:24px;height:24px;border-radius:50%;border:none;background:rgba(0,0,0,0.7);color:#fff;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;text-decoration:none;">
                                    <i class="fa-solid fa-times"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <p style="font-size:11px;color:var(--muted);margin-top:8px;">
                        <i class="fa-regular fa-info-circle"></i> Cliquez sur la croix pour supprimer une vidéo
                    </p>
                </div>
            @endif

            <!-- Ajout de nouvelles photos -->
            <div style="margin-top:16px;padding:16px;background:#F7F9FC;border-radius:10px;border:1px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;">
                    <i class="fa-regular fa-image" style="color:var(--rust);"></i> Ajouter des photos
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à 10 photos (format JPG, PNG, JPEG - max 5 Mo chacune)
                </p>
                <input type="file" name="images[]" accept="image/*" multiple 
                       style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
                @error('images.*')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Ajout de nouvelles vidéos -->
            <div style="margin-top:16px;padding:16px;background:#F7F9FC;border-radius:10px;border:1px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;">
                    <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Ajouter des vidéos
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à 3 vidéos (format MP4, MOV, AVI - max 20 Mo chacune)
                </p>
                <input type="file" name="videos[]" accept="video/*" multiple 
                       style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;">
                @error('videos.*')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Boutons -->
            <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:12px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-rust" id="submitBtn">
                    <i class="fa-solid fa-save"></i> Mettre à jour
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

    // Soumission du formulaire
    document.getElementById('bienForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mise à jour...';
        btn.disabled = true;
    });

    // Initialisation
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