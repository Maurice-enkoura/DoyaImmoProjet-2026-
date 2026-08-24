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
            </div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;">
            <div style="font-size:13px;color:var(--muted);">
                <i class="fa-solid fa-file-invoice"></i> {{ $demande->propositions->count() }} proposition(s)
            </div>
            <div style="font-size:22px;font-weight:700;color:var(--rust);">
                {{ number_format($demande->budget_maximum, 0, ',', ' ') }} <span style="font-size:13px;font-weight:400;color:var(--muted);">F/mois</span>
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
            </div>
        </div>

        <!-- Équipements -->
        @php
            $equipements = [];
            if ($demande->parking) $equipements[] = 'Parking';
            if ($demande->meuble) $equipements[] = 'Meublé';
            if ($demande->climatisation) $equipements[] = 'Climatisation';
            if ($demande->balcon) $equipements[] = 'Balcon';
            if ($demande->jardin) $equipements[] = 'Jardin';
            if ($demande->piscine) $equipements[] = 'Piscine';
            if ($demande->ascenseur) $equipements[] = 'Ascenseur';
            if ($demande->securite) $equipements[] = 'Sécurité';
        @endphp
        @if(count($equipements) > 0)
        <div style="margin-bottom:24px;">
            <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                <i class="fa-solid fa-cogs" style="margin-right:8px;"></i>Équipements souhaités
            </h4>
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                @foreach($equipements as $equipement)
                <span class="meta-pill" style="background:var(--teal-soft);color:var(--teal);">
                    <i class="fa-solid fa-check-circle"></i> {{ $equipement }}
                </span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Critères particuliers -->
        @if($demande->criteres_particuliers)
        <div style="margin-bottom:24px;">
            <h4 style="font-family:var(--display);font-size:14px;margin-bottom:8px;color:var(--text-soft);">
                <i class="fa-solid fa-clipboard-list" style="margin-right:8px;"></i>Critères particuliers
            </h4>
            <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;font-size:14px;color:var(--text-soft);line-height:1.7;">
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
            <div style="padding:12px 16px;background:#F7F9FC;border-radius:8px;font-size:14px;color:var(--text-soft);line-height:1.8;">
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
            <div style="display:flex;justify-content:space-between;align-items:center;padding:12px 16px;border:1px solid var(--border);border-radius:10px;margin-bottom:10px;background:#FAFBFC;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:var(--rust);flex-shrink:0;">
                        {{ strtoupper(substr($proposition->agence->nom_agence, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:14px;">{{ $proposition->agence->nom_agence }}</div>
                        <div style="font-size:12px;color:var(--muted);">
                            {{ $proposition->bien->titre ?? 'Bien' }} · {{ $proposition->bien->surface ?? 'N/C' }} m²
                        </div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-weight:700;color:var(--rust);font-size:16px;">
                        {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                    </div>
                    <span class="status-pill status-{{ $proposition->statut->value }}" style="font-size:10px;padding:2px 10px;display:inline-block;">
                        {{ $proposition->statut->label() }}
                    </span>
                </div>
            </div>
            @endforeach
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
                <a href="{{ route('particulier.demandes.edit', $demande) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-pen"></i> Modifier
                </a>
                <form action="{{ route('particulier.demandes.destroy', $demande) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost" style="color:#C62828;border-color:#FFCDD2;" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?')">
                        <i class="fa-solid fa-trash"></i> Annuler
                    </button>
                </form>
            @elseif($demande->statut->value === 'en_cours')
                <div style="padding:8px 16px;background:#E3F2FD;border-radius:8px;color:#0D47A1;font-size:13px;">
                    <i class="fa-solid fa-clock"></i> Cette demande est en cours de traitement
                </div>
            @elseif($demande->statut->value === 'terminee')
                <div style="padding:8px 16px;background:#E8F5E9;border-radius:8px;color:#1E7A47;font-size:13px;">
                    <i class="fa-solid fa-check-circle"></i> Cette demande est terminée
                </div>
            @elseif($demande->statut->value === 'annulee')
                <div style="padding:8px 16px;background:#FFEBEE;border-radius:8px;color:#C62828;font-size:13px;">
                    <i class="fa-solid fa-ban"></i> Cette demande a été annulée
                </div>
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

    .status-acceptee {
        background: #E8F5E9;
        color: #1E7A47;
    }

    .status-refusee {
        background: #FFEBEE;
        color: #C62828;
    }

    .status-planifie {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-confirme {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-termine {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-annule {
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
</style>
@endpush