<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreation extends Mailable
{
    use Queueable, SerializesModels;

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
                    ->subject(getSetting('APPLICATION_NAME') . ' | ' .  __('Your account has been created'))
                    ->markdown('vendor.mail.html.user-creation');
    }
}
