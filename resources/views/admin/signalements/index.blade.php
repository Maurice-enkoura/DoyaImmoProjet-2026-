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
                                @if($signalement->signalable_type === 'App\\Models\\BienImmobilier')
                                    <i class="fa-solid fa-house"></i>
                                @elseif($signalement->signalable_type === 'App\\Models\\DemandeImmobiliere')
                                    <i class="fa-solid fa-file"></i>
                                @else
                                    <i class="fa-solid fa-building"></i>
                                @endif
                                {{ class_basename($signalement->signalable_type) }}
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
                        <td><span class="meta-pill">{{ $signalement->motif_label ?? $signalement->motif }}</span></td>
                        <td>
                            <span class="status-pill status-{{ $signalement->statut }}">
                                {{ $signalement->statut_label ?? $signalement->statut }}
                            </span>
                        </td>
                        <td>{{ $signalement->created_at->format('d/m/Y H:i') }}</td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                                <a href="{{ route('admin.signalements.show', $signalement) }}" class="btn btn-sm btn-ghost" title="Voir">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if($signalement->statut === 'en_attente')
                                    <form action="{{ route('admin.signalements.traiter', $signalement) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="action" value="bloquer">
                                        <button type="submit" class="btn btn-sm btn-danger" title="Bloquer l'agence" onclick="return confirm('Bloquer définitivement cette agence suite au signalement ?')">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.signalements.traiter', $signalement) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="action" value="rejeter">
                                        <button type="submit" class="btn btn-sm btn-success" title="Rejeter le signalement" onclick="return confirm('Rejeter ce signalement ?')">
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

    <div style="margin-top:16px;">
        {{ $signalements->appends(request()->query())->links() }}
    </div>
@endsection