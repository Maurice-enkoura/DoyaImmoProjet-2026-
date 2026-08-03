@extends('layouts.dashboard-agence')

@section('title', 'Matching — DoyaImmo')
@section('page_title', 'Matching intelligent')
@section('page_sub', 'Découvrez quelles demandes correspondent à vos biens')

@section('content')
<div class="view active">
    <!-- Statistiques -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--teal-soft); color:var(--teal);">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['total_matchs'] ?? 0 }}</div>
                <div class="stat-label">Correspondances</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--gold-soft); color:#8A6414;">
                <i class="fa-solid fa-percent"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['score_moyen'] ?? 0 }}%</div>
                <div class="stat-label">Score moyen</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--rust-soft); color:var(--rust);">
                <i class="fa-solid fa-crown"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['top_score'] ?? 0 }}%</div>
                <div class="stat-label">Meilleur score</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:var(--green-soft); color:#1E7A47;">
                <i class="fa-solid fa-rocket"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value">{{ $stats['taux_couverture'] ?? 0 }}%</div>
                <div class="stat-label">Taux de couverture</div>
            </div>
        </div>
    </div>

    <!-- Message d'information -->
    @if($matchings->count() > 0)
        <div style="margin-bottom:16px;padding:12px 16px;background:#E3F2FD;border-radius:10px;border:1px solid #BBDEFB;">
            <div style="display:flex;align-items:center;gap:10px;">
                <i class="fa-solid fa-info-circle" style="color:#0D47A1;font-size:18px;"></i>
                <div>
                    <span style="font-weight:600;color:#0D47A1;">Matching intelligent :</span>
                    <span style="font-size:13px;color:#1565C0;">
                        Une tolérance de <strong>±20% sur le budget</strong> et <strong>±20% sur la surface</strong> est appliquée pour vous montrer plus de correspondances.
                        @if($matchings->count() == 0)
                            <br>Publiez des biens dans les zones où il y a des demandes.
                        @endif
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- Filtres -->
    @if($matchings->count() > 0)
        <div style="display:flex;flex-wrap:wrap;gap:12px;margin-bottom:16px;align-items:center;">
            <div style="flex:1;min-width:200px;">
                <select id="filterStatus" style="width:100%;padding:8px 14px;border:1px solid var(--border);border-radius:10px;font-size:13px;font-family:inherit;background:#fff;">
                    <option value="all">Toutes les correspondances</option>
                    <option value="high">Scores élevés (≥ 60%)</option>
                    <option value="medium">Scores moyens (40-59%)</option>
                    <option value="low">Scores faibles (< 40%)</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button class="btn btn-ghost btn-sm" onclick="window.location.reload()">
                    <i class="fa-solid fa-rotate"></i> Rafraîchir
                </button>
            </div>
        </div>
    @endif

    <!-- Résultats du matching -->
    @if($matchings->count() > 0)
        <div class="matchings-grid" id="matchingsGrid">
            @foreach($matchings as $item)
                <div class="match-card" data-score="{{ $item['score'] }}">
                    <div class="match-header">
                        <div class="match-info">
                            <div class="match-title">
                                {{ $item['demande']->type_bien->label() }}
                                <span style="font-size:12px;font-weight:400;color:var(--muted);">
                                    ({{ $item['bien']->titre ?? 'Sans bien' }})
                                </span>
                            </div>
                            <div class="match-location">
                                <i class="fa-solid fa-location-dot"></i> {{ $item['demande']->zone_recherchee }}
                            </div>
                            <div class="match-budget">
                                <i class="fa-solid fa-money-bill"></i> {{ number_format($item['demande']->budget_maximum, 0, ',', ' ') }} F/mois
                            </div>
                            <div class="match-date">
                                <i class="fa-regular fa-calendar"></i> Publié {{ $item['demande']->created_at->diffForHumans() }}
                            </div>
                            @if($item['bien'])
                                <div class="match-bien">
                                    <i class="fa-solid fa-building"></i> Bien: {{ $item['bien']->titre }}
                                    <span style="font-size:12px;color:var(--rust);font-weight:600;">
                                        {{ number_format($item['bien']->prix, 0, ',', ' ') }} FCFA
                                    </span>
                                </div>
                            @else
                                <div class="match-bien" style="color:#E65100;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Aucun bien correspondant dans cette zone
                                </div>
                            @endif
                        </div>
                        <div class="match-score">
                            <div class="score-number" style="color:{{ $item['score'] >= 80 ? '#1E7A47' : ($item['score'] >= 60 ? '#F5A623' : ($item['score'] >= 40 ? '#E65100' : '#C62828')) }}">
                                {{ $item['score'] }}%
                            </div>
                            <span class="match-badge match-{{ strtolower($item['niveau']) }}">
                                {{ $item['niveau'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Barre de progression -->
                    <div class="match-progress">
                        <div class="progress-bar">
                            <div class="progress-fill" style="width:{{ $item['score'] }}%;background:{{ $item['score'] >= 80 ? '#1E7A47' : ($item['score'] >= 60 ? '#F5A623' : ($item['score'] >= 40 ? '#E65100' : '#C62828')) }};"></div>
                        </div>
                    </div>

                    <!-- Critères -->
                    <div class="match-criteres">
                        @foreach($item['criteres'] as $key => $critere)
                            <span class="critere-pill {{ $critere['ok'] ? 'ok' : 'ko' }}">
                                {{ $critere['label'] }}
                                @if($critere['ok'])
                                    
                                @elseif($critere['ok'] === 'partiel')
                                    
                                @else
                                    
                                @endif
                            </span>
                        @endforeach
                    </div>

                    <!-- Détails -->
                    @if(isset($item['details']) && count($item['details']) > 0)
                        <div style="margin:8px 0 12px;padding:8px 12px;background:#F7F9FC;border-radius:8px;font-size:12px;color:var(--muted);">
                            @foreach($item['details'] as $detail)
                                <div style="display:flex;align-items:center;gap:6px;padding:2px 0;">
                                    <span>{{ $detail }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="match-actions">
                        <a href="{{ route('agence.demandes.show', $item['demande']) }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;">
                            <i class="fa-solid fa-eye"></i> Voir la demande
                        </a>
                        @if($item['bien'])
                            <a href="{{ route('agence.propositions.create', $item['demande']) }}" class="btn btn-rust btn-sm" style="flex:1;justify-content:center;">
                                <i class="fa-solid fa-paper-plane"></i> Proposer ce bien
                            </a>
                        @else
                            <a href="{{ route('agence.biens.create') }}" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center;">
                                <i class="fa-solid fa-plus"></i> Publier un bien
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="match-footer">
            <i class="fa-regular fa-info-circle"></i> 
            {{ $matchings->count() }} correspondance(s) trouvée(s)
        </div>
    @else
        <div class="match-empty">
            <i class="fa-solid fa-robot"></i>
            <p class="empty-title">Aucune correspondance trouvée</p>
            <p class="empty-desc">
                @if(isset($stats['total_matchs']) && $stats['total_matchs'] == 0)
                    Vous n'avez pas encore de biens publiés ou vos biens ne correspondent à aucune demande.
                    <br><br>
                    <strong>Conseils :</strong>
                    <br>
                    • Assurez-vous que vos biens ont le bon <strong>type</strong> et la bonne <strong>zone</strong>
                    <br>
                    • Le budget des demandes est comparé avec une <strong>tolérance de ±20%</strong>
                    <br>
                    • Les critères <strong>obligatoires</strong> sont : type d'opération, type de bien et zone géographique
                @else
                    Publiez des biens pour voir les demandes qui leur correspondent. 
                    Plus vos biens correspondent aux critères des demandes, plus le score sera élevé.
                @endif
            </p>
            <div class="empty-actions">
                <a href="{{ route('agence.biens.create') }}" class="btn btn-rust">
                    <i class="fa-solid fa-plus"></i> Publier un bien
                </a>
                <a href="{{ route('agence.demandes.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-search"></i> Voir les demandes
                </a>
                <a href="{{ route('agence.profil') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-gear"></i> Gérer mes zones
                </a>
            </div>
        </div>
    @endif

    <!-- Actions rapides -->
    <div class="quick-actions">
        <a href="{{ route('agence.demandes.index') }}" class="btn btn-ghost">
            <i class="fa-solid fa-list"></i> Voir toutes les demandes
        </a>
        <a href="{{ route('agence.biens.index') }}" class="btn btn-ghost">
            <i class="fa-solid fa-building"></i> Gérer mes biens
        </a>
        <a href="{{ route('agence.profil') }}" class="btn btn-ghost">
            <i class="fa-solid fa-gear"></i> Zones d'intervention
        </a>
    </div>
</div>

<script>
    // Filtrage des correspondances par score
    document.getElementById('filterStatus')?.addEventListener('change', function() {
        const status = this.value;
        const cards = document.querySelectorAll('.match-card');
        
        cards.forEach(card => {
            const score = parseInt(card.dataset.score);
            let show = true;
            
            if (status === 'high') {
                show = score >= 60;
            } else if (status === 'medium') {
                show = score >= 40 && score < 60;
            } else if (status === 'low') {
                show = score < 40;
            }
            
            card.style.display = show ? 'block' : 'none';
        });
    });
</script>
@endsection

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .matchings-grid {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: #fff;
        border-radius: var(--radius);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        border: 1px solid var(--border);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-info {
        flex: 1;
    }

    .stat-value {
        font-family: var(--display);
        font-weight: 700;
        font-size: 24px;
        line-height: 1.2;
    }

    .stat-label {
        font-size: 13px;
        color: var(--muted);
    }

    .matchings-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 16px;
    }

    .match-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .match-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .match-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
    }

    .match-info {
        flex: 1;
        min-width: 150px;
    }

    .match-title {
        font-family: var(--display);
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 4px;
        color: var(--ink);
    }

    .match-location, .match-budget, .match-date, .match-bien {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 2px;
    }

    .match-bien {
        font-size: 12px;
        color: var(--text-soft);
        margin-top: 4px;
        padding-top: 4px;
        border-top: 1px dashed var(--border);
    }

    .match-score {
        text-align: right;
        flex-shrink: 0;
    }

    .score-number {
        font-family: var(--display);
        font-weight: 700;
        font-size: 28px;
        line-height: 1.2;
    }

    .match-badge {
        display: inline-block;
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    .match-excellent {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .match-bon {
        background: #FFF8E1;
        color: #E65100;
    }
    .match-moyen {
        background: #FFF3E0;
        color: #E65100;
    }
    .match-faible {
        background: #FFEBEE;
        color: #C62828;
    }
    .match-minimal {
        background: #FFEBEE;
        color: #C62828;
    }

    .match-progress {
        margin: 12px 0;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: var(--border);
        border-radius: 999px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 999px;
        transition: width 0.5s;
    }

    .match-criteres {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 14px;
    }

    .critere-pill {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 500;
    }

    .critere-pill.ok {
        background: #E8F5E9;
        color: #1E7A47;
    }

    .critere-pill.ko {
        background: #FFEBEE;
        color: #C62828;
    }

    .critere-pill.partiel {
        background: #FFF8E1;
        color: #E65100;
    }

    .match-actions {
        display: flex;
        gap: 8px;
    }

    .match-actions .btn {
        flex: 1;
        justify-content: center;
    }

    .match-footer {
        text-align: center;
        font-size: 13px;
        color: var(--muted);
        padding: 8px 0;
    }

    .match-empty {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
    }

    .match-empty i {
        font-size: 48px;
        display: block;
        margin-bottom: 16px;
        opacity: 0.3;
        color: var(--muted);
    }

    .empty-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-soft);
        margin-bottom: 8px;
    }

    .empty-desc {
        font-size: 13px;
        color: var(--muted);
        max-width: 500px;
        margin: 0 auto 16px;
        line-height: 1.6;
    }

    .empty-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .quick-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
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
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }
</style>
@endpush