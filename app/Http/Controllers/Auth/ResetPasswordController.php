<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function showResetFormAgence(Request $request, $token = null)
    {
        return view('auth.reset-password-agence', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // ✅ Utiliser la bonne colonne 'mot_de_passe'
                $user->mot_de_passe = Hash::make($password);
                $user->remember_token = Str::random(60);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès.');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    public function resetAgence(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // ✅ Utiliser la bonne colonne 'mot_de_passe'
                $user->mot_de_passe = Hash::make($password);
                $user->remember_token = Str::random(60);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login.agence')->with('status', 'Votre mot de passe a été réinitialisé avec succès.');
        }

        return back()->withErrors(['email' => __($status)]);
    }
}