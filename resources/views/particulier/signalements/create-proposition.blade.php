@extends('layouts.dashboard')

@section('title', 'Signaler une proposition — DoyaImmo')
@section('page_title', 'Signaler une proposition')
@section('page_sub', 'Signalez une proposition inappropriée ou frauduleuse')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('particulier.propositions.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux propositions
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
        <!-- Informations de la proposition -->
        <div style="display:flex;align-items:center;gap:16px;padding:16px;background:#F7F9FC;border-radius:10px;margin-bottom:24px;border:1px solid var(--border);">
            <div style="width:48px;height:48px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--rust);flex-shrink:0;">
                {{ strtoupper(substr($proposition->agence->nom_agence, 0, 1)) }}
            </div>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:15px;">{{ $proposition->agence->nom_agence }}</div>
                <div style="font-size:13px;color:var(--muted);">
                    {{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}
                </div>
                <div style="font-size:13px;font-weight:600;color:var(--rust);">
                    {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                </div>
            </div>
            <div>
                <span class="status-pill status-{{ $proposition->statut->value }}">
                    {{ $proposition->statut->label() }}
                </span>
            </div>
        </div>

        <h3 style="font-family:var(--display);font-size:17px;margin-bottom:8px;">
            <i class="fa-solid fa-flag" style="color:#C62828;"></i> Signaler cette proposition
        </h3>
        <p style="font-size:13px;color:var(--muted);margin-bottom:20px;">
            Votre signalement sera examiné par un administrateur. Merci de fournir des informations précises.
        </p>

        <form method="POST" action="{{ route('particulier.signalements.store') }}">
            @csrf
            <input type="hidden" name="signalable_id" value="{{ $proposition->id }}">
            <input type="hidden" name="signalable_type" value="App\Models\Proposition">

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    Motif du signalement <span style="color:#C62828;">*</span>
                </label>
                <select name="motif" class="form-control @error('motif') is-invalid @enderror" 
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;background:#fff;">
                    <option value="">Sélectionnez un motif</option>
                    @foreach($motifs as $key => $label)
                        <option value="{{ $key }}" {{ old('motif') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('motif')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
            </div>

            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:6px;">
                    Description <span style="color:#C62828;">*</span>
                </label>
                <textarea name="description" rows="5" 
                          placeholder="Décrivez précisément le problème que vous avez rencontré..."
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;resize:vertical;min-height:100px;">{{ old('description') }}</textarea>
                @error('description')
                    <small style="color:#C62828;font-size:12px;">{{ $message }}</small>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Minimum 10 caractères
                </div>
            </div>

            <div style="display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-rust" style="background:#C62828;border-color:#C62828;">
                    <i class="fa-solid fa-paper-plane"></i> Envoyer le signalement
                </button>
                <a href="{{ route('particulier.propositions.show', $proposition) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-acceptee {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-refusee {
        background: #FFEBEE;
        color: #C62828;
    }
    .form-control.is-invalid {
        border-color: #C62828 !important;
    }
</style>
@endpush