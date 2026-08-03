@extends('layouts.dashboard')

@section('title', 'Offres reçues — DoyaImmo')
@section('page_title', 'Offres reçues')
@section('page_sub', 'Comparez les propositions des agences pour chacun de vos besoins')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Offres reçues</h2>
            <p>Comparez les propositions des agences pour chacun de vos besoins</p>
        </div>
    </div>

    @forelse($propositions as $proposition)
        <div class="compare-wrap" style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:24px;">
            <div class="compare-head" style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                <div>
                    <h3 style="font-family:var(--display);font-size:16px;">
                        {{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}
                    </h3>
                    <div class="sub" style="font-size:12.5px;color:var(--muted);">
                        Budget max : {{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }} F/mois
                    </div>
                </div>
                <span class="status-pill status-{{ $proposition->statut->value === 'en_attente' ? 'attente' : ($proposition->statut->value === 'acceptee' ? 'acceptee' : 'visite-prog') }}">
                    <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                    {{ $proposition->statut->label() }}
                </span>
            </div>
            <div style="padding:16px 20px;">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <div>
                        <div style="font-weight:600;font-size:15px;">
                            {{ $proposition->agence->nom_agence }}
                        </div>
                        <div style="font-size:13px;color:var(--muted);">
                            <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                            {{ number_format($proposition->agence->note_moyenne, 1) }} / 5
                            ({{ $proposition->agence->evaluations->count() }} avis)
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700;color:var(--rust);font-size:18px;">
                            {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            {{ $proposition->bien->titre ?? 'Bien' }}
                        </div>
                    </div>
                </div>
                <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="meta-pill"><i class="fa-solid fa-vector-square"></i> {{ $proposition->bien->surface }} m²</span>
                    <span class="meta-pill"><i class="fa-solid fa-bed"></i> {{ $proposition->bien->nombre_chambres }} ch.</span>
                    <span class="meta-pill"><i class="fa-solid fa-bath"></i> {{ $proposition->bien->nombre_salles_bain }} sdb</span>
                    @if($proposition->bien->parking_disponible)
                        <span class="meta-pill"><i class="fa-solid fa-car"></i> Parking</span>
                    @endif
                </div>
                <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
                    @if($proposition->statut->value === 'en_attente')
                        <form action="{{ route('particulier.propositions.selectionner', $proposition) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-rust btn-sm">
                                <i class="fa-solid fa-check"></i> Choisir cette agence
                            </button>
                        </form>
                        <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-calendar"></i> Planifier une visite
                        </a>
                    @elseif($proposition->statut->value === 'acceptee')
                        <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-rust btn-sm">
                            <i class="fa-solid fa-calendar-check"></i> Planifier la visite
                        </a>
                    @endif
                    <a href="{{ route('particulier.propositions.show', $proposition) }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-eye"></i> Détails
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:60px 20px;color:var(--muted);">
            <i class="fa-solid fa-inbox" style="font-size:40px;display:block;margin-bottom:16px;"></i>
            <p style="font-size:16px;">Vous n'avez pas encore reçu d'offres.</p>
            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust" style="margin-top:16px;">
                Publier un besoin
            </a>
        </div>
    @endforelse

    <div style="margin-top:30px;">
        {{ $propositions->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-acceptee {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-refusee {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-visite-prog {
        background: #E3F2FD;
        color: #0D47A1;
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
</style>
@endpush 