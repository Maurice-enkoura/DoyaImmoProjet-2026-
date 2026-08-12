<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banniere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBanniereController extends Controller
{
    public function index()
    {
        $bannieres = Banniere::orderBy('ordre', 'asc')->get();
        return view('admin.bannieres.index', compact('bannieres'));
    }

    public function create()
    {
        return view('admin.bannieres.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'sous_titre' => 'nullable|string|max:255',
            'image' => 'required|image|max:5120',
            'lien' => 'nullable|url|max:255',
            'position_texte' => 'required|in:gauche,centre,droite',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
        ]);

        $imagePath = $request->file('image')->store('bannieres', 'public');

        Banniere::create([
            'titre' => $request->titre,
            'sous_titre' => $request->sous_titre,
            'image' => $imagePath,
            'lien' => $request->lien,
            'ordre' => Banniere::max('ordre') + 1,
            'est_actif' => true,
            'position_texte' => $request->position_texte,
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
        ]);

        return redirect()->route('admin.bannieres.index')
            ->with('success', ' Bannière créée avec succès.');
    }

    public function show(Banniere $banniere)
    {
        return view('admin.bannieres.show', compact('banniere'));
    }

    public function edit(Banniere $banniere)
    {
        return view('admin.bannieres.edit', compact('banniere'));
    }

    public function update(Request $request, Banniere $banniere)
    {
        $request->validate([
            'titre' => 'required|string|max:255',
            'sous_titre' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:5120',
            'lien' => 'nullable|url|max:255',
            'position_texte' => 'required|in:gauche,centre,droite',
            'date_debut' => 'nullable|date',
            'date_fin' => 'nullable|date|after:date_debut',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($banniere->image && Storage::disk('public')->exists($banniere->image)) {
                Storage::disk('public')->delete($banniere->image);
            }
            $data['image'] = $request->file('image')->store('bannieres', 'public');
        }

        $banniere->update($data);

        return redirect()->route('admin.bannieres.index')
            ->with('success', ' Bannière mise à jour avec succès.');
    }

    public function toggleActif(Banniere $banniere)
    {
        $banniere->update(['est_actif' => !$banniere->est_actif]);
        $statut = $banniere->est_actif ? 'activée' : 'désactivée';
        return redirect()->route('admin.bannieres.index')
            ->with('success', " Bannière {$statut} avec succès.");
    }

    public function reordonner(Request $request)
    {
        $request->validate([
            'ordre' => 'required|array',
            'ordre.*' => 'integer|exists:bannieres,id',
        ]);

        foreach ($request->ordre as $index => $id) {
            Banniere::where('id', $id)->update(['ordre' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Banniere $banniere)
    {
        if ($banniere->image && Storage::disk('public')->exists($banniere->image)) {
            Storage::disk('public')->delete($banniere->image);
        }
        $banniere->delete();

        return redirect()->route('admin.bannieres.index')
            ->with('success', ' Bannière supprimée avec succès.');
    }
}