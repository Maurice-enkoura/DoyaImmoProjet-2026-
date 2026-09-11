@extends('layouts.admin')

@section('title', 'Gestion des signalements - DoyaImmo')
@section('page_title', 'Signalements')
@section('page_sub', 'Gestion des signalements utilisateurs')

@section('content')
    <!-- Statistiques -->
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
            <div class="kpi-label">Total</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#E65100;">{{ $stats['en_attente'] ?? 0 }}</div>
            <div class="kpi-label">En attente</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#1E7A47;">{{ $stats['traites'] ?? 0 }}</div>
            <div class="kpi-label">Traités</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-value" style="color:#C62828;">{{ $stats['rejetes'] ?? 0 }}</div>
            <div class="kpi-label">Rejetés</div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
        <a href="{{ route('admin.signalements.index') }}" class="btn btn-sm {{ !request('filtre') ? 'btn-rust' : 'btn-ghost' }}">Tous</a>
        <a href="{{ route('admin.signalements.index', ['filtre' => 'en_attente']) }}" class="btn btn-sm {{ request('filtre') === 'en_attente' ? 'btn-rust' : 'btn-ghost' }}">En attente</a>
        <a href="{{ route('admin.signalements.index', ['filtre' => 'traites']) }}" class="btn btn-sm {{ request('filtre') === 'traites' ? 'btn-rust' : 'btn-ghost' }}">Traités</a>
        <a href="{{ route('admin.signalements.index', ['filtre' => 'rejetes']) }}" class="btn btn-sm {{ request('filtre') === 'rejetes' ? 'btn-rust' : 'btn-ghost' }}">Rejetés</a>
        
        <!-- Recherche -->
        <form action="{{ route('admin.signalements.index') }}" method="GET" style="display:flex;gap:8px;margin-left:auto;">
            <input type="text" name="search" placeholder="Rechercher..." 
                   value="{{ request('search') }}" 
                   style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;min-width:200px;">
            <button type="submit" class="btn btn-sm btn-ghost">
                <i class="fa-solid fa-search"></i>
            </button>
            @if(request('search') || request('filtre'))
                <a href="{{ route('admin.signalements.index') }}" class="btn btn-sm btn-ghost">
                    <i class="fa-solid fa-times"></i> Réinitialiser
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Signalement</th>
                    <th>Signalé par</th>
                    <th>Agence concernée</th>
                    <th>Motif</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th style="text-align:center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($signalements ?? [] as $signalement)
                    <tr>
                        <td>
                            <div class="cell-main">
                                @php
                                    $type = class_basename($signalement->signalable_type);
                                    $icon = match($type) {
                                        'BienImmobilier' => 'fa-solid fa-house',
                                        'DemandeImmobiliere' => 'fa-solid fa-file',
                                        'Proposition' => 'fa-solid fa-handshake',
                                        default => 'fa-solid fa-flag'
                                    };
                                @endphp
                                <i class="{{ $icon }}"></i>
                                {{ $type }}
                                #{{ $signalement->signalable_id }}
                            </div>
                            <div class="cell-sub">{{ Str::limit($signalement->description, 60) }}</div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--rust);">
                                    {{ strtoupper(substr($signalement->particulier->user->prenom ?? 'U', 0, 1)) }}{{ strtoupper(substr($signalement->particulier->user->nom ?? 'U', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="cell-main">{{ $signalement->particulier->user->prenom ?? 'N/A' }} {{ $signalement->particulier->user->nom ?? '' }}</div>
                                    <div class="cell-sub">{{ $signalement->particulier->user->email ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($signalement->agence)
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($signalement->agence->logo)
                                        <img src="{{ asset('storage/' . $signalement->agence->logo) }}" 
                                             alt="{{ $signalement->agence->nom_agence }}" 
                                             style="width:30px;height:30px;object-fit:cover;border-radius:50%;border:1px solid var(--border);">
                                    @else
                                        <div style="width:30px;height:30px;border-radius:50%;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--gold);">
                                            {{ strtoupper(substr($signalement->agence->nom_agence ?? 'NA', 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="cell-main">{{ $signalement->agence->nom_agence ?? 'N/A' }}</div>
                                        <div class="cell-sub">{{ $signalement->agence->user->email ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            @else
                                <span style="color:var(--muted);font-size:13px;">
                                    <i class="fa-solid fa-building-circle-exclamation"></i> Aucune agence
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="meta-pill">
                                @php
                                    $motifColors = [
                                        'fraude' => '#C62828',
                                        'arnaque' => '#C62828',
                                        'contenu_inapproprie' => '#E65100',
                                        'fausse_annonce' => '#E65100',
                                        'autre' => '#6A7280',
                                    ];
                                    $motifValue = is_object($signalement->motif) ? $signalement->motif->value : $signalement->motif;
                                    $motifColor = $motifColors[$motifValue] ?? '#6A7280';
                                @endphp
                                <span style="color:{{ $motifColor }};font-weight:600;">
                                    {{ is_object($signalement->motif) ? $signalement->motif->label() : $signalement->motif }}
                                </span>
                            </span>
                        </td>
                        <td>
                            @php
                                // ✅ CORRECTION : Récupérer la valeur du statut
                                $statusValue = is_object($signalement->statut) ? $signalement->statut->value : $signalement->statut;
                                
                                $statusColors = [
                                    'en_attente' => ['bg' => '#FFF8E1', 'color' => '#E65100', 'label' => 'En attente'],
                                    'traite' => ['bg' => '#E8F5E9', 'color' => '#1E7A47', 'label' => 'Traité'],
                                    'rejete' => ['bg' => '#FFEBEE', 'color' => '#C62828', 'label' => 'Rejeté'],
                                ];
                                
                                // ✅ Utiliser la valeur pour accéder au tableau
                                $colors = $statusColors[$statusValue] ?? $statusColors['en_attente'];
                            @endphp
                            <span class="status-pill" style="background:{{ $colors['bg'] }};color:{{ $colors['color'] }};border:1px solid {{ $colors['color'] }}20;">
                                <i class="fa-solid fa-circle" style="font-size:6px;"></i>
                                {{ $colors['label'] }}
                            </span>
                        </td>
                        <td>
                            <div style="font-size:12px;color:var(--text-soft);">
                                {{ $signalement->created_at->format('d/m/Y H:i') }}
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                                <a href="{{ route('admin.signalements.show', $signalement) }}" class="btn btn-sm btn-ghost" title="Voir">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if($statusValue === 'en_attente')
                                    <form action="{{ route('admin.signalements.traiter', $signalement) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="action" value="bloquer">
                                        <button type="submit" class="btn btn-sm" style="background:#C62828;color:#fff;border:none;padding:4px 8px;border-radius:6px;cursor:pointer;font-size:12px;" 
                                                onclick="return confirm('Bloquer définitivement cette agence suite au signalement ?')">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.signalements.traiter', $signalement) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="action" value="rejeter">
                                        <button type="submit" class="btn btn-sm" style="background:#1E7A47;color:#fff;border:none;padding:4px 8px;border-radius:6px;cursor:pointer;font-size:12px;" 
                                                onclick="return confirm('Rejeter ce signalement ?')">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                            <i class="fa-solid fa-flag" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                            Aucun signalement trouvé
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top:16px;">
        {{ $signalements->appends(request()->query())->links() }}
    </div>
@endsection

@push('styles')
<style>
    /* ===== TABLE ===== */
    .table-wrap {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: auto;
    }

    .table-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 900px;
    }

    .table-wrap th {
        padding: 12px 16px;
        text-align: left;
        background: #FAFBFC;
        border-bottom: 1px solid var(--border);
        font-weight: 600;
        color: var(--text-soft);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .table-wrap td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .table-wrap tr:last-child td {
        border-bottom: none;
    }

    .table-wrap tr:hover td {
        background: #F7F9FC;
    }

    /* ===== CELL ===== */
    .cell-main {
        font-weight: 600;
        font-size: 13px;
        color: var(--ink);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .cell-main i {
        color: var(--muted);
        font-size: 13px;
    }

    .cell-sub {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
    }

    /* ===== STATUS PILL ===== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    /* ===== META PILL ===== */
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 11px;
        background: var(--border);
        color: var(--text-soft);
    }

    /* ===== BOUTONS ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
    }

    .btn-sm {
        padding: 4px 10px;
        font-size: 11px;
        border-radius: 6px;
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

    /* ===== KPI ===== */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .kpi-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        text-align: center;
    }

    .kpi-value {
        font-family: var(--display);
        font-weight: 700;
        font-size: 28px;
        color: var(--ink);
    }

    .kpi-label {
        font-size: 13px;
        color: var(--muted);
        margin-top: 4px;
    }

    /* ===== PAGINATION ===== */
    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pagination li {
        display: inline;
    }

    .pagination a,
    .pagination span {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: 13px;
        transition: all 0.2s;
        min-width: 36px;
        text-align: center;
    }

    .pagination a:hover {
        background: var(--border);
    }

    .pagination .active span {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .kpi-value {
            font-size: 22px;
        }

        .table-wrap table {
            font-size: 12px;
            min-width: 750px;
        }

        .table-wrap th,
        .table-wrap td {
            padding: 8px 12px;
        }

        [style*="display:flex;gap:8px;flex-wrap:wrap;align-items:center;"] {
            flex-direction: column;
            align-items: stretch !important;
        }

        [style*="display:flex;gap:8px;margin-left:auto;"] {
            margin-left: 0 !important;
            width: 100%;
        }

        [style*="display:flex;gap:8px;margin-left:auto;"] input {
            min-width: unset !important;
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .kpi-card {
            padding: 10px 12px;
        }

        .kpi-value {
            font-size: 18px;
        }

        .btn-sm {
            font-size: 10px;
            padding: 3px 8px;
        }

        .table-wrap td,
        .table-wrap th {
            padding: 6px 8px;
            font-size: 11px;
        }

        .status-pill {
            font-size: 10px;
            padding: 2px 8px;
        }
    }
</style>
@endpush