@extends('layouts.dashboard')

@section('title', 'Tableau de bord — DoyaImmo')
@section('page_title', 'Tableau de bord')
@section('page_sub', 'Suivez vos besoins et vos échanges avec les agences')

@section('content')
<div class="view active particulier-dashboard">

    @php
        $prenom    = Auth::user()->prenom ?? 'Client';
        $nom       = Auth::user()->nom ?? '';
        $initiales = strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));

        $heure      = now()->hour;
        $salutation = $heure < 12 ? 'Bonjour' : ($heure < 18 ? 'Bon après-midi' : 'Bonsoir');

        $totalDemandes = $stats['total_demandes'] ?? 0;
        $actifs        = $stats['actifs'] ?? 0;
        $totalOffres   = $stats['total_offres'] ?? 0;
        $nouvelles     = $nouvellesOffresCount ?? 0;
        $totalAvis     = $stats['total_evaluations'] ?? 0;
    @endphp

    {{-- ═══════════════════════════════════════════
         WELCOME
    ═══════════════════════════════════════════ --}}
    <header class="welcome">
        <div class="welcome__left">
            <div class="welcome__avatar">{{ $initiales }}</div>
            <div class="welcome__text">
                <h1>{{ $salutation }}, {{ $prenom }}</h1>
                <span class="welcome__date">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->translatedFormat('l d F Y') }}
                </span>
            </div>
        </div>

        <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust">
            <i class="fa-solid fa-plus"></i> Nouveau besoin
        </a>
    </header>

    {{-- ═══════════════════════════════════════════
         STATS BAR
    ═══════════════════════════════════════════ --}}
    <nav class="stats-bar" aria-label="Vos statistiques">
        <a href="{{ route('particulier.demandes.index') }}" class="stat-item">
            <span class="stat-item__value">{{ $totalDemandes }}</span>
            <span class="stat-item__label">Besoins publiés</span>
            @if($actifs > 0)
                <span class="stat-item__sub">{{ $actifs }} actif{{ $actifs > 1 ? 's' : '' }}</span>
            @endif
        </a>

        <span class="stat-item__sep"></span>

        <a href="{{ route('particulier.propositions.index') }}"
           class="stat-item {{ $nouvelles > 0 ? 'is-highlight' : '' }}">
            <span class="stat-item__value">{{ $totalOffres }}</span>
            <span class="stat-item__label">Offres reçues</span>
            @if($nouvelles > 0)
                <span class="stat-item__sub stat-item__sub--accent">
                    {{ $nouvelles }} en attente
                </span>
            @endif
        </a>

        <span class="stat-item__sep"></span>

        <a href="{{ route('particulier.rendezvous.index') }}" class="stat-item">
            <span class="stat-item__value">{{ $stats['rendezvous_a_venir'] ?? 0 }}</span>
            <span class="stat-item__label">Rendez-vous</span>
        </a>

        <span class="stat-item__sep"></span>

        <a href="{{ route('particulier.evaluations.index') }}" class="stat-item">
            <span class="stat-item__value">{{ $totalAvis }}</span>
            <span class="stat-item__label">Avis donnés</span>
        </a>
    </nav>

    {{-- ═══════════════════════════════════════════
         CARTE : PROCHAIN RDV (priorité 1)
    ═══════════════════════════════════════════ --}}
    @if($prochainRendezVous)
        @php
            $bien   = $prochainRendezVous->proposition->bien ?? null;
            $agence = $prochainRendezVous->agence;
            $statutValue = is_object($prochainRendezVous->statut)
                ? $prochainRendezVous->statut->value
                : $prochainRendezVous->statut;
            $statutLabel = is_object($prochainRendezVous->statut)
                ? $prochainRendezVous->statut->label()
                : ucfirst($prochainRendezVous->statut);

            $dateVisite = \Carbon\Carbon::parse($prochainRendezVous->date_visite);
            $isToday    = $dateVisite->isToday();
            $isTomorrow = $dateVisite->isTomorrow();
            $daysUntil  = now()->startOfDay()->diffInDays($dateVisite->startOfDay(), false);
        @endphp

        <section class="action-card action-card--rdv">

            <header class="action-card__head">
                <span class="action-card__label">
                    <i class="fa-solid fa-calendar-check"></i>
                    @if($isToday)
                        Rendez-vous aujourd'hui
                    @elseif($isTomorrow)
                        Rendez-vous demain
                    @else
                        Prochain rendez-vous
                    @endif
                </span>
                <span class="offre-status offre-status--{{ $statutValue }}">
                    {{ $statutLabel }}
                </span>
            </header>

            <div class="action-card__body action-card__body--rdv">

                <div class="rdv-date-box {{ $isToday ? 'rdv-date-box--today' : '' }}">
                    <span class="rdv-date-box__day">{{ $dateVisite->format('d') }}</span>
                    <span class="rdv-date-box__month">{{ strtoupper($dateVisite->translatedFormat('M')) }}</span>
                </div>

                <div class="action-card__content">
                    <h3 class="action-card__title">
                        {{ $bien->titre ?? 'Visite' }}
                    </h3>

                    <div class="rdv-meta-row">
                        <span>
                            <i class="fa-regular fa-building"></i>
                            {{ $agence->nom_agence ?? 'Agence' }}
                        </span>
                        <span>
                            <i class="fa-regular fa-clock"></i>
                            {{ \Carbon\Carbon::parse($prochainRendezVous->heure_visite)->format('H:i') }}
                        </span>
                    </div>

                    @if($bien && $bien->adresse)
                        <div class="rdv-meta-row rdv-meta-row--muted">
                            <span>
                                <i class="fa-solid fa-location-dot"></i>
                                {{ $bien->adresse }}
                            </span>
                        </div>
                    @endif

                    @if($isToday)
                        <span class="rdv-urgency rdv-urgency--today">
                            <i class="fa-solid fa-fire"></i> Aujourd'hui
                        </span>
                    @elseif($isTomorrow)
                        <span class="rdv-urgency rdv-urgency--soon">
                            <i class="fa-solid fa-hourglass-half"></i> Demain
                        </span>
                    @elseif($daysUntil > 0 && $daysUntil <= 3)
                        <span class="rdv-urgency rdv-urgency--soon">
                            <i class="fa-solid fa-hourglass-half"></i> Dans {{ $daysUntil }} jour{{ $daysUntil > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
            </div>

            <footer class="action-card__foot">
                <a href="{{ route('particulier.rendezvous.show', $prochainRendezVous) }}"
                   class="btn btn-rust btn-sm">
                    <i class="fa-solid fa-eye"></i> Voir le rendez-vous
                </a>
            </footer>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         CARTE : NOUVELLES OFFRES (priorité 2)
    ═══════════════════════════════════════════ --}}
    @if($meilleureOffre && $nouvelles > 0)
        @php
            $bien   = $meilleureOffre->bien;
            $agence = $meilleureOffre->agence;

            $cover = $bien && $bien->medias->where('type_media', 'image')->first()
                ? asset('storage/' . $bien->medias->where('type_media', 'image')->first()->fichier)
                : null;

            $initial = strtoupper(mb_substr($agence->nom_agence ?? 'A', 0, 1));
            $autres  = $nouvelles - 1;
        @endphp

        <section class="action-card action-card--offer">

            <header class="action-card__head">
                <span class="action-card__label">
                    <i class="fa-solid fa-bell"></i>
                    @if($nouvelles > 1)
                        {{ $nouvelles }} nouvelles offres reçues
                    @else
                        Nouvelle offre reçue
                    @endif
                </span>
            </header>

            @if($autres > 0)
                <div class="best-offer-tag">
                    <i class="fa-solid fa-trophy"></i>
                    Meilleure correspondance avec votre recherche
                </div>
            @endif

            <div class="action-card__body">
                <div class="action-card__media">
                    @if($cover)
                        <img src="{{ $cover }}" alt="{{ $bien->titre ?? 'Bien' }}" loading="lazy">
                    @else
                        <div class="action-card__media-placeholder">
                            <i class="fa-regular fa-image"></i>
                        </div>
                    @endif
                </div>

                <div class="action-card__content">
                    <div class="action-card__agency">
                        <div class="agency-avatar-sm">{{ $initial }}</div>
                        <div class="agency-meta">
                            <strong>{{ $agence->nom_agence ?? 'Agence' }}</strong>
                            <span>
                                <i class="fa-solid fa-star"></i>
                                {{ number_format($agence->note_moyenne ?? 0, 1) }}
                                <span class="agency-reviews">({{ $agence->evaluations->count() ?? 0 }} avis)</span>
                            </span>
                        </div>
                    </div>

                    <h3 class="action-card__title">
                        {{ $bien->titre ?? 'Bien sans titre' }}
                        @if($bien && $bien->adresse)
                            <span class="action-card__title-loc">— {{ $bien->adresse }}</span>
                        @endif
                    </h3>

                    <div class="action-card__price">
                        {{ number_format($meilleureOffre->prix_propose, 0, ',', ' ') }}
                        <small>FCFA</small>
                    </div>

                    <div class="action-card__specs">
                        @if($bien && $bien->surface)
                            <span><i class="fa-solid fa-vector-square"></i> {{ $bien->surface }} m²</span>
                        @endif
                        @if($bien && $bien->nombre_chambres)
                            <span><i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                        @endif
                    </div>
                </div>
            </div>

            @if($autres > 0)
                <a href="{{ route('particulier.propositions.index') }}" class="other-offers-bar">
                    <div class="other-offers-bar__icon">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <div class="other-offers-bar__text">
                        <strong>+ {{ $autres }} autre{{ $autres > 1 ? 's' : '' }} offre{{ $autres > 1 ? 's' : '' }}</strong>
                        <span>en attente de votre décision</span>
                    </div>
                    <span class="other-offers-bar__link">
                        Voir <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>
            @endif

            <footer class="action-card__foot">
                <a href="{{ route('particulier.propositions.show', $meilleureOffre->id) }}"
                   class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-eye"></i> Détails
                </a>

                @if($autres > 0)
                    <a href="{{ route('particulier.propositions.index') }}"
                       class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-list"></i> Voir les {{ $nouvelles }}
                    </a>
                @endif

                <form action="{{ route('particulier.propositions.selectionner', $meilleureOffre->id) }}"
                      method="POST" style="margin:0;">
                    @csrf
                    <button type="submit" class="btn btn-rust btn-sm"
                            onclick="return confirm('✅ Choisir cette offre ? Cela clôturera les autres offres en attente.')">
                        <i class="fa-solid fa-check"></i> Choisir
                    </button>
                </form>
            </footer>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         DEUX COLONNES
    ═══════════════════════════════════════════ --}}
    <div class="two-columns">

        {{-- ═══ BESOINS ACTIFS ═══ --}}
        <section class="panel">
            <header class="panel__head">
                <h3>Mes besoins actifs</h3>
                <a href="{{ route('particulier.demandes.index') }}" class="panel__see-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </header>

            <div class="panel__body">
                @forelse($derniersBesoins as $demande)
                    @php
                        $statutValue = is_object($demande->statut)
                            ? $demande->statut->value
                            : $demande->statut;
                        $statutLabel = is_object($demande->statut)
                            ? $demande->statut->label()
                            : ucfirst($demande->statut);
                        $nbOffres = $demande->propositions->count();
                    @endphp

                    <a href="{{ route('particulier.demandes.show', $demande) }}" class="row">
                        <div class="row__main">
                            <div class="row__title">{{ $demande->type_bien->label() }}</div>
                            <div class="row__meta">
                                <span><i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}</span>
                                <span><i class="fa-solid fa-wallet"></i> {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F</span>
                            </div>
                        </div>

                        <div class="row__right">
                            @if($nbOffres > 0)
                                <span class="row__count">{{ $nbOffres }}</span>
                            @endif
                            <span class="offre-status offre-status--{{ $statutValue }}">
                                {{ $statutLabel }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="empty-box">
                        <div class="empty-box__icon">
                            <i class="fa-regular fa-house"></i>
                        </div>
                        <h4>Aucun besoin actif</h4>
                        <p>Publiez un besoin pour recevoir des propositions d'agences.</p>
                        <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust btn-sm">
                            <i class="fa-solid fa-plus"></i> Publier un besoin
                        </a>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ═══ AUTRES RDV ═══ --}}
        <section class="panel">
            <header class="panel__head">
                <h3>Autres rendez-vous</h3>
                <a href="{{ route('particulier.rendezvous.index') }}" class="panel__see-all">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </header>

            <div class="panel__body">
                @forelse($prochainsRendezVous as $rdv)
                    @php
                        $statutValue = is_object($rdv->statut)
                            ? $rdv->statut->value
                            : $rdv->statut;
                        $statutLabel = is_object($rdv->statut)
                            ? $rdv->statut->label()
                            : ucfirst($rdv->statut);
                    @endphp

                    <a href="{{ route('particulier.rendezvous.show', $rdv) }}" class="row row--rdv">
                        <div class="row__date">
                            <span class="row__date-day">{{ $rdv->date_visite->format('d') }}</span>
                            <span class="row__date-month">{{ strtoupper($rdv->date_visite->translatedFormat('M')) }}</span>
                        </div>

                        <div class="row__main">
                            <div class="row__title">
                                {{ $rdv->proposition->bien->titre ?? 'Visite' }}
                            </div>
                            <div class="row__meta">
                                <span><i class="fa-regular fa-building"></i> {{ $rdv->agence->nom_agence }}</span>
                                <span><i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}</span>
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
                        <h4>Aucun autre rendez-vous</h4>
                        <p>Vos autres rendez-vous à venir apparaîtront ici.</p>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DASHBOARD PARTICULIER
   ═══════════════════════════════════════════════════════════ */

.particulier-dashboard {
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
    margin-bottom: 16px;
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
.welcome__text { min-width: 0; }
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

/* ═══ STATS BAR ═══ */
.stats-bar {
    display: flex; align-items: center;
    padding: 14px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 16px;
    overflow-x: auto;
    scrollbar-width: thin;
}
.stats-bar::-webkit-scrollbar { height: 3px; }
.stats-bar::-webkit-scrollbar-thumb { background: var(--border); border-radius: 999px; }

.stat-item {
    display: flex; flex-direction: column; gap: 2px;
    flex: 1; min-width: 110px;
    padding: 4px 12px;
    text-decoration: none; color: inherit;
    border-radius: 10px;
    transition: background .2s;
}
.stat-item:hover { background: var(--surface); }
.stat-item__value {
    font-family: var(--display);
    font-size: 22px; font-weight: 800; line-height: 1;
    color: var(--text);
}
.stat-item__label { font-size: 11.5px; color: var(--muted); font-weight: 500; }
.stat-item__sub {
    font-size: 11px; color: var(--text-soft);
    font-weight: 600; margin-top: 1px;
}
.stat-item__sub--accent { color: var(--rust); font-weight: 700; }
.stat-item.is-highlight .stat-item__value { color: var(--rust); }
.stat-item__sep {
    width: 1px; height: 32px;
    background: var(--border);
    flex-shrink: 0;
}

/* ═══ ACTION CARDS ═══ */
.action-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
    overflow: hidden;
}
.action-card--offer { border-left: 3px solid var(--rust); }
.action-card--rdv   { border-left: 3px solid var(--c-info); }

