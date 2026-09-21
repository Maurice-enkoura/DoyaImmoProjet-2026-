@extends('layouts.dashboard-agence')

@section('title', 'Détail de l\'avis — DoyaImmo')
@section('page_title', 'Détail de l\'avis')
@section('page_sub', 'Répondez à l\'avis de votre client')

@section('content')
<div class="view active ios-avis-show">

    @php
        $note        = (int) $evaluation->note;
        $sentiment   = $note >= 4 ? 'positif' : ($note === 3 ? 'neutre' : 'negatif');
        $hasReponse  = !empty($evaluation->reponse_agence);
        $prenom      = $evaluation->particulier->user->prenom ?? '';
        $nom         = $evaluation->particulier->user->nom    ?? '';
        $initial     = strtoupper(mb_substr($prenom ?: 'C', 0, 1));
        $bien        = $evaluation->proposition->bien ?? null;
    @endphp

    {{-- ═══ HERO CLIENT ═══ --}}
    <header class="ios-avis-hero">
        <div class="ios-avis-hero__client">
            <div class="ios-avis-hero__avatar">{{ $initial }}</div>
            <div class="ios-avis-hero__identity">
                <h2 class="ios-avis-hero__name">{{ $prenom }} {{ $nom }}</h2>
                <span class="ios-avis-hero__date">
                    <i class="fa-regular fa-calendar"></i>
                    Avis reçu le {{ $evaluation->created_at->translatedFormat('d F Y') }}
                </span>
            </div>
        </div>

        <div class="ios-avis-hero__rating">
            <div class="ios-avis-hero__stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa-solid fa-star {{ $i <= $note ? 'is-on' : '' }}"></i>
                @endfor
            </div>
            <div class="ios-avis-hero__score">
                {{ $note }}<small>/5</small>
            </div>
        </div>
    </header>

    {{-- ═══ LAYOUT 2 COLONNES ═══ --}}
    <div class="ios-avis-layout">

        {{-- COLONNE PRINCIPALE --}}
        <div class="ios-avis-layout__main">

            {{-- Commentaire --}}
            <div class="ios-section-header">Commentaire du client</div>

            <div class="ios-group">
                <div class="ios-cell ios-cell--stacked">
                    <div class="ios-cell__body">
                        @if($evaluation->commentaire)
                            <p class="ios-comment">{{ $evaluation->commentaire }}</p>
                        @else
                            <p class="ios-comment-empty">
                                <i class="fa-regular fa-comment"></i>
                                Aucun commentaire n'a été laissé par ce client.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bien concerné --}}
            @if($bien)
                <div class="ios-section-header">Bien concerné</div>

                <div class="ios-group">
                    <div class="ios-cell ios-cell--stacked">
                        <div class="ios-cell__body">
                            <span class="ios-cell__label ios-cell__label--strong">
                                {{ $bien->titre ?? 'Bien' }}
                            </span>
                            @if($bien->adresse)
                                <span class="ios-cell__sublabel">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $bien->adresse }}
                                </span>
                            @endif

                            <div class="ios-bien-tags">
                                @if($bien->surface)
                                    <span class="ios-bien-tag">
                                        <i class="fa-regular fa-square"></i> {{ $bien->surface }} m²
                                    </span>
                                @endif
                                @if($bien->nombre_chambres)
                                    <span class="ios-bien-tag">
                                        <i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.
                                    </span>
                                @endif
                                @if($bien->nombre_salles_bain)
                                    <span class="ios-bien-tag">
                                        <i class="fa-solid fa-bath"></i> {{ $bien->nombre_salles_bain }} sdb
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="ios-section-header">Actions</div>

            <div class="ios-actions-inline">
                <a href="{{ route('agence.evaluations.index') }}" class="ios-btn ios-btn--gray">
                    <i class="fa-solid fa-list"></i> Tous les avis
                </a>
                <a href="{{ route('agence.dashboard') }}" class="ios-btn ios-btn--gray">
                    <i class="fa-solid fa-chart-pie"></i> Tableau de bord
                </a>
            </div>
        </div>

        {{-- COLONNE LATÉRALE : RÉPONSE --}}
        <aside class="ios-avis-layout__side">

            <div class="ios-section-header">Votre réponse</div>

            <div class="ios-group ios-group--padded">
                @if($hasReponse)
                    <div class="ios-reponse-existing">
                        <div class="ios-reponse-existing__head">
                            <span class="ios-reponse-existing__status">
                                <i class="fa-solid fa-circle-check"></i> Déjà répondu
                            </span>
                            @if($evaluation->date_reponse)
                                <span class="ios-reponse-existing__date">
                                    {{ $evaluation->date_reponse->translatedFormat('d F Y') }}
                                </span>
                            @endif
                        </div>
                        <p class="ios-reponse-existing__text">{{ $evaluation->reponse_agence }}</p>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('agence.evaluations.repondre', $evaluation) }}"
                      class="ios-response-form">
                    @csrf

                    <label for="reponse" class="ios-response-form__label">
                        @if($hasReponse)
                            Modifier votre réponse <span class="is-required">*</span>
                        @else
                            Votre réponse <span class="is-required">*</span>
                        @endif
                    </label>

                    <textarea name="reponse"
                              id="reponse"
                              rows="4"
                              required
                              placeholder="Répondez à l'avis de votre client..."
                              class="ios-response-form__textarea">{{ old('reponse', $evaluation->reponse_agence) }}</textarea>

                    @error('reponse')
                        <span class="ios-response-form__error">{{ $message }}</span>
                    @enderror

                    <div class="ios-response-form__helper">
                        <i class="fa-regular fa-lightbulb"></i>
                        <span>Une réponse professionnelle et courtoise montre votre engagement.</span>
                    </div>

                    <button type="submit" class="ios-btn ios-btn--primary ios-btn--full">
                        <i class="fa-solid fa-paper-plane"></i>
                        {{ $hasReponse ? 'Mettre à jour' : 'Envoyer la réponse' }}
                    </button>
                </form>
            </div>
        </aside>
    </div>

