<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterParticulierRequest;
use App\Http\Requests\Auth\RegisterAgenceRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Models\Particulier;
use App\Models\Agence;
use App\Models\DocumentAgence;
use App\Enums\RoleEnum;
use App\Enums\StatutDemandeEnum;
use App\Enums\TypeDocumentEnum;
use App\Enums\StatutDocumentEnum;
use App\Models\DemandeImmobiliere;
use App\Models\Evaluation;
use App\Models\Proposition;
use App\Notifications\NouvelleUtilisateurNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Affiche la page de choix du type de compte
     */
    public function showRegister()
    {
        $stats = [
            'besoins' => DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count(),
            'agences' => Agence::where('statut_validation', true)->count(),
            'delai_moyen' => '48h',
            'clients' => User::where('role', 'particulier')->count(),
        ];
        
        return view('auth.register', compact('stats'));
    }

    /**
     * Affiche la page d'inscription particulier
     */
    public function showRegisterParticulier()
    {
          //dd('showRegisterParticulier appelée');
        $stats = [
            'besoins' => DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count(),
            'agences' => Agence::where('statut_validation', true)->count(),
            'delai_moyen' => '48h',
            'clients' => User::where('role', 'particulier')->count(),
        ];
        
        return view('auth.register-particulier', compact('stats'));
    }

    /**
     * Affiche la page d'inscription agence
     */
    public function showRegisterAgence()
    {
        $stats = [
            'agences' => Agence::where('statut_validation', true)->count(),
            'offres_gratuites' => 5,
            'frais_inscription' => '0 F',
            'delai_validation' => '48h',
        ];
        
        return view('auth.register-agence', compact('stats'));
    }

    /**
     * Affiche la page de connexion
     */
    public function showLogin(Request $request)
    {
        $type = $request->get('type', 'particulier');
        
        $stats = [
            'besoins' => DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count(),
            'agences' => Agence::where('statut_validation', true)->count(),
            'note_moyenne' => Evaluation::avg('note') ?? 0,
            'clients' => User::where('role', 'particulier')->count(),
            'offres_moyenne' => $this->calculerOffresMoyenne(),
        ];
        
        if ($type === 'agence') {
            return view('auth.login-agence', compact('stats'));
        }
        
        return view('auth.login', compact('stats'));
    }

    /**
     * Calcule la moyenne d'offres envoyées par mois
     */
    private function calculerOffresMoyenne(): int
    {
        $totalOffres = Proposition::count();
        $moisActifs = Proposition::selectRaw('COUNT(DISTINCT DATE_FORMAT(created_at, "%Y-%m")) as count')->first();
        
        if ($moisActifs && $moisActifs->count > 0) {
            return round($totalOffres / $moisActifs->count);
        }
        
        return 42;
    }

    /**
     * Traite la connexion - CORRIGÉ (sans Auth::attempt)
     */
    public function login(LoginRequest $request)
    {
        // ✅ Chercher l'utilisateur par email
        $user = User::where('email', $request->email)->first();
        
        // ✅ Vérifier si l'utilisateur existe et si le mot de passe correspond
        if (!$user || !Hash::check($request->mot_de_passe, $user->mot_de_passe)) {
            throw ValidationException::withMessages([
                'email' => 'Les identifiants fournis sont incorrects.',
            ]);
        }

        // Vérifier le type de compte
        $type = $request->get('type', 'particulier');
        
        if ($type === 'agence' && !$user->isAgence()) {
            throw ValidationException::withMessages([
                'email' => 'Ce compte n\'est pas un compte agence.',
            ]);
        }
        
        if ($type === 'particulier' && !$user->isParticulier() && !$user->isAdmin()) {
            throw ValidationException::withMessages([
                'email' => 'Ce compte n\'est pas un compte particulier.',
            ]);
        }

        // ✅ Authentifier l'utilisateur manuellement
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Redirection selon le rôle
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isAgence()) {
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

    /**
     * Inscription particulier
     */
    public function registerParticulier(RegisterParticulierRequest $request)
    {
        try {
            // Création de l'utilisateur
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'mot_de_passe' => Hash::make($request->mot_de_passe),
                'role' => RoleEnum::PARTICULIER->value,
            ]);

            // Création du particulier
            Particulier::create([
                'user_id' => $user->id,
                'profession' => $request->profession,
                'adresse' => $request->adresse,
            ]);

            Log::info(' Utilisateur créé avec succès : ' . $user->email);

            // Envoyer la notification de bienvenue
            $this->sendWelcomeNotification($user, RoleEnum::PARTICULIER->value);

            // Connexion automatique
            Auth::login($user);

            return redirect()->route('particulier.dashboard')
                ->with('success', 'Bienvenue sur DoyaImmo ! Votre compte a été créé avec succès.');
                
        } catch (\Exception $e) {
            Log::error(' Erreur inscription particulier : ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription.'])->withInput();
        }
    }

    /**
     * Inscription agence
     */
    public function registerAgence(RegisterAgenceRequest $request)
    {
        try {
            // Création de l'utilisateur
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'mot_de_passe' => Hash::make($request->mot_de_passe),
                'role' => RoleEnum::AGENCE->value,
            ]);

            // Création de l'agence
            $agence = Agence::create([
                'user_id' => $user->id,
                'nom_agence' => $request->nom_agence,
                'adresse' => $request->adresse,
                'quartier' => $request->quartier,
                'description' => $request->description,
                'statut_validation' => false,
            ]);

            Log::info('✅ Agence créée avec succès : ' . $user->email);

            // Upload des documents
            $documentMapping = [
                'rccm' => TypeDocumentEnum::RCCM,
                'ninea' => TypeDocumentEnum::NINEA,
                'piece_identite' => TypeDocumentEnum::PIECE_IDENTITE,
                'logo' => TypeDocumentEnum::LOGO,
            ];

            foreach ($documentMapping as $fieldName => $enumType) {
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);
                    
                    if ($file && $file->isValid()) {
                        $extension = $file->getClientOriginalExtension();
                        $fileName = time() . '_' . $fieldName . '_' . uniqid() . '.' . $extension;
                        $path = $file->storeAs('documents/agences/' . $agence->id, $fileName, 'public');
                        
                        DocumentAgence::create([
                            'agence_id' => $agence->id,
                            'type_document' => $enumType->value,
                            'nom_fichier' => $path,
                            'statut_validation' => StatutDocumentEnum::EN_ATTENTE,
                        ]);
                    }
                }
            }

            // Envoyer la notification de bienvenue
            $this->sendWelcomeNotification($user, RoleEnum::AGENCE->value);

            // Connexion automatique
            Auth::login($user);

            return redirect()->route('agence.dashboard')
                ->with('info', 'Votre compte a été créé. En attente de validation par un administrateur.');
                
        } catch (\Exception $e) {
            Log::error(' Erreur inscription agence : ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription.'])->withInput();
        }
    }

    /**
     * Envoyer une notification de bienvenue
     */
    protected function sendWelcomeNotification(User $user, string $role): void
    {
        try {
            // Supprimer les anciennes notifications de bienvenue pour cet utilisateur
            $user->notifications()
                ->where('type', 'App\Notifications\NouvelleUtilisateurNotification')
                ->delete();
            
            // Envoyer la nouvelle notification
            $user->notify(new NouvelleUtilisateurNotification($user, $role));
            
            Log::info(' Notification de bienvenue envoyée à ' . $user->email . ' (rôle: ' . $role . ')');
        } catch (\Exception $e) {
            Log::error(' Erreur envoi notification de bienvenue : ' . $e->getMessage());
        }
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