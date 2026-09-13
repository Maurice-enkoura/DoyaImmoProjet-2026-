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
    <header class="ios-avis-hero ios-avis-hero--{{ $sentiment }}">
        <div class="ios-avis-hero__client">
            <div class="ios-avis-hero__avatar ios-avis-hero__avatar--{{ $sentiment }}">
                {{ $initial }}
            </div>
            <div class="ios-avis-hero__identity">
                <h2 class="ios-avis-hero__name">{{ $prenom }} {{ $nom }}</h2>
                <span class="ios-avis-hero__date">
                    <i class="fa-regular fa-calendar"></i>
                    Avis reçu le {{ $evaluation->created_at->translatedFormat('d F Y') }}
                </span>
            </div>
        </div>

        <div class="ios-avis-hero__rating ios-avis-hero__rating--{{ $sentiment }}">
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

    {{-- ═══ CONTENU 2 COLONNES (desktop) ═══ --}}
    <div class="ios-avis-layout">

        {{-- ═══ COLONNE PRINCIPALE ═══ --}}
        <div class="ios-avis-layout__main">

            {{-- ═══ COMMENTAIRE ═══ --}}
            <div class="ios-section-header">Commentaire du client</div>

            <div class="ios-group">
                <div class="ios-cell ios-cell--stacked">
                    <div class="ios-cell__icon ios-cell__icon--blue">
                        <i class="fa-regular fa-message"></i>
                    </div>
                    <div class="ios-cell__body">
                        @if($evaluation->commentaire)
                            <p class="ios-comment ios-comment--{{ $sentiment }}">
                                {{ $evaluation->commentaire }}
                            </p>
                        @else
                            <p class="ios-comment-empty">
                                <i class="fa-regular fa-comment"></i>
                                Aucun commentaire n'a été laissé par ce client.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ═══ BIEN CONCERNÉ ═══ --}}
            @if($bien)
                <div class="ios-section-header">Bien concerné</div>

                <div class="ios-group">
                    <div class="ios-cell ios-cell--stacked">
                        <div class="ios-cell__icon ios-cell__icon--orange">
                            <i class="fa-regular fa-building"></i>
                        </div>
                        <div class="ios-cell__body">
                            <span class="ios-cell__label ios-cell__label--strong">
                                {{ $bien->titre ?? 'Bien' }}
                            </span>
                            @if($bien->adresse)
                                <span class="ios-cell__sublabel">
                                    <i class="fa-solid fa-location-dot" style="font-size:11px;margin-right:4px;"></i>
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

            {{-- ═══ ACTIONS ═══ --}}
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

        {{-- ═══ COLONNE LATÉRALE : RÉPONSE ═══ --}}
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
   DÉTAIL AVIS — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.ios-avis-show {
    --ios-blue:      #007AFF;
    --ios-green:     #34C759;
    --ios-red:       #FF3B30;
    --ios-orange:    #FF9500;
    --ios-yellow:    #FFCC00;
    --ios-gray:      #8E8E93;
    --ios-gray-3:    #C7C7CC;
    --ios-gray-5:    #E5E5EA;
    --ios-gray-6:    #F2F2F7;

    --ios-bg:        #F2F2F7;
    --ios-card:      #FFFFFF;
    --ios-label:     #000000;
    --ios-label-2:   rgba(60, 60, 67, 0.6);
    --ios-label-3:   rgba(60, 60, 67, 0.3);
    --ios-separator: rgba(60, 60, 67, 0.29);

    --ios-radius:    10px;
    --ios-radius-lg: 12px;

    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text",
                 "Helvetica Neue", Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;

    background: var(--ios-bg);
    padding-bottom: 40px;
}

/* ═══ HERO CLIENT ═══ */
.ios-avis-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 22px 26px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    margin-bottom: 22px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
}
.ios-avis-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 5px;
    height: 100%;
}
.ios-avis-hero--positif::before { background: var(--ios-green); }
.ios-avis-hero--neutre::before  { background: var(--ios-orange); }
.ios-avis-hero--negatif::before { background: var(--ios-red); }

.ios-avis-hero__client {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
    flex: 1;
}

