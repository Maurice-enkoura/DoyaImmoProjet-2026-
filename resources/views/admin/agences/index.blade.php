@extends('layouts.admin')

@section('title', 'Agences — Administration DoyaImmo')
@section('page_title', 'Agences')
@section('page_sub', 'Gérez les agences immobilières inscrites sur la plateforme')

@section('content')
<div class="view active">
    <!-- Filtres -->
    <div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
        <a href="{{ route('admin.agences.index') }}" class="btn btn-ghost btn-sm {{ !request('filtre') ? 'active' : '' }}">
            <i class="fa-solid fa-list"></i> Toutes
        </a>
        <a href="{{ route('admin.agences.index', ['filtre' => 'en_attente']) }}" class="btn btn-ghost btn-sm {{ request('filtre') === 'en_attente' ? 'active' : '' }}">
            <i class="fa-solid fa-clock"></i> En attente
            @php
                $enAttenteCount = App\Models\Agence::where('statut_validation', false)->where('est_refusee', false)->count();
            @endphp
            @if($enAttenteCount > 0)
                <span style="background:#E65100;color:#fff;padding:1px 8px;border-radius:999px;font-size:10px;">{{ $enAttenteCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.agences.index', ['filtre' => 'validees']) }}" class="btn btn-ghost btn-sm {{ request('filtre') === 'validees' ? 'active' : '' }}">
            <i class="fa-solid fa-check-circle"></i> Validées
            @php
                $valideesCount = App\Models\Agence::where('statut_validation', true)->count();
            @endphp
            @if($valideesCount > 0)
                <span style="background:#1E7A47;color:#fff;padding:1px 8px;border-radius:999px;font-size:10px;">{{ $valideesCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.agences.index', ['filtre' => 'refusees']) }}" class="btn btn-ghost btn-sm {{ request('filtre') === 'refusees' ? 'active' : '' }}">
            <i class="fa-solid fa-times-circle"></i> Refusées
            @php
                $refuseesCount = App\Models\Agence::where('est_refusee', true)->count();
            @endphp
            @if($refuseesCount > 0)
                <span style="background:#C62828;color:#fff;padding:1px 8px;border-radius:999px;font-size:10px;">{{ $refuseesCount }}</span>
            @endif
        </a>
        <a href="{{ route('admin.agences.index', ['filtre' => 'bloquees']) }}" class="btn btn-ghost btn-sm {{ request('filtre') === 'bloquees' ? 'active' : '' }}">
            <i class="fa-solid fa-ban"></i> Bloquées
            @php
                $bloqueesCount = App\Models\Agence::where('bloque', true)->count();
            @endphp
            @if($bloqueesCount > 0)
                <span style="background:#C62828;color:#fff;padding:1px 8px;border-radius:999px;font-size:10px;">{{ $bloqueesCount }}</span>
            @endif
        </a>
    </div>

    <!-- Statistiques rapides -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px;">
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:12px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:700;color:var(--ink);">{{ App\Models\Agence::count() }}</div>
            <div style="font-size:12px;color:var(--muted);">Total agences</div>
        </div>
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:12px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#E65100;">{{ App\Models\Agence::where('statut_validation', false)->where('est_refusee', false)->count() }}</div>
            <div style="font-size:12px;color:var(--muted);">En attente</div>
        </div>
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:12px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#1E7A47;">{{ App\Models\Agence::where('statut_validation', true)->count() }}</div>
            <div style="font-size:12px;color:var(--muted);">Validées</div>
        </div>
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:12px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#C62828;">{{ App\Models\Agence::where('est_refusee', true)->count() }}</div>
            <div style="font-size:12px;color:var(--muted);">Refusées</div>
        </div>
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:12px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:700;color:#C62828;">{{ App\Models\Agence::where('bloque', true)->count() }}</div>
            <div style="font-size:12px;color:var(--muted);">Bloquées</div>
        </div>
    </div>

    <!-- Tableau des agences -->
    <div class="panel">
        <div style="padding:12px 16px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
            <span style="font-size:13px;color:var(--muted);">
                <i class="fa-solid fa-building"></i> 
                {{ $agences->total() }} agence(s) trouvée(s)
            </span>
            <div style="display:flex;gap:8px;">
                <form action="{{ route('admin.agences.index') }}" method="GET" style="display:flex;gap:8px;align-items:center;">
                    <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}" 
                           style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
                    <button type="submit" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Agence</th>
                        <th>Propriétaire</th>
                        <th>Quartier</th>
                        <th>Documents</th>
                        <th>Biens</th>
                        <th>Statut</th>
                        <th style="text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($agences as $agence)
                        <tr>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    @if($agence->logo)
                                        <img src="{{ asset('storage/' . $agence->logo) }}" 
                                             alt="{{ $agence->nom_agence }}" 
                                             style="width:36px;height:36px;object-fit:cover;border-radius:50%;border:2px solid var(--border);">
                                    @else
                                        <div style="width:36px;height:36px;border-radius:50%;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;color:var(--gold);">
                                            {{ strtoupper(substr($agence->nom_agence, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="cell-main">{{ $agence->nom_agence }}</div>
                                        <div class="cell-sub">
                                            <i class="fa-regular fa-calendar"></i> Inscrite le {{ $agence->created_at->format('d/m/Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="cell-main">{{ $agence->user->prenom ?? '' }} {{ $agence->user->nom ?? '' }}</div>
                                <div class="cell-sub">{{ $agence->user->email ?? '' }}</div>
                            </td>
                            <td>
                                @php
                                    $quartierNom = 'N/A';
                                    if ($agence->quartier) {
                                        $quartierNom = is_object($agence->quartier) ? ($agence->quartier->nom ?? 'N/A') : $agence->quartier;
                                    }
                                @endphp
                                {{ $quartierNom }}
                            </td>
                            <td>
                                @php
                                    $totalDocs = $agence->documents->count();
                                    $enAttenteDocs = $agence->documents->where('statut_validation', 'en_attente')->count();
                                    $validesDocs = $agence->documents->where('statut_validation', 'valide')->count();
                                    $rejetesDocs = $agence->documents->where('statut_validation', 'rejete')->count();
                                @endphp
                                <div>
                                    <span style="font-weight:600;">{{ $totalDocs }}</span>
                                    @if($validesDocs > 0)
                                        <span style="color:#1E7A47;font-size:12px;"> {{ $validesDocs }}</span>
                                    @endif
                                    @if($enAttenteDocs > 0)
                                        <span style="color:#E65100;font-size:12px;"> {{ $enAttenteDocs }}</span>
                                    @endif
                                    @if($rejetesDocs > 0)
                                        <span style="color:#C62828;font-size:12px;"> {{ $rejetesDocs }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span style="font-weight:600;">{{ $agence->biens->count() }}</span>
                            </td>
                            <td>
                                @php
                                    // Déterminer le statut
                                    if ($agence->bloque) {
                                        $statutLabel = 'Bloquée';
                                        $statutClass = 'status-annule';
                                        $statutIcon = 'fa-ban';
                                    } elseif ($agence->est_refusee) {
                                        $statutLabel = 'Refusée';
                                        $statutClass = 'status-refusee';
                                        $statutIcon = 'fa-times-circle';
                                    } elseif ($agence->statut_validation) {
                                        $statutLabel = 'Validée';
                                        $statutClass = 'status-active';
                                        $statutIcon = 'fa-check-circle';
                                    } else {
                                        $statutLabel = 'En attente';
                                        $statutClass = 'status-en_attente';
                                        $statutIcon = 'fa-clock';
                                    }
                                @endphp
                                <span class="status-pill {{ $statutClass }}">
                                    <i class="fa-solid {{ $statutIcon }}" style="font-size:8px;"></i>
                                    {{ $statutLabel }}
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:3px;justify-content:center;flex-wrap:wrap;">
                                    {{-- Voir --}}
                                    <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                    <a href="{{ route('admin.agences.show', $agence->slug) }}" class="btn btn-sm btn-ghost" title="Voir le détail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    
                                    {{-- Boutons selon le statut --}}
                                    @if(!$agence->statut_validation && !$agence->est_refusee && !$agence->bloque)
                                        {{-- En attente : Valider / Refuser --}}
                                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                        <form action="{{ route('admin.agences.valider', $agence->slug) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Valider cette agence" onclick="return confirm('Valider cette agence ?')">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                        <form action="{{ route('admin.agences.refuser', $agence->slug) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="motif" value="Documents non conformes">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Refuser cette agence" onclick="return confirm('Refuser cette agence ?')">
                                                <i class="fa-solid fa-times"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($agence->est_refusee)
                                        {{-- Refusée : Réactiver --}}
                                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                        <form action="{{ route('admin.agences.reactiver', $agence->slug) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Réactiver cette agence" onclick="return confirm('Réactiver cette agence ? Elle sera remise en attente de validation.')">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($agence->statut_validation && !$agence->bloque)
                                        {{-- Validée : Bloquer --}}
                                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                        <form action="{{ route('admin.agences.bloquer', $agence->slug) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger" title="Bloquer cette agence" onclick="return confirm('Bloquer cette agence ?')">
                                                <i class="fa-solid fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif

                                    @if($agence->bloque)
                                        {{-- Bloquée : Débloquer --}}
                                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                        <form action="{{ route('admin.agences.debloquer', $agence->slug) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="Débloquer cette agence" onclick="return confirm('Débloquer cette agence ?')">
                                                <i class="fa-solid fa-unlock"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Documents --}}
                                    <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                    <a href="{{ route('admin.agences.documents', $agence->slug) }}" class="btn btn-sm btn-ghost" title="Voir les documents">
                                        <i class="fa-solid fa-file"></i>
                                    </a>

                                    {{-- Supprimer --}}
                                    <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                    <form action="{{ route('admin.agences.destroy', $agence->slug) }}" method="POST" style="display:inline;" onsubmit="return confirm('⚠️ Supprimer définitivement cette agence ? Cette action est irréversible.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer définitivement">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:60px 20px;color:var(--muted);">
                                <i class="fa-solid fa-building" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.3;"></i>
                                <p style="font-size:16px;font-weight:600;color:var(--text-soft);">Aucune agence trouvée</p>
                                @if(request('filtre') || request('search'))
                                    <p style="font-size:13px;">Aucune agence ne correspond à vos critères de recherche.</p>
                                    <a href="{{ route('admin.agences.index') }}" class="btn btn-ghost btn-sm" style="margin-top:8px;">
                                        <i class="fa-solid fa-rotate-left"></i> Réinitialiser les filtres
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="padding:12px 16px;border-top:1px solid var(--border);">
            {{ $agences->links() }}
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-active {
        background: #E8F5E9;
        color: #1E7A47;
    }

    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }

    .status-refusee {
        background: #FFEBEE;
        color: #C62828;
    }

    .status-annule {
        background: #FFEBEE;
        color: #C62828;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        min-height: 28px;
        min-width: 28px;
        justify-content: center;
    }

    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }

    .btn-ghost:hover {
        background: var(--border);
    }

    .btn-ghost.active {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .btn-sm {
        padding: 3px 8px;
        font-size: 11px;
        min-height: 26px;
        min-width: 26px;
    }

    .btn-success {
        background: #1E7A47;
        color: #fff;
        border-color: #1E7A47;
    }

    .btn-success:hover {
        background: #145A35;
        color: #fff;
    }

    .btn-danger {
        background: #C62828;
        color: #fff;
        border-color: #C62828;
    }

    .btn-danger:hover {
        background: #B71C1C;
        color: #fff;
    }

    .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .table-wrap {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 700px;
    }

    .table-wrap thead {
        background: #FAFBFC;
        border-bottom: 1px solid var(--border);
    }

    .table-wrap th {
        padding: 10px 14px;
        text-align: left;
        font-weight: 600;
        color: var(--muted);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table-wrap td {
        padding: 10px 14px;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
    }

    .table-wrap tbody tr:hover {
        background: #F7F9FC;
    }

    .table-wrap tbody tr:last-child td {
        border-bottom: none;
    }

    .cell-main {
        font-weight: 500;
        color: var(--ink);
    }

    .cell-sub {
        font-size: 12px;
        color: var(--muted);
    }

    .pagination {
        display: flex;
        gap: 4px;
        justify-content: center;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pagination li {
        display: inline;
    }

    .pagination a, .pagination span {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
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

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .table-wrap table {
            font-size: 12px;
            min-width: 600px;
        }
        .table-wrap th,
        .table-wrap td {
            padding: 6px 10px;
        }
        .btn-sm {
            font-size: 10px;
            padding: 2px 6px;
            min-height: 22px;
            min-width: 22px;
        }
        .status-pill {
            font-size: 10px;
            padding: 2px 8px;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));"] {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }

    @media (max-width: 480px) {
        .table-wrap table {
            font-size: 11px;
            min-width: 500px;
        }
        .table-wrap th,
        .table-wrap td {
            padding: 4px 8px;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));"] {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        .btn-sm {
            font-size: 9px;
            padding: 2px 4px;
            min-height: 20px;
            min-width: 20px;
        }
        .status-pill {
            font-size: 9px;
            padding: 1px 6px;
        }
    }
</style>
@endpush