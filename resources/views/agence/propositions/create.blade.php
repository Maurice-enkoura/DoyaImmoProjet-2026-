@extends('layouts.dashboard-agence')

@section('title', 'Faire une offre — DoyaImmo')
@section('page_title', 'Faire une offre')
@section('page_sub', 'Proposez un bien à un client')

@section('content')
<div class="view active proposition-create">

    @php
        $hasBiens = $biens->count() > 0;
    @endphp

    {{-- ═══ STEPPER ═══ --}}
    <div class="ios-stepper">
        <div class="ios-stepper__track">
            <div class="ios-stepper__fill" id="stepperFill" style="width: 33.33%;"></div>
        </div>
        <div class="ios-stepper__labels">
            <span class="is-active" data-step="1">1. Bien</span>
            <span data-step="2">2. Conditions</span>
            <span data-step="3">3. Message</span>
        </div>
    </div>

    {{-- ═══ RAPPEL BESOIN ═══ --}}
    <section class="rappel-card">
        <div class="rappel-card__head">
            <span class="rappel-card__badge">
                <i class="fa-solid fa-bullseye"></i> Besoin du client
            </span>
        </div>

        <h1 class="rappel-card__title">
            {{ $demande->type_bien->label() }}
            <span class="rappel-card__sep">—</span>
            {{ $demande->zone_recherchee }}
        </h1>

        <div class="rappel-card__meta">
            <span>
                <i class="fa-solid fa-money-bill"></i>
                <strong>{{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois</strong>
            </span>
            <span class="rappel-card__dot">·</span>
            <span>
                <i class="fa-solid fa-handshake"></i>
                {{ $demande->type_operation->label() }}
            </span>
            <span class="rappel-card__dot">·</span>
            <span>
                <i class="fa-regular fa-calendar"></i>
                Publié {{ $demande->created_at->diffForHumans() }}
            </span>
        </div>

        @if($demande->description)
            <p class="rappel-card__desc">{{ Str::limit($demande->description, 140) }}</p>
        @endif
    </section>

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
          action="{{ route('agence.propositions.store') }}"
          enctype="multipart/form-data"
          id="propositionForm">

        @csrf
        <input type="hidden" name="demande_id" value="{{ $demande->id }}">

        {{-- ═══════════════════════════════════════════
             ÉTAPE 1 — BIEN
        ═══════════════════════════════════════════ --}}
        <div class="ios-step is-active" data-step="1">

            @if($hasBiens)
                <div class="ios-section-title">
                    <i class="fa-regular fa-building"></i> Sélectionner un bien
                </div>
                <p class="ios-section-sub">
                    Choisissez un de vos biens existants <strong>OU</strong> ajoutez directement des photos/vidéos.
                </p>

                {{-- Liste des biens en cards iOS --}}
                <div class="biens-list" id="biensList">
                    @foreach($biens as $bien)
                        @php
                            $cover = $bien->medias && $bien->medias->where('type_media', 'image')->first()
                                ? asset('storage/' . $bien->medias->where('type_media', 'image')->first()->fichier)
                                : null;
                        @endphp
                        <label class="bien-option">
                            <input type="radio" name="bien_id" value="{{ $bien->id }}"
                                   data-prix="{{ $bien->prix }}"
                                   data-titre="{{ $bien->titre }}"
                                   data-disponibilite="{{ $bien->disponibilite?->format('Y-m-d') }}"
                                   data-caution="{{ $bien->caution_mois }}"
                                   data-avance="{{ $bien->avance_payee ? 1 : 0 }}"
                                   {{ old('bien_id') == $bien->id ? 'checked' : '' }}
                                   onchange="selectBien(this)">

                            <div class="bien-option__cover">
                                @if($cover)
                                    <img src="{{ $cover }}" alt="{{ $bien->titre }}" loading="lazy">
                                @else
                                    <div class="bien-option__placeholder">
                                        <i class="fa-regular fa-image"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="bien-option__body">
                                <h4 class="bien-option__title">{{ $bien->titre }}</h4>
                                <div class="bien-option__price">
                                    {{ number_format($bien->prix, 0, ',', ' ') }}
                                    <small>FCFA</small>
                                </div>
                                <div class="bien-option__meta">
                                    @if($bien->surface)
                                        <span><i class="fa-solid fa-vector-square"></i> {{ $bien->surface }} m²</span>
                                    @endif
                                    @if($bien->quartier)
                                        <span><i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="bien-option__check">
                                <i class="fa-solid fa-check"></i>
                            </div>
                        </label>
                    @endforeach

                    {{-- Option "Aucun bien" --}}
                    <label class="bien-option bien-option--none">
                        <input type="radio" name="bien_id" value=""
                               {{ old('bien_id') === null || old('bien_id') === '' ? 'checked' : '' }}
                               onchange="selectBien(this)">
                        <div class="bien-option__cover">
                            <div class="bien-option__placeholder bien-option__placeholder--none">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </div>
                        </div>
                        <div class="bien-option__body">
                            <h4 class="bien-option__title">Aucun bien sélectionné</h4>
                            <p class="bien-option__hint">Ajoutez des photos ou une vidéo ci-dessous</p>
                        </div>
                        <div class="bien-option__check">
                            <i class="fa-solid fa-check"></i>
                        </div>
                    </label>
                </div>
            @else
                <div class="ios-empty-inline">
                    <i class="fa-regular fa-building"></i>
                    <span>Vous n'avez pas encore de biens publiés. Utilisez l'upload ci-dessous.</span>
                </div>
            @endif

            {{-- ═══ UPLOAD MÉDIAS ═══ --}}
            <div id="mediaSection">
                <div class="ios-section-title">
                    <i class="fa-regular fa-images"></i> Ajouter des médias
                </div>
                <p class="ios-section-sub">
                    Choisissez <strong>soit des photos, soit une vidéo</strong> (pas les deux).
                </p>

                {{-- Toggle Photos / Vidéos --}}
                <div class="media-toggle">
                    <button type="button" class="media-toggle__btn is-active"
                            id="btnPhotos" onclick="selectMediaType('photos')">
                        <i class="fa-regular fa-image"></i> Photos
                    </button>
                    <button type="button" class="media-toggle__btn"
                            id="btnVideos" onclick="selectMediaType('videos')">
                        <i class="fa-regular fa-circle-play"></i> Vidéo
                    </button>
                </div>

                {{-- Photos --}}
                <div id="photosContainer" class="media-upload">
                    <label class="upload-zone" for="photoInput">
                        <input type="file" id="photoInput" name="proposition_images[]"
                               accept="image/*" multiple hidden
                               onchange="handlePhotoFiles(this.files)">
                        <div class="upload-zone__icon">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="upload-zone__text">
                            <strong>Cliquez pour ajouter des photos</strong>
                            <span>JPG, PNG, WebP · Max 5 Mo · Jusqu'à 10</span>
                        </div>
                    </label>

                    <div id="photoPreviewContainer" class="media-preview"></div>
                    <div class="upload-count">
                        <span id="propositionPhotoCount">0</span> / 10 photos
                    </div>
                </div>

                {{-- Vidéos --}}
                <div id="videosContainer" class="media-upload" style="display:none;">
                    <label class="upload-zone" for="videoInput">
                        <input type="file" id="videoInput" name="proposition_videos[]"
                               accept="video/*" hidden
                               onchange="handleVideoFiles(this.files)">
                        <div class="upload-zone__icon">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                        </div>
                        <div class="upload-zone__text">
                            <strong>Cliquez pour ajouter une vidéo</strong>
                            <span>MP4, MOV · Max 20 Mo · 1 seule vidéo</span>
                        </div>
                    </label>

                    <div id="videoPreviewContainer" class="media-preview media-preview--video"></div>
                    <div class="upload-count">
                        <span id="propositionVideoCount">0</span> / 1 vidéo
                    </div>
                </div>
            </div>

            {{-- ═══ PRIX (si pas de bien) ═══ --}}
            <div id="prixSection">
                <div class="ios-section-title">
                    <i class="fa-solid fa-money-bill-wave"></i> Prix proposé <span class="req">*</span>
                </div>

                <div class="ios-field">
                    <input type="number" name="prix_propose" id="prixPropose"
                           value="{{ old('prix_propose') }}"
                           placeholder="Ex : {{ number_format($demande->budget_maximum, 0, ',', ' ') }}"
                           class="ios-input"
                           inputmode="numeric">
                    <div class="ios-hint">
                        <i class="fa-regular fa-info-circle"></i>
                        Budget maximum du client : <strong>{{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             ÉTAPE 2 — CONDITIONS
        ═══════════════════════════════════════════ --}}
        <div class="ios-step" data-step="2">

            <div class="ios-section-title">
                <i class="fa-solid fa-file-signature"></i> Conditions commerciales
            </div>
            <p class="ios-section-sub">
                Ces informations seront affichées au client. Optionnel.
            </p>

            {{-- Disponibilité --}}
            <div class="ios-field">
                <label>Disponible à partir du</label>
                <input type="date" name="disponibilite"
                       value="{{ old('disponibilite') }}"
                       min="{{ now()->format('Y-m-d') }}"
                       class="ios-input">
            </div>

            {{-- Caution --}}
            <div class="ios-field">
                <label>Caution demandée</label>
                <div class="chips-group">
                    @foreach([0 => 'Aucune', 1 => '1 mois', 2 => '2 mois', 3 => '3 mois'] as $val => $label)
                        <label class="chip-option">
                            <input type="radio" name="caution_mois" value="{{ $val }}"
                                   {{ old('caution_mois', 0) == $val ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Avance --}}
            <div class="ios-field">
                <label>Avance de loyer</label>
                <div class="chips-group">
                    @foreach([0 => 'Aucune', 1 => '1 mois', 2 => '2 mois'] as $val => $label)
                        <label class="chip-option">
                            <input type="radio" name="avance_loyer_mois" value="{{ $val }}"
                                   {{ old('avance_loyer_mois', 0) == $val ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Honoraires + Charges --}}
            <div class="ios-grid-2">
                <div class="ios-field">
                    <label>Honoraires</label>
                    <input type="number" name="honoraires" min="0" step="1000"
                           value="{{ old('honoraires') }}"
                           placeholder="FCFA" class="ios-input" inputmode="numeric">
                </div>
                <div class="ios-field">
                    <label>Charges</label>
                    <input type="number" name="charges" min="0" step="1000"
                           value="{{ old('charges') }}"
                           placeholder="FCFA" class="ios-input" inputmode="numeric">
                </div>
            </div>

            {{-- Conditions particulières --}}
            <div class="ios-field">
                <label>Conditions particulières</label>
                <textarea name="conditions_particulieres" rows="3"
                          placeholder="Ex : Bail minimum 12 mois, charges locatives incluses..."
                          class="ios-input">{{ old('conditions_particulieres') }}</textarea>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             ÉTAPE 3 — MESSAGE
        ═══════════════════════════════════════════ --}}
        <div class="ios-step" data-step="3">

            <div class="ios-section-title">
                <i class="fa-regular fa-message"></i> Message au client <span class="req">*</span>
            </div>
            <p class="ios-section-sub">
                Minimum 10 caractères
            </p>

            <div class="ios-field">
                <textarea name="message" id="message" rows="6"
                          placeholder="Bonjour, nous disposons d'un bien correspondant à votre recherche..."
                          class="ios-input"
                          required>{{ old('message') }}</textarea>
                <div class="ios-counter">
                    <span><i class="fa-regular fa-info-circle"></i> Minimum 10 caractères</span>
                    <span id="charCount" style="color:#C62828;font-weight:700;">0</span>
                </div>
            </div>

            {{-- Récap --}}
            <div class="recap-card">
                <div class="recap-card__head">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <strong>Récapitulatif de votre offre</strong>
                </div>

                <div class="recap-list">
                    <div class="recap-item">
                        <span class="recap-item__label">Bien</span>
                        <span class="recap-item__value" id="recapBien">Aucun sélectionné</span>
                    </div>
                    <div class="recap-item">
                        <span class="recap-item__label">Prix proposé</span>
                        <span class="recap-item__value" id="recapPrix">—</span>
                    </div>
                    <div class="recap-item">
                        <span class="recap-item__label">Photos</span>
                        <span class="recap-item__value" id="recapPhotos">0</span>
                    </div>
                    <div class="recap-item">
                        <span class="recap-item__label">Vidéo</span>
                        <span class="recap-item__value" id="recapVideos">0</span>
                    </div>
                </div>
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
                <i class="fa-solid fa-paper-plane"></i> Envoyer
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
const totalSteps = 3;

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

    if (step === totalSteps) updateRecap();

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.getElementById('nextBtn')?.addEventListener('click', () => {
    if (currentStep === 1) {
        // Validation étape 1
        const bienId = document.querySelector('input[name="bien_id"]:checked')?.value || '';
        const hasPhotos = selectedPhotos.length > 0;
        const hasVideos = selectedVideos.length > 0;

        if (bienId === '' && !hasPhotos && !hasVideos) {
            alert('Sélectionnez un bien OU ajoutez des médias.');
            return;
        }

        if (bienId === '' && (hasPhotos || hasVideos)) {
            const prix = document.getElementById('prixPropose').value;
            if (!prix || prix <= 0) {
                alert('Veuillez saisir un prix proposé.');
                document.getElementById('prixPropose').focus();
                return;
            }
        }
    }

    goToStep(currentStep + 1);
});

