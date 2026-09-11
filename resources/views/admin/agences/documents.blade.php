@extends('layouts.admin')

@section('title', 'Documents de l\'agence — Administration DoyaImmo')
@section('page_title', 'Documents de l\'agence')
@section('page_sub', $agence->nom_agence)

@section('content')
<div style="margin-bottom:20px;">
    <!-- ✅ CORRIGÉ : Utilisation du slug -->
    <a href="{{ route('admin.agences.show', $agence->slug) }}" class="btn btn-ghost btn-sm">
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
        
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button type="button" class="btn btn-success btn-sm" onclick="ouvrirModalValidation('valide')">
                <i class="fa-solid fa-check"></i> Valider la sélection
            </button>
            <button type="button" class="btn btn-danger btn-sm" onclick="ouvrirModalValidation('rejete')">
                <i class="fa-solid fa-times"></i> Rejeter la sélection
            </button>
        </div>
        <div style="margin-top:8px;">
            <button type="button" class="btn btn-ghost btn-sm" onclick="selectAllDocuments()">
                <i class="fa-solid fa-check-double"></i> Tout sélectionner
            </button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="deselectAllDocuments()">
                <i class="fa-solid fa-square"></i> Tout désélectionner
            </button>
        </div>
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
        <form id="documentsForm" method="POST" action="{{ route('admin.agences.valider-documents', $agence->slug) }}">
            @csrf
            <input type="hidden" name="statut" id="statutInput" value="">
            <input type="hidden" name="commentaire" id="commentaireInput" value="">
            
            <div style="display:flex;flex-direction:column;gap:8px;">
                @foreach($documents as $document)
                    @php
                        $borderColor = $document->est_valide ? 'var(--green)' : ($document->est_rejete ? 'var(--red)' : '#E65100');
                        $statusClass = $document->est_valide ? 'status-active' : ($document->est_rejete ? 'status-annule' : 'status-en_attente');
                        $statusLabel = $document->statut_validation_label;
                    @endphp
                    <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#F7F9FC;border-radius:8px;border-left:4px solid {{ $borderColor }};">
                        
                        <div>
                            <input type="checkbox" name="document_ids[]" value="{{ $document->id }}" 
                                   style="width:16px;height:16px;cursor:pointer;">
                        </div>

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

                        <div>
                            <span class="status-pill {{ $statusClass }}">
                                <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                                {{ $statusLabel }}
                            </span>
                        </div>

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
    @else
        <p style="color:var(--muted);font-size:13px;text-align:center;padding:40px 0;">
            <i class="fa-solid fa-file-circle-exclamation" style="font-size:32px;display:block;margin-bottom:8px;"></i>
            Aucun document téléchargé par cette agence.
        </p>
    @endif
</div>

<!-- ✅ MODAL DE CONFIRMATION -->
<div id="validationModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:#fff; border-radius:12px; padding:24px; max-width:400px; width:90%; box-shadow:0 8px 32px rgba(0,0,0,0.2);">
        <h3 style="margin-bottom:12px; font-size:16px;">Confirmation</h3>
        <p id="modalMessage" style="font-size:14px; color:var(--text-soft); margin-bottom:16px;"></p>
        
        <div id="motifContainer" style="display:none; margin-bottom:16px;">
            <label style="font-size:13px; font-weight:600; color:var(--text-soft); display:block; margin-bottom:4px;">
                Motif du rejet <span style="color:var(--red);">*</span>
            </label>
            <textarea id="motifRejet" rows="3" style="width:100%; padding:10px; border:1px solid var(--border); border-radius:8px; font-size:13px; resize:vertical;" placeholder="Veuillez indiquer le motif du rejet..."></textarea>
            <span style="color:var(--red); font-size:12px; display:none;" id="motifError">Le motif est obligatoire</span>
        </div>
        
        <div style="display:flex; gap:8px; justify-content:flex-end;">
            <button type="button" class="btn btn-ghost" onclick="fermerModal()">
                Annuler
            </button>
            <button type="button" class="btn btn-rust" onclick="confirmerValidation()">
                Confirmer
            </button>
        </div>
    </div>
</div>

<script>
    let actionStatut = '';
    let actionCommentaire = '';

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

    function ouvrirModalValidation(statut) {
        const selected = document.querySelectorAll('input[name="document_ids[]"]:checked');
        
        if (selected.length === 0) {
            alert('Veuillez sélectionner au moins un document.');
            return;
        }
        
        actionStatut = statut;
        const modal = document.getElementById('validationModal');
        const modalMessage = document.getElementById('modalMessage');
        const motifContainer = document.getElementById('motifContainer');
        
        if (statut === 'valide') {
            modalMessage.textContent = `Voulez-vous vraiment valider ${selected.length} document(s) ?`;
            motifContainer.style.display = 'none';
        } else {
            modalMessage.textContent = `Voulez-vous vraiment rejeter ${selected.length} document(s) ?`;
            motifContainer.style.display = 'block';
        }
        
        modal.style.display = 'flex';
    }

    function fermerModal() {
        document.getElementById('validationModal').style.display = 'none';
        document.getElementById('motifError').style.display = 'none';
        document.getElementById('motifRejet').value = '';
    }

    function confirmerValidation() {
        if (actionStatut === 'rejete') {
            const motif = document.getElementById('motifRejet').value.trim();
            if (!motif) {
                document.getElementById('motifError').style.display = 'block';
                return;
            }
            actionCommentaire = motif;
        } else {
            const commentaire = prompt('Ajouter un commentaire (optionnel) :');
            actionCommentaire = commentaire !== null ? commentaire : '';
        }
        
        const form = document.getElementById('documentsForm');
        const statutInput = document.getElementById('statutInput');
        const commentaireInput = document.getElementById('commentaireInput');
        
        statutInput.value = actionStatut;
        commentaireInput.value = actionCommentaire;
        
        fermerModal();
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