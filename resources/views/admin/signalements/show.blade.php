@extends('layouts.admin')

@section('title', 'Détail du signalement — Administration DoyaImmo')
@section('page_title', 'Détail du signalement')
@section('page_sub', $signalement->motif ?? 'N/A')

@section('content')
<style>
    .statut-arnaque { background: #FFEBEE; color: #C62828; }
    .statut-contenu_inapproprie { background: #FFF3E0; color: #E65100; }
    .statut-fausse_annonce { background: #F3E5F5; color: #6A1B9A; }
    .statut-comportement_inapproprié { background: #FFE0B2; color: #BF360C; }
    .statut-fraude { background: #FFEBEE; color: #C62828; }
    
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
    
    .status-traite {
        background: #E8F5E9;
        color: #1E7A47;
    }
    
    .status-rejete {
        background: #FFEBEE;
        color: #C62828;
    }
    
    .status-active {
        background: #E8F5E9;
        color: #1E7A47;
    }
    
    .status-annule {
        background: #FFEBEE;
        color: #C62828;
    }
    
    .btn-danger {
        background: #C62828;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }
    
    .btn-danger:hover {
        background: #B71C1C;
    }
    
    .btn-success {
        background: #1E7A47;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }
    
    .btn-success:hover {
        background: #156A3B;
    }
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.signalements.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux signalements
    </a>
</div>

<div class="grid-2">
    <!-- Informations du signalement -->
    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
                <h3 style="font-size:18px;font-weight:700;">
                    <i class="fa-solid fa-flag" style="color:#C62828;"></i> 
                    {{ is_object($signalement->motif) ? $signalement->motif->label() : $signalement->motif }}
                </h3>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">
                    <i class="fa-solid fa-calendar"></i> 
                    Signalé le {{ $signalement->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
            @php
                // ✅ CORRECTION : Récupérer la valeur du statut
                $statusValue = is_object($signalement->statut) ? $signalement->statut->value : $signalement->statut;
                
                $statusColors = [
                    'en_attente' => ['class' => 'status-en_attente', 'label' => 'En attente'],
                    'traite' => ['class' => 'status-traite', 'label' => 'Traité'],
                    'rejete' => ['class' => 'status-rejete', 'label' => 'Rejeté'],
                ];
                
                // ✅ Utiliser la valeur pour accéder au tableau
                $statusInfo = $statusColors[$statusValue] ?? $statusColors['en_attente'];
            @endphp
            <span class="status-pill {{ $statusInfo['class'] }}">
                {{ $statusInfo['label'] }}
            </span>
        </div>

        <!-- Détails du signalement -->
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">Description</div>
                <div style="font-weight:600;font-size:13px;margin-top:4px;">{{ $signalement->description }}</div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Type signalé</div>
                <div style="font-weight:600;">
                    @php
                        $type = class_basename($signalement->signalable_type);
                        $icon = match($type) {
                            'BienImmobilier' => 'fa-solid fa-house',
                            'DemandeImmobiliere' => 'fa-solid fa-file',
                            'Proposition' => 'fa-solid fa-handshake',
                            default => 'fa-solid fa-flag'
                        };
                    @endphp
                    <i class="{{ $icon }}"></i> {{ $type }}
                </div>
            </div>
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">ID signalé</div>
                <div style="font-weight:600;">#{{ $signalement->signalable_id }}</div>
            </div>
            @if($signalement->date_traitement)
                <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                    <div style="font-size:11px;color:var(--muted);">Date de traitement</div>
                    <div style="font-weight:600;">{{ \Carbon\Carbon::parse($signalement->date_traitement)->format('d/m/Y H:i') }}</div>
                </div>
            @endif
            @if($signalement->commentaire_admin)
                <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                    <div style="font-size:11px;color:var(--muted);">Commentaire admin</div>
                    <div style="font-weight:600;font-size:13px;">{{ $signalement->commentaire_admin }}</div>
                </div>
            @endif
        </div>

        <!-- Auteur du signalement -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <h4 style="font-weight:600;font-size:14px;margin-bottom:8px;">
                <i class="fa-solid fa-user"></i> Auteur du signalement
            </h4>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:50px;height:50px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:var(--rust);">
                    {{ strtoupper(substr($signalement->particulier->user->prenom ?? 'U', 0, 1)) }}{{ strtoupper(substr($signalement->particulier->user->nom ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <div style="font-weight:600;">{{ $signalement->particulier->user->prenom ?? '' }} {{ $signalement->particulier->user->nom ?? '' }}</div>
                    <div style="font-size:12px;color:var(--muted);">
                        <i class="fa-solid fa-envelope"></i> {{ $signalement->particulier->user->email ?? '' }}
                    </div>
                    <div style="font-size:12px;color:var(--muted);">
                        <i class="fa-solid fa-phone"></i> {{ $signalement->particulier->user->telephone ?? 'Non renseigné' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations de l'agence -->
    <div>
        <div class="panel" style="margin-bottom:20px;">
            <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                <i class="fa-solid fa-building"></i> Agence concernée
            </h3>
            @if($signalement->agence)
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    @if($signalement->agence->logo)
                        <img src="{{ asset('storage/' . $signalement->agence->logo) }}" 
                             alt="{{ $signalement->agence->nom_agence }}" 
                             style="width:50px;height:50px;object-fit:cover;border-radius:10px;border:1px solid var(--border);">
                    @else
                        <div style="width:50px;height:50px;border-radius:10px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:var(--gold);">
                            {{ strtoupper(substr($signalement->agence->nom_agence ?? 'NA', 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight:600;">{{ $signalement->agence->nom_agence ?? 'N/A' }}</div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-envelope"></i> {{ $signalement->agence->user->email ?? '' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-phone"></i> {{ $signalement->agence->user->telephone ?? 'Non renseigné' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-location-dot"></i> {{ $signalement->agence->adresse ?? 'Non renseignée' }}
                        </div>
                    </div>
                </div>

                <!-- Statut de l'agence -->
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Statut validation</div>
                        <div style="font-weight:600;">
                            @if($signalement->agence->statut_validation)
                                <span class="status-pill status-active">Validée</span>
                            @else
                                <span class="status-pill status-en_attente">En attente</span>
                            @endif
                        </div>
                    </div>
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Bloquée</div>
                        <div style="font-weight:600;">
                            @if(isset($signalement->agence->bloque) && $signalement->agence->bloque)
                                <span class="status-pill status-annule">Oui</span>
                            @else
                                <span class="status-pill status-active">Non</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Biens de l'agence -->
                <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-house"></i> Biens publiés: 
                            <strong>{{ $signalement->agence->biens->count() }}</strong>
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-file"></i> Documents: 
                            <strong>{{ $signalement->agence->documents->count() }}</strong>
                        </div>
                    </div>
                    @if($signalement->agence->biens->count() > 0)
                        <div style="margin-top:8px;max-height:150px;overflow-y:auto;">
                            @foreach($signalement->agence->biens->take(5) as $bien)
                                <div style="display:flex;justify-content:space-between;align-items:center;padding:4px 8px;background:#FAFBFC;border-radius:4px;margin-bottom:4px;">
                                    <span style="font-size:12px;">{{ $bien->titre }}</span>
                                    <span style="font-size:11px;color:var(--muted);">{{ number_format($bien->prix, 0, ',', ' ') }} F</span>
                                </div>
                            @endforeach
                            @if($signalement->agence->biens->count() > 5)
                                <div style="font-size:11px;color:var(--muted);text-align:center;margin-top:4px;">
                                    + {{ $signalement->agence->biens->count() - 5 }} autres biens
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <p style="color:var(--muted);font-size:13px;text-align:center;padding:20px 0;">
                    <i class="fa-solid fa-building-circle-exclamation" style="font-size:32px;display:block;margin-bottom:8px;"></i>
                    Aucune agence associée à ce signalement.
                </p>
            @endif
        </div>

        <!-- ==================== BOUTONS D'ACTION ==================== -->
        @php
            $statusValue = is_object($signalement->statut) ? $signalement->statut->value : $signalement->statut;
        @endphp
        
        @if($statusValue === 'en_attente')
            <div class="panel" style="border:2px solid #E65100;">
                <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;color:#E65100;">
                    <i class="fa-solid fa-gavel"></i> Traiter le signalement
                </h3>
                
                <form action="{{ route('admin.signalements.traiter', $signalement) }}" method="POST">
                    @csrf
                    
                    <div style="display:flex;flex-direction:column;gap:12px;">
                        <!-- Boutons d'action -->
                        <div style="display:flex;gap:12px;flex-wrap:wrap;">
                            <button type="submit" name="action" value="bloquer" class="btn btn-danger" style="flex:1;" onclick="return confirm('⚠️ Bloquer définitivement cette agence ? Cette action est irréversible.')">
                                <i class="fa-solid fa-ban"></i> Bloquer l'agence
                            </button>
                            <button type="submit" name="action" value="rejeter" class="btn btn-success" style="flex:1;" onclick="return confirm('Rejeter ce signalement ?')">
                                <i class="fa-solid fa-check"></i> Rejeter le signalement
                            </button>
                        </div>
                        
                        <!-- Champ Motif (obligatoire pour rejet) -->
                        <div>
                            <label for="motif" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                                <i class="fa-solid fa-pen"></i> Motif du rejet <span style="color:#C62828;">*</span>
                                <span style="font-weight:400;color:var(--muted);font-size:11px;">(obligatoire pour un rejet)</span>
                            </label>
                            <textarea name="motif" id="motif" rows="3" 
                                      style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;"
                                      placeholder="Expliquez pourquoi vous rejetez ce signalement..."></textarea>
                        </div>
                        
                        <!-- Champ Commentaire (optionnel) -->
                        <div>
                            <label for="commentaire" style="display:block;font-weight:600;font-size:13px;margin-bottom:4px;">
                                <i class="fa-solid fa-comment"></i> Commentaire
                                <span style="font-weight:400;color:var(--muted);font-size:11px;">(optionnel)</span>
                            </label>
                            <input type="text" name="commentaire" id="commentaire" 
                                   style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;font-family:inherit;"
                                   placeholder="Ajoutez un commentaire sur ce traitement...">
                        </div>
                    </div>
                </form>
            </div>
        @else
            <div class="panel" style="background:#F7F9FC;border:1px solid var(--border);">
                <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;">
                    <i class="fa-solid fa-info-circle" style="color:var(--muted);font-size:18px;"></i>
                    <div>
                        <span style="font-size:13px;color:var(--muted);">
                            <strong>Ce signalement a été traité</strong>
                        </span>
                        <br>
                        <span style="font-size:12px;color:var(--muted);">
                            {{ $signalement->date_traitement ? 'Le ' . \Carbon\Carbon::parse($signalement->date_traitement)->format('d/m/Y H:i') : '' }}
                        </span>
                        @if($statusValue === 'traite')
                            <span class="status-pill status-active" style="margin-left:8px;">Traité</span>
                        @elseif($statusValue === 'rejete')
                            <span class="status-pill status-annule" style="margin-left:8px;">Rejeté</span>
                        @endif
                        @if($signalement->commentaire_admin)
                            <div style="margin-top:8px;padding:8px 12px;background:#fff;border-radius:6px;border:1px solid var(--border);">
                                <strong style="font-size:12px;">Commentaire :</strong>
                                <span style="font-size:13px;">{{ $signalement->commentaire_admin }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- Lien vers l'agence -->
        @if($signalement->agence)
            <div style="margin-top:12px;">
                <a href="{{ route('admin.agences.show', $signalement->agence_id) }}" class="btn btn-ghost" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-building"></i> Voir le profil complet de l'agence
                </a>
            </div>
        @endif
    </div>
</div>
@endsection