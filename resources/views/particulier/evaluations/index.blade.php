@extends('layouts.dashboard')

@section('title', 'Mes avis donnés — DoyaImmo')
@section('page_title', 'Mes avis donnés')
@section('page_sub', 'Vos évaluations des agences avec lesquelles vous avez travaillé')

@section('content')
<div class="view active avis-donnes">

    @php
        $total        = $evaluations->total();
        $moyenne      = $evaluations->avg('note') ?? 0;
        $cinqEtoiles  = $evaluations->where('note', 5)->count();
        $avecReponse  = $evaluations->whereNotNull('reponse_agence')->count();
        $positives    = $evaluations->where('note', '>=', 4)->count();
        $negatives    = $evaluations->where('note', '<=', 2)->count();

        $current = request('filtre', '');
        $tabs = [
            ''        => ['label' => 'Tous',      'icon' => 'fa-list',       'count' => $total],
            '5'       => ['label' => '5 étoiles', 'icon' => 'fa-star',       'count' => $cinqEtoiles],
            'reponse' => ['label' => 'Réponses',  'icon' => 'fa-reply',      'count' => $avecReponse],
            'positif' => ['label' => 'Positifs',  'icon' => 'fa-thumbs-up',  'count' => $positives],
            'negatif' => ['label' => 'Négatifs',  'icon' => 'fa-thumbs-down','count' => $negatives],
        ];
    @endphp

    {{-- ═══ HEADER DESKTOP ═══ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Mes avis donnés</h2>
            <p>Vos évaluations des agences avec lesquelles vous avez travaillé</p>
        </div>
        <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost btn-sm">
            Voir mes rendez-vous
        </a>
    </div>

    {{-- ═══ STATS INLINE (mobile) ═══ --}}
    @if($total > 0)
        <div class="avis-stats">
            <div class="avis-stat">
                <span class="avis-stat__value">{{ number_format($moyenne, 1) }}</span>
                <span class="avis-stat__label">Moyenne</span>
            </div>
            <div class="avis-stat">
                <span class="avis-stat__value">{{ $total }}</span>
                <span class="avis-stat__label">Avis</span>
            </div>
            <div class="avis-stat">
                <span class="avis-stat__value">{{ $cinqEtoiles }}</span>
                <span class="avis-stat__label">5 étoiles</span>
            </div>
            <div class="avis-stat">
                <span class="avis-stat__value">{{ $avecReponse }}</span>
                <span class="avis-stat__label">Réponses</span>
            </div>
        </div>
    @endif

    {{-- ═══ FILTRES PILLS ═══ --}}
    @if($total > 0)
        <nav class="filter-tabs" aria-label="Filtrer les avis">
            @foreach($tabs as $key => $meta)
                @if($meta['count'] > 0 || $key === '')
                    <a href="{{ route('particulier.evaluations.index', $key ? ['filtre' => $key] : []) }}"
                       class="filter-tab {{ $current === $key ? 'is-active' : '' }}">
                        <i class="fa-solid {{ $meta['icon'] }}"></i>
                        <span>{{ $meta['label'] }}</span>
                        <span class="filter-tab__count">{{ $meta['count'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    @endif

    @if($total > 0)

        {{-- ═══ LISTE DES AVIS ═══ --}}
        <div class="avis-list">
            @foreach($evaluations as $evaluation)

                @php
                    $note         = (int) $evaluation->note;
                    $sentiment    = $note >= 4 ? 'positif' : ($note === 3 ? 'neutre' : 'negatif');
                    $initial      = strtoupper(mb_substr($evaluation->agence->nom_agence ?? 'A', 0, 1));
                    $hasReponse   = !empty($evaluation->reponse_agence);
                    $isOwner      = $evaluation->particulier_id === Auth::user()->particulier->id;
                    $proposition  = $evaluation->proposition;
                    $bien         = $proposition->bien ?? null;
                @endphp

                <article class="avis-card avis-card--{{ $sentiment }}">

                    {{-- En-tête : agence + note --}}
                    <header class="avis-card__head">
                        <div class="avis-card__agency">
                            <div class="agency-avatar">{{ $initial }}</div>
                            <div class="agency-info">
                                <strong>{{ $evaluation->agence->nom_agence }}</strong>
                                <span class="avis-card__date">
                                    Avis du {{ $evaluation->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="avis-card__rating avis-card__rating--{{ $sentiment }}">
                            <div class="avis-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $note ? 'is-on' : '' }}"></i>
                                @endfor
                            </div>
                            <span class="avis-card__score">{{ $note }}<small>/5</small></span>
                        </div>
                    </header>

                    {{-- Proposition associée --}}
                    @if($proposition)
                        <div class="avis-card__prop">
                            <span>
                                Proposition du
                                <strong>{{ $proposition->created_at->format('d/m/Y') }}</strong>
                                @if($bien)
                                    · {{ $bien->titre ?? 'Bien' }}
                                @endif
                            </span>
                        </div>
                    @endif

                    {{-- Commentaire --}}
                    @if($evaluation->commentaire)
                        <blockquote class="avis-card__comment">
                            {{ $evaluation->commentaire }}
                        </blockquote>
                    @endif

                    {{-- Réponse agence --}}
                    @if($hasReponse)
                        <div class="reponse-block">
                            <div class="reponse-block__head">
                                <span>Réponse de l'agence</span>
                                @if($evaluation->date_reponse)
                                    <span class="reponse-block__date">
                                        · {{ $evaluation->date_reponse->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                            <p class="reponse-block__text">{{ $evaluation->reponse_agence }}</p>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <footer class="avis-card__actions">
                        <a href="{{ route('particulier.evaluations.show', $evaluation->id) }}"
                           class="btn btn-ghost">
                            Voir le détail
                        </a>

                        @if($isOwner)
                            <a href="{{ route('particulier.evaluations.edit', $evaluation->id) }}"
                               class="btn btn-ghost">
                                Modifier
                            </a>

                            <form action="{{ route('particulier.evaluations.destroy', $evaluation->id) }}"
                                  method="POST" class="inline-form avis-card__delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger-soft"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
                                    Supprimer
                                </button>
                            </form>
                        @endif
                    </footer>
                </article>
            @endforeach
        </div>

        {{-- ═══ Pagination ═══ --}}
        <div class="pagination-wrapper">
            {{ $evaluations->appends(request()->query())->links() }}
        </div>

    @else

        {{-- ═══ EMPTY STATE ═══ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-star"></i>
            </div>
            <h3>Aucun avis donné pour le moment</h3>
            <p>Évaluez les agences après avoir terminé une visite. Vos avis aident les autres clients à choisir.</p>
            <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-rust">
                Voir mes rendez-vous
            </a>
        </div>

    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MES AVIS DONNÉS — Design épuré
   ═══════════════════════════════════════════════════════════ */

.avis-donnes {
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

/* ═══ HEADER DESKTOP ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.page-header__left h2 {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.2;
}
.page-header__left p {
    font-size: 13.5px;
    color: var(--muted);
    margin: 4px 0 0;
}

/* ═══ STATS INLINE (mobile) ═══ */
.avis-stats { display: none; }

/* ═══ FILTRES ═══ */
.filter-tabs { display: none; }

/* ═══ LISTE AVIS ═══ */
.avis-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.avis-card {
    position: relative;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    transition: border-color .15s;
}
.avis-card:hover {
    border-color: #d8d8d8;
}

/* En-tête */
.avis-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.avis-card__agency { display: flex; align-items: center; gap: 11px; min-width: 0; }
.agency-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--display);
    font-size: 18px; font-weight: 700;
    flex-shrink: 0;
}
.agency-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.agency-info strong {
    font-family: var(--display);
    font-size: 15px; font-weight: 700;
    color: var(--text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.avis-card__date {
    display: inline-flex; align-items: center;
    font-size: 12px; color: var(--muted);
}

/* Note */
.avis-card__rating {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 5px 11px;
    border-radius: 999px;
    flex-shrink: 0;
    background: #F5F5F7;
}

.avis-stars { display: inline-flex; gap: 3px; font-size: 14px; }
.avis-stars .fa-star { color: #D4D8E0; }
.avis-stars .fa-star.is-on { color: var(--c-gold); }

.avis-card__score {
    font-family: var(--display);
    font-size: 15px; font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.avis-card__score small { font-size: 10px; font-weight: 600; opacity: .65; margin-left: 1px; }

/* Proposition */
.avis-card__prop {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 12px;
    background: #F5F5F7;
    border-radius: 999px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 12px;
}
.avis-card__prop strong { color: var(--text); font-weight: 600; }

/* Commentaire */
.avis-card__comment {
    padding: 14px 18px;
    border-radius: 12px;
    background: #F9FAFB;
    font-size: 13.5px;
    line-height: 1.65;
    color: var(--text);
    font-style: italic;
    margin: 0 0 12px;
    border-left: 3px solid var(--rust);
}

/* Réponse */
.reponse-block {
    padding: 12px 14px;
    background: #F9FAFB;
    border-radius: 10px;
    border-left: 3px solid var(--c-info);
    margin-bottom: 12px;
}
.reponse-block__head {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: var(--c-info);
    flex-wrap: wrap;
}
.reponse-block__date { font-weight: 500; text-transform: none; letter-spacing: 0; color: var(--muted); }
.reponse-block__text { font-size: 13px; line-height: 1.6; color: var(--text-soft); margin: 0; }

/* Actions */
.avis-card__actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.avis-card__delete { margin-left: auto; }
.inline-form { display: inline; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
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

.btn-danger-soft { background: transparent; color: var(--c-danger); border-color: var(--border); }
.btn-danger-soft:hover { background: var(--c-danger-bg); border-color: #FECACA; }

.btn-sm { padding: 6px 12px; font-size: 12px; }

/* ═══ EMPTY ═══ */
.empty-state {
    text-align: center;
    padding: 70px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 72px; height: 72px;
    margin: 0 auto 16px;
    display: flex; align-items: center; justify-content: center;
    background: #F5F5F7;
    border-radius: 50%;
    color: var(--muted);
    font-size: 28px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 17px; font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
}
.empty-state p {
    color: var(--muted);
    font-size: 13.5px;
    margin: 0 auto 20px;
    max-width: 420px;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper { margin-top: 22px; display: flex; justify-content: center; }
.pagination-wrapper .pagination,
.pagination-wrapper nav {
    display: flex; gap: 5px; justify-content: center;
    list-style: none; padding: 0; margin: 0; flex-wrap: wrap;
}
.pagination-wrapper a,
.pagination-wrapper span:not(.sr-only) {
    display: inline-block;
    padding: 7px 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    font-size: 12.5px;
    transition: background-color .15s;
    min-width: 36px;
    text-align: center;
    background: #fff;
}
.pagination-wrapper a:hover { background: #F5F5F7; }
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] { background: var(--rust); color: #fff; border-color: var(--rust); }
.pagination-wrapper .disabled span { opacity: .45; cursor: not-allowed; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .avis-donnes { background: transparent; padding-top: 4px; }

    .page-header { display: none; }

    /* STATS INLINE */
    .avis-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .avis-stat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        position: relative;
        padding: 0 4px;
    }
    .avis-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%;
        width: 0.5px;
        background: var(--ios-separator);
    }
    .avis-stat__value {
        display: inline-flex;
        align-items: center;
        gap: 3px;
        font-family: var(--display);
        font-size: 22px;
        font-weight: 800;
        color: var(--ios-label);
        letter-spacing: -.5px;
        line-height: 1;
    }
    .avis-stat__label {
        font-size: 11px;
        font-weight: 500;
        color: var(--ios-label-3);
        margin-top: 2px;
    }

    /* FILTRES PILLS iOS */
    .filter-tabs {
        display: flex;
        gap: 8px;
        margin: 0 -16px 14px;
        padding: 0 16px 2px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .filter-tabs::-webkit-scrollbar { display: none; }
    .filter-tab {
        scroll-snap-align: start;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 16px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: var(--ios-label-2);
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        -webkit-tap-highlight-color: transparent;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .filter-tab:active { transform: scale(.98); }
    .filter-tab i { font-size: 12px; }
    .filter-tab.is-active {
        background: var(--rust);
        color: #fff;
    }
    .filter-tab__count {
        background: rgba(0, 0, 0, .07);
        padding: 1px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
        min-width: 22px;
        text-align: center;
    }
    .filter-tab.is-active .filter-tab__count {
        background: rgba(255, 255, 255, .3);
        color: #fff;
    }

    /* ALERTS iOS */
    .alert {
        border-radius: 14px;
        padding: 14px 16px;
        font-size: 13.5px;
    }

    /* CARTES iOS */
    .avis-list { gap: 12px; }

    .avis-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .avis-card:hover { border: none; }

    /* En-tête */
    .avis-card__head {
        gap: 10px;
        margin-bottom: 12px;
        align-items: flex-start;
    }
    .agency-avatar {
        width: 42px; height: 42px; font-size: 17px;
        background: #F5F5F7;
    }
    .agency-info strong { font-size: 15px; font-weight: 700; color: var(--ios-label); }
    .avis-card__date { font-size: 12.5px; color: var(--ios-label-3); }

    .avis-card__rating {
        padding: 5px 10px;
        gap: 7px;
        background: #F5F5F7;
    }
    .avis-stars { font-size: 13px; gap: 2px; }
    .avis-card__score { font-size: 14px; }

    /* Proposition */
    .avis-card__prop {
        background: #EFEFF4;
        padding: 6px 12px;
        font-size: 12.5px;
        border-radius: 10px;
        margin-bottom: 12px;
    }

    /* Commentaire */
    .avis-card__comment {
        padding: 14px 16px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.6;
        color: var(--ios-label-2);
        margin: 0 0 12px;
    }

    /* Réponse iOS */
    .reponse-block {
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 12px;
        background: #F9FAFB;
    }
    .reponse-block__head {
        font-size: 11px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
    }
    .reponse-block__text { font-size: 13.5px; }

    /* Actions iOS */
    .avis-card__actions {
        gap: 8px;
        padding-top: 12px;
        border-top: 0.5px solid var(--ios-separator);
        flex-direction: column;
        align-items: stretch;
    }
    .avis-card__actions .btn,
    .avis-card__actions form,
    .avis-card__actions .inline-form {
        width: 100%;
        justify-content: center;
    }
    .avis-card__delete { margin-left: 0; }

    .btn {
        padding: 12px 18px;
        font-size: 14px;
        border-radius: 12px;
        font-weight: 600;
    }
    .btn-rust {
        background: var(--rust);
        border: none;
    }
    .btn-ghost {
        background: #F5F5F7;
        border: none;
        color: var(--ios-blue);
    }
    .btn-ghost:active { background: #EFEFF4; }
    .btn-danger-soft {
        background: transparent;
        border: 1px solid #FECACA;
        color: var(--c-danger);
    }

    /* EMPTY iOS */
    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon {
        width: 64px;
        height: 64px;
        font-size: 26px;
        background: #F5F5F7;
    }
    .empty-state h3 { font-size: 17px; }
    .empty-state p { font-size: 13.5px; color: var(--ios-label-3); }

    /* Pagination iOS */
    .pagination-wrapper a,
    .pagination-wrapper span:not(.sr-only) {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        padding: 10px 14px;
        font-size: 13px;
        border-radius: 10px;
    }
}

@media (max-width: 480px) {
    .avis-stat__value { font-size: 20px; }
    .avis-stat__label { font-size: 10.5px; }

    .avis-card { padding: 14px; }
    .agency-avatar { width: 38px; height: 38px; font-size: 15px; }
    .agency-info strong { font-size: 14px; }
    .avis-stars { font-size: 12px; }
    .avis-card__comment { font-size: 13px; padding: 12px 14px; }
    .reponse-block__text { font-size: 13px; }
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