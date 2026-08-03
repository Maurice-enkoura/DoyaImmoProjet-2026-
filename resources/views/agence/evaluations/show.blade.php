@extends('layouts.dashboard-agence')

@section('title', 'Détail de l\'avis — DoyaImmo')
@section('page_title', 'Détail de l\'avis')
@section('page_sub', 'Répondez à l\'avis de votre client')

@section('content')
<div class="view active">
    <!-- Bouton retour -->
    <div class="back-action">
        <a href="{{ route('agence.evaluations.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux avis
        </a>
    </div>

    <!-- Carte principale -->
    <div class="avis-detail-card">
        <!-- En-tête -->
        <div class="avis-detail-header">
            <div class="avis-client-info">
                <div class="client-avatar-large">
                    {{ strtoupper(substr($evaluation->particulier->user->prenom ?? 'C', 0, 1)) }}
                </div>
                <div>
                    <h3>{{ $evaluation->particulier->user->prenom ?? '' }} {{ $evaluation->particulier->user->nom ?? '' }}</h3>
                    <p class="sub">
                        <i class="fa-regular fa-calendar"></i>
                        Avis reçu le {{ $evaluation->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <div class="avis-rating-large">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa-solid fa-star {{ $i <= $evaluation->note ? 'active' : '' }}"></i>
                @endfor
                <span>{{ $evaluation->note }}/5</span>
            </div>
        </div>

        <!-- Corps -->
        <div class="avis-detail-body">
            <!-- Commentaire -->
            <div class="comment-section">
                <h4 class="section-title">
                    <i class="fa-regular fa-message" style="color:var(--rust);"></i>
                    Commentaire du client
                </h4>
                <div class="comment-content">
                    {{ $evaluation->commentaire ?? 'Aucun commentaire' }}
                </div>
            </div>

            <!-- Informations du bien -->
            @if(isset($evaluation->proposition) && $evaluation->proposition && isset($evaluation->proposition->bien))
                <div class="bien-section">
                    <h4 class="section-title">
                        <i class="fa-regular fa-building" style="color:var(--rust);"></i>
                        Bien concerné
                    </h4>
                    <div class="bien-info">
                        <div class="bien-title">{{ $evaluation->proposition->bien->titre ?? 'Bien' }}</div>
                        <div class="bien-address">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $evaluation->proposition->bien->adresse ?? 'Adresse non spécifiée' }}
                        </div>
                        <div class="bien-details">
                            <span class="bien-detail">
                                <i class="fa-regular fa-vector-square"></i>
                                {{ $evaluation->proposition->bien->surface ?? 0 }} m²
                            </span>
                            <span class="bien-detail">
                                <i class="fa-regular fa-bed"></i>
                                {{ $evaluation->proposition->bien->nombre_chambres ?? 0 }} ch.
                            </span>
                            <span class="bien-detail">
                                <i class="fa-regular fa-bath"></i>
                                {{ $evaluation->proposition->bien->nombre_salles_bain ?? 0 }} sdb
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Réponse de l'agence -->
            <div class="response-section">
                <h4 class="section-title">
                    <i class="fa-regular fa-reply" style="color:var(--rust);"></i>
                    Votre réponse
                </h4>

                @if($evaluation->reponse_agence)
                    <div class="response-existing">
                        <div class="response-content">
                            <div class="response-text">{{ $evaluation->reponse_agence }}</div>
                            <div class="response-date">
                                <i class="fa-regular fa-calendar"></i>
                                Répondu le {{ $evaluation->date_reponse ? $evaluation->date_reponse->format('d/m/Y') : '' }}
                            </div>
                        </div>
                        <div class="response-actions">
                            <button class="btn btn-ghost btn-sm" onclick="toggleEditResponse()">
                                <i class="fa-regular fa-pen-to-square"></i> Modifier
                            </button>
                        </div>
                    </div>
                @endif

                <form id="responseForm" method="POST" action="{{ route('agence.evaluations.repondre', $evaluation) }}" style="{{ $evaluation->reponse_agence ? 'display:none;' : '' }}">
                    @csrf
                    <div class="form-group">
                        <label for="reponse">Votre réponse <span class="required">*</span></label>
                        <textarea name="reponse" id="reponse" rows="4" placeholder="Répondez à l'avis de votre client..." required>{{ old('reponse', $evaluation->reponse_agence) }}</textarea>
                        <div class="helper-text">
                            <i class="fa-regular fa-info-circle"></i>
                            Une réponse professionnelle et courtoise montre votre engagement envers vos clients.
                        </div>
                        @error('reponse') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-rust">
                            <i class="fa-solid fa-paper-plane"></i>
                            {{ $evaluation->reponse_agence ? 'Modifier la réponse' : 'Envoyer la réponse' }}
                        </button>
                        @if($evaluation->reponse_agence)
                            <button type="button" class="btn btn-ghost" onclick="toggleEditResponse()">Annuler</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="avis-detail-footer">
            <a href="{{ route('agence.evaluations.index') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-list"></i> Tous les avis
            </a>
            <a href="{{ route('agence.dashboard') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;">
                <i class="fa-solid fa-arrow-left"></i> Retour au tableau de bord
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleEditResponse() {
        const form = document.getElementById('responseForm');
        const existing = document.querySelector('.response-existing');
        
        if (form.style.display === 'none') {
            form.style.display = 'block';
            if (existing) {
                existing.style.display = 'none';
            }
        } else {
            form.style.display = 'none';
            if (existing) {
                existing.style.display = 'block';
            }
        }
    }
</script>
@endpush

@push('styles')
<style>
    /* ===================== BACK ===================== */
    .back-action {
        margin-bottom: 20px;
    }

    /* ===================== CARTE ===================== */
    .avis-detail-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    /* ===================== HEADER ===================== */
    .avis-detail-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        background: #FAFBFC;
    }

    .avis-client-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .client-avatar-large {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--rust-soft);
        color: var(--rust);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 22px;
        flex-shrink: 0;
    }

    .avis-client-info h3 {
        font-family: var(--display);
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }

    .avis-client-info .sub {
        font-size: 13px;
        color: var(--muted);
        margin: 0;
    }

    .avis-client-info .sub i {
        margin-right: 4px;
    }

    .avis-rating-large {
        text-align: right;
        flex-shrink: 0;
    }

    .avis-rating-large .fa-star {
        color: #D4D8E0;
        font-size: 18px;
    }

    .avis-rating-large .fa-star.active {
        color: #F5A623;
    }

    .avis-rating-large span {
        display: block;
        font-weight: 700;
        font-size: 16px;
        color: var(--text-soft);
        margin-top: 4px;
    }

    /* ===================== BODY ===================== */
    .avis-detail-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .section-title {
        font-family: var(--display);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-soft);
    }

    /* ===================== COMMENTAIRE ===================== */
    .comment-content {
        padding: 14px 18px;
        background: #F7F9FC;
        border-radius: 10px;
        font-size: 14px;
        color: var(--text-soft);
        line-height: 1.7;
        border: 1px solid var(--border);
    }

    /* ===================== BIEN ===================== */
    .bien-info {
        padding: 14px 18px;
        background: #F7F9FC;
        border-radius: 10px;
        border: 1px solid var(--border);
    }

    .bien-title {
        font-weight: 600;
        font-size: 15px;
        color: var(--ink);
    }

    .bien-address {
        font-size: 13px;
        color: var(--muted);
        margin-top: 4px;
    }

    .bien-address i {
        margin-right: 4px;
    }

    .bien-details {
        display: flex;
        gap: 16px;
        margin-top: 8px;
        flex-wrap: wrap;
    }

    .bien-detail {
        font-size: 13px;
        color: var(--text-soft);
    }

    .bien-detail i {
        margin-right: 4px;
        color: var(--muted);
    }

    /* ===================== REPONSE ===================== */
    .response-existing {
        padding: 14px 18px;
        background: #E3F2FD;
        border-radius: 10px;
        border-left: 4px solid #0D47A1;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
    }

    .response-text {
        font-size: 14px;
        color: var(--text-soft);
        line-height: 1.7;
    }

    .response-date {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }

    .response-date i {
        margin-right: 4px;
    }

    .response-actions {
        flex-shrink: 0;
    }

    /* ===================== FORMULAIRE ===================== */
    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-soft);
        margin-bottom: 4px;
    }

    .required {
        color: var(--rust);
        margin-left: 2px;
    }

    .form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        font-family: inherit;
        resize: vertical;
        transition: border 0.2s;
        min-height: 100px;
    }

    .form-group textarea:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }

    .helper-text {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }

    .helper-text i {
        margin-right: 4px;
    }

    .error {
        display: block;
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
    }

    /* ===================== FOOTER ===================== */
    .avis-detail-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border);
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        background: #FAFBFC;
    }

    /* ===================== BOUTONS ===================== */
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

    .btn-rust {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .btn-rust:hover {
        background: #9A4523;
        border-color: #9A4523;
        color: #fff;
    }

    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }

    .btn-ghost:hover {
        background: var(--border);
        color: var(--ink);
    }

    .btn-sm {
        padding: 4px 12px;
        font-size: 12px;
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .avis-detail-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .avis-rating-large {
            text-align: left;
        }

        .avis-detail-body {
            padding: 16px 18px;
        }

        .response-existing {
            flex-direction: column;
            align-items: stretch;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            justify-content: center;
        }

        .avis-detail-footer {
            flex-direction: column;
        }

        .avis-detail-footer .btn {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .avis-client-info {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .client-avatar-large {
            width: 48px;
            height: 48px;
            font-size: 18px;
        }

        .avis-client-info h3 {
            font-size: 16px;
        }

        .bien-details {
            flex-direction: column;
            gap: 4px;
        }

        .avis-rating-large .fa-star {
            font-size: 16px;
        }
    }
</style>
@endpush