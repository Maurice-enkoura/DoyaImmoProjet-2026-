@extends('layouts.app')

@php
    $titreSEO = $demande->type_bien->label() . ' — ' . $demande->zone_recherchee . ' — DoyaImmo';
    $descriptionSEO = 'Besoin immobilier : ' . $demande->type_bien->label() . ' recherché à ' . $demande->zone_recherchee .
        ' avec un budget de ' . number_format($demande->budget_maximum, 0, ',', ' ') . ' FCFA. ' .
        strip_tags(substr($demande->description, 0, 120)) . '...';
    $urlCanonique = 'https://doyaimmo.com/besoins/' . $demande->slug;
    $ogDescription = 'Besoin immobilier : ' . $demande->type_bien->label() . ' à ' . $demande->zone_recherchee .
        ' - Budget : ' . number_format($demande->budget_maximum, 0, ',', ' ') . ' FCFA.';
@endphp

@section('title', $titreSEO)
@section('meta_description', $descriptionSEO)
@section('canonical', $urlCanonique)
@section('og_title', $titreSEO)
@section('og_description', $ogDescription)
@section('robots', 'index, follow')

@section('content')
<div class="besoin-show">

    @php
        $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;
        $statutLabel = is_object($demande->statut) ? $demande->statut->label() : ucfirst($demande->statut);

        $isNew = $demande->created_at->gt(now()->subHours(48));
        $nbOffres = $demande->propositions->count();

        $isLocation = $demande->type_operation->value === 'location';
        $budgetSuffix = $isLocation ? '/ mois' : '';

        $equipements = collect();
        if ($demande->parking)       $equipements->push(['icon' => 'fa-car',           'label' => 'Parking']);
        if ($demande->meuble)        $equipements->push(['icon' => 'fa-couch',         'label' => 'Meublé']);
        if ($demande->climatisation) $equipements->push(['icon' => 'fa-snowflake',     'label' => 'Climatisation']);
        if ($demande->balcon)        $equipements->push(['icon' => 'fa-door-open',     'label' => 'Balcon']);
        if ($demande->jardin)        $equipements->push(['icon' => 'fa-tree',          'label' => 'Jardin']);
        if ($demande->piscine)       $equipements->push(['icon' => 'fa-water',         'label' => 'Piscine']);
        if ($demande->ascenseur)     $equipements->push(['icon' => 'fa-elevator',      'label' => 'Ascenseur']);
        if ($demande->securite)      $equipements->push(['icon' => 'fa-shield-halved', 'label' => 'Sécurité 24h/24']);
    @endphp

    {{-- ═══════════════════════════════════════════
         NAV + PARTAGE
    ═══════════════════════════════════════════ --}}
    <nav class="besoin-nav">
        <a href="{{ route('besoins.index') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Retour aux besoins
        </a>

        <div class="share-group">
            <span class="share-group__label">Partager</span>
            <button type="button" class="share-btn share-btn--fb" onclick="shareFacebook()" aria-label="Partager sur Facebook">
                <i class="fa-brands fa-facebook-f"></i>
            </button>
            <button type="button" class="share-btn share-btn--wa" onclick="shareWhatsApp()" aria-label="Partager sur WhatsApp">
                <i class="fa-brands fa-whatsapp"></i>
            </button>
            <button type="button" class="share-btn share-btn--link" onclick="copyLink()" aria-label="Copier le lien">
                <i class="fa-solid fa-link"></i>
            </button>
        </div>
    </nav>

    {{-- ═══════════════════════════════════════════
         HERO
    ═══════════════════════════════════════════ --}}
    <header class="hero {{ $isNew ? 'is-new' : '' }}">

        <div class="hero__main">
            <div class="hero__badges">
                <span class="offre-status offre-status--{{ $statutValue }}">
                    <i class="fa-solid fa-circle"></i> {{ $statutLabel }}
                </span>

                @if($isNew)
                    <span class="badge badge--new">
                        <i class="fa-solid fa-bolt"></i> Nouveau
                    </span>
                @endif

                @if($nbOffres > 0)
                    <span class="badge badge--offres">
                        <i class="fa-regular fa-comment-dots"></i>
                        {{ $nbOffres }} offre{{ $nbOffres > 1 ? 's' : '' }} reçue{{ $nbOffres > 1 ? 's' : '' }}
                    </span>
                @else
                    <span class="badge badge--empty">
                        <i class="fa-regular fa-circle"></i> Aucune offre
                    </span>
                @endif
            </div>

            <h1 class="hero__title">
                {{ $demande->type_bien->label() }}
            </h1>

            <div class="hero__meta">
                <span>
                    <i class="fa-solid fa-location-dot"></i>
                    <strong>{{ $demande->zone_recherchee }}</strong>
                </span>
                <span class="hero__meta-sep">•</span>
                <span>
                    <i class="fa-solid fa-handshake"></i>
                    {{ $demande->type_operation->label() }}
                </span>
                <span class="hero__meta-sep">•</span>
                <span>
                    <i class="fa-regular fa-clock"></i>
                    Publié {{ $demande->created_at->diffForHumans() }}
                </span>
            </div>
        </div>

        <div class="hero__budget">
            <span class="hero__budget-label">Budget maximum</span>
            <span class="hero__budget-value">
                {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                <small>FCFA{{ $budgetSuffix }}</small>
            </span>
        </div>

        <div class="hero__actions">
            @auth
                @if(auth()->user()->isAgence())
                    <a href="{{ route('agence.propositions.create', $demande->slug) }}" class="btn btn-rust">
                        <i class="fa-solid fa-paper-plane"></i> Envoyer une offre
                    </a>
                @elseif(auth()->user()->isParticulier())
                    <span class="hero__notice">
                        <i class="fa-solid fa-circle-info"></i>
                        Connectez-vous en tant qu'agence pour proposer un bien.
                    </span>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-rust">
                    <i class="fa-solid fa-right-to-bracket"></i> Se connecter pour faire une offre
                </a>
                <a href="{{ route('register.agence') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-briefcase"></i> Créer un compte agence
                </a>
            @endauth
        </div>
    </header>

    {{-- ═══════════════════════════════════════════
         GRID : CONTENU + SIDEBAR
    ═══════════════════════════════════════════ --}}
    <div class="besoin-show__grid">

        {{-- ═══ COLONNE PRINCIPALE ═══ --}}
        <div class="besoin-show__main">

            {{-- DESCRIPTION --}}
            @if($demande->description)
                <section class="panel">
                    <header class="panel__head">
                        <span class="panel__icon">
                            <i class="fa-solid fa-align-left"></i>
                        </span>
                        <h2>Description du besoin</h2>
                    </header>
                    <p class="panel__text">{{ $demande->description }}</p>
                </section>
            @endif

            {{-- INFORMATIONS --}}
            <section class="panel">
                <header class="panel__head">
                    <span class="panel__icon">
                        <i class="fa-regular fa-list-check"></i>
                    </span>
                    <h2>Informations</h2>
                </header>

                <div class="info-grid">
                    <div class="info-grid__item">
                        <span class="info-grid__label">Type de bien</span>
                        <span class="info-grid__value">{{ $demande->type_bien->label() }}</span>
                    </div>
                    <div class="info-grid__item">
                        <span class="info-grid__label">Zone recherchée</span>
                        <span class="info-grid__value">{{ $demande->zone_recherchee }}</span>
                    </div>
                    <div class="info-grid__item info-grid__item--accent">
                        <span class="info-grid__label">Budget maximum</span>
                        <span class="info-grid__value info-grid__value--prix">
                            {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                            <small>FCFA{{ $budgetSuffix }}</small>
                        </span>
                    </div>
                    <div class="info-grid__item">
                        <span class="info-grid__label">Type de contrat</span>
                        <span class="info-grid__value">{{ $demande->type_operation->label() }}</span>
                    </div>

                    @if($demande->nombre_chambres)
                        <div class="info-grid__item">
                            <span class="info-grid__label">Nombre de chambres</span>
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

                    <div class="info-grid__item">
                        <span class="info-grid__label">Statut</span>
                        <span class="info-grid__value">
                            <i class="fa-solid fa-circle" style="color: var(--c-warning); font-size: 8px; margin-right: 4px;"></i>
                            {{ $demande->statut->label() }}
                        </span>
                    </div>
                    <div class="info-grid__item">
                        <span class="info-grid__label">Publié le</span>
                        <span class="info-grid__value">{{ $demande->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </section>

            {{-- ÉQUIPEMENTS SOUHAITÉS --}}
            @if($equipements->count() > 0)
                <section class="panel">
                    <header class="panel__head">
                        <span class="panel__icon">
                            <i class="fa-solid fa-cogs"></i>
                        </span>
                        <h2>Équipements souhaités</h2>
                        <span class="panel__count">{{ $equipements->count() }}</span>
                    </header>

                    <div class="equipements">
                        @foreach($equipements as $equipement)
                            <span class="equipement">
                                <i class="fa-solid {{ $equipement['icon'] }}"></i>
                                {{ $equipement['label'] }}
                            </span>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- CTA PRINCIPAL (agences connectées) --}}
            @auth
                @if(auth()->user()->isAgence())
                    <section class="cta-panel">
                        <div class="cta-panel__icon">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <div class="cta-panel__content">
                            <h3>Vous avez un bien qui correspond à ce besoin ?</h3>
                            <p>Envoyez votre proposition dès maintenant et démarquez-vous auprès de ce client.</p>
                        </div>
                        <a href="{{ route('agence.propositions.create', $demande->slug) }}" class="btn btn-rust">
                            <i class="fa-solid fa-paper-plane"></i> Envoyer une offre
                        </a>
                    </section>
                @endif
            @endauth
        </div>

        {{-- ═══ SIDEBAR ═══ --}}
        <aside class="besoin-show__sidebar">
            <div class="sidebar__sticky">

                {{-- STATS DU BESOIN --}}
                <section class="panel sidebar-panel">
                    <header class="panel__head">
                        <span class="panel__icon">
                            <i class="fa-solid fa-chart-simple"></i>
                        </span>
                        <h3>Activité</h3>
                    </header>

                    <div class="sidebar-stats">
                        <div class="sidebar-stat">
                            <div class="sidebar-stat__icon">
                                <i class="fa-regular fa-comment-dots"></i>
                            </div>
                            <div class="sidebar-stat__content">
                                <strong>{{ $nbOffres }}</strong>
                                <span>Offre{{ $nbOffres > 1 ? 's' : '' }} reçue{{ $nbOffres > 1 ? 's' : '' }}</span>
                            </div>
                        </div>

                        <div class="sidebar-stat">
                            <div class="sidebar-stat__icon">
                                <i class="fa-regular fa-calendar"></i>
                            </div>
                            <div class="sidebar-stat__content">
                                <strong>{{ $demande->created_at->diffForHumans() }}</strong>
                                <span>Publication</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- CTA AGENCE (sidebar) --}}
                @auth
                    @if(auth()->user()->isAgence())
                        <section class="panel sidebar-panel sidebar-panel--cta">
                            <div class="sidebar-panel__icon">
                                <i class="fa-solid fa-paper-plane"></i>
                            </div>
                            <h3 class="sidebar-panel__title">Envoyez votre offre</h3>
                            <p class="sidebar-panel__text">
                                Proposez un bien correspondant à ce besoin et recevez une réponse rapide.
                            </p>
                            <a href="{{ route('agence.propositions.create', $demande->slug) }}" class="btn btn-rust btn-block">
                                <i class="fa-solid fa-paper-plane"></i> Envoyer une offre
                            </a>
                        </section>
                    @elseif(auth()->user()->isParticulier())
                        <section class="panel sidebar-panel">
                            <div class="sidebar-panel__icon">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <h3 class="sidebar-panel__title">Vous êtes un particulier</h3>
                            <p class="sidebar-panel__text">
                                Seules les agences peuvent envoyer des offres sur ce besoin.
                            </p>
                            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-ghost btn-block">
                                <i class="fa-solid fa-plus"></i> Publier mon propre besoin
                            </a>
                        </section>
                    @endif
                @else
                    <section class="panel sidebar-panel sidebar-panel--cta">
                        <div class="sidebar-panel__icon">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <h3 class="sidebar-panel__title">Vous êtes une agence ?</h3>
                        <p class="sidebar-panel__text">
                            Créez votre compte gratuitement pour proposer vos biens sur DoyaImmo.
                        </p>
                        <a href="{{ route('register.agence') }}" class="btn btn-rust btn-block">
                            <i class="fa-solid fa-user-plus"></i> Créer mon compte
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-block" style="margin-top:8px;">
                            <i class="fa-solid fa-right-to-bracket"></i> J'ai déjà un compte
                        </a>
                    </section>
                @endauth

                {{-- PROPOSITIONS REÇUES --}}
                @auth
                    @if(auth()->user()->isParticulier() && $nbOffres > 0)
                        <section class="panel sidebar-panel">
                            <header class="panel__head">
                                <span class="panel__icon">
                                    <i class="fa-regular fa-comment-dots"></i>
                                </span>
                                <h3>Propositions</h3>
                                <span class="panel__count">{{ $nbOffres }}</span>
                            </header>

                            <div class="propositions-list">
                                @foreach($demande->propositions->take(3) as $proposition)
                                    <div class="proposition-item">
                                        <div class="proposition-item__avatar">
                                            {{ strtoupper(mb_substr($proposition->agence->nom_agence ?? 'A', 0, 1)) }}
                                        </div>
                                        <div class="proposition-item__info">
                                            <strong>{{ $proposition->agence->nom_agence }}</strong>
                                            <span>
                                                {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                                            </span>
                                        </div>
                                        <span class="offre-status offre-status--{{ is_object($proposition->statut) ? $proposition->statut->value : $proposition->statut }}">
                                            {{ is_object($proposition->statut) ? $proposition->statut->label() : ucfirst($proposition->statut) }}
                                        </span>
                                    </div>
                                @endforeach

                                @if($nbOffres > 3)
                                    <a href="{{ route('particulier.propositions.index') }}" class="proposition-more">
                                        + {{ $nbOffres - 3 }} autre{{ $nbOffres - 3 > 1 ? 's' : '' }} proposition{{ $nbOffres - 3 > 1 ? 's' : '' }}
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </section>
                    @endif
                @endauth
            </div>
        </aside>
    </div>
</div>
@endsection


@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════
   SHARE
═══════════════════════════════════════════════════════════ */
function shareFacebook() {
    const url = encodeURIComponent(window.location.href);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const text = encodeURIComponent("Découvrez ce besoin immobilier sur DoyaImmo !");
    window.open(`https://wa.me/?text=${text}%20${url}`, '_blank', 'width=600,height=400');
}

function copyLink() {
    const url = window.location.href;
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => showToast('🔗 Lien copié !'));
    } else {
        const input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        showToast('🔗 Lien copié !');
    }
}

