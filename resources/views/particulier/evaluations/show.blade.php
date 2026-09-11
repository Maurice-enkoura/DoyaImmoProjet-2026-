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

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="avis-show__back">
        <a href="{{ route('particulier.evaluations.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes avis
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         HERO — NOTE EN GROS
    ═══════════════════════════════════════════ --}}
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

    {{-- ═══════════════════════════════════════════
         AGENCE
    ═══════════════════════════════════════════ --}}
    <section class="panel">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-regular fa-building"></i>
            </span>
            <h4>Agence évaluée</h4>
        </header>

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

                <div class="agency-block__meta">
                    @if($agence->quartier)
                        <span><i class="fa-solid fa-map-pin"></i> {{ $agence->quartier }}</span>
                    @endif
                    @if($agence->adresse)
                        <span><i class="fa-solid fa-location-dot"></i> {{ $agence->adresse }}</span>
                    @endif
                </div>
            </div>

            <a href="{{ route('agences.public.show', $agence->slug) }}"
               class="btn btn-ghost btn-sm agency-block__cta">
                <i class="fa-solid fa-eye"></i> Voir le profil
            </a>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         MON AVIS
    ═══════════════════════════════════════════ --}}
    <section class="panel">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-regular fa-message"></i>
            </span>
            <h4>Mon avis</h4>
        </header>

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
    </section>

    {{-- ═══════════════════════════════════════════
         PROPOSITION ASSOCIÉE
    ═══════════════════════════════════════════ --}}
    @if($proposition)
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-file-lines"></i>
                </span>
                <h4>Proposition associée</h4>
            </header>

            <div class="proposition-grid">
                <div class="proposition-grid__item">
                    <span class="proposition-grid__label">Date</span>
                    <span class="proposition-grid__value">
                        {{ $proposition->created_at->format('d/m/Y') }}
                    </span>
                </div>
                <div class="proposition-grid__item">
                    <span class="proposition-grid__label">Prix proposé</span>
                    <span class="proposition-grid__value proposition-grid__value--prix">
                        {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                        <small>FCFA</small>
                    </span>
                </div>
                @if($bien)
                    <div class="proposition-grid__item proposition-grid__item--wide">
                        <span class="proposition-grid__label">Bien</span>
                        <span class="proposition-grid__value">
                            {{ $bien->titre ?? 'Non spécifié' }}
                            @if($bien->adresse)
                                <span class="proposition-grid__sub">— {{ $bien->adresse }}</span>
                            @endif
                        </span>
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         RÉPONSE DE L'AGENCE
    ═══════════════════════════════════════════ --}}
    @if($hasReponse)
        <section class="panel panel--reponse">
            <header class="panel__head">
                <span class="panel__icon panel__icon--info">
                    <i class="fa-solid fa-reply"></i>
                </span>
                <h4>Réponse de l'agence</h4>
                @if($evaluation->date_reponse)
                    <span class="panel__date">
                        {{ $evaluation->date_reponse->format('d F Y') }}
                    </span>
                @endif
            </header>

            <div class="reponse-block">
                <div class="reponse-block__head">
                    <div class="reponse-block__avatar">{{ $initial }}</div>
                    <strong>{{ $agence->nom_agence }}</strong>
                </div>
                <p class="reponse-block__text">{{ $evaluation->reponse_agence }}</p>
            </div>
        </section>
    @else
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-regular fa-clock"></i>
                </span>
                <h4>Réponse de l'agence</h4>
            </header>

            <div class="reponse-empty">
                <i class="fa-regular fa-hourglass-half"></i>
                <div>
                    <strong>Aucune réponse pour le moment</strong>
                    <p>L'agence n'a pas encore répondu à votre avis.</p>
                </div>
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════════ --}}
    <footer class="avis-actions">
        <a href="{{ route('particulier.evaluations.index') }}" class="btn btn-ghost">
            <i class="fa-solid fa-list"></i> Tous mes avis
        </a>

        <a href="{{ route('agences.public.show', $agence->slug) }}"
           class="btn btn-rust avis-actions__right">
            <i class="fa-solid fa-building"></i> Voir le profil de l'agence
        </a>
    </footer>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL AVIS — Particulier
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
   HERO NOTE
   ═══════════════════════════════════════════ */
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

/* ═══ PANNEAUX ═══ */
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
    flex-wrap: wrap;
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
.panel__icon--info {
    background: var(--c-info-bg);
    color: var(--c-info);
}
.panel__head h4 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    flex: 1;
}
.panel__date {
    font-size: 11.5px;
    color: var(--muted);
    font-weight: 500;
}

/* ═══════════════════════════════════════════
   BLOC AGENCE
   ═══════════════════════════════════════════ */
