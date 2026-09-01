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
    .plan-card.recommended {
        border-color: #D4AF37;
        border-width: 2px;
        position: relative;
    }
    .plan-card.recommended::before {
        content: '⭐ Recommandé';
        position: absolute;
        top: -12px;
        right: 20px;
        background: #D4AF37;
        color: #fff;
        padding: 2px 16px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }
    .plan-card .plan-icon {
        font-size: 32px;
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
        font-size: 28px;
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
    .plan-card .plan-offres .offres-illimite {
        color: #D4AF37;
        font-weight: 700;
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
        min-width: 20px;
        font-size: 14px;
    }
    .plan-card .plan-features li .check.gold {
        color: #D4AF37;
    }
    .plan-card .plan-features li .feature-pro {
        background: #FFF8E1;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        color: #E65100;
        font-weight: 600;
        margin-left: auto;
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
    .badge-pro {
        background: #D4AF37;
        color: #fff;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 14px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 6px;
    }
    .btn-block {
        width: 100%;
        justify-content: center;
    }
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 20px;
        max-width: 800px;
        margin-left: auto;
        margin-right: auto;
    }
    @media (max-width: 820px) {
        .plans-grid {
            grid-template-columns: 1fr !important;
        }
        .plan-card {
            padding: 18px !important;
        }
        .plan-card.recommended::before {
            right: 10px;
            font-size: 10px;
            padding: 2px 12px;
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
        .plan-card .plan-features li {
            font-size: 12px !important;
            padding: 4px 0 !important;
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
    .btn-gold {
        background: #D4AF37;
        color: #fff;
    }
    .btn-gold:hover {
        background: #C5A030;
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
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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
    .tarifs-vedette {
        margin-top: 30px;
        padding: 20px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
    }
    .tarifs-vedette .tarifs-title {
        font-family: var(--display);
        font-size: 15px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .tarifs-vedette .tarifs-title i {
        color: #D4AF37;
    }
    .tarifs-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 12px;
    }
    .tarif-card {
        background: #F7F9FC;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        border: 1px solid var(--border);
        transition: all 0.2s;
    }
    .tarif-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .tarif-card .tarif-duree {
        font-weight: 700;
        font-size: 16px;
        color: var(--ink);
    }
    .tarif-card .tarif-prix {
        font-size: 20px;
        font-weight: 700;
        color: var(--rust);
    }
    .tarif-card .tarif-label {
        font-size: 11px;
        color: var(--muted);
    }
    .vedette-info {
        margin-top: 12px;
        text-align: center;
        font-size: 13px;
        color: var(--muted);
    }
    .vedette-info i {
        margin-right: 6px;
    }
    .compare-badge {
        background: #FFF8E1;
        border: 1px solid #FFE0B2;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 11px;
        color: #E65100;
        text-align: center;
        margin-top: 4px;
    }
    .plan-subtitle {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 10px;
        font-weight: 400;
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
</style>

<div class="view active">

    <!-- ========== TITRE ========== -->
    <div class="section-head">
        <div>
            <h2>Abonnement</h2>
            <p style="color:var(--muted);font-size:14px;">Choisissez le plan qui correspond à vos besoins</p>
        </div>
    </div>

    <!-- ========== MESSAGES ========== -->
    @if(session('success'))
        <div class="msg-box msg-success">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="msg-box msg-danger">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(isset($estNonValidee) && $estNonValidee)
        <div class="msg-box msg-warning">
            <i class="fa-solid fa-clock"></i>
            <span><strong>En attente de validation.</strong> Votre agence doit être validée par un administrateur.</span>
        </div>
    @endif

    @if(isset($abonnementGratuitExpire) && $abonnementGratuitExpire && !$abonnementActuel && !session('error'))
        <div class="msg-box msg-warning">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span><strong>Abonnement gratuit expiré.</strong> <a href="#plans" style="color:var(--rust);font-weight:600;text-decoration:none;">Passez à l'offre Pro</a></span>
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
                    <span class="abo-badge actif"> Actif</span>
                </div>
                <div class="abo-date">Valable jusqu'au {{ $abonnementActuel->date_fin->format('d/m/Y') }}</div>
            </div>
            <div>
                @if($abonnementActuel->montant > 0)
                    <span style="font-size:14px;color:var(--muted);">{{ number_format($abonnementActuel->montant, 0, ',', ' ') }} FCFA/mois</span>
                @else
                    <span style="font-size:14px;color:var(--muted);">Gratuit</span>
                @endif
                <a href="#plans" class="btn btn-ghost btn-sm" style="margin-left:10px;">Gérer</a>
            </div>
        </div>
    @elseif(!isset($estNonValidee) || !$estNonValidee)
        @if(!session('error'))
            <div class="msg-box msg-info" style="background:#F7F9FC;border-color:var(--border);color:var(--text-soft);">
                <i class="fa-regular fa-clock"></i>
                <span><strong>Aucun abonnement actif.</strong> Choisissez un plan ci-dessous.</span>
            </div>
        @endif
    @endif

    <!-- ========== PLANS ========== -->
    @if(!isset($estNonValidee) || !$estNonValidee)
        @php
            $isProActif = isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value === 'pro';
            $isBasicActif = isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value === 'basic';
            $aDejaEuGratuit = isset($agence) && $agence->abonnements()
                ->where('formule', 'basic')
                ->where('statut', false)
                ->exists();
        @endphp

        <div id="plans">
            <div class="plans-grid">
                <!-- ===== PLAN GRATUIT ===== -->
                <div class="plan-card {{ $isBasicActif ? 'active' : '' }}">
                    @if($isBasicActif)
                        <span class="badge-current"> Actif</span>
                    @endif

                    <span class="plan-icon">
                        <i class="fa-regular fa-star"></i>
                    </span>

                    <div class="plan-name">Gratuit</div>

                    <div class="plan-price">0 FCFA</div>
                    
                    <div class="plan-period">/ mois</div>

                    <div class="plan-offres">
                        <i class="fa-regular fa-envelope"></i>
                        5 offres / mois
                    </div>

                    <ul class="plan-features">
                        <li><span class="check">✓</span> Création du compte agence</li>
                        <li><span class="check">✓</span> Profil agence</li>
                        <li><span class="check">✓</span> Consultation des demandes</li>
                        <li><span class="check">✓</span> Accès aux demandes pertinentes</li>
                        <li><span class="check">✓</span> 5 propositions par mois</li>
                        <li><span class="check">✓</span> Gestion des rendez-vous</li>
                    </ul>

                    @if($isBasicActif)
                        <button class="btn btn-ghost btn-block" disabled>Plan actif</button>
                        <div class="compare-badge">
                            <i class="fa-solid fa-arrow-up"></i> Passez à Pro pour plus de fonctionnalités
                        </div>
                    @elseif($aDejaEuGratuit)
                        <button class="btn btn-ghost btn-block" disabled style="opacity:0.5;cursor:not-allowed;">Déjà utilisé</button>
                        <p style="font-size:10px;color:var(--muted);margin-top:4px;">Vous avez déjà utilisé votre essai gratuit</p>
                    @elseif($isProActif)
                        <button class="btn btn-ghost btn-block" disabled>Plan Pro actif</button>
                    @else
                        <form action="{{ route('agence.abonnement.souscrire') }}" method="POST">
                            @csrf
                            <input type="hidden" name="formule" value="basic">
                            <button type="submit" class="btn btn-ghost btn-block">Démarrer gratuitement</button>
                        </form>
                    @endif
                </div>

                <!-- ===== PLAN PRO ===== -->
                <div class="plan-card recommended {{ $isProActif ? 'active' : '' }}">
                    @if($isProActif)
                        <span class="badge-current"> Actif</span>
                    @else
                        <span class="badge-pro"> Recommandé</span>
                    @endif

                    <span class="plan-icon">
                        <i class="fa-solid fa-gem" style="color:#D4AF37;"></i>
                    </span>

                    <div class="plan-name">Pro</div>

                    <div class="plan-price">5 000 FCFA</div>
                    
                    <div class="plan-period">/ mois</div>

                    <div class="plan-offres">
                        <i class="fa-regular fa-envelope"></i>
                        <span class="offres-illimite"> Offres illimitées</span>
                    </div>

                    <ul class="plan-features">
                        <li><span class="check gold">✓</span>  Tout ce qui est inclus dans Gratuit</li>
                        <li><span class="check gold">✓</span>  Propositions illimitées</li>
                        <li><span class="check gold">✓</span>  Publication de biens immobiliers</li>
                        <li><span class="check gold">✓</span>  Gestion du portefeuille de biens</li>
                        <li><span class="check gold">✓</span>  Accès complet aux demandes pertinentes</li>
                        <li><span class="check gold">✓</span>  Réception des demandes</li>
                        <li><span class="check gold">✓</span>  Gestion des rendez-vous</li>
                        <li><span class="check gold">✓</span>  Profil agence </li>
                        
                        <li>
                            <span class="check gold"></span> Possibilité de demander une mise en vedette
                            <span class="feature-pro">Payant</span>
                        </li>
                    </ul>

                    @if($isProActif)
                        <button class="btn btn-gold btn-block" disabled> Plan actif</button>
                        <div style="margin-top:8px;">
                            <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center;">
                                <i class="fa-solid fa-arrow-right"></i> Gérer mes biens
                            </a>
                        </div>
                    @else
                        <form action="{{ route('agence.abonnement.souscrire') }}" method="POST">
                            @csrf
                            <input type="hidden" name="formule" value="pro">
                            <button type="submit" class="btn btn-gold btn-block">
                                <i class="fa-solid fa-crown"></i> Passer à Pro
                            </button>
                        </form>
                        <p style="font-size:10px;color:var(--muted);margin-top:6px;">Paiement sécurisé via PayDunya</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- ========== TARIFS VEDETTE ========== -->
        @if(isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value === 'pro')
            <div class="tarifs-vedette">
                <div class="tarifs-title">
                    <i class="fa-solid fa-star"></i>
                    Mise en vedette des biens — en option
                </div>
                <div class="tarifs-grid">
                    @foreach($tarifsVedette as $jours => $prix)
                        <div class="tarif-card">
                            <div class="tarif-duree">{{ $jours }} jour{{ $jours > 1 ? 's' : '' }}</div>
                            <div class="tarif-prix">{{ number_format($prix, 0, ',', ' ') }} FCFA</div>
                            <div class="tarif-label">/ mise en vedette</div>
                        </div>
                    @endforeach
                </div>
                <div class="vedette-info">
                    <i class="fa-regular fa-circle-info"></i>
                    Service payant disponible uniquement pour les agences avec un abonnement Pro
                </div>
            </div>
        @endif

        <!-- ========== LÉGENDE ========== -->
        <div style="margin-top:20px;padding:12px 16px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);">
            <div style="display:flex;flex-wrap:wrap;gap:16px;justify-content:center;font-size:12px;color:var(--text-soft);">
                <span style="display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#1E7A47;"></span> Actif
                </span>
                <span style="display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#D4AF37;"></span> Recommandé
                </span>
                <span style="display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#BDBDBD;"></span> Indisponible
                </span>
                <span style="display:flex;align-items:center;gap:6px;">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#E65100;"></span> Upgrade
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
            <h4 style="font-family:var(--display);font-size:15px;margin-bottom:8px;">Historique des abonnements</h4>
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
                            @php
                                // ✅ Ne garder que les abonnements actifs OU ceux qui se sont terminés dans les 30 derniers jours
                                $estActif = $item->statut && $item->date_fin > now();
                                $estRecent = $item->date_fin->diffInDays(now()) <= 30;
                            @endphp
                            @if($estActif || $estRecent)
                                <tr>
                                    <td>
                                        <strong>
                                            @if(is_object($item->formule) && method_exists($item->formule, 'label'))
                                                {{ $item->formule->label() }}
                                            @else
                                                {{ ucfirst($item->formule) }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td>
                                        @if($item->montant == 0)
                                            Gratuit
                                        @else
                                            {{ number_format($item->montant, 0, ',', ' ') }} FCFA
                                        @endif
                                    </td>
                                    <td>{{ $item->date_debut->format('d/m/Y') }}</td>
                                    <td>{{ $item->date_fin->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="status-pill {{ $estActif ? 'status-success' : 'status-danger' }}">
                                            {{ $estActif ? ' Actif' : ' Expiré' }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                @php
                    $totalExpires = $historique->filter(function($item) {
                        return !($item->statut && $item->date_fin > now()) && $item->date_fin->diffInDays(now()) > 30;
                    })->count();
                @endphp
                @if($totalExpires > 0)
                    <div style="padding:10px 14px;background:#FAFBFC;border-top:1px solid var(--border);font-size:12px;color:var(--muted);text-align:center;">
                        <i class="fa-regular fa-clock"></i>
                        {{ $totalExpires }} ancien(s) abonnement(s) expiré(s) masqué(s) (plus de 30 jours)
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>
@endsection