@extends('layouts.dashboard')

@section('title', 'Offres reçues — DoyaImmo')
@section('page_title', 'Offres reçues')
@section('page_sub', 'Consultez et gérez les propositions des agences')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Offres reçues</h2>
            <p>Consultez et gérez les propositions des agences pour vos besoins actifs</p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('particulier.historique') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-clock-rotate-left"></i> Voir historique
            </a>
        </div>
    </div>

    @forelse($propositions as $proposition)
        @php
            $statutValue = $proposition->statut_value;
            $statutLabel = $proposition->statut_label;
            $statutClass = match($statutValue) {
                'en_attente' => 'attente',
                'acceptee' => 'acceptee',
                default => 'attente'
            };
        @endphp
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
                <span class="status-pill status-{{ $statutClass }}">
                    <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                    {{ $statutLabel }}
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
                    {{-- ✅ Pour les propositions en attente --}}
                    @if($statutValue === 'en_attente')
                        <form action="{{ route('particulier.propositions.selectionner', $proposition) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-rust btn-sm">
                                <i class="fa-solid fa-check"></i> Choisir cette agence
                            </button>
                        </form>
                        <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-calendar"></i> Planifier une visite
                        </a>
                    @endif

                    {{-- ✅ Pour les propositions acceptées --}}
                    @if($statutValue === 'acceptee')
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
            <p style="font-size:16px;">Aucune offre en attente pour vos besoins actifs.</p>
            <p style="font-size:13px;color:var(--muted);">Les offres refusées ou terminées sont disponibles dans l'historique.</p>
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
    .status-terminee {
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
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11.5px;
        color: var(--text-soft);
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
    }
    .btn-rust {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }
    .btn-rust:hover {
        background: #9A4523;
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
        padding: 4px 12px;
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .compare-head {
            flex-direction: column;
            align-items: stretch !important;
        }
        .compare-head .status-pill {
            align-self: flex-start;
        }
        [style*="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;"] {
            flex-direction: column;
            align-items: stretch !important;
            text-align: left !important;
        }
        [style*="text-align:right;"] {
            text-align: left !important;
        }
        [style*="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;"] {
            flex-direction: column;
            align-items: stretch !important;
        }
        [style*="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;"] .btn {
            justify-content: center !important;
        }
        [style*="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;"] {
            justify-content: center !important;
        }
    }

    @media (max-width: 480px) {
        .status-pill {
            font-size: 11px;
            padding: 3px 10px;
        }
        .meta-pill {
            font-size: 10px;
            padding: 1px 8px;
        }
        .btn-sm {
            font-size: 11px;
            padding: 4px 10px;
        }
    }
</style>
@endpush