<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PayDunyaController;
use App\Http\Controllers\NotificationController;


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
use App\Http\Controllers\Admin\AdminBienController;
use App\Http\Controllers\Admin\AdminDemandeController;
use App\Http\Controllers\Admin\AdminPropositionController;
use App\Http\Controllers\Admin\AdminEvaluationController;
use App\Http\Controllers\Admin\AdminRendezVousController;
use App\Http\Controllers\Admin\AdminProfilController;
use App\Http\Controllers\Admin\AdminBanniereController;
use App\Http\Controllers\Admin\AdminMiseEnVedetteController;

/*
|--------------------------------------------------------------------------
| ROUTES PUBLIQUES
|--------------------------------------------------------------------------
*/

// Page d'accueil
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');

// Pages légales
Route::get('/cgu', function () {
    return view('pages.cgu');
})->name('cgu');

Route::get('/mentions-legales', function () {
    return view('pages.mentions-legales');
})->name('mentions-legales');

// Recherche
Route::get('/recherche', [HomeController::class, 'recherche'])->name('recherche');
Route::get('/recherche/autocomplete', [HomeController::class, 'autocomplete'])->name('recherche.autocomplete');
Route::get('/recherche/suggestions', [HomeController::class, 'suggestions'])->name('recherche.suggestions');
Route::get('/recherche/rapide', [HomeController::class, 'rechercheRapide'])->name('recherche.rapide');

// API - Statistiques et données (AJAX)
Route::get('/api/stats', [HomeController::class, 'stats'])->name('api.stats');
Route::get('/api/derniers-besoins', [HomeController::class, 'derniersBesoins'])->name('api.derniers-besoins');
Route::get('/api/derniers-biens', [HomeController::class, 'derniersBiens'])->name('api.derniers-biens');

// Biens (consultation publique) - AVEC SLUG
Route::get('/biens', [HomeController::class, 'biens'])->name('biens.index');
Route::get('/biens/{bien:slug}', [HomeController::class, 'bienShow'])->name('biens.show');

// Besoins (consultation publique) - AVEC SLUG
Route::get('/besoins', [HomeController::class, 'demandes'])->name('besoins.index');
Route::get('/besoins/{demande:slug}', [HomeController::class, 'demandeShow'])->name('besoins.show');

// Agences (consultation publique) - AVEC SLUG
Route::get('/agences', [HomeController::class, 'agences'])->name('agences.public.index');
Route::get('/agences/{agence:slug}', [HomeController::class, 'agenceShow'])->name('agences.public.show');

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
| ROUTES MOT DE PASSE OUBLIÉ - PARTICULIERS
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Formulaire de demande
    Route::get('/mot-de-passe-oublie', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    // Envoi du lien
    Route::post('/mot-de-passe-oublie', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    // Formulaire de réinitialisation
    Route::get('/reinitialiser-mot-de-passe/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    // Réinitialisation
    Route::post('/reinitialiser-mot-de-passe', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| ROUTES MOT DE PASSE OUBLIÉ - AGENCES
|--------------------------------------------------------------------------
*/
Route::prefix('agence')->name('agence.')->middleware('guest')->group(function () {
    // Formulaire de demande
    Route::get('/mot-de-passe-oublie', [ForgotPasswordController::class, 'showLinkRequestFormAgence'])
        ->name('password.request');

    // Envoi du lien
    Route::post('/mot-de-passe-oublie', [ForgotPasswordController::class, 'sendResetLinkEmailAgence'])
        ->name('password.email');

    // Formulaire de réinitialisation
    Route::get('/reinitialiser-mot-de-passe/{token}', [ResetPasswordController::class, 'showResetFormAgence'])
        ->name('password.reset');

    // Réinitialisation
    Route::post('/reinitialiser-mot-de-passe', [ResetPasswordController::class, 'resetAgence'])
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| ROUTES PAYDUNYA (Paiement en ligne)
|--------------------------------------------------------------------------
*/
Route::prefix('paydunya')->name('paydunya.')->group(function () {
    Route::get('/pay/{abonnement}', [PayDunyaController::class, 'pay'])->name('pay');
    Route::match(['GET', 'POST'], '/callback', [PayDunyaController::class, 'callback'])->name('callback');
    Route::get('/cancel', [PayDunyaController::class, 'cancel'])->name('cancel');
    Route::get('/return', [PayDunyaController::class, 'return'])->name('return');
    Route::get('/status/{abonnement}', [PayDunyaController::class, 'status'])->name('status');
    Route::get('/force-update/{abonnement}', [PayDunyaController::class, 'forceUpdate'])->name('force-update');
});

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
    | ROUTES NOTIFICATIONS (avec le contrôleur)
    |--------------------------------------------------------------------------
    */
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{id}/read', [NotificationController::class, 'read'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
    });

    /*
    |--------------------------------------------------------------------------
    | ROUTES API NOTIFICATIONS
    |--------------------------------------------------------------------------
    */
    Route::prefix('api/notifications')->name('api.notifications.')->group(function () {
        Route::get('/count', [NotificationController::class, 'count'])->name('count');
        Route::post('/{id}/read', [NotificationController::class, 'read'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'readAll'])->name('read-all');
        Route::get('/new', [NotificationController::class, 'new'])->name('new');
    });

    /*
    |--------------------------------------------------------------------------
    | ROUTES PARTICULIER (ESPACE CLIENT)
    |--------------------------------------------------------------------------
    */
    Route::prefix('particulier')->name('particulier.')->middleware(['role:particulier'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [ParticulierDashboardController::class, 'index'])->name('dashboard');

    // Demandes - AVEC SLUG POUR LES ROUTES DE DÉTAIL
    Route::get('/demandes', [DemandeController::class, 'index'])->name('demandes.index');
    Route::get('/demandes/create', [DemandeController::class, 'create'])->name('demandes.create');
    Route::post('/demandes', [DemandeController::class, 'store'])->name('demandes.store');
    Route::get('/demandes/{demande:slug}', [DemandeController::class, 'show'])->name('demandes.show');
    Route::get('/demandes/{demande:slug}/edit', [DemandeController::class, 'edit'])->name('demandes.edit');
    Route::put('/demandes/{demande:slug}', [DemandeController::class, 'update'])->name('demandes.update');
    Route::delete('/demandes/{demande:slug}', [DemandeController::class, 'destroy'])->name('demandes.destroy');
    Route::get('/demandes/{demande:slug}/offres', [DemandeController::class, 'offres'])->name('demandes.offres');

    // Propositions - ON GARDE L'ID CAR CE SONT DES RELATIONS INTERNES
    Route::get('/propositions', [ParticulierPropositionController::class, 'index'])->name('propositions.index');
    Route::get('/propositions/{proposition}', [ParticulierPropositionController::class, 'show'])->name('propositions.show');
    Route::post('/propositions/{proposition}/selectionner', [ParticulierPropositionController::class, 'selectionner'])->name('propositions.selectionner');
    
    // ✅ AJOUTER CETTE ROUTE POUR REFUSER UNE PROPOSITION
    Route::post('/propositions/{proposition}/refuser', [ParticulierPropositionController::class, 'refuser'])->name('propositions.refuser');

    // Rendez-vous - ON GARDE L'ID
    Route::get('/rendezvous', [ParticulierRendezVousController::class, 'index'])->name('rendezvous.index');
    Route::get('/rendezvous/create/{proposition}', [ParticulierRendezVousController::class, 'create'])->name('rendezvous.create');
    Route::post('/rendezvous', [ParticulierRendezVousController::class, 'store'])->name('rendezvous.store');
    Route::get('/rendezvous/{rendezVous}', [ParticulierRendezVousController::class, 'show'])->name('rendezvous.show');
    Route::post('/rendezvous/{rendezVous}/confirmer', [ParticulierRendezVousController::class, 'confirmer'])->name('rendezvous.confirmer');
    Route::post('/rendezvous/{rendezVous}/annuler', [ParticulierRendezVousController::class, 'annuler'])->name('rendezvous.annuler');

    // ✅ Évaluations
    Route::get('/evaluations', [ParticulierEvaluationController::class, 'index'])->name('evaluations.index');
    Route::get('/evaluations/create/{proposition}', [ParticulierEvaluationController::class, 'create'])->name('evaluations.create');
    Route::post('/evaluations', [ParticulierEvaluationController::class, 'store'])->name('evaluations.store');
    Route::get('/evaluations/{evaluation}', [ParticulierEvaluationController::class, 'show'])->name('evaluations.show');
    Route::get('/evaluations/{evaluation}/edit', [ParticulierEvaluationController::class, 'edit'])->name('evaluations.edit');
    Route::put('/evaluations/{evaluation}', [ParticulierEvaluationController::class, 'update'])->name('evaluations.update');
    Route::delete('/evaluations/{evaluation}', [ParticulierEvaluationController::class, 'destroy'])->name('evaluations.destroy');

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

        // Demandes (consultation) - AVEC SLUG
        Route::get('/demandes', [AgenceController::class, 'demandes'])->name('demandes.index');
        Route::get('/demandes/{demande:slug}', [AgenceController::class, 'demandesShow'])->name('demandes.show');

        // Profil
        Route::get('/profil', [AgenceController::class, 'profil'])->name('profil');
        Route::put('/profil', [AgenceController::class, 'update'])->name('profil.update');

        // ============ PLANNING TYPE ET CRÉNEAUX RÉCURRENTS ============
        Route::post('/planning/sauvegarder', [AgenceController::class, 'sauvegarderPlanning'])->name('planning.sauvegarder');
        Route::post('/creneaux/generer-auto', [AgenceController::class, 'genererCreneauxAuto'])->name('creneaux.generer-auto');
        Route::get('/planning/get', [AgenceController::class, 'getPlanningType'])->name('planning.get');

        // ============ CRÉNEAUX RENDEZ-VOUS ============
        Route::get('/creneaux', [AgenceController::class, 'creneauxIndex'])->name('creneaux.index');
        Route::post('/creneaux/generer', [AgenceController::class, 'genererCreneaux'])->name('creneaux.generer');
        Route::post('/creneaux/{creneau}/toggle', [AgenceController::class, 'toggleCreneau'])->name('creneaux.toggle');
        Route::delete('/creneaux/{creneau}', [AgenceController::class, 'supprimerCreneau'])->name('creneaux.supprimer');
        Route::delete('/creneaux/date', [AgenceController::class, 'supprimerCreneauxDate'])->name('creneaux.supprimerDate');
        Route::post('/creneaux/date/supprimer', [AgenceController::class, 'supprimerCreneauxDate'])->name('creneaux.supprimerDatePost');

        // ============ RENDEZ-VOUS - ON GARDE L'ID ============
        Route::get('/rendezvous', [AgenceController::class, 'rendezvous'])->name('rendezvous.index');
        Route::get('/rendezvous/{rendezVous}', [AgenceController::class, 'rendezvousShow'])->name('rendezvous.show');
        Route::put('/rendezvous/{rendezVous}', [AgenceController::class, 'rendezvousUpdate'])->name('rendezvous.update');
        Route::post('/rendezvous/{rendezVous}/confirmer', [AgenceController::class, 'rendezvousConfirmer'])->name('rendezvous.confirmer');
        Route::post('/rendezvous/{rendezVous}/annuler', [AgenceController::class, 'rendezvousAnnuler'])->name('rendezvous.annuler');
        Route::post('/rendezvous/{rendezVous}/termine', [AgenceController::class, 'rendezvousTermine'])->name('rendezvous.termine');

        // Évaluations - ON GARDE L'ID
        Route::get('/evaluations', [AgenceController::class, 'evaluations'])->name('evaluations.index');
        Route::get('/evaluations/{evaluation}', [AgenceController::class, 'evaluationsShow'])->name('evaluations.show');
        Route::post('/evaluations/{evaluation}/repondre', [AgenceController::class, 'evaluationsRepondre'])->name('evaluations.repondre');

        // Abonnement - ON GARDE L'ID
        Route::get('/abonnement', [AgenceController::class, 'abonnement'])->name('abonnement');
        Route::post('/abonnement/souscrire', [AgenceController::class, 'souscrire'])->name('abonnement.souscrire');
        Route::post('/abonnement/upgrade', [AgenceController::class, 'upgrade'])->name('abonnement.upgrade');
        Route::post('/abonnement/{abonnement}/annuler', [AgenceController::class, 'annuler'])->name('abonnement.annuler');

        // ============ MISES EN VEDETTE - ON GARDE L'ID ============
        Route::get('/biens/{bien}/vedette/demander', [AgenceController::class, 'demandeVedette'])->name('biens.vedette.demander');
        Route::post('/biens/{bien}/vedette', [AgenceController::class, 'demanderVedette'])->name('biens.vedette.store');
        Route::get('/vedette/contact/{mise}', [AgenceController::class, 'contactVedette'])->name('biens.vedette.contact');

        // Historique
        Route::get('/historique', [AgenceController::class, 'historique'])->name('historique');

        // Biens (avec middleware agence.validee et abonnement.actif) - AVEC SLUG
        Route::middleware(['agence.validee', 'abonnement.actif'])->group(function () {
            Route::get('/biens', [BienController::class, 'index'])->name('biens.index');
            Route::get('/biens/create', [BienController::class, 'create'])->name('biens.create');
            Route::post('/biens', [BienController::class, 'store'])->name('biens.store');
            Route::get('/biens/{bien:slug}', [BienController::class, 'show'])->name('biens.show');
            Route::get('/biens/{bien:slug}/edit', [BienController::class, 'edit'])->name('biens.edit');
            Route::put('/biens/{bien:slug}', [BienController::class, 'update'])->name('biens.update');
            Route::delete('/biens/{bien:slug}', [BienController::class, 'destroy'])->name('biens.destroy');
            Route::post('/biens/{bien:slug}/activer', [BienController::class, 'activer'])->name('biens.activer');
            Route::post('/biens/{bien:slug}/desactiver', [BienController::class, 'desactiver'])->name('biens.desactiver');
            Route::delete('/medias/{media}', [BienController::class, 'supprimerMedia'])->name('medias.destroy');
            Route::patch('biens/{bien:slug}/vedette/toggle', [BienController::class, 'toggleVedette'])->name('biens.vedette.toggle');

            // Propositions - ON GARDE L'ID
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
        Route::get('/statistiques', [AdminStatistiqueController::class, 'index'])->name('statistiques');
        Route::get('/statistiques/export', [AdminStatistiqueController::class, 'export'])->name('statistiques.export');

        // Agences - AVEC SLUG
        Route::get('/agences', [AdminAgenceController::class, 'index'])->name('agences.index');
        Route::get('/agences/{agence:slug}', [AdminAgenceController::class, 'show'])->name('agences.show');
        Route::get('/agences/{agence:slug}/documents', [AdminAgenceController::class, 'documents'])->name('agences.documents');
        Route::post('/agences/{agence:slug}/documents/valider', [AdminAgenceController::class, 'validerDocuments'])->name('agences.valider-documents');
        Route::post('/agences/{agence:slug}/valider', [AdminAgenceController::class, 'valider'])->name('agences.valider');
        Route::post('/agences/{agence:slug}/refuser', [AdminAgenceController::class, 'refuser'])->name('agences.refuser');
        Route::post('/agences/{agence:slug}/reactiver', [AdminAgenceController::class, 'reactiver'])->name('agences.reactiver');
        Route::post('/agences/{agence:slug}/bloquer', [AdminAgenceController::class, 'bloquer'])->name('agences.bloquer');
        Route::post('/agences/{agence:slug}/debloquer', [AdminAgenceController::class, 'debloquer'])->name('agences.debloquer');
        Route::delete('/agences/{agence:slug}', [AdminAgenceController::class, 'destroy'])->name('agences.destroy');

        // Utilisateurs - ON GARDE L'ID (les users n'ont pas de slug)
        Route::get('/utilisateurs', [AdminUtilisateurController::class, 'index'])->name('utilisateurs.index');
        Route::get('/utilisateurs/create', [AdminUtilisateurController::class, 'create'])->name('utilisateurs.create');
        Route::post('/utilisateurs', [AdminUtilisateurController::class, 'store'])->name('utilisateurs.store');
        Route::get('/utilisateurs/{user}', [AdminUtilisateurController::class, 'show'])->name('utilisateurs.show');
        Route::get('/utilisateurs/{user}/edit', [AdminUtilisateurController::class, 'edit'])->name('utilisateurs.edit');
        Route::put('/utilisateurs/{user}', [AdminUtilisateurController::class, 'update'])->name('utilisateurs.update');
        Route::delete('/utilisateurs/{user}', [AdminUtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');
        Route::post('/utilisateurs/{user}/toggle-block', [AdminUtilisateurController::class, 'toggleBlock'])->name('utilisateurs.toggle-block');

        // Quartiers - ON GARDE L'ID
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

        // Signalements - ON GARDE L'ID
        Route::get('/signalements', [AdminSignalementController::class, 'index'])->name('signalements.index');
        Route::get('/signalements/{signalement}', [AdminSignalementController::class, 'show'])->name('signalements.show');
        Route::post('/signalements/{signalement}/traiter', [AdminSignalementController::class, 'traiter'])->name('signalements.traiter');
        Route::post('/signalements/{signalement}/rejeter', [AdminSignalementController::class, 'rejeter'])->name('signalements.rejeter');
        Route::post('/signalements/{signalement}/sanctionner', [AdminSignalementController::class, 'sanctionner'])->name('signalements.sanctionner');

        // Abonnements - ON GARDE L'ID
        Route::get('/abonnements', [AdminAbonnementController::class, 'index'])->name('abonnements.index');
        Route::get('/abonnements/{abonnement}', [AdminAbonnementController::class, 'show'])->name('abonnements.show');
        Route::put('/abonnements/{abonnement}/activer', [AdminAbonnementController::class, 'activer'])->name('abonnements.activer');
        Route::put('/abonnements/{abonnement}/desactiver', [AdminAbonnementController::class, 'desactiver'])->name('abonnements.desactiver');
        Route::delete('/abonnements/{abonnement}', [AdminAbonnementController::class, 'destroy'])->name('abonnements.destroy');

        // Biens - AVEC SLUG
        Route::get('/biens', [AdminBienController::class, 'index'])->name('biens.index');
        Route::get('/biens/vedette', [AdminBienController::class, 'vedette'])->name('biens.vedette');
        Route::get('/biens/{bien:slug}', [AdminBienController::class, 'show'])->name('biens.show');
        Route::post('/biens/{bien:slug}/desactiver', [AdminBienController::class, 'desactiver'])->name('biens.desactiver');
        Route::post('/biens/{bien:slug}/activer', [AdminBienController::class, 'activer'])->name('biens.activer');
        Route::delete('/biens/{bien:slug}', [AdminBienController::class, 'destroy'])->name('biens.destroy');

        // Vedette - ON GARDE L'ID POUR LES ACTIONS
        Route::post('/biens/{bien:slug}/vedette', [AdminBienController::class, 'mettreEnVedette'])->name('biens.vedette.mettre');
        Route::delete('/biens/{bien:slug}/vedette', [AdminBienController::class, 'retirerVedette'])->name('biens.vedette.retirer');
        Route::post('/biens/{bien:slug}/vedette/prolonger', [AdminBienController::class, 'prolongerVedette'])->name('biens.vedette.prolonger');

        Route::get('/mises-vedette', [AdminMiseEnVedetteController::class, 'index'])->name('mises-vedette.index');
        Route::get('/mises-vedette/{mise}', [AdminMiseEnVedetteController::class, 'show'])->name('mises-vedette.show');
        Route::post('/mises-vedette/{mise}/valider', [AdminMiseEnVedetteController::class, 'valider'])->name('mises-vedette.valider');
        Route::post('/mises-vedette/{mise}/annuler', [AdminMiseEnVedetteController::class, 'annuler'])->name('mises-vedette.annuler');

        // Demandes - AVEC SLUG
        Route::get('/demandes', [AdminDemandeController::class, 'index'])->name('demandes.index');
        Route::get('/demandes/{demande:slug}', [AdminDemandeController::class, 'show'])->name('demandes.show');
        Route::delete('/demandes/{demande:slug}', [AdminDemandeController::class, 'destroy'])->name('demandes.destroy');

        // Propositions (lecture seule) - ON GARDE L'ID
        Route::get('/propositions', [AdminPropositionController::class, 'index'])->name('propositions.index');
        Route::get('/propositions/{proposition}', [AdminPropositionController::class, 'show'])->name('propositions.show');

        // Rendez-vous (lecture seule) - ON GARDE L'ID
        Route::get('/rendezvous', [AdminRendezVousController::class, 'index'])->name('rendezvous.index');
        Route::get('/rendezvous/{rendezVous}', [AdminRendezVousController::class, 'show'])->name('rendezvous.show');

        // Profil
        Route::get('/profil', [AdminProfilController::class, 'index'])->name('profil');
        Route::put('/profil', [AdminProfilController::class, 'update'])->name('profil.update');
        Route::put('/profil/password', [AdminProfilController::class, 'updatePassword'])->name('profil.password');

        // Bannières - ON GARDE L'ID
        Route::get('/bannieres', [AdminBanniereController::class, 'index'])->name('bannieres.index');
        Route::get('/bannieres/create', [AdminBanniereController::class, 'create'])->name('bannieres.create');
        Route::post('/bannieres', [AdminBanniereController::class, 'store'])->name('bannieres.store');
        Route::get('/bannieres/{banniere}', [AdminBanniereController::class, 'show'])->name('bannieres.show');
        Route::get('/bannieres/{banniere}/edit', [AdminBanniereController::class, 'edit'])->name('bannieres.edit');
        Route::put('/bannieres/{banniere}', [AdminBanniereController::class, 'update'])->name('bannieres.update');
        Route::delete('/bannieres/{banniere}', [AdminBanniereController::class, 'destroy'])->name('bannieres.destroy');
        Route::post('/bannieres/{banniere}/toggle', [AdminBanniereController::class, 'toggleActif'])->name('bannieres.toggle');
        Route::post('/bannieres/reordonner', [AdminBanniereController::class, 'reordonner'])->name('bannieres.reordonner');
    });
});