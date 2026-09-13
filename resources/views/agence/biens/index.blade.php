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
            <div class="biens-stat {{ $countVedette > 0 ? 'is-highlight' : '' }}">
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
               class="filter-tab filter-tab--gold {{ $isVedetteFilter ? 'is-active' : '' }}">
                <i class="fa-solid fa-star"></i>
                <span>Vedettes</span>
                <span class="filter-tab__count">{{ $countVedette }}</span>
            </a>
        </nav>
    @endif

    {{-- ═══ ALERTS ═══ --}}
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

                <article class="bien-card {{ $isVedette ? 'is-vedette' : '' }}">

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
                            <i class="fa-solid fa-circle"></i>
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
                                        onclick="toggleMenu(event, 'menu-{{ $bien->id }}')"
                                        aria-label="Plus d'actions">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>

                                <div class="dropdown-menu" id="menu-{{ $bien->id }}">
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
                                            <button type="submit" class="dropdown-menu__item dropdown-menu__item--success"
                                                    onclick="return confirm('Activer ce bien ? Il sera visible par les particuliers.')">
                                                <i class="fa-solid fa-eye"></i>
                                                Activer
                                            </button>
                                        </form>
                                    @endif

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

{{-- Overlay pour fermer les menus --}}
<div id="menuOverlay" class="menu-overlay" onclick="closeAllMenus()"></div>
@endsection


