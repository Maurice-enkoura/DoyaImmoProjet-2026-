@extends('layouts.dashboard-agence')

@section('title', 'Profil agence — DoyaImmo')
@section('page_title', 'Profil agence')
@section('page_sub', 'Vos informations visibles par les clients')

@section('content')
@php
    $ongletActif = request()->get('onglet', 'infos');

    $planningType = session()->get('planning_type_' . $agence->id, []);
    $joursFr = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    $planningParJour = [];
    foreach ($planningType as $p) {
        $jour = $p['jour'];
        if (!isset($planningParJour[$jour])) $planningParJour[$jour] = [];
        $planningParJour[$jour][] = [
            'heure_debut' => $p['heure_debut'],
            'heure_fin'   => $p['heure_fin'],
        ];
    }

    $semaine         = isset($creneaux['semaine']) ? $creneaux['semaine'] : [];
    $creneauxParJour = isset($creneaux['creneaux']) ? $creneaux['creneaux'] : [];
@endphp

<div class="view active agence-profil">

    {{-- ═══ HEADER DESKTOP ═══ --}}
    <div class="page-header">
        <div class="page-header__left">
            <h2>Profil agence</h2>
            <p>Vos informations visibles par les clients</p>
        </div>
    </div>

    {{-- ═══ HERO AGENCE ═══ --}}
    <section class="profil-hero">
        <div class="profil-hero__avatar">
            @if($agence->logo)
                <img src="{{ asset('storage/' . $agence->logo) }}" alt="{{ $agence->nom_agence }}">
            @else
                {{ strtoupper(mb_substr($agence->nom_agence, 0, 2)) }}
            @endif
        </div>

        <div class="profil-hero__body">
            <h2 class="profil-hero__name">{{ $agence->nom_agence }}</h2>

            <div class="profil-hero__badges">
                @if($agence->statut_validation)
                    <span class="profil-badge profil-badge--success">
                        <i class="fa-solid fa-circle-check"></i> Vérifiée
                    </span>
                @else
                    <span class="profil-badge profil-badge--warning">
                        <i class="fa-solid fa-clock"></i> En attente
                    </span>
                @endif

                <span class="profil-badge profil-badge--gold">
                    <i class="fa-solid fa-star"></i>
                    {{ number_format($agence->evaluations->avg('note') ?? 0, 1) }}
                    <span class="profil-badge__muted">({{ $agence->evaluations->count() }})</span>
                </span>
            </div>

            <div class="profil-hero__meta">
                <span><i class="fa-solid fa-map-pin"></i> {{ $agence->quartier }}</span>
                <span class="profil-hero__dot">·</span>
                <span>Membre depuis {{ $agence->created_at->format('m/Y') }}</span>
            </div>
        </div>
    </section>

    {{-- ═══ TABS iOS ═══ --}}
    <nav class="profil-tabs" role="tablist">
        <button type="button"
                class="profil-tab {{ $ongletActif === 'infos' ? 'is-active' : '' }}"
                data-tab="infos"
                onclick="switchTab('infos')"
                role="tab">
            <i class="fa-solid fa-user"></i>
            <span>Informations</span>
        </button>

        <button type="button"
                class="profil-tab {{ $ongletActif === 'creneaux' ? 'is-active' : '' }}"
                data-tab="creneaux"
                onclick="switchTab('creneaux')"
                role="tab">
            <i class="fa-solid fa-calendar-clock"></i>
            <span>Disponibilités</span>
        </button>
    </nav>

    {{-- ═══════════════════════════════════════════
         TAB 1 — INFORMATIONS
    ═══════════════════════════════════════════ --}}
    <div id="tab-infos" class="profil-content {{ $ongletActif === 'infos' ? 'is-active' : '' }}">

        <div class="profil-layout">
            {{-- Colonne principale --}}
            <div class="profil-layout__main">
                <form method="POST"
                      action="{{ route('agence.profil.update') }}"
                      enctype="multipart/form-data"
                      class="profil-form"
                      id="profilForm">
                    @csrf
                    @method('PUT')

                    {{-- Logo --}}
                    <div class="ios-section-title">
                        <i class="fa-regular fa-image"></i> Logo
                    </div>

                    <label class="logo-card" for="logoInput">
                        <div class="logo-card__preview" id="logoPreview">
                            @if($agence->logo)
                                <img src="{{ asset('storage/' . $agence->logo) }}" alt="Logo">
                            @else
                                <div class="logo-card__placeholder">
                                    <i class="fa-solid fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="logo-card__info">
                            <span class="logo-card__title">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                Changer le logo
                            </span>
                            <span class="logo-card__hint">
                                PNG, JPEG, SVG · Max 2 Mo
                            </span>
                            @if($agence->logo)
                                <span class="logo-card__current">
                                    <i class="fa-solid fa-check-circle"></i>
                                    <span class="logo-card__filename" title="{{ basename($agence->logo) }}">
                                        {{ basename($agence->logo) }}
                                    </span>
                                </span>
                            @endif
                        </div>
                        <input type="file" name="logo" id="logoInput" accept="image/*" hidden>
                    </label>
                    @error('logo') <small class="err">{{ $message }}</small> @enderror

                    {{-- Informations --}}
                    <div class="ios-section-title">
                        <i class="fa-regular fa-building"></i> Informations
                    </div>

                    <div class="ios-form-grid">
                        <div class="ios-list__item--field">
                            <div class="ios-list__content">
                                <label>Nom de l'agence <span class="req">*</span></label>
                                <input type="text" name="nom_agence"
                                       value="{{ old('nom_agence', $agence->nom_agence) }}"
                                       class="ios-input" required>
                            </div>
                        </div>

                        <div class="ios-list__item--field">
                            <div class="ios-list__content">
                                <label>Quartier <span class="req">*</span></label>
                                <input type="text" name="quartier"
                                       value="{{ old('quartier', $agence->quartier) }}"
                                       class="ios-input" required>
                            </div>
                        </div>

                        <div class="ios-list__item--field" style="grid-column: 1 / -1;">
                            <div class="ios-list__content">
                                <label>Adresse <span class="req">*</span></label>
                                <input type="text" name="adresse"
                                       value="{{ old('adresse', $agence->adresse) }}"
                                       class="ios-input" required>
                            </div>
                        </div>
                    </div>

                    @error('nom_agence') <small class="err">{{ $message }}</small> @enderror
                    @error('quartier') <small class="err">{{ $message }}</small> @enderror
                    @error('adresse') <small class="err">{{ $message }}</small> @enderror

                    {{-- Description --}}
                    <div class="ios-section-title">
                        <i class="fa-regular fa-message"></i> Description
                    </div>

                    <div class="ios-field">
                        <textarea name="description" rows="4"
                                  class="ios-input"
                                  placeholder="Décrivez votre agence...">{{ old('description', $agence->description) }}</textarea>
                        @error('description') <small class="err">{{ $message }}</small> @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="ios-form-actions">
                        <button type="submit" class="ios-btn ios-btn--primary">
                            <i class="fa-solid fa-save"></i> Enregistrer
                        </button>
                    </div>
                </form>
            </div>

            {{-- Colonne latérale : Stats + Déconnexion --}}
            <aside class="profil-layout__side">
                {{-- Statistiques --}}
                <div class="ios-section-title">
                    <i class="fa-solid fa-chart-simple"></i> Mon activité
                </div>

                <div class="profil-stats">
                    <div class="profil-stat">
                        <span class="profil-stat__value">{{ $agence->biens->count() ?? 0 }}</span>
                        <span class="profil-stat__label">Biens publiés</span>
                    </div>
                    <div class="profil-stat">
                        <span class="profil-stat__value">{{ $besoinsDisponibles ?? 0 }}</span>
                        <span class="profil-stat__label">Besoins dispo.</span>
                    </div>
                    <div class="profil-stat">
                        <span class="profil-stat__value">{{ $rendezvousAVenir ?? 0 }}</span>
                        <span class="profil-stat__label">RDV à venir</span>
                    </div>
                </div>

                {{-- Déconnexion --}}
                <div class="ios-section-title">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Compte
                </div>

                <div class="ios-list--danger">
                    <button type="button"
                            class="ios-list__item--action"
                            onclick="openLogoutModal()">
                        <div class="ios-list__icon ios-list__icon--danger">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </div>
                        <div class="ios-list__content">
                            <span class="ios-list__label">Se déconnecter</span>
                            <span class="ios-list__sublabel">Fermer la session sur cet appareil</span>
                        </div>
                        <i class="fa-solid fa-chevron-right ios-list__chevron"></i>
                    </button>
                </div>
            </aside>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         TAB 2 — DISPONIBILITÉS
    ═══════════════════════════════════════════ --}}
    <div id="tab-creneaux" class="profil-content {{ $ongletActif === 'creneaux' ? 'is-active' : '' }}">

        {{-- Légende --}}
        <div class="legend-bar">
            <span class="legend-item">
                <span class="legend-dot legend-dot--disponible"></span> Disponible
            </span>
            <span class="legend-item">
                <span class="legend-dot legend-dot--indisponible"></span> Indisponible
            </span>
            <span class="legend-item">
                <span class="legend-dot legend-dot--today"></span> Aujourd'hui
            </span>
            <span class="legend-item">
                <span class="legend-dot legend-dot--past"></span> Passé
            </span>
        </div>

        {{-- Planning type --}}
        @if(!empty($planningParJour))
            <div class="planning-card planning-card--active">
                <div class="planning-card__head">
                    <div class="planning-card__title">
                        <i class="fa-solid fa-arrows-rotate"></i>
                        Planning type actif
                    </div>
                    <form action="{{ route('agence.creneaux.generer-auto') }}" method="POST">
                        @csrf
                        <button type="submit" class="planning-card__btn">
                            <i class="fa-solid fa-rotate"></i> Régénérer
                        </button>
                    </form>
                </div>
                <div class="planning-card__days">
                    @foreach($joursFr as $index => $jour)
                        @if(isset($planningParJour[$index]))
                            @php
                                $text = [];
                                foreach($planningParJour[$index] as $p) {
                                    $text[] = substr($p['heure_debut'], 0, 5) . '-' . substr($p['heure_fin'], 0, 5);
                                }
                            @endphp
                            <span class="planning-day">
                                <strong>{{ $jour }}</strong>
                                {{ implode(' · ', $text) }}
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>
        @else
            <div class="planning-card planning-card--empty">
                <div class="planning-card__head">
                    <div class="planning-card__title">
                        <i class="fa-solid fa-info-circle"></i>
                        Aucun planning type
                    </div>
                </div>
                <p class="planning-card__text">
                    Utilisez le bouton <strong>"Générer des créneaux"</strong> pour créer votre planning.
                    Les créneaux seront ensuite générés automatiquement chaque semaine.
                </p>
            </div>
        @endif

        {{-- Header section --}}
        <div class="section-header">
            <span>Semaine en cours</span>
            <button type="button" class="section-header__cta" onclick="openModal()">
                <i class="fa-solid fa-plus"></i> Générer
            </button>
        </div>

        {{-- Grille créneaux --}}
        @php
            $joursMap = [
                'Monday'    => 'Lundi',
                'Tuesday'   => 'Mardi',
                'Wednesday' => 'Mercredi',
                'Thursday'  => 'Jeudi',
                'Friday'    => 'Vendredi',
                'Saturday'  => 'Samedi',
                'Sunday'    => 'Dimanche',
            ];
            $aujourdhui = \Carbon\Carbon::today()->format('Y-m-d');
        @endphp

        @if(count($semaine) > 0)
            <div class="slots-grid">
                @foreach($semaine as $date)
                    @php
                        $dateObj = \Carbon\Carbon::parse($date);
                        $jourSemaine = $dateObj->format('l');
                        $estAujourdhui = $date === $aujourdhui;
                        $estPasse = $dateObj->isPast() && !$estAujourdhui;
                        $creneauxDuJour = isset($creneauxParJour[$date]) ? $creneauxParJour[$date] : collect();
                    @endphp

                    <div class="slot-card {{ $estAujourdhui ? 'is-today' : '' }} {{ $estPasse ? 'is-past' : '' }}">
                        <header class="slot-card__head">
                            <div class="slot-card__date">
                                <span class="slot-card__day">{{ $joursMap[$jourSemaine] ?? $jourSemaine }}</span>
                                <span class="slot-card__num">{{ $dateObj->format('d/m') }}</span>
                            </div>
                            @if($estAujourdhui)
                                <span class="slot-badge slot-badge--today">Aujourd'hui</span>
                            @elseif($estPasse)
                                <span class="slot-badge slot-badge--past">Passé</span>
                            @else
                                <span class="slot-badge slot-badge--count">
                                    {{ $creneauxDuJour->count() }} créneau{{ $creneauxDuJour->count() > 1 ? 'x' : '' }}
                                </span>
                            @endif
                        </header>

                        <div class="slot-card__body">
                            @if($creneauxDuJour->count() > 0)
                                @foreach($creneauxDuJour as $creneau)
                                    <div class="slot-row {{ $creneau->est_disponible ? 'is-available' : 'is-unavailable' }}">
                                        <div class="slot-row__time">
                                            {{ substr($creneau->heure_debut, 0, 5) }}
                                            <span class="slot-row__sep">–</span>
                                            {{ substr($creneau->heure_fin, 0, 5) }}
                                        </div>

                                        @if(!$estPasse)
                                            <div class="slot-row__actions">
                                                <form action="{{ route('agence.creneaux.toggle', $creneau) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="slot-action slot-action--toggle" title="Basculer">
                                                        <i class="fa-solid {{ $creneau->est_disponible ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('agence.creneaux.supprimer', $creneau) }}" method="POST"
                                                      onsubmit="return confirm('Supprimer ce créneau ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="slot-action slot-action--delete" title="Supprimer">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="slot-row__status">
                                                <i class="fa-solid {{ $creneau->est_disponible ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="slot-empty">
                                    <i class="fa-regular fa-clock"></i>
                                    <span>Aucun créneau</span>
                                </div>
                            @endif
                        </div>

                        @if($creneauxDuJour->count() > 0 && !$estPasse)
                            <footer class="slot-card__foot">
                                <form action="{{ route('agence.creneaux.supprimerDatePost') }}" method="POST"
                                      onsubmit="return confirm('Supprimer tous les créneaux de cette date ?')">
                                    @csrf
                                    <input type="hidden" name="date" value="{{ $date }}">
                                    <button type="submit" class="slot-card__delete">
                                        <i class="fa-solid fa-trash-can"></i> Tout supprimer
                                    </button>
                                </form>
                            </footer>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="ios-empty">
                <div class="ios-empty__icon">
                    <i class="fa-regular fa-calendar"></i>
                </div>
                <h4>Aucune semaine disponible</h4>
                <p>Générez des créneaux pour commencer à recevoir des rendez-vous.</p>
                <button type="button" class="ios-btn ios-btn--primary" onclick="openModal()">
                    <i class="fa-solid fa-plus"></i> Générer des créneaux
                </button>
            </div>
        @endif
    </div>

