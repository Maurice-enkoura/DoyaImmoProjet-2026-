@extends('layouts.admin')

@section('title', 'Gestion des bannières - DoyaImmo')
@section('page_title', 'Bannières')
@section('page_sub', 'Gérez les bannières de la page d\'accueil')

@section('content')
<style>
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
</style>

<div class="section-head">
    <div>
        <h2>Bannières</h2>
        <p>{{ $bannieres->count() }} bannières sur la plateforme</p>
    </div>
    <div>
        <a href="{{ route('admin.bannieres.create') }}" class="btn btn-rust">
            <i class="fa-solid fa-plus"></i> Nouvelle bannière
        </a>
    </div>
</div>

<div id="bannerList">
    @forelse($bannieres as $banniere)
    <div class="banner-item" data-id="{{ $banniere->id }}">
        <div class="drag-handle">
            <i class="fa-solid fa-grip-lines"></i>
        </div>
        
        <div style="width:150px;flex-shrink:0;">
            @if($banniere->image)
                <img src="{{ asset('storage/' . $banniere->image) }}" alt="{{ $banniere->titre }}" class="banner-preview">
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
                <span class="status-pill {{ $banniere->est_actif ? 'status-active' : 'status-inactif' }}" style="font-size:10px;padding:1px 10px;">
                    {{ $banniere->est_actif ? ' Actif' : ' Inactif' }}
                </span>
                <span style="margin-left:8px;">Ordre: {{ $banniere->ordre }}</span>
                @if($banniere->date_debut)
                    <span style="margin-left:8px;">Début: {{ $banniere->date_debut->format('d/m/Y') }}</span>
                @endif
                @if($banniere->date_fin)
                    <span style="margin-left:8px;">Fin: {{ $banniere->date_fin->format('d/m/Y') }}</span>
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
            <form action="{{ route('admin.bannieres.toggle', $banniere) }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-sm btn-ghost" title="{{ $banniere->est_actif ? 'Désactiver' : 'Activer' }}">
                    <i class="fa-solid {{ $banniere->est_actif ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                </button>
            </form>
            <form action="{{ route('admin.bannieres.destroy', $banniere) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer cette bannière ?')">
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

<!-- Script pour le drag and drop -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
</script>

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
@endsection