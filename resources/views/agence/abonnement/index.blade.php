@extends('layouts.dashboard-agence')

@section('title', 'Abonnement — DoyaImmo')
@section('page_title', 'Abonnement')
@section('page_sub', 'Votre plan et les options disponibles')

@section('content')
<style>
    .plan-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px 20px;
        text-align: center;
        transition: all 0.2s;
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
    }
    .plan-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }
    .plan-card.active {
        border-color: var(--rust);
        border-width: 2px;
    }
    .plan-card .plan-icon {
        font-size: 28px;
        margin-bottom: 6px;
        display: block;
        color: var(--muted);
    }
    .plan-card .plan-name {
        font-weight: 700;
        font-size: 20px;
        margin-bottom: 2px;
    }
    .plan-card .plan-price {
        font-size: 26px;
        font-weight: 700;
        color: var(--rust);
        margin-bottom: 2px;
    }
    .plan-card .plan-period {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 6px;
    }
    .plan-card .plan-offres {
        font-size: 13px;
        color: var(--text-soft);
        background: var(--border);
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 14px;
        font-weight: 600;
    }
    .plan-card .plan-features {
        list-style: none;
        padding: 0;
        margin: 0 0 16px;
        text-align: left;
        flex: 1;
    }
    .plan-card .plan-features li {
        padding: 5px 0;
        font-size: 13px;
        color: var(--text-soft);
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .plan-card .plan-features li:last-child {
        border-bottom: none;
    }
    .plan-card .plan-features li .check {
        color: var(--rust);
        font-weight: 700;
    }
    .badge-active {
        background: #1E7A47;
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 14px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 6px;
    }
    .badge-current {
        background: var(--rust);
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 14px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 6px;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 12px;
        border-radius: 20px;
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
    .btn-block {
        width: 100%;
        justify-content: center;
    }
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 20px;
    }
    @media (max-width: 820px) {
        .plans-grid {
            grid-template-columns: 1fr !important;
        }
        .plan-card {
            padding: 18px !important;
        }
    }
    @media (max-width: 600px) {
        .plan-card .plan-price {
            font-size: 22px !important;
        }
        .plan-card .plan-name {
            font-size: 18px !important;
        }
        .abo-box {
            flex-direction: column !important;
            align-items: stretch !important;
            text-align: center !important;
        }
        .abo-box .abo-right {
            margin-top: 8px !important;
        }
        .plan-card .plan-offres {
            font-size: 12px !important;
        }
    }
    .msg-box {
        padding: 12px 16px;
        border-radius: 10px;
        margin-bottom: 16px;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .msg-success {
        background: #E8F5E9;
        color: #1E7A47;
        border: 1px solid #C8E6C9;
    }
    .msg-warning {
        background: #FFF8E1;
        color: #E65100;
        border: 1px solid #FFE0B2;
    }
    .msg-danger {
        background: #FFEBEE;
        color: #C62828;
        border: 1px solid #FFCDD2;
    }
    .msg-info {
        background: #E3F2FD;
        color: #0D47A1;
        border: 1px solid #BBDEFB;
    }
    .abo-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 16px;
        flex-wrap: wrap;
        gap: 10px;
    }
    .abo-box .abo-plan {
        font-weight: 700;
        font-size: 18px;
    }
    .abo-box .abo-date {
        font-size: 13px;
        color: var(--muted);
    }
    .abo-box .abo-badge {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 14px;
        border-radius: 20px;
    }
    .abo-box .abo-badge.actif {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .abo-box .abo-badge.inactif {
        background: #FFEBEE;
        color: #C62828;
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
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
    }
    .btn-rust {
        background: var(--rust);
        color: #fff;
    }
    .btn-rust:hover {
        background: #9A4523;
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
        padding: 4px 12px;
        font-size: 12px;
    }
    .table-wrap {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        overflow: auto;
        margin-top: 16px;
    }
    .table-wrap table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 480px;
    }
    .table-wrap th {
        background: #FAFBFC;
        padding: 10px 14px;
        text-align: left;
        font-weight: 600;
        border-bottom: 1px solid var(--border);
    }
    .table-wrap td {
        padding: 10px 14px;
        border-bottom: 1px solid var(--border);
    }
    .table-wrap tr:last-child td {
        border-bottom: none;
    }
</style>

<div class="view active">

    <!-- ========== TITRE ========== -->
    <div class="section-head">
        <div>
            <h2>Abonnement</h2>
            <p style="color:var(--muted);font-size:14px;">Votre plan et les options disponibles</p>
        </div>
    </div>

    <!-- ========== MESSAGES ========== -->
    @if(isset($estNonValidee) && $estNonValidee)
        <div class="msg-box msg-warning">
            <i class="fa-solid fa-clock"></i>
            <span><strong>En attente de validation.</strong> Votre agence doit être validée par un administrateur.</span>
        </div>
    @endif

    @if(isset($abonnementGratuitExpire) && $abonnementGratuitExpire && !$abonnementActuel)
        <div class="msg-box msg-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span><strong>Abonnement gratuit expiré.</strong> <a href="#plans" style="color:var(--rust);font-weight:600;text-decoration:none;">Souscrivez un abonnement payant</a></span>
        </div>
    @endif

    @if(session('error'))
        <div class="msg-box msg-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- ========== ABONNEMENT ACTUEL ========== -->
    @if(isset($abonnementActuel) && $abonnementActuel)
        <div class="abo-box">
            <div>
                <div class="abo-plan">
                    @php
                        $formuleLabel = is_object($abonnementActuel->formule) && method_exists($abonnementActuel->formule, 'label') 
                            ? $abonnementActuel->formule->label() 
                            : ucfirst($abonnementActuel->formule);
                    @endphp
                    {{ $formuleLabel }}
                    <span class="abo-badge actif">Actif</span>
                </div>
                <div class="abo-date">Valable jusqu'au {{ $abonnementActuel->date_fin->format('d/m/Y') }}</div>
            </div>
            <div>
                @if($abonnementActuel->montant > 0)
                    <span style="font-size:14px;color:var(--muted);">{{ number_format($abonnementActuel->montant, 0, ',', ' ') }} FCFA/mois</span>
                @else
                    <span style="font-size:14px;color:var(--muted);">Gratuit</span>
                @endif
                <a href="{{ route('agence.abonnement') }}" class="btn btn-ghost btn-sm" style="margin-left:10px;">Gérer</a>
            </div>
        </div>
    @elseif(!isset($estNonValidee) || !$estNonValidee)
        <div class="msg-box msg-info" style="background:#F7F9FC;border-color:var(--border);color:var(--text-soft);">
            <i class="fa-regular fa-clock"></i>
            <span><strong>Aucun abonnement actif.</strong> Choisissez un plan ci-dessous.</span>
        </div>
    @endif

    <!-- ========== PLANS ========== -->
    @if(!isset($estNonValidee) || !$estNonValidee)
        @php
            $isPayantActif = isset($abonnementActuel) && $abonnementActuel && in_array($abonnementActuel->formule->value, ['premium', 'pro']);
            $isBasicActif = isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value === 'basic';
        @endphp

        <div id="plans">
            <div class="plans-grid">
                @if(isset($plans) && count($plans) > 0)
                    @foreach($plans as $key => $plan)
                        @php
                            $isActive = isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value == $key;
                            $estVerrouillé = $isPayantActif && !$isActive;
                            $peutUpgrade = $isBasicActif && in_array($key, ['premium', 'pro']);
                            
                            // Déterminer le nombre d'offres par mois
                            $offresParMois = match($key) {
                                'basic' => 5,
                                'premium' => 20,
                                'pro' => 'Illimité',
                                default => '—'
                            };
                        @endphp
                        <div class="plan-card {{ $isActive ? 'active' : '' }}">
                            
                            @if($isActive)
                                <span class="badge-current">✓ Actif</span>
                            @elseif($peutUpgrade)
                                <span class="badge-active" style="background:#E65100;">↑ Upgrade</span>
                            @endif

                            <span class="plan-icon">
                                <i class="{{ $plan['icon'] ?? 'fa-regular fa-star' }}"></i>
                            </span>

                            <div class="plan-name">{{ $plan['label'] }}</div>

                            <div class="plan-price">
                                @if($plan['price'] == 0)
                                    Gratuit
                                @else
                                    {{ number_format($plan['price'], 0, ',', ' ') }} FCFA
                                @endif
                            </div>
                            
                            <div class="plan-period">{{ $plan['period'] ?? '1 mois' }}</div>

                            <!-- ✅ AFFICHAGE DES OFFRES PAR MOIS -->
                            <div class="plan-offres">
                                <i class="fa-regular fa-envelope"></i>
                                {{ $offresParMois }} offres / mois
                            </div>

                            <ul class="plan-features">
                                @foreach($plan['features'] as $feature)
                                    <li>
                                        <span class="check">✓</span> {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>

                            @if($isActive)
                                <button class="btn btn-ghost btn-block" disabled style="opacity:0.6;cursor:not-allowed;">Plan actif</button>
                            @elseif($estVerrouillé)
                                <button class="btn btn-ghost btn-block" disabled style="opacity:0.5;cursor:not-allowed;">Verrouillé</button>
                                <p style="font-size:10px;color:var(--muted);margin-top:4px;">Attendez la fin de votre abonnement</p>
                            @elseif($peutUpgrade)
                                <form action="{{ route('agence.abonnement.souscrire') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="formule" value="{{ $key }}">
                                    <button type="submit" class="btn btn-rust btn-block">↑ Passer à {{ $plan['label'] }}</button>
                                </form>
                            @else
                                <form action="{{ route('agence.abonnement.souscrire') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="formule" value="{{ $key }}">
                                    <button type="submit" class="btn {{ $plan['price'] > 0 ? 'btn-rust' : 'btn-ghost' }} btn-block">
                                        @if($plan['price'] == 0)
                                            Démarrer gratuitement
                                        @else
                                            Souscrire - {{ number_format($plan['price'], 0, ',', ' ') }} FCFA
                                        @endif
                                    </button>
                                </form>
                            @endif

                            @if($key == 'basic' && $plan['price'] == 0)
                                <p style="font-size:10px;color:var(--muted);margin-top:6px;">Une seule fois par agence</p>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--muted);">
                        <p>Aucun plan disponible.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- ========== LÉGENDE ========== -->
        <div style="margin-top:20px;padding:12px 16px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);">
            <div style="display:flex;flex-wrap:wrap;gap:16px;justify-content:center;font-size:12px;color:var(--text-soft);">
                <span style="display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#1E7A47;"></span> Actif
                </span>
                <span style="display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#E65100;"></span> Upgrade
                </span>
                <span style="display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#BDBDBD;"></span> Verrouillé
                </span>
            </div>
        </div>

    @else
        <div style="text-align:center;padding:40px;background:#fff;border:1px solid var(--border);border-radius:12px;">
            <i class="fa-solid fa-lock" style="font-size:40px;color:var(--muted);display:block;margin-bottom:12px;"></i>
            <h3 style="font-size:17px;font-weight:600;color:var(--text-soft);">Abonnement indisponible</h3>
            <p style="color:var(--muted);font-size:13px;margin-top:4px;">Attendez la validation de votre agence.</p>
            <a href="{{ route('agence.profil') }}" class="btn btn-ghost" style="margin-top:12px;">Voir mon profil</a>
        </div>
    @endif

    <!-- ========== HISTORIQUE ========== -->
    @if(isset($historique) && $historique && $historique->count() > 0)
        <div style="margin-top:28px;">
            <h4 style="font-family:var(--display);font-size:15px;margin-bottom:8px;">Historique</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Montant</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historique as $item)
                            <tr>
                                <td><strong>{{ is_object($item->formule) && method_exists($item->formule, 'label') ? $item->formule->label() : ucfirst($item->formule) }}</strong></td>
                                <td>@if($item->montant == 0) Gratuit @else {{ number_format($item->montant, 0, ',', ' ') }} F @endif</td>
                                <td>{{ $item->date_debut->format('d/m/Y') }}</td>
                                <td>{{ $item->date_fin->format('d/m/Y') }}</td>
                                <td>
                                    <span class="status-pill {{ $item->statut && $item->date_fin > now() ? 'status-success' : 'status-danger' }}">
                                        {{ $item->statut && $item->date_fin > now() ? 'Actif' : 'Expiré' }}
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