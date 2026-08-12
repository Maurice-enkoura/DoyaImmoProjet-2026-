<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Http\Requests\DemandeImmobiliereRequest;
use App\Models\DemandeImmobiliere;
use App\Models\Particulier;
use App\Models\Quartier;
use App\Enums\TypeBienEnum;
use App\Enums\TypeOperationEnum;
use App\Enums\StatutDemandeEnum;
use Illuminate\Support\Facades\Auth;

class DemandeController extends Controller
{
    public function index()
    {
        $particulier = Particulier::where('user_id', Auth::id())->first();
        
        // ✅ Récupérer UNIQUEMENT les demandes actives (en_attente et en_cours)
        $demandes = DemandeImmobiliere::where('particulier_id', $particulier->id)
            ->with('propositions')
            ->whereIn('statut', [
                StatutDemandeEnum::EN_ATTENTE->value,
                StatutDemandeEnum::EN_COURS->value
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('particulier.demandes.index', compact('demandes'));
    }

    public function create()
    {
        $typesBien = TypeBienEnum::labels();
        $typesOperation = TypeOperationEnum::labels();
        $quartiers = Quartier::actif()->orderBy('nom')->get();

        return view('particulier.demandes.create', compact('typesBien', 'typesOperation', 'quartiers'));
    }

    public function store(DemandeImmobiliereRequest $request)
    {
        $particulier = Particulier::where('user_id', Auth::id())->first();

        $demande = DemandeImmobiliere::create([
            'particulier_id' => $particulier->id,
            'type_operation' => $request->type_operation,
            'type_bien' => $request->type_bien,
            'budget_maximum' => $request->budget_maximum,
            'zone_recherchee' => $request->zone_recherchee,
            'quartier_id' => $request->quartier_id,
            'nombre_chambres' => $request->nombre_chambres,
            'nombre_salles_bain' => $request->nombre_salles_bain,
            'surface_minimum' => $request->surface_minimum,
            'parking' => $request->boolean('parking'),
            'meuble' => $request->boolean('meuble'),
            'climatisation' => $request->boolean('climatisation'),
            'balcon' => $request->boolean('balcon'),
            'jardin' => $request->boolean('jardin'),
            'piscine' => $request->boolean('piscine'),
            'ascenseur' => $request->boolean('ascenseur'),
            'securite' => $request->boolean('securite'),
            'date_entree_souhaitee' => $request->date_entree_souhaitee,
            'criteres_particuliers' => $request->criteres_particuliers,
            'description' => $request->description,
            'statut' => StatutDemandeEnum::EN_ATTENTE->value,
        ]);

        return redirect()->route('particulier.demandes.index')
            ->with('success', 'Votre besoin a été publié avec succès.');
    }

    public function show(DemandeImmobiliere $demande)
    {
        if ($demande->particulier->user_id !== Auth::id()) {
            abort(403);
        }

        $demande->load(['propositions.agence.user', 'propositions.bien']);
        return view('particulier.demandes.show', compact('demande'));
    }

    public function edit(DemandeImmobiliere $demande)
    {
        if ($demande->particulier->user_id !== Auth::id()) {
            abort(403);
        }

        if ($demande->statut !== StatutDemandeEnum::EN_ATTENTE->value) {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $typesBien = TypeBienEnum::labels();
        $typesOperation = TypeOperationEnum::labels();
        $quartiers = Quartier::actif()->orderBy('nom')->get();

        return view('particulier.demandes.edit', compact('demande', 'typesBien', 'typesOperation', 'quartiers'));
    }

    public function update(DemandeImmobiliereRequest $request, DemandeImmobiliere $demande)
    {
        if ($demande->particulier->user_id !== Auth::id()) {
            abort(403);
        }

        if ($demande->statut !== StatutDemandeEnum::EN_ATTENTE->value) {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $demande->update([
            'type_operation' => $request->type_operation,
            'type_bien' => $request->type_bien,
            'budget_maximum' => $request->budget_maximum,
            'zone_recherchee' => $request->zone_recherchee,
            'quartier_id' => $request->quartier_id,
            'nombre_chambres' => $request->nombre_chambres,
            'nombre_salles_bain' => $request->nombre_salles_bain,
            'surface_minimum' => $request->surface_minimum,
            'parking' => $request->boolean('parking'),
            'meuble' => $request->boolean('meuble'),
            'climatisation' => $request->boolean('climatisation'),
            'balcon' => $request->boolean('balcon'),
            'jardin' => $request->boolean('jardin'),
            'piscine' => $request->boolean('piscine'),
            'ascenseur' => $request->boolean('ascenseur'),
            'securite' => $request->boolean('securite'),
            'date_entree_souhaitee' => $request->date_entree_souhaitee,
            'criteres_particuliers' => $request->criteres_particuliers,
            'description' => $request->description,
        ]);

        return redirect()->route('particulier.demandes.index')
            ->with('success', 'Votre demande a été mise à jour avec succès.');
    }

    public function destroy(DemandeImmobiliere $demande)
    {
        if ($demande->particulier->user_id !== Auth::id()) {
            abort(403);
        }

        $demande->update(['statut' => StatutDemandeEnum::ANNULEE->value]);

        return redirect()->route('particulier.demandes.index')
            ->with('success', 'Demande annulée avec succès.');
    }

    public function mesDemandes()
    {
        $particulier = Particulier::where('user_id', Auth::id())->first();
        
        // ✅ Récupérer TOUTES les demandes pour l'historique
        $demandes = DemandeImmobiliere::where('particulier_id', $particulier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('particulier.demandes.mes-demandes', compact('demandes'));
    }

    /**
     * Affiche les offres reçues pour une demande
     */
    public function offres(DemandeImmobiliere $demande)
    {
        if ($demande->particulier->user_id !== Auth::id()) {
            abort(403);
        }

        $offres = $demande->propositions()
            ->with(['agence', 'bien'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('particulier.demandes.offres', compact('demande', 'offres'));
    }
}