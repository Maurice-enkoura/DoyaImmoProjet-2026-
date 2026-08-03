<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TypeBienEnum;
use App\Enums\TypeContratEnum;
use Illuminate\Validation\Rule;

class BienImmobilierRequest extends FormRequest
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
            'surface' => 'required|numeric|min:0',
            'parking_disponible' => 'nullable|boolean',
            'est_meuble' => 'nullable|boolean',
            'description' => 'required|string|min:20|max:2000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'videos.*' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
        ];
    }

    public function messages(): array
    {
        return [
            'description.min' => 'La description doit contenir au moins 20 caractères.',
            'prix.min' => 'Le prix doit être supérieur à 0.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
            'videos.*.max' => 'Chaque vidéo ne doit pas dépasser 20 Mo.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Si c'est un terrain, mettre à 0 les champs non pertinents
        if ($this->type_bien === 'terrain') {
            $this->merge([
                'nombre_chambres' => 0,
                'nombre_salles_bain' => 0,
                'parking_disponible' => false,
                'est_meuble' => false,
            ]);
        }
    }
}