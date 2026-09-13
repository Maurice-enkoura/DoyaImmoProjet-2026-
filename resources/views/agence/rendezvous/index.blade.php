@extends('layouts.dashboard-agence')

@section('title', 'Mes rendez-vous — DoyaImmo')
@section('page_title', 'Mes rendez-vous')
@section('page_sub', 'Vos visites planifiées avec les clients')

@section('content')
<div class="view active rdv-page">

    @php
        $current = request('statut', '');

        $total = $rendezVous->total();
        $counts = [
            ''         => $total,
            'planifie' => $rendezVous->where('statut.value', 'planifie')->count(),
            'confirme' => $rendezVous->where('statut.value', 'confirme')->count(),
            'termine'  => $rendezVous->where('statut.value', 'termine')->count(),
            'annule'   => $rendezVous->where('statut.value', 'annule')->count(),
        ];

        $tabs = [
            ''         => ['label' => 'Tous',       'icon' => 'fa-list'],
            'planifie' => ['label' => 'En attente', 'icon' => 'fa-clock'],
            'confirme' => ['label' => 'Confirmés',  'icon' => 'fa-check-circle'],
            'termine'  => ['label' => 'Terminés',   'icon' => 'fa-flag-checkered'],
            'annule'   => ['label' => 'Annulés',    'icon' => 'fa-ban'],
        ];
    @endphp

    {{-- ═══ STATS (desktop + mobile) ═══ --}}
    <div class="rdv-stats">
        <div class="rdv-stat">
            <span class="rdv-stat__value">{{ $counts[''] }}</span>
            <span class="rdv-stat__label">Total</span>
        </div>
        <div class="rdv-stat {{ $counts['planifie'] > 0 ? 'is-highlight' : '' }}">
            <span class="rdv-stat__value">{{ $counts['planifie'] }}</span>
            <span class="rdv-stat__label">En attente</span>
        </div>
        <div class="rdv-stat">
            <span class="rdv-stat__value">{{ $counts['confirme'] }}</span>
            <span class="rdv-stat__label">Confirmés</span>
        </div>
        <div class="rdv-stat">
            <span class="rdv-stat__value">{{ $counts['termine'] }}</span>
            <span class="rdv-stat__label">Terminés</span>
        </div>
    </div>

    {{-- ═══ FILTRES PILLS ═══ --}}
    @if($total > 0)
        <nav class="filter-tabs" aria-label="Filtrer par statut">
            @foreach($tabs as $key => $meta)
                <a href="{{ route('agence.rendezvous.index', $key ? ['statut' => $key] : []) }}"
                   class="filter-tab {{ $current === $key ? 'is-active' : '' }}">
                    <i class="fa-solid {{ $meta['icon'] }}"></i>
                    <span>{{ $meta['label'] }}</span>
                    <span class="filter-tab__count">{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </nav>
    @endif

    {{-- ═══ FLASH MESSAGES ═══ --}}
    @if(session('error'))
        <div class="flash flash--error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    @if(session('info'))
        <div class="flash flash--info">
            <i class="fa-solid fa-info-circle"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif
    @if(session('success'))
        <div class="flash flash--success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($rendezVous->count() > 0)

        {{-- ═══ LISTE DES RENDEZ-VOUS ═══ --}}
        <div class="rdv-list">
            @foreach($rendezVous as $rdv)
                @php
                    $statutValue = $rdv->statut->value;
                    $bien        = $rdv->proposition->bien ?? null;
                    $client      = $rdv->particulier->user ?? null;
                    $telVisible  = in_array($statutValue, ['confirme', 'termine']);
                    $hasTel      = $client && !empty($client->telephone);

                    $dateRdv    = \Carbon\Carbon::parse($rdv->date_visite);
                    $isToday    = $dateRdv->isToday();
                    $isTomorrow = $dateRdv->isTomorrow();
                @endphp

                <article class="rdv-card rdv-card--{{ $statutValue }} {{ $isToday ? 'is-today' : '' }}">

                    {{-- Date --}}
                    <div class="rdv-card__date">
                        <span class="rdv-card__day">{{ $dateRdv->format('d') }}</span>
                        <span class="rdv-card__month">{{ strtoupper($dateRdv->translatedFormat('M')) }}</span>
                        @if($isToday)
                            <span class="rdv-card__badge rdv-card__badge--today">Auj.</span>
                        @elseif($isTomorrow)
                            <span class="rdv-card__badge rdv-card__badge--soon">Demain</span>
                        @endif
                    </div>

                    {{-- Corps --}}
                    <div class="rdv-card__body">

                        <header class="rdv-card__head">
                            <h3 class="rdv-card__title">{{ $bien->titre ?? 'Visite' }}</h3>
                            <span class="rdv-status rdv-status--{{ $statutValue }}">
                                <i class="fa-solid fa-circle"></i>
                                {{ $rdv->statut->label() }}
                            </span>
                        </header>

                        <div class="rdv-card__meta">
                            @if($isToday || $isTomorrow)
                                <span class="rdv-meta-item rdv-meta-item--accent">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                                    @if($isToday) · Aujourd'hui
                                    @else · Demain
                                    @endif
                                </span>
                            @else
                                <span class="rdv-meta-item">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                                </span>
                            @endif

                            @if($bien && $bien->quartier)
                                <span class="rdv-meta-item">
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $bien->quartier }}
                                </span>
                            @endif
                        </div>

                        {{-- Client --}}
                        <div class="rdv-card__agency">
                            <div class="rdv-agency-avatar">
                                {{ strtoupper(mb_substr($client->prenom ?? 'C', 0, 1)) }}
                            </div>
                            <div class="rdv-agency-info">
                                <strong>{{ $client->prenom ?? 'Client' }} {{ $client->nom ?? '' }}</strong>

                                @if($telVisible)
                                    @if($hasTel)
                                        <a href="tel:{{ $client->telephone }}" class="rdv-agency-phone">
                                            <i class="fa-solid fa-phone"></i>
                                            {{ $client->telephone }}
                                            <i class="fa-solid fa-circle-check rdv-agency-check"></i>
                                        </a>
                                    @else
                                        <span class="rdv-agency-muted">Téléphone non renseigné</span>
                                    @endif
                                @else
                                    <span class="rdv-agency-locked">
                                        <i class="fa-solid fa-lock"></i>
                                        Confirmez pour voir le numéro
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Actions --}}
                        <footer class="rdv-card__actions">

                            @if($statutValue === 'planifie')
                                <form action="{{ route('agence.rendezvous.confirmer', $rdv) }}" method="POST" class="rdv-action-form">
                                    @csrf
                                    <button type="submit" class="btn btn-rust">
                                        <i class="fa-solid fa-check"></i> Confirmer
                                    </button>
                                </form>
                                <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="btn btn-ghost">
                                    <i class="fa-solid fa-eye"></i> Détails
                                </a>
                                <form action="{{ route('agence.rendezvous.annuler', $rdv) }}" method="POST" class="rdv-action-form">
                                    @csrf
                                    <button type="submit" class="btn btn-danger-soft"
                                            onclick="return confirm('Annuler ce rendez-vous ?')">
                                        <i class="fa-solid fa-xmark"></i> Annuler
                                    </button>
                                </form>

                            @elseif($statutValue === 'confirme')
                                @if($hasTel)
                                    <a href="tel:{{ $client->telephone }}" class="btn btn-success">
                                        <i class="fa-solid fa-phone"></i> Appeler
                                    </a>
                                @endif
                                <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="btn btn-ghost">
                                    <i class="fa-solid fa-eye"></i> Détails
                                </a>
                                <form action="{{ route('agence.rendezvous.termine', $rdv) }}" method="POST" class="rdv-action-form">
                                    @csrf
                                    <button type="submit" class="btn btn-rust"
                                            onclick="return confirm('Marquer ce rendez-vous comme terminé ?')">
                                        <i class="fa-solid fa-check-circle"></i> Terminer
                                    </button>
                                </form>
                                <form action="{{ route('agence.rendezvous.annuler', $rdv) }}" method="POST" class="rdv-action-form">
                                    @csrf
                                    <button type="submit" class="btn btn-danger-soft"
                                            onclick="return confirm('Annuler ce rendez-vous ?')">
                                        <i class="fa-solid fa-xmark"></i> Annuler
                                    </button>
                                </form>

                            @elseif($statutValue === 'termine')
                                <span class="rdv-state-pill rdv-state-pill--success">
                                    <i class="fa-solid fa-check-circle"></i> Visite terminée
                                </span>
                                @if($hasTel)
                                    <a href="tel:{{ $client->telephone }}" class="btn btn-success">
                                        <i class="fa-solid fa-phone"></i> Appeler
                                    </a>
                                @endif
                                <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="btn btn-ghost">
                                    <i class="fa-solid fa-eye"></i> Détails
                                </a>

                            @elseif($statutValue === 'annule')
                                <span class="rdv-state-pill rdv-state-pill--danger">
                                    <i class="fa-solid fa-ban"></i> Rendez-vous annulé
                                </span>
                                <a href="{{ route('agence.rendezvous.show', $rdv) }}" class="btn btn-ghost">
                                    <i class="fa-solid fa-eye"></i> Détails
                                </a>
                            @endif

                        </footer>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $rendezVous->appends(request()->query())->links() }}
        </div>

    @else

        {{-- ═══ EMPTY STATE ═══ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-calendar-days"></i>
            </div>

            @if(request('statut'))
                <h3>Aucun rendez-vous dans ce filtre</h3>
                <p>Essayez un autre filtre ou revenez à la vue complète.</p>
                <a href="{{ route('agence.rendezvous.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-rotate"></i> Voir tous les RDV
                </a>
            @else
                <h3>Aucun rendez-vous planifié</h3>
                <p>Les rendez-vous apparaîtront ici après avoir accepté une proposition.</p>
                <a href="{{ route('agence.biens.index') }}" class="btn btn-rust">
                    <i class="fa-solid fa-building"></i> Voir mes biens
                </a>
            @endif
        </div>

    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MES RENDEZ-VOUS — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.rdv-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --surface:      #F7F9FC;
    --radius:       12px;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ STATS : visibles partout ═══ */
.rdv-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 16px;
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
    transition: transform .15s, box-shadow .15s;
}
.rdv-stat:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0,0,0,.05);
}
.rdv-stat.is-highlight {
    border-color: var(--c-warning);
    background: linear-gradient(135deg, #FFFBF0, #fff);
}
.rdv-stat__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.5px;
    line-height: 1;
}
.rdv-stat.is-highlight .rdv-stat__value { color: var(--c-warning); }
.rdv-stat__label {
    font-size: 11.5px;
    font-weight: 500;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .3px;
}

/* ═══ FILTRES ═══ */
.filter-tabs {
    display: flex;
    gap: 8px;
    margin: 0 0 16px;
    padding: 4px 4px 8px;
    overflow-x: auto;
    overflow-y: hidden;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.filter-tabs::-webkit-scrollbar {
    display: none;
    height: 0;
}

.filter-tab {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 15px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
    background: #fff;
    border: 1.5px solid var(--border);
    text-decoration: none;
    transition: all .2s ease;
    white-space: nowrap;
    flex-shrink: 0;
    scroll-snap-align: start;
}
.filter-tab:hover { border-color: var(--rust); transform: translateY(-1px); }
.filter-tab i { font-size: 12px; opacity: .85; }
.filter-tab.is-active {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(180, 83, 42, .22);
}
.filter-tab.is-active i { color: #fff; opacity: 1; }

.filter-tab__count {
    background: rgba(0, 0, 0, .07);
    padding: 1px 8px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    min-width: 22px;
    text-align: center;
}
.filter-tab.is-active .filter-tab__count {
    background: rgba(255, 255, 255, .25);
    color: #fff;
}

/* ═══ FLASH ═══ */
.flash {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-radius: 10px;
    margin-bottom: 14px;
    font-size: 13px;
    font-weight: 500;
    border: 1px solid;
}
.flash--success { background: var(--c-success-bg); border-color: #C8E6C9; color: var(--c-success); }
.flash--error   { background: var(--c-danger-bg);  border-color: #FFCDD2; color: var(--c-danger); }
.flash--info    { background: var(--c-info-bg);    border-color: #BBDEFB; color: var(--c-info); }

/* ═══ LISTE ═══ */
.rdv-list { display: flex; flex-direction: column; gap: 8px; }

/* ═══ CARTE ═══ */
.rdv-card {
    display: grid;
    grid-template-columns: 76px 1fr;
    gap: 14px;
    padding: 12px 16px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
    position: relative;
}
.rdv-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, .05);
    border-color: #d8d8d8;
}

.rdv-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 16px;
    right: 16px;
    height: 2.5px;
    border-radius: 0 0 4px 4px;
    background: var(--muted);
}
.rdv-card--planifie::before { background: var(--c-warning); }
.rdv-card--confirme::before { background: var(--c-info); }
.rdv-card--termine::before  { background: var(--c-success); }
.rdv-card--annule::before   { background: var(--c-danger); }

/* Date */
.rdv-card__date {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1px;
    padding: 8px 6px;
    border-radius: 10px;
    flex-shrink: 0;
    border: 1px solid transparent;
}
.rdv-card--planifie .rdv-card__date { background: var(--c-warning-bg); border-color: rgba(230, 81, 0, .1); }
.rdv-card--confirme .rdv-card__date { background: var(--c-info-bg);    border-color: rgba(13, 71, 161, .1); }
.rdv-card--termine  .rdv-card__date { background: var(--c-success-bg); border-color: rgba(30, 122, 71, .1); }
.rdv-card--annule   .rdv-card__date { background: var(--c-danger-bg);  border-color: rgba(198, 40, 40, .1); }

.rdv-card__day {
    font-family: var(--display);
    font-size: 21px;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.rdv-card__month {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: var(--muted);
}
.rdv-card__badge {
    display: inline-flex;
    align-items: center;
    padding: 1px 7px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    margin-top: 3px;
}
.rdv-card__badge--today { background: var(--c-danger); color: #fff; }
.rdv-card__badge--soon  { background: var(--c-warning); color: #fff; }

/* Corps */
.rdv-card__body {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
}
.rdv-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    flex-wrap: wrap;
}
.rdv-card__title {
    font-family: var(--display);
    font-size: 14.5px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.3;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.rdv-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    flex-shrink: 0;
}
.rdv-status i { font-size: 5px; }
.rdv-status--planifie { background: var(--c-warning-bg); color: var(--c-warning); }
.rdv-status--confirme { background: var(--c-info-bg);    color: var(--c-info); }
.rdv-status--termine  { background: var(--c-success-bg); color: var(--c-success); }
.rdv-status--annule   { background: var(--c-danger-bg);  color: var(--c-danger); }

/* Meta */
.rdv-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 14px;
    font-size: 12.5px;
    color: var(--text-soft);
}
.rdv-meta-item {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.rdv-meta-item i {
    color: var(--rust);
    font-size: 11px;
    opacity: .85;
}
.rdv-meta-item--accent {
    color: var(--c-danger);
    font-weight: 600;
}
.rdv-meta-item--accent i { color: var(--c-danger); opacity: 1; }

/* Client */
.rdv-card__agency {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 10px;
    background: var(--surface);
    border-radius: 10px;
}
.rdv-agency-avatar {
    width: 34px;
    height: 34px;
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
.rdv-agency-info {
    display: flex;
    flex-direction: column;
    gap: 1px;
    min-width: 0;
}
.rdv-agency-info strong {
    font-size: 13px;
    font-weight: 700;
    color: var(--text);
}
.rdv-agency-phone {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-soft);
    text-decoration: none;
    transition: color .2s;
}
.rdv-agency-phone:hover { color: var(--rust); }
.rdv-agency-phone i:first-child {
    font-size: 11px;
    color: var(--rust);
}
.rdv-agency-check {
    color: var(--c-success) !important;
    font-size: 10px !important;
}
.rdv-agency-muted {
    font-size: 12px;
    color: var(--muted);
    font-style: italic;
}
.rdv-agency-locked {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11.5px;
    color: var(--muted);
    font-style: italic;
}
.rdv-agency-locked i {
    font-size: 9px;
    color: var(--rust) !important;
}

/* Actions */
.rdv-card__actions {
    display: flex;
    align-items: center;
    gap: 6px;
    padding-top: 8px;
    border-top: 1px dashed var(--border);
    flex-wrap: wrap;
}
.rdv-action-form { display: inline; }

.rdv-state-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 9px;
    font-size: 11.5px;
    font-weight: 600;
    border: 1px solid;
}
.rdv-state-pill--warning { background: var(--c-warning-bg); color: var(--c-warning); border-color: #FFE0B2; }
.rdv-state-pill--success { background: var(--c-success-bg); color: var(--c-success); border-color: #C8E6C9; }
.rdv-state-pill--danger  { background: var(--c-danger-bg);  color: var(--c-danger);  border-color: #FFCDD2; }

/* Boutons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 9px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; color: #fff; border-color: #9A4523; transform: translateY(-1px); box-shadow: 0 3px 8px rgba(180, 83, 42, .22); }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: var(--surface); border-color: var(--rust); color: var(--rust); }
.btn-danger-soft { background: var(--c-danger-bg); color: var(--c-danger); border-color: #FFCDD2; }
.btn-danger-soft:hover { background: var(--c-danger); color: #fff; border-color: var(--c-danger); }
.btn-success { background: #25D366; color: #fff; border-color: #25D366; }
.btn-success:hover { background: #1DA851; color: #fff; border-color: #1DA851; transform: translateY(-1px); box-shadow: 0 3px 8px rgba(37, 211, 102, .22); }
.btn-sm { padding: 5px 11px; font-size: 11.5px; }

/* Empty */
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
    background: var(--c-info-bg);
    border-radius: 50%;
    color: var(--rust);
    font-size: 26px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    margin: 0 0 5px;
    color: var(--text);
}
.empty-state p {
    color: var(--muted);
    font-size: 13px;
    margin: 0 auto 16px;
    max-width: 400px;
}

/* Pagination */
.pagination-wrapper { margin-top: 20px; display: flex; justify-content: center; }
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
    transition: all .2s;
    min-width: 34px;
    text-align: center;
    background: #fff;
}
.pagination-wrapper a:hover { background: var(--border); border-color: var(--rust); color: var(--rust); }
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] { background: var(--rust); color: #fff; border-color: var(--rust); }
.pagination-wrapper .disabled span { opacity: .45; cursor: not-allowed; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .rdv-page {
        background: transparent;
        padding-top: 4px;
    }

    /* ─── STATS INLINE iOS ─── */
    .rdv-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        grid-template-columns: none;
    }
    .rdv-stat {
        flex: 1;
        padding: 0 4px;
        background: transparent;
        border: none;
        border-radius: 0;
        gap: 2px;
    }
    .rdv-stat:hover { transform: none; box-shadow: none; }
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
        color: var(--ios-label-3);
        text-transform: none;
        letter-spacing: 0;
    }

    /* ─── FILTRES PILLS iOS ─── */
    .filter-tabs {
        margin: 0 -16px 14px;
        padding: 4px 16px 8px;
        gap: 8px;
    }
    .filter-tab {
        padding: 9px 16px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        -webkit-tap-highlight-color: transparent;
    }
    .filter-tab:active { transform: scale(.95); }
    .filter-tab.is-active {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .filter-tab.is-active i { color: #fff; opacity: 1; }
    .filter-tab.is-active .filter-tab__count {
        background: rgba(255, 255, 255, .3);
        color: #fff;
    }
    .filter-tab__count {
        background: #EFEFF4;
        color: var(--ios-label-2);
        font-size: 11px;
        padding: 1px 8px;
    }

    .flash {
        border-radius: 14px;
        border: none;
        padding: 14px 16px;
        font-size: 13.5px;
    }

    /* ─── CARTES iOS ─── */
    .rdv-list { gap: 12px; }

    .rdv-card {
        display: block;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: transform .15s ease;
        overflow: hidden;
        position: relative;
    }
    .rdv-card:hover { transform: none; box-shadow: 0 1px 3px rgba(0,0,0,.04); border: none; }
    .rdv-card:active { transform: scale(.985); }

    .rdv-card::before {
        left: 0; right: 0;
        height: 3px;
        border-radius: 0;
    }

    .rdv-card__date {
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        gap: 10px;
        padding: 12px 14px;
        margin-bottom: 12px;
        border-radius: 14px;
        border: none;
        background: #EFEFF4 !important;
    }
    .rdv-card--planifie .rdv-card__date { background: #FFF4EC !important; }
    .rdv-card--confirme .rdv-card__date { background: #E8F0FE !important; }
    .rdv-card--termine  .rdv-card__date { background: #E8F5E9 !important; }
    .rdv-card--annule   .rdv-card__date { background: #FFEBEE !important; }

    .rdv-card__day {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -.5px;
    }
    .rdv-card__month {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .3px;
    }
    .rdv-card__badge {
        margin-top: 0;
        margin-left: auto;
        padding: 2px 10px;
        font-size: 10px;
    }

    .rdv-card__body { gap: 10px; }

    .rdv-card__head {
        flex-direction: row;
        align-items: flex-start;
        gap: 8px;
        justify-content: space-between;
    }
    .rdv-card__title {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
        color: var(--ios-label);
        white-space: normal;
        line-height: 1.3;
        flex: 1;
    }
    .rdv-status {
        font-size: 10.5px;
        text-transform: none;
        letter-spacing: 0;
        padding: 4px 10px;
        font-weight: 600;
        flex-shrink: 0;
    }
    .rdv-status i { font-size: 7px; }

    .rdv-card__meta {
        font-size: 13.5px;
        gap: 6px 14px;
        color: var(--ios-label-3);
    }
    .rdv-meta-item i { color: var(--ios-label-3); opacity: 1; }
    .rdv-meta-item--accent {
        color: var(--rust);
        font-weight: 600;
    }
    .rdv-meta-item--accent i { color: var(--rust); }

    .rdv-card__agency {
        background: #F5F5F7;
        border-radius: 12px;
        padding: 10px 12px;
        gap: 10px;
    }
    .rdv-agency-avatar { width: 36px; height: 36px; font-size: 14px; }
    .rdv-agency-info strong { font-size: 14px; font-weight: 600; }
    .rdv-agency-phone { font-size: 13px; }
    .rdv-agency-muted,
    .rdv-agency-locked { font-size: 12.5px; }

    .rdv-card__actions {
        gap: 8px;
        padding-top: 12px;
        border-top: 0.5px solid var(--ios-separator);
        flex-direction: column;
        align-items: stretch;
    }
    .rdv-card__actions .btn,
    .rdv-card__actions form,
    .rdv-card__actions .rdv-state-pill {
        width: 100%;
        justify-content: center;
    }

    .btn {
        padding: 12px 18px;
        font-size: 14px;
        border-radius: 12px;
        font-weight: 600;
    }
    .btn-rust {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    }
    .btn-ghost {
        background: var(--ios-card);
        border: 1px solid #E5E5EA;
        color: var(--ios-blue);
    }
    .btn-ghost:active { background: #EFEFF4; }
    .btn-danger-soft {
        background: rgba(198, 40, 40, .08);
        border: none;
        color: var(--c-danger);
    }
    .btn-success {
        background: linear-gradient(135deg, #25D366, #1DA851);
        border: none;
        box-shadow: 0 4px 12px rgba(37, 211, 102, .25);
    }

    .rdv-state-pill {
        padding: 12px 16px;
        font-size: 14px;
        border-radius: 12px;
        justify-content: center;
    }

    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .empty-state__icon {
        width: 68px;
        height: 68px;
        font-size: 28px;
    }
    .empty-state h3 {
        font-size: 17px;
        letter-spacing: -.3px;
    }
    .empty-state p {
        font-size: 13.5px;
        color: var(--ios-label-3);
    }

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

    .rdv-card { padding: 14px; }
    .rdv-card__title { font-size: 15px; }
    .rdv-card__day { font-size: 20px; }
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