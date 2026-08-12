<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RendezVousRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'proposition_id' => 'required|exists:propositions,id',
            'creneau_id' => 'required|exists:creneaux_rendez_vous,id',
        ];
    }

    public function messages(): array
    {
        return [
            'proposition_id.required' => 'Veuillez sélectionner une proposition.',
            'proposition_id.exists' => 'La proposition sélectionnée est invalide.',
            'creneau_id.required' => 'Veuillez sélectionner un créneau horaire.',
            'creneau_id.exists' => 'Le créneau sélectionné est invalide.',
        ];
    }
}