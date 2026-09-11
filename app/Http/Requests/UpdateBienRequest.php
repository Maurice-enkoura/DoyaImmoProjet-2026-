<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TypeBienEnum;
use App\Enums\TypeContratEnum;
use Illuminate\Validation\Rule;

class UpdateBienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'titre' => 'required|string|max:255',
            'type_bien' => ['required', Rule::enum(TypeBienEnum::class)],
            'type_contrat' => ['required', Rule::enum(TypeContratEnum::class)],
            'prix' => 'required|numeric|min:0',
            'quartier' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'nombre_chambres' => 'nullable|integer|min:0',
            'nombre_salles_bain' => 'nullable|integer|min:0',
            'surface' => 'nullable|numeric|min:0',
            'parking_disponible' => 'nullable|boolean',
            'est_meuble' => 'nullable|boolean',
            'description' => 'required|string|min:20|max:2000',
            'climatisation' => 'nullable|boolean',
            'balcon' => 'nullable|boolean',
            'jardin' => 'nullable|boolean',
            'piscine' => 'nullable|boolean',
            'ascenseur' => 'nullable|boolean',
            'securite' => 'nullable|boolean',
            // ✅ PAS DE RÈGLES POUR LES IMAGES
            // ✅ PAS DE RÈGLES POUR LES VIDÉOS
        ];
    }

    public function messages(): array
    {
        return [
            'titre.required' => 'Le titre est obligatoire.',
            'type_bien.required' => 'Le type de bien est obligatoire.',
            'type_contrat.required' => 'Le type de contrat est obligatoire.',
            'prix.required' => 'Le prix est obligatoire.',
            'prix.min' => 'Le prix doit être supérieur à 0.',
            'quartier.required' => 'Le quartier est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'description.min' => 'La description doit contenir au moins 20 caractères.',
        ];
    }
}