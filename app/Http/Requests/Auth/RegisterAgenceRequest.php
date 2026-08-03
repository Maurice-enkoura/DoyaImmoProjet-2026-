<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TypeDocumentEnum;

class RegisterAgenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $documentsRequis = TypeDocumentEnum::cases();
        $documentRules = [];

        foreach ($documentsRequis as $document) {
            $required = $document === TypeDocumentEnum::LOGO ? 'nullable' : 'required';
            $documentRules["documents.{$document->value}"] = $required . '|file|mimes:pdf,jpg,jpeg,png|max:5120';
        }

        return array_merge([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:20',
            'mot_de_passe' => 'required|string|min:8|confirmed',
            'nom_agence' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'quartier' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], $documentRules);
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Cet email est déjà utilisé.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'documents.rccm.required' => 'Le document RCCM est obligatoire.',
            'documents.ninea.required' => 'Le document NINEA est obligatoire.',
            'documents.piece_identite.required' => 'La pièce d\'identité est obligatoire.',
        ];
    }
}