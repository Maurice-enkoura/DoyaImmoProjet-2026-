<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Enums\RoleEnum;

class AdminUtilisateurController extends Controller
{
    public function index()
    {
        $users = User::with(['particulier', 'agence', 'administrateur'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.utilisateurs.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['particulier', 'agence', 'administrateur']);
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
            'email' => 'required|email|unique:users',
            'telephone' => 'nullable|string|max:20',
            'role' => 'required|in:' . implode(',', array_column(RoleEnum::cases(), 'value')),
            'mot_de_passe' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'telephone' => $request->telephone,
            'mot_de_passe' => bcrypt($request->mot_de_passe),
            'role' => $request->role,
            'email_verified_at' => now(),
        ]);

        // Créer le profil selon le rôle
        if ($user->isParticulier()) {
            $user->particulier()->create();
        } elseif ($user->isAgence()) {
            $user->agence()->create([
                'nom_agence' => $request->nom_agence ?? $request->nom,
                'adresse' => $request->adresse ?? '',
                'statut_validation' => false,
            ]);
        } elseif ($user->isAdmin()) {
            $user->administrateur()->create([
                'fonction' => $request->fonction ?? 'Administrateur',
            ]);
        }

        return redirect()->route('admin.utilisateurs')->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        $roles = RoleEnum::cases();
        return view('admin.utilisateurs.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
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
            $user->update(['mot_de_passe' => bcrypt($request->mot_de_passe)]);
        }

        return redirect()->route('admin.utilisateurs')->with('success', 'Utilisateur mis à jour.');
    }

    public function destroy(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Impossible de supprimer un administrateur.');
        }

        $user->delete();
        return redirect()->route('admin.utilisateurs')->with('success', 'Utilisateur supprimé avec succès.');
    }
}