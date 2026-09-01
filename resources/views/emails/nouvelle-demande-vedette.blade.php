@component('mail::message')
#  Nouvelle demande de mise en vedette

Une nouvelle demande de mise en vedette a été soumise.

**Agence :** {{ $nomAgence }}
**Bien :** {{ $titreBien }}
**Durée :** {{ $duree }} jours
**Montant :** {{ $montant }} FCFA

@component('mail::button', ['url' => route('admin.mises-vedette.show', $miseEnVedette)])
Voir la demande
@endcomponent

Cordialement,
L'équipe DoyaImmo
@endcomponent