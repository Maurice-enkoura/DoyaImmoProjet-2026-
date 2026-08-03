@extends('layouts.dashboard')

@section('title', 'Évaluer l\'agence — DoyaImmo')
@section('page_title', 'Évaluer l\'agence')
@section('page_sub', 'Partagez votre expérience avec ' . $agence->nom_agence)

@section('content')
<div class="view active">
    <!-- Bouton retour -->
    <div class="back-action">
        <a href="{{ route('particulier.evaluations.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes avis
        </a>
    </div>

    <!-- Carte principale -->
    <div class="evaluation-card">
        <!-- En-tête -->
        <div class="evaluation-header">
            <div class="evaluation-agency">
                <div class="agency-avatar">
                    {{ strtoupper(substr($agence->nom_agence, 0, 1)) }}
                </div>
                <div>
                    <h3>{{ $agence->nom_agence }}</h3>
                    <p class="sub">
                        <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                        {{ number_format($agence->evaluations->avg('note') ?? 0, 1) }} / 5
                        ({{ $agence->evaluations->count() }} avis)
                    </p>
                </div>
            </div>
        </div>

        <!-- Corps -->
        <div class="evaluation-body">
            <form method="POST" action="{{ route('particulier.evaluations.store') }}">
                @csrf
                <input type="hidden" name="agence_id" value="{{ $agence->id }}">

                <!-- Note -->
                <div class="form-group">
                    <label class="form-label">Votre note <span class="required">*</span></label>
                    <div class="stars-input">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" class="star-btn" data-value="{{ $i }}" onclick="setNote({{ $i }})">
                                <i class="fa-solid fa-star"></i>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="note" id="note" value="0" required>
                    <div class="note-text" id="noteText">Sélectionnez une note</div>
                    @error('note') <span class="error">{{ $message }}</span> @enderror
                </div>

                <!-- Commentaire -->
                <div class="form-group">
                    <label class="form-label" for="commentaire">Votre avis <span class="required">*</span></label>
                    <textarea name="commentaire" id="commentaire" rows="5" placeholder="Partagez votre expérience avec cette agence...">{{ old('commentaire') }}</textarea>
                    <div class="helper-text">
                        <i class="fa-regular fa-info-circle"></i>
                        Votre avis aidera d'autres clients à choisir leur agence.
                    </div>
                    @error('commentaire') <span class="error">{{ $message }}</span> @enderror
                </div>

                <!-- Conseils -->
                <div class="tips-box">
                    <h4><i class="fa-regular fa-lightbulb"></i> Conseils pour un avis utile</h4>
                    <ul>
                        <li>Soyez précis et honnête sur votre expérience</li>
                        <li>Décrivez la qualité du service et de l'accompagnement</li>
                        <li>Partagez ce qui vous a plu ou déplu</li>
                        <li>Un avis constructif aide tout le monde</li>
                    </ul>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-rust">
                        <i class="fa-solid fa-paper-plane"></i> Publier mon avis
                    </button>
                    <a href="{{ route('particulier.evaluations.index') }}" class="btn btn-ghost">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ===================== GESTION DES ÉTOILES =====================
    let selectedNote = 0;

    function setNote(note) {
        selectedNote = note;
        document.getElementById('note').value = note;

        // Mettre à jour les étoiles
        const stars = document.querySelectorAll('.star-btn');
        stars.forEach((star, index) => {
            if (index < note) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });

        // Mettre à jour le texte
        const noteText = document.getElementById('noteText');
        const labels = {
            1: 'Très insatisfait',
            2: 'Insatisfait',
            3: 'Moyen',
            4: 'Satisfait',
            5: 'Très satisfait'
        };
        noteText.textContent = labels[note] || 'Sélectionnez une note';
        noteText.style.color = note >= 4 ? '#1E7A47' : note >= 3 ? '#F5A623' : '#C62828';
    }

    // ===================== SURVOL DES ÉTOILES =====================
    document.querySelectorAll('.star-btn').forEach(star => {
        star.addEventListener('mouseenter', function() {
            const value = parseInt(this.dataset.value);
            const stars = document.querySelectorAll('.star-btn');
            stars.forEach((s, index) => {
                if (index < value) {
                    s.classList.add('hover');
                } else {
                    s.classList.remove('hover');
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            document.querySelectorAll('.star-btn').forEach(s => {
                s.classList.remove('hover');
            });
        });
    });
</script>
@endpush

@push('styles')
<style>
    /* ===================== BACK ===================== */
    .back-action {
        margin-bottom: 20px;
    }

    /* ===================== CARTE ===================== */
    .evaluation-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    /* ===================== HEADER ===================== */
    .evaluation-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
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

    /* ===================== BODY ===================== */
    .evaluation-body {
        padding: 24px;
    }

    /* ===================== FORMULAIRE ===================== */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-soft);
        margin-bottom: 8px;
    }

    .required {
        color: var(--rust);
        margin-left: 2px;
    }

    /* ===================== ÉTOILES ===================== */
    .stars-input {
        display: flex;
        gap: 8px;
        margin-bottom: 8px;
    }

    .star-btn {
        background: none;
        border: none;
        font-size: 32px;
        cursor: pointer;
        color: #D4D8E0;
        transition: all 0.2s;
        padding: 4px;
    }

    .star-btn:hover {
        transform: scale(1.1);
    }

    .star-btn.active {
        color: #F5A623;
    }

    .star-btn.hover {
        color: #F5A623;
    }

    .star-btn i {
        pointer-events: none;
    }

    .note-text {
        font-size: 14px;
        font-weight: 500;
        color: var(--muted);
        transition: color 0.3s;
    }

    /* ===================== TEXTAREA ===================== */
    textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        font-family: inherit;
        resize: vertical;
        transition: border 0.2s;
        background: #fff;
        min-height: 120px;
    }

    textarea:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }

    .helper-text {
        font-size: 12px;
        color: var(--muted);
        margin-top: 6px;
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

    /* ===================== TIPS ===================== */
    .tips-box {
        background: #F7F9FC;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 24px;
    }

    .tips-box h4 {
        font-family: var(--display);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-soft);
    }

    .tips-box h4 i {
        color: var(--rust);
        font-size: 16px;
    }

    .tips-box ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .tips-box ul li {
        padding: 4px 0;
        font-size: 13px;
        color: var(--text-soft);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .tips-box ul li::before {
        content: "•";
        color: var(--rust);
        font-weight: 700;
        font-size: 16px;
    }

    /* ===================== ACTIONS ===================== */
    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
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

    .btn-rust:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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
        .evaluation-body {
            padding: 16px 18px;
        }

        .evaluation-header {
            padding: 16px 18px;
        }

        .evaluation-agency {
            flex-direction: column;
            text-align: center;
        }

        .stars-input {
            justify-content: center;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            justify-content: center;
        }

        .star-btn {
            font-size: 28px;
        }

        .tips-box ul li {
            font-size: 12px;
        }
    }

    @media (max-width: 480px) {
        .evaluation-agency h3 {
            font-size: 16px;
        }

        .agency-avatar {
            width: 48px;
            height: 48px;
            font-size: 18px;
        }

        .star-btn {
            font-size: 24px;
        }

        .note-text {
            font-size: 13px;
            text-align: center;
        }

        textarea {
            font-size: 14px;
        }
    }
</style>
@endpush