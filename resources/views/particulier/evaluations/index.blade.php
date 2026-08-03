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
    </div>

    @if($evaluations->count() > 0)
        <!-- Statistiques rapides -->
        

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
                            </div>
                            <div style="font-size:12px;color:var(--muted);">
                                <i class="fa-regular fa-calendar"></i> {{ $evaluation->created_at->format('d/m/Y') }}
                            </div>
                            @if($evaluation->commentaire)
                                <p style="font-size:13.5px;color:var(--text-soft);margin-top:8px;line-height:1.6;">
                                    {{ $evaluation->commentaire }}
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
                        <div style="flex-shrink:0;">
                            <a href="{{ route('particulier.evaluations.show', $evaluation) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-eye"></i> Détails
                            </a>
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
</style>
@endpush