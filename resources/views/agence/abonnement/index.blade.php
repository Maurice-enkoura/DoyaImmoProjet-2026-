@extends('layouts.dashboard-agence')

@section('title', 'Abonnement — DoyaImmo')
@section('page_title', 'Abonnement')
@section('page_sub', 'Votre plan et les options disponibles')

@section('content')
<div class="view active ios-abo-page">

    @php
        $isProActif   = isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value === 'pro';
        $isBasicActif = isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value === 'basic';

        $aDejaEuGratuit = isset($agence) && $agence->abonnements()
            ->where('formule', 'basic')
            ->where('statut', false)
            ->exists();
    @endphp

    {{-- ═══ HEADER DESKTOP ═══ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Abonnement</h2>
            <p>Votre plan et les options disponibles</p>
        </div>
    </div>

    {{-- ═══ MESSAGES ═══ --}}
    @if(session('success'))
        <div class="ios-alert ios-alert--success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="ios-alert ios-alert--danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(isset($estNonValidee) && $estNonValidee)
        <div class="ios-alert ios-alert--warning">
            <i class="fa-solid fa-clock"></i>
            <span><strong>En attente de validation.</strong> Votre agence doit être validée par un administrateur.</span>
        </div>
    @endif

    @if(isset($abonnementGratuitExpire) && $abonnementGratuitExpire && !$abonnementActuel && !session('error'))
        <div class="ios-alert ios-alert--warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span><strong>Abonnement gratuit expiré.</strong> <a href="#plans">Passez à l'offre Pro</a></span>
        </div>
    @endif

    {{-- ═══ ABONNEMENT ACTUEL ═══ --}}
    @if(isset($abonnementActuel) && $abonnementActuel)
        <div class="ios-abo-status">
            <div class="ios-abo-status__icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="ios-abo-status__body">
                <span class="ios-abo-status__label">Abonnement actuel</span>
                <span class="ios-abo-status__plan">
                    @php
                        $formuleLabel = is_object($abonnementActuel->formule) && method_exists($abonnementActuel->formule, 'label')
                            ? $abonnementActuel->formule->label()
                            : ucfirst($abonnementActuel->formule);
                    @endphp
                    {{ $formuleLabel }}
                </span>
                <span class="ios-abo-status__date">
                    Valable jusqu'au {{ $abonnementActuel->date_fin->format('d/m/Y') }}
                    @if($abonnementActuel->montant > 0)
                        · {{ number_format($abonnementActuel->montant, 0, ',', ' ') }} FCFA/mois
                    @else
                        · Gratuit
                    @endif
                </span>
            </div>
        </div>
    @endif

    {{-- ═══ PLANS ═══ --}}
    @if(!isset($estNonValidee) || !$estNonValidee)

        <div class="ios-section-header">
            Choisissez votre plan
        </div>

        <div class="ios-plans" id="plans">

            {{-- ═══ PLAN GRATUIT ═══ --}}
            <div class="ios-plan {{ $isBasicActif ? 'is-active' : '' }}">
                <div class="ios-plan__badges">
                    @if($isBasicActif)
                        <span class="ios-badge ios-badge--active">
                            <i class="fa-solid fa-check"></i> Actif
                        </span>
                    @endif
                </div>

                <div class="ios-plan__head">
                    <div class="ios-plan__icon ios-plan__icon--gray">
                        <i class="fa-regular fa-star"></i>
                    </div>
                    <div class="ios-plan__name">Gratuit</div>
                </div>

                <div class="ios-plan__price">
                    <span class="ios-plan__price-value">0</span>
                    <span class="ios-plan__price-currency">FCFA</span>
                    <span class="ios-plan__price-period">/ mois</span>
                </div>

                <div class="ios-plan__pill">
                    <i class="fa-regular fa-envelope"></i>
                    5 offres / mois
                </div>

                <ul class="ios-plan__features">
                    <li><span class="ios-plan__check">✓</span> Création du compte agence</li>
                    <li><span class="ios-plan__check">✓</span> Profil agence</li>
                    <li><span class="ios-plan__check">✓</span> Consultation des demandes</li>
                    <li><span class="ios-plan__check">✓</span> Accès aux demandes pertinentes</li>
                    <li><span class="ios-plan__check">✓</span> 5 propositions par mois</li>
                    <li><span class="ios-plan__check">✓</span> Gestion des rendez-vous</li>
                </ul>

                <div class="ios-plan__cta">
                    @if($isBasicActif)
                        <button class="ios-btn ios-btn--gray ios-btn--full" disabled>
                            Plan actif
                        </button>
                    @elseif($aDejaEuGratuit)
                        <button class="ios-btn ios-btn--gray ios-btn--full" disabled>
                            Déjà utilisé
                        </button>
                        <p class="ios-plan__hint">Vous avez déjà utilisé votre essai gratuit</p>
                    @elseif($isProActif)
                        <button class="ios-btn ios-btn--gray ios-btn--full" disabled>
                            Plan Pro actif
                        </button>
                    @else
                        <form action="{{ route('agence.abonnement.souscrire') }}" method="POST">
                            @csrf
                            <input type="hidden" name="formule" value="basic">
                            <button type="submit" class="ios-btn ios-btn--gray ios-btn--full">
                                Démarrer gratuitement
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- ═══ PLAN PRO ═══ --}}
            <div class="ios-plan ios-plan--pro {{ $isProActif ? 'is-active' : '' }}">
                <div class="ios-plan__badges">
                    @if($isProActif)
                        <span class="ios-badge ios-badge--active">
                            <i class="fa-solid fa-check"></i> Actif
                        </span>
                    @else
                        <span class="ios-badge ios-badge--gold">
                            <i class="fa-solid fa-star"></i> Recommandé
                        </span>
                    @endif
                </div>

                <div class="ios-plan__head">
                    <div class="ios-plan__icon ios-plan__icon--gold">
                        <i class="fa-solid fa-gem"></i>
                    </div>
                    <div class="ios-plan__name">Pro</div>
                </div>

                <div class="ios-plan__price">
                    <span class="ios-plan__price-value">5 000</span>
                    <span class="ios-plan__price-currency">FCFA</span>
                    <span class="ios-plan__price-period">/ mois</span>
                </div>

                <div class="ios-plan__pill ios-plan__pill--gold">
                    <i class="fa-regular fa-envelope"></i>
                    <strong>Offres illimitées</strong>
                </div>

                <ul class="ios-plan__features">
                    <li><span class="ios-plan__check ios-plan__check--gold">✓</span> Tout ce qui est inclus dans Gratuit</li>
                    <li><span class="ios-plan__check ios-plan__check--gold">✓</span> Propositions illimitées</li>
                    <li><span class="ios-plan__check ios-plan__check--gold">✓</span> Publication de biens</li>
                    <li><span class="ios-plan__check ios-plan__check--gold">✓</span> Gestion du portefeuille</li>
                    <li><span class="ios-plan__check ios-plan__check--gold">✓</span> Accès complet aux demandes</li>
                    <li><span class="ios-plan__check ios-plan__check--gold">✓</span> Réception des demandes</li>
                    <li><span class="ios-plan__check ios-plan__check--gold">✓</span> Gestion des rendez-vous</li>
                    <li><span class="ios-plan__check ios-plan__check--gold">✓</span> Profil pro</li>
                    <li><span class="ios-plan__check ios-plan__check--gold">✓</span> Statistiques de base</li>
                    <li>
                        <span class="ios-plan__check ios-plan__check--gold"></span>
                        Mise en vedette
                        <span class="ios-plan__tag">Payant</span>
                    </li>
                </ul>

                <div class="ios-plan__cta">
                    @if($isProActif)
                        <button class="ios-btn ios-btn--gold ios-btn--full" disabled>
                            <i class="fa-solid fa-crown"></i> Plan actif
                        </button>
                        <a href="{{ route('agence.biens.index') }}" class="ios-btn ios-btn--ghost ios-btn--full" style="margin-top:8px;">
                            <i class="fa-solid fa-arrow-right"></i> Gérer mes biens
                        </a>
                    @else
                        <form action="{{ route('agence.abonnement.souscrire') }}" method="POST">
                            @csrf
                            <input type="hidden" name="formule" value="pro">
                            <button type="submit" class="ios-btn ios-btn--gold ios-btn--full">
                                <i class="fa-solid fa-crown"></i> Passer à Pro
                            </button>
                        </form>
                        <p class="ios-plan__hint">Paiement sécurisé via PayDunya</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- ═══ TARIFS VEDETTE ═══ --}}
        @if($isProActif)
            <div class="ios-section-header">
                Mise en vedette
                <span class="ios-section-header__count">Option</span>
            </div>

            <div class="ios-vedette-card">
                <div class="ios-vedette-head">
                    <div class="ios-vedette-head__icon">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div class="ios-vedette-head__text">
                        <strong>Mise en vedette des biens</strong>
                        <span>Service payant disponible avec Pro</span>
                    </div>
                </div>

                <div class="ios-vedette-grid">
                    @foreach($tarifsVedette as $jours => $prix)
                        <div class="ios-vedette-item">
                            <span class="ios-vedette-item__days">{{ $jours }} jour{{ $jours > 1 ? 's' : '' }}</span>
                            <span class="ios-vedette-item__price">{{ number_format($prix, 0, ',', ' ') }} FCFA</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @else

        {{-- ═══ BLOQUÉ ═══ --}}
        <div class="ios-empty">
            <div class="ios-empty__icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h3 class="ios-empty__title">Abonnement indisponible</h3>
            <p class="ios-empty__text">Attendez la validation de votre agence.</p>
            <a href="{{ route('agence.profil') }}" class="ios-btn ios-btn--primary ios-btn--full">
                <i class="fa-solid fa-user"></i> Voir mon profil
            </a>
        </div>

    @endif

    {{-- ═══ HISTORIQUE ═══ --}}
    @if(isset($historique) && $historique && $historique->count() > 0)
        @php
            $historiqueFiltre = $historique->filter(function($item) {
                $estActif = $item->statut && $item->date_fin > now();
                $estRecent = $item->date_fin->diffInDays(now()) <= 30;
                return $estActif || $estRecent;
            });
        @endphp

        @if($historiqueFiltre->count() > 0)
            <div class="ios-section-header">
                Historique
                <span class="ios-section-header__count">{{ $historiqueFiltre->count() }}</span>
            </div>

            <div class="ios-history">
                @foreach($historiqueFiltre as $item)
                    @php
                        $estActif = $item->statut && $item->date_fin > now();
                    @endphp
                    <div class="ios-history-item">
                        <div class="ios-history-item__head">
                            <span class="ios-history-item__plan">
                                @if(is_object($item->formule) && method_exists($item->formule, 'label'))
                                    {{ $item->formule->label() }}
                                @else
                                    {{ ucfirst($item->formule) }}
                                @endif
                            </span>
                            <span class="ios-badge {{ $estActif ? 'ios-badge--success' : 'ios-badge--danger' }}">
                                {{ $estActif ? 'Actif' : 'Expiré' }}
                            </span>
                        </div>
                        <div class="ios-history-item__meta">
                            <span>
                                <i class="fa-regular fa-calendar"></i>
                                {{ $item->date_debut->format('d/m/Y') }} → {{ $item->date_fin->format('d/m/Y') }}
                            </span>
                            <span class="ios-history-item__price">
                                @if($item->montant == 0)
                                    Gratuit
                                @else
                                    {{ number_format($item->montant, 0, ',', ' ') }} FCFA
                                @endif
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   ABONNEMENT — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.ios-abo-page {
    --ios-blue:      #007AFF;
    --ios-green:     #34C759;
    --ios-red:       #FF3B30;
    --ios-orange:    #FF9500;
    --ios-yellow:    #FFCC00;
    --ios-gray:      #8E8E93;
    --ios-gray-3:    #C7C7CC;
    --ios-gray-5:    #E5E5EA;
    --ios-gray-6:    #F2F2F7;

    --ios-bg:        #F2F2F7;
    --ios-card:      #FFFFFF;
    --ios-label:     #000000;
    --ios-label-2:   rgba(60, 60, 67, 0.6);
    --ios-label-3:   rgba(60, 60, 67, 0.3);
    --ios-separator: rgba(60, 60, 67, 0.29);

    --ios-radius:    10px;
    --ios-radius-lg: 12px;

    --gold:          #D4AF37;
    --gold-dark:     #B8942B;
    --gold-light:    rgba(212, 175, 55, 0.12);

    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text",
                 "Helvetica Neue", Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;

    background: var(--ios-bg);
    padding-bottom: 40px;
}

