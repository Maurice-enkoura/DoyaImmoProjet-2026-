@extends('layouts.dashboard-agence')

@section('title', 'Mes offres — DoyaImmo')
@section('page_title', 'Mes offres')
@section('page_sub', 'Suivez vos propositions envoyées')

@section('content')
<div class="view active offres-page">

    {{-- ═══════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Mes offres envoyées</h2>
            <p>Suivez le statut de chaque proposition envoyée à un client</p>
        </div>
        <a href="{{ route('agence.demandes.index') }}" class="btn btn-rust">
            <i class="fa-solid fa-plus"></i> Nouvelle offre
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         FILTRES AVEC COMPTEURS
    ═══════════════════════════════════════════ --}}
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

    {{-- ═══════════════════════════════════════════
         ALERTS
    ═══════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         LISTE DES OFFRES
    ═══════════════════════════════════════════ --}}
    @if($propositions->count() > 0)

        <div class="offres-list">
            @foreach($propositions as $proposition)
                @php
                    $isVedette  = $proposition->bien && $proposition->bien->est_vedette;
                    $statut     = $proposition->statut->value;
                    $score      = $proposition->score_matching;
                    $scoreClass = $score >= 80 ? 'excellent' : ($score >= 60 ? 'moyen' : 'faible');

                    $prenom   = $proposition->particulier->user->prenom ?? 'C';
                    $nom      = $proposition->particulier->user->nom    ?? '';
                    $initials = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
                @endphp

                <article class="offre-card offre-card--{{ $statut }} {{ $isVedette ? 'is-vedette' : '' }}">

                    {{-- En-tête : statut + vedette + date --}}
                    <header class="offre-card__top">
                        <span class="offre-status offre-status--{{ $statut }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ $proposition->statut->label() }}
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

                    {{-- Corps : 3 zones --}}
                    <div class="offre-card__body">

                        {{-- Zone 1 : Bien + client --}}
                        <div class="offre-card__main">
                            <h3 class="offre-card__title">
                                {{ $proposition->bien->titre ?? 'Bien' }}
                            </h3>

                            <div class="offre-card__meta">
                                <span>
                                    <i class="fa-solid fa-tag"></i>
                                    {{ $proposition->demande->type_bien->label() }}
                                </span>
                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $proposition->demande->zone_recherchee }}
                                </span>
                                @if($proposition->demande->surface_minimum)
                                    <span>
                                        <i class="fa-regular fa-square"></i>
                                        {{ $proposition->demande->surface_minimum }} m²
                                    </span>
                                @endif
                            </div>

                            <div class="offre-card__client">
                                <div class="avatar avatar--sm">{{ $initials }}</div>
                                <div class="offre-card__client-info">
                                    <strong>{{ $prenom }} {{ $nom }}</strong>
                                    <span>{{ $proposition->particulier->user->email ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Zone 2 : Montant --}}
                        <div class="offre-card__prix">
                            <span class="offre-card__prix-label">Montant proposé</span>
                            <span class="offre-card__prix-value">
                                {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                                <small>FCFA</small>
                            </span>
                        </div>

                        {{-- Zone 3 : Score --}}
                        <div class="offre-card__score">
                            @if($score)
                                <div class="score-ring score-ring--{{ $scoreClass }}"
                                     style="--score: {{ $score }};"
                                     role="img"
                                     aria-label="Score de matching : {{ $score }}%">
                                    <svg viewBox="0 0 36 36" aria-hidden="true">
                                        <circle class="score-ring__bg" cx="18" cy="18" r="16"/>
                                        <circle class="score-ring__fill" cx="18" cy="18" r="16"/>
                                    </svg>
                                    <span class="score-ring__value">
                                        {{ $score }}<small>%</small>
                                    </span>
                                </div>
                                @if($proposition->niveau_matching)
                                    <span class="score-label score-label--{{ $scoreClass }}">
                                        {{ $proposition->niveau_matching }}
                                    </span>
                                @endif
                            @else
                                <span class="score-empty" aria-hidden="true">—</span>
                                <small class="score-empty-label">Non évalué</small>
                            @endif
                        </div>
                    </div>

                    {{-- Pied : action --}}
                    <footer class="offre-card__footer">
                        <a href="{{ route('agence.propositions.show', $proposition) }}"
                           class="btn btn-ghost btn-sm">
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
        {{-- ═══════════════════════════════════════════
             EMPTY STATE
        ═══════════════════════════════════════════ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-solid fa-file-invoice"></i>
            </div>

            @if(request('statut'))
                <h3>Aucune offre avec le statut « {{ request('statut') }} »</h3>
                <p>Essayez de modifier vos filtres.</p>
                <a href="{{ route('agence.propositions.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-rotate"></i> Réinitialiser les filtres
                </a>
            @else
                <h3>Aucune offre envoyée.</h3>
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
   PAGE OFFRES — Styles dédiés (version compacte)
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
}

/* ═══ HEADER ═══ */
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

/* ═══ FILTRES ONGLETS ═══ */
.filter-tabs {
    display: flex;
    gap: 6px;
    margin-bottom: 18px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
}
.filter-tabs::-webkit-scrollbar { height: 4px; }
.filter-tabs::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 999px;
}

.filter-tab {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 13px;
    border-radius: 10px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-soft);
    background: #fff;
    border: 1.5px solid var(--border);
    text-decoration: none;
    transition: all .2s ease;
    white-space: nowrap;
    flex-shrink: 0;
    cursor: pointer;
}
.filter-tab:hover {
    border-color: var(--rust);
    transform: translateY(-1px);
}
.filter-tab i {
    font-size: 11px;
    opacity: .85;
}
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

