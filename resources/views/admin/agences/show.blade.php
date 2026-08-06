@extends('layouts.admin')

@section('title', 'Détail de l\'agence — Administration DoyaImmo')
@section('page_title', 'Détail de l\'agence')
@section('page_sub', $agence->nom_agence)

@section('content')
<div style="margin-bottom:20px;">
    <a href="{{ route('admin.agences.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux agences
    </a>
</div>

<div class="grid-2">
    <!-- Informations générales -->
    <div class="panel">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            @if($agence->logo)
                <img src="{{ asset('storage/' . $agence->logo) }}" 
                     alt="{{ $agence->nom_agence }}" 
                     style="width:60px;height:60px;object-fit:cover;border-radius:50%;border:2px solid var(--border);">
            @else
                <div style="width:60px;height:60px;border-radius:50%;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:var(--gold);">
                    {{ strtoupper(substr($agence->nom_agence, 0, 2)) }}
                </div>
            @endif
            <div>
                <h3 style="font-size:18px;font-weight:700;">{{ $agence->nom_agence }}</h3>
                <div style="font-size:13px;color:var(--muted);">
                    <i class="fa-solid fa-user"></i> {{ $agence->user->prenom ?? '' }} {{ $agence->user->nom ?? '' }}
                </div>
                <div style="font-size:13px;color:var(--muted);">
                    <i class="fa-solid fa-envelope"></i> {{ $agence->user->email ?? '' }}
                </div>
                <div style="font-size:13px;color:var(--muted);">
                    <i class="fa-solid fa-phone"></i> {{ $agence->user->telephone ?? 'Non renseigné' }}
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Statut</div>
                <div style="font-weight:600;">
                    @if(!$agence->statut_validation)
                        <span class="status-pill status-en_attente">En attente de validation</span>
                    @elseif(isset($agence->bloque) && $agence->bloque)
                        <span class="status-pill status-annule">Bloquée</span>
                    @else
                        <span class="status-pill status-active">Validée et active</span>
                    @endif
                </div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Membre depuis</div>
                <div style="font-weight:600;">{{ $agence->created_at->format('d/m/Y') }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Biens publiés</div>
                <div style="font-weight:600;">{{ $agence->biens->count() }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Documents</div>
                <div style="font-weight:600;">
                    {{ $agence->documents->count() }}
                    @php
                        $valides = $agence->documents->where('statut_validation', 'valide')->count();
                        $enAttente = $agence->documents->where('statut_validation', 'en_attente')->count();
                        $rejetes = $agence->documents->where('statut_validation', 'rejete')->count();
                    @endphp
                    @if($valides > 0)
                        <span style="color:var(--green);font-size:12px;">({{ $valides }} ✓)</span>
                    @endif
                    @if($enAttente > 0)
                        <span style="color:#E65100;font-size:12px;">({{ $enAttente }} ⏳)</span>
                    @endif
                    @if($rejetes > 0)
                        <span style="color:var(--red);font-size:12px;">({{ $rejetes }} ✗)</span>
                    @endif
                </div>
            </div>
        </div>

        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            @if($agence->adresse)
                <div style="font-size:13px;color:var(--text-soft);">
                    <i class="fa-solid fa-location-dot"></i> {{ $agence->adresse }}
                </div>
            @endif
            @php
                $quartierNom = 'Non défini';
                if (is_object($agence->quartier) && method_exists($agence->quartier, 'getAttribute')) {
                    $quartierNom = $agence->quartier->nom ?? 'Non défini';
                } elseif (is_string($agence->quartier) && !empty($agence->quartier)) {
                    $quartierNom = $agence->quartier;
                } elseif (is_numeric($agence->quartier_id) && $agence->quartier_id > 0) {
                    $quartier = App\Models\Quartier::find($agence->quartier_id);
                    if ($quartier) {
                        $quartierNom = $quartier->nom;
                    }
                }
            @endphp
            <div style="font-size:13px;color:var(--text-soft);">
                <i class="fa-solid fa-map-pin"></i> Quartier: {{ $quartierNom }}
            </div>
            @if($agence->description)
                <div style="margin-top:8px;padding:10px 14px;background:#F7F9FC;border-radius:8px;font-size:13px;color:var(--text-soft);">
                    {{ $agence->description }}
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;">
            @if(!$agence->statut_validation)
                <form action="{{ route('admin.agences.valider', $agence) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Valider cette agence ?')">
                        <i class="fa-solid fa-check"></i> Valider l'agence
                    </button>
                </form>
                <form action="{{ route('admin.agences.refuser', $agence) }}" method="POST">
                    @csrf
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="text" name="motif" placeholder="Motif du refus" 
                               style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;flex:1;">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Refuser cette agence ?')">
                            <i class="fa-solid fa-times"></i> Refuser
                        </button>
                    </div>
                </form>
            @endif

            @if($agence->statut_validation && (!isset($agence->bloque) || !$agence->bloque))
                <form action="{{ route('admin.agences.bloquer', $agence) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Bloquer cette agence ?')">
                        <i class="fa-solid fa-ban"></i> Bloquer
                    </button>
                </form>
            @endif

            @if(isset($agence->bloque) && $agence->bloque)
                <form action="{{ route('admin.agences.debloquer', $agence) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Débloquer cette agence ?')">
                        <i class="fa-solid fa-unlock"></i> Débloquer
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.agences.documents', $agence) }}" class="btn btn-ghost">
                <i class="fa-solid fa-file"></i> Voir les documents
            </a>

            <form action="{{ route('admin.agences.destroy', $agence) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette agence ? Cette action est irréversible.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash-can"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    <!-- Documents -->
    <div class="panel">
        <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
            <i class="fa-solid fa-file"></i> Documents de l'agence
            <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:8px;">
                ({{ $agence->documents->count() }} documents)
            </span>
        </h3>
        @if($agence->documents->count() > 0)
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($agence->documents as $document)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 12px;background:#F7F9FC;border-radius:8px;border-left:4px solid 
                        @if($document->statut_validation === 'valide') var(--green)
                        @elseif($document->statut_validation === 'rejete') var(--red)
                        @else #E65100
                        @endif;">
                        <div>
                            <div style="font-weight:600;font-size:13px;">
                                <i class="fa-solid fa-file-pdf" style="color:var(--red);"></i>
                                {{ $document->type_document }}
                            </div>
                            <div style="font-size:12px;color:var(--muted);">
                                @if($document->statut_validation === 'valide')
                                    <span style="color:var(--green);">✓ Validé</span>
                                    @if($document->date_validation)
                                        <span style="margin-left:8px;">le {{ \Carbon\Carbon::parse($document->date_validation)->format('d/m/Y') }}</span>
                                    @endif
                                @elseif($document->statut_validation === 'rejete')
                                    <span style="color:var(--red);">✗ Rejeté</span>
                                    @if($document->commentaire)
                                        <span style="margin-left:8px;font-style:italic;">"{{ $document->commentaire }}"</span>
                                    @endif
                                @else
                                    <span style="color:#E65100;"> En attente de validation</span>
                                @endif
                            </div>
                        </div>
                        <div style="display:flex;gap:4px;">
                            <a href="{{ asset('storage/' . $document->fichier) }}" target="_blank" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ asset('storage/' . $document->fichier) }}" download class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color:var(--muted);font-size:13px;text-align:center;padding:30px 0;">
                <i class="fa-solid fa-file-circle-exclamation" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                Aucun document téléchargé par cette agence.
            </p>
        @endif
    </div>
</div>
@endsection