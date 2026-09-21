@extends('layouts.dashboard-agence')

@section('title', 'Mes rendez-vous — DoyaImmo')
@section('page_title', 'Mes rendez-vous')
@section('page_sub', 'Vos visites planifiées avec les clients')

@section('content')
<div class="view active rdv-page">

    @php
        $total        = $stats['total']         ?? 0;
        $nbPlanifie   = $stats['planifie']      ?? 0;
        $nbConfirme   = $stats['confirme']      ?? 0;
        $nbTermine    = $stats['termine']       ?? 0;
        $nbClientAbs  = $stats['client_absent'] ?? 0;

        $statutIcons = [
            'planifie'      => 'fa-clock',
            'confirme'      => 'fa-check-circle',
            'termine'       => 'fa-flag-checkered',
            'annule'        => 'fa-ban',
            'client_absent' => 'fa-user-slash',
        ];
    @endphp

    {{-- ═══ STATS ═══ --}}
    <div class="rdv-stats">
        <div class="rdv-stat">
            <span class="rdv-stat__value">{{ $total }}</span>
            <span class="rdv-stat__label">Total</span>
        </div>
        <div class="rdv-stat">
            <span class="rdv-stat__value">{{ $nbPlanifie }}</span>
            <span class="rdv-stat__label">En attente</span>
        </div>
        <div class="rdv-stat">
            <span class="rdv-stat__value">{{ $nbConfirme }}</span>
            <span class="rdv-stat__label">Confirmés</span>
        </div>
        <div class="rdv-stat">
            <span class="rdv-stat__value">{{ $nbTermine }}</span>
            <span class="rdv-stat__label">Terminés</span>
        </div>
    </div>

    @if($rendezVous->count() > 0)

        <div class="rdv-list">
            @foreach($rendezVous as $rdv)

                @php
                    $statutValue = is_object($rdv->statut)
                        ? $rdv->statut->value
                        : (string) $rdv->statut;
                    $statutLabel = is_object($rdv->statut)
                        ? $rdv->statut->label()
                        : ucfirst($statutValue);

                    $bien   = $rdv->proposition->bien ?? null;
                    $client = $rdv->particulier->user ?? null;

                    // ✅ Téléphone TOUJOURS visible dès qu'il y a un RDV
                    $hasTel = $client && !empty($client->telephone);

                    $dateRdv    = \Carbon\Carbon::parse($rdv->date_visite);
                    $isToday    = $dateRdv->isToday();
                    $isTomorrow = $dateRdv->isTomorrow();

                    // ✅ Fenêtre de validation du code (24h avant)
                    $dateHeureVisite = \Carbon\Carbon::parse($rdv->date_visite)
                        ->setTimeFromTimeString($rdv->heure_visite ?? '00:00:00');
                    $fenetreValidationOuverte = now()->greaterThanOrEqualTo(
                        $dateHeureVisite->copy()->subHours(24)
                    );

                    $peutValiderCode = $statutValue === 'confirme'
                        && !$rdv->code_valide
                        && $fenetreValidationOuverte;

                    $peutMarquerAbsent = $statutValue === 'confirme'
                        && !$rdv->code_valide;
                @endphp

                <article class="rdv-card rdv-card--{{ $statutValue }}">

                    {{-- En-tête --}}
                    <header class="rdv-card__header">
                        <div class="rdv-date-pill">
                            <span class="rdv-date-pill__day">{{ $dateRdv->format('d') }}</span>
                            <span class="rdv-date-pill__month">{{ strtoupper($dateRdv->translatedFormat('M')) }}</span>
                        </div>
                        <div class="rdv-card__heading">
                            <h3 class="rdv-card__title">{{ $bien->titre ?? 'Visite' }}</h3>
                        </div>
                    </header>

                    {{-- Meta --}}
                    <div class="rdv-card__meta">
                        <span class="rdv-meta-item">
                            <i class="fa-regular fa-clock"></i>
                            {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                            @if($isToday)
                                · Aujourd'hui
                            @elseif($isTomorrow)
                                · Demain
                            @endif
                        </span>
                        @if($bien && $bien->quartier)
                            <span class="rdv-meta-sep">·</span>
                            <span class="rdv-meta-item">
                                <i class="fa-solid fa-location-dot"></i>
                                {{ $bien->quartier }}
                            </span>
                        @endif
                        <span class="rdv-meta-sep">·</span>
                        <span class="rdv-status rdv-status--{{ $statutValue }}">
                            {{ $statutLabel }}
                        </span>
                    </div>

                    {{-- Client — téléphone TOUJOURS visible --}}
                    <div class="rdv-card__client">
                        <div class="rdv-client-avatar">
                            {{ strtoupper(mb_substr($client->prenom ?? 'C', 0, 1)) }}
                        </div>
                        <div class="rdv-client-info">
                            <strong>{{ $client->prenom ?? 'Client' }} {{ $client->nom ?? '' }}</strong>

                            @if($hasTel)
                                <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="rdv-client-phone">
                                    <i class="fa-solid fa-phone"></i>
                                    {{ $client->telephone }}
                                </a>
                            @else
                                <span class="rdv-client-locked">
                                    Numéro non renseigné
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- ═══ ACTIONS ═══ --}}
                    <footer class="rdv-card__actions">

                        @if($statutValue === 'planifie')
                            <form action="{{ route('agence.rendezvous.confirmer', $rdv) }}"
                                  method="POST" class="rdv-primary-form">
                                @csrf
                                <button type="submit" class="rdv-btn rdv-btn--primary">
                                    Confirmer le rendez-vous
                                </button>
                            </form>

                            @if($hasTel)
                                <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="rdv-btn rdv-btn--success">
                                    <i class="fa-solid fa-phone"></i> Appeler le client
                                </a>
                            @endif

                            <div class="rdv-secondary-actions">
                                <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="rdv-link">
                                    Détails
                                </a>
                                <span class="rdv-link-sep">·</span>
                                <form action="{{ route('agence.rendezvous.annuler', $rdv) }}"
                                      method="POST" class="rdv-inline-form">
                                    @csrf
                                    <button type="submit" class="rdv-link rdv-link--danger"
                                            onclick="return confirm('Annuler ce rendez-vous ?')">
                                        Annuler
                                    </button>
                                </form>
                            </div>

                        @elseif($statutValue === 'confirme')

                            @if($rdv->code_valide)
                                <span class="rdv-state-pill rdv-state-pill--success">
                                    Visite validée
                                </span>
                                @if($hasTel)
                                    <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="rdv-btn rdv-btn--success">
                                        <i class="fa-solid fa-phone"></i> Appeler le client
                                    </a>
                                @endif

                            @elseif(!$fenetreValidationOuverte)
                                @if($hasTel)
                                    <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="rdv-btn rdv-btn--success">
                                        <i class="fa-solid fa-phone"></i> Appeler le client
                                    </a>
                                @endif

                                <span class="rdv-state-pill rdv-state-pill--info">
                                    Validation possible 24h avant la visite
                                </span>

                            @else
                                @if($hasTel)
                                    <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="rdv-btn rdv-btn--success">
                                        <i class="fa-solid fa-phone"></i> Appeler le client
                                    </a>
                                @endif

                                <form action="{{ route('agence.rendezvous.valider-code', $rdv) }}"
                                      method="POST" class="rdv-primary-form">
                                    @csrf
                                    <div class="rdv-code-input">
                                        <label for="code-{{ $rdv->id }}" class="rdv-code-input__label">
                                            Code de visite du client
                                        </label>
                                        <div class="rdv-code-input__wrapper">
                                            <input
                                                type="text"
                                                id="code-{{ $rdv->id }}"
                                                name="code_visite"
                                                class="rdv-code-input__field"
                                                placeholder="000000"
                                                maxlength="6"
                                                inputmode="numeric"
                                                pattern="[0-9]{6}"
                                                autocomplete="off"
                                                required
                                            >
                                            <button type="submit" class="rdv-code-input__btn">
                                                Valider
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            @endif

                            @if(!$rdv->code_valide)
                                <div class="rdv-secondary-actions">
                                    <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="rdv-link">
                                        Détails
                                    </a>

                                    @if($peutMarquerAbsent)
                                        <span class="rdv-link-sep">·</span>
                                        <form action="{{ route('agence.rendezvous.absent', $rdv) }}"
                                              method="POST" class="rdv-inline-form">
                                            @csrf
                                            <button type="submit" class="rdv-link rdv-link--warning"
                                                    onclick="return confirm('Marquer le client comme ABSENT ?\n\nLe RDV passera à CLIENT_ABSENT.\nLe particulier pourra replanifier.')">
                                                Client absent
                                            </button>
                                        </form>
                                    @endif

                                    <span class="rdv-link-sep">·</span>
                                    <form action="{{ route('agence.rendezvous.annuler', $rdv) }}"
                                          method="POST" class="rdv-inline-form">
                                        @csrf
                                        <button type="submit" class="rdv-link rdv-link--danger"
                                                onclick="return confirm('Annuler ce rendez-vous ?\n\nToute la sélection sera réinitialisée.')">
                                            Annuler
                                        </button>
                                    </form>
                                </div>
                            @endif

                        @elseif($statutValue === 'termine')
                            <span class="rdv-state-pill rdv-state-pill--success">
                                Visite effectuée
                            </span>
                            @if($hasTel)
                                <a href="tel:{{ preg_replace('/\s+/', '', $client->telephone) }}" class="rdv-btn rdv-btn--success">
                                    <i class="fa-solid fa-phone"></i> Contacter le client
                                </a>
                            @endif
                            <div class="rdv-secondary-actions">
                                <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="rdv-link">
                                    Voir le détail
                                </a>
                            </div>

                        @elseif($statutValue === 'annule')
                            <span class="rdv-state-pill rdv-state-pill--danger">
                                Visite annulée
                            </span>
                            <div class="rdv-secondary-actions">
                                <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="rdv-link">
                                    Voir le détail
                                </a>
                            </div>

                        @elseif($statutValue === 'client_absent')
                            <span class="rdv-state-pill rdv-state-pill--muted">
                                Client absent
                            </span>
                            <div class="rdv-secondary-actions">
                                <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="rdv-link">
                                    Voir le détail
                                </a>
                            </div>
                        @endif

                    </footer>
                </article>
            @endforeach
        </div>

        <div class="pagination-wrapper">
            {{ $rendezVous->appends(request()->query())->links() }}
        </div>

    @else
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-calendar-days"></i>
            </div>
            <h3>Aucun rendez-vous actif</h3>
            <p>Les rendez-vous planifiés et confirmés apparaîtront ici.</p>
            <a href="{{ route('agence.biens.index') }}" class="rdv-btn rdv-btn--primary">
                Voir mes biens
            </a>
        </div>
    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MES RENDEZ-VOUS (Agence) — Design épuré
   ═══════════════════════════════════════════════════════════ */

.rdv-page {
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
    --ios-card:     #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ STATS ═══ */
.rdv-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}
.rdv-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 16px 12px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    position: relative;
}
.rdv-stat__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.5px;
    line-height: 1;
}
.rdv-stat__label {
    font-size: 11.5px;
    font-weight: 500;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .3px;
}

/* ═══ LISTE ═══ */
.rdv-list { display: flex; flex-direction: column; gap: 14px; }

/* ═══ CARTE ═══ */
.rdv-card {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 18px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
}

/* ═══ HEADER ═══ */
.rdv-card__header { display: flex; align-items: center; gap: 14px; }
.rdv-date-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 60px; height: 60px;
    border-radius: 14px;
    flex-shrink: 0;
    background: #F5F5F7;
}
.rdv-date-pill__day {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -.5px;
    color: var(--text);
}
.rdv-date-pill__month {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    margin-top: 2px;
    color: var(--muted);
}

.rdv-card__heading { flex: 1; min-width: 0; }
.rdv-card__title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
    line-height: 1.3;
    letter-spacing: -.3px;
}

/* ═══ META ═══ */
.rdv-card__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13px;
    color: var(--text-soft);
}
.rdv-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-weight: 500;
}
.rdv-meta-item i { font-size: 12px; opacity: .8; }
.rdv-meta-sep { color: var(--border); font-weight: 700; }

.rdv-status {
    display: inline-flex;
    align-items: center;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    white-space: nowrap;
    background: #F5F5F7;
    color: #4A5260;
}
.rdv-status--planifie      { background: var(--c-warning-bg); color: var(--c-warning); }
.rdv-status--confirme      { background: var(--c-info-bg);    color: var(--c-info); }
.rdv-status--termine       { background: var(--c-success-bg); color: var(--c-success); }
.rdv-status--annule        { background: var(--c-danger-bg);  color: var(--c-danger); }
.rdv-status--client_absent { background: #F5F5F7;             color: #6C6C70; }

/* ═══ CLIENT ═══ */
.rdv-card__client {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 10px 13px;
    background: #F9FAFB;
    border-radius: 12px;
}
.rdv-client-avatar {
    width: 38px; height: 38px;
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
.rdv-client-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.rdv-client-info strong {
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
}
.rdv-client-phone {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 13px;
    font-weight: 500;
    color: var(--c-success);
    text-decoration: none;
}
.rdv-client-phone:hover { text-decoration: underline; }
.rdv-client-phone i { font-size: 11px; }
.rdv-client-locked {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    color: var(--muted);
    font-style: italic;
}

/* ═══ FORMULAIRE CODE ═══ */
.rdv-code-input {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 14px 16px;
    background: #F9FAFB;
    border: 1px solid var(--border);
    border-radius: 14px;
}
.rdv-code-input__label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--text-soft);
}
.rdv-code-input__wrapper {
    display: flex;
    gap: 8px;
    align-items: stretch;
}
.rdv-code-input__field {
    flex: 1;
    min-width: 0;
    padding: 11px 14px;
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    letter-spacing: 7px;
    color: var(--text);
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    text-align: center;
    transition: border-color .15s;
    outline: none;
}
.rdv-code-input__field::placeholder {
    color: #C7C7CC;
    letter-spacing: 7px;
    font-weight: 600;
}
.rdv-code-input__field:focus {
    border-color: var(--rust);
}
.rdv-code-input__btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 11px 18px;
    background: var(--rust);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color .15s;
    white-space: nowrap;
}
.rdv-code-input__btn:hover { background: #9A4523; }

/* ═══ ACTIONS ═══ */
.rdv-card__actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.rdv-primary-form, .rdv-inline-form { margin: 0; display: block; }

.rdv-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 13px 20px;
    border-radius: 12px;
    font-size: 14.5px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    font-family: inherit;
    transition: background-color .15s, transform .1s;
    width: 100%;
}
.rdv-btn:active { transform: scale(.98); }
.rdv-btn--primary {
    background: var(--rust);
    color: #fff;
}
.rdv-btn--primary:hover { background: #9A4523; }
.rdv-btn--success {
    background: var(--c-success);
    color: #fff;
}
.rdv-btn--success:hover { background: #175C35; }

.rdv-secondary-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    flex-wrap: wrap;
}
.rdv-link {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 10px;
    background: none;
    border: none;
    color: var(--muted);
    font-size: 13px;
    font-weight: 500;
    font-family: inherit;
    text-decoration: none;
    cursor: pointer;
    border-radius: 8px;
    transition: background-color .15s, color .15s;
}
.rdv-link:hover { background: #F5F5F7; color: var(--text); }
.rdv-link--danger:hover { color: var(--c-danger); background: var(--c-danger-bg); }
.rdv-link--warning:hover { color: var(--c-warning); background: var(--c-warning-bg); }
.rdv-link-sep { color: var(--border); font-weight: 700; font-size: 12px; }

/* ═══ STATE PILL ═══ */
.rdv-state-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    width: 100%;
}
.rdv-state-pill--success { background: var(--c-success-bg); color: var(--c-success); }
.rdv-state-pill--info    { background: var(--c-info-bg);    color: var(--c-info); }
.rdv-state-pill--danger  { background: var(--c-danger-bg);  color: var(--c-danger); }
.rdv-state-pill--muted   { background: #F5F5F7;             color: #6C6C70; }

/* ═══ EMPTY ═══ */
.empty-state {
    text-align: center;
    padding: 56px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 64px; height: 64px;
    margin: 0 auto 14px;
    display: flex; align-items: center; justify-content: center;
    background: #F5F5F7;
    border-radius: 50%;
    color: var(--muted);
    font-size: 26px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 16px; font-weight: 700;
    margin: 0 0 5px;
    color: var(--ink);
}
.empty-state p { color: var(--muted); font-size: 13px; margin: 0 auto 16px; }

/* ═══ PAGINATION ═══ */
.pagination-wrapper { margin-top: 22px; display: flex; justify-content: center; }
.pagination-wrapper .pagination,
.pagination-wrapper nav { display: flex; gap: 5px; justify-content: center; list-style: none; padding: 0; margin: 0; flex-wrap: wrap; }
.pagination-wrapper a,
.pagination-wrapper span:not(.sr-only) {
    display: inline-block;
    padding: 6px 11px;
    border-radius: 8px;
    border: 1px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    font-size: 12px;
    min-width: 34px;
    text-align: center;
    background: #fff;
}
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span { background: var(--rust); color: #fff; border-color: var(--rust); }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .rdv-page { background: transparent; padding-top: 4px; }

    /* Stats inline */
    .rdv-stats {
        display: flex;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 14px 6px;
        margin-bottom: 14px;
        grid-template-columns: none;
        gap: 0;
    }
    .rdv-stat {
        flex: 1;
        padding: 0 4px;
        background: transparent;
        border: none;
        border-radius: 0;
        gap: 3px;
    }
    .rdv-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%;
        width: 0.5px;
        background: var(--ios-separator);
    }
    .rdv-stat__value { font-size: 22px; }
    .rdv-stat__label {
        font-size: 11px;
        color: var(--muted);
        text-transform: none;
        letter-spacing: 0;
    }

    /* Cartes iOS */
    .rdv-list { gap: 12px; }
    .rdv-card {
        padding: 16px;
        gap: 12px;
        border: none;
        border-radius: var(--ios-radius-lg);
        background: var(--ios-card);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .rdv-date-pill { width: 56px; height: 56px; border-radius: 14px; background: #F5F5F7; }
    .rdv-date-pill__day { font-size: 22px; }
    .rdv-date-pill__month { font-size: 10px; }

    .rdv-card__title { font-size: 15px; }
    .rdv-card__meta { font-size: 13px; gap: 6px; }

    .rdv-card__client { padding: 10px 12px; gap: 10px; border-radius: 12px; background: #F5F5F7; }
    .rdv-client-avatar { width: 36px; height: 36px; font-size: 13px; background: #fff; }

    /* Formulaire code iOS */
    .rdv-code-input { padding: 14px; gap: 10px; background: #F5F5F7; border: none; }
    .rdv-code-input__wrapper { flex-direction: column; gap: 8px; }
    .rdv-code-input__field {
        padding: 14px;
        font-size: 26px;
        letter-spacing: 9px;
    }
    .rdv-code-input__field::placeholder {
        font-size: 26px;
        letter-spacing: 9px;
    }
    .rdv-code-input__btn {
        padding: 14px 18px;
        font-size: 15px;
        width: 100%;
    }

    /* Actions */
    .rdv-btn { padding: 14px 20px; font-size: 15px; border-radius: 12px; }
    .rdv-link { font-size: 13.5px; padding: 8px 12px; }
    .rdv-state-pill { padding: 13px 16px; font-size: 14.5px; border-radius: 12px; }
}

@media (max-width: 480px) {
    .rdv-date-pill { width: 52px; height: 52px; }
    .rdv-date-pill__day { font-size: 20px; }
    .rdv-card { padding: 14px; gap: 10px; }
    .rdv-card__title { font-size: 14.5px; }
    .rdv-code-input__field { font-size: 22px; letter-spacing: 7px; padding: 12px; }
    .rdv-code-input__field::placeholder { font-size: 22px; letter-spacing: 7px; }
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