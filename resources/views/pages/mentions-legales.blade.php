@extends('layouts.app')

@section('title', 'Mentions Légales — DoyaImmo')
@section('meta_description', 'Consultez les mentions légales de DoyaImmo. Éditeur, hébergement, propriété intellectuelle et informations légales de la plateforme immobilière.')
@section('canonical', 'https://doyaimmo.com/mentions-legales')
@section('og_title', 'Mentions Légales — DoyaImmo')
@section('og_description', 'Consultez les mentions légales de DoyaImmo. Éditeur, hébergement, propriété intellectuelle et informations légales de la plateforme.')
@section('robots', 'index, follow')

@section('content')
<div class="legal-wrap">
    <div class="container" style="max-width:760px; margin:0 auto; padding:60px 32px 90px;">
        <h1 style="font-family:var(--display); font-weight:800; font-size:30px; margin-bottom:8px;">Mentions Légales</h1>
        <p style="font-size:12.5px; color:var(--muted); margin-bottom:36px;">Dernière mise à jour : juillet 2026</p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">Éditeur du site</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">
            DoyaImmo — Plateforme de mise en relation immobilière<br>
            Dakar, Sénégal<br>
            Email : <a href="mailto:contact@doyaimmo.sn" style="color:var(--rust);text-decoration:none;">contact@doyaimmo.sn</a>
        </p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">Hébergement</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">Ce site est hébergé par un prestataire d'hébergement web tiers, conformément à la législation en vigueur au Sénégal.</p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">Propriété intellectuelle</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">L'ensemble des contenus présents sur DoyaImmo (textes, logo, éléments graphiques) est protégé par le droit d'auteur. Toute reproduction non autorisée est interdite.</p>

        <h2 style="font-family:var(--display); font-weight:700; font-size:17px; margin:32px 0 12px;">Données personnelles</h2>
        <p style="font-size:14px; line-height:1.75; color:var(--text-soft); margin-bottom:12px;">Les données collectées lors de l'inscription sont utilisées uniquement dans le cadre du fonctionnement de la plateforme (mise en relation, gestion des comptes). Conformément à la réglementation applicable, chaque utilisateur dispose d'un droit d'accès, de rectification et de suppression de ses données.</p>
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
    .legal-wrap p {
        font-size: 14px;
        line-height: 1.75;
        color: var(--text-soft);
        margin-bottom: 12px;
    }
</style>
@endpush