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

        $stats = [
            'total' => $bannieres->count(),
            'actives' => $bannieres->filter(fn($b) => $b->est_active)->count(),
            'programmees' => $bannieres->filter(fn($b) => $b->statut_reel === 'Programmée')->count(),
            'expirees' => $bannieres->filter(fn($b) => $b->statut_reel === 'Expirée')->count(),
        ];

        return view('admin.bannieres.index', compact('bannieres', 'stats'));
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
            'ordre' => (Banniere::max('ordre') ?? 0) + 1,
            'est_actif' => true,
            'position_texte' => $request->position_texte,
            'date_debut' => $request->date_debut ?? now(),
            'date_fin' => $request->date_fin,
        ]);

        return redirect()->route('admin.bannieres.index')
            ->with('success', 'Bannière créée avec succès.');
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
            ->with('success', 'Bannière mise à jour avec succès.');
    }

    /**
     * Active une bannière (avec reprogrammation si nécessaire)
     */
    public function activer(Request $request, Banniere $banniere)
    {
        if ($request->has('date_debut')) {
            $request->validate([
                'date_debut' => 'required|date',
                'date_fin' => 'nullable|date|after:date_debut',
            ]);

            $banniere->update([
                'est_actif' => true,
                'date_debut' => $request->date_debut,
                'date_fin' => $request->date_fin,
            ]);

            return redirect()->route('admin.bannieres.index')
                ->with('success', 'Bannière activée et reprogrammée avec succès.');
        }

        $banniere->update(['est_actif' => true]);

        return redirect()->route('admin.bannieres.index')
            ->with('success', 'Bannière activée avec succès.');
    }

    /**
     * Désactive une bannière
     */
    public function desactiver(Banniere $banniere)
    {
        $banniere->update(['est_actif' => false]);

        return redirect()->route('admin.bannieres.index')
            ->with('success', 'Bannière désactivée avec succès.');
    }

    /**
     * Toggle : active ou désactive
     */
    public function toggleActif(Request $request, Banniere $banniere)
    {
        if ($banniere->est_actif) {
            return $this->desactiver($banniere);
        }

        if ($banniere->necessiteReprogrammation() && !$request->has('date_debut')) {
            return redirect()->route('admin.bannieres.index')
                ->with('reprogrammer_id', $banniere->id)
                ->with('reprogrammer_titre', $banniere->titre)
                ->with('reprogrammer_date_debut', now()->format('Y-m-d'))
                ->with('reprogrammer_date_fin', $banniere->date_fin?->format('Y-m-d'));
        }

        return $this->activer($request, $banniere);
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
            ->with('success', 'Bannière supprimée avec succès.');
    }
}