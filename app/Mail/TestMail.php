<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $data;
    public $title;

    public function __construct($data, $title)
    {
        $this->data = $data;
        $this->title = $title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->view('email.testEmail', [
        //     "data" => $this->data
        // ])->subject($this->title)->attach( public_path('storage/invoice_order/MM-ORD-000005.pdf'));

        // return $this->view('email.testEmail', [
        //     "data" => $this->data
        // ])->subject($this->title);

        return $this->markdown('email.common.dynamicContent', ["data" => $this->data])->subject($this->title);

        // return $this->markdown('email.testEmail');
    }
}