</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DÉTAIL AVIS — Design épuré
   ═══════════════════════════════════════════════════════════ */

.ios-avis-show {
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ HERO CLIENT ═══ */
.ios-avis-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 20px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.ios-avis-hero__client {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
    flex: 1;
}

.ios-avis-hero__avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    flex-shrink: 0;
}

.ios-avis-hero__identity { min-width: 0; }
.ios-avis-hero__name {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--ink);
    line-height: 1.2;
    letter-spacing: -.3px;
}
.ios-avis-hero__date {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--muted);
}
.ios-avis-hero__date i { font-size: 12px; }

.ios-avis-hero__rating {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 6px;
    flex-shrink: 0;
}
.ios-avis-hero__stars {
    display: inline-flex;
    gap: 4px;
}
.ios-avis-hero__stars .fa-star {
    font-size: 20px;
    color: #D8D8DD;
}
.ios-avis-hero__stars .fa-star.is-on {
    color: var(--rust);
}

.ios-avis-hero__score {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -.4px;
    color: var(--ink);
}
.ios-avis-hero__score small {
    font-size: 13px;
    font-weight: 600;
    opacity: .6;
    margin-left: 2px;
}

/* ═══ LAYOUT 2 COLONNES ═══ */
.ios-avis-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 22px;
    align-items: start;
}
.ios-avis-layout__main { min-width: 0; }
.ios-avis-layout__side { min-width: 0; }

/* ═══ SECTION HEADER ═══ */
.ios-section-header {
    font-size: 12.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
    margin: 22px 0 8px;
    padding: 0 4px;
}
.ios-section-header:first-child { margin-top: 0; }

/* ═══ GROUPES ═══ */
.ios-group {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 4px;
}
.ios-group--padded { padding: 18px; }

.ios-cell {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 16px;
    position: relative;
    background: #fff;
}
.ios-cell--stacked { align-items: flex-start; padding: 16px 18px; }

.ios-cell__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.ios-cell__label {
    font-size: 14.5px;
    font-weight: 400;
    color: var(--ink);
}
.ios-cell__label--strong {
    font-family: var(--display);
    font-weight: 700;
    font-size: 15px;
    color: var(--ink);
}
.ios-cell__sublabel {
    font-size: 13px;
    color: var(--muted);
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ios-cell__sublabel i {
    font-size: 11px;
    color: var(--muted);
}

/* ═══ COMMENTAIRE ═══ */
.ios-comment {
    font-size: 14.5px;
    line-height: 1.7;
    color: var(--text-soft);
    font-style: italic;
    margin: 0;
    padding: 4px 0 4px 14px;
    border-left: 3px solid #D8D8DD;
}

.ios-comment-empty {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 0;
    color: var(--muted);
    font-size: 13.5px;
    font-style: italic;
    margin: 0;
    line-height: 1.5;
}
.ios-comment-empty i {
    font-size: 17px;
    opacity: 0.7;
}

/* ═══ TAGS BIEN ═══ */
.ios-bien-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 8px;
}
.ios-bien-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    background: #F5F5F7;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
    color: var(--text-soft);
}
.ios-bien-tag i { color: var(--muted); font-size: 11px; }

