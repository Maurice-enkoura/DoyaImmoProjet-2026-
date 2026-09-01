<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposition refusée — DoyaImmo</title>
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
                <h2 style="color: #1A1A2E; margin-top: 0; font-size: 22px;"> Proposition refusée</h2>
                
                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Bonjour <strong>{{ $notifiable->prenom }} {{ $notifiable->nom }}</strong>,
                </p>
                
                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Votre proposition a été <strong>refusée</strong>.
                </p>
                
                <div style="background-color: #FFF5F5; border: 1px solid #FFCDD2; border-radius: 8px; padding: 20px; margin: 24px 0;">
                    <h3 style="color: #C62828; margin-top: 0; font-size: 18px;"> Détails de la proposition refusée</h3>
                    
                    <table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px; width: 40%;"> Bien :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $proposition->bien->titre ?? 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;"> Prix proposé :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ number_format($proposition->prix_propose, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;"> Type de bien :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $proposition->bien->type_bien->label() ?? 'N/A' }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;"> Type de contrat :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $proposition->bien->type_contrat->label() ?? 'N/A' }}
                            </td>
                        </tr>
                    </table>
                </div>
                
                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Nous vous encourageons à consulter d'autres demandes qui pourraient correspondre à vos biens.
                </p>
                
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ url('/agence/demandes') }}" 
                       style="background-color: #B85C3A; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block; box-shadow: 0 4px 8px rgba(184, 92, 58, 0.3);">
                        Voir les demandes
                    </a>
                </div>
                
                <p style="color: #8A8AA0; font-size: 14px; line-height: 1.6;">
                    Si vous avez des questions, n'hésitez pas à nous contacter.
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