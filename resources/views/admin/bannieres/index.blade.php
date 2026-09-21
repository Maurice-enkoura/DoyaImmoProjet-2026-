@extends('layouts.admin')

@section('title', 'Gestion des bannières - DoyaImmo')
@section('page_title', 'Bannières')
@section('page_sub', 'Gérez les bannières de la page d\'accueil')

@section('content')
<style>
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }
    .kpi-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 16px 20px;
        text-align: center;
    }
    .kpi-value {
        font-size: 24px;
        font-weight: 800;
        color: var(--ink);
    }
    .kpi-label {
        font-size: 13px;
        color: var(--muted);
        margin-top: 2px;
    }
    .banner-preview {
        width: 100%;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
    }
    .banner-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        margin-bottom: 12px;
        cursor: grab;
        transition: all 0.2s;
    }
    .banner-item:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        border-color: var(--rust);
    }
    .banner-item.dragging {
        opacity: 0.5;
        cursor: grabbing;
    }
    .banner-item .drag-handle {
        cursor: grab;
        color: var(--muted);
        font-size: 18px;
        padding: 4px 8px;
    }
    .banner-item .banner-info {
        flex: 1;
    }
    .banner-item .banner-info .titre {
        font-weight: 600;
        font-size: 14px;
    }
    .banner-item .banner-info .sous-titre {
        font-size: 12px;
        color: var(--muted);
    }
    .banner-item .banner-actions {
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }
    .status-active {
        background: #E8F5E9;
        color: #1E7A47;
    }
    .status-en_attente {
        background: #FFF8E1;
        color: #F57C00;
    }
    .status-inactif {
        background: #F5F5F5;
        color: var(--muted);
    }
</style>

<!-- Statistiques -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-value">{{ $stats['total'] }}</div>
        <div class="kpi-label">Total</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#1E7A47;">{{ $stats['actives'] }}</div>
        <div class="kpi-label">Actives</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#F57C00;">{{ $stats['programmees'] }}</div>
        <div class="kpi-label">Programmées</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-value" style="color:#C62828;">{{ $stats['expirees'] }}</div>
        <div class="kpi-label">Expirées</div>
    </div>
</div>

<div class="section-head">
    <div>
        <h2>Bannières</h2>
        <p>{{ $bannieres->count() }} bannière(s) sur la plateforme</p>
    </div>
    <div>
        <a href="{{ route('admin.bannieres.create') }}" class="btn btn-rust">
            <i class="fa-solid fa-plus"></i> Nouvelle bannière
        </a>
    </div>
</div>

<div id="bannerList">
    @forelse($bannieres as $banniere)
    <div class="banner-item" data-id="{{ $banniere->id }}" draggable="true">
        <div class="drag-handle">
            <i class="fa-solid fa-grip-lines"></i>
        </div>

        <div style="width:150px;flex-shrink:0;">
            @if($banniere->image_url)
                <img src="{{ $banniere->image_url }}" alt="{{ $banniere->titre }}" class="banner-preview">
            @else
                <div style="width:100%;height:120px;background:#F0F0F0;border-radius:8px;display:flex;align-items:center;justify-content:center;color:var(--muted);">
                    <i class="fa-solid fa-image" style="font-size:24px;"></i>
                </div>
            @endif
        </div>

        <div class="banner-info">
            <div class="titre">{{ $banniere->titre }}</div>
            <div class="sous-titre">{{ $banniere->sous_titre ?? 'Sans sous-titre' }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                <span class="status-pill {{ $banniere->statut_class }}" style="font-size:10px;padding:1px 10px;">
                    {{ $banniere->statut_reel }}
                </span>
                <span style="margin-left:8px;">Ordre: {{ $banniere->ordre }}</span>
                @if($banniere->date_debut)
                    <span style="margin-left:8px;">Début: {{ $banniere->date_debut->format('d/m/Y') }}</span>
                @endif
                @if($banniere->date_fin)
                    <span style="margin-left:8px;">Fin: {{ $banniere->date_fin->format('d/m/Y') }}</span>
                @endif
                @if($banniere->jours_restants !== null && $banniere->jours_restants > 0)
                    <span style="margin-left:8px;color:#F57C00;">
                        <i class="fa-solid fa-clock"></i> {{ $banniere->jours_restants }} j restant(s)
                    </span>
                @endif
            </div>
        </div>

        <div class="banner-actions">
            <a href="{{ route('admin.bannieres.show', $banniere) }}" class="btn btn-sm btn-ghost" title="Voir">
                <i class="fa-solid fa-eye"></i>
            </a>
            <a href="{{ route('admin.bannieres.edit', $banniere) }}" class="btn btn-sm btn-ghost" title="Modifier">
                <i class="fa-solid fa-pen"></i>
            </a>

            {{-- Activer / Désactiver --}}
            @if($banniere->est_actif)
                <form action="{{ route('admin.bannieres.desactiver', $banniere) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-ghost" title="Désactiver"
                        onclick="return confirm('Désactiver cette bannière ?')">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </form>
            @else
                @if($banniere->necessiteReprogrammation())
                    <button type="button" class="btn btn-sm btn-ghost" title="Activer (reprogrammation requise)"
                        onclick="openReprogrammerModal(
                            {{ $banniere->id }},
                            '{{ addslashes($banniere->titre) }}',
                            '{{ now()->format('Y-m-d') }}',
                            '{{ $banniere->date_fin?->format('Y-m-d') }}'
                        )">
                        <i class="fa-solid fa-eye" style="color:#F57C00;"></i>
                    </button>
                @else
                    <form action="{{ route('admin.bannieres.activer', $banniere) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-ghost" title="Activer">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </form>
                @endif
            @endif

            {{-- Supprimer --}}
            <form action="{{ route('admin.bannieres.destroy', $banniere) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer"
                    onclick="return confirm('Supprimer cette bannière ?')">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px;color:var(--muted);">
        <i class="fa-solid fa-images" style="font-size:32px;display:block;margin-bottom:12px;"></i>
        <p>Aucune bannière créée.</p>
        <a href="{{ route('admin.bannieres.create') }}" class="btn btn-rust" style="margin-top:12px;">
            <i class="fa-solid fa-plus"></i> Créer ma première bannière
        </a>
    </div>
    @endforelse
</div>

<!-- Info drag & drop -->
<div style="margin-top:20px;">
    <div style="padding:12px 16px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;">
        <div style="display:flex;align-items:center;gap:8px;">
            <i class="fa-solid fa-info-circle" style="color:#E65100;"></i>
            <span style="font-size:13px;color:#BF360C;">
                Glissez-déposez les bannières pour modifier leur ordre d'affichage.
            </span>
        </div>
    </div>
</div>

