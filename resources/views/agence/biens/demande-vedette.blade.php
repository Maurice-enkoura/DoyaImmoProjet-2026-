@extends('layouts.dashboard-agence')

@section('title', 'Demande de mise en vedette — DoyaImmo')
@section('page_title', 'Demande de mise en vedette')
@section('page_sub', 'Mettez votre bien en avant')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mise en vedette</h2>
            <p>Choisissez la durée pour mettre en avant votre bien</p>
        </div>
        <a href="{{ route('agence.biens.show', $bien) }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour au bien
        </a>
    </div>

    <!-- Informations du bien -->
    <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 20px;margin-bottom:20px;display:flex;align-items:center;gap:16px;flex-wrap:wrap;">
        <div style="display:flex;align-items:center;gap:12px;">
            @php
                $image = $bien->medias->where('type_media', 'image')->first();
            @endphp
            @if($image)
                <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $bien->titre }}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
            @else
                <div style="width:60px;height:60px;background:#F0F2F5;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--muted);">
                    <i class="fa-solid fa-image" style="font-size:20px;"></i>
                </div>
            @endif
            <div>
                <div style="font-weight:600;font-size:16px;color:var(--ink);">{{ $bien->titre }}</div>
                <div style="font-size:13px;color:var(--muted);">{{ $bien->quartier }} · {{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
            </div>
        </div>
        @if($bien->est_vedette)
            <span style="background:#E8F5E9;color:#1E7A47;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;">
                <i class="fa-solid fa-star" style="color:#F5A623;"></i> Déjà en vedette
            </span>
        @endif
    </div>

    @if($bien->est_vedette)
        <div style="text-align:center;padding:40px;background:#fff;border:1px solid var(--border);border-radius:12px;">
            <i class="fa-solid fa-star" style="font-size:40px;color:#F5A623;display:block;margin-bottom:12px;"></i>
            <h3 style="font-size:17px;font-weight:600;color:var(--ink);">Ce bien est déjà en vedette</h3>
            <p style="color:var(--muted);font-size:13px;margin-top:4px;">Attendez la fin de la période actuelle pour faire une nouvelle demande.</p>
            <a href="{{ route('agence.biens.show', $bien) }}" class="btn btn-ghost" style="margin-top:12px;">Voir le bien</a>
        </div>
    @else
        <!-- Tarifs -->
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:20px;">
            @foreach($tarifs as $jours => $prix)
                <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:20px;text-align:center;transition:all 0.2s;">
                    <div style="font-weight:700;font-size:18px;color:var(--ink);">{{ $jours }} jour{{ $jours > 1 ? 's' : '' }}</div>
                    <div style="font-size:24px;font-weight:700;color:var(--rust);margin:8px 0;">{{ number_format($prix, 0, ',', ' ') }} FCFA</div>
                    <div style="font-size:12px;color:var(--muted);margin-bottom:12px;">/ mise en vedette</div>
                    <form action="{{ route('agence.biens.vedette.store', $bien) }}" method="POST">
                        @csrf
                        <input type="hidden" name="duree" value="{{ $jours }}">
                        <button type="submit" class="btn btn-rust" style="width:100%;justify-content:center;">
                            <i class="fa-solid fa-star"></i> Demander
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <!-- Informations complémentaires -->
        <div style="background:#FFF8E1;border:1px solid #FFE0B2;border-radius:12px;padding:16px 20px;">
            <div style="display:flex;gap:12px;align-items:flex-start;">
                <i class="fa-solid fa-circle-info" style="color:#F5A623;font-size:20px;margin-top:2px;"></i>
                <div>
                    <strong style="font-size:14px;color:#E65100;">Comment ça fonctionne ?</strong>
                    <ul style="margin:8px 0 0;padding-left:20px;font-size:13px;color:#5D4037;">
                        <li>Vous faites une demande de mise en vedette</li>
                        <li>Vous contactez DoyaImmo pour finaliser le paiement</li>
                        <li>Une fois le paiement confirmé, votre bien sera mis en vedette</li>
                        <li>La vedette expire automatiquement à la fin de la période choisie</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div style="margin-top:16px;background:#E3F2FD;border:1px solid #BBDEFB;border-radius:12px;padding:16px 20px;text-align:center;">
            <p style="margin:0;font-size:13px;color:#0D47A1;">
                <i class="fa-solid fa-phone" style="margin-right:8px;"></i>
                Pour finaliser votre demande, contactez DoyaImmo au 
                <strong>+221 78 000 00 00</strong> 
                ou par WhatsApp au 
                <strong>+221 78 000 00 00</strong>
            </p>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
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
    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }
    .btn-ghost:hover {
        background: var(--border);
    }
    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }
    @media (max-width: 600px) {
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