/* ═══ RÉPONSE EXISTANTE ═══ */
.ios-reponse-existing {
    padding: 14px 16px;
    background: #F5F5F7;
    border-radius: 12px;
    margin-bottom: 16px;
}
.ios-reponse-existing__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 6px;
    flex-wrap: wrap;
}
.ios-reponse-existing__status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--text-soft);
}
.ios-reponse-existing__status i {
    font-size: 12px;
    color: var(--rust);
}

.ios-reponse-existing__date {
    font-size: 11.5px;
    color: var(--muted);
    font-weight: 500;
}

.ios-reponse-existing__text {
    font-size: 13.5px;
    line-height: 1.55;
    color: var(--text-soft);
    margin: 0;
}

/* ═══ FORMULAIRE ═══ */
.ios-response-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.ios-response-form__label {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
}
.is-required { color: var(--rust); margin-left: 2px; }

.ios-response-form__textarea {
    width: 100%;
    padding: 13px 15px;
    border: 1px solid var(--border);
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    line-height: 1.5;
    resize: vertical;
    min-height: 130px;
    transition: border-color .15s;
    color: var(--ink);
    background: #fff;
}
.ios-response-form__textarea:focus {
    outline: none;
    border-color: var(--rust);
}
.ios-response-form__textarea::placeholder {
    color: var(--muted);
}

.ios-response-form__error {
    display: block;
    color: var(--c-danger);
    font-size: 12.5px;
    font-weight: 500;
    padding: 0 4px;
}
.ios-response-form__helper {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 12px;
    background: #F5F5F7;
    border-radius: 10px;
    font-size: 12.5px;
    color: var(--text-soft);
    line-height: 1.45;
}
.ios-response-form__helper i {
    color: var(--muted);
    font-size: 13px;
    flex-shrink: 0;
    margin-top: 1px;
}

/* ═══ ACTIONS INLINE ═══ */
.ios-actions-inline {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* ═══ BOUTONS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 18px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
    min-height: 44px;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.ios-btn--primary:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
.ios-btn--gray {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--gray:hover {
    background: #F5F5F7;
    border-color: #d8d8d8;
    color: var(--ink);
}
.ios-btn--full { width: 100%; }


/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (min-width: 1101px) {
    .ios-avis-show {
        max-width: 1200px;
        margin: 0 auto;
    }
}

@media (max-width: 1100px) and (min-width: 821px) {
    .ios-avis-layout {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .ios-avis-show {
        max-width: 900px;
        margin: 0 auto;
    }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .ios-avis-show {
        margin: 0 -16px;
        padding: 0 16px 40px;
        background: transparent;
    }

    .ios-avis-layout {
        grid-template-columns: 1fr;
        gap: 0;
    }

    .ios-avis-layout__main,
    .ios-avis-layout__side {
        display: contents;
    }

    .ios-avis-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 18px;
        gap: 14px;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-avis-hero__avatar {
        width: 52px; height: 52px;
        font-size: 20px;
        background: #F5F5F7;
        border: none;
    }
    .ios-avis-hero__name { font-size: 19px; }

    .ios-avis-hero__rating {
        align-items: flex-start;
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
    }
    .ios-avis-hero__stars .fa-star { font-size: 18px; }
    .ios-avis-hero__score { font-size: 20px; }

    .ios-group {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .ios-cell--stacked { padding: 16px; }

    .ios-comment {
        font-size: 14px;
        padding-left: 12px;
    }

    .ios-bien-tag {
        background: #F5F5F7;
        font-size: 12px;
        padding: 4px 11px;
    }

    .ios-reponse-existing {
        background: #F5F5F7;
        border-radius: 12px;
    }

    .ios-response-form__textarea {
        background: #F5F5F7;
        border: none;
        font-size: 14px;
    }
    .ios-response-form__textarea:focus {
        background: #fff;
        box-shadow: 0 0 0 1px var(--rust);
    }

    .ios-actions-inline {
        flex-direction: column;
        gap: 8px;
    }
    .ios-actions-inline .ios-btn {
        width: 100%;
        flex: 0 0 auto;
    }
    .ios-btn--gray {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
}

@media (max-width: 480px) {
    .ios-avis-hero { padding: 16px; }
    .ios-avis-hero__avatar { width: 48px; height: 48px; font-size: 18px; }
    .ios-avis-hero__name { font-size: 18px; }
    .ios-avis-hero__stars .fa-star { font-size: 16px; }
    .ios-avis-hero__score { font-size: 18px; }

    .ios-comment { font-size: 13.5px; }
    .ios-bien-tag { font-size: 11.5px; padding: 3px 10px; }

    .ios-response-form__textarea {
        font-size: 13.5px;
        min-height: 110px;
    }
    .ios-btn { font-size: 13.5px; padding: 12px 16px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush