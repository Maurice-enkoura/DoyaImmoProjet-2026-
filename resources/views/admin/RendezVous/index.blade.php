@extends('layouts.admin')

@section('title', 'Gestion des rendez-vous — Administration DoyaImmo')
@section('page_title', 'Rendez-vous')
@section('page_sub', 'Consultez les rendez-vous programmés')

@section('content')
<style>
    .statut-planifie { background: #FFF8E1; color: #E65100; }
    .statut-confirme { background: #E3F2FD; color: #0D47A1; }
    .statut-termine { background: #E8F5E9; color: #1E7A47; }
    .statut-annule { background: #FFEBEE; color: #C62828; }
</style>

<div class="section-head">
    <div>
        <h2>Rendez-vous</h2>
        <p>{{ $rendezVous->total() }} rendez-vous sur la plateforme</p>
    </div>
</div>

<!-- Statistiques -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="kpi-label">Total</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#E65100;">{{ $stats['planifies'] ?? 0 }}</div>
        <div class="kpi-label">Planifiés</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#0D47A1;">{{ $stats['confirmes'] ?? 0 }}</div>
        <div class="kpi-label"> Confirmés</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['termines'] ?? 0 }}</div>
        <div class="kpi-label"> Terminés</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['annules'] ?? 0 }}</div>
        <div class="kpi-label"> Annulés</div>
    </div>
</div>

<!-- Filtres -->
<div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <a href="{{ route('admin.rendezvous.index') }}" class="btn btn-sm {{ !request('filtre') ? 'btn-rust' : 'btn-ghost' }}">Tous</a>
    <a href="{{ route('admin.rendezvous.index', ['filtre' => 'planifie']) }}" class="btn btn-sm {{ request('filtre') === 'planifie' ? 'btn-rust' : 'btn-ghost' }}">Planifiés</a>
    <a href="{{ route('admin.rendezvous.index', ['filtre' => 'confirme']) }}" class="btn btn-sm {{ request('filtre') === 'confirme' ? 'btn-rust' : 'btn-ghost' }}">Confirmés</a>
    <a href="{{ route('admin.rendezvous.index', ['filtre' => 'termine']) }}" class="btn btn-sm {{ request('filtre') === 'termine' ? 'btn-rust' : 'btn-ghost' }}">Terminés</a>
    <a href="{{ route('admin.rendezvous.index', ['filtre' => 'annule']) }}" class="btn btn-sm {{ request('filtre') === 'annule' ? 'btn-rust' : 'btn-ghost' }}">Annulés</a>
    
    <!-- Recherche -->
    <form action="{{ route('admin.rendezvous.index') }}" method="GET" style="display:flex;gap:8px;margin-left:auto;">
        <input type="text" name="search" placeholder="Rechercher..." 
               value="{{ request('search') }}" 
               style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;min-width:200px;">
        <button type="submit" class="btn btn-sm btn-ghost">
            <i class="fa-solid fa-search"></i>
        </button>
        @if(request('search') || request('filtre'))
            <a href="{{ route('admin.rendezvous.index') }}" class="btn btn-sm btn-ghost">
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
                <th>Particulier</th>
                <th>Bien / Proposition</th>
                <th>Date & Heure</th>
                <th>Statut</th>
                <th>Créé le</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rendezVous as $rdv)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            @if($rdv->agence && $rdv->agence->logo)
                                <img src="{{ asset('storage/' . $rdv->agence->logo) }}" 
                                     alt="{{ $rdv->agence->nom_agence }}" 
                                     style="width:32px;height:32px;object-fit:cover;border-radius:50%;border:1px solid var(--border);">
                            @else
                                <div style="width:32px;height:32px;border-radius:50%;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:var(--gold);">
                                    {{ $rdv->agence ? Str::substr($rdv->agence->nom_agence, 0, 2) : 'NA' }}
                                </div>
                            @endif
                            <div>
                                <div class="cell-main">{{ $rdv->agence->nom_agence ?? 'N/A' }}</div>
                                <div class="cell-sub">{{ $rdv->agence->user->email ?? '' }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="cell-main">{{ $rdv->particulier->user->prenom ?? '' }} {{ $rdv->particulier->user->nom ?? '' }}</div>
                            <div class="cell-sub">{{ $rdv->particulier->user->email ?? '' }}</div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div class="cell-main">{{ $rdv->proposition->bien->titre ?? 'N/A' }}</div>
                            <div class="cell-sub">
                                <i class="fa-solid fa-tag"></i> 
                                {{ number_format($rdv->proposition->prix_propose ?? 0, 0, ',', ' ') }} FCFA
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            <div style="font-weight:600;font-size:14px;">
                                {{ $rdv->date_visite ? \Carbon\Carbon::parse($rdv->date_visite)->format('d/m/Y') : 'N/A' }}
                            </div>
                            <div style="font-size:12px;color:var(--muted);">
                                <i class="fa-solid fa-clock"></i> 
                                {{ $rdv->heure_visite ?? $rdv->creneau->heure_debut ?? 'N/A' }}
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="status-pill status-{{ $rdv->statut }}">
                            {{ $rdv->statut_label ?? $rdv->statut }}
                        </span>
                    </td>
                    <td>{{ $rdv->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <a href="{{ route('admin.rendezvous.show', $rdv) }}" class="btn btn-sm btn-ghost" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-solid fa-calendar-xmark" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Aucun rendez-vous trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:20px;">
    {{ $rendezVous->appends(request()->query())->links() }}
</div>
@endsection