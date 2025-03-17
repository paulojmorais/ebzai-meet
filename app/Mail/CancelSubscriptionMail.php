<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CancelSubscriptionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var
     */
    public $user;
    public $body;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $body)
    {
        $this->user = $user;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($address = env('MAIL_FROM_ADDRESS'), $name = env('MAIL_FROM_NAME'))
            ->subject(__('Subscription cancelled') . ' | ' . getSetting('APPLICATION_NAME'))
            ->markdown('vendor.mail.html.cancel-subscription');
    }
}
