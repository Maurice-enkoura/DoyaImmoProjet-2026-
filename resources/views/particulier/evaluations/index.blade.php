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
            <i class="fa-solid fa-calendar"></i> Voir mes rendez-vous
        </a>
    </div>

    {{-- ═══ STATS INLINE (mobile) ═══ --}}
    @if($total > 0)
        <div class="avis-stats">
            <div class="avis-stat">
                <span class="avis-stat__value">
                    <i class="fa-solid fa-star"></i>
                    {{ number_format($moyenne, 1) }}
                </span>
                <span class="avis-stat__label">Moyenne</span>
            </div>
            <div class="avis-stat">
                <span class="avis-stat__value">{{ $total }}</span>
                <span class="avis-stat__label">Avis</span>
            </div>
            <div class="avis-stat {{ $cinqEtoiles > 0 ? 'is-highlight' : '' }}">
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

    {{-- ═══ ALERTS ═══ --}}
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
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
                                    <i class="fa-regular fa-calendar"></i>
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
                            <i class="fa-regular fa-file-lines"></i>
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
                                <i class="fa-solid fa-reply"></i>
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

                    {{-- Actions iOS pleine largeur --}}
                    <footer class="avis-card__actions">
                        <a href="{{ route('particulier.evaluations.show', $evaluation->id) }}"
                           class="btn btn-ghost">
                            <i class="fa-solid fa-eye"></i> Voir le détail
                        </a>

                        @if($isOwner)
                            <a href="{{ route('particulier.evaluations.edit', $evaluation->id) }}"
                               class="btn btn-ghost">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </a>

                            <form action="{{ route('particulier.evaluations.destroy', $evaluation->id) }}"
                                  method="POST" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger-soft"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
                                    <i class="fa-solid fa-trash-can"></i> Supprimer
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
                <i class="fa-solid fa-calendar"></i> Voir mes rendez-vous
            </a>
        </div>

    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MES AVIS DONNÉS — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.avis-donnes {
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

/* ═══ ALERTS ═══ */
.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-radius: 10px;
    margin-bottom: 14px;
    border-left: 4px solid;
    font-size: 13px;
    font-weight: 500;
}
.alert-success { background: var(--c-success-bg); color: var(--c-success); border-left-color: var(--c-success); }
.alert-error   { background: var(--c-danger-bg);  color: var(--c-danger);  border-left-color: var(--c-danger); }
.alert-info    { background: var(--c-info-bg);    color: var(--c-info);    border-left-color: var(--c-info); }

/* ═══ STATS DESKTOP ═══ */
.summary {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 18px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.summary__item { display: flex; flex-direction: column; gap: 2px; }
.summary__value {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.summary__value i { color: var(--c-gold); font-size: 19px; }
.summary__value--success { color: var(--c-success); }
.summary__value--success i { color: var(--c-success); }
.summary__value--info    { color: var(--c-info); }
.summary__value--info i { color: var(--c-info); }
.summary__label { font-size: 12.5px; color: var(--muted); font-weight: 500; }
.summary__label i { margin-right: 3px; font-size: 10px; }
.summary__sep { width: 1px; height: 34px; background: var(--border); flex-shrink: 0; }

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
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
    overflow: hidden;
}
.avis-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, .05);
    border-color: #d8d8d8;
}

.avis-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 4px;
}
.avis-card--positif::before { background: var(--c-success); }
.avis-card--neutre::before  { background: var(--c-warning); }
.avis-card--negatif::before { background: var(--c-danger); }

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
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
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
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; color: var(--muted);
}
.avis-card__date i { font-size: 11px; opacity: .8; }

/* Note */
.avis-card__rating {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 12px;
    border-radius: 999px;
    flex-shrink: 0;
}
.avis-card__rating--positif { background: var(--c-success-bg); }
.avis-card__rating--neutre  { background: var(--c-warning-bg); }
.avis-card__rating--negatif { background: var(--c-danger-bg); }

.avis-stars { display: inline-flex; gap: 3px; font-size: 14px; }
.avis-stars .fa-star { color: #D4D8E0; }
.avis-stars .fa-star.is-on { color: var(--c-gold); }

.avis-card__score {
    font-family: var(--display);
    font-size: 15px; font-weight: 800;
    line-height: 1;
}
.avis-card__score small { font-size: 10px; font-weight: 600; opacity: .65; margin-left: 1px; }
.avis-card__rating--positif .avis-card__score { color: var(--c-success); }
.avis-card__rating--neutre  .avis-card__score { color: var(--c-warning); }
.avis-card__rating--negatif .avis-card__score { color: var(--c-danger); }

/* Proposition */
.avis-card__prop {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 12px;
    background: var(--surface);
    border-radius: 999px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 12px;
}
.avis-card__prop i { font-size: 11px; color: var(--rust); }
.avis-card__prop strong { color: var(--text); font-weight: 600; }

/* Commentaire */
.avis-card__comment {
    position: relative;
    padding: 14px 18px 14px 22px;
    border-radius: 12px;
    background: var(--surface);
    font-size: 13.5px;
    line-height: 1.65;
    color: var(--text);
    font-style: italic;
    margin: 0 0 12px;
    border-left: 3px solid var(--muted);
}
.avis-card--positif .avis-card__comment {
    background: linear-gradient(135deg, rgba(30, 122, 71, .05), rgba(30, 122, 71, .02));
    border-left-color: var(--c-success);
}
.avis-card--neutre .avis-card__comment {
    background: linear-gradient(135deg, rgba(230, 81, 0, .05), rgba(230, 81, 0, .02));
    border-left-color: var(--c-warning);
}
.avis-card--negatif .avis-card__comment {
    background: linear-gradient(135deg, rgba(198, 40, 40, .05), rgba(198, 40, 40, .02));
    border-left-color: var(--c-danger);
}

/* Réponse */
.reponse-block {
    padding: 12px 14px;
    background: var(--c-info-bg);
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
.reponse-block__head i { font-size: 10px; }
.reponse-block__date { font-weight: 500; text-transform: none; letter-spacing: 0; color: var(--muted); }
.reponse-block__text { font-size: 13px; line-height: 1.6; color: var(--text-soft); margin: 0; }

/* Actions */
.avis-card__actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px dashed var(--border);
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
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; color: #fff; border-color: #9A4523; transform: translateY(-1px); box-shadow: 0 4px 10px rgba(180, 83, 42, .25); }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: var(--surface); border-color: var(--rust); color: var(--rust); }
.btn-danger-soft { background: var(--c-danger-bg); color: var(--c-danger); border-color: #FFCDD2; }
.btn-danger-soft:hover { background: var(--c-danger); color: #fff; border-color: var(--c-danger); }
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
    width: 76px; height: 76px;
    margin: 0 auto 18px;
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #FFF8E1, #FFECB3);
    border-radius: 50%;
    color: var(--c-gold);
    font-size: 30px;
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
    transition: all .2s;
    min-width: 36px;
    text-align: center;
    background: #fff;
}
.pagination-wrapper a:hover { background: var(--border); border-color: var(--rust); color: var(--rust); }
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] { background: var(--rust); color: #fff; border-color: var(--rust); }
.pagination-wrapper .disabled span { opacity: .45; cursor: not-allowed; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .avis-donnes { background: transparent; padding-top: 4px; }

    /* ─── HEADER masqué ─── */
    .page-header { display: none; }

    /* ─── STATS INLINE ─── */
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
    .avis-stat__value i {
        color: var(--c-gold);
        font-size: 15px;
    }
    .avis-stat.is-highlight .avis-stat__value { color: var(--rust); }
    .avis-stat.is-highlight .avis-stat__value i { color: var(--rust); }
    .avis-stat__label {
        font-size: 11px;
        font-weight: 500;
        color: var(--ios-label-3);
        margin-top: 2px;
    }

    /* ─── FILTRES PILLS iOS ─── */
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
        transition: all .15s ease;
        -webkit-tap-highlight-color: transparent;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .filter-tab:active { transform: scale(.95); }
    .filter-tab i { font-size: 12px; opacity: .85; }
    .filter-tab.is-active {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .filter-tab.is-active i { opacity: 1; }
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

    /* ─── ALERTS iOS ─── */
    .alert {
        border-radius: 14px;
        border: none;
        padding: 14px 16px;
        font-size: 13.5px;
    }

    /* ─── CARTES iOS ─── */
    .avis-list { gap: 12px; }

    .avis-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: transform .15s ease;
        overflow: hidden;
    }
    .avis-card:hover { transform: none; box-shadow: 0 1px 3px rgba(0,0,0,.04); border: none; }
    .avis-card:active { transform: scale(.985); }

    /* Barre statut : plus fine en haut */
    .avis-card::before {
        top: 0; left: 0; right: 0; bottom: auto;
        width: auto;
        height: 3px;
        border-radius: 0;
    }

    /* En-tête */
    .avis-card__head {
        gap: 10px;
        margin-bottom: 12px;
        align-items: flex-start;
    }
    .agency-avatar { width: 42px; height: 42px; font-size: 17px; }
    .agency-info strong { font-size: 15px; font-weight: 700; letter-spacing: -.2px; color: var(--ios-label); }
    .avis-card__date { font-size: 12.5px; color: var(--ios-label-3); }

    .avis-card__rating {
        padding: 5px 10px;
        gap: 7px;
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
        padding: 14px 16px 14px 18px;
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
    }
    .reponse-block__head {
        font-size: 11px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
    }
    .reponse-block__text { font-size: 13.5px; }

    /* ─── Actions iOS ─── */
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
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    }
    .btn-ghost {
        background: var(--ios-card);
        border: 1px solid #E5E5EA;
        color: var(--ios-blue);
    }
    .btn-ghost:active { background: #EFEFF4; }
    .btn-danger-soft {
        background: rgba(198, 40, 40, .08);
        border: none;
        color: var(--c-danger);
    }

    /* ─── EMPTY iOS ─── */
    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon {
        width: 68px;
        height: 68px;
        font-size: 28px;
    }
    .empty-state h3 { font-size: 17px; letter-spacing: -.3px; }
    .empty-state p { font-size: 13.5px; color: var(--ios-label-3); }

    /* ─── Pagination iOS ─── */
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
    .avis-stat__value i { font-size: 13px; }
    .avis-stat__label { font-size: 10.5px; }

    .avis-card { padding: 14px; }
    .agency-avatar { width: 38px; height: 38px; font-size: 15px; }
    .agency-info strong { font-size: 14px; }
    .avis-stars { font-size: 12px; }
    .avis-card__comment { font-size: 13px; padding: 12px 14px 12px 16px; }
    .reponse-block__text { font-size: 13px; }
}
</style>
@endpush