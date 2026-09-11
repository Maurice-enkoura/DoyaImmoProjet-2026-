@extends('layouts.dashboard-agence')

@section('title', 'Tableau de bord — DoyaImmo')
@section('page_title', 'Tableau de bord')
@section('page_sub', 'Vue d\'ensemble de votre activité')

@section('content')
<div class="view active agence-dashboard">

    @php
        $agence   = Auth::user()->agence;
        $prenom   = Auth::user()->prenom ?? 'Agence';
        $nom      = Auth::user()->nom ?? '';
        $initiales = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));

        $heure = now()->hour;
        $salutation = $heure < 12 ? 'Bonjour' : ($heure < 18 ? 'Bon après-midi' : 'Bonsoir');

        // Compteurs
        $besoinsDispo    = $stats['besoins_disponibles'] ?? 0;
        $offresEnvoyees  = $stats['offres_envoyees'] ?? 0;
        $rdvAVenir       = $stats['rendezvous_a_venir'] ?? 0;
        $noteMoyenne     = $stats['note_moyenne'] ?? 0;
        $nbBiensVedette  = $biensEnVedette->count();

        // RDV du jour
        $rdvAujourdhui = $prochainsRendezVous
            ->filter(fn($rdv) => \Carbon\Carbon::parse($rdv->date_visite)->isToday())
            ->count();

        $nbAvis = $derniersAvis->count();
    @endphp

    {{-- ═══════════════════════════════════════════
         WELCOME
    ═══════════════════════════════════════════ --}}
    <header class="welcome">
        <div class="welcome__left">
            <div class="welcome__avatar">{{ $initiales }}</div>
            <div class="welcome__text">
                <h1>{{ $salutation }}, {{ $prenom }} </h1>
                <span class="welcome__date">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->translatedFormat('l d F Y') }}
                </span>
            </div>
        </div>

        <a href="{{ route('agence.biens.create') }}" class="btn btn-rust">
            <i class="fa-solid fa-plus"></i> Publier un bien
        </a>
    </header>

    {{-- ═══════════════════════════════════════════
         ALERTE ABONNEMENT (si applicable)
    ═══════════════════════════════════════════ --}}
    @if($abonnementActif && !$modeGratuit)
        @if(!$abonnementActuel)
            <div class="alert alert--danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <div class="alert__content">
                    <strong>Abonnement requis</strong>
                    <span>Souscrivez un abonnement pour publier des biens et envoyer des offres</span>
                </div>
                <a href="{{ route('agence.abonnement') }}" class="alert__cta">Voir les offres →</a>
            </div>
        @elseif($offresRestantes <= 0 && $limiteOffres !== PHP_INT_MAX)
            <div class="alert alert--warning">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div class="alert__content">
                    <strong>Quota d'offres atteint</strong>
                    <span>Vous avez utilisé vos {{ $limiteOffres }} offres ce mois-ci</span>
                </div>
                <a href="{{ route('agence.abonnement') }}" class="alert__cta">Upgrader →</a>
            </div>
        @elseif($limiteOffres !== PHP_INT_MAX && $offresRestantes <= 5)
            <div class="alert alert--info">
                <i class="fa-solid fa-clock"></i>
                <div class="alert__content">
                    <strong>{{ $offresRestantes }} offre(s) restante(s)</strong>
                    <span>Rechargez bientôt pour ne pas être bloqué</span>
                </div>
                <a href="{{ route('agence.abonnement') }}" class="alert__cta">Voir mon abonnement →</a>
            </div>
        @endif
    @endif

    {{-- ═══════════════════════════════════════════
         AUJOURD'HUI (actions rapides)
    ═══════════════════════════════════════════ --}}
    @if($rdvAujourdhui > 0 || $besoinsDispo > 0 || $nbAvis > 0)
        <section class="today-panel">
            <header class="today-panel__head">
                <span class="today-panel__icon">
                    <i class="fa-solid fa-bolt"></i>
                </span>
                <h2 class="today-panel__title">Aujourd'hui</h2>
            </header>

            <div class="today-panel__grid">
                @if($rdvAujourdhui > 0)
                    <a href="{{ route('agence.rendezvous.index') }}" class="today-item today-item--urgent">
                        <div class="today-item__icon">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <div class="today-item__content">
                            <strong>{{ $rdvAujourdhui }} RDV aujourd'hui</strong>
                            <span>Consultez votre agenda</span>
                        </div>
                        <i class="fa-solid fa-chevron-right today-item__arrow"></i>
                    </a>
                @endif

                @if($besoinsDispo > 0)
                    <a href="{{ route('agence.demandes.index') }}" class="today-item">
                        <div class="today-item__icon">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <div class="today-item__content">
                            <strong>{{ $besoinsDispo }} demandes disponibles</strong>
                            <span>Parcourez les besoins clients</span>
                        </div>
                        <i class="fa-solid fa-chevron-right today-item__arrow"></i>
                    </a>
                @endif

                @if($nbAvis > 0)
                    <a href="{{ route('agence.evaluations.index') }}" class="today-item">
                        <div class="today-item__icon">
                            <i class="fa-regular fa-star"></i>
                        </div>
                        <div class="today-item__content">
                            <strong>{{ $nbAvis }} nouveau{{ $nbAvis > 1 ? 'x' : '' }} avis</strong>
                            <span>Découvrez ce que disent vos clients</span>
                        </div>
                        <i class="fa-solid fa-chevron-right today-item__arrow"></i>
                    </a>
                @endif
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         STATS
    ═══════════════════════════════════════════ --}}
    <section class="stats-grid">
        <a href="{{ route('agence.demandes.index') }}" class="stat-card stat-card--info">
            <div class="stat-card__icon">
                <i class="fa-solid fa-house-circle-check"></i>
            </div>
            <div class="stat-card__content">
                <span class="stat-card__value">{{ $besoinsDispo }}</span>
                <span class="stat-card__label">Demandes disponibles</span>
            </div>
        </a>

        <a href="{{ route('agence.propositions.index') }}" class="stat-card stat-card--rust">
            <div class="stat-card__icon">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div class="stat-card__content">
                <span class="stat-card__value">{{ $offresEnvoyees }}</span>
                <span class="stat-card__label">Offres ce mois</span>
            </div>
        </a>

        <a href="{{ route('agence.rendezvous.index') }}" class="stat-card stat-card--warning">
            <div class="stat-card__icon">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="stat-card__content">
                <span class="stat-card__value">{{ $rdvAVenir }}</span>
                <span class="stat-card__label">RDV à venir</span>
            </div>
        </a>

        <a href="{{ route('agence.evaluations.index') }}" class="stat-card stat-card--gold">
            <div class="stat-card__icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="stat-card__content">
                <span class="stat-card__value">{{ number_format($noteMoyenne, 1) }}</span>
                <span class="stat-card__label">Note moyenne</span>
            </div>
        </a>
    </section>

    {{-- ═══════════════════════════════════════════
         BIENS EN VEDETTE
    ═══════════════════════════════════════════ --}}
    @if($nbBiensVedette > 0)
        <section class="panel panel--vedette">
            <header class="panel__head">
                <span class="panel__icon panel__icon--gold">
                    <i class="fa-solid fa-star"></i>
                </span>
                <h3>Mes biens en vedette</h3>
                <span class="panel__count">{{ $nbBiensVedette }}</span>
                <a href="{{ route('agence.biens.index', ['vedette' => 'true']) }}" class="panel__see-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </header>

            <div class="vedette-grid">
                @foreach($biensEnVedette as $bien)
                    @php
                        $cover = $bien->medias->where('type_media', 'image')->first()
                            ? asset('storage/' . $bien->medias->where('type_media', 'image')->first()->fichier)
                            : null;
                    @endphp

                    <a href="{{ route('agence.biens.show', $bien->slug) }}" class="vedette-card">
                        <div class="vedette-card__media">
                            @if($cover)
                                <img src="{{ $cover }}" alt="{{ $bien->titre }}" loading="lazy">
                            @else
                                <div class="vedette-card__placeholder">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            @endif
                            <span class="vedette-card__badge">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        </div>

                        <div class="vedette-card__body">
                            <h4 class="vedette-card__title">{{ $bien->titre }}</h4>
                            <div class="vedette-card__price">
                                {{ number_format($bien->prix ?? 0, 0, ',', ' ') }}
                                <small>FCFA</small>
                            </div>
                            <div class="vedette-card__meta">
                                <span><i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         DEUX COLONNES : DEMANDES + RDV
    ═══════════════════════════════════════════ --}}
    <div class="two-columns">

        {{-- ═══ DERNIÈRES DEMANDES ═══ --}}
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-solid fa-inbox"></i>
                </span>
                <h3>Dernières demandes</h3>
                <a href="{{ route('agence.demandes.index') }}" class="panel__see-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </header>

            <div class="panel__body">
                @forelse($derniersBesoins as $besoin)
                    @php
                        $statutValue = $besoin->statut instanceof \App\Enums\StatutDemandeEnum
                            ? $besoin->statut->value
                            : $besoin->statut;
                        $statutLabel = $besoin->statut instanceof \App\Enums\StatutDemandeEnum
                            ? $besoin->statut->label()
                            : ucfirst($besoin->statut);
                    @endphp

                    <a href="{{ route('agence.demandes.show', $besoin->slug) }}" class="row">
                        <div class="row__main">
                            <div class="row__title">{{ $besoin->type_bien->label() }}</div>
                            <div class="row__meta">
                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $besoin->zone_recherchee }}
                                </span>
                                @if($besoin->surface_minimum)
                                    <span>
                                        <i class="fa-regular fa-square"></i>
                                        {{ $besoin->surface_minimum }} m²
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="row__right">
                            <span class="row__time">{{ $besoin->created_at->diffForHumans() }}</span>
                            <span class="offre-status offre-status--{{ $statutValue }}">
                                {{ $statutLabel }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="empty-box">
                        <div class="empty-box__icon">
                            <i class="fa-regular fa-inbox"></i>
                        </div>
                        <h4>Aucune demande récente</h4>
                        <p>Les demandes des particuliers apparaîtront ici.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ═══ PROCHAINS RDV ═══ --}}
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </span>
                <h3>Prochains rendez-vous</h3>
                <a href="{{ route('agence.rendezvous.index') }}" class="panel__see-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </header>

            <div class="panel__body">
                @forelse($prochainsRendezVous as $rdv)
                    @php
                        $statutValue = $rdv->statut instanceof \App\Enums\StatutRendezVousEnum
                            ? $rdv->statut->value
                            : $rdv->statut;
                        $statutLabel = $rdv->statut instanceof \App\Enums\StatutRendezVousEnum
                            ? $rdv->statut->label()
                            : ucfirst($rdv->statut);

                        $dateVisite = \Carbon\Carbon::parse($rdv->date_visite);
                        $isToday    = $dateVisite->isToday();
                    @endphp

                    <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="row row--rdv">
                        <div class="row__date {{ $isToday ? 'row__date--today' : '' }}">
                            <span class="row__date-day">{{ $dateVisite->format('d') }}</span>
                            <span class="row__date-month">{{ strtoupper($dateVisite->translatedFormat('M')) }}</span>
                        </div>

                        <div class="row__main">
                            <div class="row__title">
                                {{ $rdv->proposition->bien->titre ?? 'Visite' }}
                            </div>
                            <div class="row__meta">
                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                                </span>
                                @if($rdv->particulier && $rdv->particulier->user)
                                    <span>
                                        <i class="fa-regular fa-user"></i>
                                        {{ $rdv->particulier->user->prenom }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <span class="offre-status offre-status--{{ $statutValue }}">
                            {{ $statutLabel }}
                        </span>
                    </a>
                @empty
                    <div class="empty-box">
                        <div class="empty-box__icon">
                            <i class="fa-regular fa-calendar"></i>
                        </div>
                        <h4>Aucun rendez-vous</h4>
                        <p>Vos prochains RDV avec les clients apparaîtront ici.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    {{-- ═══════════════════════════════════════════
         DERNIERS AVIS
    ═══════════════════════════════════════════ --}}
    @if($derniersAvis->count() > 0)
        <section class="panel">
            <header class="panel__head">
                <span class="panel__icon panel__icon--gold">
                    <i class="fa-regular fa-star"></i>
                </span>
                <h3>Derniers avis reçus</h3>
                <a href="{{ route('agence.evaluations.index') }}" class="panel__see-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </header>

            <div class="avis-list">
                @foreach($derniersAvis as $avis)
                    @php
                        $note = (int) ($avis->note ?? 0);
                        $sentiment = $note >= 4 ? 'positif' : ($note === 3 ? 'neutre' : 'negatif');
                        $prenom = $avis->particulier->user->prenom ?? 'C';
                        $initial = strtoupper(mb_substr($prenom, 0, 1));
                    @endphp

                    <div class="avis-row avis-row--{{ $sentiment }}">
                        <div class="avis-row__avatar">{{ $initial }}</div>

                        <div class="avis-row__info">
                            <div class="avis-row__head">
                                <strong>
                                    {{ $avis->particulier->user->prenom ?? '' }} {{ $avis->particulier->user->nom ?? '' }}
                                </strong>
                                <div class="avis-row__stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $note ? 'is-on' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($avis->commentaire)
                                <p class="avis-row__comment">
                                    "{{ Str::limit($avis->commentaire, 100) }}"
                                </p>
                            @endif
                            <span class="avis-row__date">
                                {{ $avis->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DASHBOARD AGENCE
   ═══════════════════════════════════════════════════════════ */

.agence-dashboard {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #F5A623;
    --c-gold-bg:    #FFF8E1;
    --surface:      #F7F9FC;
    --radius:       14px;
}

/* ═══ WELCOME ═══ */
.welcome {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 18px 22px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    flex-wrap: wrap;
}
.welcome__left { display: flex; align-items: center; gap: 14px; min-width: 0; }
.welcome__avatar {
    width: 50px; height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--display);
    font-size: 19px; font-weight: 700;
    flex-shrink: 0;
    letter-spacing: .5px;
}
.welcome__text h1 {
    font-family: var(--display);
    font-size: 19px; font-weight: 700;
    margin: 0 0 2px;
    color: var(--text);
    line-height: 1.2;
}
.welcome__date {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12.5px; color: var(--muted);
}
.welcome__date i { font-size: 11px; opacity: .8; }

/* ═══ ALERTS ═══ */
.alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 14px;
    border-left: 4px solid;
    flex-wrap: wrap;
}
.alert i { font-size: 18px; flex-shrink: 0; }
.alert__content { flex: 1; min-width: 180px; }
.alert__content strong {
    display: block;
    font-size: 13.5px;
    font-weight: 700;
}
.alert__content span {
    font-size: 12.5px;
    opacity: .85;
}
.alert__cta {
    padding: 6px 14px;
    background: rgba(255, 255, 255, .55);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    color: inherit;
    white-space: nowrap;
    transition: background .2s;
}
.alert__cta:hover { background: rgba(255, 255, 255, .85); }

.alert--danger {
    background: var(--c-danger-bg);
    border-left-color: var(--c-danger);
    color: var(--c-danger);
}
.alert--warning {
    background: var(--c-warning-bg);
    border-left-color: var(--c-warning);
    color: var(--c-warning);
}
.alert--info {
    background: var(--c-info-bg);
    border-left-color: var(--c-info);
    color: var(--c-info);
}

/* ═══ TODAY PANEL ═══ */
.today-panel {
    background: linear-gradient(135deg, #FFFBF7 0%, #fff 55%);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 22px;
    margin-bottom: 14px;
}
.today-panel__head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
}
.today-panel__icon {
    width: 32px; height: 32px;
    border-radius: 9px;
    background: var(--rust-soft);
    color: var(--rust);
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.today-panel__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
}

.today-panel__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 10px;
}

