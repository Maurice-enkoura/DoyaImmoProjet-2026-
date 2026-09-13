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
            // Identifiants
            'demande_id'               => 'required|exists:demande_immobilieres,id',
            'bien_id'                  => 'required|exists:biens_immobiliers,id',

            // Prix proposé
            'prix_propose'             => 'required|numeric|min:0',

            // 🆕 Conditions commerciales (facultatives mais propres)
            'disponibilite'            => 'nullable|date',
            'caution_mois'             => 'nullable|integer|min:0|max:12',
            'avance_loyer_mois'        => 'nullable|integer|min:0|max:12',
            'honoraires'               => 'nullable|numeric|min:0',
            'charges'                  => 'nullable|numeric|min:0',
            'conditions_particulieres' => 'nullable|string|max:2000',

            // Message
            'message'                  => 'required|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'demande_id.required'      => 'La demande est obligatoire.',
            'demande_id.exists'        => 'Cette demande n\'existe pas.',
            'bien_id.required'         => 'Le bien est obligatoire.',
            'bien_id.exists'           => 'Ce bien n\'existe pas.',
            'prix_propose.required'    => 'Le prix proposé est obligatoire.',
            'prix_propose.numeric'     => 'Le prix proposé doit être un nombre.',
            'prix_propose.min'         => 'Le prix proposé doit être supérieur à 0.',

            'disponibilite.date'       => 'La date de disponibilité n\'est pas valide.',
            'caution_mois.integer'     => 'La caution doit être un nombre entier de mois.',
            'caution_mois.min'         => 'La caution ne peut pas être négative.',
            'caution_mois.max'         => 'La caution ne peut pas dépasser 12 mois.',
            'avance_loyer_mois.integer'=> 'L\'avance de loyer doit être un nombre entier de mois.',
            'avance_loyer_mois.min'    => 'L\'avance ne peut pas être négative.',
            'avance_loyer_mois.max'    => 'L\'avance ne peut pas dépasser 12 mois.',
            'honoraires.numeric'       => 'Les honoraires doivent être un montant valide.',
            'honoraires.min'           => 'Les honoraires ne peuvent pas être négatifs.',
            'charges.numeric'          => 'Les charges doivent être un montant valide.',
            'charges.min'              => 'Les charges ne peuvent pas être négatives.',
            'conditions_particulieres.max' => 'Les conditions particulières sont trop longues (2000 caractères max).',

            'message.required'         => 'Le message est obligatoire.',
            'message.min'              => 'Le message doit contenir au moins 10 caractères.',
            'message.max'              => 'Le message ne doit pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Prépare les données avant validation.
     * Convertit les chaînes vides en null pour les champs optionnels.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'disponibilite'            => $this->disponibilite ?: null,
            'caution_mois'             => $this->caution_mois !== '' ? $this->caution_mois : null,
            'avance_loyer_mois'        => $this->avance_loyer_mois !== '' ? $this->avance_loyer_mois : null,
            'honoraires'               => $this->honoraires !== '' ? $this->honoraires : null,
            'charges'                  => $this->charges !== '' ? $this->charges : null,
            'conditions_particulieres' => $this->conditions_particulieres ?: null,
        ]);
    }
}