.action-card__head {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px;
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    background: #FAFBFC;
    flex-wrap: wrap;
}
.action-card__label {
    display: inline-flex; align-items: center; gap: 7px;
    font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .4px;
    color: var(--muted);
}
.action-card__label i { font-size: 11px; }
.action-card--offer .action-card__label { color: var(--rust); }
.action-card--rdv   .action-card__label { color: var(--c-info); }

.action-card__body {
    display: grid;
    grid-template-columns: 130px 1fr;
    gap: 16px;
    padding: 16px 18px;
}
.action-card__body--rdv {
    grid-template-columns: auto 1fr;
    align-items: center;
}

.action-card__media {
    position: relative;
    aspect-ratio: 4 / 3;
    border-radius: 10px;
    overflow: hidden;
    background: var(--surface);
    border: 1px solid var(--border);
}
.action-card__media img {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
}
.action-card__media-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted); font-size: 28px; opacity: .4;
}

.action-card__content {
    display: flex; flex-direction: column;
    gap: 8px; min-width: 0;
}

.action-card__agency { display: flex; align-items: center; gap: 9px; }
.agency-avatar-sm {
    width: 32px; height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--display);
    font-size: 13px; font-weight: 700;
    flex-shrink: 0;
}
.agency-meta { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.agency-meta strong {
    font-size: 12.5px; font-weight: 700;
    color: var(--text);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.agency-meta span {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11.5px; color: var(--text-soft);
}
.agency-meta i { color: var(--c-gold); font-size: 10px; }
.agency-reviews { color: var(--muted); }

.action-card__title {
    font-family: var(--display);
    font-size: 15px; font-weight: 700;
    margin: 0; color: var(--text);
    line-height: 1.3;
}
.action-card__title-loc {
    color: var(--muted); font-weight: 400; font-size: 13px;
}

.action-card__price {
    font-family: var(--display);
    font-size: 20px; font-weight: 800;
    color: var(--rust); line-height: 1.1;
}
.action-card__price small {
    font-size: 11px; font-weight: 600;
    opacity: .7; margin-left: 2px;
}

.action-card__specs {
    display: flex; flex-wrap: wrap; gap: 4px 14px;
    font-size: 12px; color: var(--muted);
}
.action-card__specs span { display: inline-flex; align-items: center; gap: 5px; }
.action-card__specs i { font-size: 10px; color: var(--rust); opacity: .8; }

.action-card__foot {
    display: flex; align-items: center; justify-content: flex-end;
    gap: 8px;
    padding: 12px 18px;
    border-top: 1px solid var(--border);
    background: #FAFBFC;
    flex-wrap: wrap;
}
.action-card__foot form { margin: 0; }

/* ═══ BEST OFFER TAG ═══ */
.best-offer-tag {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    margin: 0 18px;
    padding: 6px 14px;
    background: linear-gradient(135deg, #FFF8E1, #FFECB3);
    color: #B26A00;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    border: 1px solid #FFE0B2;
    width: fit-content;
}
.best-offer-tag i { font-size: 11px; color: var(--c-gold); }

/* ═══ OTHER OFFERS BAR ═══ */
.other-offers-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    border-top: 1px dashed var(--border);
    background: #FFFDF5;
    text-decoration: none;
    color: inherit;
    transition: background .2s;
}
.other-offers-bar:hover { background: #FFF9E5; }

.other-offers-bar__icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    background: var(--rust-soft);
    color: var(--rust);
    font-size: 15px;
    flex-shrink: 0;
}
.other-offers-bar__text {
    flex: 1; min-width: 0;
    display: flex; flex-direction: column; gap: 1px;
}
.other-offers-bar__text strong {
    font-family: var(--display);
    font-size: 13.5px; font-weight: 700;
    color: var(--text);
}
.other-offers-bar__text span {
    font-size: 11.5px; color: var(--muted);
}
.other-offers-bar__link {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 13px;
    background: #fff;
    color: var(--rust);
    border: 1.5px solid var(--rust);
    border-radius: 9px;
    font-size: 12px; font-weight: 700;
    transition: all .2s;
    white-space: nowrap;
    flex-shrink: 0;
}
.other-offers-bar:hover .other-offers-bar__link {
    background: var(--rust);
    color: #fff;
    gap: 9px;
}
.other-offers-bar__link i { font-size: 10px; }

/* ═══ RDV DATE BOX ═══ */
.rdv-date-box {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 2px;
    width: 62px;
    padding: 10px 6px;
    background: var(--c-info-bg);
    border-radius: 12px;
    border: 1px solid #BBDEFB;
    flex-shrink: 0;
}
.rdv-date-box--today {
    background: var(--c-warning-bg);
    border-color: #FFE0B2;
}
.rdv-date-box__day {
    font-family: var(--display);
    font-size: 24px; font-weight: 800; line-height: 1;
    color: var(--c-info);
}
.rdv-date-box--today .rdv-date-box__day { color: var(--c-warning); }
.rdv-date-box__month {
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--c-info); opacity: .85;
}
.rdv-date-box--today .rdv-date-box__month { color: var(--c-warning); }

.rdv-meta-row {
    display: flex; flex-wrap: wrap; gap: 4px 16px;
    font-size: 12.5px; color: var(--text-soft);
}
.rdv-meta-row i {
    color: var(--rust); font-size: 11px;
    margin-right: 5px; opacity: .8;
}
.rdv-meta-row--muted { color: var(--muted); }

/* ═══ RDV URGENCY ═══ */
.rdv-urgency {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11px; font-weight: 700;
    width: fit-content;
    margin-top: 2px;
}
.rdv-urgency--today {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border: 1px solid #FFCDD2;
}
.rdv-urgency--soon {
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border: 1px solid #FFE0B2;
}
.rdv-urgency i { font-size: 10px; }

/* ═══ TWO COLUMNS ═══ */
.two-columns {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    display: flex; flex-direction: column;
}
.panel__head {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px;
    padding: 13px 18px;
    border-bottom: 1px solid var(--border);
    background: #FAFBFC;
}
.panel__head h3 {
    font-family: var(--display);
    font-size: 14px; font-weight: 700;
    margin: 0; color: var(--text);
}
.panel__see-all {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11.5px; font-weight: 600;
    color: var(--rust);
    text-decoration: none;
    transition: gap .2s;
}
.panel__see-all:hover { gap: 8px; }
.panel__see-all i { font-size: 9px; }
.panel__body { display: flex; flex-direction: column; flex: 1; }

/* ═══ ROWS ═══ */
.row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px;
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    text-decoration: none; color: inherit;
    transition: background .15s;
}
.row:last-child { border-bottom: none; }
.row:hover { background: var(--surface); }
.row--rdv { justify-content: flex-start; gap: 12px; }

