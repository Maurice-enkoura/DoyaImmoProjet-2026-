@extends('layouts.dashboard')

@section('title', 'Modifier mon avis — DoyaImmo')
@section('page_title', 'Modifier mon avis')
@section('page_sub', 'Modifiez votre évaluation de l\'agence')

@section('content')
<div class="view active eval-edit">

    @php
        $labels = [
            1 => 'Très insatisfait 😞',
            2 => 'Insatisfait 😕',
            3 => 'Moyen 😐',
            4 => 'Satisfait 😊',
            5 => 'Très satisfait 🤩'
        ];
    @endphp

    {{-- ═══ HERO AGENCE ═══ --}}
    <div class="eval-hero">
        <div class="eval-hero__avatar">
            {{ strtoupper(mb_substr($evaluation->agence->nom_agence ?? 'A', 0, 1)) }}
        </div>
        <div class="eval-hero__body">
            <h1 class="eval-hero__name">{{ $evaluation->agence->nom_agence }}</h1>
            <div class="eval-hero__sub">
                <i class="fa-solid fa-pen-to-square"></i>
                Modifier mon évaluation
            </div>
        </div>
    </div>

    {{-- ═══ PROPOSITION RAPPEL ═══ --}}
    @if($evaluation->proposition)
        <div class="ios-section-title">
            <i class="fa-regular fa-file-lines"></i> Proposition concernée
        </div>

        <div class="ios-list">
            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Date</span>
                </div>
                <span class="ios-list__value">
                    {{ $evaluation->proposition->created_at->format('d/m/Y') }}
                </span>
            </div>

            <div class="ios-list__item ios-list__item--static">
                <div class="ios-list__content">
                    <span class="ios-list__title">Prix proposé</span>
                </div>
                <span class="ios-list__value ios-list__value--rust">
                    {{ number_format($evaluation->proposition->prix_propose, 0, ',', ' ') }} FCFA
                </span>
            </div>

            @if($evaluation->proposition->bien)
                <div class="ios-list__item ios-list__item--static">
                    <div class="ios-list__content">
                        <span class="ios-list__title">Bien</span>
                    </div>
                    <span class="ios-list__value">
                        {{ $evaluation->proposition->bien->titre ?? 'Non spécifié' }}
                    </span>
                </div>
            @endif
        </div>
    @endif

    <form method="POST" action="{{ route('particulier.evaluations.update', $evaluation) }}" id="evalForm">
        @csrf
        @method('PUT')

        {{-- ═══ NOTE ═══ --}}
        <div class="ios-section-title">
            <i class="fa-solid fa-star"></i> Votre note <span class="req">*</span>
        </div>

        <div class="rating-card">
            <div class="stars-input" id="starRating">
                @for($i = 1; $i <= 5; $i++)
                    <button type="button" class="star-btn {{ $i <= $evaluation->note ? 'active' : '' }}"
                            data-value="{{ $i }}"
                            onclick="setNote({{ $i }})"
                            aria-label="{{ $i }} étoile{{ $i > 1 ? 's' : '' }}">
                        <i class="fa-solid fa-star"></i>
                    </button>
                @endfor
            </div>
            <input type="hidden" name="note" id="note" value="{{ old('note', $evaluation->note) }}" required>

            <div class="note-text is-set" id="noteText">
                {{ $labels[$evaluation->note] ?? 'Sélectionnez une note' }}
            </div>
        </div>
        @error('note') <small class="err">{{ $message }}</small> @enderror

        {{-- ═══ COMMENTAIRE ═══ --}}
        <div class="ios-section-title">
            <i class="fa-regular fa-message"></i> Votre commentaire <span class="req">*</span>
        </div>

        <div class="ios-field">
            <textarea name="commentaire" id="commentaire" rows="5"
                      placeholder="Partagez votre expérience avec cette agence..."
                      class="ios-input" required>{{ old('commentaire', $evaluation->commentaire) }}</textarea>
            @error('commentaire') <small class="err">{{ $message }}</small> @enderror

            <div class="ios-counter">
                <span><i class="fa-regular fa-info-circle"></i> Minimum 10 caractères</span>
                <span id="charCount">0 / 10</span>
            </div>
        </div>

        {{-- ═══ ACTIONS ═══ --}}
        <div class="ios-form-actions">
            <a href="{{ route('particulier.evaluations.index') }}" class="ios-btn ios-btn--ghost">
                Annuler
            </a>
            <button type="submit" class="ios-btn ios-btn--primary" id="submitBtn">
                <i class="fa-solid fa-save"></i> Enregistrer
            </button>
        </div>
    </form>
</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    /* ═══ ÉTOILES ═══ */
    const stars = document.querySelectorAll('#starRating .star-btn');
    const input = document.getElementById('note');
    const noteText = document.getElementById('noteText');

    const labels = {
        1: 'Très insatisfait 😞',
        2: 'Insatisfait 😕',
        3: 'Moyen 😐',
        4: 'Satisfait 😊',
        5: 'Très satisfait 🤩'
    };

    window.setNote = function(value) {
        input.value = value;

        // Haptic feedback mobile
        if (navigator.vibrate) navigator.vibrate(10);

        stars.forEach(s => {
            const starValue = parseInt(s.dataset.value);
            s.classList.toggle('active', starValue <= value);
            s.classList.remove('hover');
        });

        noteText.textContent = labels[value] || 'Sélectionnez une note';
        noteText.style.color = value >= 4 ? '#1E7A47' : value >= 3 ? '#F5A623' : '#C62828';
        noteText.classList.add('is-set');
    };

    // Hover desktop
    stars.forEach(star => {
        star.addEventListener('mouseenter', function() {
            const value = parseInt(this.dataset.value);
            stars.forEach(s => {
                s.classList.toggle('hover', parseInt(s.dataset.value) <= value);
            });
        });

        star.addEventListener('mouseleave', function() {
            stars.forEach(s => s.classList.remove('hover'));
        });
    });

    // Init si note déjà sélectionnée
    const currentNote = parseInt(input.value) || 0;
    if (currentNote > 0) {
        noteText.style.color = currentNote >= 4 ? '#1E7A47' : currentNote >= 3 ? '#F5A623' : '#C62828';
    }


    /* ═══ COMPTEUR ═══ */
    const textarea = document.getElementById('commentaire');
    const charCount = document.getElementById('charCount');

    if (textarea && charCount) {
        function updateCharCount() {
            const len = textarea.value.length;
            charCount.textContent = len + ' / 10';
            charCount.style.color = len >= 10 ? '#1E7A47' : '#C62828';
            charCount.style.fontWeight = '700';
        }
        textarea.addEventListener('input', updateCharCount);
        updateCharCount();
    }


    /* ═══ SOUMISSION ═══ */
    document.getElementById('evalForm')?.addEventListener('submit', function(e) {
        const noteValue = parseInt(input.value) || 0;
        if (noteValue === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner une note');
            return;
        }

        const btn = document.getElementById('submitBtn');
        if (btn) {
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement...';
            btn.disabled = true;
        }
    });

});
</script>
@endpush


@push('styles')
<style>
/* ═══════════════════════════════════════════════════════════
   MODIFIER MON AVIS — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */

.eval-edit {
    --ios-bg: #F2F2F7;
    --ios-card: #FFFFFF;
    --ios-blue: #007AFF;
    --ios-label: #000000;
    --ios-label-2: #3C3C43;
    --ios-label-3: #8E8E93;
    --ios-separator: rgba(60, 60, 67, 0.12);
    --ios-radius-lg: 18px;
    --c-gold: #F5A623;
}

/* ═══ HERO AGENCE ═══ */
.eval-hero {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 22px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    margin-bottom: 20px;
}
.eval-hero__avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rust), #d4754a);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--display);
    font-size: 22px;
    font-weight: 700;
    flex-shrink: 0;
    box-shadow: 0 6px 16px rgba(180, 83, 42, .2);
}
.eval-hero__body { min-width: 0; flex: 1; }
.eval-hero__name {
    font-family: var(--display);
    font-size: 19px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--ink);
    line-height: 1.2;
}
.eval-hero__sub {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: var(--muted);
}
.eval-hero__sub i { font-size: 12px; color: var(--rust); }

/* ═══ SECTION TITLES ═══ */
.ios-section-title {
    font-family: var(--display);
    font-size: 16px;
    font-weight: 700;
    color: var(--ink);
    margin: 22px 0 10px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ios-section-title:first-of-type { margin-top: 0; }
.ios-section-title i { color: var(--rust); font-size: 15px; }
.ios-section-title .req { color: #C62828; }

/* ═══ LISTE iOS ═══ */
.ios-list {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 14px;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
}
.ios-list__item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    font-size: 13.5px;
}
.ios-list__item--static { cursor: default; }
.ios-list__item:last-child { border-bottom: none; }
.ios-list__content { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.ios-list__title {
    font-size: 12.5px;
    color: var(--muted);
    font-weight: 500;
}
.ios-list__value {
    font-size: 14px;
    font-weight: 700;
    color: var(--ink);
    text-align: right;
    flex-shrink: 0;
}
.ios-list__value--rust { color: var(--rust); }

/* ═══ RATING CARD ═══ */
.rating-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 24px 20px;
    text-align: center;
    margin-bottom: 14px;
}

.stars-input {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 12px;
}

.star-btn {
    background: none;
    border: none;
    font-size: 38px;
    cursor: pointer;
    color: #D4D8E0;
    transition: all .15s ease;
    padding: 4px;
    -webkit-tap-highlight-color: transparent;
    line-height: 1;
    font-family: inherit;
}
.star-btn:hover { transform: scale(1.12); }
.star-btn:active { transform: scale(.9); }
.star-btn.active,
.star-btn.hover { color: var(--c-gold); }
.star-btn i { pointer-events: none; }

.note-text {
    font-size: 14px;
    font-weight: 500;
    color: var(--muted);
    transition: color .3s;
}
.note-text.is-set { font-weight: 600; }

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
}
.ios-input:focus {
    outline: none;
    border-color: var(--rust);
    box-shadow: 0 0 0 3px var(--rust-soft);
}
textarea.ios-input {
    resize: vertical;
    min-height: 120px;
    line-height: 1.6;
}

