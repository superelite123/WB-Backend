<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\InvoiceNew;
use PDF;

class SignSalesPerson extends Mailable
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
        $mail = $this->view('mailTemplate.sign_sales_report',['invoice' => $this->invoice])
                     ->subject($this->invoice->number.','.$this->invoice->number2)
                     ->attach(public_path('storage/'.$this->invoice->number.'/mail.pdf'),[
                        'as' => 'invoice.pdf',
                        'mime' => 'application/pdf'
                    ]);
        return $mail;
    }
}
