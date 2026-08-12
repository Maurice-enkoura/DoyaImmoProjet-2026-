@extends('layouts.app')

@section('title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')

@section('content')
<!-- ==================== BANNIÈRES ==================== -->
@if(isset($bannieres) && $bannieres->count() > 0)
<section class="wrap banner-section">
    <div class="banner-container">
        <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($bannieres as $index => $banniere)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <div class="banner-item">
                        @if($banniere->image)
                            <img src="{{ asset('storage/' . $banniere->image) }}" 
                                 alt="{{ $banniere->titre }}" 
                                 loading="lazy">
                        @endif
                        <div class="banner-content {{ $banniere->position_texte ?? 'gauche' }}">
                            <h2>{{ $banniere->titre }}</h2>
                            @if($banniere->sous_titre)
                                <p>{{ $banniere->sous_titre }}</p>
                            @endif
                            @if($banniere->lien)
                                <a href="{{ $banniere->lien }}" class="btn btn-rust">Découvrir</a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($bannieres->count() > 1)
            <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
            @endif
        </div>
    </div>
</section>
@endif

<!-- ==================== HERO SECTION ==================== -->
<section class="wrap hero">
    <div class="hero-container">
        <div class="hero-grid">
            <div class="hero-content">
                
                <h1 class="h-hero">
                    Trouvez un logement <br>
                    <span style="color:var(--rust)">sans passer par 10 agences.</span>
                </h1>
                <p class="lead">
                    Décrivez le logement que vous cherchez, les agences immobilières inscrites vous envoient leurs propositions. 
                    Comparez, visitez, choisissez en toute transparence.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('register') }}" class="btn btn-rust btn-lg">
                        <i class="fa-solid fa-pen-to-square"></i> Publier ma recherche
                    </a>
                    <a href="{{ route('besoins.index') }}" class="btn btn-ghost btn-lg">
                        <i class="fa-solid fa-eye"></i> Parcourir les besoins
                    </a>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['besoins'] ?? 0 }}+</span>
                        <span class="stat-label">Besoins actifs</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['biens'] ?? 0 }}+</span>
                        <span class="stat-label">Biens disponibles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $stats['agences'] ?? 0 }}+</span>
                        <span class="stat-label">Agences partenaires</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" style="color:#F5A623;">{{ $stats['en_vedette'] ?? 0 }}</span>
                        <span class="stat-label"> En vedette</span>
                    </div>
                </div>
            </div>
            <div class="hero-illustration">
                @forelse($demandesRecentes ?? [] as $demande)
                <div class="mini-card">
                    <div class="t">{{ is_object($demande->type_bien) && method_exists($demande->type_bien, 'label') ? $demande->type_bien->label() : $demande->type_bien }} — {{ $demande->zone_recherchee }}</div>
                    <div class="s">
                        <i class="fa-regular fa-message"></i> {{ $demande->propositions->count() }} proposition(s) · {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F
                    </div>
                </div>
                @empty
                <div class="mini-card">
                    <div class="t">Maison — Thiaroye</div>
                    <div class="s"><i class="fa-regular fa-message"></i> 0 proposition(s) · 53 997 F</div>
                </div>
                <div class="mini-card">
                    <div class="t">Appartement — Almadies</div>
                    <div class="s"><i class="fa-regular fa-message"></i> 1 proposition(s) · 253 901 F</div>
                </div>
                <div class="mini-card" style="margin-bottom:0;">
                    <div class="t">Appartement — Diamniadio</div>
                    <div class="s"><i class="fa-regular fa-message"></i> 0 proposition(s) · 403 550 F</div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</section>

<!-- ==================== BIENS EN VEDETTE (HORIZONTAL SCROLL) ==================== -->
@if(isset($biensVedette) && $biensVedette->count() > 0)
<section class="wrap section vedette-section">
    <div class="biens-container">
        <div class="section-header">
            <div>
                <span class="eyebrow"> À la une</span>
                <h2 class="h-section">Biens en vedette</h2>
            </div>
            <a href="{{ route('biens.index') }}" class="btn btn-ghost btn-sm">
                Voir tous les biens →
            </a>
        </div>

        <div class="vedette-scroll-wrapper">
            <div class="vedette-scroll">
                @foreach($biensVedette as $bien)
                    <div class="vedette-card">
                        <div class="vedette-image">
                            @if($bien->medias && $bien->medias->where('type_media', 'image')->first())
                                <img src="{{ asset('storage/' . $bien->medias->where('type_media', 'image')->first()->fichier) }}" 
                                     alt="{{ $bien->titre }}" loading="lazy">
                            @else
                                <div class="image-placeholder">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            @endif
                            <span class="badge-vedette">
                                <i class="fa-solid fa-star"></i> Vedette
                            </span>
                            <span class="badge-status {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                                {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                            </span>
                        </div>
                        <div class="vedette-body">
                            <h3 class="vedette-title">{{ $bien->titre }}</h3>
                            <div class="vedette-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                            <div class="vedette-location">
                                <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier->nom ?? $bien->quartier }}
                            </div>
                            <div class="vedette-features">
                                <span><i class="fa-regular fa-vector-square"></i> {{ $bien->surface }} m²</span>
                                <span><i class="fa-regular fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                                <span><i class="fa-regular fa-bath"></i> {{ $bien->nombre_salles_bain }} sdb</span>
                            </div>
                            <a href="{{ route('biens.show', $bien) }}" class="btn btn-rust btn-sm btn-block">
                                <i class="fa-regular fa-eye"></i> Voir le bien
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<!-- ==================== QUARTIERS POPULAIRES ==================== -->
<section class="wrap section">
    <div class="quartiers-container">
        <div class="section-header">
            <div>
                <span class="eyebrow">Quartiers recherchés</span>
                <h2 class="h-section">Les quartiers les plus demandés</h2>
            </div>
            <a href="{{ route('besoins.index') }}" class="btn btn-ghost btn-sm">
                Voir tous les besoins →
            </a>
        </div>
        <div class="zone-row">
            @forelse($quartiersPopulaires ?? [] as $quartier)
            <div class="zone-chip">
                <b>{{ $quartier->nom }}</b>
                <span>{{ $quartier->demandes_count }} besoins</span>
            </div>
            @empty
            <div class="zone-chip">
                <b>Almadies</b>
                <span>24 besoins</span>
            </div>
            <div class="zone-chip">
                <b>Pikine</b>
                <span>18 besoins</span>
            </div>
            <div class="zone-chip">
                <b>Thiaroye</b>
                <span>15 besoins</span>
            </div>
            <div class="zone-chip">
                <b>Diamniadio</b>
                <span>11 besoins</span>
            </div>
            <div class="zone-chip">
                <b>Sacré-Cœur</b>
                <span>9 besoins</span>
            </div>
            @endforelse
        </div>
    </div>
