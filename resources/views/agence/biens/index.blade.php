@extends('layouts.dashboard-agence')

@section('title', 'Mes biens — DoyaImmo')
@section('page_title', 'Mes biens')
@section('page_sub', 'Gérez vos biens immobiliers')

@section('content')
<div class="view active biens-page">

    @php
        $currentStatut = request('statut', '');
        $isVedetteFilter = request('vedette') === 'true';

        $countAll = $biens->total();
        $countDispo = $biens->where('statut', true)->count();
        $countIndispo = $biens->where('statut', false)->count();
        $countVedette = $biens->where('est_vedette', true)->count();
    @endphp

    {{-- ═══ HEADER DESKTOP ═══ --}}
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

    {{-- ═══ STATS ═══ --}}
    @if($biens->total() > 0)
        <div class="biens-stats">
            <div class="biens-stat">
                <span class="biens-stat__value">{{ $countAll }}</span>
                <span class="biens-stat__label">Total</span>
            </div>
            <div class="biens-stat">
                <span class="biens-stat__value">{{ $countDispo }}</span>
                <span class="biens-stat__label">Disponibles</span>
            </div>
            <div class="biens-stat">
                <span class="biens-stat__value">{{ $countIndispo }}</span>
                <span class="biens-stat__label">Indisponibles</span>
            </div>
            <div class="biens-stat">
                <span class="biens-stat__value">{{ $countVedette }}</span>
                <span class="biens-stat__label">Vedettes</span>
            </div>
        </div>
    @endif

    {{-- ═══ FILTRES PILLS ═══ --}}
    @if($biens->total() > 0)
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
               class="filter-tab {{ $isVedetteFilter ? 'is-active' : '' }}">
                <i class="fa-solid fa-star"></i>
                <span>Vedettes</span>
                <span class="filter-tab__count">{{ $countVedette }}</span>
            </a>
        </nav>
    @endif

    {{-- ═══ GRILLE DES BIENS ═══ --}}
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

                <article class="bien-card">

                    {{-- Image --}}
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

                        @if($isVedette)
                            <span class="media-badge media-badge--vedette">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif

                        <span class="media-badge media-badge--statut {{ $isDispo ? 'is-dispo' : 'is-indispo' }}">
                            {{ $isDispo ? 'Disponible' : 'Indisponible' }}
                        </span>

                        <span class="media-badge media-badge--type">
                            {{ $bien->type_bien->label() }}
                        </span>

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

                    {{-- Contenu --}}
                    <div class="bien-card__body">

                        <div class="bien-card__headline">
                            <h3 class="bien-card__title">{{ $bien->titre }}</h3>
                            <div class="bien-card__price">
                                {{ number_format($bien->prix, 0, ',', ' ') }}
                                <small>FCFA</small>
                            </div>
                        </div>

                        <div class="bien-card__location">
                            <i class="fa-solid fa-location-dot"></i>
                            {{ $bien->quartier }}
                        </div>

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

                        <div class="bien-card__views">
                            <i class="fa-regular fa-eye"></i>
                            <strong>{{ $bien->vues ?? 0 }}</strong>
                            vue{{ ($bien->vues ?? 0) > 1 ? 's' : '' }}
                        </div>
                    </div>

                    {{-- Actions --}}
                    <footer class="bien-card__actions">

                        @if($peutPublier ?? false)
                            <a href="{{ route('agence.biens.edit', $bien->slug) }}"
                               class="btn btn-ghost">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </a>
                        @else
                            <button type="button" class="btn btn-ghost is-disabled"
                                    title="Abonnement Pro requis" disabled>
                                <i class="fa-solid fa-pen"></i> Modifier
                            </button>
                        @endif

                        <a href="{{ route('agence.biens.show', $bien->slug) }}"
                           class="btn btn-rust">
                            <i class="fa-solid fa-eye"></i> Voir
                        </a>

                        @if($peutPublier ?? false)
                            <div class="dropdown-wrapper">
                                <button type="button"
                                        class="btn btn-ghost btn-icon"
                                        data-menu-trigger
                                        data-menu-id="menu-{{ $bien->id }}"
                                        onclick="toggleMenu(event, 'menu-{{ $bien->id }}')"
                                        aria-label="Plus d'actions">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>

                                <div class="dropdown-menu" id="menu-{{ $bien->id }}" data-menu-content>
                                    @if($isDispo)
                                        <form action="{{ route('agence.biens.desactiver', $bien->slug) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-menu__item"
                                                    onclick="return confirm('Désactiver ce bien ? Il ne sera plus visible par les particuliers.')">
                                                <i class="fa-solid fa-eye-slash"></i>
                                                Désactiver
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('agence.biens.activer', $bien->slug) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-menu__item"
                                                    onclick="return confirm('Activer ce bien ? Il sera visible par les particuliers.')">
                                                <i class="fa-solid fa-eye"></i>
                                                Activer
                                            </button>
                                        </form>
                                    @endif

                                    @if(!$isVedette)
                                        <a href="{{ route('agence.biens.vedette.demander', $bien->slug) }}"
                                           class="dropdown-menu__item">
                                            <i class="fa-solid fa-star"></i>
                                            Demander vedette
                                        </a>
                                    @else
                                        <div class="dropdown-menu__item dropdown-menu__item--muted">
                                            <i class="fa-solid fa-star"></i>
                                            Déjà en vedette
                                        </div>
                                    @endif

                                    <div class="dropdown-menu__sep"></div>

                                    <form action="{{ route('agence.biens.destroy', $bien->slug) }}" method="POST">
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
                            <button type="button" class="btn btn-ghost btn-icon is-disabled"
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
        {{-- EMPTY STATE --}}
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