document.getElementById('prevBtn')?.addEventListener('click', () => goToStep(currentStep - 1));

/* ═══════════════════════════════════════════
   SÉLECTION BIEN
   ═══════════════════════════════════════════ */
function selectBien(input) {
    const bienId = input.value;
    const mediaSection = document.getElementById('mediaSection');
    const prixSection = document.getElementById('prixSection');
    const prixPropose = document.getElementById('prixPropose');

    if (bienId === '') {
        mediaSection.style.opacity = '1';
        mediaSection.style.pointerEvents = 'auto';
        prixSection.style.display = 'block';
        prixPropose.setAttribute('required', 'required');
    } else {
        mediaSection.style.opacity = '0.4';
        mediaSection.style.pointerEvents = 'none';
        prixSection.style.display = 'none';
        prixPropose.removeAttribute('required');
        selectedPhotos = [];
        selectedVideos = [];
        updatePhotoPreview();
        updateVideoPreview();
        document.getElementById('propositionPhotoCount').textContent = 0;
        document.getElementById('propositionVideoCount').textContent = 0;

        // Pré-remplir disponibilité + caution + avance depuis le bien
        const dispo = input.dataset.disponibilite;
        const caution = input.dataset.caution;
        const avance = input.dataset.avance;

        if (dispo) {
            const dispoInput = document.querySelector('input[name="disponibilite"]');
            if (dispoInput) dispoInput.value = dispo;
        }
        if (caution !== null && caution !== undefined && caution !== '') {
            const radio = document.querySelector(`input[name="caution_mois"][value="${caution}"]`);
            if (radio) radio.checked = true;
        }
        if (avance !== null && avance !== undefined && avance !== '') {
            const radio = document.querySelector(`input[name="avance_loyer_mois"][value="${avance}"]`);
            if (radio) radio.checked = true;
        }
    }

    // Pré-remplir le message
    const msg = document.getElementById('message');
    if (msg.value === '' || msg.value.startsWith('Bonjour, nous disposons')) {
        const titre = input.dataset.titre || '';
        msg.value = titre
            ? `Bonjour, nous disposons du bien "${titre}" qui correspond à votre recherche. N'hésitez pas à nous contacter pour plus d'informations.`
            : 'Bonjour, nous disposons d\'un bien correspondant à votre recherche...';
    }
}

/* ═══════════════════════════════════════════
   TOGGLE MEDIA TYPE
   ═══════════════════════════════════════════ */
function selectMediaType(type) {
    const photosContainer = document.getElementById('photosContainer');
    const videosContainer = document.getElementById('videosContainer');
    const btnPhotos = document.getElementById('btnPhotos');
    const btnVideos = document.getElementById('btnVideos');

    if (type === 'photos') {
        photosContainer.style.display = 'block';
        videosContainer.style.display = 'none';
        btnPhotos.classList.add('is-active');
        btnVideos.classList.remove('is-active');
    } else {
        photosContainer.style.display = 'none';
        videosContainer.style.display = 'block';
        btnPhotos.classList.remove('is-active');
        btnVideos.classList.add('is-active');
    }
}

/* ═══════════════════════════════════════════
   UPLOAD PHOTOS
   ═══════════════════════════════════════════ */
let selectedPhotos = [];
let selectedVideos = [];
const maxPhotos = 10;
const maxVideos = 1;

function handlePhotoFiles(files) {
    const fileArray = Array.from(files);
    if (selectedPhotos.length + fileArray.length > maxPhotos) {
        alert('Maximum ' + maxPhotos + ' photos.');
        return;
    }

    fileArray.forEach(file => {
        if (file.size > 5 * 1024 * 1024) {
            alert('La photo "' + file.name + '" dépasse 5 Mo.');
            return;
        }
        if (!file.type.startsWith('image/')) {
            alert('"' + file.name + '" n\'est pas une image.');
            return;
        }
        selectedPhotos.push(file);
    });

    updatePhotoPreview();
    document.getElementById('propositionPhotoCount').textContent = selectedPhotos.length;
    document.getElementById('photoInput').value = '';
}

function removePhoto(index) {
    selectedPhotos.splice(index, 1);
    updatePhotoPreview();
    document.getElementById('propositionPhotoCount').textContent = selectedPhotos.length;
}

function updatePhotoPreview() {
    const container = document.getElementById('photoPreviewContainer');
    container.innerHTML = '';
    container.style.display = selectedPhotos.length === 0 ? 'none' : 'grid';

    selectedPhotos.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'media-thumb';
            div.innerHTML = `
                <img src="${e.target.result}" alt="">
                <button type="button" class="media-thumb__remove" onclick="removePhoto(${index})">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

/* ═══════════════════════════════════════════
   UPLOAD VIDÉOS
   ═══════════════════════════════════════════ */
function handleVideoFiles(files) {
    const fileArray = Array.from(files);
    if (selectedVideos.length + fileArray.length > maxVideos) {
        alert('Maximum ' + maxVideos + ' vidéo.');
        return;
    }

    fileArray.forEach(file => {
        if (file.size > 20 * 1024 * 1024) {
            alert('La vidéo "' + file.name + '" dépasse 20 Mo.');
            return;
        }
        if (!file.type.startsWith('video/')) {
            alert('"' + file.name + '" n\'est pas une vidéo.');
            return;
        }
        selectedVideos.push(file);
    });

    updateVideoPreview();
    document.getElementById('propositionVideoCount').textContent = selectedVideos.length;
    document.getElementById('videoInput').value = '';
}

function removeVideo(index) {
    selectedVideos.splice(index, 1);
    updateVideoPreview();
    document.getElementById('propositionVideoCount').textContent = selectedVideos.length;
}

function updateVideoPreview() {
    const container = document.getElementById('videoPreviewContainer');
    container.innerHTML = '';
    container.style.display = selectedVideos.length === 0 ? 'none' : 'grid';

    selectedVideos.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.className = 'media-thumb media-thumb--video';
            div.innerHTML = `
                <video src="${e.target.result}" muted></video>
                <div class="media-thumb__play"><i class="fa-solid fa-play"></i></div>
                <button type="button" class="media-thumb__remove" onclick="removeVideo(${index})">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;
            container.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

/* ═══════════════════════════════════════════
   COMPTEUR MESSAGE
   ═══════════════════════════════════════════ */
document.getElementById('message')?.addEventListener('input', function() {
    const len = this.value.length;
    const counter = document.getElementById('charCount');
    if (counter) {
        counter.textContent = len;
        counter.style.color = len >= 10 ? '#1E7A47' : '#C62828';
    }
});

/* ═══════════════════════════════════════════
   RÉCAP
   ═══════════════════════════════════════════ */
function updateRecap() {
    const bienInput = document.querySelector('input[name="bien_id"]:checked');
    const bienTitre = bienInput?.dataset.titre || '';
    const prix = document.getElementById('prixPropose').value;

    document.getElementById('recapBien').textContent = bienTitre || 'Aucun (médias ajoutés)';
    document.getElementById('recapPrix').textContent = prix
        ? new Intl.NumberFormat('fr-FR').format(prix) + ' F'
        : (bienTitre ? 'Prix du bien' : '—');
    document.getElementById('recapPhotos').textContent = selectedPhotos.length;
    document.getElementById('recapVideos').textContent = selectedVideos.length;
}

/* ═══════════════════════════════════════════
   SUBMIT
   ═══════════════════════════════════════════ */
document.getElementById('propositionForm')?.addEventListener('submit', function(e) {
    const msg = document.getElementById('message').value;
    const bienId = document.querySelector('input[name="bien_id"]:checked')?.value || '';

    if (msg.trim().length < 10) {
        e.preventDefault();
        alert('Le message doit contenir au moins 10 caractères.');
        document.getElementById('message').focus();
        return;
    }

    if (bienId === '' && selectedPhotos.length === 0 && selectedVideos.length === 0) {
        e.preventDefault();
        alert('Sélectionnez un bien OU ajoutez des médias.');
        return;
    }

    const btn = document.getElementById('submitBtn');
    if (btn) {
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Envoi...';
        btn.disabled = true;
    }
});

/* ═══════════════════════════════════════════
   INIT
   ═══════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
    selectMediaType('photos');
    updatePhotoPreview();
    updateVideoPreview();

    // Init si bien déjà sélectionné
    const checkedBien = document.querySelector('input[name="bien_id"]:checked');
    if (checkedBien) selectBien(checkedBien);

    // Compteur message
    const msg = document.getElementById('message');
    if (msg) msg.dispatchEvent(new Event('input'));
});
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   FAIRE UNE OFFRE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.proposition-create {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --surface:      #F7F9FC;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ STEPPER ═══ */
.ios-stepper { display: none; }

/* ═══ ALERT ═══ */
.ios-alert {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 14px;
    margin-bottom: 16px;
    font-size: 13px;
    background: #FFEBEE;
    color: #C62828;
    border: 1px solid #FFCDD2;
}
.ios-alert i { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.ios-alert strong { display: block; margin-bottom: 4px; font-weight: 700; }
.ios-alert ul { margin: 6px 0 0; padding-left: 18px; }
.ios-alert ul li { margin-bottom: 2px; }

/* ═══ RAPPEL BESOIN ═══ */
.rappel-card {
    background: linear-gradient(135deg, #FFF9F5 0%, #fff 60%);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 18px 20px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.rappel-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--rust), #d4754a);
}
.rappel-card__head { margin-bottom: 10px; }
.rappel-card__badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    background: var(--rust-soft);
    color: var(--rust);
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.rappel-card__badge i { font-size: 10px; }

.rappel-card__title {
    font-family: var(--display);
    font-size: 18px;
    font-weight: 700;
    margin: 0 0 8px;
    color: var(--ink);
    line-height: 1.3;
}
.rappel-card__sep { color: var(--muted); font-weight: 400; margin: 0 4px; }

.rappel-card__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--text-soft);
    margin-bottom: 8px;
}
.rappel-card__meta i { color: var(--rust); font-size: 11px; margin-right: 5px; opacity: .85; }
.rappel-card__meta strong { color: var(--ink); font-weight: 700; }
.rappel-card__dot { opacity: .4; }

.rappel-card__desc {
    font-size: 13px;
    color: var(--text-soft);
    line-height: 1.55;
    margin: 0;
    font-style: italic;
}

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 22px 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ios-section-title:first-of-type { margin-top: 0; }
.ios-section-title i { color: var(--rust); font-size: 15px; }
.ios-section-title .req { color: #C62828; }
.ios-section-sub {
    font-size: 13px;
    color: var(--muted);
    margin: 0 0 16px;
    line-height: 1.5;
}

/* ═══ STEP ═══ */
.ios-step { display: block; }
.ios-step:not(.is-active) { display: none; }

/* ═══ LISTE DES BIENS ═══ */
.biens-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 16px;
}

.bien-option {
    position: relative;
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px;
    background: #fff;
    border: 2px solid var(--border);
    border-radius: 14px;
    cursor: pointer;
    transition: all .2s ease;
    user-select: none;
}
.bien-option:hover { border-color: var(--rust); }
.bien-option:active { transform: scale(.99); }
.bien-option input { position: absolute; opacity: 0; pointer-events: none; }

.bien-option__cover {
    width: 68px;
    height: 68px;
    border-radius: 12px;
    overflow: hidden;
    background: var(--surface);
    border: 1px solid var(--border);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bien-option__cover img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.bien-option__placeholder {
    color: var(--muted);
    font-size: 24px;
    opacity: .4;
}
.bien-option__placeholder--none {
    color: var(--rust);
    opacity: 1;
    font-size: 22px;
}

.bien-option__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.bien-option__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    color: var(--ink);
    margin: 0;
    line-height: 1.3;
}
.bien-option__price {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
}
.bien-option__price small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}
.bien-option__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 12px;
    font-size: 12px;
    color: var(--muted);
}
.bien-option__meta i {
    color: var(--rust);
    font-size: 10px;
    margin-right: 4px;
    opacity: .8;
}
.bien-option__hint {
    font-size: 12.5px;
    color: var(--muted);
    margin: 0;
    line-height: 1.4;
}

.bien-option__check {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: 2px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    color: transparent;
    flex-shrink: 0;
    transition: all .2s ease;
}

/* État sélectionné */
.bien-option:has(input:checked) {
    border-color: var(--rust);
    background: linear-gradient(135deg, #FFF9F5 0%, #fff 60%);
    box-shadow: 0 4px 14px rgba(181, 80, 42, .12);
}
.bien-option:has(input:checked) .bien-option__check {
    background: var(--rust);
    border-color: var(--rust);
    color: #fff;
}

/* ═══ EMPTY INLINE ═══ */
.ios-empty-inline {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 18px;
    background: #fff;
    border: 1px dashed var(--border);
    border-radius: 14px;
    color: var(--muted);
    font-size: 13px;
    margin-bottom: 16px;
}
.ios-empty-inline i { font-size: 16px; opacity: .6; }

/* ═══ MEDIA TOGGLE ═══ */
.media-toggle {
    display: flex;
    gap: 8px;
    padding: 4px;
    background: var(--surface);
    border-radius: 12px;
    margin-bottom: 16px;
    border: 1px solid var(--border);
}
.media-toggle__btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 16px;
    border-radius: 9px;
    border: none;
    background: transparent;
    font-family: inherit;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-soft);
    cursor: pointer;
    transition: all .15s;
}
.media-toggle__btn i { font-size: 13px; }
.media-toggle__btn.is-active {
    background: #fff;
    color: var(--rust);
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.media-toggle__btn.is-active i { color: var(--rust); }

/* ═══ UPLOAD ZONE ═══ */
.media-upload {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 16px;
}

.upload-zone {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 24px 20px;
    background: var(--surface);
    border: 2px dashed var(--border);
    border-radius: 12px;
    cursor: pointer;
    transition: all .2s;
    text-align: center;
}
.upload-zone:hover {
    border-color: var(--rust);
    background: var(--rust-soft);
}
.upload-zone:active { transform: scale(.99); }

.upload-zone__icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #fff;
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .12);
}
.upload-zone__text {
    display: flex;
    flex-direction: column;
    gap: 3px;
}
.upload-zone__text strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
}
.upload-zone__text span {
    font-size: 12px;
    color: var(--muted);
}

.media-preview {
    display: none;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 8px;
    margin-top: 14px;
}
.media-preview--video {
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
}

.media-thumb {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
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
.media-thumb__remove {
    position: absolute;
    top: 6px;
    right: 6px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    border: none;
    background: rgba(0, 0, 0, .7);
    color: #fff;
    cursor: pointer;
    font-size: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all .15s;
    -webkit-tap-highlight-color: transparent;
}
.media-thumb__remove:active { transform: scale(.9); background: rgba(198, 40, 40, .9); }

.media-thumb__play {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #fff;
    font-size: 24px;
    opacity: .6;
    pointer-events: none;
}

.upload-count {
    font-size: 11.5px;
    color: var(--muted);
    text-align: right;
    margin-top: 8px;
    font-weight: 500;
}

/* ═══ FIELDS ═══ */
.ios-field { margin-bottom: 18px; }
.ios-field label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    margin-bottom: 6px;
}
.ios-field label .req { color: #C62828; }

.ios-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--border);
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    background: #fff;
    color: var(--ink);
    transition: all .15s;
    -webkit-appearance: none;
    appearance: none;
    line-height: 1.5;
}
.ios-input:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 3px var(--rust-soft);
}
textarea.ios-input {
    resize: vertical;
    min-height: 90px;
}

