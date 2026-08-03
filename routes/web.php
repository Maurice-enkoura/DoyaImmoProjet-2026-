<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;

// Contrôleurs Particulier
use App\Http\Controllers\Particulier\ParticulierDashboardController;
use App\Http\Controllers\Particulier\DemandeController;
use App\Http\Controllers\Particulier\PropositionController as ParticulierPropositionController;
use App\Http\Controllers\Particulier\RendezVousController as ParticulierRendezVousController;
use App\Http\Controllers\Particulier\EvaluationController as ParticulierEvaluationController;
use App\Http\Controllers\Particulier\SignalementController as ParticulierSignalementController;
use App\Http\Controllers\Particulier\ProfilController;
use App\Http\Controllers\Particulier\HistoriqueController;

// Contrôleurs Agence
use App\Http\Controllers\Agence\AgenceController;
use App\Http\Controllers\Agence\BienController;
use App\Http\Controllers\Agence\PropositionAgenceController;
use App\Http\Controllers\Agence\AbonnementController;
use App\Http\Controllers\Agence\CreneauRendezVousController;

// Contrôleurs Admin
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminAgenceController;
use App\Http\Controllers\Admin\AdminUtilisateurController;
use App\Http\Controllers\Admin\AdminAbonnementController;
use App\Http\Controllers\Admin\AdminStatistiqueController;
use App\Http\Controllers\Admin\AdminSignalementController;
use App\Http\Controllers\Admin\AdminQuartierController;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Recherche
Route::get('/recherche', [HomeController::class, 'recherche'])->name('recherche');
Route::get('/recherche/autocomplete', [HomeController::class, 'autocomplete'])->name('recherche.autocomplete');
Route::get('/recherche/suggestions', [HomeController::class, 'suggestions'])->name('recherche.suggestions');
Route::get('/recherche/rapide', [HomeController::class, 'rechercheRapide'])->name('recherche.rapide');

// API - Statistiques et données (AJAX)
Route::get('/api/stats', [HomeController::class, 'stats'])->name('api.stats');
Route::get('/api/derniers-besoins', [HomeController::class, 'derniersBesoins'])->name('api.derniers-besoins');
Route::get('/api/derniers-biens', [HomeController::class, 'derniersBiens'])->name('api.derniers-biens');

// Biens (consultation publique)
Route::get('/biens', [HomeController::class, 'biens'])->name('biens.index');
Route::get('/biens/{bien}', [HomeController::class, 'bienShow'])->name('biens.show');

// Besoins (consultation publique)
Route::get('/besoins', [HomeController::class, 'demandes'])->name('besoins.index');
Route::get('/besoins/{demande}', [HomeController::class, 'demandeShow'])->name('besoins.show');

// Agences (consultation publique)
Route::get('/agences', [HomeController::class, 'agences'])->name('agences.public.index');
Route::get('/agences/{agence}', [HomeController::class, 'agenceShow'])->name('agences.public.show');

// ROUTE PUBLIQUE POUR LES CRÉNEAUX (accessible à tous)
Route::get('/creneaux/disponibles', [CreneauRendezVousController::class, 'getDisponibles'])->name('creneaux.disponibles');

/*
|--------------------------------------------------------------------------
| ROUTES D'AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

// Connexion
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login/agence', [AuthController::class, 'showLogin'])->name('login.agence');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Inscription
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::get('/register/particulier', [AuthController::class, 'showRegisterParticulier'])->name('register.particulier');
Route::post('/register/particulier', [AuthController::class, 'registerParticulier']);
Route::get('/register/agence', [AuthController::class, 'showRegisterAgence'])->name('register.agence');
Route::post('/register/agence', [AuthController::class, 'registerAgence']);

/*
|--------------------------------------------------------------------------
| ROUTES AUTHENTIFIÉES - DASHBOARD GÉNÉRAL
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isAgence()) return redirect()->route('agence.dashboard');
        return redirect()->route('particulier.dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | ROUTES PARTICULIER (ESPACE CLIENT)
    |--------------------------------------------------------------------------
    */
    Route::prefix('particulier')->name('particulier.')->middleware(['role:particulier'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [ParticulierDashboardController::class, 'index'])->name('dashboard');

        // Demandes
        Route::get('/demandes', [DemandeController::class, 'index'])->name('demandes.index');
        Route::get('/demandes/create', [DemandeController::class, 'create'])->name('demandes.create');
        Route::post('/demandes', [DemandeController::class, 'store'])->name('demandes.store');
        Route::get('/demandes/{demande}', [DemandeController::class, 'show'])->name('demandes.show');
        Route::get('/demandes/{demande}/edit', [DemandeController::class, 'edit'])->name('demandes.edit');
        Route::put('/demandes/{demande}', [DemandeController::class, 'update'])->name('demandes.update');
        Route::delete('/demandes/{demande}', [DemandeController::class, 'destroy'])->name('demandes.destroy');

        Route::get('/demandes/{demande}/offres', [DemandeController::class, 'offres'])->name('demandes.offres');

        // Propositions
        Route::get('/propositions', [ParticulierPropositionController::class, 'index'])->name('propositions.index');
        Route::get('/propositions/{proposition}', [ParticulierPropositionController::class, 'show'])->name('propositions.show');
        Route::post('/propositions/{proposition}/selectionner', [ParticulierPropositionController::class, 'selectionner'])->name('propositions.selectionner');

        // Rendez-vous
        Route::get('/rendezvous', [ParticulierRendezVousController::class, 'index'])->name('rendezvous.index');
        Route::get('/rendezvous/create/{proposition}', [ParticulierRendezVousController::class, 'create'])->name('rendezvous.create');
        Route::post('/rendezvous', [ParticulierRendezVousController::class, 'store'])->name('rendezvous.store');
        Route::get('/rendezvous/{rendezVous}', [ParticulierRendezVousController::class, 'show'])->name('rendezvous.show');
        Route::post('/rendezvous/{rendezVous}/confirmer', [ParticulierRendezVousController::class, 'confirmer'])->name('rendezvous.confirmer');
        Route::post('/rendezvous/{rendezVous}/annuler', [ParticulierRendezVousController::class, 'annuler'])->name('rendezvous.annuler');

        // Évaluations
        Route::get('/evaluations', [ParticulierEvaluationController::class, 'index'])->name('evaluations.index');
        Route::get('/evaluations/create/{agence}', [ParticulierEvaluationController::class, 'create'])->name('evaluations.create');
        Route::post('/evaluations', [ParticulierEvaluationController::class, 'store'])->name('evaluations.store');
        Route::get('/evaluations/{evaluation}', [ParticulierEvaluationController::class, 'show'])->name('evaluations.show');

        // Profil
        Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
        Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [ProfilController::class, 'updatePassword'])->name('profil.password');

        // Historique
        Route::get('/historique', [HistoriqueController::class, 'index'])->name('historique');

        // Signalements
        Route::get('/signalements/mes', [ParticulierSignalementController::class, 'mesSignalements'])->name('signalements.mes');
        Route::get('/signalements/create/bien/{bien}', [ParticulierSignalementController::class, 'createBien'])->name('signalements.create-bien');
        Route::get('/signalements/create/proposition/{proposition}', [ParticulierSignalementController::class, 'createProposition'])->name('signalements.create-proposition');
        Route::get('/signalements/create/demande/{demande}', [ParticulierSignalementController::class, 'createDemande'])->name('signalements.create-demande');
        Route::post('/signalements', [ParticulierSignalementController::class, 'store'])->name('signalements.store');
        Route::get('/signalements/{signalement}', [ParticulierSignalementController::class, 'show'])->name('signalements.show');
    });

    /*
    |--------------------------------------------------------------------------
    | ROUTES AGENCE (ESPACE AGENCE)
    |--------------------------------------------------------------------------
    */
    Route::prefix('agence')->name('agence.')->middleware(['role:agence'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AgenceController::class, 'dashboard'])->name('dashboard');

        // Demandes (consultation)
        Route::get('/demandes', [AgenceController::class, 'demandes'])->name('demandes.index');
        Route::get('/demandes/{demande}', [AgenceController::class, 'demandesShow'])->name('demandes.show');

        // Profil
        Route::get('/profil', [AgenceController::class, 'profil'])->name('profil');
        Route::put('/profil', [AgenceController::class, 'update'])->name('profil.update'); // Changé de profilUpdate à update

        // Rendez-vous


        Route::get('/rendezvous', [AgenceController::class, 'rendezvous'])->name('rendezvous.index');
        Route::get('/rendezvous/{rendezVous}', [AgenceController::class, 'rendezvousShow'])->name('rendezvous.show');
        Route::put('/rendezvous/{rendezVous}', [AgenceController::class, 'rendezvousUpdate'])->name('rendezvous.update');
        // Évaluations
        Route::get('/evaluations', [AgenceController::class, 'evaluations'])->name('evaluations.index');
        Route::get('/evaluations/{evaluation}', [AgenceController::class, 'evaluationsShow'])->name('evaluations.show');
        Route::post('/evaluations/{evaluation}/repondre', [AgenceController::class, 'evaluationsRepondre'])->name('evaluations.repondre');

        // Abonnement
        Route::get('/abonnement', [AgenceController::class, 'abonnement'])->name('abonnement');
        Route::get('/abonnement/plans', [AbonnementController::class, 'plans'])->name('abonnement.plans');
        Route::post('/abonnement/souscrire', [AbonnementController::class, 'souscrire'])->name('abonnement.souscrire');

        // ============ CRÉNEAUX RENDEZ-VOUS (avec AgenceController) ============
        Route::post('/creneaux/generer', [AgenceController::class, 'genererCreneaux'])->name('creneaux.generer');
        Route::post('/creneaux/{creneau}/toggle', [AgenceController::class, 'toggleCreneau'])->name('creneaux.toggle');
        Route::delete('/creneaux/{creneau}', [AgenceController::class, 'supprimerCreneau'])->name('creneaux.supprimer');
        Route::delete('/creneaux/date', [AgenceController::class, 'supprimerCreneauxDate'])->name('creneaux.supprimerDate');

        // Historique
        Route::get('/historique', [AgenceController::class, 'historique'])->name('historique');

        // Biens (avec middleware agence.validee et abonnement.actif)
        Route::middleware(['agence.validee', 'abonnement.actif'])->group(function () {
            Route::get('/biens', [BienController::class, 'index'])->name('biens.index');
            Route::get('/biens/create', [BienController::class, 'create'])->name('biens.create');
            Route::post('/biens', [BienController::class, 'store'])->name('biens.store');
            Route::get('/biens/{bien}', [BienController::class, 'show'])->name('biens.show');
            Route::get('/biens/{bien}/edit', [BienController::class, 'edit'])->name('biens.edit');
            Route::put('/biens/{bien}', [BienController::class, 'update'])->name('biens.update');
            Route::delete('/biens/{bien}', [BienController::class, 'destroy'])->name('biens.destroy');
            Route::post('/biens/{bien}/activer', [BienController::class, 'activer'])->name('biens.activer');
            Route::delete('/medias/{media}', [BienController::class, 'supprimerMedia'])->name('medias.destroy');

            // Propositions
            Route::get('/propositions', [PropositionAgenceController::class, 'index'])->name('propositions.index');
            Route::get('/propositions/create/{demande}', [PropositionAgenceController::class, 'create'])->name('propositions.create');
            Route::post('/propositions', [PropositionAgenceController::class, 'store'])->name('propositions.store');
            Route::get('/propositions/{proposition}', [PropositionAgenceController::class, 'show'])->name('propositions.show');
            Route::post('/propositions/{proposition}/annuler', [PropositionAgenceController::class, 'annuler'])->name('propositions.annuler');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | ROUTES ADMINISTRATEUR
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Agences
        Route::get('/agences', [AdminAgenceController::class, 'index'])->name('agences');
        Route::get('/agences/{agence}', [AdminAgenceController::class, 'show'])->name('agences.show');
        Route::get('/agences/{agence}/documents', [AdminAgenceController::class, 'documents'])->name('agences.documents');
        Route::post('/agences/{agence}/documents/valider', [AdminAgenceController::class, 'validerDocuments'])->name('agences.valider-documents');
        Route::post('/agences/{agence}/valider', [AdminAgenceController::class, 'valider'])->name('agences.valider');
        Route::post('/agences/{agence}/refuser', [AdminAgenceController::class, 'refuser'])->name('agences.refuser');
        Route::post('/agences/{agence}/toggle', [AdminAgenceController::class, 'toggleStatut'])->name('agences.toggle');
        Route::delete('/agences/{agence}', [AdminAgenceController::class, 'destroy'])->name('agences.destroy');

        // Utilisateurs
        Route::get('/utilisateurs', [AdminUtilisateurController::class, 'index'])->name('utilisateurs');
        Route::get('/utilisateurs/create', [AdminUtilisateurController::class, 'create'])->name('utilisateurs.create');
        Route::post('/utilisateurs', [AdminUtilisateurController::class, 'store'])->name('utilisateurs.store');
        Route::get('/utilisateurs/{user}', [AdminUtilisateurController::class, 'show'])->name('utilisateurs.show');
        Route::get('/utilisateurs/{user}/edit', [AdminUtilisateurController::class, 'edit'])->name('utilisateurs.edit');
        Route::put('/utilisateurs/{user}', [AdminUtilisateurController::class, 'update'])->name('utilisateurs.update');
        Route::delete('/utilisateurs/{user}', [AdminUtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');

        // Quartiers
        Route::get('/quartiers', [AdminQuartierController::class, 'index'])->name('quartiers.index');
        Route::get('/quartiers/create', [AdminQuartierController::class, 'create'])->name('quartiers.create');
        Route::post('/quartiers', [AdminQuartierController::class, 'store'])->name('quartiers.store');
        Route::get('/quartiers/{quartier}', [AdminQuartierController::class, 'show'])->name('quartiers.show');
        Route::get('/quartiers/{quartier}/edit', [AdminQuartierController::class, 'edit'])->name('quartiers.edit');
        Route::put('/quartiers/{quartier}', [AdminQuartierController::class, 'update'])->name('quartiers.update');
        Route::delete('/quartiers/{quartier}', [AdminQuartierController::class, 'destroy'])->name('quartiers.destroy');
        Route::post('/quartiers/{quartier}/toggle', [AdminQuartierController::class, 'toggleStatut'])->name('quartiers.toggle');
        Route::post('/quartiers/import', [AdminQuartierController::class, 'import'])->name('quartiers.import');
        Route::get('/quartiers/export', [AdminQuartierController::class, 'export'])->name('quartiers.export');

        // Abonnements
        Route::get('/abonnements', [AdminAbonnementController::class, 'index'])->name('abonnements');
        Route::get('/abonnements/create', [AdminAbonnementController::class, 'create'])->name('abonnements.create');
        Route::post('/abonnements', [AdminAbonnementController::class, 'store'])->name('abonnements.store');
        Route::get('/abonnements/{abonnement}', [AdminAbonnementController::class, 'show'])->name('abonnements.show');
        Route::get('/abonnements/{abonnement}/edit', [AdminAbonnementController::class, 'edit'])->name('abonnements.edit');
        Route::put('/abonnements/{abonnement}', [AdminAbonnementController::class, 'update'])->name('abonnements.update');
        Route::delete('/abonnements/{abonnement}', [AdminAbonnementController::class, 'destroy'])->name('abonnements.destroy');

        // Signalements
        Route::get('/signalements', [AdminSignalementController::class, 'index'])->name('signalements');
        Route::get('/signalements/{signalement}', [AdminSignalementController::class, 'show'])->name('signalements.show');
        Route::post('/signalements/{signalement}/traiter', [AdminSignalementController::class, 'traiter'])->name('signalements.traiter');
        Route::post('/signalements/{signalement}/rejeter', [AdminSignalementController::class, 'rejeter'])->name('signalements.rejeter');

        // Statistiques
        Route::get('/statistiques', [AdminStatistiqueController::class, 'index'])->name('statistiques');
        Route::get('/statistiques/export', [AdminStatistiqueController::class, 'export'])->name('statistiques.export');
    });
});
