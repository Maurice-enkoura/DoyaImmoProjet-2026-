<?php
// app/Http/Controllers/Admin/AdminProfilController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminProfilController extends Controller
{
    /**
     * Afficher le profil de l'administrateur
     */
    public function index()
    {
        $user = Auth::user();
        
        // Compter les notifications
        $notificationsCount = $user->unreadNotifications()->count();
        
        return view('admin.profil.index', compact('user', 'notificationsCount'));
    }

    /**
     * Mettre à jour le profil
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        return redirect()->route('admin.profil')
            ->with('success', 'Votre profil a été mis à jour avec succès.');
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
        ]);

        // Vérifier que le mot de passe actuel est correct
        if (!Hash::check($request->current_password, $user->mot_de_passe)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel est incorrect.',
            ]);
        }

        $user->update([
            'mot_de_passe' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profil')
            ->with('success', ' Votre mot de passe a été changé avec succès.');
    }
}