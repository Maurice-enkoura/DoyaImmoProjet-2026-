@extends('layouts.dashboard')

@section('title', 'Modifier ma demande — DoyaImmo')
@section('page_title', 'Modifier ma demande')
@section('page_sub', 'Modifiez les critères de votre recherche')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes demandes
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
        @if($demande->statut->value !== 'en_attente')
            <div style="padding:12px 16px;background:#FFF8E1;border-radius:10px;border:1px solid #FFE0B2;margin-bottom:16px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <i class="fa-solid fa-info-circle" style="color:#E65100;font-size:18px;"></i>
                    <div>
                        <span style="font-weight:600;color:#E65100;">Demande non modifiable</span>
                        <p style="font-size:13px;color:#BF360C;margin:0;">
                            Cette demande est actuellement <strong>{{ $demande->statut->label() }}</strong> et ne peut plus être modifiée.
                            <br>Vous pouvez uniquement la consulter.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('particulier.demandes.update', $demande) }}">
            @csrf
            @method('PUT')

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
                                style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;background:#fff;"
                                {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }} required>
                            <option value="">Sélectionnez une opération</option>
                            @foreach($typesOperation as $key => $label)
                                <option value="{{ $key }}" {{ old('type_operation', $demande->type_operation->value) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @if($demande->statut->value !== 'en_attente')
                            <input type="hidden" name="type_operation" value="{{ $demande->type_operation->value }}">
                        @endif
                        @error('type_operation')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Type de bien <span style="color:#C62828;">*</span>
                        </label>
                        <select name="type_bien" class="form-control @error('type_bien') is-invalid @enderror" 
                                style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;background:#fff;"
                                {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }} required>
                            <option value="">Sélectionnez un type</option>
                            @foreach($typesBien as $key => $label)
                                <option value="{{ $key }}" {{ old('type_bien', $demande->type_bien->value) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @if($demande->statut->value !== 'en_attente')
                            <input type="hidden" name="type_bien" value="{{ $demande->type_bien->value }}">
                        @endif
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
                        <input type="text" name="zone_recherchee" value="{{ old('zone_recherchee', $demande->zone_recherchee) }}" 
                               placeholder="Ex: Almadies, Ngor, Mermoz..." 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;"
                               {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }} required>
                        @if($demande->statut->value !== 'en_attente')
                            <input type="hidden" name="zone_recherchee" value="{{ $demande->zone_recherchee }}">
                        @endif
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
                        <input type="number" name="budget_maximum" value="{{ old('budget_maximum', $demande->budget_maximum) }}" 
                               placeholder="Ex: 500000" 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;"
                               {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }} required>
                        @if($demande->statut->value !== 'en_attente')
                            <input type="hidden" name="budget_maximum" value="{{ $demande->budget_maximum }}">
                        @endif
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
                        <input type="number" name="nombre_chambres" value="{{ old('nombre_chambres', $demande->nombre_chambres) }}" 
                               placeholder="Ex: 3" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;"
                               {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }}>
                        @if($demande->statut->value !== 'en_attente')
                            <input type="hidden" name="nombre_chambres" value="{{ $demande->nombre_chambres }}">
                        @endif
                        @error('nombre_chambres')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Salles de bain
                        </label>
                        <input type="number" name="nombre_salles_bain" value="{{ old('nombre_salles_bain', $demande->nombre_salles_bain) }}" 
                               placeholder="Ex: 2" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;"
                               {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }}>
                        @if($demande->statut->value !== 'en_attente')
                            <input type="hidden" name="nombre_salles_bain" value="{{ $demande->nombre_salles_bain }}">
                        @endif
                        @error('nombre_salles_bain')
                            <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                        @enderror
                    </div>

                    <div>
                        <label style="display:block;font-size:12.5px;font-weight:600;color:var(--text-soft);margin-bottom:4px;">
                            Surface minimum (m²)
                        </label>
                        <input type="number" step="0.01" name="surface_minimum" value="{{ old('surface_minimum', $demande->surface_minimum) }}" 
                               placeholder="Ex: 100" min="0"
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;"
                               {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }}>
                        @if($demande->statut->value !== 'en_attente')
                            <input type="hidden" name="surface_minimum" value="{{ $demande->surface_minimum }}">
                        @endif
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
                        <input type="date" name="date_entree_souhaitee" value="{{ old('date_entree_souhaitee', $demande->date_entree_souhaitee ? $demande->date_entree_souhaitee->format('Y-m-d') : '') }}" 
                               style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;"
                               {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }}>
                        @if($demande->statut->value !== 'en_attente')
                            <input type="hidden" name="date_entree_souhaitee" value="{{ $demande->date_entree_souhaitee ? $demande->date_entree_souhaitee->format('Y-m-d') : '' }}">
                        @endif
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
                    @php
                        $equipements = [
                            'parking' => ['label' => 'Parking', 'icon' => 'fa-solid fa-car'],
                            'meuble' => ['label' => 'Meublé', 'icon' => 'fa-solid fa-couch'],
                            'climatisation' => ['label' => 'Climatisation', 'icon' => 'fa-solid fa-snowflake'],
                            'balcon' => ['label' => 'Balcon', 'icon' => 'fa-solid fa-umbrella'],
                            'jardin' => ['label' => 'Jardin', 'icon' => 'fa-solid fa-tree'],
                            'piscine' => ['label' => 'Piscine', 'icon' => 'fa-solid fa-swimmer'],
                            'ascenseur' => ['label' => 'Ascenseur', 'icon' => 'fa-solid fa-elevator'],
                            'securite' => ['label' => 'Sécurité 24h/24', 'icon' => 'fa-solid fa-shield-halved'],
                        ];
                    @endphp

                    @foreach($equipements as $key => $equipement)
                        <label style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:13px;transition:all 0.2s;{{ old($key, $demande->$key) ? 'background:var(--rust-soft);border-color:var(--rust);' : '' }}">
                            <input type="checkbox" name="{{ $key }}" value="1" {{ old($key, $demande->$key) ? 'checked' : '' }}
                                   {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }}>
                            <i class="{{ $equipement['icon'] }}" style="color:{{ old($key, $demande->$key) ? 'var(--rust)' : 'var(--muted)' }};"></i>
                            {{ $equipement['label'] }}
                        </label>
                    @endforeach
                </div>
                @if($demande->statut->value !== 'en_attente')
                    @foreach(array_keys($equipements) as $key)
                        <input type="hidden" name="{{ $key }}" value="{{ $demande->$key ? 1 : 0 }}">
                    @endforeach
                @endif
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
                          placeholder="Ex: Je préfère un appartement calme, proche des écoles..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:60px;"
                          {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }}>{{ old('criteres_particuliers', $demande->criteres_particuliers) }}</textarea>
                @if($demande->statut->value !== 'en_attente')
                    <input type="hidden" name="criteres_particuliers" value="{{ $demande->criteres_particuliers }}">
                @endif
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
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;"
                          {{ $demande->statut->value !== 'en_attente' ? 'disabled' : '' }} required>{{ old('description', $demande->description) }}</textarea>
                @if($demande->statut->value !== 'en_attente')
                    <input type="hidden" name="description" value="{{ $demande->description }}">
                @endif
                @error('description')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Minimum 20 caractères
                </div>
            </div>

            <!-- Boutons -->
            <div style="display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border);">
                @if($demande->statut->value === 'en_attente')
                    <button type="submit" class="btn btn-rust" id="submitBtn">
                        <i class="fa-solid fa-save"></i> Mettre à jour
                    </button>
                    <a href="{{ route('particulier.demandes.show', $demande) }}" class="btn btn-ghost">
                        <i class="fa-solid fa-eye"></i> Voir la demande
                    </a>
                    <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost" style="margin-left:auto;">
                        <i class="fa-solid fa-times"></i> Annuler
                    </a>
                @else
                    <a href="{{ route('particulier.demandes.show', $demande) }}" class="btn btn-ghost">
                        <i class="fa-solid fa-eye"></i> Voir la demande
                    </a>
                    <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost" style="margin-left:auto;">
                        <i class="fa-solid fa-arrow-left"></i> Retour
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    // Effet visuel sur les cases à cocher
    document.querySelectorAll('label:has(input[type="checkbox"])').forEach(label => {
        const checkbox = label.querySelector('input[type="checkbox"]');
        const icon = label.querySelector('i');
        
        if (checkbox && !checkbox.disabled) {
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
        }
    });

    // Gestion du bouton de soumission
    document.querySelector('form')?.addEventListener('submit', function(e) {
        const btn = document.getElementById('submitBtn');
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mise à jour...';
            btn.disabled = true;
        }
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

    label:has(input[type="checkbox"]:disabled) {
        opacity: 0.6;
        cursor: not-allowed;
    }

    label:has(input[type="checkbox"]:disabled):hover {
        border-color: var(--border);
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