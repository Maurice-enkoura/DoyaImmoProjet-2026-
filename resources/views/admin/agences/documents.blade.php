@extends('layouts.admin')

@section('title', 'Documents de l\'agence — Administration DoyaImmo')
@section('page_title', 'Documents de l\'agence')
@section('page_sub', $agence->nom_agence)

@section('content')
<div style="margin-bottom:20px;">
    <a href="{{ route('admin.agences.show', $agence) }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour à l'agence
    </a>
</div>

<div class="grid-2">
    <!-- Informations de l'agence -->
    <div class="panel">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            @if($agence->logo)
                <img src="{{ asset('storage/' . $agence->logo) }}" 
                     alt="{{ $agence->nom_agence }}" 
                     style="width:50px;height:50px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
            @else
                <div style="width:50px;height:50px;border-radius:10px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--gold);">
                    {{ strtoupper(substr($agence->nom_agence, 0, 2)) }}
                </div>
            @endif
            <div>
                <h3 style="font-size:16px;font-weight:700;">{{ $agence->nom_agence }}</h3>
                <div style="font-size:12px;color:var(--muted);">
                    <i class="fa-solid fa-envelope"></i> {{ $agence->user->email ?? '' }}
                </div>
                <div style="font-size:12px;color:var(--muted);">
                    <i class="fa-solid fa-calendar"></i> Inscrite le {{ $agence->created_at->format('d/m/Y') }}
                </div>
            </div>
        </div>

        <!-- Statut des documents -->
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
            @php
                $total = $documents->count();
                $valides = $documents->where('statut_validation', 'valide')->count();
                $enAttente = $documents->where('statut_validation', 'en_attente')->count();
                $rejetes = $documents->where('statut_validation', 'rejete')->count();
            @endphp
            <div style="padding:8px;background:#F7F9FC;border-radius:8px;text-align:center;">
                <div style="font-size:20px;font-weight:700;">{{ $total }}</div>
                <div style="font-size:11px;color:var(--muted);">Total</div>
            </div>
            <div style="padding:8px;background:#E8F5E9;border-radius:8px;text-align:center;">
                <div style="font-size:20px;font-weight:700;color:var(--green);">{{ $valides }}</div>
                <div style="font-size:11px;color:var(--muted);">Validés</div>
            </div>
            <div style="padding:8px;background:#FFF8E1;border-radius:8px;text-align:center;">
                <div style="font-size:20px;font-weight:700;color:#E65100;">{{ $enAttente }}</div>
                <div style="font-size:11px;color:var(--muted);">En attente</div>
            </div>
        </div>
    </div>

    <!-- Actions de validation -->
    <div class="panel">
        <h3 style="font-size:15px;font-weight:600;margin-bottom:12px;">
            <i class="fa-solid fa-check-double"></i> Valider des documents
        </h3>
        <p style="font-size:13px;color:var(--muted);margin-bottom:12px;">
            Sélectionnez les documents à valider ou rejeter.
        </p>
        <form action="{{ route('admin.agences.valider-documents', $agence) }}" method="POST">
            @csrf
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <select name="statut" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;flex:1;">
                    <option value="valide">Valider</option>
                    <option value="rejete">Rejeter</option>
                </select>
                <button type="submit" class="btn btn-rust">
                    <i class="fa-solid fa-check"></i> Appliquer
                </button>
            </div>
            <div style="margin-top:8px;">
                <input type="text" name="commentaire" placeholder="Commentaire (optionnel)" 
                       style="width:100%;padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
            </div>
        </form>
    </div>
</div>

