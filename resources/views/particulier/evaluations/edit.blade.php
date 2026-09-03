@extends('layouts.dashboard')

@section('title', 'Modifier mon avis — DoyaImmo')
@section('page_title', 'Modifier mon avis')
@section('page_sub', 'Modifiez votre évaluation de l\'agence')

@section('content')
<div class="view active">
    <div style="max-width:650px;margin:0 auto;background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
        <!-- En-tête avec l'agence -->
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;">
            <div style="width:48px;height:48px;border-radius:50%;background:var(--rust-soft);color:var(--rust);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;">
                {{ strtoupper(substr($evaluation->agence->nom_agence, 0, 1)) }}
            </div>
            <div>
                <h3 style="font-family:var(--display);font-size:18px;font-weight:700;margin:0;">{{ $evaluation->agence->nom_agence }}</h3>
                <p style="font-size:13px;color:var(--muted);margin:0;">Modifiez votre évaluation</p>
            </div>
        </div>

        <!-- Informations de la proposition -->
        @if($evaluation->proposition)
        <div style="padding:12px 16px;background:#F7F9FC;border-radius:10px;border:1px solid var(--border);margin-bottom:20px;">
            <div style="display:flex;flex-wrap:wrap;gap:12px 24px;">
                <div>
                    <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Proposition du</div>
                    <div style="font-size:13px;font-weight:500;color:var(--ink);">{{ $evaluation->proposition->created_at->format('d/m/Y') }}</div>
                </div>
                <div>
                    <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Prix proposé</div>
                    <div style="font-size:14px;font-weight:700;color:var(--rust);">{{ number_format($evaluation->proposition->prix_propose, 0, ',', ' ') }} FCFA</div>
                </div>
                @if($evaluation->proposition->bien)
                <div>
                    <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;font-weight:600;">Bien</div>
                    <div style="font-size:13px;font-weight:500;color:var(--ink);">{{ $evaluation->proposition->bien->titre ?? 'Non spécifié' }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Formulaire -->
        <form method="POST" action="{{ route('particulier.evaluations.update', $evaluation) }}">
            @csrf
            @method('PUT')

            <!-- Note -->
            <div style="margin-bottom:20px;">
                <label style="display:block;font-weight:600;font-size:13px;color:var(--text-soft);margin-bottom:6px;">
                    Votre note <span style="color:var(--rust);">*</span>
                </label>
                <div style="display:flex;gap:8px;font-size:32px;cursor:pointer;" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star" data-value="{{ $i }}" 
                           style="color:{{ $i <= $evaluation->note ? '#F5A623' : '#D4D8E0' }};transition:color 0.2s;"></i>
                    @endfor
                </div>
                <div style="font-size:13px;font-weight:500;color:var(--muted);margin-top:4px;" id="noteText">
                    @php
                        $labels = [
                            1 => 'Très insatisfait 😞',
                            2 => 'Insatisfait 😕',
                            3 => 'Moyen 😐',
                            4 => 'Satisfait 😊',
                            5 => 'Très satisfait 🤩'
                        ];
                    @endphp
                    {{ $labels[$evaluation->note] ?? 'Sélectionnez une note' }}
                </div>
                <input type="hidden" name="note" id="note" value="{{ old('note', $evaluation->note) }}">
                @error('note')
                    <span style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Commentaire -->
            <div style="margin-bottom:20px;">
                <label for="commentaire" style="display:block;font-weight:600;font-size:13px;color:var(--text-soft);margin-bottom:6px;">
                    Votre commentaire <span style="color:var(--rust);">*</span>
                </label>
                <textarea id="commentaire" name="commentaire" rows="4" 
                          style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;transition:border 0.2s;min-height:100px;">{{ old('commentaire', $evaluation->commentaire) }}</textarea>
                @error('commentaire')
                    <span style="color:#C62828;font-size:12px;display:block;margin-top:4px;">{{ $message }}</span>
                @enderror
                <div style="font-size:11px;color:var(--muted);margin-top:4px;">
                    <i class="fa-regular fa-info-circle"></i> Minimum 10 caractères
                </div>
            </div>

            <!-- Actions -->
            <div style="display:flex;gap:12px;padding-top:16px;border-top:1px solid var(--border);">
                <button type="submit" class="btn btn-rust" style="flex:1;justify-content:center;" id="submitBtn">
                    <i class="fa-solid fa-save"></i> Mettre à jour
                </button>
                <a href="{{ route('particulier.evaluations.index') }}" class="btn btn-ghost" style="flex:1;justify-content:center;">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('#starRating .fa-star');
        const input = document.getElementById('note');
        const noteText = document.getElementById('noteText');
        
        const labels = {
            1: 'Très insatisfait 😞',
            2: 'Insatisfait 😕',
            3: 'Moyen 😐',
            4: 'Satisfait 😊',
            5: 'Très satisfait 🤩'
        };

        function updateStars(value) {
            stars.forEach(s => {
                const starValue = parseInt(s.dataset.value);
                s.style.color = starValue <= value ? '#F5A623' : '#D4D8E0';
            });
            noteText.textContent = labels[value] || 'Sélectionnez une note';
            noteText.style.color = value >= 4 ? '#1E7A47' : value >= 3 ? '#F5A623' : '#C62828';
        }

        // Initialiser avec la note actuelle
        const currentNote = parseInt(input.value) || 0;
        updateStars(currentNote);

        // Click sur les étoiles
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = parseInt(this.dataset.value);
                input.value = value;
                updateStars(value);
            });

            star.addEventListener('mouseenter', function() {
                const value = parseInt(this.dataset.value);
                stars.forEach(s => {
                    s.style.color = parseInt(s.dataset.value) <= value ? '#F5A623' : '#D4D8E0';
                });
            });

            star.addEventListener('mouseleave', function() {
                const currentValue = parseInt(input.value) || 0;
                stars.forEach(s => {
                    s.style.color = parseInt(s.dataset.value) <= currentValue ? '#F5A623' : '#D4D8E0';
                });
            });
        });

        // Gestion du bouton de soumission
        document.querySelector('form').addEventListener('submit', function(e) {
            const btn = document.getElementById('submitBtn');
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Mise à jour...';
            btn.disabled = true;
        });
    });
</script>
@endsection

@push('styles')
<style>
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 10px;
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
    .btn-rust:disabled {
        opacity: 0.6;
        cursor: not-allowed;
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
    textarea:focus {
        outline: none;
        border-color: var(--rust);
        box-shadow: 0 0 0 3px rgba(181, 80, 42, 0.08);
    }
</style>
@endpush