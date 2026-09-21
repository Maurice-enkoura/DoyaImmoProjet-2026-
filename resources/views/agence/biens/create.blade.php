@extends('layouts.dashboard-agence')

@section('title', 'Publier un bien — DoyaImmo')
@section('page_title', 'Publier un bien')
@section('page_sub', 'Ajoutez un nouveau bien immobilier')

@section('content')
<div class="view active bien-create">

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

    {{-- ═══ ALERTES DE VALIDATION ═══ --}}
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

    <form method="POST"
          action="{{ route('agence.biens.store') }}"
          enctype="multipart/form-data"
          id="bienForm">

        @csrf

        {{-- ═══ ÉTAPE 1 — INFOS PRINCIPALES ═══ --}}
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
                    <input type="text" name="titre" value="{{ old('titre') }}"
                           placeholder="Ex : Magnifique villa à Ngor"
                           class="ios-input" required>
                    @error('titre') <small class="err">{{ $message }}</small> @enderror
                    <div class="ios-hint">
                        <i class="fa-regular fa-info-circle"></i>
                        Le titre sera utilisé pour générer l'URL du bien
                    </div>
                </div>

                <div class="ios-field">
                    <label>Type de bien <span class="req">*</span></label>
                    <select name="type_bien" id="type_bien" class="ios-input" required>
                        <option value="">Sélectionnez un type</option>
                        @foreach(\App\Enums\TypeBienEnum::cases() as $type)
                            <option value="{{ $type->value }}" {{ old('type_bien') == $type->value ? 'selected' : '' }}>
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
                            <option value="{{ $type->value }}" {{ old('type_contrat') == $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type_contrat') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="ios-field ios-field--full">
                    <label>Prix (FCFA) <span class="req">*</span></label>
                    <input type="number" name="prix" value="{{ old('prix') }}"
                           placeholder="Ex : 250000000"
                           class="ios-input" required>
                    @error('prix') <small class="err">{{ $message }}</small> @enderror
                    <div class="ios-hint" id="prixHint">
                        <i class="fa-regular fa-info-circle"></i>
                        Prix de vente ou loyer mensuel selon le contrat
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ÉTAPE 2 — LOCALISATION ═══ --}}
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
                    <input type="text" name="quartier" value="{{ old('quartier') }}"
                           placeholder="Ex : Almadies, Ngor, Mermoz"
                           class="ios-input" required>
                    @error('quartier') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="ios-field">
                    <label>Adresse <span class="req">*</span></label>
                    <input type="text" name="adresse" value="{{ old('adresse') }}"
                           placeholder="Ex : Rue des Almadies, Dakar"
                           class="ios-input" required>
                    @error('adresse') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="ios-field ios-field--full">
                    <label>Description <span class="req">*</span></label>
                    <textarea name="description" rows="5"
                              placeholder="Décrivez votre bien en détail..."
                              class="ios-input" required>{{ old('description') }}</textarea>
                    @error('description') <small class="err">{{ $message }}</small> @enderror
                    <div class="ios-counter">
                        <span><i class="fa-regular fa-info-circle"></i> Minimum 20 caractères</span>
                        <span id="descCounter">0 / 20</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══ ÉTAPE 3 — CARACTÉRISTIQUES + ÉQUIPEMENTS ═══ --}}
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
                           value="{{ old('nombre_chambres', 0) }}" min="0"
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
                           value="{{ old('nombre_salles_bain', 0) }}" min="0"
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
                           value="{{ old('surface') }}" placeholder="Ex : 150" min="0"
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
                    <input type="checkbox" name="parking_disponible" value="1" {{ old('parking_disponible') ? 'checked' : '' }}>
                    <i class="fa-solid fa-car"></i>
                    <span>Parking</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="est_meuble" value="1" {{ old('est_meuble') ? 'checked' : '' }}>
                    <i class="fa-solid fa-couch"></i>
                    <span>Meublé</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="climatisation" value="1" {{ old('climatisation') ? 'checked' : '' }}>
                    <i class="fa-solid fa-snowflake"></i>
                    <span>Climatisation</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="balcon" value="1" {{ old('balcon') ? 'checked' : '' }}>
                    <i class="fa-solid fa-umbrella"></i>
                    <span>Balcon</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="jardin" value="1" {{ old('jardin') ? 'checked' : '' }}>
                    <i class="fa-solid fa-tree"></i>
                    <span>Jardin</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="piscine" value="1" {{ old('piscine') ? 'checked' : '' }}>
                    <i class="fa-solid fa-water"></i>
                    <span>Piscine</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="ascenseur" value="1" {{ old('ascenseur') ? 'checked' : '' }}>
                    <i class="fa-solid fa-elevator"></i>
                    <span>Ascenseur</span>
                </label>
                <label class="ios-equipment">
                    <input type="checkbox" name="securite" value="1" {{ old('securite') ? 'checked' : '' }}>
                    <i class="fa-solid fa-shield-halved"></i>
                    <span>Sécurité 24h/24</span>
                </label>
            </div>

            <div id="terrainNote" class="ios-note ios-note--warning" style="display:none;">
                <i class="fa-solid fa-info-circle"></i>
                Les équipements ne sont pas applicables pour un terrain.
            </div>
        </div>

        {{-- ═══ ÉTAPE 4 — PHOTOS + VIDÉOS ═══ --}}
        <div class="ios-step" data-step="4">

            <div class="ios-section-title">
                <i class="fa-regular fa-image"></i> Photos du bien <span class="req">*</span>
            </div>
            <p class="ios-section-sub">
                Jusqu'à 10 photos · JPG, PNG, WebP · Max 5 Mo
            </p>

            <div class="media-upload">
                <label class="upload-zone" for="imageInput">
                    <input type="file" name="images[]" id="imageInput"
                           accept="image/*" multiple hidden required>
                    <div class="upload-zone__icon">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div class="upload-zone__text">
                        <strong>Ajouter des photos</strong>
                        <span>Cliquez pour parcourir</span>
                    </div>
                </label>

                <div id="imagePreviewContainer" class="media-preview"></div>
                <div class="upload-count">
                    <span id="imageCount">0</span> / 10 photos
                </div>

                @error('images.*') <small class="err">{{ $message }}</small> @enderror
                @error('images') <small class="err">{{ $message }}</small> @enderror
            </div>

            <div class="ios-section-title">
                <i class="fa-regular fa-circle-play"></i> Vidéo du bien
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
                <div class="upload-count">
                    <span id="videoCount">0</span> / 1 vidéo
                </div>

                @error('videos.*') <small class="err">{{ $message }}</small> @enderror
                @error('videos') <small class="err">{{ $message }}</small> @enderror
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
                <i class="fa-solid fa-plus"></i> Publier
            </button>
        </div>
    </form>
