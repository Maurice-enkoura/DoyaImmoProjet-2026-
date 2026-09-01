<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Models\User;

class ResetPasswordController extends Controller
{
    // ==================== PARTICULIER ====================
    
    public function showResetForm($token = null, Request $request)
    {
        $email = $request->query('email');

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation n\'est pas valide ou a expiré.']);
        }

        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation a expiré.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->mot_de_passe = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        Mail::send('emails.password-reset-confirmation', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject(' Mot de passe réinitialisé avec succès - DoyaImmo');
        });

        return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé avec succès ! Vérifiez votre email pour la confirmation.');
    }

    // ==================== AGENCE ====================
    
    public function showResetFormAgence($token = null, Request $request)
    {
        $email = $request->query('email');

        return view('auth.reset-password-agence', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetAgence(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation n\'est pas valide ou a expiré.']);
        }

        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => 'Ce lien de réinitialisation a expiré.']);
        }

        $user = User::where('email', $request->email)->first();
        $user->mot_de_passe = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        Mail::send('emails.password-reset-confirmation-agence', ['user' => $user], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject(' Mot de passe réinitialisé avec succès - Espace Agence DoyaImmo');
        });

        return redirect()->route('login.agence')->with('status', 'Votre mot de passe a été réinitialisé avec succès ! Vérifiez votre email pour la confirmation.');
    }
}