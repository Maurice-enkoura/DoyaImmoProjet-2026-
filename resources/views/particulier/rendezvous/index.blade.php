@extends('layouts.dashboard')

@section('title', 'Mes rendez-vous — DoyaImmo')
@section('page_title', 'Mes rendez-vous')
@section('page_sub', 'Vos visites planifiées avec les agences')

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

                    $bien        = $rdv->proposition->bien ?? null;
                    $agence      = $rdv->agence ?? null;
                    $agenceUser  = $agence->user ?? null;

                    // ✅ Téléphone visible dès que l'agence a confirmé le RDV (à un moment ou un autre)
                    $telVisible = in_array($statutValue, ['confirme', 'termine', 'client_absent']);
                    $hasTel     = $agenceUser && !empty($agenceUser->telephone);
                    $showTel    = $telVisible && $hasTel;

                    $dejaEvalue = false;
                    if ($statutValue === 'termine') {
                        $dejaEvalue = \App\Models\Evaluation::where('particulier_id', Auth::user()->particulier->id)
                            ->where('proposition_id', $rdv->proposition_id)
                            ->exists();
                    }

                    $dateRdv    = \Carbon\Carbon::parse($rdv->date_visite);
                    $isToday    = $dateRdv->isToday();
                    $isTomorrow = $dateRdv->isTomorrow();

                    $dateHeureVisite = \Carbon\Carbon::parse($rdv->date_visite)
                        ->setTimeFromTimeString($rdv->heure_visite ?? '00:00:00');

                    // ✅ Code visible uniquement quand confirmé et non encore validé (24h avant)
                    $codeVisible = $statutValue === 'confirme'
                        && !$rdv->code_valide
                        && now()->greaterThanOrEqualTo($dateHeureVisite->copy()->subHours(24));

                    $propositionStatut = $rdv->proposition
                        ? (is_object($rdv->proposition->statut)
                            ? $rdv->proposition->statut->value
                            : (string) $rdv->proposition->statut)
                        : null;

                    $aRdvActif = $rdv->proposition
                        ? $rdv->proposition->rendezVous()
                            ->whereIn('statut', [
                                \App\Enums\StatutRendezVousEnum::PLANIFIE->value,
                                \App\Enums\StatutRendezVousEnum::CONFIRME->value,
                            ])
                            ->exists()
                        : false;

                    $peutReplanifier = $statutValue === 'client_absent'
                        && $propositionStatut
                        && in_array($propositionStatut, ['acceptee', 'en_attente'])
                        && !$aRdvActif;
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

                    {{-- Encart code visite --}}
                    @if($codeVisible)
                        <div class="rdv-code">
                            <div class="rdv-code__icon">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <div class="rdv-code__body">
                                <span class="rdv-code__label">Code de visite</span>
                                <div class="rdv-code__digits">
                                    @foreach(str_split($rdv->code_visite ?? '------') as $digit)
                                        <span class="rdv-code__digit">{{ $digit }}</span>
                                    @endforeach
                                </div>
                                <span class="rdv-code__hint">
                                    Présentez ce code à l'agence pour valider la visite
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Alerte CLIENT_ABSENT --}}
                    @if($statutValue === 'client_absent')
                        <div class="rdv-alert">
                            <i class="fa-solid fa-user-slash"></i>
                            <div>
                                <strong>Vous n'êtes pas venu à cette visite</strong>
                                <p>L'agence a signalé votre absence. Si vous êtes toujours intéressé, choisissez un nouveau créneau.</p>
                            </div>
                        </div>
                    @endif

                    {{-- Agence + téléphone --}}
                    <div class="rdv-card__agency">
                        <div class="rdv-agency-avatar">
                            {{ strtoupper(mb_substr($agence->nom_agence ?? 'A', 0, 1)) }}
                        </div>
                        <div class="rdv-agency-info">
                            <strong>{{ $agence->nom_agence ?? 'Agence' }}</strong>

                            @if($showTel)
                                <a href="tel:{{ $agenceUser->telephone }}" class="rdv-agency-phone">
                                    <i class="fa-solid fa-phone"></i>
                                    {{ $agenceUser->telephone }}
                                </a>
                            @elseif($telVisible && !$hasTel)
                                <span class="rdv-agency-muted">Téléphone non renseigné</span>
                            @else
                                <span class="rdv-agency-locked">
                                    <i class="fa-solid fa-lock"></i>
                                    Contact visible après confirmation
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <footer class="rdv-card__actions">

                        @if($statutValue === 'planifie')
                            <span class="rdv-state-pill rdv-state-pill--warning">
                                En attente de confirmation
                            </span>
                            <div class="rdv-secondary-actions">
                                <a href="{{ route('particulier.rendezvous.show', $rdv) }}" class="rdv-link">
                                    Détails
                                </a>
                                <span class="rdv-link-sep">·</span>
                                <form action="{{ route('particulier.rendezvous.annuler', $rdv) }}" method="POST" class="rdv-inline-form">
                                    @csrf
                                    <button type="submit" class="rdv-link rdv-link--danger"
                                            onclick="return confirm('Annuler ce rendez-vous ?')">
                                        Annuler
                                    </button>
                                </form>
                            </div>

                        @elseif($statutValue === 'confirme')
                            @if($showTel)
                                <a href="tel:{{ $agenceUser->telephone }}" class="rdv-btn rdv-btn--primary">
                                    <i class="fa-solid fa-phone"></i>
                                    Appeler l'agence
                                </a>
                            @endif

                            <div class="rdv-secondary-actions">
                                <a href="{{ route('particulier.rendezvous.show', $rdv) }}" class="rdv-link">
                                    Détails
                                </a>
                                <span class="rdv-link-sep">·</span>
                                <form action="{{ route('particulier.rendezvous.annuler', $rdv) }}" method="POST" class="rdv-inline-form">
                                    @csrf
                                    <button type="submit" class="rdv-link rdv-link--danger"
                                            onclick="return confirm('Annuler ce rendez-vous ?')">
                                        Annuler
                                    </button>
                                </form>
                            </div>

                        @elseif($statutValue === 'termine')
                            @if(!$dejaEvalue)
                                <a href="{{ route('particulier.evaluations.create', $rdv->proposition) }}" class="rdv-btn rdv-btn--primary">
                                    <i class="fa-solid fa-star"></i>
                                    Évaluer l'agence
                                </a>
                            @else
                                <span class="rdv-state-pill rdv-state-pill--success">
                                    Déjà évalué
                                </span>
                            @endif

                            @if($showTel)
                                <a href="tel:{{ $agenceUser->telephone }}" class="rdv-btn rdv-btn--primary">
                                    <i class="fa-solid fa-phone"></i>
                                    Rappeler l'agence
                                </a>
                            @endif

                            <div class="rdv-secondary-actions">
                                <a href="{{ route('particulier.rendezvous.show', $rdv) }}" class="rdv-link">
                                    Voir les détails
                                </a>
                            </div>

                        @elseif($statutValue === 'client_absent')
                            <span class="rdv-state-pill rdv-state-pill--warning">
                                Visite non honorée
                            </span>

                            @if($showTel)
                                <a href="tel:{{ $agenceUser->telephone }}" class="rdv-btn rdv-btn--primary">
                                    <i class="fa-solid fa-phone"></i>
                                    Appeler l'agence
                                </a>
                            @endif

                            @if($peutReplanifier)
                                <a href="{{ route('particulier.rendezvous.create', $rdv->proposition_id) }}"
                                   class="rdv-btn rdv-btn--primary">
                                    <i class="fa-solid fa-calendar-plus"></i>
                                    Choisir un nouveau créneau
                                </a>
                            @endif

                            <div class="rdv-secondary-actions">
                                <a href="{{ route('particulier.rendezvous.show', $rdv) }}" class="rdv-link">
                                    Voir les détails
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
            <h3>Aucun rendez-vous planifié</h3>
            <p>Les rendez-vous apparaîtront ici après avoir accepté une proposition d'agence.</p>
            <a href="{{ route('particulier.demandes.create') }}" class="rdv-btn rdv-btn--primary">
                <i class="fa-solid fa-plus"></i> Publier un besoin
            </a>
        </div>
    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MES RENDEZ-VOUS — Design épuré
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
    --radius:       16px;

    --ios-card: #FFFFFF;
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
    justify-content: center;
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
.rdv-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

