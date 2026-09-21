<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvel avis reçu — DoyaImmo</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">

    @php
        $eval = $evaluation;
        $particulier = $eval->particulier->user ?? null;
        $bien = $eval->proposition->bien ?? null;
        $note = (int) $eval->note;

        // Couleur selon la note
        if ($note >= 4) {
            $color = '#2E7D32';
            $bgColor = '#E8F5E9';
            $borderColor = '#C8E6C9';
            $emoji = '🌟';
            $label = 'Excellent';
        } elseif ($note === 3) {
            $color = '#F57F17';
            $bgColor = '#FFF8E1';
            $borderColor = '#FFE082';
            $emoji = '👍';
            $label = 'Correct';
        } else {
            $color = '#C62828';
            $bgColor = '#FFEBEE';
            $borderColor = '#FFCDD2';
            $emoji = '';
            $label = 'À améliorer';
        }
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
                    ⭐ Nouvel avis reçu
                </h2>

                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Bonjour <strong>{{ $notifiable->prenom }} {{ $notifiable->nom }}</strong>,
                </p>

                <p style="color: #4A4A6A; font-size: 16px; line-height: 1.6;">
                    Un client a laissé un avis sur votre agence :
                </p>

                {{-- Note --}}
                <div style="background-color: {{ $bgColor }}; border: 1px solid {{ $borderColor }}; border-radius: 8px; padding: 24px; margin: 24px 0; text-align: center;">
                    <div style="font-size: 14px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: {{ $color }}; margin-bottom: 8px;">
                        {{ $emoji }} {{ $label }}
                    </div>
                    <div style="font-size: 42px; font-weight: bold; color: {{ $color }}; line-height: 1; margin-bottom: 8px;">
                        {{ $note }}<span style="font-size: 20px;">/5</span>
                    </div>
                    <div style="font-size: 16px; color: {{ $color }};">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $note)
                                ★
                            @else
                                ☆
                            @endif
                        @endfor
                    </div>
                </div>

                {{-- Détails --}}
                <div style="background-color: #F7F9FC; border: 1px solid #E8E8F0; border-radius: 8px; padding: 20px; margin: 24px 0;">
                    <h3 style="color: #1A1A2E; margin-top: 0; font-size: 18px;">
                        📋 Détails de l'avis
                    </h3>

                    <table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
                        @if($particulier)
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px; width: 40%;">Client :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $particulier->prenom }} {{ $particulier->nom }}
                            </td>
                        </tr>
                        @endif
                        @if($bien)
                        <tr>
                            <td style="padding: 8px 0; color: #8A8AA0; font-size: 14px;">Bien concerné :</td>
                            <td style="padding: 8px 0; color: #4A4A6A; font-size: 16px; font-weight: bold;">
                                {{ $bien->titre ?? 'N/A' }}
                            </td>
                        </tr>
                        @endif
                    </table>

                    @if($eval->commentaire)
                        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid #E8E8F0;">
                            <p style="margin: 0 0 8px 0; color: #8A8AA0; font-size: 14px;">Commentaire :</p>
                            <p style="margin: 0; color: #4A4A6A; font-size: 15px; line-height: 1.6; font-style: italic;">
                                "{{ $eval->commentaire }}"
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div style="background-color: #F7F9FC; border: 1px solid #E8E8F0; border-radius: 8px; padding: 16px; margin-top: 24px;">
                    <p style="margin: 0; color: #4A4A6A; font-size: 14px; line-height: 1.6;">
                        <strong>💡 Astuce :</strong> Répondez à cet avis pour montrer votre engagement et votre professionnalisme.
                    </p>
                </div>

                {{-- CTA --}}
                <div style="text-align: center; margin: 32px 0;">
                    <a href="{{ url('/agence/evaluations/' . $eval->id) }}"
                       style="background-color: #B85C3A; color: #ffffff; padding: 14px 32px; text-decoration: none; border-radius: 8px; font-size: 16px; font-weight: bold; display: inline-block;">
                        Voir et répondre
                    </a>
                </div>

                <p style="color: #8A8AA0; font-size: 14px; line-height: 1.6;">
                    Votre réactivité fait la différence auprès des clients.
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