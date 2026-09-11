@extends('layouts.dashboard-agence')

@section('title', 'Détail de la demande — DoyaImmo')
@section('page_title', 'Détail de la demande')
@section('page_sub', 'Informations complètes sur la demande du client')

@section('content')
<div class="view active demande-show">

    @php
        $statutValue = $demande->statut->value;
        $statutLabel = $demande->statut->label();
        $client      = $demande->particulier->user ?? null;
        $initial     = strtoupper(mb_substr($client->prenom ?? 'C', 0, 1));

        $isLocation  = $demande->type_operation->value === 'location';
        $budgetLabel = $isLocation ? 'F/mois' : 'F';

        // Équipements souhaités
        $equipements = collect();
        if ($demande->parking)       $equipements->push(['icon' => 'fa-car',           'label' => 'Parking']);
        if ($demande->meuble)        $equipements->push(['icon' => 'fa-couch',         'label' => 'Meublé']);
        if ($demande->climatisation) $equipements->push(['icon' => 'fa-snowflake',     'label' => 'Climatisation']);
        if ($demande->balcon)        $equipements->push(['icon' => 'fa-door-open',     'label' => 'Balcon']);
        if ($demande->jardin)        $equipements->push(['icon' => 'fa-tree',          'label' => 'Jardin']);
        if ($demande->piscine)       $equipements->push(['icon' => 'fa-water',         'label' => 'Piscine']);
        if ($demande->ascenseur)     $equipements->push(['icon' => 'fa-elevator',      'label' => 'Ascenseur']);
        if ($demande->securite)      $equipements->push(['icon' => 'fa-shield-halved', 'label' => 'Sécurité 24h/24']);

        $nbPropositions = $demande->propositions->count();
        $dateEntree = $demande->date_entree_souhaitee;
    @endphp

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="demande-show__back">
        <a href="{{ route('agence.demandes.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux demandes
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <header class="demande-hero demande-hero--{{ $statutValue }}">

        <div class="demande-hero__main">
            <div class="demande-hero__badges">
                <span class="offre-status offre-status--{{ $statutValue }}">
                    <i class="fa-solid fa-circle"></i> {{ $statutLabel }}
                </span>
                @if($nbPropositions > 0)
                    <span class="propositions-badge">
                        <i class="fa-regular fa-envelope"></i>
                        <strong>{{ $nbPropositions }}</strong>
                        offre{{ $nbPropositions > 1 ? 's' : '' }} déjà reçue{{ $nbPropositions > 1 ? 's' : '' }}
                    </span>
                @endif
            </div>

            <h1 class="demande-hero__title">
                {{ $demande->type_bien->label() }}
            </h1>

            <div class="demande-hero__meta">
                <span>
                    <i class="fa-solid fa-location-dot"></i>
                    <strong>{{ $demande->zone_recherchee }}</strong>
                </span>
                <span class="demande-hero__sep">•</span>
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    Publiée {{ $demande->created_at->diffForHumans() }}
                </span>
                @if($demande->updated_at != $demande->created_at)
                    <span class="demande-hero__sep">•</span>
                    <span class="demande-hero__updated">
                        <i class="fa-regular fa-pen-to-square"></i>
                        Modifiée le {{ $demande->updated_at->format('d/m/Y') }}
                    </span>
                @endif
            </div>
        </div>

        <div class="demande-hero__budget">
            <span class="demande-hero__budget-label">
                @if($isLocation) Loyer maximum @else Budget d'achat @endif
            </span>
            <span class="demande-hero__budget-value">
                {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                <small>{{ $budgetLabel }}</small>
            </span>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════
         CLIENT
    ═══════════════════════════════════════════ --}}
    <section class="client-card">
        <div class="client-card__avatar">{{ $initial }}</div>

        <div class="client-card__info">
            <h3>{{ $client->prenom ?? '' }} {{ $client->nom ?? '' }}</h3>

            <div class="client-card__contact">
                @if($client->email ?? false)
                    <span><i class="fa-solid fa-envelope"></i> {{ $client->email }}</span>
                @endif
                @if($client->telephone ?? false)
                    <span><i class="fa-solid fa-phone"></i> {{ $client->telephone }}</span>
                @else
                    <span class="is-muted"><i class="fa-solid fa-phone"></i> Non renseigné</span>
                @endif
            </div>
        </div>

        <div class="client-card__meta">
            <i class="fa-regular fa-clock"></i>
            Membre depuis {{ $client->created_at->translatedFormat('F Y') }}
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         DÉTAILS
    ═══════════════════════════════════════════ --}}
    <section class="panel">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-solid fa-list-ul"></i>
            </span>
            <h4>Détails de la demande</h4>
        </header>

        <div class="info-grid">
            <div class="info-grid__item">
                <span class="info-grid__label">Type de bien</span>
                <span class="info-grid__value">{{ $demande->type_bien->label() }}</span>
            </div>
            <div class="info-grid__item">
                <span class="info-grid__label">Opération</span>
                <span class="info-grid__value">{{ $demande->type_operation->label() }}</span>
            </div>
            <div class="info-grid__item info-grid__item--accent">
                <span class="info-grid__label">
                    @if($isLocation) Loyer max @else Budget @endif
                </span>
                <span class="info-grid__value info-grid__value--prix">
                    {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                    <small>{{ $budgetLabel }}</small>
                </span>
            </div>
            <div class="info-grid__item">
                <span class="info-grid__label">Zone recherchée</span>
                <span class="info-grid__value">{{ $demande->zone_recherchee }}</span>
            </div>
            @if($demande->surface_minimum)
                <div class="info-grid__item">
                    <span class="info-grid__label">Surface minimum</span>
                    <span class="info-grid__value">{{ $demande->surface_minimum }} m²</span>
                </div>
            @endif
            @if($demande->nombre_chambres)
                <div class="info-grid__item">
                    <span class="info-grid__label">Chambres</span>
                    <span class="info-grid__value">{{ $demande->nombre_chambres }}</span>
                </div>
            @endif
            @if($demande->nombre_salles_bain)
                <div class="info-grid__item">
                    <span class="info-grid__label">Salles de bain</span>
                    <span class="info-grid__value">{{ $demande->nombre_salles_bain }}</span>
                </div>
            @endif
            @if($dateEntree)
                <div class="info-grid__item">
                    <span class="info-grid__label">Entrée souhaitée</span>
                    <span class="info-grid__value">{{ $dateEntree->format('d/m/Y') }}</span>
                </div>
            @endif
            @if($demande->meuble !== null)
                <div class="info-grid__item">
                    <span class="info-grid__label">Meublé</span>
                    <span class="info-grid__value">
                        <i class="fa-solid {{ $demande->meuble ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                        {{ $demande->meuble ? 'Oui' : 'Non' }}
                    </span>
                </div>
            @endif
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         ÉQUIPEMENTS SOUHAITÉS
    ═══════════════════════════════════════════ --}}
    <section class="panel">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-solid fa-cogs"></i>
            </span>
            <h4>Équipements souhaités</h4>
            @if($equipements->count() > 0)
                <span class="panel__count">{{ $equipements->count() }}</span>
            @endif
        </header>

        @if($equipements->count() > 0)
            <div class="equipements-grid">
                @foreach($equipements as $equipement)
                    <span class="equipement-pill">
                        <i class="fa-solid {{ $equipement['icon'] }}"></i>
                        {{ $equipement['label'] }}
                    </span>
                @endforeach
            </div>
        @else
            <div class="empty-inline">
                <i class="fa-regular fa-circle-question"></i>
                <span>Aucun équipement spécifique demandé par le client</span>
            </div>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════
         CRITÈRES PARTICULIERS
    ═══════════════════════════════════════════ --}}
    @if($demande->criteres_particuliers)
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-solid fa-clipboard-list"></i>
                </span>
                <h4>Critères particuliers</h4>
            </header>
            <blockquote class="quote-block quote-block--info">
                {{ $demande->criteres_particuliers }}
            </blockquote>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         DESCRIPTION
    ═══════════════════════════════════════════ --}}
    @if($demande->description)
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-solid fa-align-left"></i>
                </span>
                <h4>Description du client</h4>
            </header>
            <blockquote class="quote-block quote-block--rust">
                {{ $demande->description }}
            </blockquote>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         OFFRES DÉJÀ ENVOYÉES
    ═══════════════════════════════════════════ --}}
    @if($nbPropositions > 0)
        <section class="panel panel--propositions">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-solid fa-handshake"></i>
                </span>
                <h4>Offres déjà envoyées</h4>
                <span class="panel__count">{{ $nbPropositions }}</span>
            </header>

            <div class="props-list">
                @foreach($demande->propositions as $proposition)
                    @php
                        $pStatutValue = is_object($proposition->statut)
                            ? $proposition->statut->value
                            : $proposition->statut;
                        $pStatutLabel = is_object($proposition->statut)
                            ? $proposition->statut->label()
                            : ucfirst($proposition->statut);

                        $pAgence = $proposition->agence;
                        $pInitial = strtoupper(mb_substr($pAgence->nom_agence ?? 'A', 0, 1));

                        // Est-ce l'agence actuelle ?
                        $isMyAgency = Auth::user()->agence && Auth::user()->agence->id === $proposition->agence_id;
                    @endphp

                    <div class="prop-row {{ $isMyAgency ? 'prop-row--mine' : '' }}">
                        <div class="prop-row__avatar">{{ $pInitial }}</div>

                        <div class="prop-row__info">
                            <strong>
                                {{ $pAgence->nom_agence ?? 'Agence' }}
                                @if($isMyAgency)
                                    <span class="prop-row__mine-tag">Vous</span>
                                @endif
                            </strong>
                            <span>
                                {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                                @if($proposition->bien)
                                    · {{ $proposition->bien->titre ?? 'Bien' }}
                                @endif
                            </span>
                        </div>

                        <span class="offre-status offre-status--{{ $pStatutValue }}">
                            {{ $pStatutLabel }}
                        </span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         ACTIONS
    ═══════════════════════════════════════════ --}}
    <footer class="demande-actions">
        <a href="{{ route('agence.demandes.index') }}" class="btn btn-ghost">
            <i class="fa-solid fa-list"></i> Toutes les demandes
        </a>

        <a href="{{ route('agence.propositions.create', $demande->slug) }}"
           class="btn btn-rust demande-actions__cta">
            <i class="fa-solid fa-paper-plane"></i> Faire une offre
        </a>
    </footer>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL DEMANDE — Agence
   ═══════════════════════════════════════════════════════════ */

