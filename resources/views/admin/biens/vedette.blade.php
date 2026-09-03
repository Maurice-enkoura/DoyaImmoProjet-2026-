@extends('layouts.admin')

@section('title', 'Biens à la une - DoyaImmo')
@section('page_title', 'Biens à la une')
@section('page_sub', 'Biens mis en avant sur la plateforme')

@section('content')
<style>
    .vedette-badge {
        background: #F5A623;
        color: #fff;
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .progress-bar {
        width: 100%;
        height: 6px;
        background: #E8ECF0;
        border-radius: 3px;
        overflow: hidden;
        margin-top: 4px;
    }
    
    .progress-bar .fill {
        height: 100%;
        border-radius: 3px;
        background: #F5A623;
        transition: width 0.3s ease;
    }
</style>

<div class="section-head">
    <div>
        <h2>⭐ À la une</h2>
        <p>{{ $biens->total() }} biens en vedette</p>
    </div>
    <a href="{{ route('admin.biens.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux biens
    </a>
</div>

<!-- Statistiques -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['en_vedette'] ?? 0 }}</div>
        <div class="kpi-label">⭐ En vedette</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['expirees'] ?? 0 }}</div>
        <div class="kpi-label">⏳ Expirées</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['total'] ?? 0 }}</div>
        <div class="kpi-label">📦 Total des biens</div>
    </div>
</div>