.ios-avis-hero__avatar {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    font-weight: 600;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
    letter-spacing: -0.5px;
}
.ios-avis-hero__avatar--positif { background: linear-gradient(135deg, #34C759, #2E9B5F); }
.ios-avis-hero__avatar--neutre  { background: linear-gradient(135deg, #FF9500, #F57C00); }
.ios-avis-hero__avatar--negatif { background: linear-gradient(135deg, #FF3B30, #E53935); }

.ios-avis-hero__identity { min-width: 0; }
.ios-avis-hero__name {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--ios-label);
    line-height: 1.2;
    letter-spacing: -0.4px;
}
.ios-avis-hero__date {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--ios-label-2);
    letter-spacing: -0.1px;
}
.ios-avis-hero__date i { font-size: 12px; opacity: .8; }

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
    font-size: 22px;
    color: var(--ios-gray-3);
    transition: color .2s;
}
.ios-avis-hero__stars .fa-star.is-on {
    color: var(--ios-yellow);
    filter: drop-shadow(0 2px 4px rgba(255, 204, 0, .35));
}

.ios-avis-hero__score {
    font-size: 24px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -0.5px;
}
.ios-avis-hero__score small {
    font-size: 14px;
    font-weight: 600;
    opacity: .6;
    margin-left: 2px;
}
.ios-avis-hero__rating--positif .ios-avis-hero__score { color: var(--ios-green); }
.ios-avis-hero__rating--neutre  .ios-avis-hero__score { color: var(--ios-orange); }
.ios-avis-hero__rating--negatif .ios-avis-hero__score { color: var(--ios-red); }

/* ═══ LAYOUT 2 COLONNES (desktop) ═══ */
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
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--ios-label-2);
    margin: 24px 0 8px;
    padding: 0 4px;
}
.ios-section-header:first-child { margin-top: 0; }

/* ═══ GROUPED LIST ═══ */
.ios-group {
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    overflow: hidden;
    margin-bottom: 4px;
}
.ios-group--padded { padding: 18px; }

.ios-cell {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 16px;
    min-height: 44px;
    position: relative;
    background: var(--ios-card);
    transition: background 0.1s;
}
.ios-cell + .ios-cell::before {
    content: '';
    position: absolute;
    top: 0;
    left: 16px;
    right: 0;
    height: 0.5px;
    background: var(--ios-separator);
}
.ios-cell--stacked { align-items: flex-start; padding: 16px 18px; }

.ios-cell__icon {
    width: 30px;
    height: 30px;
    border-radius: 7px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 15px;
    color: #fff;
}
.ios-cell__icon--blue   { background: var(--ios-blue); }
.ios-cell__icon--green  { background: var(--ios-green); }
.ios-cell__icon--orange { background: var(--ios-orange); }
.ios-cell__icon--red    { background: var(--ios-red); }
.ios-cell__icon--gray   { background: var(--ios-gray); }
.ios-cell__icon--rust   { background: #B4532A; }

.ios-cell__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.ios-cell__label {
    font-size: 15px;
    font-weight: 400;
    color: var(--ios-label);
    letter-spacing: -0.2px;
}
.ios-cell__label--strong { font-weight: 600; font-size: 16px; }
.ios-cell__sublabel {
    font-size: 13.5px;
    color: var(--ios-label-2);
    letter-spacing: -0.1px;
}

/* ═══ COMMENTAIRE ═══ */
.ios-comment {
    font-size: 15.5px;
    line-height: 1.65;
    color: var(--ios-label);
    font-style: italic;
    margin: 0;
    padding: 4px 0 4px 14px;
    border-left: 3px solid var(--ios-gray-3);
    letter-spacing: -0.1px;
}
.ios-comment--positif { border-left-color: var(--ios-green); }
.ios-comment--neutre  { border-left-color: var(--ios-orange); }
.ios-comment--negatif { border-left-color: var(--ios-red); }

.ios-comment-empty {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 0;
    color: var(--ios-label-3);
    font-size: 14px;
    font-style: italic;
    margin: 0;
    line-height: 1.5;
}
.ios-comment-empty i {
    font-size: 18px;
    opacity: 0.6;
}

/* ═══ BIEN TAGS ═══ */
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
    padding: 5px 12px;
    background: var(--ios-gray-6);
    border-radius: 100px;
    font-size: 12.5px;
    font-weight: 500;
    color: var(--ios-label-2);
    letter-spacing: -0.1px;
}
.ios-bien-tag i { color: #B4532A; font-size: 11px; }

/* ═══ RÉPONSE EXISTANTE ═══ */
.ios-reponse-existing {
    padding: 14px 16px;
    background: rgba(0, 122, 255, 0.08);
    border-radius: 10px;
    border-left: 3px solid var(--ios-blue);
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
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    color: var(--ios-blue);
}
.ios-reponse-existing__status i { font-size: 12px; }

.ios-reponse-existing__date {
    font-size: 11.5px;
    color: var(--ios-label-2);
    font-weight: 500;
}

.ios-reponse-existing__text {
    font-size: 14px;
    line-height: 1.55;
    color: var(--ios-label);
    margin: 0;
    letter-spacing: -0.1px;
}

/* ═══ FORMULAIRE ═══ */
.ios-response-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.ios-response-form__label {
    font-size: 13px;
    font-weight: 600;
    color: var(--ios-label-2);
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.is-required { color: #B4532A; margin-left: 2px; }

.ios-response-form__textarea {
    width: 100%;
    padding: 14px 16px;
    border: 1px solid var(--ios-separator);
    border-radius: 12px;
    font-size: 15px;
    font-family: inherit;
    line-height: 1.5;
    resize: vertical;
    min-height: 130px;
    transition: border .2s, box-shadow .2s;
    color: var(--ios-label);
    background: var(--ios-gray-6);
    letter-spacing: -0.1px;
}
.ios-response-form__textarea:focus {
    outline: none;
    border-color: var(--ios-blue);
    background: var(--ios-card);
    box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.15);
}
.ios-response-form__textarea::placeholder {
    color: var(--ios-label-3);
}

.ios-response-form__error {
    display: block;
    color: var(--ios-red);
    font-size: 12.5px;
    font-weight: 500;
    padding: 0 4px;
}
.ios-response-form__helper {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 12px;
    background: rgba(255, 204, 0, 0.12);
    border-radius: 10px;
    font-size: 12.5px;
    color: #8B4B15;
    line-height: 1.45;
    letter-spacing: -0.1px;
}
.ios-response-form__helper i {
    color: var(--ios-yellow);
    font-size: 13px;
    flex-shrink: 0;
    margin-top: 1px;
}

/* ═══ ACTIONS INLINE (desktop) ═══ */
.ios-actions-inline {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

/* ═══ BOUTONS iOS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 14px 20px;
    border-radius: var(--ios-radius-lg);
    font-size: 16px;
    font-weight: 600;
    font-family: inherit;
    letter-spacing: -0.2px;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: opacity 0.1s, transform 0.1s;
    white-space: nowrap;
    min-height: 48px;
}
.ios-btn:active { transform: scale(0.98); opacity: 0.85; }

.ios-btn--primary { background: #B4532A; color: #fff; }
.ios-btn--primary:hover { background: #9A4523; }
.ios-btn--gray    { background: var(--ios-gray-5); color: var(--ios-blue); }
.ios-btn--gray:hover { background: #D8D8DD; }
.ios-btn--full    { width: 100%; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

/* Desktop : conteneur large */
@media (min-width: 1101px) {
    .ios-avis-show {
        max-width: 1200px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }
}

/* Tablette : 1 colonne */
@media (max-width: 1100px) and (min-width: 821px) {
    .ios-avis-layout {
        grid-template-columns: 1fr;
        gap: 16px;
    }

    .ios-avis-show {
        max-width: 900px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }
}

/* Mobile iOS */
@media (max-width: 820px) {

    .ios-avis-show {
        margin: 0 -16px;
        padding: 0 16px 40px;
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
    }
    .ios-avis-hero__rating {
        align-items: flex-start;
        width: 100%;
        flex-direction: row;
        justify-content: space-between;
    }
    .ios-avis-hero__stars .fa-star { font-size: 18px; }
    .ios-avis-hero__score { font-size: 20px; }

    .ios-actions-inline {
        flex-direction: column;
        gap: 8px;
    }
    .ios-actions-inline .ios-btn {
        width: 100%;
        flex: 0 0 auto;
    }
}

@media (max-width: 480px) {
    .ios-avis-hero { padding: 16px; }
    .ios-avis-hero__avatar { width: 54px; height: 54px; font-size: 21px; }
    .ios-avis-hero__name { font-size: 19px; }
    .ios-avis-hero__stars .fa-star { font-size: 17px; }
    .ios-avis-hero__score { font-size: 20px; }

    .ios-comment { font-size: 14px; }
    .ios-bien-tag { font-size: 11.5px; padding: 3px 10px; }

    .ios-response-form__textarea {
        font-size: 14px;
        min-height: 110px;
    }
    .ios-btn { font-size: 15px; padding: 13px 18px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush