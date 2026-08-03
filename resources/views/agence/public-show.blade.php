@extends('layouts.app')

@section('title', $agence->nom_agence . ' — DoyaImmo')

@section('content')
<div class="wrap section" style="padding-top:36px;">
    <a href="{{ route('agences.public.index') }}" style="font-size:13px; color:var(--muted); display:inline-block; margin-bottom:20px;">
        ← Retour aux agences
    </a>

    <!-- En-tête de l'agence -->
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;margin-bottom:24px;">
        <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
            <div style="width:80px;height:80px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:700;color:var(--rust);flex-shrink:0;">
                {{ strtoupper(substr($agence->nom_agence, 0, 1)) }}
            </div>
            <div style="flex:1;">
                <h1 style="font-family:var(--display);font-weight:800;font-size:28px;margin-bottom:4px;">
                    {{ $agence->nom_agence }}
                </h1>
                <div style="display:flex;flex-wrap:wrap;gap:16px;font-size:14px;color:var(--muted);">
                    <span><i class="fa-solid fa-location-dot"></i> {{ $agence->adresse }}</span>
                    <span><i class="fa-solid fa-star" style="color:#F5A623;"></i> {{ number_format($stats['note_moyenne'] ?? 0, 1) }} / 5 ({{ $stats['total_evaluations'] ?? 0 }} avis)</span>
                    <span><i class="fa-solid fa-building"></i> {{ $stats['biens_disponibles'] ?? 0 }} biens disponibles</span>
                </div>
            </div>
            <div>
                <a href="#biens" class="btn btn-rust">
                    <i class="fa-solid fa-eye"></i> Voir les biens
                </a>
            </div>
        </div>
        @if($agence->description)
            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
                <p style="font-size:14px;color:var(--text-soft);line-height:1.7;">{{ $agence->description }}</p>
            </div>
        @endif
    </div>

    <!-- Statistiques -->
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:24px;">
        <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 20px;text-align:center;">
            <div style="font-size:24px;font-weight:700;color:var(--rust);">{{ $stats['total_biens'] ?? 0 }}</div>
            <div style="font-size:13px;color:var(--muted);">Total biens</div>
        </div>
        <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 20px;text-align:center;">
            <div style="font-size:24px;font-weight:700;color:var(--rust);">{{ $stats['biens_disponibles'] ?? 0 }}</div>
            <div style="font-size:13px;color:var(--muted);">Biens disponibles</div>
        </div>
        <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 20px;text-align:center;">
            <div style="font-size:24px;font-weight:700;color:var(--rust);">{{ $stats['total_evaluations'] ?? 0 }}</div>
            <div style="font-size:13px;color:var(--muted);">Évaluations</div>
        </div>
        <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:16px 20px;text-align:center;">
            <div style="font-size:24px;font-weight:700;color:var(--rust);">{{ number_format($stats['note_moyenne'] ?? 0, 1) }}</div>
            <div style="font-size:13px;color:var(--muted);">Note moyenne ★</div>
        </div>
    </div>

    <!-- Coordonnées de contact -->
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:20px 24px;margin-bottom:24px;">
        <h3 style="font-family:var(--display);font-size:16px;margin-bottom:12px;">
            <i class="fa-solid fa-phone" style="color:var(--rust);"></i> Contact
        </h3>
        <div style="display:flex;flex-wrap:wrap;gap:20px;">
            @if($agence->user->telephone)
                <div>
                    <span style="font-size:12px;color:var(--muted);">Téléphone</span>
                    <div style="font-weight:600;font-size:15px;">
                        <a href="tel:{{ $agence->user->telephone }}" style="color:var(--ink);text-decoration:none;">
                            {{ $agence->user->telephone }}
                        </a>
                    </div>
                </div>
            @endif
            @if($agence->user->email)
                <div>
                    <span style="font-size:12px;color:var(--muted);">Email</span>
                    <div style="font-weight:600;font-size:15px;">
                        <a href="mailto:{{ $agence->user->email }}" style="color:var(--ink);text-decoration:none;">
                            {{ $agence->user->email }}
                        </a>
                    </div>
                </div>
            @endif
            <div>
                <span style="font-size:12px;color:var(--muted);">Adresse</span>
                <div style="font-weight:600;font-size:15px;">{{ $agence->adresse }}</div>
            </div>
        </div>
    </div>

    <!-- Biens de l'agence -->
    <div id="biens">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;">
            <h2 style="font-family:var(--display);font-size:22px;">Biens disponibles</h2>
            <span style="font-size:14px;color:var(--muted);">{{ $biens->total() ?? 0 }} biens</span>
        </div>

        @if(isset($biens) && $biens->count() > 0)
            <div class="biens-grid">
                @foreach($biens as $bien)
                    <div class="bien-card">
                        <div class="bien-image" style="height:180px;">
                            @if($bien->medias->first())
                                <img src="{{ asset('storage/' . $bien->medias->first()->fichier) }}" alt="{{ $bien->titre }}">
                            @else
                                <i class="fa-solid fa-image" style="font-size:32px;opacity:0.3;"></i>
                            @endif
                        </div>
                        <div class="bien-body">
                            <div style="font-weight:600;font-size:15px;margin-bottom:4px;">{{ $bien->titre }}</div>
                            <div style="font-weight:700;color:var(--rust);font-size:16px;">
                                {{ number_format($bien->prix, 0, ',', ' ') }} FCFA
                            </div>
                            <div style="font-size:13px;color:var(--muted);">
                                <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}
                            </div>
                            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;">
                                <span class="meta-pill">{{ $bien->type_bien->label() }}</span>
                                <span class="meta-pill">{{ $bien->type_contrat->label() }}</span>
                                <span class="meta-pill">{{ $bien->surface }} m²</span>
                            </div>
                            <div style="margin-top:12px;">
                                <a href="{{ route('biens.show', $bien) }}" class="btn btn-rust btn-sm btn-block">Voir le détail</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="margin-top:30px;">
                {{ $biens->links() }}
            </div>
        @else
            <div style="text-align:center;padding:40px;background:#fff;border-radius:var(--radius);border:1px solid var(--border);color:var(--muted);">
                <i class="fa-solid fa-building" style="font-size:32px;display:block;margin-bottom:12px;opacity:0.3;"></i>
                <p>Aucun bien disponible pour le moment.</p>
            </div>
        @endif
    </div>

    <!-- Évaluations -->
    @if(isset($agence->evaluations) && $agence->evaluations->count() > 0)
        <div style="margin-top:40px;">
            <h2 style="font-family:var(--display);font-size:22px;margin-bottom:20px;">
                Évaluations ({{ $agence->evaluations->count() }})
            </h2>
            <div style="display:flex;flex-direction:column;gap:16px;">
                @foreach($agence->evaluations->take(5) as $evaluation)
                    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:16px 20px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:8px;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:32px;height:32px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;color:var(--rust);">
                                    {{ strtoupper(substr($evaluation->particulier->user->prenom, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight:600;font-size:14px;">{{ $evaluation->particulier->user->prenom }} {{ $evaluation->particulier->user->nom }}</div>
                                    <div style="font-size:12px;color:var(--muted);">{{ $evaluation->created_at->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            <div style="color:#F5A623;font-size:16px;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= $evaluation->note ? 'active' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                        @if($evaluation->commentaire)
                            <p style="font-size:13.5px;color:var(--text-soft);line-height:1.6;">{{ $evaluation->commentaire }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .biens-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 20px;
    }
    .bien-card {
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s;
    }
    .bien-card:hover {
        transform: translateY(-3px);
    }
    .bien-image {
        background: #E8ECF0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        position: relative;
        overflow: hidden;
    }
    .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .bien-body {
        padding: 14px 16px 16px;
    }
    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 11px;
        color: var(--text-soft);
    }
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