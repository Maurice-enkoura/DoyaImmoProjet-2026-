@extends('layouts.admin')

@section('title', 'Détail du rendez-vous — Administration DoyaImmo')
@section('page_title', 'Détail du rendez-vous')
@section('page_sub', 'Rendez-vous du ' . ($rendezVous->date_visite ? \Carbon\Carbon::parse($rendezVous->date_visite)->format('d/m/Y') : 'N/A'))

@section('content')
<div class="rdv-show">

    @php
        // ✅ Statut sécurisé
        $statutValue = is_object($rendezVous->statut)
            ? $rendezVous->statut->value
            : (string) $rendezVous->statut;
        $statutLabel = is_object($rendezVous->statut)
            ? $rendezVous->statut->label()
            : ucfirst($statutValue);

        // ✅ Relations sécurisées
        $agence      = $rendezVous->agence;
        $agenceUser  = $agence->user ?? null;
        $particulier = $rendezVous->particulier;
        $partUser    = $particulier->user ?? null;
        $proposition = $rendezVous->proposition;
        $bien        = $proposition->bien ?? null;

        // ✅ Date sécurisée
        $dateVisite = $rendezVous->date_visite
            ? \Carbon\Carbon::parse($rendezVous->date_visite)
            : null;

        // ✅ Médias du bien
        $photos = $bien ? $bien->medias->where('type_media', 'image')->values() : collect();

        // Initiales particulier
        $initiales = strtoupper(
            mb_substr($partUser->prenom ?? 'U', 0, 1) .
            mb_substr($partUser->nom ?? 'U', 0, 1)
        );
    @endphp

    {{-- ═══ ACTIONS ═══ --}}
    <div class="page-actions">
        <a href="{{ route('admin.rendezvous.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux rendez-vous
        </a>
        @if($proposition)
            <a href="{{ route('admin.propositions.show', $proposition->id) }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-handshake"></i> Voir la proposition
            </a>
        @endif
        @if($bien)
            <a href="{{ route('admin.biens.show', $bien->id) }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-house"></i> Voir le bien
            </a>
        @endif
    </div>

    <div class="grid-2">

        {{-- ═══ COLONNE GAUCHE ═══ --}}
        <div>

            {{-- Carte principale RDV --}}
            <div class="panel">
                <div class="panel-head">
                    <div>
                        <h3>
                            <i class="fa-solid fa-calendar-check"></i>
                            Rendez-vous #{{ $rendezVous->id }}
                        </h3>
                        <div class="panel-head__meta">
                            <i class="fa-solid fa-calendar"></i>
                            {{ $dateVisite ? $dateVisite->format('d/m/Y') : 'N/A' }}
                            <span class="sep">·</span>
                            <i class="fa-solid fa-clock"></i>
                            {{ $rendezVous->heure_visite ?? 'N/A' }}
                        </div>
                    </div>
                    <span class="status-pill status-{{ $statutValue }}">
                        {{ $statutLabel }}
                    </span>
                </div>

                {{-- ✅ Code de visite --}}
                @if($rendezVous->code_visite)
                    <div class="code-block {{ $rendezVous->code_valide ? 'code-block--validated' : '' }}">
                        <div class="code-block__icon">
                            <i class="fa-solid fa-key"></i>
                        </div>
                        <div class="code-block__body">
                            <div class="code-block__label">Code de visite</div>
                            <div class="code-block__value">{{ $rendezVous->code_visite }}</div>
                            @if($rendezVous->code_valide)
                                <div class="code-block__status">
                                    <i class="fa-solid fa-circle-check"></i>
                                    Validé le
                                    {{ $rendezVous->code_valide_le ? $rendezVous->code_valide_le->format('d/m/Y à H:i') : '—' }}
                                </div>
                            @else
                                <div class="code-block__status code-block__status--pending">
                                    <i class="fa-regular fa-clock"></i>
                                    En attente de validation
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="info-grid">

                    {{-- Agence --}}
                    @if($agence)
                        <div class="info-block info-block--full">
                            <div class="info-block__label">
                                <i class="fa-solid fa-building"></i> Agence
                            </div>
                            <div class="info-agency">
                                @if($agence->logo)
                                    <img src="{{ asset('storage/' . $agence->logo) }}"
                                         alt="{{ $agence->nom_agence }}"
                                         class="info-agency__logo">
                                @else
                                    <div class="info-agency__logo info-agency__logo--fallback">
                                        {{ Str::substr($agence->nom_agence ?? 'NA', 0, 2) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="info-agency__name">{{ $agence->nom_agence ?? 'N/A' }}</div>
                                    @if($agenceUser)
                                        <div class="info-agency__contact">
                                            <i class="fa-solid fa-envelope"></i> {{ $agenceUser->email ?? '—' }}
                                        </div>
                                        <div class="info-agency__contact">
                                            <i class="fa-solid fa-phone"></i> {{ $agenceUser->telephone ?? 'Non renseigné' }}
                                        </div>
                                    @endif
                                    @if($agence->adresse)
                                        <div class="info-agency__contact">
                                            <i class="fa-solid fa-location-dot"></i> {{ $agence->adresse }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Particulier --}}
                    @if($particulier)
                        <div class="info-block info-block--full">
                            <div class="info-block__label">
                                <i class="fa-solid fa-user"></i> Particulier
                            </div>
                            <div class="info-agency">
                                <div class="info-agency__avatar-rust">{{ $initiales }}</div>
                                <div>
                                    <div class="info-agency__name">
                                        {{ $partUser->prenom ?? '' }} {{ $partUser->nom ?? '' }}
                                    </div>
                                    @if($partUser)
                                        <div class="info-agency__contact">
                                            <i class="fa-solid fa-envelope"></i> {{ $partUser->email ?? '—' }}
                                        </div>
                                        <div class="info-agency__contact">
                                            <i class="fa-solid fa-phone"></i> {{ $partUser->telephone ?? 'Non renseigné' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Proposition --}}
                    @if($proposition)
                        <div class="info-block info-block--full">
                            <div class="info-block__label">
                                <i class="fa-solid fa-handshake"></i> Proposition associée
                            </div>
                            <div class="info-block__value">
                                Proposition #{{ $proposition->id }}
                                <span class="info-block__sep">·</span>
                                <strong>{{ number_format($proposition->prix_propose ?? 0, 0, ',', ' ') }} FCFA</strong>
                            </div>
                            @if($bien)
                                <div class="info-block__sub">
                                    <i class="fa-solid fa-house"></i> {{ $bien->titre ?? 'N/A' }}
                                    @if($bien->type_contrat)
                                        <span class="meta-pill meta-pill--info">
                                            {{ is_object($bien->type_contrat) ? $bien->type_contrat->label() : $bien->type_contrat }}
                                        </span>
                                    @endif
                                    @if($bien->adresse)
                                        <span class="info-block__sep">·</span>
                                        <i class="fa-solid fa-location-dot"></i> {{ $bien->adresse }}
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Date --}}
                    <div class="info-block">
                        <div class="info-block__label">Date visite</div>
                        <div class="info-block__value">
                            {{ $dateVisite ? $dateVisite->format('d/m/Y') : 'N/A' }}
                        </div>
                    </div>

                    {{-- Heure --}}
                    <div class="info-block">
                        <div class="info-block__label">Heure visite</div>
                        <div class="info-block__value">{{ $rendezVous->heure_visite ?? 'N/A' }}</div>
                    </div>

                    {{-- Créé le --}}
                    <div class="info-block">
                        <div class="info-block__label">Créé le</div>
                        <div class="info-block__value">{{ $rendezVous->created_at->format('d/m/Y H:i') }}</div>
                    </div>

                    {{-- Modifié le --}}
                    <div class="info-block">
                        <div class="info-block__label">Dernière modification</div>
                        <div class="info-block__value">{{ $rendezVous->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

                {{-- Note lecture seule --}}
                <div class="readonly-note">
                    <i class="fa-solid fa-info-circle"></i>
                    <span>
                        <strong>Lecture seule</strong> — Les rendez-vous ne peuvent pas être modifiés par l'administrateur.
                    </span>
                </div>
            </div>

            {{-- Infos du bien --}}
            @if($bien)
                <div class="panel">
                    <h3 class="panel-title">
                        <i class="fa-solid fa-info-circle"></i> Informations du bien
                    </h3>
                    <div class="characteristics">
                        <div class="char-item">
                            <span class="char-item__label">Titre</span>
                            <span class="char-item__value">{{ $bien->titre ?? 'N/A' }}</span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Prix</span>
                            <span class="char-item__value char-item__value--price">
                                {{ number_format($bien->prix ?? 0, 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Surface</span>
                            <span class="char-item__value">{{ $bien->surface ?? 0 }} m²</span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Chambres</span>
                            <span class="char-item__value">{{ $bien->nombre_chambres ?? '—' }}</span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Salles de bain</span>
                            <span class="char-item__value">{{ $bien->nombre_salles_bain ?? '—' }}</span>
                        </div>
                        <div class="char-item">
                            <span class="char-item__label">Adresse</span>
                            <span class="char-item__value">{{ $bien->adresse ?? '—' }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- ═══ COLONNE DROITE ═══ --}}
        <div>

            {{-- Photos du bien --}}
            @if($photos->count() > 0)
                <div class="panel">
                    <h3 class="panel-title">
                        <i class="fa-solid fa-image"></i> Photos du bien
                        <span class="panel-title__count">{{ $photos->count() }}</span>
                    </h3>
                    <div class="media-grid">
                        @foreach($photos->take(6) as $media)
                            <a href="{{ asset('storage/' . $media->fichier) }}" target="_blank"
                               class="media-grid-item">
                                <img src="{{ asset('storage/' . $media->fichier) }}"
                                     alt="Photo" loading="lazy">
                            </a>
                        @endforeach
                    </div>
                    @if($photos->count() > 6)
                        <div class="media-more">
                            + {{ $photos->count() - 6 }} autres photos
                        </div>
                    @endif
                </div>
            @endif

            {{-- Actions rapides --}}
            <div class="panel">
                <h3 class="panel-title">
                    <i class="fa-solid fa-bolt"></i> Actions rapides
                </h3>
                <div class="quick-actions">
                    @if($bien)
                        <a href="{{ route('admin.biens.show', $bien->id) }}" class="btn btn-rust btn-block">
                            <i class="fa-solid fa-house"></i> Voir le bien
                        </a>
                    @endif
                    @if($agence && Route::has('admin.agences.show'))
                        <a href="{{ route('admin.agences.show', $agence->id) }}" class="btn btn-ghost btn-block">
                            <i class="fa-solid fa-building"></i> Voir l'agence
                        </a>
                    @endif
                    @if($particulier && Route::has('admin.particuliers.show'))
                        <a href="{{ route('admin.particuliers.show', $particulier->id) }}" class="btn btn-ghost btn-block">
                            <i class="fa-solid fa-user"></i> Voir le particulier
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('styles')
<style>
/* ═══ PAGE ACTIONS ═══ */
.page-actions {
    display: flex;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

/* ═══ GRID 2 COLONNES ═══ */
.grid-2 {
    display: grid;
    grid-template-columns: 1.2fr 1fr;
    gap: 20px;
}

/* ═══ PANELS ═══ */
.panel {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px 22px;
    margin-bottom: 16px;
}
.panel-head {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
}
.panel-head h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--ink);
    display: flex;
    align-items: center;
    gap: 8px;
}
.panel-head h3 i { color: var(--rust); font-size: 15px; }
.panel-head__meta {
    font-size: 12.5px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}
.panel-head__meta i { color: var(--rust); opacity: 0.7; font-size: 11px; }
.panel-head__meta .sep { color: var(--border); }

.panel-title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 16px;
    color: var(--ink);
    display: flex;
    align-items: center;
    gap: 8px;
}
.panel-title i { color: var(--rust); font-size: 14px; }
.panel-title__count {
    margin-left: auto;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 10px;
    background: #FFF8E1;
    color: var(--rust);
    border-radius: 999px;
}

/* ═══ STATUS PILLS ═══ */
.status-pill {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    white-space: nowrap;
}
.status-planifie      { background: #FFF8E1; color: #E65100; }
.status-confirme      { background: #E3F2FD; color: #0D47A1; }
.status-termine       { background: #E8F5E9; color: #1E7A47; }
.status-annule        { background: #FFEBEE; color: #C62828; }
.status-client_absent { background: #EFEFF4; color: #6C6C70; }

.meta-pill {
    display: inline-flex;
    align-items: center;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 600;
    background: #F7F9FC;
    color: var(--text-soft);
    border: 1px solid var(--border);
    margin-left: 4px;
}
.meta-pill--info { background: #E3F2FD; color: #0D47A1; border-color: #BBDEFB; }

/* ═══ CODE BLOCK ═══ */
.code-block {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    background: linear-gradient(135deg, #FFF8E1, #FFFDF5);
    border: 1.5px solid #FFE0B2;
    border-radius: 12px;
    margin-bottom: 18px;
    position: relative;
    overflow: hidden;
}
.code-block--validated {
    background: linear-gradient(135deg, #E8F5E9, #F1F8F4);
    border-color: #A5D6A7;
}
.code-block::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 4px;
    background: #E65100;
}
.code-block--validated::before { background: #1E7A47; }

.code-block__icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #E65100;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.code-block--validated .code-block__icon { background: #1E7A47; }

.code-block__body { flex: 1; min-width: 0; }
.code-block__label {
    font-size: 11px;
    font-weight: 700;
    color: #E65100;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
}
.code-block--validated .code-block__label { color: #1E7A47; }

.code-block__value {
    font-family: monospace;
    font-size: 26px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: 4px;
    line-height: 1;
}
.code-block__status {
    font-size: 12px;
    font-weight: 600;
    color: #1E7A47;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.code-block__status--pending {
    color: #E65100;
    font-style: italic;
}

/* ═══ INFO GRID ═══ */
.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}
.info-block {
    padding: 12px 14px;
    background: #F7F9FC;
    border-radius: 10px;
    border: 1px solid #EEF1F5;
}
.info-block--full { grid-column: span 2; }
.info-block__label {
    font-size: 11px;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.info-block__label i { color: var(--rust); font-size: 11px; }
.info-block__value {
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1.4;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
}
.info-block__value--price { color: var(--rust); font-family: var(--display); font-size: 16px; }
.info-block__sub {
    font-size: 12.5px;
    color: var(--text-soft);
    margin-top: 6px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
}
.info-block__sub i { color: var(--rust); opacity: 0.7; font-size: 11px; }
.info-block__sep { color: var(--muted); }

/* ═══ INFO AGENCY ═══ */
.info-agency {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 4px;
}
.info-agency__logo {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid var(--border);
    flex-shrink: 0;
}
.info-agency__logo--fallback {
    background: #FFF8E1;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    color: #F5A623;
}
.info-agency__avatar-rust {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-weight: 700;
    font-size: 15px;
    flex-shrink: 0;
}
.info-agency__name {
    font-weight: 700;
    font-size: 14px;
    color: var(--ink);
    margin-bottom: 3px;
}
.info-agency__contact {
    font-size: 12px;
    color: var(--muted);
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 2px;
}
.info-agency__contact i { color: var(--rust); font-size: 11px; }

/* ═══ CARACTÉRISTIQUES ═══ */
.characteristics {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.char-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 8px;
    padding: 9px 12px;
    background: #F7F9FC;
    border-radius: 8px;
    border: 1px solid #EEF1F5;
}
.char-item__label {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.char-item__value {
    font-size: 13px;
    font-weight: 600;
    color: var(--ink);
    text-align: right;
}
.char-item__value--price { color: var(--rust); font-weight: 700; }

/* ═══ MEDIA GRID ═══ */
.media-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}
.media-grid-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid var(--border);
    background: #F7F9FC;
    transition: transform 0.2s, box-shadow 0.2s;
    display: block;
}
.media-grid-item:hover {
    transform: scale(1.02);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
}
.media-grid-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.media-more {
    text-align: center;
    margin-top: 10px;
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}

/* ═══ QUICK ACTIONS ═══ */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.btn-block {
    width: 100%;
    justify-content: center;
}

/* ═══ READONLY NOTE ═══ */
.readonly-note {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #FFF8E1;
    border: 1px solid #FFE0B2;
    border-radius: 10px;
    font-size: 13px;
    color: var(--text-soft);
    margin-top: 16px;
}
.readonly-note i { color: #E65100; font-size: 16px; flex-shrink: 0; }
.readonly-note strong { color: #E65100; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    white-space: nowrap;
}
.btn-rust { background: var(--rust); color: #fff; border-color: var(--rust); }
.btn-rust:hover { background: #9A4523; border-color: #9A4523; color: #fff; }
.btn-ghost { background: transparent; color: var(--text-soft); border-color: var(--border); }
.btn-ghost:hover { background: var(--border); color: var(--ink); }
.btn-sm { padding: 5px 12px; font-size: 12px; border-radius: 6px; }

/* ═══════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════ */
@media (max-width: 992px) {
    .grid-2 { grid-template-columns: 1fr; }
}

@media (max-width: 640px) {
    .info-grid { grid-template-columns: 1fr; }
    .info-block--full { grid-column: span 1; }
    .characteristics { grid-template-columns: 1fr; }
    .media-grid { grid-template-columns: repeat(2, 1fr); }
    .page-actions { flex-direction: column; }
    .page-actions .btn { width: 100%; justify-content: center; }
    .code-block__value { font-size: 22px; letter-spacing: 3px; }
}
</style>
@endpush