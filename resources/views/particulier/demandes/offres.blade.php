@extends('layouts.dashboard')

@section('title', 'Offres reçues — DoyaImmo')
@section('page_title', 'Offres reçues')
@section('page_sub', 'Propositions pour votre besoin : ' . $demande->type_bien->label())

@section('content')
<div class="view active offres-demande">

    @php
        use App\Enums\StatutPropositionEnum;
        use App\Enums\StatutDemandeEnum;
        use App\Enums\StatutRendezVousEnum;

        $isLocation  = is_object($demande->type_operation)
            ? $demande->type_operation->value === 'location'
            : $demande->type_operation === 'location';
        $budgetLabel = $isLocation ? 'F/mois' : 'F';

        $statutDemande = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
        $statutDemandeLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);

        $allPropositions = $demande->propositions;

        $total           = $allPropositions->count();
        $nbEnAttente     = $allPropositions->where('statut', StatutPropositionEnum::EN_ATTENTE)->count();
        $nbAcceptees     = $allPropositions->where('statut', StatutPropositionEnum::ACCEPTEE)->count();
        $nbRefusees      = $allPropositions->where('statut', StatutPropositionEnum::REFUSEE)->count();
        $nbTerminees     = $allPropositions->where('statut', StatutPropositionEnum::TERMINEE)->count();

        $currentStatut = request('statut', '');

        $propositionAcceptee = $allPropositions
            ->where('statut', StatutPropositionEnum::ACCEPTEE)
            ->first();
        $hasPropositionAcceptee = $propositionAcceptee !== null;

        if ($currentStatut === '') {
            $offres = $allPropositions
                ->filter(fn($o) => in_array($o->statut, [
                    StatutPropositionEnum::EN_ATTENTE,
                    StatutPropositionEnum::ACCEPTEE,
                ]))
                ->sortByDesc('created_at')
                ->values();
        } else {
            $statutFiltre = match($currentStatut) {
                'en_attente' => StatutPropositionEnum::EN_ATTENTE,
                'acceptee'   => StatutPropositionEnum::ACCEPTEE,
                'refusee'    => StatutPropositionEnum::REFUSEE,
                'terminee'   => StatutPropositionEnum::TERMINEE,
                default      => null,
            };

            $offres = $statutFiltre
                ? $allPropositions
                    ->where('statut', $statutFiltre)
                    ->sortByDesc('created_at')
                    ->values()
                : $allPropositions->sortByDesc('created_at')->values();
        }

        $maxScore = $allPropositions->max(fn($o) => (int) ($o->score_matching ?? 0));

        $filtres = [
            ''           => ['label' => 'Toutes',     'count' => $total,       'icon' => 'fa-list'],
            'en_attente' => ['label' => 'En attente', 'count' => $nbEnAttente, 'icon' => 'fa-clock'],
            'acceptee'   => ['label' => 'Acceptées',  'count' => $nbAcceptees, 'icon' => 'fa-check-circle'],
            'refusee'    => ['label' => 'Refusées',   'count' => $nbRefusees,  'icon' => 'fa-times-circle'],
            'terminee'   => ['label' => 'Terminées',  'count' => $nbTerminees, 'icon' => 'fa-check-double'],
        ];
    @endphp

    {{-- ═══ RAPPEL DU BESOIN ═══ --}}
    <header class="besoin-recap">
        <div class="besoin-recap__badges">
            <span class="besoin-status besoin-status--{{ $statutDemande }}">
                {{ $statutDemandeLabel }}
            </span>
            @if($total > 0)
                <span class="besoin-recap__count">
                    <strong>{{ $total }}</strong> proposition{{ $total > 1 ? 's' : '' }}
                </span>
            @endif
        </div>

        <h1 class="besoin-recap__title">
            {{ is_object($demande->type_bien) ? $demande->type_bien->label() : $demande->type_bien }}
            <span class="besoin-recap__title-sep">—</span>
            {{ $demande->zone_recherchee }}
        </h1>

        <div class="besoin-recap__meta">
            <span>
                <i class="fa-solid fa-wallet"></i>
                <strong>{{ number_format($demande->budget_maximum, 0, ',', ' ') }} {{ $budgetLabel }}</strong>
            </span>
            @if($demande->surface_minimum)
                <span>
                    <i class="fa-regular fa-square"></i>
                    <strong>{{ $demande->surface_minimum }} m²</strong>
                </span>
            @endif
            @if($demande->nombre_chambres)
                <span>
                    <i class="fa-solid fa-bed"></i>
                    <strong>{{ $demande->nombre_chambres }}</strong> ch.
                </span>
            @endif
            <span>
                <i class="fa-regular fa-calendar"></i>
                {{ $demande->created_at->format('d/m/Y') }}
            </span>
        </div>
    </header>

    {{-- ═══ STATS INLINE (mobile) ═══ --}}
    <div class="offres-stats">
        <div class="offres-stat">
            <span class="offres-stat__value">{{ $total }}</span>
            <span class="offres-stat__label">Total</span>
        </div>
        <div class="offres-stat">
            <span class="offres-stat__value">{{ $nbEnAttente }}</span>
            <span class="offres-stat__label">En attente</span>
        </div>
        <div class="offres-stat">
            <span class="offres-stat__value">{{ $nbAcceptees }}</span>
            <span class="offres-stat__label">Acceptées</span>
        </div>
        <div class="offres-stat">
            <span class="offres-stat__value">{{ $nbRefusees + $nbTerminees }}</span>
            <span class="offres-stat__label">Clôturées</span>
        </div>
    </div>

    {{-- ═══ FILTRES PILLS ═══ --}}
    @if($total > 0)
        <nav class="filter-tabs" aria-label="Filtrer par statut">
            @foreach($filtres as $key => $meta)
                @if($meta['count'] > 0 || $key === '')
                    <a href="{{ route('particulier.demandes.offres', [
                            'demande' => $demande->slug,
                            'statut' => $key ?: null,
                        ]) }}"
                       class="filter-tab {{ $currentStatut === $key ? 'is-active' : '' }}">
                        <i class="fa-solid {{ $meta['icon'] }}"></i>
                        <span>{{ $meta['label'] }}</span>
                        <span class="filter-tab__count">{{ $meta['count'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    @endif

    {{-- ═══ LISTE DES OFFRES ═══ --}}
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

                    $isVedette = $bien && $bien->est_vedette;

                    $cover = $bien && $bien->medias->where('type_media', 'image')->first()
                        ? asset('storage/' . $bien->medias->where('type_media', 'image')->first()->fichier)
                        : null;

                    $equipementsBien = $bien->equipements ?? [];

                    $aRdvActif = $offre->rendezVous()
                        ->whereIn('statut', [
                            StatutRendezVousEnum::PLANIFIE->value,
                            StatutRendezVousEnum::CONFIRME->value,
                        ])
                        ->exists();

                    $estAcceptee = $statutValue === 'acceptee';

                    $autreAcceptee = $hasPropositionAcceptee
                        && !$estAcceptee
                        && $propositionAcceptee->id !== $offre->id;

                    $peutAgir = $statutValue === 'en_attente' && !$hasPropositionAcceptee;
                @endphp

                <article class="offre-card offre-card--{{ $statutValue }} {{ $isVedette ? 'is-vedette' : '' }} {{ $autreAcceptee ? 'is-cloturee' : '' }}">

                    {{-- Statut en badge haut à droite (pas de ruban) --}}
                    <header class="offre-card__top">
                        <span class="offre-status offre-status--{{ $statutValue }}">
                            {{ $statutLabel }}
                        </span>

                        @if($score)
                            <span class="score-badge score-badge--{{ $scoreClass }}">
                                Match {{ $score }}%
                            </span>
                        @endif

                        @if($isVedette)
                            <span class="offre-tag offre-tag--gold">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif

                        @if($autreAcceptee)
                            <span class="offre-tag offre-tag--muted">
                                <i class="fa-solid fa-lock"></i> Clôturée
                            </span>
                        @elseif($aRdvActif && $statutValue === 'en_attente')
                            <span class="offre-tag offre-tag--info">
                                <i class="fa-solid fa-calendar-check"></i> Visite prévue
                            </span>
                        @endif

                        <time class="offre-card__time"
                              datetime="{{ $offre->created_at->toIso8601String() }}"
                              title="{{ $offre->created_at->format('d/m/Y à H:i') }}">
                            {{ $offre->created_at->diffForHumans() }}
                        </time>
                    </header>

                    {{-- Image (mobile uniquement) --}}
                    @if($cover)
                        <div class="offre-card__media">
                            <img src="{{ $cover }}" alt="{{ $bien->titre ?? 'Bien' }}" loading="lazy">
                        </div>
                    @endif

                    {{-- Contenu --}}
                    <div class="offre-card__content">

                        <div class="offre-card__price">
                            {{ number_format($offre->prix_propose, 0, ',', ' ') }}
                            <small>FCFA</small>
                        </div>

                        <h3 class="offre-card__title">
                            {{ $bien->titre ?? 'Bien sans titre' }}
                        </h3>

                        @if($bien && $bien->adresse)
                            <div class="offre-card__address">
                                <i class="fa-solid fa-location-dot"></i>
                                {{ $bien->adresse }}
                            </div>
                        @endif

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

                        <div class="offre-card__specs">
                            @if($bien && $bien->surface)
                                <span class="spec-pill">{{ $bien->surface }} m²</span>
                            @endif
                            @if($bien && $bien->nombre_chambres)
                                <span class="spec-pill">{{ $bien->nombre_chambres }} ch.</span>
                            @endif
                            @if($bien && $bien->nombre_salles_bain)
                                <span class="spec-pill">{{ $bien->nombre_salles_bain }} sdb</span>
                            @endif
                            @if($bien && $bien->parking_disponible)
                                <span class="spec-pill">Parking</span>
                            @endif
                            @if(count($equipementsBien) > 3)
                                <span class="spec-pill">+{{ count($equipementsBien) - 3 }} équipements</span>
                            @endif
                        </div>

                        @if($offre->message)
                            <div class="offre-card__message">
                                <p>{{ Str::limit($offre->message, 130) }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <footer class="offre-card__actions">

                        @if($estAcceptee)
                            <span class="state-pill state-pill--success">
                                Offre acceptée
                            </span>

                            <div class="offre-card__actions-btns">
                                @if($aRdvActif)
                                    <a href="{{ route('particulier.rendezvous.index') }}"
                                       class="btn btn-ghost">
                                        Voir la visite
                                    </a>
                                @else
                                    <a href="{{ route('particulier.rendezvous.create', $offre->id) }}"
                                       class="btn btn-rust">
                                        Planifier la visite
                                    </a>
                                @endif

                                <a href="{{ route('particulier.propositions.show', $offre->id) }}"
                                   class="btn btn-ghost">
                                    Détails
                                </a>
                            </div>

                        @elseif($autreAcceptee)
                            <span class="state-pill state-pill--muted">
                                Offre clôturée
                            </span>

                            <a href="{{ route('particulier.propositions.show', $offre->id) }}"
                               class="btn btn-ghost offre-card__details-inline">
                                Détails
                            </a>

                        @elseif($peutAgir)

                            @if($aRdvActif)
                                <span class="state-pill state-pill--success">
                                    Visite programmée
                                </span>

                                <div class="offre-card__actions-btns">
                                    <a href="{{ route('particulier.rendezvous.index') }}"
                                       class="btn btn-ghost">
                                        Voir mon RDV
                                    </a>
                                    <a href="{{ route('particulier.propositions.show', $offre->id) }}"
                                       class="btn btn-ghost">
                                        Détails
                                    </a>
                                </div>

                            @else
                                <div class="offre-actions-trio">

                                    <form action="{{ route('particulier.propositions.choisir-et-planifier', $offre->id) }}"
                                          method="POST" class="inline-form"
                                          onsubmit="return confirm('Choisir cette agence et planifier une visite ?\n\nLes autres offres seront automatiquement refusées.')">
                                        @csrf
                                        <button type="submit" class="btn btn-rust">
                                            Choisir et planifier
                                        </button>
                                    </form>

                                    <form action="{{ route('particulier.propositions.refuser', $offre->id) }}"
                                          method="POST" class="inline-form"
                                          onsubmit="return confirm('Refuser cette offre ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost">
                                            Refuser
                                        </button>
                                    </form>

                                    <a href="{{ route('particulier.propositions.show', $offre->id) }}"
                                       class="btn btn-ghost">
                                        Détails
                                    </a>
                                </div>
                            @endif

                        @elseif($statutValue === 'refusee')
                            <span class="state-pill state-pill--danger">
                                Offre refusée
                            </span>

                            <a href="{{ route('particulier.propositions.show', $offre->id) }}"
                               class="btn btn-ghost offre-card__details-inline">
                                Détails
                            </a>

                        @elseif($statutValue === 'terminee')
                            <span class="state-pill state-pill--info">
                                Offre terminée
                            </span>

                            <a href="{{ route('particulier.propositions.show', $offre->id) }}"
                               class="btn btn-ghost offre-card__details-inline">
                                Détails
                            </a>
                        @endif

                    </footer>
                </article>
            @endforeach
        </div>

    @else

        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-inbox"></i>
            </div>

            @if(request('statut'))
                <h3>Aucune offre avec ce statut</h3>
                <p>Essayez un autre filtre ou revenez à la vue complète.</p>
                <a href="{{ route('particulier.demandes.offres', $demande->slug) }}" class="btn btn-ghost">
                    Voir toutes les offres
                </a>
            @else
                <h3>Aucune offre active pour ce besoin</h3>
                <p>
                    Les offres que vous avez refusées sont masquées.
                    Les agences vous contacteront dès qu'elles auront un bien correspondant.
                </p>
                <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost">
                    Retour à mes besoins
                </a>
            @endif
        </div>
    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   OFFRES D'UNE DEMANDE — Design épuré
   ═══════════════════════════════════════════════════════════ */

.offres-demande {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #F1F8F4;
    --c-danger:     #C62828;
    --c-danger-bg:  #FEF2F2;
    --c-info:       #1E40AF;
    --c-info-bg:    #EFF6FF;
    --c-gold:       #F5A623;
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}


/* ═══ STATS INLINE (mobile) ═══ */
.offres-stats { display: none; }

/* ═══ RAPPEL DU BESOIN ═══ */
.besoin-recap {
    padding: 18px 22px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 16px;
}

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
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    background: #F5F5F7;
    color: #4A5260;
}
.besoin-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.besoin-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.besoin-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.besoin-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }

.besoin-recap__count {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 999px;
    background: #F5F5F7;
    color: var(--text-soft);
    font-size: 11.5px;
    font-weight: 500;
}
.besoin-recap__count strong {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    color: var(--text);
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
.besoin-recap__meta i { font-size: 12px; margin-right: 5px; }
.besoin-recap__meta strong { color: var(--text); font-weight: 600; }

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
.filter-tabs::-webkit-scrollbar-thumb { background: var(--border); border-radius: 999px; }

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
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.filter-tab:hover { border-color: var(--rust); color: var(--rust); }
.filter-tab i { font-size: 12px; opacity: .85; }
.filter-tab.is-active {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
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

/* ═══ LISTE ═══ */
.offres-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ═══ CARTE ═══ */
.offre-card {
    position: relative;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    transition: border-color .15s;
    overflow: hidden;
}
.offre-card:hover {
    border-color: #d8d8d8;
}
.offre-card.is-vedette {
    border-color: rgba(245, 166, 35, .4);
}
.offre-card.is-cloturee { opacity: .82; }

/* En-tête */
.offre-card__top {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.offre-card__time {
    margin-left: auto;
    font-size: 11.5px;
    color: var(--muted);
    white-space: nowrap;
}

.offre-status {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    background: #F5F5F7;
    color: #4A5260;
}
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.offre-status--terminee   { background: var(--c-info-bg);    color: var(--c-info); }

.score-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
    background: #F5F5F7;
    color: var(--text-soft);
}
.score-badge--excellent { background: var(--c-success-bg); color: var(--c-success); }
.score-badge--moyen     { background: var(--c-warning-bg); color: var(--c-warning); }
.score-badge--faible    { background: var(--c-danger-bg);  color: var(--c-danger); }

.offre-tag {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
    background: #F5F5F7;
    color: var(--text-soft);
}
.offre-tag i { font-size: 10px; }
.offre-tag--gold  { background: #FFF8E1; color: #A05A00; }
.offre-tag--info  { background: var(--c-info-bg); color: var(--c-info); }
.offre-tag--muted { background: #F5F5F7; color: #6C6C70; }

/* Image (mobile) */
.offre-card__media { display: none; }

/* Contenu */
.offre-card__content {
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 14px;
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

.offre-card__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.3;
}

.offre-card__address {
    font-size: 12.5px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 5px;
}
.offre-card__address i { color: var(--muted); font-size: 11px; }

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
    background: #F5F5F7;
    color: var(--text);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
}
.agency-info { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.agency-info strong {
    font-size: 13px;
    font-weight: 600;
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

.offre-card__specs {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.spec-pill {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    background: #F5F5F7;
    font-size: 11.5px;
    color: var(--text-soft);
    font-weight: 500;
}

.offre-card__message {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 12px;
    background: #F9FAFB;
    border-radius: 10px;
    border-left: 3px solid var(--rust);
}
.offre-card__message p {
    font-size: 12.5px;
    color: var(--text-soft);
    line-height: 1.55;
    margin: 0;
    font-style: italic;
}

/* ═══ ACTIONS ═══ */
.offre-card__actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding-top: 14px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}

.offre-card__actions-btns {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
    flex-wrap: wrap;
}

.offre-actions-trio {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-left: auto;
    flex-wrap: wrap;
}

.offre-actions-trio .inline-form {
    display: inline-flex;
    margin: 0;
}

.offre-card__details-inline {
    margin-left: auto;
}

.inline-form { display: inline; }

.state-pill {
    display: inline-flex;
    align-items: center;
    padding: 6px 13px;
    border-radius: 10px;
    font-size: 12px;
    font-weight: 500;
    background: #F5F5F7;
    color: var(--text-soft);
}
.state-pill--success { background: var(--c-success-bg); color: var(--c-success); }
.state-pill--danger  { background: var(--c-danger-bg);  color: var(--c-danger); }
.state-pill--info    { background: var(--c-info-bg);    color: var(--c-info); }
.state-pill--muted   { background: #F5F5F7;             color: #6C6C70; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
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

/* Empty state */
.empty-state {
    text-align: center;
    padding: 64px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 72px; height: 72px;
    margin: 0 auto 16px;
    display: flex; align-items: center; justify-content: center;
    background: #F5F5F7;
    border-radius: 50%;
    color: var(--muted);
    font-size: 28px;
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
    margin: 0 auto 18px;
    max-width: 420px;
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .offres-demande { background: transparent; padding-top: 4px; }

    /* RAPPEL BESOIN iOS */
    .besoin-recap {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .besoin-recap__badges { gap: 6px; margin-bottom: 10px; }
    .besoin-status {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
    }
    .besoin-recap__count { font-size: 12px; padding: 4px 10px; }
    .besoin-recap__count strong { font-size: 13px; }

    .besoin-recap__title {
        font-size: 19px;
        letter-spacing: -.3px;
    }
    .besoin-recap__title-sep { color: var(--ios-label-3); }

    .besoin-recap__meta {
        font-size: 13.5px;
        gap: 8px 14px;
    }

    /* STATS INLINE */
    .offres-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .offres-stat {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
        position: relative;
        padding: 0 4px;
    }
    .offres-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%;
        width: 0.5px;
        background: var(--ios-separator);
    }
    .offres-stat__value {
        font-family: var(--display);
        font-size: 22px;
        font-weight: 800;
        color: var(--ios-label);
        letter-spacing: -.5px;
        line-height: 1;
    }
    .offres-stat__label {
        font-size: 11px;
        font-weight: 500;
        color: var(--ios-label-3);
        margin-top: 2px;
    }

    /* FILTRES PILLS iOS */
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
        -webkit-tap-highlight-color: transparent;
    }
    .filter-tab:active { transform: scale(.98); }
    .filter-tab.is-active {
        background: var(--rust);
        color: #fff;
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

    /* CARTES iOS */
    .offres-list { gap: 12px; }

    .offre-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .offre-card:hover { border: none; }

    .offre-card__top {
        gap: 8px;
        margin-bottom: 12px;
    }
    .offre-status {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
    }
    .score-badge {
        font-size: 11.5px;
        padding: 4px 11px;
    }
    .offre-tag {
        font-size: 11.5px;
        padding: 4px 10px;
    }
    .offre-card__time {
        font-size: 11.5px;
        color: var(--ios-label-3);
        margin-left: auto;
    }

    .offre-card__media {
        display: block;
        aspect-ratio: 16/9;
        border-radius: 12px;
        overflow: hidden;
        background: #EFEFF4;
        margin-bottom: 14px;
    }
    .offre-card__media img {
        width: 100%; height: 100%;
        object-fit: cover;
        display: block;
    }

    .offre-card__content { gap: 8px; margin-bottom: 12px; }

    .offre-card__price {
        font-size: 24px;
        letter-spacing: -.4px;
    }

    .offre-card__title {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
    }

    .offre-card__address {
        font-size: 13px;
        color: var(--ios-label-3);
    }

    .offre-card__agency { gap: 10px; }
    .agency-avatar {
        width: 38px;
        height: 38px;
        font-size: 15px;
        background: #F5F5F7;
    }
    .agency-info strong { font-size: 14px; font-weight: 600; }
    .agency-rating { font-size: 12.5px; }

    .offre-card__specs { gap: 6px; }
    .spec-pill {
        font-size: 12px;
        padding: 4px 10px;
    }

    .offre-card__message {
        background: #F9FAFB;
        border-left: 3px solid var(--rust);
        border-radius: 10px;
        padding: 12px 14px;
    }
    .offre-card__message p { font-size: 13px; }

    /* ACTIONS iOS */
    .offre-card__actions {
        gap: 8px;
        padding-top: 12px;
        border-top: 0.5px solid var(--ios-separator);
        flex-direction: column;
        align-items: stretch;
    }

    .offre-card__actions-btns,
    .offre-actions-trio {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
        margin-left: 0;
    }

    .offre-actions-trio .inline-form,
    .offre-actions-trio .btn,
    .offre-card__actions-btns .btn,
    .offre-card__actions > a,
    .offre-card__actions > form .btn {
        width: 100%;
        justify-content: center;
    }

    .offre-card__details-inline { margin-left: 0; }

    .btn {
        padding: 13px 18px;
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

    .state-pill {
        padding: 12px 16px;
        font-size: 14px;
        border-radius: 12px;
        justify-content: center;
    }

    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon { width: 64px; height: 64px; font-size: 26px; }
}

@media (max-width: 480px) {
    .offres-stat__value { font-size: 20px; }
    .offres-stat__label { font-size: 10.5px; }

    .besoin-recap { padding: 16px; }
    .besoin-recap__title { font-size: 18px; }

    .offre-card { padding: 14px; }
    .offre-card__price { font-size: 22px; }
    .offre-card__title { font-size: 15px; }
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