<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $emailForm;

    public function __construct($data)
    {
        $this->data = $data;
        $this->emailForm = $data['email'];

    }

    public function build()
    {
        return $this->from($this->emailForm)
                    ->subject('New Contact Form Submission')
                    ->view('emails.contact');
    }
}