function showToast(message) {
    const t = document.createElement('div');
    t.className = 'toast';
    t.textContent = message;
    document.body.appendChild(t);
    setTimeout(() => t.classList.add('is-out'), 2500);
    setTimeout(() => t.remove(), 2800);
}
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE DÉTAIL D'UN BESOIN — Publique
   ═══════════════════════════════════════════════════════════ */

.besoin-show {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #D4AF37;
    --c-whatsapp:   #25D366;
    --surface:      #F7F9FC;
    --radius:       16px;

    max-width: 1200px;
    margin: 0 auto;
    padding: 32px 24px 40px;
}

/* ═══ NAV ═══ */
.besoin-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    color: var(--muted);
    text-decoration: none;
    transition: color .2s;
    font-weight: 500;
}
.back-link:hover { color: #B85C3A; }
.back-link i { font-size: 12px; }

.share-group {
    display: flex;
    align-items: center;
    gap: 8px;
}
.share-group__label {
    font-size: 12.5px;
    color: var(--muted);
    margin-right: 4px;
    font-weight: 500;
}

.share-btn {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform .2s, box-shadow .2s;
    font-size: 14px;
}
.share-btn:hover {
    transform: scale(1.08);
    box-shadow: 0 4px 12px rgba(0, 0, 0, .15);
}
.share-btn--fb   { background: #1877F2; color: #fff; }
.share-btn--wa   { background: var(--c-whatsapp); color: #fff; }
.share-btn--link { background: var(--border); color: var(--text-soft); }

/* ═══ HERO ═══ */
.hero {
    position: relative;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 26px 28px;
    margin-bottom: 22px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 24px;
    align-items: start;
    overflow: hidden;
}
.hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 4px;
    background: var(--c-warning);
}
.hero.is-new {
    border-color: rgba(181, 80, 42, .3);
    background: linear-gradient(135deg, #FFFBF7 0%, #fff 55%);
}
.hero.is-new::before {
    background: linear-gradient(90deg, var(--rust), #d4754a);
}

.hero__main { min-width: 0; }

.hero__badges {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 11px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
}
.badge i { font-size: 9px; }

.badge--new {
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    box-shadow: 0 2px 6px rgba(181, 80, 42, .3);
    animation: pulseNew 2.2s ease-in-out infinite;
}
@keyframes pulseNew {
    0%, 100% { opacity: 1; }
    50%      { opacity: .85; }
}

.badge--offres {
    background: var(--c-info-bg);
    color: var(--c-info);
    border: 1px solid #BBDEFB;
}

.badge--empty {
    background: var(--surface);
    color: var(--muted);
    border: 1px solid var(--border);
    font-weight: 600;
}

.hero__title {
    font-family: var(--display);
    font-size: clamp(24px, 3vw, 30px);
    font-weight: 800;
    margin: 0 0 12px;
    color: var(--ink);
    line-height: 1.2;
    letter-spacing: -.02em;
    word-break: break-word;
}

.hero__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13.5px;
    color: var(--text-soft);
}
.hero__meta i {
    font-size: 12px;
    margin-right: 5px;
    color: var(--rust);
    opacity: .85;
}
.hero__meta strong {
    color: var(--ink);
    font-weight: 700;
}
.hero__meta-sep { opacity: .4; }

.hero__budget {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    padding-left: 24px;
    border-left: 1px dashed var(--border);
    flex-shrink: 0;
}
.hero__budget-label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .5px;
    font-weight: 700;
}
.hero__budget-value {
    font-family: var(--display);
    font-size: clamp(24px, 3vw, 32px);
    font-weight: 800;
    color: #B85C3A;
    line-height: 1.1;
    white-space: nowrap;
}
.hero__budget-value small {
    font-size: 13px;
    font-weight: 700;
    opacity: .75;
    margin-left: 3px;
}

.hero__actions {
    grid-column: 1 / -1;
    display: flex;
    gap: 10px;
    padding-top: 18px;
    border-top: 1px dashed var(--border);
    flex-wrap: wrap;
}

.hero__notice {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid #BBDEFB;
}

/* ═══ GRID ═══ */
.besoin-show__grid {
    display: grid;
    grid-template-columns: minmax(0, 1.6fr) minmax(280px, 1fr);
    gap: 22px;
    align-items: start;
}

.besoin-show__main { min-width: 0; }
.besoin-show__sidebar { min-width: 0; }

.sidebar__sticky {
    position: sticky;
    top: 90px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

/* ═══ PANNEAUX ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 22px 24px;
    margin-bottom: 16px;
}
.panel:last-child { margin-bottom: 0; }

.panel__head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
}
.panel__icon {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: rgba(181, 80, 42, .1);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
}
.panel__head h2,
.panel__head h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
    flex: 1;
    min-width: 0;
}
.panel__count {
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: rgba(181, 80, 42, .1);
    color: var(--rust);
    border-radius: 999px;
}

.panel__text {
    font-size: 14px;
    line-height: 1.75;
    color: var(--text-soft);
    margin: 0;
    white-space: pre-line;
}

/* ═══ INFO GRID ═══ */
.info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.info-grid__item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 11px 14px;
    background: var(--surface);
    border-radius: 10px;
    border: 1px solid transparent;
    transition: border-color .2s;
}
.info-grid__item:hover { border-color: var(--border); }
.info-grid__item--accent {
    background: linear-gradient(135deg, #F5E6DF, #FFFBF7);
    border-color: rgba(181, 80, 42, .25);
}
.info-grid__label {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 700;
}
.info-grid__value {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink);
}
.info-grid__value--prix {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 800;
    color: #B85C3A;
}
.info-grid__value--prix small {
    font-size: 11px;
    font-weight: 600;
    opacity: .75;
    margin-left: 2px;
}

/* ═══ ÉQUIPEMENTS ═══ */
.equipements {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.equipement {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 7px 14px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border: 1px solid #BBDEFB;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
}
.equipement i { font-size: 12px; }

/* ═══ CTA PANEL (dans le flux) ═══ */
.cta-panel {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    background: linear-gradient(135deg, #F5E6DF 0%, #FFFBF7 100%);
    border: 1px solid rgba(181, 80, 42, .2);
    border-radius: var(--radius);
    flex-wrap: wrap;
}
.cta-panel__icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    background: #fff;
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .15);
}
.cta-panel__content {
    flex: 1;
    min-width: 200px;
}
.cta-panel__content h3 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 3px;
    color: var(--ink);
}
.cta-panel__content p {
    font-size: 13px;
    color: var(--text-soft);
    margin: 0;
    line-height: 1.5;
}

/* ═══ SIDEBAR PANELS ═══ */
.sidebar-panel {
    padding: 18px 20px;
}
.sidebar-panel--cta {
    background: linear-gradient(135deg, #F5E6DF 0%, #FFFBF7 100%);
    border-color: rgba(181, 80, 42, .2);
    text-align: center;
}
.sidebar-panel__icon {
    width: 44px; height: 44px;
    margin: 0 auto 12px;
    border-radius: 50%;
    background: #fff;
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .15);
}
.sidebar-panel__title {
    font-family: var(--display);
    font-size: 14.5px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--ink);
}
.sidebar-panel__text {
    font-size: 12.5px;
    color: var(--text-soft);
    margin: 0 0 14px;
    line-height: 1.55;
}

/* Stats sidebar */
.sidebar-stats {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.sidebar-stat {
    display: flex;
    align-items: center;
    gap: 12px;
}
.sidebar-stat__icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: var(--surface);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.sidebar-stat__content {
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.sidebar-stat__content strong {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 800;
    color: var(--ink);
    line-height: 1.2;
}
.sidebar-stat__content span {
    font-size: 11.5px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .3px;
    font-weight: 600;
}

/* Propositions list */
.propositions-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.proposition-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: var(--surface);
    border-radius: 10px;
    border: 1px solid var(--border);
}
.proposition-item__avatar {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    flex-shrink: 0;
}
.proposition-item__info {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 1px;
}
.proposition-item__info strong {
    font-size: 12.5px;
    font-weight: 700;
    color: var(--ink);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.proposition-item__info span {
    font-size: 11.5px;
    color: var(--rust);
    font-weight: 700;
    font-family: var(--display);
}

.proposition-more {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    padding: 8px 12px;
    background: transparent;
    color: var(--rust);
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: background .2s, gap .2s;
    justify-content: center;
}
.proposition-more:hover {
    background: rgba(181, 80, 42, .08);
    gap: 9px;
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
.offre-status i { font-size: 5px; }
.offre-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.offre-status--en_cours   { background: var(--c-info-bg);    color: var(--c-info); }
.offre-status--terminee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--annulee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.offre-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.offre-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 11px 20px;
    border-radius: 11px;
    font-size: 13.5px;
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
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
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
.btn-block { width: 100%; }

/* ═══ TOAST ═══ */
.toast {
    position: fixed;
    bottom: 100px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--ink);
    color: #fff;
    padding: 12px 22px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 600;
    z-index: 9999;
    box-shadow: 0 8px 32px rgba(0, 0, 0, .25);
    animation: toastIn .3s ease;
    pointer-events: none;
}
@keyframes toastIn {
    from { opacity: 0; transform: translateX(-50%) translateY(20px); }
    to   { opacity: 1; transform: translateX(-50%) translateY(0); }
}
.toast.is-out { animation: toastOut .3s ease forwards; }
@keyframes toastOut {
    to { opacity: 0; transform: translateX(-50%) translateY(20px); }
}

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .besoin-show { padding: 24px 20px 40px; }

    .besoin-show__grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .sidebar__sticky { position: static; }

    .info-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 768px) {
    .besoin-show { padding: 18px 16px 40px; }

    .hero {
        grid-template-columns: 1fr;
        gap: 18px;
        padding: 22px;
    }
    .hero__budget {
        align-items: flex-start;
        padding-left: 0;
        padding-top: 16px;
        border-left: none;
        border-top: 1px dashed var(--border);
        width: 100%;
    }
    .hero__budget-value { font-size: 28px; }
    .hero__title { font-size: 22px; }
    .hero__actions { flex-direction: column; }
    .hero__actions .btn { width: 100%; }

    .panel { padding: 18px 20px; }
    .panel__head h2,
    .panel__head h3 { font-size: 15px; }

    .info-grid { grid-template-columns: 1fr; }

    .cta-panel {
        flex-direction: column;
        text-align: center;
        align-items: stretch;
    }
    .cta-panel__icon { margin: 0 auto; }
    .cta-panel .btn { width: 100%; }
}

@media (max-width: 480px) {
    .besoin-show { padding: 14px 14px 40px; }

    .besoin-nav { flex-direction: column; align-items: stretch; gap: 10px; }
    .share-group { justify-content: flex-start; }

    .hero { padding: 18px; }
    .hero__title { font-size: 19px; }
    .hero__meta { font-size: 12.5px; gap: 6px; }
    .hero__budget-value { font-size: 24px; }
    .hero__budget-value small { font-size: 11px; }

    .badge { font-size: 10px; padding: 3px 9px; }

    .panel { padding: 16px 18px; }
    .panel__head { padding-bottom: 12px; margin-bottom: 14px; }
    .panel__head h2,
    .panel__head h3 { font-size: 14px; }

    .panel__text { font-size: 13px; }

    .info-grid__item { padding: 10px 12px; }
    .info-grid__value { font-size: 13px; }
    .info-grid__value--prix { font-size: 15px; }

    .equipement { font-size: 12px; padding: 6px 12px; }
    .equipement i { font-size: 11px; }

    .proposition-item { padding: 9px; }
    .proposition-item__avatar { width: 30px; height: 30px; font-size: 12px; }

    .btn { font-size: 12.5px; padding: 10px 16px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
    .badge--new { animation: none !important; }
}
</style>
@endpush