<!-- Liste des documents -->
<div class="panel" style="margin-top:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <h3 style="font-size:15px;font-weight:600;">
            <i class="fa-solid fa-file"></i> Tous les documents
            <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:8px;">
                ({{ $documents->count() }} documents)
            </span>
        </h3>
        <span style="font-size:12px;color:var(--muted);">
            <i class="fa-solid fa-asterisk" style="color:var(--red);"></i> Cliquez sur l'icône pour voir le document
        </span>
    </div>

    @if($documents->count() > 0)
        <form id="documentsForm" method="POST">
            @csrf
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($documents as $document)
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#F7F9FC;border-radius:8px;border-left:4px solid 
                        @if($document->statut_validation === 'valide') var(--green)
                        @elseif($document->statut_validation === 'rejete') var(--red)
                        @else #E65100
                        @endif;">
                        
                        <!-- Checkbox pour sélectionner -->
                        <div>
                            <input type="checkbox" name="document_ids[]" value="{{ $document->id }}" 
                                   style="width:16px;height:16px;cursor:pointer;">
                        </div>

                        <!-- Informations du document -->
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span style="font-weight:600;font-size:14px;">
                                    <i class="fa-solid fa-file-pdf" style="color:var(--red);"></i>
                                    {{ $document->type_document }}
                                </span>
                                <span style="font-size:11px;color:var(--muted);">
                                    ({{ basename($document->fichier) }})
                                </span>
                            </div>
                            <div style="font-size:12px;color:var(--muted);margin-top:4px;">
                                <i class="fa-solid fa-clock"></i> 
                                Téléchargé le {{ $document->created_at->format('d/m/Y H:i') }}
                            </div>
                            @if($document->statut_validation === 'valide' && $document->date_validation)
                                <div style="font-size:12px;color:var(--green);">
                                    ✓ Validé le {{ \Carbon\Carbon::parse($document->date_validation)->format('d/m/Y') }}
                                    @if($document->validePar)
                                        par {{ $document->validePar->user->prenom ?? '' }} {{ $document->validePar->user->nom ?? '' }}
                                    @endif
                                </div>
                            @endif
                            @if($document->statut_validation === 'rejete' && $document->commentaire)
                                <div style="font-size:12px;color:var(--red);">
                                    ✗ Rejeté: "{{ $document->commentaire }}"
                                </div>
                            @endif
                        </div>

                        <!-- Statut -->
                        <div>
                            @if($document->statut_validation === 'valide')
                                <span class="status-pill status-active">Validé</span>
                            @elseif($document->statut_validation === 'rejete')
                                <span class="status-pill status-annule">Rejeté</span>
                            @else
                                <span class="status-pill status-en_attente">En attente</span>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div style="display:flex;gap:4px;">
                            <a href="{{ asset('storage/' . $document->fichier) }}" target="_blank" class="btn btn-ghost btn-sm" title="Voir le document">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ asset('storage/' . $document->fichier) }}" download class="btn btn-ghost btn-sm" title="Télécharger">
                                <i class="fa-solid fa-download"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>

        <!-- Actions en bas -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;">
            <button type="button" class="btn btn-ghost btn-sm" onclick="selectAllDocuments()">
                <i class="fa-solid fa-check-double"></i> Tout sélectionner
            </button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="deselectAllDocuments()">
                <i class="fa-solid fa-square"></i> Tout désélectionner
            </button>
            <button type="button" class="btn btn-success btn-sm" onclick="validateSelected('valide')">
                <i class="fa-solid fa-check"></i> Valider la sélection
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="validateSelected('rejete')">
                <i class="fa-solid fa-times"></i> Rejeter la sélection
            </button>
        </div>
    @else
        <p style="color:var(--muted);font-size:13px;text-align:center;padding:40px 0;">
            <i class="fa-solid fa-file-circle-exclamation" style="font-size:32px;display:block;margin-bottom:8px;"></i>
            Aucun document téléchargé par cette agence.
        </p>
    @endif
</div>

<script>
    function selectAllDocuments() {
        document.querySelectorAll('input[name="document_ids[]"]').forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function deselectAllDocuments() {
        document.querySelectorAll('input[name="document_ids[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    function validateSelected(statut) {
        const form = document.getElementById('documentsForm');
        const selected = document.querySelectorAll('input[name="document_ids[]"]:checked');
        
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins un document.');
            return;
        }
        
        if (!confirm(`Voulez-vous vraiment ${statut === 'valide' ? 'valider' : 'rejeter'} ${selected.length} document(s) ?`)) {
            return;
        }
        
        // Ajouter le statut au formulaire
        const statutInput = document.createElement('input');
        statutInput.type = 'hidden';
        statutInput.name = 'statut';
        statutInput.value = statut;
        form.appendChild(statutInput);
        
        // Ajouter le commentaire
        const commentaire = prompt('Ajouter un commentaire (optionnel) :');
        if (commentaire !== null) {
            const commentaireInput = document.createElement('input');
            commentaireInput.type = 'hidden';
            commentaireInput.name = 'commentaire';
            commentaireInput.value = commentaire;
            form.appendChild(commentaireInput);
        }
        
        form.action = "{{ route('admin.agences.valider-documents', $agence) }}";
        form.submit();
    }
</script>
@endsection