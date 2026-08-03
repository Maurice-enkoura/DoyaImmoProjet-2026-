@extends('layouts.dashboard-agence')

@section('title', 'Détail de la demande — DoyaImmo')
@section('page_title', 'Détail de la demande')
@section('page_sub', 'Informations complètes sur la demande du client')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('agence.demandes.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux demandes
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">

        <!-- En-tête -->
        <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:#FAFBFC;">
            <div>
                <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                    <h3 style="font-family:var(--display);font-size:18px;font-weight:700;margin:0;">
                        {{ $demande->type_bien->label() }}
                    </h3>
                    <span class="status-pill status-{{ $demande->statut->value }}">
                        <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                        {{ $demande->statut->label() }}
                    </span>
                </div>
                <div style="font-size:14px;color:var(--muted);margin-top:4px;">
                    <i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}
                </div>
                <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                    <i class="fa-regular fa-calendar"></i> Publiée le {{ $demande->created_at->format('d/m/Y à H:i') }}
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                <div style="font-size:22px;font-weight:700;color:var(--rust);">
                    {{ number_format($demande->budget_maximum, 0, ',', ' ') }} <span style="font-size:13px;font-weight:400;color:var(--muted);">F/mois</span>
                </div>
                <span style="font-size:12px;color:var(--muted);">
                    <i class="fa-solid fa-file-invoice"></i> {{ $demande->propositions->count() }} offre(s)
                </span>
            </div>
        </div>

        <!-- Corps -->
        <div style="padding:24px;">

            <!-- Client -->
            <div style="display:flex;align-items:center;gap:16px;padding:14px 18px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);margin-bottom:24px;flex-wrap:wrap;">
                <div style="width:48px;height:48px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--rust);flex-shrink:0;">
                    {{ strtoupper(substr($demande->particulier->user->prenom ?? 'C', 0, 1)) }}
                </div>
                <div style="flex:1;min-width:120px;">
                    <div style="font-weight:600;font-size:15px;">
                        {{ $demande->particulier->user->prenom ?? '' }} {{ $demande->particulier->user->nom ?? '' }}
                    </div>
                    <div style="font-size:13px;color:var(--muted);">
                        <i class="fa-solid fa-envelope"></i> {{ $demande->particulier->user->email ?? '' }}
                    </div>
                    <div style="font-size:13px;color:var(--muted);">
                        <i class="fa-solid fa-phone"></i> {{ $demande->particulier->user->telephone ?? 'Non renseigné' }}
                    </div>
                </div>
                <div style="text-align:right;flex-shrink:0;">
                    <div style="font-size:13px;color:var(--muted);">
                        <i class="fa-regular fa-clock"></i> Membre depuis {{ $demande->particulier->user->created_at->format('d/m/Y') }}
                    </div>
                </div>
            </div>

            <!-- Informations de la demande -->
            <div style="margin-bottom:24px;">
                <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                    <i class="fa-solid fa-list-ul" style="margin-right:8px;"></i>Détails de la demande
                </h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;border:1px solid var(--border);border-radius:10px;overflow:hidden;">
                    <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Type de bien</span>
                        <span style="font-weight:500;font-size:13px;">{{ $demande->type_bien->label() }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Opération</span>
                        <span style="font-weight:500;font-size:13px;">{{ $demande->type_operation->label() }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Budget</span>
                        <span style="font-weight:700;font-size:13px;color:var(--rust);">{{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois</span>
                    </div>
                    <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Zone</span>
                        <span style="font-weight:500;font-size:13px;">{{ $demande->zone_recherchee }}</span>
                    </div>
                    @if($demande->nombre_chambres)
                        <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Chambres</span>
                            <span style="font-weight:500;font-size:13px;">{{ $demande->nombre_chambres }}</span>
                        </div>
                    @endif
                    @if($demande->surface_minimum)
                        <div style="padding:10px 16px;background:#fff;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Surface min.</span>
                            <span style="font-weight:500;font-size:13px;">{{ $demande->surface_minimum }} m²</span>
                        </div>
                    @endif
                    @if($demande->date_entree_souhaitee)
                        <div style="padding:10px 16px;background:#FAFBFC;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Entrée souhaitée</span>
                            <span style="font-weight:500;font-size:13px;">{{ $demande->date_entree_souhaitee->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    @if($demande->criteres_particuliers)
                        <div style="padding:10px 16px;background:#fff;grid-column:1/3;display:flex;justify-content:space-between;border-bottom:none;">
                            <span style="color:var(--muted);font-size:13px;">Critères particuliers</span>
                            <span style="font-weight:500;font-size:13px;text-align:right;max-width:60%;">{{ $demande->criteres_particuliers }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            @if($demande->description)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                        <i class="fa-solid fa-align-left" style="margin-right:8px;"></i>Description
                    </h4>
                    <div style="padding:14px 18px;background:#F7F9FC;border-radius:10px;font-size:14px;color:var(--text-soft);line-height:1.7;border:1px solid var(--border);">
                        {{ $demande->description }}
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div style="display:flex;gap:12px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border);">
                <a href="{{ route('agence.propositions.create', $demande) }}" class="btn btn-rust">
                    <i class="fa-solid fa-paper-plane"></i> Faire une offre
                </a>
                <a href="{{ route('agence.demandes.index') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;">
                    <i class="fa-solid fa-list"></i> Toutes les demandes
                </a>
            </div>

            <!-- Propositions existantes -->
            @if($demande->propositions->count() > 0)
                <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                        <i class="fa-solid fa-handshake"></i> Propositions existantes ({{ $demande->propositions->count() }})
                    </h4>
                    @foreach($demande->propositions as $proposition)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;border:1px solid var(--border);border-radius:8px;margin-bottom:8px;background:#FAFBFC;">
                            <div>
                                <div style="font-weight:600;font-size:14px;">{{ $proposition->agence->nom_agence }}</div>
                                <div style="font-size:12px;color:var(--muted);">
                                    {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                            <div>
                                <span class="status-pill status-{{ $proposition->statut->value }}" style="font-size:11px;padding:2px 12px;">
                                    {{ $proposition->statut->label() }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
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
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-en_cours {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-terminee {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-annulee {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-acceptee {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-refusee {
        background: #FFEBEE;
        color: #C62828;
    }

    @media (max-width: 768px) {
        [style*="display:grid;grid-template-columns:1fr 1fr;gap:8px;"] {
            grid-template-columns: 1fr !important;
        }
        [style*="grid-column:1/3"] {
            grid-column: 1 !important;
        }
    }
</style>
@endpush