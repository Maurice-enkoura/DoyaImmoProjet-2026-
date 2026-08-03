<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function index()
    {
        return view('particulier.profil');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $particulier = $user->particulier;

        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'telephone' => 'nullable|string|max:20',
            'profession' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
        ]);

        $user->update([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        if ($particulier) {
            $particulier->update([
                'profession' => $request->profession,
                'adresse' => $request->adresse,
            ]);
        }

        return redirect()->route('particulier.profil')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'mot_de_passe' => Hash::make($request->password),
        ]);

        return redirect()->route('particulier.profil')
            ->with('success', 'Mot de passe modifié avec succès.');
    }
}