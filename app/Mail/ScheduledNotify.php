<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\InvoiceNew;
use PDF;

class ScheduledNotify extends Mailable
{
    use Queueable, SerializesModels;
    public $invoice;
    public $coa_path = 'assets/upload/files/coa/';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        //
        $this->mailData = $mailData;
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
        $mail = $this->view('mailTemplate.scheduled_notify',['mailData' => $this->mailData])
                     ->subject('Order Scheduled');
        return $mail;
    }
}
