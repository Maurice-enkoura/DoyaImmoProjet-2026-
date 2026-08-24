@extends('layouts.dashboard-agence')

@section('title', 'Besoins disponibles — DoyaImmo')
@section('page_title', 'Besoins disponibles')
@section('page_sub', 'Parcourez les demandes de logement publiées par les clients')

@section('content')
<div class="view active">
    <!-- Onglets -->
    <div style="display:flex;gap:8px;margin-bottom:24px;border-bottom:1px solid var(--border);">
        <a href="{{ route('agence.demandes.index', ['onglet' => 'compatibles']) }}" 
           class="onglet-link {{ $onglet === 'compatibles' ? 'active' : '' }}"
           style="padding:10px 20px;text-decoration:none;color:{{ $onglet === 'compatibles' ? 'var(--rust)' : 'var(--text-soft)' }};font-weight:600;border-bottom:3px solid {{ $onglet === 'compatibles' ? 'var(--rust)' : 'transparent' }};transition:all 0.2s;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-robot"></i>
            Demandes compatibles
            <span style="background:var(--border);color:var(--text-soft);padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600;">
                {{ $stats['compatibles'] ?? 0 }}
            </span>
        </a>
        <a href="{{ route('agence.demandes.index', ['onglet' => 'toutes']) }}" 
           class="onglet-link {{ $onglet === 'toutes' ? 'active' : '' }}"
           style="padding:10px 20px;text-decoration:none;color:{{ $onglet === 'toutes' ? 'var(--rust)' : 'var(--text-soft)' }};font-weight:600;border-bottom:3px solid {{ $onglet === 'toutes' ? 'var(--rust)' : 'transparent' }};transition:all 0.2s;display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-list"></i>
            Toutes les demandes
            <span style="background:var(--border);color:var(--text-soft);padding:2px 10px;border-radius:999px;font-size:12px;font-weight:600;">
                {{ $stats['total'] ?? 0 }}
            </span>
        </a>
    </div>

    <!-- Filtres -->
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
        <button class="btn btn-ghost btn-sm" onclick="applyFilters()">Filtrer</button>
        <button class="btn btn-ghost btn-sm" onclick="resetFilters()">Réinitialiser</button>
    </div>

    <!-- Liste des demandes -->
    <div class="besoin-grid">
        @forelse($demandes as $item)
            @php
                $demande = $onglet === 'compatibles' ? $item->demande : $item;
                $score = $onglet === 'compatibles' ? $item->score : ($item->score ?? 0);
                $niveau = $onglet === 'compatibles' ? $item->niveau : ($item->niveau ?? 'Aucune correspondance');
                $bien = $onglet === 'compatibles' ? $item->bien : ($item->bien ?? null);
                $estCompatible = $score > 0;
            @endphp
            <div class="besoin-card {{ $estCompatible ? 'compatible' : '' }}">
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
                        <div class="besoin-budget">{{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois</div>
                    </div>

                    <!-- Niveau de compatibilité -->
                    @if($estCompatible)
                        <div class="niveau-badge">
                            <i class="fa-solid fa-robot"></i> {{ $niveau }}
                        </div>
                    @endif

                    <div class="besoin-meta">
                        <span class="meta-pill"><i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}</span>
                        <span class="meta-pill"><i class="fa-solid fa-house"></i> {{ $demande->type_operation->label() }}</span>
                        <span class="meta-pill">Publié {{ $demande->created_at->diffForHumans() }}</span>
                        @if($estCompatible && $bien)
                            <span class="meta-pill compat">
                                <i class="fa-solid fa-building"></i> {{ $bien->titre }}
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

                    <p class="besoin-desc">{{ Str::limit($demande->description, 100) }}</p>

                    <div class="besoin-foot">
                        <span class="posted">
                            {{ $demande->propositions->count() }} offre(s) reçue(s)
                        </span>
                        <div style="display:flex;gap:8px;">
                            <a href="{{ route('agence.demandes.show', $demande) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i> Voir
                            </a>
                            <a href="{{ route('agence.propositions.create', $demande) }}" class="btn btn-rust btn-sm">
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
                    <a href="{{ route('agence.biens.create') }}" class="btn btn-rust" style="margin-top:16px;">
                        Publier un bien
                    </a>
                @else
                    <i class="fa-solid fa-inbox" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.3;"></i>
                    <p style="font-size:16px;font-weight:600;color:var(--text-soft);">Aucune demande disponible</p>
                    <p style="font-size:13px;max-width:400px;margin:0 auto;">
                        Revenez plus tard, de nouvelles demandes seront publiées.
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
        if (zone) url += '&zone=' + zone;
        if (type) url += '&type_bien=' + type;
        if (operation) url += '&type_operation=' + operation;
        if (search) url += '&search=' + search;
        
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
</script>
@endsection

@push('styles')
<style>
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
    }

    .onglet-link.active {
        color: var(--rust);
        border-bottom-color: var(--rust);
    }

    .onglet-link:hover:not(.active) {
        color: var(--ink);
        border-bottom-color: var(--border);
    }

    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        margin-bottom: 24px;
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
    }

    .filter-bar .grow {
        flex: 1;
        min-width: 160px;
    }

    .besoin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 20px;
    }

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
    }

    .compatible-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 999px;
        white-space: nowrap;
        background: #E8F5E9;
        color: #1E7A47;
        border: 1px solid #C8E6C9;
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
        background: var(--border);
        color: var(--text-soft);
        border: 1px solid var(--border);
    }

    .besoin-budget {
        font-weight: 700;
        color: var(--rust);
        font-size: 13px;
        white-space: nowrap;
    }

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
        background: #F7F9FC;
        color: var(--text-soft);
        border-color: var(--border);
    }

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
    }

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

    @media (max-width: 768px) {
        .onglet-link {
            font-size: 13px;
            padding: 8px 12px;
        }
        .filter-bar {
            flex-direction: column;
        }
        .filter-bar select,
        .filter-bar input {
            width: 100%;
            min-width: unset;
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
    }
</style>
@endpush