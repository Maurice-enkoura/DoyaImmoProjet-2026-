@extends('layouts.admin')

@section('title', 'Détail mise en vedette — Administration')
@section('page_title', 'Détail de la demande')
@section('page_sub', 'Gérez la demande de mise en vedette')

@section('content')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }
    .detail-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
    }
    .detail-card h4 {
        font-family: var(--display);
        font-size: 14px;
        margin: 0 0 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-soft);
    }
    .detail-card h4 i {
        color: var(--rust);
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
        font-size: 13px;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-row .label {
        color: var(--muted);
    }
    .detail-row .value {
        font-weight: 600;
        color: var(--ink);
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .status-badge.en_attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-badge.actif {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-badge.expire {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-badge.annule {
        background: #F5F5F5;
        color: #757575;
    }
    .actions-bar {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
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
    }
    .btn-success {
        background: #1E7A47;
        color: #fff;
        border-color: #1E7A47;
    }
    .btn-success:hover {
        background: #145A35;
        color: #fff;
    }
    .btn-danger {
        background: #C62828;
        color: #fff;
        border-color: #C62828;
    }
    .btn-danger:hover {
        background: #B71C1C;
        color: #fff;
    }
    .btn-warning {
        background: #F5A623;
        color: #fff;
        border-color: #F5A623;
    }
    .btn-warning:hover {
        background: #E0951A;
        color: #fff;
    }
    .btn-sm {
        padding: 4px 12px;
        font-size: 12px;
    }
    .comment-box {
        background: #FAFBFC;
        border-radius: 8px;
        padding: 12px 16px;
        border: 1px solid var(--border);
        margin-top: 8px;
    }
    .comment-box textarea {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        resize: vertical;
        min-height: 80px;
    }
    .comment-box textarea:focus {
        outline: none;
        border-color: var(--rust);
    }

    /* ✅ Carte du bien */
    .bien-preview {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        background: #FAFBFC;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 16px;
        margin-top: 8px;
    }
    .bien-preview .bien-image {
        width: 100px;
        height: 100px;
        border-radius: 8px;
        overflow: hidden;
        flex-shrink: 0;
        background: #F0F2F5;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bien-preview .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .bien-preview .bien-image .no-image {
        font-size: 30px;
        color: var(--muted);
        opacity: 0.3;
    }
    .bien-preview .bien-info {
        flex: 1;
    }
    .bien-preview .bien-info .bien-titre {
        font-size: 16px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 4px;
    }
    .bien-preview .bien-info .bien-adresse {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 4px;
    }
    .bien-preview .bien-info .bien-prix {
        font-size: 15px;
        font-weight: 700;
        color: var(--rust);
    }
    .bien-preview .bien-info .bien-caracteristiques {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 6px;
        font-size: 12px;
        color: var(--text-soft);
    }
    .bien-preview .bien-info .bien-caracteristiques span {
        background: #fff;
        padding: 2px 10px;
        border-radius: 4px;
        border: 1px solid var(--border);
    }

    @media (max-width: 820px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
        .bien-preview {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .bien-preview .bien-image {
            width: 120px;
            height: 120px;
        }
        .bien-preview .bien-info .bien-caracteristiques {
            justify-content: center;
        }
    }
    @media (max-width: 600px) {
        .actions-bar {
            flex-direction: column;
        }
        .actions-bar .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="view active">
    <div class="section-head">
        <div>
            <h2>Détail de la demande</h2>
            <p>Mise en vedette #{{ $mise->id }}</p>
        </div>
        <a href="{{ route('admin.mises-vedette.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    @if(session('success'))
        <div class="flash-message flash-success">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flash-message flash-error">
            <i class="fa-solid fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Statut -->
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:16px 20px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
        <div style="display:flex;align-items:center;gap:12px;">
            <span style="font-weight:600;font-size:14px;">Statut actuel :</span>
            @php
                $statutClass = match($mise->statut) {
                    'en_attente' => 'en_attente',
                    'actif' => 'actif',
                    'expire' => 'expire',
                    'annule' => 'annule',
                    default => 'en_attente'
                };
                $statutLabel = match($mise->statut) {
                    'en_attente' => 'En attente',
                    'actif' => 'Actif',
                    'expire' => 'Expiré',
                    'annule' => 'Annulé',
                    default => ucfirst($mise->statut)
                };
                $statutIcon = match($mise->statut) {
                    'en_attente' => 'fa-clock',
                    'actif' => 'fa-check-circle',
                    'expire' => 'fa-circle-exclamation',
                    'annule' => 'fa-ban',
                    default => 'fa-circle'
                };
            @endphp
            <span class="status-badge {{ $statutClass }}">
                <i class="fa-solid {{ $statutIcon }}"></i>
                {{ $statutLabel }}
            </span>
        </div>
        <div style="font-size:13px;color:var(--muted);">
            Créée le {{ $mise->created_at->format('d/m/Y à H:i') }}
        </div>
    </div>

    <!-- ✅ CARTE DU BIEN À METTRE EN VEDETTE -->
    @if($mise->bien)
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px;margin-bottom:20px;">
        <h4 style="font-family:var(--display);font-size:14px;margin:0 0 12px;display:flex;align-items:center;gap:8px;color:var(--text-soft);">
            <i class="fa-solid fa-house" style="color:var(--rust);"></i> Bien à mettre en vedette
        </h4>
        <div class="bien-preview">
            <div class="bien-image">
                @php
                    $image = $mise->bien->medias->where('type_media', 'image')->first();
                @endphp
                @if($image)
                    <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $mise->bien->titre }}">
                @else
                    <div class="no-image">
                        <i class="fa-solid fa-image"></i>
                    </div>
                @endif
            </div>
            <div class="bien-info">
                <div class="bien-titre">{{ $mise->bien->titre }}</div>
                <div class="bien-adresse">
                    <i class="fa-solid fa-location-dot" style="color:var(--muted);font-size:12px;"></i>
                    {{ $mise->bien->quartier }}
                </div>
                <div class="bien-prix">{{ number_format($mise->bien->prix, 0, ',', ' ') }} FCFA</div>
                <div class="bien-caracteristiques">
                    <span><i class="fa-solid fa-home"></i> {{ $mise->bien->type_bien->label() ?? 'N/A' }}</span>
                    <span>{{ $mise->bien->type_contrat->label() ?? 'N/A' }}</span>
                    <span><i class="fa-solid fa-vector-square"></i> {{ $mise->bien->surface }} m²</span>
                    @if($mise->bien->nombre_chambres > 0)
                        <span><i class="fa-solid fa-bed"></i> {{ $mise->bien->nombre_chambres }} ch.</span>
                    @endif
                    @if($mise->bien->est_vedette)
                        <span style="background:#FFF8E1;color:#E65100;border-color:#FFE0B2;">
                            <i class="fa-solid fa-star" style="color:#F5A623;"></i> En vedette
                        </span>
                    @endif
                </div>
                <div style="margin-top:8px;">
                    <a href="{{ route('admin.biens.show', $mise->bien) }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-eye"></i> Voir le bien
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div style="background:#FFEBEE;border:1px solid #FFCDD2;border-radius:var(--radius);padding:16px 20px;margin-bottom:20px;">
        <p style="margin:0;color:#C62828;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <strong>Bien supprimé</strong> — Le bien associé à cette demande n'existe plus.
        </p>
    </div>
    @endif

    <!-- Grille détails -->
    <div class="detail-grid">
        <!-- Informations de la demande -->
        <div class="detail-card">
            <h4><i class="fa-solid fa-file-invoice"></i> Informations de la demande</h4>
            <div class="detail-row">
                <span class="label">Demande n°</span>
                <span class="value">#{{ $mise->id }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Agence</span>
                <span class="value">
                    @if($mise->agence)
                        <a href="{{ route('admin.agences.show', $mise->agence) }}" style="color:var(--rust);text-decoration:none;">
                            {{ $mise->agence->nom_agence ?? 'N/A' }}
                        </a>
                    @else
                        <span style="color:var(--muted);">Agence supprimée</span>
                    @endif
                </span>
            </div>
            <div class="detail-row">
                <span class="label">Contact agence</span>
                <span class="value">
                    @if($mise->agence && $mise->agence->user)
                        {{ $mise->agence->user->email ?? 'N/A' }}
                    @else
                        <span style="color:var(--muted);">N/A</span>
                    @endif
                </span>
            </div>
            <div class="detail-row">
                <span class="label">Durée demandée</span>
                <span class="value">{{ $mise->duree }} jour{{ $mise->duree > 1 ? 's' : '' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Montant</span>
                <span class="value" style="font-size:18px;color:var(--rust);">
                    {{ number_format($mise->montant, 0, ',', ' ') }} FCFA
                </span>
            </div>
        </div>

        <!-- Période -->
        <div class="detail-card">
            <h4><i class="fa-solid fa-calendar"></i> Période de la vedette</h4>
            <div class="detail-row">
                <span class="label">Date de début</span>
                <span class="value">
                    @if($mise->date_debut)
                        {{ $mise->date_debut->format('d/m/Y H:i') }}
                    @else
                        <span style="color:var(--muted);">Non démarré</span>
                    @endif
                </span>
            </div>
            <div class="detail-row">
                <span class="label">Date de fin</span>
                <span class="value">
                    @if($mise->date_fin)
                        {{ $mise->date_fin->format('d/m/Y H:i') }}
                        @if($mise->estActive())
                            <span style="font-size:12px;color:#1E7A47;display:block;">
                                ✅ {{ $mise->getJoursRestants() }} jour(s) restant(s)
                            </span>
                        @endif
                    @else
                        <span style="color:var(--muted);">Non défini</span>
                    @endif
                </span>
            </div>
            <div class="detail-row">
                <span class="label">Validée par</span>
                <span class="value">
                    @if($mise->valideePar)
                        {{ $mise->valideePar->prenom ?? '' }} {{ $mise->valideePar->nom ?? '' }}
                        <span style="font-size:12px;color:var(--muted);display:block;">
                            le {{ $mise->validee_par_admin_at ? $mise->validee_par_admin_at->format('d/m/Y H:i') : '' }}
                        </span>
                    @else
                        <span style="color:var(--muted);">En attente de validation</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Commentaire -->
    @if($mise->commentaire_admin)
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:16px 20px;margin-bottom:20px;">
            <h4 style="font-family:var(--display);font-size:14px;margin:0 0 8px;display:flex;align-items:center;gap:8px;color:var(--text-soft);">
                <i class="fa-solid fa-comment"></i> Commentaire de l'administrateur
            </h4>
            <p style="margin:0;font-size:13px;color:var(--ink);background:#FAFBFC;padding:12px 16px;border-radius:8px;border:1px solid var(--border);">
                {{ $mise->commentaire_admin }}
            </p>
        </div>
    @endif

    <!-- Actions -->
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px;">
        <h4 style="font-family:var(--display);font-size:14px;margin:0 0 12px;display:flex;align-items:center;gap:8px;color:var(--text-soft);">
            <i class="fa-solid fa-gear"></i> Actions
        </h4>

        @if($mise->statut === 'en_attente')
            <div class="actions-bar">
                <form action="{{ route('admin.mises-vedette.valider', $mise) }}" method="POST">
                    @csrf
                    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:flex-end;">
                        <div>
                            <label style="font-size:12px;color:var(--muted);display:block;margin-bottom:4px;">Commentaire (optionnel)</label>
                            <input type="text" name="commentaire" placeholder="Commentaire pour l'agence..." style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;width:250px;font-family:inherit;">
                        </div>
                        <button type="submit" class="btn btn-success" onclick="return confirm('Valider cette mise en vedette ? Le bien sera mis en avant pour {{ $mise->duree }} jours.')">
                            <i class="fa-solid fa-check"></i> Valider la mise en vedette
                        </button>
                    </div>
                </form>
                <form action="{{ route('admin.mises-vedette.annuler', $mise) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Annuler cette demande ?')">
                        <i class="fa-solid fa-xmark"></i> Annuler la demande
                    </button>
                </form>
            </div>
        @elseif($mise->statut === 'actif')
            <div class="actions-bar">
                <form action="{{ route('admin.mises-vedette.annuler', $mise) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Désactiver cette mise en vedette ? Le bien ne sera plus en avant.')">
                        <i class="fa-solid fa-eye-slash"></i> Désactiver la vedette
                    </button>
                </form>
                <a href="{{ route('admin.biens.show', $mise->bien) }}" class="btn btn-ghost">
                    <i class="fa-solid fa-eye"></i> Voir le bien
                </a>
            </div>
        @else
            <div class="actions-bar">
                <span style="color:var(--muted);font-size:13px;">Cette demande n'est plus modifiable.</span>
                <a href="{{ route('admin.mises-vedette.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-arrow-left"></i> Retour à la liste
                </a>
                @if($mise->bien)
                    <a href="{{ route('admin.biens.show', $mise->bien) }}" class="btn btn-ghost">
                        <i class="fa-solid fa-eye"></i> Voir le bien
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Bouton retour -->
    <div style="margin-top:16px;">
        <a href="{{ route('admin.mises-vedette.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour à la liste des demandes
        </a>
    </div>
</div>
@endsection