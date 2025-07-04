<?php

namespace App\Notifications;

use App\Models\Avance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AvanceValidee extends Notification
{
    use Queueable;

    public function __construct(public Avance $avance) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $net = number_format($this->avance->montant_net, 2, ',', ' ');

        return (new MailMessage)
            ->subject('✅ Avance de salaire validée')
            ->greeting("Bonjour {$notifiable->name},")
            ->line("🎉 Votre demande d'avance de salaire a été **acceptée**.")
            ->line("💶 Montant net versé : **{$net} €**")
            ->line('Vous pouvez consulter vos avances depuis votre tableau de bord.')
            ->action('Accéder à mes avances', url('/avances'))
            ->line('Merci de votre confiance.')
            ->salutation("Cordialement,\nL’équipe RH");
    }
}
