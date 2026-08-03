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
        'nombre_salles_bain' => 'nullable|integer|min:0', // Ajouté
        'surface_minimum' => 'nullable|numeric|min:0',
        'parking' => 'boolean',
        'meuble' => 'boolean',
        'climatisation' => 'boolean',
        'balcon' => 'boolean',
        'jardin' => 'boolean',
        'piscine' => 'boolean',
        'ascenseur' => 'boolean',
        'securite' => 'boolean',
        'date_entree_souhaitee' => 'nullable|date|after:today',
        'criteres_particuliers' => 'nullable|string',
        'description' => 'required|string|min:20|max:2000',
    ];
}

    

    
}