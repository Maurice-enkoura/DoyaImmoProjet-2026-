@extends('layouts.dashboard-agence')

@section('title', 'Mes offres — DoyaImmo')
@section('page_title', 'Mes offres')
@section('page_sub', 'Suivez vos propositions envoyées')

@section('content')
<div class="view active offres-page">

    @php
        $current = request('statut', '');
        $counts = [
            ''           => $propositions->total(),
            'en_attente' => $propositions->where('statut.value', 'en_attente')->count(),
            'acceptee'   => $propositions->where('statut.value', 'acceptee')->count(),
            'refusee'    => $propositions->where('statut.value', 'refusee')->count(),
            'terminee'   => $propositions->where('statut.value', 'terminee')->count(),
        ];
        $tabs = [
            ''           => ['label' => 'Tous',       'icon' => 'fa-list'],
            'en_attente' => ['label' => 'En attente', 'icon' => 'fa-clock'],
            'acceptee'   => ['label' => 'Acceptées',  'icon' => 'fa-check-circle'],
            'refusee'    => ['label' => 'Refusées',   'icon' => 'fa-times-circle'],
            'terminee'   => ['label' => 'Terminées',  'icon' => 'fa-check-double'],
        ];
    @endphp

    {{-- ═══ HEADER DESKTOP ═══ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Mes offres envoyées</h2>
            <p>Suivez le statut de chaque proposition envoyée à un client</p>
        </div>
        <a href="{{ route('agence.demandes.index') }}" class="btn btn-rust">
            <i class="fa-solid fa-plus"></i> Nouvelle offre
        </a>
    </div>

    {{-- ═══ STATS (desktop + mobile) ═══ --}}
    @if($counts[''] > 0)
        <div class="offres-stats">
            <div class="offres-stat">
                <span class="offres-stat__value">{{ $counts[''] }}</span>
                <span class="offres-stat__label">Total</span>
            </div>
            <div class="offres-stat {{ $counts['en_attente'] > 0 ? 'is-highlight' : '' }}">
                <span class="offres-stat__value">{{ $counts['en_attente'] }}</span>
                <span class="offres-stat__label">En attente</span>
            </div>
            <div class="offres-stat">
                <span class="offres-stat__value">{{ $counts['acceptee'] }}</span>
                <span class="offres-stat__label">Acceptées</span>
            </div>
            <div class="offres-stat">
                <span class="offres-stat__value">{{ $counts['terminee'] }}</span>
                <span class="offres-stat__label">Terminées</span>
            </div>
        </div>
    @endif

    {{-- ═══ FILTRES PILLS ═══ --}}
    @if($counts[''] > 0)
        <nav class="filter-tabs" aria-label="Filtrer par statut">
            @foreach($tabs as $key => $meta)
                <a href="{{ route('agence.propositions.index', $key ? ['statut' => $key] : []) }}"
                   class="filter-tab {{ $current === $key ? 'is-active' : '' }}">
                    <i class="fa-solid {{ $meta['icon'] }}"></i>
                    <span>{{ $meta['label'] }}</span>
                    <span class="filter-tab__count">{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </nav>
    @endif

    {{-- ═══ LISTE DES OFFRES ═══ --}}
    @if($propositions->count() > 0)

        <div class="offres-list">
            @foreach($propositions as $proposition)
                @php
                    $isVedette  = $proposition->bien && $proposition->bien->est_vedette;
                    $statut     = is_object($proposition->statut) ? $proposition->statut->value : $proposition->statut;
                    $statutLabel = is_object($proposition->statut) ? $proposition->statut->label() : ucfirst($proposition->statut);
                    $score      = $proposition->score_matching;
                    $scoreClass = $score >= 80 ? 'excellent' : ($score >= 60 ? 'moyen' : 'faible');

                    $prenom   = $proposition->particulier->user->prenom ?? 'C';
                    $nom      = $proposition->particulier->user->nom    ?? '';
                    $initials = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
                @endphp

                <article class="offre-card offre-card--{{ $statut }} {{ $isVedette ? 'is-vedette' : '' }}">

                    {{-- En-tête --}}
                    <header class="offre-card__top">
                        <span class="offre-status offre-status--{{ $statut }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ $statutLabel }}
                        </span>

                        @if($isVedette)
                            <span class="offre-badge offre-badge--vedette">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif

                        <time class="offre-card__time"
                              datetime="{{ $proposition->created_at->toIso8601String() }}"
                              title="{{ $proposition->created_at->format('d/m/Y à H:i') }}">
                            <i class="fa-regular fa-clock"></i>
                            {{ $proposition->created_at->diffForHumans() }}
                        </time>
                    </header>

                    {{-- Titre bien --}}
                    <h3 class="offre-card__title">
                        {{ $proposition->bien->titre ?? 'Bien' }}
                    </h3>

                    {{-- Meta demande --}}
                    <div class="offre-card__meta">
                        <span class="meta-item">
                            <i class="fa-solid fa-tag"></i>
                            {{ $proposition->demande->type_bien->label() }}
                        </span>
                        <span class="meta-item">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $proposition->demande->zone_recherchee }}
                        </span>
                        @if($proposition->demande->surface_minimum)
                            <span class="meta-item">
                                <i class="fa-regular fa-square"></i>
                                {{ $proposition->demande->surface_minimum }} m²
                            </span>
                        @endif
                    </div>

                    {{-- Client --}}
                    <div class="client-block">
                        <div class="client-avatar">{{ $initials }}</div>
                        <div class="client-info">
                            <strong>{{ $prenom }} {{ $nom }}</strong>
                            <span>{{ $proposition->particulier->user->email ?? '' }}</span>
                        </div>
                    </div>

                    {{-- Prix + Score --}}
                    <div class="offre-card__data">
                        <div class="offre-card__prix">
                            <span class="offre-card__prix-label">Montant proposé</span>
                            <span class="offre-card__prix-value">
                                {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                                <small>FCFA</small>
                            </span>
                        </div>

                        <div class="offre-card__score">
                            @if($score)
                                <div class="score-ring score-ring--{{ $scoreClass }}"
                                     style="--score: {{ $score }};">
                                    <svg viewBox="0 0 36 36" aria-hidden="true">
                                        <circle class="score-ring__bg" cx="18" cy="18" r="16"/>
                                        <circle class="score-ring__fill" cx="18" cy="18" r="16"/>
                                    </svg>
                                    <span class="score-ring__value">
                                        {{ $score }}<small>%</small>
                                    </span>
                                </div>
                                <span class="score-label score-label--{{ $scoreClass }}">
                                    {{ $score >= 80 ? 'Excellent' : ($score >= 60 ? 'Bon match' : 'Faible') }}
                                </span>
                            @else
                                <span class="score-empty">—</span>
                                <span class="score-empty-label">Non évalué</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <footer class="offre-card__footer">
                        <a href="{{ route('agence.propositions.show', $proposition) }}" class="btn btn-ghost">
                            <i class="fa-solid fa-eye"></i> Voir le détail
                        </a>
                    </footer>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $propositions->appends(request()->query())->links() }}
        </div>

    @else
        {{-- ═══ EMPTY STATE ═══ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-solid fa-file-invoice"></i>
            </div>

            @if(request('statut'))
                <h3>Aucune offre avec ce statut</h3>
                <p>Essayez de modifier vos filtres.</p>
                <a href="{{ route('agence.propositions.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-rotate"></i> Réinitialiser les filtres
                </a>
            @else
                <h3>Aucune offre envoyée</h3>
                <p>Parcourez les besoins des clients et proposez vos biens.</p>
                <a href="{{ route('agence.demandes.index') }}" class="btn btn-rust">
                    <i class="fa-solid fa-search"></i> Voir les besoins disponibles
                </a>
            @endif
        </div>
    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MES OFFRES — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.offres-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --card-radius:  12px;

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
    gap: 16px;
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
    font-size: 13px;
    color: var(--muted);
    margin: 4px 0 0;
}

/* ═══ STATS : visibles partout ═══ */
.offres-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}
.offres-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    padding: 16px 12px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    transition: transform .15s, box-shadow .15s;
}
.offres-stat:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0,0,0,.05);
}
.offres-stat.is-highlight {
    border-color: var(--c-warning);
    background: linear-gradient(135deg, #FFFBF0, #fff);
}
.offres-stat__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.5px;
    line-height: 1;
}
.offres-stat.is-highlight .offres-stat__value { color: var(--c-warning); }
.offres-stat__label {
    font-size: 11.5px;
    font-weight: 500;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .3px;
}

/* ═══ FILTRES : visibles partout ═══ */
.filter-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 18px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: thin;
}
.filter-tabs::-webkit-scrollbar { height: 4px; }
.filter-tabs::-webkit-scrollbar-thumb { background: var(--border); border-radius: 999px; }

.filter-tab {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    background: #fff;
    border: 1.5px solid var(--border);
    text-decoration: none;
    transition: all .2s ease;
    white-space: nowrap;
    flex-shrink: 0;
}
.filter-tab:hover { border-color: var(--rust); transform: translateY(-1px); }
.filter-tab i { font-size: 11px; opacity: .85; }
.filter-tab.is-active {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(180, 83, 42, .22);
}
.filter-tab.is-active i { color: #fff; opacity: 1; }

.filter-tab__count {
    background: rgba(0, 0, 0, .07);
    padding: 1px 7px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    line-height: 1.6;
    min-width: 20px;
    text-align: center;
}
.filter-tab.is-active .filter-tab__count {
    background: rgba(255, 255, 255, .25);
    color: #fff;
}

/* ═══ LISTE ═══ */
.offres-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* ═══ CARTE ═══ */
.offre-card {
    position: relative;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--card-radius);
    padding: 14px 18px;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
}
.offre-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
    border-color: #d8d8d8;
}
.offre-card.is-vedette {
    background: linear-gradient(135deg, #FFFDF5 0%, #fff 55%);
    border-color: #F5A623;
}

.offre-card__top {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}
.offre-card__time {
    margin-left: auto;
    font-size: 11.5px;
    color: var(--muted);
    display: inline-flex;
    align-items: center;
    gap: 5px;
    white-space: nowrap;
}

.offre-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.offre-status i { font-size: 5px; }
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.offre-status--terminee   { background: var(--c-info-bg);    color: var(--c-info); }

.offre-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 999px;
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}
.offre-badge--vedette {
    background: linear-gradient(135deg, #F5A623, #E8901A);
    color: #fff;
    box-shadow: 0 2px 6px rgba(245, 166, 35, .3);
}

.offre-card__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
    line-height: 1.3;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.offre-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 12px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 10px;
}
.meta-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.meta-item i {
    font-size: 10px;
    color: var(--rust);
    opacity: .8;
}

