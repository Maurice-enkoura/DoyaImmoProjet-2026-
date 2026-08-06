@extends('layouts.admin')

@section('title', 'Modifier le quartier — Administration DoyaImmo')
@section('page_title', 'Modifier le quartier')
@section('page_sub', $quartier->nom)

@section('content')
<style>
    /* ==================== RESPONSIVE ==================== */
    @media (max-width: 768px) {
        .panel {
            padding: 16px !important;
        }
        
        .field input,
        .field select {
            font-size: 14px !important;
            padding: 8px 10px !important;
        }
        
        .check-row {
            font-size: 13px !important;
        }
        
        .panel .btn {
            flex: 1 !important;
            justify-content: center !important;
        }
    }
    
    @media (max-width: 480px) {
        .panel {
            padding: 12px !important;
        }
        
        .field {
            margin-bottom: 12px !important;
        }
        
        .field label {
            font-size: 12px !important;
        }
        
        .field input,
        .field select {
            font-size: 13px !important;
            padding: 6px 8px !important;
        }
        
        .panel .btn {
            width: 100% !important;
            justify-content: center !important;
            font-size: 13px !important;
        }
        
        .panel > div:last-child {
            flex-direction: column !important;
            gap: 8px !important;
        }
    }
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.quartiers.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux quartiers
    </a>
</div>

<div class="panel" style="max-width:600px;margin:0 auto;">
    @if(session('error'))
        <div class="flash-message flash-error" style="margin-bottom:16px;">
            <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.quartiers.update', $quartier) }}">
        @csrf
        @method('PUT')

        <div class="field">
            <label for="nom">
                Nom du quartier <span style="color:var(--red);">*</span>
            </label>
            <input type="text" id="nom" name="nom" value="{{ old('nom', $quartier->nom) }}" 
                   style="width:100%;padding:8px 12px;border:1px solid {{ $errors->has('nom') ? 'var(--red)' : 'var(--border)' }};border-radius:8px;font-size:13px;"
                   required placeholder="Ex: Almadies">
            @error('nom') 
                <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="field" style="margin-top:12px;">
            <label for="ville">
                Ville <span style="color:var(--red);">*</span>
            </label>
            <input type="text" id="ville" name="ville" value="{{ old('ville', $quartier->ville) }}" 
                   style="width:100%;padding:8px 12px;border:1px solid {{ $errors->has('ville') ? 'var(--red)' : 'var(--border)' }};border-radius:8px;font-size:13px;"
                   required placeholder="Ex: Dakar">
            @error('ville') 
                <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="field" style="margin-top:12px;">
            <label class="check-row" style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:normal;">
                <input type="checkbox" name="est_actif" value="1" {{ old('est_actif', $quartier->est_actif) ? 'checked' : '' }} 
                       style="width:16px;height:16px;cursor:pointer;">
                <span>Actif</span>
            </label>
        </div>

        <div style="display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border);margin-top:16px;">
            <button type="submit" class="btn btn-rust" style="flex:1;">
                <i class="fa-solid fa-save"></i> Mettre à jour
            </button>
            <a href="{{ route('admin.quartiers.index') }}" class="btn btn-ghost" style="flex:0.5;">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection