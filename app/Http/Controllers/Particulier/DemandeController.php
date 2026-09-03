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

    /**
     * Affiche le détail d'une demande - UTILISE LE SLUG
     */
    public function show(DemandeImmobiliere $demande)
    {
        if ($demande->particulier->user_id !== Auth::id()) {
            abort(403);
        }

        // Convertir le statut en Enum si c'est une chaîne
        if (is_string($demande->statut)) {
            $demande->statut = StatutDemandeEnum::from($demande->statut);
        }

        $demande->load(['propositions.agence.user', 'propositions.bien']);
        
        // Convertir les statuts des propositions en Enum
        foreach ($demande->propositions as $proposition) {
            if (is_string($proposition->statut)) {
                $proposition->statut = \App\Enums\StatutPropositionEnum::from($proposition->statut);
            }
        }
        
        return view('particulier.demandes.show', compact('demande'));
    }

    /**
     * Formulaire d'édition d'une demande - UTILISE LE SLUG
     */
    public function edit(DemandeImmobiliere $demande)
    {
        if ($demande->particulier->user_id !== Auth::id()) {
            abort(403);
        }

        // Récupérer la valeur du statut (que ce soit un Enum ou une chaîne)
        $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;

        // Vérifier si le statut est "en_attente"
        if ($statutValue !== StatutDemandeEnum::EN_ATTENTE->value) {
            return back()->with('error', 'Cette demande ne peut plus être modifiée.');
        }

        $typesBien = TypeBienEnum::labels();
        $typesOperation = TypeOperationEnum::labels();
        $quartiers = Quartier::actif()->orderBy('nom')->get();

        // Convertir le statut en Enum pour la vue si ce n'est pas déjà fait
        if (is_string($demande->statut)) {
            $demande->statut = StatutDemandeEnum::from($demande->statut);
        }

        return view('particulier.demandes.edit', compact('demande', 'typesBien', 'typesOperation', 'quartiers'));
    }

    /**
     * Met à jour une demande - UTILISE LE SLUG
     */
    public function update(DemandeImmobiliereRequest $request, DemandeImmobiliere $demande)
    {
        if ($demande->particulier->user_id !== Auth::id()) {
            abort(403);
        }

        $statutValue = is_object($demande->statut) ? $demande->statut->value : $demande->statut;

        if ($statutValue !== StatutDemandeEnum::EN_ATTENTE->value) {
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

    /**
     * Supprime/annule une demande - UTILISE LE SLUG
     */
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
        
        $demandes = DemandeImmobiliere::where('particulier_id', $particulier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('particulier.demandes.mes-demandes', compact('demandes'));
    }

    /**
     * Affiche les offres pour une demande - UTILISE LE SLUG
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