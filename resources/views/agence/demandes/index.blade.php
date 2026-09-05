@extends('layouts.dashboard-agence')

@section('title', 'Besoins disponibles — DoyaImmo')
@section('page_title', 'Besoins disponibles')
@section('page_sub', 'Parcourez les demandes de logement publiées par les clients')

@section('content')
<div class="view active">
    <!-- Onglets -->
    <div style="display:flex;gap:8px;margin-bottom:24px;border-bottom:1px solid var(--border);flex-wrap:wrap;">
        <a href="{{ route('agence.demandes.index', ['onglet' => 'compatibles']) }}" 
           class="onglet-link {{ $onglet === 'compatibles' ? 'active' : '' }}">
            <i class="fa-solid fa-robot"></i>
            Demandes compatibles
            <span class="onglet-count">
                {{ $stats['compatibles'] ?? 0 }}
            </span>
        </a>
        <a href="{{ route('agence.demandes.index', ['onglet' => 'toutes']) }}" 
           class="onglet-link {{ $onglet === 'toutes' ? 'active' : '' }}">
            <i class="fa-solid fa-list"></i>
            Toutes les demandes
            <span class="onglet-count">
                {{ $stats['total'] ?? 0 }}
            </span>
        </a>
    </div>

    <!-- ✅ Filtres -->
    <div class="filter-bar">
        <select name="zone" id="filterZone">
            <option value="">Toutes les zones</option>
            @foreach($zones as $zone)
                <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>
                    {{ $zone }}
                </option>
            @endforeach
        </select>
        <select name="type_bien" id="filterType">
            <option value="">Tous types de bien</option>
            @foreach(\App\Enums\TypeBienEnum::cases() as $type)
                <option value="{{ $type->value }}" {{ request('type_bien') == $type->value ? 'selected' : '' }}>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        <select name="type_operation" id="filterOperation">
            <option value="">Toutes opérations</option>
            @foreach(\App\Enums\TypeOperationEnum::cases() as $type)
                <option value="{{ $type->value }}" {{ request('type_operation') == $type->value ? 'selected' : '' }}>
                    {{ $type->label() }}
                </option>
            @endforeach
        </select>
        <input class="grow" type="text" id="filterSearch" placeholder="Rechercher..." value="{{ request('search') }}">
        <button class="btn btn-ghost btn-sm" onclick="applyFilters()">
            <i class="fa-solid fa-filter"></i> Filtrer
        </button>
        <button class="btn btn-ghost btn-sm" onclick="resetFilters()">
            <i class="fa-solid fa-rotate"></i> Réinitialiser
        </button>
    </div>

    <!-- ✅ Dans l'onglet "toutes", afficher le nombre de compatibles/non compatibles -->
    @if($onglet === 'toutes' && $demandes->count() > 0)
        @php
            $compatiblesCount = $demandes->filter(function($item) {
                return $item->score > 0;
            })->count();
            $nonCompatiblesCount = $demandes->filter(function($item) {
                return $item->score == 0;
            })->count();
        @endphp
        <div style="margin-bottom:16px;padding:12px 16px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <div>
                <span style="font-weight:600;color:var(--text-soft);">
                    <i class="fa-solid fa-circle-check" style="color:#1E7A47;"></i> 
                    {{ $compatiblesCount }} demande(s) compatible(s)
                </span>
                <span style="margin-left:12px;color:var(--muted);">
                    <i class="fa-solid fa-circle-xmark" style="color:#C62828;"></i> 
                    {{ $nonCompatiblesCount }} non compatible(s)
                </span>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button class="btn btn-ghost btn-sm" onclick="filtrerCompatibles(true)"> Compatibles</button>
                <button class="btn btn-ghost btn-sm" onclick="filtrerCompatibles(false)"> Non compatibles</button>
                <button class="btn btn-rust btn-sm" onclick="filtrerCompatibles(null)"> Tout voir</button>
            </div>
        </div>
    @endif

    <!-- Liste des demandes -->
    <div class="besoin-grid" id="besoinGrid">
        @forelse($demandes as $item)
            @php
                $demande = $onglet === 'compatibles' ? $item->demande : $item;
                $score = $onglet === 'compatibles' ? $item->score : ($item->score ?? 0);
                $niveau = $onglet === 'compatibles' ? $item->niveau : ($item->niveau ?? 'Aucune correspondance');
                $bien = $onglet === 'compatibles' ? $item->bien : ($item->bien ?? null);
                $estCompatible = $score > 0;
                
                // Déterminer le libellé du budget
                $budgetLabel = is_object($demande->type_operation) && $demande->type_operation->value === 'location' ? 'F/mois' : 'F';
            @endphp
            <div class="besoin-card {{ $estCompatible ? 'compatible' : '' }}" data-compatible="{{ $estCompatible ? 'true' : 'false' }}">
                <div class="besoin-body">
                    <div class="besoin-top">
                        <div class="besoin-type">
                            {{ $demande->type_bien->label() }}
                            @if($estCompatible)
                                <span class="compatible-badge">
                                    <i class="fa-solid fa-circle-check"></i> {{ $score }}% compatible
                                </span>
                            @elseif($onglet === 'toutes')
                                <span class="compatible-badge non-compatible">
                                    <i class="fa-solid fa-circle-xmark"></i> Non compatible
                                </span>
                            @endif
                        </div>
                        <div class="besoin-budget">
                            {{ number_format($demande->budget_maximum, 0, ',', ' ') }} {{ $budgetLabel }}
                        </div>
                    </div>

                    <!-- Niveau de compatibilité -->
                    @if($estCompatible)
                        <div class="niveau-badge niveau-{{ $score >= 80 ? 'excellent' : ($score >= 60 ? 'bon' : ($score >= 40 ? 'moyen' : 'faible')) }}">
                            <i class="fa-solid fa-robot"></i> {{ $niveau }}
                        </div>
                    @endif

                    <div class="besoin-meta">
                        <span class="meta-pill"><i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}</span>
                        <span class="meta-pill"><i class="fa-solid fa-handshake"></i> {{ $demande->type_operation->label() }}</span>
                        @if($demande->surface_minimum)
                            <span class="meta-pill"><i class="fa-regular fa-square"></i> {{ $demande->surface_minimum }} m²</span>
                        @endif
                        @if($demande->nombre_chambres)
                            <span class="meta-pill"><i class="fa-regular fa-bed"></i> {{ $demande->nombre_chambres }} ch.</span>
                        @endif
                        <span class="meta-pill"><i class="fa-regular fa-calendar"></i> {{ $demande->created_at->diffForHumans() }}</span>
                        @if($estCompatible && $bien)
                            <span class="meta-pill compat">
                                <i class="fa-solid fa-building"></i> {{ Str::limit($bien->titre, 20) }}
                            </span>
                        @endif
                    </div>

                    <!-- Barre de compatibilité -->
                    @if($estCompatible)
                        <div class="compat-progress">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width:{{ $score }}%;"></div>
                            </div>
                            <div class="progress-label">{{ $score }}% compatible</div>
                        </div>
                    @endif

                    <!-- ✅ Équipements demandés (aperçu) -->
                    @php
                        $equipementsDemande = $demande->equipements;
                    @endphp
                    @if(count($equipementsDemande) > 0)
                        <div class="equipements-preview">
                            <span style="font-size:11px;color:var(--muted);font-weight:600;">Équipements souhaités :</span>
                            @foreach(array_slice($equipementsDemande, 0, 3) as $equipement)
                                <span class="equip-tag">{{ $equipement }}</span>
                            @endforeach
                            @if(count($equipementsDemande) > 3)
                                <span class="equip-tag more">+{{ count($equipementsDemande) - 3 }}</span>
                            @endif
                        </div>
                    @endif

                    <p class="besoin-desc">{{ Str::limit($demande->description, 100) }}</p>

                    <div class="besoin-foot">
                        <span class="posted">
                            <i class="fa-regular fa-envelope"></i>
                            {{ $demande->propositions->count() }} offre(s) reçue(s)
                            @if($demande->propositions->where('statut', 'en_attente')->count() > 0)
                                <span class="offre-attente">
                                    ({{ $demande->propositions->where('statut', 'en_attente')->count() }} en attente)
                                </span>
                            @endif
                        </span>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;">
                            <a href="{{ route('agence.demandes.show', $demande->slug) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i> Voir
                            </a>
                            <a href="{{ route('agence.propositions.create', $demande->slug) }}" class="btn btn-rust btn-sm">
                                <i class="fa-solid fa-paper-plane"></i> Faire une offre
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
                @if($onglet === 'compatibles')
                    <i class="fa-solid fa-robot" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.3;"></i>
                    <p style="font-size:16px;font-weight:600;color:var(--text-soft);">Aucune demande compatible</p>
                    <p style="font-size:13px;max-width:400px;margin:0 auto;">
                        Publiez des biens dans les zones où il y a des demandes pour voir des correspondances.
                    </p>
                    <a href="{{ route('agence.biens.create') }}" class="btn btn-rust" style="margin-top:16px;display:inline-flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-plus"></i> Publier un bien
                    </a>
                @else
                    <i class="fa-solid fa-inbox" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.3;"></i>
                    <p style="font-size:16px;font-weight:600;color:var(--text-soft);">Aucune demande disponible</p>
                    <p style="font-size:13px;max-width:400px;margin:0 auto;">
                        Revenez plus tard, de nouvelles demandes seront publiées par les particuliers.
                    </p>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div style="margin-top:30px;">
        {{ $demandes->appends(request()->query())->links() }}
    </div>
</div>

<script>
    function applyFilters() {
        const zone = document.getElementById('filterZone').value;
        const type = document.getElementById('filterType').value;
        const operation = document.getElementById('filterOperation').value;
        const search = document.getElementById('filterSearch').value;
        const onglet = '{{ $onglet }}';
        
        let url = '{{ route("agence.demandes.index") }}?onglet=' + onglet;
        if (zone) url += '&zone=' + encodeURIComponent(zone);
        if (type) url += '&type_bien=' + encodeURIComponent(type);
        if (operation) url += '&type_operation=' + encodeURIComponent(operation);
        if (search) url += '&search=' + encodeURIComponent(search);
        
        window.location.href = url;
    }

    function resetFilters() {
        window.location.href = '{{ route("agence.demandes.index") }}?onglet={{ $onglet }}';
    }

    document.getElementById('filterSearch')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });

    // ✅ Fonction pour filtrer les demandes compatibles/non compatibles
    function filtrerCompatibles(showCompatible) {
        const cards = document.querySelectorAll('#besoinGrid .besoin-card');
        let visibleCount = 0;
        
        cards.forEach(card => {
            const isCompatible = card.dataset.compatible === 'true';
            
            if (showCompatible === true) {
                // Afficher uniquement les compatibles
                if (isCompatible) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            } else if (showCompatible === false) {
                // Afficher uniquement les non compatibles
                if (!isCompatible) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            } else {
                // Tout afficher
                card.style.display = 'block';
                visibleCount++;
            }
        });

        // Mettre à jour le texte du bouton
        const buttons = document.querySelectorAll('[onclick^="filtrerCompatibles"]');
        buttons.forEach(btn => {
            if (btn.textContent.includes('Compatibles') && showCompatible === true) {
                btn.style.background = 'var(--rust)';
                btn.style.color = '#fff';
            } else if (btn.textContent.includes('Non compatibles') && showCompatible === false) {
                btn.style.background = 'var(--rust)';
                btn.style.color = '#fff';
            } else if (btn.textContent.includes('Tout voir') && showCompatible === null) {
                btn.style.background = 'var(--rust)';
                btn.style.color = '#fff';
            } else {
                btn.style.background = 'transparent';
                btn.style.color = 'var(--text-soft)';
            }
        });
    }
