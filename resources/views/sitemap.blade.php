<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- ==================== PAGES STATIQUES ==================== -->
    <url>
        <loc>{{ route('home') }}</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ route('biens.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc>{{ route('besoins.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ route('agences.public.index') }}</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc>{{ route('recherche') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>
    <url>
        <loc>{{ route('cgu') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>
    <url>
        <loc>{{ route('mentions-legales') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.4</priority>
    </url>
    @if(Route::has('about'))
    <url>
        <loc>{{ route('about') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    @endif
    @if(Route::has('contact'))
    <url>
        <loc>{{ route('contact') }}</loc>
        <changefreq>monthly</changefreq>
        <priority>0.3</priority>
    </url>
    @endif

    <!-- ==================== BIENS ACTIFS ==================== -->
    @foreach($biens as $bien)
    <url>
        <loc>{{ route('biens.show', $bien->slug) }}</loc>
        <lastmod>{{ $bien->updated_at->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

    <!-- ==================== DEMANDES ACTIVES ==================== -->
    @foreach($demandes as $demande)
    <url>
        <loc>{{ route('besoins.show', $demande->slug) }}</loc>
        <lastmod>{{ $demande->updated_at->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach

    <!-- ==================== AGENCES VALIDÉES ==================== -->
    @foreach($agences as $agence)
    <url>
        <loc>{{ route('agences.public.show', $agence->slug) }}</loc>
        <lastmod>{{ $agence->updated_at->toW3cString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.5</priority>
    </url>
    @endforeach
</urlset>