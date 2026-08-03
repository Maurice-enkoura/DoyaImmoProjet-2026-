@extends('layouts.dashboard')

@section('title', 'Détail de l\'avis — DoyaImmo')
@section('page_title', 'Détail de l\'avis')
@section('page_sub', 'Votre évaluation de l\'agence')

@section('content')
<div class="view active">
    <!-- Bouton retour -->
    <div class="back-action">
        <a href="{{ route('particulier.evaluations.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes avis
        </a>
    </div>

    <!-- Carte principale -->
    <div class="evaluation-detail-card">
        <!-- En-tête -->
        <div class="evaluation-detail-header">
            <div class="evaluation-agency">
                <div class="agency-avatar">
                    {{ strtoupper(substr($evaluation->agence->nom_agence, 0, 1)) }}
                </div>
                <div>
                    <h3>{{ $evaluation->agence->nom_agence }}</h3>
                    <p class="sub">
                        <i class="fa-regular fa-calendar"></i>
                        Évalué le {{ $evaluation->created_at->format('d/m/Y') }}
                    </p>
                </div>
            </div>
            <span class="status-pill status-published">
                <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                Publié
            </span>
        </div>

        <!-- Corps -->
        <div class="evaluation-detail-body">
            <!-- Note -->
            <div class="rating-section">
                <div class="rating-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star {{ $i <= $evaluation->note ? 'active' : '' }}"></i>
                    @endfor
                </div>
                <div class="rating-label">
                    {{ $evaluation->note }} / 5
                    <span class="rating-text">
                        @if($evaluation->note >= 4)
                            Très satisfait
                        @elseif($evaluation->note >= 3)
                            Satisfait
                        @elseif($evaluation->note >= 2)
                            Insatisfait
                        @else
                            Très insatisfait
                        @endif
                    </span>
                </div>
            </div>

            <!-- Commentaire -->
            <div class="comment-section">
                <h4 class="section-title">
                    <i class="fa-regular fa-message" style="color:var(--rust);"></i>
                    Mon avis
                </h4>
                <div class="comment-content">
                    {{ $evaluation->commentaire }}
                </div>
            </div>

            <!-- Informations sur l'agence -->
            <div class="agency-info-section">
                <h4 class="section-title">
                    <i class="fa-regular fa-building-columns" style="color:var(--rust);"></i>
                    Informations sur l'agence
                </h4>
                <div class="agency-info-grid">
                    <div class="info-item">
                        <span class="info-label">Nom</span>
                        <span class="info-value">{{ $evaluation->agence->nom_agence }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Quartier</span>
                        <span class="info-value">{{ $evaluation->agence->quartier ?? 'Non spécifié' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Adresse</span>
                        <span class="info-value">{{ $evaluation->agence->adresse ?? 'Non spécifiée' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Note moyenne</span>
                        <span class="info-value" style="color:#F5A623;">
                            <i class="fa-solid fa-star"></i>
                            {{ number_format($evaluation->agence->evaluations->avg('note') ?? 0, 1) }} / 5
                            ({{ $evaluation->agence->evaluations->count() }} avis)
                        </span>
                    </div>
                </div>
            </div>

            <!-- Réponse de l'agence -->
            @if($evaluation->reponse_agence)
                <div class="response-section">
                    <h4 class="section-title">
                        <i class="fa-regular fa-reply" style="color:var(--rust);"></i>
                        Réponse de l'agence
                    </h4>
                    <div class="response-content">
                        <div class="response-header">
                            <span class="response-agency">{{ $evaluation->agence->nom_agence }}</span>
                            <span class="response-date">{{ $evaluation->date_reponse ? $evaluation->date_reponse->format('d/m/Y') : '' }}</span>
                        </div>
                        <p>{{ $evaluation->reponse_agence }}</p>
                    </div>
                </div>
            @else
                <div class="no-response">
                    <i class="fa-regular fa-clock"></i>
                    <span>L'agence n'a pas encore répondu à votre avis.</span>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="evaluation-detail-footer">
            <a href="{{ route('particulier.evaluations.index') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-list"></i> Tous mes avis
            </a>
            <!-- ✅ Route corrigée -->
            <a href="{{ route('agences.public.show', $evaluation->agence) }}" class="btn btn-rust btn-sm" style="margin-left:auto;">
                <i class="fa-solid fa-eye"></i> Voir le profil de l'agence
            </a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ===================== BACK ===================== */
    .back-action {
        margin-bottom: 20px;
    }

    /* ===================== CARTE ===================== */
    .evaluation-detail-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    /* ===================== HEADER ===================== */
    .evaluation-detail-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        background: #FAFBFC;
    }

    .evaluation-agency {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .agency-avatar {
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

    .evaluation-agency h3 {
        font-family: var(--display);
        font-size: 18px;
        font-weight: 700;
        margin: 0;
    }

    .evaluation-agency .sub {
        font-size: 13px;
        color: var(--muted);
        margin: 0;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-published {
        background: #E8F5E9;
        color: #1E7A47;
    }

    /* ===================== BODY ===================== */
    .evaluation-detail-body {
        padding: 24px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* ===================== RATING ===================== */
    .rating-section {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        background: #F7F9FC;
        border-radius: 10px;
        border: 1px solid var(--border);
    }

    .rating-stars {
        font-size: 32px;
        color: #D4D8E0;
    }

    .rating-stars .active {
        color: #F5A623;
    }

    .rating-label {
        font-size: 18px;
        font-weight: 700;
        color: var(--ink);
        margin-top: 8px;
    }

    .rating-text {
        font-size: 14px;
        font-weight: 400;
        color: var(--muted);
        margin-left: 8px;
    }

    /* ===================== SECTION TITLE ===================== */
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
    .comment-section {
        border-bottom: 1px solid var(--border);
        padding-bottom: 20px;
    }

    .comment-content {
        padding: 14px 18px;
        background: #F7F9FC;
        border-radius: 10px;
        font-size: 14px;
        color: var(--text-soft);
        line-height: 1.7;
        border: 1px solid var(--border);
    }

    /* ===================== AGENCE INFO ===================== */
    .agency-info-section {
        border-bottom: 1px solid var(--border);
        padding-bottom: 20px;
    }

    .agency-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 12px;
        color: var(--muted);
        font-weight: 500;
    }

    .info-value {
        font-size: 14px;
        font-weight: 500;
        color: var(--ink);
    }

    /* ===================== REPONSE ===================== */
    .response-section {
        border-bottom: 1px solid var(--border);
        padding-bottom: 20px;
    }

    .response-content {
        padding: 14px 18px;
        background: #E3F2FD;
        border-radius: 10px;
        border-left: 4px solid #0D47A1;
    }

    .response-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .response-agency {
        font-weight: 600;
        color: var(--ink);
    }

    .response-date {
        font-size: 12px;
        color: var(--muted);
    }

    .response-content p {
        font-size: 14px;
        color: var(--text-soft);
        line-height: 1.7;
        margin: 0;
    }

    /* ===================== NO RESPONSE ===================== */
    .no-response {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
        background: #F7F9FC;
        border-radius: 10px;
        color: var(--muted);
        font-size: 13px;
        border: 1px solid var(--border);
    }

    .no-response i {
        font-size: 16px;
        color: var(--muted);
    }

    /* ===================== FOOTER ===================== */
    .evaluation-detail-footer {
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
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13.5px;
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
        padding: 6px 14px;
        font-size: 12.5px;
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .evaluation-detail-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .evaluation-detail-body {
            padding: 16px 18px;
        }

        .agency-info-grid {
            grid-template-columns: 1fr;
        }

        .rating-stars {
            font-size: 28px;
        }

        .rating-label {
            font-size: 16px;
        }

        .response-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 4px;
        }

        .evaluation-detail-footer {
            flex-direction: column;
        }

        .evaluation-detail-footer .btn {
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .evaluation-agency {
            flex-direction: column;
            text-align: center;
        }

        .evaluation-agency h3 {
            font-size: 16px;
        }

        .agency-avatar {
            width: 48px;
            height: 48px;
            font-size: 18px;
        }

        .rating-stars {
            font-size: 24px;
        }

        .rating-label {
            font-size: 14px;
        }

        .rating-text {
            font-size: 12px;
            display: block;
            margin-left: 0;
        }

        .comment-content {
            font-size: 13px;
            padding: 12px 14px;
        }

        .response-content {
            padding: 12px 14px;
        }

        .response-content p {
            font-size: 13px;
        }
    }
</style>
@endpush