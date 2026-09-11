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

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:clamp(16px, 2vw, 24px);">
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

        @if(session('success'))
            <div style="padding:12px 16px;background:#E8F5E9;border-radius:10px;border:1px solid #C8E6C9;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
                <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i>
                <span style="color:#1E7A47;font-size:13px;">{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('agence.biens.update', $bien->slug) }}" enctype="multipart/form-data" id="bienForm">
            @csrf
            @method('PUT')

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
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            Le type de bien détermine les critères obligatoires.
                        </div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Type de contrat <span style="color:#C62828;">*</span>
                        </label>
                        <select name="type_contrat" id="type_contrat" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
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
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            Vente ou Location.
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Prix (FCFA) <span style="color:#C62828;">*</span>
                        </label>
                        <input type="number" name="prix" value="{{ old('prix', $bien->prix) }}" 
                               placeholder="Ex: 250000000"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('prix')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            <span id="prixHint">Prix de vente ou loyer mensuel selon le contrat.</span>
                        </div>
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
                        <input type="text" name="quartier" value="{{ old('quartier', $bien->quartier) }}" 
                               placeholder="Ex: Almadies, Ngor, Mermoz"
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
                               placeholder="Ex: Rue des Almadies, Dakar"
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
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    <span id="caractLabel">Renseignez les caractéristiques du bien.</span>
                </p>

                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            <span id="chambresLabel">Chambres <span style="color:#C62828;">*</span></span>
                        </label>
                        <input type="number" name="nombre_chambres" id="nombre_chambres" value="{{ old('nombre_chambres', $bien->nombre_chambres) }}" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                        @error('nombre_chambres')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            <span id="chambresHint">Nombre de chambres.</span>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            <span id="sdbLabel">Salles de bain <span style="color:#C62828;">*</span></span>
                        </label>
                        <input type="number" name="nombre_salles_bain" id="nombre_salles_bain" value="{{ old('nombre_salles_bain', $bien->nombre_salles_bain) }}" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                        @error('nombre_salles_bain')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            <span id="sdbHint">Nombre de salles de bain.</span>
                        </div>
                    </div>
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Surface (m²) <span style="color:#C62828;">*</span>
                        </label>
                        <input type="number" step="0.01" name="surface" id="surface" value="{{ old('surface', $bien->surface) }}" 
                               placeholder="Ex: 150" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('surface')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            <span id="surfaceHint">Surface habitable en m².</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ Équipements COMPLETS -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:clamp(14px, 1vw, 15px);margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-cogs" style="color:var(--rust);"></i> Équipements
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    <span id="equipLabel">Sélectionnez les équipements disponibles dans ce bien.</span>
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
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;{{ old('climatisation', $bien->climatisation ?? false) ? 'background:var(--rust-soft);border-color:var(--rust);' : '' }}">
                        <input type="checkbox" name="climatisation" value="1" {{ old('climatisation', $bien->climatisation ?? false) ? 'checked' : '' }}>
                        <i class="fa-solid fa-snowflake" style="color:{{ old('climatisation', $bien->climatisation ?? false) ? 'var(--rust)' : 'var(--muted)' }};"></i> Climatisation
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;{{ old('balcon', $bien->balcon ?? false) ? 'background:var(--rust-soft);border-color:var(--rust);' : '' }}">
                        <input type="checkbox" name="balcon" value="1" {{ old('balcon', $bien->balcon ?? false) ? 'checked' : '' }}>
                        <i class="fa-solid fa-umbrella" style="color:{{ old('balcon', $bien->balcon ?? false) ? 'var(--rust)' : 'var(--muted)' }};"></i> Balcon
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;{{ old('jardin', $bien->jardin ?? false) ? 'background:var(--rust-soft);border-color:var(--rust);' : '' }}">
                        <input type="checkbox" name="jardin" value="1" {{ old('jardin', $bien->jardin ?? false) ? 'checked' : '' }}>
                        <i class="fa-solid fa-tree" style="color:{{ old('jardin', $bien->jardin ?? false) ? 'var(--rust)' : 'var(--muted)' }};"></i> Jardin
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;{{ old('piscine', $bien->piscine ?? false) ? 'background:var(--rust-soft);border-color:var(--rust);' : '' }}">
                        <input type="checkbox" name="piscine" value="1" {{ old('piscine', $bien->piscine ?? false) ? 'checked' : '' }}>
                        <i class="fa-solid fa-water" style="color:{{ old('piscine', $bien->piscine ?? false) ? 'var(--rust)' : 'var(--muted)' }};"></i> Piscine
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;{{ old('ascenseur', $bien->ascenseur ?? false) ? 'background:var(--rust-soft);border-color:var(--rust);' : '' }}">
                        <input type="checkbox" name="ascenseur" value="1" {{ old('ascenseur', $bien->ascenseur ?? false) ? 'checked' : '' }}>
                        <i class="fa-solid fa-elevator" style="color:{{ old('ascenseur', $bien->ascenseur ?? false) ? 'var(--rust)' : 'var(--muted)' }};"></i> Ascenseur
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;{{ old('securite', $bien->securite ?? false) ? 'background:var(--rust-soft);border-color:var(--rust);' : '' }}">
                        <input type="checkbox" name="securite" value="1" {{ old('securite', $bien->securite ?? false) ? 'checked' : '' }}>
                        <i class="fa-solid fa-shield-halved" style="color:{{ old('securite', $bien->securite ?? false) ? 'var(--rust)' : 'var(--muted)' }};"></i> Sécurité 24h/24
                    </label>
                </div>

                <div id="terrainNote" style="display:none;margin-top:8px;padding:8px 12px;background:#FFF8E1;border-radius:8px;font-size:12px;color:#E65100;">
                    <i class="fa-solid fa-info-circle"></i> Les équipements ne sont pas applicables pour un terrain.
                </div>
            </div>

            <!-- ✅ Description avec compteur de caractères -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:clamp(14px, 1vw, 15px);margin-bottom:8px;color:var(--text-soft);">
                    <i class="fa-solid fa-align-left" style="color:var(--rust);"></i> Description <span style="color:#C62828;">*</span>
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:8px;">
                    Décrivez votre bien en détail. Plus votre description est précise, meilleures seront les propositions.
                </p>
                <textarea name="description" rows="4" 
                          placeholder="Ex: Magnifique appartement F3 de 100m² à Liberté 6, avec 3 chambres, 2 salles de bain, parking sécurisé, climatisé, proche des transports en commun..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;" 
                          required>{{ old('description', $bien->description) }}</textarea>
                @error('description')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">
                        <i class="fa-solid fa-exclamation-circle"></i> {{ $message }}
                    </small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;display:flex;justify-content:space-between;align-items:center;">
                    <span><i class="fa-regular fa-info-circle"></i> Minimum 20 caractères</span>
                    <span id="descriptionCounter" style="font-weight:500;">
                        <span id="charCount" style="color:{{ strlen(old('description', $bien->description)) >= 20 ? '#1E7A47' : '#C62828' }};">{{ strlen(old('description', $bien->description)) }}</span> / 20 caractères
                    </span>
                </div>
            </div>

            <!-- ==================== GESTION DES MÉDIAS ==================== -->
            <!-- Photos existantes -->
            @php
                $images = $bien->medias->where('type_media', 'image');
            @endphp
            @if($images->count() > 0)
                <div style="margin-top:20px;padding:clamp(14px, 1.5vw, 20px);background:#F7F9FC;border-radius:10px;border:1px solid var(--border);">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                        <h4 style="font-family:var(--display);font-size:clamp(13px, 0.9vw, 14px);margin:0;">
                            <i class="fa-regular fa-image" style="color:var(--rust);"></i> Photos actuelles
                            <span style="font-size:11px;color:var(--muted);font-weight:400;">({{ $images->count() }}/10)</span>
                        </h4>
                    </div>
                    <div id="mediaContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:12px;">
                        @foreach($images as $media)
                            <div id="media-{{ $media->id }}" style="position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);aspect-ratio:1/1;background:#F0F2F5;">
                                <img src="{{ asset('storage/' . $media->fichier) }}" 
                                     alt="Photo" 
                                     style="width:100%;height:100%;object-fit:cover;">
                                <button type="button" 
                                        onclick="supprimerMedia({{ $media->id }}, this)"
                                        style="position:absolute;top:6px;right:6px;width:28px;height:28px;border-radius:50%;border:none;background:rgba(0,0,0,0.7);color:#fff;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;">
                                    <i class="fa-solid fa-times"></i>
                                </button>
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
                <div style="margin-top:16px;padding:clamp(14px, 1.5vw, 20px);background:#F7F9FC;border-radius:10px;border:1px solid var(--border);">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:12px;">
                        <h4 style="font-family:var(--display);font-size:clamp(13px, 0.9vw, 14px);margin:0;">
                            <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Vidéos actuelles
                            <span style="font-size:11px;color:var(--muted);font-weight:400;">({{ $videos->count() }}/1)</span>
                        </h4>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
                        @foreach($videos as $media)
                            <div id="media-{{ $media->id }}" style="position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);background:#000;aspect-ratio:16/9;">
                                <video src="{{ asset('storage/' . $media->fichier) }}" 
                                       style="width:100%;height:100%;object-fit:cover;"
                                       muted>
                                </video>
                                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:#fff;font-size:32px;opacity:0.5;pointer-events:none;">
                                    <i class="fa-solid fa-play"></i>
                                </div>
                                <button type="button" 
                                        onclick="supprimerMedia({{ $media->id }}, this)"
                                        style="position:absolute;top:6px;right:6px;width:28px;height:28px;border-radius:50%;border:none;background:rgba(0,0,0,0.7);color:#fff;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;transition:all 0.2s;">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    <p style="font-size:11px;color:var(--muted);margin-top:8px;">
                        <i class="fa-regular fa-info-circle"></i> Cliquez sur la croix pour supprimer une vidéo
                    </p>
                </div>
            @endif

            <!-- ✅ AJOUT DE NOUVELLES PHOTOS - SANS RÈGLES DE VALIDATION -->
            <div style="margin-top:16px;padding:clamp(14px, 1.5vw, 20px);background:#F7F9FC;border-radius:10px;border:2px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:clamp(13px, 0.9vw, 14px);margin-bottom:8px;">
                    <i class="fa-regular fa-image" style="color:var(--rust);"></i> Ajouter des photos
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à <strong>10 photos</strong> (format JPG, PNG, JPEG, WebP - max 5 Mo chacune)
                </p>
                <input type="file" name="images[]" accept="image/*" multiple 
                       id="imageInput"
                       style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
                
                <div id="imagePreviewContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:10px;margin-top:12px;display:none;"></div>
                
                <!-- ✅ Ne pas afficher d'erreurs de validation pour les images ici car le contrôleur les gère -->
            </div>

            <!-- ✅ AJOUT DE NOUVELLES VIDÉOS - SANS RÈGLES DE VALIDATION -->
            <div style="margin-top:16px;padding:clamp(14px, 1.5vw, 20px);background:#F7F9FC;border-radius:10px;border:2px dashed var(--border);">
                <h4 style="font-family:var(--display);font-size:clamp(13px, 0.9vw, 14px);margin-bottom:8px;">
                    <i class="fa-regular fa-circle-play" style="color:var(--rust);"></i> Ajouter des vidéos
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ajoutez jusqu'à <strong>1 vidéo</strong> (format MP4, MOV, AVI - max 20 Mo)
                </p>
                <input type="file" name="videos[]" accept="video/*" multiple 
                       id="videoInput"
                       style="width:100%;padding:8px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;background:#fff;">
                
                <div id="videoPreviewContainer" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-top:12px;display:none;"></div>
                
                <!-- ✅ Ne pas afficher d'erreurs de validation pour les vidéos ici car le contrôleur les gère -->
            </div>

            <!-- Boutons -->
            <div style="margin-top:24px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:12px;flex-wrap:wrap;">
                <button type="submit" class="btn btn-rust" id="submitBtn" style="padding:10px 24px;border:none;border-radius:10px;background:var(--rust);color:#fff;font-weight:600;cursor:pointer;transition:background 0.2s;display:inline-flex;align-items:center;gap:8px;font-size:14px;">
                    <i class="fa-solid fa-save"></i> Mettre à jour
                </button>
                <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost" style="padding:10px 24px;border-radius:10px;border:1px solid var(--border);background:transparent;color:var(--text-soft);font-weight:600;cursor:pointer;transition:all 0.2s;display:inline-flex;align-items:center;gap:8px;font-size:14px;text-decoration:none;">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // ============================================
    // ADAPTATION SELON LE TYPE DE BIEN
    // ============================================
    document.getElementById('type_bien')?.addEventListener('change', function() {
        const type = this.value;
        
        const equipementsContainer = document.getElementById('equipementsContainer');
        const terrainNote = document.getElementById('terrainNote');
        const chambresInput = document.getElementById('nombre_chambres');
        const sdbInput = document.getElementById('nombre_salles_bain');
        const surfaceInput = document.getElementById('surface');
        const chambresLabel = document.getElementById('chambresLabel');
        const sdbLabel = document.getElementById('sdbLabel');
        const chambresHint = document.getElementById('chambresHint');
        const sdbHint = document.getElementById('sdbHint');
        const surfaceHint = document.getElementById('surfaceHint');
        const caracteristiquesLabel = document.getElementById('caractLabel');
        const equipLabel = document.getElementById('equipLabel');
        const prixHint = document.getElementById('prixHint');
        
        chambresInput.disabled = false;
        sdbInput.disabled = false;
        chambresInput.removeAttribute('required');
        sdbInput.removeAttribute('required');
        surfaceInput.setAttribute('required', 'required');
        
        const isTerrain = type === 'terrain';
        const isLocal = type === 'local_commercial' || type === 'bureau';
        const isResidentiel = type === 'appartement' || type === 'maison' || type === 'villa' || type === 'immeuble';
        const isChambre = type === 'chambre' || type === 'studio';
        
        if (isTerrain) {
            equipementsContainer.style.display = 'none';
            terrainNote.style.display = 'block';
            chambresInput.value = 0;
            chambresInput.disabled = true;
            sdbInput.value = 0;
            sdbInput.disabled = true;
            chambresLabel.innerHTML = 'Chambres <span style="color:#8A91A0;font-weight:400;">(N/A)</span>';
            sdbLabel.innerHTML = 'Salles de bain <span style="color:#8A91A0;font-weight:400;">(N/A)</span>';
            chambresHint.textContent = 'Non applicable pour un terrain.';
            sdbHint.textContent = 'Non applicable pour un terrain.';
            surfaceHint.textContent = 'Surface du terrain en m².';
            caracteristiquesLabel.textContent = 'Un terrain n\'a pas de chambres ni de salles de bain.';
            equipLabel.textContent = 'Les équipements ne sont pas applicables pour un terrain.';
            surfaceInput.setAttribute('required', 'required');
            prixHint.textContent = 'Prix de vente du terrain.';
        } else if (isLocal) {
            equipementsContainer.style.display = 'grid';
            terrainNote.style.display = 'none';
            chambresInput.disabled = false;
            sdbInput.disabled = false;
            chambresLabel.innerHTML = 'Chambres <span style="color:#8A91A0;font-weight:400;">(optionnel)</span>';
            sdbLabel.innerHTML = 'Salles de bain <span style="color:#8A91A0;font-weight:400;">(optionnel)</span>';
            chambresHint.textContent = 'Peuvent être utiles pour un local avec logement.';
            sdbHint.textContent = 'Peuvent être utiles pour un local avec logement.';
            surfaceHint.textContent = 'Surface du local en m².';
            caracteristiquesLabel.textContent = 'Renseignez les caractéristiques du local.';
            equipLabel.textContent = 'Sélectionnez les équipements disponibles.';
            chambresInput.removeAttribute('required');
            sdbInput.removeAttribute('required');
            surfaceInput.setAttribute('required', 'required');
            prixHint.textContent = 'Prix de vente ou loyer selon le contrat.';
        } else if (isResidentiel || isChambre) {
            equipementsContainer.style.display = 'grid';
            terrainNote.style.display = 'none';
            chambresInput.disabled = false;
            sdbInput.disabled = false;
            chambresLabel.innerHTML = 'Chambres <span style="color:#C62828;">*</span>';
            sdbLabel.innerHTML = 'Salles de bain <span style="color:#C62828;">*</span>';
            chambresHint.textContent = 'Nombre de chambres du bien.';
            sdbHint.textContent = 'Nombre de salles de bain.';
            surfaceHint.textContent = 'Surface habitable en m².';
            caracteristiquesLabel.textContent = 'Renseignez les caractéristiques du bien.';
            equipLabel.textContent = 'Sélectionnez les équipements disponibles dans ce bien.';
            chambresInput.setAttribute('required', 'required');
            sdbInput.setAttribute('required', 'required');
            surfaceInput.setAttribute('required', 'required');
            prixHint.textContent = 'Prix de vente ou loyer selon le contrat.';
        } else {
            equipementsContainer.style.display = 'grid';
            terrainNote.style.display = 'none';
            chambresInput.disabled = false;
            sdbInput.disabled = false;
            chambresLabel.innerHTML = 'Chambres <span style="color:#C62828;">*</span>';
            sdbLabel.innerHTML = 'Salles de bain <span style="color:#C62828;">*</span>';
            chambresHint.textContent = 'Nombre de chambres.';
            sdbHint.textContent = 'Nombre de salles de bain.';
            surfaceHint.textContent = 'Surface en m².';
            caracteristiquesLabel.textContent = 'Renseignez les caractéristiques du bien.';
            equipLabel.textContent = 'Sélectionnez les équipements disponibles.';
            chambresInput.setAttribute('required', 'required');
            sdbInput.setAttribute('required', 'required');
            surfaceInput.setAttribute('required', 'required');
            prixHint.textContent = 'Prix de vente ou loyer selon le contrat.';
        }
    });

    // ============================================
    // ADAPTATION SELON LE TYPE DE CONTRAT
    // ============================================
    document.getElementById('type_contrat')?.addEventListener('change', function() {
        const value = this.value;
        const prixHint = document.getElementById('prixHint');
        
        if (value === 'location') {
            prixHint.textContent = 'Loyer mensuel en FCFA.';
        } else if (value === 'vente') {
            prixHint.textContent = 'Prix de vente en FCFA.';
        } else {
            prixHint.textContent = 'Prix de vente ou loyer selon le contrat.';
        }
    });

    // ============================================
    // PRÉVISUALISATION DES IMAGES
    // ============================================
    document.getElementById('imageInput')?.addEventListener('change', function(e) {
        const container = document.getElementById('imagePreviewContainer');
        container.innerHTML = '';
        container.style.display = 'grid';
        
        const files = Array.from(this.files);
        
        if (files.length === 0) {
            container.style.display = 'none';
            return;
        }
        
        if (files.length > 10) {
            alert('⚠️ Vous ne pouvez sélectionner que 10 photos maximum.');
            this.value = '';
            container.style.display = 'none';
            return;
        }
        
        files.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(event) {
                const div = document.createElement('div');
                div.style.cssText = 'position:relative;border-radius:8px;overflow:hidden;border:1px solid var(--border);aspect-ratio:1/1;background:#F0F2F5;';
                div.innerHTML = `
                    <img src="${event.target.result}" alt="Aperçu ${index + 1}" style="width:100%;height:100%;object-fit:cover;">
                    <button type="button" onclick="this.parentElement.remove();" 
                            style="position:absolute;top:6px;right:6px;width:24px;height:24px;border-radius:50%;border:none;background:rgba(0,0,0,0.7);color:#fff;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;">
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
    });

    // ============================================
    // PRÉVISUALISATION DES VIDÉOS
    // ============================================
    document.getElementById('videoInput')?.addEventListener('change', function(e) {
        const container = document.getElementById('videoPreviewContainer');
        container.innerHTML = '';
        container.style.display = 'grid';
        
        const files = Array.from(this.files);
        
        if (files.length === 0) {
            container.style.display = 'none';
            return;
        }
        
        if (files.length > 1) {
            alert('⚠️ Vous ne pouvez sélectionner qu\'1 vidéo maximum.');
            this.value = '';
            container.style.display = 'none';
            return;
        }
        
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
                    <button type="button" onclick="this.parentElement.remove();" 
                            style="position:absolute;top:6px;right:6px;width:24px;height:24px;border-radius:50%;border:none;background:rgba(0,0,0,0.7);color:#fff;cursor:pointer;font-size:12px;display:flex;align-items:center;justify-content:center;">
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
    });

    // ============================================
    // SUPPRESSION DE MÉDIA AVEC AJAX
    // ============================================
    function supprimerMedia(mediaId, element) {
        if (!confirm('Voulez-vous vraiment supprimer ce média ?')) {
            return;
        }

        const parent = element.closest('div[id^="media-"]');
        if (!parent) {
            alert('Erreur: impossible de trouver le média.');
            return;
        }

        const originalHtml = parent.innerHTML;
        parent.style.opacity = '0.5';
        parent.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--muted);"><i class="fa-solid fa-spinner fa-spin"></i></div>';

        const token = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value;

        fetch(`/agence/medias/${mediaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                parent.remove();
                showToast('Média supprimé avec succès', 'success');
            } else {
                parent.innerHTML = originalHtml;
                parent.style.opacity = '1';
                alert('Erreur: ' + (data.message || ''));
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            parent.innerHTML = originalHtml;
            parent.style.opacity = '1';
            alert('Une erreur est survenue. Veuillez réessayer.');
        });
    }

    // ============================================
    // TOAST UNIQUE
    // ============================================
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        const colors = {
            success: '#1E7A47',
            error: '#C62828',
            warning: '#E65100',
            info: '#0D47A1'
        };
        toast.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: ${colors[type] || colors.success};
            color: #fff;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            z-index: 9999;
            box-shadow: 0 8px 32px rgba(0,0,0,0.2);
            animation: slideInRight 0.4s ease;
            max-width: 380px;
            width: 90%;
        `;
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-triangle-exclamation',
            info: 'fa-info-circle'
        };
        toast.innerHTML = `
            <i class="fa-solid ${icons[type] || icons.success}" style="margin-right:8px;"></i>
            ${message}
        `;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    // ============================================
    // SOUMISSION DU FORMULAIRE
    // ============================================
    document.getElementById('bienForm')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mise à jour...';
        btn.disabled = true;
    });

    // ============================================
    // INITIALISATION
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('type_bien')?.dispatchEvent(new Event('change'));
        document.getElementById('type_contrat')?.dispatchEvent(new Event('change'));
        
        const descriptionTextarea = document.querySelector('textarea[name="description"]');
        const charCount = document.getElementById('charCount');
        
        if (descriptionTextarea && charCount) {
            function updateCharCount() {
                const length = descriptionTextarea.value.length;
                const color = length >= 20 ? '#1E7A47' : '#C62828';
                charCount.textContent = length;
                charCount.style.color = color;
            }
            
            descriptionTextarea.addEventListener('input', updateCharCount);
        }
    });

    // ============================================
    // ANIMATIONS CSS
    // ============================================
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection

@push('styles')
<style>
    label:has(input[type="checkbox"]) {
        transition: all 0.2s;
        cursor: pointer;
    }

    label:has(input[type="checkbox"]:hover) {
        border-color: var(--rust);
    }

    label:has(input[type="checkbox"]:checked) {
        background: var(--rust-soft);
        border-color: var(--rust);
    }

    label:has(input[type="checkbox"]:checked) i {
        color: var(--rust) !important;
    }

    label:has(input[type="checkbox"]) input[type="checkbox"] {
        width: 16px;
        height: 16px;
        cursor: pointer;
        flex-shrink: 0;
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

    #equipementsContainer, #terrainNote {
        transition: all 0.3s ease;
    }

    input:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        background: #F7F9FC;
    }

    #charCount {
        font-weight: 700;
        transition: color 0.2s;
    }

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
        [style*="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr))"] {
            grid-template-columns: 1fr 1fr !important;
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
        [style*="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr))"] {
            grid-template-columns: 1fr !important;
        }
        [style*="margin-top:24px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:12px;flex-wrap:wrap;"] {
            flex-direction: column !important;
        }
        label:has(input[type="checkbox"]) {
            font-size: 12px !important;
            padding: 6px 10px !important;
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