.today-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    transition: all .2s;
}
.today-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, .06);
    border-color: #d8d8d8;
}
.today-item--urgent {
    background: var(--c-warning-bg);
    border-color: #FFE0B2;
}

.today-item__icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    background: var(--surface);
    color: var(--rust);
    font-size: 16px;
    flex-shrink: 0;
}
.today-item--urgent .today-item__icon {
    background: rgba(230, 81, 0, .12);
    color: var(--c-warning);
}

.today-item__content {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.today-item__content strong {
    font-family: var(--display);
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text);
}
.today-item__content span {
    font-size: 12px;
    color: var(--muted);
}
.today-item__arrow {
    color: var(--border);
    font-size: 12px;
    transition: all .2s;
}
.today-item:hover .today-item__arrow {
    color: var(--rust);
    transform: translateX(3px);
}

/* ═══ STATS GRID ═══ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 14px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    text-decoration: none;
    color: inherit;
    transition: transform .2s, box-shadow .2s;
}
.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
}

.stat-card__icon {
    width: 44px; height: 44px;
    border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.stat-card--info    .stat-card__icon { background: var(--c-info-bg);    color: var(--c-info); }
.stat-card--rust    .stat-card__icon { background: var(--rust-soft);    color: var(--rust); }
.stat-card--warning .stat-card__icon { background: var(--c-warning-bg); color: var(--c-warning); }
.stat-card--gold    .stat-card__icon { background: var(--c-gold-bg);    color: var(--c-gold); }

.stat-card__content {
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 0;
}
.stat-card__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.stat-card__label {
    font-size: 12px;
    color: var(--muted);
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ═══ PANNEAUX ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    overflow: hidden;
}
.panel--vedette {
    background: linear-gradient(135deg, #FFFBF0 0%, #fff 45%);
    border-color: #FFE0B2;
}

.panel__head {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    background: #FAFBFC;
    flex-wrap: wrap;
}
.panel--vedette .panel__head {
    background: transparent;
    border-bottom-color: #FFE0B2;
}
.panel__icon {
    width: 32px; height: 32px;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    background: var(--c-warning-bg);
    color: var(--rust);
    font-size: 14px;
    flex-shrink: 0;
}
.panel__icon--gold {
    background: var(--c-gold-bg);
    color: var(--c-gold);
}
.panel__head h3 {
    font-family: var(--display);
    font-size: 14.5px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    flex: 1;
    min-width: 0;
}
.panel__count {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: var(--c-gold-bg);
    color: var(--c-warning);
    border-radius: 999px;
}
.panel__see-all {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 600;
    color: var(--rust);
    text-decoration: none;
    transition: gap .2s;
    white-space: nowrap;
}
.panel__see-all:hover { gap: 8px; }
.panel__see-all i { font-size: 10px; }

.panel__body {
    display: flex;
    flex-direction: column;
}

/* ═══ BIENS EN VEDETTE ═══ */
.vedette-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
    padding: 16px 18px;
}

