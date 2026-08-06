@extends('layouts.admin')

@section('title', 'Gestion des propositions — Administration DoyaImmo')
@section('page_title', 'Propositions')
@section('page_sub', 'Consultez les propositions des agences')

@section('content')
<div class="section-head">
    <div>
        <h2>Propositions</h2>
        <p>{{ $propositions->total() }} propositions sur la plateforme</p>
    </div>
</div>

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
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['acceptees'] ?? 0 }}</div>
        <div class="kpi-label">Acceptées</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['refusees'] ?? 0 }}</div>
        <div class="kpi-label">Refusées</div>
    </div>
</div>

<!-- Filtres -->
<div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <a href="{{ route('admin.propositions.index') }}" class="btn btn-sm {{ !request('filtre') ? 'btn-rust' : 'btn-ghost' }}">Toutes</a>
    <a href="{{ route('admin.propositions.index', ['filtre' => 'en_attente']) }}" class="btn btn-sm {{ request('filtre') === 'en_attente' ? 'btn-rust' : 'btn-ghost' }}">En attente</a>
    <a href="{{ route('admin.propositions.index', ['filtre' => 'acceptee']) }}" class="btn btn-sm {{ request('filtre') === 'acceptee' ? 'btn-rust' : 'btn-ghost' }}">Acceptées</a>
    <a href="{{ route('admin.propositions.index', ['filtre' => 'refusee']) }}" class="btn btn-sm {{ request('filtre') === 'refusee' ? 'btn-rust' : 'btn-ghost' }}">Refusées</a>
    
    <!-- Recherche -->
    <form action="{{ route('admin.propositions.index') }}" method="GET" style="display:flex;gap:8px;margin-left:auto;">
        <input type="text" name="search" placeholder="Rechercher..." 
               value="{{ request('search') }}" 
               style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;min-width:200px;">
        <button type="submit" class="btn btn-sm btn-ghost">
            <i class="fa-solid fa-search"></i>
        </button>
        @if(request('search') || request('filtre'))
            <a href="{{ route('admin.propositions.index') }}" class="btn btn-sm btn-ghost">
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
                <th>Agence</th>
                <th>Demande</th>
                <th>Bien proposé</th>
                <th>Prix proposé</th>
                <th>Statut</th>
                <th>Date</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($propositions as $proposition)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            @if($proposition->agence && $proposition->agence->logo)
                                <img src="{{ asset('storage/' . $proposition->agence->logo) }}" 
                                     alt="{{ $proposition->agence->nom_agence }}" 
                                     style="width:32px;height:32px;object-fit:cover;border-radius:50%;border:1px solid var(--border);">
                            @else
                                <div style="width:32px;height:32px;border-radius:50%;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--gold);">
                                    {{ $proposition->agence ? Str::substr($proposition->agence->nom_agence, 0, 2) : 'NA' }}
                                </div>
                            @endif
                            <div>
                                <div class="cell-main">{{ $proposition->agence->nom_agence ?? 'N/A' }}</div>
                                <div class="cell-sub">{{ $proposition->agence->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="cell-main">
                                {{ $proposition->demande->type_operation_label ?? ($proposition->demande->type_operation ?? 'N/A') }}
                            </div>
                            <div class="cell-sub">
                                <i class="fa-solid fa-user"></i> {{ $proposition->demande->particulier->user->prenom ?? '' }} {{ $proposition->demande->particulier->user->nom ?? '' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $bien = $proposition->bien;
                            $titre = $bien->titre ?? 'N/A';
                            $contrat = is_object($bien->type_contrat) ? $bien->type_contrat->label() : ($bien->type_contrat ?? '');
                            $quartierNom = $bien->quartier_nom ?? 'N/A';
                        @endphp
                        <div>
                            <div class="cell-main">
                                {{ $titre }}
                                @if($contrat && $contrat !== 'N/A')
                                    <span class="meta-pill" style="font-size:10px;background:#E3F2FD;color:#0D47A1;">
                                        {{ $contrat }}
                                    </span>
                                @endif
                            </div>
                            <div class="cell-sub">
                                <i class="fa-solid fa-location-dot"></i> {{ $quartierNom }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-weight:600;color:var(--rust);">
                            {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                        </div>
                    </td>
                    <td>
                        <span class="status-pill status-{{ $proposition->statut }}">
                            {{ $proposition->statut_label ?? $proposition->statut }}
                        </span>
                    </td>
                    <td>{{ $proposition->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.propositions.show', $proposition) }}" class="btn btn-sm btn-ghost" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-solid fa-handshake" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Aucune proposition trouvée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:20px;">
    {{ $propositions->appends(request()->query())->links() }}
</div>
@endsection