/* ═══ CARTE RDV ═══ */
.rdv-card {
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 18px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    position: relative;
    transition: border-color .2s;
}
.rdv-card:hover { border-color: #d8d8d8; }

/* Header */
.rdv-card__header {
    display: flex;
    align-items: center;
    gap: 14px;
}
.rdv-date-pill {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 62px;
    height: 62px;
    border-radius: 14px;
    flex-shrink: 0;
    background: #F5F5F7;
}
.rdv-date-pill__day {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    line-height: 1;
    letter-spacing: -1px;
    color: var(--text);
}
.rdv-date-pill__month {
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .8px;
    margin-top: 2px;
    color: var(--muted);
}

.rdv-card__heading { flex: 1; min-width: 0; }
.rdv-card__title {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
    line-height: 1.3;
    letter-spacing: -.3px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Meta */
.rdv-card__meta {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 13.5px;
    color: var(--text-soft);
}
.rdv-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 500;
}
.rdv-meta-item i {
    font-size: 12px;
    color: var(--muted);
}
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
.rdv-status--client_absent { background: #F5F5F7;             color: #6C6C70; }

/* ═══ ENCART CODE DE VISITE ═══ */
.rdv-code {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    background: #F9FAFB;
    border: 1px solid var(--border);
    border-radius: 14px;
}
.rdv-code__icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #fff;
    color: var(--rust);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
    border: 1px solid var(--border);
}
.rdv-code__body {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.rdv-code__label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--muted);
}
.rdv-code__digits {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}
.rdv-code__digit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 42px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 10px;
    font-family: var(--display);
    font-size: 20px;
    font-weight: 700;
    color: var(--text);
}
.rdv-code__hint {
    font-size: 12px;
    color: var(--muted);
    font-weight: 500;
    line-height: 1.4;
}

/* ═══ ALERTE CLIENT ABSENT ═══ */
.rdv-alert {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 12px;
    background: #FFFBF0;
    border-left: 3px solid #E65100;
}
.rdv-alert i {
    font-size: 16px;
    color: #E65100;
    flex-shrink: 0;
    margin-top: 2px;
}
.rdv-alert strong {
    display: block;
    font-size: 13.5px;
    font-weight: 600;
    color: #8B4B15;
    margin-bottom: 3px;
}
.rdv-alert p {
    margin: 0;
    font-size: 12.5px;
    color: #8B4B15;
    line-height: 1.5;
    opacity: .85;
}

/* ═══ AGENCE ═══ */
.rdv-card__agency {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    background: #F9FAFB;
    border-radius: 12px;
    border: 1px solid var(--border);
}
.rdv-agency-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #F5F5F7;
    color: var(--text);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    flex-shrink: 0;
    border: 1px solid var(--border);
}
.rdv-agency-info {
    display: flex;
    flex-direction: column;
    gap: 3px;
    min-width: 0;
}
.rdv-agency-info strong {
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
    line-height: 1.2;
}
.rdv-agency-phone {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-soft);
    text-decoration: none;
    transition: color .15s;
}
.rdv-agency-phone:hover { color: var(--rust); }
.rdv-agency-phone i {
    font-size: 11px;
    color: var(--muted);
}
.rdv-agency-muted,
.rdv-agency-locked {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    color: var(--muted);
}
.rdv-agency-locked i {
    font-size: 10px;
}

/* ═══ ACTIONS ═══ */
.rdv-card__actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding-top: 2px;
}
.rdv-primary-form,
.rdv-inline-form { margin: 0; display: block; }

.rdv-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 13px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: background-color .15s;
    width: 100%;
}
.rdv-btn i { font-size: 13px; }

.rdv-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.rdv-btn--primary:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
.rdv-btn--primary:active { transform: scale(.99); }

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
.rdv-link:hover {
    color: var(--text);
    background: #F5F5F7;
}
.rdv-link--danger { color: var(--muted); }
.rdv-link--danger:hover {
    color: var(--c-danger);
    background: var(--c-danger-bg);
}
.rdv-link-sep {
    color: var(--border);
    font-weight: 700;
    font-size: 12px;
    user-select: none;
}

.rdv-state-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 18px;
    border-radius: 12px;
    font-size: 13.5px;
    font-weight: 500;
    width: 100%;
    background: #F5F5F7;
    color: var(--text-soft);
}
.rdv-state-pill--warning { background: var(--c-warning-bg); color: var(--c-warning); }
.rdv-state-pill--success { background: var(--c-success-bg); color: var(--c-success); }

/* ═══ EMPTY ═══ */
.empty-state {
    text-align: center;
    padding: 56px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #F5F5F7;
    border-radius: 50%;
    color: var(--muted);
    font-size: 24px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--ink);
}
.empty-state p {
    color: var(--muted);
    font-size: 13px;
    margin: 0 auto 16px;
    max-width: 400px;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper { margin-top: 22px; display: flex; justify-content: center; }
.pagination-wrapper .pagination,
.pagination-wrapper nav {
    display: flex; gap: 5px; justify-content: center;
    list-style: none; padding: 0; margin: 0; flex-wrap: wrap;
}
.pagination-wrapper a,
.pagination-wrapper span:not(.sr-only) {
    display: inline-block;
    padding: 6px 11px;
    border-radius: 8px;
    border: 1px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    font-size: 12px;
    transition: background-color .15s;
    min-width: 34px;
    text-align: center;
    background: #fff;
}
.pagination-wrapper a:hover { background: #F5F5F7; }
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] { background: var(--rust); color: #fff; border-color: var(--rust); }
.pagination-wrapper .disabled span { opacity: .45; cursor: not-allowed; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE iOS
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .rdv-page { background: transparent; padding-top: 4px; }

    .rdv-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        grid-template-columns: none;
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
        font-weight: 500;
    }

    .rdv-list { gap: 12px; }

    .rdv-card {
        padding: 16px;
        gap: 12px;
        border: none;
        border-radius: var(--ios-radius-lg);
        background: var(--ios-card);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .rdv-card:hover { border: none; }

    .rdv-card__header { gap: 12px; }
    .rdv-date-pill { width: 56px; height: 56px; border-radius: 14px; background: #F5F5F7; }
    .rdv-date-pill__day { font-size: 22px; }
    .rdv-date-pill__month { font-size: 10px; }

    .rdv-card__title { font-size: 16px; }

    .rdv-card__meta { font-size: 13.5px; gap: 6px; }

    .rdv-status { font-size: 11.5px; padding: 3px 10px; }

    /* Code iOS */
    .rdv-code { padding: 14px; gap: 12px; border-radius: 14px; }
    .rdv-code__icon { width: 40px; height: 40px; font-size: 16px; }
    .rdv-code__label { font-size: 10.5px; letter-spacing: .5px; }
    .rdv-code__digits { gap: 5px; }
    .rdv-code__digit {
        width: 32px;
        height: 40px;
        font-size: 18px;
        border-radius: 9px;
    }
    .rdv-code__hint { font-size: 11.5px; }

    /* Alerte iOS */
    .rdv-alert { padding: 12px 14px; border-radius: 10px; }
    .rdv-alert i { font-size: 15px; }
    .rdv-alert strong { font-size: 13px; }
    .rdv-alert p { font-size: 12px; }

    /* Agence iOS */
    .rdv-card__agency { padding: 11px 13px; border-radius: 12px; gap: 11px; }
    .rdv-agency-avatar { width: 36px; height: 36px; font-size: 13px; }
    .rdv-agency-info strong { font-size: 14px; }
    .rdv-agency-phone { font-size: 13px; }

    /* Actions iOS */
    .rdv-card__actions { gap: 10px; }
    .rdv-btn {
        padding: 13px 18px;
        font-size: 14px;
        border-radius: 12px;
    }
    .rdv-secondary-actions { gap: 2px; }
    .rdv-link { font-size: 13.5px; padding: 8px 12px; }

    .rdv-state-pill {
        padding: 12px 16px;
        font-size: 13.5px;
        border-radius: 12px;
    }

    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon { width: 64px; height: 64px; font-size: 26px; }

    .pagination-wrapper a,
    .pagination-wrapper span:not(.sr-only) {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        padding: 10px 14px;
        font-size: 13px;
        border-radius: 10px;
    }
}

@media (max-width: 480px) {
    .rdv-stat__value { font-size: 20px; }
    .rdv-stat__label { font-size: 10.5px; }

    .rdv-card { padding: 14px; gap: 11px; }
    .rdv-card__title { font-size: 15px; }
    .rdv-date-pill { width: 52px; height: 52px; border-radius: 12px; }
    .rdv-date-pill__day { font-size: 20px; }
    .rdv-date-pill__month { font-size: 9.5px; }
    .rdv-agency-avatar { width: 34px; height: 34px; font-size: 12px; }
    .rdv-btn { padding: 12px 16px; font-size: 13.5px; }

    .rdv-code { padding: 12px; gap: 10px; }
    .rdv-code__icon { width: 36px; height: 36px; font-size: 15px; }
    .rdv-code__digit {
        width: 28px;
        height: 36px;
        font-size: 17px;
        border-radius: 8px;
    }
    .rdv-code__digits { gap: 4px; }
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