.demande-show {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --surface:      #F7F9FC;
    --radius:       14px;
}

.demande-show__back { margin-bottom: 16px; }

/* ═══ HERO ═══ */
.demande-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 24px;
    padding: 22px 26px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    flex-wrap: wrap;
    position: relative;
    overflow: hidden;
}
.demande-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--muted);
}
.demande-hero--en_attente::before { background: var(--c-warning); }
.demande-hero--en_cours::before   { background: var(--c-info); }
.demande-hero--terminee::before   { background: var(--c-success); }
.demande-hero--annulee::before    { background: var(--c-danger); }

.demande-hero__main { flex: 1; min-width: 0; }

.demande-hero__badges {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.demande-hero__title {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 10px;
    color: var(--text);
    line-height: 1.2;
}

.demande-hero__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13px;
    color: var(--muted);
}
.demande-hero__meta i { font-size: 12px; margin-right: 5px; opacity: .8; }
.demande-hero__meta strong { color: var(--text); font-weight: 600; }
.demande-hero__sep { opacity: .4; }
.demande-hero__updated { color: var(--c-info); }

.demande-hero__budget {
    text-align: right;
    flex-shrink: 0;
    padding-left: 24px;
    border-left: 1px dashed var(--border);
}
.demande-hero__budget-label {
    display: block;
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
    margin-bottom: 4px;
}
.demande-hero__budget-value {
    font-family: var(--display);
    font-size: 26px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
    white-space: nowrap;
}
.demande-hero__budget-value small {
    font-size: 13px;
    font-weight: 600;
    opacity: .7;
    margin-left: 3px;
}

/* ═══ STATUTS ═══ */
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
.offre-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.offre-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.offre-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }

.propositions-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    background: var(--c-success-bg);
    color: var(--c-success);
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
}
.propositions-badge i { font-size: 11px; }
.propositions-badge strong {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 800;
}

/* ═══ CARTE CLIENT ═══ */
.client-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-left: 3px solid var(--rust);
    border-radius: var(--radius);
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.client-card__avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    flex-shrink: 0;
}
.client-card__info { flex: 1; min-width: 160px; }
.client-card__info h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--text);
}
.client-card__contact {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 16px;
    font-size: 12.5px;
    color: var(--muted);
}
.client-card__contact i {
    margin-right: 5px;
    font-size: 11px;
    opacity: .8;
}
.client-card__contact .is-muted { opacity: .6; font-style: italic; }
.client-card__meta {
    font-size: 12px;
    color: var(--muted);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: var(--surface);
    border-radius: 999px;
    flex-shrink: 0;
}
.client-card__meta i { font-size: 11px; opacity: .8; }

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
.panel__count {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: var(--c-warning-bg);
    color: var(--rust);
    border-radius: 999px;
}

/* ═══ GRILLE INFOS ═══ */
.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.info-grid__item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 10px 14px;
    background: var(--surface);
    border-radius: 8px;
    border: 1px solid transparent;
    transition: border-color .2s;
}
.info-grid__item:hover { border-color: var(--border); }
.info-grid__item--accent {
    background: var(--c-warning-bg);
    border-color: #FFE0B2;
}
.info-grid__label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}
.info-grid__value {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text);
}
.info-grid__value--prix {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 800;
    color: var(--rust);
}
.info-grid__value--prix small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}
.info-grid__value .is-yes { color: var(--c-success); margin-right: 4px; }
.info-grid__value .is-no  { color: var(--c-danger);  margin-right: 4px; }

/* ═══ ÉQUIPEMENTS ═══ */
.equipements-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.equipement-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 13px;
    background: var(--c-success-bg);
    color: var(--c-success);
    border: 1px solid #C8E6C9;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}
.equipement-pill i { font-size: 11px; }

.empty-inline {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px;
    background: var(--surface);
    border: 1px dashed var(--border);
    border-radius: 10px;
    color: var(--muted);
    font-size: 13px;
    font-style: italic;
}
.empty-inline i { font-size: 15px; opacity: .6; }

/* ═══ CITATIONS ═══ */
.quote-block {
    position: relative;
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    line-height: 1.75;
    color: var(--text);
    margin: 0;
    border-left: 4px solid var(--muted);
    background: var(--surface);
}
.quote-block--rust {
    background: linear-gradient(135deg, rgba(180, 83, 42, .06), rgba(180, 83, 42, .02));
    border-left-color: var(--rust);
    font-style: italic;
}
.quote-block--info {
    background: var(--c-info-bg);
    border-left-color: var(--c-info);
    color: var(--c-info);
    font-weight: 500;
}

/* ═══ PROPOSITIONS ═══ */
.props-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.prop-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    transition: background .15s;
}
.prop-row--mine {
    background: var(--c-warning-bg);
    border-color: #FFE0B2;
}

.prop-row__avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    flex-shrink: 0;
}
.prop-row__info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.prop-row__info strong {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.prop-row__info span {
    font-size: 12px;
    color: var(--muted);
}
.prop-row__mine-tag {
    display: inline-block;
    padding: 1px 8px;
    background: var(--rust);
    color: #fff;
    border-radius: 999px;
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
}

/* ═══ ACTIONS ═══ */
.demande-actions {
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
.demande-actions__cta { margin-left: auto; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 13px;
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
.btn-sm { padding: 7px 14px; font-size: 12px; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .info-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .demande-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 18px;
        gap: 14px;
    }
    .demande-hero__title { font-size: 20px; }
    .demande-hero__budget {
        width: 100%;
        text-align: left;
        padding-left: 0;
        padding-top: 12px;
        border-left: none;
        border-top: 1px dashed var(--border);
    }
    .demande-hero__budget-value { font-size: 22px; }

    .client-card {
        padding: 14px 16px;
        gap: 12px;
    }
    .client-card__meta {
        width: 100%;
        justify-content: center;
    }

    .panel { padding: 16px; }

    .info-grid { grid-template-columns: 1fr; }

    .prop-row { flex-wrap: wrap; gap: 10px; }

    .demande-actions { flex-direction: column; align-items: stretch; }
    .demande-actions .btn { width: 100%; justify-content: center; }
    .demande-actions__cta { margin-left: 0; }
}

@media (max-width: 480px) {
    .demande-hero { padding: 14px; }
    .demande-hero__title { font-size: 18px; }
    .demande-hero__budget-value { font-size: 20px; }

    .client-card { padding: 12px 14px; }
    .client-card__avatar { width: 42px; height: 42px; font-size: 17px; }
    .client-card__info h3 { font-size: 14.5px; }

    .panel { padding: 14px; }
    .panel__head h4 { font-size: 14px; }

    .quote-block { padding: 12px 14px; font-size: 13px; }

    .prop-row { padding: 10px 12px; }
    .prop-row__avatar { width: 34px; height: 34px; font-size: 13px; }
    .prop-row__info strong { font-size: 12.5px; }

    .btn { font-size: 12px; padding: 8px 14px; }
}
</style>
@endpush