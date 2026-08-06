@extends('layouts.admin')

@section('title', 'Détail de la demande — Administration DoyaImmo')
@section('page_title', 'Détail de la demande')
@section('page_sub', ($demande->type_operation_label ?? 'N/A') . ' - ' . ($demande->type_bien_label ?? 'N/A'))

@section('content')
<div style="margin-bottom:20px;">
    <a href="{{ route('admin.demandes.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux demandes
    </a>
</div>

<div class="grid-2">
    <!-- Informations générales -->
    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
                <h3 style="font-size:18px;font-weight:700;">
                    {{ $demande->type_operation_label ?? 'N/A' }} - {{ $demande->type_bien_label ?? 'N/A' }}
                </h3>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">
                    <i class="fa-solid fa-user"></i> 
                    {{ $demande->particulier->user->prenom ?? '' }} {{ $demande->particulier->user->nom ?? '' }}
                    <span style="margin-left:12px;">
                        <i class="fa-solid fa-envelope"></i> {{ $demande->particulier->user->email ?? '' }}
                    </span>
                </div>
            </div>
            <span class="status-pill status-{{ $demande->statut }}">
                {{ $demande->statut_label ?? $demande->statut }}
            </span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <!-- Type d'opération -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Type d'opération</div>
                <div style="font-weight:600;">
                    {{ $demande->type_operation_label ?? 'N/A' }}
                </div>
            </div>
            
            <!-- Type de bien -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Type de bien</div>
                <div style="font-weight:600;">
                    {{ $demande->type_bien_label ?? 'N/A' }}
                </div>
            </div>
            
            <!-- Budget maximum -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Budget maximum</div>
                <div style="font-weight:600;color:var(--rust);font-size:16px;">
                    {{ number_format($demande->budget_maximum ?? 0, 0, ',', ' ') }} FCFA
                </div>
            </div>
            
            <!-- Zone recherchée -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Zone recherchée</div>
                <div style="font-weight:600;">{{ $demande->zone_recherchee ?? 'N/A' }}</div>
            </div>
            
            <!-- Surface minimum -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Surface minimum</div>
                <div style="font-weight:600;">{{ $demande->surface_minimum ?? 'N/A' }} m²</div>
            </div>
            
            <!-- Nombre de chambres -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Nombre de chambres</div>
                <div style="font-weight:600;">{{ $demande->nombre_chambres ?? 0 }}</div>
            </div>
            
            <!-- Nombre de salles de bain -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Salles de bain</div>
                <div style="font-weight:600;">{{ $demande->nombre_salles_bain ?? 0 }}</div>
            </div>
            
            <!-- Date d'entrée souhaitée -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Date d'entrée souhaitée</div>
                <div style="font-weight:600;">
                    {{ $demande->date_entree_souhaitee ? $demande->date_entree_souhaitee->format('d/m/Y') : 'Non spécifiée' }}
                </div>
            </div>
            
            <!-- Date de publication -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Date de publication</div>
                <div style="font-weight:600;">
                    {{ $demande->date_publication ? $demande->date_publication->format('d/m/Y') : $demande->created_at->format('d/m/Y') }}
                </div>
            </div>
            
            <!-- QUARTIER CORRIGÉ -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">Quartier</div>
                <div style="font-weight:600;">
                    @php
                        $quartierNom = 'Non renseigné';
                        $quartierVille = '';
                        
                        if ($demande->quartier) {
                            if (is_object($demande->quartier)) {
                                $quartierNom = $demande->quartier->nom ?? 'Non renseigné';
                                $quartierVille = $demande->quartier->ville ?? '';
                            } elseif (is_string($demande->quartier)) {
                                $quartierNom = $demande->quartier;
                            }
                        } elseif ($demande->quartier_id) {
                            $quartier = \App\Models\Quartier::find($demande->quartier_id);
                            if ($quartier) {
                                $quartierNom = $quartier->nom;
                                $quartierVille = $quartier->ville;
                            }
                        }
                    @endphp
                    {{ $quartierNom }}
                    @if($quartierVille)
                        <span style="font-weight:400;color:var(--muted);font-size:13px;">
                            ({{ $quartierVille }})
                        </span>
                    @endif
                </div>
            </div>

            <!-- Critères particuliers -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">Critères particuliers</div>
                <div style="font-weight:600;font-size:13px;">{{ $demande->criteres_particuliers ?? 'Aucun critère particulier' }}</div>
            </div>
        </div>

        <!-- Équipements souhaités -->
        <div style="margin-top:16px;padding:12px 16px;background:#F7F9FC;border-radius:8px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:8px;">Équipements souhaités</div>
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                @php
                    $equipements = [
                        'parking' => ['label' => ' Parking', 'value' => $demande->parking ?? false],
                        'meuble' => ['label' => 'Meublé', 'value' => $demande->meuble ?? false],
                        'climatisation' => ['label' => ' Climatisation', 'value' => $demande->climatisation ?? false],
                        'balcon' => ['label' => ' Balcon', 'value' => $demande->balcon ?? false],
                        'jardin' => ['label' => ' Jardin', 'value' => $demande->jardin ?? false],
                        'piscine' => ['label' => ' Piscine', 'value' => $demande->piscine ?? false],
                        'ascenseur' => ['label' => ' Ascenseur', 'value' => $demande->ascenseur ?? false],
                        'securite' => ['label' => '  Sécurité', 'value' => $demande->securite ?? false],
                    ];
                @endphp
                
                @foreach($equipements as $key => $equipement)
                    @if($equipement['value'])
                        <span class="status-pill status-active" style="background:#E8F5E9;color:#1E7A47;border:1px solid #C8E6C9;">
                            {{ $equipement['label'] }}
                        </span>
                    @else
                        <span class="status-pill status-inactif" style="background:#F5F5F5;color:#999;border:1px solid #E0E0E0;">
                            {{ $equipement['label'] }}
                        </span>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- Description -->
        @if($demande->description)
            <div style="margin-top:16px;padding:12px 16px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);margin-bottom:4px;">Description</div>
                <div style="font-size:13px;color:var(--text-soft);line-height:1.6;">{{ $demande->description }}</div>
            </div>
        @endif

        <!-- Actions (lecture seule) -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;">
            <form action="{{ route('admin.demandes.destroy', $demande) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cette demande ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fa-solid fa-trash-can"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    <!-- Propositions -->
    <div>
        <div class="panel" style="margin-bottom:20px;">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-handshake"></i> Propositions reçues
                <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:8px;">
                    ({{ $demande->propositions->count() }} offres)
                </span>
            </h3>
            @if($demande->propositions->count() > 0)
                @foreach($demande->propositions as $proposition)
                    <a href="{{ route('admin.propositions.show', $proposition) }}" style="text-decoration:none;color:inherit;display:block;">
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:12px;background:#F7F9FC;border-radius:8px;margin-bottom:8px;border-left:4px solid 
                            @if($proposition->statut === 'acceptee') var(--green)
                            @elseif($proposition->statut === 'refusee') var(--red)
                            @else #E65100
                            @endif;
                            transition:background 0.2s;">
                            <div>
                                <div style="font-weight:600;font-size:14px;">
                                    {{ $proposition->agence->nom_agence ?? 'N/A' }}
                                </div>
                                <div style="font-size:12px;color:var(--muted);">
                                    <i class="fa-solid fa-building"></i> {{ $proposition->bien->titre ?? 'N/A' }}
                                </div>
                                <div style="font-size:12px;color:var(--muted);">
                                    <i class="fa-solid fa-money-bill"></i> {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <span class="status-pill status-{{ $proposition->statut }}">
                                    {{ $proposition->statut_label ?? $proposition->statut }}
                                </span>
                                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                                    {{ $proposition->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            @else
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0;">
                    <i class="fa-solid fa-handshake" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    Aucune proposition reçue.
                </p>
            @endif
        </div>
    </div>
</div>
@endsection