.ios-counter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11.5px;
    color: var(--muted);
    margin-top: 6px;
    padding-left: 4px;
}
.ios-counter span:first-child {
    display: inline-flex;
    gap: 5px;
    align-items: center;
}
.ios-counter span:first-child i { font-size: 11px; }

.err {
    display: block;
    color: #C62828;
    font-size: 12px;
    margin-top: 5px;
    font-weight: 500;
    padding-left: 4px;
}

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
.ios-btn--primary {
    background: var(--rust);
    color: #fff;
    border-color: var(--rust);
    box-shadow: 0 4px 12px rgba(181, 80, 42, .2);
}
.ios-btn--primary:hover { background: #9A4523; transform: translateY(-1px); }
.ios-btn--primary:active { transform: scale(.97); }
.ios-btn--primary:disabled {
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
.ios-btn--ghost:hover { border-color: var(--rust); color: var(--rust); }
.ios-btn--ghost:active { transform: scale(.97); }


/* ═══════════════════════════════════════════════════════════
   📱 MOBILE — iOS NATIVE
   ═══════════════════════════════════════════════════════════ */
@media (max-width: 820px) {

    .eval-edit { background: transparent; padding-top: 4px; }

    /* ─── HERO iOS ─── */
    .eval-hero {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 18px;
        margin-bottom: 16px;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .eval-hero__avatar {
        width: 56px;
        height: 56px;
        font-size: 22px;
    }
    .eval-hero__name {
        font-size: 20px;
        letter-spacing: -.3px;
        color: var(--ios-label);
        font-weight: 700;
    }
    .eval-hero__sub { font-size: 14px; color: var(--ios-label-3); }
    .eval-hero__sub i { color: var(--rust); font-size: 13px; }

    /* ─── SECTION TITLES iOS ─── */
    .ios-section-title {
        font-size: 17px;
        font-weight: 700;
        color: var(--ios-label);
        letter-spacing: -.3px;
        margin: 22px 4px 10px;
    }
    .ios-section-title i { font-size: 15px; color: var(--rust); }

    /* ─── LISTE iOS ─── */
    .ios-list {
        display: block;
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        margin-bottom: 14px;
    }
    .ios-list__item {
        padding: 14px 16px;
        gap: 12px;
        border-bottom: 0.5px solid var(--ios-separator);
        font-size: 15px;
    }
    .ios-list__item:last-child { border-bottom: none; }
    .ios-list__title {
        font-size: 15px;
        color: var(--ios-label);
        font-weight: 500;
        letter-spacing: -.2px;
    }
    .ios-list__value {
        font-size: 15px;
        font-weight: 600;
        color: var(--ios-label-3);
        text-align: right;
    }
    .ios-list__value--rust { color: var(--rust); }

    /* ─── RATING CARD iOS ─── */
    .rating-card {
        background: var(--ios-card);
        border: none;
        border-radius: var(--ios-radius-lg);
        padding: 28px 20px;
        margin-bottom: 14px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .stars-input { gap: 6px; margin-bottom: 16px; }
    .star-btn { font-size: 44px; padding: 6px; }
    .note-text { font-size: 15px; font-weight: 600; }

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
        box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 0 0 3px rgba(181, 80, 42, .15);
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
    .ios-btn--primary {
        background: linear-gradient(135deg, var(--rust), #d4754a);
        border: none;
        box-shadow: 0 4px 12px rgba(181, 80, 42, .3);
    }
    .ios-btn--primary:active {
        transform: scale(.97);
        box-shadow: 0 2px 6px rgba(181, 80, 42, .4);
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
    .eval-edit {
        padding-bottom: calc(100px + env(safe-area-inset-bottom, 0px));
    }
}

@media (max-width: 480px) {
    .eval-hero { padding: 16px; }
    .eval-hero__avatar { width: 52px; height: 52px; font-size: 20px; }
    .eval-hero__name { font-size: 18px; }

    .star-btn { font-size: 38px; padding: 4px; }
    .note-text { font-size: 14px; }

    .ios-section-title { font-size: 16px; }
}
</style>
@endpush