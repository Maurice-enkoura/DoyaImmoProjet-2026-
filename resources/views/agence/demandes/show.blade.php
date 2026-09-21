@extends('layouts.dashboard-agence')

@section('title', 'Détail de la demande — DoyaImmo')
@section('page_title', 'Détail de la demande')
@section('page_sub', 'Informations complètes sur la demande du client')

@section('content')
<div class="view active demande-show">

    @php
        $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
        $statutLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);

        // ✅ Récupération du client (nom uniquement, pas de contact affiché)
        $particulier = $demande->particulier;
        $client      = $particulier->user ?? null;

        $initial            = strtoupper(mb_substr($client->prenom ?? $particulier->prenom ?? 'C', 0, 1));
        $clientPrenom       = $client->prenom ?? $particulier->prenom ?? '';
        $clientNom          = $client->nom ?? $particulier->nom ?? '';
        $clientMembreDepuis = $client->created_at ?? $particulier->created_at ?? null;

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
    <header class="demande-hero">
        <div class="demande-hero__badges">
            <span class="offre-status offre-status--{{ $statutValue }}">
                {{ $statutLabel }}
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

    {{-- ═══ CLIENT (nom uniquement) ═══ --}}
    <div class="ios-section-title">
        <i class="fa-regular fa-user"></i> Client
    </div>

    <div class="client-card">
        <div class="client-card__avatar">{{ $initial }}</div>

        <div class="client-card__info">
            <h3>{{ $clientPrenom }} {{ $clientNom }}</h3>

            <div class="client-card__meta">
                <i class="fa-regular fa-clock"></i>
                Membre depuis {{ $clientMembreDepuis?->translatedFormat('F Y') ?? '—' }}
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
        <blockquote class="quote-block">
            {{ $demande->criteres_particuliers }}
        </blockquote>
    @endif

    {{-- ═══ DESCRIPTION ═══ --}}
    @if($demande->description)
        <div class="ios-section-title">
            <i class="fa-solid fa-align-left"></i> Description du client
        </div>
        <blockquote class="quote-block quote-block--italic">
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
   DÉTAIL DEMANDE (Agence) — Design épuré
   ═══════════════════════════════════════════════════════════ */

.demande-show {
    --c-success:    #1E7A47;
    --c-success-bg: #F1F8F4;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --c-info:       #1E40AF;
    --c-info-bg:    #EFF6FF;
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    color: var(--ink);
    margin: 22px 0 10px;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: -.2px;
}
.ios-section-title i { color: var(--muted); font-size: 14px; }
.ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 700;
    padding: 2px 9px;
    background: #F5F5F7;
    color: var(--text-soft);
    border-radius: 999px;
}

/* ═══ HERO ═══ */
.demande-hero {
    padding: 20px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
}

.demande-hero__badges {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}

.demande-hero__title {
    font-family: var(--display);
    font-size: 22px;
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
.demande-hero__meta i { font-size: 12px; margin-right: 5px; }
.demande-hero__meta strong { color: var(--ink); font-weight: 600; }
.demande-hero__sep { color: var(--border); }

.demande-hero__updated {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
}

.demande-hero__budget {
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding-top: 14px;
    margin-top: 14px;
    border-top: 1px solid var(--border);
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
    font-size: 24px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
    letter-spacing: -.4px;
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
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
    background: #F5F5F7;
    color: var(--text-soft);
}
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
    padding: 3px 10px;
    border-radius: 999px;
    background: #F5F5F7;
    color: var(--text-soft);
    font-size: 11.5px;
    font-weight: 500;
    white-space: nowrap;
}
.propositions-badge i { font-size: 11px; color: var(--muted); }
.propositions-badge strong {
    font-family: var(--display);
    font-size: 12.5px;
    font-weight: 700;
    color: var(--ink);
}

/* ═══ CLIENT ═══ */
.client-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    flex-wrap: wrap;
}
.client-card__avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 18px;
    font-weight: 700;
    flex-shrink: 0;
    border: 1px solid var(--border);
}
.client-card__info { flex: 1; min-width: 0; }
.client-card__info h3 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--ink);
    letter-spacing: -.2px;
}

.client-card__meta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--muted);
    padding: 4px 10px;
    background: #F5F5F7;
    border-radius: 999px;
}
.client-card__meta i { font-size: 11px; }

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
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink);
    text-align: right;
    flex-shrink: 0;
}
.ios-list__value--rust { color: var(--rust); }

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
    padding: 5px 12px;
    background: #F5F5F7;
    color: var(--text-soft);
    border-radius: 999px;
    font-size: 12px;
    font-weight: 500;
}
.equipement-pill i { font-size: 11px; color: var(--muted); }

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
    line-height: 1.7;
    color: var(--text-soft);
    margin: 0 0 14px;
    background: #fff;
    border: 1px solid var(--border);
    border-left: 3px solid #d8d8d8;
}
.quote-block--italic { font-style: italic; }

/* ═══ PROPOSITIONS ═══ */
.prop-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
}
.prop-row:last-child { border-bottom: none; }
.prop-row.is-mine {
    background: #FAFBFC;
}

.prop-row__avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
    border: 1px solid var(--border);
}
.prop-row__info { flex: 1; min-width: 0; }
.prop-row__title {
    font-size: 13.5px;
    font-weight: 600;
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
    background: #F5F5F7;
    color: var(--text-soft);
    border-radius: 999px;
    font-size: 10px;
    font-weight: 600;
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
    font-weight: 600;
}
.prop-row__dot { color: var(--border); }

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
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
    flex: 1;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.ios-btn--primary:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
    flex: 0 0 auto;
}
.ios-btn--ghost:hover {
    background: #F5F5F7;
    border-color: #d8d8d8;
    color: var(--ink);
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .demande-show { background: transparent; padding-top: 4px; }

    .ios-section-title {
        font-size: 15px;
        margin: 20px 4px 10px;
        color: var(--ink);
    }
    .ios-section-title i { font-size: 13px; }

    .demande-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .demande-hero__badges { gap: 6px; margin-bottom: 8px; }

    .demande-hero__title {
        font-size: 20px;
        letter-spacing: -.3px;
        margin-bottom: 8px;
    }

    .demande-hero__meta {
        font-size: 13px;
        color: var(--muted);
        gap: 6px;
    }
    .demande-hero__sep { display: none; }

    .demande-hero__updated {
        font-size: 12px;
        margin-top: 6px;
    }

    .demande-hero__budget {
        padding-top: 14px;
        margin-top: 14px;
        border-top: 0.5px solid var(--ios-separator);
    }
    .demande-hero__budget-value { font-size: 24px; }

    .client-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .client-card__avatar {
        width: 48px;
        height: 48px;
        font-size: 18px;
        background: #F5F5F7;
        border: none;
    }
    .client-card__info h3 { font-size: 15.5px; margin-bottom: 8px; }
    .client-card__meta { background: #F5F5F7; font-size: 12px; }

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
        font-size: 14px;
    }
    .ios-list__item:last-child { border-bottom: none; }
    .ios-list__title {
        font-size: 14px;
        color: var(--ink);
        font-weight: 500;
        letter-spacing: -.2px;
    }
    .ios-list__value {
        font-size: 14px;
        font-weight: 600;
        color: var(--muted);
    }
    .ios-list__value--rust { color: var(--rust); }

    .equipements-grid { gap: 8px; }
    .equipement-pill {
        background: var(--ios-card);
        color: var(--text-soft);
        padding: 7px 13px;
        font-size: 12.5px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .ios-empty-inline {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 22px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        font-size: 13px;
    }

    .quote-block {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid #d8d8d8;
        border-radius: 12px;
        padding: 16px 18px;
        font-size: 13.5px;
        line-height: 1.65;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .prop-row {
        padding: 14px 16px;
        gap: 12px;
        border-bottom: 0.5px solid var(--ios-separator);
        flex-wrap: wrap;
    }
    .prop-row.is-mine { background: #FAFBFC; }
    .prop-row__avatar { width: 38px; height: 38px; font-size: 15px; background: #F5F5F7; border: none; }
    .prop-row__title { font-size: 14px; font-weight: 600; }
    .prop-row__meta { font-size: 12.5px; }

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
        padding: 13px 20px;
        font-size: 14.5px;
        border-radius: 12px;
        flex: 1;
    }
    .ios-btn--primary {
        background: var(--rust);
        border: none;
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--text-soft);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex: 0 0 auto;
    }

    .demande-show {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }
}

@media (max-width: 480px) {
    .demande-hero { padding: 16px; }
    .demande-hero__title { font-size: 19px; }
    .demande-hero__budget-value { font-size: 22px; }

    .client-card { padding: 14px; }
    .client-card__avatar { width: 44px; height: 44px; font-size: 17px; }
    .client-card__info h3 { font-size: 15px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush