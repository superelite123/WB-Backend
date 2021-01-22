<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\InvoiceNew;
use PDF;

class ReportNewOrder extends Mailable
{
    use Queueable, SerializesModels;
    public $invoice;
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
        $mail = $this->view('order.report_mail',['invoice' => $this->invoice])
                     ->subject('Sales Order has been approved - '.$this->invoice->number)
                     ->attach(public_path('storage/'.$this->invoice->number.'/mail.pdf'),[
                        'as' => 'invoice.pdf',
                        'mime' => 'application/pdf'
                      ]);
        return $mail;
    }
}
