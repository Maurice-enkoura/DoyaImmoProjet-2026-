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
                $valides = $documents->filter(function($doc) { return $doc->est_valide; })->count();
                $enAttente = $documents->filter(function($doc) { return $doc->est_en_attente; })->count();
                $rejetes = $documents->filter(function($doc) { return $doc->est_rejete; })->count();
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
        <form id="validationForm" action="{{ route('admin.agences.valider-documents', $agence) }}" method="POST">
            @csrf
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <select id="validationStatut" name="statut" style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;flex:1;">
                    <option value="valide"> Valider</option>
                    <option value="rejete"> Rejeter</option>
                </select>
                <button type="submit" class="btn btn-rust">
                    <i class="fa-solid fa-check"></i> Appliquer
                </button>
            </div>
            <div style="margin-top:8px;">
                <input type="text" id="validationCommentaire" name="commentaire" placeholder="Commentaire (optionnel)" 
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
                    @php
                        // Déterminer la couleur de la bordure selon le statut
                        $borderColor = $document->est_valide ? 'var(--green)' : ($document->est_rejete ? 'var(--red)' : '#E65100');
                        
                        // Déterminer le statut CSS
                        $statusClass = $document->est_valide ? 'status-active' : ($document->est_rejete ? 'status-annule' : 'status-en_attente');
                        $statusLabel = $document->statut_validation_label;
                    @endphp
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#F7F9FC;border-radius:8px;border-left:4px solid {{ $borderColor }};">
                        
                        <!-- Checkbox pour sélectionner -->
                        <div>
                            <input type="checkbox" name="document_ids[]" value="{{ $document->id }}" 
                                   style="width:16px;height:16px;cursor:pointer;">
                        </div>

                        <!-- Informations du document -->
                        <div style="flex:1;">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                <span style="font-weight:600;font-size:14px;">
                                    <i class="fa-solid fa-file-pdf" style="color:var(--red);"></i>
                                    {{ $document->type_document_label }}
                                </span>
                                <span style="font-size:11px;color:var(--muted);">
                                    ({{ basename($document->nom_fichier) }})
                                </span>
                            </div>
                            <div style="font-size:12px;color:var(--muted);margin-top:4px;">
                                <i class="fa-solid fa-clock"></i> 
                                Téléchargé le {{ $document->created_at->format('d/m/Y H:i') }}
                            </div>
                            @if($document->est_valide && $document->date_validation)
                                <div style="font-size:12px;color:var(--green);margin-top:2px;">
                                    ✓ Validé le {{ \Carbon\Carbon::parse($document->date_validation)->format('d/m/Y') }}
                                    @if($document->validePar)
                                        par {{ $document->validePar->user->prenom ?? '' }} {{ $document->validePar->user->nom ?? '' }}
                                    @endif
                                </div>
                            @endif
                            @if($document->est_rejete && $document->commentaire)
                                <div style="font-size:12px;color:var(--red);margin-top:2px;">
                                    ✗ Rejeté: "{{ $document->commentaire }}"
                                </div>
                            @endif
                        </div>

                        <!-- Statut -->
                        <div>
                            <span class="status-pill {{ $statusClass }}">
                                <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                                {{ $statusLabel }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div style="display:flex;gap:4px;">
                            <a href="{{ $document->fichier_url }}" target="_blank" class="btn btn-ghost btn-sm" title="Voir le document">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ $document->fichier_url }}" download class="btn btn-ghost btn-sm" title="Télécharger">
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

@push('styles')
<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-active {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-annule {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }

    .btn-ghost:hover {
        background: var(--border);
    }

    .btn-rust {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
    }

    .btn-sm {
        padding: 4px 12px;
        font-size: 12px;
    }

    .btn-success {
        background: #1E7A47;
        color: #fff;
        border-color: #1E7A47;
    }

    .btn-success:hover {
        background: #145A35;
        color: #fff;
    }

    .btn-danger {
        background: #C62828;
        color: #fff;
        border-color: #C62828;
    }

    .btn-danger:hover {
        background: #B71C1C;
        color: #fff;
    }

    .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 24px;
        margin-bottom: 20px;
    }

    .panel:last-child {
        margin-bottom: 0;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush