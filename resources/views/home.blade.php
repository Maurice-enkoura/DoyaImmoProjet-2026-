@extends('layouts.app')

@section('title', 'DoyaImmo — Trouvez votre logement à Dakar, simplement')

@section('content')
<!-- Hero Section -->
<section class="wrap hero">
    <div class="hero-grid">
        <div>
            <span class="eyebrow">● Nouveau à Dakar</span>
            <h1 class="h-hero">Trouvez un logement <span style="color:var(--rust)">sans passer par 10 agences.</span></h1>
            <p class="lead" style="margin:22px 0 32px; max-width:480px;">
                Décrivez le logement que vous cherchez, les agences immobilières inscrites vous envoient leurs propositions. Comparez, visitez, choisissez.
            </p>
            <div class="cta-actions" style="justify-content:flex-start;">
                <a href="{{ route('register') }}" class="btn btn-rust btn-lg">Publier ma recherche →</a>
                <a href="{{ route('besoins.index') }}" class="btn btn-ghost btn-lg">Parcourir les besoins</a>
            </div>
        </div>
        <div class="hero-illustration">
            @forelse($demandesRecentes as $demande)
                <div class="mini-card">
                    <div class="t">{{ $demande->type_bien->label() }} — {{ $demande->zone_recherchee }}</div>
                    <div class="s">{{ $demande->propositions->count() }} propositions reçues · {{ number_format($demande->budget_maximum, 0, ',', ' ') }} F/mois</div>
                </div>
            @empty
                <div class="mini-card">
                    <div class="t">Appartement F4 — Almadies</div>
                    <div class="s">3 propositions reçues · 450 000 F/mois</div>
                </div>
                <div class="mini-card">
                    <div class="t">Studio meublé — Mermoz</div>
                    <div class="s">5 propositions reçues · 120 000 F/mois</div>
                </div>
                <div class="mini-card" style="margin-bottom:0;">
                    <div class="t">Villa — Ngor</div>
                    <div class="s">Visite confirmée pour le 08 juillet</div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Zones populaires -->
<section class="wrap" style="padding-bottom:10px;">
    <div class="zone-row">
        @forelse($quartiersPopulaires as $quartier)
            <div class="zone-chip">
                <b>{{ $quartier->nom }}</b>
                <span>{{ $quartier->demandes_count ?? 0 }} besoins actifs</span>
            </div>
        @empty
            <div class="zone-chip"><b>Almadies</b><span>24 besoins actifs</span></div>
            <div class="zone-chip"><b>Mermoz</b><span>18 besoins actifs</span></div>
            <div class="zone-chip"><b>Sacré-Cœur</b><span>15 besoins actifs</span></div>
            <div class="zone-chip"><b>Plateau</b><span>11 besoins actifs</span></div>
            <div class="zone-chip"><b>Ngor</b><span>9 besoins actifs</span></div>
        @endforelse
    </div>
</section>

<!-- Comment ça marche -->
<section class="wrap section" id="comment">
    <span class="eyebrow">Comment ça marche</span>
    <h2 class="h-section">De la recherche à la visite, en 3 étapes</h2>
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
</section>

<!-- Pourquoi DoyaImmo -->
<section class="wrap section" style="padding-top:0;">
    <span class="eyebrow">Pourquoi DoyaImmo</span>
    <h2 class="h-section" style="margin-bottom:10px;">Un marché plus simple, pour tout le monde</h2>
    <div class="value-grid">
        <div class="value-card">
            <div class="value-ic" style="background:var(--teal-soft); color:var(--teal);"><i class="fa-solid fa-clock"></i></div>
            <h3>Gain de temps</h3>
            <p>Une seule demande envoyée à plusieurs agences à la fois, plus besoin de démarcher un par un.</p>
        </div>
        <div class="value-card">
            <div class="value-ic" style="background:var(--gold-soft); color:#8A6414;"><i class="fa-solid fa-scale-balanced"></i></div>
            <h3>Comparaison simple</h3>
            <p>Recevez plusieurs devis et comparez-les au même endroit avant de vous décider.</p>
        </div>
        <div class="value-card">
            <div class="value-ic" style="background:var(--green-soft); color:#1E7A47;"><i class="fa-solid fa-star"></i></div>
            <h3>Agences notées</h3>
            <p>Chaque agence est évaluée par les clients précédents, pour plus de confiance.</p>
        </div>
        <div class="value-card">
            <div class="value-ic" style="background:var(--rust-soft); color:var(--rust);"><i class="fa-solid fa-location-dot"></i></div>
            <h3>Ancré à Dakar</h3>
            <p>Pensé pour le marché immobilier local, quartier par quartier.</p>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="wrap section" style="padding-top:0;">
    <div class="cta-band">
        <h2>Prêt à trouver votre prochain logement ?</h2>
        <p>Publiez votre recherche gratuitement et recevez vos premières propositions sous 48h.</p>
        <div class="cta-actions">
            <a href="{{ route('register.particulier') }}" class="btn btn-rust btn-lg">Créer mon compte client</a>
            <a href="{{ route('register.agence') }}" class="btn btn-ghost btn-lg" style="background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.2); color:#fff;">Je suis une agence</a>
        </div>
    </div>
</section>
@endsection