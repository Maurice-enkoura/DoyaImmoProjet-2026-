<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterParticulierRequest;
use App\Http\Requests\Auth\RegisterAgenceRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Particulier;
use App\Models\Agence;
use App\Services\DocumentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Enums\RoleEnum;

class AuthController extends Controller
{
    public function __construct(private DocumentService $documentService) {}

    /**
     * Affiche la page de choix du type de compte
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Affiche la page d'inscription particulier
     */
    public function showRegisterParticulier()
    {
        return view('auth.register-particulier');
    }

    /**
     * Affiche la page d'inscription agence
     */
    public function showRegisterAgence()
    {
        return view('auth.register-agence');
    }

    /**
     * Affiche la page de connexion
     */
    public function showLogin(Request $request)
    {
        $type = $request->get('type', 'particulier');
        
        if ($type === 'agence') {
            return view('auth.login-agence');
        }
        
        return view('auth.login');
    }

    /**
     * Traite la connexion
     */
    public function login(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->mot_de_passe,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Vérifier que l'utilisateur a le bon rôle
            $type = $request->get('type', 'particulier');
            
            if ($type === 'agence' && !$user->isAgence()) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Ce compte n\'est pas un compte agence.',
                ])->onlyInput('email');
            }
            
            if ($type === 'particulier' && !$user->isParticulier() && !$user->isAdmin()) {
                // Les administrateurs peuvent aussi se connecter
                if (!$user->isAdmin()) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Ce compte n\'est pas un compte particulier.',
                    ])->onlyInput('email');
                }
            }

            // Redirection selon le rôle
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isAgence()) {
                // Vérifier si l'agence est validée
                $agence = $user->agence;
                if ($agence && !$agence->statut_validation) {
                    return redirect()->route('agence.dashboard')
                        ->with('info', 'Votre agence est en attente de validation par un administrateur.');
                }
                return redirect()->route('agence.dashboard');
            } else {
                return redirect()->route('particulier.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis sont incorrects.',
        ])->onlyInput('email');
    }

    /**
     * Inscription particulier
     */
    public function registerParticulier(RegisterParticulierRequest $request)
    {
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'role' => RoleEnum::PARTICULIER->value,
        ]);

        Particulier::create([
            'user_id' => $user->id,
            'profession' => $request->profession,
            'adresse' => $request->adresse,
        ]);

        Auth::login($user);

        return redirect()->route('particulier.dashboard')
            ->with('success', 'Bienvenue sur DoyaImmo ! Votre compte a été créé avec succès.');
    }

    /**
     * Inscription agence
     */
    public function registerAgence(RegisterAgenceRequest $request)
    {
        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'role' => RoleEnum::AGENCE->value,
        ]);

        $agence = Agence::create([
            'user_id' => $user->id,
            'nom_agence' => $request->nom_agence,
            'adresse' => $request->adresse,
            'quartier' => $request->quartier,
            'description' => $request->description,
            'statut_validation' => false,
        ]);

        // Télécharger les documents
        if ($request->hasFile('documents')) {
            $this->documentService->uploadDocuments($agence, $request->file('documents'));
        }

        Auth::login($user);

        return redirect()->route('agence.dashboard')
            ->with('info', 'Votre compte a été créé. En attente de validation par un administrateur.');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    /**
     * Redirection après connexion
     */
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isAgence()) {
            $agence = $user->agence;
            if ($agence && !$agence->statut_validation) {
                return redirect()->route('agence.dashboard')
                    ->with('info', 'Votre agence est en attente de validation.');
            }
            return redirect()->route('agence.dashboard');
        }

        return redirect()->route('particulier.dashboard');
    }
}