<!-- Table -->
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Bien</th>
                <th>Agence</th>
                <th>Vedette</th>
                <th>Progression</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($biens as $bien)
            <tr>
                <td>
                    <div style="display:flex;align-items:center;gap:10px;">
                        @if($bien->medias && $bien->medias->first())
                        <img src="{{ asset('storage/' . $bien->medias->first()->fichier) }}"
                            alt="{{ $bien->titre }}"
                            style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                        @else
                        <div style="width:50px;height:50px;background:#F0F0F0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--muted);">
                            <i class="fa-solid fa-image"></i>
                        </div>
                        @endif
                        <div>
                            <div class="cell-main">
                                {{ Str::limit($bien->titre, 40) }}
                                @if($bien->est_vedette && $bien->vedette_fin > now())
                                    <span class="vedette-badge">
                                        <i class="fa-solid fa-star"></i> Vedette
                                    </span>
                                @endif
                            </div>
                            <div class="cell-sub">
                                <i class="fa-solid fa-location-dot" style="font-size:10px;"></i> 
                                {{ $bien->quartier_nom }}
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div>{{ $bien->agence->nom_agence ?? 'N/A' }}</div>
                    <div class="cell-sub">{{ $bien->agence->user->email ?? '' }}</div>
                </td>
                <td>
                    @if($bien->est_vedette && $bien->vedette_fin > now())
                        <span class="vedette-badge">
                            <i class="fa-solid fa-star"></i> En vedette
                        </span>
                        <div style="font-size:12px;color:var(--muted);margin-top:4px;">
                            Début: {{ $bien->vedette_debut->format('d/m/Y') }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            Fin: {{ $bien->vedette_fin->format('d/m/Y') }}
                            <span style="color:#E65100;font-weight:600;">
                                ({{ $bien->vedette_jours_label }})
                            </span>
                        </div>
                    @else
                        <span class="status-pill status-inactif">Expirée</span>
                    @endif
                </td>
                <td>
                    @if($bien->est_vedette && $bien->vedette_fin > now())
                        @php
                            $total = $bien->vedette_debut->diffInDays($bien->vedette_fin);
                            $ecoule = $bien->vedette_debut->diffInDays(now());
                            $pourcentage = $total > 0 ? round(($ecoule / $total) * 100) : 0;
                            $pourcentage = min(100, max(0, $pourcentage));
                        @endphp
                        <div>
                            <div style="display:flex;justify-content:space-between;font-size:12px;">
                                <span>{{ $bien->vedette_debut->format('d/m/Y') }}</span>
                                <span>{{ $bien->vedette_fin->format('d/m/Y') }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="fill" style="width:{{ $pourcentage }}%;"></div>
                            </div>
                            <div style="text-align:center;font-size:11px;color:var(--muted);margin-top:2px;">
                                {{ $pourcentage }}%
                            </div>
                        </div>
                    @else
                        <span style="font-size:12px;color:var(--muted);">—</span>
                    @endif
                </td>
                <td style="text-align:center;">
                    <div style="display:flex;gap:4px;justify-content:center;">
                        <!-- ✅ CORRIGÉ : Utilisation du slug -->
                        <a href="{{ route('admin.biens.show', $bien->slug) }}" class="btn btn-sm btn-ghost" title="Voir">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                        @if($bien->est_vedette && $bien->vedette_fin > now())
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <form action="{{ route('admin.biens.vedette.retirer', $bien->slug) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Retirer de la vedette" onclick="return confirm('Retirer ce bien de la vedette ?')">
                                    <i class="fa-solid fa-star-half-stroke"></i>
                                </button>
                            </form>
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <button type="button" class="btn btn-sm btn-ghost" title="Prolonger" onclick="openProlongerModal('{{ $bien->slug }}')">
                                <i class="fa-solid fa-clock"></i>
                            </button>
                        @else
                            <!-- ✅ CORRIGÉ : Utilisation du slug -->
                            <button type="button" class="btn btn-sm btn-ghost" title="Mettre en vedette" onclick="openVedetteModal('{{ $bien->slug }}')">
                                <i class="fa-solid fa-star" style="color:#F5A623;"></i>
                            </button>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;padding:40px;color:var(--muted);">
                    <i class="fa-solid fa-star" style="font-size:32px;display:block;margin-bottom:12px;color:#F5A623;"></i>
                    Aucun bien en vedette.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:20px;">
    {{ $biens->appends(request()->query())->links() }}
</div>

<!-- Modals -->
<div id="vedetteModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:var(--radius);padding:32px;max-width:400px;width:90%;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-family:var(--display);font-size:18px;">
                <i class="fa-solid fa-star" style="color:#F5A623;"></i> Mettre en vedette
            </h3>
            <button onclick="closeVedetteModal()" style="background:none;border:none;font-size:24px;cursor:pointer;">&times;</button>
        </div>
        
        <!-- ✅ CORRIGÉ : Utilisation du slug via JavaScript -->
        <form id="vedetteForm" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label for="duree" style="display:block;font-weight:600;margin-bottom:4px;">Durée (en jours)</label>
                <select name="duree" id="duree" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:14px;">
                    <option value="1">1 jour</option>
                    <option value="3">3 jours</option>
                    <option value="7" selected>7 jours</option>
                    <option value="14">14 jours</option>
                    <option value="30">30 jours</option>
                </select>
            </div>
            <div style="padding:12px 16px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;margin-bottom:16px;">
                <p style="font-size:13px;color:#BF360C;margin:0;">
                    <i class="fa-solid fa-info-circle"></i> 
                    Le bien apparaîtra dans la section "À la une" de l'accueil.
                </p>
            </div>
            <button type="submit" class="btn btn-rust" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-star"></i> Mettre en vedette
            </button>
        </form>
    </div>
</div>

<div id="prolongerModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:var(--radius);padding:32px;max-width:400px;width:90%;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
            <h3 style="font-family:var(--display);font-size:18px;">
                <i class="fa-solid fa-clock" style="color:#F5A623;"></i> Prolonger la vedette
            </h3>
            <button onclick="closeProlongerModal()" style="background:none;border:none;font-size:24px;cursor:pointer;">&times;</button>
        </div>
        
        <!-- ✅ CORRIGÉ : Utilisation du slug via JavaScript -->
        <form id="prolongerForm" method="POST">
            @csrf
            <div style="margin-bottom:16px;">
                <label for="duree_prolonger" style="display:block;font-weight:600;margin-bottom:4px;">Prolonger de (en jours)</label>
                <select name="duree" id="duree_prolonger" style="width:100%;padding:8px 12px;border:1px solid var(--border);border-radius:8px;font-size:14px;">
                    <option value="1">+1 jour</option>
                    <option value="3">+3 jours</option>
                    <option value="7" selected>+7 jours</option>
                    <option value="14">+14 jours</option>
                    <option value="30">+30 jours</option>
                </select>
            </div>
            <button type="submit" class="btn btn-rust" style="width:100%;justify-content:center;">
                <i class="fa-solid fa-clock"></i> Prolonger
            </button>
        </form>
    </div>
</div>

<script>
    // ✅ CORRIGÉ : Utilisation du slug dans les fonctions JavaScript
    function openVedetteModal(bienSlug) {
        document.getElementById('vedetteForm').action = `/admin/biens/${bienSlug}/vedette`;
        document.getElementById('vedetteModal').style.display = 'flex';
    }

    function closeVedetteModal() {
        document.getElementById('vedetteModal').style.display = 'none';
    }

    function openProlongerModal(bienSlug) {
        document.getElementById('prolongerForm').action = `/admin/biens/${bienSlug}/vedette/prolonger`;
        document.getElementById('prolongerModal').style.display = 'flex';
    }

    function closeProlongerModal() {
        document.getElementById('prolongerModal').style.display = 'none';
    }

    document.getElementById('vedetteModal').addEventListener('click', function(e) {
        if (e.target === this) closeVedetteModal();
    });
    document.getElementById('prolongerModal').addEventListener('click', function(e) {
        if (e.target === this) closeProlongerModal();
    });
</script>
@endsection