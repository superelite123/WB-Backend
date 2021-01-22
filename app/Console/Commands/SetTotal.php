<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InvoiceNew;
class SetTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settotal:onetime';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        
        //update total info
        $invoices = InvoiceNew::where('status','>',2)->orderBy('id','desc')->limit(100)->get();
        foreach($invoices as $invoice)
        {
            $totalInfo = $invoice->TotalInfo;
            $invoice->update([
                'tbp' => $totalInfo['base_price'],
                'discount' => $totalInfo['discount'],
                'e_discount' => $totalInfo['e_discount'],
                'extended' => $totalInfo['extended'],
                'promotion' => $totalInfo['promotion'],
                'pr_value' => $totalInfo['prValue'],
                'tax' => $totalInfo['tax'],
                'total' => $totalInfo['adjust_price'],
                'qty' => $totalInfo['qty'],
            ]);
        }
    }
}
