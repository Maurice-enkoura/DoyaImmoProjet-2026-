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
            $note === 5 => 'Très satisfait 🤩',
            $note === 4 => 'Satisfait 😊',
            $note === 3 => 'Neutre 😐',
            $note === 2 => 'Insatisfait 😕',
            default     => 'Très insatisfait 😞',
        };

        $agence      = $evaluation->agence;
        $initial     = strtoupper(mb_substr($agence->nom_agence ?? 'A', 0, 1));
        $hasReponse  = !empty($evaluation->reponse_agence);
        $proposition = $evaluation->proposition;
        $bien        = $proposition->bien ?? null;

        $noteMoyenne = $agence->evaluations->avg('note') ?? 0;
        $nbAvis      = $agence->evaluations->count();
    @endphp

    {{-- ═══ HERO — NOTE EN GROS ═══ --}}
    <header class="note-hero note-hero--{{ $sentiment }}">

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
            <i class="fa-regular fa-calendar"></i>
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
                    <span class="rating-stars">
                        <i class="fa-solid fa-star"></i>
                        <strong>{{ number_format($noteMoyenne, 1) }}</strong>
                        <span class="rating-max">/ 5</span>
                    </span>
                    <span class="rating-reviews">
                        ({{ $nbAvis }} avis)
                    </span>
                </div>

                @if($agence->quartier || $agence->adresse)
                    <div class="agency-block__meta">
                        @if($agence->quartier)
                            <span><i class="fa-solid fa-map-pin"></i> {{ $agence->quartier }}</span>
                        @endif
                        @if($agence->adresse)
                            <span><i class="fa-solid fa-location-dot"></i> {{ $agence->adresse }}</span>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <a href="{{ route('agences.public.show', $agence->slug) }}"
           class="btn btn-ghost btn-block">
            <i class="fa-solid fa-eye"></i> Voir le profil de l'agence
        </a>
    </div>

    {{-- ═══ MON AVIS ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-message"></i> Mon avis
    </div>

    @if($evaluation->commentaire)
        <blockquote class="comment-block comment-block--{{ $sentiment }}">
            {{ $evaluation->commentaire }}
        </blockquote>
    @else
        <div class="comment-empty">
            <i class="fa-regular fa-comment"></i>
            <span>Vous n'avez pas laissé de commentaire.</span>
        </div>
    @endif

    {{-- ═══ PROPOSITION ASSOCIÉE ═══ --}}
    @if($proposition)
        <div class="ios-section-title">
            <i class="fa-regular fa-file-lines"></i> Proposition associée
        </div>

        <div class="ios-list">
            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Date</span>
                </div>
                <span class="ios-list__value">
                    {{ $proposition->created_at->format('d/m/Y') }}
                </span>
            </div>

            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Prix proposé</span>
                </div>
                <span class="ios-list__value ios-list__value--rust">
                    {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                </span>
            </div>

            @if($bien)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Bien</span>
                    </div>
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
            <span class="ios-section-count ios-section-count--info">
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
            <div class="reponse-empty__icon">
                <i class="fa-regular fa-hourglass-half"></i>
            </div>
            <div class="reponse-empty__body">
                <strong>Aucune réponse pour le moment</strong>
                <p>L'agence n'a pas encore répondu à votre avis.</p>
            </div>
        </div>
    @endif

    {{-- ═══ ACTIONS ═══ --}}
    <footer class="avis-actions">
        <a href="{{ route('particulier.evaluations.index') }}" class="btn btn-ghost btn-block">
            <i class="fa-solid fa-list"></i> Tous mes avis
        </a>
    </footer>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DÉTAIL AVIS — iOS NATIVE
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

    --ios-bg: #F2F2F7;
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
.ios-section-title i { color: var(--rust); font-size: 15px; }
.ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: var(--c-warning-bg);
    color: var(--rust);
    border-radius: 999px;
}
.ios-section-count--info {
    background: var(--c-info-bg);
    color: var(--c-info);
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
    position: relative;
    overflow: hidden;
    text-align: center;
    border: 1px solid;
}
.note-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    opacity: .5;
    pointer-events: none;
}

.note-hero--positif {
    background: linear-gradient(135deg, #F0FBF4 0%, #fff 70%);
    border-color: #C8E6C9;
}
.note-hero--positif::before {
    background: radial-gradient(circle at top, rgba(30, 122, 71, .06), transparent 60%);
}
.note-hero--neutre {
    background: linear-gradient(135deg, #FFF9F0 0%, #fff 70%);
    border-color: #FFE0B2;
}
.note-hero--neutre::before {
    background: radial-gradient(circle at top, rgba(230, 81, 0, .06), transparent 60%);
}
.note-hero--negatif {
    background: linear-gradient(135deg, #FFF5F5 0%, #fff 70%);
    border-color: #FFCDD2;
}
.note-hero--negatif::before {
    background: radial-gradient(circle at top, rgba(198, 40, 40, .06), transparent 60%);
}

.note-hero__stars {
    display: inline-flex;
    gap: 6px;
    font-size: 32px;
    position: relative;
}
.note-hero__stars .fa-star {
    color: #D4D8E0;
    transition: transform .3s ease;
}
.note-hero__stars .fa-star.is-on {
    color: var(--c-gold);
    filter: drop-shadow(0 4px 8px rgba(245, 166, 35, .35));
}

.note-hero__value {
    font-family: var(--display);
    font-size: 52px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -.03em;
    position: relative;
}
.note-hero--positif .note-hero__value { color: var(--c-success); }
.note-hero--neutre  .note-hero__value { color: var(--c-warning); }
.note-hero--negatif .note-hero__value { color: var(--c-danger); }
.note-hero__value small {
    font-size: 20px;
    font-weight: 600;
    color: var(--muted);
    margin-left: 4px;
}

.note-hero__label {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    position: relative;
}
.note-hero--positif .note-hero__label { color: var(--c-success); }
.note-hero--neutre  .note-hero__label { color: var(--c-warning); }
.note-hero--negatif .note-hero__label { color: var(--c-danger); }

.note-hero__date {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--muted);
    margin-top: 6px;
    padding: 4px 12px;
    background: rgba(255, 255, 255, .7);
    border-radius: 999px;
    border: 1px solid var(--border);
    position: relative;
}
.note-hero__date i { font-size: 11px; opacity: .8; }

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
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(180, 83, 42, .2);
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
    gap: 8px;
    font-size: 13px;
    margin-bottom: 6px;
    flex-wrap: wrap;
}
.rating-stars { display: inline-flex; align-items: center; gap: 4px; }
.rating-stars i { color: var(--c-gold); font-size: 13px; }
.rating-stars strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 800;
    color: var(--text);
}
.rating-max { color: var(--muted); font-size: 12px; font-weight: 500; }
.rating-reviews { color: var(--muted); font-size: 12px; }

.agency-block__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 14px;
    font-size: 12.5px;
    color: var(--muted);
}
.agency-block__meta i {
    color: var(--rust);
    font-size: 11px;
    margin-right: 5px;
}

/* ═══ COMMENTAIRE ═══ */
.comment-block {
    position: relative;
    padding: 16px 20px 16px 26px;
    border-radius: 12px;
    font-size: 14.5px;
    line-height: 1.75;
    color: var(--text);
    font-style: italic;
    margin: 0 0 14px;
    border-left: 4px solid var(--muted);
    background: var(--surface);
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
    background: #fff;
    border-radius: 12px;
    border: 1px dashed var(--border);
    color: var(--muted);
    font-size: 13.5px;
    font-style: italic;
    margin-bottom: 14px;
}
.comment-empty i { font-size: 18px; opacity: .5; }

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
.ios-list__item--static { cursor: default; }
.ios-list__content { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.ios-list__title {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    text-align: right;
    flex-shrink: 0;
}
.ios-list__value--rust { color: var(--rust); }

/* ═══ RÉPONSE AGENCE ═══ */
.reponse-card {
    background: linear-gradient(135deg, #F0F7FF 0%, #fff 60%);
    border: 1px solid #BBDEFB;
    border-radius: 14px;
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
    background: linear-gradient(135deg, var(--c-info), #1565C0);
    color: #fff;
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
    font-weight: 700;
    color: var(--c-info);
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
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    background: #fff;
    border-radius: 14px;
    border: 1px dashed var(--border);
    margin-bottom: 14px;
}
.reponse-empty__icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: var(--surface);
    color: var(--muted);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    opacity: .6;
}
.reponse-empty__body strong {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
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
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; color: #fff; border-color: #9A4523; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(180, 83, 42, .25); }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: var(--surface); border-color: var(--rust); color: var(--rust); }
.btn-block { width: 100%; justify-content: center; }
.btn-sm { padding: 6px 13px; font-size: 12px; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .avis-show { background: transparent; padding-top: 4px; }

    /* ─── HERO NOTE iOS ─── */
    .note-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 32px 20px;
        margin-bottom: 16px;
        gap: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        position: relative;
        overflow: hidden;
    }
    /* Bande colorée en haut */
    .note-hero::after {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        border-radius: var(--ios-radius-lg) var(--ios-radius-lg) 0 0;
    }
    .note-hero--positif::after { background: linear-gradient(90deg, var(--c-success), #4CAF50); }
    .note-hero--neutre::after  { background: linear-gradient(90deg, var(--c-warning), #FF9800); }
    .note-hero--negatif::after { background: linear-gradient(90deg, var(--c-danger), #E53935); }

    .note-hero::before { display: none; }
    .note-hero--positif,
    .note-hero--neutre,
    .note-hero--negatif { background: var(--ios-card); border: none; }

    .note-hero__stars {
        font-size: 34px;
        gap: 8px;
        margin-bottom: 8px;
    }
    .note-hero__stars .fa-star.is-on {
        filter: drop-shadow(0 6px 12px rgba(245, 166, 35, .4));
    }

    .note-hero__value {
        font-size: 56px;
        letter-spacing: -.04em;
        margin-bottom: 2px;
    }
    .note-hero__value small {
        font-size: 22px;
        margin-left: 5px;
    }

    .note-hero__label {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -.2px;
    }

    .note-hero__date {
        font-size: 13px;
        padding: 5px 14px;
        margin-top: 10px;
        background: #EFEFF4;
        border: none;
        color: var(--ios-label-3);
    }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-count {
        font-size: 11px;
        padding: 3px 10px;
    }

    /* ─── PANELS iOS ─── */
    .panel {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    /* ─── AGENCE iOS ─── */
    .agency-block {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 14px;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .agency-block__avatar { width: 52px; height: 52px; font-size: 20px; }
    .agency-block__info { width: 100%; }
    .agency-block__name { font-size: 18px; font-weight: 700; letter-spacing: -.2px; }
    .agency-block__rating { font-size: 13px; }
    .rating-stars strong { font-size: 14px; }
    .agency-block__meta { font-size: 13px; gap: 6px 12px; }

    /* ─── COMMENTAIRE iOS ─── */
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
    .comment-block--positif { border-left-color: var(--c-success); background: var(--ios-card); }
    .comment-block--neutre  { border-left-color: var(--c-warning); background: var(--ios-card); }
    .comment-block--negatif { border-left-color: var(--c-danger);  background: var(--ios-card); }

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

    /* ─── LISTE iOS ─── */
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
        letter-spacing: -.2px;
    }
    .ios-list__value {
        font-size: 15px;
        font-weight: 600;
        color: var(--ios-label-3);
        text-align: right;
    }
    .ios-list__value--rust { color: var(--rust); }

    /* ─── RÉPONSE iOS ─── */
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
        background: linear-gradient(135deg, var(--c-info), #1565C0);
        box-shadow: 0 3px 8px rgba(13, 71, 161, .2);
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
        gap: 14px;
    }
    .reponse-empty__icon {
        width: 46px;
        height: 46px;
        background: #EFEFF4;
        font-size: 20px;
        opacity: 1;
        color: var(--ios-label-3);
    }
    .reponse-empty__body strong {
        font-size: 14px;
        color: var(--ios-label);
    }
    .reponse-empty__body p {
        font-size: 13px;
        color: var(--ios-label-3);
    }

    /* ─── ACTIONS iOS ─── */
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
    .btn-rust {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    }
    .btn-ghost {
        background: #EFEFF4;
        border: none;
        color: var(--ios-blue);
    }
    .btn-ghost:active { background: #E0E0E5; }
}

@media (max-width: 480px) {
    .note-hero { padding: 28px 16px; }
    .note-hero__stars { font-size: 30px; gap: 6px; }
    .note-hero__value { font-size: 48px; }
    .note-hero__value small { font-size: 20px; }
    .note-hero__label { font-size: 16px; }

    .agency-block__avatar { width: 48px; height: 48px; font-size: 18px; }
    .agency-block__name { font-size: 17px; }

    .comment-block { padding: 14px 16px; font-size: 14px; }

    .reponse-card__text { padding-left: 0; margin-top: 4px; }

    .ios-section-title { font-size: 16px; }
}
</style>
@endpush