<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreditNoteSender extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('mailTemplate.credit_note', $this->data)
                     ->subject('New Credit Note')
                     ->attach(public_path('storage/'.$this->data['filename']),[
                        'as' => $this->data['filename'], 
                        'mime' => 'application/pdf'
                    ]);
    }
}
