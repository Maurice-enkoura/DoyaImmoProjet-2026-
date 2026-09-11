@extends('layouts.dashboard-agence')

@section('title', 'Mes biens — DoyaImmo')
@section('page_title', 'Mes biens')
@section('page_sub', 'Gérez vos biens immobiliers')

@section('content')
<div class="view active biens-page">

    @php
        $currentStatut = request('statut', '');
        $isVedetteFilter = request('vedette') === 'true';

        // Compteurs (page courante)
        $countAll = $biens->total();
        $countDispo = $biens->where('statut', true)->count();
        $countIndispo = $biens->where('statut', false)->count();
        $countVedette = $biens->where('est_vedette', true)->count();
    @endphp

    {{-- ═══════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Mes biens</h2>
            <p>
                <strong>{{ $biens->total() }}</strong>
                bien{{ $biens->total() > 1 ? 's' : '' }} au total
            </p>
        </div>
        @if($peutPublier ?? false)
            <a href="{{ route('agence.biens.create') }}" class="btn btn-rust">
                <i class="fa-solid fa-plus"></i> Nouveau bien
            </a>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════
         FILTRES
    ═══════════════════════════════════════════ --}}
    <nav class="filter-tabs" aria-label="Filtrer les biens">
        <a href="{{ route('agence.biens.index') }}"
           class="filter-tab {{ !$currentStatut && !$isVedetteFilter ? 'is-active' : '' }}">
            <i class="fa-solid fa-list"></i>
            <span>Tous</span>
            <span class="filter-tab__count">{{ $countAll }}</span>
        </a>

        <a href="{{ route('agence.biens.index', ['statut' => 'disponible']) }}"
           class="filter-tab {{ $currentStatut == 'disponible' ? 'is-active' : '' }}">
            <i class="fa-solid fa-circle-check"></i>
            <span>Disponibles</span>
            <span class="filter-tab__count">{{ $countDispo }}</span>
        </a>

        <a href="{{ route('agence.biens.index', ['statut' => 'indisponible']) }}"
           class="filter-tab {{ $currentStatut == 'indisponible' ? 'is-active' : '' }}">
            <i class="fa-solid fa-eye-slash"></i>
            <span>Indisponibles</span>
            <span class="filter-tab__count">{{ $countIndispo }}</span>
        </a>

        <a href="{{ route('agence.biens.index', ['vedette' => 'true']) }}"
           class="filter-tab filter-tab--gold {{ $isVedetteFilter ? 'is-active' : '' }}">
            <i class="fa-solid fa-star"></i>
            <span>Vedettes</span>
            <span class="filter-tab__count">{{ $countVedette }}</span>
        </a>
    </nav>

    {{-- ═══════════════════════════════════════════
         ALERTS
    ═══════════════════════════════════════════ --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════
         GRILLE DES BIENS
    ═══════════════════════════════════════════ --}}
    @if($biens->count() > 0)
        <div class="biens-grid">
            @foreach($biens as $bien)

                @php
                    $image       = $bien->medias->where('type_media', 'image')->first();
                    $imagesCount = $bien->medias->where('type_media', 'image')->count();
                    $videosCount = $bien->medias->where('type_media', 'video')->count();

                    $isDispo    = (bool) $bien->statut;
                    $isVedette  = (bool) $bien->est_vedette;
                @endphp

                <article class="bien-card {{ $isVedette ? 'is-vedette' : '' }}">

                    {{-- ═══ Image ═══ --}}
                    <div class="bien-card__media">

                        @if($image)
                            <img src="{{ asset('storage/' . $image->fichier) }}"
                                 alt="{{ $bien->titre }}"
                                 loading="lazy">
                        @else
                            <div class="bien-card__media-placeholder">
                                <i class="fa-regular fa-image"></i>
                            </div>
                        @endif

                        {{-- Badge vedette en haut à gauche --}}
                        @if($isVedette)
                            <span class="media-badge media-badge--vedette">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif

                        {{-- Badge statut en haut à droite --}}
                        <span class="media-badge media-badge--statut {{ $isDispo ? 'is-dispo' : 'is-indispo' }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ $isDispo ? 'Disponible' : 'Indisponible' }}
                        </span>

                        {{-- Type de bien en bas à gauche --}}
                        <span class="media-badge media-badge--type">
                            {{ $bien->type_bien->label() }}
                        </span>

                        {{-- Compteur médias en bas à droite --}}
                        @if($imagesCount > 0 || $videosCount > 0)
                            <span class="media-badge media-badge--count">
                                @if($imagesCount > 0)
                                    <i class="fa-regular fa-image"></i> {{ $imagesCount }}
                                @endif
                                @if($videosCount > 0)
                                    <i class="fa-regular fa-circle-play"></i> {{ $videosCount }}
                                @endif
                            </span>
                        @endif
                    </div>

                    {{-- ═══ Corps ═══ --}}
                    <div class="bien-card__body">

                        {{-- Titre + Prix --}}
                        <div class="bien-card__headline">
                            <h3 class="bien-card__title">
                                {{ $bien->titre }}
                            </h3>
                            <div class="bien-card__price">
                                {{ number_format($bien->prix, 0, ',', ' ') }}
                                <small>FCFA</small>
                            </div>
                        </div>

                        {{-- Localisation --}}
                        <div class="bien-card__location">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $bien->quartier }}
                        </div>

                        {{-- Méta compactes --}}
                        <div class="bien-card__meta">
                            <span class="meta-pill">
                                <i class="fa-regular fa-square"></i> {{ $bien->surface }} m²
                            </span>
                            @if($bien->nombre_chambres)
                                <span class="meta-pill">
                                    <i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.
                                </span>
                            @endif
                            <span class="meta-pill">
                                {{ $bien->type_contrat->label() }}
                            </span>
                        </div>

                        {{-- Vues --}}
                        <div class="bien-card__views">
                            <i class="fa-regular fa-eye"></i>
                            <strong>{{ $bien->vues ?? 0 }}</strong>
                            vue{{ ($bien->vues ?? 0) > 1 ? 's' : '' }}
                        </div>
                    </div>

                    {{-- ═══ Actions ═══ --}}
                    <footer class="bien-card__actions">

                        @if($peutPublier ?? false)
                            <a href="{{ route('agence.biens.edit', $bien->slug) }}"
                               class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </a>
                        @else
                            <button type="button" class="btn btn-ghost btn-sm is-disabled"
                                    title="Abonnement Pro requis" disabled>
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                        @endif

                        <a href="{{ route('agence.biens.show', $bien->slug) }}"
                           class="btn btn-rust btn-sm">
                            <i class="fa-solid fa-eye"></i> Voir
                        </a>

                        {{-- Menu contextuel "…" --}}
                        @if($peutPublier ?? false)
                            <div class="dropdown-wrapper">
                                <button type="button"
                                        class="btn btn-ghost btn-sm btn-icon"
                                        onclick="toggleMenu(event, 'menu-{{ $bien->id }}')"
                                        aria-label="Plus d'actions">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>

                                <div class="dropdown-menu" id="menu-{{ $bien->id }}">
                                    {{-- Activer / Désactiver --}}
                                    @if($isDispo)
                                        <form action="{{ route('agence.biens.desactiver', $bien->slug) }}"
                                              method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-menu__item"
                                                    onclick="return confirm('Désactiver ce bien ? Il ne sera plus visible par les particuliers.')">
                                                <i class="fa-solid fa-eye-slash"></i>
                                                Désactiver
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('agence.biens.activer', $bien->slug) }}"
                                              method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-menu__item dropdown-menu__item--success"
                                                    onclick="return confirm('Activer ce bien ? Il sera visible par les particuliers.')">
                                                <i class="fa-solid fa-eye"></i>
                                                Activer
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Vedette --}}
                                    @if(!$isVedette)
                                        <a href="{{ route('agence.biens.vedette.demander', $bien->slug) }}"
                                           class="dropdown-menu__item dropdown-menu__item--gold">
                                            <i class="fa-solid fa-star"></i>
                                            Demander vedette
                                        </a>
                                    @else
                                        <div class="dropdown-menu__item dropdown-menu__item--muted">
                                            <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                                            Déjà en vedette
                                        </div>
                                    @endif

                                    <div class="dropdown-menu__sep"></div>

                                    {{-- Supprimer --}}
                                    <form action="{{ route('agence.biens.destroy', $bien->slug) }}"
                                          method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-menu__item dropdown-menu__item--danger"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bien ? Cette action est irréversible.')">
                                            <i class="fa-solid fa-trash-can"></i>
                                            Supprimer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <button type="button" class="btn btn-ghost btn-sm btn-icon is-disabled"
                                    title="Abonnement Pro requis" disabled>
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                        @endif
                    </footer>
                </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="pagination-wrapper">
            {{ $biens->appends(request()->query())->links() }}
        </div>

    @else

        {{-- ═══════════════════════════════════════════
             EMPTY STATE
        ═══════════════════════════════════════════ --}}
        <div class="empty-state">
            <div class="empty-state__icon">
                <i class="fa-regular fa-building"></i>
            </div>
            <h3>Aucun bien publié</h3>
            <p>Publiez votre premier bien pour commencer à recevoir des demandes de clients.</p>
            @if($peutPublier ?? false)
                <a href="{{ route('agence.biens.create') }}" class="btn btn-rust">
                    <i class="fa-solid fa-plus"></i> Publier un bien
                </a>
            @endif
        </div>
    @endif
</div>

{{-- Overlay pour fermer les menus --}}
<div id="menuOverlay" class="menu-overlay" onclick="closeAllMenus()"></div>
@endsection


@push('scripts')
<script>
    /* ═══════════════════════════════════════════
       MENUS CONTEXTUELS
    ═══════════════════════════════════════════ */
    function toggleMenu(event, menuId) {
        event.stopPropagation();
        const menu = document.getElementById(menuId);
        const overlay = document.getElementById('menuOverlay');
        const isOpen = menu.classList.contains('is-open');

        closeAllMenus();

        if (!isOpen) {
            menu.classList.add('is-open');
            overlay.classList.add('is-open');
        }
    }

    function closeAllMenus() {
        document.querySelectorAll('.dropdown-menu.is-open').forEach(m => m.classList.remove('is-open'));
        document.getElementById('menuOverlay')?.classList.remove('is-open');
    }

    // Fermer avec Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAllMenus();
    });
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE MES BIENS — Agence
   ═══════════════════════════════════════════════════════════ */

.biens-page {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #F5A623;
    --c-gold-bg:    #FFF8E1;
    --surface:      #F7F9FC;
    --radius:       14px;
}

/* ═══ HEADER ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
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
.page-header__left strong {
    color: var(--text);
    font-weight: 700;
}

/* ═══ FILTRES ═══ */
.filter-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 18px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: thin;
}
.filter-tabs::-webkit-scrollbar { height: 4px; }
.filter-tabs::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 999px;
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
}
.filter-tab:hover {
    border-color: var(--rust);
    transform: translateY(-1px);
}
.filter-tab i { font-size: 12px; opacity: .85; }
.filter-tab.is-active {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(180, 83, 42, .22);
}
.filter-tab.is-active i { color: #fff; opacity: 1; }

.filter-tab--gold.is-active {
    background: var(--c-gold);
    border-color: var(--c-gold);
    box-shadow: 0 4px 12px rgba(245, 166, 35, .3);
}

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
    background: rgba(255, 255, 255, .28);
    color: #fff;
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
.alert-warning { background: var(--c-warning-bg); color: var(--c-warning); border-left-color: var(--c-gold); }

/* ═══ GRILLE ═══ */
.biens-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

/* ═══ CARTE BIEN ═══ */
.bien-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s;
    position: relative;
}
.bien-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(0, 0, 0, .07);
    border-color: #d8d8d8;
}
.bien-card.is-vedette {
    border-color: var(--c-gold);
    box-shadow: 0 0 0 1px rgba(245, 166, 35, .2);
}
.bien-card.is-vedette:hover {
    box-shadow: 0 10px 28px rgba(245, 166, 35, .15);
}

/* ═══ IMAGE ═══ */
.bien-card__media {
    position: relative;
    aspect-ratio: 16 / 10;
    background: var(--surface);
    overflow: hidden;
}
.bien-card__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s ease;
}
.bien-card:hover .bien-card__media img {
    transform: scale(1.03);
}
.bien-card__media-placeholder {
    width: 100%; height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    font-size: 40px;
    opacity: .3;
}

/* Badges sur l'image */
.media-badge {
    position: absolute;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
    backdrop-filter: blur(4px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
    z-index: 2;
}

/* Vedette en haut à gauche */
.media-badge--vedette {
    top: 10px;
    left: 10px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
}
.media-badge--vedette i { font-size: 9px; }

/* Statut en haut à droite */
.media-badge--statut {
    top: 10px;
    right: 10px;
    background: rgba(255, 255, 255, .95);
}
.media-badge--statut i { font-size: 5px; }
.media-badge--statut.is-dispo {
    color: var(--c-success);
}
.media-badge--statut.is-indispo {
    color: var(--muted);
}

/* Type en bas à gauche */
.media-badge--type {
    bottom: 10px;
    left: 10px;
    background: rgba(0, 0, 0, .75);
    color: #fff;
}

/* Compteur en bas à droite */
.media-badge--count {
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, .7);
    color: #fff;
    gap: 8px;
    padding: 4px 10px;
}
.media-badge--count i { font-size: 10px; }

/* ═══ CORPS ═══ */
.bien-card__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 14px 16px;
}

/* Titre + Prix */
.bien-card__headline {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
}
.bien-card__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.3;
    min-width: 0;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}
.bien-card__price {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
    flex-shrink: 0;
}
.bien-card__price small {
    font-size: 10px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}

/* Localisation */
.bien-card__location {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--muted);
}
.bien-card__location i {
    color: var(--rust);
    font-size: 11px;
    opacity: .8;
}

/* Méta pills */
.bien-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 10px;
    border-radius: 999px;
    background: var(--surface);
    border: 1px solid var(--border);
    font-size: 11px;
    color: var(--text-soft);
    font-weight: 500;
}
.meta-pill i {
    color: var(--rust);
    font-size: 10px;
    opacity: .8;
}

/* Vues */
.bien-card__views {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: var(--c-info-bg);
    color: var(--c-info);
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 600;
    align-self: flex-start;
    margin-top: auto;
}
.bien-card__views i { font-size: 11px; }
.bien-card__views strong {
    font-family: var(--display);
    font-size: 12px;
    font-weight: 800;
}

/* ═══ ACTIONS ═══ */
.bien-card__actions {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: #FAFBFC;
}

.bien-card__actions .btn-rust { margin-left: auto; }

/* ═══ MENU CONTEXTUEL ═══ */
.dropdown-wrapper {
    position: relative;
    margin-left: auto;
}

.dropdown-menu {
    position: absolute;
    bottom: calc(100% + 6px);
    right: 0;
    min-width: 200px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, .12);
    padding: 6px;
    z-index: 100;
    opacity: 0;
    visibility: hidden;
    transform: translateY(4px);
    transition: all .15s ease;
}
.dropdown-menu.is-open {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-menu__item {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 9px 12px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: var(--text);
    font-size: 13px;
    font-weight: 500;
    font-family: inherit;
    text-decoration: none;
    cursor: pointer;
    transition: background .15s;
    text-align: left;
}
.dropdown-menu__item:hover {
    background: var(--surface);
}
.dropdown-menu__item i {
    width: 16px;
    text-align: center;
    font-size: 13px;
    color: var(--muted);
    flex-shrink: 0;
}
.dropdown-menu__item:hover i { color: var(--rust); }

.dropdown-menu__item--success i { color: var(--c-success); }
.dropdown-menu__item--success:hover { background: var(--c-success-bg); }

.dropdown-menu__item--gold i { color: var(--c-gold); }
.dropdown-menu__item--gold:hover { background: var(--c-gold-bg); }

.dropdown-menu__item--danger { color: var(--c-danger); }
.dropdown-menu__item--danger i { color: var(--c-danger); }
.dropdown-menu__item--danger:hover { background: var(--c-danger-bg); }

.dropdown-menu__item--muted {
    color: var(--muted);
    cursor: default;
    font-style: italic;
}
.dropdown-menu__item--muted:hover { background: transparent; }

.dropdown-menu__sep {
    height: 1px;
    background: var(--border);
    margin: 4px 8px;
}

/* Overlay pour fermer */
.menu-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 50;
    background: transparent;
}
.menu-overlay.is-open { display: block; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
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
.btn-sm { padding: 7px 13px; font-size: 12px; }

.btn-icon {
    padding: 7px 10px;
}

.btn.is-disabled,
.btn:disabled {
    opacity: .4;
    cursor: not-allowed;
}

/* ═══ EMPTY STATE ═══ */
.empty-state {
    text-align: center;
    padding: 64px 20px;
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
    background: var(--c-warning-bg);
    border-radius: 50%;
    color: var(--rust);
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
    margin: 0 auto 18px;
    max-width: 420px;
}

/* ═══ PAGINATION ═══ */
.pagination-wrapper {
    margin-top: 24px;
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
    .page-header {
        flex-direction: column;
        align-items: stretch;
    }
    .page-header .btn { justify-content: center; }

    .filter-tab { font-size: 12px; padding: 8px 13px; }
    .filter-tab__count { font-size: 10.5px; }

    .biens-grid { grid-template-columns: 1fr; gap: 12px; }

    .bien-card__actions {
        flex-wrap: wrap;
    }
    .bien-card__actions .btn:not(.btn-icon) {
        flex: 1;
        min-width: 0;
    }
    .bien-card__actions .btn-rust { margin-left: 0; }
}

@media (max-width: 480px) {
    .page-header__left h2 { font-size: 19px; }

    .bien-card__headline { flex-direction: column; align-items: flex-start; gap: 4px; }
    .bien-card__title { font-size: 14px; }
    .bien-card__price { font-size: 14px; }

    .bien-card__body { padding: 12px 14px; }
    .bien-card__actions { padding: 10px 14px; }

    .media-badge { font-size: 9.5px; padding: 3px 8px; }
    .media-badge--vedette i { font-size: 8px; }

    .dropdown-menu { min-width: 180px; }
    .dropdown-menu__item { font-size: 12.5px; padding: 8px 10px; }

    .btn { font-size: 11.5px; padding: 7px 12px; }
    .btn-sm { font-size: 11px; padding: 6px 11px; }
}
</style>
@endpush