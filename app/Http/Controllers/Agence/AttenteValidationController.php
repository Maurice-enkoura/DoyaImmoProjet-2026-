<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AttenteValidationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $agence = $user->agence;
        
        // ✅ Si l'agence est déjà validée, rediriger vers le dashboard
        if ($agence && $agence->statut_validation) {
            return redirect()->route('agence.dashboard');
        }
        
        return view('agence.attente-validation', compact('agence'));
    }
}