@extends('layouts.dashboard')

@section('title', 'Mes rendez-vous — DoyaImmo')
@section('page_title', 'Mes rendez-vous')
@section('page_sub', 'Vos visites planifiées avec les agences')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mes rendez-vous</h2>
            <p>Vos visites planifiées avec les agences</p>
        </div>
    </div>

    @if($rendezVous->count() > 0)
        <!-- Statistiques rapides -->
        
        <!-- Liste des rendez-vous -->
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
            @foreach($rendezVous as $rdv)
                <div style="display:flex;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;transition:background 0.2s;">
                    
                    <!-- Date -->
                    <div style="display:flex;flex-direction:column;align-items:center;background:#F7F9FC;border-radius:10px;padding:6px 14px;min-width:56px;flex-shrink:0;">
                        <span style="font-family:var(--display);font-weight:700;font-size:20px;line-height:1.2;color:var(--ink);">
                            {{ $rdv->date_visite->format('d') }}
                        </span>
                        <span style="font-size:10px;text-transform:uppercase;color:var(--muted);">
                            {{ $rdv->date_visite->format('M') }}
                        </span>
                    </div>

                    <!-- Informations -->
                    <div style="flex:1;min-width:150px;">
                        <div style="font-weight:600;font-size:15px;color:var(--ink);">
                            {{ $rdv->proposition->bien->titre ?? 'Visite' }}
                            @if($rdv->proposition->bien)
                                <span style="font-weight:400;font-size:13px;color:var(--muted);">
                                    — {{ $rdv->proposition->bien->quartier }}
                                </span>
                            @endif
                        </div>
                        <div style="font-size:13px;color:var(--muted);">
                            <i class="fa-regular fa-building" style="margin-right:4px;"></i>
                            {{ $rdv->agence->nom_agence }}
                        </div>
                        <div style="font-size:13px;color:var(--muted);">
                            <i class="fa-regular fa-clock" style="margin-right:4px;"></i>
                            
                            {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                            @if($rdv->proposition->bien)
                                · Visite du bien
                            @endif
                        </div>
                    </div>

                    <!-- Statut -->
                    <div style="flex-shrink:0;">
                        <span class="status-pill status-{{ $rdv->statut->value }}">
                            <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                            {{ $rdv->statut->label() }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div style="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;">
                        @if($rdv->statut->value === 'planifie')
                            <form action="{{ route('particulier.rendezvous.confirmer', $rdv) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-rust btn-sm">
                                    <i class="fa-solid fa-check"></i> Confirmer
                                </button>
                            </form>
                            <form action="{{ route('particulier.rendezvous.annuler', $rdv) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm" onclick="return confirm('Annuler ce rendez-vous ?')">
                                    <i class="fa-solid fa-xmark"></i> Annuler
                                </button>
                            </form>
                        @elseif($rdv->statut->value === 'confirme')
                            <form action="{{ route('particulier.rendezvous.annuler', $rdv) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm" onclick="return confirm('Annuler ce rendez-vous ?')">
                                    <i class="fa-solid fa-xmark"></i> Annuler
                                </button>
                            </form>
                        @elseif($rdv->statut->value === 'termine')
                            <a href="{{ route('particulier.evaluations.create', $rdv->agence) }}" class="btn btn-rust btn-sm">
                                <i class="fa-solid fa-star"></i> Évaluer
                            </a>
                        @endif
                        <a href="{{ route('particulier.rendezvous.show', $rdv) }}" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-eye"></i> Détails
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="margin-top:30px;">
            {{ $rendezVous->links() }}
        </div>
    @else
        <!-- Message vide -->
        <div style="text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
            <i class="fa-regular fa-calendar-days" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.3;"></i>
            <p style="font-size:16px;font-weight:600;color:var(--text-soft);">Aucun rendez-vous planifié</p>
            <p style="font-size:13px;max-width:400px;margin:4px auto 16px;">
                Les rendez-vous apparaîtront ici après avoir accepté une proposition d'agence.
            </p>
            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust">
                <i class="fa-solid fa-plus"></i> Publier un besoin
            </a>
        </div>
    @endif
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
    .status-planifie {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-confirme {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-annule {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-termine {
        background: #E8F5E9;
        color: #1E7A47;
    }

    .btn-rust {
        background: var(--rust);
        color: #fff;
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
    }
    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }

    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        list-style: none;
        padding: 0;
    }
    .pagination li {
        display: inline;
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
    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 820px) {
        [style*="display:flex;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;"] {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px !important;
        }
        [style*="flex-shrink:0;"] {
            align-self: center !important;
        }
        [style*="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;"] {
            justify-content: center !important;
        }
    }

    @media (max-width: 480px) {
        [style*="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:24px;"] {
            grid-template-columns: 1fr 1fr !important;
        }
    }
</style>
@endpush