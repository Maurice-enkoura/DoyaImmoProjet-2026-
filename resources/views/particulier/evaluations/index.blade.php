@extends('layouts.dashboard')

@section('title', 'Mes avis donnés — DoyaImmo')
@section('page_title', 'Mes avis donnés')
@section('page_sub', 'Vos évaluations des agences avec lesquelles vous avez travaillé')

@section('content')
<div class="view active avis-donnes">

    @php
        $total = $evaluations->total();
        $moyenne = $evaluations->avg('note') ?? 0;
        $cinqEtoiles = $evaluations->where('note', 5)->count();
        $avecReponse = $evaluations->whereNotNull('reponse_agence')->count();
    @endphp

    {{-- ═══════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Mes avis donnés</h2>
            <p>Vos évaluations des agences avec lesquelles vous avez travaillé</p>
        </div>
        <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-calendar"></i> Voir mes rendez-vous
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         ALERTS
    ═══════════════════════════════════════════ --}}
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($total > 0)

        {{-- ═══════════════════════════════════════════
             STATS RAPIDES
        ═══════════════════════════════════════════ --}}
        <section class="summary">
            <div class="summary__item">
                <span class="summary__value">
                    <i class="fa-solid fa-star"></i>
                    {{ number_format($moyenne, 1) }}
                </span>
                <span class="summary__label">Note moyenne</span>
            </div>

            <div class="summary__sep"></div>

            <div class="summary__item">
                <span class="summary__value">{{ $total }}</span>
                <span class="summary__label">Avis donnés</span>
            </div>

            @if($cinqEtoiles > 0)
                <div class="summary__sep"></div>
                <div class="summary__item">
                    <span class="summary__value summary__value--success">{{ $cinqEtoiles }}</span>
                    <span class="summary__label">
                        <i class="fa-solid fa-star"></i> 5 étoiles
                    </span>
                </div>
            @endif

            @if($avecReponse > 0)
                <div class="summary__sep"></div>
                <div class="summary__item">
                    <span class="summary__value summary__value--info">{{ $avecReponse }}</span>
                    <span class="summary__label">
                        <i class="fa-solid fa-reply"></i> Avec réponse
                    </span>
                </div>
            @endif
        </section>

        {{-- ═══════════════════════════════════════════
             LISTE DES AVIS
        ═══════════════════════════════════════════ --}}
        <div class="avis-list">
            @foreach($evaluations as $evaluation)

                @php
                    $note         = (int) $evaluation->note;
                    $sentiment    = $note >= 4 ? 'positif' : ($note === 3 ? 'neutre' : 'negatif');
                    $initial      = strtoupper(mb_substr($evaluation->agence->nom_agence ?? 'A', 0, 1));
                    $hasReponse   = !empty($evaluation->reponse_agence);
                    $isOwner      = $evaluation->particulier_id === Auth::user()->particulier->id;
                    $proposition  = $evaluation->proposition;
                    $bien         = $proposition->bien ?? null;
                @endphp

                <article class="avis-card avis-card--{{ $sentiment }}">

                    {{-- En-tête : agence + note --}}
                    <header class="avis-card__head">
                        <div class="avis-card__agency">
                            <div class="agency-avatar">{{ $initial }}</div>
                            <div class="agency-info">
                                <strong>{{ $evaluation->agence->nom_agence }}</strong>
                                <span class="avis-card__date">
                                    <i class="fa-regular fa-calendar"></i>
                                    Avis du {{ $evaluation->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>

                        <div class="avis-card__rating avis-card__rating--{{ $sentiment }}">
                            <div class="avis-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $note ? 'is-on' : '' }}"></i>
                                @endfor
                            </div>
                            <span class="avis-card__score">{{ $note }}<small>/5</small></span>
                        </div>
                    </header>

                    {{-- Proposition associée --}}
                    @if($proposition)
                        <div class="avis-card__prop">
                            <i class="fa-regular fa-file-lines"></i>
                            <span>
                                Proposition du
                                <strong>{{ $proposition->created_at->format('d/m/Y') }}</strong>
                                @if($bien)
                                    · {{ $bien->titre ?? 'Bien' }}
                                @endif
                            </span>
                        </div>
                    @endif

                    {{-- Commentaire --}}
                    @if($evaluation->commentaire)
                        <blockquote class="avis-card__comment">
                            {{ $evaluation->commentaire }}
                        </blockquote>
                    @endif

                    {{-- Réponse de l'agence --}}
                    @if($hasReponse)
                        <div class="reponse-block">
                            <div class="reponse-block__head">
                                <i class="fa-solid fa-reply"></i>
                                <span>Réponse de l'agence</span>
                                @if($evaluation->date_reponse)
                                    <span class="reponse-block__date">
                                        · {{ $evaluation->date_reponse->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                            <p class="reponse-block__text">{{ $evaluation->reponse_agence }}</p>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <footer class="avis-card__actions">
                        <a href="{{ route('particulier.evaluations.show', $evaluation->id) }}"
                           class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-eye"></i> Voir le détail
                        </a>

                        @if($isOwner)
                            <a href="{{ route('particulier.evaluations.edit', $evaluation->id) }}"
                               class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </a>

                            <form action="{{ route('particulier.evaluations.destroy', $evaluation->id) }}"
                                  method="POST" class="inline-form avis-card__delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger-soft btn-sm"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
                                    <i class="fa-solid fa-trash-can"></i> Supprimer
                                </button>
                            </form>
                        @endif
                    </footer>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $evaluations->links() }}
        </div>

    @else

        {{-- ═══════════════════════════════════════════
             EMPTY STATE
        ═══════════════════════════════════════════ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-star"></i>
            </div>
            <h3>Aucun avis donné pour le moment</h3>
            <p>Évaluez les agences après avoir terminé une visite. Vos avis aident les autres clients à choisir.</p>
            <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-rust">
                <i class="fa-solid fa-calendar"></i> Voir mes rendez-vous
            </a>
        </div>
    @endif
</div>
@endsection


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE AVIS DONNÉS — Particulier
   ═══════════════════════════════════════════════════════════ */

.avis-donnes {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #F5A623;
    --surface:      #F7F9FC;
    --radius:       14px;
}

/* ═══ HEADER ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 18px;
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

/* ═══ ALERTS ═══ */
.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-radius: 10px;
    margin-bottom: 14px;
    border-left: 4px solid;
    font-size: 13px;
    font-weight: 500;
}
.alert-success { background: var(--c-success-bg); color: var(--c-success); border-left-color: var(--c-success); }
.alert-error   { background: var(--c-danger-bg);  color: var(--c-danger);  border-left-color: var(--c-danger); }
.alert-info    { background: var(--c-info-bg);    color: var(--c-info);    border-left-color: var(--c-info); }

/* ═══ STATS RAPIDES ═══ */
.summary {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 18px 24px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.summary__item {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.summary__value {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    line-height: 1;
    color: var(--text);
}
.summary__value i {
    color: var(--c-gold);
    font-size: 19px;
}
.summary__value--success { color: var(--c-success); }
.summary__value--success i { color: var(--c-success); }
.summary__value--info    { color: var(--c-info); }
.summary__value--info i { color: var(--c-info); }

.summary__label {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.summary__label i { margin-right: 3px; font-size: 10px; }

.summary__sep {
    width: 1px;
    height: 34px;
    background: var(--border);
    flex-shrink: 0;
}

/* ═══ LISTE AVIS ═══ */
.avis-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.avis-card {
    position: relative;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 20px;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
    overflow: hidden;
}
.avis-card:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, .05);
    border-color: #d8d8d8;
}

/* Bande colorée selon sentiment */
.avis-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; bottom: 0;
    width: 4px;
}
.avis-card--positif::before { background: var(--c-success); }
.avis-card--neutre::before  { background: var(--c-warning); }
.avis-card--negatif::before { background: var(--c-danger); }

/* En-tête */
.avis-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.avis-card__agency {
    display: flex;
    align-items: center;
    gap: 11px;
    min-width: 0;
}
.agency-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 18px;
    font-weight: 700;
    flex-shrink: 0;
}
.agency-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
}
.agency-info strong {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    color: var(--text);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.avis-card__date {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    color: var(--muted);
}
.avis-card__date i { font-size: 11px; opacity: .8; }

/* Note */
.avis-card__rating {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 12px;
    border-radius: 999px;
    flex-shrink: 0;
}
.avis-card__rating--positif { background: var(--c-success-bg); }
.avis-card__rating--neutre  { background: var(--c-warning-bg); }
.avis-card__rating--negatif { background: var(--c-danger-bg); }

.avis-stars {
    display: inline-flex;
    gap: 3px;
    font-size: 14px;
}
.avis-stars .fa-star { color: #D4D8E0; }
.avis-stars .fa-star.is-on { color: var(--c-gold); }

.avis-card__score {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 800;
    line-height: 1;
}
.avis-card__score small {
    font-size: 10px;
    font-weight: 600;
    opacity: .65;
    margin-left: 1px;
}
.avis-card__rating--positif .avis-card__score { color: var(--c-success); }
.avis-card__rating--neutre  .avis-card__score { color: var(--c-warning); }
.avis-card__rating--negatif .avis-card__score { color: var(--c-danger); }

/* Proposition associée */
.avis-card__prop {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 12px;
    background: var(--surface);
    border-radius: 999px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 12px;
}
.avis-card__prop i { font-size: 11px; color: var(--rust); }
.avis-card__prop strong { color: var(--text); font-weight: 600; }

/* Commentaire */
.avis-card__comment {
    position: relative;
    padding: 14px 18px 14px 22px;
    border-radius: 12px;
    background: var(--surface);
    font-size: 13.5px;
    line-height: 1.65;
    color: var(--text);
    font-style: italic;
    margin: 0 0 12px;
    border-left: 3px solid var(--muted);
}
.avis-card--positif .avis-card__comment {
    background: linear-gradient(135deg, rgba(30, 122, 71, .05), rgba(30, 122, 71, .02));
    border-left-color: var(--c-success);
}
.avis-card--neutre .avis-card__comment {
    background: linear-gradient(135deg, rgba(230, 81, 0, .05), rgba(230, 81, 0, .02));
    border-left-color: var(--c-warning);
}
.avis-card--negatif .avis-card__comment {
    background: linear-gradient(135deg, rgba(198, 40, 40, .05), rgba(198, 40, 40, .02));
    border-left-color: var(--c-danger);
}

/* Réponse agence */
.reponse-block {
    padding: 12px 14px;
    background: var(--c-info-bg);
    border-radius: 10px;
    border-left: 3px solid var(--c-info);
    margin-bottom: 12px;
}
.reponse-block__head {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    color: var(--c-info);
    flex-wrap: wrap;
}
.reponse-block__head i { font-size: 10px; }
.reponse-block__date {
    font-weight: 500;
    text-transform: none;
    letter-spacing: 0;
    color: var(--muted);
}
.reponse-block__text {
    font-size: 13px;
    line-height: 1.6;
    color: var(--text-soft);
    margin: 0;
}

/* Actions */
.avis-card__actions {
    display: flex;
    align-items: center;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px dashed var(--border);
    flex-wrap: wrap;
}
.avis-card__delete { margin-left: auto; }
.inline-form { display: inline; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border-radius: 10px;
    font-size: 12.5px;
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
    box-shadow: 0 4px 10px rgba(180, 83, 42, .25);
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
.btn-sm { padding: 6px 12px; font-size: 12px; }

/* ═══ EMPTY STATE ═══ */
.empty-state {
    text-align: center;
    padding: 70px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 76px;
    height: 76px;
    margin: 0 auto 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #FFF8E1, #FFECB3);
    border-radius: 50%;
    color: var(--c-gold);
    font-size: 30px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
}
.empty-state p {
    color: var(--muted);
    font-size: 13.5px;
    margin: 0 auto 20px;
    max-width: 420px;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper {
    margin-top: 22px;
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
    padding: 7px 12px;
    border-radius: 8px;
    border: 1px solid var(--border);
    color: var(--text-soft);
    text-decoration: none;
    font-size: 12.5px;
    transition: all .2s;
    min-width: 36px;
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
    .page-header__left p  { font-size: 13px; }

    .summary {
        padding: 14px 18px;
        gap: 16px;
    }
    .summary__value { font-size: 20px; }
    .summary__value i { font-size: 16px; }
    .summary__label { font-size: 11.5px; }
    .summary__sep { height: 28px; }

    .avis-card { padding: 16px; }
    .avis-card__head { flex-direction: column; align-items: flex-start; gap: 10px; }
    .avis-card__rating { align-self: flex-start; }

    .avis-card__actions { flex-direction: column; align-items: stretch; }
    .avis-card__actions .btn,
    .avis-card__actions form,
    .avis-card__actions .inline-form { width: 100%; justify-content: center; }
    .avis-card__delete { margin-left: 0; }
}

@media (max-width: 480px) {
    .summary {
        gap: 12px;
        padding: 12px 14px;
    }
    .summary__value { font-size: 18px; gap: 5px; }
    .summary__value i { font-size: 14px; }
    .summary__label { font-size: 11px; }

    .avis-card { padding: 14px; }
    .agency-avatar { width: 38px; height: 38px; font-size: 15px; }
    .agency-info strong { font-size: 14px; }
    .avis-stars { font-size: 12px; }
    .avis-card__comment { font-size: 13px; padding: 12px 14px 12px 18px; }
    .reponse-block__text { font-size: 12.5px; }

    .btn { font-size: 12px; padding: 7px 13px; }
    .btn-sm { font-size: 11.5px; padding: 6px 11px; }
}
</style>
@endpush