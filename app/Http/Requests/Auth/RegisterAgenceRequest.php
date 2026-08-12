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
        return [
            // Identité du responsable
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'telephone' => 'nullable|string|max:20',
            'mot_de_passe' => 'required|string|min:8|confirmed',
            'mot_de_passe_confirmation' => 'required|string|min:8',
            
            // Agence
            'nom_agence' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'quartier' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            
            // Documents (noms corrects des champs du formulaire)
            'rccm' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'ninea' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'piece_identite' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'logo' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            
            // Conditions
            'conditions' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            // Identité
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'mot_de_passe.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'mot_de_passe_confirmation.required' => 'La confirmation du mot de passe est obligatoire.',
            
            // Agence
            'nom_agence.required' => 'Le nom de l\'agence est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            
            // Documents
            'rccm.required' => 'Le document RCCM est obligatoire.',
            'rccm.file' => 'Le RCCM doit être un fichier.',
            'rccm.mimes' => 'Le RCCM doit être au format PDF, JPG, JPEG ou PNG.',
            'rccm.max' => 'Le RCCM ne doit pas dépasser 5 Mo.',
            
            'ninea.required' => 'Le document NINEA est obligatoire.',
            'ninea.file' => 'Le NINEA doit être un fichier.',
            'ninea.mimes' => 'Le NINEA doit être au format PDF, JPG, JPEG ou PNG.',
            'ninea.max' => 'Le NINEA ne doit pas dépasser 5 Mo.',
            
            'piece_identite.required' => 'La pièce d\'identité est obligatoire.',
            'piece_identite.file' => 'La pièce d\'identité doit être un fichier.',
            'piece_identite.mimes' => 'La pièce d\'identité doit être au format PDF, JPG, JPEG ou PNG.',
            'piece_identite.max' => 'La pièce d\'identité ne doit pas dépasser 5 Mo.',
            
            'logo.file' => 'Le logo doit être un fichier.',
            'logo.mimes' => 'Le logo doit être au format JPG, JPEG ou PNG.',
            'logo.max' => 'Le logo ne doit pas dépasser 5 Mo.',
            
            'conditions.required' => 'Vous devez accepter les conditions d\'utilisation.',
            'conditions.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ];
    }
}