</div>
@endsection


@push('scripts')
<script>
/* ═══ WIZARD ═══ */
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

/* ═══ ADAPTATION PAR TYPE DE BIEN ═══ */
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

    } else {
        equipementsContainer.style.display = 'grid';
        equipSectionTitle.style.display = 'flex';
        terrainNote.style.display = 'none';

        chambresLabel.innerHTML = 'Chambres <span class="req">*</span>';
        sdbLabel.innerHTML = 'Salles de bain <span class="req">*</span>';
        chambresHint.textContent = 'Nombre de chambres';
        sdbHint.textContent = 'Nombre de salles de bain';
        surfaceHint.textContent = 'Surface en m²';
        caracteristiquesLabel.textContent = 'Renseignez les caractéristiques du bien';
        equipLabel.textContent = 'Sélectionnez les équipements disponibles';

        chambresInput.setAttribute('required', 'required');
        sdbInput.setAttribute('required', 'required');
        prixHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Prix de vente ou loyer selon le contrat';
    }
});

/* ═══ ADAPTATION PAR CONTRAT ═══ */
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

/* ═══ UPLOAD PHOTOS ═══ */
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
    document.getElementById('imageCount').textContent = files.length;

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

/* ═══ UPLOAD VIDÉOS ═══ */
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
    document.getElementById('videoCount').textContent = files.length;

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

/* ═══ COMPTEUR DESCRIPTION ═══ */
document.querySelector('textarea[name="description"]')?.addEventListener('input', function () {
    const len = this.value.length;
    const counter = document.getElementById('descCounter');
    if (counter) {
        counter.textContent = len + ' / 20';
        counter.style.color = len >= 20 ? '#1E7A47' : '#C62828';
    }
});

