@extends('layouts.dashboard')

@section('title', 'Détail de ma demande — DoyaImmo')
@section('page_title', 'Détail de ma demande')
@section('page_sub', 'Informations complètes sur votre demande de logement')

@section('content')
<div class="view active demande-show">

    @php
        $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
        $statutLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);

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

        $totalPropositions = $demande->propositions->count();
    @endphp

    {{-- ═══ HERO ═══ --}}
    <header class="demande-hero">
        <div class="demande-hero__main">
            <div class="demande-hero__badges">
                <span class="offre-status offre-status--{{ $statutValue }}">
                    {{ $statutLabel }}
                </span>
                @if($totalPropositions > 0)
                    <span class="propositions-badge">
                        <strong>{{ $totalPropositions }}</strong>
                        proposition{{ $totalPropositions > 1 ? 's' : '' }}
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
                    {{ $demande->created_at->format('d/m/Y') }}
                </span>
                <span class="demande-hero__sep">•</span>
                <span>
                    <i class="fa-regular fa-clock"></i>
                    {{ $demande->created_at->diffForHumans() }}
                </span>
            </div>

            @if($demande->updated_at != $demande->created_at)
                <div class="demande-hero__updated">
                    Modifiée le {{ $demande->updated_at->format('d/m/Y à H:i') }}
                </div>
            @endif
        </div>

        <div class="demande-hero__budget">
            <span class="demande-hero__budget-label">Budget maximum</span>
            <span class="demande-hero__budget-value">
                {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                <small>{{ $budgetLabel }}</small>
            </span>
        </div>
    </header>

    {{-- ═══ STATS INLINE (mobile) ═══ --}}
    <div class="demande-stats">
        <div class="demande-stat">
            <span class="demande-stat__value">{{ $demande->nombre_chambres ?: '—' }}</span>
            <span class="demande-stat__label">Chambres</span>
        </div>
        <div class="demande-stat">
            <span class="demande-stat__value">{{ $demande->nombre_salles_bain ?: '—' }}</span>
            <span class="demande-stat__label">Salles d'eau</span>
        </div>
        <div class="demande-stat">
            <span class="demande-stat__value">{{ $demande->surface_minimum ? $demande->surface_minimum . 'm²' : '—' }}</span>
            <span class="demande-stat__label">Surface</span>
        </div>
        <div class="demande-stat">
            <span class="demande-stat__value">{{ $totalPropositions }}</span>
            <span class="demande-stat__label">Offres</span>
        </div>
    </div>

    {{-- ═══ DÉTAILS ═══ --}}
    <div class="ios-section-title">
        <i class="fa-solid fa-list-ul"></i> Détails
    </div>

    <div class="ios-list">
        <div class="ios-list__item">
            <span class="ios-list__title">Type de bien</span>
            <span class="ios-list__value">{{ $demande->type_bien->label() }}</span>
        </div>

        <div class="ios-list__item">
            <span class="ios-list__title">Opération</span>
            <span class="ios-list__value">{{ $demande->type_operation->label() }}</span>
        </div>

        @if($demande->nombre_chambres)
            <div class="ios-list__item">
                <span class="ios-list__title">Chambres</span>
                <span class="ios-list__value">{{ $demande->nombre_chambres }}</span>
            </div>
        @endif

        @if($demande->nombre_salles_bain)
            <div class="ios-list__item">
                <span class="ios-list__title">Salles de bain</span>
                <span class="ios-list__value">{{ $demande->nombre_salles_bain }}</span>
            </div>
        @endif

        @if($demande->surface_minimum)
            <div class="ios-list__item">
                <span class="ios-list__title">Surface minimum</span>
                <span class="ios-list__value">{{ $demande->surface_minimum }} m²</span>
            </div>
        @endif

        @if($demande->date_entree_souhaitee)
            <div class="ios-list__item">
                <span class="ios-list__title">Entrée souhaitée</span>
                <span class="ios-list__value">{{ $demande->date_entree_souhaitee->format('d/m/Y') }}</span>
            </div>
        @endif

        @if($demande->meuble !== null)
            <div class="ios-list__item">
                <span class="ios-list__title">Meublé</span>
                <span class="ios-list__value">
                    {{ $demande->meuble ? 'Oui' : 'Non' }}
                </span>
            </div>
        @endif
    </div>

    {{-- ═══ ÉQUIPEMENTS ═══ --}}
    <div class="ios-section-title">
        <i class="fa-solid fa-cogs"></i> Équipements souhaités
        @if($equipements->count() > 0)
            <span class="ios-section-count">{{ $equipements->count() }}</span>
        @endif
    </div>

    @if($equipements->count() > 0)
        <div class="equipements-grid">
            @foreach($equipements as $equipement)
                <span class="equipement-pill">
                    {{ $equipement['label'] }}
                </span>
            @endforeach
        </div>
    @else
        <div class="ios-empty-inline">
            <span>Aucun équipement spécifique demandé</span>
        </div>
    @endif

    {{-- ═══ CRITÈRES PARTICULIERS ═══ --}}
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
            <i class="fa-solid fa-align-left"></i> Description
        </div>
        <blockquote class="quote-block">
            {{ $demande->description }}
        </blockquote>
    @endif

    {{-- ═══ PROPOSITIONS ═══ --}}
    <div class="ios-section-title">
        <i class="fa-solid fa-handshake"></i> Propositions reçues
        @if($totalPropositions > 0)
            <span class="ios-section-count">{{ $totalPropositions }}</span>
        @endif
    </div>

    @if($totalPropositions > 0)
        <div class="props-list">
            @foreach($demande->propositions as $proposition)
                @php
                    $pStatut = is_object($proposition->statut) ? $proposition->statut->value : $proposition->statut;
                    $pStatutLabel = is_object($proposition->statut) ? $proposition->statut->label() : ucfirst($proposition->statut);

                    $score = (int) ($proposition->score_matching ?? 0);
                    $scoreClass = $score >= 80 ? 'excellent' : ($score >= 60 ? 'moyen' : 'faible');

                    $agence = $proposition->agence;
                    $bien   = $proposition->bien;
                    $initial = strtoupper(mb_substr($agence->nom_agence ?? 'A', 0, 1));
                @endphp

                <article class="prop-card">
                    <header class="prop-card__top">
                        <span class="prop-status prop-status--{{ $pStatut }}">
                            {{ $pStatutLabel }}
                        </span>

                        @if($score)
                            <span class="prop-score prop-score--{{ $scoreClass }}">
                                Match {{ $score }}%
                            </span>
                        @endif
                    </header>

                    <div class="prop-card__body">
                        <div class="prop-card__agency">
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

                        <div class="prop-card__price">
                            {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                            <small>FCFA</small>
                        </div>
                    </div>

                    @if($bien)
                        <div class="prop-card__bien">
                            <span class="bien-tag">
                                {{ $bien->titre ?? 'Bien' }}
                            </span>
                            @if($bien->surface)
                                <span class="bien-spec">{{ $bien->surface }} m²</span>
                            @endif
                            @if($bien->nombre_chambres)
                                <span class="bien-spec">{{ $bien->nombre_chambres }} ch.</span>
                            @endif
                        </div>
                    @endif

                    @if($proposition->message)
                        <div class="prop-card__message">
                            <p>{{ Str::limit($proposition->message, 100) }}</p>
                        </div>
                    @endif

                    <footer class="prop-card__footer">
                        <a href="{{ route('particulier.propositions.show', $proposition) }}"
                           class="btn btn-rust btn-sm">
                            Voir cette offre
                        </a>
                    </footer>
                </article>
            @endforeach
        </div>

        @if($totalPropositions > 3)
            <div class="panel__more">
                <a href="{{ route('particulier.propositions.index', ['demande' => $demande->slug]) }}"
                   class="btn btn-ghost">
                    Voir toutes les propositions
                </a>
            </div>
        @endif

    @else
        <div class="empty-props">
            <div class="empty-props__icon">
                <i class="fa-regular fa-inbox"></i>
            </div>
            <h5>Aucune proposition pour le moment</h5>
            <p>Les agences vous contacteront dès qu'elles auront un bien correspondant à votre recherche.</p>
        </div>
    @endif

    {{-- ═══ ACTIONS ═══ --}}
    <footer class="demande-actions">
        <div class="demande-actions__left">
            @if($statutValue === 'en_attente')
                <a href="{{ route('particulier.demandes.edit', $demande->slug) }}" class="btn btn-ghost">
                    Modifier
                </a>
                <form action="{{ route('particulier.demandes.destroy', $demande->slug) }}"
                      method="POST" class="inline-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                        Annuler
                    </button>
                </form>

            @elseif($statutValue === 'en_cours')
                <span class="state-pill state-pill--info">
                    Demande en cours
                </span>

            @elseif($statutValue === 'terminee')
                <span class="state-pill state-pill--success">
                    Demande terminée
                </span>

            @elseif($statutValue === 'annulee')
                <span class="state-pill state-pill--danger">
                    Demande annulée
                </span>
            @endif
        </div>

        <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost demande-actions__right">
            Toutes mes demandes
        </a>
    </footer>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DÉTAIL DEMANDE — Design épuré
   ═══════════════════════════════════════════════════════════ */

.demande-show {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #F1F8F4;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --c-info:       #1E40AF;
    --c-info-bg:    #EFF6FF;
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
}
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
.demande-hero__meta i { font-size: 12px; margin-right: 5px; }
.demande-hero__meta strong { color: var(--text); font-weight: 600; }
.demande-hero__sep { opacity: .4; }
.demande-hero__updated {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
    align-self: flex-start;
}

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

/* ═══ STATS INLINE (mobile) ═══ */
.demande-stats { display: none; }

/* ═══ STATUTS ═══ */
.offre-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    background: #F5F5F7;
    color: #4A5260;
}
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.offre-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }

