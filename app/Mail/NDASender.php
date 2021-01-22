<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Nda;
use PDF;
class NDASender extends Mailable
{
    use Queueable, SerializesModels;
    public $nda;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Nda $nda)
    {
        //
        $this->nda = $nda;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->view('mailTemplate.nda',['nda' => $this->nda])
                     ->subject('Thank you for submitting your NDA.')
                     ->from('noreply@cultivision.us','Walnut Distro NDA')
                     ->attach(public_path('storage/ndaPdfs/'.$this->nda->pdf_file),[
                        'as' => $this->nda->pdf_file, 
                        'mime' => 'application/pdf'
                    ]);
    }
}
