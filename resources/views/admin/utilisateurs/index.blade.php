@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs — Administration DoyaImmo')
@section('page_title', 'Gestion des utilisateurs')
@section('page_sub', 'Gérez tous les utilisateurs de la plateforme')

@section('content')
<div class="section-head">
    <div>
        <h2>Utilisateurs</h2>
        <p>{{ $users->total() }} utilisateurs inscrits sur la plateforme</p>
    </div>
    <div>
        <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-rust">
            <i class="fa-solid fa-user-plus"></i> Ajouter un utilisateur
        </a>
    </div>
</div>

<!-- Filtres et recherche -->
<div style="margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <!-- Filtres par rôle -->
    <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-sm {{ !request('role') ? 'btn-rust' : 'btn-ghost' }}">
        <i class="fa-solid fa-users"></i> Tous
    </a>
    <a href="{{ route('admin.utilisateurs.index', ['role' => 'admin']) }}" class="btn btn-sm {{ request('role') === 'admin' ? 'btn-rust' : 'btn-ghost' }}">
        <i class="fa-solid fa-user-shield"></i> Admins
    </a>
    <a href="{{ route('admin.utilisateurs.index', ['role' => 'agence']) }}" class="btn btn-sm {{ request('role') === 'agence' ? 'btn-rust' : 'btn-ghost' }}">
        <i class="fa-solid fa-building"></i> Agences
    </a>
    <a href="{{ route('admin.utilisateurs.index', ['role' => 'particulier']) }}" class="btn btn-sm {{ request('role') === 'particulier' ? 'btn-rust' : 'btn-ghost' }}">
        <i class="fa-solid fa-user"></i> Particuliers
    </a>
    
    <!-- Recherche -->
    <form action="{{ route('admin.utilisateurs.index') }}" method="GET" style="display:flex;gap:8px;margin-left:auto;">
        <input type="text" name="search" placeholder="Rechercher par nom, email..." 
               value="{{ request('search') }}" 
               style="padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;min-width:200px;">
        <button type="submit" class="btn btn-sm btn-ghost">
            <i class="fa-solid fa-search"></i>
        </button>
        @if(request('search') || request('role'))
            <a href="{{ route('admin.utilisateurs.index') }}" class="btn btn-sm btn-ghost">
                <i class="fa-solid fa-times"></i> Réinitialiser
            </a>
        @endif
    </form>
</div>

<!-- Table -->
<div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Email / Téléphone</th>
                <th>Rôle</th>
                <th>Abonnement</th>
                <th>Statut</th>
                <th>Inscrit le</th>
                <th style="text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px;">
                            <div style="width:40px;height:40px;border-radius:50%;background:var(--rust-soft);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:16px;color:var(--rust);">
                                {{ strtoupper(substr($user->prenom ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->nom ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="cell-main">{{ $user->prenom }} {{ $user->nom }}</div>
                                <div class="cell-sub">
                                    @if($user->isAgence() && $user->agence)
                                        <i class="fa-solid fa-building"></i> {{ $user->agence->nom_agence ?? 'N/A' }}
                                    @elseif($user->isParticulier())
                                        <i class="fa-solid fa-user"></i> Particulier
                                    @elseif($user->isAdmin())
                                        <i class="fa-solid fa-user-shield"></i> Administrateur
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:13px;">{{ $user->email }}</div>
                        <div style="font-size:12px;color:var(--muted);">{{ $user->telephone ?? 'Pas de téléphone' }}</div>
                    </td>
                    <td>
                        <span class="status-pill 
                            @if($user->isAdmin()) status-active
                            @elseif($user->isAgence()) status-confirme
                            @else status-en_attente
                            @endif">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td>
                        @if($user->isAgence() && $user->agence)
                            @php
                                $abonnement = $user->agence->abonnements()->where('statut', true)->where('date_fin', '>', now())->first();
                            @endphp
                            @if($abonnement)
                                <span class="status-pill status-active">
                                    <i class="fa-solid fa-crown"></i> 
                                    {{ is_object($abonnement->formule) ? $abonnement->formule->label() : ucfirst($abonnement->formule) }}
                                </span>
                                <div style="font-size:10px;color:var(--muted);">
                                    Expire le {{ \Carbon\Carbon::parse($abonnement->date_fin)->format('d/m/Y') }}
                                </div>
                            @else
                                <span class="status-pill status-inactif">
                                    <i class="fa-solid fa-circle-xmark"></i> Aucun
                                </span>
                            @endif
                        @else
                            <span style="font-size:12px;color:var(--muted);">—</span>
                        @endif
                    </td>
                    <td>
                        @if($user->email_verified_at)
                            <span class="status-pill status-active">Vérifié</span>
                        @else
                            <span class="status-pill status-en_attente">Non vérifié</span>
                        @endif
                        @if($user->isAgence() && $user->agence && isset($user->agence->bloque) && $user->agence->bloque)
                            <span class="status-pill status-annule" style="margin-top:4px;display:block;">Bloqué</span>
                        @endif
                    </td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                    <td style="text-align:center;">
                        <div style="display:flex;gap:4px;justify-content:center;flex-wrap:wrap;">
                            <!-- Voir - accessible pour tous -->
                            <a href="{{ route('admin.utilisateurs.show', $user) }}" class="btn btn-sm btn-ghost" title="Voir">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            
                            <!-- PAS de bouton MODIFIER pour les admins -->
                            
                            <!-- Bloquer/Débloquer - PAS pour les admins -->
                            @if(!$user->isAdmin() && $user->isAgence())
                                @if(isset($user->agence->bloque) && $user->agence->bloque)
                                    <form action="{{ route('admin.utilisateurs.toggle-block', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Débloquer">
                                            <i class="fa-solid fa-unlock"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.utilisateurs.toggle-block', $user) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger" title="Bloquer" onclick="return confirm('Bloquer cet utilisateur ?')">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    </form>
                                @endif
                            @endif
                            
                            <!-- Supprimer - PAS pour les admins -->
                            @if(!$user->isAdmin())
                                <form action="{{ route('admin.utilisateurs.destroy', $user) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer définitivement cet utilisateur ?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--muted);">
                        <i class="fa-solid fa-users" style="font-size:32px;display:block;margin-bottom:12px;"></i>
                        Aucun utilisateur trouvé.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:20px;">
    {{ $users->appends(request()->query())->links() }}
</div>
@endsection