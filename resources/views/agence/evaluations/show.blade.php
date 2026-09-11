@extends('layouts.dashboard-agence')

@section('title', 'Détail de l\'avis — DoyaImmo')
@section('page_title', 'Détail de l\'avis')
@section('page_sub', 'Répondez à l\'avis de votre client')

@section('content')
<div class="view active avis-show">

    @php
        $note        = (int) $evaluation->note;
        $sentiment   = $note >= 4 ? 'positif' : ($note === 3 ? 'neutre' : 'negatif');
        $hasReponse  = !empty($evaluation->reponse_agence);
        $prenom      = $evaluation->particulier->user->prenom ?? '';
        $nom         = $evaluation->particulier->user->nom    ?? '';
        $initial     = strtoupper(mb_substr($prenom ?: 'C', 0, 1));
        $bien        = $evaluation->proposition->bien ?? null;
    @endphp

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="avis-show__back">
        <a href="{{ route('agence.evaluations.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux avis
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         HERO CLIENT
    ═══════════════════════════════════════════ --}}
    <header class="avis-hero avis-hero--{{ $sentiment }}">
        <div class="avis-hero__client">
            <div class="avis-hero__avatar avis-hero__avatar--{{ $sentiment }}">
                {{ $initial }}
            </div>
            <div class="avis-hero__identity">
                <h1 class="avis-hero__name">{{ $prenom }} {{ $nom }}</h1>
                <span class="avis-hero__date">
                    <i class="fa-regular fa-calendar"></i>
                    Avis reçu le {{ $evaluation->created_at->translatedFormat('d F Y') }}
                </span>
            </div>
        </div>

        <div class="avis-hero__rating avis-hero__rating--{{ $sentiment }}">
            <div class="avis-hero__stars">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fa-solid fa-star {{ $i <= $note ? 'is-on' : '' }}"></i>
                @endfor
            </div>
            <div class="avis-hero__score">
                {{ $note }}<small>/5</small>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════
         COMMENTAIRE DU CLIENT
    ═══════════════════════════════════════════ --}}
    <section class="panel panel--comment">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-regular fa-message"></i>
            </span>
            <h4>Commentaire du client</h4>
        </header>

        @if($evaluation->commentaire)
            <blockquote class="comment-block comment-block--{{ $sentiment }}">
                {{ $evaluation->commentaire }}
            </blockquote>
        @else
            <div class="comment-empty">
                <i class="fa-regular fa-comment"></i>
                <span>Aucun commentaire n'a été laissé par ce client.</span>
            </div>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════
         BIEN CONCERNÉ
    ═══════════════════════════════════════════ --}}
    @if($bien)
        <section class="panel panel--bien">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-building"></i>
                </span>
                <h4>Bien concerné</h4>
            </header>

            <div class="bien-info">
                <h3 class="bien-info__title">{{ $bien->titre ?? 'Bien' }}</h3>

                @if($bien->adresse)
                    <div class="bien-info__address">
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $bien->adresse }}
                    </div>
                @endif

                <div class="bien-info__details">
                    @if($bien->surface)
                        <span class="bien-detail">
                            <i class="fa-regular fa-square"></i>
                            {{ $bien->surface }} m²
                        </span>
                    @endif
                    @if($bien->nombre_chambres)
                        <span class="bien-detail">
                            <i class="fa-solid fa-bed"></i>
                            {{ $bien->nombre_chambres }} ch.
                        </span>
                    @endif
                    @if($bien->nombre_salles_bain)
                        <span class="bien-detail">
                            <i class="fa-solid fa-bath"></i>
                            {{ $bien->nombre_salles_bain }} sdb
                        </span>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         RÉPONSE DE L'AGENCE
    ═══════════════════════════════════════════ --}}
    <section class="panel panel--response">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-regular fa-reply"></i>
            </span>
            <h4>Votre réponse</h4>
        </header>

        {{-- Réponse existante --}}
        @if($hasReponse)
            <div class="response-existing">
                <div class="response-existing__head">
                    <span class="response-existing__status">
                        <i class="fa-solid fa-circle-check"></i> Déjà répondu
                    </span>
                    @if($evaluation->date_reponse)
                        <span class="response-existing__date">
                            {{ $evaluation->date_reponse->translatedFormat('d F Y') }}
                        </span>
                    @endif
                </div>
                <p class="response-existing__text">{{ $evaluation->reponse_agence }}</p>
            </div>
        @endif

        {{-- Formulaire (toujours visible) --}}
        <form method="POST"
              action="{{ route('agence.evaluations.repondre', $evaluation) }}"
              class="response-form">

            @csrf

            <label for="reponse" class="response-form__label">
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
                      class="response-form__textarea">{{ old('reponse', $evaluation->reponse_agence) }}</textarea>

            @error('reponse')
                <span class="response-form__error">{{ $message }}</span>
            @enderror

            <div class="response-form__helper">
                <i class="fa-regular fa-lightbulb"></i>
                <span>Une réponse professionnelle et courtoise montre votre engagement envers vos clients.</span>
            </div>

            <div class="response-form__actions">
                <button type="submit" class="btn btn-rust">
                    <i class="fa-solid fa-paper-plane"></i>
                    {{ $hasReponse ? 'Mettre à jour' : 'Envoyer la réponse' }}
                </button>
            </div>
        </form>
    </section>

    {{-- ═══════════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════════ --}}
    <footer class="avis-actions">
        <a href="{{ route('agence.evaluations.index') }}" class="btn btn-ghost">
            <i class="fa-solid fa-list"></i> Tous les avis
        </a>
        <a href="{{ route('agence.dashboard') }}" class="btn btn-ghost avis-actions__right">
            <i class="fa-solid fa-chart-pie"></i> Tableau de bord
        </a>
    </footer>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL AVIS
   ═══════════════════════════════════════════════════════════ */

