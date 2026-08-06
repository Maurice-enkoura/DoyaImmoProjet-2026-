@extends('layouts.admin')

@section('title', 'Détail de l\'abonnement — Administration DoyaImmo')
@section('page_title', 'Détail de l\'abonnement')
@section('page_sub', 'Abonnement #' . $abonnement->id)

@section('content')
<style>
    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr !important;
        }
        .panel {
            padding: 16px !important;
        }
        .info-grid {
            grid-template-columns: 1fr !important;
        }
    }
    @media (max-width: 480px) {
        .panel {
            padding: 12px !important;
        }
        .info-grid {
            grid-template-columns: 1fr !important;
        }
        .btn {
            width: 100% !important;
            justify-content: center !important;
        }
        .actions-group {
            flex-direction: column !important;
        }
    }
    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    .info-item {
        padding: 8px 12px;
        background: #F7F9FC;
        border-radius: 8px;
    }
    .info-item .label {
        font-size: 11px;
        color: var(--muted);
    }
    .info-item .value {
        font-weight: 600;
        font-size: 14px;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-active {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-inactif {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-warning {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-confirme {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.abonnements.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux abonnements
    </a>
</div>

<div class="grid-2">
    <!-- Informations de l'abonnement -->
    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
                <h3 style="font-size:18px;font-weight:700;">
                    <i class="fa-solid fa-award" style="color:var(--rust);"></i> 
                    Abonnement #{{ $abonnement->id }}
                </h3>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">
                    <i class="fa-solid fa-calendar"></i> 
                    Créé le {{ $abonnement->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            <span class="status-pill {{ $abonnement->estActif() ? 'status-active' : 'status-inactif' }}">
                {{ $abonnement->estActif() ? ' Actif' : ' Expiré' }}
            </span>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <div class="label">Agence</div>
                <div class="value">{{ $abonnement->agence->nom_agence ?? 'N/A' }}</div>
            </div>
            <div class="info-item">
                <div class="label">Formule</div>
                <div class="value">
                    @php
                        $formuleLabel = is_object($abonnement->formule) && method_exists($abonnement->formule, 'label') 
                            ? $abonnement->formule->label() 
                            : ucfirst($abonnement->formule);
                    @endphp
                    <span class="status-pill 
                        @if($formuleLabel === 'Pro') status-active
                        @elseif($formuleLabel === 'Premium') status-confirme
                        @else status-en_attente
                        @endif">
                        {{ $formuleLabel }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="label">Montant</div>
                <div class="value" style="color:var(--rust);font-size:16px;">
                    @if($abonnement->montant == 0)
                        Gratuit
                    @else
                        {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="label">Durée</div>
                <div class="value">
                    @php
                        $debut = \Carbon\Carbon::parse($abonnement->date_debut);
                        $fin = \Carbon\Carbon::parse($abonnement->date_fin);
                        $diffMois = $debut->diffInMonths($fin);
                    @endphp
                    {{ $diffMois }} mois
                </div>
            </div>
            <div class="info-item">
                <div class="label">Date de début</div>
                <div class="value">{{ $abonnement->date_debut->format('d/m/Y') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Date de fin</div>
                <div class="value">
                    {{ $abonnement->date_fin->format('d/m/Y') }}
                    @if($abonnement->estExpirant(7))
                        <span class="status-pill status-warning" style="font-size:9px;margin-left:8px;">Expire bientôt</span>
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="label">Statut</div>
                <div class="value">
                    <span class="status-pill {{ $abonnement->estActif() ? 'status-active' : 'status-inactif' }}">
                        {{ $abonnement->estActif() ? 'Actif' : 'Expiré' }}
                    </span>
                </div>
            </div>
            <div class="info-item">
                <div class="label">Paiement</div>
                <div class="value">
                    @if($abonnement->paydunya_status === 'paid')
                        <span class="status-pill status-active"> Payé</span>
                    @elseif($abonnement->paydunya_status === 'pending')
                        <span class="status-pill status-warning"> En attente</span>
                    @elseif($abonnement->paydunya_status === 'free')
                        <span class="status-pill status-active"> Gratuit</span>
                    @else
                        <span class="status-pill status-inactif"> Non payé</span>
                    @endif
                </div>
            </div>
            @if($abonnement->paydunya_token)
                <div class="info-item" style="grid-column:span 2;">
                    <div class="label">Token PayDunya</div>
                    <div class="value" style="font-size:12px;word-break:break-all;">{{ $abonnement->paydunya_token }}</div>
                </div>
            @endif
            @if($abonnement->paydunya_paid_at)
                <div class="info-item" style="grid-column:span 2;">
                    <div class="label">Payé le</div>
                    <div class="value">{{ $abonnement->paydunya_paid_at->format('d/m/Y H:i') }}</div>
                </div>
            @endif
        </div>

        <!-- Actions -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <div style="display:flex;gap:8px;flex-wrap:wrap;" class="actions-group">
                @if($abonnement->estActif())
                    <form action="{{ route('admin.abonnements.desactiver', $abonnement) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Suspendre cet abonnement ?')">
                            <i class="fa-solid fa-pause"></i> Suspendre
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.abonnements.activer', $abonnement) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success" onclick="return confirm('Réactiver cet abonnement ?')">
                            <i class="fa-solid fa-play"></i> Réactiver
                        </button>
                    </form>
                @endif
                <form action="{{ route('admin.abonnements.destroy', $abonnement) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cet abonnement ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> Supprimer
                    </button>
                </form>
                <a href="{{ route('admin.agences.show', $abonnement->agence_id) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-building"></i> Voir l'agence
                </a>
            </div>
        </div>
    </div>

    <!-- Informations de l'agence -->
    <div>
        <div class="panel">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-building"></i> Informations de l'agence
            </h3>
            @if($abonnement->agence)
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    @if($abonnement->agence->logo)
                        <img src="{{ asset('storage/' . $abonnement->agence->logo) }}" 
                             alt="{{ $abonnement->agence->nom_agence }}" 
                             style="width:50px;height:50px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
                    @else
                        <div style="width:50px;height:50px;border-radius:10px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:var(--gold);">
                            {{ $abonnement->agence ? Str::substr($abonnement->agence->nom_agence, 0, 2) : 'NA' }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight:600;font-size:16px;">{{ $abonnement->agence->nom_agence ?? 'N/A' }}</div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-envelope"></i> {{ $abonnement->agence->user->email ?? '' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-phone"></i> {{ $abonnement->agence->user->telephone ?? 'Non renseigné' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-location-dot"></i> {{ $abonnement->agence->adresse ?? 'Non renseignée' }}
                        </div>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Statut validation</div>
                        <div style="font-weight:600;">
                            @if($abonnement->agence->statut_validation)
                                <span class="status-pill status-active">Validée</span>
                            @else
                                <span class="status-pill status-warning">En attente</span>
                            @endif
                        </div>
                    </div>
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Biens</div>
                        <div style="font-weight:600;">{{ $abonnement->agence->biens->count() }}</div>
                    </div>
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Propositions</div>
                        <div style="font-weight:600;">{{ $abonnement->agence->propositions->count() }}</div>
                    </div>
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Membre depuis</div>
                        <div style="font-weight:600;">{{ $abonnement->agence->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>

                <div style="margin-top:12px;">
                    <a href="{{ route('admin.agences.show', $abonnement->agence_id) }}" class="btn btn-rust" style="width:100%;justify-content:center;">
                        <i class="fa-solid fa-building"></i> Voir le profil complet
                    </a>
                </div>
            @else
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0;">
                    <i class="fa-solid fa-building-circle-exclamation" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    Agence non trouvée.
                </p>
            @endif
        </div>

        <!-- Paiement PayDunya -->
        <div class="panel" style="margin-top:20px;">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-credit-card"></i> Informations de paiement
            </h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                    <div style="font-size:10px;color:var(--muted);">Statut</div>
                    <div style="font-weight:600;">
                        @if($abonnement->paydunya_status === 'paid')
                            <span class="status-pill status-active"> Payé</span>
                        @elseif($abonnement->paydunya_status === 'pending')
                            <span class="status-pill status-warning"> En attente</span>
                        @elseif($abonnement->paydunya_status === 'free')
                            <span class="status-pill status-active"> Gratuit</span>
                        @else
                            <span class="status-pill status-inactif"> Non payé</span>
                        @endif
                    </div>
                </div>
                <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                    <div style="font-size:10px;color:var(--muted);">Montant</div>
                    <div style="font-weight:600;color:var(--rust);">
                        @if($abonnement->montant == 0)
                            Gratuit
                        @else
                            {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA
                        @endif
                    </div>
                </div>
            </div>
            @if($abonnement->paydunya_status === 'pending')
                <div style="margin-top:12px;padding:10px 14px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;">
                    <div style="display:flex;align-items:center;gap:8px;">
                        <i class="fa-solid fa-clock" style="color:#E65100;"></i>
                        <span style="font-size:13px;color:#E65100;">
                            Paiement en attente de confirmation
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection