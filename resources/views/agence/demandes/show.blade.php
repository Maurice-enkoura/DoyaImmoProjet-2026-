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
                    @if($demande->updated_at != $demande->created_at)
                        <span style="margin-left:8px;">
                            <i class="fa-regular fa-pen-to-square"></i>
                            Modifiée le {{ $demande->updated_at->format('d/m/Y à H:i') }}
                        </span>
                    @endif
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
                <div style="font-size:22px;font-weight:700;color:var(--rust);">
                    {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                    <span style="font-size:13px;font-weight:400;color:var(--muted);">
                        @if($demande->type_operation->value === 'location')
                            F/mois
                        @else
                            F
                        @endif
                    </span>
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
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px;">
                    <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Type de bien</span>
                        <span style="font-weight:500;font-size:13px;">{{ $demande->type_bien->label() }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Opération</span>
                        <span style="font-weight:500;font-size:13px;">{{ $demande->type_operation->label() }}</span>
                    </div>
                    <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">
                            @if($demande->type_operation->value === 'location')
                                Loyer max / mois
                            @else
                                Budget d'achat
                            @endif
                        </span>
                        <span style="font-weight:700;font-size:13px;color:var(--rust);">
                            {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                            @if($demande->type_operation->value === 'location')
                                F/mois
                            @else
                                F
                            @endif
                        </span>
                    </div>
                    <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Zone</span>
                        <span style="font-weight:500;font-size:13px;">{{ $demande->zone_recherchee }}</span>
                    </div>
                    @if($demande->nombre_chambres)
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Chambres</span>
                            <span style="font-weight:500;font-size:13px;">{{ $demande->nombre_chambres }}</span>
                        </div>
                    @endif
                    @if($demande->nombre_salles_bain)
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Salles de bain</span>
                            <span style="font-weight:500;font-size:13px;">{{ $demande->nombre_salles_bain }}</span>
                        </div>
                    @endif
                    @if($demande->surface_minimum)
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Surface min.</span>
                            <span style="font-weight:500;font-size:13px;">{{ $demande->surface_minimum }} m²</span>
                        </div>
                    @endif
                    @if($demande->date_entree_souhaitee)
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Entrée souhaitée</span>
                            <span style="font-weight:500;font-size:13px;">{{ $demande->date_entree_souhaitee->format('d/m/Y') }}</span>
                        </div>
                    @endif
                    <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                        <span style="color:var(--muted);font-size:13px;">Statut</span>
                        <span style="font-weight:500;font-size:13px;">{{ $demande->statut->label() }}</span>
                    </div>
                    @if($demande->meuble !== null)
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Meublé</span>
                            <span style="font-weight:500;font-size:13px;">{{ $demande->meuble ? ' Oui' : ' Non' }}</span>
                        </div>
                    @endif
                    @if($demande->criteres_particuliers)
                        <div style="padding:10px 16px;background:#F7F9FC;border-radius:8px;grid-column:1/3;display:flex;justify-content:space-between;">
                            <span style="color:var(--muted);font-size:13px;">Critères particuliers</span>
                            <span style="font-weight:500;font-size:13px;text-align:right;max-width:60%;">{{ $demande->criteres_particuliers }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- ✅ Équipements demandés -->
            @php
                $equipements = [];
                if ($demande->parking) $equipements[] = ' Parking';
                if ($demande->meuble) $equipements[] = ' Meublé';
                if ($demande->climatisation) $equipements[] = ' Climatisation';
                if ($demande->balcon) $equipements[] = ' Balcon';
                if ($demande->jardin) $equipements[] = ' Jardin';
                if ($demande->piscine) $equipements[] = ' Piscine';
                if ($demande->ascenseur) $equipements[] = ' Ascenseur';
                if ($demande->securite) $equipements[] = ' Sécurité 24h/24';
            @endphp

            @if(count($equipements) > 0)
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                        <i class="fa-solid fa-cogs" style="margin-right:8px;"></i>Équipements souhaités
                        <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:8px;">
                            ({{ count($equipements) }} équipement(s))
                        </span>
                    </h4>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;padding:12px 14px;background:#F7F9FC;border-radius:8px;border:1px solid var(--border);">
                        @foreach($equipements as $equipement)
                            <span class="meta-pill" style="background:#E8F5E9;color:#1E7A47;border:1px solid #C8E6C9;">
                                <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i> {{ $equipement }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @else
                <div style="margin-bottom:24px;">
                    <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                        <i class="fa-solid fa-cogs" style="margin-right:8px;"></i>Équipements souhaités
                    </h4>
                    <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;font-size:13px;color:var(--muted);text-align:center;border:1px dashed var(--border);">
                        <i class="fa-regular fa-circle" style="margin-right:6px;"></i>
                        Aucun équipement spécifique demandé
                    </div>
                </div>
            @endif

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
                <a href="{{ route('agence.propositions.create', $demande->slug) }}" class="btn btn-rust">
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
                        @php
                            $statusColors = [
                                'en_attente' => ['bg' => '#FFF8E1', 'color' => '#E65100'],
                                'acceptee' => ['bg' => '#E8F5E9', 'color' => '#1E7A47'],
                                'refusee' => ['bg' => '#FFEBEE', 'color' => '#C62828'],
                                'terminee' => ['bg' => '#E3F2FD', 'color' => '#0D47A1'],
                            ];
                            $statusInfo = $statusColors[$proposition->statut->value] ?? $statusColors['en_attente'];
                        @endphp
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;border:1px solid var(--border);border-radius:8px;margin-bottom:8px;background:#FAFBFC;transition:all 0.2s;">
                            <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
                                <div style="width:36px;height:36px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:var(--rust);flex-shrink:0;">
                                    {{ strtoupper(substr($proposition->agence->nom_agence, 0, 1)) }}
                                </div>
                                <div style="min-width:0;">
                                    <div style="font-weight:600;font-size:14px;">{{ $proposition->agence->nom_agence }}</div>
                                    <div style="font-size:12px;color:var(--muted);">
                                        {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                                        @if($proposition->bien)
                                            · {{ $proposition->bien->titre ?? 'Bien' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div>
                                <span class="status-pill" style="font-size:11px;padding:2px 12px;background:{{ $statusInfo['bg'] }};color:{{ $statusInfo['color'] }};border:1px solid {{ $statusInfo['color'] }}20;">
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
    /* ===================== STATUS PILL ===================== */
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

    /* ===================== META PILL ===================== */
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 11px;
        color: var(--text-soft);
    }

    /* ===================== BOUTONS ===================== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 12.5px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
    }

    .btn-rust {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
        border-color: #9A4523;
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

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        [style*="display:grid;grid-template-columns:1fr 1fr;gap:4px;"] {
            grid-template-columns: 1fr !important;
        }
        [style*="grid-column:1/3"] {
            grid-column: 1 !important;
        }
        [style*="display:grid;grid-template-columns:1fr 1fr;gap:8px;border:1px solid var(--border);border-radius:10px;overflow:hidden;"] {
            grid-template-columns: 1fr !important;
        }
        .besoin-foot {
            flex-direction: column;
            align-items: stretch;
        }
        .besoin-foot > div {
            flex-direction: column;
            gap: 6px;
        }
        .besoin-foot .btn {
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .status-pill {
            font-size: 10px;
            padding: 3px 10px;
        }
        .meta-pill {
            font-size: 10px;
            padding: 1px 8px;
        }
        .btn-sm {
            font-size: 11px;
            padding: 5px 10px;
        }
        [style*="max-width:60%"] {
            max-width: 100% !important;
            text-align: left !important;
            margin-top: 4px;
        }
    }
</style>
@endpush