</section>

<!-- ==================== DERNIERS BIENS ==================== -->
<section class="wrap section" style="padding-top:10px;">
    <div class="biens-container">
        <div class="section-header">
            <div>
                <span class="eyebrow">Nouveautés</span>
                <h2 class="h-section">Derniers biens disponibles</h2>
            </div>
            <a href="{{ route('biens.index') }}" class="btn btn-ghost btn-sm">
                Voir tous les biens →
            </a>
        </div>

        @if(isset($derniersBiens) && $derniersBiens->count() > 0)
            <div class="biens-grid">
                @foreach($derniersBiens as $bien)
                    <div class="bien-card {{ $bien->est_vedette ? 'vedette-card' : '' }}">
                        <div class="bien-image">
                            @if($bien->medias && $bien->medias->where('type_media', 'image')->first())
                                <img src="{{ asset('storage/' . $bien->medias->where('type_media', 'image')->first()->fichier) }}" 
                                     alt="{{ $bien->titre }}" loading="lazy">
                            @else
                                <div class="image-placeholder">
                                    <i class="fa-regular fa-image"></i>
                                </div>
                            @endif
                            
                            @if($bien->est_vedette)
                                <span class="badge-vedette">
                                    <i class="fa-solid fa-star"></i> Vedette
                                </span>
                            @endif
                            
                            <span class="badge-status {{ $bien->statut ? 'disponible' : 'indisponible' }}">
                                {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                            </span>
                            <span class="badge-type">{{ is_object($bien->type_bien) && method_exists($bien->type_bien, 'label') ? $bien->type_bien->label() : $bien->type_bien }}</span>
                        </div>
                        <div class="bien-body">
                            <h3 class="bien-title">{{ $bien->titre }}</h3>
                            <div class="bien-price">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</div>
                            <div class="bien-location">
                                <i class="fa-solid fa-location-dot"></i> {{ $bien->quartier->nom ?? $bien->quartier }}
                            </div>
                            <div class="bien-features">
                                <span class="feature-pill"><i class="fa-regular fa-vector-square"></i> {{ $bien->surface }} m²</span>
                                <span class="feature-pill"><i class="fa-regular fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                                <span class="feature-pill"><i class="fa-regular fa-bath"></i> {{ $bien->nombre_salles_bain }} sdb</span>
                            </div>
                            <div class="bien-agency">
                                <i class="fa-regular fa-building-columns"></i>
                                {{ $bien->agence->nom_agence ?? 'Agence' }}
                            </div>
                            <a href="{{ route('biens.show', $bien) }}" class="btn btn-rust btn-sm btn-block">
                                <i class="fa-regular fa-eye"></i> Voir le bien
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="fa-regular fa-home"></i>
                <p>Aucun bien disponible pour le moment.</p>
            </div>
        @endif
    </div>
</section>

<!-- ==================== COMMENT ÇA MARCHE ==================== -->
<section class="wrap section" id="comment">
    <div class="how-container">
        <div class="text-center">
            <span class="eyebrow">Comment ça marche</span>
            <h2 class="h-section">De la recherche à la visite, en 3 étapes</h2>
        </div>
        <div class="how-grid">
            <div class="how-card">
                <div class="how-num">1</div>
                <h3>Publiez votre besoin</h3>
                <p>Type de bien, budget, zone recherchée — décrivez ce que vous cherchez en quelques minutes.</p>
            </div>
            <div class="how-card">
                <div class="how-num">2</div>
                <h3>Recevez des propositions</h3>
                <p>Les agences inscrites vous envoient des offres correspondant à vos critères. Vous comparez librement.</p>
            </div>
            <div class="how-card">
                <div class="how-num">3</div>
                <h3>Visitez et choisissez</h3>
                <p>Fixez un rendez-vous de visite avec l'agence de votre choix, puis notez votre expérience.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== POURQUOI DOYAIMMO ==================== -->
<section class="wrap section" style="padding-top:0;">
    <div class="value-container">
        <div class="text-center">
            <span class="eyebrow">Pourquoi DoyaImmo</span>
            <h2 class="h-section" style="margin-bottom:10px;">Un marché plus simple, pour tout le monde</h2>
        </div>
        <div class="value-grid">
            <div class="value-card">
                <div class="value-ic" style="background:var(--teal-soft); color:var(--teal);">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h3>Gain de temps</h3>
                <p>Une seule demande envoyée à plusieurs agences à la fois, plus besoin de démarcher un par un.</p>
            </div>
            <div class="value-card">
                <div class="value-ic" style="background:var(--gold-soft); color:#8A6414;">
                    <i class="fa-solid fa-scale-balanced"></i>
                </div>
                <h3>Comparaison simple</h3>
                <p>Recevez plusieurs devis et comparez-les au même endroit avant de vous décider.</p>
            </div>
            <div class="value-card">
                <div class="value-ic" style="background:var(--green-soft); color:#1E7A47;">
                    <i class="fa-solid fa-star"></i>
                </div>
                <h3>Agences notées</h3>
                <p>Chaque agence est évaluée par les clients précédents, pour plus de confiance.</p>
            </div>
            <div class="value-card">
                <div class="value-ic" style="background:var(--rust-soft); color:var(--rust);">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <h3>Ancré à Dakar</h3>
                <p>Pensé pour le marché immobilier local, quartier par quartier.</p>
            </div>
        </div>
    </div>
</section>

<!-- ==================== CTA ==================== -->
<section class="wrap section" style="padding-top:0;">
    <div class="cta-container">
        <div class="cta-band">
            <h2>Prêt à trouver votre prochain logement ?</h2>
            <p>Publiez votre recherche gratuitement et recevez vos premières propositions sous 48h.</p>
            <div class="cta-actions">
                <a href="{{ route('register.particulier') }}" class="btn btn-rust btn-lg">
                    <i class="fa-solid fa-user-plus"></i> Créer mon compte client
                </a>
                <a href="{{ route('register.agence') }}" class="btn btn-ghost btn-lg" style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.2); color:#fff;">
                    <i class="fa-solid fa-building"></i> Je suis une agence
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
/* ==================== CONTENEURS CENTRÉS ==================== */
.hero-container,
.banner-container,
.quartiers-container,
.biens-container,
.how-container,
.value-container,
.cta-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    width: 100%;
}

@media (min-width: 768px) {
    .hero-container,
    .banner-container,
    .quartiers-container,
    .biens-container,
    .how-container,
    .value-container,
    .cta-container {
        padding: 0 32px;
    }
}

@media (min-width: 1200px) {
    .hero-container,
    .banner-container,
    .quartiers-container,
    .biens-container,
    .how-container,
    .value-container,
    .cta-container {
        padding: 0 40px;
    }
}

/* ==================== BANNIÈRES ==================== */
.banner-section {
    padding: 20px 0 0;
}

.banner-item {
    position: relative;
    height: 380px;
    overflow: hidden;
    border-radius: 16px;
    background: #1A1D26;
}

.banner-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.6;
}

.banner-content {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    padding: 20px 40px;
    color: #fff;
    max-width: 600px;
    text-shadow: 0 2px 10px rgba(0,0,0,0.3);
    width: 100%;
}

.banner-content.gauche {
    left: 10%;
    text-align: left;
}

.banner-content.droite {
    right: 10%;
    left: auto;
    text-align: right;
}

.banner-content.centre {
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.banner-content h2 {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 8px;
}

.banner-content p {
    font-size: 17px;
    opacity: 0.9;
    margin-bottom: 16px;
}

.carousel-control-prev,
.carousel-control-next {
    width: 44px;
    height: 44px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0,0,0,0.4);
    border-radius: 50%;
    opacity: 0.7;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    opacity: 1;
}

.carousel-control-prev {
    left: 16px;
}

.carousel-control-next {
    right: 16px;
}

/* ==================== WRAP ==================== */
.wrap {
    width: 100%;
    box-sizing: border-box;
    padding: 0;
}

.section {
    padding: 40px 0;
}

.vedette-section {
    padding-top: 10px;
}

/* ==================== HERO ==================== */
.hero {
    padding: 30px 0 20px;
    background: #F7F9FC;
}

.hero-grid {
    display: grid;
    grid-template-columns: 1fr 0.9fr;
    gap: 48px;
    align-items: center;
}

.hero-content {
    min-width: 0;
}

.hero-content .lead {
    margin: 16px 0 28px;
    max-width: 500px;
    font-size: 16px;
    color: var(--text-soft);
    line-height: 1.7;
}

.hero-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-bottom: 32px;
}

.hero-stats {
    display: flex;
    gap: 32px;
    flex-wrap: wrap;
    padding-top: 4px;
}

.hero-stats .stat-item {
    display: flex;
    flex-direction: column;
}

.hero-stats .stat-number {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    color: var(--ink);
}

.hero-stats .stat-label {
    font-size: 13px;
    color: var(--muted);
}

/* ==================== HERO ILLUSTRATION ==================== */
.hero-illustration {
    min-width: 0;
    width: 100%;
    background: var(--ink);
    border-radius: 16px;
    padding: 20px;
    color: #fff;
    position: relative;
    overflow: hidden;
}

.hero-illustration::before {
    content: "";
    position: absolute;
    left: -80px;
    bottom: -80px;
    width: 250px;
    height: 250px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(14, 122, 122, 0.25), transparent 70%);
    pointer-events: none;
}

.mini-card {
    background: rgba(255, 255, 255, 0.06);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 8px;
    position: relative;
    z-index: 2;
    transition: background 0.2s;
}

.mini-card:hover {
    background: rgba(255, 255, 255, 0.10);
}

.mini-card:last-child {
    margin-bottom: 0;
}

.mini-card .t {
    font-family: var(--display);
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 2px;
    color: #fff;
}

.mini-card .s {
    font-size: 12px;
    color: #9AA1AB;
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
}

.mini-card .s i {
    font-size: 11px;
    color: #6A7280;
}

/* ==================== SECTION HEADER ==================== */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}

.text-center {
    text-align: center;
}

/* ==================== ZONE ROW ==================== */
.zone-row {
    display: flex;
    gap: 12px;
    overflow-x: auto;
    padding: 4px 0 8px;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
    scroll-snap-type: x proximity;
}

.zone-row::-webkit-scrollbar {
    height: 3px;
}

.zone-row::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 10px;
}

.zone-chip {
    flex-shrink: 0;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 14px 20px;
    text-align: center;
    min-width: 110px;
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
    cursor: default;
    scroll-snap-align: start;
}

.zone-chip b {
    display: block;
    font-family: var(--display);
    font-size: 14px;
    margin-bottom: 2px;
}

.zone-chip span {
    font-size: 11px;
    color: var(--muted);
}

/* ============================================
   VEDETTE - SCROLL HORIZONTAL
============================================ */
.vedette-scroll-wrapper {
    overflow-x: auto;
    overflow-y: hidden;
    padding: 8px 0 12px;
    margin: 0 -4px;
    scrollbar-width: thin;
    -webkit-overflow-scrolling: touch;
    scroll-snap-type: x proximity;
}

.vedette-scroll-wrapper::-webkit-scrollbar {
    height: 4px;
}

.vedette-scroll-wrapper::-webkit-scrollbar-thumb {
    background: var(--border);
    border-radius: 10px;
}

.vedette-scroll {
    display: flex;
    gap: 16px;
    padding: 0 4px;
    min-width: max-content;
}

.vedette-card {
    flex: 0 0 280px;
    background: #fff;
    border-radius: var(--radius);
    border: 2px solid #F5A623;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
    scroll-snap-align: start;
}

.vedette-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 30px rgba(245, 166, 35, 0.15);
}

.vedette-image {
    position: relative;
    height: 160px;
    background: #F0F2F5;
    overflow: hidden;
    flex-shrink: 0;
}

.vedette-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.vedette-body {
    padding: 12px 14px 14px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.vedette-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
    color: var(--ink);
    word-break: break-word;
    line-height: 1.3;
}

.vedette-price {
    font-weight: 700;
    color: var(--rust);
    font-size: 15px;
    margin-bottom: 2px;
}

.vedette-location {
    font-size: 11px;
    color: var(--muted);
    margin-bottom: 6px;
    word-break: break-word;
}

.vedette-location i {
    margin-right: 3px;
    font-size: 10px;
}

.vedette-features {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 8px;
    font-size: 10px;
    color: var(--text-soft);
}

.vedette-features span {
    display: flex;
    align-items: center;
    gap: 3px;
    background: var(--border);
    padding: 1px 8px;
    border-radius: 999px;
}

.vedette-features i {
    font-size: 9px;
}

.vedette-body .btn {
    margin-top: auto;
    font-size: 11px;
    padding: 6px 12px;
}

/* ==================== BIENS GRID ==================== */
.biens-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
}

.bien-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    display: flex;
    flex-direction: column;
}

.bien-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
}

.bien-card.vedette-card {
    border-color: #F5A623;
    border-width: 2px;
}

.bien-image {
    position: relative;
    height: 160px;
    background: #F0F2F5;
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
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    font-size: 32px;
    opacity: 0.3;
}

.badge-status {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 600;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.badge-status.disponible {
    background: #1E7A47;
}

.badge-status.indisponible {
    background: var(--muted);
}

.badge-vedette {
    position: absolute;
    top: 8px;
    left: 8px;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 600;
    color: #fff;
    background: #F5A623;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    display: flex;
    align-items: center;
    gap: 4px;
    z-index: 2;
}

.badge-vedette i {
    font-size: 8px;
}

.badge-type {
    position: absolute;
    bottom: 8px;
    left: 8px;
    padding: 2px 10px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 600;
    color: #fff;
    background: rgba(0,0,0,0.6);
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.bien-body {
    padding: 12px 14px 14px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.bien-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
    color: var(--ink);
    word-break: break-word;
    line-height: 1.3;
}

.bien-price {
    font-weight: 700;
    color: var(--rust);
    font-size: 15px;
    margin-bottom: 2px;
}

.bien-location {
    font-size: 11px;
    color: var(--muted);
    margin-bottom: 6px;
    word-break: break-word;
}

.bien-location i {
    margin-right: 3px;
    font-size: 10px;
}

.bien-features {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    margin-bottom: 6px;
}

.feature-pill {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    background: var(--border);
    padding: 1px 8px;
    border-radius: 999px;
    font-size: 9px;
    color: var(--text-soft);
    white-space: nowrap;
}

.feature-pill i {
    font-size: 8px;
}

.bien-agency {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 10px;
    color: var(--muted);
    margin-bottom: 8px;
    word-break: break-word;
}

.bien-agency i {
    font-size: 11px;
}

.bien-body .btn {
    margin-top: auto;
    font-size: 11px;
    padding: 6px 12px;
}

/* ==================== HOW GRID ==================== */
.how-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 32px;
}

.how-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 20px;
}

