<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewEnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $enquirer;
    public array $details;

    public function __construct(array $enquirer, array $details)
    {
        $this->enquirer = $enquirer; // ['name' => ..., 'email' => ..., 'phone' => ...]
        $this->details = $details;   // structured enquiry payload
    }

    public function build()
    {
        return $this->subject('New Service Enquiry')
            ->view('mail.enquiry');
    }
}