.vedette-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform .2s, box-shadow .2s;
    position: relative;
}
.vedette-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(245, 166, 35, .2);
}

.vedette-card__media {
    position: relative;
    aspect-ratio: 4 / 3;
    background: var(--surface);
    overflow: hidden;
}
.vedette-card__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s;
}
.vedette-card:hover .vedette-card__media img { transform: scale(1.05); }

.vedette-card__placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted);
    font-size: 32px;
    opacity: .4;
}

.vedette-card__badge {
    position: absolute;
    top: 8px;
    left: 8px;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    box-shadow: 0 2px 6px rgba(245, 166, 35, .35);
}
.vedette-card__badge i { font-size: 8px; }

.vedette-card__body {
    padding: 10px 12px;
}
.vedette-card__title {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.vedette-card__price {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
    margin-bottom: 4px;
}
.vedette-card__price small {
    font-size: 10px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}
.vedette-card__meta {
    font-size: 11px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 4px;
}
.vedette-card__meta i {
    color: var(--rust);
    font-size: 10px;
    opacity: .8;
}

/* ═══ TWO COLUMNS ═══ */
.two-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
}

/* ═══ ROWS ═══ */
.row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    text-decoration: none;
    color: inherit;
    transition: background .15s;
}
.row:last-child { border-bottom: none; }
.row:hover { background: var(--surface); }

.row--rdv { justify-content: flex-start; gap: 12px; }

.row__date {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 44px;
    padding: 6px 4px;
    background: var(--surface);
    border-radius: 10px;
    border: 1px solid var(--border);
    flex-shrink: 0;
}
.row__date--today {
    background: var(--c-warning-bg);
    border-color: #FFE0B2;
}
.row__date-day {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.row__date--today .row__date-day { color: var(--c-warning); }
.row__date-month {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
    margin-top: 1px;
}
.row__date--today .row__date-month { color: var(--c-warning); }

.row__main { flex: 1; min-width: 0; }
.row__title {
    font-family: var(--display);
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.row__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 3px 12px;
    font-size: 11.5px;
    color: var(--muted);
}
.row__meta i {
    font-size: 10px;
    margin-right: 4px;
    opacity: .8;
    color: var(--rust);
}

.row__right {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
    flex-direction: column;
    align-items: flex-end;
}
.row__time {
    font-size: 11px;
    color: var(--muted);
    white-space: nowrap;
}

/* ═══ STATUTS ═══ */
.offre-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
}
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.offre-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.offre-status--planifie   { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--confirme   { background: var(--c-info-bg);    color: var(--c-info); }
.offre-status--termine    { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--annule     { background: var(--c-danger-bg);  color: var(--c-danger); }

/* ═══ AVIS ═══ */
.avis-list {
    display: flex;
    flex-direction: column;
    gap: 1px;
}

.avis-row {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    border-left: 3px solid var(--muted);
}
.avis-row:last-child { border-bottom: none; }
.avis-row--positif { border-left-color: var(--c-success); background: rgba(30, 122, 71, .02); }
.avis-row--neutre  { border-left-color: var(--c-warning); background: rgba(230, 81, 0, .02); }
.avis-row--negatif { border-left-color: var(--c-danger);  background: rgba(198, 40, 40, .02); }

.avis-row__avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--display);
    font-size: 16px; font-weight: 700;
    flex-shrink: 0;
}
.avis-row__info { flex: 1; min-width: 0; }
.avis-row__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 4px;
    flex-wrap: wrap;
}
.avis-row__head strong {
    font-size: 13.5px;
    font-weight: 700;
    color: var(--text);
}
.avis-row__stars {
    display: inline-flex;
    gap: 2px;
    font-size: 12px;
}
.avis-row__stars i { color: #D4D8E0; }
.avis-row__stars i.is-on { color: var(--c-gold); }

.avis-row__comment {
    font-size: 12.5px;
    color: var(--text-soft);
    font-style: italic;
    line-height: 1.55;
    margin: 0 0 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.avis-row__date {
    font-size: 11px;
    color: var(--muted);
}

/* ═══ EMPTY BOX ═══ */
.empty-box {
    text-align: center;
    padding: 32px 20px;
    color: var(--muted);
}
.empty-box__icon {
    width: 56px; height: 56px;
    margin: 0 auto 12px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: var(--surface);
    color: var(--muted);
    font-size: 22px;
}
.empty-box h4 {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--text);
    margin: 0 0 4px;
}
.empty-box p {
    font-size: 12.5px;
    color: var(--muted);
    margin: 0 auto;
    max-width: 320px;
}

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

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .two-columns { grid-template-columns: 1fr; }
    .today-panel__grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .welcome {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
        padding: 16px;
    }
    .welcome__text h1 { font-size: 17px; }
    .welcome .btn { justify-content: center; }

    .stats-grid { gap: 8px; }
    .stat-card { padding: 12px 14px; gap: 10px; }
    .stat-card__icon { width: 38px; height: 38px; font-size: 15px; }
    .stat-card__value { font-size: 20px; }
    .stat-card__label { font-size: 11px; }

    .vedette-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; padding: 14px; }

    .panel__head { padding: 12px 14px; }
    .panel__head h3 { font-size: 13.5px; }

    .row { padding: 11px 14px; }
    .row__title { font-size: 13px; }

    .row__right {
        flex-direction: row;
        align-items: center;
        gap: 6px;
    }

    .avis-row { padding: 12px 14px; }
}

@media (max-width: 480px) {
    .welcome { padding: 14px; }
    .welcome__avatar { width: 44px; height: 44px; font-size: 16px; }
    .welcome__text h1 { font-size: 16px; }

    .today-panel { padding: 14px; }
    .today-item { padding: 10px 12px; }
    .today-item__icon { width: 34px; height: 34px; font-size: 14px; }
    .today-item__content strong { font-size: 13px; }

    .stats-grid { grid-template-columns: 1fr 1fr; gap: 6px; }
    .stat-card { padding: 10px 12px; gap: 8px; }
    .stat-card__icon { width: 32px; height: 32px; font-size: 13px; border-radius: 9px; }
    .stat-card__value { font-size: 17px; }
    .stat-card__label { font-size: 10px; }

    .vedette-grid { grid-template-columns: 1fr 1fr; gap: 8px; padding: 12px; }

    .row__date { width: 38px; padding: 5px 3px; }
    .row__date-day { font-size: 14px; }

    .avis-row__avatar { width: 34px; height: 34px; font-size: 13px; }
    .avis-row__head strong { font-size: 12.5px; }
    .avis-row__comment { font-size: 12px; }

    .btn { font-size: 12px; padding: 8px 14px; }
}
</style>
@endpush