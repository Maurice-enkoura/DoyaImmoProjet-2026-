@extends('layouts.dashboard')

@section('title', 'Offres reçues — DoyaImmo')
@section('page_title', 'Offres reçues')
@section('page_sub', 'Consultez et gérez les propositions des agences')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Offres reçues</h2>
            <p>Consultez et gérez les propositions des agences pour vos besoins actifs</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            <a href="{{ route('particulier.historique') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-clock-rotate-left"></i> Voir historique
            </a>
        </div>
    </div>

    <!-- ✅ Filtres par statut -->
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:20px;">
        <a href="{{ route('particulier.propositions.index') }}" class="filter-btn {{ !request('statut') ? 'active' : '' }}">
            <i class="fa-solid fa-list"></i> Toutes
        </a>
        <a href="{{ route('particulier.propositions.index', ['statut' => 'en_attente']) }}" class="filter-btn {{ request('statut') == 'en_attente' ? 'active' : '' }}">
            <i class="fa-regular fa-clock" style="color:#E65100;"></i> En attente
        </a>
        <a href="{{ route('particulier.propositions.index', ['statut' => 'acceptee']) }}" class="filter-btn {{ request('statut') == 'acceptee' ? 'active' : '' }}">
            <i class="fa-solid fa-check-circle" style="color:#1E7A47;"></i> Acceptées
        </a>
        <a href="{{ route('particulier.propositions.index', ['statut' => 'refusee']) }}" class="filter-btn {{ request('statut') == 'refusee' ? 'active' : '' }}">
            <i class="fa-solid fa-times-circle" style="color:#C62828;"></i> Refusées
        </a>
    </div>



    @if(session('error'))
        <div style="padding:12px 16px;background:#FFEBEE;border-radius:10px;border:1px solid #FFCDD2;margin-bottom:16px;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-exclamation-circle" style="color:#C62828;"></i>
            <span style="color:#C62828;font-size:13px;">{{ session('error') }}</span>
        </div>
    @endif

    @forelse($propositions as $proposition)
        @php
            $statutValue = $proposition->statut_value;
            $statutLabel = $proposition->statut_label;
            
            // Déterminer le libellé du budget
            $budgetLabel = is_object($proposition->demande->type_operation) && $proposition->demande->type_operation->value === 'location' ? 'F/mois' : 'F';
            
            // Couleurs des statuts
            $statusColors = [
                'en_attente' => ['class' => 'attente', 'icon' => 'fa-regular fa-clock'],
                'acceptee' => ['class' => 'acceptee', 'icon' => 'fa-solid fa-check-circle'],
                'refusee' => ['class' => 'refusee', 'icon' => 'fa-solid fa-times-circle'],
                'terminee' => ['class' => 'terminee', 'icon' => 'fa-solid fa-circle-check'],
            ];
            $statusInfo = $statusColors[$statutValue] ?? $statusColors['en_attente'];
        @endphp
        <div class="compare-wrap" style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:24px;{{ $statutValue === 'en_attente' ? 'border-left:4px solid #F5A623;' : '' }}">
            <div class="compare-head" style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                <div>
                    <h3 style="font-family:var(--display);font-size:16px;margin:0;">
                        {{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}
                    </h3>
                    <div class="sub" style="font-size:12.5px;color:var(--muted);margin-top:2px;">
                        Budget max : {{ number_format($proposition->demande->budget_maximum, 0, ',', ' ') }} {{ $budgetLabel }}
                        @if($proposition->demande->surface_minimum)
                            <span style="margin-left:8px;">
                                <i class="fa-regular fa-square"></i> {{ $proposition->demande->surface_minimum }} m²
                            </span>
                        @endif
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    @if($proposition->score_matching)
                        <span style="font-size:12px;color:var(--muted);">
                            Score : 
                            <span style="font-weight:700;color:{{ $proposition->score_matching >= 80 ? '#1E7A47' : ($proposition->score_matching >= 60 ? '#E65100' : '#C62828') }};">
                                {{ $proposition->score_matching }}%
                            </span>
                        </span>
                    @endif
                    <span class="status-pill status-{{ $statusInfo['class'] }}">
                        <i class="{{ $statusInfo['icon'] }}" style="font-size:10px;"></i>
                        {{ $statutLabel }}
                    </span>
                </div>
            </div>
            <div style="padding:16px 20px;">
                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                    <div style="display:flex;align-items:center;gap:12px;">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:var(--rust);flex-shrink:0;">
                            {{ strtoupper(substr($proposition->agence->nom_agence, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight:600;font-size:15px;">
                                {{ $proposition->agence->nom_agence }}
                            </div>
                            <div style="font-size:13px;color:var(--muted);">
                                <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                                {{ number_format($proposition->agence->note_moyenne, 1) }} / 5
                                ({{ $proposition->agence->evaluations->count() }} avis)
                            </div>
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-weight:700;color:var(--rust);font-size:18px;">
                            {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            {{ $proposition->bien->titre ?? 'Bien' }}
                            @if($proposition->bien && $proposition->bien->est_vedette)
                                <span style="color:#F5A623;margin-left:4px;">
                                    <i class="fa-solid fa-star"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- ✅ Équipements du bien -->
                @php
                    $equipementsBien = $proposition->bien->equipements ?? [];
                @endphp
                <div style="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;">
                    <span class="meta-pill"><i class="fa-solid fa-vector-square"></i> {{ $proposition->bien->surface }} m²</span>
                    <span class="meta-pill"><i class="fa-solid fa-bed"></i> {{ $proposition->bien->nombre_chambres }} ch.</span>
                    <span class="meta-pill"><i class="fa-solid fa-bath"></i> {{ $proposition->bien->nombre_salles_bain }} sdb</span>
                    @if($proposition->bien->parking_disponible)
                        <span class="meta-pill"><i class="fa-solid fa-car"></i> Parking</span>
                    @endif
                    @if($proposition->bien->est_meuble)
                        <span class="meta-pill"><i class="fa-solid fa-couch"></i> Meublé</span>
                    @endif
                    @if($proposition->bien->climatisation)
                        <span class="meta-pill"><i class="fa-solid fa-snowflake"></i> Clim</span>
                    @endif
                    @if(count($equipementsBien) > 3)
                        <span class="meta-pill" style="background:#E3F2FD;color:#0D47A1;">
                            +{{ count($equipementsBien) - 3 }} équipements
                        </span>
                    @endif
                </div>

                <!-- Message de l'agence -->
                @if($proposition->message)
                    <div style="margin-top:10px;padding:8px 12px;background:#F7F9FC;border-radius:8px;font-size:13px;color:var(--text-soft);border-left:3px solid var(--rust);">
                        <i class="fa-regular fa-message" style="color:var(--rust);margin-right:6px;"></i>
                        {{ Str::limit($proposition->message, 120) }}
                    </div>
                @endif

                <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;">
                    {{-- ✅ Pour les propositions en attente --}}
                    @if($statutValue === 'en_attente')
                        <form action="{{ route('particulier.propositions.selectionner', $proposition) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-rust btn-sm" onclick="return confirm('✅ Sélectionner cette offre ? Cela clôturera les autres offres en attente.')">
                                <i class="fa-solid fa-check"></i> Choisir cette agence
                            </button>
                        </form>
                        <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-calendar"></i> Planifier une visite
                        </a>
                    @endif

                    {{-- ✅ Pour les propositions acceptées --}}
                    @if($statutValue === 'acceptee')
                        <a href="{{ route('particulier.rendezvous.create', $proposition) }}" class="btn btn-rust btn-sm">
                            <i class="fa-solid fa-calendar-check"></i> Planifier la visite
                        </a>
                    @endif

                    <a href="{{ route('particulier.propositions.show', $proposition) }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-eye"></i> Détails
                    </a>

                    @if($statutValue === 'en_attente')
                        <form action="{{ route('particulier.propositions.refuser', $proposition) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-ghost btn-sm" style="color:#C62828;border-color:#FFCDD2;" onclick="return confirm('❌ Refuser cette offre ?')">
                                <i class="fa-solid fa-times"></i> Refuser
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
            <i class="fa-solid fa-inbox" style="font-size:40px;display:block;margin-bottom:16px;opacity:0.3;"></i>
            @if(request('statut'))
                <p style="font-size:16px;font-weight:600;color:var(--text-soft);">
                    Aucune offre avec le statut "{{ request('statut') }}"
                </p>
                <p style="font-size:13px;color:var(--muted);">Essayez de modifier vos filtres.</p>
                <a href="{{ route('particulier.propositions.index') }}" class="btn btn-ghost" style="margin-top:12px;">
                    <i class="fa-solid fa-rotate"></i> Réinitialiser les filtres
                </a>
            @else
                <p style="font-size:16px;">Aucune offre reçue pour vos besoins actifs.</p>
                <p style="font-size:13px;color:var(--muted);">Les offres refusées ou terminées sont disponibles dans l'historique.</p>
                <a href="{{ route('particulier.demandes.create') }}" class="btn btn-rust" style="margin-top:16px;display:inline-flex;align-items:center;gap:8px;">
                    <i class="fa-solid fa-plus"></i> Publier un besoin
                </a>
            @endif
        </div>
    @endforelse

    <div style="margin-top:30px;">
        {{ $propositions->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ===================== SECTION HEAD ===================== */
    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 20px;
    }

    .section-head h2 {
        font-family: var(--display);
        font-size: 22px;
        font-weight: 700;
        margin: 0;
    }

    .section-head p {
        font-size: 14px;
        color: var(--muted);
        margin: 4px 0 0;
    }

    /* ===================== FILTRES ===================== */
    .filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-soft);
        background: #fff;
        border: 1px solid var(--border);
        text-decoration: none;
        transition: all 0.2s;
    }

    .filter-btn:hover {
        background: var(--border);
    }

    .filter-btn.active {
        background: var(--rust);
        color: #fff;
        border-color: var(--rust);
    }

    .filter-btn.active i {
        color: #fff !important;
    }

    /* ===================== STATUS PILL ===================== */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-attente {
        background: #FFF8E1;
        color: #E65100;
    }
    .status-acceptee {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-refusee {
        background: #FFEBEE;
        color: #C62828;
    }
    .status-terminee {
        background: #E3F2FD;
        color: #0D47A1;
    }

    /* ===================== PAGINATION ===================== */
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
        min-width: 40px;
        text-align: center;
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

    /* ===================== META PILL ===================== */
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11.5px;
        color: var(--text-soft);
    }

    /* ===================== BOUTONS ===================== */
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
        padding: 4px 12px;
        font-size: 12px;
    }

    /* ===================== RESPONSIVE ===================== */
    @media (max-width: 768px) {
        .section-head {
            flex-direction: column;
            align-items: stretch;
        }

        .filter-btn {
            font-size: 12px;
            padding: 5px 12px;
        }

        .compare-head {
            flex-direction: column;
            align-items: stretch !important;
        }
        .compare-head .status-pill {
            align-self: flex-start;
        }
        [style*="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;"] {
            flex-direction: column;
            align-items: stretch !important;
            text-align: left !important;
        }
        [style*="text-align:right;"] {
            text-align: left !important;
        }
        [style*="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;"] {
            flex-direction: column;
            align-items: stretch !important;
        }
        [style*="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap;"] .btn {
            justify-content: center !important;
        }
        [style*="margin-top:12px;display:flex;gap:8px;flex-wrap:wrap;"] {
            justify-content: center !important;
        }
        .pagination a, .pagination span {
            padding: 6px 10px;
            font-size: 12px;
            min-width: 32px;
        }
    }

    @media (max-width: 480px) {
        .filter-btn {
            font-size: 11px;
            padding: 4px 10px;
        }
        .filter-btn i {
            font-size: 10px;
        }
        .status-pill {
            font-size: 11px;
            padding: 3px 10px;
        }
        .meta-pill {
            font-size: 10px;
            padding: 1px 8px;
        }
        .btn-sm {
            font-size: 11px;
            padding: 4px 10px;
        }
        [style*="display:flex;align-items:center;gap:12px;"] {
            flex-direction: column;
            align-items: center !important;
        }
        [style*="display:flex;align-items:center;gap:12px;"] .agency-avatar {
            margin-bottom: 4px;
        }
    }
</style>
@endpush