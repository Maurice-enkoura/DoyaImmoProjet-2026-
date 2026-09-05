@extends('layouts.admin')

@section('title', 'Gestion des biens - DoyaImmo')
@section('page_title', 'Biens immobiliers')
@section('page_sub', 'Gestion des biens publiés sur la plateforme')

@section('content')
<style>
    .vedette-badge {
        background: #F5A623;
        color: #fff;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
</style>

<!-- Statistiques -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="kpi-label">Total des biens</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['disponibles'] ?? 0 }}</div>
        <div class="kpi-label"> Disponibles</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['indisponibles'] ?? 0 }}</div>
        <div class="kpi-label"> Indisponibles</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#F5A623;">{{ $stats['en_vedette'] ?? 0 }}</div>
        <div class="kpi-label"> En vedette</div>
    </div>
</div>

<!-- Filtres -->
<div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <a href="{{ route('admin.biens.index') }}" class="btn btn-sm {{ !request('filtre') ? 'btn-rust' : 'btn-ghost' }}">
        Tous
    </a>
    <a href="{{ route('admin.biens.index', ['filtre' => 'disponibles']) }}" class="btn btn-sm {{ request('filtre') === 'disponibles' ? 'btn-rust' : 'btn-ghost' }}">
        Disponibles
    </a>
    <a href="{{ route('admin.biens.index', ['filtre' => 'indisponibles']) }}" class="btn btn-sm {{ request('filtre') === 'indisponibles' ? 'btn-rust' : 'btn-ghost' }}">
        Indisponibles
    </a>
    <a href="{{ route('admin.biens.vedette') }}" class="btn btn-sm" style="background:#F5A623;color:#fff;border-color:#F5A623;">
        <i class="fa-solid fa-star"></i> À la une
    </a>

    <!-- Recherche -->
    <form action="{{ route('admin.biens.index') }}" method="GET" style="display:flex;gap:8px;margin-left:auto;">
        <input type="text" name="search" placeholder="Rechercher un bien..."
            value="{{ request('search') }}"
            style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;min-width:180px;">
        <button type="submit" class="btn btn-sm btn-ghost">
            <i class="fa-solid fa-search"></i>
        </button>
        @if(request('search') || request('filtre'))
            <a href="{{ route('admin.biens.index') }}" class="btn btn-sm btn-ghost">
                <i class="fa-solid fa-times"></i>
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Bien</th>
                <th>Agence</th>
                <th>Type</th>
                <th>Prix</th>
                <th>Statut</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($biens ?? [] as $bien)
            <tr>
                <td>{{ $bien->id }}</td>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        @if($bien->medias && $bien->medias->first())
                        <img src="{{ asset('storage/' . $bien->medias->first()->fichier) }}"
                            alt="{{ $bien->titre }}"
                            style="width:50px;height:50px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
                        @else
                        <div style="width:50px;height:50px;background:#F0F0F0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--muted);">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        @endif
                        <div>
                            <div class="cell-main">
                                {{ Str::limit($bien->titre, 40) }}
                                @if($bien->est_vedette && $bien->vedette_fin > now())
                                    <span class="vedette-badge">
                                        <i class="fa-solid fa-star"></i> Vedette
                                    </span>
                                @endif
                            </div>
                            <div class="cell-sub">
                                @php
                                $quartierNom = 'N/A';
                                if (is_object($bien->quartier) && method_exists($bien->quartier, 'getAttribute')) {
                                    $quartierNom = $bien->quartier->nom ?? 'N/A';
                                } elseif (is_string($bien->quartier) && !empty($bien->quartier)) {
                                    $quartierNom = $bien->quartier;
                                } elseif (is_numeric($bien->quartier_id) && $bien->quartier_id > 0) {
                                    $quartier = App\Models\Quartier::find($bien->quartier_id);
                                    if ($quartier) {
                                        $quartierNom = $quartier->nom;
                                    }
                                }
                                @endphp
                                <i class="fa-solid fa-location-dot" style="font-size:10px;"></i> {{ $quartierNom }}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div style="display:flex;align-items:center;gap:8px;">
                        @if($bien->agence && $bien->agence->logo)
                        <img src="{{ asset('storage/' . $bien->agence->logo) }}"
                            alt="{{ $bien->agence->nom_agence }}"
                            style="width:30px;height:30px;object-fit:cover;border-radius:50%;border:1px solid var(--border);">
                        @else
                        <div style="width:30px;height:30px;background:var(--gold-soft);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--gold);">
                            {{ $bien->agence ? Str::substr($bien->agence->nom_agence, 0, 2) : 'NA' }}
                        </div>
                        @endif
                        <div>
                            <div class="cell-main">{{ $bien->agence->nom_agence ?? 'N/A' }}</div>
                            <div class="cell-sub">{{ $bien->agence->user->email ?? '' }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="meta-pill">{{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : ($bien->type_bien ?? 'N/A') }}</span>
                    <span class="meta-pill" style="background:#E3F2FD;color:#0D47A1;">
                        {{ is_object($bien->type_contrat) && method_exists($bien->type_contrat, 'label') ? $bien->type_contrat->label() : ($bien->type_contrat ?? 'N/A') }}
                    </span>
                </td>
                <td>
                    <div style="font-weight:600;color:var(--rust);">
                        {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                    </div>
                    <div class="cell-sub">{{ $bien->surface }} m²</div>
                </td>
                <td>
                    <span class="status-pill {{ $bien->statut ? 'status-active' : 'status-inactif' }}">
                        {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                    </span>
                    @if($bien->est_vedette && $bien->vedette_fin > now())
                        <div style="font-size:10px;color:#F5A623;margin-top:2px;">
                            ⭐ {{ $bien->vedette_restante ?? 0 }} jours restants
                        </div>
                    @endif
                </td>
                <td style="text-align:center;">
                    <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                        <a href="{{ route('admin.biens.show', $bien->slug) }}" class="btn btn-sm btn-ghost" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        
                        @if($bien->statut)
                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                        <form action="{{ route('admin.biens.desactiver', $bien->slug) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" title="Désactiver" onclick="return confirm('Désactiver ce bien ?')">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </form>
                        @else
                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                        <form action="{{ route('admin.biens.activer', $bien->slug) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" title="Activer" onclick="return confirm('Activer ce bien ?')">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </form>
                        @endif

                        <!-- Action Vedette - AVEC FORMULAIRE POST -->
                        @if($bien->est_vedette && $bien->vedette_fin > now())
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <form action="{{ route('admin.biens.vedette.retirer', $bien->slug) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Retirer de la vedette" onclick="return confirm('Retirer ce bien de la vedette ?')">
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-sm btn-ghost" title="Prolonger" onclick="openProlongerModal('{{ $bien->slug }}')">
                                <i class="fa-solid fa-clock"></i>
                            </button>
                        @else
                            <!-- Utiliser un bouton qui ouvre le modal avec le slug -->
                            <button type="button" class="btn btn-sm btn-ghost" title="Mettre en vedette" onclick="openVedetteModal('{{ $bien->slug }}')">
                                <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                            </button>
                        @endif

                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                        <form action="{{ route('admin.biens.destroy', $bien->slug) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer définitivement ce bien ?')">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                    <i class="fa-solid fa-house-circle-exclamation" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                    Aucun bien trouvé
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- ==================== PAGINATION RÉORGANISÉE ==================== -->
@if(isset($biens) && $biens->hasPages())
<div class="pagination-container">
    <nav class="pagination-nav" aria-label="Pagination des biens">
        {{-- Informations de pagination --}}
        <div class="pagination-info">
            <span class="pagination-stats">
                <i class="fa-solid fa-paper-plane" style="color:var(--rust);"></i>
                Affichage de <strong>{{ $biens->firstItem() }}</strong> à <strong>{{ $biens->lastItem() }}</strong> 
                sur <strong>{{ $biens->total() }}</strong> biens
            </span>
        </div>

        {{-- Liens de pagination --}}
        <ul class="pagination">
            {{-- Lien "Précédent" --}}
            @if($biens->onFirstPage())
                <li class="disabled" aria-disabled="true">
                    <span><i class="fa-solid fa-chevron-left"></i> Précédent</span>
                </li>
            @else
                <li>
                    <a href="{{ $biens->previousPageUrl() }}" rel="prev" aria-label="Page précédente">
                        <i class="fa-solid fa-chevron-left"></i> Précédent
                    </a>
                </li>
            @endif

            {{-- Éléments de pagination --}}
            @php
                $currentPage = $biens->currentPage();
                $lastPage = $biens->lastPage();
                $window = 2;
            @endphp

            @foreach(range(1, $lastPage) as $page)
                @if($page == 1 || $page == $lastPage || abs($page - $currentPage) <= $window)
                    @if($page == $currentPage)
                        <li class="active" aria-current="page">
                            <span>{{ $page }}</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $biens->url($page) }}" aria-label="Page {{ $page }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endif
                @elseif($page == $currentPage - $window - 1 || $page == $currentPage + $window + 1)
                    <li class="disabled" aria-disabled="true">
                        <span>&hellip;</span>
                    </li>
                @endif
            @endforeach

            {{-- Lien "Suivant" --}}
            @if($biens->hasMorePages())
                <li>
                    <a href="{{ $biens->nextPageUrl() }}" rel="next" aria-label="Page suivante">
                        Suivant <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="disabled" aria-disabled="true">
                    <span>Suivant <i class="fa-solid fa-chevron-right"></i></span>
                </li>
            @endif
        </ul>

        {{-- Sélecteur de nombre d'éléments par page --}}
        <div class="pagination-per-page">
            <span class="per-page-label">
                <i class="fa-solid fa-paper-plane" style="color:var(--rust);font-size:12px;"></i>
                Afficher :
            </span>
            <select id="perPage" class="per-page-select" onchange="changePerPage(this.value)">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="per-page-text">par page</span>
        </div>
    </nav>
</div>
@endif

<!-- Modal pour mettre en vedette -->
<div id="vedetteModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:var(--radius);padding:32px;max-width:400px;width:90%;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-family:var(--display);font-size:18px;">
                <i class="fa-solid fa-star" style="color:#F5A623;"></i> Mettre en vedette
            </h3>
            <button onclick="closeVedetteModal()" style="background:none;border:none;font-size:24px;cursor:pointer;">&times;</button>
        </div>
        
        <!-- ✅ CORRIGÉ : Utilisation du slug via JavaScript -->
        <form id="vedetteForm" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label for="duree" style="display:block;font-weight:600;margin-bottom:4px;">Durée (en jours)</label>
                <select name="duree" id="duree" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:14px;">
                    <option value="1">1 jour</option>
                    <option value="3">3 jours</option>
                    <option value="7" selected>7 jours</option>
                    <option value="14">14 jours</option>
                    <option value="30">30 jours</option>
                </select>
            </div>
            <div style="padding:12px 16px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;margin-bottom:16px;">
                <p style="font-size:13px;color:#BF360C;margin:0;">
                    <i class="fa-solid fa-info-circle"></i> 
                    Le bien apparaîtra dans la section "À la une" de l'accueil.
                </p>
            </div>
            <button type="submit" class="btn btn-rust" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-star"></i> Mettre en vedette
            </button>
        </form>
    </div>
</div>

<!-- Modal pour prolonger -->
<div id="prolongerModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:var(--radius);padding:32px;max-width:400px;width:90%;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-family:var(--display);font-size:18px;">
                <i class="fa-solid fa-clock" style="color:#F5A623;"></i> Prolonger la vedette
            </h3>
            <button onclick="closeProlongerModal()" style="background:none;border:none;font-size:24px;cursor:pointer;">&times;</button>
        </div>
        
        <!-- ✅ CORRIGÉ : Utilisation du slug via JavaScript -->
        <form id="prolongerForm" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label for="duree_prolonger" style="display:block;font-weight:600;margin-bottom:4px;">Prolonger de (en jours)</label>
                <select name="duree" id="duree_prolonger" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:14px;">
                    <option value="1">+1 jour</option>
                    <option value="3">+3 jours</option>
                    <option value="7" selected>+7 jours</option>
                    <option value="14">+14 jours</option>
                    <option value="30">+30 jours</option>
                </select>
            </div>
            <button type="submit" class="btn btn-rust" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-clock"></i> Prolonger
            </button>
        </form>
    </div>
</div>

<script>
    // ✅ CORRIGÉ : Utilisation du slug dans les fonctions JavaScript
    function openVedetteModal(bienSlug) {
        const modal = document.getElementById('vedetteModal');
        const form = document.getElementById('vedetteForm');
        form.action = `/admin/biens/${bienSlug}/vedette`;
        modal.style.display = 'flex';
    }

    function closeVedetteModal() {
        document.getElementById('vedetteModal').style.display = 'none';
    }

    function openProlongerModal(bienSlug) {
        const modal = document.getElementById('prolongerModal');
        const form = document.getElementById('prolongerForm');
        form.action = `/admin/biens/${bienSlug}/vedette/prolonger`;
        modal.style.display = 'flex';
    }

    function closeProlongerModal() {
        document.getElementById('prolongerModal').style.display = 'none';
    }

    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    // Fermer en cliquant à l'extérieur
    document.getElementById('vedetteModal').addEventListener('click', function(e) {
        if (e.target === this) closeVedetteModal();
    });
    document.getElementById('prolongerModal').addEventListener('click', function(e) {
        if (e.target === this) closeProlongerModal();
    });
</script>

@push('styles')
<style>
    /* ===== KPI CARDS ===== */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .kpi-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        text-align: center;
    }

    .kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--ink);
    }

    .kpi-label {
        font-size: 13px;
        color: var(--muted);
        margin-top: 2px;
    }

    /* ===== STATUS PILLS ===== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-active {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-inactif {
        background: #F5F5F5;
        color: var(--muted);
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 10px;
        color: var(--text-soft);
        margin: 2px;
    }

    /* ===== TABLE ===== */
    .table-wrap {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow-x: auto;
    }

    .table-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .table-wrap thead th {
        background: #F7F9FC;
        text-align: left;
        padding: 12px 16px;
        font-weight: 600;
        color: var(--text-soft);
        border-bottom: 1px solid var(--border);
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .table-wrap tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .table-wrap tbody tr:hover {
        background: #FAFBFD;
    }

    .cell-main {
        font-weight: 600;
        color: var(--ink);
    }
    .cell-sub {
        font-size: 12px;
        color: var(--muted);
    }

    /* ===== BUTTONS ===== */
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
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }
    .btn-ghost:hover {
        background: var(--border);
        color: var(--ink);
    }

    .btn-sm {
        padding: 5px 12px;
        font-size: 12px;
        border-radius: 6px;
    }

    .btn-success {
        background: #1E7A47;
        color: #fff;
        border: none;
    }
    .btn-success:hover {
        background: #145c35;
        color: #fff;
    }
    .btn-danger {
        background: #C62828;
        color: #fff;
        border: none;
    }
    .btn-danger:hover {
        background: #9A1E1E;
        color: #fff;
    }

    /* ============================================
       PAGINATION RÉORGANISÉE AVEC PAPER PLANE
    ============================================ */
    .pagination-container {
        margin-top: 24px;
    }

    .pagination-nav {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        align-items: center;
    }

    .pagination-info {
        width: 100%;
        text-align: center;
    }

    .pagination-stats {
        font-size: clamp(12px, 0.8vw, 14px);
        color: var(--text-soft);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .pagination-stats strong {
        color: var(--ink);
        font-weight: 700;
    }

    .pagination-stats .fa-paper-plane {
        font-size: 14px;
    }

    /* ===== PAGINATION LINKS ===== */
    .pagination {
        display: flex;
        gap: 4px;
        list-style: none;
        padding: 0;
        margin: 0;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination li {
        display: inline;
    }

    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        padding: clamp(6px, 0.5vw, 8px) clamp(10px, 0.8vw, 14px);
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: clamp(12px, 0.8vw, 13px);
        transition: all 0.2s ease;
        min-width: clamp(32px, 3vw, 40px);
        min-height: clamp(32px, 3vw, 40px);
        text-align: center;
        background: #fff;
        font-weight: 500;
    }

    .pagination a:hover {
        background: var(--border);
        border-color: var(--border);
        color: var(--ink);
        transform: translateY(-1px);
    }

    .pagination .active span {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
        box-shadow: 0 2px 8px rgba(181, 80, 42, 0.25);
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f7f7f7;
    }

    .pagination .disabled span:hover {
        transform: none;
        background: #f7f7f7;
    }

    .pagination a[rel="prev"],
    .pagination a[rel="next"] {
        font-weight: 600;
        padding: clamp(6px, 0.5vw, 8px) clamp(12px, 0.8vw, 16px);
    }

    .pagination a[rel="prev"] i,
    .pagination a[rel="next"] i,
    .pagination .disabled span i {
        font-size: 11px;
    }

    /* ===== PER PAGE SELECTOR ===== */
    .pagination-per-page {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--text-soft);
        border-top: 1px solid var(--border);
        padding-top: 14px;
        width: 100%;
        justify-content: center;
        flex-wrap: wrap;
    }

    .per-page-label {
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .per-page-label .fa-paper-plane {
        font-size: 13px;
    }

    .per-page-select {
        padding: 5px 24px 5px 12px;
        border: 1px solid var(--border);
        border-radius: 6px;
        font-size: clamp(12px, 0.8vw, 13px);
        font-family: inherit;
        color: var(--ink);
        background: #fff;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 10 10'%3E%3Cpath fill='%238A91A0' d='M5 7L1 3h8z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 8px center;
        cursor: pointer;
        -webkit-appearance: none;
        appearance: none;
        transition: border-color 0.3s;
    }

    .per-page-select:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }

    .per-page-text {
        color: var(--muted);
    }

    /* ============================================
       RESPONSIVE
    ============================================ */

    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .kpi-value {
            font-size: 20px;
        }

        /* Filtres responsive */
        [style*="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;"] {
            flex-direction: column;
            align-items: stretch !important;
        }
        [style*="margin-left:auto;"] {
            margin-left: 0 !important;
            width: 100%;
        }
        [style*="margin-left:auto;"] input {
            min-width: 0 !important;
            flex: 1;
        }

        .table-wrap table {
            font-size: 12px;
        }
        .table-wrap thead th,
        .table-wrap tbody td {
            padding: 8px 10px;
        }

        /* Pagination responsive */
        .pagination-nav {
            padding: 14px 16px;
            gap: 14px;
        }

        .pagination a,
        .pagination span {
            padding: 4px 8px;
            font-size: 11px;
            min-width: 28px;
            min-height: 28px;
            border-radius: 6px;
        }

        .pagination a[rel="prev"],
        .pagination a[rel="next"] {
            font-size: 11px;
            padding: 4px 10px;
        }

        .pagination a[rel="prev"] i,
        .pagination a[rel="next"] i {
            font-size: 10px;
        }

        .pagination-per-page {
            flex-wrap: wrap;
            justify-content: center;
            gap: 6px;
            padding-top: 12px;
        }

        .per-page-select {
            font-size: 12px;
            padding: 4px 20px 4px 10px;
        }

        .pagination-stats {
            font-size: 12px;
        }

        .pagination-stats .fa-paper-plane {
            font-size: 12px;
        }

        /* Actions sur mobile */
        td:last-child .btn {
            padding: 4px 8px;
            font-size: 11px;
        }
        td:last-child [style*="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;"] {
            gap: 3px !important;
        }
    }

    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .kpi-card {
            padding: 12px 16px;
        }
        .kpi-value {
            font-size: 18px;
        }

        .pagination a,
        .pagination span {
            padding: 3px 6px;
            font-size: 10px;
            min-width: 24px;
            min-height: 24px;
        }

        .pagination a[rel="prev"],
        .pagination a[rel="next"] {
            font-size: 10px;
            padding: 3px 8px;
        }

        .pagination-nav {
            padding: 10px 12px;
        }

        .pagination-per-page {
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        .pagination a,
        .pagination span {
            transition: none !important;
        }
        .btn {
            transition: none !important;
        }
    }
</style>
@endpush
@endsection