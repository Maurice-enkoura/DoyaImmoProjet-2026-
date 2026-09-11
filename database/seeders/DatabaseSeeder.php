<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Administrateur;
use App\Models\Particulier;
use App\Models\Agence;
use App\Models\DemandeImmobiliere;
use App\Models\BienImmobilier;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Evaluation;
use App\Models\Abonnement;
use App\Models\DocumentAgence;
use App\Models\Media;
use App\Models\Quartier;
use App\Enums\RoleEnum;
use App\Enums\TypeDocumentEnum;
use App\Enums\StatutDocumentEnum;
use App\Enums\StatutRendezVousEnum;
use App\Enums\FormuleAbonnementEnum;
use App\Enums\TypeBienEnum;
use App\Enums\TypeOperationEnum;
use App\Enums\StatutDemandeEnum;
use App\Enums\StatutPropositionEnum;
use App\Enums\TypeContratEnum;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;

class DatabaseSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create('fr_FR');
    }

    public function run(): void
    {
        $this->command->info('Début du seeding de la base de données...');

        // 1. Création des quartiers par défaut
        $this->command->info('Création des quartiers...');
        $this->createDefaultQuartiers();

        // 2. Admin
        $this->command->info('Création de l\'administrateur...');
        $adminUser = User::create([
            'nom' => 'Admin',
            'prenom' => 'Super',
            'email' => 'admin@doyaimmo.com',
            'telephone' => '+221 78 123 45 67',
            'mot_de_passe' => Hash::make('admin123'),
            'role' => RoleEnum::ADMIN->value,
            'email_verified_at' => now(),
        ]);

        $admin = Administrateur::create([
            'user_id' => $adminUser->id,
            'fonction' => 'Administrateur Principal',
        ]);

        // 3. Récupération des IDs des quartiers
        $quartiers = Quartier::pluck('id')->toArray();

        // 4. Particuliers
        $this->command->info('Création des particuliers...');
        $particuliers = [];

        for ($i = 0; $i < 10; $i++) {
            $user = User::factory()->particulier()->create();
            $quartierId = $this->faker->randomElement($quartiers);
            $quartierNom = Quartier::find($quartierId)->nom;
            
            $particulier = Particulier::create([
                'user_id' => $user->id,
                'profession' => $this->faker->jobTitle,
                'adresse' => $this->faker->streetAddress,
                'quartier' => $quartierNom,
                'quartier_id' => $quartierId,
            ]);
            $particuliers[] = $particulier;
        }

        // 5. Agences
        $this->command->info('Création des agences...');
        $agences = [];

        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->agence()->create();
            $quartierId = $this->faker->randomElement($quartiers);
            $quartierNom = Quartier::find($quartierId)->nom;
            
            // ✅ SLUG COURT pour l'agence
            $slug = 'agence-' . ($i + 1) . '-' . Str::random(8);
            
            $agence = Agence::create([
                'user_id' => $user->id,
                'nom_agence' => $this->faker->company . ' Immobilier',
                'adresse' => $this->faker->streetAddress,
                'quartier' => $quartierNom,
                'quartier_id' => $quartierId,
                'description' => $this->faker->paragraphs(3, true),
                'logo' => null,
                'statut_validation' => $i < 4,
                'slug' => $slug,
            ]);
            $agences[] = $agence;

            // Documents pour l'agence
            $types = TypeDocumentEnum::cases();
            foreach ($types as $type) {
                DocumentAgence::create([
                    'agence_id' => $agence->id,
                    'type_document' => $type->value,
                    'nom_fichier' => 'documents/sample_' . $type->value . '.pdf',
                    'statut_validation' => $i < 4 ? StatutDocumentEnum::VALIDE->value : StatutDocumentEnum::EN_ATTENTE->value,
                    'valide_par' => $i < 4 ? $admin->id : null,
                    'date_validation' => $i < 4 ? now() : null,
                ]);
            }

            // Abonnement pour l'agence
            $formule = $this->faker->randomElement([
                FormuleAbonnementEnum::BASIC, 
                FormuleAbonnementEnum::PRO
            ]);
            
            Abonnement::create([
                'agence_id' => $agence->id,
                'formule' => $formule->value,
                'montant' => $formule->prix(),
                'date_debut' => now()->subMonths(rand(0, 3)),
                'date_fin' => now()->addMonths(rand(1, 6)),
                'statut' => $this->faker->boolean(80),
            ]);
        }

        // 6. Biens immobiliers pour les agences validées
        $this->command->info('Création des biens immobiliers...');
        $typeBiens = TypeBienEnum::cases();
        $typeContrats = TypeContratEnum::cases();

        $bienCounter = 0;
        foreach ($agences as $agence) {
            if ($agence->statut_validation) {
                $nbBiens = rand(2, 5);
                for ($i = 0; $i < $nbBiens; $i++) {
                    $bienCounter++;
                    $quartierId = $this->faker->randomElement($quartiers);
                    $quartierNom = Quartier::find($quartierId)->nom;
                    
                    // ✅ SLUG COURT pour le bien
                    $slug = 'bien-' . $bienCounter . '-' . Str::random(8);
                    
                    $bien = BienImmobilier::create([
                        'agence_id' => $agence->id,
                        'titre' => $this->faker->sentence(3),
                        'slug' => $slug,
                        'type_bien' => $this->faker->randomElement($typeBiens)->value,
                        'type_contrat' => $this->faker->randomElement($typeContrats)->value,
                        'prix' => $this->faker->numberBetween(50000, 500000),
                        'quartier' => $quartierNom,
                        'quartier_id' => $quartierId,
                        'adresse' => $this->faker->streetAddress,
                        'nombre_chambres' => $this->faker->numberBetween(0, 5),
                        'nombre_salles_bain' => $this->faker->numberBetween(0, 3),
                        'surface' => $this->faker->numberBetween(30, 300),
                        'parking_disponible' => $this->faker->boolean,
                        'est_meuble' => $this->faker->boolean,
                        'description' => $this->faker->paragraphs(2, true),
                        'statut' => true,
                    ]);

                    // Ajouter des médias
                    for ($m = 0; $m < rand(1, 3); $m++) {
                        Media::create([
                            'agence_id' => $agence->id,
                            'mediable_id' => $bien->id,
                            'mediable_type' => BienImmobilier::class,
                            'type_media' => 'image',
                            'fichier' => 'biens/images/sample_' . $m . '.jpg',
                        ]);
                    }
                }
            }
        }

        // 7. Demandes immobilières
        $this->command->info('Création des demandes immobilières...');
        $statutsDemande = StatutDemandeEnum::cases();

        $demandeCounter = 0;
        foreach ($particuliers as $particulier) {
            for ($j = 0; $j < rand(1, 3); $j++) {
                $demandeCounter++;
                $quartierId = $this->faker->randomElement($quartiers);
                $quartierNom = Quartier::find($quartierId)->nom;
                
                // ✅ SLUG COURT pour la demande
                $slug = 'demande-' . $demandeCounter . '-' . Str::random(8);
                
                
                $demande = DemandeImmobiliere::create([
                    'particulier_id' => $particulier->id,
                    'type_operation' => $this->faker->randomElement(TypeOperationEnum::cases())->value,
                    'type_bien' => $this->faker->randomElement($typeBiens)->value,
                    'budget_maximum' => $this->faker->numberBetween(50000, 500000),
                    'zone_recherchee' => $quartierNom,
                    'quartier_id' => $quartierId,
                    'nombre_chambres' => $this->faker->numberBetween(0, 5),
                    'surface_minimum' => $this->faker->numberBetween(30, 300),
                    'date_entree_souhaitee' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
                    'criteres_particuliers' => $this->faker->paragraph,
                    'description' => $this->faker->paragraphs(2, true),
                    'statut' => $this->faker->randomElement($statutsDemande)->value,
                    'slug' => $slug,
                ]);

                // Des propositions sur cette demande
                $nbPropositions = rand(0, 3);
                $selectedAgences = $this->faker->randomElements($agences, min($nbPropositions, count($agences)));

                foreach ($selectedAgences as $agence) {
                    // Récupérer un bien aléatoire de l'agence
                    $bien = $agence->biens()->inRandomOrder()->first();
                    if (!$bien) continue;

                    $proposition = Proposition::create([
                        'demande_id' => $demande->id,
                        'agence_id' => $agence->id,
                        'bien_id' => $bien->id,
                        'particulier_id' => $particulier->id,
                        'prix_propose' => $demande->budget_maximum * (0.8 + (rand(0, 40) / 100)),
                        'message' => $this->faker->paragraph,
                        'statut' => $this->faker->randomElement(StatutPropositionEnum::cases())->value,
                    ]);

                    // Si la proposition est acceptée, créer un rendez-vous
                    if ($proposition->statut === StatutPropositionEnum::ACCEPTEE->value) {
                        $statutRdv = $this->faker->randomElement([
                            StatutRendezVousEnum::PLANIFIE,
                            StatutRendezVousEnum::CONFIRME,
                            StatutRendezVousEnum::TERMINE,
                        ]);
                        
                        RendezVous::create([
                            'proposition_id' => $proposition->id,
                            'particulier_id' => $particulier->id,
                            'agence_id' => $agence->id,
                            'date_visite' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
                            'heure_visite' => $this->faker->time('H:i'),
                            'statut' => $statutRdv->value,
                        ]);
                    }
                }
            }
        }

        // 8. Évaluations
        $this->command->info('Création des évaluations...');
        foreach ($particuliers as $particulier) {
            $agencesPourEvaluation = $this->faker->randomElements($agences, rand(1, 3));
            foreach ($agencesPourEvaluation as $agence) {
                $aEuRendezVous = RendezVous::where('particulier_id', $particulier->id)
                    ->where('agence_id', $agence->id)
                    ->exists();

                if ($aEuRendezVous) {
                    Evaluation::create([
                        'particulier_id' => $particulier->id,
                        'agence_id' => $agence->id,
                        'note' => $this->faker->numberBetween(1, 5),
                        'commentaire' => $this->faker->sentence,
                        'date_evaluation' => $this->faker->dateTimeBetween('-6 months', 'now'),
                    ]);
                }
            }
        }

        // 9. Utilisateur de test (Particulier)
        $this->command->info('Création des utilisateurs de test...');
        $quartierAlmadies = Quartier::where('nom', 'Almadies')->first();
        $quartierTestId = $quartierAlmadies ? $quartierAlmadies->id : $quartiers[0];
        
        $testUser = User::create([
            'nom' => 'Test',
            'prenom' => 'Particulier',
            'email' => 'test@doyaimmo.com',
            'telephone' => '+221 77 123 45 67',
            'mot_de_passe' => Hash::make('test123'),
            'role' => RoleEnum::PARTICULIER->value,
            'email_verified_at' => now(),
        ]);

        Particulier::create([
            'user_id' => $testUser->id,
            'profession' => 'Développeur',
            'adresse' => '123 Rue Test, Dakar',
            'quartier' => 'Almadies',
            'quartier_id' => $quartierTestId,
        ]);

        // 10. Agence de test
        $quartierNgor = Quartier::where('nom', 'Ngor')->first();
        $quartierNgorId = $quartierNgor ? $quartierNgor->id : $quartiers[0];
        
        $testAgenceUser = User::create([
            'nom' => 'Test',
            'prenom' => 'Agence',
            'email' => 'agence@doyaimmo.com',
            'telephone' => '+221 76 123 45 67',
            'mot_de_passe' => Hash::make('test123'),
            'role' => RoleEnum::AGENCE->value,
            'email_verified_at' => now(),
        ]);

        $testAgence = Agence::create([
            'user_id' => $testAgenceUser->id,
            'nom_agence' => 'DoyaImmo Test Agency',
            'adresse' => '456 Avenue Test, Dakar',
            'quartier' => 'Ngor',
            'quartier_id' => $quartierNgorId,
            'description' => 'Agence immobilière de test spécialisée dans les biens de luxe.',
            'statut_validation' => true,
            'slug' => 'doyaimmo-test-agency',
        ]);

        // Documents pour l'agence de test
        foreach (TypeDocumentEnum::cases() as $type) {
            DocumentAgence::create([
                'agence_id' => $testAgence->id,
                'type_document' => $type->value,
                'nom_fichier' => 'documents/test_' . $type->value . '.pdf',
                'statut_validation' => StatutDocumentEnum::VALIDE->value,
                'valide_par' => $admin->id,
                'date_validation' => now(),
            ]);
        }

        // Abonnement PRO pour l'agence de test
        Abonnement::create([
            'agence_id' => $testAgence->id,
            'formule' => FormuleAbonnementEnum::PRO->value,
            'montant' => FormuleAbonnementEnum::PRO->prix(),
            'date_debut' => now(),
            'date_fin' => now()->addMonths(3),
            'statut' => true,
        ]);

        // 11. Création de biens pour l'agence de test
        $this->command->info('Création des biens pour l\'agence de test...');
        $quartierDiamniadio = Quartier::where('nom', 'Diamniadio')->first();
        $quartierDiamniadioId = $quartierDiamniadio ? $quartierDiamniadio->id : $quartiers[0];

        $biensTest = [
            [
                'titre' => 'Magnifique Villa à Ngor',
                'slug' => 'villa-ngor-luxe',
                'type_bien' => TypeBienEnum::VILLA->value,
                'type_contrat' => TypeContratEnum::VENTE->value,
                'prix' => 250000000,
                'quartier' => 'Ngor',
                'quartier_id' => $quartierNgorId,
                'adresse' => 'Avenue des Almadies, Ngor',
                'nombre_chambres' => 5,
                'nombre_salles_bain' => 4,
                'surface' => 350,
                'parking_disponible' => true,
                'est_meuble' => true,
                'description' => 'Superbe villa avec piscine et vue sur la mer. Idéale pour une famille nombreuse.',
            ],
            [
                'titre' => 'Appartement de Luxe aux Almadies',
                'slug' => 'appart-almadies',
                'type_bien' => TypeBienEnum::APPARTEMENT->value,
                'type_contrat' => TypeContratEnum::LOCATION->value,
                'prix' => 500000,
                'quartier' => 'Almadies',
                'quartier_id' => $quartierTestId,
                'adresse' => 'Rue des Almadies, Dakar',
                'nombre_chambres' => 3,
                'nombre_salles_bain' => 2,
                'surface' => 150,
                'parking_disponible' => true,
                'est_meuble' => true,
                'description' => 'Appartement moderne avec terrasse et vue sur l\'océan.',
            ],
            [
                'titre' => 'Terrain à Diamniadio',
                'slug' => 'terrain-diamniadio',
                'type_bien' => TypeBienEnum::TERRAIN->value,
                'type_contrat' => TypeContratEnum::VENTE->value,
                'prix' => 75000000,
                'quartier' => 'Diamniadio',
                'quartier_id' => $quartierDiamniadioId,
                'adresse' => 'Zone A, Diamniadio',
                'nombre_chambres' => 0,
                'nombre_salles_bain' => 0,
                'surface' => 500,
                'parking_disponible' => true,
                'est_meuble' => false,
                'description' => 'Terrain idéal pour construction résidentielle ou commerciale.',
            ],
            [
                'titre' => 'Studio Meublé à Ouakam',
                'slug' => 'studio-ouakam',
                'type_bien' => TypeBienEnum::STUDIO->value,
                'type_contrat' => TypeContratEnum::LOCATION->value,
                'prix' => 250000,
                'quartier' => 'Ouakam',
                'quartier_id' => Quartier::where('nom', 'Ouakam')->first()->id ?? $quartiers[0],
                'adresse' => 'Rue de Ouakam, Dakar',
                'nombre_chambres' => 1,
                'nombre_salles_bain' => 1,
                'surface' => 45,
                'parking_disponible' => false,
                'est_meuble' => true,
                'description' => 'Studio moderne entièrement meublé. Idéal pour un étudiant.',
            ],
        ];

        foreach ($biensTest as $bienData) {
            $bien = BienImmobilier::create(array_merge($bienData, ['agence_id' => $testAgence->id, 'statut' => true]));
            
            // Ajouter des médias
            for ($m = 0; $m < rand(1, 3); $m++) {
                Media::create([
                    'agence_id' => $testAgence->id,
                    'mediable_id' => $bien->id,
                    'mediable_type' => BienImmobilier::class,
                    'type_media' => 'image',
                    'fichier' => 'biens/images/test_' . $m . '.jpg',
                ]);
            }
        }

        // 12. Création de quelques signalements de test
        $this->command->info('Création des signalements de test...');
        foreach ($particuliers as $key => $particulier) {
            if ($key < 3) {
                $biens = BienImmobilier::inRandomOrder()->limit(1)->get();
                foreach ($biens as $bien) {
                    \App\Models\Signalement::create([
                        'particulier_id' => $particulier->id,
                        'signalable_id' => $bien->id,
                        'signalable_type' => BienImmobilier::class,
                        'motif' => $this->faker->randomElement(['fraude', 'contenu_inapproprie', 'arnaque']),
                        'description' => $this->faker->paragraph,
                        'statut' => $this->faker->randomElement(['en_attente', 'traite', 'rejete']),
                        'date_signalement' => now(),
                    ]);
                }
            }
        }

        $this->command->info('Seeding terminé avec succès !');
        $this->command->info('========================================');
        $this->command->info('Comptes de test:');
        $this->command->info('  - Admin: admin@doyaimmo.com / admin123');
        $this->command->info('  - Particulier: test@doyaimmo.com / test123');
        $this->command->info('  - Agence: agence@doyaimmo.com / test123');
        $this->command->info('========================================');
        $this->command->info('Nombre total d\'enregistrements créés:');
        $this->command->info('  - Utilisateurs: ' . User::count());
        $this->command->info('  - Quartiers: ' . Quartier::count());
        $this->command->info('  - Agences: ' . Agence::count());
        $this->command->info('  - Biens immobiliers: ' . BienImmobilier::count());
        $this->command->info('  - Demandes: ' . DemandeImmobiliere::count());
        $this->command->info('  - Propositions: ' . Proposition::count());
        $this->command->info('  - Rendez-vous: ' . RendezVous::count());
        $this->command->info('  - Évaluations: ' . Evaluation::count());
        $this->command->info('  - Abonnements: ' . Abonnement::count());
    }

    /**
     * Crée les quartiers par défaut de Dakar
     */
    private function createDefaultQuartiers(): void
    {
        $quartiers = [
            // Dakar Plateau
            ['nom' => 'Plateau', 'ville' => 'Dakar'],
            ['nom' => 'Médina', 'ville' => 'Dakar'],
            ['nom' => 'Gueule Tapée', 'ville' => 'Dakar'],
            ['nom' => 'Fass', 'ville' => 'Dakar'],
            ['nom' => 'Colobane', 'ville' => 'Dakar'],
            ['nom' => 'Hlm', 'ville' => 'Dakar'],
            ['nom' => 'Grand Dakar', 'ville' => 'Dakar'],
            ['nom' => 'Dieuppeul', 'ville' => 'Dakar'],
            ['nom' => 'Derklé', 'ville' => 'Dakar'],
            
            // Almadies
            ['nom' => 'Almadies', 'ville' => 'Dakar'],
            ['nom' => 'Ngor', 'ville' => 'Dakar'],
            ['nom' => 'Ouakam', 'ville' => 'Dakar'],
            ['nom' => 'Yoff', 'ville' => 'Dakar'],
            ['nom' => 'Mermoz', 'ville' => 'Dakar'],
            ['nom' => 'Sacré-Cœur', 'ville' => 'Dakar'],
            ['nom' => 'Point E', 'ville' => 'Dakar'],
            ['nom' => 'Fann', 'ville' => 'Dakar'],
            ['nom' => 'Hann', 'ville' => 'Dakar'],
            ['nom' => 'Ameth Fall', 'ville' => 'Dakar'],
            
            // Parcelles Assainies
            ['nom' => 'Parcelles Assainies', 'ville' => 'Dakar'],
            ['nom' => 'Grand Yoff', 'ville' => 'Dakar'],
            ['nom' => 'Cité Cej', 'ville' => 'Dakar'],
            ['nom' => 'Cité SOW', 'ville' => 'Dakar'],
            ['nom' => 'Cité Mbow', 'ville' => 'Dakar'],
            ['nom' => 'Cité CUS', 'ville' => 'Dakar'],
            
            // Ouest Foire
            ['nom' => 'Ouest Foire', 'ville' => 'Dakar'],
            ['nom' => 'Liberté', 'ville' => 'Dakar'],
            ['nom' => 'Mbao', 'ville' => 'Dakar'],
            ['nom' => 'Rufisque', 'ville' => 'Dakar'],
            ['nom' => 'Pikine', 'ville' => 'Dakar'],
            
            // Autres
            ['nom' => 'Thiaroye', 'ville' => 'Dakar'],
            ['nom' => 'Yeumbeul', 'ville' => 'Dakar'],
            ['nom' => 'Keur Massar', 'ville' => 'Dakar'],
            ['nom' => 'Malika', 'ville' => 'Dakar'],
            ['nom' => 'Diamniadio', 'ville' => 'Dakar'],
            ['nom' => 'Sébikotane', 'ville' => 'Dakar'],
            ['nom' => 'Bargny', 'ville' => 'Dakar'],
            ['nom' => 'Golf Sud', 'ville' => 'Dakar'],
            ['nom' => 'Guédiawaye', 'ville' => 'Dakar'],
        ];

        foreach ($quartiers as $quartier) {
            Quartier::firstOrCreate(
                ['nom' => $quartier['nom']],
                [
                    'ville' => $quartier['ville'],
                    'est_actif' => true,
                ]
            );
        }
    }
}