{{-- ==================== MODALE DE REPROGRAMMATION ==================== --}}
<div id="reprogrammerModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:32px;max-width:500px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:18px;font-weight:700;margin:0;">
                <i class="fa-solid fa-calendar-check" style="color:var(--rust);"></i>
                Reprogrammer la bannière
            </h3>
            <button onclick="closeReprogrammerModal()" style="background:none;border:none;font-size:24px;cursor:pointer;color:var(--muted);">&times;</button>
        </div>

        <div style="padding:12px 16px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;margin-bottom:20px;">
            <p style="font-size:13px;color:#BF360C;margin:0;display:flex;align-items:flex-start;gap:8px;">
                <i class="fa-solid fa-triangle-exclamation" style="margin-top:2px;"></i>
                <span>
                    Cette bannière a des dates passées ou futures. Vous devez <strong>reprogrammer</strong> ses dates pour l'activer.
                </span>
            </p>
        </div>

        <form id="reprogrammerForm" method="POST">
            @csrf
            @method('PATCH')

            <div style="margin-bottom:16px;">
                <label style="display:block;font-weight:600;font-size:13px;margin-bottom:6px;">
                    Titre de la bannière
                </label>
                <div id="reprogrammerTitre" style="padding:10px 14px;background:#F7F9FC;border-radius:8px;font-weight:600;color:var(--ink);">
                    —
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label for="reprogrammer_date_debut" style="display:block;font-weight:600;font-size:13px;margin-bottom:6px;">
                        Date de début <span style="color:#C62828;">*</span>
                    </label>
                    <input type="date" name="date_debut" id="reprogrammer_date_debut" required
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:14px;">
                </div>
                <div>
                    <label for="reprogrammer_date_fin" style="display:block;font-weight:600;font-size:13px;margin-bottom:6px;">
                        Date de fin
                    </label>
                    <input type="date" name="date_fin" id="reprogrammer_date_fin"
                        style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:8px;font-size:14px;">
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;">Vide = illimitée</div>
                </div>
            </div>

            <div style="display:flex;gap:8px;">
                <button type="submit" class="btn btn-rust" style="flex:1;justify-content:center;">
                    <i class="fa-solid fa-check"></i> Activer et programmer
                </button>
                <button type="button" onclick="closeReprogrammerModal()" class="btn btn-ghost">
                    Annuler
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ==================== DRAG & DROP ====================
        const container = document.getElementById('bannerList');
        let dragItem = null;

        container.addEventListener('dragstart', function(e) {
            const item = e.target.closest('.banner-item');
            if (item) {
                dragItem = item;
                item.classList.add('dragging');
                e.dataTransfer.effectAllowed = 'move';
            }
        });

        container.addEventListener('dragend', function(e) {
            const item = e.target.closest('.banner-item');
            if (item) {
                item.classList.remove('dragging');
            }
        });

        container.addEventListener('dragover', function(e) {
            e.preventDefault();
            const afterElement = getDragAfterElement(container, e.clientY);
            const currentItem = e.target.closest('.banner-item');

            if (currentItem && dragItem && currentItem !== dragItem) {
                if (afterElement == null) {
                    container.appendChild(dragItem);
                } else {
                    container.insertBefore(dragItem, afterElement);
                }
            }
        });

        function getDragAfterElement(container, y) {
            const items = [...container.querySelectorAll('.banner-item:not(.dragging)')];

            return items.reduce((closest, child) => {
                const box = child.getBoundingClientRect();
                const offset = y - box.top - box.height / 2;

                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        // Sauvegarder l'ordre automatiquement
        let saveTimeout = null;
        const observer = new MutationObserver(function() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                const items = container.querySelectorAll('.banner-item');
                const ordre = [];
                items.forEach((item) => {
                    ordre.push(parseInt(item.dataset.id));
                });

                fetch('{{ route('admin.bannieres.reordonner') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ordre: ordre })
                });
            }, 500);
        });
        observer.observe(container, { childList: true });
    });

    // ==================== MODALE REPROGRAMMATION ====================
    function openReprogrammerModal(id, titre, dateDebut, dateFin) {
        const modal = document.getElementById('reprogrammerModal');
        const form = document.getElementById('reprogrammerForm');
        const titreEl = document.getElementById('reprogrammerTitre');
        const inputDebut = document.getElementById('reprogrammer_date_debut');
        const inputFin = document.getElementById('reprogrammer_date_fin');

        form.action = `/admin/bannieres/${id}/activer`;

        titreEl.textContent = titre;
        inputDebut.value = dateDebut || new Date().toISOString().split('T')[0];
        inputFin.value = dateFin || '';

        inputDebut.onchange = function() {
            if (this.value) {
                inputFin.min = this.value;
                if (inputFin.value && inputFin.value < this.value) {
                    inputFin.value = this.value;
                }
            }
        };
        inputDebut.onchange();

        modal.style.display = 'flex';
    }

    function closeReprogrammerModal() {
        document.getElementById('reprogrammerModal').style.display = 'none';
    }

    document.getElementById('reprogrammerModal').addEventListener('click', function(e) {
        if (e.target === this) closeReprogrammerModal();
    });

    // ==================== AUTO-OUVERTURE ====================
    @if(session('reprogrammer_id'))
        openReprogrammerModal(
            '{{ session('reprogrammer_id') }}',
            '{{ session('reprogrammer_titre') }}',
            '{{ session('reprogrammer_date_debut') }}',
            '{{ session('reprogrammer_date_fin') }}'
        );
    @endif
</script>
@endsection