@extends('layouts.dashboard-agence')

@section('title', 'Contact pour la mise en vedette — DoyaImmo')
@section('page_title', 'Mise en vedette - Contact')
@section('page_sub', 'Finalisez votre demande')

@section('content')
<div class="view active">
    <div class="section-head" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
        <div>
            <h2 style="font-size:clamp(18px, 2.5vw, 22px);font-weight:700;margin:0;">Contactez DoyaImmo</h2>
            <p style="color:var(--muted);font-size:clamp(13px, 1vw, 14px);margin:4px 0 0;">Finalisez votre demande de mise en vedette</p>
        </div>
        <a href="{{ route('agence.biens.show', $bien) }}" class="btn btn-ghost btn-sm" style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border-radius:8px;font-size:12.5px;font-weight:600;text-decoration:none;color:var(--text-soft);border:1px solid var(--border);background:transparent;transition:all 0.2s;white-space:nowrap;">
            <i class="fa-solid fa-arrow-left"></i> Retour au bien
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background:#E8F5E9;color:#1E7A47;border-left:4px solid #1E7A47;padding:12px 20px;border-radius:var(--radius);margin-bottom:20px;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- ✅ Affichage de l'ID de la demande -->
    <div style="background:#E3F2FD;border:1px solid #BBDEFB;border-radius:12px;padding:12px 16px;margin-bottom:20px;">
        <p style="margin:0;font-size:clamp(12px, 0.9vw, 13px);color:#0D47A1;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <i class="fa-solid fa-info-circle"></i>
            <strong>Demande #{{ $mise->id }}</strong> — En attente de paiement
        </p>
    </div>

    <!-- ✅ GRID RESPONSIVE -->
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
        <!-- Résumé de la demande -->
        <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:clamp(16px, 2vw, 24px);overflow:hidden;">
            <h3 style="font-size:clamp(15px, 1.2vw, 16px);font-weight:700;margin:0 0 16px;color:var(--ink);display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-file-invoice" style="color:var(--rust);"></i>
                Résumé de la demande
            </h3>
            <div style="display:grid;gap:10px;">
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:4px;">
                    <span style="color:var(--text-soft);font-size:clamp(12px, 0.8vw, 13px);">Demande #</span>
                    <span style="font-weight:600;font-size:clamp(12px, 0.8vw, 13px);">{{ $mise->id }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:4px;">
                    <span style="color:var(--text-soft);font-size:clamp(12px, 0.8vw, 13px);">Bien</span>
                    <span style="font-weight:600;font-size:clamp(12px, 0.8vw, 13px);word-break:break-word;max-width:60%;text-align:right;">{{ $bien->titre }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:4px;">
                    <span style="color:var(--text-soft);font-size:clamp(12px, 0.8vw, 13px);">Adresse</span>
                    <span style="font-weight:600;font-size:clamp(12px, 0.8vw, 13px);">{{ $bien->quartier }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:4px;">
                    <span style="color:var(--text-soft);font-size:clamp(12px, 0.8vw, 13px);">Prix du bien</span>
                    <span style="font-weight:600;font-size:clamp(12px, 0.8vw, 13px);">{{ number_format($bien->prix, 0, ',', ' ') }} FCFA</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:4px;">
                    <span style="color:var(--text-soft);font-size:clamp(12px, 0.8vw, 13px);">Durée de vedette</span>
                    <span style="font-weight:600;font-size:clamp(12px, 0.8vw, 13px);">{{ $duree }} jour{{ $duree > 1 ? 's' : '' }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);flex-wrap:wrap;gap:4px;">
                    <span style="color:var(--text-soft);font-size:clamp(12px, 0.8vw, 13px);">Montant à payer</span>
                    <span style="font-weight:700;font-size:clamp(16px, 1.2vw, 18px);color:var(--rust);">{{ number_format($montant, 0, ',', ' ') }} FCFA</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:8px 0;flex-wrap:wrap;gap:4px;">
                    <span style="color:var(--text-soft);font-size:clamp(12px, 0.8vw, 13px);">Statut</span>
                    <span style="background:#FFF8E1;color:#E65100;padding:2px 12px;border-radius:20px;font-size:clamp(11px, 0.7vw, 12px);font-weight:600;display:inline-block;">
                        En attente de paiement
                    </span>
                </div>
            </div>
        </div>

        <!-- Contact -->
        <div style="background:#fff;border:1px solid var(--border);border-radius:12px;padding:clamp(16px, 2vw, 24px);">
            <h3 style="font-size:clamp(15px, 1.2vw, 16px);font-weight:700;margin:0 0 16px;color:var(--ink);display:flex;align-items:center;gap:8px;">
                <i class="fa-solid fa-phone" style="color:var(--rust);"></i>
                Contactez-nous
            </h3>
            <p style="color:var(--text-soft);font-size:clamp(12px, 0.8vw, 13px);margin-bottom:16px;">
                Pour finaliser votre demande de mise en vedette, contactez l'équipe DoyaImmo :
            </p>

            <div style="display:grid;gap:10px;">
                <a href="tel:+221781234567" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#F7F9FC;border-radius:8px;text-decoration:none;color:var(--ink);transition:background 0.2s;flex-wrap:wrap;">
                    <div style="width:clamp(36px, 3vw, 40px);height:clamp(36px, 3vw, 40px);border-radius:50%;background:var(--rust);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-phone" style="font-size:clamp(14px, 1vw, 16px);"></i>
                    </div>
                    <div style="flex:1;min-width:120px;">
                        <div style="font-weight:600;font-size:clamp(13px, 0.9vw, 14px);">Appelez-nous</div>
                        <div style="font-size:clamp(12px, 0.8vw, 13px);color:var(--muted);">+221 774612082</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:var(--muted);font-size:14px;margin-left:auto;"></i>
                </a>

                <a href="https://wa.me/221774612082?text=Bonjour%2C%20je%20souhaite%20finaliser%20ma%20demande%20de%20mise%20en%20vedette%20%23{{ $mise->id }}%20pour%20le%20bien%20%3A%20{{ urlencode($bien->titre) }}" target="_blank" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#E8F5E9;border-radius:8px;text-decoration:none;color:var(--ink);border:1px solid #C8E6C9;transition:background 0.2s;flex-wrap:wrap;">
                    <div style="width:clamp(36px, 3vw, 40px);height:clamp(36px, 3vw, 40px);border-radius:50%;background:#25D366;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-brands fa-whatsapp" style="font-size:clamp(18px, 1.2vw, 20px);"></i>
                    </div>
                    <div style="flex:1;min-width:120px;">
                        <div style="font-weight:600;font-size:clamp(13px, 0.9vw, 14px);">WhatsApp</div>
                        <div style="font-size:clamp(12px, 0.8vw, 13px);color:var(--muted);">+221 774612082</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:var(--muted);font-size:14px;margin-left:auto;"></i>
                </a>

                <a href="mailto:contact@doyaimmo.com?subject=Demande%20de%20mise%20en%20vedette%20%23{{ $mise->id }}&body=Bonjour,%20je%20souhaite%20finaliser%20ma%20demande%20de%20mise%20en%20vedette%20%23{{ $mise->id }}%20pour%20le%20bien%20:%20{{ $bien->titre }}" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#E3F2FD;border-radius:8px;text-decoration:none;color:var(--ink);border:1px solid #BBDEFB;transition:background 0.2s;flex-wrap:wrap;">
                    <div style="width:clamp(36px, 3vw, 40px);height:clamp(36px, 3vw, 40px);border-radius:50%;background:#0D47A1;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="fa-solid fa-envelope" style="font-size:clamp(14px, 1vw, 16px);"></i>
                    </div>
                    <div style="flex:1;min-width:120px;">
                        <div style="font-weight:600;font-size:clamp(13px, 0.9vw, 14px);">Email</div>
                        <div style="font-size:clamp(12px, 0.8vw, 13px);color:var(--muted);">contact@doyaimmo.com</div>
                    </div>
                    <i class="fa-solid fa-chevron-right" style="color:var(--muted);font-size:14px;margin-left:auto;"></i>
                </a>
            </div>

            <div style="margin-top:16px;padding:clamp(10px, 1vw, 12px) clamp(12px, 1.5vw, 16px);background:#FFF8E1;border-radius:8px;border-left:4px solid #F5A623;">
                <p style="margin:0;font-size:clamp(11px, 0.8vw, 12px);color:#5D4037;line-height:1.6;">
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

    <!-- ✅ FOOTER RESPONSIVE -->
    <div style="margin-top:20px;text-align:center;padding:clamp(12px, 1.5vw, 16px);background:#fff;border:1px solid var(--border);border-radius:12px;">
        <p style="margin:0;font-size:clamp(12px, 0.8vw, 13px);color:var(--muted);display:flex;align-items:center;justify-content:center;gap:8px;flex-wrap:wrap;">
            <i class="fa-regular fa-clock"></i>
            <span>Une fois le paiement confirmé, votre bien sera mis en vedette sous 24h. La vedette expire automatiquement après la période choisie.</span>
        </p>
    </div>
</div>

<!-- ✅ STYLES RESPONSIVE -->
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

    /* ✅ RESPONSIVE TABLETTE ET MOBILE */
    @media (max-width: 820px) {
        .section-head {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 10px !important;
        }
        .section-head .btn {
            width: 100% !important;
            justify-content: center !important;
        }
        .grid {
            grid-template-columns: 1fr !important;
        }
        .grid-2 {
            grid-template-columns: 1fr !important;
        }
    }

    @media (max-width: 600px) {
        .section-head {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
        }
        .section-head .btn {
            width: 100% !important;
            justify-content: center !important;
        }
        .flex-between {
            flex-wrap: wrap !important;
            gap: 4px !important;
        }
        .alert {
            font-size: 12px !important;
            padding: 10px 14px !important;
            flex-wrap: wrap !important;
        }
        .card {
            padding: 14px !important;
        }
        .info-row {
            flex-wrap: wrap !important;
            gap: 4px !important;
        }
        .info-row .label {
            font-size: 12px !important;
        }
        .info-row .value {
            font-size: 12px !important;
            max-width: 100% !important;
            text-align: left !important;
        }
        .contact-btn {
            padding: 10px 14px !important;
            gap: 10px !important;
        }
        .contact-btn .icon {
            width: 32px !important;
            height: 32px !important;
        }
        .contact-btn .icon i {
            font-size: 14px !important;
        }
        .contact-btn .text {
            font-size: 12px !important;
        }
        .contact-btn .sub {
            font-size: 11px !important;
        }
        .info-box {
            padding: 10px 12px !important;
            font-size: 11px !important;
        }
        .info-box strong {
            font-size: 11px !important;
        }
        .footer-note {
            font-size: 11px !important;
            padding: 12px 14px !important;
        }
    }

    @media (max-width: 400px) {
        .section-head h2 {
            font-size: 17px !important;
        }
        .section-head p {
            font-size: 12px !important;
        }
        .price {
            font-size: 15px !important;
        }
        .btn-sm {
            font-size: 11px !important;
            padding: 5px 10px !important;
        }
        .contact-btn {
            padding: 8px 12px !important;
            gap: 8px !important;
        }
        .contact-btn .icon {
            width: 28px !important;
            height: 28px !important;
        }
        .contact-btn .icon i {
            font-size: 12px !important;
        }
        .contact-btn .text {
            font-size: 11px !important;
        }
        .contact-btn .sub {
            font-size: 10px !important;
        }
        .info-box {
            font-size: 10px !important;
            padding: 8px 10px !important;
        }
        .info-box strong {
            font-size: 10px !important;
        }
        .footer-note {
            font-size: 10px !important;
            padding: 10px 12px !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        * {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
        }
    }
</style>
@endsection