/* ═══ HEADER DESKTOP ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.page-header__left h2 {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.2;
}
.page-header__left p {
    font-size: 13.5px;
    color: var(--muted);
    margin: 4px 0 0;
}

/* ═══ ALERTES ═══ */
.ios-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 14px 16px;
    border-radius: var(--ios-radius-lg);
    margin-bottom: 16px;
    font-size: 13.5px;
    line-height: 1.45;
    letter-spacing: -0.1px;
}
.ios-alert i { font-size: 15px; flex-shrink: 0; margin-top: 1px; }
.ios-alert a { color: inherit; text-decoration: underline; font-weight: 600; }

.ios-alert--success { background: rgba(52, 199, 89, 0.12); color: #175C35; }
.ios-alert--warning { background: rgba(255, 149, 0, 0.12); color: #8B4B15; }
.ios-alert--danger  { background: rgba(255, 59, 48, 0.12); color: #8B1A1A; }

/* ═══ ABONNEMENT ACTUEL ═══ */
.ios-abo-status {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 20px 22px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    margin-bottom: 24px;
}
.ios-abo-status__icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: linear-gradient(135deg, #34C759, #2E9B5F);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(52, 199, 89, .25);
}
.ios-abo-status__body {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.ios-abo-status__label {
    font-size: 12px;
    color: var(--ios-label-3);
    text-transform: uppercase;
    letter-spacing: 0.4px;
    font-weight: 600;
}
.ios-abo-status__plan {
    font-size: 20px;
    font-weight: 700;
    color: var(--ios-label);
    letter-spacing: -0.3px;
}
.ios-abo-status__date {
    font-size: 13.5px;
    color: var(--ios-label-2);
    letter-spacing: -0.1px;
}

/* ═══ SECTION HEADER ═══ */
.ios-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 13.5px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--ios-label-2);
    margin: 28px 0 12px;
    padding: 0 4px;
}
.ios-section-header:first-of-type { margin-top: 0; }
.ios-section-header__count {
    font-size: 12px;
    font-weight: 500;
    background: var(--ios-gray-5);
    color: var(--ios-label-2);
    padding: 2px 10px;
    border-radius: 100px;
    text-transform: none;
    letter-spacing: 0;
}

/* ═══ PLANS (grille desktop) ═══ */
.ios-plans {
    display: grid;
    grid-template-columns: 1fr;
    gap: 16px;
    margin-bottom: 12px;
}

.ios-plan {
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    padding: 24px;
    position: relative;
    border: 2px solid transparent;
    transition: all 0.15s;
    display: flex;
    flex-direction: column;
}
.ios-plan:hover {
    box-shadow: 0 8px 24px rgba(0,0,0,.06);
}
.ios-plan.is-active {
    border-color: var(--rust);
}
.ios-plan--pro.is-active {
    border-color: var(--gold);
    background: linear-gradient(180deg, #FFFDF5 0%, var(--ios-card) 100%);
}

.ios-plan__badges {
    display: flex;
    justify-content: flex-end;
    gap: 6px;
    margin-bottom: 10px;
    min-height: 22px;
}

.ios-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 100px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.ios-badge i { font-size: 9px; }
.ios-badge--active { background: rgba(52, 199, 89, 0.15); color: #007A33; }
.ios-badge--gold   { background: rgba(212, 175, 55, 0.15); color: var(--gold-dark); }
.ios-badge--success { background: rgba(52, 199, 89, 0.15); color: #007A33; }
.ios-badge--danger  { background: rgba(255, 59, 48, 0.15); color: #D70015; }

.ios-plan__head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.ios-plan__icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.ios-plan__icon--gray {
    background: var(--ios-gray-5);
    color: var(--ios-label-2);
}
.ios-plan__icon--gold {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #fff;
    box-shadow: 0 4px 12px rgba(212, 175, 55, .3);
}

.ios-plan__name {
    font-size: 22px;
    font-weight: 700;
    color: var(--ios-label);
    letter-spacing: -0.4px;
}

.ios-plan__price {
    display: flex;
    align-items: baseline;
    gap: 5px;
    margin-bottom: 16px;
}
.ios-plan__price-value {
    font-size: 32px;
    font-weight: 800;
    color: var(--ios-label);
    letter-spacing: -1px;
    line-height: 1;
}
.ios-plan__price-currency {
    font-size: 16px;
    font-weight: 600;
    color: var(--ios-label-2);
}
.ios-plan__price-period {
    font-size: 14px;
    color: var(--ios-label-3);
    margin-left: 2px;
}
.ios-plan--pro .ios-plan__price-value {
    color: var(--gold-dark);
}

.ios-plan__pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: var(--ios-gray-6);
    border-radius: 100px;
    font-size: 13px;
    font-weight: 600;
    color: var(--ios-label-2);
    margin-bottom: 18px;
    letter-spacing: -0.1px;
    align-self: flex-start;
}
.ios-plan__pill i { font-size: 11px; }
.ios-plan__pill--gold {
    background: rgba(212, 175, 55, 0.15);
    color: var(--gold-dark);
}
.ios-plan__pill--gold i { color: var(--gold); }

.ios-plan__features {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
    display: flex;
    flex-direction: column;
    gap: 0;
    flex: 1;
}
.ios-plan__features li {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 9px 0;
    font-size: 13.5px;
    color: var(--ios-label);
    border-bottom: 0.5px solid var(--ios-separator);
    letter-spacing: -0.1px;
}
.ios-plan__features li:last-child { border-bottom: none; }

.ios-plan__check {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    color: var(--ios-label-2);
    font-weight: 700;
    font-size: 12px;
    flex-shrink: 0;
}
.ios-plan__check--gold { color: var(--gold); }

.ios-plan__tag {
    margin-left: auto;
    padding: 2px 8px;
    background: rgba(255, 149, 0, 0.15);
    color: #C93400;
    font-size: 10px;
    font-weight: 700;
    border-radius: 100px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.ios-plan__cta {
    padding-top: 4px;
    margin-top: auto;
}
.ios-plan__hint {
    font-size: 11.5px;
    color: var(--ios-label-3);
    text-align: center;
    margin: 8px 0 0;
    letter-spacing: -0.1px;
}

/* ═══ BOUTONS iOS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 14px 20px;
    border-radius: var(--ios-radius-lg);
    font-size: 15px;
    font-weight: 600;
    font-family: inherit;
    letter-spacing: -0.2px;
    cursor: pointer;
    border: none;
    text-decoration: none;
    transition: opacity 0.1s, transform 0.1s;
    white-space: nowrap;
    min-height: 48px;
    width: 100%;
}
.ios-btn:active { transform: scale(0.98); opacity: 0.85; }
.ios-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.ios-btn--primary { background: var(--rust); color: #fff; }
.ios-btn--gold {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #fff;
    box-shadow: 0 4px 12px rgba(212, 175, 55, .3);
}
.ios-btn--gold:hover { background: linear-gradient(135deg, #E5C04A, var(--gold-dark)); }
.ios-btn--gray {
    background: var(--ios-gray-5);
    color: var(--ios-blue);
}
.ios-btn--ghost {
    background: transparent;
    color: var(--ios-blue);
    border: 1px solid var(--ios-separator);
}
.ios-btn--full { width: 100%; }

/* ═══ VEDETTE ═══ */
.ios-vedette-card {
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    padding: 20px 22px;
}
.ios-vedette-head {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 0.5px solid var(--ios-separator);
}
.ios-vedette-head__icon {
    width: 42px;
    height: 42px;
    border-radius: 11px;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(212, 175, 55, .25);
}
.ios-vedette-head__text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.ios-vedette-head__text strong {
    font-size: 15px;
    font-weight: 600;
    color: var(--ios-label);
    letter-spacing: -0.2px;
}
.ios-vedette-head__text span {
    font-size: 13px;
    color: var(--ios-label-2);
}

.ios-vedette-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 10px;
}
.ios-vedette-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 14px 10px;
    background: var(--ios-gray-6);
    border-radius: 12px;
    text-align: center;
}
.ios-vedette-item__days {
    font-size: 13px;
    font-weight: 600;
    color: var(--ios-label-2);
    letter-spacing: -0.1px;
}
.ios-vedette-item__price {
    font-size: 15px;
    font-weight: 700;
    color: var(--rust);
    letter-spacing: -0.2px;
}

/* ═══ EMPTY ═══ */
.ios-empty {
    text-align: center;
    padding: 60px 24px;
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
}
.ios-empty__icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 18px;
    border-radius: 50%;
    background: var(--ios-gray-6);
    color: var(--ios-gray-3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
}
.ios-empty__title {
    font-size: 18px;
    font-weight: 600;
    color: var(--ios-label);
    margin: 0 0 8px;
    letter-spacing: -0.3px;
}
.ios-empty__text {
    font-size: 14px;
    color: var(--ios-label-2);
    margin: 0 auto 22px;
    max-width: 360px;
    line-height: 1.55;
}

/* ═══ HISTORIQUE ═══ */
.ios-history {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
}
.ios-history-item {
    background: var(--ios-card);
    border-radius: var(--ios-radius-lg);
    padding: 16px 18px;
}
.ios-history-item__head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
}
.ios-history-item__plan {
    font-size: 15.5px;
    font-weight: 600;
    color: var(--ios-label);
    letter-spacing: -0.2px;
}
.ios-history-item__meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    color: var(--ios-label-2);
    flex-wrap: wrap;
}
.ios-history-item__meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.ios-history-item__meta i { font-size: 11.5px; opacity: 0.7; }
.ios-history-item__price {
    font-weight: 700;
    color: var(--ios-label) !important;
}

/* ═══════════════════════════════════════════════════════════
   DESKTOP : conteneur large + grille 2 colonnes
   ═══════════════════════════════════════════════════════════ */
@media (min-width: 1000px) {
    .ios-abo-page {
        max-width: 1100px;
        margin: 0 auto;
        padding: 8px 0 60px;
    }

    /* Plans côte à côte */
    .ios-plans {
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: stretch;
    }

    /* Historique en grille */
    .ios-history {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 14px;
    }
}

@media (max-width: 999px) and (min-width: 821px) {
    .ios-abo-page {
        max-width: 720px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }

    /* Tablette : 2 colonnes aussi */
    .ios-plans {
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .ios-abo-page {
        margin: 0 -16px;
        padding: 0 16px 40px;
        max-width: none;
    }

    .page-header { display: none; }

    /* Plans en colonne */
    .ios-plans {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .ios-plan {
        padding: 20px;
    }
    .ios-plan__price-value { font-size: 30px; }
    .ios-plan__name { font-size: 22px; }

    .ios-history {
        grid-template-columns: 1fr;
        gap: 8px;
    }

    .ios-abo-status {
        padding: 18px;
    }
}

@media (max-width: 480px) {
    .ios-plan { padding: 18px; }
    .ios-plan__name { font-size: 20px; }
    .ios-plan__price-value { font-size: 26px; }
    .ios-plan__features li { font-size: 13px; padding: 8px 0; }

    .ios-vedette-grid { grid-template-columns: repeat(2, 1fr); }

    .ios-abo-status { padding: 14px; gap: 12px; }
    .ios-abo-status__icon { width: 40px; height: 40px; font-size: 18px; }
    .ios-abo-status__plan { font-size: 17px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush