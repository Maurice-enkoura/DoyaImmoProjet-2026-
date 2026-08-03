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
    </div>

    @if($propositions->count() > 0)
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:auto;">
            <table style="width:100%;border-collapse:collapse;font-size:13px;min-width:600px;">
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
                        <tr style="border-bottom:1px solid var(--border);">
                            <td style="padding:12px 16px;">
                                <div style="font-weight:600;font-size:13px;">{{ $proposition->bien->titre ?? 'Bien' }}</div>
                                <div style="font-size:12px;color:var(--muted);">{{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}</div>
                            </td>
                            <td style="padding:12px 16px;">
                                <div style="font-size:13px;">Client #{{ $proposition->particulier->id }}</div>
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

        <div style="margin-top:30px;">
            {{ $propositions->links() }}
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