<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mise en vedette validée — DoyaImmo</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
    
    <div style="background-color: #f4f4f4; padding: 40px 20px;">
        <div style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.1);">
            
            <!-- EN-TÊTE AVEC LOGO -->
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
            
            <!-- CONTENU -->
            <div style="padding: 32px;">
                <h2 style="color: #1A1A2E; margin-top: 0; font-size: 22px;"> Mise en vedette activée</h2>
                
                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Bonjour <strong>{{ $notifiable->prenom }} {{ $notifiable->nom }}</strong>,
                </p>
                
                <div style="background-color: #E8F5E9; border: 1px solid #C8E6C9; border-radius: 8px; padding: 16px; margin: 24px 0; text-align: center;">
                    <span style="font-size: 40px;">🎉</span>
                    <p style="color: #1E7A47; font-size: 18px; font-weight: bold; margin: 8px 0 0 0;">
                        Félicitations ! Votre bien est en vedette !
                    </p>
                </div>
                
                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Nous avons le plaisir de vous informer que votre demande de mise en vedette a été validée.
                    Votre bien est désormais visible en vedette sur DoyaImmo.
                </p>
                
                <div style="background-color: #F7F9FC; border: 1px solid #E8E8F0; border-radius: 8px; padding: 20px; margin: 24px 0;">
                    <h3 style="color: #1A1A2E; margin-top: 0; font-size: 18px;"> Détails de la mise en vedette</h3>
                    
                    <table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px; width: 40%;">Bien :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $bien->titre }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;">Durée :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $duree }} jour{{ $duree > 1 ? 's' : '' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;">Date de fin :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $dateFin }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;">Statut :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                <span style="background-color: #E8F5E9; color: #1E7A47; padding: 4px 12px; border-radius: 20px; font-size: 14px;">
                                     Actif
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ route('agence.biens.show', $bien) }}" 
                       style="background-color: #2A9D8F; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block; box-shadow: 0 4px 8px rgba(42, 157, 143, 0.3);">
                         👁️ Voir mon bien
                    </a>
                </div>
                
                <p style="color: #8A8AA0; font-size: 14px; line-height: 1.6;">
                    Merci de faire confiance à DoyaImmo.
                </p>
            </div>
            
            <!-- PIED DE PAGE -->
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