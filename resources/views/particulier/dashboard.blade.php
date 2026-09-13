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
        $heure     = now()->hour;
        $salutation = $heure < 12 ? 'Bonjour' : ($heure < 18 ? 'Bon après-midi' : 'Bonsoir');

        $totalDemandes = $stats['total_demandes'] ?? 0;
        $actifs        = $stats['actifs'] ?? 0;
        $totalOffres   = $stats['total_offres'] ?? 0;
        $nouvelles     = $nouvellesOffresCount ?? 0;
        $totalAvis     = $stats['total_evaluations'] ?? 0;
        $totalRdv      = $stats['rendezvous_a_venir'] ?? 0;

        // ✅ Logique : l'utilisateur a-t-il de l'activité ?
        $aDeLActivite = ($totalDemandes + $totalOffres + $totalRdv + $totalAvis) > 0;
    @endphp

    {{-- ═══════════════════════════════════════════
         HERO DE BIENVENUE (desktop)
    ═══════════════════════════════════════════ --}}
    <section class="desk-hero">
        <div class="desk-hero__left">
            <div class="desk-hero__avatar">{{ $initiales }}</div>
            <div class="desk-hero__text">
                <h1 class="desk-hero__title">{{ $salutation }}, {{ $prenom }} </h1>
                <span class="desk-hero__date">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->translatedFormat('l d F Y') }}
                </span>
            </div>
        </div>

        <a href="{{ route('particulier.demandes.create') }}" class="desk-hero__cta">
            <i class="fa-solid fa-plus"></i>
            Nouveau besoin
        </a>
    </section>

    {{-- ═══════════════════════════════════════════
         STATS — 4 CARDS (desktop)
    ═══════════════════════════════════════════ --}}
    <section class="desk-stats">
        <a href="{{ route('particulier.demandes.index') }}" class="desk-stat desk-stat--rust">
            <div class="desk-stat__icon">
                <i class="fa-solid fa-house-circle-check"></i>
            </div>
            <div class="desk-stat__body">
                <span class="desk-stat__value">{{ $totalDemandes }}</span>
                <span class="desk-stat__label">Besoins actifs</span>
                @if($actifs > 0)
                    <span class="desk-stat__sub">{{ $actifs }} en cours</span>
                @endif
            </div>
        </a>

        <a href="{{ route('particulier.propositions.index') }}"
           class="desk-stat desk-stat--blue {{ $nouvelles > 0 ? 'has-badge' : '' }}">
            <div class="desk-stat__icon">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <div class="desk-stat__body">
                <span class="desk-stat__value">{{ $totalOffres }}</span>
                <span class="desk-stat__label">Offres reçues</span>
                @if($nouvelles > 0)
                    <span class="desk-stat__sub desk-stat__sub--accent">
                        <i class="fa-solid fa-circle"></i>
                        {{ $nouvelles }} en attente
                    </span>
                @endif
            </div>
            @if($nouvelles > 0)
                <span class="desk-stat__badge">{{ $nouvelles }}</span>
            @endif
        </a>

        <a href="{{ route('particulier.rendezvous.index') }}" class="desk-stat desk-stat--gold">
            <div class="desk-stat__icon">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="desk-stat__body">
                <span class="desk-stat__value">{{ $totalRdv }}</span>
                <span class="desk-stat__label">Rendez-vous</span>
                <span class="desk-stat__sub">à venir</span>
            </div>
        </a>

        <a href="{{ route('particulier.evaluations.index') }}" class="desk-stat desk-stat--green">
            <div class="desk-stat__icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="desk-stat__body">
                <span class="desk-stat__value">{{ $totalAvis }}</span>
                <span class="desk-stat__label">Avis donnés</span>
            </div>
        </a>
    </section>

    {{-- ═══════════════════════════════════════════
         BANNIÈRE NOUVELLES OFFRES
    ═══════════════════════════════════════════ --}}
    @if($nouvelles > 0)
        <a href="{{ route('particulier.propositions.index') }}" class="desk-alert">
            <div class="desk-alert__icon">
                <i class="fa-solid fa-bell"></i>
            </div>
            <div class="desk-alert__text">
                <strong>{{ $nouvelles }} nouvelle{{ $nouvelles > 1 ? 's' : '' }} offre{{ $nouvelles > 1 ? 's' : '' }}</strong>
                <span>en attente de votre décision</span>
            </div>
            <span class="desk-alert__cta">
                Voir les offres
                <i class="fa-solid fa-arrow-right"></i>
            </span>
        </a>
    @endif

    {{-- ═══════════════════════════════════════════
         LAYOUT 2 COLONNES (desktop)
    ═══════════════════════════════════════════ --}}
    <div class="desk-layout">

        {{-- ════ COLONNE PRINCIPALE ════ --}}
        <div class="desk-layout__main">

            {{-- Prochain RDV --}}
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

                <div class="desk-section-title">
                    <span>Prochain rendez-vous</span>
                </div>

                <a href="{{ route('particulier.rendezvous.show', $prochainRendezVous) }}" class="desk-rdv">
                    <div class="desk-rdv__date {{ $isToday ? 'is-today' : '' }}">
                        <span class="desk-rdv__day">{{ $dateVisite->format('d') }}</span>
                        <span class="desk-rdv__month">{{ strtoupper($dateVisite->translatedFormat('M')) }}</span>
                    </div>

                    <div class="desk-rdv__content">
                        <div class="desk-rdv__head">
                            <h3 class="desk-rdv__title">{{ $bien->titre ?? 'Visite' }}</h3>
                            <div class="desk-rdv__badges">
                                <span class="desk-badge desk-badge--{{ $statutValue }}">{{ $statutLabel }}</span>
                                @if($isToday)
                                    <span class="desk-badge desk-badge--danger"><i class="fa-solid fa-fire"></i> Aujourd'hui</span>
                                @elseif($isTomorrow)
                                    <span class="desk-badge desk-badge--warning">Demain</span>
                                @elseif($daysUntil > 0 && $daysUntil <= 3)
                                    <span class="desk-badge desk-badge--warning">Dans {{ $daysUntil }}j</span>
                                @endif
                            </div>
                        </div>

                        <div class="desk-rdv__meta">
                            <span><i class="fa-regular fa-building"></i> {{ $agence->nom_agence ?? 'Agence' }}</span>
                            <span><i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($prochainRendezVous->heure_visite)->format('H:i') }}</span>
                            @if($bien && $bien->adresse)
                                <span><i class="fa-solid fa-location-dot"></i> {{ $bien->adresse }}</span>
                            @endif
                        </div>
                    </div>

                    <i class="fa-solid fa-chevron-right desk-rdv__arrow"></i>
                </a>
            @endif

            {{-- Meilleure offre --}}
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

                <div class="desk-section-title">
                    <span>Meilleure offre</span>
                    @if($autres > 0)
                        <a href="{{ route('particulier.propositions.index') }}" class="desk-section-title__link">
                            Tout voir <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    @endif
                </div>

                <div class="desk-offer">
                    <div class="desk-offer__media">
                        @if($cover)
                            <img src="{{ $cover }}" alt="{{ $bien->titre ?? 'Bien' }}" loading="lazy">
                        @else
                            <div class="desk-offer__media-empty">
                                <i class="fa-regular fa-image"></i>
                            </div>
                        @endif

                        @if($autres > 0)
                            <span class="desk-offer__tag">
                                <i class="fa-solid fa-trophy"></i> Meilleure correspondance
                            </span>
                        @endif
                    </div>

                    <div class="desk-offer__body">
                        <div class="desk-offer__agency">
                            <div class="desk-offer__agency-avatar">{{ $initial }}</div>
                            <div class="desk-offer__agency-info">
                                <strong>{{ $agence->nom_agence ?? 'Agence' }}</strong>
                                <span>
                                    <i class="fa-solid fa-star"></i>
                                    {{ number_format($agence->note_moyenne ?? 0, 1) }}
                                    <span class="desk-offer__reviews">({{ $agence->evaluations->count() ?? 0 }} avis)</span>
                                </span>
                            </div>
                        </div>

                        <h3 class="desk-offer__title">{{ $bien->titre ?? 'Bien sans titre' }}</h3>

                        @if($bien && $bien->adresse)
                            <div class="desk-offer__address">
                                <i class="fa-solid fa-location-dot"></i> {{ $bien->adresse }}
                            </div>
                        @endif

                        <div class="desk-offer__price">
                            {{ number_format($meilleureOffre->prix_propose, 0, ',', ' ') }}
                            <small>FCFA</small>
                        </div>

                        <div class="desk-offer__specs">
                            @if($bien && $bien->surface)
                                <span><i class="fa-solid fa-vector-square"></i> {{ $bien->surface }} m²</span>
                            @endif
                            @if($bien && $bien->nombre_chambres)
                                <span><i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                            @endif
                        </div>

                        <div class="desk-offer__actions">
                            <a href="{{ route('particulier.propositions.show', $meilleureOffre->id) }}" class="desk-btn desk-btn--ghost">
                                <i class="fa-solid fa-eye"></i> Détails
                            </a>
                            <form action="{{ route('particulier.propositions.selectionner', $meilleureOffre->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="desk-btn desk-btn--primary"
                                        onclick="return confirm('Choisir cette offre ? Cela clôturera les autres offres en attente.')">
                                    <i class="fa-solid fa-check"></i> Choisir cette offre
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Mes besoins actifs --}}
            <div class="desk-section-title">
                <span>Mes besoins actifs</span>
                <a href="{{ route('particulier.demandes.index') }}" class="desk-section-title__link">
                    Tout voir <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            @if($derniersBesoins->count() > 0)
                <div class="desk-list">
                    @foreach($derniersBesoins as $demande)
                        @php
                            $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
                            $statutLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);
                            $nbOffres = $demande->propositions->count();
                        @endphp
                        <a href="{{ route('particulier.demandes.show', $demande) }}" class="desk-list__item">
                            <div class="desk-list__icon desk-list__icon--rust">
                                <i class="fa-solid fa-house"></i>
                            </div>
                            <div class="desk-list__content">
                                <span class="desk-list__title">{{ $demande->type_bien->label() }}</span>
                                <div class="desk-list__sub">
                                    <span><i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}</span>
                                    <span class="desk-list__dot">·</span>
                                    <span><i class="fa-solid fa-wallet"></i> {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F</span>
                                </div>
                            </div>
                            <div class="desk-list__right">
                                @if($nbOffres > 0)
                                    <span class="desk-list__count">{{ $nbOffres }}</span>
                                @endif
                                <span class="desk-badge desk-badge--{{ $statutValue }}">{{ $statutLabel }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-right desk-list__chevron"></i>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="desk-empty">
                    <div class="desk-empty__icon">
                        <i class="fa-regular fa-house"></i>
                    </div>
                    <h4>Aucun besoin actif</h4>
                    <p>Publiez un besoin pour recevoir des propositions d'agences.</p>
                    <a href="{{ route('particulier.demandes.create') }}" class="desk-btn desk-btn--primary">
                        <i class="fa-solid fa-plus"></i> Publier un besoin
                    </a>
                </div>
            @endif
        </div>

        {{-- ════ SIDEBAR (desktop) ════ --}}
        <aside class="desk-layout__side">

            <div class="desk-side-card">
                <div class="desk-side-card__head">
                    <h4>Prochains rendez-vous</h4>
                    <a href="{{ route('particulier.rendezvous.index') }}" class="desk-side-card__link">Voir tout</a>
                </div>

                @if($prochainsRendezVous->count() > 0)
                    <div class="desk-side-list">
                        @foreach($prochainsRendezVous as $rdv)
                            <a href="{{ route('particulier.rendezvous.show', $rdv) }}" class="desk-side-item">
                                <div class="desk-side-item__date">
                                    <span class="desk-side-item__day">{{ $rdv->date_visite->format('d') }}</span>
                                    <span class="desk-side-item__month">{{ strtoupper($rdv->date_visite->translatedFormat('M')) }}</span>
                                </div>
                                <div class="desk-side-item__body">
                                    <span class="desk-side-item__title">{{ $rdv->proposition->bien->titre ?? 'Visite' }}</span>
                                    <span class="desk-side-item__meta">
                                        {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                                        · {{ $rdv->agence->nom_agence }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="desk-side-empty">
                        <i class="fa-regular fa-calendar"></i>
                        <span>Aucun RDV à venir</span>
                    </div>
                @endif
            </div>

            <div class="desk-side-card">
                <div class="desk-side-card__head">
                    <h4>Raccourcis</h4>
                </div>
                <div class="desk-shortcuts">
                    <a href="{{ route('particulier.demandes.create') }}" class="desk-shortcut">
                        <div class="desk-shortcut__icon desk-shortcut__icon--rust"><i class="fa-solid fa-plus"></i></div>
                        <span>Publier un besoin</span>
                    </a>
                    <a href="{{ route('particulier.propositions.index') }}" class="desk-shortcut">
                        <div class="desk-shortcut__icon desk-shortcut__icon--blue"><i class="fa-solid fa-file-invoice"></i></div>
                        <span>Voir les offres</span>
                    </a>
                    <a href="{{ route('particulier.rendezvous.index') }}" class="desk-shortcut">
                        <div class="desk-shortcut__icon desk-shortcut__icon--gold"><i class="fa-solid fa-calendar-days"></i></div>
                        <span>Mes rendez-vous</span>
                    </a>
                    <a href="{{ route('particulier.historique') }}" class="desk-shortcut">
                        <div class="desk-shortcut__icon desk-shortcut__icon--gray"><i class="fa-solid fa-clock-rotate-left"></i></div>
                        <span>Historique</span>
                    </a>
                </div>
            </div>
        </aside>
    </div>

    {{-- ═══════════════════════════════════════════
         📱 VERSION MOBILE — Logique conditionnelle
    ═══════════════════════════════════════════ --}}
    <div class="ios-mobile-only">

        {{-- Hero simple --}}
        <div class="ios-hero-mobile">
            <h1 class="ios-hero-mobile__title">{{ $salutation }}, {{ $prenom }}</h1>
            <span class="ios-hero-mobile__date">
                {{ now()->translatedFormat('l d F Y') }}
            </span>
        </div>

        @if($aDeLActivite)

            {{-- ═══ UTILISATEUR ACTIF ═══ --}}

            {{-- Stats en 1 ligne --}}
            <div class="ios-stats-line">
                <a href="{{ route('particulier.demandes.index') }}" class="ios-stat-line">
                    <strong>{{ $totalDemandes }}</strong>
                    <span>Besoins</span>
                </a>
                <a href="{{ route('particulier.propositions.index') }}" class="ios-stat-line">
                    <strong>{{ $totalOffres }}</strong>
                    <span>Offres</span>
                </a>
                <a href="{{ route('particulier.rendezvous.index') }}" class="ios-stat-line">
                    <strong>{{ $totalRdv }}</strong>
                    <span>RDV</span>
                </a>
                <a href="{{ route('particulier.evaluations.index') }}" class="ios-stat-line">
                    <strong>{{ $totalAvis }}</strong>
                    <span>Avis</span>
                </a>
            </div>

            {{-- Mes besoins --}}
            @if($derniersBesoins->count() > 0)
                <div class="ios-section-header">
                    Mes besoins
                    <a href="{{ route('particulier.demandes.index') }}" class="ios-section-header__link">
                        Tout voir
                    </a>
                </div>

                <div class="ios-list-mobile">
                    @foreach($derniersBesoins as $demande)
                        @php
                            $nbOffres = $demande->propositions->count();
                        @endphp

                        <a href="{{ route('particulier.demandes.show', $demande) }}" class="ios-item-mobile">
                            <div class="ios-item-mobile__icon">
                                <i class="fa-solid fa-house"></i>
                            </div>

                            <div class="ios-item-mobile__body">
                                <span class="ios-item-mobile__title">
                                    {{ $demande->type_bien->label() }}
                                </span>
                                <span class="ios-item-mobile__meta">
                                    {{ $demande->zone_recherchee }}
                                    @if($nbOffres > 0)
                                        · {{ $nbOffres }} offre{{ $nbOffres > 1 ? 's' : '' }}
                                    @endif
                                </span>
                            </div>

                            <i class="fa-solid fa-chevron-right ios-item-mobile__arrow"></i>
                        </a>
                    @endforeach
                </div>
            @endif

        @else

            {{-- ═══ UTILISATEUR NOUVEAU — ONBOARDING ═══ --}}

            <div class="ios-onboarding">
                <div class="ios-onboarding__icon">
                    <i class="fa-solid fa-rocket"></i>
                </div>
                <h2 class="ios-onboarding__title">Bienvenue sur DoyaImmo</h2>
                <p class="ios-onboarding__text">
                    Publiez un besoin pour recevoir des propositions d'agences immobilières.
                </p>
            </div>

            <div class="ios-section-header" style="margin-top: 20px;">
                Pour commencer
            </div>

            <div class="ios-shortcuts-grid">
                <a href="{{ route('particulier.demandes.create') }}" class="ios-shortcut">
                    <div class="ios-shortcut__icon ios-shortcut__icon--rust">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <span class="ios-shortcut__label">Publier un besoin</span>
                </a>

                <a href="{{ route('particulier.propositions.index') }}" class="ios-shortcut">
                    <div class="ios-shortcut__icon ios-shortcut__icon--blue">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                    <span class="ios-shortcut__label">Voir les offres</span>
                </a>

                <a href="{{ route('particulier.rendezvous.index') }}" class="ios-shortcut">
                    <div class="ios-shortcut__icon ios-shortcut__icon--gold">
                        <i class="fa-solid fa-calendar-days"></i>
                    </div>
                    <span class="ios-shortcut__label">Mes rendez-vous</span>
                </a>

                <a href="{{ route('particulier.historique') }}" class="ios-shortcut">
                    <div class="ios-shortcut__icon ios-shortcut__icon--gray">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="ios-shortcut__label">Historique</span>
                </a>
            </div>

        @endif

    </div>

</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DASHBOARD PARTICULIER
   ═══════════════════════════════════════════════════════════ */

.particulier-dashboard {
    --c-success: #1E7A47; --c-success-bg: #E8F5E9;
    --c-warning: #E65100; --c-warning-bg: #FFF8E1;
    --c-danger: #C62828; --c-danger-bg: #FFEBEE;
    --c-info: #0D47A1; --c-info-bg: #E3F2FD;
    --c-gold: #F5A623;
    --surface: #F7F9FC;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-gray-3: #C7C7CC;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══════════ DESKTOP ═══════════ */
.desk-hero {
    display: flex; align-items: center; justify-content: space-between;
    gap: 20px; padding: 24px 28px;
    background: linear-gradient(135deg, #FFF9F5 0%, #fff 60%);
    border: 1px solid var(--border); border-radius: 16px;
    margin-bottom: 18px; flex-wrap: wrap;
}
.desk-hero__left { display: flex; align-items: center; gap: 16px; min-width: 0; }
.desk-hero__avatar {
    width: 56px; height: 56px; border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-family: var(--display); font-size: 20px; font-weight: 700;
    flex-shrink: 0; box-shadow: 0 6px 16px rgba(181, 80, 42, .25);
}
.desk-hero__text { min-width: 0; }
.desk-hero__title {
    font-family: var(--display); font-size: 22px; font-weight: 700;
    margin: 0 0 4px; color: var(--ink); letter-spacing: -.3px;
}
.desk-hero__date {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 13px; color: var(--muted);
}
.desk-hero__date i { font-size: 12px; opacity: .8; }
.desk-hero__cta {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 12px 20px;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff; border-radius: 12px; font-size: 13.5px; font-weight: 600;
    text-decoration: none; box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    transition: transform .2s, box-shadow .2s; white-space: nowrap;
}
.desk-hero__cta:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(181, 80, 42, .35); }

/* Stats */
.desk-stats {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 12px; margin-bottom: 18px;
}
.desk-stat {
    position: relative; display: flex; align-items: center; gap: 14px;
    padding: 18px 20px; background: #fff; border: 1px solid var(--border);
    border-radius: 14px; text-decoration: none; color: inherit;
    transition: all .2s; overflow: hidden;
}
.desk-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0, 0, 0, .06); }
.desk-stat::before {
    content: ''; position: absolute; left: 0; top: 50%;
    transform: translateY(-50%); width: 3px; height: 40%;
    border-radius: 0 3px 3px 0; background: var(--rust);
}
.desk-stat--rust::before { background: var(--rust); }
.desk-stat--blue::before { background: #1565C0; }
.desk-stat--gold::before { background: var(--c-gold); }
.desk-stat--green::before { background: var(--c-success); }
.desk-stat__icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.desk-stat--rust .desk-stat__icon { background: var(--rust-soft); color: var(--rust); }
.desk-stat--blue .desk-stat__icon { background: #E3F2FD; color: #1565C0; }
.desk-stat--gold .desk-stat__icon { background: #FFF8E1; color: var(--c-gold); }
.desk-stat--green .desk-stat__icon { background: #E8F5E9; color: var(--c-success); }
.desk-stat__body { display: flex; flex-direction: column; gap: 1px; min-width: 0; flex: 1; }
.desk-stat__value {
    font-family: var(--display); font-size: 26px; font-weight: 800;
    color: var(--ink); line-height: 1; letter-spacing: -.5px;
}
.desk-stat__label { font-size: 12.5px; font-weight: 500; color: var(--muted); margin-top: 3px; }
.desk-stat__sub { font-size: 11.5px; color: var(--text-soft); font-weight: 600; margin-top: 2px; }
.desk-stat__sub--accent { color: var(--rust); display: inline-flex; align-items: center; gap: 5px; }
.desk-stat__sub--accent i { font-size: 6px; color: var(--rust); animation: pulse 1.8s infinite; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
.desk-stat__badge {
    position: absolute; top: 14px; right: 16px;
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 22px; height: 22px; padding: 0 7px;
    background: var(--rust); color: #fff; border-radius: 999px;
    font-family: var(--display); font-size: 11.5px; font-weight: 800;
    box-shadow: 0 2px 6px rgba(181, 80, 42, .3);
}

/* Alert */
.desk-alert {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 22px;
    background: linear-gradient(135deg, #FFF4EC 0%, #FFE8D9 100%);
    border: 1px solid #FFD4B8; border-radius: 14px;
    margin-bottom: 18px; text-decoration: none; color: inherit;
    transition: all .2s;
}
.desk-alert:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(181, 80, 42, .15); }
.desk-alert__icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: var(--rust); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0; box-shadow: 0 4px 12px rgba(181, 80, 42, .35);
}
.desk-alert__text { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.desk-alert__text strong { font-family: var(--display); font-size: 15px; font-weight: 700; color: var(--ink); }
.desk-alert__text span { font-size: 13px; color: var(--text-soft); }
.desk-alert__cta {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px; background: #fff; color: var(--rust);
    border-radius: 10px; font-size: 13px; font-weight: 700;
    border: 1.5px solid var(--rust); transition: all .2s; flex-shrink: 0;
}
.desk-alert:hover .desk-alert__cta { background: var(--rust); color: #fff; gap: 10px; }
.desk-alert__cta i { font-size: 11px; }

/* Layout */
.desk-layout {
    display: grid; grid-template-columns: 1fr 340px;
    gap: 20px; align-items: start;
}
.desk-layout__main { min-width: 0; }
.desk-layout__side { min-width: 0; }

/* Section titles */
.desk-section-title {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; margin: 24px 0 12px; padding: 0 2px;
}
.desk-section-title:first-child { margin-top: 0; }
.desk-section-title span {
    font-family: var(--display); font-size: 16px; font-weight: 700;
    color: var(--ink); letter-spacing: -.2px;
}
.desk-section-title__link {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12.5px; font-weight: 600; color: var(--rust);
    text-decoration: none; transition: gap .2s;
}
.desk-section-title__link:hover { gap: 9px; }
.desk-section-title__link i { font-size: 10px; }

/* RDV Card */
.desk-rdv {
    display: flex; align-items: center; gap: 18px;
    padding: 18px 22px; background: #fff;
    border: 1px solid var(--border); border-radius: 14px;
    text-decoration: none; color: inherit; margin-bottom: 14px;
    transition: all .2s;
}
.desk-rdv:hover { border-color: var(--rust); box-shadow: 0 8px 20px rgba(0, 0, 0, .06); transform: translateY(-1px); }
.desk-rdv__date {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 2px; width: 64px; padding: 10px 6px;
    background: var(--c-info-bg); border-radius: 12px; flex-shrink: 0;
}
.desk-rdv__date.is-today { background: var(--c-warning-bg); }
.desk-rdv__day { font-family: var(--display); font-size: 24px; font-weight: 800; line-height: 1; color: var(--c-info); }
.desk-rdv__date.is-today .desk-rdv__day { color: var(--c-warning); }
.desk-rdv__month { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--c-info); opacity: .85; }
.desk-rdv__date.is-today .desk-rdv__month { color: var(--c-warning); }
.desk-rdv__content { flex: 1; min-width: 0; }
.desk-rdv__head { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; margin-bottom: 8px; }
.desk-rdv__title { font-family: var(--display); font-size: 16px; font-weight: 700; color: var(--ink); margin: 0; line-height: 1.3; }
.desk-rdv__badges { display: flex; gap: 6px; flex-wrap: wrap; }
.desk-rdv__meta { display: flex; flex-wrap: wrap; gap: 4px 16px; font-size: 13px; color: var(--text-soft); }
.desk-rdv__meta i { color: var(--rust); font-size: 11px; margin-right: 5px; opacity: .85; }
.desk-rdv__arrow { color: var(--muted); font-size: 16px; flex-shrink: 0; opacity: .4; transition: transform .2s, color .2s; }
.desk-rdv:hover .desk-rdv__arrow { color: var(--rust); transform: translateX(4px); }

/* Badges */
.desk-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 11px; border-radius: 999px;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .3px; white-space: nowrap;
}
.desk-badge i { font-size: 9px; }
.desk-badge--en_attente, .desk-badge--planifie { background: var(--c-warning-bg); color: var(--c-warning); }
.desk-badge--en_cours, .desk-badge--confirme { background: var(--c-info-bg); color: var(--c-info); }
.desk-badge--terminee, .desk-badge--termine, .desk-badge--acceptee { background: var(--c-success-bg); color: var(--c-success); }
.desk-badge--annulee, .desk-badge--annule, .desk-badge--refusee { background: var(--c-danger-bg); color: var(--c-danger); }
.desk-badge--danger { background: var(--c-danger-bg); color: var(--c-danger); }
.desk-badge--warning { background: var(--c-warning-bg); color: var(--c-warning); }

/* Offer */
.desk-offer {
    display: grid; grid-template-columns: 220px 1fr;
    background: #fff; border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden; margin-bottom: 14px; transition: all .2s;
}
.desk-offer:hover { box-shadow: 0 8px 24px rgba(0, 0, 0, .06); border-color: #d8d8d8; }
.desk-offer__media { position: relative; background: var(--surface); overflow: hidden; }
.desk-offer__media img { width: 100%; height: 100%; object-fit: cover; display: block; }
.desk-offer__media-empty { width: 100%; height: 100%; min-height: 200px; display: flex; align-items: center; justify-content: center; color: var(--muted); font-size: 32px; opacity: .4; }
.desk-offer__tag {
    position: absolute; top: 12px; left: 12px;
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff; border-radius: 999px; font-size: 10.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .3px;
    box-shadow: 0 4px 10px rgba(245, 166, 35, .35);
}
.desk-offer__tag i { font-size: 10px; }
.desk-offer__body { padding: 18px 22px; display: flex; flex-direction: column; gap: 10px; min-width: 0; }
.desk-offer__agency { display: flex; align-items: center; gap: 10px; }
.desk-offer__agency-avatar {
    width: 38px; height: 38px; border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-family: var(--display); font-size: 15px; font-weight: 700; flex-shrink: 0;
}
.desk-offer__agency-info { display: flex; flex-direction: column; gap: 1px; min-width: 0; }
.desk-offer__agency-info strong { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.desk-offer__agency-info span { display: inline-flex; align-items: center; gap: 4px; font-size: 12px; color: var(--text-soft); }
.desk-offer__agency-info i { color: var(--c-gold); font-size: 10px; }
.desk-offer__reviews { color: var(--muted); }
.desk-offer__title { font-family: var(--display); font-size: 17px; font-weight: 700; color: var(--ink); margin: 0; line-height: 1.3; }
.desk-offer__address { display: flex; align-items: center; gap: 6px; font-size: 13px; color: var(--muted); }
.desk-offer__address i { color: var(--rust); font-size: 11px; }
.desk-offer__price { font-family: var(--display); font-size: 24px; font-weight: 800; color: var(--rust); line-height: 1.1; letter-spacing: -.3px; }
.desk-offer__price small { font-size: 13px; font-weight: 600; opacity: .7; margin-left: 4px; }
.desk-offer__specs { display: flex; flex-wrap: wrap; gap: 6px 18px; font-size: 13px; color: var(--muted); }
.desk-offer__specs span { display: inline-flex; align-items: center; gap: 5px; }
.desk-offer__specs i { font-size: 11px; color: var(--rust); opacity: .8; }
.desk-offer__actions { display: flex; gap: 8px; margin-top: auto; padding-top: 8px; }
.desk-offer__actions form { flex: 1; margin: 0; }
.desk-offer__actions form .desk-btn { width: 100%; justify-content: center; }

/* Buttons */
.desk-btn {
    display: inline-flex; align-items: center; justify-content: center;
    gap: 7px; padding: 11px 20px; border-radius: 11px;
    font-size: 13.5px; font-weight: 600; text-decoration: none;
    border: 1px solid transparent; cursor: pointer;
    font-family: inherit; transition: all .2s; white-space: nowrap;
}
.desk-btn--primary {
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff; border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181, 80, 42, .2);
}
.desk-btn--primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(181, 80, 42, .3); }
.desk-btn--ghost { background: #fff; color: var(--text-soft); border-color: var(--border); }
.desk-btn--ghost:hover { border-color: var(--rust); color: var(--rust); background: var(--surface); }

/* List */
.desk-list { background: #fff; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
.desk-list__item {
    display: flex; align-items: center; gap: 14px;
    padding: 14px 18px; border-bottom: 1px solid var(--border);
    text-decoration: none; color: inherit; transition: background .15s;
}
.desk-list__item:last-child { border-bottom: none; }
.desk-list__item:hover { background: #FAFBFC; }
.desk-list__icon {
    width: 40px; height: 40px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0;
}
.desk-list__icon--rust { background: var(--rust-soft); color: var(--rust); }
.desk-list__content { flex: 1; min-width: 0; }
.desk-list__title {
    display: block; font-family: var(--display); font-size: 14px; font-weight: 700;
    color: var(--ink); margin-bottom: 3px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.desk-list__sub { display: flex; flex-wrap: wrap; gap: 3px 10px; font-size: 12.5px; color: var(--muted); }
.desk-list__sub i { color: var(--rust); font-size: 10px; margin-right: 3px; opacity: .8; }
.desk-list__dot { opacity: .5; }
.desk-list__right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.desk-list__count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 24px; height: 24px; padding: 0 8px;
    background: var(--rust); color: #fff; border-radius: 999px;
    font-family: var(--display); font-size: 12px; font-weight: 800;
}
.desk-list__chevron { color: var(--muted); font-size: 13px; opacity: .4; transition: transform .2s, color .2s; }
.desk-list__item:hover .desk-list__chevron { color: var(--rust); transform: translateX(3px); }

/* Empty */
.desk-empty { background: #fff; border: 1px dashed var(--border); border-radius: 14px; padding: 40px 24px; text-align: center; }
.desk-empty__icon {
    width: 60px; height: 60px; margin: 0 auto 16px;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    background: var(--surface); color: var(--muted); font-size: 24px;
}
.desk-empty h4 { font-family: var(--display); font-size: 15px; font-weight: 700; color: var(--ink); margin: 0 0 6px; }
.desk-empty p { font-size: 13px; color: var(--muted); margin: 0 0 18px; max-width: 320px; margin-inline: auto; line-height: 1.5; }

/* Sidebar */
.desk-side-card { background: #fff; border: 1px solid var(--border); border-radius: 14px; padding: 18px 20px; margin-bottom: 16px; }
.desk-side-card__head {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px; margin-bottom: 14px; padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}
.desk-side-card__head h4 { font-family: var(--display); font-size: 14px; font-weight: 700; color: var(--ink); margin: 0; }
.desk-side-card__link { font-size: 12px; font-weight: 600; color: var(--rust); text-decoration: none; transition: opacity .15s; }
.desk-side-card__link:hover { opacity: .7; }
.desk-side-list { display: flex; flex-direction: column; gap: 10px; }
.desk-side-item { display: flex; align-items: center; gap: 12px; padding: 10px; border-radius: 10px; text-decoration: none; color: inherit; transition: background .15s; }
.desk-side-item:hover { background: var(--surface); }
.desk-side-item__date {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    width: 42px; padding: 5px 3px; background: var(--surface); border-radius: 9px; flex-shrink: 0;
}
.desk-side-item__day { font-family: var(--display); font-size: 15px; font-weight: 800; line-height: 1; color: var(--ink); }
.desk-side-item__month { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); margin-top: 1px; }
.desk-side-item__body { min-width: 0; }
.desk-side-item__title { display: block; font-size: 13px; font-weight: 600; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.desk-side-item__meta { display: block; font-size: 11.5px; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.desk-side-empty { display: flex; flex-direction: column; align-items: center; gap: 8px; padding: 20px; color: var(--muted); font-size: 13px; }
.desk-side-empty i { font-size: 24px; opacity: .4; }

/* Shortcuts sidebar */
.desk-shortcuts { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.desk-shortcut {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    padding: 14px 8px; border-radius: 12px; background: var(--surface);
    text-decoration: none; color: inherit; transition: all .2s; text-align: center;
}
.desk-shortcut:hover { background: #fff; box-shadow: 0 4px 12px rgba(0, 0, 0, .06); transform: translateY(-2px); }
.desk-shortcut__icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 15px; }
.desk-shortcut__icon--rust { background: var(--rust-soft); color: var(--rust); }
.desk-shortcut__icon--blue { background: #E3F2FD; color: #1565C0; }
.desk-shortcut__icon--gold { background: #FFF8E1; color: var(--c-gold); }
.desk-shortcut__icon--gray { background: #F2F2F7; color: #8E8E93; }
.desk-shortcut span { font-size: 11.5px; font-weight: 600; color: var(--text-soft); line-height: 1.3; }

@media (max-width: 1200px) {
    .desk-layout { grid-template-columns: 1fr; }
    .desk-layout__side { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
}
@media (max-width: 1024px) {
    .desk-stats { grid-template-columns: repeat(2, 1fr); }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — Version simplifiée conditionnelle
   ═══════════════════════════════════════════════════════════ */

.ios-mobile-only { display: none; }

@media (max-width: 820px) {

    /* Masquer tout le desktop */
    .desk-hero,
    .desk-stats,
    .desk-alert,
    .desk-section-title,
    .desk-rdv,
    .desk-offer,
    .desk-list,
    .desk-empty,
    .desk-layout { display: none; }

    /* Afficher le mobile */
    .ios-mobile-only { display: block; padding: 4px 0; }

    /* ─── HERO ─── */
    .ios-hero-mobile { margin-bottom: 18px; }
    .ios-hero-mobile__title {
        font-size: 24px; font-weight: 700; color: var(--ios-label);
        margin: 0 0 4px; letter-spacing: -0.5px; line-height: 1.2;
    }
    .ios-hero-mobile__date {
        font-size: 13px; color: var(--ios-label-3);
        text-transform: capitalize; letter-spacing: -0.1px;
    }

    /* ─── STATS 1 LIGNE ─── */
    .ios-stats-line {
        display: flex; align-items: stretch;
        background: var(--ios-card); border-radius: var(--ios-radius-lg);
        padding: 14px 8px; margin-bottom: 4px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .ios-stat-line {
        flex: 1; display: flex; flex-direction: column; align-items: center;
        gap: 2px; text-decoration: none; color: inherit;
        position: relative; padding: 0 4px; transition: opacity 0.15s;
    }
    .ios-stat-line:active { opacity: 0.5; }
    .ios-stat-line:not(:last-child)::after {
        content: ''; position: absolute; right: 0; top: 20%;
        height: 60%; width: 0.5px; background: var(--ios-separator);
    }
    .ios-stat-line strong {
        font-size: 22px; font-weight: 800; color: var(--ios-label);
        letter-spacing: -0.5px; line-height: 1;
    }
    .ios-stat-line span {
        font-size: 11px; font-weight: 500; color: var(--ios-label-3);
        letter-spacing: -0.05px;
    }

    /* ─── SECTION HEADER ─── */
    .ios-section-header {
        display: flex; align-items: center; justify-content: space-between;
        font-size: 13px; font-weight: 600; text-transform: uppercase;
        letter-spacing: 0.5px; color: var(--ios-label-3);
        margin: 20px 0 10px; padding: 0 4px;
    }
    .ios-section-header__link {
        font-size: 12px; font-weight: 600; color: var(--rust);
        text-decoration: none; text-transform: none; letter-spacing: 0;
    }

    /* ─── LISTE BESOINS ─── */
    .ios-list-mobile { display: flex; flex-direction: column; gap: 8px; }
    .ios-item-mobile {
        display: flex; align-items: center; gap: 12px;
        padding: 14px 16px; background: var(--ios-card);
        border-radius: var(--ios-radius-lg); text-decoration: none;
        color: inherit; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: transform 0.15s ease, background 0.15s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .ios-item-mobile:active { transform: scale(0.98); background: #F5F5F7; }
    .ios-item-mobile__icon {
        width: 38px; height: 38px; border-radius: 10px;
        background: linear-gradient(135deg, #B4532A, #d4754a);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-size: 15px; flex-shrink: 0;
    }
    .ios-item-mobile__body {
        flex: 1; min-width: 0;
        display: flex; flex-direction: column; gap: 2px;
    }
    .ios-item-mobile__title {
        font-size: 15px; font-weight: 600; color: var(--ios-label);
        letter-spacing: -0.2px; white-space: nowrap;
        overflow: hidden; text-overflow: ellipsis;
    }
    .ios-item-mobile__meta {
        font-size: 12.5px; color: var(--ios-label-3);
        letter-spacing: -0.1px; white-space: nowrap;
        overflow: hidden; text-overflow: ellipsis;
    }
    .ios-item-mobile__arrow {
        color: var(--ios-label-3); font-size: 13px;
        flex-shrink: 0; opacity: 0.5;
    }

    /* ─── EMPTY ─── */
    .ios-empty-mobile {
        text-align: center; padding: 32px 20px;
        background: var(--ios-card); border-radius: var(--ios-radius-lg);
    }
    .ios-empty-mobile > i {
        font-size: 36px; color: var(--ios-gray-3);
        margin-bottom: 10px; display: block;
    }
    .ios-empty-mobile p {
        font-size: 14px; color: var(--ios-label-2); margin: 0 0 16px;
    }

    /* ─── BOUTON ─── */
    .ios-btn-mobile {
        display: inline-flex; align-items: center; justify-content: center;
        gap: 6px; padding: 11px 18px; border-radius: 11px;
        font-size: 14px; font-weight: 600; text-decoration: none;
        transition: opacity 0.15s, transform 0.15s;
        letter-spacing: -0.2px;
    }
    .ios-btn-mobile:active { transform: scale(0.97); opacity: 0.85; }
    .ios-btn-mobile--primary {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff; box-shadow: 0 4px 12px rgba(181, 80, 42, 0.25);
    }

    /* ─── ONBOARDING ─── */
    .ios-onboarding {
        text-align: center; padding: 28px 20px;
        background: var(--ios-card); border-radius: var(--ios-radius-lg);
        margin-bottom: 8px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    }
    .ios-onboarding__icon {
        width: 64px; height: 64px; margin: 0 auto 14px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff; display: flex; align-items: center; justify-content: center;
        font-size: 26px; box-shadow: 0 6px 20px rgba(181, 80, 42, 0.3);
    }
    .ios-onboarding__title {
        font-size: 18px; font-weight: 700; color: var(--ios-label);
        margin: 0 0 6px; letter-spacing: -0.3px;
    }
    .ios-onboarding__text {
        font-size: 14px; color: var(--ios-label-2);
        margin: 0 auto; max-width: 280px;
        line-height: 1.5; letter-spacing: -0.1px;
    }

    /* ─── ACCÈS RAPIDES ─── */
    .ios-shortcuts-grid {
        display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;
    }
    .ios-shortcut {
        display: flex; flex-direction: column; align-items: center; justify-content: center;
        gap: 10px; padding: 20px 12px;
        background: var(--ios-card); border-radius: var(--ios-radius-lg);
        text-decoration: none; color: inherit;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        transition: transform 0.15s ease, background 0.15s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .ios-shortcut:active { transform: scale(0.97); background: #F5F5F7; }
    .ios-shortcut__icon {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
    }
    .ios-shortcut__icon--rust {
        background: linear-gradient(135deg, #B4532A, #d4754a);
        color: #fff; box-shadow: 0 4px 12px rgba(181, 80, 42, 0.25);
    }
    .ios-shortcut__icon--blue {
        background: linear-gradient(135deg, #007AFF, #0055CC);
        color: #fff; box-shadow: 0 4px 12px rgba(0, 122, 255, 0.25);
    }
    .ios-shortcut__icon--gold {
        background: linear-gradient(135deg, #FFCC00, #F5A623);
        color: #fff; box-shadow: 0 4px 12px rgba(245, 166, 35, 0.25);
    }
    .ios-shortcut__icon--gray {
        background: linear-gradient(135deg, #8E8E93, #636366);
        color: #fff; box-shadow: 0 4px 12px rgba(142, 142, 147, 0.25);
    }
    .ios-shortcut__label {
        font-size: 13px; font-weight: 600; color: var(--ios-label);
        text-align: center; line-height: 1.3; letter-spacing: -0.1px;
    }
}

@media (max-width: 480px) {
    .ios-shortcuts-grid { gap: 10px; }
    .ios-shortcut { padding: 18px 10px; gap: 8px; }
    .ios-shortcut__icon { width: 44px; height: 44px; font-size: 18px; border-radius: 12px; }
    .ios-shortcut__label { font-size: 12.5px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush