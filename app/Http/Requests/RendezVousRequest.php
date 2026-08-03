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
            'creneau_id.required' => 'Veuillez sélectionner un créneau.',
            'creneau_id.exists' => 'Le créneau sélectionné n\'existe pas.',
        ];
    }
}