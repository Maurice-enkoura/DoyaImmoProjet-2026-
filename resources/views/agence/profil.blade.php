@extends('layouts.dashboard-agence')

@section('title', 'Profil agence — DoyaImmo')
@section('page_title', 'Profil agence')
@section('page_sub', 'Vos informations visibles par les clients')

@section('content')
<div class="view active">
    <!-- En-tête de la section -->
    <div class="section-head">
        <div>
            <h2>Profil de l'agence</h2>
            <p>Ces informations sont visibles par les clients de DoyaImmo</p>
        </div>
    </div>

    <!-- Onglets -->
    <div class="tabs">
        <button class="tab-btn active" data-tab="infos" onclick="switchTab('infos')">
            <i class="fa-solid fa-user"></i> Informations
        </button>
        <button class="tab-btn" data-tab="creneaux" onclick="switchTab('creneaux')">
            <i class="fa-solid fa-calendar-clock"></i> Disponibilités
        </button>
    </div>

    <!-- ==================== ONGLET INFORMATIONS ==================== -->
    <div id="tab-infos" class="tab-content active">
        <div class="profile-grid">
            <!-- Carte de gauche - Profil -->
            <div class="profile-card profile-left">
                <div class="profile-avatar">
                    {{ strtoupper(substr($agence->nom_agence, 0, 2)) }}
                </div>
                <h3 class="profile-name">{{ $agence->nom_agence }}</h3>
                <div class="profile-status {{ $agence->statut_validation ? 'verified' : 'pending' }}">
                    <i class="fa-solid {{ $agence->statut_validation ? 'fa-circle-check' : 'fa-clock' }}"></i>
                    {{ $agence->statut_validation ? 'Agence vérifiée' : 'En attente de validation' }}
                </div>
                <div class="profile-meta">
                    Membre depuis {{ $agence->created_at->format('d/m/Y') }}
                </div>
                <div class="profile-rating">
                    <span class="stars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= round($agence->evaluations->avg('note') ?? 0) ? 'active' : '' }}"></i>
                        @endfor
                    </span>
                    <span>{{ number_format($agence->evaluations->avg('note') ?? 0, 1) }} ★</span>
                    <span class="reviews">({{ $agence->evaluations->count() }} avis)</span>
                </div>
                <div class="profile-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ $besoinsDisponibles ?? 0 }}</span>
                        <span class="stat-label">Besoins disponibles</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $rendezvousAVenir ?? 0 }}</span>
                        <span class="stat-label">Rendez-vous à venir</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ $agence->biens->count() ?? 0 }}</span>
                        <span class="stat-label">Biens publiés</span>
                    </div>
                </div>
            </div>

            <!-- Carte de droite - Formulaire -->
            <div class="profile-card profile-right">
                <form method="POST" action="{{ route('agence.profil.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-row">
                        <div class="field">
                            <label>Nom de l'agence <span class="required">*</span></label>
                            <input type="text" name="nom_agence" value="{{ old('nom_agence', $agence->nom_agence) }}" required>
                            @error('nom_agence') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div class="field">
                            <label>Quartier <span class="required">*</span></label>
                            <input type="text" name="quartier" value="{{ old('quartier', $agence->quartier) }}" required>
                            @error('quartier') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label>Adresse <span class="required">*</span></label>
                        <input type="text" name="adresse" value="{{ old('adresse', $agence->adresse) }}" required>
                        @error('adresse') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label>Description</label>
                        <textarea name="description" rows="4">{{ old('description', $agence->description) }}</textarea>
                        @error('description') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label>Logo</label>
                        <input type="file" name="logo" accept="image/*">
                        @if($agence->logo)
                            <div class="file-info">
                                <i class="fa-solid fa-check-circle" style="color:var(--green);"></i>
                                Logo actuel
                            </div>
                        @endif
                        @error('logo') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-rust">
                            <i class="fa-solid fa-save"></i> Enregistrer
                        </button>
                        <a href="{{ route('agence.dashboard') }}" class="btn btn-ghost">
                            <i class="fa-solid fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ==================== ONGLET DISPONIBILITÉS ==================== -->
    <div id="tab-creneaux" class="tab-content">
        <!-- Légende -->
        <div class="legend-bar">
            <div class="legend-item">
                <span class="legend-dot disponible"></span> Disponible
            </div>
            <div class="legend-item">
                <span class="legend-dot indisponible"></span> Indisponible
            </div>
            <div class="legend-item">
                <span class="legend-dot today"></span> Aujourd'hui
            </div>
            <div class="legend-item">
                <span class="legend-dot past"></span> Passé
            </div>
        </div>

        <!-- Bouton Générer -->
        <div class="creneaux-header">
            <div class="creneaux-info">
                <i class="fa-regular fa-clock"></i>
                <span>Gérez vos créneaux de disponibilité pour les visites</span>
            </div>
            <button class="btn btn-rust" onclick="openModal()">
                <i class="fa-solid fa-plus"></i> Générer des créneaux
            </button>
        </div>

        <!-- Grille des créneaux -->
        <div class="slots-grid">
            @php
                $joursMap = [
                    'Monday' => 'Lundi',
                    'Tuesday' => 'Mardi',
                    'Wednesday' => 'Mercredi',
                    'Thursday' => 'Jeudi',
                    'Friday' => 'Vendredi',
                    'Saturday' => 'Samedi',
                    'Sunday' => 'Dimanche'
                ];
                $aujourdhui = \Carbon\Carbon::today()->format('Y-m-d');
            @endphp

            @foreach($semaine as $date)
                @php
                    $dateObj = \Carbon\Carbon::parse($date);
                    $jourSemaine = $dateObj->format('l');
                    $estAujourdhui = $date === $aujourdhui;
                    $estPasse = $dateObj->isPast() && !$estAujourdhui;
                    $creneauxDuJour = $creneaux[$date] ?? collect();
                @endphp

                <div class="slot-card {{ $estAujourdhui ? 'today' : '' }} {{ $estPasse ? 'past' : '' }}">
                    <div class="slot-card-header">
                        <div class="slot-day">
                            <span class="day-name">{{ $joursMap[$jourSemaine] ?? $jourSemaine }}</span>
                            <span class="day-date">{{ $dateObj->format('d/m/Y') }}</span>
                        </div>
                        @if($estAujourdhui)
                            <span class="today-badge">Aujourd'hui</span>
                        @endif
                        @if($estPasse)
                            <span class="past-badge">Passé</span>
                        @endif
                    </div>

                    <div class="slot-card-body">
                        @if($creneauxDuJour->count() > 0)
                            <div class="slots-list">
                                @foreach($creneauxDuJour as $creneau)
                                    <div class="slot-item {{ $creneau->est_disponible ? 'disponible' : 'indisponible' }}">
                                        <span class="slot-time">
                                            {{ substr($creneau->heure_debut, 0, 5) }} - {{ substr($creneau->heure_fin, 0, 5) }}
                                        </span>
                                        <span class="slot-status">
                                            @if($creneau->est_disponible)
                                                <i class="fa-solid fa-circle-check"></i> Disponible
                                            @else
                                                <i class="fa-solid fa-circle-xmark"></i> Indisponible
                                            @endif
                                        </span>
                                        @if(!$estPasse)
                                            <form action="{{ route('agence.creneaux.toggle', $creneau) }}" method="POST" class="slot-form">
                                                @csrf
                                                <button type="submit" class="slot-toggle" title="Basculer la disponibilité">
                                                    <i class="fa-solid {{ $creneau->est_disponible ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('agence.creneaux.supprimer', $creneau) }}" method="POST" class="slot-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="slot-delete" title="Supprimer ce créneau" onclick="return confirm('Supprimer ce créneau ?')">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-slots">
                                <i class="fa-regular fa-clock"></i>
                                <span>Aucun créneau</span>
                            </div>
                        @endif
                    </div>

                    <div class="slot-card-footer">
                        <span class="slot-count">
                            <i class="fa-regular fa-clock"></i>
                            {{ $creneauxDuJour->count() }} créneau(x)
                        </span>
                        @if($creneauxDuJour->count() > 0 && !$estPasse)
                            <form action="{{ route('agence.creneaux.supprimerDate') }}" method="POST" class="slot-form">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="date" value="{{ $date }}">
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:#C62828;border-color:#FFCDD2;" onclick="return confirm('Supprimer tous les créneaux de cette date ?')">
                                    <i class="fa-solid fa-trash-can"></i> Supprimer tout
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- ==================== MODAL GÉNÉRER DES CRÉNEAUX ==================== -->
<div class="modal" id="modalCreneaux">
    <div class="modal-overlay" onclick="closeModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h3>
                <i class="fa-solid fa-calendar-plus" style="color:var(--rust);"></i>
                Générer des créneaux
            </h3>
            <button class="modal-close" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form method="POST" action="{{ route('agence.creneaux.generer') }}" class="modal-body">
            @csrf

            <div class="form-group">
                <label>Sélectionnez les jours</label>
                <div class="days-grid">
                    @php
                        $joursSemaine = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
                    @endphp
                    @foreach($joursSemaine as $jour)
                        <label class="day-check">
                            <input type="checkbox" name="jours[]" value="{{ $jour }}" checked>
                            {{ $jour }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label>Plages horaires</label>
                <div class="hours-grid">
                    @foreach(['09:00', '10:00', '11:00', '14:00', '15:00', '16:00', '17:00'] as $heure)
                        @php
                            $heureFin = \Carbon\Carbon::parse($heure)->addHour()->format('H:i');
                        @endphp
                        <label class="hour-check">
                            <input type="checkbox" name="heures[]" value="{{ $heure }}" checked>
                            {{ $heure }} - {{ $heureFin }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="closeModal()">
                    Annuler
                </button>
                <button type="submit" class="btn btn-rust">
                    <i class="fa-solid fa-check"></i> Générer les créneaux
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ===================== SWITCH ONGLETS =====================
    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => {
            el.classList.remove('active');
            el.style.display = 'none';
        });
        
        document.querySelectorAll('.tab-btn').forEach(el => {
            el.classList.remove('active');
        });
        
        const content = document.getElementById('tab-' + tab);
        content.classList.add('active');
        content.style.display = 'block';
        
        const btn = document.querySelector(`.tab-btn[data-tab="${tab}"]`);
        btn.classList.add('active');
    }

    // ===================== MODAL =====================
    function openModal() {
        document.getElementById('modalCreneaux').classList.add('active');
        document.getElementById('modalCreneaux').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('modalCreneaux').classList.remove('active');
        document.getElementById('modalCreneaux').style.display = 'none';
    }

    // Fermer le modal avec Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });

    // Fermer le modal en cliquant sur l'overlay
    document.querySelector('.modal-overlay').addEventListener('click', closeModal);
</script>
@endpush

@push('styles')
<style>
    /* ===================== ONGLETS ===================== */
    .tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 1px solid var(--border);
    }

    .tab-btn {
        padding: 10px 20px;
        border: none;
        background: none;
        cursor: pointer;
        font-weight: 600;
        color: var(--text-soft);
        border-bottom: 3px solid transparent;
        font-size: 14px;
        font-family: inherit;
        transition: all 0.2s;
    }

    .tab-btn:hover {
        color: var(--ink);
        border-bottom-color: var(--border);
    }

    .tab-btn.active {
        color: var(--rust);
        border-bottom-color: var(--rust);
    }

    .tab-btn i {
        margin-right: 8px;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    /* ===================== PROFIL ===================== */
    .profile-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 24px;
    }

    @media (max-width: 820px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    .profile-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
    }

    .profile-left {
        text-align: center;
    }

    .profile-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--rust-soft);
        color: var(--rust);
        font-size: 32px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
    }

    .profile-name {
        font-family: var(--display);
        font-size: 18px;
        margin: 0 0 8px;
    }

    .profile-status {
        font-size: 13px;
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
    }

    .profile-status.verified {
        color: #1E7A47;
        background: rgba(30, 122, 71, 0.1);
    }

    .profile-status.pending {
        color: #E65100;
        background: rgba(230, 81, 0, 0.1);
    }

    .profile-meta {
        font-size: 12.5px;
        color: var(--muted);
        margin: 8px 0;
    }

    .profile-rating {
        font-size: 13px;
        color: var(--text-soft);
    }

    .profile-rating .stars {
        color: #D4D8E0;
    }

    .profile-rating .stars .active {
        color: #F5A623;
    }

    .profile-rating .reviews {
        color: var(--muted);
        font-size: 12px;
    }

    .profile-stats {
        display: flex;
        gap: 16px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
        justify-content: center;
        flex-wrap: wrap;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        display: block;
        font-family: var(--display);
        font-weight: 700;
        font-size: 20px;
        color: var(--ink);
    }

    .stat-label {
        font-size: 11px;
        color: var(--muted);
    }

    /* ===================== FORMULAIRE ===================== */
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }

    .field {
        margin-bottom: 16px;
    }

    .field label {
        display: block;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--text-soft);
        margin-bottom: 4px;
    }

    .field input,
    .field textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        font-family: inherit;
        transition: border 0.2s;
        background: #fff;
    }

    .field input:focus,
    .field textarea:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }

    .field textarea {
        resize: vertical;
        min-height: 100px;
    }

    .field input[type="file"] {
        padding: 8px;
        border: 1px dashed var(--border);
        cursor: pointer;
    }

    .field input[type="file"]:hover {
        border-color: var(--rust);
    }

    .file-info {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }

    .required {
        color: var(--rust);
    }

    .error {
        display: block;
        color: #C62828;
        font-size: 12px;
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
        margin-top: 8px;
    }

    /* ===================== CRÉNEAUX ===================== */
    .legend-bar {
        display: flex;
        gap: 20px;
        padding: 12px 16px;
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 12px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-soft);
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .legend-dot.disponible {
        background: #1E7A47;
    }

    .legend-dot.indisponible {
        background: #C62828;
    }

    .legend-dot.today {
        background: #4A90D9;
        border: 2px solid #4A90D9;
        box-shadow: 0 0 0 2px #fff, 0 0 0 3px #4A90D9;
    }

    .legend-dot.past {
        background: #E8ECF0;
        border: 1px dashed #B0B8C4;
    }

    .creneaux-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .creneaux-info {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--muted);
    }

    .creneaux-info i {
        color: var(--rust);
    }

    .slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 16px;
    }

    @media (max-width: 768px) {
        .slots-grid {
            grid-template-columns: 1fr;
        }
    }

    .slot-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        transition: all 0.2s;
    }

    .slot-card.today {
        border-color: #4A90D9;
        box-shadow: 0 0 0 1px #4A90D9;
    }

    .slot-card.past {
        opacity: 0.5;
    }

    .slot-card.past .slot-toggle,
    .slot-card.past .slot-delete {
        display: none;
    }

    .slot-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        background: #FAFBFC;
    }

    .slot-day .day-name {
        font-weight: 600;
        font-size: 15px;
    }

    .slot-day .day-date {
        font-size: 12px;
        color: var(--muted);
    }

    .today-badge {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        background: #4A90D9;
        color: #fff;
        padding: 2px 12px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }

    .past-badge {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        background: var(--border);
        color: var(--muted);
        padding: 2px 12px;
        border-radius: 20px;
        letter-spacing: 0.5px;
    }

    .slot-card-body {
        padding: 12px 16px;
        max-height: 220px;
        overflow-y: auto;
    }

    .slots-list {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .slot-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 13px;
    }

    .slot-item.disponible {
        background: rgba(30, 122, 71, 0.06);
        border: 1px solid rgba(30, 122, 71, 0.15);
    }

    .slot-item.indisponible {
        background: rgba(198, 40, 40, 0.06);
        border: 1px solid rgba(198, 40, 40, 0.15);
        opacity: 0.7;
    }

    .slot-item .slot-time {
        font-weight: 500;
        min-width: 80px;
    }

    .slot-item .slot-status {
        flex: 1;
        font-size: 12px;
    }

    .slot-item.disponible .slot-status {
        color: #1E7A47;
    }

    .slot-item.indisponible .slot-status {
        color: #C62828;
    }

    .slot-item .slot-status i {
        margin-right: 4px;
    }

    .slot-form {
        display: inline;
        margin: 0;
        padding: 0;
    }

    .slot-toggle {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 18px;
        padding: 4px;
        color: var(--muted);
    }

    .slot-item.disponible .slot-toggle {
        color: #1E7A47;
    }

    .slot-item.indisponible .slot-toggle {
        color: #C62828;
    }

    .slot-delete {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        padding: 4px;
        color: var(--muted);
        opacity: 0.5;
    }

    .slot-delete:hover {
        color: #C62828;
        opacity: 1;
    }

    .no-slots {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        color: var(--muted);
        font-size: 13px;
    }

    .no-slots i {
        font-size: 28px;
        margin-bottom: 8px;
        opacity: 0.3;
    }

    .slot-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 16px;
        border-top: 1px solid var(--border);
        background: #FAFBFC;
        font-size: 12px;
        color: var(--muted);
    }

    .slot-count {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* ===================== MODAL ===================== */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal.active {
        display: flex;
    }

    .modal-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.4);
    }

    .modal-content {
        position: relative;
        background: #fff;
        border-radius: var(--radius);
        max-width: 560px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 24px 64px rgba(0,0,0,0.2);
    }

    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 1;
    }

    .modal-header h3 {
        font-family: var(--display);
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: var(--muted);
        padding: 4px;
    }

    .modal-close:hover {
        color: var(--ink);
    }

    .modal-body {
        padding: 24px;
    }

    .days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 8px;
    }

    @media (max-width: 600px) {
        .days-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .day-check {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 13px;
        cursor: pointer;
        padding: 8px 4px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: #fff;
        user-select: none;
    }

    .day-check:hover {
        border-color: var(--rust);
        background: rgba(181, 80, 42, 0.04);
    }

    .day-check:has(input:checked) {
        background: var(--rust-soft);
        border-color: var(--rust);
    }

    .day-check input[type="checkbox"] {
        accent-color: var(--rust);
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .hours-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    @media (max-width: 600px) {
        .hours-grid {
            grid-template-columns: 1fr;
        }
    }

    .hour-check {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: #fff;
        user-select: none;
    }

    .hour-check:hover {
        border-color: var(--rust);
        background: rgba(181, 80, 42, 0.04);
    }

    .hour-check:has(input:checked) {
        background: var(--rust-soft);
        border-color: var(--rust);
    }

    .hour-check input[type="checkbox"] {
        accent-color: var(--rust);
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .modal-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        padding-top: 16px;
        border-top: 1px solid var(--border);
        margin-top: 8px;
    }

    /* ===================== BOUTONS ===================== */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 12px;
        font-size: 13.5px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
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
        padding: 6px 14px;
        font-size: 12.5px;
    }
</style>
@endpush