/* ═══ ALERTS ═══ */
.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 16px;
    border-radius: 10px;
    margin-bottom: 16px;
    border-left: 4px solid;
    font-size: 13px;
    font-weight: 500;
}
.alert-success {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-left-color: var(--c-success);
}
.alert-error {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border-left-color: var(--c-danger);
}

/* ═══ LISTE ═══ */
.offres-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* ═══ CARTE OFFRE ═══ */
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
.offre-card.is-vedette:hover {
    box-shadow: 0 6px 18px rgba(245, 166, 35, .12);
}

/* En-tête */
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

/* Statut pill */
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

/* Badge vedette */
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
    animation: pulseVedette 2s ease-in-out infinite;
}
@keyframes pulseVedette {
    0%, 100% { opacity: 1; }
    50%      { opacity: .8; }
}

/* Corps : 3 zones */
.offre-card__body {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(130px, 1fr) auto;
    gap: 22px;
    align-items: center;
}

/* Zone 1 : bien + client */
.offre-card__main { min-width: 0; }

.offre-card__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 5px;
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
    margin-bottom: 9px;
}
.offre-card__meta span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.offre-card__meta i {
    font-size: 10px;
    opacity: .75;
}

/* Client */
.offre-card__client {
    display: flex;
    align-items: center;
    gap: 9px;
    padding-top: 9px;
    border-top: 1px dashed var(--border);
}
.offre-card__client-info { min-width: 0; }
.offre-card__client strong {
    display: block;
    font-size: 12.5px;
    color: var(--text);
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.offre-card__client span {
    display: block;
    font-size: 11.5px;
    color: var(--muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Avatar */
.avatar {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    font-weight: 700;
    font-family: var(--display);
    flex-shrink: 0;
    letter-spacing: .5px;
}
.avatar--sm {
    width: 32px;
    height: 32px;
    font-size: 11px;
}

/* Zone 2 : prix */
.offre-card__prix {
    display: flex;
    flex-direction: column;
    gap: 2px;
    text-align: right;
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

/* Zone 3 : score circulaire */
.offre-card__score {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    min-width: 64px;
}

.score-ring {
    position: relative;
    width: 50px;
    height: 50px;
}
.score-ring svg {
    transform: rotate(-90deg);
    width: 100%;
    height: 100%;
    display: block;
}
.score-ring circle {
    fill: none;
    stroke-width: 3;
}
.score-ring__bg {
    stroke: var(--border);
}
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
    font-size: 12.5px;
    font-weight: 800;
    line-height: 1;
}
.score-ring--excellent .score-ring__value { color: var(--c-success); }
.score-ring--moyen     .score-ring__value { color: var(--c-warning); }
.score-ring--faible    .score-ring__value { color: var(--c-danger); }
.score-ring__value small {
    font-size: 8px;
    margin-left: 1px;
    font-weight: 700;
}

.score-label {
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    text-align: center;
}
.score-label--excellent { color: var(--c-success); }
.score-label--moyen     { color: var(--c-warning); }
.score-label--faible    { color: var(--c-danger); }

.score-empty {
    font-size: 20px;
    color: var(--muted);
    font-weight: 300;
    line-height: 1;
}
.score-empty-label {
    font-size: 9.5px;
    color: var(--muted);
    text-align: center;
}

/* Pied de carte */
.offre-card__footer {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid var(--border);
    display: flex;
    justify-content: flex-end;
}

/* ═══ EMPTY STATE ═══ */
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
.pagination-wrapper {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}
.pagination-wrapper .pagination,
.pagination-wrapper nav {
    display: flex;
    gap: 5px;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
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
.pagination-wrapper a:hover {
    background: var(--border);
    border-color: var(--rust);
    color: var(--rust);
}
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.pagination-wrapper .disabled span {
    opacity: .45;
    cursor: not-allowed;
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
.btn-sm {
    padding: 5px 12px;
    font-size: 11.5px;
}

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1100px) {
    .offre-card__body { gap: 18px; }
    .offre-card__prix-value { font-size: 16px; }
}

@media (max-width: 900px) {
    .offre-card__body {
        grid-template-columns: 1fr auto;
        gap: 14px 18px;
    }
    .offre-card__main { grid-column: 1; grid-row: 1; }
    .offre-card__score { grid-column: 2; grid-row: 1; }
    .offre-card__prix {
        grid-column: 1 / -1;
        grid-row: 2;
        flex-direction: row;
        align-items: baseline;
        justify-content: flex-start;
        gap: 10px;
        padding-top: 10px;
        border-top: 1px dashed var(--border);
        text-align: left;
    }
}

@media (max-width: 640px) {
    .page-header__left h2 { font-size: 18px; }
    .page-header__left p  { font-size: 12.5px; }

    .filter-tab {
        padding: 6px 11px;
        font-size: 11.5px;
    }
    .filter-tab i { font-size: 10px; }

    .offre-card {
        padding: 12px 14px;
        border-radius: 10px;
    }

    .offre-card__top {
        gap: 6px;
        margin-bottom: 9px;
    }
    .offre-card__time {
        margin-left: 0;
        width: 100%;
        font-size: 11px;
    }

    .offre-card__body {
        grid-template-columns: 1fr;
        gap: 10px;
    }

    .offre-card__title { font-size: 14px; }

    .offre-card__meta {
        font-size: 11.5px;
        gap: 3px 10px;
    }

    .offre-card__client { padding-top: 8px; }

    .offre-card__prix {
        grid-column: 1;
        grid-row: auto;
        flex-direction: row;
        align-items: baseline;
        justify-content: space-between;
        gap: 8px;
        padding: 8px 0;
        border-top: 1px dashed var(--border);
        border-bottom: 1px dashed var(--border);
        text-align: left;
    }
    .offre-card__prix-value { font-size: 15px; }

    .offre-card__score {
        grid-column: 1;
        grid-row: auto;
        flex-direction: row;
        justify-content: flex-start;
        gap: 10px;
    }
    .score-ring { width: 42px; height: 42px; }
    .score-ring__value { font-size: 11px; }

    .offre-card__footer {
        margin-top: 9px;
        padding-top: 9px;
    }
    .offre-card__footer .btn {
        width: 100%;
        justify-content: center;
    }

    .empty-state { padding: 40px 16px; }
    .empty-state__icon {
        width: 56px;
        height: 56px;
        font-size: 22px;
    }
    .empty-state h3 { font-size: 15px; }

    .pagination-wrapper a,
    .pagination-wrapper span:not(.sr-only) {
        padding: 5px 9px;
        font-size: 11.5px;
        min-width: 30px;
    }
}

@media (max-width: 400px) {
    .filter-tab span:not(.filter-tab__count) { display: none; }
    .filter-tab.is-active span:not(.filter-tab__count) { display: inline; }
    .filter-tab { padding: 6px 10px; }
    .offre-card__prix-value { font-size: 14px; }
}
</style>
@endpush