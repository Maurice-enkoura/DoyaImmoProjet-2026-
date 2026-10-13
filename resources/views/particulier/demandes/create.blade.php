@extends('layouts.dashboard')

@section('title', 'Publier un besoin — DoyaImmo')
@section('page_title', 'Publier un besoin')
@section('page_sub', 'Décrivez ce que vous cherchez et recevez des propositions des agences')

@section('content')
<div class="view active particulier-create-demande">

    {{-- ═══ STEPPER ═══ --}}
    <div class="ios-stepper">
        <div class="ios-stepper__track">
            <div class="ios-stepper__fill" id="stepperFill" style="width: 25%;"></div>
        </div>
        <div class="ios-stepper__labels">
            <span class="is-active" data-step="1">1. Besoin</span>
            <span data-step="2">2. Critères</span>
            <span data-step="3">3. Équipements</span>
            <span data-step="4">4. Description</span>
        </div>
    </div>

    @if($errors->any())
        <div class="ios-alert ios-alert--error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Veuillez corriger les erreurs suivantes :</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('particulier.demandes.store') }}" id="demandeForm">
        @csrf

        {{-- ═══ ÉTAPE 1 — BESOIN ═══ --}}
        <div class="ios-step is-active" data-step="1">
            <div class="ios-section-title">
                <i class="fa-solid fa-info-circle"></i> Informations principales
            </div>

            <div class="ios-field">
                <label>Type d'opération <span class="req">*</span></label>
                <select name="type_operation" id="typeOperation"
                        class="ios-input @error('type_operation') is-invalid @enderror" required>
                    <option value="">Sélectionnez une opération</option>
                    @foreach($typesOperation as $key => $label)
                        <option value="{{ $key }}" {{ old('type_operation') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type_operation') <small class="err">{{ $message }}</small> @enderror
                <div class="ios-hint">
                    <i class="fa-regular fa-info-circle"></i>
                    Location = vous cherchez à louer · Achat = vous cherchez à acheter
                </div>
            </div>

            <div class="ios-field">
                <label>Type de bien <span class="req">*</span></label>
                <select name="type_bien" class="ios-input @error('type_bien') is-invalid @enderror" required>
                    <option value="">Sélectionnez un type</option>
                    @foreach($typesBien as $key => $label)
                        <option value="{{ $key }}" {{ old('type_bien') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('type_bien') <small class="err">{{ $message }}</small> @enderror
                <div class="ios-hint">
                    <i class="fa-regular fa-info-circle"></i>
                    Exemples : Appartement, Maison, Villa, Terrain, Local commercial...
                </div>
            </div>

            <div class="ios-field">
                <label>Quartier / Zone recherchée <span class="req">*</span></label>
                <input type="text" name="zone_recherchee" value="{{ old('zone_recherchee') }}"
                       placeholder="Ex : Almadies, Ngor, Mermoz..."
                       class="ios-input" required>
                @error('zone_recherchee') <small class="err">{{ $message }}</small> @enderror
            </div>

            <div class="ios-field">
                <label id="budgetLabel">Budget <span class="req">*</span></label>
                <input type="number" name="budget_maximum" id="budgetInput"
                       value="{{ old('budget_maximum') }}"
                       placeholder="Ex : 500000" class="ios-input" required>
                @error('budget_maximum') <small class="err">{{ $message }}</small> @enderror
                <div class="ios-hint" id="budgetHint">
                    <i class="fa-regular fa-info-circle"></i>
                    Votre budget maximum (FCFA)
                </div>
            </div>
        </div>

        {{-- ═══ ÉTAPE 2 — CRITÈRES ═══ --}}
        <div class="ios-step" data-step="2">
            <div class="ios-section-title">
                <i class="fa-solid fa-sliders-h"></i> Critères de compatibilité
            </div>
            <p class="ios-section-sub">
                Ces critères aident les agences à vous proposer des biens adaptés.
            </p>

            <div id="achatFields">
                <div class="ios-grid-2">
                    <div class="ios-field">
                        <label>Nombre de chambres</label>
                        <input type="number" name="nombre_chambres" value="{{ old('nombre_chambres') }}"
                               placeholder="Ex : 3" min="0" class="ios-input">
                        @error('nombre_chambres') <small class="err">{{ $message }}</small> @enderror
                    </div>

                    <div class="ios-field">
                        <label>Salles de bain</label>
                        <input type="number" name="nombre_salles_bain" value="{{ old('nombre_salles_bain') }}"
                               placeholder="Ex : 2" min="0" class="ios-input">
                        @error('nombre_salles_bain') <small class="err">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="ios-field">
                    <label>Surface minimum (m²)</label>
                    <input type="number" step="0.01" name="surface_minimum" id="surfaceInput"
                           value="{{ old('surface_minimum') }}" placeholder="Ex : 80" min="0" class="ios-input">
                    @error('surface_minimum') <small class="err">{{ $message }}</small> @enderror
                    
                </div>
            </div>

            <div id="locationFields" style="display:none;">
                <div class="ios-field">
                    <label>Date d'entrée souhaitée</label>
                    <input type="date" name="date_entree_souhaitee" value="{{ old('date_entree_souhaitee') }}"
                           min="{{ date('Y-m-d') }}" class="ios-input">
                    @error('date_entree_souhaitee') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="ios-field">
                    <label>Meublé ?</label>
                    <div class="ios-radio-group">
                        <label class="ios-radio">
                            <input type="radio" name="meuble" value="1" {{ old('meuble') == '1' ? 'checked' : '' }}>
                            <span>Oui</span>
                        </label>
                        <label class="ios-radio">
                            <input type="radio" name="meuble" value="0" {{ old('meuble') == '0' ? 'checked' : '' }}>
                            <span>Non</span>
                        </label>
                        <label class="ios-radio">
                            <input type="radio" name="meuble" value="" {{ old('meuble') === null ? 'checked' : '' }}>
                            <span>Indifférent</span>
                        </label>
                    </div>
                </div>

                <div class="ios-grid-2">
                    <div class="ios-field">
                        <label>Salles de bain <small>(optionnel)</small></label>
                        <input type="number" name="nombre_salles_bain" value="{{ old('nombre_salles_bain') }}"
                               placeholder="Ex : 2" min="0" class="ios-input">
                    </div>

                    <div class="ios-field">
                        <label>Surface min (m²) <small>(optionnel)</small></label>
                        <input type="number" step="0.01" name="surface_minimum_location"
                               id="surfaceInputLocation" value="{{ old('surface_minimum_location') }}"
                               placeholder="Ex : 60" min="0" class="ios-input">
                    </div>
                </div>

                <div class="ios-field">
                    <label>Nombre de chambres <small>(optionnel)</small></label>
                    <input type="number" name="nombre_chambres" value="{{ old('nombre_chambres') }}"
                           placeholder="Ex : 3" min="0" class="ios-input">
                </div>
            </div>
        </div>

        {{-- ═══ ÉTAPE 3 — ÉQUIPEMENTS ═══ --}}
        <div class="ios-step" data-step="3">
            <div class="ios-section-title">
                <i class="fa-solid fa-cogs"></i> Équipements souhaités
            </div>
            <p class="ios-section-sub">
                Sélectionnez les équipements que vous recherchez.
            </p>

            @php
                $equipements = [
                    ['name' => 'parking',      'icon' => 'fa-car',           'label' => 'Parking'],
                    ['name' => 'meuble',       'icon' => 'fa-couch',         'label' => 'Meublé'],
                    ['name' => 'climatisation','icon' => 'fa-snowflake',     'label' => 'Climatisation'],
                    ['name' => 'balcon',       'icon' => 'fa-umbrella',      'label' => 'Balcon'],
                    ['name' => 'jardin',       'icon' => 'fa-tree',          'label' => 'Jardin'],
                    ['name' => 'piscine',      'icon' => 'fa-water',         'label' => 'Piscine'],
                    ['name' => 'ascenseur',    'icon' => 'fa-elevator',      'label' => 'Ascenseur'],
                    ['name' => 'securite',     'icon' => 'fa-shield-halved', 'label' => 'Sécurité 24h/24'],
                ];
            @endphp

            <div class="ios-equipments">
                @foreach($equipements as $eq)
                    <label class="ios-equipment">
                        <input type="checkbox" name="{{ $eq['name'] }}" value="1"
                               {{ old($eq['name']) ? 'checked' : '' }}>
                        <i class="fa-solid {{ $eq['icon'] }}"></i>
                        <span>{{ $eq['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- ═══ ÉTAPE 4 — DESCRIPTION ═══ --}}
        <div class="ios-step" data-step="4">
            <div class="ios-section-title">
                <i class="fa-solid fa-pen"></i> Critères particuliers
            </div>

            <div class="ios-field">
                <textarea name="criteres_particuliers" rows="2"
                          placeholder="Ex : Je préfère un appartement calme, proche des écoles..."
                          class="ios-input">{{ old('criteres_particuliers') }}</textarea>
                @error('criteres_particuliers') <small class="err">{{ $message }}</small> @enderror
                <div class="ios-hint">
                    <i class="fa-regular fa-info-circle"></i>
                    Informations complémentaires visibles par les agences
                </div>
            </div>

            <div class="ios-section-title">
                <i class="fa-solid fa-align-left"></i> Description <span class="req">*</span>
            </div>
            <p class="ios-section-sub">
                Décrivez votre recherche en détail. Plus c'est précis, meilleures seront les propositions.
            </p>

            <div class="ios-field">
                <textarea name="description" rows="5" id="descriptionTextarea"
                          placeholder="Ex : Je recherche un appartement F3 de 100m² minimum à Liberté 6, avec parking, climatisé, proche des transports et des écoles..."
                          class="ios-input" required>{{ old('description') }}</textarea>
                @error('description')
                    <small class="err"><i class="fa-solid fa-exclamation-circle"></i> {{ $message }}</small>
                @enderror
                <div class="ios-counter">
                    <span><i class="fa-regular fa-info-circle"></i> Minimum 20 caractères</span>
                    <span id="charCount" style="color:#C62828;font-weight:700;">0 / 20</span>
                </div>
            </div>
        </div>

        {{-- ═══ ACTIONS FIXES EN BAS ═══ --}}
        <div class="ios-form-actions" id="formActions">
            <button type="button" class="ios-btn ios-btn--ghost" id="prevBtn" style="display:none;">
                <i class="fa-solid fa-chevron-left"></i> Précédent
            </button>
            <button type="button" class="ios-btn ios-btn--primary" id="nextBtn">
                Suivant <i class="fa-solid fa-chevron-right"></i>
            </button>
            <button type="submit" class="ios-btn ios-btn--primary" id="submitBtn" style="display:none;">
                <i class="fa-solid fa-paper-plane"></i> Publier
            </button>
        </div>
    </form>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   CRÉATION DEMANDE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.particulier-create-demande {
    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ STEPPER (desktop caché) ═══ */
.ios-stepper { display: none; }

/* ═══ ALERT ═══ */
.ios-alert {
    display: flex;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 14px;
    margin-bottom: 16px;
    font-size: 13px;
}
.ios-alert--error { background: #FFEBEE; color: #C62828; border: 1px solid #FFCDD2; }
.ios-alert i { font-size: 18px; flex-shrink: 0; margin-top: 1px; }
.ios-alert strong { display: block; margin-bottom: 6px; font-weight: 700; }
.ios-alert ul { margin: 0; padding-left: 18px; }
.ios-alert ul li { margin-bottom: 2px; }

/* ═══ STEP (desktop : tout visible) ═══ */
.ios-step { display: block; }

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
}
.ios-section-title:first-child { margin-top: 0; }
.ios-section-title i { color: var(--rust); font-size: 15px; }
.ios-section-title .req { color: #C62828; }
.ios-section-sub { font-size: 13px; color: var(--muted); margin-bottom: 16px; line-height: 1.5; }

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
.ios-field label small { color: var(--muted); font-weight: 400; }

.ios-input {
    width: 100%;
    padding: 11px 14px;
    border: 1px solid var(--border);
    border-radius: 12px;
    font-size: 14px;
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
textarea.ios-input { resize: vertical; min-height: 70px; line-height: 1.5; }
select.ios-input {
    background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'%3E%3Cpath fill='%238A91A0' d='M6 8L2 4h8z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    background-size: 10px;
    padding-right: 36px;
}

.ios-hint {
    font-size: 11.5px;
    color: var(--muted);
    margin-top: 5px;
    line-height: 1.5;
    display: flex;
    gap: 5px;
    align-items: flex-start;
}
.ios-hint i { font-size: 11px; margin-top: 2px; flex-shrink: 0; }
.ios-hint--boxed {
    display: block;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 10px 12px;
    margin-top: 8px;
    font-size: 11px;
    line-height: 1.6;
}
.ios-hint--boxed strong { color: var(--text-soft); font-weight: 700; display: block; margin-bottom: 4px; }
.err { display: block; color: #C62828; font-size: 12px; margin-top: 5px; font-weight: 500; }

.ios-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

/* ═══ RADIO ═══ */
.ios-radio-group { display: flex; flex-wrap: wrap; gap: 8px; }
.ios-radio {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-soft);
    cursor: pointer;
    transition: all .15s;
    user-select: none;
    flex: 1;
    min-width: 90px;
}
.ios-radio input { display: none; }
.ios-radio:has(input:checked) {
    background: var(--rust-soft);
    border-color: var(--rust);
    color: var(--rust);
    font-weight: 600;
}
.ios-radio:active { transform: scale(.97); }

/* ═══ ÉQUIPEMENTS ═══ */
.ios-equipments {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 10px;
}
.ios-equipment {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 16px;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 14px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-soft);
    transition: all .15s ease;
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
.ios-equipment span { flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ios-equipment:has(input:checked) {
    background: var(--rust-soft);
    border-color: var(--rust);
    color: var(--rust);
    font-weight: 600;
}
.ios-equipment:has(input:checked) i { color: var(--rust); }
.ios-equipment:active { transform: scale(.97); }

/* ═══ COUNTER ═══ */
.ios-counter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    color: var(--muted);
    margin-top: 5px;
}
.ios-counter span:first-child { display: inline-flex; gap: 5px; align-items: center; }
.ios-counter span:first-child i { font-size: 11px; }

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
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181,80,42,.2);
}
.ios-btn--primary:hover { background: #9A4523; transform: translateY(-1px); }
.ios-btn--primary:active { transform: scale(.97); }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--ghost:active { transform: scale(.97); }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS WIZARD
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .particulier-create-demande {
        background: transparent;
        padding-top: 4px;
        /* ⚠️ PAS de padding-bottom ici — géré par le layout via body:has(.ios-form-actions) */
    }

    /* ─── STEPPER ─── */
    .ios-stepper {
        display: block;
        margin-bottom: 20px;
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

    /* ─── STEP (mobile : une seule visible) ─── */
    .ios-step:not(.is-active) { display: none; }

    /* ─── ALERT ─── */
    .ios-alert { border-radius: 16px; font-size: 13px; padding: 14px 16px; }

    /* ─── SECTION TITLES ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 24px 0 6px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-sub { font-size: 13px; color: var(--ios-label-3); margin-bottom: 18px; }

    /* ─── FIELDS (font-size 16px anti-zoom iOS) ─── */
    .ios-field { margin-bottom: 16px; }
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

    textarea.ios-input {
        min-height: 90px;
        padding: 14px 16px;
        line-height: 1.5;
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
        line-height: 1.5;
    }
    .ios-hint--boxed {
        background: var(--ios-card);
        border: none;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 12.5px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-top: 10px;
    }
    .err { font-size: 12.5px; margin-top: 6px; padding-left: 4px; }

    /* ─── GRID ─── */
    .ios-grid-2 { grid-template-columns: 1fr; gap: 16px; }

    /* ─── RADIO ─── */
    .ios-radio {
        background: var(--ios-card);
        border: 1.5px solid transparent;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 15px;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-radio:has(input:checked) { background: var(--rust-soft); border-color: var(--rust); }

    /* ─── ÉQUIPEMENTS ─── */
    .ios-equipments { grid-template-columns: 1fr 1fr; gap: 10px; }
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

    /* ─── COUNTER ─── */
    .ios-counter { font-size: 12px; padding-left: 4px; }

    /* ─── BARRE FIXE EN BAS ─── */
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
        display: flex;
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

@media (max-width: 380px) {
    .ios-equipments { grid-template-columns: 1fr; }
    .ios-stepper__labels { font-size: 10px; }
}

/* ═══ Animation shake ═══ */
@keyframes iosShake {
    0%, 100% { transform: translateX(0); }
    25%      { transform: translateX(-6px); }
    75%      { transform: translateX(6px); }
}
.ios-input.shake { animation: iosShake .3s ease; }
</style>
@endpush


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    /* ═══ WIZARD ═══ */
    const steps = document.querySelectorAll('.ios-step');
    const stepperFill = document.getElementById('stepperFill');
    const stepperLabels = document.querySelectorAll('.ios-stepper__labels span');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    let currentStep = 1;
    const totalSteps = steps.length;

    function goToStep(step) {
        if (step < 1 || step > totalSteps) return;
        currentStep = step;

        steps.forEach(s => s.classList.remove('is-active'));
        document.querySelector(`.ios-step[data-step="${step}"]`).classList.add('is-active');

        const pct = (step / totalSteps) * 100;
        if (stepperFill) stepperFill.style.width = pct + '%';

        stepperLabels.forEach(lbl => {
            const s = parseInt(lbl.dataset.step);
            lbl.classList.remove('is-active', 'is-done');
            if (s === step) lbl.classList.add('is-active');
            else if (s < step) lbl.classList.add('is-done');
        });

        if (prevBtn) prevBtn.style.display = step === 1 ? 'none' : 'inline-flex';
        if (nextBtn) nextBtn.style.display = step === totalSteps ? 'none' : 'inline-flex';
        if (submitBtn) submitBtn.style.display = step === totalSteps ? 'inline-flex' : 'none';

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    if (nextBtn) nextBtn.addEventListener('click', () => {
        const currentEl = document.querySelector(`.ios-step[data-step="${currentStep}"]`);
        const requiredInputs = currentEl.querySelectorAll('[required]');
        let valid = true;
        requiredInputs.forEach(inp => {
            if (!inp.value.trim()) {
                inp.classList.add('shake', 'is-invalid');
                setTimeout(() => inp.classList.remove('shake'), 300);
                valid = false;
            } else {
                inp.classList.remove('is-invalid');
            }
        });
        if (valid) goToStep(currentStep + 1);
    });

    if (prevBtn) prevBtn.addEventListener('click', () => goToStep(currentStep - 1));


    /* ═══ TYPE D'OPÉRATION ═══ */
    const typeOperation = document.getElementById('typeOperation');
    const achatFields = document.getElementById('achatFields');
    const locationFields = document.getElementById('locationFields');
    const budgetLabel = document.getElementById('budgetLabel');
    const budgetInput = document.getElementById('budgetInput');
    const budgetHint = document.getElementById('budgetHint');
    const surfaceInput = document.getElementById('surfaceInput');

    function toggleFields() {
        const value = typeOperation?.value;

        if (value === 'achat') {
            achatFields.style.display = 'block';
            locationFields.style.display = 'none';
            budgetLabel.innerHTML = 'Budget d\'achat <span class="req">*</span>';
            budgetInput.placeholder = 'Ex : 50000000';
            budgetHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Votre budget total d\'achat (FCFA)';
            surfaceInput?.setAttribute('required', 'required');
        } else if (value === 'location') {
            achatFields.style.display = 'none';
            locationFields.style.display = 'block';
            budgetLabel.innerHTML = 'Loyer max / mois <span class="req">*</span>';
            budgetInput.placeholder = 'Ex : 500000';
            budgetHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Votre loyer maximum par mois';
            surfaceInput?.removeAttribute('required');
        } else {
            achatFields.style.display = 'block';
            locationFields.style.display = 'none';
            budgetLabel.innerHTML = 'Budget <span class="req">*</span>';
            budgetInput.placeholder = 'Ex : 500000';
            budgetHint.innerHTML = '<i class="fa-regular fa-info-circle"></i> Votre budget maximum';
            surfaceInput?.removeAttribute('required');
        }
    }

    typeOperation?.addEventListener('change', toggleFields);
    toggleFields();


    /* ═══ COMPTEUR ═══ */
    const descTextarea = document.getElementById('descriptionTextarea');
    const charCount = document.getElementById('charCount');

    if (descTextarea && charCount) {
        function updateCharCount() {
            const len = descTextarea.value.length;
            charCount.textContent = len + ' / 20';
            charCount.style.color = len >= 20 ? '#1E7A47' : '#C62828';
        }
        descTextarea.addEventListener('input', updateCharCount);
        updateCharCount();
    }


    /* ═══ SOUMISSION ═══ */
    document.getElementById('demandeForm')?.addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publication...';
            btn.disabled = true;
        }
    });

});
</script>
@endpush