@extends('layouts.dashboard-agence')

@section('title', 'Abonnement — DoyaImmo')
@section('page_title', 'Abonnement')
@section('page_sub', 'Votre plan et les options disponibles')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Abonnement</h2>
            <p>Votre plan actuel et les options disponibles</p>
        </div>
    </div>

    <!-- Message si abonnement gratuit expiré -->
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

    <!-- Abonnement actuel -->
    @if(isset($abonnementActuel) && $abonnementActuel)
        <div style="padding:16px 20px;background:#E8F5E9;border-radius:10px;border:1px solid #C8E6C9;margin-bottom:24px;">
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
                <i class="fa-solid fa-circle-check" style="color:#1E7A47;font-size:24px;"></i>
                <div style="flex:1;">
                    <h4 style="font-family:var(--display);font-size:15px;color:#1E7A47;margin-bottom:4px;">
                        {{ $abonnementActuel->formule->label() }} - Actif
                    </h4>
                    <p style="font-size:13px;color:#1E7A47;margin:0;">
                        Valable jusqu'au {{ $abonnementActuel->date_fin->format('d/m/Y') }}
                        ({{ $abonnementActuel->date_fin->diffForHumans() }})
                    </p>
                </div>
                <span style="padding:4px 16px;background:#1E7A47;color:#fff;border-radius:999px;font-size:12px;font-weight:600;">
                    {{ $abonnementActuel->formule->label() }}
                </span>
            </div>
        </div>
    @else
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

    <!-- Plans -->
    <div id="plans" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:24px;margin-top:24px;">
        @if(isset($plans) && count($plans) > 0)
            @foreach($plans as $key => $plan)
                <div style="background:#fff;border:2px solid {{ isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value == $key ? 'var(--rust)' : 'var(--border)' }};border-radius:var(--radius);padding:24px;text-align:center;position:relative;transition:all 0.3s;">
                    @if($plan['badge'])
                        <span style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:var(--rust);color:#fff;font-size:10px;font-weight:700;padding:2px 16px;border-radius:999px;text-transform:uppercase;">
                            {{ $plan['badge'] }}
                        </span>
                    @endif

                    @if(isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value == $key)
                        <span style="position:absolute;top:-10px;right:-10px;background:#1E7A47;color:#fff;font-size:10px;font-weight:700;padding:2px 16px;border-radius:999px;text-transform:uppercase;">
                            Actif
                        </span>
                    @endif

                    <div style="font-family:var(--display);font-weight:700;font-size:22px;margin-bottom:4px;">
                        {{ $plan['label'] }}
                    </div>
                    <div style="font-size:28px;font-weight:700;color:var(--rust);margin-bottom:16px;">
                        @if($plan['price'] == 0)
                            Gratuit
                        @else
                            {{ $plan['price_label'] }} <span style="font-size:14px;font-weight:400;color:var(--muted);">/ mois</span>
                        @endif
                    </div>

                    <ul style="list-style:none;padding:0;margin:0 0 20px;text-align:left;">
                        @foreach($plan['features'] as $feature)
                            <li style="padding:6px 0;font-size:13px;color:var(--text-soft);border-bottom:1px solid var(--border);">
                                ✓ {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    @if(isset($abonnementActuel) && $abonnementActuel && $abonnementActuel->formule->value == $key)
                        <button class="btn btn-ghost btn-block" disabled>Plan actif</button>
                    @else
                        <form action="{{ route('agence.abonnement.souscrire') }}" method="POST">
                            @csrf
                            <input type="hidden" name="formule" value="{{ $key }}">
                            <button type="submit" class="btn {{ $key == 'premium' ? 'btn-rust' : 'btn-ghost' }} btn-block">
                                @if($key == 'basic' && isset($abonnementGratuitExpire) && $abonnementGratuitExpire)
                                    Réessayer
                                @elseif($plan['price'] == 0)
                                    Démarrer
                                @else
                                    Souscrire
                                @endif
                            </button>
                        </form>
                    @endif

                    @if($key == 'basic' && $plan['price'] == 0)
                        <p style="font-size:11px;color:var(--muted);margin-top:8px;">
                            <i class="fa-regular fa-info-circle"></i> Valable 30 jours, renouvellement automatique
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

    <!-- Historique -->
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
                            <th style="padding:12px 16px;text-align:left;">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($historique as $item)
                            <tr style="border-bottom:1px solid var(--border);">
                                <td style="padding:12px 16px;font-weight:600;">{{ $item->formule->label() }}</td>
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
                                    <span class="status-pill status-{{ $item->statut && $item->date_fin > now() ? 'success' : 'danger' }}">
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

@push('styles')
<style>
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
</style>
@endpush