@extends('layouts.dashboard')

@section('title', 'Mes rendez-vous — DoyaImmo')
@section('page_title', 'Mes rendez-vous')
@section('page_sub', 'Vos visites planifiées avec les agences')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mes rendez-vous</h2>
            <p>Vos visites planifiées avec les agences</p>
        </div>
    </div>

    @if(session('error'))
        <div style="padding:12px 16px;background:#FFEBEE;color:#C62828;border:1px solid #FFCDD2;border-radius:10px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div style="padding:12px 16px;background:#E3F2FD;color:#0D47A1;border:1px solid #BBDEFB;border-radius:10px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-info-circle"></i>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if(session('success'))
        <div style="padding:12px 16px;background:#E8F5E9;color:#1E7A47;border:1px solid #C8E6C9;border-radius:10px;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($rendezVous->count() > 0)
        <!-- Statistiques rapides -->
       

        <!-- Liste des rendez-vous -->
        <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
            @foreach($rendezVous as $rdv)
                <div style="display:flex;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;transition:background 0.2s;">
                    
                    <!-- Date -->
                    <div style="display:flex;flex-direction:column;align-items:center;background:#F7F9FC;border-radius:10px;padding:6px 14px;min-width:56px;flex-shrink:0;">
                        <span style="font-family:var(--display);font-weight:700;font-size:20px;line-height:1.2;color:var(--ink);">
                            {{ $rdv->date_visite->format('d') }}
                        </span>
                        <span style="font-size:10px;text-transform:uppercase;color:var(--muted);">
                            {{ $rdv->date_visite->format('M') }}
                        </span>
                    </div>

                    <!-- Informations -->
                    <div style="flex:1;min-width:150px;">
                        <div style="font-weight:600;font-size:15px;color:var(--ink);">
                            {{ $rdv->proposition->bien->titre ?? 'Visite' }}
                            @if($rdv->proposition->bien)
                                <span style="font-weight:400;font-size:13px;color:var(--muted);">
                                    — {{ $rdv->proposition->bien->quartier }}
                                </span>
                            @endif
                        </div>
                        
                        <!-- Agence -->
                        <div style="font-size:13px;color:var(--muted);">
                            <i class="fa-regular fa-building" style="margin-right:4px;"></i>
                            {{ $rdv->agence->nom_agence }}
                        </div>
                        
                        <!-- ✅ Numéro de téléphone - Visible UNIQUEMENT si confirmé ou terminé -->
                        <div style="font-size:13px;color:var(--muted);">
                            <i class="fa-solid fa-phone" style="margin-right:4px;color:var(--rust);"></i>
                            @if($rdv->statut->value === 'confirme' || $rdv->statut->value === 'termine')
                                @if($rdv->agence->user && $rdv->agence->user->telephone)
                                    <a href="tel:{{ $rdv->agence->user->telephone }}" style="color:var(--ink);text-decoration:none;font-weight:500;">
                                        {{ $rdv->agence->user->telephone }}
                                    </a>
                                    <span style="font-size:10px;color:#1E7A47;margin-left:4px;">
                                        <i class="fa-solid fa-check-circle"></i>
                                    </span>
                                @else
                                    <span style="color:var(--muted);">Non renseigné</span>
                                @endif
                            @else
                                <span style="color:var(--muted);">
                                    <i class="fa-solid fa-lock" style="font-size:10px;"></i> 
                                    En attente de confirmation
                                </span>
                            @endif
                        </div>
                        
                        <!-- Heure -->
                        <div style="font-size:13px;color:var(--muted);">
                            <i class="fa-regular fa-clock" style="margin-right:4px;"></i>
                            {{ \Carbon\Carbon::parse($rdv->heure_visite)->format('H:i') }}
                            @if($rdv->proposition->bien)
                                · Visite du bien
                            @endif
                        </div>
                    </div>

                    <!-- Statut -->
                    <div style="flex-shrink:0;">
                        <span class="status-pill status-{{ $rdv->statut->value }}">
                            <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                            {{ $rdv->statut->label() }}
                        </span>
                    </div>

                    <!-- Actions -->
                    <div style="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;">
                        @if($rdv->statut->value === 'planifie')
                            <!-- ❌ Le particulier ne peut PAS confirmer, seulement annuler -->
                            <div style="padding:6px 14px;background:#FFF8E1;border-radius:10px;color:#E65100;font-size:12px;display:flex;align-items:center;gap:6px;border:1px solid #FFE0B2;">
                                <i class="fa-solid fa-clock"></i>
                                En attente de confirmation
                            </div>
                            <form action="{{ route('particulier.rendezvous.annuler', $rdv) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:#C62828;border-color:#FFCDD2;" onclick="return confirm('Annuler ce rendez-vous ?')">
                                    <i class="fa-solid fa-xmark"></i> Annuler
                                </button>
                            </form>
                        @elseif($rdv->statut->value === 'confirme')
                            <!-- ✅ Bouton Appeler - UNIQUEMENT si confirmé ET numéro présent -->
                            @if($rdv->agence->user && $rdv->agence->user->telephone)
                                <a href="tel:{{ $rdv->agence->user->telephone }}" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-phone"></i> Appeler
                                </a>
                            @endif
                            <form action="{{ route('particulier.rendezvous.annuler', $rdv) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:#C62828;border-color:#FFCDD2;" onclick="return confirm('Annuler ce rendez-vous ?')">
                                    <i class="fa-solid fa-xmark"></i> Annuler
                                </button>
                            </form>
                        @elseif($rdv->statut->value === 'termine')
                            @php
                                // ✅ CORRIGÉ : Vérifier par proposition_id, pas par agence_id
                                $dejaEvalue = \App\Models\Evaluation::where('particulier_id', Auth::user()->particulier->id)
                                    ->where('proposition_id', $rdv->proposition_id)
                                    ->exists();
                            @endphp
                            @if(!$dejaEvalue)
                                <!-- ✅ CORRIGÉ : Passer la proposition, PAS l'agence -->
                                <a href="{{ route('particulier.evaluations.create', $rdv->proposition) }}" class="btn btn-rust btn-sm">
                                    <i class="fa-solid fa-star"></i> Évaluer
                                </a>
                            @else
                                <span style="font-size:12px;color:var(--muted);padding:4px 10px;background:#F7F9FC;border-radius:8px;border:1px solid var(--border);">
                                    <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i> Déjà évalué
                                </span>
                            @endif
                            <!-- ✅ Bouton Appeler - UNIQUEMENT si terminé ET numéro présent -->
                            @if($rdv->agence->user && $rdv->agence->user->telephone)
                                <a href="tel:{{ $rdv->agence->user->telephone }}" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-phone"></i> Appeler
                                </a>
                            @endif
                        @elseif($rdv->statut->value === 'annule')
                            <span style="font-size:12px;color:var(--muted);padding:4px 14px;background:#F7F9FC;border-radius:8px;border:1px solid var(--border);">
                                <i class="fa-solid fa-clock"></i> Rendez-vous annulé
                            </span>
                        @endif
                        <a href="{{ route('particulier.rendezvous.show', $rdv) }}" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-eye"></i> Détails
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="margin-top:30px;">
            {{ $rendezVous->links() }}
        </div>
    @else
        <!-- Message vide -->
        <div style="text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
            <i class="fa-regular fa-calendar-days" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.3;"></i>
            <p style="font-size:16px;font-weight:600;color:var(--text-soft);">Aucun rendez-vous planifié</p>
            <p style="font-size:13px;max-width:400px;margin:4px auto 16px;">
                Les rendez-vous apparaîtront ici après avoir accepté une proposition d'agence.
            </p>
            <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust">
                <i class="fa-solid fa-plus"></i> Publier un besoin
            </a>
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
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-planifie {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-confirme {
        background: #E3F2FD;
        color: #0D47A1;
    }
    .status-annule {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-termine {
        background: #E8F5E9;
        color: #1E7A47;
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

    .btn-rust {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .btn-rust:hover {
        background: #9A4523;
        border-color: #9A4523;
        color: #fff;
    }

    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }

    .btn-ghost:hover {
        background: var(--border);
        color: var(--ink);
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }

    .btn-success {
        background: #25D366;
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .btn-success:hover {
        background: #1DA851;
        color: #fff;
    }

    .pagination {
        display: flex;
        gap: 6px;
        justify-content: center;
        list-style: none;
        padding: 0;
    }
    .pagination li {
        display: inline;
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
    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    @media (max-width: 820px) {
        [style*="display:flex;align-items:center;gap:16px;padding:16px 20px;border-bottom:1px solid var(--border);flex-wrap:wrap;"] {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px !important;
        }
        [style*="flex-shrink:0;"] {
            align-self: center !important;
        }
        [style*="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;"] {
            justify-content: center !important;
        }
        [style*="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;"] form {
            width: 100%;
        }
        [style*="display:flex;gap:8px;flex-shrink:0;flex-wrap:wrap;"] .btn {
            justify-content: center !important;
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .btn-sm {
            font-size: 11px;
            padding: 4px 10px;
        }
        [style*="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:24px;"] {
            grid-template-columns: 1fr 1fr !important;
        }
        .status-pill {
            font-size: 11px;
            padding: 3px 10px;
        }
    }
</style>
@endpush