.ios-hint {
    font-size: 11.5px;
    color: var(--muted);
    margin-top: 6px;
    line-height: 1.5;
    display: flex;
    gap: 5px;
    align-items: flex-start;
}
.ios-hint i { font-size: 11px; margin-top: 2px; flex-shrink: 0; }
.ios-hint strong { color: var(--ink); font-weight: 700; }

.ios-counter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    color: var(--muted);
    margin-top: 6px;
}
.ios-counter span:first-child {
    display: inline-flex;
    gap: 5px;
    align-items: center;
}
.ios-counter span:first-child i { font-size: 11px; }

/* ═══ CHIPS RADIO ═══ */
.chips-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.chip-option {
    cursor: pointer;
    flex: 1;
    min-width: 80px;
}
.chip-option input { display: none; }
.chip-option span {
    display: block;
    padding: 12px 8px;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    text-align: center;
    font-family: var(--display);
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-soft);
    background: #fff;
    transition: all .2s;
}
.chip-option:hover span { border-color: var(--rust); color: var(--rust); }
.chip-option:has(input:checked) span {
    border-color: var(--rust);
    background: var(--rust);
    color: #fff;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
}

/* ═══ GRID 2 ═══ */
.ios-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

/* ═══ RÉCAP ═══ */
.recap-card {
    background: linear-gradient(135deg, #FFF9F5 0%, #fff 60%);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 16px 18px;
    margin-top: 20px;
}
.recap-card__head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px dashed var(--border);
    font-family: var(--display);
    font-size: 14px;
    color: var(--rust);
}
.recap-card__head i { font-size: 14px; }
.recap-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.recap-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    font-size: 13px;
}
.recap-item__label {
    color: var(--muted);
    font-weight: 500;
}
.recap-item__value {
    font-weight: 700;
    color: var(--ink);
    text-align: right;
    font-family: var(--display);
}