.how-num {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: var(--rust-soft);
    color: var(--rust);
    font-family: var(--display);
    font-weight: 800;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    flex-shrink: 0;
}

.how-card h3 {
    font-family: var(--display);
    font-size: 15px;
    margin-bottom: 6px;
}

.how-card p {
    font-size: 13px;
    color: var(--text-soft);
    line-height: 1.6;
}

/* ==================== VALUE GRID ==================== */
.value-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-top: 24px;
}

.value-card {
    padding: 16px 0;
}

.value-ic {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
    font-size: 16px;
    flex-shrink: 0;
}

.value-card h3 {
    font-family: var(--display);
    font-size: 14px;
    margin-bottom: 4px;
}

.value-card p {
    font-size: 12px;
    color: var(--text-soft);
    line-height: 1.6;
}

/* ==================== CTA BAND ==================== */
.cta-band {
    background: var(--ink);
    border-radius: 16px;
    padding: 36px 28px;
    text-align: center;
    color: #fff;
}

.cta-band h2 {
    font-family: var(--display);
    font-weight: 700;
    font-size: 24px;
    margin-bottom: 8px;
}

.cta-band p {
    font-size: 13px;
    color: #B9BFCC;
    max-width: 480px;
    margin: 0 auto 20px;
}

.cta-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

.cta-actions .btn {
    flex: 1 1 auto;
    min-width: 160px;
    justify-content: center;
}

/* ==================== BUTTONS ==================== */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    white-space: nowrap;
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
    font-size: 11px;
}

.btn-lg {
    padding: 10px 20px;
    font-size: 13px;
}

.btn-block {
    width: 100%;
    justify-content: center;
}

/* ==================== EMPTY STATE ==================== */
.empty-state {
    text-align: center;
    padding: 28px;
    background: #fff;
    border-radius: 12px;
    border: 1px solid var(--border);
    color: var(--muted);
}

.empty-state i {
    font-size: 28px;
    display: block;
    margin-bottom: 8px;
    opacity: 0.3;
}

.empty-state p {
    font-size: 12px;
}

/* ==================== TYPOGRAPHY ==================== */
.eyebrow {
    display: inline-block;
    font-size: 11px;
    font-weight: 600;
    color: var(--rust);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 8px;
}

.h-hero {
    font-family: var(--display);
    font-weight: 800;
    font-size: clamp(28px, 4.5vw, 44px);
    line-height: 1.15;
}

.h-section {
    font-family: var(--display);
    font-weight: 700;
    font-size: clamp(22px, 3vw, 28px);
    line-height: 1.2;
}

.lead {
    font-size: clamp(14px, 1.1vw, 16px);
    color: var(--text-soft);
    line-height: 1.7;
}

/* ==================== RESPONSIVE ==================== */

@media (max-width: 992px) {
    .hero-grid {
        gap: 32px;
    }
    .banner-item {
        height: 300px;
    }
    .banner-content h2 {
        font-size: 28px;
    }
    .banner-content p {
        font-size: 15px;
    }
}

@media (max-width: 900px) {
    .hero-grid {
        grid-template-columns: 1fr;
        gap: 28px;
    }
    .hero-illustration {
        order: 2;
    }
    .hero-content {
        order: 1;
    }
    .hero-content .lead {
        max-width: 100%;
    }
    .hero-stats {
        gap: 20px;
    }
    .how-grid {
        grid-template-columns: 1fr 1fr;
    }
    .value-grid {
        grid-template-columns: 1fr 1fr;
    }
    .biens-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
    .banner-item {
        height: 260px;
    }
    .banner-content {
        padding: 16px 24px;
    }
    .banner-content h2 {
        font-size: 22px;
    }
    .banner-content p {
        font-size: 13px;
    }
    .carousel-control-prev,
    .carousel-control-next {
        display: none !important;
    }
}

