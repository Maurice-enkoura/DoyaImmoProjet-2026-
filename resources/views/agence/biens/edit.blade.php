@extends('layouts.dashboard-agence')

@section('title', 'Modifier le bien — DoyaImmo')
@section('page_title', 'Modifier le bien')
@section('page_sub', 'Modifiez les informations de votre bien')

@section('content')
<div class="view active bien-edit">

    @php
        $images = $bien->medias->where('type_media', 'image');
        $videos = $bien->medias->where('type_media', 'video');
    @endphp

    {{-- ═══ STEPPER ═══ --}}
    <div class="ios-stepper">
        <div class="ios-stepper__track">
            <div class="ios-stepper__fill" id="stepperFill" style="width: 25%;"></div>
        </div>
        <div class="ios-stepper__labels">
            <span class="is-active" data-step="1">1. Infos</span>
            <span data-step="2">2. Lieu</span>
            <span data-step="3">3. Détails</span>
            <span data-step="4">4. Photos</span>
        </div>
    </div>

    {{-- ═══ ALERTES ═══ --}}
    @if($errors->any())
        <div class="ios-alert ios-alert--error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Veuillez corriger les erreurs :</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="ios-alert ios-alert--success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <form method="POST"
          action="{{ route('agence.biens.update', $bien->slug) }}"
          enctype="multipart/form-data"
          id="bienForm">

        @csrf
        @method('PUT')

        {{-- ═══════════════════════════════════════════
             ÉTAPE 1 — INFOS PRINCIPALES
        ═══════════════════════════════════════════ --}}
        <div class="ios-step is-active" data-step="1">

            <div class="ios-section-title">
                <i class="fa-solid fa-info-circle"></i> Informations principales
            </div>
            <p class="ios-section-sub">
                Ces infos seront visibles par les clients
            </p>

            <div class="ios-form-grid">
                <div class="ios-field ios-field--full">
                    <label>Titre du bien <span class="req">*</span></label>
                    <input type="text" name="titre" value="{{ old('titre', $bien->titre) }}"
                           class="ios-input" required>
                    @error('titre') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="ios-field">
                    <label>Type de bien <span class="req">*</span></label>
                    <select name="type_bien" id="type_bien" class="ios-input" required>
                        <option value="">Sélectionnez un type</option>
                        @foreach(\App\Enums\TypeBienEnum::cases() as $type)
                            <option value="{{ $type->value }}" {{ old('type_bien', $bien->type_bien->value) == $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_bien') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="ios-field">
                    <label>Type de contrat <span class="req">*</span></label>
                    <select name="type_contrat" id="type_contrat" class="ios-input" required>
                        <option value="">Sélectionnez un contrat</option>
                        @foreach(\App\Enums\TypeContratEnum::cases() as $type)
                            <option value="{{ $type->value }}" {{ old('type_contrat', $bien->type_contrat->value) == $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_contrat') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="ios-field ios-field--full">
                    <label>Prix (FCFA) <span class="req">*</span></label>
                    <input type="number" name="prix" value="{{ old('prix', $bien->prix) }}"
                           class="ios-input" required>
                    @error('prix') <small class="err">{{ $message }}</small> @enderror
                    <div class="ios-hint" id="prixHint">
                        <i class="fa-regular fa-info-circle"></i>
                        Prix de vente ou loyer mensuel selon le contrat
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             ÉTAPE 2 — LOCALISATION
        ═══════════════════════════════════════════ --}}
        <div class="ios-step" data-step="2">

            <div class="ios-section-title">
                <i class="fa-solid fa-location-dot"></i> Localisation
            </div>
            <p class="ios-section-sub">
                Où se situe le bien ?
            </p>

            <div class="ios-form-grid">
                <div class="ios-field">
                    <label>Quartier <span class="req">*</span></label>
                    <input type="text" name="quartier" value="{{ old('quartier', $bien->quartier) }}"
                           placeholder="Ex : Almadies, Ngor, Mermoz"
                           class="ios-input" required>
                    @error('quartier') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="ios-field">
                    <label>Adresse <span class="req">*</span></label>
                    <input type="text" name="adresse" value="{{ old('adresse', $bien->adresse) }}"
                           placeholder="Ex : Rue des Almadies, Dakar"
                           class="ios-input" required>
                    @error('adresse') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="ios-field ios-field--full">
                    <label>Description <span class="req">*</span></label>
                    <textarea name="description" rows="5"
                              class="ios-input" required>{{ old('description', $bien->description) }}</textarea>
                    @error('description') <small class="err">{{ $message }}</small> @enderror
                    <div class="ios-counter">
                        <span><i class="fa-regular fa-info-circle"></i> Minimum 20 caractères</span>
                        <span id="charCount" style="color:{{ strlen(old('description', $bien->description)) >= 20 ? '#1E7A47' : '#C62828' }};font-weight:700;">
                            {{ strlen(old('description', $bien->description)) }} / 20
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             ÉTAPE 3 — CARACTÉRISTIQUES + ÉQUIPEMENTS
        ═══════════════════════════════════════════ --}}
        <div class="ios-step" data-step="3">

            <div class="ios-section-title">
                <i class="fa-solid fa-sliders-h"></i> Caractéristiques
            </div>
            <p class="ios-section-sub" id="caractLabel">
                Renseignez les caractéristiques du bien
            </p>

            <div class="ios-form-grid ios-form-grid--3">
                <div class="ios-field">
                    <label id="chambresLabel">Chambres <span class="req">*</span></label>
                    <input type="number" name="nombre_chambres" id="nombre_chambres"
                           value="{{ old('nombre_chambres', $bien->nombre_chambres) }}" min="0"
                           class="ios-input">
                    @error('nombre_chambres') <small class="err">{{ $message }}</small> @enderror
                    <div class="ios-hint">
                        <i class="fa-regular fa-info-circle"></i>
                        <span id="chambresHint">Nombre de chambres du bien</span>
                    </div>
                </div>

                <div class="ios-field">
                    <label id="sdbLabel">Salles de bain <span class="req">*</span></label>
                    <input type="number" name="nombre_salles_bain" id="nombre_salles_bain"
                           value="{{ old('nombre_salles_bain', $bien->nombre_salles_bain) }}" min="0"
                           class="ios-input">
                    @error('nombre_salles_bain') <small class="err">{{ $message }}</small> @enderror
                    <div class="ios-hint">
                        <i class="fa-regular fa-info-circle"></i>
                        <span id="sdbHint">Nombre de salles de bain</span>
                    </div>
                </div>

                <div class="ios-field">
                    <label>Surface (m²) <span class="req">*</span></label>
                    <input type="number" step="0.01" name="surface" id="surface"
                           value="{{ old('surface', $bien->surface) }}" min="0"
                           class="ios-input" required>
                    @error('surface') <small class="err">{{ $message }}</small> @enderror
                    <div class="ios-hint">
                        <i class="fa-regular fa-info-circle"></i>
                        <span id="surfaceHint">Surface habitable en m²</span>
                    </div>
                </div>
            </div>

            {{-- Équipements --}}
            <div class="ios-section-title" id="equipSectionTitle">
                <i class="fa-solid fa-cogs"></i> Équipements
            </div>
            <p class="ios-section-sub" id="equipLabel">
                Sélectionnez les équipements disponibles
            </p>

            <div id="equipementsContainer" class="ios-equipments">
                <label class="ios-equipment">
                    <input type="checkbox" name="parking_disponible" value="1" {{ old('parking_disponible', $bien->parking_disponible) ? 'checked' : '' }}>
                    <i class="fa-solid fa-car"></i>
                    <span>Parking</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="est_meuble" value="1" {{ old('est_meuble', $bien->est_meuble) ? 'checked' : '' }}>
                    <i class="fa-solid fa-couch"></i>
                    <span>Meublé</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="climatisation" value="1" {{ old('climatisation', $bien->climatisation ?? false) ? 'checked' : '' }}>
                    <i class="fa-solid fa-snowflake"></i>
                    <span>Climatisation</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="balcon" value="1" {{ old('balcon', $bien->balcon ?? false) ? 'checked' : '' }}>
                    <i class="fa-solid fa-umbrella"></i>
                    <span>Balcon</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="jardin" value="1" {{ old('jardin', $bien->jardin ?? false) ? 'checked' : '' }}>
                    <i class="fa-solid fa-tree"></i>
                    <span>Jardin</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="piscine" value="1" {{ old('piscine', $bien->piscine ?? false) ? 'checked' : '' }}>
                    <i class="fa-solid fa-water"></i>
                    <span>Piscine</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="ascenseur" value="1" {{ old('ascenseur', $bien->ascenseur ?? false) ? 'checked' : '' }}>
                    <i class="fa-solid fa-elevator"></i>
                    <span>Ascenseur</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="securite" value="1" {{ old('securite', $bien->securite ?? false) ? 'checked' : '' }}>
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Sécurité 24h/24</span>
                </label>
            </div>

            <div id="terrainNote" class="ios-note ios-note--warning" style="display:none;">
                <i class="fa-solid fa-info-circle"></i>
                Les équipements ne sont pas applicables pour un terrain.
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             ÉTAPE 4 — PHOTOS + VIDÉOS
        ═══════════════════════════════════════════ --}}
        <div class="ios-step" data-step="4">

            {{-- Photos actuelles --}}
            @if($images->count() > 0)
                <div class="ios-section-title">
                    <i class="fa-regular fa-image"></i> Photos actuelles
                    <span class="ios-section-count">{{ $images->count() }}/10</span>
                </div>

                <div class="media-preview media-preview--current">
                    @foreach($images as $media)
                        <div id="media-{{ $media->id }}" class="media-thumb">
                            <img src="{{ asset('storage/' . $media->fichier) }}" alt="Photo">
                            <button type="button"
                                    onclick="supprimerMedia({{ $media->id }}, this)"
                                    class="media-thumb__remove">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Vidéos actuelles --}}
            @if($videos->count() > 0)
                <div class="ios-section-title">
                    <i class="fa-regular fa-circle-play"></i> Vidéos actuelles
                    <span class="ios-section-count">{{ $videos->count() }}/1</span>
                </div>

                <div class="media-preview media-preview--video media-preview--current">
                    @foreach($videos as $media)
                        <div id="media-{{ $media->id }}" class="media-thumb media-thumb--video">
                            <video src="{{ asset('storage/' . $media->fichier) }}" muted></video>
                            <div class="media-thumb__play"><i class="fa-solid fa-play"></i></div>
                            <button type="button"
                                    onclick="supprimerMedia({{ $media->id }}, this)"
                                    class="media-thumb__remove">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Ajouter des photos --}}
            <div class="ios-section-title">
                <i class="fa-solid fa-plus-circle"></i> Ajouter des photos
            </div>
            <p class="ios-section-sub">
                Jusqu'à 10 photos · JPG, PNG, WebP · Max 5 Mo
            </p>

            <div class="media-upload">
                <label class="upload-zone" for="imageInput">
                    <input type="file" name="images[]" id="imageInput"
                           accept="image/*" multiple hidden>
                    <div class="upload-zone__icon">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div class="upload-zone__text">
                        <strong>Ajouter des photos</strong>
                        <span>Cliquez pour parcourir</span>
                    </div>
                </label>

                <div id="imagePreviewContainer" class="media-preview"></div>
            </div>

            {{-- Ajouter des vidéos --}}
            <div class="ios-section-title">
                <i class="fa-solid fa-plus-circle"></i> Ajouter une vidéo
            </div>
            <p class="ios-section-sub">
                Jusqu'à 1 vidéo · MP4, MOV · Max 20 Mo
            </p>

            <div class="media-upload">
                <label class="upload-zone" for="videoInput">
                    <input type="file" name="videos[]" id="videoInput"
                           accept="video/*" hidden>
                    <div class="upload-zone__icon">
                        <i class="fa-solid fa-video"></i>
                    </div>
                    <div class="upload-zone__text">
                        <strong>Ajouter une vidéo</strong>
                        <span>Optionnel · 1 vidéo max</span>
                    </div>
                </label>

                <div id="videoPreviewContainer" class="media-preview media-preview--video"></div>
            </div>
        </div>

        {{-- ═══ ACTIONS FIXES ═══ --}}
        <div class="ios-form-actions" id="formActions">
            <button type="button" class="ios-btn ios-btn--ghost" id="prevBtn" style="display:none;">
                <i class="fa-solid fa-chevron-left"></i> Précédent
            </button>
            <button type="button" class="ios-btn ios-btn--primary" id="nextBtn">
                Suivant <i class="fa-solid fa-chevron-right"></i>
            </button>
            <button type="submit" class="ios-btn ios-btn--primary" id="submitBtn" style="display:none;">
                <i class="fa-solid fa-save"></i> Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection


@push('scripts')
<script>
/* ═══════════════════════════════════════════
   WIZARD
   ═══════════════════════════════════════════ */
let currentStep = 1;
const totalSteps = 4;

function goToStep(step) {
    if (step < 1 || step > totalSteps) return;
    currentStep = step;

    document.querySelectorAll('.ios-step').forEach(s => s.classList.remove('is-active'));
    document.querySelector(`.ios-step[data-step="${step}"]`).classList.add('is-active');

    document.getElementById('stepperFill').style.width = ((step / totalSteps) * 100) + '%';

    document.querySelectorAll('.ios-stepper__labels span').forEach(lbl => {
        const s = parseInt(lbl.dataset.step);
        lbl.classList.remove('is-active', 'is-done');
        if (s === step) lbl.classList.add('is-active');
        else if (s < step) lbl.classList.add('is-done');
    });

    document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'inline-flex';
    document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'inline-flex';
    document.getElementById('submitBtn').style.display = step === totalSteps ? 'inline-flex' : 'none';

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.getElementById('nextBtn')?.addEventListener('click', () => {
    const currentEl = document.querySelector(`.ios-step[data-step="${currentStep}"]`);
    const requiredInputs = currentEl.querySelectorAll('[required]:not([disabled])');
    let valid = true;

    requiredInputs.forEach(inp => {
        if (!inp.value || !inp.value.trim()) {
            inp.classList.add('shake', 'is-invalid');
            setTimeout(() => inp.classList.remove('shake'), 300);
            valid = false;
        } else {
            inp.classList.remove('is-invalid');
        }
    });

    if (valid) goToStep(currentStep + 1);
});

document.getElementById('prevBtn')?.addEventListener('click', () => goToStep(currentStep - 1));

/* ═══════════════════════════════════════════
   ADAPTATION PAR TYPE DE BIEN
   ═══════════════════════════════════════════ */
document.getElementById('type_bien')?.addEventListener('change', function () {
    const type = this.value;

    const equipementsContainer = document.getElementById('equipementsContainer');
    const equipSectionTitle = document.getElementById('equipSectionTitle');
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
    const isResidentiel = ['appartement', 'maison', 'villa', 'immeuble'].includes(type);
    const isChambre = type === 'chambre' || type === 'studio';

    if (isTerrain) {
        equipementsContainer.style.display = 'none';
        equipSectionTitle.style.display = 'none';
        terrainNote.style.display = 'flex';

        chambresInput.value = 0;
        chambresInput.disabled = true;
        sdbInput.value = 0;
        sdbInput.disabled = true;

        chambresLabel.innerHTML = 'Chambres <small>(N/A)</small>';
        sdbLabel.innerHTML = 'Salles de bain <small>(N/A)</small>';
        chambresHint.textContent = 'Non applicable pour un terrain';
        sdbHint.textContent = 'Non applicable pour un terrain';
        surfaceHint.textContent = 'Surface du terrain en m²';
        caracteristiquesLabel.textContent = 'Un terrain n\'a pas de chambres ni de salles de bain';
        prixHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Prix de vente du terrain';

    } else if (isLocal) {
        equipementsContainer.style.display = 'grid';
        equipSectionTitle.style.display = 'flex';
        terrainNote.style.display = 'none';

        chambresLabel.innerHTML = 'Chambres <small>(optionnel)</small>';
        sdbLabel.innerHTML = 'Salles de bain <small>(optionnel)</small>';
        chambresHint.textContent = 'Peut être utile pour un local avec logement';
        sdbHint.textContent = 'Peut être utile pour un local avec logement';
        surfaceHint.textContent = 'Surface du local en m²';
        caracteristiquesLabel.textContent = 'Renseignez les caractéristiques du local';
        equipLabel.textContent = 'Sélectionnez les équipements disponibles';
        prixHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Prix de vente ou loyer selon le contrat';

    } else if (isResidentiel || isChambre) {
        equipementsContainer.style.display = 'grid';
        equipSectionTitle.style.display = 'flex';
        terrainNote.style.display = 'none';

        chambresLabel.innerHTML = 'Chambres <span class="req">*</span>';
        sdbLabel.innerHTML = 'Salles de bain <span class="req">*</span>';
        chambresHint.textContent = 'Nombre de chambres du bien';
        sdbHint.textContent = 'Nombre de salles de bain';
        surfaceHint.textContent = 'Surface habitable en m²';
        caracteristiquesLabel.textContent = 'Renseignez les caractéristiques du bien';
        equipLabel.textContent = 'Sélectionnez les équipements disponibles';

        chambresInput.setAttribute('required', 'required');
        sdbInput.setAttribute('required', 'required');
        prixHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Prix de vente ou loyer selon le contrat';
    }
});

/* ═══════════════════════════════════════════
   ADAPTATION PAR CONTRAT
   ═══════════════════════════════════════════ */
document.getElementById('type_contrat')?.addEventListener('change', function () {
    const value = this.value;
    const prixHint = document.getElementById('prixHint');

    if (value === 'location') {
        prixHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Loyer mensuel en FCFA';
    } else if (value === 'vente') {
        prixHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Prix de vente en FCFA';
    } else {
        prixHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Prix de vente ou loyer selon le contrat';
    }
});

/* ═══════════════════════════════════════════
   COMPTEUR DESCRIPTION
   ═══════════════════════════════════════════ */
document.querySelector('textarea[name="description"]')?.addEventListener('input', function () {
    const len = this.value.length;
    const counter = document.getElementById('charCount');
    if (counter) {
        counter.textContent = len + ' / 20';
        counter.style.color = len >= 20 ? '#1E7A47' : '#C62828';
    }
});

/* ═══════════════════════════════════════════
   UPLOAD PHOTOS
   ═══════════════════════════════════════════ */
document.getElementById('imageInput')?.addEventListener('change', function () {
    const container = document.getElementById('imagePreviewContainer');
    const files = Array.from(this.files);

    if (files.length > 10) {
        alert('Maximum 10 photos.');
        this.value = '';
        return;
    }

    container.innerHTML = '';
    container.style.display = files.length === 0 ? 'none' : 'grid';

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'media-thumb';
            div.innerHTML = `
                <img src="${e.target.result}" alt="">
                <div class="media-thumb__name">${file.name.length > 15 ? file.name.substring(0, 15) + '…' : file.name}</div>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

/* ═══════════════════════════════════════════
   UPLOAD VIDÉOS
   ═══════════════════════════════════════════ */
document.getElementById('videoInput')?.addEventListener('change', function () {
    const container = document.getElementById('videoPreviewContainer');
    const files = Array.from(this.files);

    if (files.length > 1) {
        alert('Maximum 1 vidéo.');
        this.value = '';
        return;
    }

    container.innerHTML = '';
    container.style.display = files.length === 0 ? 'none' : 'grid';

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'media-thumb media-thumb--video';
            div.innerHTML = `
                <video src="${e.target.result}" muted></video>
                <div class="media-thumb__play"><i class="fa-solid fa-play"></i></div>
                <div class="media-thumb__name">${file.name.length > 15 ? file.name.substring(0, 15) + '…' : file.name}</div>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});

/* ═══════════════════════════════════════════
   SUPPRESSION MÉDIA AJAX
   ═══════════════════════════════════════════ */
function supprimerMedia(mediaId, element) {
    if (!confirm('Voulez-vous vraiment supprimer ce média ?')) return;

    const parent = element.closest('div[id^="media-"]');
    if (!parent) return;

    parent.style.opacity = '0.5';

    const token = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value;

    fetch(`/agence/medias/${mediaId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            parent.style.transform = 'scale(0.8)';
            parent.style.opacity = '0';
            setTimeout(() => parent.remove(), 200);
        } else {
            parent.style.opacity = '1';
            alert('Erreur : ' + (data.message || ''));
        }
    })
    .catch(() => {
        parent.style.opacity = '1';
        alert('Une erreur est survenue.');
    });
}

/* ═══════════════════════════════════════════
   SUBMIT
   ═══════════════════════════════════════════ */
document.getElementById('bienForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    if (btn) {
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement...';
        btn.disabled = true;
    }
});

/* ═══════════════════════════════════════════
   INIT
   ═══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('type_bien')?.dispatchEvent(new Event('change'));
    document.getElementById('type_contrat')?.dispatchEvent(new Event('change'));
    document.querySelector('textarea[name="description"]')?.dispatchEvent(new Event('input'));
});
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MODIFIER UN BIEN — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.bien-edit {
    --c-success: #1E7A47; --c-success-bg: #E8F5E9;
    --c-warning: #E65100; --c-warning-bg: #FFF8E1;
    --c-danger: #C62828; --c-danger-bg: #FFEBEE;
    --c-info: #0D47A1; --c-info-bg: #E3F2FD;
    --surface: #F7F9FC;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ STEPPER (desktop) ═══ */
.ios-stepper {
    display: block;
    margin-bottom: 24px;
    padding: 0 4px;
    max-width: 720px;
}
.ios-stepper__track {
    height: 4px;
    background: #E5E5EA;
    border-radius: 999px;
    overflow: hidden;
    margin-bottom: 12px;
}
.ios-stepper__fill {
    height: 100%;
    background: linear-gradient(90deg, var(--rust), #d4754a);
    border-radius: 999px;
    transition: width .4s cubic-bezier(.4,0,.2,1);
}
.ios-stepper__labels {
    display: flex;
    justify-content: space-between;
    gap: 4px;
    font-size: 12px;
    font-weight: 600;
    color: var(--muted);
    letter-spacing: -.1px;
}
.ios-stepper__labels span {
    transition: color .3s;
    text-align: center;
    flex: 1;
}
.ios-stepper__labels span.is-active { color: var(--rust); font-weight: 700; }
.ios-stepper__labels span.is-done { color: var(--text-soft); }

/* ═══ ALERT ═══ */
.ios-alert {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 14px;
    margin-bottom: 16px;
    font-size: 13px;
}
.ios-alert--error {
    background: #FFEBEE;
    color: #C62828;
    border: 1px solid #FFCDD2;
}
.ios-alert--success {
    background: #E8F5E9;
    color: #1E7A47;
    border: 1px solid #C8E6C9;
    align-items: center;
}
.ios-alert i { font-size: 18px; flex-shrink: 0; }
.ios-alert strong { display: block; margin-bottom: 4px; font-weight: 700; }
.ios-alert ul { margin: 6px 0 0; padding-left: 18px; }
.ios-alert ul li { margin-bottom: 2px; }

/* ═══ STEP ═══ */
.ios-step { display: block; }
.ios-step:not(.is-active) { display: none; }

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    color: var(--ink);
    margin: 24px 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ios-section-title:first-child { margin-top: 0; }
.ios-section-title i { color: var(--rust); font-size: 16px; }
.ios-section-title .req { color: #C62828; }
.ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: var(--c-warning-bg);
    color: var(--rust);
    border-radius: 999px;
}
.ios-section-sub {
    font-size: 13.5px;
    color: var(--muted);
    margin: 0 0 18px;
    line-height: 1.5;
}

/* ═══ FORM GRID (desktop) ═══ */
.ios-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px 20px;
}
.ios-form-grid--3 {
    grid-template-columns: repeat(3, 1fr);
}
.ios-field--full {
    grid-column: 1 / -1;
}

/* ═══ FIELDS ═══ */
.ios-field { margin-bottom: 0; }
.ios-field label {
    display: block;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-soft);
    margin-bottom: 8px;
}
.ios-field label .req { color: #C62828; }
.ios-field label small {
    color: var(--muted);
    font-weight: 400;
    font-size: 12px;
}

.ios-input {
    width: 100%;
    padding: 13px 16px;
    border: 1px solid var(--border);
    border-radius: 12px;
    font-size: 14.5px;
    font-family: inherit;
    background: #fff;
    color: var(--ink);
    transition: all .15s;
    -webkit-appearance: none;
    appearance: none;
}
.ios-input:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 3px var(--rust-soft);
}
.ios-input.is-invalid { border-color: #C62828; }
.ios-input:disabled {
    background: #EFEFF4;
    color: var(--muted);
    cursor: not-allowed;
}
textarea.ios-input {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
}
select.ios-input {
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath fill='%238A91A0' d='M6 8L2 4h8z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 10px;
    padding-right: 36px;
    cursor: pointer;
}

.ios-hint {
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
    line-height: 1.5;
    display: flex;
    gap: 5px;
    align-items: flex-start;
}
.ios-hint i { font-size: 11.5px; margin-top: 2px; flex-shrink: 0; }

.ios-counter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
}
.ios-counter span:first-child {
    display: inline-flex;
    gap: 5px;
    align-items: center;
}
.ios-counter span:first-child i { font-size: 11.5px; }

.err {
    display: block;
    color: #C62828;
    font-size: 12.5px;
    margin-top: 6px;
    font-weight: 500;
}

/* ═══ ÉQUIPEMENTS (desktop) ═══ */
.ios-equipments {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 14px;
}
.ios-equipment {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    cursor: pointer;
    font-size: 13.5px;
    font-weight: 500;
    color: var(--text-soft);
    transition: all .15s ease;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}
.ios-equipment input { display: none; }
.ios-equipment i {
    font-size: 16px;
    color: var(--muted);
    transition: color .15s;
    width: 20px;
    text-align: center;
    flex-shrink: 0;
}
.ios-equipment span {
    flex: 1;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.ios-equipment:has(input:checked) {
    background: var(--rust-soft);
    border-color: var(--rust);
    color: var(--rust);
    font-weight: 600;
}
.ios-equipment:has(input:checked) i { color: var(--rust); }
.ios-equipment:hover {
    border-color: var(--rust);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,.04);
}

/* ═══ NOTE ═══ */
.ios-note {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    line-height: 1.5;
}
.ios-note--warning {
    background: #FFF8E1;
    color: #E65100;
    border: 1px solid #FFE0B2;
}
.ios-note i { font-size: 16px; flex-shrink: 0; }

/* ═══ UPLOAD MÉDIAS ═══ */
.media-upload {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 22px;
}
.upload-zone {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 32px 24px;
    background: var(--surface);
    border: 2px dashed var(--border);
    border-radius: 14px;
    cursor: pointer;
    transition: all .2s;
    text-align: center;
}
.upload-zone:hover {
    border-color: var(--rust);
    background: var(--rust-soft);
}
.upload-zone__icon {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #fff;
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .12);
}
.upload-zone__text {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.upload-zone__text strong {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    color: var(--ink);
}
.upload-zone__text span {
    font-size: 12.5px;
    color: var(--muted);
}

.media-preview {
    display: none;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 10px;
    margin-top: 16px;
}
.media-preview--current { display: grid; margin-bottom: 22px; }
.media-preview--video {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
}
.media-thumb {
    position: relative;
    aspect-ratio: 1;
    border-radius: 12px;
    overflow: hidden;
    background: #F0F2F5;
    border: 1px solid var(--border);
    transition: transform .2s, opacity .2s;
}
.media-thumb--video { aspect-ratio: 16/9; background: #000; }
.media-thumb img,
.media-thumb video {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.media-thumb__name {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 4px 8px;
    background: rgba(0, 0, 0, .65);
    color: #fff;
    font-size: 10.5px;
    text-align: center;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.media-thumb__play {
    position: absolute;
    top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-size: 26px;
    opacity: .6;
    pointer-events: none;
}
.media-thumb__remove {
    position: absolute;
    top: 6px; right: 6px;
    width: 30px; height: 30px;
    border-radius: 50%;
    border: none;
    background: rgba(0, 0, 0, .7);
    color: #fff;
    cursor: pointer;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .15s;
    -webkit-tap-highlight-color: transparent;
    backdrop-filter: blur(4px);
    z-index: 2;
}
.media-thumb__remove:hover {
    background: rgba(198, 40, 40, .9);
    transform: scale(1.1);
}
.media-thumb__remove:active { transform: scale(.9); }

/* ═══ ACTIONS (desktop) ═══ */
.ios-form-actions {
    display: flex;
    gap: 10px;
    padding-top: 24px;
    margin-top: 24px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 22px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181,80,42,.2);
}
.ios-btn--primary:hover { background: #9A4523; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(181,80,42,.3); }
.ios-btn--primary:active { transform: scale(.97); }
.ios-btn--primary:disabled { opacity: .7; cursor: not-allowed; transform: none; }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); background: var(--surface); }
.ios-btn--ghost:active { transform: scale(.97); }

/* ═══ SHAKE ═══ */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    75% { transform: translateX(6px); }
}
.shake { animation: shake .3s ease; }

/* ═══════════════════════════════════════════════════════════
   DESKTOP : conteneur large
   ═══════════════════════════════════════════════════════════ */
@media (min-width: 1000px) {
    .bien-edit {
        max-width: 960px;
        margin: 0 auto;
        padding: 8px 0 60px;
    }
}

@media (max-width: 999px) and (min-width: 821px) {
    .ios-form-grid,
    .ios-form-grid--3 {
        grid-template-columns: 1fr 1fr;
    }
    .ios-equipments {
        grid-template-columns: repeat(3, 1fr);
    }
    .bien-edit {
        max-width: 720px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .bien-edit {
        background: transparent;
        padding-top: 4px;
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
        max-width: none;
        margin: 0;
    }

    /* ─── STEPPER ─── */
    .ios-stepper {
        display: block;
        margin-bottom: 20px;
        padding: 0 4px;
    }

    /* ─── FORM GRID → 1 colonne ─── */
    .ios-form-grid,
    .ios-form-grid--3 {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .ios-field--full { grid-column: auto; }

    /* ─── SECTION TITLES ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 24px 4px 6px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-sub {
        font-size: 13px;
        color: var(--ios-label-3);
        margin: 0 4px 16px;
    }

    /* ─── FIELDS iOS ─── */
    .ios-field { margin-bottom: 0; }
    .ios-field label {
        font-size: 13px;
        color: var(--ios-label-3);
        font-weight: 500;
        margin-bottom: 6px;
        padding-left: 4px;
    }
    .ios-field label .req { color: var(--rust); font-weight: 700; }

    .ios-input {
        background: var(--ios-card);
        border: none;
        border-radius: 14px;
        padding: 14px 16px;
        font-size: 16px;
        font-weight: 500;
        color: var(--ios-label);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-input:focus {
        box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 0 0 3px rgba(181, 80, 42, .15);
    }
    .ios-input.is-invalid { box-shadow: 0 0 0 2px rgba(198, 40, 40, .3); }
    .ios-input:disabled {
        background: #EFEFF4;
        color: var(--ios-label-3);
        box-shadow: none;
    }
    textarea.ios-input {
        min-height: 120px;
        padding: 16px;
        line-height: 1.55;
        font-size: 16px;
    }
    select.ios-input {
        padding: 14px 36px 14px 16px;
        background-position: right 16px center;
        font-size: 16px;
        font-weight: 500;
    }

    .ios-hint {
        font-size: 12.5px;
        color: var(--ios-label-3);
        margin-top: 6px;
        padding-left: 4px;
    }

    .ios-counter {
        font-size: 12px;
        padding-left: 4px;
        margin-top: 6px;
    }

    .err {
        font-size: 12.5px;
        margin-top: 6px;
        padding-left: 4px;
    }

    /* ─── ÉQUIPEMENTS iOS ─── */
    .ios-equipments {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .ios-equipment {
        background: var(--ios-card);
        border: 1.5px solid transparent;
        border-radius: 14px;
        padding: 14px 12px;
        flex-direction: column;
        gap: 8px;
        text-align: center;
        font-size: 12.5px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        min-height: 84px;
        justify-content: center;
    }
    .ios-equipment i { font-size: 22px; width: auto; }
    .ios-equipment span { white-space: normal; font-size: 12.5px; line-height: 1.3; }
    .ios-equipment:has(input:checked) {
        background: linear-gradient(135deg, #FFF4EC, #FFE8D9);
        border-color: var(--rust);
        box-shadow: 0 4px 12px rgba(181,80,42,.15);
    }
    .ios-equipment:hover { transform: none; }

    /* ─── NOTE iOS ─── */
    .ios-note {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid var(--c-warning);
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 13.5px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-note--warning { color: var(--ios-label-2); }

    /* ─── UPLOAD iOS ─── */
    .media-upload {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .upload-zone {
        background: #EFEFF4;
        border: 2px dashed transparent;
        border-radius: 14px;
        padding: 22px 16px;
        gap: 12px;
    }
    .upload-zone:active {
        background: #E5E5EA;
        transform: none;
    }
    .upload-zone__icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        font-size: 24px;
        box-shadow: 0 6px 16px rgba(181, 80, 42, .3);
    }
    .upload-zone__text strong { font-size: 15px; font-weight: 600; color: var(--ios-label); }
    .upload-zone__text span { font-size: 12.5px; color: var(--ios-label-3); }

    .media-preview { gap: 6px; }
    .media-thumb { border-radius: 12px; border: none; }
    .media-thumb__remove {
        width: 30px; height: 30px;
        font-size: 13px;
    }

    /* ─── ACTIONS FIXES ─── */
    .ios-form-actions {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(242, 242, 247, 0.95);
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        border-top: 0.5px solid var(--ios-separator);
        padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
        margin: 0;
        gap: 10px;
        z-index: 100;
        border-radius: 0;
    }
    .ios-btn {
        padding: 14px 20px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 14px;
        flex: 1;
        letter-spacing: -.1px;
    }
    .ios-btn--primary {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .ios-btn--primary:active {
        transform: scale(.97);
        box-shadow: 0 2px 6px rgba(181, 80, 42, .4);
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex: 0 0 auto;
        padding: 14px 18px;
    }
    .ios-btn--ghost:active { background: #EFEFF4; }
}

@media (max-width: 480px) {
    .ios-equipments { grid-template-columns: 1fr 1fr; }
    .ios-stepper__labels { font-size: 10px; }
    .ios-section-title { font-size: 16px; }
    .ios-field label { font-size: 12.5px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush