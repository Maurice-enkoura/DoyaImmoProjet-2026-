@extends('layouts.app')

@section('title', 'DoyaImmo — Immobilier à Dakar')
@section('meta_description', 'Trouvez un logement à Dakar. Publiez votre besoin, recevez des propositions d\'agences et choisissez en toute transparence.')
@section('canonical', 'https://doyaimmo.com')
@section('og_title', 'DoyaImmo — Immobilier à Dakar')
@section('og_description', 'Trouvez un logement à Dakar. Publiez votre besoin, recevez des propositions d\'agences.')
@section('robots', 'index, follow')

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     1. BANNIÈRE
═══════════════════════════════════════════════════════════ --}}
@if(isset($bannieres) && $bannieres->count() > 0)
<section class="banner-section">
    <div class="banner-wrapper" id="bannerCarouselWrapper">
        <div class="banner-carousel" id="bannerCarousel">
            @foreach($bannieres as $index => $banniere)
                <div class="banner-slide {{ $index === 0 ? 'active' : '' }}">
                    <div class="banner-item">
                        @if($banniere->image)
                            <img src="{{ asset('storage/' . $banniere->image) }}" alt="{{ $banniere->titre }}" loading="lazy">
                        @endif
                        <div class="banner-overlay"></div>

                        <div class="banner-content banner-content--{{ $banniere->position_texte ?? 'gauche' }}">
                            <h2>{{ $banniere->titre }}</h2>
                            @if($banniere->sous_titre)
                                <p>{{ $banniere->sous_titre }}</p>
                            @endif
                            @if($banniere->lien)
                                <a href="{{ $banniere->lien }}" class="btn btn-rust btn-sm">
                                    Découvrir <i class="fa-solid fa-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($bannieres->count() > 1)
            <button type="button" class="banner-nav banner-nav--prev" onclick="moveBannerCarousel(-1)" aria-label="Précédent">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="banner-nav banner-nav--next" onclick="moveBannerCarousel(1)" aria-label="Suivant">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <div class="banner-dots" id="bannerDots">
                @foreach($bannieres as $index => $banniere)
                    <button type="button"
                            class="banner-dot {{ $index === 0 ? 'active' : '' }}"
                            onclick="goToBannerSlide({{ $index }})"
                            aria-label="Bannière {{ $index + 1 }}"></button>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════
     2. HERO
═══════════════════════════════════════════════════════════ --}}
<section class="hero">
    <div class="hero__content">
        <h1 class="hero__title">
            Trouvez un logement <br>
            <span class="hero__title-accent">sans passer par 10 agences.</span>
        </h1>

        <p class="hero__sub">
            Publiez votre recherche, recevez des propositions des agences de Dakar, choisissez en toute transparence.
        </p>

        <div class="hero__actions">
            <a href="{{ route('register') }}" class="btn btn-rust btn-lg">
                <i class="fa-solid fa-pen-to-square"></i> Publier ma recherche
            </a>
            <a href="{{ route('besoins.index') }}" class="btn btn-ghost btn-lg">
                <i class="fa-solid fa-eye"></i> Voir les besoins
            </a>
        </div>

        <div class="hero__stats">
            <div class="hero__stat">
                <strong>{{ $stats['besoins'] ?? 0 }}</strong>
                <span>besoins actifs</span>
            </div>
            <span class="hero__stat-sep"></span>
            <div class="hero__stat">
                <strong>{{ $stats['biens'] ?? 0 }}</strong>
                <span>biens disponibles</span>
            </div>
            <span class="hero__stat-sep"></span>
            <div class="hero__stat">
                <strong>{{ $stats['agences'] ?? 0 }}</strong>
                <span>agences</span>
            </div>
        </div>
    </div>

    <aside class="hero__aside">
        <div class="hero__aside-head">
            <span class="hero__aside-dot"></span>
            <span>Besoins en direct</span>
        </div>

        <div class="hero__aside-list">
            @forelse($demandesRecentes ?? [] as $demande)
                <div class="mini-card">
                    <div class="mini-card__title">
                        {{ is_object($demande->type_bien) && method_exists($demande->type_bien, 'label')
                            ? $demande->type_bien->label()
                            : $demande->type_bien }}
                    </div>
                    <div class="mini-card__loc">{{ $demande->zone_recherchee }}</div>
                    <div class="mini-card__meta">
                        <span><i class="fa-regular fa-message"></i> {{ $demande->propositions->count() }} proposition{{ $demande->propositions->count() > 1 ? 's' : '' }}</span>
                        <span class="mini-card__budget">{{ number_format($demande->budget_maximum, 0, ',', ' ') }} F</span>
                    </div>
                </div>
            @empty
                <div class="mini-empty">
                    <i class="fa-regular fa-inbox"></i>
                    <p>Aucune demande pour l'instant</p>
                </div>
            @endforelse
        </div>
    </aside>
</section>

{{-- ═══════════════════════════════════════════════════════════
     3. ⭐ BIENS EN VEDETTE — juste après le hero
═══════════════════════════════════════════════════════════ --}}
@if(isset($biensVedette) && $biensVedette->count() > 0)
<section class="section vedette-section">
    <header class="section-head">
        <div>
            <span class="eyebrow"> À la une</span>
            <h2 class="h-section">Biens en vedette</h2>
        </div>
        <div class="section-head__actions">
            <button type="button" class="carousel-btn" onclick="moveVedetteCarousel(-1)" aria-label="Précédent">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <button type="button" class="carousel-btn" onclick="moveVedetteCarousel(1)" aria-label="Suivant">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
            <a href="{{ route('biens.index') }}" class="btn btn-ghost btn-sm">
                Voir tous
            </a>
        </div>
    </header>

    <div class="vedette-carousel-wrapper">
        <div class="vedette-carousel" id="vedetteCarousel">
            @foreach($biensVedette as $bien)
                @php $image = $bien->medias->where('type_media', 'image')->first(); @endphp
                <article class="card">
                    <div class="card__media">
                        @if($image)
                            <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $bien->titre }}" loading="lazy">
                        @else
                            <div class="card__placeholder">
                                <i class="fa-regular fa-image"></i>
                            </div>
                        @endif
                        <span class="card-badge card-badge--vedette">
                            <i class="fa-solid fa-star"></i> Vedette
                        </span>
                        <span class="card-badge card-badge--status {{ $bien->statut ? 'is-dispo' : 'is-indispo' }}">
                            <i class="fa-solid fa-circle"></i>
                            {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                        </span>
                    </div>

                    <div class="card__body">
                        <h3 class="card__title">{{ $bien->titre }}</h3>
                        <div class="card__price">
                            {{ number_format($bien->prix, 0, ',', ' ') }}
                            <small>FCFA</small>
                        </div>
                        <div class="card__meta">
                            <span><i class="fa-solid fa-location-dot"></i> {{ $bien->quartier->nom ?? $bien->quartier }}</span>
                        </div>
                        <div class="card__specs">
                            <span><i class="fa-regular fa-square"></i> {{ $bien->surface }} m²</span>
                            @if($bien->nombre_chambres)
                                <span><i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                            @endif
                        </div>
                        <a href="{{ route('biens.show', $bien) }}" class="btn btn-rust btn-sm card__cta">
                            Voir le bien <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    @if($biensVedette->count() > 3)
        <div class="carousel-dots" id="vedetteDots">
            @php $totalSlides = ceil($biensVedette->count() / 3); @endphp
            @for($i = 0; $i < $totalSlides; $i++)
                <button type="button" class="dot {{ $i === 0 ? 'active' : '' }}"
                        onclick="goToVedetteSlide({{ $i }})"
                        aria-label="Page {{ $i + 1 }}"></button>
            @endfor
        </div>
    @endif
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════
     4. COMMENT ÇA MARCHE
═══════════════════════════════════════════════════════════ --}}
<section class="section how-section">
    <header class="section-head section-head--center">
        <span class="eyebrow">Comment ça marche</span>
        <h2 class="h-section">Simple et transparent</h2>
    </header>

    <div class="how-grid">
        <div class="how-card">
            <div class="how-card__num">1</div>
            <h3>Publiez votre besoin</h3>
            <p>Type de bien, budget, zone recherchée — en quelques minutes.</p>
        </div>
        <div class="how-card">
            <div class="how-card__num">2</div>
            <h3>Recevez des propositions</h3>
            <p>Les agences inscrites vous envoient des offres adaptées à vos critères.</p>
        </div>
        <div class="how-card">
            <div class="how-card__num">3</div>
            <h3>Visitez et choisissez</h3>
            <p>Prenez rendez-vous avec l'agence de votre choix, puis notez votre expérience.</p>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     5. BANDEAU AGENCES
═══════════════════════════════════════════════════════════ --}}
<section class="section agency-section">
    <div class="agency-band">
        <div class="agency-band__icon">
            <i class="fa-solid fa-briefcase"></i>
        </div>

        <div class="agency-band__content">
            <h3>Vous êtes une agence immobilière ?</h3>
            <p>Recevez les demandes des particuliers et proposez vos biens en priorité.</p>
        </div>

        <a href="{{ route('register.agence') }}" class="btn btn-rust">
            Créer mon compte <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════
     6. DERNIERS BIENS
═══════════════════════════════════════════════════════════ --}}
@if(isset($derniersBiens) && $derniersBiens->count() > 0)
<section class="section dernier-section">
    <header class="section-head">
        <div>
            <span class="eyebrow">Nouveautés</span>
            <h2 class="h-section">Derniers biens disponibles</h2>
        </div>
        <a href="{{ route('biens.index') }}" class="btn btn-ghost btn-sm">
            Voir tous
        </a>
    </header>

    <div class="biens-grid">
        @foreach($derniersBiens as $bien)
            @php $image = $bien->medias->where('type_media', 'image')->first(); @endphp
            <article class="card {{ $bien->est_vedette ? 'is-vedette' : '' }}">
                <div class="card__media">
                    @if($image)
                        <img src="{{ asset('storage/' . $image->fichier) }}" alt="{{ $bien->titre }}" loading="lazy">
                    @else
                        <div class="card__placeholder">
                            <i class="fa-regular fa-image"></i>
                        </div>
                    @endif
                    @if($bien->est_vedette)
                        <span class="card-badge card-badge--vedette">
                            <i class="fa-solid fa-star"></i> Vedette
                        </span>
                    @endif
                    <span class="card-badge card-badge--status {{ $bien->statut ? 'is-dispo' : 'is-indispo' }}">
                        <i class="fa-solid fa-circle"></i>
                        {{ $bien->statut ? 'Disponible' : 'Indisponible' }}
                    </span>
                </div>

                <div class="card__body">
                    <h3 class="card__title">{{ $bien->titre }}</h3>
                    <div class="card__price">
                        {{ number_format($bien->prix, 0, ',', ' ') }}
                        <small>FCFA</small>
                    </div>
                    <div class="card__meta">
                        <span><i class="fa-solid fa-location-dot"></i> {{ $bien->quartier->nom ?? $bien->quartier }}</span>
                        <span><i class="fa-regular fa-building"></i> {{ $bien->agence->nom_agence ?? 'Agence' }}</span>
                    </div>
                    <div class="card__specs">
                        <span><i class="fa-regular fa-square"></i> {{ $bien->surface }} m²</span>
                        @if($bien->nombre_chambres)
                            <span><i class="fa-solid fa-bed"></i> {{ $bien->nombre_chambres }} ch.</span>
                        @endif
                    </div>
                    <a href="{{ route('biens.show', $bien) }}" class="btn btn-rust btn-sm card__cta">
                        Voir le bien <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </article>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════════════
     7. CTA FINAL
═══════════════════════════════════════════════════════════ --}}
<section class="section cta-section">
    <div class="cta-band">
        <h2>Prêt à trouver votre logement ?</h2>
        <p>Publiez votre recherche gratuitement et recevez vos premières propositions sous 48h.</p>

        <div class="cta-band__actions">
            <a href="{{ route('register.particulier') }}" class="btn btn-rust btn-lg">
                <i class="fa-solid fa-user-plus"></i> Créer mon compte
            </a>
            <a href="{{ route('register.agence') }}" class="btn btn-ghost-on-dark btn-lg">
                <i class="fa-solid fa-building"></i> Je suis une agence
            </a>
        </div>
    </div>
</section>

@endsection


@push('scripts')
<script>
/* ═══════════════════════════════════════════════════════════
   BANNER CARROUSEL
═══════════════════════════════════════════════════════════ */
let bannerCurrentSlide = 0;
let bannerTotalSlides = 0;
let bannerAutoPlayInterval = null;
const BANNER_AUTOPLAY_DELAY = 5000;

document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.getElementById('bannerCarousel');
    if (!carousel) return;

    bannerTotalSlides = carousel.querySelectorAll('.banner-slide').length;
    updateBannerDots();
    startBannerAutoplay();

    const wrapper = document.getElementById('bannerCarouselWrapper');
    if (wrapper) {
        wrapper.addEventListener('mouseenter', stopBannerAutoplay);
        wrapper.addEventListener('mouseleave', startBannerAutoplay);
    }
});

function moveBannerCarousel(direction) {
    if (bannerTotalSlides <= 1) return;
    bannerCurrentSlide = (bannerCurrentSlide + direction + bannerTotalSlides) % bannerTotalSlides;
    updateBannerCarousel();
    stopBannerAutoplay();
    startBannerAutoplay();
}

function goToBannerSlide(index) {
    if (index === bannerCurrentSlide || bannerTotalSlides === 0) return;
    bannerCurrentSlide = index;
    updateBannerCarousel();
    stopBannerAutoplay();
    startBannerAutoplay();
}

function updateBannerCarousel() {
    const carousel = document.getElementById('bannerCarousel');
    if (!carousel) return;
    carousel.style.transform = `translateX(-${bannerCurrentSlide * 100}%)`;
    const slides = carousel.querySelectorAll('.banner-slide');
    slides.forEach((slide, i) => slide.classList.toggle('active', i === bannerCurrentSlide));
    updateBannerDots();
}

function updateBannerDots() {
    const dotsContainer = document.getElementById('bannerDots');
    if (!dotsContainer) return;
    const dots = dotsContainer.querySelectorAll('.banner-dot');
    dots.forEach((dot, index) => dot.classList.toggle('active', index === bannerCurrentSlide));
}

function startBannerAutoplay() {
    if (bannerAutoPlayInterval || bannerTotalSlides <= 1) return;
    bannerAutoPlayInterval = setInterval(() => {
        goToBannerSlide((bannerCurrentSlide + 1) % bannerTotalSlides);
    }, BANNER_AUTOPLAY_DELAY);
}

function stopBannerAutoplay() {
    if (bannerAutoPlayInterval) {
        clearInterval(bannerAutoPlayInterval);
        bannerAutoPlayInterval = null;
    }
}

/* ═══════════════════════════════════════════════════════════
   VEDETTE CARROUSEL
═══════════════════════════════════════════════════════════ */
let vedetteCurrentSlide = 0;
let vedetteTotalSlides = 0;
let vedetteAutoPlayInterval = null;
const VEDETTE_AUTOPLAY_DELAY = 4500;

document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.getElementById('vedetteCarousel');
    if (!carousel) return;

    const cards = carousel.querySelectorAll('.card');
    let cardsPerSlide = getVedetteCardsPerSlide();
    vedetteTotalSlides = Math.ceil(cards.length / cardsPerSlide);

    updateVedetteDots();
    startVedetteAutoplay();

    const wrapper = carousel.closest('.vedette-carousel-wrapper');
    if (wrapper) {
        wrapper.addEventListener('mouseenter', stopVedetteAutoplay);
        wrapper.addEventListener('mouseleave', startVedetteAutoplay);
    }

    let resizeTimeout;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            const newCardsPerSlide = getVedetteCardsPerSlide();
            if (newCardsPerSlide !== cardsPerSlide) {
                cardsPerSlide = newCardsPerSlide;
                vedetteTotalSlides = Math.ceil(cards.length / cardsPerSlide);
                vedetteCurrentSlide = 0;
                updateVedetteCarousel();
                updateVedetteDots();
            }
        }, 250);
    });
});

function getVedetteCardsPerSlide() {
    if (window.innerWidth <= 640) return 1;
    if (window.innerWidth <= 992) return 2;
    return 3;
}

function moveVedetteCarousel(direction) {
    if (vedetteTotalSlides <= 1) return;
    vedetteCurrentSlide = (vedetteCurrentSlide + direction + vedetteTotalSlides) % vedetteTotalSlides;
    updateVedetteCarousel();
    updateVedetteDots();
    stopVedetteAutoplay();
    startVedetteAutoplay();
}

function goToVedetteSlide(index) {
    if (index === vedetteCurrentSlide) return;
    vedetteCurrentSlide = index;
    updateVedetteCarousel();
    updateVedetteDots();
    stopVedetteAutoplay();
    startVedetteAutoplay();
}

function updateVedetteCarousel() {
    const carousel = document.getElementById('vedetteCarousel');
    if (!carousel) return;
    const firstCard = carousel.querySelector('.card');
    if (!firstCard) return;
    const cardWidth = firstCard.offsetWidth;
    const gap = 16;
    const offset = vedetteCurrentSlide * (cardWidth + gap) * getVedetteCardsPerSlide();
    carousel.style.transform = `translateX(-${offset}px)`;
}

function updateVedetteDots() {
    const dotsContainer = document.getElementById('vedetteDots');
    if (!dotsContainer) return;
    const dots = dotsContainer.querySelectorAll('.dot');
    dots.forEach((dot, index) => dot.classList.toggle('active', index === vedetteCurrentSlide));
}

function startVedetteAutoplay() {
    if (vedetteAutoPlayInterval || vedetteTotalSlides <= 1) return;
    vedetteAutoPlayInterval = setInterval(() => {
        goToVedetteSlide((vedetteCurrentSlide + 1) % vedetteTotalSlides);
    }, VEDETTE_AUTOPLAY_DELAY);
}

function stopVedetteAutoplay() {
    if (vedetteAutoPlayInterval) {
        clearInterval(vedetteAutoPlayInterval);
        vedetteAutoPlayInterval = null;
    }
}
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   HOME — DoyaImmo
   ═══════════════════════════════════════════════════════════ */

:root {
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --c-gold:       #D4AF37;
}

/* ═══ SECTIONS ═══ */
.section {
    padding: 56px 24px;
    max-width: 1200px;
    margin: 0 auto;
}

.section-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
    margin-bottom: 26px;
}
.section-head--center {
    flex-direction: column;
    text-align: center;
    gap: 6px;
}
.section-head__actions {
    display: flex;
    gap: 8px;
    align-items: center;
    flex-wrap: wrap;
}

.eyebrow {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--rust);
    text-transform: uppercase;
    letter-spacing: .8px;
    margin-bottom: 6px;
}

.h-section {
    font-family: var(--display);
    font-weight: 700;
    font-size: clamp(22px, 2.8vw, 26px);
    line-height: 1.2;
    margin: 0;
    color: var(--ink);
    letter-spacing: -.02em;
}

/* ═══════════════════════════════════════════
   BANNIÈRE
═══════════════════════════════════════════ */
.banner-section {
    padding: 20px 24px 0;
    max-width: 1200px;
    margin: 0 auto;
}

.banner-wrapper {
    position: relative;
    overflow: hidden;
    border-radius: 18px;
    box-shadow: 0 12px 32px rgba(20, 30, 50, .1);
}

.banner-carousel {
    display: flex;
    transition: transform .6s ease-in-out;
}

.banner-slide { flex: 0 0 100%; }

.banner-item {
    position: relative;
    aspect-ratio: 24 / 9;
    background: #EEF1F6;
    overflow: hidden;
}
.banner-item img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    animation: bannerZoom 14s ease-in-out infinite alternate;
}
@keyframes bannerZoom {
    from { transform: scale(1); }
    to   { transform: scale(1.05); }
}

.banner-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(8, 10, 18, .78) 0%, rgba(8, 10, 18, .25) 50%, transparent 100%);
    pointer-events: none;
}

.banner-content {
    position: absolute;
    bottom: 22px;
    left: 26px;
    max-width: 55%;
    color: #fff;
    z-index: 2;
    opacity: 0;
}
.banner-slide.active .banner-content {
    animation: bannerFadeInUp .7s ease .15s forwards;
}
@keyframes bannerFadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}

.banner-content--droite { left: auto; right: 26px; text-align: right; }
.banner-content--centre { left: 50%; right: auto; transform: translateX(-50%); text-align: center; max-width: 70%; }
.banner-slide.active .banner-content--centre {
    animation: bannerFadeInUpCentre .7s ease .15s forwards;
}
@keyframes bannerFadeInUpCentre {
    from { opacity: 0; transform: translate(-50%, 10px); }
    to   { opacity: 1; transform: translate(-50%, 0); }
}

.banner-content h2 {
    font-family: var(--display);
    font-size: clamp(20px, 2.4vw, 28px);
    font-weight: 800;
    margin: 0 0 6px;
    color: #fff;
    letter-spacing: -.02em;
    line-height: 1.2;
}
.banner-content p {
    font-size: clamp(13px, 1vw, 14.5px);
    margin: 0 0 12px;
    opacity: .92;
    line-height: 1.5;
}

.banner-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 40px; height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, .18);
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255, 255, 255, .25);
    color: #fff;
    font-size: 13px;
    cursor: pointer;
    z-index: 3;
    transition: all .2s;
    display: flex; align-items: center; justify-content: center;
}
.banner-nav:hover {
    background: rgba(255, 255, 255, .3);
    transform: translateY(-50%) scale(1.06);
}
.banner-nav--prev { left: 14px; }
.banner-nav--next { right: 14px; }

