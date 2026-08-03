@extends('layouts.app')

@section('title', $bien->titre . ' — DoyaImmo')

@section('content')
<div class="wrap section" style="padding-top:36px;">
    <a href="{{ route('biens.index') }}" style="font-size:13px; color:var(--muted); display:inline-block; margin-bottom:20px;">
        ← Retour aux biens
    </a>

    <div class="detail-grid">
        <div>
            <!-- Galerie d'images -->
            <div style="background:#E8ECF0;border-radius:var(--radius);overflow:hidden;margin-bottom:24px;position:relative;min-height:300px;">
                @if($bien->medias->first())
                    <img src="{{ asset('storage/' . $bien->medias->first()->fichier) }}" 
                         alt="{{ $bien->titre }}" 
                         style="width:100%;height:400px;object-fit:cover;">
                @else
                    <div style="display:flex;align-items:center;justify-content:center;height:400px;color:var(--muted);">
                        <i class="fa-solid fa-image" style="font-size:48px;opacity:0.3;"></i>
                    </div>
                @endif

                @if($bien->medias->count() > 1)
                    <div style="position:absolute;bottom:16px;right:16px;display:flex;gap:8px;">
                        @foreach($bien->medias->take(4) as $media)
                            <div style="width:50px;height:50px;border-radius:8px;overflow:hidden;border:2px solid rgba(255,255,255,0.8);">
                                <img src="{{ asset('storage/' . $media->fichier) }}" 
                                     alt="" 
                                     style="width:100%;height:100%;object-fit:cover;">
                            </div>
                        @endforeach
                        @if($bien->medias->count() > 4)
                            <div style="width:50px;height:50px;border-radius:8px;background:rgba(0,0,0,0.6);color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;border:2px solid rgba(255,255,255,0.8);">
                                +{{ $bien->medias->count() - 4 }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <div class="panel">
                <h1 style="font-family:var(--display);font-weight:800;font-size:28px;margin-bottom:6px;">
                    {{ $bien->titre }}
                </h1>
                <div style="font-size:14px;color:var(--muted);margin-bottom:14px;">
                    <i class="fa-solid fa-location-dot"></i> {{ $bien->adresse }} · {{ $bien->quartier }}
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:20px;">
                    <div style="background:var(--rust-soft);color:var(--rust);padding:8px 16px;border-radius:12px;font-weight:700;font-size:18px;">
                        {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                    </div>
                    <span class="meta-pill">{{ $bien->type_contrat->label() }}</span>
                    <span class="meta-pill">{{ $bien->type_bien->label() }}</span>
                    @if($bien->parking_disponible)
                        <span class="meta-pill"><i class="fa-solid fa-car"></i> Parking</span>
                    @endif
                    @if($bien->est_meuble)
                        <span class="meta-pill"><i class="fa-solid fa-couch"></i> Meublé</span>
                    @endif
                </div>

                <h3 style="font-family:var(--display);font-size:16px;margin-bottom:12px;">Description</h3>
                <p style="font-size:14px;line-height:1.8;color:var(--text-soft);">
                    {{ $bien->description }}
                </p>
            </div>

            <div class="panel" style="margin-top:20px;">
                <h3 style="font-family:var(--display);font-size:15px;margin-bottom:6px;">Caractéristiques</h3>
                <div class="info-row"><span>Surface</span><span>{{ $bien->surface }} m²</span></div>
                <div class="info-row"><span>Nombre de chambres</span><span>{{ $bien->nombre_chambres }}</span></div>
                <div class="info-row"><span>Nombre de salles de bain</span><span>{{ $bien->nombre_salles_bain }}</span></div>
                <div class="info-row"><span>Parking</span><span>{{ $bien->parking_disponible ? 'Disponible' : 'Non disponible' }}</span></div>
                <div class="info-row"><span>Meublé</span><span>{{ $bien->est_meuble ? 'Oui' : 'Non' }}</span></div>
                <div class="info-row"><span>Type de contrat</span><span>{{ $bien->type_contrat->label() }}</span></div>
                <div class="info-row"><span>Publié le</span><span>{{ $bien->created_at->format('d/m/Y') }}</span></div>
            </div>
        </div>

        <div class="sticky-box">
            <!-- Carte Agence avec coordonnées -->
            <div class="panel">
                <h3 style="font-family:var(--display);font-size:15px;margin-bottom:14px;">Agence</h3>
                
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <div style="width:48px;height:48px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--rust);">
                        {{ substr($bien->agence->nom_agence, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:15px;">{{ $bien->agence->nom_agence }}</div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-star" style="color:#F5A623;"></i> 
                            {{ number_format($bien->agence->note_moyenne, 1) }} / 5
                            ({{ $bien->agence->evaluations->count() }} avis)
                        </div>
                    </div>
                </div>

                <div style="font-size:13px;color:var(--text-soft);margin-bottom:12px;">
                    <i class="fa-solid fa-location-dot"></i> {{ $bien->agence->adresse }}
                </div>

                <!-- Coordonnées de contact visibles pour tous -->
                <div style="background:#F7F9FC;border-radius:10px;padding:12px 14px;margin-bottom:14px;border:1px solid var(--border);">
                    @if($bien->agence->user->telephone)
                        <div style="display:flex;align-items:center;gap:10px;padding:4px 0;">
                            <i class="fa-solid fa-phone" style="color:var(--rust);width:16px;"></i>
                            <a href="tel:{{ $bien->agence->user->telephone }}" style="color:var(--ink);text-decoration:none;font-weight:500;">
                                {{ $bien->agence->user->telephone }}
                            </a>
                        </div>
                    @endif
                    @if($bien->agence->user->email)
                        <div style="display:flex;align-items:center;gap:10px;padding:4px 0;">
                            <i class="fa-solid fa-envelope" style="color:var(--rust);width:16px;"></i>
                            <a href="mailto:{{ $bien->agence->user->email }}" style="color:var(--ink);text-decoration:none;font-weight:500;">
                                {{ $bien->agence->user->email }}
                            </a>
                        </div>
                    @endif
                </div>

                <a href="{{ route('agences.public.show', $bien->agence) }}" class="btn btn-ghost btn-block">
                    <i class="fa-solid fa-building"></i> Voir le profil de l'agence
                </a>
            </div>

            <!-- Carte : Vous cherchez un logement personnalisé ? -->
            <div class="panel" style="margin-top:16px;background:var(--rust-soft);border-color:rgba(181,80,42,0.2);">
                <div style="text-align:center;">
                    <div style="font-size:32px;margin-bottom:8px;">🏠</div>
                    <h3 style="font-family:var(--display);font-size:16px;margin-bottom:6px;color:var(--ink);">
                        Vous cherchez un logement ?
                    </h3>
                    <p style="font-size:13px;color:var(--text-soft);line-height:1.6;margin-bottom:14px;">
                        Publiez vos critères et recevez des propositions sur mesure des agences.
                    </p>
                    
                    @auth
                        @if(auth()->user()->isParticulier())
                            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust btn-block">
                                <i class="fa-solid fa-plus"></i> Publier un besoin
                            </a>
                        @elseif(auth()->user()->isAgence())
                            <a href="{{ route('agence.dashboard') }}" class="btn btn-ghost btn-block">
                                <i class="fa-solid fa-gauge"></i> Tableau de bord
                            </a>
                        @endif
                    @else
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <a href="{{ route('register.particulier') }}" class="btn btn-rust btn-block">
                                <i class="fa-solid fa-user-plus"></i> Créer un compte
                            </a>
                            <p style="font-size:11px;color:var(--muted);">
                                <i class="fa-regular fa-circle-check"></i> Gratuit · En 2 minutes
                            </p>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Signalement -->
            @auth
                @if(auth()->user()->isParticulier())
                    <div class="panel" style="margin-top:16px;">
                        <h3 style="font-family:var(--display);font-size:14px;margin-bottom:12px;">Signaler ce bien</h3>
                        <p style="font-size:12px;color:var(--muted);margin-bottom:12px;">
                            Vous avez remarqué une anomalie ou une information incorrecte ?
                        </p>
                        <a href="{{ route('particulier.signalements.create-bien', $bien) }}" class="btn btn-ghost btn-sm btn-block" style="color:#C62828;border-color:#FFCDD2;">
                            <i class="fa-solid fa-flag"></i> Signaler ce bien
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    </div>

    <!-- Biens similaires -->
    @if($biensSimilaires->count() > 0)
    <div style="margin-top:48px;">
        <h2 style="font-family:var(--display);font-size:22px;margin-bottom:20px;">Biens similaires</h2>
        <div class="biens-grid" style="grid-template-columns:repeat(auto-fill,minmax(240px,1fr));">
            @foreach($biensSimilaires as $bienSimilaire)
                <div class="bien-card">
                    <div class="bien-image" style="height:160px;">
                        @if($bienSimilaire->medias->first())
                            <img src="{{ asset('storage/' . $bienSimilaire->medias->first()->fichier) }}" alt="{{ $bienSimilaire->titre }}">
                        @else
                            <i class="fa-solid fa-image" style="font-size:24px;opacity:0.3;"></i>
                        @endif
                    </div>
                    <div class="bien-body" style="padding:14px 16px 16px;">
                        <div style="font-weight:600;font-size:14px;margin-bottom:4px;">{{ $bienSimilaire->titre }}</div>
                        <div style="font-weight:700;color:var(--rust);font-size:14px;">
                            {{ number_format($bienSimilaire->prix, 0, ',', ' ') }} FCFA
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-location-dot"></i> {{ $bienSimilaire->quartier }}
                        </div>
                        <div style="margin-top:10px;">
                            <a href="{{ route('biens.show', $bienSimilaire) }}" class="btn btn-ghost btn-sm btn-block">Voir</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .detail-grid {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 30px;
        align-items: start;
    }

    @media (max-width: 900px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }

    .biens-grid {
        display: grid;
        gap: 20px;
    }
    
    .bien-card {
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s;
    }
    
    .bien-card:hover {
        transform: translateY(-3px);
    }
    
    .bien-image {
        background: #E8ECF0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        position: relative;
        overflow: hidden;
    }
    
    .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .bien-body {
        padding: 14px 16px 16px;
    }
    
    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
        font-size: 13.5px;
    }
    
    .info-row:last-child {
        border-bottom: none;
    }
    
    .info-row span:first-child {
        color: var(--muted);
    }
    
    .info-row span:last-child {
        font-weight: 600;
    }
    
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--border);
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        color: var(--text-soft);
    }
    
    .panel {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px 24px;
    }

    .sticky-box {
        position: sticky;
        top: 100px;
    }
</style>
@endpush