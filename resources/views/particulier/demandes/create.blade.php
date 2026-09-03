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
        <form method="POST" action="{{ route('particulier.demandes.store') }}">
            @csrf

            <!-- Informations principales -->
            <div style="margin-bottom:20px;">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-info-circle" style="color:var(--rust);"></i> Informations principales
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ces critères sont obligatoires pour le matching avec les agences.
                </p>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Type d'opération <span style="color:#C62828;">*</span>
                        </label>
                        <select name="type_operation" class="form-control @error('type_operation') is-invalid @enderror" 
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
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Quartier / Zone recherchée <span style="color:#C62828;">*</span>
                        </label>
                        <input type="text" name="zone_recherchee" value="{{ old('zone_recherchee') }}" 
                               placeholder="Ex: Almadies, Ngor, Mermoz..." 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('zone_recherchee')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> Indiquez le quartier ou la zone où vous souhaitez vous installer.
                        </div>
                    </div>

                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Budget maximum (F/mois) <span style="color:#C62828;">*</span>
                        </label>
                        <input type="number" name="budget_maximum" value="{{ old('budget_maximum') }}" 
                               placeholder="Ex: 500000" 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;" required>
                        @error('budget_maximum')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> Votre budget maximum par mois.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Critères de compatibilité -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-sliders-h" style="color:var(--rust);"></i> Critères de compatibilité
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Ces critères sont utilisés pour le calcul du score de compatibilité avec les biens des agences.
                </p>

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
                    </div>

                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Surface minimum (m²)
                        </label>
                        <input type="number" step="0.01" name="surface_minimum" value="{{ old('surface_minimum') }}" 
                               placeholder="Ex: 100" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                        @error('surface_minimum')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-top:16px;">
                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Date d'entrée souhaitée
                        </label>
                        <!-- ✅ CORRIGÉ : Ajout de l'attribut min pour empêcher les dates passées -->
                        <input type="date" name="date_entree_souhaitee" value="{{ old('date_entree_souhaitee') }}" 
                               min="{{ date('Y-m-d') }}" 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;">
                        @error('date_entree_souhaitee')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                        <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                            <i class="fa-regular fa-info-circle"></i> Date à partir de laquelle vous souhaitez emménager.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Équipements souhaités -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-cogs" style="color:var(--rust);"></i> Équipements souhaités
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                    Sélectionnez les équipements que vous recherchez. Ces critères sont pris en compte dans le matching.
                </p>

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;">
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="parking" value="1" {{ old('parking') ? 'checked' : '' }}>
                        <i class="fa-solid fa-car" style="color:var(--muted);"></i> Parking
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;">
                        <input type="checkbox" name="meuble" value="1" {{ old('meuble') ? 'checked' : '' }}>
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
                        <i class="fa-solid fa-swimmer" style="color:var(--muted);"></i> Piscine
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

            <!-- Critères particuliers (texte libre) -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:8px;color:var(--text-soft);">
                    <i class="fa-solid fa-pen" style="color:var(--rust);"></i> Critères particuliers
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:8px;">
                    Informations complémentaires pour les agences. 
                    <span style="color:#C62828;">(Non pris en compte dans le matching automatique)</span>
                </p>
                <textarea name="criteres_particuliers" rows="2" 
                          placeholder="Ex: Je préfère un appartement calme, proche des écoles, avec peu de circulation..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:60px;">{{ old('criteres_particuliers') }}</textarea>
                @error('criteres_particuliers')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Ces informations seront visibles par les agences qui consulteront votre demande.
                </div>
            </div>

            <!-- Description -->
            <div style="margin-bottom:20px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-family:var(--display);font-size:15px;margin-bottom:8px;color:var(--text-soft);">
                    <i class="fa-solid fa-align-left" style="color:var(--rust);"></i> Description <span style="color:#C62828;">*</span>
                </h4>
                <p style="font-size:12px;color:var(--muted);margin-bottom:8px;">
                    Décrivez votre recherche en détail. Plus votre description est précise, meilleures seront les propositions.
                </p>
                <textarea name="description" rows="4" 
                          placeholder="Décrivez précisément ce que vous recherchez..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;" required>{{ old('description') }}</textarea>
                @error('description')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Minimum 20 caractères
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

    // Gestion du bouton de soumission
    document.querySelector('form')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Publication en cours...';
        btn.disabled = true;
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