/* ═══ ACTIONS ═══ */
.ios-form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    margin-top: 20px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
    flex: 1;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181, 80, 42, .2);
}
.ios-btn--primary:hover { background: #9A4523; transform: translateY(-1px); }
.ios-btn--primary:active { transform: scale(.97); }
.ios-btn--primary:disabled { opacity: .7; cursor: not-allowed; transform: none; }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
    flex: 0 0 auto;
    padding: 12px 18px;
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--ghost:active { transform: scale(.97); }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .proposition-create { background: transparent; padding-top: 4px; }

    /* ─── STEPPER ─── */
    .ios-stepper {
        display: block;
        margin-bottom: 18px;
        padding: 0 4px;
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
        font-size: 10.5px;
        font-weight: 600;
        color: var(--ios-label-3);
        letter-spacing: -.1px;
    }
    .ios-stepper__labels span {
        transition: color .3s;
        text-align: center;
        flex: 1;
    }
    .ios-stepper__labels span.is-active { color: var(--rust); font-weight: 700; }
    .ios-stepper__labels span.is-done { color: var(--ios-label-2); }

    /* ─── RAPPEL BESOIN iOS ─── */
    .rappel-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .rappel-card::before {
        left: 0; right: 0;
        height: 3px;
        border-radius: 0;
    }
    .rappel-card__badge {
        font-size: 10.5px;
        padding: 4px 10px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
    }
    .rappel-card__title {
        font-size: 20px;
        letter-spacing: -.4px;
        color: var(--ios-label);
        font-weight: 700;
    }
    .rappel-card__sep { color: var(--ios-label-3); }
    .rappel-card__meta {
        font-size: 13.5px;
        color: var(--ios-label-3);
        gap: 6px;
    }
    .rappel-card__meta i { color: var(--ios-label-3); }
    .rappel-card__meta strong { color: var(--ios-label); }
    .rappel-card__desc {
        font-size: 13px;
        color: var(--ios-label-2);
        margin-top: 6px;
    }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 6px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-sub {
        font-size: 13px;
        color: var(--ios-label-3);
        margin: 0 4px 16px;
    }

    /* ─── BIENS LIST iOS ─── */
    .biens-list { gap: 10px; margin-bottom: 14px; }

    .bien-option {
        background: var(--ios-card);
        border: 2px solid transparent;
        border-radius: var(--ios-radius-lg);
        padding: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        gap: 12px;
    }
    .bien-option:hover { border-color: transparent; }
    .bien-option:active { transform: scale(.985); }
    .bien-option__cover {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        border: none;
        background: #EFEFF4;
    }
    .bien-option__title { font-size: 16px; font-weight: 600; letter-spacing: -.2px; }
    .bien-option__price { font-size: 17px; letter-spacing: -.2px; }
    .bien-option__meta { font-size: 12.5px; }
    .bien-option__check { width: 24px; height: 24px; }

    /* Sélection */
    .bien-option:has(input:checked) {
        background: linear-gradient(135deg, #FFF4EC, #FFE8D9);
        border-color: var(--rust);
        box-shadow: 0 4px 14px rgba(181, 80, 42, .18);
    }
    .bien-option:has(input:checked) .bien-option__check {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border-color: var(--rust);
    }

    /* ─── MEDIA TOGGLE iOS ─── */
    .media-toggle {
        background: #EFEFF4;
        border: none;
        padding: 4px;
        border-radius: 12px;
        margin-bottom: 14px;
    }
    .media-toggle__btn {
        padding: 11px 14px;
        font-size: 14px;
    }
    .media-toggle__btn.is-active {
        background: var(--ios-card);
        color: var(--rust);
    }

    /* ─── UPLOAD iOS ─── */
    .media-upload {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .upload-zone {
        background: #EFEFF4;
        border: 2px dashed transparent;
        border-radius: 14px;
        padding: 22px 16px;
        gap: 12px;
    }
    .upload-zone:hover {
        border-color: transparent;
        background: #EFEFF4;
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
        width: 28px; height: 28px;
        font-size: 12px;
    }

    .upload-count {
        font-size: 12px;
        color: var(--ios-label-3);
        padding: 0 4px;
    }

    /* ─── FIELDS iOS (16px anti-zoom) ─── */
    .ios-field { margin-bottom: 16px; }
    .ios-field label {
        font-size: 13px;
        color: var(--ios-label-3);
        font-weight: 500;
        margin-bottom: 6px;
        padding-left: 4px;
    }
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
    textarea.ios-input {
        min-height: 100px;
        padding: 16px;
        line-height: 1.55;
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

    /* ─── CHIPS iOS ─── */
    .chips-group { gap: 8px; }
    .chip-option span {
        background: var(--ios-card);
        border: 1.5px solid transparent;
        border-radius: 12px;
        padding: 14px 8px;
        font-size: 14px;
        font-weight: 600;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: all .15s;
    }
    .chip-option:active span { transform: scale(.96); }
    .chip-option:has(input:checked) span {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        border-color: var(--rust);
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }

    /* ─── GRID 2 iOS ─── */
    .ios-grid-2 { grid-template-columns: 1fr; gap: 16px; }

    /* ─── RÉCAP iOS ─── */
    .recap-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .recap-card__head {
        font-size: 15px;
        font-weight: 700;
        margin-bottom: 14px;
        padding-bottom: 12px;
    }
    .recap-item { font-size: 14px; padding: 4px 0; }
    .recap-item__label { color: var(--ios-label-3); }
    .recap-item__value { font-weight: 600; font-size: 14.5px; }

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

    /* Padding bottom */
    .proposition-create {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }
}

@media (max-width: 480px) {
    .rappel-card { padding: 16px; }
    .rappel-card__title { font-size: 18px; }
    .ios-section-title { font-size: 16px; }

    .bien-option { padding: 12px; }
    .bien-option__cover { width: 58px; height: 58px; }
    .bien-option__title { font-size: 15px; }
    .bien-option__price { font-size: 16px; }

    .chip-option span { padding: 12px 6px; font-size: 13px; }
}
</style>
@endpush