.row__date {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    width: 42px; padding: 5px 3px;
    background: var(--surface);
    border-radius: 9px;
    border: 1px solid var(--border);
    flex-shrink: 0;
}
.row__date-day {
    font-family: var(--display);
    font-size: 16px; font-weight: 800; line-height: 1;
    color: var(--text);
}
.row__date-month {
    font-size: 9px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .5px;
    color: var(--muted); margin-top: 1px;
}

.row__main { flex: 1; min-width: 0; }
.row__title {
    font-family: var(--display);
    font-size: 13.5px; font-weight: 700;
    color: var(--text);
    margin-bottom: 2px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.row__meta {
    display: flex; flex-wrap: wrap; gap: 3px 12px;
    font-size: 11.5px; color: var(--muted);
}
.row__meta i { font-size: 10px; margin-right: 4px; opacity: .8; }
.row__meta i.fa-location-dot,
.row__meta i.fa-wallet,
.row__meta i.fa-building,
.row__meta i.fa-clock { color: var(--rust); }

.row__right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
.row__count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 22px; height: 22px;
    padding: 0 6px;
    background: var(--rust);
    color: #fff;
    border-radius: 999px;
    font-family: var(--display);
    font-size: 11px; font-weight: 800;
}

/* ═══ STATUTS ═══ */
.offre-status {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .3px;
    white-space: nowrap;
}
.offre-status--en_attente,
.offre-status--planifie { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--en_cours,
.offre-status--confirme  { background: var(--c-info-bg);    color: var(--c-info); }
.offre-status--terminee,
.offre-status--termine,
.offre-status--acceptee  { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--annulee,
.offre-status--annule,
.offre-status--refusee   { background: var(--c-danger-bg);  color: var(--c-danger); }

/* ═══ EMPTY BOX ═══ */
.empty-box {
    text-align: center; padding: 30px 20px;
    color: var(--muted);
}
.empty-box__icon {
    width: 52px; height: 52px;
    margin: 0 auto 12px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: var(--surface);
    color: var(--muted);
    font-size: 20px;
}
.empty-box h4 {
    font-family: var(--display);
    font-size: 13.5px; font-weight: 700;
    color: var(--text); margin: 0 0 4px;
}
.empty-box p {
    font-size: 12px; color: var(--muted);
    margin: 0 0 14px;
    max-width: 300px; margin-inline: auto;
}

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex; align-items: center; gap: 7px;
    padding: 9px 18px;
    border-radius: 10px;
    font-size: 13px; font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust); color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523; color: #fff;
    border-color: #9A4523;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(180, 83, 42, .25);
}
.btn-ghost {
    background: transparent; color: var(--text-soft);
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
    .two-columns { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .welcome {
        flex-direction: column; align-items: stretch;
        gap: 12px; padding: 16px;
    }
    .welcome__text h1 { font-size: 17px; }
    .welcome .btn { justify-content: center; }

    .stats-bar { padding: 10px 14px; overflow-x: auto; }
    .stat-item { min-width: 96px; padding: 3px 8px; }
    .stat-item__value { font-size: 18px; }
    .stat-item__label { font-size: 10.5px; }

    .action-card__body { grid-template-columns: 1fr; gap: 12px; }
    .action-card__media { aspect-ratio: 16 / 9; }
    .action-card__body--rdv { grid-template-columns: auto 1fr; }

    .action-card__foot { justify-content: stretch; }
    .action-card__foot .btn,
    .action-card__foot form { flex: 1; }
    .action-card__foot form .btn { width: 100%; justify-content: center; }

    .best-offer-tag { margin: 0 14px; font-size: 10.5px; padding: 5px 12px; }

    .other-offers-bar { flex-wrap: wrap; gap: 10px; }
    .other-offers-bar__link { width: 100%; justify-content: center; }
}

@media (max-width: 480px) {
    .welcome { padding: 14px; }
    .welcome__avatar { width: 44px; height: 44px; font-size: 16px; }
    .welcome__text h1 { font-size: 16px; }

    .stat-item { min-width: 84px; }
    .stat-item__value { font-size: 16px; }
    .stat-item__label { font-size: 10px; }

    .action-card__head { padding: 10px 14px; }
    .action-card__label { font-size: 11px; }
    .action-card__body { padding: 14px; }
    .action-card__foot { padding: 10px 14px; }

    .action-card__title { font-size: 14px; }
    .action-card__price { font-size: 18px; }

    .rdv-date-box { width: 54px; padding: 8px 4px; }
    .rdv-date-box__day { font-size: 20px; }

    .panel__head { padding: 11px 14px; }
    .panel__head h3 { font-size: 13px; }
    .row { padding: 11px 14px; }
    .row__title { font-size: 13px; }
    .row__date { width: 38px; }
    .row__date-day { font-size: 14px; }

    .btn { font-size: 12px; padding: 8px 14px; }
}
</style>
@endpush