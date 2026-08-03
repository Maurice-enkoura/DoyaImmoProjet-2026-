@extends('layouts.dashboard')

@section('title', 'Planifier une visite — DoyaImmo')
@section('page_title', 'Planifier une visite')
@section('page_sub', 'Choisissez un créneau pour visiter le bien')

@section('content')
<div class="view active">
    <div style="margin-bottom:20px;">
        <a href="{{ route('particulier.propositions.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux propositions
        </a>
    </div>

    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
        <!-- Informations de la proposition -->
        <div style="display:flex;align-items:center;gap:16px;padding:16px;background:#F7F9FC;border-radius:10px;margin-bottom:24px;border:1px solid var(--border);">
            <div style="width:48px;height:48px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--rust);flex-shrink:0;">
                {{ strtoupper(substr($proposition->agence->nom_agence, 0, 1)) }}
            </div>
            <div style="flex:1;">
                <div style="font-weight:600;font-size:15px;">{{ $proposition->agence->nom_agence }}</div>
                <div style="font-size:13px;color:var(--muted);">
                    {{ $proposition->demande->type_bien->label() }} — {{ $proposition->demande->zone_recherchee }}
                </div>
                <div style="font-size:13px;font-weight:600;color:var(--rust);">
                    {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                </div>
            </div>
            <div>
                <span class="status-pill status-acceptee">
                    <i class="fa-solid fa-circle" style="font-size:8px;"></i>
                    Acceptée
                </span>
            </div>
        </div>

        <h3 style="font-family:var(--display);font-size:17px;margin-bottom:6px;">
            <i class="fa-solid fa-calendar-check" style="color:var(--rust);"></i> Choisissez un créneau
        </h3>
        <p style="font-size:13px;color:var(--muted);margin-bottom:20px;">
            Sélectionnez une date, puis un créneau disponible pour la visite du bien.
        </p>

        <form method="POST" action="{{ route('particulier.rendezvous.store') }}" id="rendezvousForm">
            @csrf
            <input type="hidden" name="proposition_id" value="{{ $proposition->id }}">
            <input type="hidden" name="creneau_id" id="creneau_id" value="">

            <!-- Sélection de la date -->
            <div style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:8px;">
                    Sélectionnez une date <span style="color:#C62828;">*</span>
                </label>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    @php
                        $dates = [];
                        for ($i = 0; $i < 14; $i++) {
                            $dates[] = \Carbon\Carbon::today()->addDays($i);
                        }
                    @endphp
                    @foreach($dates as $date)
                        @php
                            $dateObj = $date;
                            $jourSemaine = $dateObj->locale('fr')->isoFormat('dddd');
                            $mois = $dateObj->locale('fr')->isoFormat('MMM');
                        @endphp
                        <button type="button" 
                                class="date-btn" 
                                data-date="{{ $dateObj->format('Y-m-d') }}"
                                onclick="selectDate(this)"
                                style="padding:10px 16px;border:1px solid var(--border);border-radius:10px;background:#fff;cursor:pointer;font-size:13px;transition:all 0.2s;font-family:inherit;min-width:70px;text-align:center;">
                            <div style="font-weight:600;font-size:15px;line-height:1.2;">{{ $dateObj->format('d') }}</div>
                            <div style="font-size:10px;color:var(--muted);line-height:1.2;">{{ $mois }}</div>
                            <div style="font-size:8px;color:var(--muted);margin-top:2px;text-transform:lowercase;line-height:1.2;">{{ $jourSemaine }}</div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Créneaux disponibles -->
            <div id="creneauxContainer" style="margin-bottom:20px;">
                <label style="display:block;font-size:13px;font-weight:600;color:var(--text-soft);margin-bottom:8px;">
                    Créneaux disponibles
                </label>
                <div id="creneauxList" style="display:flex;flex-wrap:wrap;gap:8px;min-height:60px;padding:16px;background:#F7F9FC;border-radius:10px;border:1px dashed var(--border);">
                    <span style="color:var(--muted);font-size:13px;width:100%;text-align:center;">
                        Sélectionnez une date pour voir les créneaux disponibles
                    </span>
                </div>
            </div>

            <!-- Boutons -->
            <div style="display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-rust" id="submitBtn" disabled>
                    <i class="fa-solid fa-calendar-check"></i> Planifier la visite
                </button>
                <a href="{{ route('particulier.propositions.index') }}" class="btn btn-ghost">
                    <i class="fa-solid fa-times"></i> Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    let selectedDate = null;
    let selectedCreneau = null;

    function selectDate(button) {
        document.querySelectorAll('.date-btn').forEach(btn => {
            btn.style.borderColor = 'var(--border)';
            btn.style.background = '#fff';
        });
        
        button.style.borderColor = 'var(--rust)';
        button.style.background = 'var(--rust-soft)';
        
        selectedDate = button.dataset.date;
        loadCreneaux(selectedDate);
    }

    function loadCreneaux(date) {
        const container = document.getElementById('creneauxList');
        container.innerHTML = '<span style="color:var(--muted);font-size:13px;width:100%;text-align:center;"><i class="fa-solid fa-spinner fa-spin"></i> Chargement des créneaux...</span>';

        const agenceId = {{ $proposition->agence->id }};

        // Utiliser la route publique
        const url = `/creneaux/disponibles?agence_id=${agenceId}&date=${date}`;

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (!data || data.length === 0) {
                    container.innerHTML = `
                        <span style="color:var(--muted);font-size:13px;width:100%;text-align:center;">
                            <i class="fa-regular fa-calendar-xmark"></i> Aucun créneau disponible pour cette date
                        </span>
                    `;
                    document.getElementById('submitBtn').disabled = true;
                    return;
                }

                container.innerHTML = '';
                data.forEach(creneau => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'creneau-btn';
                    btn.dataset.id = creneau.id;
                    btn.style.cssText = 'padding:8px 16px;border:1px solid var(--border);border-radius:10px;background:#fff;cursor:pointer;font-size:13px;transition:all 0.2s;font-family:inherit;';
                    btn.innerHTML = `<i class="fa-regular fa-clock"></i> ${creneau.heure} - ${creneau.heure_fin}`;
                    btn.onclick = function() { selectCreneau(this); };
                    container.appendChild(btn);
                });

                document.getElementById('submitBtn').disabled = true;
                selectedCreneau = null;
            })
            .catch(error => {
                console.error('Erreur:', error);
                container.innerHTML = `
                    <span style="color:#C62828;font-size:13px;width:100%;text-align:center;">
                        <i class="fa-solid fa-triangle-exclamation"></i> Erreur de chargement
                    </span>
                `;
            });
    }

    function selectCreneau(button) {
        document.querySelectorAll('.creneau-btn').forEach(btn => {
            btn.style.borderColor = 'var(--border)';
            btn.style.background = '#fff';
        });
        
        button.style.borderColor = 'var(--rust)';
        button.style.background = 'var(--rust-soft)';
        
        selectedCreneau = button.dataset.id;
        document.getElementById('creneau_id').value = selectedCreneau;
        document.getElementById('submitBtn').disabled = false;
    }

    document.getElementById('rendezvousForm').addEventListener('submit', function(e) {
        if (!selectedCreneau) {
            e.preventDefault();
            alert('Veuillez sélectionner un créneau.');
            return;
        }
        
        const btn = document.getElementById('submitBtn');
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Planification en cours...';
        btn.disabled = true;
    });
</script>
@endsection

@push('styles')
<style>
    .date-btn:hover {
        border-color: var(--rust);
    }
    .creneau-btn:hover {
        border-color: var(--rust);
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-acceptee {
        background: #E8F5E9;
        color: #1E7A47;
    }

    @media (max-width: 768px) {
        .date-btn {
            min-width: 60px !important;
            padding: 8px 12px !important;
        }
        .date-btn div:first-child {
            font-size: 13px !important;
        }
    }
</style>
@endpush