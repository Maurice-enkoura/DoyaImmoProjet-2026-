@extends('layouts.dashboard')

@section('title', 'Détail de l\'avis — DoyaImmo')
@section('page_title', 'Détail de l\'avis')
@section('page_sub', 'Votre évaluation de l\'agence')

@section('content')
<div class="view active avis-show">

    @php
        $note        = (int) $evaluation->note;
        $sentiment   = $note >= 4 ? 'positif' : ($note === 3 ? 'neutre' : 'negatif');
        $sentimentLabel = match (true) {
            $note === 5 => 'Très satisfait',
            $note === 4 => 'Satisfait',
            $note === 3 => 'Neutre',
            $note === 2 => 'Insatisfait',
            default     => 'Très insatisfait',
        };

        $agence      = $evaluation->agence;
        $initial     = strtoupper(mb_substr($agence->nom_agence ?? 'A', 0, 1));
        $hasReponse  = !empty($evaluation->reponse_agence);
        $proposition = $evaluation->proposition;
        $bien        = $proposition->bien ?? null;

        $noteMoyenne = $agence->evaluations->avg('note') ?? 0;
        $nbAvis      = $agence->evaluations->count();
    @endphp

    {{-- ═══ HERO — NOTE ═══ --}}
    <header class="note-hero">
        <div class="note-hero__stars">
            @for($i = 1; $i <= 5; $i++)
                <i class="fa-solid fa-star {{ $i <= $note ? 'is-on' : '' }}"></i>
            @endfor
        </div>

        <div class="note-hero__value">
            {{ $note }}<small>/5</small>
        </div>

        <div class="note-hero__label">
            {{ $sentimentLabel }}
        </div>

        <div class="note-hero__date">
            Publié le {{ $evaluation->created_at->format('d F Y') }}
        </div>
    </header>

    {{-- ═══ AGENCE ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-building"></i> Agence évaluée
    </div>

    <div class="panel panel--agency">
        <div class="agency-block">
            <div class="agency-block__avatar">{{ $initial }}</div>

            <div class="agency-block__info">
                <h3 class="agency-block__name">{{ $agence->nom_agence }}</h3>

                <div class="agency-block__rating">
                    <i class="fa-solid fa-star"></i>
                    <strong>{{ number_format($noteMoyenne, 1) }}</strong>
                    <span class="rating-max">/ 5</span>
                    <span class="rating-reviews">({{ $nbAvis }} avis)</span>
                </div>

                @if($agence->quartier || $agence->adresse)
                    <div class="agency-block__meta">
                        @if($agence->quartier)
                            <span>{{ $agence->quartier }}</span>
                        @endif
                        @if($agence->adresse)
                            <span>{{ $agence->adresse }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <a href="{{ route('agences.public.show', $agence->slug) }}"
           class="btn btn-ghost btn-block">
            Voir le profil de l'agence
        </a>
    </div>

    {{-- ═══ MON AVIS ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-message"></i> Mon avis
    </div>

    @if($evaluation->commentaire)
        <blockquote class="comment-block">
            {{ $evaluation->commentaire }}
        </blockquote>
    @else
        <div class="comment-empty">
            <span>Vous n'avez pas laissé de commentaire.</span>
        </div>
    @endif

    {{-- ═══ PROPOSITION ASSOCIÉE ═══ --}}
    @if($proposition)
        <div class="ios-section-title">
            <i class="fa-regular fa-file-lines"></i> Proposition associée
        </div>

        <div class="ios-list">
            <div class="ios-list__item">
                <span class="ios-list__title">Date</span>
                <span class="ios-list__value">
                    {{ $proposition->created_at->format('d/m/Y') }}
                </span>
            </div>

            <div class="ios-list__item">
                <span class="ios-list__title">Prix proposé</span>
                <span class="ios-list__value ios-list__value--rust">
                    {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                </span>
            </div>

            @if($bien)
                <div class="ios-list__item">
                    <span class="ios-list__title">Bien</span>
                    <span class="ios-list__value">
                        {{ $bien->titre ?? 'Non spécifié' }}
                    </span>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══ RÉPONSE DE L'AGENCE ═══ --}}
    <div class="ios-section-title">
        <i class="fa-solid fa-reply"></i> Réponse de l'agence
        @if($hasReponse && $evaluation->date_reponse)
            <span class="ios-section-count">
                {{ $evaluation->date_reponse->format('d/m/Y') }}
            </span>
        @endif
    </div>

    @if($hasReponse)
        <div class="reponse-card">
            <div class="reponse-card__head">
                <div class="reponse-card__avatar">{{ $initial }}</div>
                <strong>{{ $agence->nom_agence }}</strong>
            </div>
            <p class="reponse-card__text">{{ $evaluation->reponse_agence }}</p>
        </div>
    @else
        <div class="reponse-empty">
            <div class="reponse-empty__body">
                <strong>Aucune réponse pour le moment</strong>
                <p>L'agence n'a pas encore répondu à votre avis.</p>
            </div>
        </div>
    @endif

    {{-- ═══ ACTIONS ═══ --}}
    <footer class="avis-actions">
        <a href="{{ route('particulier.evaluations.index') }}" class="btn btn-ghost btn-block">
            Tous mes avis
        </a>
    </footer>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DÉTAIL AVIS — Design épuré
   ═══════════════════════════════════════════════════════════ */

.avis-show {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #F1F8F4;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --c-info:       #1E40AF;
    --c-info-bg:    #EFF6FF;
    --c-gold:       #F5A623;
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 22px 0 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ios-section-title:first-of-type { margin-top: 0; }
.ios-section-title i { color: var(--muted); font-size: 15px; }
.ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    background: #F5F5F7;
    color: var(--muted);
    border-radius: 999px;
}

/* ═══ HERO NOTE ═══ */
.note-hero {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 32px 24px;
    border-radius: var(--radius);
    margin-bottom: 14px;
    background: #fff;
    border: 1px solid var(--border);
    text-align: center;
}

.note-hero__stars {
    display: inline-flex;
    gap: 6px;
    font-size: 30px;
}
.note-hero__stars .fa-star {
    color: #D4D8E0;
}
.note-hero__stars .fa-star.is-on {
    color: var(--c-gold);
}

.note-hero__value {
    font-family: var(--display);
    font-size: 48px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -.03em;
    color: var(--text);
}
.note-hero__value small {
    font-size: 20px;
    font-weight: 600;
    color: var(--muted);
    margin-left: 4px;
}

.note-hero__label {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--text);
}

.note-hero__date {
    font-size: 12.5px;
    color: var(--muted);
    margin-top: 6px;
}

/* ═══ PANELS ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 22px;
    margin-bottom: 14px;
}

/* ═══ AGENCE ═══ */
.agency-block {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 14px;
}
.agency-block__avatar {
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
.agency-block__info { flex: 1; min-width: 180px; }
.agency-block__name {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--text);
}
.agency-block__rating {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    margin-bottom: 6px;
    flex-wrap: wrap;
}
.agency-block__rating i { color: var(--c-gold); font-size: 13px; }
.agency-block__rating strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
}
.rating-max { color: var(--muted); font-size: 12px; }
.rating-reviews { color: var(--muted); font-size: 12px; }

.agency-block__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 14px;
    font-size: 12.5px;
    color: var(--muted);
}

/* ═══ COMMENTAIRE ═══ */
.comment-block {
    padding: 16px 20px;
    border-radius: 12px;
    font-size: 14.5px;
    line-height: 1.75;
    color: var(--text);
    font-style: italic;
    margin: 0 0 14px;
    border-left: 3px solid var(--rust);
    background: #F9FAFB;
}

.comment-empty {
    padding: 16px 20px;
    background: #fff;
    border-radius: 12px;
    border: 1px dashed var(--border);
    color: var(--muted);
    font-size: 13.5px;
    font-style: italic;
    margin-bottom: 14px;
}

/* ═══ LISTE iOS ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 14px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
}
.ios-list__item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    font-size: 13.5px;
}
.ios-list__title {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
    text-align: right;
    flex-shrink: 0;
}
.ios-list__value--rust { color: var(--rust); }

/* ═══ RÉPONSE AGENCE ═══ */
.reponse-card {
    background: #F9FAFB;
    border: 1px solid var(--border);
    border-left: 3px solid var(--c-info);
    border-radius: 12px;
    padding: 16px 18px;
    margin-bottom: 14px;
}
.reponse-card__head {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 10px;
}
.reponse-card__avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
}
.reponse-card__head strong {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text);
}
.reponse-card__text {
    font-size: 14px;
    line-height: 1.7;
    color: var(--text-soft);
    margin: 0;
    padding-left: 41px;
}

/* Réponse vide */
.reponse-empty {
    padding: 16px 18px;
    background: #fff;
    border-radius: 12px;
    border: 1px dashed var(--border);
    margin-bottom: 14px;
}
.reponse-empty__body strong {
    display: block;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-soft);
    margin-bottom: 2px;
}
.reponse-empty__body p {
    font-size: 12.5px;
    color: var(--muted);
    margin: 0;
}

/* ═══ ACTIONS ═══ */
.avis-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    flex-wrap: wrap;
    margin-top: 8px;
}
.avis-actions .btn-block { width: 100%; justify-content: center; }

/* ═══ BOUTONS ═══ */
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
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; border-color: #9A4523; color: #fff; }

.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: #F5F5F7; color: var(--text); }

.btn-block { width: 100%; justify-content: center; }
.btn-sm { padding: 6px 13px; font-size: 12px; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .avis-show { background: transparent; padding-top: 4px; }

    /* HERO NOTE iOS */
    .note-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 32px 20px;
        margin-bottom: 16px;
        gap: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .note-hero__stars {
        font-size: 32px;
        gap: 6px;
        margin-bottom: 6px;
    }

    .note-hero__value {
        font-size: 52px;
        letter-spacing: -.04em;
        margin-bottom: 2px;
    }
    .note-hero__value small {
        font-size: 20px;
        margin-left: 4px;
    }

    .note-hero__label {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
    }

    .note-hero__date {
        font-size: 12.5px;
        padding: 5px 14px;
        margin-top: 8px;
        background: #EFEFF4;
        border-radius: 999px;
        color: var(--ios-label-3);
    }

    /* SECTION TITLES iOS */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; color: var(--muted); }
    .ios-section-count {
        font-size: 11px;
        padding: 3px 10px;
    }

    /* PANELS iOS */
    .panel {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    /* AGENCE iOS */
    .agency-block {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 14px;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .agency-block__avatar {
        width: 52px;
        height: 52px;
        font-size: 20px;
        background: #F5F5F7;
    }
    .agency-block__info { width: 100%; }
    .agency-block__name { font-size: 18px; font-weight: 700; letter-spacing: -.2px; }
    .agency-block__rating { font-size: 13px; }
    .agency-block__meta { font-size: 13px; gap: 6px 12px; }

    /* COMMENTAIRE iOS */
    .comment-block {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid var(--rust);
        border-radius: 12px;
        padding: 16px 18px;
        font-size: 14.5px;
        line-height: 1.65;
        color: var(--ios-label-2);
        margin: 0 0 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .comment-empty {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 20px;
        font-size: 13.5px;
        color: var(--ios-label-3);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
    }

    /* LISTE iOS */
    .ios-list {
        display: block;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
    }
    .ios-list__item {
        padding: 14px 16px;
        gap: 12px;
        border-bottom: 0.5px solid var(--ios-separator);
        font-size: 15px;
    }
    .ios-list__item:last-child { border-bottom: none; }
    .ios-list__title {
        font-size: 15px;
        color: var(--ios-label);
        font-weight: 500;
    }
    .ios-list__value {
        font-size: 15px;
        font-weight: 500;
        color: var(--ios-label-3);
        text-align: right;
    }
    .ios-list__value--rust { color: var(--rust); }

    /* RÉPONSE iOS */
    .reponse-card {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid var(--c-info);
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .reponse-card__head {
        gap: 10px;
        margin-bottom: 10px;
    }
    .reponse-card__avatar {
        width: 34px;
        height: 34px;
        font-size: 14px;
        background: #F5F5F7;
    }
    .reponse-card__head strong { font-size: 14px; }
    .reponse-card__text {
        font-size: 14px;
        padding-left: 44px;
        color: var(--ios-label-2);
        line-height: 1.65;
    }

    .reponse-empty {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .reponse-empty__body strong {
        font-size: 14px;
        color: var(--ios-label);
    }
    .reponse-empty__body p {
        font-size: 13px;
        color: var(--ios-label-3);
    }

    /* ACTIONS iOS */
    .avis-actions {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 8px;
    }
    .btn {
        padding: 12px 18px;
        font-size: 14px;
        border-radius: 12px;
        font-weight: 600;
    }
    .btn-ghost {
        background: #F5F5F7;
        border: none;
        color: var(--ios-blue);
    }
    .btn-ghost:active { background: #EFEFF4; }
}

@media (max-width: 480px) {
    .note-hero { padding: 28px 16px; }
    .note-hero__stars { font-size: 28px; gap: 5px; }
    .note-hero__value { font-size: 44px; }
    .note-hero__value small { font-size: 18px; }
    .note-hero__label { font-size: 16px; }

    .agency-block__avatar { width: 48px; height: 48px; font-size: 18px; }
    .agency-block__name { font-size: 17px; }

    .comment-block { padding: 14px 16px; font-size: 14px; }

    .reponse-card__text { padding-left: 0; margin-top: 4px; }

    .ios-section-title { font-size: 16px; }
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