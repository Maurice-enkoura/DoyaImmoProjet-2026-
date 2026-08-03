<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropositionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'demande_id' => 'required|exists:demande_immobilieres,id',
            'bien_id' => 'required|exists:biens_immobiliers,id',
            'prix_propose' => 'required|numeric|min:0',
            'message' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'message.min' => 'Le message doit contenir au moins 10 caractères.',
            'prix_propose.min' => 'Le prix proposé doit être supérieur à 0.',
        ];
    }
}