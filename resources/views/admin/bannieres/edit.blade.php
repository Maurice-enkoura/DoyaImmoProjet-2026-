@extends('layouts.admin')

@section('title', 'Modifier la bannière - DoyaImmo')
@section('page_title', 'Modifier la bannière')
@section('page_sub', $banniere->titre)

@section('content')
<style>
    .form-group {
        margin-bottom: 16px;
    }
    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 4px;
        color: var(--text-soft);
    }
    .form-group label .optional {
        font-weight: 400;
        color: var(--muted);
        font-size: 12px;
    }
    .form-group input,
    .form-group select {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s;
        background: #fff;
    }
    .form-group input:focus,
    .form-group select:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.1);
    }
    .form-group .help-text {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }
    .preview-image {
        max-width: 200px;
        max-height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--border);
        margin-top: 8px;
    }
    .current-image {
        max-width: 200px;
        max-height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid var(--border);
    }
    .image-upload-wrapper {
        border: 2px dashed var(--border);
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #FAFBFC;
    }
    .image-upload-wrapper:hover {
        border-color: var(--rust);
        background: var(--rust-soft);
    }
    .image-upload-wrapper .icon {
        font-size: 32px;
        color: var(--muted);
        display: block;
        margin-bottom: 8px;
    }
    .image-upload-wrapper .text {
        font-size: 14px;
        color: var(--muted);
    }
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.bannieres.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux bannières
    </a>
</div>

<div class="panel" style="max-width:700px;margin:0 auto;">
    @if(session('error'))
        <div class="flash-message flash-error" style="margin-bottom:16px;">
            <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.bannieres.update', $banniere) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="titre">Titre <span style="color:var(--red);">*</span></label>
            <input type="text" name="titre" id="titre" value="{{ old('titre', $banniere->titre) }}" required>
            @error('titre')
                <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="sous_titre">Sous-titre <span class="optional">(optionnel)</span></label>
            <input type="text" name="sous_titre" id="sous_titre" value="{{ old('sous_titre', $banniere->sous_titre) }}">
            @error('sous_titre')
                <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label>Image actuelle</label>
            @if($banniere->image)
                <img src="{{ asset('storage/' . $banniere->image) }}" alt="{{ $banniere->titre }}" class="current-image">
            @else
                <div style="color:var(--muted);font-size:13px;">Aucune image</div>
            @endif
        </div>

        <div class="form-group">
            <label for="image">Nouvelle image <span class="optional">(optionnel)</span></label>
            <div class="image-upload-wrapper" onclick="document.getElementById('image').click()">
                <span class="icon"><i class="fa-regular fa-image"></i></span>
                <span class="text">Cliquez pour changer l'image</span>
                <span style="display:block;font-size:12px;color:var(--muted);margin-top:4px;">JPG, PNG, WEBP (max 5MB)</span>
            </div>
            <input type="file" name="image" id="image" accept="image/*" style="display:none;">
            <img id="imagePreview" class="preview-image" style="display:none;">
            <div class="help-text">Laissez vide pour conserver l'image actuelle.</div>
            @error('image')
                <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="lien">Lien <span class="optional">(optionnel)</span></label>
            <input type="url" name="lien" id="lien" value="{{ old('lien', $banniere->lien) }}" placeholder="https://...">
            <div class="help-text">Lien vers la page de destination (optionnel).</div>
            @error('lien')
                <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="position_texte">Position du texte</label>
            <select name="position_texte" id="position_texte">
                <option value="gauche" {{ old('position_texte', $banniere->position_texte) == 'gauche' ? 'selected' : '' }}>Gauche</option>
                <option value="centre" {{ old('position_texte', $banniere->position_texte) == 'centre' ? 'selected' : '' }}>Centre</option>
                <option value="droite" {{ old('position_texte', $banniere->position_texte) == 'droite' ? 'selected' : '' }}>Droite</option>
            </select>
            @error('position_texte')
                <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
            <div class="form-group">
                <label for="date_debut">Date de début <span class="optional">(optionnel)</span></label>
                <input type="date" name="date_debut" id="date_debut" value="{{ old('date_debut', $banniere->date_debut ? $banniere->date_debut->format('Y-m-d') : now()->format('Y-m-d')) }}">
                <div class="help-text">Par défaut: aujourd'hui</div>
                @error('date_debut')
                    <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="date_fin">Date de fin <span class="optional">(optionnel)</span></label>
                <input type="date" name="date_fin" id="date_fin" value="{{ old('date_fin', $banniere->date_fin ? $banniere->date_fin->format('Y-m-d') : '') }}">
                <div class="help-text">Laissez vide pour une durée illimitée.</div>
                @error('date_fin')
                    <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;">
            <button type="submit" class="btn btn-rust" style="flex:1;justify-content:center;">
                <i class="fa-solid fa-save"></i> Mettre à jour
            </button>
            <a href="{{ route('admin.bannieres.index') }}" class="btn btn-ghost">Annuler</a>
        </div>
    </form>
</div>

<script>
    document.getElementById('image').addEventListener('change', function(e) {
        const preview = document.getElementById('imagePreview');
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    });

    document.getElementById('date_debut').addEventListener('change', function() {
        const dateFin = document.getElementById('date_fin');
        if (this.value) {
            dateFin.min = this.value;
        }
    });
</script>
@endsection