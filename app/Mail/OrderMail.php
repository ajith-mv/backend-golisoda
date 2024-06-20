<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $title;
    public $filePath;

    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
        $this->filePath = $filePath;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.common.dynamicContent')->subject($this->title)
        ->attach( public_path($this->filePath));
        // return $this->markdown('email.testEmail');
    }
}
