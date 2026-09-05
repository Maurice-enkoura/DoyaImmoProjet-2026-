<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TypeBienEnum;
use App\Enums\TypeContratEnum;
use Illuminate\Validation\Rule;
use App\Enums\TypeOperationEnum;

class DemandeImmobiliereRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_operation' => ['required', Rule::enum(TypeOperationEnum::class)],
            'type_bien' => ['required', Rule::enum(TypeBienEnum::class)],
            'budget_maximum' => 'required|numeric|min:0',
            'zone_recherchee' => 'required|string|max:255',
            'quartier_id' => 'nullable|exists:quartiers,id',
            'nombre_chambres' => 'nullable|integer|min:0',
            'nombre_salles_bain' => 'nullable|integer|min:0',
            'surface_minimum' => 'nullable|numeric|min:0',
            'parking' => 'nullable|boolean',
            'meuble' => 'nullable|boolean',
            'climatisation' => 'nullable|boolean',
            'balcon' => 'nullable|boolean',
            'jardin' => 'nullable|boolean',
            'piscine' => 'nullable|boolean',
            'ascenseur' => 'nullable|boolean',
            'securite' => 'nullable|boolean',
            'date_entree_souhaitee' => 'nullable|date|after:today',
            'criteres_particuliers' => 'nullable|string|max:1000',
            'description' => 'required|string|min:20|max:5000',
        ];
    }

    /**
     * Messages de validation personnalisés
     */
    public function messages(): array
    {
        return [
            'type_operation.required' => 'Le type d\'opération est obligatoire.',
            'type_operation.enum' => 'Le type d\'opération sélectionné est invalide.',
            'type_bien.required' => 'Le type de bien est obligatoire.',
            'type_bien.enum' => 'Le type de bien sélectionné est invalide.',
            'budget_maximum.required' => 'Le budget maximum est obligatoire.',
            'budget_maximum.numeric' => 'Le budget maximum doit être un nombre.',
            'budget_maximum.min' => 'Le budget maximum doit être supérieur ou égal à 0.',
            'zone_recherchee.required' => 'La zone recherchée est obligatoire.',
            'zone_recherchee.max' => 'La zone recherchée ne peut pas dépasser 255 caractères.',
            'nombre_chambres.integer' => 'Le nombre de chambres doit être un nombre entier.',
            'nombre_chambres.min' => 'Le nombre de chambres doit être supérieur ou égal à 0.',
            'nombre_salles_bain.integer' => 'Le nombre de salles de bain doit être un nombre entier.',
            'nombre_salles_bain.min' => 'Le nombre de salles de bain doit être supérieur ou égal à 0.',
            'surface_minimum.numeric' => 'La surface minimum doit être un nombre.',
            'surface_minimum.min' => 'La surface minimum doit être supérieure ou égale à 0.',
            'date_entree_souhaitee.date' => 'La date d\'entrée souhaitée doit être une date valide.',
            'date_entree_souhaitee.after' => 'La date d\'entrée souhaitée doit être une date future.',
            'description.required' => 'La description est obligatoire.',
            'description.min' => 'La description doit contenir au moins 20 caractères.',
            'description.max' => 'La description ne peut pas dépasser 5000 caractères.',
        ];
    }
}