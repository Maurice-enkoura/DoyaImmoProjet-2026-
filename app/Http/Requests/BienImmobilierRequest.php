<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TypeBienEnum;
use App\Enums\TypeContratEnum;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

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
            'surface' => 'nullable|numeric|min:0',
            'parking_disponible' => 'nullable|boolean',
            'est_meuble' => 'nullable|boolean',
            'description' => 'required|string|min:20|max:2000',
            // ✅ IMPORTANT : Les images doivent être présentes mais pas obligatoires
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            // ✅ IMPORTANT : Les vidéos doivent être présentes mais pas obligatoires
            'videos' => 'nullable|array|max:1',
            'videos.*' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
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
            'images.max' => 'Vous ne pouvez pas télécharger plus de 10 images par bien.',
            'images.*.image' => 'Le fichier doit être une image.',
            'images.*.mimes' => 'Format d\'image accepté: JPEG, PNG, JPG, WEBP.',
            'images.*.max' => 'Chaque image ne doit pas dépasser 5 Mo.',
            'videos.max' => 'Vous ne pouvez pas télécharger plus d\'1 vidéo par bien.',
            'videos.*.file' => 'Le fichier doit être une vidéo.',
            'videos.*.mimes' => 'Format de vidéo accepté: MP4, MOV, AVI.',
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
        
        // Si surface est vide, la mettre à null
        if ($this->has('surface') && $this->surface === '') {
            $this->merge(['surface' => null]);
        }
    }

    // ✅ AJOUT : Pour voir les erreurs de validation
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}