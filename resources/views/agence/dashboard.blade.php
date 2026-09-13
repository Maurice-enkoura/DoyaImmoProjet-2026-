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

        $besoinsDispo    = $stats['besoins_disponibles'] ?? 0;
        $offresEnvoyees  = $stats['offres_envoyees'] ?? 0;
        $rdvAVenir       = $stats['rendezvous_a_venir'] ?? 0;
        $noteMoyenne     = $stats['note_moyenne'] ?? 0;
        $nbBiensVedette  = $biensEnVedette->count();

        $rdvAujourdhui = $prochainsRendezVous
            ->filter(fn($rdv) => \Carbon\Carbon::parse($rdv->date_visite)->isToday())
            ->count();

        $nbAvis = $derniersAvis->count();
        $nbBiens = $agence->biens()->where('statut', true)->count();
    @endphp

    {{-- ═══════════════════════════════════════════
         HERO DE BIENVENUE (desktop)
    ═══════════════════════════════════════════ --}}
    <section class="desk-hero">
        <div class="desk-hero__left">
            <div class="desk-hero__avatar">{{ $initiales }}</div>
            <div class="desk-hero__text">
                <h1 class="desk-hero__title">{{ $salutation }}, {{ $prenom }}</h1>
                <span class="desk-hero__date">
                    <i class="fa-regular fa-calendar"></i>
                    {{ now()->translatedFormat('l d F Y') }}
                </span>
            </div>
        </div>

        <a href="{{ route('agence.biens.create') }}" class="desk-hero__cta">
            <i class="fa-solid fa-plus"></i>
            Publier un bien
        </a>
    </section>

    {{-- ═══════════════════════════════════════════
         STATS INLINE (mobile iOS)
    ═══════════════════════════════════════════ --}}
    <div class="ios-stats">
        <a href="{{ route('agence.demandes.index') }}" class="ios-stat">
            <span class="ios-stat__value">{{ $besoinsDispo }}</span>
            <span class="ios-stat__label">Besoins</span>
        </a>
        <a href="{{ route('agence.propositions.index') }}" class="ios-stat">
            <span class="ios-stat__value">{{ $offresEnvoyees }}</span>
            <span class="ios-stat__label">Offres</span>
        </a>
        <a href="{{ route('agence.rendezvous.index') }}" class="ios-stat">
            <span class="ios-stat__value">{{ $rdvAVenir }}</span>
            <span class="ios-stat__label">RDV</span>
        </a>
        <a href="{{ route('agence.evaluations.index') }}" class="ios-stat">
            <span class="ios-stat__value">{{ number_format($noteMoyenne, 1) }}</span>
            <span class="ios-stat__label">Note</span>
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         STATS DESKTOP (4 cards)
    ═══════════════════════════════════════════ --}}
    <section class="desk-stats">
        <a href="{{ route('agence.demandes.index') }}" class="desk-stat desk-stat--blue">
            <div class="desk-stat__icon">
                <i class="fa-solid fa-house-circle-check"></i>
            </div>
            <div class="desk-stat__body">
                <span class="desk-stat__value">{{ $besoinsDispo }}</span>
                <span class="desk-stat__label">Demandes disponibles</span>
            </div>
        </a>

        <a href="{{ route('agence.propositions.index') }}" class="desk-stat desk-stat--rust">
            <div class="desk-stat__icon">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div class="desk-stat__body">
                <span class="desk-stat__value">{{ $offresEnvoyees }}</span>
                <span class="desk-stat__label">Offres ce mois</span>
            </div>
        </a>

        <a href="{{ route('agence.rendezvous.index') }}" class="desk-stat desk-stat--gold">
            <div class="desk-stat__icon">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="desk-stat__body">
                <span class="desk-stat__value">{{ $rdvAVenir }}</span>
                <span class="desk-stat__label">RDV à venir</span>
            </div>
        </a>

        <a href="{{ route('agence.evaluations.index') }}" class="desk-stat desk-stat--green">
            <div class="desk-stat__icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="desk-stat__body">
                <span class="desk-stat__value">{{ number_format($noteMoyenne, 1) }}</span>
                <span class="desk-stat__label">Note moyenne</span>
            </div>
        </a>
    </section>

    {{-- ═══════════════════════════════════════════
         ALERTE ABONNEMENT (desktop)
    ═══════════════════════════════════════════ --}}
    @if($abonnementActif && !$modeGratuit)
        @if(!$abonnementActuel)
            <div class="desk-alert desk-alert--danger">
                <div class="desk-alert__icon"><i class="fa-solid fa-circle-exclamation"></i></div>
                <div class="desk-alert__text">
                    <strong>Abonnement requis</strong>
                    <span>Souscrivez un abonnement pour publier des biens et envoyer des offres</span>
                </div>
                <a href="{{ route('agence.abonnement') }}" class="desk-alert__cta">Voir les offres <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        @elseif($offresRestantes <= 0 && $limiteOffres !== PHP_INT_MAX)
            <div class="desk-alert desk-alert--warning">
                <div class="desk-alert__icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div class="desk-alert__text">
                    <strong>Quota d'offres atteint</strong>
                    <span>Vous avez utilisé vos {{ $limiteOffres }} offres ce mois-ci</span>
                </div>
                <a href="{{ route('agence.abonnement') }}" class="desk-alert__cta">Upgrader <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        @elseif($limiteOffres !== PHP_INT_MAX && $offresRestantes <= 5)
            <div class="desk-alert desk-alert--info">
                <div class="desk-alert__icon"><i class="fa-solid fa-clock"></i></div>
                <div class="desk-alert__text">
                    <strong>{{ $offresRestantes }} offre(s) restante(s)</strong>
                    <span>Rechargez bientôt pour ne pas être bloqué</span>
                </div>
                <a href="{{ route('agence.abonnement') }}" class="desk-alert__cta">Voir mon abonnement <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        @endif
    @endif

    {{-- ═══════════════════════════════════════════
         ACCÈS RAPIDES (visible desktop + mobile)
    ═══════════════════════════════════════════ --}}
    <div class="desk-section-title quick-access-title">
        <span><i class="fa-solid fa-bolt" style="color:var(--rust);margin-right:8px;"></i> Accès rapides</span>
    </div>

    <div class="quick-access">
        <a href="{{ route('agence.demandes.index') }}" class="quick-access__item">
            <div class="quick-access__icon quick-access__icon--blue">
                <i class="fa-solid fa-house-circle-check"></i>
            </div>
            <span class="quick-access__label">Besoins</span>
            @if($besoinsDispo > 0)
                <span class="quick-access__badge">{{ $besoinsDispo }}</span>
            @endif
        </a>

        <a href="{{ route('agence.propositions.index') }}" class="quick-access__item">
            <div class="quick-access__icon quick-access__icon--rust">
                <i class="fa-solid fa-file-invoice"></i>
            </div>
            <span class="quick-access__label">Mes offres</span>
        </a>

        <a href="{{ route('agence.rendezvous.index') }}" class="quick-access__item">
            <div class="quick-access__icon quick-access__icon--gold">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <span class="quick-access__label">Rendez-vous</span>
            @if($rdvAVenir > 0)
                <span class="quick-access__badge">{{ $rdvAVenir }}</span>
            @endif
        </a>

        <a href="{{ route('agence.biens.index') }}" class="quick-access__item">
            <div class="quick-access__icon quick-access__icon--green">
                <i class="fa-solid fa-building"></i>
            </div>
            <span class="quick-access__label">Mes biens</span>
            @if($nbBiens > 0)
                <span class="quick-access__badge quick-access__badge--gray">{{ $nbBiens }}</span>
            @endif
        </a>

        <a href="{{ route('agence.evaluations.index') }}" class="quick-access__item">
            <div class="quick-access__icon quick-access__icon--yellow">
                <i class="fa-solid fa-star"></i>
            </div>
            <span class="quick-access__label">Avis reçus</span>
        </a>

        <a href="{{ route('agence.historique') }}" class="quick-access__item">
            <div class="quick-access__icon quick-access__icon--gray">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <span class="quick-access__label">Historique</span>
        </a>

        @if(config('abonnement.actif', false))
            <a href="{{ route('agence.abonnement') }}" class="quick-access__item">
                <div class="quick-access__icon quick-access__icon--purple">
                    <i class="fa-solid fa-award"></i>
                </div>
                <span class="quick-access__label">Abonnement</span>
            </a>
        @endif

        <a href="{{ route('agence.profil') }}" class="quick-access__item">
            <div class="quick-access__icon quick-access__icon--gray">
                <i class="fa-solid fa-user"></i>
            </div>
            <span class="quick-access__label">Profil</span>
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         AUJOURD'HUI
    ═══════════════════════════════════════════ --}}
    @if($rdvAujourdhui > 0 || $besoinsDispo > 0 || $nbAvis > 0)
        <section class="desk-today">
            <div class="desk-section-title">
                <span><i class="fa-solid fa-bolt" style="color:var(--rust);margin-right:8px;"></i> Aujourd'hui</span>
            </div>

            <div class="desk-today__grid">
                @if($rdvAujourdhui > 0)
                    <a href="{{ route('agence.rendezvous.index') }}" class="desk-today__item desk-today__item--urgent">
                        <div class="desk-today__icon"><i class="fa-solid fa-calendar-day"></i></div>
                        <div class="desk-today__body">
                            <strong>{{ $rdvAujourdhui }} RDV aujourd'hui</strong>
                            <span>Consultez votre agenda</span>
                        </div>
                        <i class="fa-solid fa-chevron-right desk-today__arrow"></i>
                    </a>
                @endif

                @if($besoinsDispo > 0)
                    <a href="{{ route('agence.demandes.index') }}" class="desk-today__item">
                        <div class="desk-today__icon"><i class="fa-solid fa-bullseye"></i></div>
                        <div class="desk-today__body">
                            <strong>{{ $besoinsDispo }} demandes disponibles</strong>
                            <span>Parcourez les besoins clients</span>
                        </div>
                        <i class="fa-solid fa-chevron-right desk-today__arrow"></i>
                    </a>
                @endif

                @if($nbAvis > 0)
                    <a href="{{ route('agence.evaluations.index') }}" class="desk-today__item">
                        <div class="desk-today__icon"><i class="fa-regular fa-star"></i></div>
                        <div class="desk-today__body">
                            <strong>{{ $nbAvis }} nouveau{{ $nbAvis > 1 ? 'x' : '' }} avis</strong>
                            <span>Découvrez ce que disent vos clients</span>
                        </div>
                        <i class="fa-solid fa-chevron-right desk-today__arrow"></i>
                    </a>
                @endif
            </div>
        </section>
    @endif

    {{-- ═══════════════════════════════════════════
         BIENS EN VEDETTE
    ═══════════════════════════════════════════ --}}
    @if($nbBiensVedette > 0)
        <div class="desk-section-title">
            <span><i class="fa-solid fa-star" style="color:var(--c-gold);margin-right:8px;"></i> Mes biens en vedette</span>
            <a href="{{ route('agence.biens.index', ['vedette' => 'true']) }}" class="desk-section-title__link">
                Voir tout <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="desk-vedette-grid">
            @foreach($biensEnVedette as $bien)
                @php
                    $cover = $bien->medias->where('type_media', 'image')->first()
                        ? asset('storage/' . $bien->medias->where('type_media', 'image')->first()->fichier)
                        : null;
                @endphp

                <a href="{{ route('agence.biens.show', $bien->slug) }}" class="desk-vedette">
                    <div class="desk-vedette__media">
                        @if($cover)
                            <img src="{{ $cover }}" alt="{{ $bien->titre }}" loading="lazy">
                        @else
                            <div class="desk-vedette__placeholder"><i class="fa-regular fa-image"></i></div>
                        @endif
                        <span class="desk-vedette__badge"><i class="fa-solid fa-star"></i> Vedette</span>
                    </div>
                    <div class="desk-vedette__body">
                        <h4 class="desk-vedette__title">{{ $bien->titre }}</h4>
                        <div class="desk-vedette__price">{{ number_format($bien->prix ?? 0, 0, ',', ' ') }} <small>FCFA</small></div>
                        <div class="desk-vedette__meta"><i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         LAYOUT 2 COLONNES
    ═══════════════════════════════════════════ --}}
    <div class="desk-layout">
        <div class="desk-layout__main">

            {{-- Dernières demandes --}}
            <div class="desk-section-title">
                <span>Dernières demandes</span>
                <a href="{{ route('agence.demandes.index') }}" class="desk-section-title__link">
                    Voir tout <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            @if($derniersBesoins->count() > 0)
                <div class="desk-list">
                    @foreach($derniersBesoins as $besoin)
                        @php
                            $statutValue = $besoin->statut instanceof \App\Enums\StatutDemandeEnum ? $besoin->statut->value : $besoin->statut;
                            $statutLabel = $besoin->statut instanceof \App\Enums\StatutDemandeEnum ? $besoin->statut->label() : ucfirst($besoin->statut);
                        @endphp
                        <a href="{{ route('agence.demandes.show', $besoin->slug) }}" class="desk-list__item">
                            <div class="desk-list__icon desk-list__icon--blue">
                                <i class="fa-solid fa-house"></i>
                            </div>
                            <div class="desk-list__content">
                                <span class="desk-list__title">{{ $besoin->type_bien->label() }}</span>
                                <div class="desk-list__sub">
                                    <span><i class="fa-solid fa-location-dot"></i> {{ $besoin->zone_recherchee }}</span>
                                    @if($besoin->surface_minimum)
                                        <span class="desk-list__dot">·</span>
                                        <span><i class="fa-regular fa-square"></i> {{ $besoin->surface_minimum }} m²</span>
                                    @endif
                                </div>
                            </div>
                            <div class="desk-list__right">
                                <span class="desk-badge desk-badge--{{ $statutValue }}">{{ $statutLabel }}</span>
                            </div>
                            <i class="fa-solid fa-chevron-right desk-list__chevron"></i>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="desk-empty">
                    <div class="desk-empty__icon"><i class="fa-regular fa-inbox"></i></div>
                    <h4>Aucune demande récente</h4>
                    <p>Les demandes des particuliers apparaîtront ici.</p>
                </div>
            @endif

            {{-- Derniers avis --}}
            @if($derniersAvis->count() > 0)
                <div class="desk-section-title">
                    <span>Derniers avis reçus</span>
                    <a href="{{ route('agence.evaluations.index') }}" class="desk-section-title__link">
                        Voir tout <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="desk-list">
                    @foreach($derniersAvis as $avis)
                        @php
                            $note = (int) ($avis->note ?? 0);
                            $sentiment = $note >= 4 ? 'positif' : ($note === 3 ? 'neutre' : 'negatif');
                            $prenomAv = $avis->particulier->user->prenom ?? 'C';
                            $initialAv = strtoupper(mb_substr($prenomAv, 0, 1));
                        @endphp
                        <div class="desk-avis desk-avis--{{ $sentiment }}">
                            <div class="desk-avis__avatar">{{ $initialAv }}</div>
                            <div class="desk-avis__info">
                                <div class="desk-avis__head">
                                    <strong>{{ $avis->particulier->user->prenom ?? '' }} {{ $avis->particulier->user->nom ?? '' }}</strong>
                                    <div class="desk-avis__stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-solid fa-star {{ $i <= $note ? 'is-on' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                @if($avis->commentaire)
                                    <p class="desk-avis__comment">"{{ Str::limit($avis->commentaire, 120) }}"</p>
                                @endif
                                <span class="desk-avis__date">{{ $avis->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ═══ SIDEBAR (Prochains RDV) ═══ --}}
        <aside class="desk-layout__side">

            <div class="desk-side-card">
                <div class="desk-side-card__head">
                    <h4>Prochains rendez-vous</h4>
                    <a href="{{ route('agence.rendezvous.index') }}" class="desk-side-card__link">Voir tout</a>
                </div>

                @if($prochainsRendezVous->count() > 0)
                    <div class="desk-side-list">
                        @foreach($prochainsRendezVous as $rdv)
                            @php
                                $dateVisite = \Carbon\Carbon::parse($rdv->date_visite);
                                $isToday = $dateVisite->isToday();
                            @endphp
                            <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="desk-side-item {{ $isToday ? 'is-today' : '' }}">
                                <div class="desk-side-item__date">
                                    <span class="desk-side-item__day">{{ $dateVisite->format('d') }}</span>
                                    <span class="desk-side-item__month">{{ strtoupper($dateVisite->translatedFormat('M')) }}</span>
                                </div>
                                <div class="desk-side-item__body">
                                    <span class="desk-side-item__title">{{ $rdv->proposition->bien->titre ?? 'Visite' }}</span>
                                    <span class="desk-side-item__meta">
                                        {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                                        @if($rdv->particulier && $rdv->particulier->user)
                                            · {{ $rdv->particulier->user->prenom }}
                                        @endif
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

        </aside>
    </div>

</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   DASHBOARD AGENCE
   ═══════════════════════════════════════════════════════════ */

.agence-dashboard {
    --c-success: #1E7A47; --c-success-bg: #E8F5E9;
    --c-warning: #E65100; --c-warning-bg: #FFF8E1;
    --c-danger: #C62828; --c-danger-bg: #FFEBEE;
    --c-info: #0D47A1; --c-info-bg: #E3F2FD;
    --c-gold: #F5A623; --c-gold-bg: #FFF8E1;
    --surface: #F7F9FC;
    --radius: 14px;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ DESKTOP ═══ */

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
.desk-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,.06); }
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
.desk-stat__label {
    font-size: 12.5px; font-weight: 500; color: var(--muted); margin-top: 3px;
}