.propositions-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 999px;
    background: #F5F5F7;
    color: var(--text-soft);
    font-size: 11.5px;
    font-weight: 500;
    white-space: nowrap;
}
.propositions-badge strong {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    color: var(--text);
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
.ios-section-title i { color: var(--muted); font-size: 15px; }
.ios-section-count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 600;
    padding: 3px 10px;
    background: #F5F5F7;
    color: var(--muted);
    border-radius: 999px;
}

/* ═══ LISTE iOS (desktop : grille) ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    margin-bottom: 14px;
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
.ios-list__title {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 14px;
    font-weight: 600;
    color: var(--text);
    text-align: right;
    flex-shrink: 0;
}

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
    padding: 7px 14px;
    background: #F5F5F7;
    color: var(--text);
    border-radius: 999px;
    font-size: 12.5px;
    font-weight: 500;
}

.ios-empty-inline {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 18px;
    background: #F9FAFB;
    border: 1px dashed var(--border);
    border-radius: 12px;
    color: var(--muted);
    font-size: 13px;
    margin-bottom: 14px;
}

/* ═══ QUOTES ═══ */
.quote-block {
    padding: 14px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    line-height: 1.75;
    color: var(--text);
    margin: 0 0 14px;
    border-left: 3px solid var(--rust);
    background: #F9FAFB;
}

/* ═══ PROPOSITIONS ═══ */
.props-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 14px;
}

.prop-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px 16px;
    transition: border-color .15s;
}
.prop-card:hover {
    border-color: #d8d8d8;
}

.prop-card__top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.prop-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    background: #F5F5F7;
    color: #4A5260;
}
.prop-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.prop-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.prop-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.prop-status--terminee   { background: var(--c-info-bg);    color: var(--c-info); }

.prop-score {
    display: inline-flex;
    align-items: center;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
    background: #F5F5F7;
    color: var(--text-soft);
}
.prop-score--excellent { background: var(--c-success-bg); color: var(--c-success); }
.prop-score--moyen     { background: var(--c-warning-bg); color: var(--c-warning); }
.prop-score--faible    { background: var(--c-danger-bg);  color: var(--c-danger); }

.prop-card__body {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 10px;
    flex-wrap: wrap;
}
.prop-card__agency {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    flex: 1;
}
.agency-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    border: 1px solid var(--border);
    display: flex; align-items: center; justify-content: center;
    font-family: var(--display);
    font-size: 16px; font-weight: 700;
    flex-shrink: 0;
}
.agency-info { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.agency-info strong {
    font-size: 14px; font-weight: 600;
    color: var(--text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.agency-rating {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 12px; color: var(--text-soft);
}
.agency-rating i { color: #F5A623; font-size: 11px; }
.agency-reviews { color: var(--muted); font-size: 11px; }

.prop-card__price {
    font-family: var(--display);
    font-size: 18px; font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
    white-space: nowrap;
    flex-shrink: 0;
}
.prop-card__price small { font-size: 11px; font-weight: 600; opacity: .7; margin-left: 2px; }

.prop-card__bien {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px 0;
    border-top: 1px solid var(--border);
}
.bien-tag {
    display: inline-flex; align-items: center;
    padding: 3px 10px;
    background: #F5F5F7;
    border-radius: 999px;
    font-size: 11.5px; font-weight: 600;
    color: var(--text);
}
.bien-spec {
    display: inline-flex; align-items: center;
    padding: 3px 10px;
    background: #F5F5F7;
    border-radius: 999px;
    font-size: 11.5px;
    color: var(--text-soft);
}

.prop-card__message {
    padding: 9px 12px;
    background: #F9FAFB;
    border-radius: 9px;
    border-left: 3px solid var(--rust);
    margin-top: 8px;
}
.prop-card__message p {
    font-size: 12px;
    color: var(--text-soft);
    line-height: 1.55;
    margin: 0;
    font-style: italic;
}

.prop-card__footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 10px;
}

.panel__more { text-align: center; margin-bottom: 14px; }

.empty-props {
    text-align: center;
    padding: 40px 20px;
    background: #F9FAFB;
    border-radius: 12px;
    border: 1px dashed var(--border);
    margin-bottom: 14px;
}
.empty-props__icon {
    width: 60px; height: 60px;
    margin: 0 auto 12px;
    display: flex; align-items: center; justify-content: center;
    background: #F5F5F7;
    border-radius: 50%;
    color: var(--muted);
    font-size: 22px;
}
.empty-props h5 {
    font-family: var(--display);
    font-size: 15px; font-weight: 700;
    margin: 0 0 5px;
    color: var(--text);
}
.empty-props p {
    font-size: 12.5px;
    color: var(--muted);
    margin: 0;
    max-width: 360px;
    margin-inline: auto;
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
    margin-top: 8px;
}
.demande-actions__left { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
.demande-actions__right { margin-left: auto; }
.inline-form { display: inline; }

.state-pill {
    display: inline-flex;
    align-items: center;
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    background: #F5F5F7;
    color: var(--text-soft);
}
.state-pill--info { background: var(--c-info-bg); color: var(--c-info); }
.state-pill--success { background: var(--c-success-bg); color: var(--c-success); }
.state-pill--danger { background: var(--c-danger-bg); color: var(--c-danger); }

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
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; border-color: #9A4523; color: #fff; }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: #F5F5F7; color: var(--text); }
.btn-danger-soft { background: transparent; color: var(--c-danger); border-color: var(--border); }
.btn-danger-soft:hover { background: var(--c-danger-bg); border-color: #FECACA; }
.btn-sm { padding: 6px 13px; font-size: 12px; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .demande-show { background: transparent; padding-top: 4px; }

    /* HERO iOS */
    .demande-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 20px;
        margin-bottom: 12px;
        flex-direction: column;
        align-items: stretch;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .demande-hero__title {
        font-size: 22px;
        letter-spacing: -.4px;
    }
    .demande-hero__badges { gap: 6px; margin-bottom: 8px; }
    .offre-status {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
    }
    .propositions-badge { font-size: 12px; padding: 4px 10px; }

    .demande-hero__meta {
        font-size: 13.5px;
        gap: 6px;
    }
    .demande-hero__sep { display: none; }

    .demande-hero__budget {
        width: 100%;
        text-align: left;
        padding-left: 0;
        padding-top: 14px;
        border-left: none;
        border-top: 0.5px solid var(--ios-separator);
    }
    .demande-hero__budget-label {
        font-size: 11px;
        text-transform: none;
        letter-spacing: 0;
        margin-bottom: 4px;
    }
    .demande-hero__budget-value {
        font-size: 26px;
        letter-spacing: -.5px;
    }

    /* STATS INLINE */
    .demande-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .demande-stat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        position: relative;
        padding: 0 4px;
        min-width: 0;
    }
    .demande-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%;
        width: 0.5px;
        background: var(--ios-separator);
    }
    .demande-stat__value {
        font-family: var(--display);
        font-size: 20px;
        font-weight: 800;
        color: var(--ios-label);
        letter-spacing: -.4px;
        line-height: 1;
        white-space: nowrap;
    }
    .demande-stat__label {
        font-size: 10.5px;
        font-weight: 500;
        color: var(--ios-label-3);
        margin-top: 2px;
        white-space: nowrap;
    }

    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; }
    .ios-section-count { font-size: 11px; padding: 3px 10px; }

    /* LISTE iOS */
    .ios-list {
        display: block;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        overflow: hidden;
        margin-bottom: 12px;
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
    }
    .ios-list__value {
        font-size: 15px;
        font-weight: 500;
        color: var(--ios-label-3);
    }

    /* ÉQUIPEMENTS */
    .equipements-grid { gap: 8px; margin-bottom: 8px; }
    .equipement-pill {
        padding: 8px 14px;
        font-size: 13px;
        font-weight: 500;
        background: var(--ios-card);
        color: var(--ios-label);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .ios-empty-inline {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 22px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        font-size: 13.5px;
        margin-bottom: 12px;
    }

    /* QUOTES */
    .quote-block {
        background: var(--ios-card);
        border: none;
        border-left: 3px solid var(--rust);
        border-radius: 12px;
        padding: 16px 18px;
        font-size: 14px;
        line-height: 1.65;
        color: var(--ios-label-2);
        margin: 0 0 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    /* PROPOSITIONS */
    .props-list { gap: 10px; }

    .prop-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .prop-card:hover { border: none; }

    .prop-card__top {
        gap: 8px;
        margin-bottom: 12px;
    }
    .prop-status {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
    }
    .prop-score { font-size: 11.5px; padding: 4px 10px; }

    .prop-card__body {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
        margin-bottom: 10px;
    }
    .agency-avatar {
        width: 38px; height: 38px; font-size: 15px;
        background: #F5F5F7;
    }
    .agency-info strong { font-size: 15px; font-weight: 600; }
    .agency-rating { font-size: 12.5px; }

    .prop-card__price {
        font-size: 22px;
        align-self: flex-start;
    }

    .prop-card__bien {
        padding: 10px 0;
        gap: 6px;
        border-top: 0.5px solid var(--ios-separator);
    }
    .bien-tag, .bien-spec {
        background: #EFEFF4;
        color: var(--ios-label-2);
        padding: 4px 11px;
        font-size: 12px;
    }

    .prop-card__message {
        background: #F9FAFB;
        border-left: 3px solid var(--rust);
        border-radius: 10px;
        padding: 12px 14px;
        margin-top: 10px;
    }
    .prop-card__message p { font-size: 13px; }

    .prop-card__footer {
        margin-top: 12px;
        justify-content: stretch;
    }
    .prop-card__footer .btn {
        flex: 1;
        justify-content: center;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 14px;
    }

    /* EMPTY PROPS */
    .empty-props {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 40px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 12px;
    }
    .empty-props__icon {
        background: #F5F5F7;
        color: var(--muted);
    }

    /* ACTIONS */
    .demande-actions {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
        margin-bottom: 8px;
    }
    .demande-actions__left {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    .demande-actions__left .btn,
    .demande-actions__left form,
    .demande-actions__left .state-pill,
    .demande-actions__left .inline-form {
        width: 100%;
        justify-content: center;
    }
    .demande-actions__left .inline-form .btn { width: 100%; justify-content: center; }

    .state-pill {
        padding: 14px 16px;
        font-size: 14px;
        border-radius: 12px;
        justify-content: center;
    }

    .demande-actions__right {
        margin-left: 0;
        width: 100%;
        justify-content: center;
    }

    /* BOUTONS iOS */
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
    .btn-danger-soft {
        background: transparent;
        border: 1px solid #FECACA;
        color: var(--c-danger);
    }
}

@media (max-width: 480px) {
    .demande-hero { padding: 16px; }
    .demande-hero__title { font-size: 20px; }
    .demande-hero__budget-value { font-size: 24px; }

    .demande-stat__value { font-size: 18px; }
    .demande-stat__label { font-size: 10px; }

    .ios-section-title { font-size: 16px; }

    .prop-card { padding: 14px; }
    .agency-avatar { width: 36px; height: 36px; font-size: 14px; }
    .agency-info strong { font-size: 14px; }
    .prop-card__price { font-size: 20px; }
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