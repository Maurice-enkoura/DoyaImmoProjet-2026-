@extends('layouts.admin')

@section('title', 'Détail du rendez-vous — Administration DoyaImmo')
@section('page_title', 'Détail du rendez-vous')
@section('page_sub', 'Rendez-vous du ' . ($rendezVous->date_visite ? \Carbon\Carbon::parse($rendezVous->date_visite)->format('d/m/Y') : 'N/A'))

@section('content')
<style>
    .statut-planifie { background: #FFF8E1; color: #E65100; }
    .statut-confirme { background: #E3F2FD; color: #0D47A1; }
    .statut-termine { background: #E8F5E9; color: #1E7A47; }
    .statut-annule { background: #FFEBEE; color: #C62828; }
</style>

<div style="margin-bottom:20px;">
    <a href="{{ route('admin.rendezvous.index') }}" class="btn btn-ghost btn-sm">
        <i class="fa-solid fa-arrow-left"></i> Retour aux rendez-vous
    </a>
    @if($rendezVous->proposition)
        <a href="{{ route('admin.propositions.show', $rendezVous->proposition_id) }}" class="btn btn-ghost btn-sm" style="margin-left:8px;">
            <i class="fa-solid fa-handshake"></i> Voir la proposition
        </a>
    @endif
    @if($rendezVous->proposition && $rendezVous->proposition->bien)
        <a href="{{ route('admin.biens.show', $rendezVous->proposition->bien_id) }}" class="btn btn-ghost btn-sm" style="margin-left:8px;">
            <i class="fa-solid fa-house"></i> Voir le bien
        </a>
    @endif
</div>

<div class="grid-2">
    <!-- Informations du rendez-vous -->
    <div class="panel">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
            <div>
                <h3 style="font-size:18px;font-weight:700;">
                    <i class="fa-solid fa-calendar-check" style="color:var(--rust);"></i> 
                    Rendez-vous #{{ $rendezVous->id }}
                </h3>
                <div style="font-size:13px;color:var(--muted);margin-top:4px;">
                    <i class="fa-solid fa-calendar"></i> 
                    {{ $rendezVous->date_visite ? \Carbon\Carbon::parse($rendezVous->date_visite)->format('d/m/Y') : 'N/A' }}
                    <span style="margin-left:12px;">
                        <i class="fa-solid fa-clock"></i> 
                        {{ $rendezVous->heure_visite ?? ($rendezVous->creneau->heure_debut ?? 'N/A') }}
                    </span>
                </div>
            </div>
            <span class="status-pill status-{{ $rendezVous->statut }}">
                {{ $rendezVous->statut_label ?? $rendezVous->statut }}
            </span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
            <!-- Agence -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">
                    <i class="fa-solid fa-building"></i> Agence
                </div>
                <div style="display:flex;align-items:center;gap:12px;margin-top:4px;">
                    @if($rendezVous->agence && $rendezVous->agence->logo)
                        <img src="{{ asset('storage/' . $rendezVous->agence->logo) }}" 
                             alt="{{ $rendezVous->agence->nom_agence }}" 
                             style="width:40px;height:40px;object-fit:cover;border-radius:8px;border:1px solid var(--border);">
                    @else
                        <div style="width:40px;height:40px;border-radius:8px;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:var(--gold);">
                            {{ $rendezVous->agence ? Str::substr($rendezVous->agence->nom_agence, 0, 2) : 'NA' }}
                        </div>
                    @endif
                    <div>
                        <div style="font-weight:600;">{{ $rendezVous->agence->nom_agence ?? 'N/A' }}</div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-envelope"></i> {{ $rendezVous->agence->user->email ?? '' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-phone"></i> {{ $rendezVous->agence->user->telephone ?? 'Non renseigné' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-location-dot"></i> {{ $rendezVous->agence->adresse ?? 'Non renseignée' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Particulier -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">
                    <i class="fa-solid fa-user"></i> Particulier
                </div>
                <div style="display:flex;align-items:center;gap:12px;margin-top:4px;">
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:var(--rust);">
                        {{ strtoupper(substr($rendezVous->particulier->user->prenom ?? 'U', 0, 1)) }}{{ strtoupper(substr($rendezVous->particulier->user->nom ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:600;">{{ $rendezVous->particulier->user->prenom ?? '' }} {{ $rendezVous->particulier->user->nom ?? '' }}</div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-envelope"></i> {{ $rendezVous->particulier->user->email ?? '' }}
                        </div>
                        <div style="font-size:12px;color:var(--muted);">
                            <i class="fa-solid fa-phone"></i> {{ $rendezVous->particulier->user->telephone ?? 'Non renseigné' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proposition -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                <div style="font-size:11px;color:var(--muted);">
                    <i class="fa-solid fa-handshake"></i> Proposition associée
                </div>
                <div style="font-weight:600;margin-top:4px;">
                    @if($rendezVous->proposition)
                        Proposition #{{ $rendezVous->proposition_id }}
                        <span style="font-weight:400;font-size:13px;color:var(--muted);">
                            - {{ number_format($rendezVous->proposition->prix_propose ?? 0, 0, ',', ' ') }} FCFA
                        </span>
                    @else
                        <span style="color:var(--muted);">Aucune proposition associée</span>
                    @endif
                </div>
                @if($rendezVous->proposition && $rendezVous->proposition->bien)
                    <div style="font-size:12px;color:var(--muted);margin-top:4px;">
                        <i class="fa-solid fa-house"></i> 
                        {{ $rendezVous->proposition->bien->titre ?? 'N/A' }}
                        @if($rendezVous->proposition->bien->type_contrat)
                            <span class="meta-pill" style="font-size:10px;background:#E3F2FD;color:#0D47A1;">
                                {{ is_object($rendezVous->proposition->bien->type_contrat) ? $rendezVous->proposition->bien->type_contrat->label() : $rendezVous->proposition->bien->type_contrat }}
                            </span>
                        @endif
                        <span style="margin-left:8px;">
                            <i class="fa-solid fa-location-dot"></i> 
                            {{ $rendezVous->proposition->bien->quartier_nom ?? 'N/A' }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Détails du rendez-vous -->
            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Date</div>
                <div style="font-weight:600;">
                    {{ $rendezVous->date_visite ? \Carbon\Carbon::parse($rendezVous->date_visite)->format('d/m/Y') : 'N/A' }}
                </div>
            </div>

            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Heure</div>
                <div style="font-weight:600;">
                    {{ $rendezVous->heure_visite ?? ($rendezVous->creneau->heure_debut ?? 'N/A') }}
                </div>
            </div>

            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Créé le</div>
                <div style="font-weight:600;">{{ $rendezVous->created_at->format('d/m/Y H:i') }}</div>
            </div>

            <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;">
                <div style="font-size:11px;color:var(--muted);">Dernière mise à jour</div>
                <div style="font-weight:600;">{{ $rendezVous->updated_at->format('d/m/Y H:i') }}</div>
            </div>

            @if($rendezVous->creneau)
                <div style="padding:8px 12px;background:#F7F9FC;border-radius:8px;grid-column:span 2;">
                    <div style="font-size:11px;color:var(--muted);">Créneau horaire</div>
                    <div style="font-weight:600;">
                        {{ $rendezVous->creneau->heure_debut ?? 'N/A' }} - {{ $rendezVous->creneau->heure_fin ?? 'N/A' }}
                    </div>
                </div>
            @endif
        </div>

        <!-- Note : Lecture seule -->
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
            <div style="display:flex;align-items:center;gap:8px;padding:10px 14px;background:#FFF8E1;border-radius:8px;border:1px solid #FFE0B2;">
                <i class="fa-solid fa-info-circle" style="color:#E65100;"></i>
                <span style="font-size:13px;color:var(--text-soft);">
                    <strong>Lecture seule</strong> — Les rendez-vous ne peuvent pas être modifiés par l'administrateur.
                </span>
            </div>
        </div>
    </div>

    <!-- Informations complémentaires -->
    <div>
        <!-- Jours disponibles de l'agence -->
        @if($rendezVous->agence && method_exists($rendezVous->agence, 'creneaux') && $rendezVous->agence->creneaux && $rendezVous->agence->creneaux->count() > 0)
            <div class="panel" style="margin-bottom:20px;">
                <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                    <i class="fa-solid fa-calendar-days"></i> Jours disponibles de l'agence
                </h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    @php
                        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
                        $joursLabels = [
                            'lundi' => 'Lundi',
                            'mardi' => 'Mardi',
                            'mercredi' => 'Mercredi',
                            'jeudi' => 'Jeudi',
                            'vendredi' => 'Vendredi',
                            'samedi' => 'Samedi',
                            'dimanche' => 'Dimanche'
                        ];
                    @endphp
                    @foreach($jours as $jour)
                        @php
                            $creneau = $rendezVous->agence->creneaux->where('jour', $jour)->first();
                        @endphp
                        <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-weight:600;font-size:12px;">{{ $joursLabels[$jour] ?? $jour }}</span>
                            @if($creneau && $creneau->est_disponible)
                                <span style="font-size:11px;color:var(--green);">
                                    <i class="fa-solid fa-check-circle"></i> 
                                    {{ \Carbon\Carbon::parse($creneau->heure_debut)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($creneau->heure_fin)->format('H:i') }}
                                </span>
                            @else
                                <span style="font-size:11px;color:var(--muted);">
                                    <i class="fa-solid fa-xmark-circle"></i> Fermé
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Photos du bien concerné -->
        @if($rendezVous->proposition && $rendezVous->proposition->bien)
            <div class="panel" style="margin-bottom:20px;">
                <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                    <i class="fa-solid fa-image"></i> Photos du bien concerné
                    <span style="font-size:12px;color:var(--muted);font-weight:400;margin-left:8px;">
                        ({{ $rendezVous->proposition->bien->medias->where('type_media', 'image')->count() }} photos)
                    </span>
                </h3>
                @php
                    $photos = $rendezVous->proposition->bien->medias->where('type_media', 'image') ?? collect();
                @endphp
                @if($photos->count() > 0)
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
                        @foreach($photos->take(6) as $media)
                            <a href="{{ asset('storage/' . $media->fichier) }}" target="_blank" 
                               style="border-radius:8px;overflow:hidden;aspect-ratio:1;border:1px solid var(--border);display:block;">
                                <img src="{{ asset('storage/' . $media->fichier) }}" 
                                     alt="Photo" 
                                     style="width:100%;height:100%;object-fit:cover;">
                            </a>
                        @endforeach
                    </div>
                    @if($photos->count() > 6)
                        <div style="text-align:center;margin-top:8px;font-size:12px;color:var(--muted);">
                            + {{ $photos->count() - 6 }} autres photos
                        </div>
                    @endif
                @else
                    <p style="color:var(--muted);font-size:13px;text-align:center;padding:10px 0;">
                        <i class="fa-solid fa-image" style="font-size:24px;display:block;margin-bottom:8px;"></i>
                        Aucune photo disponible.
                    </p>
                @endif
            </div>

            <!-- Informations du bien -->
            <div class="panel" style="margin-bottom:20px;">
                <h3 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                    <i class="fa-solid fa-info-circle"></i> Informations du bien
                </h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Titre</div>
                        <div style="font-weight:600;font-size:13px;">{{ $rendezVous->proposition->bien->titre ?? 'N/A' }}</div>
                    </div>
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Prix</div>
                        <div style="font-weight:600;font-size:13px;color:var(--rust);">
                            {{ number_format($rendezVous->proposition->bien->prix ?? 0, 0, ',', ' ') }} FCFA
                        </div>
                    </div>
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Surface</div>
                        <div style="font-weight:600;font-size:13px;">{{ $rendezVous->proposition->bien->surface ?? 0 }} m²</div>
                    </div>
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;">
                        <div style="font-size:10px;color:var(--muted);">Chambres</div>
                        <div style="font-weight:600;font-size:13px;">{{ $rendezVous->proposition->bien->nombre_chambres ?? 0 }}</div>
                    </div>
                    <div style="padding:6px 10px;background:#F7F9FC;border-radius:6px;grid-column:span 2;">
                        <div style="font-size:10px;color:var(--muted);">Quartier</div>
                        <div style="font-weight:600;font-size:13px;">
                            {{ $rendezVous->proposition->bien->quartier_nom ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liens -->
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="{{ route('admin.biens.show', $rendezVous->proposition->bien_id) }}" class="btn btn-rust" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-house"></i> Voir le bien en détail
                </a>
                <a href="{{ route('admin.agences.show', $rendezVous->agence_id) }}" class="btn btn-ghost" style="width:100%;justify-content:center;">
                    <i class="fa-solid fa-building"></i> Voir l'agence
                </a>
            </div>
        @endif
    </div>
</div>
@endsection