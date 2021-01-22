<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProblemOrder extends Mailable
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
        //
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //->attach(asset('storage/'.$invoice->number.'/invoice.pdf'))
        //->attach(asset('assets/upload/files/INV-1019/sign.png'))
        //storage/'.$this->invoice->number.'/invoice.
        $mail = $this->view('order.fulfillment_problem_order_email',['data' => $this->data])
                     ->subject('Problem fulfillment');
        return $mail;
    }
}
