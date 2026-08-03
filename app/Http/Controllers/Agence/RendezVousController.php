<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\RendezVous;
use App\Models\Proposition;
use App\Models\Agence;
use App\Models\Particulier;
use App\Http\Requests\RendezVousRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\StatutRendezVousEnum;
use App\Notifications\RendezVousConfirmeNotification;
use App\Notifications\RendezVousAnnuleNotification;
use App\Notifications\RendezVousTermineNotification;

class RendezVousController extends Controller
{
    /**
     * Affiche la liste des rendez-vous de l'agence
     */
    public function index()
    {
        $agence = Auth::user()->agence;

        $rendezVous = RendezVous::with([
            'proposition.demande',
            'proposition.bien',
            'particulier.user'
        ])
        ->where('agence_id', $agence->id)
        ->orderBy('date_visite', 'asc')
        ->paginate(10);

        $stats = [
            'total' => $agence->rendezVous()->count(),
            'planifies' => $agence->rendezVous()->where('statut', StatutRendezVousEnum::PLANIFIE)->count(),
            'confirmes' => $agence->rendezVous()->where('statut', StatutRendezVousEnum::CONFIRME)->count(),
            'termines' => $agence->rendezVous()->where('statut', StatutRendezVousEnum::TERMINE)->count(),
            'annules' => $agence->rendezVous()->where('statut', StatutRendezVousEnum::ANNULE)->count(),
        ];

        return view('agence.rendezvous.index', compact('rendezVous', 'stats'));
    }

    /**
     * Affiche les détails d'un rendez-vous
     */
    public function show(RendezVous $rendezVous)
    {
        $agence = Auth::user()->agence;

        if ($rendezVous->agence_id !== $agence->id) {
            abort(403, 'Vous n\'êtes pas autorisé à voir ce rendez-vous.');
        }

        $rendezVous->load([
            'proposition.demande',
            'proposition.bien.medias',
            'particulier.user'
        ]);

        return view('agence.rendezvous.show', compact('rendezVous'));
    }

    /**
     * Affiche le formulaire de création d'un rendez-vous
     */
    public function create(Proposition $proposition)
    {
        $agence = Auth::user()->agence;

        if ($proposition->agence_id !== $agence->id) {
            abort(403);
        }

        if ($proposition->statut !== 'acceptee') {
            return back()->with('error', 'Cette proposition n\'est pas acceptée.');
        }

        // Vérifier si un rendez-vous existe déjà
        $existant = RendezVous::where('proposition_id', $proposition->id)->first();
        if ($existant) {
            return redirect()->route('agence.rendezvous.show', $existant)
                ->with('info', 'Un rendez-vous existe déjà pour cette proposition.');
        }

        return view('agence.rendezvous.create', compact('proposition'));
    }

    /**
     * Enregistre un nouveau rendez-vous
     */
    public function store(RendezVousRequest $request)
    {
        $agence = Auth::user()->agence;
        $proposition = Proposition::findOrFail($request->proposition_id);

        if ($proposition->agence_id !== $agence->id) {
            abort(403);
        }

        // Vérifier les disponibilités
        $conflit = RendezVous::where('agence_id', $agence->id)
            ->where('date_visite', $request->date_visite)
            ->where('heure_visite', $request->heure_visite)
            ->where('statut', '!=', StatutRendezVousEnum::ANNULE)
            ->exists();

        if ($conflit) {
            return back()->with('error', 'Un rendez-vous existe déjà à cette date et heure.');
        }

        $rendezVous = RendezVous::create([
            'proposition_id' => $request->proposition_id,
            'particulier_id' => $proposition->particulier_id,
            'agence_id' => $agence->id,
            'date_visite' => $request->date_visite,
            'heure_visite' => $request->heure_visite,
            'statut' => StatutRendezVousEnum::PLANIFIE,
        ]);

        // Notifier le particulier
        $rendezVous->particulier->user->notify(new RendezVousConfirmeNotification($rendezVous));

        return redirect()->route('agence.rendezvous.index')
            ->with('success', 'Rendez-vous planifié avec succès.');
    }

