@extends('layouts.dashboard')

@section('title', 'Publier un besoin — DoyaImmo')
@section('page_title', 'Publier un besoin')
@section('page_sub', 'Décrivez ce que vous cherchez et recevez des propositions des agences')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes demandes
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
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

        <form method="POST" action="{{ route('particulier.demandes.store') }}" id="demandeForm">
            @csrf

            <!-- Informations principales -->
            <div style="margin-bottom:20px;">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-info-circle" style="color:var(--rust);"></i> Informations principales
                </h4>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Type d'opération <span style="color:#C62828;">*</span>
                        </label>
                        <select name="type_operation" id="typeOperation" class="form-control @error('type_operation') is-invalid @enderror" 
                                style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;background:#fff;" required>
                            <option value="">Sélectionnez une opération</option>
                            @foreach($typesOperation as $key => $label)
                                <option value="{{ $key }}" {{ old('type_operation') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_operation')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            Location = vous cherchez un logement à louer. Achat = vous cherchez un bien à acheter.
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Type de bien <span style="color:#C62828;">*</span>
                        </label>
                        <select name="type_bien" class="form-control @error('type_bien') is-invalid @enderror" 
                                style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;background:#fff;" required>
                            <option value="">Sélectionnez un type</option>
                            @foreach($typesBien as $key => $label)
                                <option value="{{ $key }}" {{ old('type_bien') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type_bien')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            Exemples : Appartement, Maison, Villa, Terrain, Local commercial...
                        </div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Quartier / Zone recherchée <span style="color:#C62828;">*</span>
                        </label>
                        <input type="text" name="zone_recherchee" value="{{ old('zone_recherchee') }}" 
                               placeholder="Ex: Almadies, Ngor, Mermoz, Grand Dakar..." 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('zone_recherchee')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            Indiquez le quartier ou la zone où vous souhaitez vous installer.
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            <span id="budgetLabel">Budget <span style="color:#C62828;">*</span></span>
                        </label>
                        <input type="number" name="budget_maximum" id="budgetInput" value="{{ old('budget_maximum') }}" 
                               placeholder="Ex: 500000" 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('budget_maximum')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i>
                            <span id="budgetHint">Votre budget maximum</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ Critères de compatibilité avec affichage conditionnel -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-sliders-h" style="color:var(--rust);"></i> Critères de compatibilité
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ces critères permettent aux agences de vous proposer des biens qui correspondent à vos besoins.
                </p>

                <!-- ✅ Section ACHAT (affichée par défaut) -->
                <div id="achatFields">
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                                Nombre de chambres
                            </label>
                            <input type="number" name="nombre_chambres" value="{{ old('nombre_chambres') }}" 
                                   placeholder="Ex: 3" min="0"
                                   style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                            @error('nombre_chambres')
                                <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                            @enderror
                            <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                                <i class="fa-regular fa-info-circle"></i> 
                                Nombre de chambres souhaité.
                            </div>
                        </div>

                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                                Salles de bain
                            </label>
                            <input type="number" name="nombre_salles_bain" value="{{ old('nombre_salles_bain') }}" 
                                   placeholder="Ex: 2" min="0"
                                   style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                            @error('nombre_salles_bain')
                                <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                            @enderror
                            <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                                <i class="fa-regular fa-info-circle"></i> 
                                Nombre de salles de bain souhaité.
                            </div>
                        </div>

                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                                Surface minimum (m²)
                            </label>
                            <input type="number" step="0.01" name="surface_minimum" id="surfaceInput" value="{{ old('surface_minimum') }}" 
                                   placeholder="Ex: 80" min="0"
                                   style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                            @error('surface_minimum')
                                <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                            @enderror
                            <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                                <i class="fa-regular fa-info-circle"></i> 
                                Surface habitable minimale que vous recherchez (en m²).
                                <br>
                                <span style="font-size:10px;color:var(--text-soft);display:block;margin-top:4px;padding:6px 10px;background:#F7F9FC;border-radius:6px;border:1px solid var(--border);">
                                    <strong>Ordres de grandeur :</strong><br>
                                    <span id="surfaceHint">
                                        • Studio : 25-35 m²<br>
                                        • T1 (1 chambre) : 50-60 m²<br>
                                        • T2 (2 chambres) : 65-80 m²<br>
                                        • T3 (3 chambres) : 85-110 m²<br>
                                        • Maison : 120-180 m²<br>
                                        • Villa : 200 m² et plus
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ✅ Section LOCATION (cachée par défaut) -->
                <div id="locationFields" style="display:none;">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                                Date d'entrée souhaitée
                            </label>
                            <input type="date" name="date_entree_souhaitee" value="{{ old('date_entree_souhaitee') }}" 
                                   min="{{ date('Y-m-d') }}" 
                                   style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                            @error('date_entree_souhaitee')
                                <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                            @enderror
                            <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                                <i class="fa-regular fa-info-circle"></i> 
                                Date à partir de laquelle vous souhaitez emménager.
                            </div>
                        </div>

                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                                Meublé ?
                            </label>
                            <div style="display:flex;align-items:center;gap:12px;padding-top:6px;">
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;">
                                    <input type="radio" name="meuble" value="1" {{ old('meuble') == '1' ? 'checked' : '' }}> Oui
                                </label>
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;">
                                    <input type="radio" name="meuble" value="0" {{ old('meuble') == '0' ? 'checked' : '' }}> Non
                                </label>
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;">
                                    <input type="radio" name="meuble" value="" {{ old('meuble') === null ? 'checked' : '' }}> Indifférent
                                </label>
                            </div>
                            @error('meuble')
                                <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                            @enderror
                            <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                                <i class="fa-regular fa-info-circle"></i> 
                                Préférez-vous un logement meublé ou non meublé ?
                            </div>
                        </div>
                    </div>

                    <!-- ✅ Salles de bain pour la location -->
                    <div style="margin-top:16px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                                Salles de bain <span style="color:#8A91A0;font-weight:400;">(optionnel)</span>
                            </label>
                            <input type="number" name="nombre_salles_bain" value="{{ old('nombre_salles_bain') }}" 
                                   placeholder="Ex: 2" min="0"
                                   style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                            @error('nombre_salles_bain')
                                <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                            @enderror
                            <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                                <i class="fa-regular fa-info-circle"></i> 
                                Nombre de salles de bain souhaité.
                            </div>
                        </div>

                        <div>
                            <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                                Surface minimum (m²) <span style="color:#8A91A0;font-weight:400;">(optionnel)</span>
                            </label>
                            <input type="number" step="0.01" name="surface_minimum_location" id="surfaceInputLocation" value="{{ old('surface_minimum_location') }}" 
                                   placeholder="Ex: 60" min="0"
                                   style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                            @error('surface_minimum_location')
                                <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                            @enderror
                            <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                                <i class="fa-regular fa-info-circle"></i> 
                                Surface habitable minimale que vous recherchez (en m²).
                                <br>
                                <span style="font-size:10px;color:var(--text-soft);display:block;margin-top:4px;padding:6px 10px;background:#F7F9FC;border-radius:6px;border:1px solid var(--border);">
                                    <strong>Ordres de grandeur :</strong><br>
                                    • Studio : 20-30 m²<br>
                                    • T1 (1 chambre) : 40-50 m²<br>
                                    • T2 (2 chambres) : 55-70 m²<br>
                                    • T3 (3 chambres) : 75-95 m²<br>
                                    • T4+ (4 chambres+) : 100 m² et plus
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ✅ Nombre de chambres pour la location -->
                    <div style="margin-top:16px;">
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Nombre de chambres <span style="color:#8A91A0;font-weight:400;">(optionnel)</span>
                        </label>
                        <input type="number" name="nombre_chambres" value="{{ old('nombre_chambres') }}" 
                               placeholder="Ex: 3" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                        @error('nombre_chambres')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> 
                            Nombre de chambres souhaité.
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ Équipements souhaités COMPLETS -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-cogs" style="color:var(--rust);"></i> Équipements souhaités
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Sélectionnez les équipements que vous recherchez. Ces critères aident les agences à vous proposer des biens adaptés.
                </p>

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="parking" value="1" {{ old('parking') ? 'checked' : '' }}>
                        <i class="fa-solid fa-car" style="color:var(--muted);"></i> Parking
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="meuble" value="1" {{ old('meuble') == '1' ? 'checked' : '' }}>
                        <i class="fa-solid fa-couch" style="color:var(--muted);"></i> Meublé
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="climatisation" value="1" {{ old('climatisation') ? 'checked' : '' }}>
                        <i class="fa-solid fa-snowflake" style="color:var(--muted);"></i> Climatisation
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="balcon" value="1" {{ old('balcon') ? 'checked' : '' }}>
                        <i class="fa-solid fa-umbrella" style="color:var(--muted);"></i> Balcon
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="jardin" value="1" {{ old('jardin') ? 'checked' : '' }}>
                        <i class="fa-solid fa-tree" style="color:var(--muted);"></i> Jardin
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="piscine" value="1" {{ old('piscine') ? 'checked' : '' }}>
                        <i class="fa-solid fa-water" style="color:var(--muted);"></i> Piscine
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="ascenseur" value="1" {{ old('ascenseur') ? 'checked' : '' }}>
                        <i class="fa-solid fa-elevator" style="color:var(--muted);"></i> Ascenseur
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="securite" value="1" {{ old('securite') ? 'checked' : '' }}>
                        <i class="fa-solid fa-shield-halved" style="color:var(--muted);"></i> Sécurité 24h/24
                    </label>
                </div>
                @error('parking')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
            </div>

            <!-- Critères particuliers -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:8px;color:var(--text-soft);">
                    <i class="fa-solid fa-pen" style="color:var(--rust);"></i> Critères particuliers
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:8px;">
                    Informations complémentaires pour les agences.
                </p>
                <textarea name="criteres_particuliers" rows="2" 
                          placeholder="Ex: Je préfère un appartement calme, proche des écoles..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:60px;">{{ old('criteres_particuliers') }}</textarea>
                @error('criteres_particuliers')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> 
                    Ces informations seront visibles par les agences qui consulteront votre demande.
                </div>
            </div>

            <!-- ✅ Description avec compteur de caractères -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:8px;color:var(--text-soft);">
                    <i class="fa-solid fa-align-left" style="color:var(--rust);"></i> Description <span style="color:#C62828;">*</span>
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:8px;">
                    Décrivez votre recherche en détail. Plus votre description est précise, meilleures seront les propositions.
                </p>
                <textarea name="description" rows="4" 
                          placeholder="Ex: Je recherche un appartement F3 de 100m² minimum à Liberté 6, avec parking, climatisé, proche des transports et des écoles..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;" 
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <small style="color:#C62828;font-size:12px;display:block;margin-top:4px;">
                        <i class="fa-solid fa-exclamation-circle"></i> {{ $message }}
                    </small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;display:flex;justify-content:space-between;align-items:center;">
                    <span><i class="fa-regular fa-info-circle"></i> Minimum 20 caractères</span>
                    <span id="descriptionCounter" style="font-weight:500;">
                        <span id="charCount" style="color:#C62828;">0</span> / 20 caractères
                    </span>
                </div>
            </div>

            <!-- Boutons -->
            <div style="display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-rust" id="submitBtn">
                    <i class="fa-solid fa-paper-plane"></i> Publier ma demande
                </button>
                <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // ============================================
    // AFFICHAGE CONDITIONNEL SELON LE TYPE D'OPÉRATION
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const typeOperation = document.getElementById('typeOperation');
        const achatFields = document.getElementById('achatFields');
        const locationFields = document.getElementById('locationFields');
        const budgetLabel = document.getElementById('budgetLabel');
        const budgetInput = document.getElementById('budgetInput');
        const budgetHint = document.getElementById('budgetHint');
        const surfaceInput = document.getElementById('surfaceInput');
        const surfaceHint = document.getElementById('surfaceHint');

        function toggleFields() {
            const value = typeOperation.value;

            if (value === 'achat') {
                achatFields.style.display = 'block';
                locationFields.style.display = 'none';
                budgetLabel.innerHTML = 'Budget d\'achat <span style="color:#C62828;">*</span>';
                budgetInput.placeholder = 'Ex: 50000000';
                budgetHint.textContent = 'Votre budget total d\'achat (en FCFA)';
                surfaceInput.setAttribute('required', 'required');
                surfaceHint.innerHTML = `
                    • Studio : 25-35 m²<br>
                    • T1 (1 chambre) : 50-60 m²<br>
                    • T2 (2 chambres) : 65-80 m²<br>
                    • T3 (3 chambres) : 85-110 m²<br>
                    • Maison : 120-180 m²<br>
                    • Villa : 200 m² et plus
                `;
            } else if (value === 'location') {
                achatFields.style.display = 'none';
                locationFields.style.display = 'block';
                budgetLabel.innerHTML = 'Loyer max / mois <span style="color:#C62828;">*</span>';
                budgetInput.placeholder = 'Ex: 500000';
                budgetHint.textContent = 'Votre loyer maximum par mois';
                surfaceInput.removeAttribute('required');
                surfaceHint.innerHTML = `
                    • Studio : 20-30 m²<br>
                    • T1 (1 chambre) : 40-50 m²<br>
                    • T2 (2 chambres) : 55-70 m²<br>
                    • T3 (3 chambres) : 75-95 m²<br>
                    • T4+ (4 chambres+) : 100 m² et plus
                `;
            } else {
                achatFields.style.display = 'block';
                locationFields.style.display = 'none';
                budgetLabel.innerHTML = 'Budget <span style="color:#C62828;">*</span>';
                budgetInput.placeholder = 'Ex: 500000';
                budgetHint.textContent = 'Votre budget maximum';
                surfaceInput.removeAttribute('required');
            }
        }

        typeOperation.addEventListener('change', toggleFields);
        
        // Initialisation
        toggleFields();

        // Effet visuel sur les cases à cocher
        document.querySelectorAll('label:has(input[type="checkbox"])').forEach(label => {
            const checkbox = label.querySelector('input[type="checkbox"]');
            const icon = label.querySelector('i');
            
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    label.style.background = 'var(--rust-soft)';
                    label.style.borderColor = 'var(--rust)';
                    if (icon) icon.style.color = 'var(--rust)';
                } else {
                    label.style.background = 'transparent';
                    label.style.borderColor = 'var(--border)';
                    if (icon) icon.style.color = 'var(--muted)';
                }
            });
        });

        // ✅ Compteur de caractères pour la description
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
            updateCharCount();
        }

        // Gestion du bouton de soumission
        document.getElementById('demandeForm')?.addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publication en cours...';
            btn.disabled = true;
        });
    });
</script>
@endsection

@push('styles')
<style>
    .form-control.is-invalid {
        border-color: #C62828 !important;
    }

    label:has(input[type="checkbox"]) {
        transition: all 0.2s;
        cursor: pointer;
        user-select: none;
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

    #achatFields, #locationFields {
        transition: all 0.3s ease;
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
        [style*="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;"] {
            grid-template-columns: 1fr 1fr !important;
        }
    }

    @media (max-width: 480px) {
        [style*="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush