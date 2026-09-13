@extends('layouts.dashboard')

@section('title', 'Planifier une visite — DoyaImmo')
@section('page_title', 'Planifier une visite')
@section('page_sub', 'Choisissez un créneau pour visiter le bien')

@section('content')
<div class="view active rdv-create">

    @php
        // Créneaux disponibles (non passés)
        $creneauxDisponibles = $creneaux->filter(function($creneau) {
            $creneauDateTime = \Carbon\Carbon::parse($creneau->date)->setTimeFromTimeString($creneau->heure_debut);
            return !$creneauDateTime->isPast();
        });

        // Grouper par date pour un meilleur rendu iOS
        $creneauxParDate = $creneauxDisponibles->groupBy(function($c) {
            return \Carbon\Carbon::parse($c->date)->format('Y-m-d');
        })->sortKeys();
    @endphp

    {{-- ═══ HERO BIEN ═══ --}}
    <div class="rdv-hero">
        <div class="rdv-hero__icon">
            <i class="fa-regular fa-building"></i>
        </div>
        <div class="rdv-hero__body">
            <h1 class="rdv-hero__title">
                {{ $proposition->bien->titre ?? 'Bien' }}
            </h1>
            @if($proposition->bien->quartier)
                <div class="rdv-hero__meta">
                    <i class="fa-solid fa-location-dot"></i>
                    {{ $proposition->bien->quartier }}
                </div>
            @endif
            <div class="rdv-hero__price">
                {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                <small>FCFA</small>
            </div>
            @if($proposition->agence)
                <div class="rdv-hero__agency">
                    <i class="fa-regular fa-building"></i>
                    {{ $proposition->agence->nom_agence }}
                </div>
            @endif
        </div>
    </div>

    <form action="{{ route('particulier.rendezvous.store') }}" method="POST" id="rdvForm">
        @csrf
        <input type="hidden" name="proposition_id" value="{{ $proposition->id }}">

        @if($creneauxDisponibles->count() > 0)

            {{-- ═══ SECTION CRÉNEAUX ═══ --}}
            <div class="ios-section-title">
                <i class="fa-regular fa-calendar-check"></i> Choisissez un créneau
            </div>
            <p class="ios-section-sub">
                Sélectionnez la date et l'heure qui vous conviennent
            </p>

            {{-- Liste groupée par date --}}
            @foreach($creneauxParDate as $date => $creneauxJour)
                @php
                    $dateCarbon = \Carbon\Carbon::parse($date);
                    $isToday = $dateCarbon->isToday();
                    $isTomorrow = $dateCarbon->isTomorrow();
                @endphp

                <div class="rdv-day">
                    <div class="rdv-day__header">
                        <span class="rdv-day__date">
                            @if($isToday)
                                Aujourd'hui
                            @elseif($isTomorrow)
                                Demain
                            @else
                                {{ $dateCarbon->translatedFormat('l d F') }}
                            @endif
                        </span>
                        <span class="rdv-day__count">
                            {{ $creneauxJour->count() }} créneau{{ $creneauxJour->count() > 1 ? 'x' : '' }}
                        </span>
                    </div>

                    <div class="rdv-slots">
                        @foreach($creneauxJour as $creneau)
                            <label class="rdv-slot">
                                <input type="radio" name="creneau_id" value="{{ $creneau->id }}" required>
                                <div class="rdv-slot__inner">
                                    <div class="rdv-slot__time">
                                        {{ \Carbon\Carbon::parse($creneau->heure_debut)->format('H:i') }}
                                        <span class="rdv-slot__sep">–</span>
                                        {{ \Carbon\Carbon::parse($creneau->heure_fin)->format('H:i') }}
                                    </div>
                                    <div class="rdv-slot__check">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            {{-- ═══ ACTIONS FIXES EN BAS ═══ --}}
            <div class="ios-form-actions">
                <a href="{{ route('particulier.propositions.index') }}" class="ios-btn ios-btn--ghost">
                    Annuler
                </a>
                <button type="submit" class="ios-btn ios-btn--primary">
                    <i class="fa-solid fa-calendar-check"></i> Planifier
                </button>
            </div>

        @else

            {{-- ═══ EMPTY STATE ═══ --}}
            <div class="ios-section-title">
                <i class="fa-regular fa-calendar-check"></i> Créneaux
            </div>

            <div class="empty-slots">
                <div class="empty-slots__icon">
                    <i class="fa-regular fa-calendar-xmark"></i>
                </div>
                <h4>Aucun créneau disponible</h4>
                <p>Tous les créneaux sont passés ou aucune date n'est proposée. Contactez directement l'agence pour convenir d'un rendez-vous.</p>

                @if($proposition->agence && $proposition->agence->user && $proposition->agence->user->telephone)
                    <a href="tel:{{ $proposition->agence->user->telephone }}" class="ios-btn ios-btn--primary">
                        <i class="fa-solid fa-phone"></i> Appeler l'agence
                    </a>
                @endif

                <a href="{{ route('particulier.propositions.index') }}" class="ios-btn ios-btn--ghost">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
            </div>

        @endif
    </form>
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PLANIFIER VISITE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.rdv-create {
    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ HERO ═══ */
.rdv-hero {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 18px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.rdv-hero::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--rust), #d4754a);
}
.rdv-hero__icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--rust-soft);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.rdv-hero__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.rdv-hero__title {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    color: var(--ink);
    margin: 0;
    line-height: 1.3;
}
.rdv-hero__meta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--muted);
}
.rdv-hero__meta i { color: var(--rust); font-size: 11px; }
.rdv-hero__price {
    font-family: var(--display);
    font-size: 20px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.2;
    margin-top: 2px;
}
.rdv-hero__price small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 3px;
}
.rdv-hero__agency {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--text-soft);
    margin-top: 2px;
}
.rdv-hero__agency i { color: var(--muted); font-size: 11px; }

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ios-section-title i { color: var(--rust); font-size: 15px; }
.ios-section-sub {
    font-size: 13px;
    color: var(--muted);
    margin: 0 0 18px;
    line-height: 1.5;
}

/* ═══ GROUPES PAR JOUR ═══ */
.rdv-day {
    margin-bottom: 18px;
}
.rdv-day__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 10px;
    padding: 0 4px;
}
.rdv-day__date {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--text-soft);
    text-transform: capitalize;
}
.rdv-day__count {
    font-size: 11.5px;
    font-weight: 600;
    color: var(--muted);
    padding: 3px 10px;
    background: var(--surface);
    border-radius: 999px;
}

/* ═══ SLOTS ═══ */
.rdv-slots {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
}

.rdv-slot {
    cursor: pointer;
    display: block;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}
.rdv-slot input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.rdv-slot__inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 14px 16px;
    background: #fff;
    border: 2px solid var(--border);
    border-radius: 12px;
    transition: all .2s ease;
}
.rdv-slot:hover .rdv-slot__inner {
    border-color: var(--text-soft);
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(0,0,0,.05);
}
.rdv-slot:active .rdv-slot__inner {
    transform: scale(.98);
}

.rdv-slot__time {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -.2px;
}
.rdv-slot__sep {
    color: var(--muted);
    font-weight: 400;
    margin: 0 3px;
}

.rdv-slot__check {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid var(--border);
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    color: transparent;
    transition: all .2s ease;
    flex-shrink: 0;
}

/* État sélectionné */
.rdv-slot input:checked + .rdv-slot__inner {
    border-color: var(--rust);
    background: linear-gradient(135deg, #FFF4EC 0%, #FFE8D9 100%);
    box-shadow: 0 4px 14px rgba(181, 80, 42, .15);
}
.rdv-slot input:checked + .rdv-slot__inner .rdv-slot__time {
    color: var(--rust);
}
.rdv-slot input:checked + .rdv-slot__inner .rdv-slot__check {
    background: var(--rust);
    border-color: var(--rust);
    color: #fff;
}

/* ═══ EMPTY ═══ */
.empty-slots {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 40px 24px;
    text-align: center;
}
.empty-slots__icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    border-radius: 50%;
    background: var(--c-warning-bg);
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
}
.empty-slots h4 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 6px;
}
.empty-slots p {
    font-size: 13px;
    color: var(--muted);
    line-height: 1.6;
    margin: 0 0 20px;
    max-width: 400px;
    margin-inline: auto;
}
.empty-slots .ios-btn {
    margin: 6px 4px 0;
}

/* ═══ BOUTONS ═══ */
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181,80,42,.2);
}
.ios-btn--primary:hover { background: #9A4523; transform: translateY(-1px); }
.ios-btn--primary:active { transform: scale(.97); }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--ghost:active { transform: scale(.97); }

/* Actions fixes */
.ios-form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    margin-top: 20px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.ios-form-actions .ios-btn {
    flex: 1;
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .rdv-create { background: transparent; padding-top: 4px; }

    /* ─── HERO iOS ─── */
    .rdv-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px;
        margin-bottom: 16px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .rdv-hero::before {
        left: 0; right: 0;
        height: 3px;
        border-radius: 0;
    }
    .rdv-hero__icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        box-shadow: 0 4px 10px rgba(181,80,42,.25);
        font-size: 22px;
    }
    .rdv-hero__title {
        font-size: 18px;
        letter-spacing: -.3px;
        color: var(--ios-label);
        font-weight: 700;
    }
    .rdv-hero__meta {
        font-size: 13.5px;
        color: var(--ios-label-3);
    }
    .rdv-hero__price {
        font-size: 24px;
        letter-spacing: -.5px;
        margin-top: 4px;
    }
    .rdv-hero__agency {
        font-size: 13px;
        color: var(--ios-label-3);
        margin-top: 4px;
    }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 0 4px 6px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }
    .ios-section-sub {
        font-size: 13px;
        color: var(--ios-label-3);
        margin: 0 4px 16px;
    }

    /* ─── GROUPES ─── */
    .rdv-day {
        margin-bottom: 16px;
    }
    .rdv-day__header {
        margin-bottom: 10px;
        padding: 0 4px;
    }
    .rdv-day__date {
        font-size: 15px;
        font-weight: 700;
        color: var(--ios-label);
        text-transform: capitalize;
    }
    .rdv-day__count {
        background: #EFEFF4;
        color: var(--ios-label-3);
        font-size: 11.5px;
    }

    /* ─── SLOTS iOS ─── */
    .rdv-slots {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .rdv-slot__inner {
        background: var(--ios-card);
        border: 2px solid transparent;
        border-radius: 14px;
        padding: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex-direction: column;
        align-items: center;
        gap: 8px;
        text-align: center;
        min-height: 76px;
        justify-content: center;
    }
    .rdv-slot:hover .rdv-slot__inner {
        border-color: transparent;
        transform: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .rdv-slot:active .rdv-slot__inner {
        transform: scale(.96);
    }

    .rdv-slot__time {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -.2px;
    }
    .rdv-slot__check {
        width: 22px;
        height: 22px;
        font-size: 10px;
    }

    /* Sélection iOS */
    .rdv-slot input:checked + .rdv-slot__inner {
        background: linear-gradient(135deg, #FFF4EC, #FFE8D9);
        border-color: var(--rust);
        box-shadow: 0 4px 14px rgba(181, 80, 42, .18);
    }
    .rdv-slot input:checked + .rdv-slot__inner .rdv-slot__check {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border-color: var(--rust);
        color: #fff;
    }

    /* ─── EMPTY iOS ─── */
    .empty-slots {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 44px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-slots__icon {
        width: 68px;
        height: 68px;
        font-size: 28px;
    }
    .empty-slots h4 {
        font-size: 17px;
        letter-spacing: -.3px;
    }
    .empty-slots p {
        font-size: 13.5px;
        color: var(--ios-label-3);
    }

    /* ─── BARRE ACTIONS FIXE ─── */
    .ios-form-actions {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(242, 242, 247, 0.95);
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        border-top: 0.5px solid var(--ios-separator);
        padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
        margin: 0;
        gap: 10px;
        z-index: 100;
        display: flex;
        border-top: 0.5px solid var(--ios-separator);
    }
    .ios-btn {
        padding: 14px 20px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 14px;
        flex: 1;
        letter-spacing: -.1px;
    }
    .ios-btn--primary {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .ios-btn--primary:active {
        transform: scale(.97);
        box-shadow: 0 2px 6px rgba(181, 80, 42, .4);
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex: 0 0 auto;
        padding: 14px 18px;
    }
    .ios-btn--ghost:active { background: #EFEFF4; }

    /* Padding bottom pour ne pas cacher sous la barre fixe */
    .rdv-create {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }

    /* Empty state : boutons non fixes */
    .empty-slots .ios-btn {
        flex: 0 0 auto;
        width: auto;
        margin: 6px 4px 0;
    }
}

@media (max-width: 480px) {
    .rdv-hero { padding: 16px; }
    .rdv-hero__title { font-size: 17px; }
    .rdv-hero__price { font-size: 22px; }

    .rdv-slot__inner { padding: 12px; min-height: 70px; }
    .rdv-slot__time { font-size: 14.5px; }

    .ios-section-title { font-size: 16px; }
}
</style>
@endpush