</script>
@endsection

@push('styles')
<style>
    /* ===================== ONGLETS ===================== */
    .onglet-link {
        padding: 10px 20px;
        text-decoration: none;
        color: var(--text-soft);
        font-weight: 600;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .onglet-link.active {
        color: var(--rust);
        border-bottom-color: var(--rust);
    }

    .onglet-link:hover:not(.active) {
        color: var(--ink);
        border-bottom-color: var(--border);
    }

    .onglet-count {
        background: var(--border);
        color: var(--text-soft);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }

    .onglet-link.active .onglet-count {
        background: var(--rust-soft);
        color: var(--rust);
    }

    /* ===================== FILTRES ===================== */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-bottom: 24px;
        padding: 16px;
        background: #FAFBFC;
        border-radius: var(--radius);
        border: 1px solid var(--border);
    }

    .filter-bar select,
    .filter-bar input {
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        font-size: 13px;
        font-family: inherit;
        min-width: 140px;
        transition: border-color 0.2s;
    }

    .filter-bar select:focus,
    .filter-bar input:focus {
        outline: none;
        border-color: var(--rust);
    }

    .filter-bar .grow {
        flex: 1;
        min-width: 160px;
    }

    /* ===================== GRILLE ===================== */
    .besoin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
    }

    /* ===================== CARTE ===================== */
    .besoin-card {
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        background: #fff;
    }

    .besoin-card.compatible {
        border-color: var(--rust);
        border-width: 2px;
    }

    .besoin-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .besoin-body {
        padding: 16px;
    }

    /* ===================== EN-TÊTE ===================== */
    .besoin-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 6px;
        gap: 8px;
    }

    .besoin-type {
        font-family: var(--display);
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        flex: 1;
    }

    .besoin-budget {
        font-weight: 700;
        color: var(--rust);
        font-size: 13px;
        white-space: nowrap;
        flex-shrink: 0;
    }

    /* ===================== BADGES ===================== */
    .compatible-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 999px;
        white-space: nowrap;
        background: #E8F5E9;
        color: #1E7A47;
        border: 1px solid #C8E6C9;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .compatible-badge.non-compatible {
        background: #FFEBEE;
        color: #C62828;
        border-color: #FFCDD2;
    }

    .niveau-badge {
        display: inline-block;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 12px;
        border-radius: 999px;
        margin-bottom: 8px;
        border: 1px solid var(--border);
    }

    .niveau-excellent {
        background: #E8F5E9;
        color: #1E7A47;
        border-color: #C8E6C9;
    }

    .niveau-bon {
        background: #E3F2FD;
        color: #0D47A1;
        border-color: #BBDEFB;
    }

    .niveau-moyen {
        background: #FFF8E1;
        color: #E65100;
        border-color: #FFE0B2;
    }

    .niveau-faible {
        background: #FFF3E0;
        color: #BF360C;
        border-color: #FFCCBC;
    }

    /* ===================== MÉTADONNÉES ===================== */
    .besoin-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #F7F9FC;
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11.5px;
        color: var(--text-soft);
        border: 1px solid var(--border);
    }

    .meta-pill.compat {
        background: #E8F5E9;
        color: #1E7A47;
        border-color: #C8E6C9;
    }

    /* ===================== PROGRESS BAR ===================== */
    .compat-progress {
        margin: 10px 0 12px;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: var(--border);
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.5s;
        background: var(--rust);
    }

    .progress-label {
        font-size: 11px;
        color: var(--muted);
        margin-top: 4px;
        text-align: right;
    }

    /* ===================== ÉQUIPEMENTS ===================== */
    .equipements-preview {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px;
        margin: 6px 0 10px;
        padding: 6px 10px;
        background: #F7F9FC;
        border-radius: 6px;
        border: 1px solid var(--border);
    }

    .equip-tag {
        display: inline-block;
        padding: 1px 8px;
        background: #fff;
        border-radius: 999px;
        font-size: 10px;
        color: var(--text-soft);
        border: 1px solid var(--border);
    }

    .equip-tag.more {
        background: var(--border);
        color: var(--muted);
        border: none;
        font-weight: 600;
    }

    /* ===================== DESCRIPTION ===================== */
    .besoin-desc {
        font-size: 13px;
        color: var(--text-soft);
        line-height: 1.6;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ===================== PIED DE CARTE ===================== */
    .besoin-foot {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 8px;
    }

    .posted {
        font-size: 12px;
        color: var(--muted);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .offre-attente {
        color: #E65100;
        font-weight: 600;
    }

    /* ===================== BOUTONS ===================== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
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
    }

    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }

    .btn-ghost:hover {
        background: var(--border);
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }

    /* ===================== PAGINATION ===================== */
    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        list-style: none;
        padding: 0;
    }

    .pagination a, .pagination span {
        display: inline-block;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
    }

    .pagination a:hover {
        background: var(--border);
    }

    .pagination .active span {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .onglet-link {
            font-size: 13px;
            padding: 8px 12px;
        }

        .filter-bar {
            flex-direction: column;
            padding: 12px;
        }

        .filter-bar select,
        .filter-bar input {
            width: 100%;
            min-width: unset;
        }

        .filter-bar .btn {
            width: 100%;
            justify-content: center;
        }

        .besoin-grid {
            grid-template-columns: 1fr;
        }

        .besoin-top {
            flex-direction: column;
        }

        .besoin-type {
            font-size: 13px;
        }

        .besoin-foot {
            flex-direction: column;
            align-items: stretch;
        }

        .besoin-foot .btn {
            justify-content: center;
        }

        .besoin-foot > div {
            flex-direction: column;
            gap: 6px;
        }

        .besoin-foot > div .btn {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .onglet-link {
            font-size: 12px;
            padding: 6px 10px;
        }

        .onglet-count {
            font-size: 10px;
            padding: 1px 8px;
        }

        .besoin-body {
            padding: 12px;
        }

        .compatible-badge {
            font-size: 10px;
            padding: 1px 8px;
        }

        .meta-pill {
            font-size: 10px;
            padding: 1px 8px;
        }

        .btn-sm {
            font-size: 11px;
            padding: 5px 10px;
        }

        .pagination a, .pagination span {
            padding: 6px 10px;
            font-size: 12px;
            min-width: 32px;
            text-align: center;
        }
    }
</style>
@endpush