    /**
     * Met à jour le statut d'un rendez-vous
     */
    public function update(Request $request, RendezVous $rendezVous)
    {
        $agence = Auth::user()->agence;

        if ($rendezVous->agence_id !== $agence->id) {
            abort(403);
        }

        $request->validate([
            'statut' => 'required|in:' . implode(',', [
                StatutRendezVousEnum::CONFIRME->value,
                StatutRendezVousEnum::ANNULE->value,
                StatutRendezVousEnum::TERMINE->value
            ]),
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $ancienStatut = $rendezVous->statut;
        $nouveauStatut = StatutRendezVousEnum::from($request->statut);

        $rendezVous->update([
            'statut' => $nouveauStatut,
        ]);

        // Notifications selon le changement de statut
        switch ($nouveauStatut) {
            case StatutRendezVousEnum::CONFIRME:
                $rendezVous->particulier->user->notify(
                    new RendezVousConfirmeNotification($rendezVous)
                );
                break;

            case StatutRendezVousEnum::ANNULE:
                $rendezVous->particulier->user->notify(
                    new RendezVousAnnuleNotification($rendezVous, $request->commentaire)
                );
                break;

            case StatutRendezVousEnum::TERMINE:
                $rendezVous->particulier->user->notify(
                    new RendezVousTermineNotification($rendezVous)
                );
                break;
        }

        $message = 'Statut du rendez-vous mis à jour : ' . $nouveauStatut->label();

        return redirect()->route('agence.rendezvous.index')
            ->with('success', $message);
    }

    /**
     * Confirme un rendez-vous
     */
    public function confirmer(RendezVous $rendezVous)
    {
        $agence = Auth::user()->agence;

        if ($rendezVous->agence_id !== $agence->id) {
            abort(403);
        }

        if ($rendezVous->statut !== StatutRendezVousEnum::PLANIFIE) {
            return back()->with('error', 'Ce rendez-vous ne peut plus être confirmé.');
        }

        $rendezVous->update(['statut' => StatutRendezVousEnum::CONFIRME]);

        $rendezVous->particulier->user->notify(
            new RendezVousConfirmeNotification($rendezVous)
        );

        return redirect()->route('agence.rendezvous.index')
            ->with('success', 'Rendez-vous confirmé avec succès.');
    }

    /**
     * Annule un rendez-vous
     */
    public function annuler(Request $request, RendezVous $rendezVous)
    {
        $agence = Auth::user()->agence;

        if ($rendezVous->agence_id !== $agence->id) {
            abort(403);
        }

        if ($rendezVous->statut === StatutRendezVousEnum::TERMINE) {
            return back()->with('error', 'Impossible d\'annuler un rendez-vous terminé.');
        }

        $request->validate([
            'motif' => 'nullable|string|max:1000',
        ]);

        $rendezVous->update([
            'statut' => StatutRendezVousEnum::ANNULE,
        ]);

        $rendezVous->particulier->user->notify(
            new RendezVousAnnuleNotification($rendezVous, $request->motif)
        );

        return redirect()->route('agence.rendezvous.index')
            ->with('success', 'Rendez-vous annulé avec succès.');
    }

    /**
     * Marque un rendez-vous comme terminé
     */
    public function terminer(RendezVous $rendezVous)
    {
        $agence = Auth::user()->agence;

        if ($rendezVous->agence_id !== $agence->id) {
            abort(403);
        }

        if ($rendezVous->statut === StatutRendezVousEnum::ANNULE) {
            return back()->with('error', 'Impossible de terminer un rendez-vous annulé.');
        }

        $rendezVous->update(['statut' => StatutRendezVousEnum::TERMINE]);

        $rendezVous->particulier->user->notify(
            new RendezVousTermineNotification($rendezVous)
        );

        return redirect()->route('agence.rendezvous.index')
            ->with('success', 'Rendez-vous marqué comme terminé.');
    }

    /**
     * Récupère les rendez-vous à venir
     */
    public function aVenir()
    {
        $agence = Auth::user()->agence;

        $rendezVous = RendezVous::with([
            'proposition.demande',
            'proposition.bien',
            'particulier.user'
        ])
        ->where('agence_id', $agence->id)
        ->where('date_visite', '>=', now()->toDateString())
        ->whereIn('statut', [
            StatutRendezVousEnum::PLANIFIE,
            StatutRendezVousEnum::CONFIRME
        ])
        ->orderBy('date_visite', 'asc')
        ->orderBy('heure_visite', 'asc')
        ->get();

        return response()->json($rendezVous);
    }

    /**
     * Exporte les rendez-vous au format CSV
     */
    public function export()
    {
        $agence = Auth::user()->agence;

        $rendezVous = RendezVous::with(['particulier.user', 'proposition.bien'])
            ->where('agence_id', $agence->id)
            ->orderBy('date_visite', 'desc')
            ->get();

        $filename = 'rendez-vous_' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');

        // En-têtes
        fputcsv($handle, [
            'Date',
            'Heure',
            'Particulier',
            'Email',
            'Téléphone',
            'Bien',
            'Statut'
        ]);

        // Données
        foreach ($rendezVous as $rdv) {
            fputcsv($handle, [
                $rdv->date_visite->format('d/m/Y'),
                $rdv->heure_visite->format('H:i'),
                $rdv->particulier->user->full_name,
                $rdv->particulier->user->email,
                $rdv->particulier->user->telephone ?? '-',
                $rdv->proposition->bien->titre ?? '-',
                $rdv->statut->label()
            ]);
        }

        fclose($handle);

        return response()->stream(
            function () use ($handle) {
                // Le contenu est déjà écrit
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}