.avis-show {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #F5A623;
    --surface:      #F7F9FC;
    --radius:       14px;
}

.avis-show__back { margin-bottom: 16px; }

/* ═══════════════════════════════════════════
   HERO CLIENT
   ═══════════════════════════════════════════ */
.avis-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 20px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
}
.avis-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0;
    width: 5px;
    height: 100%;
}
.avis-hero--positif::before { background: var(--c-success); }
.avis-hero--neutre::before  { background: var(--c-warning); }
.avis-hero--negatif::before { background: var(--c-danger); }

.avis-hero__client {
    display: flex;
    align-items: center;
    gap: 14px;
    min-width: 0;
    flex: 1;
}

.avis-hero__avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 24px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(0, 0, 0, .12);
}
.avis-hero__avatar--positif { background: linear-gradient(135deg, var(--c-success), #2E9B5F); }
.avis-hero__avatar--neutre  { background: linear-gradient(135deg, var(--c-warning), #F57C00); }
.avis-hero__avatar--negatif { background: linear-gradient(135deg, var(--c-danger), #E53935); }

.avis-hero__identity { min-width: 0; }
.avis-hero__name {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--text);
    line-height: 1.2;
}
.avis-hero__date {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    color: var(--muted);
}
.avis-hero__date i { font-size: 11px; opacity: .8; }

.avis-hero__rating {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 5px;
    flex-shrink: 0;
}
.avis-hero__stars {
    display: inline-flex;
    gap: 3px;
}
.avis-hero__stars .fa-star {
    font-size: 20px;
    color: #D4D8E0;
    transition: color .2s;
}
.avis-hero__stars .fa-star.is-on {
    color: var(--c-gold);
    filter: drop-shadow(0 2px 4px rgba(245, 166, 35, .35));
}

.avis-hero__score {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 800;
    line-height: 1;
}
.avis-hero__score small {
    font-size: 13px;
    font-weight: 600;
    opacity: .6;
    margin-left: 2px;
}
.avis-hero__rating--positif .avis-hero__score { color: var(--c-success); }
.avis-hero__rating--neutre  .avis-hero__score { color: var(--c-warning); }
.avis-hero__rating--negatif .avis-hero__score { color: var(--c-danger); }

/* ═══════════════════════════════════════════
   PANNEAUX
   ═══════════════════════════════════════════ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 22px;
    margin-bottom: 14px;
}

.panel__head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}
.panel__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: var(--c-warning-bg);
    color: var(--rust);
    font-size: 14px;
    flex-shrink: 0;
}
.panel__head h4 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    flex: 1;
}

/* ═══ Commentaire ═══ */
.comment-block {
    position: relative;
    padding: 16px 20px 16px 24px;
    border-radius: 12px;
    background: var(--surface);
    font-size: 14.5px;
    line-height: 1.75;
    color: var(--text);
    font-style: italic;
    margin: 0;
    border-left: 4px solid var(--muted);
}
.comment-block--positif {
    background: linear-gradient(135deg, rgba(30, 122, 71, .06), rgba(30, 122, 71, .02));
    border-left-color: var(--c-success);
}
.comment-block--neutre {
    background: linear-gradient(135deg, rgba(230, 81, 0, .06), rgba(230, 81, 0, .02));
    border-left-color: var(--c-warning);
}
.comment-block--negatif {
    background: linear-gradient(135deg, rgba(198, 40, 40, .06), rgba(198, 40, 40, .02));
    border-left-color: var(--c-danger);
}

.comment-empty {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 16px 20px;
    background: var(--surface);
    border-radius: 12px;
    border: 1px dashed var(--border);
    color: var(--muted);
    font-size: 13.5px;
    font-style: italic;
}
.comment-empty i {
    font-size: 18px;
    opacity: .5;
}

/* ═══ Bien ═══ */
.bien-info {
    padding: 14px 18px;
    background: var(--surface);
    border-radius: 12px;
    border: 1px solid var(--border);
}
.bien-info__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
}
.bien-info__address {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--muted);
    margin-bottom: 10px;
}
.bien-info__address i { color: var(--rust); font-size: 12px; }

.bien-info__details {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 16px;
}
.bien-detail {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 11px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-soft);
}
.bien-detail i { color: var(--rust); font-size: 11px; }

/* ═══ Réponse ═══ */
.response-existing {
    padding: 14px 16px;
    background: var(--c-info-bg);
    border-radius: 10px;
    border-left: 3px solid var(--c-info);
    margin-bottom: 16px;
}
.response-existing__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-bottom: 6px;
    flex-wrap: wrap;
}
.response-existing__status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: var(--c-info);
}
.response-existing__status i { font-size: 12px; }

