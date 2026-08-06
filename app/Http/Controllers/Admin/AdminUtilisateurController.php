<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agence;
use App\Models\Administrateur;
use App\Models\Particulier;
use Illuminate\Http\Request;
use App\Enums\RoleEnum;
use App\Notifications\NouvelUtilisateurNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUtilisateurController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['particulier', 'agence', 'administrateur', 'agence.abonnements']);

        // Filtre par rôle
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.utilisateurs.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['particulier', 'agence', 'administrateur', 'agence.abonnements']);
        return view('admin.utilisateurs.show', compact('user'));
    }

    public function create()
    {
        $roles = RoleEnum::cases();
        return view('admin.utilisateurs.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', array_column(RoleEnum::cases(), 'value')),
            'mot_de_passe' => 'required|string|min:8|confirmed',
        ], [
            'nom.required' => 'Le nom est obligatoire.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné n\'est pas valide.',
            'mot_de_passe.required' => 'Le mot de passe est obligatoire.',
            'mot_de_passe.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'mot_de_passe.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $plainPassword = $request->mot_de_passe;

        DB::beginTransaction();

        try {
            // Créer l'utilisateur
            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'mot_de_passe' => Hash::make($plainPassword),
                'role' => $request->role,
                'email_verified_at' => now(),
            ]);

            // Créer le profil selon le rôle
            if ($user->isParticulier()) {
                Particulier::create([
                    'user_id' => $user->id,
                ]);
            } elseif ($user->isAgence()) {
                Agence::create([
                    'user_id' => $user->id,
                    'nom_agence' => $request->nom_agence ?? $request->nom . ' ' . $request->prenom,
                    'adresse' => $request->adresse ?? '',
                    'statut_validation' => false,
                ]);
            } elseif ($user->isAdmin()) {
                Administrateur::create([
                    'user_id' => $user->id,
                    'fonction' => $request->fonction ?? 'Administrateur',
                ]);
            }

            // Envoyer l'email de notification
            $user->notify(new NouvelUtilisateurNotification($user, $plainPassword, $user->role));

            DB::commit();

            return redirect()->route('admin.utilisateurs.index')
                ->with('success', 'Utilisateur créé avec succès. Un email de bienvenue a été envoyé.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erreur lors de la création: ' . $e->getMessage());
        }
    }

    public function edit(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.utilisateurs.index')
                ->with('error', 'Les administrateurs ne peuvent pas être modifiés.');
        }

        $roles = RoleEnum::cases();
        return view('admin.utilisateurs.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.utilisateurs.index')
                ->with('error', 'Les administrateurs ne peuvent pas être modifiés.');
        }

        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', array_column(RoleEnum::cases(), 'value')),
        ]);

        $user->update($request->only(['nom', 'prenom', 'email', 'telephone', 'role']));

        if ($request->filled('mot_de_passe')) {
            $request->validate(['mot_de_passe' => 'string|min:8|confirmed']);
            $user->update(['mot_de_passe' => Hash::make($request->mot_de_passe)]);
        }

        return redirect()->route('admin.utilisateurs.index')
            ->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Impossible de supprimer un administrateur.');
        }

        $user->delete();
        return redirect()->route('admin.utilisateurs.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function toggleBlock(Request $request, User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Impossible de bloquer un administrateur.');
        }

        if ($user->isAgence() && $user->agence) {
            $user->agence->update(['bloque' => !$user->agence->bloque]);
            $status = $user->agence->bloque ? 'bloqué' : 'débloqué';
            return redirect()->route('admin.utilisateurs.index')
                ->with('success', "L'utilisateur a été {$status} avec succès.");
        }

        return back()->with('error', 'Action non disponible pour cet utilisateur.');
    }
}