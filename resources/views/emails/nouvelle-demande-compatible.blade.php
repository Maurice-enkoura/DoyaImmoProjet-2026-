<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle demande compatible — DoyaImmo</title>
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
                <h2 style="color: #1A1A2E; margin-top: 0; font-size: 22px;"> Nouvelle demande compatible</h2>
                
                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Bonjour <strong>{{ $notifiable->agence->nom_agence ?? $notifiable->prenom }}</strong>,
                </p>
                
                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Un nouveau besoin correspond à vos biens :
                </p>
                
                <div style="background-color: #F7F9FC; border: 1px solid #E8E8F0; border-radius: 8px; padding: 20px; margin: 24px 0;">
                    <h3 style="color: #1A1A2E; margin-top: 0; font-size: 18px;"> Détails de la demande</h3>
                    
                    <table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px; width: 40%;"> Type de bien :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $demande->type_bien->label() ?? $demande->type_bien }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;"> Type d'opération :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $demande->type_operation->label() ?? $demande->type_operation }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;"> Zone recherchée :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $demande->zone_recherchee }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;"> Budget :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ number_format($demande->budget_maximum, 0, ',', ' ') }} FCFA
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;"> Score de compatibilité :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $score }}%
                            </td>
                        </tr>
                    </table>
                </div>
                
                <!-- Badge de compatibilité -->
                <div style="text-align: center; margin: 20px 0;">
                    @if($score >= 80)
                        <span style="background-color: #2E7D32; color: #ffffff; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: bold;">🏆 Excellente compatibilité !</span>
                    @elseif($score >= 60)
                        <span style="background-color: #1976D2; color: #ffffff; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: bold;">👍 Bonne compatibilité</span>
                    @elseif($score >= 40)
                        <span style="background-color: #F57C00; color: #ffffff; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: bold;">📊 Compatibilité moyenne</span>
                    @else
                        <span style="background-color: #C62828; color: #ffffff; padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: bold;">📈 Compatibilité à améliorer</span>
                    @endif
                </div>
                
                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Répondez rapidement pour maximiser vos chances !
                </p>
                
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ url('/agence/demandes/' . $demande->id) }}" 
                       style="background-color: #2A9D8F; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block; box-shadow: 0 4px 8px rgba(42, 157, 143, 0.3);">
                         Voir et proposer
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