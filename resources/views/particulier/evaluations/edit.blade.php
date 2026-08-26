@extends('layouts.dashboard')

@section('title', 'Modifier mon avis — DoyaImmo')
@section('page_title', 'Modifier mon avis')
@section('page_sub', 'Modifiez votre évaluation de l\'agence')

@section('content')
<div class="view active">
    <div style="max-width:600px;margin:0 auto;background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:24px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <div style="width:48px;height:48px;border-radius:50%;background:var(--rust-soft);color:var(--rust);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;">
                {{ strtoupper(substr($evaluation->agence->nom_agence, 0, 1)) }}
            </div>
            <div>
                <h3 style="font-family:var(--display);font-size:18px;font-weight:700;margin:0;">{{ $evaluation->agence->nom_agence }}</h3>
                <p style="font-size:13px;color:var(--muted);margin:0;">Modifiez votre évaluation</p>
            </div>
        </div>

        <form method="POST" action="{{ route('particulier.evaluations.update', $evaluation) }}">
            @csrf
            @method('PUT')

            <div style="margin-bottom:20px;">
                <label style="display:block;font-weight:600;font-size:13px;color:var(--text-soft);margin-bottom:6px;">Votre note</label>
                <div style="display:flex;gap:8px;font-size:32px;cursor:pointer;" id="starRating">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-solid fa-star" data-value="{{ $i }}" style="color:{{ $i <= $evaluation->note ? '#F5A623' : '#D4D8E0' }};transition:color 0.2s;"></i>
                    @endfor
                </div>
                <input type="hidden" name="note" id="note" value="{{ $evaluation->note }}">
                @error('note')
                    <span style="color:#C62828;font-size:12px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom:20px;">
                <label for="commentaire" style="display:block;font-weight:600;font-size:13px;color:var(--text-soft);margin-bottom:6px;">Votre commentaire</label>
                <textarea id="commentaire" name="commentaire" rows="4" style="width:100%;padding:10px 14px;border:1px solid var(--border);border-radius:10px;font-family:inherit;font-size:13px;resize:vertical;transition:border 0.2s;">{{ old('commentaire', $evaluation->commentaire) }}</textarea>
                @error('commentaire')
                    <span style="color:#C62828;font-size:12px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn btn-rust" style="flex:1;justify-content:center;">
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

        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = parseInt(this.dataset.value);
                input.value = value;
                stars.forEach(s => {
                    s.style.color = parseInt(s.dataset.value) <= value ? '#F5A623' : '#D4D8E0';
                });
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
    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }
    .btn-ghost:hover {
        background: var(--border);
        color: var(--ink);
    }
</style>
@endpush