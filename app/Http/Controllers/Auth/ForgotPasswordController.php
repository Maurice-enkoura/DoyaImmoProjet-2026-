<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    /**
     * Afficher le formulaire de réinitialisation pour les particuliers
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Afficher le formulaire de réinitialisation pour les agences
     */
    public function showLinkRequestFormAgence()
    {
        return view('auth.forgot-password-agence');
    }

    /**
     * Envoyer le lien de réinitialisation pour les particuliers
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Aucun compte trouvé avec cette adresse email.',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Envoyer le lien de réinitialisation pour les agences
     */
    public function sendResetLinkEmailAgence(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Aucun compte agence trouvé avec cette adresse email.',
        ]);

        // Vérifier que l'utilisateur est bien une agence
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user && $user->role !== 'agence') {
            return back()->withErrors(['email' => 'Ce compte n\'est pas une agence.']);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }
}