.agency-block {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
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

.agency-block__info {
    flex: 1;
    min-width: 180px;
}
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
.rating-stars {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.rating-stars i { color: var(--c-gold); font-size: 13px; }
.rating-stars strong {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 800;
    color: var(--text);
}
.rating-max {
    color: var(--muted);
    font-size: 12px;
    font-weight: 500;
}
.rating-reviews {
    color: var(--muted);
    font-size: 12px;
}

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
.agency-block__cta { flex-shrink: 0; }

/* ═══════════════════════════════════════════
   COMMENTAIRE
   ═══════════════════════════════════════════ */
.comment-block {
    position: relative;
    padding: 16px 20px 16px 26px;
    border-radius: 12px;
    font-size: 14.5px;
    line-height: 1.75;
    color: var(--text);
    font-style: italic;
    margin: 0;
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
    background: var(--surface);
    border-radius: 12px;
    border: 1px dashed var(--border);
    color: var(--muted);
    font-size: 13.5px;
    font-style: italic;
}
.comment-empty i { font-size: 18px; opacity: .5; }

/* ═══════════════════════════════════════════
   PROPOSITION
   ═══════════════════════════════════════════ */
.proposition-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.proposition-grid__item {
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding: 10px 14px;
    background: var(--surface);
    border-radius: 10px;
    border: 1px solid transparent;
    transition: border-color .2s;
}
.proposition-grid__item:hover {
    border-color: var(--border);
}
.proposition-grid__item--wide {
    grid-column: 1 / -1;
}
.proposition-grid__label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}
.proposition-grid__value {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text);
}
.proposition-grid__value--prix {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 800;
    color: var(--rust);
}
.proposition-grid__value--prix small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}
.proposition-grid__sub {
    font-weight: 400;
    color: var(--muted);
}

/* ═══════════════════════════════════════════
   RÉPONSE AGENCE
   ═══════════════════════════════════════════ */
.panel--reponse {
    background: linear-gradient(135deg, #F0F7FF 0%, #fff 60%);
    border-color: #BBDEFB;
}

.reponse-block {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.reponse-block__head {
    display: flex;
    align-items: center;
    gap: 9px;
}
.reponse-block__avatar {
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
.reponse-block__head strong {
    font-size: 13px;
    font-weight: 700;
    color: var(--c-info);
}
.reponse-block__text {
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
    background: var(--surface);
    border-radius: 12px;
    border: 1px dashed var(--border);
}
.reponse-empty i {
    font-size: 26px;
    color: var(--muted);
    opacity: .5;
    flex-shrink: 0;
}
.reponse-empty strong {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text-soft);
    margin-bottom: 2px;
}
.reponse-empty p {
    font-size: 12.5px;
    color: var(--muted);
    margin: 0;
}

/* ═══ FOOTER ═══ */
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
}
.avis-actions__right { margin-left: auto; }

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
    box-shadow: 0 4px 10px rgba(180, 83, 42, .25);
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
    .note-hero { padding: 26px 18px; }
    .note-hero__stars { font-size: 26px; }
    .note-hero__value { font-size: 44px; }
    .note-hero__value small { font-size: 17px; }
    .note-hero__label { font-size: 15px; }

    .panel { padding: 16px; }

    .agency-block { gap: 12px; }
    .agency-block__avatar { width: 48px; height: 48px; font-size: 18px; }
    .agency-block__cta { width: 100%; justify-content: center; }

    .proposition-grid { grid-template-columns: 1fr; }

    .reponse-block__text { padding-left: 0; margin-top: 4px; }

    .avis-actions { flex-direction: column; align-items: stretch; }
    .avis-actions .btn { width: 100%; justify-content: center; }
    .avis-actions__right { margin-left: 0; }
}

@media (max-width: 480px) {
    .note-hero { padding: 22px 14px; gap: 6px; }
    .note-hero__stars { font-size: 22px; gap: 4px; }
    .note-hero__value { font-size: 38px; }
    .note-hero__value small { font-size: 15px; }
    .note-hero__label { font-size: 14px; }
    .note-hero__date { font-size: 11.5px; padding: 3px 10px; }

    .panel { padding: 14px; }
    .panel__head h4 { font-size: 14px; }

    .agency-block__avatar { width: 44px; height: 44px; font-size: 16px; }
    .agency-block__name { font-size: 15px; }

    .comment-block { font-size: 13.5px; padding: 14px 16px 14px 20px; }

    .proposition-grid__value--prix { font-size: 15px; }

    .reponse-block__text { font-size: 13px; }

    .btn { font-size: 12px; padding: 7px 13px; }
}
</style>
@endpush