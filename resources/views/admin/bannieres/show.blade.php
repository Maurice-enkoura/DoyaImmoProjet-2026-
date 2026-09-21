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
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-active { background: #E8F5E9; color: #1E7A47; }
    .status-en_attente { background: #FFF8E1; color: #F57C00; }
    .status-inactif { background: #F5F5F5; color: var(--muted); }
    @media (max-width: 600px) {
        .info-grid { grid-template-columns: 1fr; }
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
    <div class="panel">
        <h3 style="font-size:15px;font-weight:600;margin-bottom:12px;">
            <i class="fa-solid fa-image"></i> Aperçu
        </h3>
        @if($banniere->image_url)
            <img src="{{ $banniere->image_url }}" alt="{{ $banniere->titre }}" class="banner-detail-image">
        @else
            <div style="text-align:center;padding:40px;color:var(--muted);background:#F7F9FC;border-radius:8px;">
                <i class="fa-regular fa-image" style="font-size:48px;display:block;margin-bottom:12px;"></i>
                <p>Aucune image</p>
            </div>
        @endif
    </div>

    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:12px;">
            <h3 style="font-size:15px;font-weight:600;">
                <i class="fa-solid fa-info-circle"></i> Informations
            </h3>
            <span class="status-pill {{ $banniere->statut_class }}">
                {{ $banniere->statut_reel }}
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
                        Gauche
                    @elseif($banniere->position_texte == 'centre')
                        Centre
                    @else
                        Droite
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="label">Lien</div>
                <div class="value">
                    @if($banniere->lien)
                        <a href="{{ $banniere->lien }}" target="_blank" style="color:var(--rust);">
                            {{ Str::limit($banniere->lien, 30) }}
                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:12px;"></i>
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
            @if($banniere->jours_restants !== null && $banniere->jours_restants > 0)
            <div class="info-item" style="grid-column:span 2;background:#FFF8E1;">
                <div class="label">Jours restants</div>
                <div class="value" style="color:#F57C00;">
                    <i class="fa-solid fa-clock"></i> {{ $banniere->jours_restants }} jour(s)
                </div>
            </div>
            @endif
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

        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.bannieres.edit', $banniere) }}" class="btn btn-rust">
                <i class="fa-solid fa-pen"></i> Modifier
            </a>

            @if($banniere->est_actif)
                <form action="{{ route('admin.bannieres.desactiver', $banniere) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-ghost" onclick="return confirm('Désactiver cette bannière ?')">
                        <i class="fa-solid fa-eye-slash"></i> Désactiver
                    </button>
                </form>
            @else
                @if($banniere->necessiteReprogrammation())
                    <form action="{{ route('admin.bannieres.activer', $banniere) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="date_debut" value="{{ now()->format('Y-m-d') }}">
                        <input type="hidden" name="date_fin" value="{{ $banniere->date_fin?->format('Y-m-d') }}">
                        <button type="submit" class="btn btn-ghost" style="color:#F57C00;">
                            <i class="fa-solid fa-calendar-check"></i> Reprogrammer et activer
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.bannieres.activer', $banniere) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-ghost">
                            <i class="fa-solid fa-eye"></i> Activer
                        </button>
                    </form>
                @endif
            @endif

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