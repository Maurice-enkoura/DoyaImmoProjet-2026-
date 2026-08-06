@extends('layouts.admin')

@section('title', 'Détail de l\'utilisateur — Administration DoyaImmo')
@section('page_title', 'Détail de l\'utilisateur')
@section('page_sub', $user->prenom . ' ' . $user->nom)

@section('content')
<div style="margin-bottom:20px;">
    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux utilisateurs
    </a>
</div>

<div class="grid-2">
    <!-- Informations générales -->
    <div class="panel">
        <div style="display:flex;align-items:center;gap:16px;margin-bottom:16px;">
            <div style="width:60px;height:60px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:var(--rust);">
                {{ strtoupper(substr($user->prenom ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->nom ?? 'U', 0, 1)) }}
            </div>
            <div>
                <h3 style="font-size:18px;font-weight:700;">{{ $user->prenom }} {{ $user->nom }}</h3>
                <div style="font-size:13px;color:var(--muted);">
                    <i class="fa-solid fa-envelope"></i> {{ $user->email }}
                </div>
                <div style="font-size:13px;color:var(--muted);">
                    <i class="fa-solid fa-phone"></i> {{ $user->telephone ?? 'Non renseigné' }}
                </div>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Rôle</div>
                <div style="font-weight:600;">
                    <span class="status-pill 
                        @if($user->isAdmin()) status-active
                        @elseif($user->isAgence()) status-confirme
                        @else status-en_attente
                        @endif">
                        {{ $user->role }}
                    </span>
                </div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Statut</div>
                <div style="font-weight:600;">
                    @if($user->email_verified_at)
                        <span class="status-pill status-active">Email vérifié</span>
                    @else
                        <span class="status-pill status-en_attente"> Non vérifié</span>
                    @endif
                </div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Membre depuis</div>
                <div style="font-weight:600;">{{ $user->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Dernière mise à jour</div>
                <div style="font-weight:600;">{{ $user->updated_at->format('d/m/Y H:i') }}</div>
            </div>
            @if($user->isAgence())
                <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                    <div style="font-size:11px;color:var(--muted);">Abonnement</div>
                    <div style="font-weight:600;">
                        @php
                            $abonnement = $user->agence->abonnements()->where('statut', true)->where('date_fin', '>', now())->first();
                        @endphp
                        @if($abonnement)
                            <span class="status-pill status-active">
                                <i class="fa-solid fa-crown"></i> 
                                {{ is_object($abonnement->formule) ? $abonnement->formule->label() : ucfirst($abonnement->formule) }}
                            </span>
                            <span style="font-size:12px;color:var(--muted);margin-left:8px;">
                                Expire le {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}
                            </span>
                            <br>
                            <span style="font-size:12px;color:var(--muted);">
                                Montant: {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA
                            </span>
                        @else
                            <span class="status-pill status-inactif">
                                <i class="fa-solid fa-circle-xmark"></i> Aucun abonnement actif
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Informations spécifiques selon le rôle -->
        @if($user->isAgence() && $user->agence)
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-weight:600;margin-bottom:8px;">
                    <i class="fa-solid fa-building"></i> Informations de l'agence
                </h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;color:var(--text-soft);">
                    <div><strong>Nom :</strong> {{ $user->agence->nom_agence }}</div>
                    <div><strong>Adresse :</strong> {{ $user->agence->adresse ?? 'Non renseignée' }}</div>
                    <div>
                        <strong>Statut :</strong> 
                        @if($user->agence->statut_validation)
                            <span class="status-pill status-active">Validée</span>
                        @else
                            <span class="status-pill status-en_attente">En attente</span>
                        @endif
                    </div>
                    <div>
                        <strong>Bloquée :</strong>
                        @if(isset($user->agence->bloque) && $user->agence->bloque)
                            <span class="status-pill status-annule">Oui</span>
                        @else
                            <span class="status-pill status-active">Non</span>
                        @endif
                    </div>
                    <div><strong>Biens :</strong> {{ $user->agence->biens->count() }}</div>
                    <div><strong>Documents :</strong> {{ $user->agence->documents->count() }}</div>
                </div>
                @if($user->agence->description)
                    <div style="margin-top:8px;padding:10px 14px;background:#F7F9FC;border-radius:8px;font-size:13px;color:var(--text-soft);">
                        <strong>Description :</strong> {{ $user->agence->description }}
                    </div>
                @endif
            </div>
        @endif

        @if($user->isParticulier() && $user->particulier)
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-weight:600;margin-bottom:8px;">
                    <i class="fa-solid fa-user"></i> Informations du particulier
                </h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-size:13px;color:var(--text-soft);">
                    <div><strong>Profession :</strong> {{ $user->particulier->profession ?? 'Non renseignée' }}</div>
                    <div><strong>Adresse :</strong> {{ $user->particulier->adresse ?? 'Non renseignée' }}</div>
                </div>
            </div>
        @endif

        @if($user->isAdmin() && $user->administrateur)
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-weight:600;margin-bottom:8px;">
                    <i class="fa-solid fa-user-shield"></i> Informations administrateur
                </h4>
                <div style="font-size:13px;color:var(--text-soft);">
                    <div><strong>Fonction :</strong> {{ $user->administrateur->fonction ?? 'Administrateur' }}</div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:8px;flex-wrap:wrap;">
            <!-- PAS de bouton MODIFIER pour les admins -->
            
            <!-- Bloquer/Débloquer - PAS pour les admins -->
            @if(!$user->isAdmin() && $user->isAgence())
                @if(isset($user->agence->bloque) && $user->agence->bloque)
                    <form action="{{ route('admin.utilisateurs.toggle-block', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fa-solid fa-unlock"></i> Débloquer
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.utilisateurs.toggle-block', $user) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Bloquer cet utilisateur ?')">
                            <i class="fa-solid fa-ban"></i> Bloquer
                        </button>
                    </form>
                @endif
            @endif
            
            <!-- Supprimer - PAS pour les admins -->
            @if(!$user->isAdmin())
                <form action="{{ route('admin.utilisateurs.destroy', $user) }}" method="POST" onsubmit="return confirm('Supprimer définitivement cet utilisateur ? Cette action est irréversible.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-trash-can"></i> Supprimer
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Activités récentes -->
    <div class="panel">
        <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
            <i class="fa-solid fa-clock-rotate-left"></i> Activités récentes
        </h3>
        
        @if($user->isAgence())
            <div style="margin-bottom:16px;">
                <h4 style="font-size:13px;font-weight:600;color:var(--muted);margin-bottom:8px;">
                    <i class="fa-solid fa-house"></i> Derniers biens publiés
                </h4>
                @if($user->agence && $user->agence->biens->count() > 0)
                    @foreach($user->agence->biens->take(5) as $bien)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
                            <div>
                                <div style="font-weight:600;font-size:13px;">{{ $bien->titre }}</div>
                                <div style="font-size:12px;color:var(--muted);">
                                    {{ number_format($bien->prix, 0, ',', ' ') }} FCFA - {{ $bien->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                            <span class="status-pill {{ $bien->statut ? 'status-active' : 'status-inactif' }}">
                                {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <p style="color:var(--muted);font-size:13px;">Aucun bien publié.</p>
                @endif
            </div>
        @endif

        @if($user->isParticulier())
            <div style="margin-bottom:16px;">
                <h4 style="font-size:13px;font-weight:600;color:var(--muted);margin-bottom:8px;">
                    <i class="fa-solid fa-house-circle-check"></i> Dernières demandes
                </h4>
                @if($user->particulier && $user->particulier->demandes->count() > 0)
                    @foreach($user->particulier->demandes->take(5) as $demande)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
                            <div>
                                <div style="font-weight:600;font-size:13px;">
                                    {{ $demande->type_operation }} - {{ $demande->type_bien }}
                                </div>
                                <div style="font-size:12px;color:var(--muted);">
                                    {{ $demande->created_at->format('d/m/Y') }}
                                </div>
                            </div>
                            <span class="status-pill status-{{ $demande->statut }}">
                                {{ $demande->statut }}
                            </span>
                        </div>
                    @endforeach
                @else
                    <p style="color:var(--muted);font-size:13px;">Aucune demande effectuée.</p>
                @endif
            </div>
        @endif

        <!-- Signalements -->
        <div>
            <h4 style="font-size:13px;font-weight:600;color:var(--muted);margin-bottom:8px;">
                <i class="fa-solid fa-flag"></i> Signalements
            </h4>
            @php
                $signalements = \App\Models\Signalement::where('particulier_id', $user->particulier->id ?? 0)->take(5)->get();
            @endphp
            @if($signalements->count() > 0)
                @foreach($signalements as $signalement)
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
                        <div>
                            <div style="font-weight:600;font-size:13px;">{{ $signalement->motif }}</div>
                            <div style="font-size:12px;color:var(--muted);">
                                {{ $signalement->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                        <span class="status-pill status-{{ $signalement->statut }}">
                            {{ $signalement->statut }}
                        </span>
                    </div>
                @endforeach
            @else
                <p style="color:var(--muted);font-size:13px;">Aucun signalement.</p>
            @endif
        </div>

        <!-- Historique des abonnements pour les agences -->
        @if($user->isAgence() && $user->agence)
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                <h4 style="font-size:13px;font-weight:600;color:var(--muted);margin-bottom:8px;">
                    <i class="fa-solid fa-clock-rotate-left"></i> Historique des abonnements
                </h4>
                @if($user->agence->abonnements->count() > 0)
                    @foreach($user->agence->abonnements->take(5) as $abonnement)
                        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border);">
                            <div>
                                <div style="font-weight:600;font-size:13px;">
                                    {{ is_object($abonnement->formule) ? $abonnement->formule->label() : ucfirst($abonnement->formule) }}
                                </div>
                                <div style="font-size:12px;color:var(--muted);">
                                    {{ number_format($abonnement->montant, 0, ',', ' ') }} FCFA
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-size:12px;color:var(--muted);">
                                    {{ \Carbon\Carbon::parse($abonnement->date_debut)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}
                                </div>
                                <span class="status-pill {{ $abonnement->statut && $abonnement->date_fin > now() ? 'status-active' : 'status-inactif' }}">
                                    {{ $abonnement->statut && $abonnement->date_fin > now() ? 'Actif' : 'Expiré' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="color:var(--muted);font-size:13px;">Aucun abonnement.</p>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection