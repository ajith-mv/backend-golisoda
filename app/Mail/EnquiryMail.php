<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $title;

    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

   
    public function build()
    {
        return $this->markdown('email.enquiry_mail')->subject($this->title);
    }
    
}
