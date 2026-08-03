<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Agence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    /**
     * Affiche la liste des évaluations de l'agence
     */
    public function index()
    {
        $agence = Auth::user()->agence;

        $evaluations = Evaluation::with(['particulier.user'])
            ->where('agence_id', $agence->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => $agence->evaluations()->count(),
            'note_moyenne' => $agence->evaluations()->avg('note') ?? 0,
            'repartition' => [
                '1' => $agence->evaluations()->where('note', 1)->count(),
                '2' => $agence->evaluations()->where('note', 2)->count(),
                '3' => $agence->evaluations()->where('note', 3)->count(),
                '4' => $agence->evaluations()->where('note', 4)->count(),
                '5' => $agence->evaluations()->where('note', 5)->count(),
            ],
            'derniers_mois' => $agence->evaluations()
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as mois, AVG(note) as moyenne, COUNT(*) as total')
                ->groupBy('mois')
                ->orderBy('mois', 'desc')
                ->limit(6)
                ->get(),
        ];

        return view('agence.evaluations.index', compact('evaluations', 'stats'));
    }

    /**
     * Affiche les détails d'une évaluation
     */
    public function show(Evaluation $evaluation)
    {
        $agence = Auth::user()->agence;

        if ($evaluation->agence_id !== $agence->id) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette évaluation.');
        }

        $evaluation->load(['particulier.user', 'agence.user']);
        return view('agence.evaluations.show', compact('evaluation'));
    }

    /**
     * Répond à une évaluation (commentaire de l'agence)
     */
    public function repondre(Request $request, Evaluation $evaluation)
    {
        $agence = Auth::user()->agence;

        if ($evaluation->agence_id !== $agence->id) {
            abort(403);
        }

        $request->validate([
            'reponse' => 'required|string|max:1000',
        ]);

        $evaluation->update([
            'reponse_agence' => $request->reponse,
            'date_reponse' => now(),
        ]);

        return redirect()->route('agence.evaluations.show', $evaluation)
            ->with('success', 'Réponse envoyée avec succès.');
    }

    /**
     * Affiche le tableau de bord des évaluations
     */
    public function dashboard()
    {
        $agence = Auth::user()->agence;

        $stats = [
            'total' => $agence->evaluations()->count(),
            'note_moyenne' => $agence->evaluations()->avg('note') ?? 0,
            'note_min' => $agence->evaluations()->min('note') ?? 0,
            'note_max' => $agence->evaluations()->max('note') ?? 0,
            'avec_commentaire' => $agence->evaluations()->whereNotNull('commentaire')->count(),
            'avec_reponse' => $agence->evaluations()->whereNotNull('reponse_agence')->count(),
        ];

        $dernieresEvals = $agence->evaluations()
            ->with('particulier.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $evolution = $agence->evaluations()
            ->selectRaw('DATE(created_at) as date, AVG(note) as moyenne, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $motsCles = $this->extractKeywords($agence->evaluations()->pluck('commentaire')->toArray());

        return view('agence.evaluations.dashboard', compact('stats', 'dernieresEvals', 'evolution', 'motsCles'));
    }

    /**
     * Extrait les mots-clés des commentaires
     */
    private function extractKeywords(array $commentaires): array
    {
        $mots = [];
        $stopWords = ['le', 'la', 'les', 'un', 'une', 'des', 'et', 'ou', 'mais', 'donc', 'car', 'pour', 'avec', 'sans', 'par', 'dans'];

        foreach ($commentaires as $commentaire) {
            if (empty($commentaire)) continue;
            
            $motsBruts = preg_split('/[\s,;.!?]+/', strtolower($commentaire));
            
            foreach ($motsBruts as $mot) {
                if (strlen($mot) > 3 && !in_array($mot, $stopWords)) {
                    if (!isset($mots[$mot])) {
                        $mots[$mot] = 0;
                    }
                    $mots[$mot]++;
                }
            }
        }

        arsort($mots);
        return array_slice($mots, 0, 20);
    }

    /**
     * Exporte les évaluations au format CSV
     */
    public function export(Request $request)
    {
        $agence = Auth::user()->agence;

        $format = $request->format ?? 'csv';

        $evaluations = Evaluation::with('particulier.user')
            ->where('agence_id', $agence->id)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($format === 'csv') {
            return $this->exportCSV($evaluations);
        }

        if ($format === 'excel') {
            return $this->exportExcel($evaluations);
        }

        return back()->with('error', 'Format d\'export non supporté.');
    }

    /**
     * Export en CSV
     */
    private function exportCSV($evaluations)
    {
        $filename = 'evaluations_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://temp', 'w+');

        // En-têtes
        fputcsv($handle, [
            'Date',
            'Particulier',
            'Note',
            'Commentaire',
            'Réponse de l\'agence'
        ]);

        // Données
        foreach ($evaluations as $eval) {
            fputcsv($handle, [
                $eval->created_at->format('d/m/Y H:i'),
                $eval->particulier->user->full_name ?? 'Anonyme',
                $eval->note . '/5',
                $eval->commentaire ?? '-',
                $eval->reponse_agence ?? '-'
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

    /**
     * Export en Excel (via un package comme maatwebsite/excel)
     */
    private function exportExcel($evaluations)
    {
        // Si vous utilisez Laravel Excel
        // return Excel::download(new EvaluationsExport($evaluations), 'evaluations.xlsx');
        
        // Fallback: export CSV
        return $this->exportCSV($evaluations);
    }
}