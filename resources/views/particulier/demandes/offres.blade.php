@extends('layouts.dashboard')

@section('title', 'Offres reçues — DoyaImmo')
@section('page_title', 'Offres reçues')
@section('page_sub', 'Propositions pour votre besoin : ' . $demande->type_bien->label())

@section('content')
<div class="view active offres-demande">

    @php
        $isLocation  = $demande->type_operation->value === 'location';
        $budgetLabel = $isLocation ? 'F/mois' : 'F';

        $statutDemande = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
        $statutDemandeLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);

        // Compteurs (sur la page courante, comme ta version)
        $total        = $offres->total();
        $enAttente    = $offres->where('statut.value', 'en_attente')->count();
        $acceptees    = $offres->where('statut.value', 'acceptee')->count();
        $refusees     = $offres->where('statut.value', 'refusee')->count();

        // Filtres actifs
        $currentStatut = request('statut', '');

        $filtres = [
            ''           => ['label' => 'Toutes',     'count' => $total,     'icon' => 'fa-list'],
            'en_attente' => ['label' => 'En attente', 'count' => $enAttente, 'icon' => 'fa-clock'],
            'acceptee'   => ['label' => 'Acceptées',  'count' => $acceptees, 'icon' => 'fa-check-circle'],
            'refusee'    => ['label' => 'Refusées',   'count' => $refusees,  'icon' => 'fa-times-circle'],
        ];
    @endphp

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="back-action">
        <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes besoins
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         ALERTS
    ═══════════════════════════════════════════ --}}
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         RAPPEL DU BESOIN
    ═══════════════════════════════════════════ --}}
    <header class="besoin-recap besoin-recap--{{ $statutDemande }}">

        <div class="besoin-recap__main">
            <div class="besoin-recap__badges">
                <span class="besoin-status besoin-status--{{ $statutDemande }}">
                    <i class="fa-solid fa-circle"></i> {{ $statutDemandeLabel }}
                </span>
                @if($total > 0)
                    <span class="besoin-recap__count">
                        <i class="fa-solid fa-handshake"></i>
                        <strong>{{ $total }}</strong> proposition{{ $total > 1 ? 's' : '' }}
                    </span>
                @endif
            </div>

            <h1 class="besoin-recap__title">
                {{ $demande->type_bien->label() }}
                <span class="besoin-recap__title-sep">—</span>
                {{ $demande->zone_recherchee }}
            </h1>

            <div class="besoin-recap__meta">
                <span>
                    <i class="fa-solid fa-wallet"></i>
                    Budget
                    <strong>{{ number_format($demande->budget_maximum, 0, ',', ' ') }} {{ $budgetLabel }}</strong>
                </span>
                @if($demande->surface_minimum)
                    <span class="besoin-recap__sep">•</span>
                    <span>
                        <i class="fa-regular fa-square"></i>
                        Surface min <strong>{{ $demande->surface_minimum }} m²</strong>
                    </span>
                @endif
                @if($demande->nombre_chambres)
                    <span class="besoin-recap__sep">•</span>
                    <span>
                        <i class="fa-solid fa-bed"></i>
                        <strong>{{ $demande->nombre_chambres }}</strong> ch.
                    </span>
                @endif
                <span class="besoin-recap__sep">•</span>
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    Publié le {{ $demande->created_at->format('d/m/Y') }}
                </span>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════
         FILTRES AVEC COMPTEURS
    ═══════════════════════════════════════════ --}}
    @if($total > 0)
        <nav class="filter-tabs" aria-label="Filtrer par statut">
            @foreach($filtres as $key => $meta)
                <a href="{{ route('particulier.demandes.offres', [
                        'demande' => $demande->slug,
                        'statut' => $key ?: null,
                    ]) }}"
                   class="filter-tab {{ $currentStatut === $key ? 'is-active' : '' }}">
                    <i class="fa-solid {{ $meta['icon'] }}"></i>
                    <span>{{ $meta['label'] }}</span>
                    <span class="filter-tab__count">{{ $meta['count'] }}</span>
                </a>
            @endforeach
        </nav>
    @endif

    {{-- ═══════════════════════════════════════════
         LISTE DES OFFRES
    ═══════════════════════════════════════════ --}}
    @if($offres->count() > 0)

        <div class="offres-list">
            @foreach($offres as $offre)

                @php
                    $statutValue = is_object($offre->statut) ? $offre->statut->value : $offre->statut;
                    $statutLabel = is_object($offre->statut) ? $offre->statut->label() : ucfirst($offre->statut);

                    $agence = $offre->agence;
                    $bien   = $offre->bien;
                    $initial = strtoupper(mb_substr($agence->nom_agence ?? 'A', 0, 1));

                    $score = (int) ($offre->score_matching ?? 0);
                    $scoreClass = $score >= 80 ? 'excellent' : ($score >= 60 ? 'moyen' : 'faible');
                    $scoreLabel = $score >= 80 ? 'Excellent match' : ($score >= 60 ? 'Bon match' : 'Match faible');

                    $isVedette = $bien && $bien->est_vedette;

                    // Miniature
                    $cover = $bien && $bien->medias->where('type_media', 'image')->first()
                        ? asset('storage/' . $bien->medias->where('type_media', 'image')->first()->fichier)
                        : null;

                    $equipementsBien = $bien->equipements ?? [];
                @endphp

                <article class="offre-card offre-card--{{ $statutValue }} {{ $isVedette ? 'is-vedette' : '' }}">

                    {{-- Ruban vedette --}}
                    @if($isVedette)
                        <div class="vedette-ribbon">
                            <i class="fa-solid fa-star"></i> Bien en vedette
                        </div>
                    @endif

                    {{-- En-tête : score + statut --}}
                    <header class="offre-card__top">

                        @if($score)
                            <div class="score-badge score-badge--{{ $scoreClass }}">
                                <i class="fa-solid fa-bullseye"></i>
                                <strong>{{ $score }}%</strong>
                                <span>{{ $scoreLabel }}</span>
                            </div>
                        @endif

                        <span class="offre-status offre-status--{{ $statutValue }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ $statutLabel }}
                        </span>

                        <time class="offre-card__time"
                              datetime="{{ $offre->created_at->toIso8601String() }}"
                              title="{{ $offre->created_at->format('d/m/Y à H:i') }}">
                            <i class="fa-regular fa-clock"></i>
                            {{ $offre->created_at->diffForHumans() }}
                        </time>
                    </header>

                    {{-- Corps : photo + infos --}}
                    <div class="offre-card__body">

                        {{-- Photo --}}
                        <div class="offre-card__media">
                            @if($cover)
                                <img src="{{ $cover }}" alt="{{ $bien->titre ?? 'Bien' }}" loading="lazy">
                            @else
                                <div class="offre-card__media-placeholder">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            @endif
                        </div>

                        {{-- Contenu --}}
                        <div class="offre-card__content">

                            {{-- Prix + agence --}}
                            <div class="offre-card__headline">
                                <div class="offre-card__price">
                                    {{ number_format($offre->prix_propose, 0, ',', ' ') }}
                                    <small>FCFA</small>
                                </div>

                                <div class="offre-card__agency">
                                    <div class="agency-avatar">{{ $initial }}</div>
                                    <div class="agency-info">
                                        <strong>{{ $agence->nom_agence ?? 'Agence' }}</strong>
                                        <span class="agency-rating">
                                            <i class="fa-solid fa-star"></i>
                                            {{ number_format($agence->note_moyenne ?? 0, 1) }}
                                            <span class="agency-reviews">
                                                ({{ $agence->evaluations->count() ?? 0 }} avis)
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Titre bien --}}
                            <h3 class="offre-card__title">
                                {{ $bien->titre ?? 'Bien sans titre' }}
                                @if($bien && $bien->adresse)
                                    <span class="offre-card__title-loc">
                                        — {{ $bien->adresse }}
                                    </span>
                                @endif
                            </h3>

                            {{-- Specs --}}
                            <div class="offre-card__specs">
                                @if($bien && $bien->surface)
                                    <span class="spec-pill"><i class="fa-solid fa-vector-square"></i> {{ $bien->surface }} m²</span>
                                @endif
                                @if($bien && $bien->nombre_chambres)
                                    <span class="spec-pill"><i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                                @endif
                                @if($bien && $bien->nombre_salles_bain)
                                    <span class="spec-pill"><i class="fa-solid fa-bath"></i> {{ $bien->nombre_salles_bain }} sdb</span>
                                @endif
                                @if($bien && $bien->parking_disponible)
                                    <span class="spec-pill"><i class="fa-solid fa-car"></i> Parking</span>
                                @endif
                                @if(count($equipementsBien) > 3)
                                    <span class="spec-pill spec-pill--info">
                                        +{{ count($equipementsBien) - 3 }} équipements
                                    </span>
                                @endif
                            </div>

                            {{-- Message --}}
                            @if($offre->message)
                                <div class="offre-card__message">
                                    <i class="fa-regular fa-message"></i>
                                    <p>{{ Str::limit($offre->message, 130) }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <footer class="offre-card__actions">

                        @if($statutValue === 'en_attente')
                            <form action="{{ route('particulier.propositions.selectionner', $offre->id) }}"
                                  method="POST" class="inline-form"
                                  onsubmit="return confirm('✅ Sélectionner cette offre ? Cela clôturera les autres offres en attente.')">
                                @csrf
                                <button type="submit" class="btn btn-rust btn-sm">
                                    <i class="fa-solid fa-check"></i> Choisir cette offre
                                </button>
                            </form>
                        @elseif($statutValue === 'acceptee')
                            <span class="state-pill state-pill--success">
                                <i class="fa-solid fa-circle-check"></i> Offre acceptée
                            </span>
                        @elseif($statutValue === 'refusee')
                            <span class="state-pill state-pill--danger">
                                <i class="fa-solid fa-times-circle"></i> Offre refusée
                            </span>
                        @elseif($statutValue === 'terminee')
                            <span class="state-pill state-pill--info">
                                <i class="fa-solid fa-check-double"></i> Offre terminée
                            </span>
                        @endif

                        <a href="{{ route('particulier.propositions.show', $offre->id) }}"
                           class="btn btn-ghost btn-sm offre-card__details">
                            <i class="fa-solid fa-eye"></i> Voir les détails
                        </a>
                    </footer>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $offres->links() }}
        </div>

    @else

        {{-- ═══════════════════════════════════════════
             EMPTY STATE
        ═══════════════════════════════════════════ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-inbox"></i>
            </div>
            <h3>Aucune offre pour ce besoin</h3>
            <p>
                Les agences vous contacteront dès qu'elles auront un bien correspondant à votre recherche.
            </p>
            <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost">
                <i class="fa-solid fa-list"></i> Retour à mes besoins
            </a>
        </div>
    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE OFFRES D'UNE DEMANDE — Particulier
   ═══════════════════════════════════════════════════════════ */

.offres-demande {
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

.back-action { margin-bottom: 16px; }

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

/* ═══ RAPPEL DU BESOIN ═══ */
.besoin-recap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    padding: 18px 22px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 16px;
    position: relative;
    overflow: hidden;
    flex-wrap: wrap;
}
.besoin-recap::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: var(--muted);
}
.besoin-recap--en_attente::before { background: var(--c-warning); }
.besoin-recap--en_cours::before   { background: var(--c-info); }
.besoin-recap--terminee::before   { background: var(--c-success); }
.besoin-recap--annulee::before    { background: var(--c-danger); }

.besoin-recap__main { flex: 1; min-width: 0; }

.besoin-recap__badges {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.besoin-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.besoin-status i { font-size: 5px; }
.besoin-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.besoin-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.besoin-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.besoin-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }

.besoin-recap__count {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    background: var(--c-success-bg);
    color: var(--c-success);
    font-size: 11.5px;
    font-weight: 600;
}
.besoin-recap__count i { font-size: 11px; }
.besoin-recap__count strong {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 800;
}

.besoin-recap__title {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    margin: 0 0 8px;
    color: var(--text);
    line-height: 1.25;
}
.besoin-recap__title-sep {
    color: var(--muted);
    font-weight: 400;
    margin: 0 4px;
}

.besoin-recap__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13px;
    color: var(--muted);
}
.besoin-recap__meta i { font-size: 12px; margin-right: 5px; opacity: .8; }
.besoin-recap__meta strong { color: var(--text); font-weight: 600; }
.besoin-recap__sep { opacity: .4; }

/* ═══ FILTRES ONGLETS ═══ */
.filter-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: thin;
}
.filter-tabs::-webkit-scrollbar { height: 4px; }
.filter-tabs::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 999px;
}

.filter-tab {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 15px;
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
.filter-tab:hover {
    border-color: var(--rust);
    transform: translateY(-1px);
}
.filter-tab i { font-size: 12px; opacity: .85; }
.filter-tab.is-active {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(180, 83, 42, .22);
}
.filter-tab.is-active i { color: #fff; opacity: 1; }

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
    background: rgba(255, 255, 255, .25);
    color: #fff;
}

/* ═══════════════════════════════════════════
   CARTE OFFRE
   ═══════════════════════════════════════════ */
.offres-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.offre-card {
    position: relative;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
    overflow: hidden;
}
.offre-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(0, 0, 0, .07);
    border-color: #d8d8d8;
}
.offre-card.is-vedette {
    border-color: var(--c-gold);
    background: linear-gradient(135deg, #FFFDF5 0%, #fff 55%);
}

/* Ruban vedette */
.vedette-ribbon {
    position: absolute;
    top: 0;
    right: 0;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 14px;
    border-radius: 0 var(--radius) 0 10px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    box-shadow: 0 4px 10px rgba(245, 166, 35, .3);
    z-index: 2;
}
.vedette-ribbon i { font-size: 10px; }

/* En-tête */
.offre-card__top {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    padding-right: 130px;
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
.offre-card__time i { font-size: 11px; opacity: .8; }

/* Score badge */
.score-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid;
    white-space: nowrap;
}
.score-badge i { font-size: 11px; }
.score-badge strong {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 800;
}
.score-badge span {
    padding-left: 6px;
    border-left: 1px solid currentColor;
    opacity: .85;
    font-weight: 600;
}
.score-badge--excellent {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-color: #C8E6C9;
}
.score-badge--moyen {
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-color: #FFE0B2;
}
.score-badge--faible {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border-color: #FFCDD2;
}

/* Statut */
.offre-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
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

/* ═══ Corps ═══ */
.offre-card__body {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 18px;
    margin-bottom: 14px;
}

/* Photo */
.offre-card__media {
    position: relative;
    aspect-ratio: 4 / 3;
    border-radius: 10px;
    overflow: hidden;
    background: var(--surface);
    border: 1px solid var(--border);
}
.offre-card__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.offre-card__media-placeholder {
    width: 100%; height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    font-size: 32px;
    opacity: .4;
}

/* Contenu */
.offre-card__content {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* Headline : prix + agence */
.offre-card__headline {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}
.offre-card__price {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
    white-space: nowrap;
}
.offre-card__price small {
    font-size: 12px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}

.offre-card__agency {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}
.agency-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
}
.agency-info {
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 0;
}
.agency-info strong {
    font-size: 13px;
    font-weight: 700;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.agency-rating {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    color: var(--text-soft);
}
.agency-rating i { color: var(--c-gold); font-size: 11px; }
.agency-reviews { color: var(--muted); font-size: 11px; }

/* Titre bien */
.offre-card__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.3;
}
.offre-card__title-loc {
    font-weight: 400;
    color: var(--muted);
    font-size: 13px;
}

/* Specs */
.offre-card__specs {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.spec-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    background: var(--surface);
    border: 1px solid var(--border);
    font-size: 11.5px;
    color: var(--text-soft);
    font-weight: 500;
}
.spec-pill i { font-size: 10px; color: var(--rust); opacity: .8; }
.spec-pill--info {
    background: var(--c-info-bg);
    color: var(--c-info);
    border-color: #BBDEFB;
}
.spec-pill--info i { color: var(--c-info); }

/* Message */
.offre-card__message {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 12px;
    background: var(--surface);
    border-radius: 10px;
    border-left: 3px solid var(--rust);
}
.offre-card__message i {
    color: var(--rust);
    font-size: 13px;
    margin-top: 2px;
    flex-shrink: 0;
}
.offre-card__message p {
    font-size: 12.5px;
    color: var(--text-soft);
    line-height: 1.55;
    margin: 0;
    font-style: italic;
}

/* ═══ Actions ═══ */
.offre-card__actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-top: 14px;
    border-top: 1px dashed var(--border);
    flex-wrap: wrap;
}
.offre-card__details { margin-left: auto; }
.inline-form { display: inline; }

.state-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 13px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid;
}
.state-pill--success {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-color: #C8E6C9;
}
.state-pill--danger {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border-color: #FFCDD2;
}
.state-pill--info {
    background: var(--c-info-bg);
    color: var(--c-info);
    border-color: #BBDEFB;
}

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
.btn-sm { padding: 6px 12px; font-size: 12px; }

/* ═══ EMPTY STATE ═══ */
.empty-state {
    text-align: center;
    padding: 64px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 76px;
    height: 76px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--c-warning-bg);
    border-radius: 50%;
    color: var(--rust);
    font-size: 30px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
}
.empty-state p {
    color: var(--muted);
    font-size: 13.5px;
    margin: 0 auto 18px;
    max-width: 420px;
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

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 768px) {
    .besoin-recap {
        padding: 16px;
        gap: 14px;
    }
    .besoin-recap__title { font-size: 17px; }
    .besoin-recap__meta { font-size: 12.5px; }

    .offre-card__top { padding-right: 0; }
    .vedette-ribbon {
        position: static;
        margin-bottom: 10px;
        border-radius: 999px;
        align-self: flex-start;
        width: fit-content;
    }
    .offre-card__time { margin-left: 0; }

    .offre-card__body {
        grid-template-columns: 1fr;
    }
    .offre-card__media {
        aspect-ratio: 16 / 9;
    }

    .offre-card__headline {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .offre-card__actions {
        flex-direction: column;
        align-items: stretch;
    }
    .offre-card__actions .btn,
    .offre-card__actions form,
    .offre-card__actions .inline-form,
    .offre-card__actions .state-pill {
        width: 100%;
        justify-content: center;
    }
    .offre-card__details { margin-left: 0; }
}

@media (max-width: 480px) {
    .besoin-recap { padding: 14px; }
    .besoin-recap__title { font-size: 15.5px; }

    .offre-card { padding: 14px; }
    .offre-card__price { font-size: 19px; }
    .offre-card__title { font-size: 14px; }
    .spec-pill { font-size: 11px; padding: 3px 9px; }

    .filter-tab { font-size: 12px; padding: 8px 13px; }

    .btn { font-size: 12px; padding: 7px 13px; }
    .btn-sm { font-size: 11.5px; padding: 6px 11px; }
}
</style>
@endpush