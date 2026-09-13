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

    {{-- ═══ AVERTISSEMENT ═══ --}}
    <div class="warning-banner">
        <div class="warning-banner__icon">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="warning-banner__content">
            <strong>Signaler est une action importante</strong>
            <p>Votre signalement sera examiné par un administrateur. Merci de fournir des informations précises et honnêtes.</p>
        </div>
    </div>

    {{-- ═══ PROPOSITION SIGNALÉE ═══ --}}
    <div class="ios-section-title">
        <i class="fa-solid fa-flag"></i> Proposition signalée
    </div>

    <section class="target-card">
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
            <div class="target-price">
                {{ number_format($proposition->prix_propose, 0, ',', ' ') }}
                <small>FCFA</small>
            </div>
        </div>

        <span class="target-status target-status--{{ $statutValue }}">
            <i class="fa-solid fa-circle"></i>
            {{ $statutLabel }}
        </span>
    </section>

    {{-- ═══ FORMULAIRE ═══ --}}
    <form method="POST"
          action="{{ route('particulier.signalements.store') }}"
          class="signalement-form"
          id="signalementForm">

        @csrf
        <input type="hidden" name="signalable_id" value="{{ $proposition->id }}">
        <input type="hidden" name="signalable_type" value="App\Models\Proposition">

        {{-- Motif --}}
        <div class="ios-section-title">
            <i class="fa-solid fa-circle-question"></i> Motif <span class="req">*</span>
        </div>
        <p class="ios-section-sub">
            Sélectionnez la raison principale de votre signalement
        </p>

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
                    <i class="fa-solid fa-check motif-option__check"></i>
                </label>
            @endforeach
        </div>

        @error('motif')
            <small class="err">
                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
            </small>
        @enderror

        {{-- Description --}}
        <div class="ios-section-title">
            <i class="fa-regular fa-message"></i> Description <span class="req">*</span>
        </div>
        <p class="ios-section-sub">
            Décrivez précisément le problème rencontré
        </p>

        <div class="ios-field">
            <textarea name="description"
                      id="description"
                      rows="5"
                      maxlength="500"
                      placeholder="Décrivez précisément le problème que vous avez rencontré (minimum 10 caractères)..."
                      class="ios-input @error('description') is-invalid @enderror"
                      required>{{ old('description') }}</textarea>

            <div class="ios-counter">
                <span>
                    <i class="fa-regular fa-lightbulb"></i>
                    Soyez précis : dates, montants, faits observés.
                </span>
                <span id="descCounter">0 / 500</span>
            </div>

            @error('description')
                <small class="err">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                </small>
            @enderror
        </div>

        {{-- Actions fixes --}}
        <div class="ios-form-actions">
            <a href="{{ route('particulier.propositions.show', $proposition) }}"
               class="ios-btn ios-btn--ghost">
                Annuler
            </a>
            <button type="submit" class="ios-btn ios-btn--danger" id="submitBtn">
                <i class="fa-solid fa-flag"></i> Envoyer
            </button>
        </div>
    </form>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ═══ MOTIFS ═══ */
    const motifs = document.querySelectorAll('.motif-option');
    motifs.forEach(opt => {
        const radio = opt.querySelector('input[type="radio"]');
        if (radio.checked) opt.classList.add('is-selected');

        radio.addEventListener('change', () => {
            motifs.forEach(o => o.classList.remove('is-selected'));
            opt.classList.add('is-selected');

            // Haptic
            if (navigator.vibrate) navigator.vibrate(8);
        });
    });

    /* ═══ COMPTEUR ═══ */
    const textarea = document.getElementById('description');
    const counter  = document.getElementById('descCounter');

    if (textarea && counter) {
        const update = () => {
            const len = textarea.value.length;
            counter.textContent = len + ' / 500';
            counter.classList.toggle('is-warning', len > 450);
        };
        textarea.addEventListener('input', update);
        update();
    }

    /* ═══ SOUMISSION ═══ */
    document.getElementById('signalementForm')?.addEventListener('submit', function () {
        const btn = document.getElementById('submitBtn');
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Envoi...';
            btn.disabled = true;
        }
    });
});
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   SIGNALEMENT — iOS NATIVE
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

    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
}

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 22px 0 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ios-section-title:first-of-type { margin-top: 0; }
.ios-section-title i { color: var(--c-danger); font-size: 15px; }
.ios-section-title .req { color: #C62828; }

.ios-section-sub {
    font-size: 13px;
    color: var(--muted);
    margin: 0 0 14px;
    line-height: 1.5;
}

/* ═══ AVERTISSEMENT ═══ */
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

/* ═══ PROPOSITION SIGNALÉE ═══ */
.target-card {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 20px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    margin-bottom: 14px;
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

.target-info { flex: 1; min-width: 160px; }
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
    display: block;
}
.target-sep { margin: 0 5px; opacity: .5; }

.target-price {
    font-family: var(--display);
    font-size: 17px;
    font-weight: 800;
    color: var(--rust);
    white-space: nowrap;
    display: block;
    margin-top: 4px;
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

/* ═══ MOTIFS ═══ */
.motifs-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 14px;
}

.motif-option {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border: 1.5px solid var(--border);
    border-radius: 12px;
    background: #fff;
    cursor: pointer;
    transition: all .2s ease;
    font-size: 13.5px;
    color: var(--text);
    font-weight: 500;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}
.motif-option:hover {
    border-color: var(--c-danger);
    background: #FFF9F9;
}
.motif-option:active {
    transform: scale(.98);
}
.motif-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.motif-option__radio {
    width: 22px;
    height: 22px;
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

.motif-option__check {
    color: var(--c-danger);
    font-size: 14px;
    opacity: 0;
    transform: scale(.7);
    transition: all .2s ease;
    flex-shrink: 0;
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
.motif-option.is-selected .motif-option__check {
    opacity: 1;
    transform: scale(1);
}

/* ═══ FIELDS ═══ */
.ios-field { margin-bottom: 18px; }

.ios-input {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--border);
    border-radius: 12px;
    font-size: 14px;
    font-family: inherit;
    background: #fff;
    color: var(--ink);
    transition: all .15s;
    -webkit-appearance: none;
    appearance: none;
    line-height: 1.6;
}
.ios-input:focus {
    outline: none;
    border-color: var(--c-danger);
    box-shadow: 0 0 0 3px rgba(198, 40, 40, .1);
}
.ios-input.is-invalid {
    border-color: var(--c-danger);
}
textarea.ios-input {
    resize: vertical;
    min-height: 120px;
}

.ios-counter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    color: var(--muted);
    margin-top: 6px;
    padding-left: 4px;
    gap: 12px;
}
.ios-counter span:first-child {
    display: inline-flex;
    gap: 5px;
    align-items: center;
}
.ios-counter span:first-child i { font-size: 11px; color: #F5A623; }
.ios-counter span:last-child {
    font-family: var(--display);
    font-weight: 700;
    padding: 2px 10px;
    background: var(--surface);
    border-radius: 999px;
    white-space: nowrap;
}
.ios-counter span:last-child.is-warning {
    color: var(--c-danger);
    background: var(--c-danger-bg);
}

.err {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #C62828;
    font-size: 12px;
    margin-top: 6px;
    font-weight: 500;
    padding-left: 4px;
}
.err i { font-size: 12px; }

/* ═══ ACTIONS ═══ */
.ios-form-actions {
    display: flex;
    gap: 10px;
    padding-top: 20px;
    margin-top: 4px;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
}
.ios-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 600;
    font-family: inherit;
    cursor: pointer;
    border: 1px solid transparent;
    text-decoration: none;
    transition: all .15s;
    white-space: nowrap;
    flex: 1;
}
.ios-btn--danger {
    background: var(--c-danger);
    color: #fff;
    border-color: var(--c-danger);
    box-shadow: 0 4px 12px rgba(198, 40, 40, .2);
}
.ios-btn--danger:hover {
    background: #A31F1F;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(198, 40, 40, .3);
}
.ios-btn--danger:active { transform: scale(.97); }
.ios-btn--danger:disabled {
    opacity: .7;
    cursor: not-allowed;
    transform: none;
}
.ios-btn--ghost {
    background: #fff;
    color: var(--text-soft);
    border-color: var(--border);
    flex: 0 0 auto;
    padding: 12px 18px;
}
.ios-btn--ghost:hover {
    border-color: var(--rust);
    color: var(--rust);
}
.ios-btn--ghost:active { transform: scale(.97); }

/* ═══ RESPONSIVE DESKTOP ═══ */
@media (max-width: 640px) {
    .warning-banner { padding: 14px 16px; gap: 12px; }
    .warning-banner__icon { width: 34px; height: 34px; font-size: 15px; }
    .target-card { padding: 14px 16px; }
    .target-price { margin-top: 6px; }
}


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .signalement-page { background: transparent; padding-top: 4px; }

    /* ─── AVERTISSEMENT iOS ─── */
    .warning-banner {
        background: linear-gradient(135deg, #FFF4E5 0%, #FFE8D9 100%);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        gap: 12px;
        margin-bottom: 16px;
    }
    .warning-banner__icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #FF9800, #E65100);
        color: #fff;
        font-size: 17px;
        box-shadow: 0 4px 10px rgba(230, 81, 0, .25);
    }
    .warning-banner__content strong {
        font-size: 15px;
        font-weight: 700;
    }
    .warning-banner__content p {
        font-size: 13px;
        color: #8B4B15;
        line-height: 1.5;
    }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 6px;
    }
    .ios-section-title i { font-size: 15px; color: var(--c-danger); }
    .ios-section-sub {
        font-size: 13px;
        color: var(--ios-label-3);
        margin: 0 4px 14px;
    }

    /* ─── CARTE PROPOSITION iOS ─── */
    .target-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 16px;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        gap: 12px;
        align-items: flex-start;
        position: relative;
        overflow: hidden;
    }
    /* Bande rouge en haut */
    .target-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--c-danger), #E53935);
        border-radius: var(--ios-radius-lg) var(--ios-radius-lg) 0 0;
    }

    .target-avatar {
        width: 48px;
        height: 48px;
        font-size: 19px;
        box-shadow: 0 4px 10px rgba(181, 80, 42, .2);
    }
    .target-info { min-width: 0; }
    .target-title {
        font-size: 16px;
        font-weight: 700;
        letter-spacing: -.2px;
        color: var(--ios-label);
        margin-bottom: 4px;
    }
    .target-sub {
        font-size: 13px;
        color: var(--ios-label-3);
    }
    .target-price {
        font-size: 22px;
        font-weight: 800;
        letter-spacing: -.5px;
        color: var(--rust);
        margin-top: 8px;
    }
    .target-price small {
        font-size: 13px;
        margin-left: 3px;
    }
    .target-status {
        position: absolute;
        top: 16px;
        right: 16px;
        font-size: 10.5px;
        padding: 4px 10px;
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0;
    }

    /* ─── MOTIFS iOS ─── */
    .motifs-list {
        gap: 10px;
        margin-bottom: 16px;
    }

    .motif-option {
        background: var(--ios-card);
        border: 1.5px solid transparent;
        border-radius: 14px;
        padding: 16px;
        font-size: 15px;
        font-weight: 500;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        gap: 14px;
        color: var(--ios-label);
    }
    .motif-option:hover {
        border-color: transparent;
        background: var(--ios-card);
    }
    .motif-option:active {
        transform: scale(.98);
    }

    .motif-option__radio {
        width: 24px;
        height: 24px;
        border: 2px solid #C7C7CC;
    }
    .motif-option__radio::after {
        width: 12px;
        height: 12px;
        background: linear-gradient(135deg, var(--c-danger), #E53935);
    }

    .motif-option__label {
        font-size: 15px;
        font-weight: 500;
        letter-spacing: -.2px;
        line-height: 1.35;
    }

    .motif-option__check { display: none; }

    /* Sélection iOS */
    .motif-option.is-selected {
        background: linear-gradient(135deg, #FFF5F5, #FFE8E8);
        border-color: var(--c-danger);
        color: var(--ios-label);
        font-weight: 600;
        box-shadow: 0 4px 14px rgba(198, 40, 40, .12);
    }
    .motif-option.is-selected .motif-option__radio {
        border-color: var(--c-danger);
    }
    .motif-option.is-selected .motif-option__radio::after {
        transform: scale(1);
    }

    /* ─── FIELDS iOS (16px anti-zoom) ─── */
    .ios-field { margin-bottom: 16px; }
    .ios-input {
        background: var(--ios-card);
        border: none;
        border-radius: 14px;
        padding: 14px 16px;
        font-size: 16px;
        font-weight: 500;
        color: var(--ios-label);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .ios-input:focus {
        box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 0 0 3px rgba(198, 40, 40, .15);
    }
    textarea.ios-input {
        min-height: 140px;
        padding: 16px;
        font-size: 16px;
        line-height: 1.55;
    }

    .ios-counter {
        font-size: 12px;
        padding-left: 4px;
        margin-top: 8px;
    }
    .ios-counter span:last-child {
        padding: 3px 11px;
        font-size: 12px;
    }

    .err {
        font-size: 12.5px;
        margin-top: 6px;
        padding-left: 4px;
    }

    /* ─── BARRE ACTIONS FIXE ─── */
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
        display: flex;
    }
    .ios-btn {
        padding: 14px 20px;
        font-size: 15px;
        font-weight: 600;
        border-radius: 14px;
        flex: 1;
        letter-spacing: -.1px;
    }
    .ios-btn--danger {
        background: linear-gradient(135deg, var(--c-danger), #E53935);
        border: none;
        box-shadow: 0 4px 12px rgba(198, 40, 40, .3);
    }
    .ios-btn--danger:active {
        transform: scale(.97);
        box-shadow: 0 2px 6px rgba(198, 40, 40, .4);
    }
    .ios-btn--ghost {
        background: var(--ios-card);
        border: none;
        color: var(--ios-label-2);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        flex: 0 0 auto;
        padding: 14px 18px;
    }
    .ios-btn--ghost:active { background: #EFEFF4; }

    /* Padding pour ne pas cacher sous la barre fixe */
    .signalement-page {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }
}

@media (max-width: 480px) {
    .warning-banner { padding: 14px; }
    .warning-banner__content strong { font-size: 14px; }
    .warning-banner__content p { font-size: 12.5px; }

    .target-avatar { width: 44px; height: 44px; font-size: 17px; }
    .target-title { font-size: 15px; }
    .target-price { font-size: 20px; }
    .target-price small { font-size: 12px; }

    .ios-section-title { font-size: 16px; }
    .motif-option { padding: 14px; font-size: 14.5px; }
    .motif-option__label { font-size: 14.5px; }
}
</style>
@endpush