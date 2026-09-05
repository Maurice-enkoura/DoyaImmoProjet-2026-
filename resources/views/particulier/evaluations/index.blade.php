@extends('layouts.dashboard')

@section('title', 'Mes avis donnés — DoyaImmo')
@section('page_title', 'Mes avis donnés')
@section('page_sub', 'Vos évaluations des agences avec lesquelles vous avez travaillé')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mes avis donnés</h2>
            <p>Vos évaluations des agences avec lesquelles vous avez travaillé</p>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-calendar"></i> Voir mes rendez-vous
            </a>
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

    @if($evaluations->count() > 0)
       

        <!-- Liste des avis -->
        <div style="display:flex;flex-direction:column;gap:16px;">
            @foreach($evaluations as $evaluation)
                <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:16px 20px;transition:box-shadow 0.2s;">
                    <div style="display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap;">
                        <!-- Avatar -->
                        <div style="width:44px;height:44px;border-radius:50%;background:var(--rust-soft);color:var(--rust);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;flex-shrink:0;">
                            {{ strtoupper(substr($evaluation->agence->nom_agence, 0, 1)) }}
                        </div>
                        
                        <!-- Informations -->
                        <div style="flex:1;min-width:150px;">
                            <div style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
                                <span style="font-weight:600;font-size:15px;">{{ $evaluation->agence->nom_agence }}</span>
                                <div style="color:#F5A623;font-size:14px;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $evaluation->note ? 'active' : '' }}"></i>
                                    @endfor
                                </div>
                                <span style="font-size:11px;color:var(--muted);background:var(--border);padding:0 10px;border-radius:20px;">
                                    {{ $evaluation->created_at->format('d/m/Y') }}
                                </span>
                            </div>
                            <!-- Proposition associée -->
                            @if($evaluation->proposition)
                                <div style="font-size:12px;color:var(--muted);margin-top:4px;">
                                    <i class="fa-regular fa-file-lines"></i>
                                    Proposition du {{ $evaluation->proposition->created_at->format('d/m/Y') }}
                                    @if($evaluation->proposition->bien)
                                        - {{ $evaluation->proposition->bien->titre ?? 'Bien' }}
                                    @endif
                                </div>
                            @endif
                            @if($evaluation->commentaire)
                                <p style="font-size:13.5px;color:var(--text-soft);margin-top:8px;line-height:1.6;">
                                    "{{ $evaluation->commentaire }}"
                                </p>
                            @endif
                            @if($evaluation->reponse_agence)
                                <div style="margin-top:8px;padding:10px 14px;background:#F7F9FC;border-radius:8px;border-left:3px solid var(--rust);">
                                    <div style="font-size:12px;color:var(--muted);font-weight:600;">
                                        <i class="fa-regular fa-reply"></i> Réponse de l'agence :
                                    </div>
                                    <div style="font-size:13px;color:var(--text-soft);margin-top:4px;">
                                        {{ $evaluation->reponse_agence }}
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div style="display:flex;gap:6px;flex-shrink:0;flex-wrap:wrap;">
                            <!-- ✅ CORRIGÉ : Utilisation de l'ID pour l'évaluation -->
                            <a href="{{ route('particulier.evaluations.show', $evaluation->id) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i> Détails
                            </a>
                            @if($evaluation->particulier_id === Auth::user()->particulier->id)
                                <!-- ✅ CORRIGÉ : Utilisation de l'ID pour l'évaluation -->
                                <a href="{{ route('particulier.evaluations.edit', $evaluation->id) }}" class="btn btn-ghost btn-sm">
                                    <i class="fa-solid fa-pen"></i> Modifier
                                </a>
                                <!-- ✅ CORRIGÉ : Utilisation de l'ID pour l'évaluation -->
                                <form action="{{ route('particulier.evaluations.destroy', $evaluation->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm" style="color:#C62828;border-color:#FFCDD2;" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div style="margin-top:30px;">
            {{ $evaluations->links() }}
        </div>
    @else
        <!-- Message vide -->
        <div style="text-align:center;padding:60px 20px;color:var(--muted);background:#fff;border-radius:var(--radius);border:1px solid var(--border);">
            <i class="fa-regular fa-star" style="font-size:48px;display:block;margin-bottom:16px;opacity:0.3;"></i>
            <p style="font-size:16px;font-weight:600;color:var(--text-soft);">Aucun avis donné</p>
            <p style="font-size:13px;max-width:400px;margin:4px auto 16px;">
                Évaluez les agences après avoir terminé une visite. Vos avis aident les autres clients.
            </p>
            <a href="{{ route('particulier.rendezvous.index') }}" class="btn btn-rust">
                <i class="fa-solid fa-calendar"></i> Voir mes rendez-vous
            </a>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .fa-star.active {
        color: #F5A623;
    }
    .fa-star:not(.active) {
        color: #D4D8E0;
    }

    /* ===== STATS ===== */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 12px 16px;
        text-align: center;
    }

    .stat-number {
        font-size: 22px;
        font-weight: 700;
        color: var(--ink);
    }

    .stat-label {
        font-size: 12px;
        color: var(--muted);
        margin-top: 2px;
    }

    /* ===== PAGINATION ===== */
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

    /* ===== BOUTONS ===== */
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
        padding: 4px 12px;
        font-size: 12px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .section-head {
            flex-direction: column;
            align-items: stretch !important;
            gap: 12px;
        }
        .section-head .btn {
            justify-content: center;
        }
        .stats-row {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        [style*="display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap;"] {
            flex-direction: column;
        }
        [style*="display:flex;gap:6px;flex-shrink:0;flex-wrap:wrap;"] {
            width: 100%;
            justify-content: stretch;
        }
        [style*="display:flex;gap:6px;flex-shrink:0;flex-wrap:wrap;"] .btn {
            flex: 1;
            justify-content: center;
        }
    }

    @media (max-width: 480px) {
        .stats-row {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endpush