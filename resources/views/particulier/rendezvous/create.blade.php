@extends('layouts.dashboard')

@section('title', 'Planifier une visite — DoyaImmo')
@section('page_title', 'Planifier une visite')
@section('page_sub', 'Choisissez un créneau pour visiter le bien')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Planifier une visite</h2>
            <p>Sélectionnez un créneau disponible pour visiter le bien</p>
        </div>
        <a href="{{ route('particulier.propositions.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;max-width:700px;margin:0 auto;">
        <!-- Informations de la proposition -->
        <div style="margin-bottom:24px;padding-bottom:16px;border-bottom:1px solid var(--border);">
            <h3 style="font-family:var(--display);font-size:18px;margin-bottom:6px;">
                {{ $proposition->bien->titre ?? 'Bien' }}
            </h3>
            <p style="color:var(--muted);font-size:14px;">
                <i class="fa-solid fa-location-dot"></i> {{ $proposition->bien->quartier ?? 'N/A' }}
            </p>
            <p style="color:var(--rust);font-weight:700;font-size:18px;">
                {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
            </p>
            <p style="color:var(--muted);font-size:13px;">
                <i class="fa-regular fa-building"></i> {{ $proposition->agence->nom_agence }}
            </p>
        </div>

        <!-- Formulaire de choix du créneau -->
        <form action="{{ route('particulier.rendezvous.store') }}" method="POST">
            @csrf
            <input type="hidden" name="proposition_id" value="{{ $proposition->id }}">

            <div style="margin-bottom:20px;">
                <label style="display:block;font-weight:600;font-size:14px;margin-bottom:8px;">
                    Choisissez un créneau horaire
                </label>

                @php
                    // ✅ CORRIGÉ : Utiliser setTimeFromTimeString au lieu de concaténer
                    $creneauxDisponibles = $creneaux->filter(function($creneau) {
                        $creneauDateTime = \Carbon\Carbon::parse($creneau->date)->setTimeFromTimeString($creneau->heure_debut);
                        return !$creneauDateTime->isPast();
                    });
                @endphp

                @if($creneauxDisponibles->count() > 0)
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px;">
                        @foreach($creneauxDisponibles as $creneau)
                            <label style="display:flex;align-items:center;gap:10px;padding:12px 16px;border:2px solid var(--border);border-radius:10px;cursor:pointer;transition:all 0.2s;">
                                <input type="radio" name="creneau_id" value="{{ $creneau->id }}" required>
                                <div>
                                    <div style="font-weight:600;font-size:14px;">
                                        {{ \Carbon\Carbon::parse($creneau->date)->format('d/m/Y') }}
                                    </div>
                                    <div style="color:var(--muted);font-size:13px;">
                                        {{ \Carbon\Carbon::parse($creneau->heure_debut)->format('H:i') }}
                                        - {{ \Carbon\Carbon::parse($creneau->heure_fin)->format('H:i') }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center;padding:40px 20px;color:var(--muted);background:#F7F9FC;border-radius:10px;">
                        <i class="fa-regular fa-calendar-xmark" style="font-size:32px;display:block;margin-bottom:12px;opacity:0.5;"></i>
                        <p>Aucun créneau disponible pour le moment.</p>
                        <p style="font-size:13px;">Tous les créneaux sont passés. Veuillez contacter l'agence directement.</p>
                    </div>
                @endif
            </div>

            @if($creneauxDisponibles->count() > 0)
                <div style="display:flex;gap:10px;flex-wrap:wrap;">
                    <button type="submit" class="btn btn-rust">
                        <i class="fa-solid fa-calendar-check"></i> Planifier la visite
                    </button>
                    <a href="{{ route('particulier.propositions.index') }}" class="btn btn-ghost">
                        Annuler
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn-rust {
        background: var(--rust);
        color: #fff;
        padding: 10px 20px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: background 0.2s;
    }
    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
    }
    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border: 1px solid var(--border);
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .btn-ghost:hover {
        background: var(--border);
    }
    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }

    input[type="radio"]:checked + div {
        border-color: var(--rust);
    }
    label:has(input[type="radio"]:checked) {
        border-color: var(--rust);
        background: var(--rust-soft);
    }
    label:hover {
        border-color: var(--text-soft);
    }
</style>
@endpush