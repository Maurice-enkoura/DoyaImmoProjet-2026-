@extends('layouts.dashboard-agence')

@section('title', 'En attente de validation — DoyaImmo')
@section('page_title', 'En attente de validation')
@section('page_sub', 'Votre agence est en cours de validation')

@section('content')
<div class="view active">
    <div style="max-width:600px;margin:40px auto;text-align:center;">
        <!-- Icône -->
        <div style="font-size:80px;color:#F5A623;margin-bottom:24px;">
            <i class="fa-regular fa-clock"></i>
        </div>
        
        <h2 style="font-family:var(--display);font-weight:700;font-size:28px;margin-bottom:12px;color:var(--ink);">
            Agence en cours de validation
        </h2>
        
        <p style="font-size:16px;color:var(--text-soft);line-height:1.8;margin-bottom:16px;">
            Votre demande d'inscription a bien été enregistrée. 
            Nos équipes vérifient vos documents dans les plus brefs délais.
        </p>
        
        <div style="background:#FFF8E1;border-radius:12px;padding:20px;border:1px solid #FFE0B2;margin-bottom:24px;text-align:left;">
            <h4 style="font-weight:600;font-size:14px;color:#E65100;margin-bottom:8px;">
                <i class="fa-solid fa-info-circle"></i> Informations
            </h4>
            <ul style="list-style:none;padding:0;margin:0;font-size:14px;color:var(--text-soft);">
                <li style="padding:6px 0;border-bottom:1px solid #FFE0B2;">
                    <strong>Délai moyen :</strong> 48h
                </li>
                <li style="padding:6px 0;border-bottom:1px solid #FFE0B2;">
                    <strong>Agence :</strong> {{ $agence->nom_agence ?? 'Non renseigné' }}
                </li>
                <li style="padding:6px 0;">
                    <strong>Statut :</strong> 
                    <span style="background:#FFF8E1;color:#E65100;padding:2px 12px;border-radius:999px;font-weight:600;">
                        En attente de validation
                    </span>
                </li>
            </ul>
        </div>
        
        <p style="font-size:14px;color:var(--muted);">
            <i class="fa-regular fa-envelope"></i> 
            Une notification vous sera envoyée dès que votre agence sera validée.
        </p>
        
        <div style="margin-top:24px;padding-top:24px;border-top:1px solid var(--border);">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               style="color:var(--muted);text-decoration:none;font-size:14px;">
                <i class="fa-solid fa-right-from-bracket"></i> Se déconnecter
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection