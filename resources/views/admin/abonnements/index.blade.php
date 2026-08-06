@extends('layouts.admin')

@section('title', 'Gestion des abonnements — Administration DoyaImmo')
@section('page_title', 'Abonnements')
@section('page_sub', 'Gérez les abonnements des agences')

@section('content')
<style>
    @media (max-width: 768px) {
        .section-head {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 12px !important;
        }
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px !important;
        }
        .kpi-card {
            padding: 12px !important;
        }
        .kpi-value {
            font-size: 20px !important;
        }
        .table-wrap {
            overflow-x: auto !important;
        }
        .table-wrap table {
            min-width: 600px !important;
            font-size: 12px !important;
        }
        .table-wrap th,
        .table-wrap td {
            padding: 8px 10px !important;
        }
    }
    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr !important;
            gap: 8px !important;
        }
        .kpi-card {
            padding: 10px !important;
        }
        .kpi-value {
            font-size: 16px !important;
        }
        .kpi-label {
            font-size: 10px !important;
        }
    }
</style>

<div class="section-head">
    <div>
        <h2>Abonnements</h2>
        <p>{{ $abonnements->total() }} abonnements au total</p>
    </div>
    <div>
        <span style="font-size:13px;color:var(--muted);">
            <i class="fa-solid fa-info-circle"></i> Les agences souscrivent elles-mêmes
        </span>
    </div>
</div>

<!-- Statistiques -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="kpi-label">Total</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['actifs'] ?? 0 }}</div>
        <div class="kpi-label">Actifs</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['expires'] ?? 0 }}</div>
        <div class="kpi-label"> Expirés</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#0D47A1;">
            @foreach($stats['par_formule'] ?? [] as $item)
                @php
                    $label = is_object($item->formule) && method_exists($item->formule, 'label') 
                        ? $item->formule->label() 
                        : ucfirst($item->formule);
                @endphp
                {{ $label }}: {{ $item->total }}<br>
            @endforeach
        </div>
        <div class="kpi-label">Par formule</div>
    </div>
</div>

<!-- Table -->
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Agence</th>
                <th>Formule</th>
                <th>Montant</th>
                <th>Date début</th>
                <th>Date fin</th>
                <th>Statut</th>
                <th>Paiement</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($abonnements as $abonnement)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            @if($abonnement->agence && $abonnement->agence->logo)
                                <img src="{{ asset('storage/' . $abonnement->agence->logo) }}" 
                                     alt="{{ $abonnement->agence->nom_agence }}" 
                                     style="width:30px;height:30px;object-fit:cover;border-radius:50%;border:1px solid var(--border);">
                            @else
                                <div style="width:30px;height:30px;border-radius:50%;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--gold);">
                                    {{ $abonnement->agence ? Str::substr($abonnement->agence->nom_agence, 0, 2) : 'NA' }}
                                </div>
                            @endif
                            <div>
                                <div class="cell-main">{{ $abonnement->agence->nom_agence ?? 'N/A' }}</div>
                                <div class="cell-sub">{{ $abonnement->agence->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $formuleLabel = is_object($abonnement->formule) && method_exists($abonnement->formule, 'label') 
                                ? $abonnement->formule->label() 
                                : ucfirst($abonnement->formule);
                        @endphp
                        <span class="status-pill 
                            @if($formuleLabel === 'Pro') status-active
                            @elseif($formuleLabel === 'Premium') status-confirme
                            @else status-en_attente
                            @endif">
                            {{ $formuleLabel }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--rust);">
                            @if($abonnement->montant == 0)
                                Gratuit
                            @else
                                {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA
                            @endif
                        </div>
                    </td>
                    <td>{{ $abonnement->date_debut->format('d/m/Y') }}</td>
                    <td>
                        {{ $abonnement->date_fin->format('d/m/Y') }}
                        @if($abonnement->estExpirant(7))
                            <span class="status-pill status-warning" style="font-size:9px;">Expire bientôt</span>
                        @endif
                    </td>
                    <td>
                        @if($abonnement->estActif())
                            <span class="status-pill status-active"> Actif</span>
                        @else
                            <span class="status-pill status-inactif"> Expiré</span>
                        @endif
                    </td>
                    <td>
                        @if($abonnement->paydunya_status === 'paid')
                            <span class="status-pill status-active"> Payé</span>
                        @elseif($abonnement->paydunya_status === 'pending')
                            <span class="status-pill status-warning"> En attente</span>
                        @elseif($abonnement->paydunya_status === 'free')
                            <span class="status-pill status-active"> Gratuit</span>
                        @else
                            <span class="status-pill status-inactif"> Non payé</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                            <!-- Voir -->
                            <a href="{{ route('admin.abonnements.show', $abonnement) }}" class="btn btn-sm btn-ghost" title="Voir">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            
                            <!-- PAS DE BOUTON MODIFIER - l'admin ne peut pas modifier les caractéristiques -->
                            
                            <!-- Activer/Désactiver (Suspendre) -->
                            @if($abonnement->estActif())
                                <form action="{{ route('admin.abonnements.desactiver', $abonnement) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Suspendre/Désactiver" onclick="return confirm('Suspendre cet abonnement ?')">
                                        <i class="fa-solid fa-pause"></i>
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.abonnements.activer', $abonnement) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success" title="Réactiver" onclick="return confirm('Réactiver cet abonnement ?')">
                                        <i class="fa-solid fa-play"></i>
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Supprimer -->
                            <form action="{{ route('admin.abonnements.destroy', $abonnement) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer définitivement cet abonnement ?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-solid fa-award" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Aucun abonnement trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:20px;">
    {{ $abonnements->appends(request()->query())->links() }}
</div>
@endsection