/* ═══ SUBMIT ═══ */
document.getElementById('bienForm')?.addEventListener('submit', function () {
    const btn = document.getElementById('submitBtn');
    if (btn) {
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publication...';
        btn.disabled = true;
    }
});

/* ═══ INIT ═══ */
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
   PUBLIER UN BIEN — Design épuré
   ═══════════════════════════════════════════════════════════ */

.bien-create {
    --c-success: #1E7A47; --c-success-bg: #E8F5E9;
    --c-warning: #E65100; --c-warning-bg: #FFF8E1;
    --c-danger: #C62828; --c-danger-bg: #FFEBEE;
    --c-info: #0D47A1; --c-info-bg: #E3F2FD;
    --surface: #F7F9FC;
    --radius: 14px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ STEPPER ═══ */
.ios-stepper {
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
    background: var(--rust);
    border-radius: 999px;
    transition: width .4s cubic-bezier(.4,0,.2,1);
}
.ios-stepper__labels {
    display: flex;
    justify-content: space-between;
    gap: 4px;
    font-size: 12px;
    font-weight: 500;
    color: var(--muted);
}
.ios-stepper__labels span {
    transition: color .2s;
    text-align: center;
    flex: 1;
}
.ios-stepper__labels span.is-active { color: var(--rust); font-weight: 700; }
.ios-stepper__labels span.is-done { color: var(--text-soft); }

/* ═══ ALERT DE VALIDATION ═══ */
.ios-alert {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 14px;
    margin-bottom: 16px;
    font-size: 13px;
}
.ios-alert--error {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border: 1px solid #FFCDD2;
}
.ios-alert i { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.ios-alert strong { display: block; margin-bottom: 4px; font-weight: 700; }
.ios-alert ul { margin: 6px 0 0; padding-left: 18px; }
.ios-alert ul li { margin-bottom: 2px; }

/* ═══ STEP ═══ */
.ios-step { display: block; }
.ios-step:not(.is-active) { display: none; }

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 24px 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: -.2px;
}
.ios-section-title:first-child { margin-top: 0; }
.ios-section-title i { color: var(--muted); font-size: 15px; }
.ios-section-title .req { color: var(--c-danger); }
.ios-section-sub {
    font-size: 13px;
    color: var(--muted);
    margin: 0 0 18px;
    line-height: 1.5;
}

/* ═══ FORM GRID ═══ */
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
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    margin-bottom: 8px;
}
.ios-field label .req { color: var(--c-danger); }
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
    font-size: 14px;
    font-family: inherit;
    background: #fff;
    color: var(--ink);
    transition: border-color .15s;
    -webkit-appearance: none;
    appearance: none;
}
.ios-input:focus {
    outline: none;
    border-color: var(--rust);
}
.ios-input.is-invalid { border-color: var(--c-danger); }
.ios-input:disabled {
    background: #F5F5F7;
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
    color: var(--c-danger);
    font-size: 12.5px;
    margin-top: 6px;
    font-weight: 500;
}

/* ═══ ÉQUIPEMENTS ═══ */
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
    border: 1px solid var(--border);
    border-radius: 12px;
    cursor: pointer;
    font-size: 13.5px;
    font-weight: 500;
    color: var(--text-soft);
    transition: border-color .15s, background-color .15s, color .15s;
    user-select: none;
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
.ios-equipment:hover { border-color: #d8d8d8; }

/* ═══ NOTE ═══ */
.ios-note {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13px;
    line-height: 1.5;
    background: #F5F5F7;
    color: var(--text-soft);
}
.ios-note i { font-size: 16px; flex-shrink: 0; color: var(--muted); }

/* ═══ UPLOAD MÉDIAS ═══ */
.media-upload {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
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
    background: #F5F5F7;
    border: 1px dashed var(--border);
    border-radius: 12px;
    cursor: pointer;
    transition: border-color .15s, background-color .15s;
    text-align: center;
}
.upload-zone:hover {
    border-color: var(--rust);
    background: var(--surface);
}
.upload-zone__icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #fff;
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    border: 1px solid var(--border);
}
.upload-zone__text {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.upload-zone__text strong {
    font-family: var(--display);
    font-size: 14.5px;
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
.upload-count {
    font-size: 12px;
    color: var(--muted);
    text-align: right;
    margin-top: 10px;
    font-weight: 500;
}

/* ═══ ACTIONS ═══ */
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
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.ios-btn--primary:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
.ios-btn--primary:disabled { opacity: .7; cursor: not-allowed; }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--ghost:hover { border-color: #d8d8d8; color: var(--ink); background: #F5F5F7; }

/* ═══ SHAKE ═══ */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    75% { transform: translateX(6px); }
}
.shake { animation: shake .3s ease; }

/* ═══ DESKTOP ═══ */
@media (min-width: 1000px) {
    .bien-create {
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
    .ios-equipments { grid-template-columns: repeat(3, 1fr); }
    .bien-create {
        max-width: 720px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .bien-create {
        background: transparent;
        padding-top: 4px;
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
        max-width: none;
        margin: 0;
    }

    /* STEPPER */
    .ios-stepper {
        margin-bottom: 20px;
        padding: 0 4px;
    }

    /* GRID → 1 colonne */
    .ios-form-grid,
    .ios-form-grid--3 {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .ios-field--full { grid-column: auto; }

    /* SECTION TITLES */
    .ios-section-title {
        font-size: 15px;
        margin: 20px 4px 6px;
        color: var(--ink);
    }
    .ios-section-title i { font-size: 14px; }
    .ios-section-sub { font-size: 13px; margin: 0 4px 16px; }

    /* FIELDS */
    .ios-field label {
        font-size: 13px;
        margin-bottom: 6px;
        padding-left: 4px;
    }
    .ios-input {
        background: var(--ios-card);
        border: none;
        border-radius: 12px;
        padding: 14px 16px;
        font-size: 16px;
        color: var(--ink);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-input:focus {
        box-shadow: 0 0 0 2px rgba(181, 80, 42, .25);
    }
    .ios-input.is-invalid { box-shadow: 0 0 0 2px rgba(198, 40, 40, .25); }
    .ios-input:disabled {
        background: #EFEFF4;
        box-shadow: none;
    }
    textarea.ios-input {
        min-height: 120px;
        padding: 16px;
        line-height: 1.5;
    }
    select.ios-input {
        padding: 14px 36px 14px 16px;
        background-position: right 16px center;
        font-size: 16px;
    }

    .ios-hint { font-size: 12.5px; padding-left: 4px; margin-top: 6px; }
    .ios-counter { font-size: 12px; padding-left: 4px; margin-top: 6px; }
    .err { font-size: 12.5px; padding-left: 4px; margin-top: 6px; }

    /* ÉQUIPEMENTS */
    .ios-equipments {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .ios-equipment {
        background: var(--ios-card);
        border: 1px solid transparent;
        border-radius: 12px;
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
    .ios-equipment span {
        white-space: normal;
        font-size: 12.5px;
        line-height: 1.3;
    }
    .ios-equipment:has(input:checked) {
        background: var(--rust-soft);
        border-color: var(--rust);
        color: var(--rust);
    }
    .ios-equipment:hover { border-color: transparent; }

    /* NOTE */
    .ios-note {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid var(--muted);
        border-radius: 12px;
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-note i { color: var(--muted); }

    /* UPLOAD */
    .media-upload {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .upload-zone {
        background: #F5F5F7;
        border: 1px dashed transparent;
        border-radius: 12px;
        padding: 22px 16px;
        gap: 12px;
    }
    .upload-zone__icon {
        width: 52px;
        height: 52px;
        background: #fff;
        color: var(--muted);
        font-size: 22px;
        border: 1px solid var(--border);
    }
    .upload-zone__text strong { font-size: 14.5px; font-weight: 600; }
    .upload-zone__text span { font-size: 12.5px; }

    .media-preview { gap: 6px; }
    .media-thumb { border-radius: 12px; border: none; }
    .upload-count { font-size: 12px; padding: 0 4px; }

    /* ACTIONS FIXES */
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
        border-radius: 12px;
        flex: 1;
    }
    .ios-btn--primary {
        background: var(--rust);
        border: none;
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--text-soft);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex: 0 0 auto;
        padding: 14px 18px;
    }
}

@media (max-width: 480px) {
    .ios-equipments { grid-template-columns: 1fr 1fr; }
    .ios-stepper__labels { font-size: 10px; }
    .ios-section-title { font-size: 14.5px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush