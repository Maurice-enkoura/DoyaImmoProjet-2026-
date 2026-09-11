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

        $totalPropositions = $demande->propositions->count();
    @endphp

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="demande-show__back">
        <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à mes demandes
        </a>
    </div>

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

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <header class="demande-hero demande-hero--{{ $statutValue }}">

        <div class="demande-hero__main">
            <div class="demande-hero__badges">
                <span class="offre-status offre-status--{{ $statutValue }}">
                    <i class="fa-solid fa-circle"></i> {{ $statutLabel }}
                </span>
                @if($totalPropositions > 0)
                    <span class="propositions-badge">
                        <i class="fa-solid fa-handshake"></i>
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
                    Publiée le {{ $demande->created_at->format('d/m/Y') }}
                </span>
                <span class="demande-hero__sep">•</span>
                <span>
                    <i class="fa-regular fa-clock"></i>
                    {{ $demande->created_at->diffForHumans() }}
                </span>
            </div>

            @if($demande->updated_at != $demande->created_at)
                <div class="demande-hero__updated">
                    <i class="fa-regular fa-pen-to-square"></i>
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

    {{-- ═══════════════════════════════════════════
         DÉTAILS DE LA DEMANDE
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
            @if($demande->surface_minimum)
                <div class="info-grid__item">
                    <span class="info-grid__label">Surface minimum</span>
                    <span class="info-grid__value">{{ $demande->surface_minimum }} m²</span>
                </div>
            @endif
            @if($demande->date_entree_souhaitee)
                <div class="info-grid__item">
                    <span class="info-grid__label">Entrée souhaitée</span>
                    <span class="info-grid__value">{{ $demande->date_entree_souhaitee->format('d/m/Y') }}</span>
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
                <span>Aucun équipement spécifique demandé</span>
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
                <h4>Description</h4>
            </header>
            <blockquote class="quote-block quote-block--rust">
                {{ $demande->description }}
            </blockquote>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         PROPOSITIONS REÇUES
    ═══════════════════════════════════════════ --}}
    <section class="panel panel--propositions">
        <header class="panel__head">
            <span class="panel__icon">
                <i class="fa-solid fa-handshake"></i>
            </span>
            <h4>Propositions reçues</h4>
            @if($totalPropositions > 0)
                <span class="panel__count">{{ $totalPropositions }}</span>
            @endif
        </header>

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

                    <article class="prop-card prop-card--{{ $pStatut }}">

                        <header class="prop-card__top">
                            <span class="prop-status prop-status--{{ $pStatut }}">
                                <i class="fa-solid fa-circle"></i>
                                {{ $pStatutLabel }}
                            </span>

                            @if($score)
                                <span class="prop-score prop-score--{{ $scoreClass }}">
                                    <i class="fa-solid fa-bullseye"></i>
                                    <strong>{{ $score }}%</strong>
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
                                    <i class="fa-regular fa-building"></i>
                                    {{ $bien->titre ?? 'Bien' }}
                                </span>
                                @if($bien->surface)
                                    <span class="bien-spec">
                                        <i class="fa-regular fa-square"></i> {{ $bien->surface }} m²
                                    </span>
                                @endif
                                @if($bien->nombre_chambres)
                                    <span class="bien-spec">
                                        <i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.
                                    </span>
                                @endif
                            </div>
                        @endif

                        @if($proposition->message)
                            <div class="prop-card__message">
                                <i class="fa-regular fa-message"></i>
                                <p>{{ Str::limit($proposition->message, 100) }}</p>
                            </div>
                        @endif

                        <footer class="prop-card__footer">
                            <a href="{{ route('particulier.propositions.show', $proposition) }}"
                               class="btn btn-rust btn-sm">
                                <i class="fa-solid fa-eye"></i> Voir cette offre
                            </a>
                        </footer>
                    </article>
                @endforeach
            </div>

            @if($totalPropositions > 3)
                <div class="panel__more">
                    <a href="{{ route('particulier.propositions.index', ['demande' => $demande->slug]) }}"
                       class="btn btn-ghost">
                        <i class="fa-solid fa-list"></i>
                        Voir toutes les propositions
                    </a>
                </div>
            @endif

        @else
            <div class="empty-props">
                <div class="empty-props__icon">
                    <i class="fa-solid fa-inbox"></i>
                </div>
                <h5>Aucune proposition pour le moment</h5>
                <p>Les agences vous contacteront dès qu'elles auront un bien correspondant à votre recherche.</p>
            </div>
        @endif
    </section>

    {{-- ═══════════════════════════════════════════
         ACTIONS
    ═══════════════════════════════════════════ --}}
    <footer class="demande-actions">

        <div class="demande-actions__left">
            @if($statutValue === 'en_attente')
                <a href="{{ route('particulier.demandes.edit', $demande->slug) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
                <form action="{{ route('particulier.demandes.destroy', $demande->slug) }}"
                      method="POST" class="inline-form">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger-soft"
                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                        <i class="fa-solid fa-trash"></i> Annuler la demande
                    </button>
                </form>

            @elseif($statutValue === 'en_cours')
                <span class="state-pill state-pill--info">
                    <i class="fa-solid fa-clock"></i> Demande en cours de traitement
                </span>

            @elseif($statutValue === 'terminee')
                <span class="state-pill state-pill--success">
                    <i class="fa-solid fa-check-circle"></i> Demande terminée
                </span>

            @elseif($statutValue === 'annulee')
                <span class="state-pill state-pill--danger">
                    <i class="fa-solid fa-ban"></i> Demande annulée
                </span>
            @endif
        </div>

        <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost demande-actions__right">
            <i class="fa-solid fa-list"></i> Toutes mes demandes
        </a>
    </footer>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL DEMANDE — Particulier
   ═══════════════════════════════════════════════════════════ */