{{-- ✅ FAB Publier un bien — mobile uniquement --}}
@if($peutPublier ?? false)
    <a href="{{ route('agence.biens.create') }}"
       class="biens-fab"
       aria-label="Publier un bien">
        <i class="fa-solid fa-plus"></i>
    </a>
@endif

{{-- Overlay pour fermer les menus --}}
<div id="menuOverlay" class="menu-overlay" onclick="closeAllMenus()"></div>
@endsection


@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════
   MENU CONTEXTUEL — v5
   ═══════════════════════════════════════════════════════════ */

let activeMenu = null;
let activeMenuPlaceholder = null;

function isMobile() {
    return window.innerWidth <= 820;
}

function toggleMenu(event, menuId) {
    if (event) {
        event.stopPropagation();
        event.preventDefault();
    }

    const menu = document.getElementById(menuId);
    if (!menu) return;

    if (activeMenu === menu) {
        closeAllMenus();
        return;
    }

    closeAllMenus();

    const trigger = document.querySelector('[data-menu-id="' + menuId + '"]');
    const card = trigger ? trigger.closest('.bien-card') : null;

    if (isMobile() && menu.parentNode !== document.body) {
        const placeholder = document.createElement('div');
        placeholder.style.display = 'none';
        menu.parentNode.insertBefore(placeholder, menu);
        document.body.appendChild(menu);
        activeMenuPlaceholder = placeholder;
    }

    if (card) card.classList.add('is-menu-open');

    requestAnimationFrame(() => {
        menu.classList.add('is-open');
    });

    document.getElementById('menuOverlay')?.classList.add('is-open');

    if (isMobile()) {
        document.body.classList.add('menu-open');
    }

    activeMenu = menu;
}

function closeAllMenus() {
    const menus = document.querySelectorAll('.dropdown-menu.is-open, body > .dropdown-menu');

    menus.forEach(menu => {
        menu.classList.remove('is-open');

        if (menu.parentNode === document.body && activeMenuPlaceholder && activeMenuPlaceholder.parentNode) {
            activeMenuPlaceholder.parentNode.insertBefore(menu, activeMenuPlaceholder);
            activeMenuPlaceholder.remove();
        }
    });

    document.querySelectorAll('.bien-card.is-menu-open').forEach(c => c.classList.remove('is-menu-open'));
    document.getElementById('menuOverlay')?.classList.remove('is-open');
    document.body.classList.remove('menu-open');

    activeMenu = null;
    activeMenuPlaceholder = null;
}

document.addEventListener('DOMContentLoaded', () => {
    closeAllMenus();

    const style = document.createElement('style');
    style.textContent = `
        body.menu-open {
            overflow: hidden !important;
            position: fixed !important;
            width: 100% !important;
        }

        body > .dropdown-menu {
            display: block !important;
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            top: auto !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 100% !important;
            margin: 0 !important;
            border-radius: 16px 16px 0 0 !important;
            padding: 8px 0 calc(12px + env(safe-area-inset-bottom, 0px)) !important;
            box-shadow: 0 -8px 24px rgba(0, 0, 0, .15) !important;
            z-index: 99999 !important;
            border: none !important;
            background: #ffffff !important;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(100%);
            transition: transform .28s cubic-bezier(.32, .72, 0, 1),
                        opacity .2s ease,
                        visibility .28s ease;
        }
        body > .dropdown-menu.is-open {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
            pointer-events: auto !important;
        }
        body > .dropdown-menu::before {
            content: '' !important;
            display: block !important;
            width: 36px !important;
            height: 4px !important;
            background: #C7C7CC !important;
            border-radius: 999px !important;
            margin: 4px auto 8px !important;
            pointer-events: none !important;
        }
        body > .dropdown-menu .dropdown-menu__item {
            padding: 14px 20px !important;
            font-size: 15px !important;
            border-radius: 0 !important;
            font-weight: 500 !important;
        }
        body > .dropdown-menu .dropdown-menu__item i {
            width: 24px !important;
            font-size: 17px !important;
            color: #8A91A0 !important;
        }
        body > .dropdown-menu .dropdown-menu__item:active {
            background: #F5F5F7 !important;
        }
        body > .dropdown-menu .dropdown-menu__item--danger,
        body > .dropdown-menu .dropdown-menu__item--danger i {
            color: #C62828 !important;
        }
        body > .dropdown-menu .dropdown-menu__sep {
            margin: 6px 20px !important;
            background: rgba(60, 60, 67, 0.12) !important;
        }
    `;
    document.head.appendChild(style);
});

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeAllMenus();
});

document.addEventListener('click', e => {
    const item = e.target.closest('.dropdown-menu__item');
    if (!item) return;

    if (item.closest('form') && item.tagName === 'BUTTON' && item.type === 'submit') {
        closeAllMenus();
        return;
    }

    if (!item.classList.contains('dropdown-menu__item--muted')) {
        setTimeout(closeAllMenus, 100);
    }
});

window.addEventListener('resize', () => {
    if (activeMenu) closeAllMenus();
});
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MES BIENS — Design épuré
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
    --surface:      #F7F9FC;
    --radius:       14px;

    --ios-card: #FFFFFF;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
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
    font-size: 20px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.2;
    letter-spacing: -.3px;
}
.page-header__left p {
    font-size: 13px;
    color: var(--muted);
    margin: 4px 0 0;
}
.page-header__left strong {
    color: var(--text);
    font-weight: 600;
}

/* ═══ STATS ═══ */
.biens-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}
.biens-stat {
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
.biens-stat__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.5px;
    line-height: 1;
}
.biens-stat__label {
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
    margin-bottom: 18px;
    overflow-x: auto;
    padding-bottom: 4px;
    scrollbar-width: thin;
}
.filter-tabs::-webkit-scrollbar { height: 4px; }
.filter-tabs::-webkit-scrollbar-thumb { background: var(--border); border-radius: 999px; }

.filter-tab {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 14px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-soft);
    background: #fff;
    border: 1px solid var(--border);
    text-decoration: none;
    transition: border-color .15s, background-color .15s, color .15s;
    white-space: nowrap;
    flex-shrink: 0;
}
.filter-tab:hover { border-color: #d8d8d8; }
.filter-tab i { font-size: 11px; color: var(--muted); }
.filter-tab.is-active {
    background: #F5F5F7;
    color: var(--ink);
    border-color: #d8d8d8;
    font-weight: 600;
}
.filter-tab.is-active i { color: var(--rust); }

.filter-tab__count {
    background: #F5F5F7;
    padding: 1px 7px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    min-width: 20px;
    text-align: center;
    color: var(--text-soft);
}
.filter-tab.is-active .filter-tab__count {
    background: #fff;
    color: var(--ink);
}

/* ═══ GRILLE ═══ */
.biens-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 16px;
}

/* ═══ CARTE ═══ */
.bien-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: visible;
    transition: border-color .2s;
    position: relative;
}
.bien-card:hover { border-color: #d8d8d8; }

.bien-card__media {
    position: relative;
    aspect-ratio: 16 / 10;
    background: var(--surface);
    overflow: hidden;
    border-radius: var(--radius) var(--radius) 0 0;
}
.bien-card__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.bien-card__media-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted);
    font-size: 36px;
    opacity: .35;
}

.media-badge {
    position: absolute;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 600;
    white-space: nowrap;
    z-index: 2;
}
.media-badge--vedette {
    top: 10px; left: 10px;
    background: #fff;
    color: #B8860B;
    border: 1px solid #F0E0B0;
}
.media-badge--vedette i { font-size: 9px; }

.media-badge--statut {
    top: 10px; right: 10px;
    background: rgba(255, 255, 255, .95);
    border: 1px solid var(--border);
}
.media-badge--statut.is-dispo { color: var(--c-success); }
.media-badge--statut.is-indispo { color: var(--muted); }

.media-badge--type {
    bottom: 10px; left: 10px;
    background: rgba(0, 0, 0, .7);
    color: #fff;
}
.media-badge--count {
    bottom: 10px; right: 10px;
    background: rgba(0, 0, 0, .65);
    color: #fff;
    gap: 8px;
}
.media-badge--count i { font-size: 10px; }

.bien-card__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 14px 16px;
}

.bien-card__headline {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
}
.bien-card__title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 600;
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
    font-weight: 700;
    color: var(--rust);
    white-space: nowrap;
    line-height: 1.2;
    flex-shrink: 0;
}
.bien-card__price small {
    font-size: 10px;
    font-weight: 500;
    opacity: .7;
    margin-left: 2px;
}

.bien-card__location {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    color: var(--muted);
}
.bien-card__location i {
    color: var(--muted);
    font-size: 11px;
}

.bien-card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.meta-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 999px;
    background: #F5F5F7;
    font-size: 11px;
    color: var(--text-soft);
    font-weight: 500;
}
.meta-pill i { color: var(--muted); font-size: 10px; }

.bien-card__views {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    background: #F5F5F7;
    color: var(--text-soft);
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 500;
    align-self: flex-start;
    margin-top: auto;
}
.bien-card__views i { font-size: 11px; color: var(--muted); }
.bien-card__views strong {
    font-family: var(--display);
    font-size: 12px;
    font-weight: 700;
    color: var(--text);
}

.bien-card__actions {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: #FAFBFC;
    border-radius: 0 0 var(--radius) var(--radius);
}

/* ═══ MENU CONTEXTUEL ═══ */
.bien-card.is-menu-open,
.bien-card:has(.dropdown-menu.is-open) {
    z-index: 9999 !important;
    position: relative !important;
}

.dropdown-wrapper {
    position: relative;
    margin-left: auto;
}

.dropdown-menu {
    display: none;
    position: absolute;
    bottom: calc(100% + 8px);
    right: 0;
    min-width: 210px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0, 0, 0, .08);
    padding: 6px;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .15s ease, visibility .15s ease;
}

.dropdown-menu.is-open {
    display: block;
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

.dropdown-menu::before {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    right: 0;
    height: 10px;
    background: transparent;
}

.dropdown-menu__item {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 10px 12px;
    border-radius: 8px;
    border: none;
    background: transparent;
    color: var(--text);
    font-size: 13px;
    font-weight: 500;
    font-family: inherit;
    text-decoration: none;
    cursor: pointer;
    transition: background-color .15s ease;
    text-align: left;
}
.dropdown-menu__item:hover { background: #F5F5F7; }
.dropdown-menu__item i {
    width: 16px;
    text-align: center;
    font-size: 13px;
    color: var(--muted);
    flex-shrink: 0;
}

.dropdown-menu__item--danger { color: var(--c-danger); }
.dropdown-menu__item--danger i { color: var(--c-danger); }
.dropdown-menu__item--danger:hover { background: var(--c-danger-bg); }

.dropdown-menu__item--muted {
    color: var(--muted);
    cursor: default;
    font-style: italic;
}
.dropdown-menu__item--muted:hover { background: transparent; }
.dropdown-menu__item--muted i { color: #B8860B; }

.dropdown-menu__sep {
    height: 1px;
    background: var(--border);
    margin: 4px 8px;
}

.menu-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9998;
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
    transition: background-color .15s, border-color .15s, color .15s;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523;
    border-color: #9A4523;
    color: #fff;
}
.btn-ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: #F5F5F7;
    border-color: #d8d8d8;
    color: var(--ink);
}
.btn-icon { padding: 7px 10px; }
.btn.is-disabled,
.btn:disabled {
    opacity: .4;
    cursor: not-allowed;
}

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
    color: var(--text);
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
    min-width: 34px;
    text-align: center;
    background: #fff;
}
.pagination-wrapper a:hover { background: #F5F5F7; }
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] { background: var(--rust); color: #fff; border-color: var(--rust); }
.pagination-wrapper .disabled span { opacity: .45; cursor: not-allowed; }

/* ═══ FAB (mobile only) ═══ */
.biens-fab { display: none; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .biens-page { background: transparent; padding-top: 4px; }

    .page-header { display: none; }

    /* STATS INLINE */
    .biens-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 14px 6px;
        margin-bottom: 12px;
        grid-template-columns: none;
    }
    .biens-stat {
        flex: 1;
        padding: 0 4px;
        background: transparent;
        border: none;
        border-radius: 0;
        gap: 3px;
    }
    .biens-stat:not(:last-child)::after {
        content: '';
        position: absolute;
        right: 0; top: 20%;
        height: 60%; width: 0.5px;
        background: var(--ios-separator);
    }
    .biens-stat__value { font-size: 22px; }
    .biens-stat__label {
        font-size: 11px;
        color: var(--muted);
        text-transform: none;
        letter-spacing: 0;
    }

    /* FILTRES */
    .filter-tabs {
        gap: 8px;
        margin: 0 -16px 14px;
        padding: 0 16px 2px;
        scrollbar-width: none;
    }
    .filter-tabs::-webkit-scrollbar { display: none; }

    .filter-tab {
        padding: 8px 15px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-size: 13px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .filter-tab.is-active {
        background: var(--rust);
        color: #fff;
    }
    .filter-tab__count {
        background: #EFEFF4;
        color: var(--text-soft);
    }
    .filter-tab.is-active .filter-tab__count {
        background: rgba(255,255,255,.25);
        color: #fff;
    }

    /* GRILLE */
    .biens-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    /* CARTES iOS */
    .bien-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .bien-card__media {
        aspect-ratio: 16/9;
        border-radius: var(--ios-radius-lg) var(--ios-radius-lg) 0 0;
    }

    .media-badge {
        font-size: 10.5px;
        padding: 3px 9px;
    }
    .media-badge--vedette { top: 12px; left: 12px; }
    .media-badge--statut { top: 12px; right: 12px; }
    .media-badge--type { bottom: 12px; left: 12px; }
    .media-badge--count { bottom: 12px; right: 12px; }

    .bien-card__body { padding: 14px 16px; }

    .bien-card__title { font-size: 15px; }
    .bien-card__price { font-size: 17px; }

    .bien-card__location { font-size: 13px; }

    .meta-pill {
        background: #F5F5F7;
        font-size: 12px;
        padding: 4px 10px;
    }

    .bien-card__views {
        background: #F5F5F7;
        font-size: 12.5px;
    }

    .bien-card__actions {
        padding: 12px 16px;
        background: transparent;
        border-top: 0.5px solid var(--ios-separator);
        gap: 8px;
        border-radius: 0 0 var(--ios-radius-lg) var(--ios-radius-lg);
    }
    .bien-card__actions .btn {
        padding: 11px 14px;
        font-size: 14px;
        border-radius: 12px;
        flex: 1;
        justify-content: center;
    }
    .bien-card__actions .btn-ghost {
        background: #F5F5F7;
        border: none;
        color: var(--text-soft);
    }
    .bien-card__actions .btn-rust {
        background: var(--rust);
        border: none;
    }
    .bien-card__actions .btn-icon {
        flex: 0 0 auto;
        padding: 11px 14px;
        background: #F5F5F7;
        border: none;
        color: var(--text-soft);
    }

    .menu-overlay {
        background: rgba(0, 0, 0, .4);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        z-index: 99998;
    }

    .empty-state {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 48px 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
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

    /* ✅ FAB — Publier un bien */
    .biens-fab {
        display: flex;
        position: fixed;
        right: 16px;
        bottom: calc(24px + env(safe-area-inset-bottom, 0px));
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--rust);
        color: #fff;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .18);
        z-index: 90;
        text-decoration: none;
        transition: background-color .15s;
        -webkit-tap-highlight-color: transparent;
    }
    .biens-fab:hover { background: #9A4523; color: #fff; }
    .biens-fab:active { background: #8A3D1F; }
}

@media (max-width: 480px) {
    .biens-stat__value { font-size: 20px; }
    .biens-stat__label { font-size: 10.5px; }

    .bien-card__title { font-size: 14.5px; }
    .bien-card__price { font-size: 16px; }
    .bien-card__body { padding: 12px 14px; }
    .bien-card__actions { padding: 10px 14px; }

    .bien-card__actions .btn {
        padding: 10px 12px;
        font-size: 13px;
    }

    .biens-fab {
        right: 14px;
        width: 52px;
        height: 52px;
        font-size: 20px;
    }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush