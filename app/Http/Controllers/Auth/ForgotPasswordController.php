<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    // ==================== PARTICULIER ====================
    
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Aucun utilisateur trouvé avec cette adresse email.']);
        }

        DB::table('password_resets')->where('email', $request->email)->delete();

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        Mail::send('emails.reset-password', ['user' => $user, 'token' => $token], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Réinitialisation de votre mot de passe - DoyaImmo');
        });

        return back()->with('status', 'Nous vous avons envoyé par email le lien de réinitialisation de votre mot de passe !');
    }

    // ==================== AGENCE ====================
    
    public function showLinkRequestFormAgence()
    {
        return view('auth.forgot-password-agence');
    }

    public function sendResetLinkEmailAgence(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->where('role', 'agence')->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Aucune agence trouvée avec cette adresse email.']);
        }

        DB::table('password_resets')->where('email', $request->email)->delete();

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        Mail::send('emails.reset-password-agence', ['user' => $user, 'token' => $token], function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Réinitialisation de votre mot de passe - Espace Agence DoyaImmo');
        });

        return back()->with('status', 'Nous vous avons envoyé par email le lien de réinitialisation de votre mot de passe !');
    }
}