@extends('layouts.dashboard-agence')

@section('title', 'Mes offres — DoyaImmo')
@section('page_title', 'Mes offres')
@section('page_sub', 'Suivez vos propositions envoyées')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mes offres envoyées</h2>
            <p>Suivez le statut de chaque proposition envoyée à un client</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <span style="font-size:13px;color:var(--muted);">
                <i class="fa-solid fa-star" style="color:#F5A623;"></i> En vedette
            </span>
            <span style="font-size:13px;color:var(--muted);">
                <i class="fa-solid fa-file-invoice"></i> {{ $propositions->total() }} offres
            </span>
        </div>
    </div>

    @if($propositions->count() > 0)
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;min-width:700px;">
                <thead>
                    <tr style="background:#FAFBFC;border-bottom:1px solid var(--border);">
                        <th style="padding:12px 16px;text-align:left;">Bien proposé</th>
                        <th style="padding:12px 16px;text-align:left;">Client</th>
                        <th style="padding:12px 16px;text-align:left;">Montant</th>
                        <th style="padding:12px 16px;text-align:left;">Date d'envoi</th>
                        <th style="padding:12px 16px;text-align:left;">Statut</th>
                        <th style="padding:12px 16px;text-align:left;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($propositions as $proposition)
                        <tr style="border-bottom:1px solid var(--border);{{ $proposition->bien && $proposition->bien->est_vedette ? 'background:#FFFDF5;' : '' }}">
                            <td style="padding:12px 16px;">
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <span style="font-weight:600;font-size:13px;">{{ $proposition->bien->titre ?? 'Bien' }}</span>
                                    @if($proposition->bien && $proposition->bien->est_vedette)
                                        <span class="vedette-tag-table">
                                            <i class="fa-solid fa-star"></i> Vedette
                                        </span>
                                    @endif
                                </div>
                                <div style="font-size:12px;color:var(--muted);">{{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}</div>
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="font-size:13px;">{{ $proposition->particulier->user->prenom ?? 'Client' }} {{ $proposition->particulier->user->nom ?? '' }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ $proposition->particulier->user->email ?? '' }}</div>
                            </td>
                            <td style="padding:12px 16px;font-weight:600;color:var(--rust);">
                                {{ number_format($proposition->prix_propose, 0, ',', ' ') }} F
                            </td>
                            <td style="padding:12px 16px;font-size:13px;">
                                {{ $proposition->created_at->format('d/m/Y') }}
                            </td>
                            <td style="padding:12px 16px;">
                                <span class="status-pill status-{{ $proposition->statut->value }}">
                                    {{ $proposition->statut->label() }}
                                </span>
                            </td>
                            <td style="padding:12px 16px;">
                                <a href="{{ route('agence.propositions.show', $proposition) }}" class="btn btn-ghost btn-sm">
                                    Détail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        
    @else
        <div style="text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
            <i class="fa-solid fa-file-invoice" style="font-size:40px;display:block;margin-bottom:16px;opacity:0.3;"></i>
            <p style="font-size:16px;">Aucune offre envoyée.</p>
            <a href="{{ route('agence.demandes.index') }}" class="btn btn-rust" style="margin-top:16px;">
                Voir les besoins disponibles
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
    .status-en_attente {
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
    
    /* ===== TAG VEDETTE DANS LE TABLEAU ===== */
    .vedette-tag-table {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        background: #F5A623;
        box-shadow: 0 2px 6px rgba(245, 166, 35, 0.25);
        animation: pulseVedette 2s ease-in-out infinite;
    }

    .vedette-tag-table i {
        font-size: 9px;
    }

    @keyframes pulseVedette {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }

    /* ===== PAGINATION ===== */
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

    /* ============================================
       RESPONSIVE
    ============================================ */
    @media (max-width: 768px) {
        .section-head {
            flex-direction: column;
            align-items: flex-start;
        }
        table {
            font-size: 12px;
        }
        table th,
        table td {
            padding: 8px 10px !important;
        }
        .vedette-tag-table {
            font-size: 9px;
            padding: 1px 8px;
        }
    }

    @media (max-width: 600px) {
        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>
@endpush