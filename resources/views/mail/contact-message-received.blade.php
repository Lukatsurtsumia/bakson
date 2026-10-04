<x-mail::message>
# Nouvelle demande de devis

**Nom :** {{ $contactMessage->name }}<br>
**E-mail :** {{ $contactMessage->email }}<br>
**Téléphone :** {{ $contactMessage->phone ?? 'non renseigné' }}<br>
**Créneau souhaité :** {{ $contactMessage->preferredTimeLabel() ?? 'non précisé' }}<br>
**Reçue le :** {{ $contactMessage->created_at->timezone('Europe/Paris')->format('d/m/Y à H:i') }}

<x-mail::panel>
{!! nl2br(e($contactMessage->message)) !!}
</x-mail::panel>

Répondez simplement à cet e-mail pour écrire directement à {{ $contactMessage->name }}.
</x-mail::message>
