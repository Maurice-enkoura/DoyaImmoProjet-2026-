@extends('layouts.admin')

@section('title', 'Détail de la bannière - DoyaImmo')
@section('page_title', 'Détail de la bannière')
@section('page_sub', $banniere->titre)

@section('content')
<style>
    .banner-detail-image {
        max-width: 100%;
        max-height: 400px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid var(--border);
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 16px;
    }
    .info-item {
        padding: 12px 16px;
        background: #F7F9FC;
        border-radius: 8px;
    }
    .info-item .label {
        font-size: 11px;
        color: var(--muted);
    }
    .info-item .value {
        font-weight: 600;
        font-size: 14px;
        margin-top: 2px;
    }
    @media (max-width: 600px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.bannieres.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux bannières
    </a>
    <a href="{{ route('admin.bannieres.edit', $banniere) }}" class="btn btn-rust btn-sm" style="margin-left:8px;">
        <i class="fa-solid fa-pen"></i> Modifier
    </a>
</div>

<div class="grid-2">
    <!-- Image -->
    <div class="panel">
        <h3 style="font-size:15px;font-weight:600;margin-bottom:12px;">
            <i class="fa-solid fa-image"></i> Aperçu
        </h3>
        @if($banniere->image)
            <img src="{{ asset('storage/' . $banniere->image) }}" alt="{{ $banniere->titre }}" class="banner-detail-image">
        @else
            <div style="text-align:center;padding:40px;color:var(--muted);background:#F7F9FC;border-radius:8px;">
                <i class="fa-regular fa-image" style="font-size:48px;display:block;margin-bottom:12px;"></i>
                <p>Aucune image</p>
            </div>
        @endif
    </div>

    <!-- Informations -->
    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
            <h3 style="font-size:15px;font-weight:600;">
                <i class="fa-solid fa-info-circle"></i> Informations
            </h3>
            <span class="status-pill {{ $banniere->est_actif ? 'status-active' : 'status-inactif' }}">
                {{ $banniere->est_actif ? ' Actif' : ' Inactif' }}
            </span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="label">Titre</div>
                <div class="value">{{ $banniere->titre }}</div>
            </div>
            <div class="info-item">
                <div class="label">Sous-titre</div>
                <div class="value">{{ $banniere->sous_titre ?? 'Non renseigné' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Position du texte</div>
                <div class="value">
                    @if($banniere->position_texte == 'gauche')
                        ⬅️ Gauche
                    @elseif($banniere->position_texte == 'centre')
                        ⬛ Centre
                    @else
                        ➡️ Droite
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="label">Lien</div>
                <div class="value">
                    @if($banniere->lien)
                        <a href="{{ $banniere->lien }}" target="_blank" style="color:var(--rust);">
                            {{ Str::limit($banniere->lien, 30) }}
                            <i class="fa-solid fa-external-link-alt" style="font-size:12px;"></i>
                        </a>
                    @else
                        <span style="color:var(--muted);">Non renseigné</span>
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="label">Date de début</div>
                <div class="value">
                    @if($banniere->date_debut)
                        {{ $banniere->date_debut->format('d/m/Y H:i') }}
                    @else
                        <span style="color:var(--muted);">Immédiate</span>
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="label">Date de fin</div>
                <div class="value">
                    @if($banniere->date_fin)
                        {{ $banniere->date_fin->format('d/m/Y H:i') }}
                    @else
                        <span style="color:var(--muted);">Illimitée</span>
                    @endif
                </div>
            </div>
            <div class="info-item" style="grid-column:span 2;">
                <div class="label">Ordre d'affichage</div>
                <div class="value">{{ $banniere->ordre }}</div>
            </div>
            <div class="info-item" style="grid-column:span 2;">
                <div class="label">Date de création</div>
                <div class="value">{{ $banniere->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-item" style="grid-column:span 2;">
                <div class="label">Dernière mise à jour</div>
                <div class="value">{{ $banniere->updated_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <!-- Actions -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.bannieres.edit', $banniere) }}" class="btn btn-rust">
                <i class="fa-solid fa-pen"></i> Modifier
            </a>
            <form action="{{ route('admin.bannieres.toggle', $banniere) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-ghost">
                    <i class="fa-solid {{ $banniere->est_actif ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                    {{ $banniere->est_actif ? 'Désactiver' : 'Activer' }}
                </button>
            </form>
            <form action="{{ route('admin.bannieres.destroy', $banniere) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette bannière ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash-can"></i> Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection