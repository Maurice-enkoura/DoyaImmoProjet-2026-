@extends('layouts.dashboard-agence')

@section('title', 'Détail de la demande — DoyaImmo')
@section('page_title', 'Détail de la demande')
@section('page_sub', 'Informations complètes sur la demande du client')

@section('content')
<div class="view active demande-show">

    @php
        $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
        $statutLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);
        $client      = $demande->particulier->user ?? null;
        $initial     = strtoupper(mb_substr($client->prenom ?? 'C', 0, 1));

        $isLocation  = is_object($demande->type_operation) && $demande->type_operation->value === 'location';
        $budgetLabel = $isLocation ? 'F/mois' : 'F';

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

        $myAgenceId = Auth::user()->agence->id ?? null;
        $myProposition = $demande->propositions->firstWhere('agence_id', $myAgenceId);
    @endphp

    {{-- ═══ HERO ═══ --}}
    <header class="demande-hero demande-hero--{{ $statutValue }}">
        <div class="demande-hero__badges">
            <span class="offre-status offre-status--{{ $statutValue }}">
                <i class="fa-solid fa-circle"></i> {{ $statutLabel }}
            </span>
            @if($nbPropositions > 0)
                <span class="propositions-badge">
                    <i class="fa-regular fa-envelope"></i>
                    <strong>{{ $nbPropositions }}</strong>
                    offre{{ $nbPropositions > 1 ? 's' : '' }}
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
        </div>

        @if($demande->updated_at != $demande->created_at)
            <div class="demande-hero__updated">
                <i class="fa-regular fa-pen-to-square"></i>
                Modifiée le {{ $demande->updated_at->format('d/m/Y') }}
            </div>
        @endif

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

    {{-- ═══ CLIENT ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-user"></i> Client
    </div>

    <div class="client-card">
        <div class="client-card__avatar">{{ $initial }}</div>

        <div class="client-card__info">
            <h3>{{ $client->prenom ?? '' }} {{ $client->nom ?? '' }}</h3>

            <div class="client-card__contact">
                @if($client->email ?? false)
                    <a href="mailto:{{ $client->email }}">
                        <i class="fa-solid fa-envelope"></i> {{ $client->email }}
                    </a>
                @endif
                @if($client->telephone ?? false)
                    <a href="tel:{{ $client->telephone }}">
                        <i class="fa-solid fa-phone"></i> {{ $client->telephone }}
                    </a>
                @else
                    <span class="is-muted"><i class="fa-solid fa-phone"></i> Non renseigné</span>
                @endif
            </div>

            <div class="client-card__meta">
                <i class="fa-regular fa-clock"></i>
                Membre depuis {{ $client->created_at->translatedFormat('F Y') }}
            </div>
        </div>
    </div>

    {{-- ═══ DÉTAILS ═══ --}}
    <div class="ios-section-title">
        <i class="fa-solid fa-list-ul"></i> Détails
    </div>

    <div class="ios-list">
        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Type de bien</span>
            </div>
            <span class="ios-list__value">{{ $demande->type_bien->label() }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Opération</span>
            </div>
            <span class="ios-list__value">{{ $demande->type_operation->label() }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">Zone recherchée</span>
            </div>
            <span class="ios-list__value">{{ $demande->zone_recherchee }}</span>
        </div>

        <div class="ios-list__item ios-list__item--static">
            <div class="ios-list__content">
                <span class="ios-list__title">
                    @if($isLocation) Loyer max @else Budget @endif
                </span>
            </div>
            <span class="ios-list__value ios-list__value--rust">
                {{ number_format($demande->budget_maximum, 0, ',', ' ') }} {{ $budgetLabel }}
            </span>
        </div>

        @if($demande->surface_minimum)
            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Surface min.</span>
                </div>
                <span class="ios-list__value">{{ $demande->surface_minimum }} m²</span>
            </div>
        @endif

        @if($demande->nombre_chambres)
            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Chambres</span>
                </div>
                <span class="ios-list__value">{{ $demande->nombre_chambres }}</span>
            </div>
        @endif

        @if($demande->nombre_salles_bain)
            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Salles de bain</span>
                </div>
                <span class="ios-list__value">{{ $demande->nombre_salles_bain }}</span>
            </div>
        @endif

        @if($dateEntree)
            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Entrée souhaitée</span>
                </div>
                <span class="ios-list__value">{{ $dateEntree->format('d/m/Y') }}</span>
            </div>
        @endif

        @if($demande->meuble !== null)
            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Meublé</span>
                </div>
                <span class="ios-list__value">
                    <i class="fa-solid {{ $demande->meuble ? 'fa-circle-check is-yes' : 'fa-circle-xmark is-no' }}"></i>
                    {{ $demande->meuble ? 'Oui' : 'Non' }}
                </span>
            </div>
        @endif
    </div>

    {{-- ═══ ÉQUIPEMENTS ═══ --}}
    <div class="ios-section-title">
        <i class="fa-solid fa-cogs"></i> Équipements
        @if($equipements->count() > 0)
            <span class="ios-section-count">{{ $equipements->count() }}</span>
        @endif
    </div>

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
        <div class="ios-empty-inline">
            <i class="fa-regular fa-circle-question"></i>
            <span>Aucun équipement spécifique demandé</span>
        </div>
    @endif

    {{-- ═══ CRITÈRES ═══ --}}
    @if($demande->criteres_particuliers)
        <div class="ios-section-title">
            <i class="fa-solid fa-clipboard-list"></i> Critères particuliers
        </div>
        <blockquote class="quote-block quote-block--info">
            {{ $demande->criteres_particuliers }}
        </blockquote>
    @endif

    {{-- ═══ DESCRIPTION ═══ --}}
    @if($demande->description)
        <div class="ios-section-title">
            <i class="fa-solid fa-align-left"></i> Description du client
        </div>
        <blockquote class="quote-block quote-block--rust">
            {{ $demande->description }}
        </blockquote>
    @endif

    {{-- ═══ OFFRES ENVOYÉES ═══ --}}
    @if($nbPropositions > 0)
        <div class="ios-section-title">
            <i class="fa-solid fa-handshake"></i> Offres déjà envoyées
            <span class="ios-section-count">{{ $nbPropositions }}</span>
        </div>

        <div class="ios-list">
            @foreach($demande->propositions as $proposition)
                @php
                    $pStatutValue = is_object($proposition->statut) ? $proposition->statut->value : $proposition->statut;
                    $pStatutLabel = is_object($proposition->statut) ? $proposition->statut->label() : ucfirst($proposition->statut);
                    $pAgence = $proposition->agence;
                    $pInitial = strtoupper(mb_substr($pAgence->nom_agence ?? 'A', 0, 1));
                    $isMyAgency = $myAgenceId && $myAgenceId === $proposition->agence_id;
                @endphp

                <div class="prop-row {{ $isMyAgency ? 'is-mine' : '' }}">
                    <div class="prop-row__avatar">{{ $pInitial }}</div>

                    <div class="prop-row__info">
                        <div class="prop-row__title">
                            {{ $pAgence->nom_agence ?? 'Agence' }}
                            @if($isMyAgency)
                                <span class="prop-row__mine-tag">Vous</span>
                            @endif
                        </div>
                        <div class="prop-row__meta">
                            <span class="prop-row__price">
                                {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                            </span>
                            @if($proposition->bien)
                                <span class="prop-row__dot">·</span>
                                <span>{{ $proposition->bien->titre ?? 'Bien' }}</span>
                            @endif
                        </div>
                    </div>

                    <span class="offre-status offre-status--{{ $pStatutValue }}">
                        {{ $pStatutLabel }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══ ACTIONS FIXES ═══ --}}
    <div class="ios-form-actions">
        <a href="{{ route('agence.demandes.index') }}" class="ios-btn ios-btn--ghost">
            <i class="fa-solid fa-list"></i>
            Toutes les demandes
        </a>

        @if($myProposition)
            <a href="{{ route('agence.propositions.show', $myProposition) }}" class="ios-btn ios-btn--primary">
                <i class="fa-solid fa-eye"></i>
                Voir mon offre
            </a>
        @else
            <a href="{{ route('agence.propositions.create', $demande->slug) }}" class="ios-btn ios-btn--primary">
                <i class="fa-solid fa-paper-plane"></i>
                Faire une offre
            </a>
        @endif
    </div>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DÉTAIL DEMANDE (Agence) — iOS NATIVE
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

/* ═══ HERO ═══ */
.demande-hero {
    padding: 20px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
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
    color: var(--ink);
    line-height: 1.2;
    letter-spacing: -.3px;
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
.demande-hero__meta strong { color: var(--ink); font-weight: 600; }
.demande-hero__sep { opacity: .4; }

.demande-hero__updated {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--c-info);
    margin-top: 6px;
    padding: 3px 10px;
    background: var(--c-info-bg);
    border-radius: 999px;
}

.demande-hero__budget {
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding-top: 14px;
    margin-top: 14px;
    border-top: 1px dashed var(--border);
}
.demande-hero__budget-label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 600;
}
.demande-hero__budget-value {
    font-family: var(--display);
    font-size: 26px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
}
.demande-hero__budget-value small {
    font-size: 13px;
    font-weight: 600;
    opacity: .7;
    margin-left: 3px;
}

/* ═══ BADGES ═══ */
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

/* ═══ CLIENT ═══ */
.client-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-left: 3px solid var(--rust);
    border-radius: var(--radius);
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
.client-card__info { flex: 1; min-width: 0; }
.client-card__info h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--ink);
}
.client-card__contact {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 16px;
    font-size: 12.5px;
    color: var(--text-soft);
}
.client-card__contact a {
    color: inherit;
    text-decoration: none;
    transition: color .15s;
}
.client-card__contact a:hover { color: var(--rust); }
.client-card__contact i {
    margin-right: 5px;
    font-size: 11px;
    color: var(--rust);
    opacity: .8;
}
.client-card__contact .is-muted { opacity: .6; font-style: italic; }
.client-card__contact .is-muted i { color: var(--muted); }

.client-card__meta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--muted);
    padding: 5px 12px;
    background: var(--surface);
    border-radius: 999px;
    margin-top: 6px;
}
.client-card__meta i { font-size: 11px; opacity: .8; }

/* ═══ LISTE iOS ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
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
    color: var(--ink);
    text-align: right;
    flex-shrink: 0;
}
.ios-list__value--rust { color: var(--rust); }
.ios-list__value .is-yes { color: var(--c-success); margin-right: 4px; }
.ios-list__value .is-no  { color: var(--c-danger);  margin-right: 4px; }

/* ═══ ÉQUIPEMENTS ═══ */
.equipements-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-bottom: 8px;
}
.equipement-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 13px;
    background: var(--c-success-bg);
    color: var(--c-success);
    border: 1px solid #C8E6C9;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
}
.equipement-pill i { font-size: 11px; }

.ios-empty-inline {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 18px;
    background: #fff;
    border: 1px dashed var(--border);
    border-radius: 12px;
    color: var(--muted);
    font-size: 13px;
    font-style: italic;
    margin-bottom: 8px;
}
.ios-empty-inline i { font-size: 15px; opacity: .6; }

/* ═══ QUOTES ═══ */
.quote-block {
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    line-height: 1.75;
    color: var(--ink);
    margin: 0 0 14px;
    border-left: 4px solid var(--muted);
    background: #fff;
    border-top: 1px solid var(--border);
    border-right: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
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
.prop-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    transition: background .15s;
}
.prop-row:last-child { border-bottom: none; }
.prop-row.is-mine {
    background: linear-gradient(135deg, #FFF9F5 0%, #fff 60%);
    border-left: 3px solid var(--rust);
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
.prop-row__info { flex: 1; min-width: 0; }
.prop-row__title {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--ink);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 2px;
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
.prop-row__meta {
    font-size: 12px;
    color: var(--muted);
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    align-items: center;
}
.prop-row__price {
    color: var(--rust);
    font-weight: 700;
}
.prop-row__dot { opacity: .5; }

/* ═══ ACTIONS ═══ */
.ios-form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    margin-top: 20px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
    flex: 1;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181,80,42,.2);
}
.ios-btn--primary:hover { background: #9A4523; transform: translateY(-1px); }
.ios-btn--primary:active { transform: scale(.97); }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
    flex: 0 0 auto;
    padding: 12px 18px;
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--ghost:active { transform: scale(.97); }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .demande-show { background: transparent; padding-top: 4px; }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-count { font-size: 11px; padding: 3px 10px; }

    /* ─── HERO iOS ─── */
    .demande-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 20px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .demande-hero::before {
        left: 0; right: 0;
        height: 3px;
        border-radius: 0;
    }

    .demande-hero__badges { gap: 6px; margin-bottom: 8px; }
    .offre-status {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
    }
    .offre-status i { font-size: 7px; }
    .propositions-badge { font-size: 12px; padding: 4px 10px; }

    .demande-hero__title {
        font-size: 22px;
        letter-spacing: -.4px;
        color: var(--ios-label);
        font-weight: 700;
        margin: 0 0 8px;
    }

    .demande-hero__meta {
        font-size: 13.5px;
        color: var(--ios-label-3);
        gap: 6px;
    }
    .demande-hero__meta i { color: var(--ios-label-3); }
    .demande-hero__meta strong { color: var(--ios-label); }
    .demande-hero__sep { display: none; }

    .demande-hero__updated {
        font-size: 12px;
        color: var(--c-info);
        background: transparent;
        padding: 0;
        margin-top: 6px;
    }

    .demande-hero__budget {
        padding-top: 14px;
        margin-top: 14px;
        border-top: 0.5px solid var(--ios-separator);
        border-radius: 0;
    }
    .demande-hero__budget-label {
        font-size: 11px;
        text-transform: none;
        letter-spacing: 0;
        color: var(--ios-label-3);
    }
    .demande-hero__budget-value {
        font-size: 26px;
        letter-spacing: -.5px;
    }

    /* ─── CLIENT iOS ─── */
    .client-card {
        background: var(--ios-card);
        border: none;
        border-left: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        position: relative;
        overflow: hidden;
    }
    .client-card::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 3px;
        background: var(--rust);
        border-radius: 0 3px 3px 0;
    }
    .client-card__avatar { width: 52px; height: 52px; font-size: 20px; }
    .client-card__info h3 { font-size: 17px; font-weight: 700; letter-spacing: -.2px; margin-bottom: 8px; }
    .client-card__contact { font-size: 13px; gap: 6px 14px; }
    .client-card__contact i { color: var(--rust); font-size: 12px; }
    .client-card__meta {
        background: #EFEFF4;
        padding: 5px 12px;
        font-size: 12.5px;
    }

    /* ─── LISTE iOS ─── */
    .ios-list {
        display: block;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
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
    }
    .ios-list__value--rust { color: var(--rust); }

    /* ─── ÉQUIPEMENTS iOS ─── */
    .equipements-grid { gap: 8px; margin-bottom: 8px; }
    .equipement-pill {
        background: var(--ios-card);
        color: var(--ios-label);
        border: none;
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .equipement-pill i { font-size: 12px; color: var(--c-success); }

    .ios-empty-inline {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 22px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        font-size: 13.5px;
        color: var(--ios-label-3);
    }

    /* ─── QUOTES iOS ─── */
    .quote-block {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid var(--rust);
        border-radius: 12px;
        padding: 16px 18px;
        font-size: 14px;
        line-height: 1.65;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .quote-block--rust {
        background: var(--ios-card);
        border-left-color: var(--rust);
    }
    .quote-block--info {
        background: var(--ios-card);
        border-left-color: var(--c-info);
        color: var(--ios-label-2);
        font-weight: 400;
    }

    /* ─── PROPOSITIONS iOS ─── */
    .prop-row {
        padding: 14px 16px;
        gap: 12px;
        border-bottom: 0.5px solid var(--ios-separator);
        flex-wrap: wrap;
    }
    .prop-row.is-mine {
        background: linear-gradient(135deg, #FFF9F5 0%, #fff 60%);
        border-left: 3px solid var(--rust);
        padding-left: 13px;
    }
    .prop-row__avatar { width: 40px; height: 40px; font-size: 16px; }
    .prop-row__title { font-size: 15px; font-weight: 600; }
    .prop-row__meta { font-size: 13px; }
    .prop-row__mine-tag {
        padding: 2px 8px;
        font-size: 9.5px;
    }
    .offre-status {
        font-size: 10.5px;
        padding: 4px 10px;
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0;
    }

    /* ─── ACTIONS FIXES ─── */
    .ios-form-actions {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(242, 242, 247, 0.95);
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        border-top: 0.5px solid var(--ios-separator);
        padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
        margin: 0;
        gap: 10px;
        z-index: 100;
        border-radius: 0;
    }
    .ios-btn {
        padding: 14px 20px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 14px;
        flex: 1;
        letter-spacing: -.1px;
    }
    .ios-btn--primary {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .ios-btn--primary:active {
        transform: scale(.97);
        box-shadow: 0 2px 6px rgba(181, 80, 42, .4);
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--ios-blue);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex: 0 0 auto;
        padding: 14px 18px;
    }
    .ios-btn--ghost:active { background: #EFEFF4; }

    /* Padding pour ne pas cacher sous la barre fixe */
    .demande-show {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }
}

@media (max-width: 480px) {
    .demande-hero { padding: 16px; }
    .demande-hero__title { font-size: 20px; }
    .demande-hero__budget-value { font-size: 24px; }

    .client-card { padding: 14px; }
    .client-card__avatar { width: 46px; height: 46px; font-size: 18px; }
    .client-card__info h3 { font-size: 16px; }

    .ios-section-title { font-size: 16px; }
}
</style>
@endpush