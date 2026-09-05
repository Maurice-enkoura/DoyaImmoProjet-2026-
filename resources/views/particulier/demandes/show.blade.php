@extends('layouts.dashboard')

@section('title', 'Détail de ma demande — DoyaImmo')
@section('page_title', 'Détail de ma demande')
@section('page_sub', 'Informations complètes sur votre demande de logement')

@section('content')
<!-- Bouton retour -->
<div style="margin-bottom:20px;">
    <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour à mes demandes
    </a>
</div>

<!-- ✅ AFFICHAGE DES MESSAGES FLASH -->
@if(session('success'))
    <div style="padding:12px 16px;background:#E8F5E9;border-radius:10px;border:1px solid #C8E6C9;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i>
        <span style="color:#1E7A47;font-size:13px;">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div style="padding:12px 16px;background:#FFEBEE;border-radius:10px;border:1px solid #FFCDD2;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-exclamation-circle" style="color:#C62828;"></i>
        <span style="color:#C62828;font-size:13px;">{{ session('error') }}</span>
    </div>
@endif

@if(session('info'))
    <div style="padding:12px 16px;background:#E3F2FD;border-radius:10px;border:1px solid #BBDEFB;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
        <i class="fa-solid fa-info-circle" style="color:#0D47A1;"></i>
        <span style="color:#0D47A1;font-size:13px;">{{ session('info') }}</span>
    </div>
@endif

<!-- Carte principale -->
<div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">

    <!-- En-tête de la carte -->
    <div style="padding:20px 24px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;background:#FAFBFC;">
        <div>
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <h3 style="font-family:var(--display);font-size:20px;font-weight:700;margin:0;color:var(--ink);">
                    {{ $demande->type_bien->label() }}
                </h3>
                <span class="status-pill status-{{ $demande->statut->value }}">
                    <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                    {{ $demande->statut->label() }}
                </span>
            </div>
            <div style="font-size:14px;color:var(--muted);margin-top:4px;">
                <i class="fa-solid fa-location-dot" style="margin-right:4px;"></i>
                {{ $demande->zone_recherchee }}
            </div>
            <div style="font-size:12px;color:var(--muted);margin-top:2px;">
                <i class="fa-regular fa-calendar" style="margin-right:4px;"></i>
                Publiée le {{ $demande->created_at->format('d/m/Y à H:i') }}
                @if($demande->updated_at != $demande->created_at)
                    <span style="margin-left:8px;">
                        <i class="fa-regular fa-pen-to-square"></i>
                        Modifiée le {{ $demande->updated_at->format('d/m/Y à H:i') }}
                    </span>
                @endif
            </div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
            <div style="font-size:13px;color:var(--muted);">
                <i class="fa-solid fa-file-invoice"></i> {{ $demande->propositions->count() }} proposition(s)
            </div>
            <div style="font-size:22px;font-weight:700;color:var(--rust);">
                {{ number_format($demande->budget_maximum, 0, ',', ' ') }}
                <span style="font-size:13px;font-weight:400;color:var(--muted);">
                    @if($demande->type_operation->value === 'location')
                        F/mois
                    @else
                        F (achat)
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Corps de la carte -->
    <div style="padding:24px;">

        <!-- Informations de la demande - 2 colonnes -->
        <div style="margin-bottom:24px;">
            <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                <i class="fa-solid fa-list-ul" style="margin-right:8px;"></i>Détails de la demande
            </h4>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Type de bien</span>
                    <span style="font-weight:500;font-size:13px;">{{ $demande->type_bien->label() }}</span>
                </div>
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Opération</span>
                    <span style="font-weight:500;font-size:13px;">{{ $demande->type_operation->label() }}</span>
                </div>
                @if($demande->nombre_chambres)
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Chambres</span>
                    <span style="font-weight:500;font-size:13px;">{{ $demande->nombre_chambres }}</span>
                </div>
                @endif
                @if($demande->nombre_salles_bain)
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Salles de bain</span>
                    <span style="font-weight:500;font-size:13px;">{{ $demande->nombre_salles_bain }}</span>
                </div>
                @endif
                @if($demande->surface_minimum)
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Surface min.</span>
                    <span style="font-weight:500;font-size:13px;">{{ $demande->surface_minimum }} m²</span>
                </div>
                @endif
                @if($demande->date_entree_souhaitee)
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Entrée souhaitée</span>
                    <span style="font-weight:500;font-size:13px;">{{ $demande->date_entree_souhaitee->format('d/m/Y') }}</span>
                </div>
                @endif
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Statut</span>
                    <span style="font-weight:500;font-size:13px;">{{ $demande->statut->label() }}</span>
                </div>
                @if($demande->meuble !== null)
                <div style="padding:10px 14px;background:#F7F9FC;border-radius:8px;display:flex;justify-content:space-between;">
                    <span style="color:var(--muted);font-size:13px;">Meublé</span>
                    <span style="font-weight:500;font-size:13px;">{{ $demande->meuble ? ' Oui' : ' Non' }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- ✅ Équipements souhaités (COMPLETS) -->
        @php
            $equipements = [];
            if ($demande->parking) $equipements[] = ['label' => ' Parking', 'checked' => true];
            if ($demande->meuble) $equipements[] = ['label' => ' Meublé', 'checked' => true];
            if ($demande->climatisation) $equipements[] = ['label' => ' Climatisation', 'checked' => true];
            if ($demande->balcon) $equipements[] = ['label' => ' Balcon', 'checked' => true];
            if ($demande->jardin) $equipements[] = ['label' => ' Jardin', 'checked' => true];
            if ($demande->piscine) $equipements[] = ['label' => ' Piscine', 'checked' => true];
            if ($demande->ascenseur) $equipements[] = ['label' => ' Ascenseur', 'checked' => true];
            if ($demande->securite) $equipements[] = ['label' => ' Sécurité 24h/24', 'checked' => true];
        @endphp

        @if(count($equipements) > 0)
        <div style="margin-bottom:24px;">
            <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft%;">
                <i class="fa-solid fa-cogs" style="margin-right:8px;"></i>Équipements souhaités
                <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:8px;">
                    ({{ count($equipements) }} équipement(s))
                </span>
            </h4>
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                @foreach($equipements as $equipement)
                <span class="meta-pill" style="background:#E8F5E9;color:#1E7A47;border:1px solid #C8E6C9;">
                    <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i> {{ $equipement['label'] }}
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

        <!-- Critères particuliers -->
        @if($demande->criteres_particuliers)
        <div style="margin-bottom:24px;">
            <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                <i class="fa-solid fa-clipboard-list" style="margin-right:8px;"></i>Critères particuliers
            </h4>
            <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;font-size:14px;color:var(--text-soft);line-height:1.7;border:1px solid var(--border);">
                {{ $demande->criteres_particuliers }}
            </div>
        </div>
        @endif

        <!-- Description -->
        @if($demande->description)
        <div style="margin-bottom:24px;">
            <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                <i class="fa-solid fa-align-left" style="margin-right:8px;"></i>Description
            </h4>
            <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;font-size:14px;color:var(--text-soft);line-height:1.8;border:1px solid var(--border);">
                {{ $demande->description }}
            </div>
        </div>
        @endif

        <!-- Propositions reçues -->
        <div style="margin-bottom:24px;">
            <h4 style="font-family:var(--display);font-size:14px;margin-bottom:12px;color:var(--text-soft);">
                <i class="fa-solid fa-handshake" style="margin-right:8px;"></i>
                Propositions reçues ({{ $demande->propositions->count() }})
            </h4>

            @if($demande->propositions->count() > 0)
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
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border:1px solid var(--border);border-radius:10px;margin-bottom:10px;background:#FAFBFC;transition:all 0.2s;">
                        <div style="display:flex;align-items:center;gap:12px;flex:1;min-width:0;">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:var(--rust);flex-shrink:0;">
                                {{ strtoupper(substr($proposition->agence->nom_agence, 0, 1)) }}
                            </div>
                            <div style="min-width:0;">
                                <div style="font-weight:600;font-size:14px;">{{ $proposition->agence->nom_agence }}</div>
                                <div style="font-size:12px;color:var(--muted);display:flex;flex-wrap:wrap;gap:4px 12px;">
                                    <span>{{ $proposition->bien->titre ?? 'Bien' }}</span>
                                    <span>·</span>
                                    <span>{{ $proposition->bien->surface ?? 'N/C' }} m²</span>
                                    @if($proposition->bien->nombre_chambres)
                                        <span>·</span>
                                        <span>{{ $proposition->bien->nombre_chambres }} ch.</span>
                                    @endif
                                </div>
                                @if($proposition->message)
                                    <div style="font-size:12px;color:var(--text-soft);margin-top:4px;padding:4px 10px;background:#fff;border-radius:6px;border:1px solid var(--border);">
                                        <i class="fa-regular fa-message" style="color:var(--rust);"></i>
                                        {{ Str::limit($proposition->message, 80) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div style="text-align:right;flex-shrink:0;margin-left:12px;">
                            <div style="font-weight:700;color:var(--rust);font-size:16px;">
                                {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                            </div>
                            <span class="status-pill" style="font-size:10px;padding:2px 12px;display:inline-block;background:{{ $statusInfo['bg'] }};color:{{ $statusInfo['color'] }};border:1px solid {{ $statusInfo['color'] }}20;">
                                {{ $proposition->statut->label() }}
                            </span>
                            @if($proposition->score_matching)
                                <div style="font-size:10px;color:var(--muted);margin-top:2px;">
                                    Score: {{ $proposition->score_matching }}%
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($demande->propositions->count() > 3)
                    <div style="text-align:center;margin-top:8px;">
                        <a href="{{ route('particulier.propositions.index', $demande->slug) }}" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-eye"></i> Voir toutes les propositions
                        </a>
                    </div>
                @endif
            @else
                <div style="padding:30px;background:#F7F9FC;border-radius:10px;text-align:center;color:var(--muted);border:1px dashed var(--border);">
                    <i class="fa-solid fa-inbox" style="font-size:32px;display:block;margin-bottom:10px;opacity:0.3;"></i>
                    <span style="font-size:14px;">Aucune proposition reçue pour le moment</span>
                    <p style="font-size:12px;margin-top:4px;">Les agences vous contacteront dès qu'elles auront un bien correspondant.</p>
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div style="display:flex;gap:12px;flex-wrap:wrap;padding-top:16px;border-top:1px solid var(--border);">
            @if($demande->statut->value === 'en_attente')
                <a href="{{ route('particulier.demandes.edit', $demande->slug) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
                <form action="{{ route('particulier.demandes.destroy', $demande->slug) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost" style="color:#C62828;border-color:#FFCDD2;" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                        <i class="fa-solid fa-trash"></i> Annuler
                    </button>
                </form>
            @elseif($demande->statut->value === 'en_cours')
                <div style="padding:8px 16px;background:#E3F2FD;border-radius:8px;color:#0D47A1;font-size:13px;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-clock"></i> Cette demande est en cours de traitement
                </div>
            @elseif($demande->statut->value === 'terminee')
                <div style="padding:8px 16px;background:#E8F5E9;border-radius:8px;color:#1E7A47;font-size:13px;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-check-circle"></i> Cette demande est terminée
                </div>
            @elseif($demande->statut->value === 'annulee')
                <div style="padding:8px 16px;background:#FFEBEE;border-radius:8px;color:#C62828;font-size:13px;display:flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-ban"></i> Cette demande a été annulée
                </div>
            @endif
            
            @if($demande->statut->value === 'en_attente')
                <a href="{{ route('particulier.demandes.edit', $demande->slug) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
            @endif
            
            <a href="{{ route('particulier.demandes.index') }}" class="btn btn-ghost btn-sm" style="margin-left:auto;">
                <i class="fa-solid fa-list"></i> Toutes mes demandes
            </a>
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
        [style*="display:grid;grid-template-columns:1fr 1fr;gap:8px;"] {
            grid-template-columns: 1fr !important;
        }
        .offre-item {
            flex-direction: column;
            align-items: stretch !important;
            text-align: center;
        }
        .offre-item > div:first-child {
            flex-direction: column;
            align-items: center;
        }
        .offre-item > div:last-child {
            text-align: center;
        }
    }
</style>
@endpush