.demande-show {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --surface:      #F7F9FC;
    --radius:       14px;
}

.demande-show__back { margin-bottom: 16px; }

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

.demande-hero__updated {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: var(--muted);
    margin-top: 6px;
    padding: 3px 10px;
    background: var(--surface);
    border-radius: 999px;
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

/* ═══ STATUT / BADGES ═══ */
.offre-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.offre-status i { font-size: 6px; }
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.offre-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }

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
.panel__more {
    text-align: center;
    margin-top: 12px;
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

/* ═══════════════════════════════════════════
   PROPOSITIONS
   ═══════════════════════════════════════════ */
.props-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.prop-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px 16px;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
    position: relative;
    overflow: hidden;
}
.prop-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
    border-color: #d8d8d8;
}
.prop-card::before {
    content: '';
    position: absolute;
    top: 0; left: 16px; right: 16px;
    height: 3px;
    border-radius: 0 0 4px 4px;
}
.prop-card--en_attente::before { background: var(--c-warning); }
.prop-card--acceptee::before   { background: var(--c-success); }
.prop-card--refusee::before    { background: var(--c-danger); }
.prop-card--terminee::before   { background: var(--c-info); }

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
    gap: 6px;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.prop-status i { font-size: 5px; }
.prop-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.prop-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.prop-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.prop-status--terminee   { background: var(--c-info-bg);    color: var(--c-info); }

.prop-score {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid;
}
.prop-score i { font-size: 10px; }
.prop-score strong {
    font-family: var(--display);
    font-size: 12.5px;
    font-weight: 800;
}
.prop-score--excellent {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-color: #C8E6C9;
}
.prop-score--moyen {
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-color: #FFE0B2;
}
.prop-score--faible {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border-color: #FFCDD2;
}

/* Corps : agence + prix */
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
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    flex-shrink: 0;
}
.agency-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.agency-info strong {
    font-size: 14px;
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
    font-size: 12px;
    color: var(--text-soft);
}
.agency-rating i { color: #F5A623; font-size: 11px; }
.agency-reviews { color: var(--muted); font-size: 11px; }

.prop-card__price {
    font-family: var(--display);
    font-size: 18px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
    white-space: nowrap;
    flex-shrink: 0;
}
.prop-card__price small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}

/* Bien */
.prop-card__bien {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding: 8px 0;
    border-top: 1px dashed var(--border);
}
.bien-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    background: var(--surface);
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    color: var(--text);
}
.bien-tag i { color: var(--rust); font-size: 10px; }
.bien-spec {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    background: var(--surface);
    border-radius: 999px;
    font-size: 11.5px;
    color: var(--text-soft);
}
.bien-spec i { color: var(--rust); font-size: 10px; opacity: .8; }

/* Message */
.prop-card__message {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 9px 12px;
    background: var(--surface);
    border-radius: 9px;
    border-left: 3px solid var(--rust);
    margin-top: 8px;
}
.prop-card__message i {
    color: var(--rust);
    font-size: 12px;
    margin-top: 2px;
    flex-shrink: 0;
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

/* Message vide */
.empty-props {
    text-align: center;
    padding: 40px 20px;
    background: var(--surface);
    border-radius: 12px;
    border: 1px dashed var(--border);
}
.empty-props__icon {
    width: 60px;
    height: 60px;
    margin: 0 auto 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--c-warning-bg);
    border-radius: 50%;
    color: var(--rust);
    font-size: 22px;
}
.empty-props h5 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
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
}
.demande-actions__left {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.demande-actions__right { margin-left: auto; }
.inline-form { display: inline; }

.state-pill {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    border: 1px solid;
}
.state-pill--info {
    background: var(--c-info-bg);
    color: var(--c-info);
    border-color: #BBDEFB;
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
.btn-danger-soft {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border-color: #FFCDD2;
}
.btn-danger-soft:hover {
    background: var(--c-danger);
    color: #fff;
    border-color: var(--c-danger);
}
.btn-sm { padding: 6px 13px; font-size: 12px; }

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
        gap: 16px;
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

    .panel { padding: 16px; }

    .info-grid { grid-template-columns: 1fr; }

    .prop-card__body {
        flex-direction: column;
        align-items: stretch;
    }
    .prop-card__price {
        font-size: 17px;
        align-self: flex-start;
    }

    .demande-actions { flex-direction: column; align-items: stretch; }
    .demande-actions__left { flex-direction: column; align-items: stretch; }
    .demande-actions__left .btn,
    .demande-actions__left form,
    .demande-actions__left .state-pill,
    .demande-actions__left .inline-form { width: 100%; justify-content: center; }
    .demande-actions__left .inline-form .btn { width: 100%; justify-content: center; }
    .demande-actions__right { margin-left: 0; width: 100%; justify-content: center; }
}

@media (max-width: 480px) {
    .demande-hero { padding: 14px; }
    .demande-hero__title { font-size: 18px; }
    .demande-hero__budget-value { font-size: 20px; }

    .panel { padding: 14px; }
    .panel__head h4 { font-size: 14px; }

    .prop-card { padding: 12px 14px; }
    .agency-avatar { width: 36px; height: 36px; font-size: 14px; }
    .agency-info strong { font-size: 13px; }
    .prop-card__price { font-size: 15px; }

    .quote-block { padding: 12px 14px; font-size: 13px; }

    .btn { font-size: 12px; padding: 7px 13px; }
}
</style>
@endpush