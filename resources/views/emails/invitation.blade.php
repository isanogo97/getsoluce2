@component('mail::message')
# Invitation

Vous avez été invité à rejoindre {{ config('app.name') }}. Cliquez sur le bouton ci-dessous pour accepter l'invitation.

@component('mail::button', ['url' => $invite->token])
Accepter l'invitation
@endcomponent

Merci,<br>
{{ config('app.name') }}
@endcomponent
