<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rendez-vous confirmé — DoyaImmo</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    @php
        $rdv = $rendezVous;
        $agence = $rdv->agence;
        $bien = $rdv->proposition->bien ?? null;
        $agenceUser = $agence->user ?? null;
        $dateVisite = $rdv->date_visite ? \Carbon\Carbon::parse($rdv->date_visite)->format('d/m/Y') : 'N/A';
        $heureVisite = $rdv->heure_visite ?? 'N/A';
        $telephone = $agenceUser->telephone ?? null;
    @endphp

    <div style="background-color: #f4f4f4; padding: 40px 20px;">
        <div style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.1);">

            {{-- EN-TÊTE --}}
            <div style="background-color: #1A1A2E; padding: 24px 32px; text-align: center;">
                <table role="presentation" style="margin: 0 auto; border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle;">
                            <div style="display: inline-block; width: 48px; height: 48px; background-color: #B85C3A; border-radius: 12px; text-align: center; line-height: 48px; font-size: 24px; font-weight: bold; color: #ffffff; margin-right: 12px;">
                                D
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <span style="font-size: 24px; font-weight: bold; color: #ffffff; letter-spacing: -0.5px;">
                                Doya<span style="color: #D4AF37;">Immo</span>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- CONTENU --}}
            <div style="padding: 32px;">
                <h2 style="color: #1A1A2E; margin-top: 0; font-size: 22px;">
                    ✅ Rendez-vous confirmé
                </h2>

                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Bonjour <strong>{{ $notifiable->prenom }} {{ $notifiable->nom }}</strong>,
                </p>

                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Excellente nouvelle ! L'agence a confirmé votre rendez-vous de visite.
                </p>

                {{-- Détails RDV --}}
                <div style="background-color: #F7F9FC; border: 1px solid #E8E8F0; border-radius: 8px; padding: 20px; margin: 24px 0;">
                    <h3 style="color: #1A1A2E; margin-top: 0; font-size: 18px;">
                        📅 Détails du rendez-vous
                    </h3>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px; width: 40%;">Date :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $dateVisite }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;">Heure :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $heureVisite }}
                            </td>
                        </tr>
                        @if($bien)
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;">Bien :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $bien->titre ?? 'N/A' }}
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;">Agence :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $agence->nom_agence ?? 'N/A' }}
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- ⭐ COORDONNÉES AGENCE (seul email qui les affiche) --}}
                @if($agence && ($agence->adresse || $telephone))
                    <div style="background-color: #FFF8E1; border: 1px solid #FFE0B2; border-radius: 8px; padding: 16px; margin-top: 24px;">
                        <p style="margin: 0 0 8px 0; color: #B85C3A; font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;">
                            📞 Coordonnées de l'agence
                        </p>

                        @if($agence->adresse)
                            <p style="margin: 4px 0; color: #4A4A6A; font-size: 14px;">
                                <strong>Adresse :</strong> {{ $agence->adresse }}
                            </p>
                        @endif

                        @if($telephone)
                            <p style="margin: 4px 0; color: #4A4A6A; font-size: 14px;">
                                <strong>Téléphone :</strong>
                                <a href="tel:{{ preg_replace('/\s+/', '', $telephone) }}" style="color: #B85C3A; text-decoration: none; font-weight: bold;">
                                    {{ $telephone }}
                                </a>
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Rappel --}}
                <div style="background-color: #F7F9FC; border: 1px solid #E8E8F0; border-radius: 8px; padding: 16px; margin-top: 24px;">
                    <p style="margin: 0; color: #4A4A6A; font-size: 14px; line-height: 1.6;">
                        <strong>💡 Rappel :</strong> Merci de vous présenter à l'heure convenue. Votre code de visite vous sera communiqué 24h avant la date.
                    </p>
                </div>

                {{-- CTA --}}
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ url('/particulier/rendezvous/' . $rdv->id) }}"
                       style="background-color: #B85C3A; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block;">
                        Voir le rendez-vous
                    </a>
                </div>

                <p style="color: #8A8AA0; font-size: 14px; line-height: 1.6;">
                    Si vous avez des questions, n'hésitez pas à nous contacter.
                </p>
            </div>

            {{-- PIED --}}
            <div style="background-color: #F7F9FC; padding: 20px 32px; text-align: center; border-top: 1px solid #E8E8F0;">
                <table role="presentation" style="margin: 0 auto; border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle;">
                            <div style="display: inline-block; width: 28px; height: 28px; background-color: #B85C3A; border-radius: 6px; text-align: center; line-height: 28px; font-size: 14px; font-weight: bold; color: #ffffff; margin-right: 8px;">
                                D
                            </div>
                        </td>
                        <td style="vertical-align: middle;">
                            <span style="font-size: 16px; font-weight: bold; color: #1A1A2E;">
                                Doya<span style="color: #B85C3A;">Immo</span>
                            </span>
                        </td>
                    </tr>
                </table>
                <p style="color: #8A8AA0; font-size: 12px; margin-top: 12px;">
                    © {{ date('Y') }} DoyaImmo. Tous droits réservés.
                </p>
            </div>

        </div>
    </div>

</body>
</html>