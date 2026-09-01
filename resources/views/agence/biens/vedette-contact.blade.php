@extends('layouts.dashboard-agence')

@section('title', 'Contact pour la mise en vedette — DoyaImmo')
@section('page_title', 'Mise en vedette - Contact')
@section('page_sub', 'Finalisez votre demande')

@section('content')
<div class="view active">
    <div class="section-head">
        <div>
            <h2>Contactez DoyaImmo</h2>
            <p>Finalisez votre demande de mise en vedette</p>
        </div>
        <a href="{{ route('agence.biens.show', $bien) }}" class="btn btn-ghost btn-sm">
            <i class="fa-solid fa-arrow-left"></i> Retour au bien
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background:#E8F5E9;color:#1E7A47;border-left:4px solid #1E7A47;padding:12px 20px;border-radius:var(--radius);margin-bottom:20px;">
            <i class="fa-solid fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- ✅ Affichage de l'ID de la demande -->
    <div style="background:#E3F2FD;border:1px solid #BBDEFB;border-radius:12px;padding:12px 16px;margin-bottom:20px;">
        <p style="margin:0;font-size:13px;color:#0D47A1;">
            <i class="fa-solid fa-info-circle"></i>
            <strong>Demande #{{ $mise->id }}</strong> — En attente de paiement
        </p>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <!-- Résumé de la demande -->
        <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
            <h3 style="font-size:16px;font-weight:700;margin:0 0 16px;color:var(--ink);">
                <i class="fa-solid fa-file-invoice" style="color:var(--rust);margin-right:8px;"></i>
                Résumé de la demande
            </h3>
            <div style="display:grid;gap:12px;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-soft);">Demande #</span>
                    <span style="font-weight:600;">{{ $mise->id }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-soft);">Bien</span>
                    <span style="font-weight:600;">{{ $bien->titre }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-soft);">Adresse</span>
                    <span style="font-weight:600;">{{ $bien->quartier }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-soft);">Prix du bien</span>
                    <span style="font-weight:600;">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-soft);">Durée de vedette</span>
                    <span style="font-weight:600;">{{ $duree }} jour{{ $duree > 1 ? 's' : '' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);">
                    <span style="color:var(--text-soft);">Montant à payer</span>
                    <span style="font-weight:700;font-size:18px;color:var(--rust);">{{ number_format($montant, 0, ',', ' ') }} FCFA</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;">
                    <span style="color:var(--text-soft);">Statut</span>
                    <span style="background:#FFF8E1;color:#E65100;padding:2px 12px;border-radius:20px;font-size:12px;font-weight:600;">
                        En attente de paiement
                    </span>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:24px;">
            <h3 style="font-size:16px;font-weight:700;margin:0 0 16px;color:var(--ink);">
                <i class="fa-solid fa-phone" style="color:var(--rust);margin-right:8px;"></i>
                Contactez-nous
            </h3>
            <p style="color:var(--text-soft);font-size:13px;margin-bottom:16px;">
                Pour finaliser votre demande de mise en vedette, contactez l'équipe DoyaImmo :
            </p>

            <div style="display:grid;gap:12px;">
                <a href="tel:+221781234567" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#F7F9FC;border-radius:8px;text-decoration:none;color:var(--ink);">
                    <div style="width:40px;height:40px;border-radius:50%;background:var(--rust);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:14px;">Appelez-nous</div>
                        <div style="font-size:13px;color:var(--muted);">+221 78 123 45 67</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="margin-left:auto;color:var(--muted);"></i>
                </a>

                <a href="https://wa.me/221781234567?text=Bonjour%2C%20je%20souhaite%20finaliser%20ma%20demande%20de%20mise%20en%20vedette%20%23{{ $mise->id }}%20pour%20le%20bien%20%3A%20{{ urlencode($bien->titre) }}" target="_blank" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#E8F5E9;border-radius:8px;text-decoration:none;color:var(--ink);border:1px solid #C8E6C9;">
                    <div style="width:40px;height:40px;border-radius:50%;background:#25D366;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-brands fa-whatsapp"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:14px;">WhatsApp</div>
                        <div style="font-size:13px;color:var(--muted);">+221 78 123 45 67</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="margin-left:auto;color:var(--muted);"></i>
                </a>

                <a href="mailto:contact@doyaimmo.com?subject=Demande%20de%20mise%20en%20vedette%20%23{{ $mise->id }}&body=Bonjour,%20je%20souhaite%20finaliser%20ma%20demande%20de%20mise%20en%20vedette%20%23{{ $mise->id }}%20pour%20le%20bien%20:%20{{ $bien->titre }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#E3F2FD;border-radius:8px;text-decoration:none;color:var(--ink);border:1px solid #BBDEFB;">
                    <div style="width:40px;height:40px;border-radius:50%;background:#0D47A1;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <div style="font-weight:600;font-size:14px;">Email</div>
                        <div style="font-size:13px;color:var(--muted);">contact@doyaimmo.com</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="margin-left:auto;color:var(--muted);"></i>
                </a>
            </div>

            <div style="margin-top:16px;padding:12px 16px;background:#FFF8E1;border-radius:8px;border-left:4px solid #F5A623;">
                <p style="margin:0;font-size:12px;color:#5D4037;">
                    <i class="fa-solid fa-info-circle" style="color:#F5A623;"></i>
                    <strong>Informations à fournir :</strong>
                    <br>
                    - Numéro de demande : <strong>#{{ $mise->id }}</strong>
                    <br>
                    - Nom de l'agence : <strong>{{ $bien->agence->nom_agence ?? 'Non renseigné' }}</strong>
                    <br>
                    - Bien concerné : <strong>{{ $bien->titre }}</strong>
                    <br>
                    - Durée souhaitée : <strong>{{ $duree }} jour{{ $duree > 1 ? 's' : '' }}</strong>
                    <br>
                    - Montant : <strong>{{ number_format($montant, 0, ',', ' ') }} FCFA</strong>
                </p>
            </div>
        </div>
    </div>

    <div style="margin-top:20px;text-align:center;padding:16px;background:#fff;border:1px solid var(--border);border-radius:12px;">
        <p style="margin:0;font-size:13px;color:var(--muted);">
            <i class="fa-regular fa-clock"></i>
            Une fois le paiement confirmé, votre bien sera mis en vedette sous 24h.
            La vedette expire automatiquement après la période choisie.
        </p>
    </div>
</div>
@endsection

@push('styles')
<style>
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.2s;
    }
    .btn-rust {
        background: var(--rust);
        color: #fff;
        border: none;
    }
    .btn-rust:hover {
        background: #9A4523;
        color: #fff;
    }
    .btn-ghost {
        background: transparent;
        color: var(--text-soft);
        border-color: var(--border);
    }
    .btn-ghost:hover {
        background: var(--border);
    }
    .btn-sm {
        padding: 6px 14px;
        font-size: 12.5px;
    }
    .alert {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        border-left: 4px solid;
    }
    @media (max-width: 820px) {
        .grid {
            grid-template-columns: 1fr !important;
        }
    }
    @media (max-width: 600px) {
        .section-head {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }
        .section-head .btn {
            width: 100%;
            justify-content: center;
        }
        .flex-between {
            flex-wrap: wrap;
            gap: 4px;
        }
    }
</style>
@endpush