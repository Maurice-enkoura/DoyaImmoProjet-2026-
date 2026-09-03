<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|max:255',
            'mot_de_passe' => 'required|string',
            'type' => 'nullable|string|in:particulier,agence',
            //'remember' => 'boolean',
             'remember' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
        ];
    }
}