@media (max-width: 640px) {
    .hero {
        padding: 16px 0 10px;
    }
    .hero-grid {
        gap: 20px;
    }
    .hero-illustration {
        padding: 14px;
        border-radius: 10px;
    }
    .mini-card {
        padding: 8px 12px;
        margin-bottom: 5px;
    }
    .mini-card .t {
        font-size: 12px;
    }
    .mini-card .s {
        font-size: 10px;
    }
    .hero-stats {
        gap: 12px;
        margin-top: 0;
    }
    .hero-stats .stat-number {
        font-size: 16px;
    }
    .hero-stats .stat-label {
        font-size: 10px;
    }
    .hero-actions {
        flex-direction: column;
        align-items: stretch;
    }
    .hero-actions .btn {
        justify-content: center;
        width: 100%;
    }
    .section {
        padding: 24px 0;
    }
    .section-header {
        flex-direction: column;
        align-items: flex-start;
    }
    .how-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }
    .value-grid {
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .biens-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    .cta-band {
        padding: 24px 16px;
    }
    .cta-band h2 {
        font-size: 20px;
    }
    .cta-band .cta-actions {
        flex-direction: column;
        align-items: stretch;
    }
    .cta-band .cta-actions .btn {
        justify-content: center;
        min-width: unset;
    }
    .zone-chip {
        padding: 10px 14px;
        min-width: 90px;
    }
    .zone-chip b {
        font-size: 12px;
    }
    .zone-chip span {
        font-size: 9px;
    }
    .h-hero {
        font-size: 24px;
    }
    .btn-lg {
        padding: 10px 16px;
        font-size: 12px;
    }
    .hero-container,
    .banner-container,
    .quartiers-container,
    .biens-container,
    .how-container,
    .value-container,
    .cta-container {
        padding: 0 12px;
    }
    .banner-section {
        padding: 8px 0 0;
    }
    .banner-item {
        height: 180px;
        border-radius: 10px;
    }
    .banner-content {
        padding: 12px 16px;
    }
    .banner-content h2 {
        font-size: 17px;
    }
    .banner-content p {
        font-size: 12px;
        margin-bottom: 8px;
    }
    .banner-content .btn {
        padding: 4px 12px;
        font-size: 11px;
    }

    .vedette-card {
        flex: 0 0 230px;
    }
    .vedette-image {
        height: 140px;
    }
    .vedette-body {
        padding: 10px 12px 12px;
    }
    .vedette-title {
        font-size: 13px;
    }
    .vedette-price {
        font-size: 14px;
    }
    .vedette-features {
        font-size: 9px;
    }
}

@media (max-width: 480px) {
    .hero-container,
    .banner-container,
    .quartiers-container,
    .biens-container,
    .how-container,
    .value-container,
    .cta-container {
        padding: 0 8px;
    }
    .hero-stats {
        gap: 8px;
        flex-wrap: wrap;
    }
    .hero-stats .stat-item {
        flex: 1;
        min-width: 60px;
    }
    .h-hero {
        font-size: 20px;
    }
    .bien-image {
        height: 140px;
    }
    .badge-status,
    .badge-vedette,
    .badge-type {
        font-size: 8px;
        padding: 1px 8px;
    }
    .bien-title {
        font-size: 13px;
    }
    .bien-price {
        font-size: 14px;
    }
    .value-grid {
        grid-template-columns: 1fr;
    }
    .cta-band h2 {
        font-size: 18px;
    }
    .btn {
        font-size: 11px;
        padding: 6px 12px;
    }
    .btn-lg {
        padding: 8px 14px;
        font-size: 11px;
    }
    .banner-item {
        height: 150px;
    }
    .banner-content h2 {
        font-size: 15px;
    }
    .banner-content p {
        font-size: 11px;
    }

    .vedette-card {
        flex: 0 0 200px;
    }
    .vedette-image {
        height: 120px;
    }
    .vedette-body {
        padding: 8px 10px 10px;
    }
    .vedette-title {
        font-size: 12px;
    }
    .vedette-price {
        font-size: 13px;
    }
    .vedette-features {
        font-size: 8px;
        gap: 2px;
    }
    .vedette-features span {
        padding: 1px 6px;
    }
    .vedette-body .btn {
        font-size: 10px;
        padding: 4px 10px;
    }
}

@media (max-width: 360px) {
    .h-hero {
        font-size: 18px;
    }
    .zone-chip {
        min-width: 70px;
        padding: 8px 10px;
    }
    .hero-stats .stat-number {
        font-size: 14px;
    }
    .hero-stats .stat-label {
        font-size: 9px;
    }
    .mini-card .t {
        font-size: 11px;
    }
    .mini-card .s {
        font-size: 9px;
    }
    .banner-item {
        height: 130px;
    }
    .banner-content h2 {
        font-size: 13px;
    }
    .banner-content p {
        font-size: 10px;
    }

    .vedette-card {
        flex: 0 0 170px;
    }
    .vedette-image {
        height: 100px;
    }
}

@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush