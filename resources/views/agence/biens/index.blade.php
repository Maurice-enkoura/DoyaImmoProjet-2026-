@extends('layouts.dashboard-agence')

@section('title', 'Mes biens — DoyaImmo')
@section('page_title', 'Mes biens')
@section('page_sub', 'Gérez vos biens immobiliers')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Mes biens</h2>
            <p>Gérez vos biens immobiliers</p>
        </div>
        <a href="{{ route('agence.biens.create') }}" class="btn btn-rust btn-sm">
            + Nouveau bien
        </a>
    </div>

    @if($biens->count() > 0)
    <div class="biens-grid">
        @foreach($biens as $bien)
        <div class="bien-card">
            <!-- Image du bien -->
            <div class="bien-image">
                @php
                    $image = $bien->medias->where('type_media', 'image')->first();
                @endphp
                @if($image)
                    <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $bien->titre }}">
                @else
                    <div class="bien-image-placeholder">
                        <i class="fa-solid fa-image"></i>
                    </div>
                @endif
                <div class="bien-status {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                    {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                </div>
            </div>
            
            <div class="bien-body">
                <div class="bien-title">{{ $bien->titre }}</div>
                <div class="bien-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                <div class="bien-location">
                    <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}
                </div>
                <div class="bien-features">
                    <span class="meta-pill">{{ $bien->type_bien->label() }}</span>
                    <span class="meta-pill">{{ $bien->type_contrat->label() }}</span>
                    <span class="meta-pill">{{ $bien->surface }} m²</span>
                </div>
                <div class="bien-actions">
                    <a href="{{ route('agence.biens.edit', $bien) }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-pen"></i> Modifier
                    </a>
                    <a href="{{ route('agence.biens.show', $bien) }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-eye"></i> Voir
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top:30px;">
        {{ $biens->links() }}
    </div>
    @else
    <div class="empty-state">
        <i class="fa-solid fa-building"></i>
        <p>Aucun bien publié.</p>
        <a href="{{ route('agence.biens.create') }}" class="btn btn-rust">
            Publier un bien
        </a>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .biens-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .bien-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .bien-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.08);
    }

    .bien-image {
        height: 180px;
        background: #F0F2F5;
        position: relative;
        overflow: hidden;
    }

    .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .bien-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        font-size: 40px;
        opacity: 0.3;
    }

    .bien-status {
        position: absolute;
        top: 12px;
        right: 12px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
    }

    .bien-status.disponible {
        background: var(--green);
    }

    .bien-status.indisponible {
        background: var(--muted);
    }

    .bien-body {
        padding: 14px 16px 16px;
    }

    .bien-title {
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 2px;
        color: var(--ink);
    }

    .bien-price {
        font-weight: 700;
        color: var(--rust);
        font-size: 17px;
        margin-bottom: 4px;
    }

    .bien-location {
        font-size: 13px;
        color: var(--muted);
        margin-bottom: 8px;
    }

    .bien-features {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 12px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        background: var(--border);
        padding: 2px 12px;
        border-radius: 999px;
        font-size: 11px;
        color: var(--text-soft);
    }

    .bien-actions {
        display: flex;
        gap: 8px;
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

    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }

    .btn-ghost:hover {
        background: var(--border);
    }

    .btn-rust {
        background: var(--rust);
        color: #fff;
    }

    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
    }

    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--muted);
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
    }

    .empty-state i {
        font-size: 48px;
        display: block;
        margin-bottom: 16px;
        opacity: 0.3;
    }

    .empty-state p {
        font-size: 16px;
        margin-bottom: 16px;
    }

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