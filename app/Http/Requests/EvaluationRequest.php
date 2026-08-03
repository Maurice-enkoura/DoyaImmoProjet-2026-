<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'agence_id' => 'required|exists:agences,id',
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'note.min' => 'La note doit être comprise entre 1 et 5.',
            'note.max' => 'La note doit être comprise entre 1 et 5.',
        ];
    }
}