.response-existing__date {
    font-size: 11.5px;
    color: var(--muted);
    font-weight: 500;
}

.response-existing__text {
    font-size: 14px;
    line-height: 1.65;
    color: var(--text-soft);
    margin: 0;
}

/* ═══ Formulaire réponse ═══ */
.response-form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.response-form__label {
    font-size: 13px;
    font-weight: 700;
    color: var(--text-soft);
}
.is-required {
    color: var(--rust);
    margin-left: 2px;
}
.response-form__textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 13.5px;
    font-family: inherit;
    line-height: 1.6;
    resize: vertical;
    min-height: 100px;
    transition: border .2s, box-shadow .2s;
    color: var(--text);
    background: #fff;
}
.response-form__textarea:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 3px rgba(180, 83, 42, .1);
}
.response-form__error {
    display: block;
    color: var(--c-danger);
    font-size: 12px;
    font-weight: 500;
}
.response-form__helper {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 12px;
    background: var(--c-warning-bg);
    border-radius: 8px;
    font-size: 12.5px;
    color: #8B4B15;
    line-height: 1.5;
}
.response-form__helper i {
    color: var(--c-gold);
    font-size: 13px;
    flex-shrink: 0;
    margin-top: 1px;
}
.response-form__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 4px;
}

/* ═══ Footer ═══ */
.avis-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    flex-wrap: wrap;
}
.avis-actions__right { margin-left: auto; }

/* ═══ Boutons ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523;
    color: #fff;
    border-color: #9A4523;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(180, 83, 42, .25);
}
.btn-ghost {
    background: transparent;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: var(--surface);
    border-color: var(--rust);
    color: var(--rust);
}
.btn-sm { padding: 6px 13px; font-size: 12px; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 768px) {
    .avis-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 18px;
        gap: 14px;
    }
    .avis-hero__rating {
        align-items: flex-start;
        width: 100%;
    }
    .avis-hero__stars .fa-star { font-size: 17px; }
    .avis-hero__score { font-size: 19px; }

    .panel { padding: 16px; }

    .comment-block { font-size: 13.5px; padding: 14px 16px 14px 20px; }

    .response-form__actions { flex-direction: column; }
    .response-form__actions .btn { width: 100%; justify-content: center; }

    .avis-actions { flex-direction: column; align-items: stretch; }
    .avis-actions .btn { width: 100%; justify-content: center; }
    .avis-actions__right { margin-left: 0; }
}

@media (max-width: 480px) {
    .avis-hero { padding: 16px; }
    .avis-hero__avatar { width: 50px; height: 50px; font-size: 20px; }
    .avis-hero__name { font-size: 17px; }
    .avis-hero__stars .fa-star { font-size: 15px; }

    .panel { padding: 14px; }
    .panel__head h4 { font-size: 14px; }

    .comment-block { font-size: 13px; }
    .bien-info { padding: 12px 14px; }
    .bien-info__title { font-size: 14px; }
    .bien-detail { font-size: 11.5px; padding: 3px 9px; }

    .btn { font-size: 12px; padding: 7px 13px; }
}
</style>
@endpush