</div>

{{-- ═══ MODAL — GÉNÉRER DES CRÉNEAUX ═══ --}}
<div class="modal" id="modalCreneaux">
    <div class="modal__overlay" onclick="closeModal()"></div>
    <div class="modal__content">
        <header class="modal__head">
            <h3>
                <i class="fa-solid fa-calendar-plus"></i>
                Générer des créneaux
            </h3>
            <button type="button" class="modal__close" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <form method="POST" action="{{ route('agence.creneaux.generer') }}" class="modal__body">
            @csrf

            <div class="ios-section-title">
                <i class="fa-solid fa-calendar-days"></i> Jours
            </div>

            <div class="days-grid">
                @foreach(['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'] as $jour)
                    <label class="check-card">
                        <input type="checkbox" name="jours[]" value="{{ $jour }}" checked>
                        <span>{{ $jour }}</span>
                    </label>
                @endforeach
            </div>

            <div class="ios-section-title">
                <i class="fa-regular fa-clock"></i> Plages horaires
            </div>

            <div class="hours-grid">
                @foreach(['09:00','10:00','11:00','14:00','15:00','16:00','17:00'] as $heure)
                    @php
                        $heureFin = \Carbon\Carbon::parse($heure)->addHour()->format('H:i');
                    @endphp
                    <label class="check-card">
                        <input type="checkbox" name="heures[]" value="{{ $heure }}" checked>
                        <span>{{ $heure }} – {{ $heureFin }}</span>
                    </label>
                @endforeach
            </div>

            <div class="planning-toggle">
                <label>
                    <input type="checkbox" name="est_recurrent" value="1" checked>
                    <span>
                        <strong>Sauvegarder comme planning type</strong>
                        <small>Génération automatique chaque semaine</small>
                    </span>
                </label>
            </div>

            <div class="modal__actions">
                <button type="button" class="ios-btn ios-btn--ghost" onclick="closeModal()">
                    Annuler
                </button>
                <button type="submit" class="ios-btn ios-btn--primary">
                    <i class="fa-solid fa-check"></i> Générer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ═══ MODAL — DÉCONNEXION ═══ --}}
<div class="modal" id="modalLogout">
    <div class="modal__overlay" onclick="closeLogoutModal()"></div>
    <div class="modal__content modal__content--sheet">
        <header class="modal__head">
            <h3>
                <i class="fa-solid fa-arrow-right-from-bracket" style="color:#C62828;"></i>
                Se déconnecter
            </h3>
            <button type="button" class="modal__close" onclick="closeLogoutModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </header>

        <div class="modal__body">
            <p class="logout-confirm-text">
                Voulez-vous vraiment vous déconnecter de votre compte DoyaImmo ?
            </p>
            <div class="modal__actions">
                <button type="button" class="ios-btn ios-btn--ghost" onclick="closeLogoutModal()">
                    Annuler
                </button>
                <form method="POST" action="{{ route('logout') }}" style="display:contents;">
                    @csrf
                    <button type="submit" class="ios-btn ios-btn--danger">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    /* ═══ ONGLET via URL ═══ */
    const params = new URLSearchParams(window.location.search);
    const onglet = params.get('onglet');
    if (onglet === 'infos' || onglet === 'creneaux') switchTab(onglet);

    /* ═══ APERÇU LOGO ═══ */
    const logoInput = document.getElementById('logoInput');
    const preview = document.getElementById('logoPreview');
    if (logoInput && preview) {
        logoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML = `<img src="${e.target.result}" alt="Logo">`;
            };
            reader.readAsDataURL(file);
        });
    }

    /* ═══ SOUMISSION ═══ */
    document.getElementById('profilForm')?.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement...';
            btn.disabled = true;
        }
    });
});

/* ═══ SWITCH TABS ═══ */
function switchTab(tab) {
    document.querySelectorAll('.profil-content').forEach(el => el.classList.remove('is-active'));
    document.querySelectorAll('.profil-tab').forEach(el => el.classList.remove('is-active'));

    document.getElementById('tab-' + tab)?.classList.add('is-active');
    document.querySelector(`.profil-tab[data-tab="${tab}"]`)?.classList.add('is-active');

    const url = new URL(window.location);
    url.searchParams.set('onglet', tab);
    window.history.pushState({}, '', url);
}

/* ═══ MODAL CRÉNEAUX ═══ */
function openModal() {
    document.getElementById('modalCreneaux').classList.add('is-active');
    document.body.style.overflow = 'hidden';
}
function closeModal() {
    document.getElementById('modalCreneaux').classList.remove('is-active');
    document.body.style.overflow = '';
}

/* ═══ MODAL DÉCONNEXION ═══ */
function openLogoutModal() {
    document.getElementById('modalLogout').classList.add('is-active');
    document.body.style.overflow = 'hidden';
}
function closeLogoutModal() {
    document.getElementById('modalLogout').classList.remove('is-active');
    document.body.style.overflow = '';
}

/* ═══ ESC key ═══ */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        closeModal();
        closeLogoutModal();
    }
});
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PROFIL AGENCE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.agence-profil {
    --c-success: #1E7A47; --c-success-bg: #E8F5E9;
    --c-warning: #E65100; --c-warning-bg: #FFF8E1;
    --c-danger: #C62828; --c-danger-bg: #FFEBEE;
    --c-info: #0D47A1; --c-info-bg: #E3F2FD;
    --c-gold: #F5A623; --c-gold-bg: #FFF8E1;
    --surface: #F7F9FC;
    --radius: 14px;

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ HEADER DESKTOP ═══ */
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}
.page-header__left h2 {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
    line-height: 1.2;
}
.page-header__left p {
    font-size: 13.5px;
    color: var(--muted);
    margin: 4px 0 0;
}

/* ═══ HERO AGENCE ═══ */
.profil-hero {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 24px 28px;
    background: linear-gradient(135deg, #FFF9F5 0%, #fff 60%);
    border: 1px solid var(--border);
    border-radius: 16px;
    margin-bottom: 22px;
}
.profil-hero__avatar {
    width: 76px;
    height: 76px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 26px;
    font-weight: 700;
    flex-shrink: 0;
    overflow: hidden;
    box-shadow: 0 6px 16px rgba(181, 80, 42, .25);
}
.profil-hero__avatar img { width: 100%; height: 100%; object-fit: cover; }
.profil-hero__body { min-width: 0; flex: 1; }
.profil-hero__name {
    font-family: var(--display);
    font-size: 24px;
    font-weight: 700;
    color: var(--ink);
    margin: 0 0 10px;
    letter-spacing: -.4px;
}
.profil-hero__badges {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 10px;
}
.profil-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11.5px;
    font-weight: 700;
}
.profil-badge i { font-size: 10px; }
.profil-badge--success { background: var(--c-success-bg); color: var(--c-success); }
.profil-badge--warning { background: var(--c-warning-bg); color: var(--c-warning); }
.profil-badge--gold { background: var(--c-gold-bg); color: #B26A00; }
.profil-badge--gold i { color: var(--c-gold); }
.profil-badge__muted { opacity: .7; font-weight: 500; }
.profil-hero__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    color: var(--muted);
}
.profil-hero__meta i { color: var(--rust); font-size: 12px; margin-right: 4px; }
.profil-hero__dot { opacity: .5; }

/* ═══ TABS ═══ */
.profil-tabs {
    display: flex;
    gap: 6px;
    padding: 4px;
    background: #EFEFF4;
    border-radius: 12px;
    margin-bottom: 22px;
    overflow-x: auto;
    scrollbar-width: none;
    max-width: 500px;
}
.profil-tabs::-webkit-scrollbar { display: none; }
.profil-tab {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px 20px;
    background: transparent;
    border: none;
    border-radius: 9px;
    font-family: inherit;
    font-size: 14px;
    font-weight: 600;
    color: var(--text-soft);
    cursor: pointer;
    transition: all .2s;
    white-space: nowrap;
}
.profil-tab i { font-size: 13px; }
.profil-tab:hover { color: var(--ink); }
.profil-tab.is-active {
    background: #fff;
    color: var(--ink);
    box-shadow: 0 2px 8px rgba(0,0,0,.08);
}
.profil-tab.is-active i { color: var(--rust); }

/* ═══ CONTENT ═══ */
.profil-content { display: none; }
.profil-content.is-active { display: block; }

/* ═══ LAYOUT 2 COLONNES (desktop) ═══ */
.profil-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 24px;
    align-items: start;
}
.profil-layout__main { min-width: 0; }
.profil-layout__side { min-width: 0; }
.profil-layout__side .ios-section-title:first-child { margin-top: 0; }

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    margin: 24px 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    text-transform: uppercase;
    letter-spacing: .4px;
}
.ios-section-title:first-child { margin-top: 0; }
.ios-section-title i { color: var(--rust); font-size: 13px; }

/* ═══ LOGO CARD ═══ */
.logo-card {
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 20px;
    background: #fff;
    border: 1.5px dashed var(--border);
    border-radius: 16px;
    cursor: pointer;
    transition: all .2s;
    margin-bottom: 16px;
}
.logo-card:hover { border-color: var(--rust); background: #FFF9F5; }
.logo-card__preview {
    width: 80px;
    height: 80px;
    border-radius: 14px;
    overflow: hidden;
    background: var(--surface);
    border: 1px solid var(--border);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
.logo-card__preview img { width: 100%; height: 100%; object-fit: cover; }
.logo-card__placeholder { color: var(--muted); font-size: 26px; opacity: .4; }
.logo-card__info { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 4px; }
.logo-card__title {
    font-family: var(--display);
    font-size: 14.5px;
    font-weight: 700;
    color: var(--ink);
}
.logo-card__title i { color: var(--rust); margin-right: 6px; }
.logo-card__hint { font-size: 12.5px; color: var(--muted); }
.logo-card__current {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    color: var(--c-success);
    margin-top: 2px;
    min-width: 0;
}
.logo-card__current i { font-size: 10px; flex-shrink: 0; }

/* ✅ Nom du fichier logo tronqué */
.logo-card__filename {
    display: inline-block;
    max-width: 220px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    vertical-align: bottom;
}

/* ═══ FORM GRID (desktop) ═══ */
.ios-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 16px;
}

.ios-list__item--field { display: flex; flex-direction: column; gap: 6px; }
.ios-list__content { display: flex; flex-direction: column; gap: 6px; }
.ios-list__content label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-soft);
}
.ios-list__content label .req { color: #C62828; }

/* ═══ FIELDS ═══ */
.ios-field { margin-bottom: 16px; }
.ios-input {
    width: 100%;
    padding: 13px 16px;
    border: 1px solid var(--border);
    border-radius: 12px;
    font-size: 14.5px;
    font-family: inherit;
    background: #fff;
    color: var(--ink);
    transition: all .15s;
    -webkit-appearance: none;
    appearance: none;
}
.ios-input:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 3px var(--rust-soft);
}
textarea.ios-input { resize: vertical; min-height: 120px; line-height: 1.6; }

.err {
    display: block;
    color: #C62828;
    font-size: 12.5px;
    margin: 4px 0 12px 4px;
    font-weight: 500;
}

/* ═══ STATS (sidebar) ═══ */
.profil-stats {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 16px;
}
.profil-stat {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 16px;
    background: var(--surface);
    border-radius: 12px;
}
.profil-stat__value {
    font-family: var(--display);
    font-size: 22px;
    font-weight: 800;
    color: var(--ink);
    line-height: 1;
    letter-spacing: -.4px;
}
.profil-stat__label {
    font-size: 13px;
    color: var(--muted);
    font-weight: 500;
    order: -1;
}

/* ═══ ACTIONS ═══ */
.ios-form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    margin-top: 8px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 13px 22px;
    border-radius: 12px;
    font-size: 14.5px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
}
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181,80,42,.2);
}
.ios-btn--primary:hover { background: #9A4523; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(181,80,42,.3); }
.ios-btn--primary:active { transform: scale(.97); }
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
}
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--danger {
    background: var(--c-danger);
    color: #fff;
    border-color: var(--c-danger);
    box-shadow: 0 4px 12px rgba(198, 40, 40, .25);
}
.ios-btn--danger:hover {
    background: #A61F1F;
    border-color: #A61F1F;
    transform: translateY(-1px);
}
.ios-btn--danger:active { transform: scale(.97); }

/* ═══ LISTE DANGER (déconnexion) ═══ */
.ios-list--danger {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 16px;
}
.ios-list__item--action {
    display: flex;
    align-items: center;
    gap: 14px;
    width: 100%;
    padding: 16px 18px;
    background: transparent;
    border: none;
    font-family: inherit;
    cursor: pointer;
    text-align: left;
    transition: background .15s;
    -webkit-tap-highlight-color: transparent;
}
.ios-list__item--action:hover { background: #FAFBFC; }
.ios-list__item--action:active { background: #F2F2F7; }
.ios-list__icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    flex-shrink: 0;
    color: #fff;
}
.ios-list__icon--danger {
    background: linear-gradient(135deg, #FF3B30, #C62828);
    box-shadow: 0 3px 8px rgba(255, 59, 48, .25);
}
.ios-list__content { flex: 1; min-width: 0; }
.ios-list__label {
    font-size: 15px;
    font-weight: 600;
    color: var(--c-danger);
    letter-spacing: -.2px;
    display: block;
}
.ios-list__sublabel {
    font-size: 12.5px;
    color: var(--muted);
    display: block;
    margin-top: 2px;
    letter-spacing: -.1px;
}
.ios-list__chevron {
    color: var(--muted);
    font-size: 13px;
    flex-shrink: 0;
    opacity: .6;
}

/* ═══ LÉGENDE ═══ */
.legend-bar {
    display: flex;
    gap: 20px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    margin-bottom: 18px;
    flex-wrap: wrap;
}
.legend-item {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    color: var(--text-soft);
    font-weight: 500;
}
.legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.legend-dot--disponible { background: var(--c-success); }
.legend-dot--indisponible { background: var(--c-danger); }
.legend-dot--today { background: var(--ios-blue); box-shadow: 0 0 0 3px rgba(0,122,255,.2); }
.legend-dot--past { background: #E8ECF0; border: 1px dashed #B0B8C4; }

/* ═══ PLANNING CARD ═══ */
.planning-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 18px 22px;
    margin-bottom: 18px;
}
.planning-card--active {
    background: linear-gradient(135deg, #F0FBF4 0%, #fff 60%);
    border-color: #C8E6C9;
}
.planning-card--empty {
    background: linear-gradient(135deg, #FFF9F0 0%, #fff 60%);
    border-color: #FFE0B2;
}
.planning-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}
.planning-card__title {
    font-family: var(--display);
    font-size: 14.5px;
    font-weight: 700;
    color: var(--c-success);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.planning-card--empty .planning-card__title { color: var(--c-warning); }
.planning-card__title i { font-size: 13px; }
.planning-card__btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: var(--c-success);
    color: #fff;
    border: none;
    border-radius: 9px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background .2s;
}
.planning-card__btn:hover { background: #175C35; }
.planning-card__days { display: flex; flex-wrap: wrap; gap: 8px; }
.planning-day {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    background: #fff;
    border: 1px solid #C8E6C9;
    border-radius: 9px;
    font-size: 12.5px;
    color: var(--text-soft);
}
.planning-day strong { color: var(--ink); font-weight: 700; font-size: 12px; }
.planning-card__text {
    font-size: 13.5px;
    color: var(--text-soft);
    line-height: 1.55;
    margin: 0;
}
.planning-card__text strong { color: var(--c-warning); }

/* ═══ SECTION HEADER (semaine) ═══ */
.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin: 22px 0 14px;
    padding: 0 2px;
    flex-wrap: wrap;
}
.section-header span {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
}
.section-header__cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 16px;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    border: none;
    border-radius: 10px;
    font-family: inherit;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 10px rgba(181,80,42,.25);
    transition: transform .15s, box-shadow .15s;
}
.section-header__cta:hover { transform: translateY(-1px); box-shadow: 0 6px 14px rgba(181,80,42,.3); }
.section-header__cta:active { transform: scale(.96); }

/* ═══ GRID SLOTS ═══ */
.slots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 14px;
}
.slot-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    transition: all .2s;
}
.slot-card:hover { box-shadow: 0 6px 18px rgba(0,0,0,.06); }
.slot-card.is-today {
    border-color: var(--ios-blue);
    box-shadow: 0 0 0 2px rgba(0,122,255,.15);
}
.slot-card.is-past { opacity: .55; background: #FAFBFC; }
.slot-card__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border);
    background: #FAFBFC;
}
.slot-card.is-today .slot-card__head {
    background: linear-gradient(135deg, #E8F0FE 0%, #FAFBFC 60%);
    border-bottom-color: rgba(0,122,255,.2);
}
.slot-card__date { display: flex; align-items: baseline; gap: 8px; min-width: 0; }
.slot-card__day {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    color: var(--ink);
}
.slot-card__num { font-size: 12.5px; color: var(--muted); }
.slot-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.slot-badge--today { background: var(--ios-blue); color: #fff; }
.slot-badge--past { background: var(--border); color: var(--muted); }
.slot-badge--count {
    background: var(--surface);
    color: var(--muted);
    font-weight: 600;
    text-transform: none;
    letter-spacing: 0;
}
.slot-card__body {
    padding: 12px 16px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    max-height: 280px;
    overflow-y: auto;
}
.slot-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 13.5px;
}
.slot-row.is-available {
    background: rgba(30, 122, 71, .06);
    border: 1px solid rgba(30, 122, 71, .15);
}
.slot-row.is-unavailable {
    background: rgba(198, 40, 40, .06);
    border: 1px solid rgba(198, 40, 40, .15);
    opacity: .8;
}
.slot-row__time {
    font-family: var(--display);
    font-weight: 700;
    color: var(--ink);
    letter-spacing: -.2px;
}
.slot-row__sep { color: var(--muted); margin: 0 2px; font-weight: 400; }
.slot-row__actions { display: flex; align-items: center; gap: 4px; }
.slot-row__actions form { margin: 0; }
.slot-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    background: transparent;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 16px;
    transition: all .15s;
    -webkit-tap-highlight-color: transparent;
}
.slot-action--toggle { color: var(--c-success); }
.slot-row.is-unavailable .slot-action--toggle { color: var(--c-danger); }
.slot-action--delete { color: var(--muted); opacity: .6; }
.slot-action:hover { background: rgba(0,0,0,.05); opacity: 1; }
.slot-action:active { transform: scale(.9); }
.slot-row__status { font-size: 15px; color: var(--muted); }
.slot-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 28px 16px;
    color: var(--muted);
    font-size: 13px;
}
.slot-empty i { font-size: 24px; opacity: .4; }
.slot-card__foot {
    padding: 12px 16px;
    border-top: 1px solid var(--border);
    background: #FAFBFC;
    text-align: right;
}
.slot-card__delete {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    background: transparent;
    color: var(--c-danger);
    border: 1px solid #FFCDD2;
    border-radius: 9px;
    font-family: inherit;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
}
.slot-card__delete:hover {
    background: var(--c-danger-bg);
    border-color: var(--c-danger);
}

