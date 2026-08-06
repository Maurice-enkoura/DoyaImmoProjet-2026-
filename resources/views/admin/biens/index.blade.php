@extends('layouts.admin')

@section('title', 'Gestion des biens - DoyaImmo')
@section('page_title', 'Biens immobiliers')
@section('page_sub', 'Gestion des biens publiés sur la plateforme')

@section('content')
<!-- Statistiques -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="kpi-label">Total des biens</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['disponibles'] ?? 0 }}</div>
        <div class="kpi-label">Disponibles</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['indisponibles'] ?? 0 }}</div>
        <div class="kpi-label">Indisponibles</div>
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

    <!-- Recherche -->
    <form action="{{ route('admin.biens.index') }}" method="GET" style="display:flex;gap:8px;margin-left:auto;">
        <input type="text" name="search" placeholder="Rechercher un bien..."
            value="{{ request('search') }}"
            style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;">
        <button type="submit" class="btn btn-sm btn-ghost">
            <i class="fa-solid fa-search"></i>
        </button>
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
                            <div class="cell-main">{{ Str::limit($bien->titre, 40) }}</div>
                            <div class="cell-sub">
                                @php
                                // Déterminer le nom du quartier
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
                    <span class="meta-pill">{{ $bien->type_bien ?? 'N/A' }}</span>
                    <span class="meta-pill" style="background:#E3F2FD;color:#0D47A1;">
                        {{ $bien->type_contrat ?? 'N/A' }}
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
                </td>
                <td style="text-align:center;">
                    <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                        <a href="{{ route('admin.biens.show', $bien) }}" class="btn btn-sm btn-ghost" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @if($bien->statut)
                        <form action="{{ route('admin.biens.desactiver', $bien) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" title="Désactiver" onclick="return confirm('Désactiver ce bien ?')">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.biens.activer', $bien) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" title="Activer" onclick="return confirm('Activer ce bien ?')">
                                <i class="fa-solid fa-check"></i>
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('admin.biens.destroy', $bien) }}" method="POST" style="display:inline;">
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

<div style="margin-top:16px;">
    {{ $biens->links() ?? '' }}
</div>
@endsection