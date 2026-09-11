@extends('layouts.dashboard')

@section('title', 'Signaler une proposition — DoyaImmo')
@section('page_title', 'Signaler une proposition')
@section('page_sub', 'Signalez une proposition inappropriée ou frauduleuse')

@section('content')
<div class="view active signalement-page">

    @php
        $statutValue = is_object($proposition->statut) ? $proposition->statut->value : $proposition->statut;
        $statutLabel = is_object($proposition->statut) ? $proposition->statut->label() : ucfirst($proposition->statut);
        $agence      = $proposition->agence;
        $initial     = strtoupper(mb_substr($agence->nom_agence ?? 'A', 0, 1));
    @endphp

    {{-- ═══════════════════════════════════════════
         RETOUR
    ═══════════════════════════════════════════ --}}
    <div class="signalement-page__back">
        <a href="{{ route('particulier.propositions.index') }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour aux propositions
        </a>
    </div>

    {{-- ═══════════════════════════════════════════
         AVERTISSEMENT
    ═══════════════════════════════════════════ --}}
    <div class="warning-banner">
        <div class="warning-banner__icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="warning-banner__content">
            <strong>Signaler est une action importante</strong>
            <p>Votre signalement sera examiné par un administrateur. Merci de fournir des informations précises et honnêtes.</p>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════
         PROPOSITION SIGNALÉE
    ═══════════════════════════════════════════ --}}
    <section class="target-card">
        <header class="target-card__head">
            <span class="target-label">
                <i class="fa-solid fa-flag"></i>
                Proposition signalée
            </span>
        </header>

        <div class="target-body">
            <div class="target-avatar">{{ $initial }}</div>

            <div class="target-info">
                <h3 class="target-title">{{ $agence->nom_agence ?? 'Agence' }}</h3>
                <span class="target-sub">
                    {{ is_object($proposition->demande->type_bien)
                        ? $proposition->demande->type_bien->label()
                        : $proposition->demande->type_bien }}
                    <span class="target-sep">·</span>
                    {{ $proposition->demande->zone_recherchee }}
                </span>
            </div>

            <div class="target-price">
                {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                <small>FCFA</small>
            </div>

            <span class="target-status target-status--{{ $statutValue }}">
                <i class="fa-solid fa-circle"></i>
                {{ $statutLabel }}
            </span>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════
         FORMULAIRE
    ═══════════════════════════════════════════ --}}
    <form method="POST"
          action="{{ route('particulier.signalements.store') }}"
          class="signalement-form">

        @csrf
        <input type="hidden" name="signalable_id" value="{{ $proposition->id }}">
        <input type="hidden" name="signalable_type" value="App\Models\Proposition">

        {{-- Motif --}}
        <section class="form-section">
            <header class="form-section__head">
                <span class="form-section__icon">
                    <i class="fa-solid fa-circle-question"></i>
                </span>
                <h4>Motif du signalement <span class="required">*</span></h4>
            </header>

            <div class="motifs-list">
                @foreach($motifs as $key => $label)
                    <label class="motif-option {{ old('motif') == $key ? 'is-selected' : '' }}">
                        <input type="radio"
                               name="motif"
                               value="{{ $key }}"
                               {{ old('motif') == $key ? 'checked' : '' }}
                               required>
                        <span class="motif-option__radio"></span>
                        <span class="motif-option__label">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            @error('motif')
                <span class="form-error">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                </span>
            @enderror
        </section>

        {{-- Description --}}
        <section class="form-section">
            <header class="form-section__head">
                <span class="form-section__icon">
                    <i class="fa-regular fa-message"></i>
                </span>
                <h4>Description détaillée <span class="required">*</span></h4>
            </header>

            <textarea name="description"
                      id="description"
                      rows="5"
                      maxlength="500"
                      placeholder="Décrivez précisément le problème que vous avez rencontré (minimum 10 caractères)..."
                      class="form-textarea @error('description') is-invalid @enderror"
                      required>{{ old('description') }}</textarea>

            <div class="form-textarea-foot">
                <span class="form-hint">
                    <i class="fa-regular fa-lightbulb"></i>
                    Soyez précis : dates, montants, faits observés.
                </span>
                <span class="form-counter" id="descCounter">0 / 500</span>
            </div>

            @error('description')
                <span class="form-error">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                </span>
            @enderror
        </section>

        {{-- Actions --}}
        <footer class="form-actions">
            <a href="{{ route('particulier.propositions.show', $proposition) }}"
               class="btn btn-ghost form-actions__cancel">
                <i class="fa-solid fa-times"></i> Annuler
            </a>

            <button type="submit" class="btn btn-danger form-actions__submit">
                <i class="fa-solid fa-flag"></i> Envoyer le signalement
            </button>
        </footer>
    </form>
</div>
@endsection


@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ─── Motifs cliquables : refléter le radio dans la classe ───
        const motifs = document.querySelectorAll('.motif-option');
        motifs.forEach(opt => {
            const radio = opt.querySelector('input[type="radio"]');
            if (radio.checked) opt.classList.add('is-selected');

            radio.addEventListener('change', () => {
                motifs.forEach(o => o.classList.remove('is-selected'));
                opt.classList.add('is-selected');
            });
        });

        // ─── Compteur de caractères ───
        const textarea = document.getElementById('description');
        const counter  = document.getElementById('descCounter');
        if (textarea && counter) {
            const update = () => {
                counter.textContent = textarea.value.length + ' / 500';
                counter.classList.toggle('is-warning', textarea.value.length > 450);
            };
            textarea.addEventListener('input', update);
            update();
        }
    });
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   PAGE SIGNALEMENT — Particulier
   ═══════════════════════════════════════════════════════════ */

.signalement-page {
    --c-warning:    #E65100;
    --c-warning-bg: #FFF8E1;
    --c-success:    #1E7A47;
    --c-success-bg: #E8F5E9;
    --c-danger:     #C62828;
    --c-danger-bg:  #FFEBEE;
    --c-info:       #0D47A1;
    --c-info-bg:    #E3F2FD;
    --surface:      #F7F9FC;
    --radius:       14px;
}

.signalement-page__back { margin-bottom: 16px; }

/* ═══════════════════════════════════════════
   AVERTISSEMENT
   ═══════════════════════════════════════════ */
.warning-banner {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 16px 20px;
    border-radius: var(--radius);
    background: linear-gradient(135deg, #FFF4E5 0%, #FFF9F0 100%);
    border: 1px solid #FFE0B2;
    margin-bottom: 16px;
}
.warning-banner__icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    background: rgba(230, 81, 0, .12);
    color: var(--c-warning);
}
.warning-banner__content strong {
    display: block;
    font-size: 14px;
    font-weight: 700;
    color: var(--c-warning);
    margin-bottom: 3px;
}
.warning-banner__content p {
    font-size: 13px;
    line-height: 1.55;
    color: #8B4B15;
    margin: 0;
}

/* ═══════════════════════════════════════════
   PROPOSITION SIGNALÉE
   ═══════════════════════════════════════════ */
.target-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 20px;
    margin-bottom: 16px;
}
.target-card__head {
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px dashed var(--border);
}
.target-label {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    color: var(--c-danger);
}
.target-label i { font-size: 11px; }

.target-body {
    display: flex;
    align-items: center;
    gap: 14px;
    flex-wrap: wrap;
}

.target-avatar {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 18px;
    font-weight: 700;
    flex-shrink: 0;
}

.target-info {
    flex: 1;
    min-width: 160px;
}
.target-title {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0 0 3px;
    color: var(--text);
}
.target-sub {
    font-size: 12.5px;
    color: var(--muted);
}
.target-sep { margin: 0 5px; opacity: .5; }

.target-price {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    flex-shrink: 0;
}
.target-price small {
    font-size: 11px;
    font-weight: 600;
    opacity: .7;
    margin-left: 2px;
}

.target-status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 11px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .4px;
    white-space: nowrap;
    flex-shrink: 0;
}
.target-status i { font-size: 5px; }
.target-status--en_attente { background: var(--c-warning-bg); color: var(--c-warning); }
.target-status--acceptee   { background: var(--c-success-bg); color: var(--c-success); }
.target-status--refusee    { background: var(--c-danger-bg);  color: var(--c-danger); }
.target-status--terminee   { background: var(--c-info-bg);    color: var(--c-info); }

/* ═══════════════════════════════════════════
   FORMULAIRE
   ═══════════════════════════════════════════ */
.signalement-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.form-section {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 18px 22px;
}

.form-section__head {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border);
}
.form-section__icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 9px;
    background: var(--c-danger-bg);
    color: var(--c-danger);
    font-size: 14px;
    flex-shrink: 0;
}
.form-section__head h4 {
    font-family: var(--display);
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    color: var(--text);
}
.required { color: var(--c-danger); margin-left: 2px; }

/* ═══ MOTIFS (radios stylisées) ═══ */
.motifs-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.motif-option {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    background: #fff;
    cursor: pointer;
    transition: all .2s ease;
    font-size: 13.5px;
    color: var(--text);
    font-weight: 500;
}
.motif-option:hover {
    border-color: var(--c-danger);
    background: #FFF9F9;
}
.motif-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}
.motif-option__radio {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 2px solid var(--border);
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all .2s ease;
    position: relative;
}
.motif-option__radio::after {
    content: '';
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--c-danger);
    transform: scale(0);
    transition: transform .2s ease;
}
.motif-option__label {
    flex: 1;
    min-width: 0;
}

/* État sélectionné */
.motif-option.is-selected {
    border-color: var(--c-danger);
    background: var(--c-danger-bg);
    color: var(--c-danger);
    font-weight: 600;
}
.motif-option.is-selected .motif-option__radio {
    border-color: var(--c-danger);
}
.motif-option.is-selected .motif-option__radio::after {
    transform: scale(1);
}

/* Focus clavier */
.motif-option input[type="radio"]:focus-visible ~ .motif-option__radio {
    outline: 3px solid rgba(198, 40, 40, .25);
    outline-offset: 2px;
}

/* ═══ TEXTAREA ═══ */
.form-textarea {
    width: 100%;
    padding: 12px 14px;
    border: 1.5px solid var(--border);
    border-radius: 10px;
    font-size: 13.5px;
    font-family: inherit;
    line-height: 1.6;
    resize: vertical;
    min-height: 110px;
    color: var(--text);
    background: #fff;
    transition: border .2s, box-shadow .2s;
}
.form-textarea:focus {
    outline: none;
    border-color: var(--c-danger);
    box-shadow: 0 0 0 3px rgba(198, 40, 40, .1);
}
.form-textarea.is-invalid {
    border-color: var(--c-danger);
}

.form-textarea-foot {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-top: 8px;
    flex-wrap: wrap;
}
.form-hint {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    color: var(--muted);
}
.form-hint i { color: #F5A623; font-size: 12px; }

.form-counter {
    font-family: var(--display);
    font-size: 11.5px;
    font-weight: 700;
    color: var(--muted);
    padding: 3px 10px;
    background: var(--surface);
    border-radius: 999px;
}
.form-counter.is-warning {
    color: var(--c-danger);
    background: var(--c-danger-bg);
}

/* ═══ ERREURS ═══ */
.form-error {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    font-size: 12px;
    font-weight: 500;
    color: var(--c-danger);
}
.form-error i { font-size: 12px; }

/* ═══ ACTIONS ═══ */
.form-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 14px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    flex-wrap: wrap;
}
.form-actions__cancel { margin-right: auto; }

/* ═══ BOUTONS ═══ */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
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
.btn-danger {
    background: var(--c-danger);
    color: #fff;
    border-color: var(--c-danger);
}
.btn-danger:hover {
    background: #A31F1F;
    color: #fff;
    border-color: #A31F1F;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(198, 40, 40, .3);
}
.btn-sm { padding: 6px 13px; font-size: 12px; }

/* ═══════════════════════════════════════════════════════════
   RESPONSIVE
   ═══════════════════════════════════════════════════════════ */

@media (max-width: 640px) {
    .warning-banner { padding: 14px 16px; gap: 12px; }
    .warning-banner__icon { width: 34px; height: 34px; font-size: 15px; }

    .target-card { padding: 14px 16px; }
    .target-body { gap: 12px; }
    .target-price { width: 100%; text-align: left; }
    .target-status { order: -1; }

    .form-section { padding: 16px; }
    .form-section__head h4 { font-size: 14px; }

    .motif-option { padding: 11px 14px; font-size: 13px; }

    .form-actions { flex-direction: column-reverse; align-items: stretch; }
    .form-actions__cancel,
    .form-actions__submit { width: 100%; justify-content: center; margin-right: 0; }
}

@media (max-width: 480px) {
    .target-avatar { width: 40px; height: 40px; font-size: 15px; }
    .target-title { font-size: 14px; }
    .target-price { font-size: 15px; }

    .form-section { padding: 14px; }
    .motif-option { font-size: 12.5px; gap: 10px; }
    .motif-option__radio { width: 18px; height: 18px; }
    .motif-option__radio::after { width: 8px; height: 8px; }

    .form-textarea { font-size: 13px; padding: 10px 12px; }

    .btn { font-size: 12px; padding: 8px 14px; }
}
</style>
@endpush