/* ═══ EMPTY iOS ═══ */
.ios-empty {
    background: #fff;
    border: 1px dashed var(--border);
    border-radius: 16px;
    padding: 48px 24px;
    text-align: center;
}
.ios-empty__icon {
    width: 68px; height: 68px;
    margin: 0 auto 16px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: var(--surface);
    color: var(--muted);
    font-size: 26px;
    opacity: .7;
}
.ios-empty h4 {
    font-family: var(--display);
    font-size: 16px; font-weight: 700;
    color: var(--ink);
    margin: 0 0 6px;
}
.ios-empty p {
    font-size: 13.5px;
    color: var(--muted);
    margin: 0 auto 20px;
    max-width: 360px;
    line-height: 1.5;
}

/* ═══ MODAL ═══ */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.modal.is-active { display: flex; }
.modal__overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.5);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
}
.modal__content {
    position: relative;
    background: #fff;
    border-radius: 18px;
    max-width: 560px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 24px 64px rgba(0,0,0,.25);
    animation: modalIn .25s ease;
}
.modal__content--sheet { max-width: 460px; }
@keyframes modalIn {
    from { opacity: 0; transform: scale(.96) translateY(10px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.modal__head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 20px 24px;
    border-bottom: 1px solid var(--border);
    position: sticky; top: 0;
    background: #fff;
    z-index: 1;
    border-radius: 18px 18px 0 0;
}
.modal__head h3 {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
.modal__head h3 i { color: var(--rust); font-size: 16px; }
.modal__close {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #F2F2F7;
    border: none;
    cursor: pointer;
    font-size: 16px;
    color: var(--muted);
    transition: all .15s;
}
.modal__close:hover { background: #E5E5EA; color: var(--ink); }
.modal__body { padding: 22px 24px; }

.days-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    margin-bottom: 20px;
}
.hours-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin-bottom: 20px;
}
.check-card {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 12px;
    background: #fff;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-soft);
    cursor: pointer;
    transition: all .15s;
    user-select: none;
}
.check-card:hover { border-color: var(--rust); }
.check-card:has(input:checked) {
    background: var(--rust-soft);
    border-color: var(--rust);
    color: var(--rust);
    font-weight: 600;
}
.check-card input {
    accent-color: var(--rust);
    width: 15px; height: 15px;
    cursor: pointer;
}
.planning-toggle {
    padding: 14px 16px;
    background: #F0FBF4;
    border: 1px solid #C8E6C9;
    border-radius: 12px;
    margin-bottom: 20px;
}
.planning-toggle label {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
}
.planning-toggle input {
    accent-color: var(--c-success);
    width: 16px; height: 16px;
    margin-top: 2px;
    cursor: pointer;
}
.planning-toggle span { display: flex; flex-direction: column; gap: 2px; }
.planning-toggle strong {
    font-family: var(--display);
    font-size: 13.5px;
    font-weight: 700;
    color: var(--c-success);
}
.planning-toggle small {
    font-size: 12px;
    color: var(--text-soft);
    line-height: 1.4;
}
.modal__actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    padding-top: 16px;
    border-top: 1px solid var(--border);
}

.logout-confirm-text {
    font-size: 14.5px;
    line-height: 1.55;
    color: var(--text-soft);
    margin: 0 0 20px;
    text-align: center;
    letter-spacing: -.1px;
}

/* ═══════════════════════════════════════════════════════════
   DESKTOP : conteneur large
   ═══════════════════════════════════════════════════════════ */
@media (min-width: 1000px) {
    .agence-profil {
        max-width: 1100px;
        margin: 0 auto;
        padding: 8px 0 60px;
    }
}

@media (max-width: 999px) and (min-width: 821px) {
    .profil-layout {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    .profil-layout__side {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .agence-profil {
        max-width: 720px;
        margin: 0 auto;
        padding: 8px 0 40px;
    }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .agence-profil {
        margin: 0 -16px;
        padding: 0 16px 40px;
        max-width: none;
    }

    /* HEADER desktop masqué */
    .page-header { display: none; }

    /* Layout → 1 colonne */
    .profil-layout {
        grid-template-columns: 1fr;
        gap: 0;
    }
    .profil-layout__main,
    .profil-layout__side {
        display: contents;
    }

    /* ✅ Espace pour le bouton fixe en bas */
    .profil-form {
        padding-bottom: 100px;
    }

    /* Form grid → 1 colonne */
    .ios-form-grid {
        grid-template-columns: 1fr;
        padding: 16px;
        gap: 14px;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .profil-hero {
        padding: 18px;
        gap: 14px;
    }
    .profil-hero__avatar { width: 60px; height: 60px; font-size: 20px; }
    .profil-hero__name { font-size: 19px; margin-bottom: 6px; }

    .profil-tabs { max-width: none; }

    .ios-section-title {
        text-transform: none;
        letter-spacing: 0;
        font-size: 13px;
        color: var(--ios-label-3);
        margin: 22px 4px 8px;
    }

    .logo-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .logo-card:hover { background: var(--ios-card); border: none; }
    .logo-card:active { background: #F5F5F7; }
    .logo-card__preview { width: 64px; height: 64px; border-radius: 12px; }

    /* Nom du fichier logo tronqué sur mobile */
    .logo-card__filename {
        max-width: 140px;
        font-size: 11.5px;
    }

    .ios-list__content label {
        font-size: 12px;
        text-transform: none;
        letter-spacing: 0;
        color: var(--ios-label-3);
        font-weight: 500;
    }

    .ios-input {
        background: #EFEFF4;
        border: none;
        font-size: 16px;
        padding: 12px 14px;
        border-radius: 10px;
    }
    .ios-input:focus {
        background: #fff;
        box-shadow: 0 0 0 3px var(--rust-soft);
    }

    /* Stats iOS */
    .profil-stats {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        padding: 14px;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
    }
    .profil-stat {
        flex-direction: column;
        align-items: center;
        padding: 12px 6px;
        background: #EFEFF4;
        gap: 4px;
    }
    .profil-stat__label { order: 0; font-size: 11.5px; }
    .profil-stat__value { font-size: 20px; }

    .legend-bar {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }

    .planning-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        padding: 16px;
    }
    .planning-card--active { background: linear-gradient(135deg, #F0FBF4 0%, #fff 60%); }
    .planning-card--empty { background: linear-gradient(135deg, #FFF9F0 0%, #fff 60%); }

    .slots-grid { grid-template-columns: 1fr; gap: 10px; }

    .slot-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .slot-card__head {
        background: transparent;
        border-bottom: 0.5px solid var(--ios-separator);
        padding: 14px 16px;
    }
    .slot-card.is-today .slot-card__head {
        background: linear-gradient(135deg, #E8F0FE 0%, transparent 60%);
    }
    .slot-row {
        border: none;
        background: #F5F5F7;
        padding: 12px 14px;
    }
    .slot-row.is-available { background: linear-gradient(135deg, #E8F5E9 0%, #F5F5F7 100%); }
    .slot-row.is-unavailable { background: #FFEBEE; }
    .slot-action { width: 38px; height: 38px; font-size: 18px; }
    .slot-card__foot {
        background: transparent;
        border-top: 0.5px solid var(--ios-separator);
    }

    /* Actions : fixe en bas, safe-area */
    .ios-form-actions {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(242, 242, 247, 0.95);
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        border-top: 0.5px solid var(--ios-separator);
        padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
        margin: 0;
        gap: 10px;
        z-index: 100;
        border-radius: 0;
    }
    .ios-form-actions .ios-btn {
        flex: 1;
        padding: 14px 18px;
        font-size: 15px;
        border-radius: 14px;
    }
    .ios-btn {
        padding: 14px 18px;
        font-size: 15px;
        border-radius: 14px;
        letter-spacing: -.1px;
    }
    .ios-btn--primary {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }

    .ios-list--danger {
        background: var(--ios-card);
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        border-radius: var(--ios-radius-lg);
    }
    .ios-list__item--action {
        padding: 16px;
        gap: 14px;
    }
    .ios-list__item--action:active { background: #F5F5F7; }
    .ios-list__icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
    }
    .ios-list__label {
        font-size: 16px;
        color: var(--c-danger);
    }
    .ios-list__sublabel {
        font-size: 13px;
        color: var(--ios-label-3);
    }

    .modal {
        padding: 0;
        align-items: flex-end;
    }
    .modal__content {
        border-radius: 20px 20px 0 0;
        max-height: 92vh;
        padding-bottom: env(safe-area-inset-bottom, 0px);
        animation: sheetIn .3s cubic-bezier(.4,0,.2,1);
    }
    .modal__content--sheet { max-width: 100%; width: 100%; }
    @keyframes sheetIn {
        from { transform: translateY(100%); }
        to { transform: translateY(0); }
    }
    .modal__head {
        border-radius: 20px 20px 0 0;
        padding: 18px 20px;
    }
    .modal__head h3 { font-size: 16px; }
    .modal__body { padding: 18px 20px; }

    .days-grid { grid-template-columns: repeat(4, 1fr); gap: 6px; }
    .hours-grid { grid-template-columns: 1fr; gap: 6px; }
    .check-card { padding: 12px 8px; font-size: 13px; }
    .modal__actions { flex-direction: column-reverse; gap: 10px; }
    .modal__actions .ios-btn { width: 100%; padding: 14px 18px; font-size: 15px; }
    .ios-btn--danger {
        background: linear-gradient(135deg, #FF3B30, #C62828);
        border: none;
    }

    .logout-confirm-text {
        font-size: 14px;
        color: var(--ios-label-2);
    }
}

@media (max-width: 480px) {
    .profil-hero { padding: 16px; gap: 12px; }
    .profil-hero__avatar { width: 54px; height: 54px; font-size: 18px; }
    .profil-hero__name { font-size: 18px; }
    .profil-hero__badges { gap: 4px; }
    .profil-badge { font-size: 10.5px; padding: 2px 8px; }

    .profil-tab { font-size: 13px; padding: 9px 12px; }
    .ios-section-title { font-size: 12px; }
    .logo-card__preview { width: 56px; height: 56px; }
    .logo-card__title { font-size: 13px; }
    .logo-card__hint { font-size: 11.5px; }

    .logo-card__filename {
        max-width: 100px;
        font-size: 11px;
    }

    .profil-stat__value { font-size: 18px; }
    .profil-stat__label { font-size: 10.5px; }
    .section-header span { font-size: 14px; }
}

@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
@endpush