.desk-alert {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 22px; border-radius: 14px;
    margin-bottom: 18px; text-decoration: none; color: inherit;
    transition: all .2s; border: 1px solid;
}
.desk-alert:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(0,0,0,.06); }
.desk-alert--danger { background: var(--c-danger-bg); border-color: #FFCDD2; color: var(--c-danger); }
.desk-alert--warning { background: var(--c-warning-bg); border-color: #FFE0B2; color: var(--c-warning); }
.desk-alert--info { background: var(--c-info-bg); border-color: #BBDEFB; color: var(--c-info); }
.desk-alert__icon {
    width: 44px; height: 44px; border-radius: 12px;
    background: rgba(255,255,255,.6);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.desk-alert__text { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.desk-alert__text strong { font-family: var(--display); font-size: 15px; font-weight: 700; }
.desk-alert__text span { font-size: 13px; opacity: .85; }
.desk-alert__cta {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 16px; background: #fff; color: inherit;
    border-radius: 10px; font-size: 13px; font-weight: 700;
    border: 1.5px solid currentColor;
    transition: all .2s; flex-shrink: 0;
}
.desk-alert__cta i { font-size: 11px; }

.desk-today { margin-bottom: 18px; }
.desk-today__grid {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 10px; margin-top: 10px;
}
.desk-today__item {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 16px; background: #fff;
    border: 1px solid var(--border); border-radius: 12px;
    text-decoration: none; color: inherit; transition: all .2s;
}
.desk-today__item:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,.06); border-color: #d8d8d8; }
.desk-today__item--urgent { background: var(--c-warning-bg); border-color: #FFE0B2; }
.desk-today__icon {
    width: 40px; height: 40px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    background: var(--surface); color: var(--rust); font-size: 16px; flex-shrink: 0;
}
.desk-today__item--urgent .desk-today__icon { background: rgba(230, 81, 0, .12); color: var(--c-warning); }
.desk-today__body { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 2px; }
.desk-today__body strong { font-family: var(--display); font-size: 13.5px; font-weight: 700; color: var(--ink); }
.desk-today__body span { font-size: 12px; color: var(--muted); }
.desk-today__arrow { color: var(--border); font-size: 12px; transition: all .2s; }
.desk-today__item:hover .desk-today__arrow { color: var(--rust); transform: translateX(3px); }

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

.desk-vedette-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px; margin-bottom: 18px;
}
.desk-vedette {
    background: #fff; border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden;
    text-decoration: none; color: inherit;
    transition: all .2s; position: relative;
}
.desk-vedette:hover { transform: translateY(-3px); box-shadow: 0 8px 22px rgba(245, 166, 35, .2); }
.desk-vedette__media {
    position: relative; aspect-ratio: 4 / 3;
    background: var(--surface); overflow: hidden;
}
.desk-vedette__media img {
    width: 100%; height: 100%; object-fit: cover; display: block;
    transition: transform .3s;
}
.desk-vedette:hover .desk-vedette__media img { transform: scale(1.05); }
.desk-vedette__placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted); font-size: 32px; opacity: .4;
}
.desk-vedette__badge {
    position: absolute; top: 10px; left: 10px;
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px; border-radius: 999px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff; font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .3px;
    box-shadow: 0 2px 6px rgba(245, 166, 35, .35);
}
.desk-vedette__badge i { font-size: 8px; }
.desk-vedette__body { padding: 12px 14px; }
.desk-vedette__title {
    font-family: var(--display); font-size: 14px; font-weight: 700;
    margin: 0 0 6px; color: var(--ink);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.desk-vedette__price {
    font-family: var(--display); font-size: 15px; font-weight: 800;
    color: var(--rust); line-height: 1.1; margin-bottom: 6px;
}
.desk-vedette__price small { font-size: 10px; font-weight: 600; opacity: .7; margin-left: 2px; }
.desk-vedette__meta {
    font-size: 11.5px; color: var(--muted);
    display: flex; align-items: center; gap: 4px;
}
.desk-vedette__meta i { color: var(--rust); font-size: 10px; opacity: .8; }

.desk-layout {
    display: grid; grid-template-columns: 1fr 340px;
    gap: 20px; align-items: start;
}
.desk-layout__main { min-width: 0; }
.desk-layout__side { min-width: 0; }

.desk-list {
    background: #fff; border: 1px solid var(--border);
    border-radius: 14px; overflow: hidden; margin-bottom: 14px;
}
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
.desk-list__icon--blue { background: #E3F2FD; color: #1565C0; }

.desk-list__content { flex: 1; min-width: 0; }
.desk-list__title {
    display: block; font-family: var(--display);
    font-size: 14px; font-weight: 700; color: var(--ink);
    margin-bottom: 3px;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.desk-list__sub { display: flex; flex-wrap: wrap; gap: 3px 10px; font-size: 12.5px; color: var(--muted); }
.desk-list__sub i { color: var(--rust); font-size: 10px; margin-right: 3px; opacity: .8; }
.desk-list__dot { opacity: .5; }

.desk-list__right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.desk-list__chevron {
    color: var(--muted); font-size: 13px; opacity: .4;
    transition: transform .2s, color .2s;
}
.desk-list__item:hover .desk-list__chevron { color: var(--rust); transform: translateX(3px); }

.desk-avis {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 14px 18px; border-bottom: 1px solid var(--border);
    border-left: 3px solid var(--muted);
}
.desk-avis:last-child { border-bottom: none; }
.desk-avis--positif { border-left-color: var(--c-success); background: rgba(30, 122, 71, .02); }
.desk-avis--neutre  { border-left-color: var(--c-warning); background: rgba(230, 81, 0, .02); }
.desk-avis--negatif { border-left-color: var(--c-danger);  background: rgba(198, 40, 40, .02); }
.desk-avis__avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-family: var(--display); font-size: 16px; font-weight: 700;
    flex-shrink: 0;
}
.desk-avis__info { flex: 1; min-width: 0; }
.desk-avis__head {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px; margin-bottom: 4px; flex-wrap: wrap;
}
.desk-avis__head strong { font-size: 13.5px; font-weight: 700; color: var(--ink); }
.desk-avis__stars { display: inline-flex; gap: 2px; font-size: 12px; }
.desk-avis__stars i { color: #D4D8E0; }
.desk-avis__stars i.is-on { color: var(--c-gold); }
.desk-avis__comment {
    font-size: 12.5px; color: var(--text-soft); font-style: italic;
    line-height: 1.55; margin: 0 0 4px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.desk-avis__date { font-size: 11px; color: var(--muted); }

.desk-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 3px 10px; border-radius: 999px;
    font-size: 10px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .3px; white-space: nowrap;
}
.desk-badge--en_attente, .desk-badge--planifie { background: var(--c-warning-bg); color: var(--c-warning); }
.desk-badge--en_cours, .desk-badge--confirme { background: var(--c-info-bg); color: var(--c-info); }
.desk-badge--terminee, .desk-badge--termine, .desk-badge--acceptee { background: var(--c-success-bg); color: var(--c-success); }
.desk-badge--annulee, .desk-badge--annule, .desk-badge--refusee { background: var(--c-danger-bg); color: var(--c-danger); }

.desk-empty {
    background: #fff; border: 1px dashed var(--border);
    border-radius: 14px; padding: 40px 24px;
    text-align: center; margin-bottom: 14px;
}
.desk-empty__icon {
    width: 60px; height: 60px; margin: 0 auto 16px;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    background: var(--surface); color: var(--muted); font-size: 24px;
}
.desk-empty h4 { font-family: var(--display); font-size: 15px; font-weight: 700; color: var(--ink); margin: 0 0 6px; }
.desk-empty p { font-size: 13px; color: var(--muted); margin: 0; max-width: 320px; margin-inline: auto; }

.desk-side-card {
    background: #fff; border: 1px solid var(--border);
    border-radius: 14px; padding: 18px 20px; margin-bottom: 16px;
}
.desk-side-card__head {
    display: flex; align-items: center; justify-content: space-between;
    gap: 10px; margin-bottom: 14px; padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}
.desk-side-card__head h4 {
    font-family: var(--display); font-size: 14px; font-weight: 700;
    color: var(--ink); margin: 0;
}
.desk-side-card__link {
    font-size: 12px; font-weight: 600; color: var(--rust);
    text-decoration: none; transition: opacity .15s;
}
.desk-side-card__link:hover { opacity: .7; }

.desk-side-list { display: flex; flex-direction: column; gap: 10px; }
.desk-side-item {
    display: flex; align-items: center; gap: 12px;
    padding: 10px; border-radius: 10px;
    text-decoration: none; color: inherit; transition: background .15s;
}
.desk-side-item:hover { background: var(--surface); }
.desk-side-item.is-today { background: var(--c-warning-bg); }
.desk-side-item__date {
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    width: 42px; padding: 5px 3px;
    background: var(--surface); border-radius: 9px; flex-shrink: 0;
}
.desk-side-item.is-today .desk-side-item__date { background: rgba(230, 81, 0, .12); }
.desk-side-item__day { font-family: var(--display); font-size: 15px; font-weight: 800; line-height: 1; color: var(--ink); }
.desk-side-item.is-today .desk-side-item__day { color: var(--c-warning); }
.desk-side-item__month { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); margin-top: 1px; }
.desk-side-item.is-today .desk-side-item__month { color: var(--c-warning); }
.desk-side-item__body { min-width: 0; }
.desk-side-item__title { display: block; font-size: 13px; font-weight: 600; color: var(--ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.desk-side-item__meta { display: block; font-size: 11.5px; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.desk-side-empty {
    display: flex; flex-direction: column; align-items: center; gap: 8px;
    padding: 20px; color: var(--muted); font-size: 13px;
}
.desk-side-empty i { font-size: 24px; opacity: .4; }

.quick-access {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 10px; margin-bottom: 20px;
}
.quick-access__item {
    position: relative; display: flex; flex-direction: column;
    align-items: center; gap: 8px;
    padding: 14px 8px; background: #fff;
    border: 1px solid var(--border); border-radius: 14px;
    text-decoration: none; color: inherit;
    transition: all .2s; text-align: center;
}
.quick-access__item:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,.06);
    border-color: var(--rust);
}
.quick-access__item:active { transform: scale(.97); }
.quick-access__icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.quick-access__icon--blue   { background: #E3F2FD; color: #0D47A1; }
.quick-access__icon--rust   { background: var(--rust-soft); color: var(--rust); }
.quick-access__icon--gold   { background: #FFF8E1; color: #F5A623; }
.quick-access__icon--green  { background: #E8F5E9; color: #1E7A47; }
.quick-access__icon--yellow { background: #FFF3E0; color: #E65100; }
.quick-access__icon--gray   { background: #F2F2F7; color: #6A7280; }
.quick-access__icon--purple { background: #F3E5F5; color: #7B1FA2; }
.quick-access__label {
    font-size: 11.5px; font-weight: 600;
    color: var(--text-soft); line-height: 1.2;
}
.quick-access__badge {
    position: absolute; top: 8px; right: 8px;
    min-width: 20px; height: 20px; padding: 0 6px;
    background: var(--rust); color: #fff;
    border-radius: 999px; font-size: 10.5px; font-weight: 800;
    font-family: var(--display);
    display: flex; align-items: center; justify-content: center;
}
.quick-access__badge--gray { background: #6A7280; }

.ios-stats { display: none; }

@media (max-width: 1200px) {
    .desk-layout { grid-template-columns: 1fr; }
    .desk-layout__side { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
}
@media (max-width: 1024px) {
    .desk-stats { grid-template-columns: repeat(2, 1fr); }
    .quick-access { grid-template-columns: repeat(4, 1fr); }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .agence-dashboard { background: transparent; padding-top: 4px; }

    .desk-hero { display: none !important; }
    .desk-alert { display: none !important; }
    .desk-side-card { display: none !important; }
    .desk-stats { display: none !important; }

    .ios-stats {
        display: flex !important;
        align-items: stretch;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 18px 8px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-stat {
        flex: 1; padding: 0 8px; text-align: center;
        display: flex; flex-direction: column; align-items: center;
        position: relative; text-decoration: none; color: inherit;
    }
    .ios-stat:not(:last-child)::after {
        content: ''; position: absolute; right: 0; top: 20%;
        height: 60%; width: 0.5px; background: var(--ios-separator);
    }
    .ios-stat__value {
        font-family: var(--display); font-size: 24px; font-weight: 800;
        color: var(--ios-label); letter-spacing: -.5px;
    }
    .ios-stat__label {
        font-size: 11px; font-weight: 500;
        color: var(--ios-label-3); margin-top: 2px;
    }

    .desk-section-title {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin: 20px 4px 10px !important;
        padding: 0 !important;
    }
    .desk-section-title span {
        font-size: 17px !important;
        font-weight: 700 !important;
        color: var(--ios-label) !important;
        letter-spacing: -.3px !important;
    }
    .desk-section-title span i {
        font-size: 15px !important;
        margin-right: 6px !important;
    }
    .desk-section-title__link {
        font-size: 13px !important;
        font-weight: 500 !important;
    }

    .quick-access {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
        margin-bottom: 8px;
    }
    .quick-access__item {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px 6px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        gap: 6px;
    }
    .quick-access__item:hover {
        transform: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        border: none;
    }
    .quick-access__item:active { background: #F5F5F7; transform: scale(.97); }

    .quick-access__icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        font-size: 17px;
    }
    .quick-access__label {
        font-size: 10.5px;
        font-weight: 500;
        color: var(--ios-label-2);
    }
    .quick-access__badge {
        top: 6px;
        right: 6px;
        min-width: 18px;
        height: 18px;
        font-size: 10px;
        background: linear-gradient(135deg, var(--rust), #d4754a);
        box-shadow: 0 2px 6px rgba(181,80,42,.3);
    }
    .quick-access__badge--gray {
        background: #8E8E93;
        box-shadow: none;
    }

    .desk-today { display: block !important; }
    .desk-today__grid {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 10px;
    }
    .desk-today__item {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 14px 16px;
        gap: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .desk-today__item:hover { transform: none; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .desk-today__item:active { background: #F5F5F7; }
    .desk-today__item--urgent {
        background: var(--ios-card);
        border: none;
    }
    .desk-today__item--urgent .desk-today__icon {
        background: var(--c-warning-bg);
        color: var(--c-warning);
    }
    .desk-today__icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--rust-soft);
        color: var(--rust);
    }
    .desk-today__body strong {
        font-size: 15px;
        font-weight: 600;
        color: var(--ios-label);
    }
    .desk-today__body span {
        font-size: 13px;
        color: var(--ios-label-3);
    }
    .desk-today__arrow {
        color: var(--ios-label-3);
        opacity: .4;
    }

    .desk-vedette-grid {
        display: flex !important;
        gap: 10px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        margin: 0 -16px 12px;
        padding: 0 16px 8px;
    }
    .desk-vedette-grid::-webkit-scrollbar { display: none; }
    .desk-vedette {
        flex-shrink: 0;
        width: 180px;
        scroll-snap-align: start;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .desk-vedette:hover { transform: none; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .desk-vedette:active { transform: scale(.97); }
    .desk-vedette__body { padding: 10px 12px; }
    .desk-vedette__title { font-size: 13px; }
    .desk-vedette__price { font-size: 13px; }
    .desk-vedette__meta { font-size: 11px; }
    .desk-vedette__badge {
        top: 8px;
        left: 8px;
        font-size: 9px;
        padding: 3px 8px;
    }

    .desk-list {
        display: block !important;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 16px;
        overflow: hidden;
    }
    .desk-list__item {
        padding: 14px 16px;
        gap: 12px;
        border-bottom: 0.5px solid var(--ios-separator);
    }
    .desk-list__item:last-child { border-bottom: none; }
    .desk-list__item:active { background: #F5F5F7; }
    .desk-list__icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        font-size: 14px;
    }
    .desk-list__title {
        font-size: 15px;
        font-weight: 500;
        color: var(--ios-label);
    }
    .desk-list__sub {
        font-size: 13px;
        color: var(--ios-label-3);
    }
    .desk-list__chevron {
        color: var(--ios-label-3);
        opacity: .4;
    }

    .desk-avis {
        background: var(--ios-card);
        border-bottom: 0.5px solid var(--ios-separator);
        border-left: 3px solid var(--rust);
        padding: 14px 16px;
    }
    .desk-avis--positif { border-left-color: var(--c-success); background: var(--ios-card); }
    .desk-avis--neutre  { border-left-color: var(--c-warning); background: var(--ios-card); }
    .desk-avis--negatif { border-left-color: var(--c-danger);  background: var(--ios-card); }
    .desk-avis__avatar { width: 36px; height: 36px; font-size: 14px; }
    .desk-avis__head strong { font-size: 14px; }
    .desk-avis__comment { font-size: 13px; }
    .desk-avis__date { font-size: 11.5px; }

    .desk-empty {
        display: block !important;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 40px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .desk-empty__icon {
        width: 60px;
        height: 60px;
        font-size: 24px;
        background: #EFEFF4;
    }
    .desk-empty h4 { font-size: 16px; font-weight: 600; }
    .desk-empty p { font-size: 13.5px; }

    .desk-layout {
        display: block !important;
        grid-template-columns: 1fr;
    }
    .desk-layout__main { display: block; }
    .desk-layout__side { display: none !important; }
}

@media (max-width: 480px) {
    .ios-stat__value { font-size: 22px; }
    .ios-stat__label { font-size: 10.5px; }

    .quick-access { gap: 6px; }
    .quick-access__item { padding: 12px 4px; }
    .quick-access__icon { width: 38px; height: 38px; font-size: 15px; }
    .quick-access__label { font-size: 10px; }

    .desk-section-title span { font-size: 16px !important; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush