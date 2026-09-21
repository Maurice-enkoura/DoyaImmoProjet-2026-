@extends('layouts.dashboard-agence')

@section('title', 'Mes offres — DoyaImmo')
@section('page_title', 'Mes offres')
@section('page_sub', 'Suivez vos propositions envoyées')

@section('content')
<div class="view active offres-page">

    @php
        use App\Enums\StatutPropositionEnum;

        $current = request('statut', '');

        $counts = [
            ''           => $propositions->total(),
            'en_attente' => $propositions->filter(fn($p) => $p->statut === StatutPropositionEnum::EN_ATTENTE)->count(),
            'acceptee'   => $propositions->filter(fn($p) => $p->statut === StatutPropositionEnum::ACCEPTEE)->count(),
            'refusee'    => $propositions->filter(fn($p) => $p->statut === StatutPropositionEnum::REFUSEE)->count(),
            'terminee'   => $propositions->filter(fn($p) => $p->statut === StatutPropositionEnum::TERMINEE)->count(),
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

    {{-- ═══ STATS ═══ --}}
    @if($counts[''] > 0)
        <div class="offres-stats">
            <div class="offres-stat">
                <span class="offres-stat__value">{{ $counts[''] }}</span>
                <span class="offres-stat__label">Total</span>
            </div>
            <div class="offres-stat">
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

    {{-- ═══ FILTRES ═══ --}}
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

    {{-- ═══ LISTE ═══ --}}
    @if($propositions->count() > 0)

        <div class="offres-list">
            @foreach($propositions as $proposition)

                @php
                    $statutValue = is_object($proposition->statut)
                        ? $proposition->statut->value
                        : $proposition->statut;
                    $statutLabel = is_object($proposition->statut)
                        ? $proposition->statut->label()
                        : ucfirst($proposition->statut);

                    $isVedette = $proposition->bien && $proposition->bien->est_vedette;
                    $score     = (int) ($proposition->score_matching ?? 0);

                    // ✅ Nom du client (sans email affiché)
                    $prenom   = $proposition->particulier->user->prenom ?? 'C';
                    $nom      = $proposition->particulier->user->nom    ?? '';
                    $initials = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));

                    $hasRdv = $proposition->rendezVous()->exists();
                    $peutAnnuler = in_array($statutValue, ['en_attente', 'acceptee']) && !$hasRdv;

                    $estReactivee = $statutValue === 'en_attente'
                        && $proposition->updated_at->gt($proposition->created_at->addMinutes(1));
                @endphp

                <article class="offre-card offre-card--{{ $statutValue }}">

                    {{-- En-tête --}}
                    <header class="offre-card__top">
                        <span class="offre-status offre-status--{{ $statutValue }}">
                            {{ $statutLabel }}
                        </span>

                        @if($estReactivee)
                            <span class="offre-badge">
                                <i class="fa-solid fa-rotate-right"></i> Réactivée
                            </span>
                        @endif

                        @if($isVedette)
                            <span class="offre-badge">
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

                    {{-- Client (nom uniquement, sans email) --}}
                    <div class="client-block">
                        <div class="client-avatar">{{ $initials }}</div>
                        <div class="client-info">
                            <strong>{{ $prenom }} {{ $nom }}</strong>
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
                                <div class="score-ring" style="--score: {{ $score }};">
                                    <svg viewBox="0 0 36 36" aria-hidden="true">
                                        <circle class="score-ring__bg" cx="18" cy="18" r="16"/>
                                        <circle class="score-ring__fill" cx="18" cy="18" r="16"/>
                                    </svg>
                                    <span class="score-ring__value">
                                        {{ $score }}<small>%</small>
                                    </span>
                                </div>
                                <span class="score-label">
                                    {{ $score >= 80 ? 'Excellent' : ($score >= 60 ? 'Bon match' : 'Faible') }}
                                </span>
                            @else
                                <span class="score-empty">—</span>
                                <span class="score-empty-label">Non évalué</span>
                            @endif
                        </div>
                    </div>

                    {{-- Indicateur RDV planifié --}}
                    @if($hasRdv)
                        <div class="rdv-indicator">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Une visite a été planifiée pour cette offre</span>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <footer class="offre-card__footer">
                        <a href="{{ route('agence.propositions.show', $proposition) }}" class="btn btn-ghost">
                            <i class="fa-solid fa-eye"></i> Voir le détail
                        </a>

                        @if($peutAnnuler)
                            <form action="{{ route('agence.propositions.annuler', $proposition) }}"
                                  method="POST" class="inline-form">
                                @csrf
                                <button type="submit" class="btn btn-ghost-danger"
                                        onclick="return confirm('⚠️ Annuler cette proposition ?\n\n{{ $statutValue === 'acceptee' ? 'La demande du particulier repassera en attente.' : 'Cette offre sera retirée.' }}')">
                                    <i class="fa-solid fa-rotate-left"></i> Annuler l'offre
                                </button>
                            </form>
                        @endif
                    </footer>
                </article>
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $propositions->appends(request()->query())->links() }}
        </div>

    @else
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
   MES OFFRES — Design épuré
   ═══════════════════════════════════════════════════════════ */

.offres-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #F1F8F4;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --c-info:       #1E40AF;
    --c-info-bg:    #EFF6FF;
    --card-radius:  12px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
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
    font-size: 20px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.2;
    letter-spacing: -.3px;
}
.page-header__left p {
    font-size: 13px;
    color: var(--muted);
    margin: 4px 0 0;
}

/* ═══ STATS ═══ */
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
    position: relative;
}
.offres-stat__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.5px;
    line-height: 1;
}
.offres-stat__label {
    font-size: 11.5px;
    font-weight: 500;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .3px;
}

/* ═══ FILTRES ═══ */
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
    font-weight: 500;
    color: var(--text-soft);
    background: #fff;
    border: 1px solid var(--border);
    text-decoration: none;
    transition: border-color .15s, background-color .15s, color .15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.filter-tab:hover { border-color: #d8d8d8; }
.filter-tab i { font-size: 11px; color: var(--muted); }
.filter-tab.is-active {
    background: #F5F5F7;
    color: var(--ink);
    border-color: #d8d8d8;
    font-weight: 600;
}
.filter-tab.is-active i { color: var(--rust); }

.filter-tab__count {
    background: #F5F5F7;
    padding: 1px 7px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    line-height: 1.6;
    min-width: 20px;
    text-align: center;
    color: var(--text-soft);
}
.filter-tab.is-active .filter-tab__count {
    background: #fff;
    color: var(--ink);
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
    transition: border-color .2s;
}
.offre-card:hover { border-color: #d8d8d8; }

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
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
    background: #F5F5F7;
    color: var(--text-soft);
}
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.offre-status--terminee   { background: var(--c-info-bg);    color: var(--c-info); }

.offre-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 500;
    white-space: nowrap;
    background: #F5F5F7;
    color: var(--text-soft);
}
.offre-badge i { font-size: 10px; color: var(--muted); }

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
    letter-spacing: -.2px;
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
    color: var(--muted);
}

/* ✅ Bloc client — nom uniquement */
.client-block {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 10px 12px;
    background: #F5F5F7;
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
    background: #fff;
    color: var(--text);
    border: 1px solid var(--border);
    font-family: var(--display);
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
}
.client-info { min-width: 0; flex: 1; }
.client-info strong {
    display: block;
    font-size: 13px;
    color: var(--text);
    font-weight: 600;
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
    background: #F5F5F7;
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
    stroke: var(--rust);
    stroke-dasharray: calc(var(--score, 0) * 1.005) 100.5;
    transition: stroke-dasharray .6s ease;
}

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
    color: var(--ink);
}
.score-ring__value small { font-size: 8px; margin-left: 1px; font-weight: 700; color: var(--muted); }

.score-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--muted);
}

.score-empty {
    font-size: 22px;
    color: var(--muted);
    font-weight: 300;
    line-height: 1;
    min-width: 44px;
    text-align: center;
}
.score-empty-label {
    font-size: 11px;
    color: var(--muted);
}

.rdv-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #F5F5F7;
    border-radius: 10px;
    font-size: 12px;
    color: var(--text-soft);
    font-weight: 500;
    margin-bottom: 10px;
}
.rdv-indicator i { font-size: 13px; color: var(--muted); }

.offre-card__footer {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
}
.inline-form { display: inline; }

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
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
.btn-ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: #F5F5F7;
    border-color: #d8d8d8;
    color: var(--ink);
}
.btn-ghost-danger {
    background: #fff;
    color: var(--c-danger);
    border-color: var(--border);
}
.btn-ghost-danger:hover {
    background: var(--c-danger-bg);
    border-color: #f5c6c6;
}

/* ═══ EMPTY ═══ */
.empty-state {
    text-align: center;
    padding: 56px 20px;
    background: #fff;
    border-radius: var(--card-radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F5F5F7;
    border-radius: 50%;
    color: var(--muted);
    font-size: 26px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--text);
}
.empty-state p {
    color: var(--muted);
    font-size: 13px;
    margin: 0 0 16px;
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
    padding: 6px 11px;
    border-radius: 8px;
    border: 1px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    font-size: 12px;
    min-width: 34px;
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

    .offres-page { background: transparent; padding-top: 4px; }

    .page-header { display: none; }

    .offres-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 14px 6px;
        margin-bottom: 12px;
        grid-template-columns: none;
    }
    .offres-stat {
        flex: 1;
        padding: 0 4px;
        background: transparent;
        border: none;
        border-radius: 0;
        gap: 3px;
    }
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
        color: var(--muted);
        text-transform: none;
        letter-spacing: 0;
    }

    .filter-tabs {
        gap: 8px;
        margin: 0 -16px 14px;
        padding: 0 16px 2px;
        scrollbar-width: none;
    }
    .filter-tabs::-webkit-scrollbar { display: none; }

    .filter-tab {
        padding: 8px 15px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .filter-tab.is-active {
        background: var(--rust);
        color: #fff;
        box-shadow: none;
    }
    .filter-tab__count {
        background: #EFEFF4;
        color: var(--text-soft);
    }
    .filter-tab.is-active .filter-tab__count {
        background: rgba(255,255,255,.25);
        color: #fff;
    }

    .offres-list { gap: 12px; }

    .offre-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .offre-card__top { gap: 8px; margin-bottom: 12px; }
    .offre-status { font-size: 11.5px; padding: 3px 10px; }
    .offre-badge { font-size: 11px; padding: 3px 9px; }
    .offre-card__time { font-size: 11.5px; }

    .offre-card__title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .offre-card__meta {
        font-size: 13px;
        gap: 5px 12px;
        margin-bottom: 12px;
    }
    .meta-item i { font-size: 11px; }

    .client-block {
        background: #F5F5F7;
        border-radius: 12px;
        padding: 10px 12px;
        gap: 10px;
        margin-bottom: 12px;
    }
    .client-avatar { width: 36px; height: 36px; font-size: 12.5px; background: #fff; border: none; }
    .client-info strong { font-size: 14px; }

    .offre-card__data {
        background: #F5F5F7;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 12px;
    }
    .offre-card__prix-label { font-size: 11px; text-transform: none; letter-spacing: 0; }
    .offre-card__prix-value { font-size: 20px; }

    .score-ring { width: 46px; height: 46px; }
    .score-ring__value { font-size: 13px; }
    .score-ring__value small { font-size: 9px; }
    .score-label { font-size: 12px; font-weight: 600; }

    .rdv-indicator {
        background: #F5F5F7;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 13px;
        margin-bottom: 12px;
    }

    .offre-card__footer {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    .offre-card__footer .btn,
    .offre-card__footer .inline-form {
        width: 100%;
    }
    .offre-card__footer .btn {
        justify-content: center;
        padding: 12px 18px;
        font-size: 14px;
        border-radius: 12px;
    }
    .offre-card__footer .btn-ghost {
        background: #F5F5F7;
        border: none;
        color: var(--text-soft);
    }
    .offre-card__footer .btn-ghost-danger {
        background: var(--c-danger-bg);
        border: none;
    }

    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon {
        background: #F5F5F7;
        color: var(--muted);
    }
    .empty-state h3 { font-size: 16px; }
    .empty-state p { font-size: 13px; }

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