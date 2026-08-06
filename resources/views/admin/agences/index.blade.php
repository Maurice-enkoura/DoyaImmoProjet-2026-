@extends('layouts.admin')

@section('title', 'Gestion des agences — Administration DoyaImmo')
@section('page_title', 'Gestion des agences')
@section('page_sub', 'Validez et gérez les comptes agences')

@section('content')
<div class="section-head">
    <div>
        <h2>Agences</h2>
        <p>{{ $agences->total() }} agences inscrites sur la plateforme</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="{{ route('admin.agences.index') }}" class="btn btn-sm {{ !request('filtre') ? 'btn-rust' : 'btn-ghost' }}">Toutes</a>
        <a href="{{ route('admin.agences.index', ['filtre' => 'en_attente']) }}" class="btn btn-sm {{ request('filtre') === 'en_attente' ? 'btn-rust' : 'btn-ghost' }}">En attente</a>
        <a href="{{ route('admin.agences.index', ['filtre' => 'validees']) }}" class="btn btn-sm {{ request('filtre') === 'validees' ? 'btn-rust' : 'btn-ghost' }}">Validées</a>
        <a href="{{ route('admin.agences.index', ['filtre' => 'bloquees']) }}" class="btn btn-sm {{ request('filtre') === 'bloquees' ? 'btn-rust' : 'btn-ghost' }}">Bloquées</a>
    </div>
</div>

<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Agence</th>
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
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if($agence->logo)
                                <img src="{{ asset('storage/' . $agence->logo) }}" 
                                     alt="{{ $agence->nom_agence }}" 
                                     style="width:40px;height:40px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
                            @else
                                <div style="width:40px;height:40px;background:var(--gold-soft);border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:var(--gold);">
                                    {{ Str::substr($agence->nom_agence, 0, 2) }}
                                </div>
                            @endif
                            <div>
                                <div class="cell-main">{{ $agence->nom_agence }}</div>
                                <div class="cell-sub">
                                    <i class="fa-solid fa-user"></i> {{ $agence->user->prenom ?? '' }} {{ $agence->user->nom ?? '' }}
                                </div>
                                <div class="cell-sub">
                                    <i class="fa-solid fa-calendar"></i> Inscrite le {{ $agence->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $quartierNom = 'Non défini';
                            if (is_object($agence->quartier) && method_exists($agence->quartier, 'getAttribute')) {
                                $quartierNom = $agence->quartier->nom ?? 'Non défini';
                            } elseif (is_string($agence->quartier) && !empty($agence->quartier)) {
                                $quartierNom = $agence->quartier;
                            } elseif (is_numeric($agence->quartier_id) && $agence->quartier_id > 0) {
                                $quartier = App\Models\Quartier::find($agence->quartier_id);
                                if ($quartier) {
                                    $quartierNom = $quartier->nom;
                                }
                            }
                        @endphp
                        {{ $quartierNom }}
                    </td>
                    <td>
                        @php
                            $totalDocs = $agence->documents->count();
                            $valides = $agence->documents->where('statut_validation', 'valide')->count();
                            $enAttente = $agence->documents->where('statut_validation', 'en_attente')->count();
                            $rejetes = $agence->documents->where('statut_validation', 'rejete')->count();
                        @endphp
                        <div style="font-size:12px;">
                            <div>Total: <strong>{{ $totalDocs }}</strong></div>
                            @if($valides > 0)
                                <div style="color:var(--green);"> {{ $valides }} validés</div>
                            @endif
                            @if($enAttente > 0)
                                <div style="color:#E65100;">{{ $enAttente }} en attente</div>
                            @endif
                            @if($rejetes > 0)
                                <div style="color:var(--red);">{{ $rejetes }} rejetés</div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <span style="font-weight:600;">{{ $agence->biens->count() }}</span>
                        <span style="font-size:11px;color:var(--muted);">biens</span>
                    </td>
                    <td>
                        @if(!$agence->statut_validation)
                            <span class="status-pill status-en_attente">En attente</span>
                        @elseif(isset($agence->bloque) && $agence->bloque)
                            <span class="status-pill status-annule">Bloquée</span>
                        @else
                            <span class="status-pill status-active">Active</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                            <a href="{{ route('admin.agences.show', $agence) }}" class="btn btn-sm btn-ghost" title="Voir">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.agences.documents', $agence) }}" class="btn btn-sm btn-ghost" title="Documents">
                                <i class="fa-solid fa-file"></i>
                            </a>
                            @if(!$agence->statut_validation)
                                <form action="{{ route('admin.agences.valider', $agence) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Valider" onclick="return confirm('Valider cette agence ?')">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            @endif
                            @if(isset($agence->bloque) && $agence->bloque)
                                <form action="{{ route('admin.agences.debloquer', $agence) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Débloquer" onclick="return confirm('Débloquer cette agence ?')">
                                        <i class="fa-solid fa-unlock"></i>
                                    </button>
                                </form>
                            @elseif($agence->statut_validation)
                                <form action="{{ route('admin.agences.bloquer', $agence) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger" title="Bloquer" onclick="return confirm('Bloquer cette agence ?')">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('admin.agences.destroy', $agence) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer définitivement cette agence ?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-solid fa-building" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Aucune agence trouvée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:20px;">
    {{ $agences->appends(request()->query())->links() }}
</div>
@endsection