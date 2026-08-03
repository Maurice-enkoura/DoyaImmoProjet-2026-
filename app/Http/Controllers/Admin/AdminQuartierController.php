<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuartierRequest;
use App\Models\Quartier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminQuartierController extends Controller
{
    /**
     * Affiche la liste des quartiers
     */
    public function index(Request $request)
    {
        $query = Quartier::query();

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('ville', 'like', "%{$search}%");
            });
        }

        if ($request->filled('ville')) {
            $query->where('ville', $request->ville);
        }

        if ($request->filled('est_actif')) {
            $query->where('est_actif', $request->boolean('est_actif'));
        }

        $quartiers = $query->orderBy('nom')->paginate(20);

        // Statistiques
        $stats = [
            'total' => Quartier::count(),
            'actifs' => Quartier::where('est_actif', true)->count(),
            'inactifs' => Quartier::where('est_actif', false)->count(),
            'villes' => Quartier::select('ville')->distinct()->pluck('ville'),
        ];

        return view('admin.quartiers.index', compact('quartiers', 'stats'));
    }

    /**
     * Affiche le formulaire de création
     */
    public function create()
    {
        return view('admin.quartiers.create');
    }

    /**
     * Enregistre un nouveau quartier
     */
    public function store(QuartierRequest $request)
    {
        try {
            DB::beginTransaction();

            $quartier = Quartier::create([
                'nom' => $request->nom,
                'ville' => $request->ville,
                'est_actif' => $request->boolean('est_actif'),
            ]);

            DB::commit();

            return redirect()->route('admin.quartiers.index')
                ->with('success', 'Quartier créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création du quartier: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'un quartier
     */
    public function show(Quartier $quartier)
    {
        // Statistiques du quartier
        $stats = [
            'total_demandes' => $quartier->demandes()->count(),
            'demandes_actives' => $quartier->demandes()->where('statut', 'en_attente')->count(),
            'total_biens' => $quartier->biens()->count(),
            'biens_disponibles' => $quartier->biens()->where('statut', true)->count(),
            'total_agences' => $quartier->agences()->count(),
            'agences_validees' => $quartier->agences()->where('statut_validation', true)->count(),
        ];

        $demandesRecentes = $quartier->demandes()
            ->with('particulier.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $biensRecents = $quartier->biens()
            ->with('agence.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.quartiers.show', compact(
            'quartier',
            'stats',
            'demandesRecentes',
            'biensRecents'
        ));
    }

    /**
     * Affiche le formulaire d'édition
     */
    public function edit(Quartier $quartier)
    {
        return view('admin.quartiers.edit', compact('quartier'));
    }

    /**
     * Met à jour un quartier
     */
    public function update(QuartierRequest $request, Quartier $quartier)
    {
        try {
            DB::beginTransaction();

            $quartier->update([
                'nom' => $request->nom,
                'ville' => $request->ville,
                'est_actif' => $request->boolean('est_actif'),
            ]);

            DB::commit();

            return redirect()->route('admin.quartiers.index')
                ->with('success', 'Quartier mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la mise à jour du quartier: ' . $e->getMessage());
        }
    }

    /**
     * Supprime un quartier
     */
    public function destroy(Quartier $quartier)
    {
        try {
            // Vérifier si le quartier est utilisé
            $utilisations = [
                'demandes' => $quartier->demandes()->count(),
                'biens' => $quartier->biens()->count(),
                'agences' => $quartier->agences()->count(),
            ];

            if (array_sum($utilisations) > 0) {
                return back()->with('error', 'Ce quartier est utilisé et ne peut pas être supprimé.');
            }

            $quartier->delete();

            return redirect()->route('admin.quartiers.index')
                ->with('success', 'Quartier supprimé avec succès.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression du quartier: ' . $e->getMessage());
        }
    }

    /**
     * Active/Désactive un quartier
     */
    public function toggleStatut(Quartier $quartier)
    {
        try {
            $quartier->update([
                'est_actif' => !$quartier->est_actif
            ]);

            $statut = $quartier->est_actif ? 'activé' : 'désactivé';

            return redirect()->route('admin.quartiers.index')
                ->with('success', "Quartier {$statut} avec succès.");

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors du changement de statut: ' . $e->getMessage());
        }
    }

    /**
     * Importe les quartiers depuis un fichier (massivement)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $handle = fopen($file->getPathname(), 'r');
            
            // Lire l'en-tête
            $header = fgetcsv($handle);
            
            $count = 0;
            $errors = [];
            
            while (($row = fgetcsv($handle)) !== false) {
                $data = array_combine($header, $row);
                
                try {
                    Quartier::create([
                        'nom' => $data['nom'],
                        'ville' => $data['ville'] ?? 'Dakar',
                        'est_actif' => ($data['est_actif'] ?? 'true') === 'true',
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    $errors[] = "Ligne " . ($count + 2) . ": " . $e->getMessage();
                }
            }
            
            fclose($handle);

            $message = "{$count} quartiers importés avec succès.";
            if (!empty($errors)) {
                $message .= " Erreurs: " . implode(', ', $errors);
            }

            return redirect()->route('admin.quartiers.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'importation: ' . $e->getMessage());
        }
    }

    /**
     * Exporte les quartiers en CSV
     */
    public function export(Request $request)
    {
        $format = $request->format ?? 'csv';
        
        $quartiers = Quartier::orderBy('nom')->get();

        if ($format === 'csv') {
            $filename = 'quartiers_' . date('Y-m-d') . '.csv';
            $handle = fopen('php://temp', 'w+');

            // En-têtes
            fputcsv($handle, ['ID', 'Nom', 'Ville', 'Statut']);

            // Données
            foreach ($quartiers as $quartier) {
                fputcsv($handle, [
                    $quartier->id,
                    $quartier->nom,
                    $quartier->ville,
                    $quartier->est_actif ? 'Actif' : 'Inactif'
                ]);
            }

            rewind($handle);
            $content = stream_get_contents($handle);
            fclose($handle);

            return response($content, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]);
        }

        return back()->with('error', 'Format d\'export non supporté.');
    }

    /**
     * API: Récupère les quartiers pour les formulaires (AJAX)
     */
    public function getQuartiers(Request $request)
    {
        $query = Quartier::actif();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nom', 'like', "%{$search}%");
        }

        if ($request->filled('ville')) {
            $query->where('ville', $request->ville);
        }

        $quartiers = $query->orderBy('nom')
            ->get(['id', 'nom', 'ville']);

        return response()->json($quartiers);
    }

    /**
     * Récupère les villes disponibles (pour les filtres)
     */
    public function getVilles()
    {
        $villes = Quartier::select('ville')
            ->distinct()
            ->orderBy('ville')
            ->pluck('ville');

        return response()->json($villes);
    }
}