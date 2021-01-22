<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Storage;
class DeleteAllID extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ndaId:6hours';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete All ID Photo every six horus';

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
        Storage::disk('id_uploads')->delete(Storage::disk('id_uploads')->allFiles());
        //
    }
}
