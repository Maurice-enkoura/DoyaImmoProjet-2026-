@extends('layouts.dashboard-agence')

@section('title', 'Abonnement — DoyaImmo')
@section('page_title', 'Abonnement')
@section('page_sub', 'Votre plan et les options disponibles')

@section('content')
<style>
    .plan-card {
        background: #fff;
        border: 2px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
        text-align: center;
        position: relative;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .plan-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .plan-card.active {
        border-color: var(--rust);
        box-shadow: 0 0 0 4px rgba(181, 80, 42, 0.15);
    }
    .plan-card .plan-icon {
        font-size: 32px;
        margin-bottom: 8px;
        display: block;
    }
    .plan-card .plan-name {
        font-family: var(--display);
        font-weight: 700;
        font-size: 22px;
        margin-bottom: 4px;
    }
    .plan-card .plan-price {
        font-size: 28px;
        font-weight: 700;
        color: var(--rust);
        margin-bottom: 4px;
    }
    .plan-card .plan-period {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 16px;
    }
    .plan-card .plan-features {
        list-style: none;
        padding: 0;
        margin: 0 0 20px;
        text-align: left;
        flex: 1;
    }
    .plan-card .plan-features li {
        padding: 6px 0;
        font-size: 13px;
        color: var(--text-soft);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .plan-card .plan-features li .check {
        color: #1E7A47;
        font-weight: 700;
    }
    .plan-card .badge-plan {
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 16px;
        border-radius: 999px;
        text-transform: uppercase;
    }
    .plan-card .badge-active {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #1E7A47;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 16px;
        border-radius: 999px;
        text-transform: uppercase;
    }
    .plan-card .badge-upgrade {
        position: absolute;
        top: -10px;
        right: -10px;
        background: #E65100;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 16px;
        border-radius: 999px;
        text-transform: uppercase;
    }
    .plan-card .btn-block {
        width: 100%;
        justify-content: center;
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
    .status-success {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-danger {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-warning {
        background: #FFF8E1;
        color: #E65100;
    }
    
    @media (max-width: 768px) {
        .plans-grid {
            grid-template-columns: 1fr !important;
        }
        .plan-card {
            padding: 16px !important;
        }
        .plan-card .plan-price {
            font-size: 22px !important;
        }
    }
</style>

<div class="view active">
    <div class="section-head">
        <div>
            <h2>Abonnement</h2>
            <p>Votre plan actuel et les options disponibles</p>
        </div>
    </div>

    {{-- ✅ Message si l'agence n'est pas validée --}}
    @if(isset($estNonValidee) && $estNonValidee)
        <div style="padding:16px 20px;background:#FFF3E0;border-radius:10px;border:1px solid #FFE0B2;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <i class="fa-solid fa-triangle-exclamation" style="color:#E65100;font-size:24px;"></i>
                <div style="flex:1;">
                    <h4 style="font-family:var(--display);font-size:15px;color:#E65100;margin-bottom:4px;">
                        Agence en attente de validation
                    </h4>
                    <p style="font-size:13px;color:#BF360C;margin:0;">
                        Votre agence doit être validée par un administrateur pour souscrire à un abonnement.
                        Veuillez patienter pendant le traitement de votre dossier.
                    </p>
                </div>
                <a href="{{ route('agence.profil') }}" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-file"></i> Voir mon profil
                </a>
            </div>
        </div>
    @endif

    {{-- Message si abonnement gratuit expiré --}}
    @if(isset($abonnementGratuitExpire) && $abonnementGratuitExpire && !$abonnementActuel)
        <div style="padding:16px 20px;background:#FFF8E1;border-radius:10px;border:1px solid #FFE0B2;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <i class="fa-solid fa-triangle-exclamation" style="color:#E65100;font-size:24px;"></i>
                <div style="flex:1;">
                    <h4 style="font-family:var(--display);font-size:15px;color:#E65100;margin-bottom:4px;">
                        Votre abonnement gratuit est expiré !
                    </h4>
                    <p style="font-size:13px;color:#BF360C;margin:0;">
                        Pour continuer à publier des biens et faire des offres, souscrivez à un abonnement payant.
                    </p>
                </div>
                <a href="#plans" class="btn btn-rust">Voir les offres</a>
            </div>
        </div>
    @endif

    {{-- 🔥 Message d'erreur : Abonnement payant actif - ne peut pas changer --}}
    @if(session('error') && str_contains(session('error'), 'Vous ne pouvez pas changer avant la fin'))
        <div style="padding:16px 20px;background:#FFF3E0;border-radius:10px;border:1px solid #FFE0B2;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <i class="fa-solid fa-clock" style="color:#E65100;font-size:24px;"></i>
                <div style="flex:1;">
                    <h4 style="font-family:var(--display);font-size:15px;color:#E65100;margin-bottom:4px;">
                        ⏳ Abonnement en cours
                    </h4>
                    <p style="font-size:13px;color:#BF360C;margin:0;">
                        {{ session('error') }}
                    </p>
                    <p style="font-size:12px;color:#BF360C;margin-top:4px;">
                        <i class="fa-regular fa-lightbulb"></i> Vous pourrez changer d'abonnement après la date d'expiration.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- 🔥 Message d'erreur : Même abonnement déjà actif --}}
    @if(session('error') && str_contains(session('error'), 'déjà un abonnement') && !str_contains(session('error'), 'Vous ne pouvez pas changer avant la fin'))
        <div style="padding:16px 20px;background:#FFF8E1;border-radius:10px;border:1px solid #FFE0B2;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <i class="fa-solid fa-circle-info" style="color:#E65100;font-size:24px;"></i>
                <div style="flex:1;">
                    <h4 style="font-family:var(--display);font-size:15px;color:#E65100;margin-bottom:4px;">
                        ℹ️ Abonnement déjà actif
                    </h4>
                    <p style="font-size:13px;color:#BF360C;margin:0;">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- 🔥 Message d'erreur : Gratuit déjà utilisé --}}
    @if(session('error') && str_contains(session('error'), 'Vous avez déjà utilisé votre abonnement gratuit'))
        <div style="padding:16px 20px;background:#FFEBEE;border-radius:10px;border:1px solid #FFCDD2;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <i class="fa-solid fa-circle-exclamation" style="color:#C62828;font-size:24px;"></i>
                <div style="flex:1;">
                    <h4 style="font-family:var(--display);font-size:15px;color:#C62828;margin-bottom:4px;">
                        ❌ Abonnement gratuit déjà utilisé
                    </h4>
                    <p style="font-size:13px;color:#B71C1C;margin:0;">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Abonnement actuel --}}
    @if(isset($abonnementActuel) && $abonnementActuel)
        <div style="padding:16px 20px;background:#E8F5E9;border-radius:10px;border:1px solid #C8E6C9;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <i class="fa-solid fa-circle-check" style="color:#1E7A47;font-size:24px;"></i>
                <div style="flex:1;">
                    <h4 style="font-family:var(--display);font-size:15px;color:#1E7A47;margin-bottom:4px;">
                        @php
                            $formuleLabel = is_object($abonnementActuel->formule) && method_exists($abonnementActuel->formule, 'label') 
                                ? $abonnementActuel->formule->label() 
                                : ucfirst($abonnementActuel->formule);
                        @endphp
                        {{ $formuleLabel }} - Actif
                    </h4>
                    <p style="font-size:13px;color:#1E7A47;margin:0;">
                        Valable jusqu'au {{ $abonnementActuel->date_fin->format('d/m/Y') }}
                        ({{ $abonnementActuel->date_fin->diffForHumans() }})
                    </p>
                    @if($abonnementActuel->montant > 0)
                        <p style="font-size:12px;color:var(--muted);margin-top:4px;">
                            {{ number_format($abonnementActuel->montant, 0, ',', ' ') }} FCFA
                        </p>
                        <p style="font-size:11px;color:#1E7A47;margin-top:2px;">
                            <i class="fa-regular fa-clock"></i> Renouvellement automatique mensuel
                        </p>
                    @else
                        <p style="font-size:12px;color:var(--muted);margin-top:4px;">Gratuit</p>
                        <p style="font-size:11px;color:#E65100;margin-top:2px;">
                            <i class="fa-regular fa-clock"></i> Expire dans {{ $abonnementActuel->date_fin->diffForHumans() }}
                        </p>
                    @endif
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                    <span style="padding:4px 16px;background:#1E7A47;color:#fff;border-radius:999px;font-size:12px;font-weight:600;">
                        {{ $formuleLabel }}
                    </span>
                    @if($abonnementActuel->formule->value === 'basic')
                        <span style="font-size:12px;color:#1E7A47;">
                            <i class="fa-solid fa-arrow-up"></i> Passez à Premium ou Pro
                        </span>
                    @else
                        <span style="font-size:12px;color:#E65100;">
                            <i class="fa-solid fa-lock"></i> Abonnement verrouillé
                        </span>
                    @endif
                </div>
            </div>
        </div>
    @elseif(!isset($estNonValidee) || !$estNonValidee)
        <div style="padding:16px 20px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <i class="fa-regular fa-clock" style="color:var(--muted);font-size:24px;"></i>
                <div style="flex:1;">
                    <h4 style="font-family:var(--display);font-size:15px;color:var(--text-soft);margin-bottom:4px;">
                        Aucun abonnement actif
                    </h4>
                    <p style="font-size:13px;color:var(--muted);margin:0;">
                        Souscrivez à un abonnement pour accéder à toutes les fonctionnalités.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Paiement en attente --}}
    @if(isset($abonnementActuel) && $abonnementActuel && !$abonnementActuel->estActif() && $abonnementActuel->paydunya_token)
        <div style="margin-top:16px;padding:12px 16px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
                <div>
                    <i class="fa-solid fa-clock" style="color:#E65100;"></i>
                    <span style="font-size:13px;color:#E65100;">
                        ⏳ Paiement en attente de confirmation
                    </span>
                </div>
                <div style="display:flex;gap:8px;">
                    <a href="{{ route('paydunya.status', $abonnementActuel) }}" class="btn btn-sm btn-rust">
                        <i class="fa-solid fa-sync"></i> Vérifier le paiement
                    </a>
                    <a href="{{ route('paydunya.force-update', $abonnementActuel) }}" class="btn btn-sm btn-success" onclick="return confirm('Activer manuellement cet abonnement ?')">
                        <i class="fa-solid fa-check"></i> Activer manuellement
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Plans --}}
    @if(!isset($estNonValidee) || !$estNonValidee)
        @php
            // Définir les variables globales
            $isPayantActif = isset($abonnementActuel) && $abonnementActuel && in_array($abonnementActuel->formule->value, ['premium', 'pro']);
            $isBasicActif = isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value === 'basic';
        @endphp

        <div id="plans" class="plans-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;margin-top:24px;">
            @if(isset($plans) && count($plans) > 0)
                @foreach($plans as $key => $plan)
                    @php
                        $isActive = isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value == $key;
                        $estVerrouillé = $isPayantActif && !$isActive;
                        $peutUpgrade = $isBasicActif && in_array($key, ['premium', 'pro']);
                        $estDejaActif = $isActive;
                    @endphp
                    <div class="plan-card {{ $isActive ? 'active' : '' }}">
                        
                        @if($plan['badge'])
                            <span class="badge-plan" style="background:{{ $plan['color'] ?? 'var(--rust)' }};">
                                {{ $plan['badge'] }}
                            </span>
                        @endif

                        @if($isActive)
                            <span class="badge-active">Actif</span>
                        @elseif($peutUpgrade)
                            <span class="badge-upgrade"> Upgrade</span>
                        @endif

                        <span class="plan-icon" style="color:{{ $plan['color'] ?? 'var(--ink)' }}">
                            <i class="{{ $plan['icon'] ?? 'fa-regular fa-star' }}"></i>
                        </span>

                        <div class="plan-name" style="color:{{ $plan['color'] ?? 'var(--ink)' }}">
                            {{ $plan['label'] }}
                        </div>

                        <div class="plan-price">
                            @if($plan['price'] == 0)
                                Gratuit
                            @else
                                {{ number_format($plan['price'], 0, ',', ' ') }} FCFA
                            @endif
                        </div>
                        
                        <div class="plan-period">
                            <i class="fa-regular fa-calendar"></i> {{ $plan['period'] ?? '1 mois' }}
                            @if($plan['price'] > 0)
                                <span style="display:block;font-size:11px;color:var(--muted);">
                                    <i class="fa-solid fa-credit-card"></i> PayDunya (Orange Money, Wave, Visa)
                                </span>
                                <span style="display:block;font-size:10px;color:#1E7A47;">
                                    <i class="fa-regular fa-clock"></i> Renouvellement automatique mensuel
                                </span>
                            @endif
                        </div>

                        <ul class="plan-features">
                            @foreach($plan['features'] as $feature)
                                <li>
                                    <span class="check">✓</span> {{ $feature }}
                                </li>
                            @endforeach
                        </ul>

                        @if($isActive)
                            <button class="btn btn-ghost btn-block" disabled style="opacity:0.6;cursor:not-allowed;">
                                <i class="fa-solid fa-check"></i> Plan actif
                            </button>
                        @elseif($estVerrouillé)
                            <button class="btn btn-ghost btn-block" disabled style="opacity:0.5;cursor:not-allowed;background:#FFF3E0;border-color:#FFE0B2;">
                                <i class="fa-solid fa-lock"></i> Abonnement verrouillé
                            </button>
                            <p style="font-size:10px;color:#E65100;margin-top:4px;">
                                <i class="fa-regular fa-clock"></i> Attendez la fin de votre abonnement
                            </p>
                        @elseif($peutUpgrade)
                            <form action="{{ route('agence.abonnement.souscrire') }}" method="POST">
                                @csrf
                                <input type="hidden" name="formule" value="{{ $key }}">
                                <button type="submit" class="btn {{ $key == 'pro' ? 'btn-rust' : 'btn-rust' }} btn-block">
                                    <i class="fa-solid fa-arrow-up"></i> Passer à {{ $plan['label'] }}
                                </button>
                            </form>
                        @else
                            <form action="{{ route('agence.abonnement.souscrire') }}" method="POST">
                                @csrf
                                <input type="hidden" name="formule" value="{{ $key }}">
                                <button type="submit" class="btn {{ $key == 'pro' || $key == 'premium' ? 'btn-rust' : 'btn-ghost' }} btn-block">
                                    @if($key == 'basic' && isset($abonnementGratuitExpire) && $abonnementGratuitExpire)
                                        <i class="fa-solid fa-rotate"></i> Réessayer
                                    @elseif($plan['price'] == 0)
                                        <i class="fa-solid fa-play"></i> Démarrer gratuitement
                                    @else
                                        <i class="fa-solid fa-credit-card"></i> Souscrire - {{ number_format($plan['price'], 0, ',', ' ') }} FCFA/mois
                                    @endif
                                </button>
                            </form>
                        @endif

                        @if($key == 'basic' && $plan['price'] == 0)
                            <p style="font-size:11px;color:var(--muted);margin-top:8px;">
                                <i class="fa-regular fa-info-circle"></i> Valable 1 mois, renouvellement manuel
                                <span style="display:block;font-size:10px;color:#E65100;">Une seule fois par agence</span>
                            </p>
                        @elseif($plan['price'] > 0)
                            <p style="font-size:11px;color:var(--muted);margin-top:8px;">
                                <i class="fa-solid fa-lock" style="color:#1E7A47;"></i> Paiement sécurisé
                                <span style="display:block;font-size:10px;color:#E65100;">
                                    Montant minimum: 200 FCFA
                                </span>
                                <span style="display:block;font-size:10px;color:var(--muted);">
                                    <i class="fa-regular fa-clock"></i> Renouvellement automatique chaque mois
                                </span>
                            </p>
                        @endif
                    </div>
                @endforeach
            @else
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted);">
                    <p>Aucun plan disponible pour le moment.</p>
                </div>
            @endif
        </div>

        {{-- Légende des statuts --}}
        <div style="margin-top:24px;padding:16px 20px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);">
            <div style="display:flex;flex-wrap:wrap;gap:20px;justify-content:center;">
                <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-soft);">
                    <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#1E7A47;"></span>
                    <span>Actif - Votre plan actuel</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-soft);">
                    <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#E65100;"></span>
                    <span>Upgrade - Passez à un plan supérieur</span>
                </div>
                <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--text-soft);">
                    <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:#BDBDBD;"></span>
                    <span> Verrouillé - Attendez la fin de votre abonnement</span>
                </div>
            </div>
        </div>
    @else
        {{-- Message pour les agences non validées --}}
        <div style="text-align:center;padding:40px;background:#fff;border:1px solid var(--border);border-radius:var(--radius);">
            <i class="fa-solid fa-lock" style="font-size:48px;color:#E65100;display:block;margin-bottom:16px;"></i>
            <h3 style="font-family:var(--display);font-size:18px;font-weight:700;color:var(--text-soft);">
                Abonnement indisponible
            </h3>
            <p style="color:var(--muted);max-width:400px;margin:0 auto;">
                Votre agence est en attente de validation. Une fois votre agence validée par un administrateur,
                vous pourrez souscrire à un abonnement.
            </p>
            <a href="{{ route('agence.profil') }}" class="btn btn-ghost" style="margin-top:16px;">
                <i class="fa-solid fa-file"></i> Voir mon profil
            </a>
        </div>
    @endif

    {{-- Historique --}}
    @if(isset($historique) && $historique && $historique->count() > 0)
        <div style="margin-top:40px;">
            <h4 style="font-family:var(--display);font-size:16px;margin-bottom:12px;">Historique des abonnements</h4>
            <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:auto;">
                <table style="width:100%;border-collapse:collapse;font-size:13px;min-width:500px;">
                    <thead>
                        <tr style="background:#FAFBFC;border-bottom:1px solid var(--border);">
                            <th style="padding:12px 16px;text-align:left;">Formule</th>
                            <th style="padding:12px 16px;text-align:left;">Montant</th>
                            <th style="padding:12px 16px;text-align:left;">Début</th>
                            <th style="padding:12px 16px;text-align:left;">Fin</th>
                            <th style="padding:12px 16px;text-align:left;">Durée</th>
                            <th style="padding:12px 16px;text-align:left;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historique as $item)
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:12px 16px;font-weight:600;">
                                    {{ is_object($item->formule) && method_exists($item->formule, 'label') ? $item->formule->label() : ucfirst($item->formule) }}
                                </td>
                                <td style="padding:12px 16px;">
                                    @if($item->montant == 0)
                                        Gratuit
                                    @else
                                        {{ number_format($item->montant, 0, ',', ' ') }} F
                                    @endif
                                </td>
                                <td style="padding:12px 16px;">{{ $item->date_debut->format('d/m/Y') }}</td>
                                <td style="padding:12px 16px;">{{ $item->date_fin->format('d/m/Y') }}</td>
                                <td style="padding:12px 16px;">
                                    @php
                                        $debut = \Carbon\Carbon::parse($item->date_debut);
                                        $fin = \Carbon\Carbon::parse($item->date_fin);
                                        $diffMois = $debut->diffInMonths($fin);
                                    @endphp
                                    {{ $diffMois }} mois
                                </td>
                                <td style="padding:12px 16px;">
                                    <span class="status-pill {{ $item->statut && $item->date_fin > now() ? 'status-success' : 'status-danger' }}">
                                        {{ $item->statut && $item->date_fin > now() ? ' Actif' : ' Expiré' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection