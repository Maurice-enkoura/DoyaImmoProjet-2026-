@extends('layouts.dashboard-agence')

@section('title', 'Mes biens — DoyaImmo')
@section('page_title', 'Mes biens')
@section('page_sub', 'Gérez vos biens immobiliers')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mes biens</h2>
            <p>Gérez vos biens immobiliers</p>
        </div>
        @if($peutPublier ?? false)
            <a href="{{ route('agence.biens.create') }}" class="btn btn-rust btn-sm">
                <i class="fa-solid fa-plus"></i> Nouveau bien
            </a>
        @else
            <a href="{{ route('agence.abonnement') }}" class="btn btn-ghost btn-sm" style="border-color:#D4AF37;color:#D4AF37;">
                <i class="fa-solid fa-crown"></i> Passer à Pro
            </a>
        @endif
    </div>

    <!-- ✅ Message si l'agence n'a pas l'abonnement Pro -->
    @if(!($peutPublier ?? false))
        <div class="alert alert-warning" style="background:#FFF8E1;color:#E65100;border-left-color:#F5A623;display:flex;align-items:center;gap:12px;padding:12px 20px;border-radius:var(--radius);margin-bottom:20px;border-left:4px solid;">
            <i class="fa-solid fa-crown" style="font-size:20px;"></i>
            <div style="flex:1;">
                <strong style="display:block;">Abonnement Pro requis</strong>
                <span style="font-size:13px;">Seules les agences avec un abonnement Pro peuvent publier et gérer des biens.</span>
            </div>
            <a href="{{ route('agence.abonnement') }}" style="color:var(--rust);font-weight:600;text-decoration:none;white-space:nowrap;padding:4px 12px;border:1px solid var(--rust);border-radius:6px;">
                Passer à Pro →
            </a>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fa-solid fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ session('warning') }}
        </div>
    @endif

    @if($biens->count() > 0)
    <div class="biens-grid">
        @foreach($biens as $bien)
        <div class="bien-card {{ $bien->est_vedette ? 'vedette-card' : '' }}">
            <!-- Image du bien -->
            <div class="bien-image">
                @php
                    $image = $bien->medias->where('type_media', 'image')->first();
                    $imagesCount = $bien->medias->where('type_media', 'image')->count();
                    $videosCount = $bien->medias->where('type_media', 'video')->count();
                @endphp
                @if($image)
                    <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $bien->titre }}">
                @else
                    <div class="bien-image-placeholder">
                        <i class="fa-solid fa-image"></i>
                    </div>
                @endif
                
                <!-- Badge Vedette -->
                @if($bien->est_vedette)
                    <div class="badge-vedette">
                        <i class="fa-solid fa-star"></i> Vedette
                    </div>
                @endif
                
                <!-- Statut du bien -->
                <div class="bien-status {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                    {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                </div>
                <!-- Type de bien sur l'image -->
                <div class="bien-type-badge">{{ $bien->type_bien->label() }}</div>
                <!-- Compteur de médias -->
                <div class="media-badge">
                    @if($imagesCount > 0)
                        <span><i class="fa-regular fa-image"></i> {{ $imagesCount }}</span>
                    @endif
                    @if($videosCount > 0)
                        <span><i class="fa-regular fa-circle-play"></i> {{ $videosCount }}</span>
                    @endif
                </div>
            </div>
            
            <div class="bien-body">
                <div class="bien-header">
                    <div class="bien-title">
                        {{ $bien->titre }}
                        @if($bien->est_vedette)
                            <span class="vedette-tag"><i class="fa-solid fa-star"></i></span>
                        @endif
                    </div>
                    <div class="bien-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                </div>
                
                <div class="bien-location">
                    <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}
                </div>
                
                <div class="bien-features">
                    <span class="meta-pill"><i class="fa-solid fa-home"></i> {{ $bien->type_bien->label() }}</span>
                    <span class="meta-pill">{{ $bien->type_contrat->label() }}</span>
                    <span class="meta-pill">{{ $bien->surface }} m²</span>
                    <span class="meta-pill"><i class="fa-regular fa-eye"></i> {{ $bien->vues ?? 0 }}</span>
                    @if($bien->est_vedette)
                        <span class="meta-pill vedette-pill"><i class="fa-solid fa-star"></i> Vedette</span>
                    @endif
                </div>
                
                <!-- Groupe d'actions -->
                <div class="bien-actions">
                    <!-- Actions principales -->
                    <div class="actions-primary">
                        @if($peutPublier ?? false)
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <a href="{{ route('agence.biens.edit', $bien->slug) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </a>
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <a href="{{ route('agence.biens.show', $bien->slug) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i> Voir
                            </a>
                        @else
                            <a href="#" class="btn btn-ghost btn-sm" style="opacity:0.4;cursor:not-allowed;" title="Abonnement Pro requis">
                                <i class="fa-solid fa-pen"></i> Modifier
                            </a>
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <a href="{{ route('agence.biens.show', $bien->slug) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i> Voir
                            </a>
                        @endif
                    </div>

                    <!-- Actions de gestion -->
                    <div class="actions-management">
                        @if($peutPublier ?? false)
                            @if($bien->statut)
                                <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                <form action="{{ route('agence.biens.desactiver', $bien->slug) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-warning btn-sm" 
                                            onclick="return confirm('Désactiver ce bien ? Il ne sera plus visible par les particuliers.')">
                                        <i class="fa-solid fa-eye-slash"></i> Désactiver
                                    </button>
                                </form>
                            @else
                                <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                <form action="{{ route('agence.biens.activer', $bien->slug) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-success btn-sm" 
                                            onclick="return confirm('Activer ce bien ? Il sera visible par les particuliers.')">
                                        <i class="fa-solid fa-eye"></i> Activer
                                    </button>
                                </form>
                            @endif

                            @if(!$bien->est_vedette)
                                <!-- ✅ CORRIGÉ : Utilisation du slug -->
                                <a href="{{ route('agence.biens.vedette.demander', $bien->slug) }}" class="btn btn-ghost btn-sm" style="border-color:#D4AF37;color:#D4AF37;">
                                    <i class="fa-solid fa-star"></i> Vedette
                                </a>
                            @else
                                <span class="btn btn-sm" style="background:#FFF8E1;color:#E65100;border:none;cursor:default;">
                                    <i class="fa-solid fa-star" style="color:#F5A623;"></i> En vedette
                                </span>
                            @endif

                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <form action="{{ route('agence.biens.destroy', $bien->slug) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce bien ? Cette action est irréversible.')">
                                    <i class="fa-solid fa-trash-can"></i> Supprimer
                                </button>
                            </form>
                        @else
                            <button class="btn btn-ghost btn-sm" style="opacity:0.4;cursor:not-allowed;" title="Abonnement Pro requis">
                                <i class="fa-solid fa-eye-slash"></i> Gérer
                            </button>
                            <button class="btn btn-ghost btn-sm" style="opacity:0.4;cursor:not-allowed;" title="Abonnement Pro requis">
                                <i class="fa-solid fa-trash-can"></i> Supprimer
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:30px;">
        {{ $biens->links() }}
    </div>
    @else
    <div class="empty-state">
        <i class="fa-solid fa-building"></i>
        <p>Aucun bien publié.</p>
        @if($peutPublier ?? false)
            <a href="{{ route('agence.biens.create') }}" class="btn btn-rust">
                Publier un bien
            </a>
        @else
            <div style="margin-top:12px;">
                <p style="font-size:14px;color:var(--muted);">Publiez des biens avec l'abonnement Pro</p>
                <a href="{{ route('agence.abonnement') }}" class="btn btn-gold" style="background:#D4AF37;color:#fff;border:none;padding:8px 24px;border-radius:10px;font-weight:600;text-decoration:none;display:inline-block;">
                    <i class="fa-solid fa-crown"></i> Passer à Pro
                </a>
            </div>
        @endif
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .biens-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .bien-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .bien-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .bien-card.vedette-card {
        border-color: #F5A623;
        border-width: 2px;
        position: relative;
    }

    .bien-card.vedette-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: var(--radius);
        background: linear-gradient(135deg, rgba(245, 166, 35, 0.05), transparent);
        pointer-events: none;
        z-index: 0;
    }

    .bien-image {
        height: 180px;
        background: #F0F2F5;
        position: relative;
        overflow: hidden;
    }

    .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .bien-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        font-size: 40px;
        opacity: 0.3;
    }

    .badge-vedette {
        position: absolute;
        top: 12px;
        left: 12px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        background: #F5A623;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(245, 166, 35, 0.3);
    }

    .badge-vedette i {
        font-size: 10px;
    }

    .bien-status {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        z-index: 2;
    }

    .bien-status.disponible {
        background: #1E7A47;
    }

    .bien-status.indisponible {
        background: #757575;
    }

    .bien-type-badge {
        position: absolute;
        bottom: 12px;
        left: 12px;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
        background: rgba(0,0,0,0.7);
        z-index: 2;
    }

    .media-badge {
        position: absolute;
        bottom: 12px;
        right: 12px;
        display: flex;
        gap: 6px;
        font-size: 10px;
        color: #fff;
        background: rgba(0,0,0,0.6);
        padding: 3px 10px;
        border-radius: 4px;
        z-index: 2;
    }

    .media-badge span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .bien-body {
        padding: 14px 16px 16px;
        position: relative;
        z-index: 1;
    }

    .bien-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
        margin-bottom: 2px;
    }

    .bien-title {
        font-weight: 600;
        font-size: 16px;
        color: var(--ink);
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        flex: 1;
    }

    .vedette-tag {
        color: #F5A623;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
    }

    .bien-price {
        font-weight: 700;
        color: var(--rust);
        font-size: 17px;
        white-space: nowrap;
    }

    .bien-location {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 8px;
    }

    .bien-features {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 12px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11px;
        color: var(--text-soft);
    }

    .meta-pill i {
        font-size: 10px;
    }

    .vedette-pill {
        background: #FFF8E1;
        color: #E65100;
        font-weight: 600;
    }

    .vedette-pill i {
        color: #F5A623;
    }

    .bien-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .actions-primary,
    .actions-management {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .actions-primary {
        border-bottom: 1px solid var(--border);
        padding-bottom: 8px;
    }

    .actions-management {
        padding-top: 2px;
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
    }

    .btn-rust {
        background: var(--rust);
        color: #fff;
        border: none;
    }

    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
    }

    .btn-success {
        background: #1E7A47;
        color: #fff;
        border: none;
    }

    .btn-success:hover {
        background: #156A3B;
        color: #fff;
    }

    .btn-warning {
        background: #F5A623;
        color: #fff;
        border: none;
    }

    .btn-warning:hover {
        background: #E0951A;
        color: #fff;
    }

    .btn-danger {
        background: #C62828;
        color: #fff;
        border: none;
    }

    .btn-danger:hover {
        background: #B71C1C;
        color: #fff;
    }

    .btn-gold {
        background: #D4AF37;
        color: #fff;
        border: none;
    }

    .btn-gold:hover {
        background: #C5A030;
        color: #fff;
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }

    .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
    }

    .empty-state i {
        font-size: 48px;
        display: block;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    .empty-state p {
        font-size: 16px;
        margin-bottom: 16px;
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

    .alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        border-left: 4px solid;
    }

    .alert-success {
        background: #E8F5E9;
        color: #1E7A47;
        border-left-color: #1E7A47;
    }

    .alert-error {
        background: #FFEBEE;
        color: #C62828;
        border-left-color: #C62828;
    }

    .alert-warning {
        background: #FFF8E1;
        color: #E65100;
        border-left-color: #F5A623;
    }

    @media (max-width: 768px) {
        .bien-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .bien-price {
            font-size: 16px;
        }

        .bien-actions {
            flex-direction: column;
        }

        .actions-primary,
        .actions-management {
            flex-wrap: wrap;
        }

        .actions-primary .btn,
        .actions-primary form,
        .actions-management .btn,
        .actions-management form {
            flex: 1 1 auto;
        }

        .bien-actions .btn,
        .bien-actions form {
            width: 100%;
        }

        .bien-actions .btn {
            justify-content: center;
        }

        .bien-actions form {
            display: block !important;
        }

        .bien-actions form .btn {
            width: 100%;
        }

        .alert {
            flex-wrap: wrap;
        }

        .alert a {
            margin-left: 0 !important;
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 640px) {
        .biens-grid {
            grid-template-columns: 1fr;
        }

        .bien-features {
            gap: 4px;
        }

        .meta-pill {
            font-size: 10px;
            padding: 1px 10px;
        }

        .bien-type-badge {
            font-size: 10px;
            padding: 3px 10px;
        }

        .media-badge {
            font-size: 9px;
            padding: 2px 8px;
        }

        .badge-vedette {
            font-size: 10px;
            padding: 3px 10px;
        }

        .badge-vedette i {
            font-size: 9px;
        }

        .bien-title {
            font-size: 15px;
        }

        .vedette-tag {
            font-size: 11px;
        }

        .bien-price {
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .bien-title {
            font-size: 14px;
        }

        .bien-price {
            font-size: 15px;
        }

        .bien-status {
            font-size: 10px;
            padding: 3px 10px;
        }

        .bien-image {
            height: 160px;
        }

        .btn {
            font-size: 11px;
            padding: 5px 12px;
        }

        .btn-sm {
            font-size: 11px;
            padding: 5px 12px;
        }

        .section-head {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .section-head .btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush