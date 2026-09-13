@extends('layouts.dashboard-agence')

@section('title', 'Contact pour la mise en vedette — DoyaImmo')
@section('page_title', 'Mise en vedette - Contact')
@section('page_sub', 'Finalisez votre demande')

@section('content')
<div class="view active vedette-contact-page">

    {{-- ═══ EN-TÊTE ═══ --}}
    <div class="ios-page-head">
        <div>
            <h2 class="ios-page-head__title">Contactez DoyaImmo</h2>
            <p class="ios-page-head__sub">Finalisez votre demande de mise en vedette</p>
        </div>
        
    </div>

    {{-- ═══ ALERT SUCCESS ═══ --}}
    @if(session('success'))
        <div class="ios-alert ios-alert--success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- ═══ BADGE DEMANDE ═══ --}}
    <div class="ios-info-banner">
        <i class="fa-solid fa-info-circle"></i>
        <span><strong>Demande #{{ $mise->id }}</strong> — En attente de paiement</span>
    </div>

    {{-- ═══ GRILLE ═══ --}}
    <div class="vedette-contact-grid">

        {{-- ─── RÉSUMÉ ─── --}}
        <div class="ios-card">
            <h3 class="ios-card__title">
                <i class="fa-solid fa-file-invoice"></i>
                Résumé de la demande
            </h3>

            <div class="ios-info-list">
                <div class="ios-info-row">
                    <span class="ios-info-row__label">Demande #</span>
                    <span class="ios-info-row__value">{{ $mise->id }}</span>
                </div>
                <div class="ios-info-row">
                    <span class="ios-info-row__label">Bien</span>
                    <span class="ios-info-row__value">{{ $bien->titre }}</span>
                </div>
                <div class="ios-info-row">
                    <span class="ios-info-row__label">Adresse</span>
                    <span class="ios-info-row__value">{{ $bien->quartier }}</span>
                </div>
                <div class="ios-info-row">
                    <span class="ios-info-row__label">Prix du bien</span>
                    <span class="ios-info-row__value">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="ios-info-row">
                    <span class="ios-info-row__label">Durée de vedette</span>
                    <span class="ios-info-row__value">{{ $duree }} jour{{ $duree > 1 ? 's' : '' }}</span>
                </div>
                <div class="ios-info-row ios-info-row--total">
                    <span class="ios-info-row__label">Montant à payer</span>
                    <span class="ios-info-row__value ios-info-row__value--price">
                        {{ number_format($montant, 0, ',', ' ') }} FCFA
                    </span>
                </div>
                <div class="ios-info-row">
                    <span class="ios-info-row__label">Statut</span>
                    <span class="ios-badge ios-badge--warning">En attente de paiement</span>
                </div>
            </div>
        </div>

        {{-- ─── CONTACT ─── --}}
        <div class="ios-card">
            <h3 class="ios-card__title">
                <i class="fa-solid fa-phone"></i>
                Contactez-nous
            </h3>
            <p class="ios-card__text">
                Pour finaliser votre demande de mise en vedette, contactez l'équipe DoyaImmo :
            </p>

            <div class="ios-contact-list">
                <a href="tel:+221774612082" class="ios-contact-item">
                    <div class="ios-contact-item__icon ios-contact-item__icon--rust">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div class="ios-contact-item__body">
                        <div class="ios-contact-item__title">Appelez-nous</div>
                        <div class="ios-contact-item__sub">+221 77 461 20 82</div>
                    </div>
                    <i class="fa-solid fa-chevron-right ios-contact-item__arrow"></i>
                </a>

                <a href="https://wa.me/221774612082?text=Bonjour%2C%20je%20souhaite%20finaliser%20ma%20demande%20de%20mise%20en%20vedette%20%23{{ $mise->id }}%20pour%20le%20bien%20%3A%20{{ urlencode($bien->titre) }}"
                   target="_blank" rel="noopener"
                   class="ios-contact-item ios-contact-item--whatsapp">
                    <div class="ios-contact-item__icon ios-contact-item__icon--whatsapp">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div class="ios-contact-item__body">
                        <div class="ios-contact-item__title">WhatsApp</div>
                        <div class="ios-contact-item__sub">+221 77 461 20 82</div>
                    </div>
                    <i class="fa-solid fa-chevron-right ios-contact-item__arrow"></i>
                </a>

                <a href="mailto:contact@doyaimmo.com?subject=Demande%20de%20mise%20en%20vedette%20%23{{ $mise->id }}&body=Bonjour,%20je%20souhaite%20finaliser%20ma%20demande%20de%20mise%20en%20vedette%20%23{{ $mise->id }}%20pour%20le%20bien%20:%20{{ $bien->titre }}"
                   class="ios-contact-item ios-contact-item--email">
                    <div class="ios-contact-item__icon ios-contact-item__icon--email">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div class="ios-contact-item__body">
                        <div class="ios-contact-item__title">Email</div>
                        <div class="ios-contact-item__sub">contact@doyaimmo.com</div>
                    </div>
                    <i class="fa-solid fa-chevron-right ios-contact-item__arrow"></i>
                </a>
            </div>

            {{-- ═══ INFO À FOURNIR ═══ --}}
            <div class="ios-note">
                <p>
                    <i class="fa-solid fa-info-circle"></i>
                    <strong>Informations à fournir :</strong>
                </p>
                <ul class="ios-note__list">
                    <li>Numéro de demande : <strong>#{{ $mise->id }}</strong></li>
                    <li>Nom de l'agence : <strong>{{ $bien->agence->nom_agence ?? 'Non renseigné' }}</strong></li>
                    <li>Bien concerné : <strong>{{ $bien->titre }}</strong></li>
                    <li>Durée souhaitée : <strong>{{ $duree }} jour{{ $duree > 1 ? 's' : '' }}</strong></li>
                    <li>Montant : <strong>{{ number_format($montant, 0, ',', ' ') }} FCFA</strong></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- ═══ FOOTER INFO ═══ --}}
    <div class="ios-footer-note">
        <i class="fa-regular fa-clock"></i>
        <span>
            Une fois le paiement confirmé, votre bien sera mis en vedette sous 24h.
            La vedette expire automatiquement après la période choisie.
        </span>
    </div>

</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   VEDETTE CONTACT — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.vedette-contact-page {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #F5A623;
    --c-gold-bg:    #FFF8E1;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;

    padding-bottom: 20px;
}

/* ═══ EN-TÊTE ═══ */
.ios-page-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.ios-page-head__title {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    color: var(--ink);
    margin: 0;
    letter-spacing: -.3px;
}
.ios-page-head__sub {
    font-size: 13px;
    color: var(--muted);
    margin: 4px 0 0;
}

/* ═══ ALERT ═══ */
.ios-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 16px;
    font-size: 13.5px;
    font-weight: 500;
    border-left: 4px solid;
}
.ios-alert--success {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-color: var(--c-success);
}
.ios-alert i { font-size: 15px; flex-shrink: 0; }

/* ═══ BANNIÈRE INFO ═══ */
.ios-info-banner {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border: 1px solid #BBDEFB;
    border-radius: 12px;
    font-size: 13px;
    margin-bottom: 18px;
    line-height: 1.4;
}
.ios-info-banner i { font-size: 15px; flex-shrink: 0; }
.ios-info-banner strong { font-weight: 700; }

/* ═══ GRILLE ═══ */
.vedette-contact-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    margin-bottom: 18px;
}

/* ═══ CARTE iOS ═══ */
.ios-card {
    background: var(--ios-card);
    border: 1px solid var(--border);
    border-radius: var(--ios-radius-lg);
    padding: 20px 22px;
    box-shadow: 0 1px 3px rgba(0,0,0,.04);
}
.ios-card__title {
    font-family: var(--display);
    font-size: 15.5px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    letter-spacing: -.2px;
}
.ios-card__title i { color: var(--rust); font-size: 15px; }
.ios-card__text {
    font-size: 13px;
    color: var(--text-soft);
    margin: 0 0 16px;
    line-height: 1.5;
}

/* ═══ LISTE INFO ═══ */
.ios-info-list { display: grid; gap: 0; }
.ios-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid var(--ios-separator);
    flex-wrap: wrap;
}
.ios-info-row:last-child { border-bottom: none; }
.ios-info-row__label {
    font-size: 13px;
    color: var(--ios-label-3);
    flex-shrink: 0;
}
.ios-info-row__value {
    font-size: 13px;
    font-weight: 600;
    color: var(--ios-label);
    text-align: right;
    word-break: break-word;
    max-width: 60%;
}
.ios-info-row--total {
    padding-top: 14px;
    margin-top: 4px;
    border-top: 1px solid var(--ios-separator);
    border-bottom: none;
}
.ios-info-row__value--price {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 800;
    color: var(--rust);
}

/* ═══ BADGE ═══ */
.ios-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
}
.ios-badge--warning {
    background: var(--c-warning-bg);
    color: var(--c-warning);
}

/* ═══ LISTE CONTACT ═══ */
.ios-contact-list {
    display: grid;
    gap: 10px;
    margin-bottom: 16px;
}
.ios-contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: #F7F9FC;
    border-radius: 12px;
    text-decoration: none;
    color: var(--ink);
    transition: background .15s, transform .15s;
    border: 1px solid transparent;
}
.ios-contact-item:hover {
    background: #EFF3F8;
    transform: translateY(-1px);
}
.ios-contact-item:active { transform: scale(.98); }

.ios-contact-item--whatsapp { background: var(--c-success-bg); border-color: #C8E6C9; }
.ios-contact-item--whatsapp:hover { background: #D7EFD9; }

.ios-contact-item--email { background: var(--c-info-bg); border-color: #BBDEFB; }
.ios-contact-item--email:hover { background: #D6E9FB; }

.ios-contact-item__icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #fff;
    font-size: 16px;
}
.ios-contact-item__icon--rust      { background: var(--rust); }
.ios-contact-item__icon--whatsapp  { background: #25D366; font-size: 20px; }
.ios-contact-item__icon--email     { background: var(--c-info); }

.ios-contact-item__body { flex: 1; min-width: 0; }
.ios-contact-item__title {
    font-size: 13.5px;
    font-weight: 600;
    color: var(--ink);
    line-height: 1.2;
}
.ios-contact-item__sub {
    font-size: 12.5px;
    color: var(--muted);
    margin-top: 2px;
}
.ios-contact-item__arrow {
    color: var(--muted);
    font-size: 13px;
    flex-shrink: 0;
}

/* ═══ NOTE ═══ */
.ios-note {
    background: var(--c-gold-bg);
    border-left: 4px solid var(--c-gold);
    border-radius: 10px;
    padding: 12px 14px;
    margin-top: 16px;
}
.ios-note p {
    margin: 0 0 8px;
    font-size: 12.5px;
    color: #5D4037;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ios-note p i { color: var(--c-gold); }
.ios-note__list {
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 12px;
    color: #5D4037;
    line-height: 1.7;
}
.ios-note__list li { padding-left: 0; }
.ios-note__list strong { font-weight: 700; }

/* ═══ FOOTER NOTE ═══ */
.ios-footer-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 16px;
    background: var(--ios-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    font-size: 12.5px;
    color: var(--muted);
    text-align: center;
    line-height: 1.5;
    flex-wrap: wrap;
}
.ios-footer-note i { color: var(--muted); font-size: 14px; }

/* ═══ BOUTONS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
}
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--ghost:active { transform: scale(.97); }
.ios-btn--sm { padding: 8px 14px; font-size: 12.5px; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 820px) {
    .vedette-contact-page { background: transparent; padding-top: 4px; }

    .ios-page-head {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    .ios-page-head__title { font-size: 19px; }
    .ios-page-head .ios-btn { width: 100%; }

    .vedette-contact-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .ios-card {
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-card__title { font-size: 16px; letter-spacing: -.3px; }

    .ios-info-row__value { max-width: 65%; }
    .ios-info-row__value--price { font-size: 18px; }

    .ios-contact-item { padding: 14px; }
    .ios-contact-item__icon { width: 42px; height: 42px; }
    .ios-contact-item__title { font-size: 14.5px; }
    .ios-contact-item__sub { font-size: 13px; }

    .ios-note { padding: 14px 16px; }
    .ios-note p { font-size: 13px; }
    .ios-note__list { font-size: 12.5px; }

    .ios-footer-note { font-size: 13px; padding: 14px 16px; }
}

@media (max-width: 480px) {
    .ios-page-head__title { font-size: 18px; }
    .ios-page-head__sub { font-size: 12.5px; }

    .ios-card { padding: 16px; }
    .ios-card__title { font-size: 15.5px; }

    .ios-info-row { padding: 9px 0; }
    .ios-info-row__label { font-size: 12.5px; }
    .ios-info-row__value { font-size: 12.5px; max-width: 60%; }
    .ios-info-row__value--price { font-size: 16px; }

    .ios-contact-item { padding: 12px; gap: 10px; }
    .ios-contact-item__icon { width: 38px; height: 38px; font-size: 15px; }
    .ios-contact-item__icon--whatsapp { font-size: 18px; }
    .ios-contact-item__title { font-size: 13.5px; }
    .ios-contact-item__sub { font-size: 12px; }

    .ios-note { padding: 12px 14px; }
    .ios-note p { font-size: 12px; }
    .ios-note__list { font-size: 11.5px; line-height: 1.6; }

    .ios-footer-note { font-size: 12px; padding: 12px 14px; gap: 6px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}
</style>
@endpush