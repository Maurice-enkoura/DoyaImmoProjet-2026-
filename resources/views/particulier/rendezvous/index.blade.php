@extends('layouts.dashboard')

@section('title', 'Mes rendez-vous — DoyaImmo')
@section('page_title', 'Mes rendez-vous')
@section('page_sub', 'Vos visites planifiées avec les agences')

@section('content')
<div class="view active rdv-page">

    {{-- ═══════════════════════════════════════════
         FLASH MESSAGES
    ═══════════════════════════════════════════ --}}
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

    {{-- ═══════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Mes rendez-vous</h2>
            <p>Vos visites planifiées avec les agences</p>
        </div>
    </div>

    @if($rendezVous->count() > 0)

        {{-- ═══════════════════════════════════════════
             LISTE DES RENDEZ-VOUS
        ═══════════════════════════════════════════ --}}
        <div class="rdv-list">
            @foreach($rendezVous as $rdv)
                @php
                    $statutValue = $rdv->statut->value;
                    $bien        = $rdv->proposition->bien ?? null;
                    $agence      = $rdv->agence ?? null;
                    $telVisible  = in_array($statutValue, ['confirme', 'termine']);
                    $hasTel      = $agence && $agence->user && !empty($agence->user->telephone);

                    // Vérifie si déjà évalué (uniquement si terminé)
                    $dejaEvalue = false;
                    if ($statutValue === 'termine') {
                        $dejaEvalue = \App\Models\Evaluation::where('particulier_id', Auth::user()->particulier->id)
                            ->where('proposition_id', $rdv->proposition_id)
                            ->exists();
                    }
                @endphp

                <article class="rdv-card rdv-card--{{ $statutValue }}">

                    {{-- ═══ Colonne Date ═══ --}}
                    <div class="rdv-card__date">
                        <span class="rdv-card__day">{{ $rdv->date_visite->format('d') }}</span>
                        <span class="rdv-card__month">{{ strtoupper($rdv->date_visite->translatedFormat('M')) }}</span>
                        <span class="rdv-card__time">
                            {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                        </span>
                    </div>

                    {{-- ═══ Corps ═══ --}}
                    <div class="rdv-card__body">

                        {{-- En-tête : titre + statut --}}
                        <header class="rdv-card__head">
                            <h3 class="rdv-card__title">
                                {{ $bien->titre ?? 'Visite' }}
                                @if($bien && $bien->quartier)
                                    <span class="rdv-card__title-loc">— {{ $bien->quartier }}</span>
                                @endif
                            </h3>
                            <span class="rdv-status rdv-status--{{ $statutValue }}">
                                <i class="fa-solid fa-circle"></i>
                                {{ $rdv->statut->label() }}
                            </span>
                        </header>

                        {{-- Infos agence + contact --}}
                        <div class="rdv-card__infos">

                            <span class="rdv-info">
                                <i class="fa-regular fa-building"></i>
                                {{ $agence->nom_agence ?? 'Agence' }}
                            </span>

                            <span class="rdv-info">
                                <i class="fa-solid fa-phone"></i>
                                @if($telVisible)
                                    @if($hasTel)
                                        <a href="tel:{{ $agence->user->telephone }}" class="rdv-info__phone">
                                            {{ $agence->user->telephone }}
                                        </a>
                                        <i class="fa-solid fa-circle-check rdv-info__check"></i>
                                    @else
                                        <span class="rdv-info__muted">Non renseigné</span>
                                    @endif
                                @else
                                    <span class="rdv-info__locked">
                                        <i class="fa-solid fa-lock"></i>
                                        En attente de confirmation
                                    </span>
                                @endif
                            </span>

                            @if($bien)
                                <span class="rdv-info rdv-info--muted">
                                    <i class="fa-regular fa-clock"></i>
                                    Visite du bien
                                </span>
                            @endif

                        </div>

                        {{-- Actions --}}
                        <footer class="rdv-card__actions">

                            @if($statutValue === 'planifie')
                                {{-- En attente : juste annuler --}}
                                <span class="rdv-state-pill rdv-state-pill--warning">
                                    <i class="fa-solid fa-clock"></i>
                                    En attente de confirmation
                                </span>
                                <form action="{{ route('particulier.rendezvous.annuler', $rdv) }}" method="POST" class="rdv-action-form">
                                    @csrf
                                    <button type="submit" class="btn btn-danger-soft btn-sm"
                                            onclick="return confirm('Annuler ce rendez-vous ?')">
                                        <i class="fa-solid fa-xmark"></i> Annuler
                                    </button>
                                </form>

                            @elseif($statutValue === 'confirme')
                                {{-- Confirmé : appeler + annuler --}}
                                @if($hasTel)
                                    <a href="tel:{{ $agence->user->telephone }}" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-phone"></i> Appeler l'agence
                                    </a>
                                @endif
                                <form action="{{ route('particulier.rendezvous.annuler', $rdv) }}" method="POST" class="rdv-action-form">
                                    @csrf
                                    <button type="submit" class="btn btn-danger-soft btn-sm"
                                            onclick="return confirm('Annuler ce rendez-vous ?')">
                                        <i class="fa-solid fa-xmark"></i> Annuler
                                    </button>
                                </form>

                            @elseif($statutValue === 'termine')
                                {{-- Terminé : évaluer + appeler --}}
                                @if(!$dejaEvalue)
                                    <a href="{{ route('particulier.evaluations.create', $rdv->proposition) }}" class="btn btn-rust btn-sm">
                                        <i class="fa-solid fa-star"></i> Évaluer l'agence
                                    </a>
                                @else
                                    <span class="rdv-state-pill rdv-state-pill--success">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Déjà évalué
                                    </span>
                                @endif
                                @if($hasTel)
                                    <a href="tel:{{ $agence->user->telephone }}" class="btn btn-success btn-sm">
                                        <i class="fa-solid fa-phone"></i> Appeler
                                    </a>
                                @endif

                            @elseif($statutValue === 'annule')
                                {{-- Annulé : juste informatif --}}
                                <span class="rdv-state-pill rdv-state-pill--danger">
                                    <i class="fa-solid fa-ban"></i>
                                    Rendez-vous annulé
                                </span>
                            @endif

                            <a href="{{ route('particulier.rendezvous.show', $rdv) }}" class="btn btn-ghost btn-sm rdv-card__details">
                                <i class="fa-solid fa-eye"></i> Détails
                            </a>
                        </footer>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- ═══ Pagination ═══ --}}
        <div class="pagination-wrapper">
            {{ $rendezVous->links() }}
        </div>

    @else

        {{-- ═══════════════════════════════════════════
             EMPTY STATE
        ═══════════════════════════════════════════ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-calendar-days"></i>
            </div>
            <h3>Aucun rendez-vous planifié</h3>
            <p>Les rendez-vous apparaîtront ici après avoir accepté une proposition d'agence.</p>
            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust">
                <i class="fa-solid fa-plus"></i> Publier un besoin
            </a>
        </div>
    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE RENDEZ-VOUS — Particulier
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
}

/* ═══ FLASH MESSAGES ═══ */
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

/* ═══ HEADER ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.page-header__left h2 {
    font-family: var(--display);
    font-size: 21px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.2;
}
.page-header__left p {
    font-size: 13px;
    color: var(--muted);
    margin: 3px 0 0;
}

/* ═══ LISTE RDV ═══ */
.rdv-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

/* ═══ CARTE RDV ═══ */
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

/* Bande colorée statut en haut */
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

/* ═══ Colonne Date ═══ */
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
.rdv-card__time {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 2px 8px;
    background: #fff;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    color: var(--text);
    margin-top: 3px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .06);
}

/* ═══ Corps ═══ */
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
.rdv-card__title-loc {
    font-weight: 400;
    color: var(--muted);
    font-size: 13px;
}

/* Statut pill */
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

/* ═══ Infos ═══ */
.rdv-card__infos {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 18px;
    font-size: 12.5px;
    color: var(--text-soft);
}

.rdv-info {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-width: 0;
}
.rdv-info i {
    color: var(--muted);
    font-size: 11px;
    flex-shrink: 0;
}
.rdv-info--muted {
    color: var(--muted);
    font-style: italic;
}

.rdv-info__phone {
    color: var(--text);
    font-weight: 600;
    text-decoration: none;
    transition: color .2s;
}
.rdv-info__phone:hover { color: var(--rust); }

.rdv-info__check {
    color: var(--c-success) !important;
    font-size: 10px !important;
}

.rdv-info__muted {
    color: var(--muted);
    font-style: italic;
    opacity: .85;
}

.rdv-info__locked {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    color: var(--muted);
    font-size: 11.5px;
    font-style: italic;
}
.rdv-info__locked i {
    font-size: 9px;
    color: var(--rust) !important;
}

/* ═══ Actions ═══ */
.rdv-card__actions {
    display: flex;
    align-items: center;
    gap: 6px;
    padding-top: 8px;
    border-top: 1px dashed var(--border);
    flex-wrap: wrap;
}
.rdv-action-form { display: inline; }

.rdv-card__details { margin-left: auto; }

/* Pills d'état (informatifs) */
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
.rdv-state-pill--warning {
    background: var(--c-warning-bg);
    color: var(--c-warning);
    border-color: #FFE0B2;
}
.rdv-state-pill--success {
    background: var(--c-success-bg);
    color: var(--c-success);
    border-color: #C8E6C9;
}
.rdv-state-pill--danger {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border-color: #FFCDD2;
}

/* ═══ BOUTONS ═══ */
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
    box-shadow: 0 3px 8px rgba(180, 83, 42, .22);
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
.btn-danger-soft {
    background: var(--c-danger-bg);
    color: var(--c-danger);
    border-color: #FFCDD2;
}
.btn-danger-soft:hover {
    background: var(--c-danger);
    color: #fff;
    border-color: var(--c-danger);
}
.btn-success {
    background: #25D366;
    color: #fff;
    border-color: #25D366;
}
.btn-success:hover {
    background: #1DA851;
    color: #fff;
    border-color: #1DA851;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(37, 211, 102, .22);
}
.btn-sm { padding: 5px 11px; font-size: 11.5px; }

/* ═══ EMPTY STATE ═══ */
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

/* ═══ PAGINATION ═══ */
.pagination-wrapper {
    margin-top: 20px;
    display: flex;
    justify-content: center;
}
.pagination-wrapper .pagination,
.pagination-wrapper nav {
    display: flex;
    gap: 5px;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
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
.pagination-wrapper a:hover {
    background: var(--border);
    border-color: var(--rust);
    color: var(--rust);
}
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.pagination-wrapper .disabled span {
    opacity: .45;
    cursor: not-allowed;
}

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 768px) {
    .page-header__left h2 { font-size: 19px; }
    .page-header__left p  { font-size: 12.5px; }

    .rdv-card {
        grid-template-columns: 1fr;
        gap: 10px;
        padding: 12px 14px;
    }
    .rdv-card::before { left: 14px; right: 14px; }

    .rdv-card__date {
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        gap: 8px;
        padding: 7px 12px;
    }
    .rdv-card__day { font-size: 18px; }
    .rdv-card__month { margin-bottom: 0; }
    .rdv-card__time { margin-left: auto; margin-top: 0; }

    .rdv-card__head { flex-direction: column; align-items: flex-start; gap: 5px; }
    .rdv-card__title { white-space: normal; }
    .rdv-card__infos { gap: 4px 14px; }

    .rdv-card__actions { gap: 5px; }
    .rdv-card__actions .btn,
    .rdv-card__actions form,
    .rdv-card__actions .rdv-state-pill {
        flex: 1;
        min-width: 0;
        justify-content: center;
    }
    .rdv-action-form { flex: 1; }
    .rdv-action-form .btn { width: 100%; }
    .rdv-card__details { margin-left: 0; }
}

@media (max-width: 480px) {
    .rdv-card { padding: 10px 12px; }
    .rdv-card::before { left: 12px; right: 12px; }
    .rdv-card__title { font-size: 13.5px; }
    .rdv-card__title-loc { font-size: 12px; display: block; }
    .rdv-info { font-size: 12px; }

    .btn { font-size: 11px; padding: 5px 10px; }
    .btn-sm { font-size: 10.5px; padding: 5px 10px; }

    .rdv-card__actions .btn,
    .rdv-card__actions form,
    .rdv-card__actions .rdv-state-pill { flex-basis: 100%; }
}
</style>
@endpush