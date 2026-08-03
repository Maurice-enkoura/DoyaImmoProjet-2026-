<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuartierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'nom' => 'required|string|max:255|unique:quartiers,nom',
            'ville' => 'required|string|max:255',
            'est_actif' => 'boolean',
        ];

        // Pour la mise à jour, ignorer l'unicité du nom pour le même enregistrement
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $quartierId = $this->route('quartier')?->id ?? $this->quartier;
            $rules['nom'] = 'required|string|max:255|unique:quartiers,nom,' . $quartierId;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du quartier est obligatoire.',
            'nom.unique' => 'Ce quartier existe déjà.',
            'ville.required' => 'La ville est obligatoire.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'est_actif' => $this->boolean('est_actif'),
        ]);
    }
}