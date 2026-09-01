@extends('layouts.app')

@section('title', $agence->nom_agence . ' — DoyaImmo')

@section('content')
<div class="wrap section" style="padding-top:36px;">
    <div class="agence-container">
        <a href="{{ route('agences.public.index') }}" class="back-link">
            <i class="fa-solid fa-arrow-left"></i> Retour aux agences
        </a>

        <!-- En-tête de l'agence -->
        <div class="agency-header-card">
            <div class="agency-header">
                <div class="agency-avatar">
                    {{ strtoupper(substr($agence->nom_agence, 0, 1)) }}
                </div>
                <div class="agency-info">
                    <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                        <h1 class="agency-name">{{ $agence->nom_agence }}</h1>
                        @php
                            $hasVedette = $agence->biens()->where('est_vedette', true)->exists();
                        @endphp
                        @if($hasVedette)
                            <span class="badge-vedette-agency">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                        @endif
                    </div>
                    <div class="agency-meta">
                        <span><i class="fa-solid fa-location-dot"></i> {{ $agence->adresse }}</span>
                        <span><i class="fa-solid fa-star" style="color:#D4AF37;"></i> {{ number_format($stats['note_moyenne'] ?? 0, 1) }} / 5 ({{ $stats['total_evaluations'] ?? 0 }} avis)</span>
                        <span><i class="fa-solid fa-building"></i> {{ $stats['biens_disponibles'] ?? 0 }} biens disponibles</span>
                        @if($hasVedette)
                            <span style="color:#D4AF37;"><i class="fa-solid fa-star"></i> Biens en vedette</span>
                        @endif
                    </div>
                </div>
                <div class="agency-action">
                    <a href="#biens" class="btn btn-rust">
                        <i class="fa-solid fa-eye"></i> Voir les biens
                    </a>
                </div>
            </div>
            @if($agence->description)
                <div class="agency-description">
                    <p>{{ $agence->description }}</p>
                </div>
            @endif
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_biens'] ?? 0 }}</div>
                <div class="stat-label">Total biens</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['biens_disponibles'] ?? 0 }}</div>
                <div class="stat-label">Biens disponibles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ $stats['total_evaluations'] ?? 0 }}</div>
                <div class="stat-label">Évaluations</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">{{ number_format($stats['note_moyenne'] ?? 0, 1) }}</div>
                <div class="stat-label">Note moyenne ★</div>
            </div>
        </div>

        <!-- Coordonnées de contact -->
        <div class="contact-card">
            <h3 class="contact-title">
                <i class="fa-solid fa-phone" style="color:#B85C3A;"></i> Contact
            </h3>
            <div class="contact-grid">
                @if($agence->user->telephone)
                    <div class="contact-item">
                        <span class="contact-label">Téléphone</span>
                        <div class="contact-value">
                            <a href="tel:{{ $agence->user->telephone }}">{{ $agence->user->telephone }}</a>
                        </div>
                    </div>
                @endif
                @if($agence->user->email)
                    <div class="contact-item">
                        <span class="contact-label">Email</span>
                        <div class="contact-value">
                            <a href="mailto:{{ $agence->user->email }}">{{ $agence->user->email }}</a>
                        </div>
                    </div>
                @endif
                <div class="contact-item">
                    <span class="contact-label">Adresse</span>
                    <div class="contact-value">{{ $agence->adresse }}</div>
                </div>
            </div>
        </div>

        <!-- Biens de l'agence -->
        <div id="biens" class="biens-section">
            <div class="biens-header">
                <h2 class="biens-title">Biens disponibles</h2>
                <span class="biens-count">{{ $biens->total() ?? 0 }} biens</span>
            </div>

            @if(isset($biens) && $biens->count() > 0)
                <div class="biens-grid">
                    @foreach($biens as $bien)
                        <div class="bien-card {{ $bien->est_vedette ? 'vedette-card' : '' }}">
                            <div class="bien-image">
                                @if($bien->medias->first())
                                    <img src="{{ asset('storage/' . $bien->medias->first()->fichier) }}" alt="{{ $bien->titre }}" loading="lazy">
                                @else
                                    <div class="image-placeholder">
                                        <i class="fa-solid fa-image"></i>
                                    </div>
                                @endif
                                
                                <!-- ✅ Badge Vedette -->
                                @if($bien->est_vedette)
                                    <span class="badge-vedette">
                                        <i class="fa-solid fa-star"></i> Vedette
                                    </span>
                                @endif

                                <!-- ✅ Badge Type de bien -->
                                <span class="badge-type">
                                    {{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}
                                </span>

                                <!-- ✅ Badge Statut -->
                                <span class="badge-statut {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                                    {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                                </span>
                            </div>
                            <div class="bien-body">
                                <div class="bien-title">
                                    {{ $bien->titre }}
                                    @if($bien->est_vedette)
                                        <span class="vedette-tag"><i class="fa-solid fa-star"></i></span>
                                    @endif
                                </div>
                                <div class="bien-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                                <div class="bien-location">
                                    <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier }}
                                </div>
                                <!-- ✅ DATE DE PUBLICATION DU BIEN -->
                                <div class="bien-date">
                                    <i class="fa-regular fa-clock"></i>
                                    Publié {{ $bien->created_at->diffForHumans() }}
                                </div>
                                <div class="bien-tags">
                                    <span class="meta-pill">{{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}</span>
                                    <span class="meta-pill">{{ is_object($bien->type_contrat) && method_exists($bien->type_contrat, 'label') ? $bien->type_contrat->label() : $bien->type_contrat }}</span>
                                    <span class="meta-pill">{{ $bien->surface }} m²</span>
                                    @if($bien->est_vedette)
                                        <span class="meta-pill vedette-pill"><i class="fa-solid fa-star"></i> Vedette</span>
                                    @endif
                                </div>
                                <div class="bien-action">
                                    <a href="{{ route('biens.show', $bien) }}" class="btn btn-rust btn-sm btn-block">Voir le détail</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="pagination-wrapper">
                    {{ $biens->links() }}
                </div>
            @else
                <div class="empty-state">
                    <i class="fa-solid fa-building"></i>
                    <p>Aucun bien disponible pour le moment.</p>
                </div>
            @endif
        </div>

        <!-- Évaluations -->
        @if(isset($agence->evaluations) && $agence->evaluations->count() > 0)
            <div class="evaluations-section">
                <h2 class="evaluations-title">
                    Évaluations ({{ $agence->evaluations->count() }})
                </h2>
                <div class="evaluations-list">
                    @foreach($agence->evaluations->take(5) as $evaluation)
                        <div class="evaluation-card">
                            <div class="evaluation-header">
                                <div class="evaluation-user">
                                    <div class="evaluation-avatar">
                                        {{ strtoupper(substr($evaluation->particulier->user->prenom, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="evaluation-name">{{ $evaluation->particulier->user->prenom }} {{ $evaluation->particulier->user->nom }}</div>
                                        <div class="evaluation-date">{{ $evaluation->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                                <div class="evaluation-stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star {{ $i <= $evaluation->note ? 'active' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            @if($evaluation->commentaire)
                                <p class="evaluation-comment">{{ $evaluation->commentaire }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* ===== CONTENEUR ===== */
    .agence-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    @media (min-width: 768px) {
        .agence-container {
            padding: 0 24px;
        }
    }

    @media (min-width: 1200px) {
        .agence-container {
            padding: 0 40px;
        }
    }

    /* ===== BACK LINK ===== */
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
        text-decoration: none;
        margin-bottom: 20px;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: #B85C3A;
    }

    .back-link i {
        font-size: 12px;
    }

    /* ===== BADGE VEDETTE AGENCE ===== */
    .badge-vedette-agency {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        background: #D4AF37;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
        animation: pulseVedette 2s ease-in-out infinite;
    }

    .badge-vedette-agency i {
        font-size: 11px;
    }

    @keyframes pulseVedette {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.85;
        }
    }

    /* ===== AGENCY HEADER ===== */
    .agency-header-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: clamp(20px, 2.5vw, 24px);
        margin-bottom: 24px;
    }

    .agency-header {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .agency-avatar {
        width: clamp(60px, 8vw, 80px);
        height: clamp(60px, 8vw, 80px);
        border-radius: 50%;
        background: #F5E6DF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: clamp(24px, 3vw, 32px);
        font-weight: 700;
        color: #B85C3A;
        flex-shrink: 0;
    }

    .agency-info {
        flex: 1;
        min-width: 150px;
    }

    .agency-name {
        font-family: var(--display);
        font-weight: 800;
        font-size: clamp(22px, 3vw, 28px);
        margin-bottom: 4px;
        word-break: break-word;
    }

    .agency-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        font-size: clamp(12px, 0.8vw, 14px);
        color: var(--muted);
    }

    .agency-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .agency-action {
        flex-shrink: 0;
    }

    .agency-description {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
    }

    .agency-description p {
        font-size: clamp(13px, 0.9vw, 14px);
        color: var(--text-soft);
        line-height: 1.7;
    }

    /* ===== STATS ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 150px), 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: clamp(14px, 1.5vw, 16px) clamp(16px, 1.5vw, 20px);
        text-align: center;
    }

    .stat-number {
        font-size: clamp(20px, 2.5vw, 24px);
        font-weight: 700;
        color: #B85C3A;
    }

    .stat-label {
        font-size: clamp(11px, 0.7vw, 13px);
        color: var(--muted);
        margin-top: 2px;
    }

    /* ===== CONTACT ===== */
    .contact-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: clamp(16px, 2vw, 20px) clamp(16px, 2vw, 24px);
        margin-bottom: 24px;
    }

    .contact-title {
        font-family: var(--display);
        font-size: clamp(15px, 1.1vw, 16px);
        margin-bottom: 12px;
    }

    .contact-title i {
        margin-right: 6px;
    }

    .contact-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .contact-item {
        flex: 1;
        min-width: 140px;
    }

    .contact-label {
        font-size: clamp(11px, 0.7vw, 12px);
        color: var(--muted);
        display: block;
    }

    .contact-value {
        font-weight: 600;
        font-size: clamp(13px, 0.9vw, 15px);
        word-break: break-word;
    }

    .contact-value a {
        color: var(--ink);
        text-decoration: none;
        transition: color 0.2s;
    }

    .contact-value a:hover {
        color: #B85C3A;
    }

    /* ===== BIENS SECTION ===== */
    .biens-section {
        margin-top: 24px;
    }

    .biens-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 12px;
    }

    .biens-title {
        font-family: var(--display);
        font-size: clamp(20px, 2.5vw, 22px);
        margin: 0;
    }

    .biens-count {
        font-size: clamp(13px, 0.9vw, 14px);
        color: var(--muted);
    }

    /* ===== BIENS GRID ===== */
    .biens-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 260px), 1fr));
        gap: 20px;
    }

    .bien-card {
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        overflow: hidden;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }

    .bien-card.vedette-card {
        border-color: #D4AF37;
        border-width: 2px;
        position: relative;
    }

    .bien-card.vedette-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: var(--radius);
        background: linear-gradient(135deg, rgba(212, 175, 55, 0.05), transparent);
        pointer-events: none;
        z-index: 0;
    }

    .bien-image {
        height: clamp(150px, 20vw, 180px);
        background: #F0F2F5;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        position: relative;
        overflow: hidden;
        flex-shrink: 0;
    }

    .bien-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .image-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        opacity: 0.3;
    }

    .image-placeholder i {
        font-size: 32px;
    }

    /* ✅ BADGES SUR LES IMAGES */
    .badge-vedette {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        background: #D4AF37;
        z-index: 2;
        display: flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.3);
        animation: pulseVedette 2s ease-in-out infinite;
    }

    .badge-vedette i {
        font-size: 9px;
    }

    .badge-type {
        position: absolute;
        bottom: 10px;
        left: 10px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        background: #B85C3A;
        z-index: 2;
    }

    .badge-statut {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 3px 12px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        z-index: 2;
    }

    .badge-statut.disponible {
        background: #2A9D8F;
    }

    .badge-statut.indisponible {
        background: #8A91A0;
    }

    .bien-body {
        padding: clamp(12px, 1.5vw, 14px) clamp(14px, 1.5vw, 16px) clamp(14px, 1.5vw, 16px);
        flex: 1;
        display: flex;
        flex-direction: column;
        position: relative;
        z-index: 1;
    }

    .bien-title {
        font-weight: 600;
        font-size: clamp(14px, 1vw, 15px);
        margin-bottom: 2px;
        word-break: break-word;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .vedette-tag {
        color: #D4AF37;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
    }

    .bien-price {
        font-weight: 700;
        color: #B85C3A;
        font-size: clamp(15px, 1.1vw, 16px);
    }

    .bien-location {
        font-size: clamp(12px, 0.8vw, 13px);
        color: var(--muted);
        margin-bottom: 6px;
        word-break: break-word;
    }

    .bien-location i {
        font-size: 11px;
        margin-right: 3px;
    }

    /* ✅ DATE DE PUBLICATION */
    .bien-date {
        font-size: clamp(11px, 0.7vw, 12px);
        color: var(--muted);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .bien-date i {
        font-size: 11px;
        color: var(--muted);
    }

    .bien-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 8px;
    }

    .meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--border);
        padding: 2px 10px;
        border-radius: 999px;
        font-size: clamp(10px, 0.7vw, 11px);
        color: var(--text-soft);
    }

    .vedette-pill {
        background: #FDF5E6;
        color: #B85C3A;
        font-weight: 600;
    }

    .vedette-pill i {
        color: #D4AF37;
    }

    .bien-action {
        margin-top: auto;
    }

    /* ===== EVALUATIONS ===== */
    .evaluations-section {
        margin-top: 40px;
    }

    .evaluations-title {
        font-family: var(--display);
        font-size: clamp(20px, 2.5vw, 22px);
        margin-bottom: 20px;
    }

    .evaluations-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .evaluation-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: clamp(14px, 1.5vw, 16px) clamp(16px, 1.5vw, 20px);
    }

    .evaluation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 8px;
    }

    .evaluation-user {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .evaluation-avatar {
        width: clamp(28px, 3vw, 32px);
        height: clamp(28px, 3vw, 32px);
        border-radius: 50%;
        background: #F5E6DF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: clamp(11px, 0.8vw, 12px);
        color: #B85C3A;
        flex-shrink: 0;
    }

    .evaluation-name {
        font-weight: 600;
        font-size: clamp(13px, 0.9vw, 14px);
    }

    .evaluation-date {
        font-size: clamp(11px, 0.7vw, 12px);
        color: var(--muted);
    }

    .evaluation-stars {
        color: #D4AF37;
        font-size: clamp(13px, 0.9vw, 16px);
    }

    .evaluation-stars .fa-star:not(.active) {
        color: #D4D8E0;
    }

    .evaluation-comment {
        font-size: clamp(12px, 0.8vw, 13.5px);
        color: var(--text-soft);
        line-height: 1.6;
        margin-top: 4px;
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        text-align: center;
        padding: clamp(30px, 4vw, 40px);
        background: #fff;
        border-radius: var(--radius);
        border: 1px solid var(--border);
        color: var(--muted);
    }

    .empty-state i {
        font-size: 32px;
        display: block;
        margin-bottom: 12px;
        opacity: 0.3;
    }

    .empty-state p {
        font-size: clamp(13px, 0.9vw, 14px);
    }

    /* ===== PAGINATION ===== */
    .pagination-wrapper {
        margin-top: 30px;
    }

    .pagination {
        display: flex;
        gap: 5px;
        justify-content: center;
        list-style: none;
        padding: 0;
        flex-wrap: wrap;
    }

    .pagination li {
        display: inline;
    }

    .pagination a,
    .pagination span {
        display: inline-block;
        padding: clamp(5px, 0.5vw, 8px) clamp(8px, 0.8vw, 14px);
        border-radius: 8px;
        border: 1px solid var(--border);
        color: var(--text-soft);
        text-decoration: none;
        font-size: clamp(11px, 0.8vw, 13px);
        transition: all 0.2s;
        min-width: clamp(28px, 3vw, 40px);
        text-align: center;
    }

    .pagination a:hover {
        background: var(--border);
        border-color: var(--border);
    }

    .pagination .active span {
        background: #B85C3A;
        color: #fff;
        border-color: #B85C3A;
    }

    .pagination .disabled span {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ===== BUTTONS ===== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: clamp(11px, 0.8vw, 12.5px);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        white-space: nowrap;
    }

    .btn-rust {
        background: #B85C3A;
        color: #fff;
        border-color: #B85C3A;
    }

    .btn-rust:hover {
        background: #9A4523;
        border-color: #9A4523;
        color: #fff;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: clamp(10px, 0.7vw, 11.5px);
        border-radius: 6px;
    }

    .btn-block {
        width: 100%;
        justify-content: center;
    }

    /* ============================================
       RESPONSIVE
    ============================================ */

    @media (max-width: 820px) {
        .agency-header {
            flex-direction: column;
            text-align: center;
        }

        .agency-meta {
            justify-content: center;
        }

        .agency-action {
            width: 100%;
        }

        .agency-action .btn {
            width: 100%;
            justify-content: center;
        }

        .contact-grid {
            flex-direction: column;
            gap: 12px;
        }

        .biens-grid {
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 220px), 1fr));
            gap: 16px;
        }

        .evaluation-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }

    @media (max-width: 640px) {
        .agence-container {
            padding: 0 12px;
        }

        .agency-header-card {
            padding: 16px;
            border-radius: 12px;
        }

        .agency-avatar {
            width: 56px;
            height: 56px;
            font-size: 20px;
        }

        .agency-name {
            font-size: 20px;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .contact-card {
            padding: 14px 16px;
            border-radius: 12px;
        }

        .biens-grid {
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .bien-image {
            height: 140px;
        }

        .bien-title {
            font-size: 13px;
        }

        .bien-price {
            font-size: 14px;
        }

        .bien-location {
            font-size: 11px;
        }

        .bien-date {
            font-size: 10px;
        }

        .meta-pill {
            font-size: 9px;
            padding: 1px 8px;
        }

        .badge-vedette,
        .badge-type,
        .badge-statut {
            font-size: 9px;
            padding: 2px 10px;
        }

        .badge-vedette i {
            font-size: 8px;
        }

        .badge-vedette-agency {
            font-size: 11px;
            padding: 3px 10px;
        }

        .badge-vedette-agency i {
            font-size: 10px;
        }

        .pagination a,
        .pagination span {
            padding: 4px 8px;
            font-size: 11px;
            min-width: 28px;
        }

        .evaluation-card {
            padding: 12px 14px;
            border-radius: 10px;
        }

        .evaluation-stars {
            font-size: 13px;
        }
    }

    @media (max-width: 460px) {
        .agence-container {
            padding: 0 8px;
        }

        .biens-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }

        .bien-image {
            height: 180px;
        }

        .bien-body {
            padding: 12px 14px 14px;
        }

        .bien-title {
            font-size: 15px;
        }

        .bien-price {
            font-size: 16px;
        }

        .bien-date {
            font-size: 11px;
        }

        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }

        .stat-card {
            padding: 12px;
        }

        .stat-number {
            font-size: 18px;
        }

        .stat-label {
            font-size: 10px;
        }

        .agency-header-card {
            padding: 14px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
        .badge-vedette,
        .badge-vedette-agency {
            animation: none !important;
        }
    }
</style>
@endpush