@push('scripts')
<script>
    /* ═══════════════════════════════════════════════════════════
       MENU CONTEXTUEL — STABILISÉ v3
       → z-index maximal
       → empêche tout chevauchement avec autres cartes/boutons
       → bloque le scroll mobile pendant l'ouverture
    ═══════════════════════════════════════════════════════════ */
    function toggleMenu(event, menuId) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }

        const menu = document.getElementById(menuId);
        const overlay = document.getElementById('menuOverlay');
        if (!menu) return;

        const isOpen = menu.classList.contains('is-open');

        closeAllMenus();

        if (!isOpen) {
            menu.classList.add('is-open');
            overlay?.classList.add('is-open');

            const card = menu.closest('.bien-card');
            if (card) card.classList.add('is-menu-open');

            // Sécurité mobile : bloque le scroll de la page sous la bottom sheet
            if (window.innerWidth <= 820) {
                document.body.style.overflow = 'hidden';
            }
        }
    }

    function closeAllMenus() {
        document.querySelectorAll('.dropdown-menu.is-open').forEach(m => m.classList.remove('is-open'));
        document.querySelectorAll('.bien-card.is-menu-open').forEach(c => c.classList.remove('is-menu-open'));
        document.getElementById('menuOverlay')?.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    // Assure la fermeture stricte de tous les menus au chargement de la page
    document.addEventListener('DOMContentLoaded', closeAllMenus);

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAllMenus();
    });

    // Clic sur un item → fermeture après action (sauf submit de form)
    document.addEventListener('click', e => {
        const item = e.target.closest('.dropdown-menu__item');
        if (!item) return;

        // Ne ferme pas si c'est un bouton submit dans un form
        if (item.closest('form') && item.tagName === 'BUTTON' && item.type === 'submit') {
            return;
        }

        // Ne ferme pas si c'est un item "muté" (Déjà en vedette)
        if (!item.classList.contains('dropdown-menu__item--muted')) {
            setTimeout(closeAllMenus, 100);
        }
    });
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MES BIENS — iOS NATIVE
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

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ HEADER DESKTOP ═══ */
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
    transition: transform .15s, box-shadow .15s;
}
.biens-stat:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0,0,0,.05);
}
.biens-stat.is-highlight {
    border-color: var(--c-gold);
    background: linear-gradient(135deg, #FFFBF0, #fff);
}
.biens-stat__value {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 800;
    color: var(--ink);
    letter-spacing: -.5px;
    line-height: 1;
}
.biens-stat.is-highlight .biens-stat__value { color: #C77800; }
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
.filter-tab:hover { border-color: var(--rust); transform: translateY(-1px); }
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

/* ═══ CARTE ═══ */
.bien-card {
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: visible;                 /* ← indispensable pour le dropdown */
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
    transition: transform .3s ease;
}
.bien-card:hover .bien-card__media img { transform: scale(1.03); }
.bien-card__media-placeholder {
    width: 100%; height: 100%;
    display: flex; align-items: center; justify-content: center;
    color: var(--muted);
    font-size: 40px;
    opacity: .3;
}

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
.media-badge--vedette {
    top: 10px; left: 10px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
}
.media-badge--vedette i { font-size: 9px; }

.media-badge--statut {
    top: 10px; right: 10px;
    background: rgba(255, 255, 255, .95);
}
.media-badge--statut i { font-size: 5px; }
.media-badge--statut.is-dispo { color: var(--c-success); }
.media-badge--statut.is-indispo { color: var(--muted); }

.media-badge--type {
    bottom: 10px; left: 10px;
    background: rgba(0, 0, 0, .75);
    color: #fff;
}
.media-badge--count {
    bottom: 10px; right: 10px;
    background: rgba(0, 0, 0, .7);
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
.meta-pill i { color: var(--rust); font-size: 10px; opacity: .8; }

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

.bien-card__actions {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: #FAFBFC;
    border-radius: 0 0 var(--radius) var(--radius);
}

/* ═══════════════════════════════════════════════════════════
   DROPDOWN MENU — STABILISÉ v3
   → z-index maximal + isolation stricte
   → items en position relative pour passer au-dessus de tout
   ═══════════════════════════════════════════════════════════ */
.bien-card.is-menu-open,
.bien-card:has(.dropdown-menu.is-open) {
    z-index: 9999 !important;
    position: relative !important;
}

.dropdown-wrapper {
    position: relative;
    margin-left: auto;
    isolation: isolate;
}

.dropdown-wrapper:has(.dropdown-menu.is-open),
.dropdown-wrapper.is-open {
    z-index: 10000 !important;
}

.dropdown-menu {
    display: none;
    position: absolute;
    bottom: calc(100% + 8px);
    right: 0;
    min-width: 220px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 10px 28px rgba(0, 0, 0, .12);
    padding: 6px;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition: opacity .15s ease, visibility .15s ease;
    isolation: isolate;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
}

.dropdown-menu.is-open {
    display: block;
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
}

/* Pont invisible bouton → menu (10px) */
.dropdown-menu::before {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    right: 0;
    height: 10px;
    background: transparent;
    pointer-events: auto;
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
    transition: background-color .15s ease, color .15s ease;
    text-align: left;
    -webkit-user-select: none;
    user-select: none;
    position: relative;
    z-index: 1;
}
.dropdown-menu__item:hover { background: var(--surface); }
.dropdown-menu__item:active { background: #EEF1F5; }
.dropdown-menu__item i {
    width: 16px;
    text-align: center;
    font-size: 13px;
    color: var(--muted);
    flex-shrink: 0;
    transition: color .15s ease;
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

/* L'overlay doit rester SOUS le menu mais AU-DESSUS de tout le reste */
.menu-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9998;
    background: transparent;
}
.menu-overlay.is-open { display: block; }

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
.btn-icon { padding: 7px 10px; }
.btn.is-disabled,
.btn:disabled {
    opacity: .4;
    cursor: not-allowed;
}

.empty-state {
    text-align: center;
    padding: 64px 20px;
    background: #fff;
    border-radius: var(--radius);
    border: 1px dashed var(--border);
}
.empty-state__icon {
    width: 76px; height: 76px;
    margin: 0 auto 18px;
    display: flex; align-items: center; justify-content: center;
    background: var(--c-warning-bg);
    border-radius: 50%;
    color: var(--rust);
    font-size: 30px;
}
.empty-state h3 {
    font-family: var(--display);
    font-size: 17px; font-weight: 700;
    margin: 0 0 6px;
    color: var(--text);
}
.empty-state p {
    color: var(--muted);
    font-size: 13.5px;
    margin: 0 auto 18px;
    max-width: 420px;
}

.pagination-wrapper { margin-top: 24px; display: flex; justify-content: center; }
.pagination-wrapper .pagination,
.pagination-wrapper nav {
    display: flex; gap: 5px; justify-content: center;
    list-style: none; padding: 0; margin: 0; flex-wrap: wrap;
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
.pagination-wrapper a:hover { background: var(--border); border-color: var(--rust); color: var(--rust); }
.pagination-wrapper .active span,
.pagination-wrapper [aria-current="page"] span,
.pagination-wrapper [aria-current="page"] { background: var(--rust); color: #fff; border-color: var(--rust); }
.pagination-wrapper .disabled span { opacity: .45; cursor: not-allowed; }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .biens-page { background: transparent; padding-top: 4px; }

    .page-header { display: none; }

    .biens-stats {
        display: flex;
        gap: 0;
        background: var(--ios-card);
        border-radius: var(--ios-radius-lg);
        padding: 16px 6px;
        margin-bottom: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        grid-template-columns: none;
    }
    .biens-stat {
        flex: 1;
        padding: 0 4px;
        background: transparent;
        border: none;
        border-radius: 0;
        gap: 2px;
    }
    .biens-stat:hover { transform: none; box-shadow: none; }
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
        color: var(--ios-label-3);
        text-transform: none;
        letter-spacing: 0;
    }

    .filter-tabs {
        gap: 8px;
        margin: 0 -16px 14px;
        padding: 0 16px 2px;
        overflow-x: auto;
        scroll-snap-type: x mandatory;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    .filter-tabs::-webkit-scrollbar { display: none; }

    .filter-tab {
        scroll-snap-align: start;
        padding: 9px 16px;
        background: var(--ios-card);
        border: none;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: all .15s ease;
        -webkit-tap-highlight-color: transparent;
    }
    .filter-tab:active { transform: scale(.95); }
    .filter-tab.is-active {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        color: #fff;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .filter-tab--gold.is-active {
        background: linear-gradient(135deg, var(--c-gold), #E8901A);
        box-shadow: 0 4px 12px rgba(245, 166, 35, .35);
    }
    .filter-tab.is-active .filter-tab__count {
        background: rgba(255, 255, 255, .3);
    }
    .filter-tab__count {
        background: #EFEFF4;
        color: var(--ios-label-2);
        font-size: 11px;
        padding: 1px 8px;
    }

    .biens-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .bien-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        overflow: visible;
        transition: transform .15s ease;
    }
    .bien-card:hover { transform: none; box-shadow: 0 1px 3px rgba(0,0,0,.04); border: none; }
    .bien-card:active { transform: scale(.985); }

    .bien-card__media {
        aspect-ratio: 16/9;
        border-radius: var(--ios-radius-lg) var(--ios-radius-lg) 0 0;
    }

    .bien-card.is-vedette {
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .media-badge {
        font-size: 10px;
        padding: 4px 10px;
        text-transform: none;
        letter-spacing: 0;
        font-weight: 600;
    }
    .media-badge--vedette { top: 12px; left: 12px; }
    .media-badge--statut { top: 12px; right: 12px; }
    .media-badge--type { bottom: 12px; left: 12px; }
    .media-badge--count { bottom: 12px; right: 12px; }

    .bien-card__body {
        padding: 14px 16px;
        gap: 8px;
    }

    .bien-card__title {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
        color: var(--ios-label);
    }
    .bien-card__price {
        font-size: 18px;
        letter-spacing: -.3px;
    }

    .bien-card__location {
        font-size: 13px;
        color: var(--ios-label-3);
    }
    .bien-card__location i { font-size: 12px; color: var(--rust); }

    .bien-card__meta { gap: 6px; }
    .meta-pill {
        background: #EFEFF4;
        border: none;
        color: var(--ios-label-2);
        font-size: 12px;
        padding: 4px 11px;
    }
    .meta-pill i { color: var(--rust); font-size: 11px; }

    .bien-card__views {
        background: #E3F2FD;
        color: var(--c-info);
        font-size: 12.5px;
        padding: 5px 11px;
    }
    .bien-card__views i { font-size: 12px; }
    .bien-card__views strong { font-size: 13px; }

    .bien-card__actions {
        padding: 12px 16px;
        background: transparent;
        border-top: 0.5px solid var(--ios-separator);
        gap: 8px;
        flex-wrap: nowrap;
        border-radius: 0 0 var(--ios-radius-lg) var(--ios-radius-lg);
    }
    .bien-card__actions .btn {
        padding: 11px 14px;
        font-size: 14px;
        border-radius: 12px;
        font-weight: 600;
        flex: 1;
        justify-content: center;
    }
    .bien-card__actions .btn-ghost {
        background: #EFEFF4;
        border: none;
        color: var(--ios-blue);
    }
    .bien-card__actions .btn-ghost:active { background: #E0E0E5; }
    .bien-card__actions .btn-rust {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
    }
    .bien-card__actions .btn-icon {
        flex: 0 0 auto;
        padding: 11px 14px;
        background: #EFEFF4;
        border: none;
        color: var(--ios-label-2);
    }
    .bien-card__actions .btn-icon:active { background: #E0E0E5; }

    .bien-card.is-menu-open,
    .bien-card.is-menu-open:active {
        transform: none !important;
        z-index: 99999 !important;
    }

    .dropdown-wrapper {
        isolation: auto !important;
    }

    /* ─── Bottom sheet iOS ─── */
    .dropdown-menu {
        display: none !important;
        position: fixed !important;
        bottom: 0 !important;
        left: 0 !important;
        right: 0 !important;
        top: auto !important;
        min-width: 100% !important;
        width: 100% !important;
        max-width: 100% !important;
        border-radius: 20px 20px 0 0 !important;
        padding: 8px 0 calc(12px + env(safe-area-inset-bottom, 0px)) !important;
        transform: translateY(100%) !important;
        box-shadow: 0 -20px 60px rgba(0, 0, 0, .25) !important;
        z-index: 99999 !important;
        border: none !important;
        background: #ffffff !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        transition: transform .3s cubic-bezier(.32, .72, 0, 1), opacity .2s ease, visibility .2s ease !important;
        isolation: auto !important;
    }
    .dropdown-menu.is-open {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
        transform: translateY(0) !important;
        pointer-events: auto !important;
    }
    .dropdown-menu::before {
        /* Reset du pont invisible desktop — inutile en bottom sheet */
        content: '';
        display: block;
        position: static;
        width: 36px;
        height: 4px;
        background: #C7C7CC;
        border-radius: 999px;
        margin: 4px auto 8px;
        pointer-events: none;
    }

    .dropdown-menu__item {
        padding: 14px 20px;
        font-size: 15px;
        border-radius: 0;
        font-weight: 500;
        position: relative;
        z-index: 1;
        pointer-events: auto;
    }
    .dropdown-menu__item:active { background: #F5F5F7; }
    .dropdown-menu__item i {
        width: 24px;
        font-size: 17px;
        color: var(--ios-label-2);
    }

    .dropdown-menu__sep {
        margin: 6px 20px;
        background: var(--ios-separator);
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
    .empty-state__icon {
        width: 68px;
        height: 68px;
        background: #EFEFF4;
        color: var(--ios-label-3);
        font-size: 28px;
    }
    .empty-state h3 { font-size: 17px; letter-spacing: -.3px; }
    .empty-state p { font-size: 13.5px; color: var(--ios-label-3); }

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
    .biens-stat__value { font-size: 20px; }
    .biens-stat__label { font-size: 10.5px; }

    .bien-card__title { font-size: 15px; }
    .bien-card__price { font-size: 17px; }
    .bien-card__body { padding: 12px 14px; }
    .bien-card__actions { padding: 10px 14px; }

    .bien-card__actions .btn {
        padding: 10px 12px;
        font-size: 13px;
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