@extends('layouts.app')

@section('title', $demande->type_bien->label() . ' — ' . $demande->zone_recherchee . ' — DoyaImmo')

@section('content')
<div class="wrap section" style="padding-top:36px;">
    <a href="{{ route('besoins.index') }}" style="font-size:13px; color:var(--muted); display:inline-block; margin-bottom:20px;">
        ← Retour aux besoins
    </a>

    <div class="detail-grid">
        <div>
            <div class="detail-head">
                <div>
                    <span class="meta-pill" style="margin-bottom:10px; display:inline-flex;">
                        <i class="fa-solid fa-location-dot"></i> {{ $demande->zone_recherchee }}, Dakar
                    </span>
                    <h1 style="font-family:var(--display); font-weight:800; font-size:28px; margin-top:10px;">
                        {{ $demande->type_bien->label() }}
                    </h1>
                </div>
                <span class="offer-count">{{ $demande->propositions->count() }} offres reçues</span>
            </div>

            <div class="panel" style="margin-bottom:20px;">
                <h3 style="font-family:var(--display); font-size:15px; margin-bottom:14px;">Description du besoin</h3>
                <p style="font-size:14px; line-height:1.8; color:var(--text-soft);">
                    {{ $demande->description }}
                </p>
            </div>

            <div class="panel">
                <h3 style="font-family:var(--display); font-size:15px; margin-bottom:6px;">Informations</h3>
                <div class="info-row">
                    <span>Type de bien</span>
                    <span>{{ $demande->type_bien->label() }}</span>
                </div>
                <div class="info-row">
                    <span>Zone recherchée</span>
                    <span>{{ $demande->zone_recherchee }}</span>
                </div>
                <div class="info-row">
                    <span>Budget maximum</span>
                    <span>{{ number_format($demande->budget_maximum, 0, ',', ' ') }} FCFA / mois</span>
                </div>
                <div class="info-row">
                    <span>Type de contrat</span>
                    <span>{{ $demande->type_operation->label() }}</span>
                </div>
                @if($demande->nombre_chambres)
                <div class="info-row">
                    <span>Nombre de chambres</span>
                    <span>{{ $demande->nombre_chambres }}</span>
                </div>
                @endif
                @if($demande->surface_minimum)
                <div class="info-row">
                    <span>Surface minimum</span>
                    <span>{{ $demande->surface_minimum }} m²</span>
                </div>
                @endif
                @if($demande->date_entree_souhaitee)
                <div class="info-row">
                    <span>Disponibilité souhaitée</span>
                    <span>{{ $demande->date_entree_souhaitee->format('d/m/Y') }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span>Statut</span>
                    <span>{{ $demande->statut->label() }}</span>
                </div>
                <div class="info-row">
                    <span>Publié le</span>
                    <span>{{ $demande->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="sticky-box">
            <div class="panel">
                <h3 style="font-family:var(--display); font-size:15px; margin-bottom:14px;">
                    Vous êtes une agence ?
                </h3>
                <p style="font-size:13px; color:var(--text-soft); line-height:1.7; margin-bottom:18px;">
                    Connectez-vous à votre espace agence pour envoyer une offre correspondant à ce besoin.
                </p>
                @auth
                    @if(auth()->user()->isAgence())
                        <a href="{{ route('agence.propositions.create', $demande) }}" class="btn btn-rust btn-block">
                            Envoyer une offre
                        </a>
                    @elseif(auth()->user()->isParticulier())
                        <p style="font-size:13px; color:var(--muted); text-align:center;">
                            Vous êtes un particulier. <a href="{{ route('login') }}">Connectez-vous</a> en tant qu'agence pour faire une offre.
                        </p>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-rust btn-block">Se connecter</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-rust btn-block">Se connecter</a>
                    <a href="{{ route('register.agence') }}" class="btn btn-ghost btn-block" style="margin-top:10px;">
                        Créer un compte agence
                    </a>
                @endauth
            </div>

            @if($demande->propositions->count() > 0 && auth()->check() && auth()->user()->isParticulier())
            <div class="panel" style="margin-top:16px;">
                <h3 style="font-family:var(--display); font-size:15px; margin-bottom:14px;">
                    Propositions reçues
                </h3>
                @foreach($demande->propositions->take(3) as $proposition)
                    <div style="padding:12px 0; border-bottom:1px solid var(--border);">
                        <div style="font-weight:600;font-size:14px;">
                            {{ $proposition->agence->nom_agence }}
                        </div>
                        <div style="font-size:13px; color:var(--rust);">
                            {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                        </div>
                        <div style="font-size:12px; color:var(--muted);">
                            {{ $proposition->statut->label() }}
                        </div>
                    </div>
                @endforeach
                @if($demande->propositions->count() > 3)
                    <div style="text-align:center;margin-top:12px;font-size:13px;color:var(--muted);">
                        + {{ $demande->propositions->count() - 3 }} autres propositions
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection