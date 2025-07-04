<?php
namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Invitation $invite;

    public function __construct(Invitation $invite)
    {
        $this->invite = $invite;
    }

    public function build()
    {
        return $this
            ->subject("Vous êtes invité sur ".config('app.name'))
            ->markdown('emails.invitation');
    }
}
