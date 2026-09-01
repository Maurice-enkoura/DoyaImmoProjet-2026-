@extends('layouts.app')

@section('title', 'Conditions Générales d\'Utilisation — DoyaImmo')

@section('content')
<div class="legal-wrap">
    <div class="container" style="max-width:760px; margin:0 auto; padding:60px 32px 90px;">
        <h1 style="font-family:var(--display); font-weight:800; font-size:30px; margin-bottom:8px;">Conditions Générales d'Utilisation</h1>
        <p style="font-size:12.5px; color:var(--muted); margin-bottom:36px;">Dernière mise à jour : juillet 2026</p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">1. Objet</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">DoyaImmo est une plateforme web de mise en relation entre des particuliers à la recherche d'un logement et des agences immobilières présentes à Dakar. Les présentes conditions régissent l'accès et l'utilisation de la plateforme par les clients et les agences.</p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">2. Inscription et comptes</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">L'accès aux fonctionnalités de DoyaImmo nécessite la création d'un compte client ou d'un compte agence. Chaque utilisateur s'engage à fournir des informations exactes et à jour.</p>
        <ul style="padding-left:4px;">
            <li style="display:flex; gap:8px; font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;"><span style="color:var(--rust); flex-shrink:0;">—</span> Les comptes clients permettent de publier des besoins de logement et de recevoir des offres d'agences.</li>
            <li style="display:flex; gap:8px; font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;"><span style="color:var(--rust); flex-shrink:0;">—</span> Les comptes agences sont soumis à une vérification avant activation (registre de commerce / NINEA).</li>
        </ul>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">3. Rôle de DoyaImmo</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">DoyaImmo agit uniquement en tant qu'intermédiaire technique de mise en relation. La plateforme n'est ni propriétaire, ni locataire, ni partie aux contrats conclus entre clients et agences. DoyaImmo ne garantit pas l'exactitude des offres publiées par les agences.</p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">4. Abonnement des agences</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">Les agences immobilières peuvent souscrire à un abonnement payant (Standard ou Premium) donnant accès à des fonctionnalités avancées, notamment un nombre d'offres plus élevé et une meilleure visibilité des annonces.</p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">5. Avis et évaluations</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">Les clients peuvent laisser un avis sur une agence après une prestation. Les avis doivent rester factuels et respectueux ; DoyaImmo se réserve le droit de modérer tout contenu abusif.</p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">6. Responsabilités</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">Chaque utilisateur est responsable des informations qu'il publie sur la plateforme. DoyaImmo ne saurait être tenue responsable des litiges survenant entre un client et une agence dans le cadre d'une transaction immobilière.</p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">7. Modification des conditions</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">DoyaImmo se réserve le droit de modifier les présentes conditions à tout moment. Les utilisateurs seront informés de tout changement significatif.</p>
    </div>
</div>
@endsection

@push('styles')
<style>
    .legal-wrap h1 {
        font-family: var(--display);
        font-weight: 800;
        font-size: 30px;
        margin-bottom: 8px;
    }
    .legal-wrap .updated {
        font-size: 12.5px;
        color: var(--muted);
        margin-bottom: 36px;
    }
    .legal-wrap h2 {
        font-family: var(--display);
        font-weight: 700;
        font-size: 17px;
        margin: 32px 0 12px;
    }
    .legal-wrap p, .legal-wrap li {
        font-size: 14px;
        line-height: 1.75;
        color: var(--text-soft);
        margin-bottom: 12px;
    }
    .legal-wrap ul {
        padding-left: 4px;
    }
    .legal-wrap li {
        display: flex;
        gap: 8px;
    }
    .legal-wrap li::before {
        content: "—";
        color: var(--rust);
        flex-shrink: 0;
    }
</style>
@endpush