<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\InvoiceNew;
use PDF;

class PendingOrderSender extends Mailable
{
    use Queueable, SerializesModels;
    public $invoice;
    public $coa_path = 'assets/upload/files/coa/';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(InvoiceNew $invoice)
    {
        //
        $this->invoice = $invoice;
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
        $mail = $this->view('order.mail_template',['invoice' => $this->invoice])
                     ->subject('Here is your invoice')
                     ->attach(public_path('storage/'.$this->invoice->number.'/mail.pdf'),[
                        'as' => 'invoice.pdf', 
                        'mime' => 'application/pdf'
                      ]);
        return $mail;
    }
}