.banner-dots {
    position: absolute;
    bottom: 16px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 6px;
    z-index: 3;
}
.banner-dot {
    width: 8px; height: 8px;
    padding: 0;
    border: none;
    border-radius: 999px;
    background: rgba(255, 255, 255, .55);
    cursor: pointer;
    transition: all .3s;
}
.banner-dot.active {
    background: #fff;
    width: 22px;
}

/* ═══════════════════════════════════════════
   HERO
═══════════════════════════════════════════ */
.hero {
    padding: 56px 24px 24px;
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1.15fr 1fr;
    gap: 48px;
    align-items: center;
}

.hero__title {
    font-family: var(--display);
    font-size: clamp(28px, 4vw, 42px);
    font-weight: 800;
    line-height: 1.15;
    margin: 0 0 14px;
    color: var(--ink);
    letter-spacing: -.03em;
}
.hero__title-accent { color: var(--rust); }

.hero__sub {
    font-size: clamp(14px, 1.1vw, 16px);
    color: var(--text-soft);
    line-height: 1.65;
    margin: 0 0 24px;
    max-width: 500px;
}

.hero__actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 28px;
}

.hero__stats {
    display: flex;
    align-items: center;
    gap: 22px;
    flex-wrap: wrap;
}
.hero__stat {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.hero__stat strong {
    font-family: var(--display);
    font-size: clamp(20px, 2.2vw, 24px);
    font-weight: 800;
    color: var(--ink);
    line-height: 1;
}
.hero__stat span {
    font-size: 11.5px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: .4px;
    font-weight: 600;
}
.hero__stat-sep {
    width: 1px;
    height: 30px;
    background: var(--border);
    flex-shrink: 0;
}

.hero__aside {
    background: linear-gradient(135deg, var(--ink), #2A2F3D);
    border-radius: 18px;
    padding: 20px 22px;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.hero__aside::before {
    content: '';
    position: absolute;
    top: -40%; right: -20%;
    width: 320px; height: 320px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(181, 80, 42, .25), transparent 65%);
    pointer-events: none;
}

.hero__aside-head {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #B9BFCC;
    margin-bottom: 14px;
    position: relative;
}
.hero__aside-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    background: #4ADE80;
    box-shadow: 0 0 0 4px rgba(74, 222, 128, .2);
    animation: pulseDot 2s ease-in-out infinite;
}
@keyframes pulseDot {
    0%, 100% { transform: scale(1); }
    50%      { transform: scale(1.15); }
}

.hero__aside-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    position: relative;
}

.mini-card {
    background: rgba(255, 255, 255, .05);
    border: 1px solid rgba(255, 255, 255, .08);
    border-radius: 11px;
    padding: 11px 14px;
    transition: background .2s;
}
.mini-card:hover { background: rgba(255, 255, 255, .09); }

.mini-card__title {
    font-family: var(--display);
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 2px;
    color: #fff;
}
.mini-card__loc {
    font-size: 12px;
    color: #9AA1AB;
    margin-bottom: 6px;
}

.mini-card__meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    font-size: 11px;
    color: #9AA1AB;
}
.mini-card__meta i { font-size: 10px; }
.mini-card__budget {
    color: #F5A623;
    font-weight: 700;
    font-family: var(--display);
}

.mini-empty {
    text-align: center;
    padding: 28px 16px;
    color: #8A91A0;
}
.mini-empty i {
    font-size: 26px;
    display: block;
    margin-bottom: 6px;
    opacity: .4;
}
.mini-empty p {
    margin: 0;
    font-size: 12px;
}

/* ═══════════════════════════════════════════
   VEDETTE (juste après le hero)
═══════════════════════════════════════════ */
.vedette-section {
    padding-top: 40px;
    padding-bottom: 24px;
    background: linear-gradient(180deg, transparent 0%, #FFFBEB 100%);
    border-radius: 24px;
}

.vedette-carousel-wrapper {
    overflow: hidden;
    position: relative;
    padding: 4px 4px 12px;
}

.vedette-carousel {
    display: flex;
    gap: 16px;
    transition: transform .5s ease-in-out;
    will-change: transform;
}

.carousel-btn {
    width: 36px; height: 36px;
    border-radius: 50%;
    border: 1px solid var(--border);
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-soft);
    cursor: pointer;
    transition: all .2s;
    font-family: inherit;
}
.carousel-btn:hover {
    border-color: var(--rust);
    color: var(--rust);
    background: rgba(181, 80, 42, .06);
}

.carousel-dots {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-top: 14px;
}
.carousel-dots .dot {
    width: 8px; height: 8px;
    padding: 0;
    border: none;
    border-radius: 999px;
    background: var(--border);
    cursor: pointer;
    transition: all .3s;
}
.carousel-dots .dot.active {
    background: var(--rust);
    width: 22px;
}

/* ═══════════════════════════════════════════
   CARD (unifiée)
═══════════════════════════════════════════ */
.card {
    flex: 0 0 calc((100% - 32px) / 3);
    min-width: 0;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .2s, box-shadow .2s, border-color .2s;
}
.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 26px rgba(0, 0, 0, .07);
    border-color: #d8d8d8;
}
.card.is-vedette {
    border-color: var(--c-gold);
}

.card__media {
    position: relative;
    aspect-ratio: 4 / 3;
    background: #F0F2F5;
    overflow: hidden;
}
.card__media img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
    transition: transform .3s;
}
.card:hover .card__media img { transform: scale(1.05); }

.card__placeholder {
    width: 100%; height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    font-size: 32px;
    opacity: .3;
}

.card-badge {
    position: absolute;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 9px;
    border-radius: 999px;
    font-size: 9.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
    white-space: nowrap;
    z-index: 2;
    backdrop-filter: blur(6px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, .15);
}
.card-badge i { font-size: 8px; }

.card-badge--vedette {
    top: 10px; left: 10px;
    background: linear-gradient(135deg, var(--c-gold), #E8901A);
    color: #fff;
}
.card-badge--status {
    top: 10px; right: 10px;
    background: rgba(255, 255, 255, .95);
}
.card-badge--status i { font-size: 5px; }
.card-badge--status.is-dispo   { color: var(--c-success); }
.card-badge--status.is-indispo { color: var(--muted); }

.card__body {
    padding: 14px 16px 16px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.card__title {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    margin: 0;
    color: var(--ink);
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card__price {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 800;
    color: var(--rust);
    line-height: 1.1;
}
.card__price small {
    font-size: 11px;
    font-weight: 700;
    opacity: .75;
    margin-left: 3px;
}

.card__meta {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 14px;
    font-size: 11.5px;
    color: var(--muted);
}
.card__meta span {
    display: inline-flex;
    align-items: center;
    gap: 5px;
}
.card__meta i {
    color: var(--rust);
    font-size: 10px;
    opacity: .8;
}

.card__specs {
    display: flex;
    flex-wrap: wrap;
    gap: 4px 12px;
    font-size: 11.5px;
    color: var(--text-soft);
    padding-top: 8px;
    border-top: 1px dashed var(--border);
}
.card__specs span {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.card__specs i {
    color: var(--rust);
    font-size: 10px;
    opacity: .8;
}

.card__cta { margin-top: auto; }

.biens-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 16px;
}
.biens-grid .card {
    flex: none;
    max-width: none;
}

/* ═══════════════════════════════════════════
   HOW
═══════════════════════════════════════════ */
.how-section { padding-top: 32px; }

.how-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-top: 32px;
}

.how-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 22px;
    text-align: center;
    transition: transform .2s, box-shadow .2s, border-color .2s;
}
.how-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(0, 0, 0, .05);
    border-color: #d8d8d8;
}

.how-card__num {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px; height: 38px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    font-family: var(--display);
    font-weight: 800;
    font-size: 16px;
    margin-bottom: 14px;
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
}

.how-card h3 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--ink);
}
.how-card p {
    font-size: 13px;
    color: var(--text-soft);
    line-height: 1.55;
    margin: 0;
}

/* ═══════════════════════════════════════════
   AGENCY BAND
═══════════════════════════════════════════ */
.agency-section { padding-top: 24px; padding-bottom: 24px; }

.agency-band {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 22px 26px;
    background: linear-gradient(135deg, #F0F7FF 0%, #FFFBF7 100%);
    border: 1px solid #BBDEFB;
    border-radius: 18px;
    flex-wrap: wrap;
}

.agency-band__icon {
    width: 52px; height: 52px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(181, 80, 42, .25);
}

.agency-band__content {
    flex: 1;
    min-width: 240px;
}
.agency-band__content h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--ink);
}
.agency-band__content p {
    font-size: 13.5px;
    color: var(--text-soft);
    margin: 0;
    line-height: 1.5;
}

/* ═══════════════════════════════════════════
   CTA FINAL
═══════════════════════════════════════════ */
.cta-section { padding-top: 24px; padding-bottom: 64px; }

.cta-band {
    background: linear-gradient(135deg, var(--ink) 0%, #2A2F3D 100%);
    border-radius: 20px;
    padding: 44px 32px;
    text-align: center;
    color: #fff;
    position: relative;
    overflow: hidden;
}
.cta-band::before {
    content: '';
    position: absolute;
    top: -50%; right: -15%;
    width: 400px; height: 400px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(181, 80, 42, .3), transparent 65%);
    pointer-events: none;
}

.cta-band h2 {
    font-family: var(--display);
    font-size: clamp(22px, 2.6vw, 28px);
    font-weight: 800;
    margin: 0 0 8px;
    color: #fff;
    letter-spacing: -.02em;
    position: relative;
}
.cta-band p {
    font-size: clamp(13px, 1vw, 14.5px);
    color: #B9BFCC;
    max-width: 480px;
    margin: 0 auto 22px;
    line-height: 1.6;
    position: relative;
}
.cta-band__actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
    position: relative;
}
.btn-ghost-on-dark {
    background: rgba(255, 255, 255, .1);
    color: #fff;
    border-color: rgba(255, 255, 255, .25);
}
.btn-ghost-on-dark:hover {
    background: rgba(255, 255, 255, .18);
    color: #fff;
    border-color: rgba(255, 255, 255, .4);
}

/* ═══════════════════════════════════════════
   BOUTONS
═══════════════════════════════════════════ */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 18px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: 1px solid transparent;
    cursor: pointer;
    font-family: inherit;
    transition: all .2s ease;
    white-space: nowrap;
}
.btn-rust {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
}
.btn-rust:hover {
    background: #9A4523;
    color: #fff;
    border-color: #9A4523;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(181, 80, 42, .25);
}
.btn-ghost {
    background: transparent;
    color: var(--text-soft);
    border-color: var(--border);
}
.btn-ghost:hover {
    background: var(--surface);
    border-color: var(--rust);
    color: var(--rust);
}
.btn-sm { padding: 7px 13px; font-size: 12px; }
.btn-lg { padding: 13px 22px; font-size: 14px; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 1024px) {
    .hero { gap: 32px; }
}

@media (max-width: 900px) {
    .hero {
        grid-template-columns: 1fr;
        gap: 26px;
    }
    .hero__aside { order: 2; }
    .hero__content { order: 1; }

    .banner-item { aspect-ratio: 16 / 9; }
    .banner-content { max-width: 75%; }

    .how-grid { grid-template-columns: 1fr 1fr; }
    .how-grid .how-card:last-child { grid-column: 1 / -1; }

    .agency-band { padding: 20px; gap: 16px; }
}

@media (max-width: 768px) {
    .section { padding: 40px 18px; }
    .banner-section { padding: 16px 18px 0; }
    .hero { padding: 40px 18px 20px; }

    .hero__title { font-size: 26px; }
    .hero__sub { font-size: 14px; }
    .hero__actions { flex-direction: column; }
    .hero__actions .btn { width: 100%; }

    .how-grid { grid-template-columns: 1fr; }

    .card { flex: 0 0 calc((100% - 16px) / 2); }
    .biens-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }

    .agency-band {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
        padding: 22px 20px;
    }
    .agency-band__icon { margin: 0 auto; }
    .agency-band .btn { width: 100%; }

    .cta-band { padding: 32px 20px; }
    .cta-band__actions { flex-direction: column; }
    .cta-band__actions .btn { width: 100%; }

    .banner-nav { width: 34px; height: 34px; font-size: 12px; }
    .banner-nav--prev { left: 10px; }
    .banner-nav--next { right: 10px; }
}

@media (max-width: 480px) {
    .section { padding: 32px 14px; }
    .banner-section { padding: 14px 14px 0; }
    .hero { padding: 32px 14px 16px; }

    .hero__title { font-size: 22px; }

    .banner-item { aspect-ratio: 3 / 2; }
    .banner-content { max-width: 88%; bottom: 14px; left: 14px; }
    .banner-content h2 { font-size: 16px; }
    .banner-content p { font-size: 11.5px; margin-bottom: 10px; }
    .banner-dots { bottom: 12px; }

    .card { flex: 0 0 100%; }
    .biens-grid { grid-template-columns: 1fr; gap: 12px; }

    .cta-band { padding: 28px 18px; }

    .hero__aside { padding: 16px 18px; }
    .mini-card { padding: 10px 12px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: .01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: .01ms !important;
    }
    .banner-item img { animation: none !important; }
    .hero__aside-dot { animation: none !important; }
}
</style>
@endpush