.client-block {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 10px 12px;
    background: var(--surface);
    border-radius: 10px;
    margin-bottom: 10px;
}
.client-avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    font-family: var(--display);
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
}
.client-info { min-width: 0; flex: 1; }
.client-info strong {
    display: block;
    font-size: 12.5px;
    color: var(--text);
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.client-info span {
    display: block;
    font-size: 11.5px;
    color: var(--muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.offre-card__data {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 10px 12px;
    background: var(--surface);
    border-radius: 10px;
    margin-bottom: 10px;
}

.offre-card__prix {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.offre-card__prix-label {
    font-size: 10px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
}
.offre-card__prix-value {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
}
.offre-card__prix-value small {
    font-size: 10px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}

.offre-card__score {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.score-ring {
    position: relative;
    width: 44px;
    height: 44px;
}
.score-ring svg {
    transform: rotate(-90deg);
    width: 100%;
    height: 100%;
    display: block;
}
.score-ring circle { fill: none; stroke-width: 3; }
.score-ring__bg { stroke: var(--border); }
.score-ring__fill {
    stroke-linecap: round;
    stroke-dasharray: calc(var(--score, 0) * 1.005) 100.5;
    transition: stroke-dasharray .6s ease;
}
.score-ring--excellent .score-ring__fill { stroke: var(--c-success); }
.score-ring--moyen     .score-ring__fill { stroke: var(--c-warning); }
.score-ring--faible    .score-ring__fill { stroke: var(--c-danger); }

.score-ring__value {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 12px;
    font-weight: 800;
    line-height: 1;
}
.score-ring--excellent .score-ring__value { color: var(--c-success); }
.score-ring--moyen     .score-ring__value { color: var(--c-warning); }
.score-ring--faible    .score-ring__value { color: var(--c-danger); }
.score-ring__value small { font-size: 8px; margin-left: 1px; font-weight: 700; }

.score-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.score-label--excellent { color: var(--c-success); }
.score-label--moyen     { color: var(--c-warning); }
.score-label--faible    { color: var(--c-danger); }

.score-empty {
    font-size: 22px;
    color: var(--muted);
    font-weight: 300;
    line-height: 1;
    min-width: 44px;
    text-align: center;
}
.score-empty-label {
    font-size: 10px;
    color: var(--muted);
}

.offre-card__footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 9px;
    font-size: 12px;
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
    background: #fff;
    border-color: var(--rust);
    color: var(--rust);
}

/* ═══ EMPTY ═══ */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #fff;
    border-radius: var(--card-radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--c-warning-bg);
    border-radius: 50%;
    color: var(--rust);
    font-size: 26px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
}
.empty-state p {
    color: var(--muted);
    font-size: 13px;
    margin: 0 0 16px;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
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

    .offres-page { background: transparent; padding-top: 4px; }

    /* ─── HEADER masqué ─── */
    .page-header { display: none; }

    /* ─── STATS INLINE iOS ─── */
    .offres-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        grid-template-columns: none;
    }
    .offres-stat {
        flex: 1;
        padding: 0 4px;
        background: transparent;
        border: none;
        border-radius: 0;
        gap: 2px;
    }
    .offres-stat:hover { transform: none; box-shadow: none; }
    .offres-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%; width: 0.5px;
        background: var(--ios-separator);
    }
    .offres-stat__value { font-size: 22px; }
    .offres-stat__label {
        font-size: 11px;
        color: var(--ios-label-3);
        text-transform: none;
        letter-spacing: 0;
    }

    /* ─── FILTRES PILLS iOS ─── */
    .filter-tabs {
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
        padding: 9px 16px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: all .15s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .filter-tab:active { transform: scale(.95); }
    .filter-tab.is-active {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .filter-tab.is-active .filter-tab__count {
        background: rgba(255, 255, 255, .3);
    }
    .filter-tab__count {
        background: #EFEFF4;
        color: var(--ios-label-2);
        font-size: 11px;
        padding: 1px 8px;
    }

    /* ─── CARTES iOS ─── */
    .offres-list { gap: 12px; }

    .offre-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: transform .15s ease;
        overflow: hidden;
        position: relative;
    }
    .offre-card:hover { transform: none; box-shadow: 0 1px 3px rgba(0,0,0,.04); border: none; }
    .offre-card:active { transform: scale(.985); }

    .offre-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        border-radius: var(--ios-radius-lg) var(--ios-radius-lg) 0 0;
    }
    .offre-card--en_attente::before { background: var(--c-warning); }
    .offre-card--acceptee::before   { background: var(--c-success); }
    .offre-card--refusee::before    { background: var(--c-danger); }
    .offre-card--terminee::before   { background: var(--c-info); }

    .offre-card.is-vedette {
        background: linear-gradient(135deg, #FFFDF5 0%, #fff 40%);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .offre-card__top { gap: 8px; margin-bottom: 12px; }
    .offre-status {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
    }
    .offre-status i { font-size: 7px; }
    .offre-badge {
        font-size: 10px;
        padding: 3px 9px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
    }
    .offre-card__time {
        font-size: 11.5px;
        color: var(--ios-label-3);
        margin-left: auto;
    }

    .offre-card__title {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
        color: var(--ios-label);
        margin-bottom: 6px;
    }

    .offre-card__meta {
        font-size: 13px;
        color: var(--ios-label-3);
        gap: 5px 12px;
        margin-bottom: 12px;
    }
    .meta-item i { color: var(--ios-label-3); font-size: 11px; }

    .client-block {
        background: #EFEFF4;
        border-radius: 12px;
        padding: 10px 12px;
        gap: 10px;
        margin-bottom: 12px;
    }
    .client-avatar { width: 36px; height: 36px; font-size: 13px; }
    .client-info strong { font-size: 14px; font-weight: 600; color: var(--ios-label); }
    .client-info span { font-size: 12.5px; color: var(--ios-label-3); }

    .offre-card__data {
        background: #EFEFF4;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 12px;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
    .offre-card__prix { text-align: left; }
    .offre-card__prix-label {
        font-size: 11px;
        color: var(--ios-label-3);
        text-transform: none;
        letter-spacing: 0;
        font-weight: 500;
    }
    .offre-card__prix-value {
        font-size: 20px;
        letter-spacing: -.4px;
    }

    .offre-card__score { flex-direction: row; gap: 8px; }
    .score-ring { width: 46px; height: 46px; }
    .score-ring__value { font-size: 13px; }
    .score-ring__value small { font-size: 9px; }
    .score-label { font-size: 12px; font-weight: 600; text-transform: none; letter-spacing: 0; }

    .offre-card__footer { justify-content: stretch; }
    .offre-card__footer .btn {
        width: 100%;
        justify-content: center;
        padding: 12px 18px;
        font-size: 14px;
        border-radius: 12px;
        font-weight: 600;
        background: var(--ios-card);
        border: 1px solid #E5E5EA;
        color: var(--ios-blue);
    }
    .offre-card__footer .btn:active { background: #EFEFF4; }

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
        background: #EFEFF4;
        color: var(--ios-label-3);
        font-size: 28px;
    }
    .empty-state h3 { font-size: 17px; letter-spacing: -.3px; }
    .empty-state p { font-size: 13.5px; color: var(--ios-label-3); }

    .pagination-wrapper { margin-top: 20px; }
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
    .offres-stat__value { font-size: 20px; }
    .offres-stat__label { font-size: 10.5px; }

    .offre-card { padding: 14px; }
    .offre-card__title { font-size: 15px; }
    .offre-card__prix-value { font-size: 19px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush